<?php
/*---------------------------------------------------------------------------
 FCM HTTP v1 access token helper.

 The `Authorization: Bearer ...` token Google issues is valid for 1 hour only,
 so it must never be hard coded. This file signs a JWT with the service
 account private key, exchanges it for a fresh access token, and caches that
 token on disk until shortly before it expires.

 Usage:
   include "fcm_token.php";
   $tok = get_fcm_access_token();
   if($tok["access_token"]==""){ ... $tok["error"] ... }

 SECURITY: service-account.json is the private key of the Firebase project.
 It currently sits inside the web root, so it is reachable over HTTP unless
 the shipped .htaccess is honoured. Move it above the web root and point
 FCM_SERVICE_ACCOUNT_FILE at the new location as soon as you can.
---------------------------------------------------------------------------*/

if(!defined("FCM_SERVICE_ACCOUNT_FILE")){
	define("FCM_SERVICE_ACCOUNT_FILE", dirname(__FILE__)."/service-account.json");
}
/*---scope required to call the fcm.googleapis.com v1 messages:send endpoint---*/
if(!defined("FCM_SCOPE")){
	define("FCM_SCOPE", "https://www.googleapis.com/auth/firebase.messaging");
}
/*---refresh this many seconds before the real expiry, to absorb clock skew---*/
if(!defined("FCM_TOKEN_EARLY_REFRESH")){
	define("FCM_TOKEN_EARLY_REFRESH", 300);
}
/*---TLS certificate checking stays ON. Only set this to false (in the calling
     script, before the include) if the server has an outdated CA bundle and
     curl reports "SSL certificate problem" - it is a downgrade, not a fix---*/
if(!defined("FCM_SSL_VERIFYPEER")){
	define("FCM_SSL_VERIFYPEER", true);
}

function fcm_base64url_encode($data){
	return rtrim(strtr(base64_encode($data), "+/", "-_"), "=");
}

/*---the cache lives in the system temp dir, not the web root, so the token
     can never be downloaded through a browser---*/
function fcm_token_cache_file($project_id){
	$project_id = $project_id ? preg_replace("/[^A-Za-z0-9_\-]/", "_", $project_id) : "default";
	return rtrim(sys_get_temp_dir(), "/\\")."/fcm_token_".$project_id.".json";
}

function fcm_read_service_account(){
	$sa_file = FCM_SERVICE_ACCOUNT_FILE;
	if(!file_exists($sa_file)){
		return array("ok"=>false,"error"=>"Service account file not found: ".$sa_file);
	}
	$raw = file_get_contents($sa_file);
	$sa = json_decode($raw, true);
	if(!is_array($sa)){
		return array("ok"=>false,"error"=>"Service account file is not valid JSON.");
	}
	if(!isset($sa["client_email"]) || !isset($sa["private_key"])){
		return array("ok"=>false,"error"=>"Service account file is missing client_email / private_key.");
	}
	if(!isset($sa["token_uri"]) || $sa["token_uri"]==""){
		$sa["token_uri"] = "https://oauth2.googleapis.com/token";
	}
	return array("ok"=>true,"sa"=>$sa);
}

/*---signs the JWT assertion and swaps it for an access token at Google---*/
function fcm_request_new_access_token($sa){
	if(!function_exists("openssl_sign")){
		return array("access_token"=>"","expires_in"=>0,"error"=>"PHP openssl extension is not enabled.");
	}
	$now = time();
	$header = array("alg"=>"RS256","typ"=>"JWT");
	$claims = array(
		"iss"   => $sa["client_email"],
		"scope" => FCM_SCOPE,
		"aud"   => $sa["token_uri"],
		"iat"   => $now,
		"exp"   => $now + 3600
	);
	$signing_input = fcm_base64url_encode(json_encode($header)).".".fcm_base64url_encode(json_encode($claims));

	$pkey = openssl_pkey_get_private($sa["private_key"]);
	if(!$pkey){
		return array("access_token"=>"","expires_in"=>0,"error"=>"private_key in the service account file could not be read.");
	}
	$signature = "";
	$signed = openssl_sign($signing_input, $signature, $pkey, "sha256");
	if(function_exists("openssl_free_key")){
		@openssl_free_key($pkey);
	}
	if(!$signed){
		return array("access_token"=>"","expires_in"=>0,"error"=>"Could not sign the JWT assertion.");
	}
	$assertion = $signing_input.".".fcm_base64url_encode($signature);

	$post_fields = "grant_type=".urlencode("urn:ietf:params:oauth:grant-type:jwt-bearer")."&assertion=".urlencode($assertion);
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => $sa["token_uri"],
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => $post_fields,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_SSL_VERIFYPEER => FCM_SSL_VERIFYPEER,
		CURLOPT_HTTPHEADER => array("Content-Type: application/x-www-form-urlencoded")
	));
	$response = curl_exec($curl);
	$curl_error = curl_error($curl);
	$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);

	if($response===false){
		return array("access_token"=>"","expires_in"=>0,"error"=>"Token request failed: ".$curl_error);
	}
	$token_data = json_decode($response, true);
	if(!is_array($token_data) || !isset($token_data["access_token"]) || $token_data["access_token"]==""){
		return array("access_token"=>"","expires_in"=>0,"error"=>"Token endpoint returned HTTP ".$http_code.": ".$response);
	}
	$expires_in = isset($token_data["expires_in"]) ? intval($token_data["expires_in"]) : 3600;
	return array("access_token"=>$token_data["access_token"],"expires_in"=>$expires_in,"error"=>"");
}

/*---returns a valid token, reusing the cached one while it is still fresh.
     Pass true to ignore the cache (used to retry once after a 401).---*/
function get_fcm_access_token($force_refresh=false){
	$sa_read = fcm_read_service_account();
	if(!$sa_read["ok"]){
		return array("access_token"=>"","project_id"=>"","error"=>$sa_read["error"]);
	}
	$sa = $sa_read["sa"];
	$project_id = isset($sa["project_id"]) ? $sa["project_id"] : "";
	$cache_file = fcm_token_cache_file($project_id);

	if(!$force_refresh && file_exists($cache_file)){
		$cached = json_decode(@file_get_contents($cache_file), true);
		if(is_array($cached) && isset($cached["access_token"]) && isset($cached["expires_at"])){
			if($cached["access_token"]!="" && intval($cached["expires_at"]) > time()){
				return array("access_token"=>$cached["access_token"],"project_id"=>$project_id,"error"=>"");
			}
		}
	}

	$fresh = fcm_request_new_access_token($sa);
	if($fresh["access_token"]==""){
		return array("access_token"=>"","project_id"=>$project_id,"error"=>$fresh["error"]);
	}
	$expires_at = time() + $fresh["expires_in"] - FCM_TOKEN_EARLY_REFRESH;
	@file_put_contents($cache_file, json_encode(array("access_token"=>$fresh["access_token"],"expires_at"=>$expires_at)), LOCK_EX);
	@chmod($cache_file, 0600);
	return array("access_token"=>$fresh["access_token"],"project_id"=>$project_id,"error"=>"");
}

/*---posts a message payload to FCM v1, refreshing the token once on 401---*/
function fcm_send_message($message_payload){
	$tok = get_fcm_access_token();
	if($tok["access_token"]==""){
		return array("http_code"=>0,"response"=>"","error"=>$tok["error"]);
	}
	$result = fcm_post_to_fcm($tok["project_id"], $tok["access_token"], $message_payload);
	if($result["http_code"]==401){
		/*---cached token was rejected, force a brand new one and retry once---*/
		$tok = get_fcm_access_token(true);
		if($tok["access_token"]==""){
			return array("http_code"=>401,"response"=>$result["response"],"error"=>$tok["error"]);
		}
		$result = fcm_post_to_fcm($tok["project_id"], $tok["access_token"], $message_payload);
	}
	return $result;
}

function fcm_post_to_fcm($project_id, $access_token, $message_payload){
	$url = "https://fcm.googleapis.com/v1/projects/".$project_id."/messages:send";
	$body = is_string($message_payload) ? $message_payload : json_encode(array("message"=>$message_payload));
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => $body,
		CURLOPT_SSL_VERIFYPEER => FCM_SSL_VERIFYPEER,
		CURLOPT_HTTPHEADER => array(
			"Content-Type: application/json",
			"Authorization: Bearer ".$access_token
		)
	));
	$response = curl_exec($curl);
	$curl_error = curl_error($curl);
	$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);
	if($response===false){
		return array("http_code"=>$http_code,"response"=>"","error"=>$curl_error);
	}
	return array("http_code"=>$http_code,"response"=>$response,"error"=>"");
}
/*---no closing php tag on purpose: this file is included, and any character
     after `?>` would be echoed into the caller's json response---*/

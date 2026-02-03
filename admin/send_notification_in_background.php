<?php
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
date_default_timezone_set('Asia/Kolkata');
include "star_connection.php";
$notification_message = "notification_message";
$img_dir = "noty_images/";
$server_url1 = "http://" . $_SERVER['SERVER_NAME']."/";
$image_link_url = $server_url1."admin/noty_images/";


function send_push_notification_in_android($registration_ids,$message) {
$apikey = "AAAAXvy8orQ:APA91bEM7eOUXD-n11s4C4SmSgmbsZyjEp9zOS_sBYpwxUB8ov0okUqrQLiYl7RvEp3K2HMlwsBZe0NTFN3L9-XIH4ymebSolmzBM2rtF7gbczsyQyOuBeRkCLqeqH9ymXtS5iZRKQlV";	
       
$url = 'https://fcm.googleapis.com/fcm/send';
$headers = array(
'Authorization: key='.$apikey,
'Content-Type: application/json'
);
$fields = array(
'to' => $registration_ids,
'data' => $message
);
	// Open connection
	$ch = curl_init(); 
	// Set the url, number of POST vars, POST data
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	// Disabling SSL Certificate support temporarly
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields)); 
	// Execute post
	$result = curl_exec($ch);	
	//print_r($result);
	if ($result === FALSE) {
	$result = '{"success":0,"failure":1}';
	} 
	// Close connection
	curl_close($ch);
	$result_str = json_decode($result,true);
	if($result_str["success"]==1){
	return "TRUE";
	}else{
	return "FALSE";
	}
    }

function pushNotificationInIOS($noti_mode,$message_body,$noty_title,$deviceToken,$noty_image){
$pnres = "FALSE";
$keyfile = 'AuthKey_A4PUG4TL3P.p8';               # <- Your AuthKey file
$keyid = 'A4PUG4TL3P';                            # <- Your Key ID
$teamid = 'LTCHGYC6BS';                           # <- Your Team ID (see Developer Portal)
$bundleid = 'com.forcepower.starsaathi';         # <- Your Bundle ID
if($noti_mode=="development"){
$url = 'https://api.development.push.apple.com';  # <- development url, or use http://api.push.apple.com for production environment
}else{
$url = 'https://api.push.apple.com';	
}
$arr_msg = array();
$arr_msg["aps"]["alert"] = array("title"=>$noty_title,"body"=>$message_body);
$arr_msg["aps"]["sound"] = "default";
$arr_msg["aps"]["badge"] = 1;
if($noty_image!=""){
$arr_msg['mediaUrl'] = $noty_image;
}
$message = json_encode($arr_msg);

$key = openssl_pkey_get_private('file://'.$keyfile);

$header["alg"] = "ES256";
$header["kid"] = $keyid;

$claims["iss"] = $teamid;
$claims["iat"] = time();

$header_encoded = base64($header);
$claims_encoded = base64($claims);

$signature = '';
openssl_sign($header_encoded . '.' . $claims_encoded, $signature, $key, 'sha256');
$jwt = $header_encoded . '.' . $claims_encoded . '.' . base64_encode($signature);

// only needed for PHP prior to 5.5.24
if (!defined('CURL_HTTP_VERSION_2_0')) {
define('CURL_HTTP_VERSION_2_0', 3);
}

$http2ch = curl_init();
curl_setopt_array($http2ch, array(
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
CURLOPT_URL => "$url/3/device/$deviceToken",
CURLOPT_PORT => 443,
CURLOPT_HTTPHEADER => array(
"apns-topic: {$bundleid}",
"authorization: bearer $jwt"
),
CURLOPT_POST => TRUE,
CURLOPT_POSTFIELDS => $message,
CURLOPT_RETURNTRANSFER => TRUE,
CURLOPT_TIMEOUT => 30,
CURLOPT_HEADER => 1
));

$result = curl_exec($http2ch);
if ($result === FALSE) {
//throw new Exception("Curl failed: ".curl_error($http2ch));
}else{
$pn_status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);
if($pn_status==200){
$pnres = "TRUE";	
}
}


return $pnres;
  
}


function sentNotificationToAll($pno,$the_title,$the_message,$the_branch_code,$the_m_image_link,$noty_id,$total_sending_count){
	$changepassword = "changepassword";
	$notification_message = "notification_message";
	$customer_master = "customer_master";
if($pno!=''){
$limit = 50;
$start_from = (($pno-1)*$limit);
if($the_branch_code=="ALL"){
	$sql1 = "select `customer_code`,`registrationid`,`device_type` from $changepassword where `registrationid`!='' and `device_type`!='' order by `customer_code` asc limit $start_from,$limit";
}else{
	$sql1 = "select $customer_master.`customer_code`,$changepassword.`registrationid`,$changepassword.`device_type` from $customer_master left join $changepassword on $customer_master.`customer_code`=$changepassword.`customer_code` where $customer_master.`branch_code` in('".$the_branch_code."') and $changepassword.`registrationid`!='' and $changepassword.`device_type`!='' order by $customer_master.`customer_code` asc limit $start_from,$limit";
}
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
$sent_done = array();
if($totres1>0){
$android_message = array();
$curr_timestamp = date('Y-m-d H:i:s');
$curr_date_time = date("Y-m-d H:i:s");
$nt_title = $the_title;
$payload = array();
$payload['team'] = 'India';
$payload['score'] = '5.6';
$app_noty_image = $the_m_image_link;	
$android_message['data'] = array("title"=>$nt_title,"is_background"=>FALSE,"message"=>$the_message,"image"=>$app_noty_image,"payload"=>$payload,"timestamp"=>$curr_timestamp);			
$body['aps'] = array("alert"=> array('body'=>$noty_msg),"sound"=>"default");

	while($row1=mysql_fetch_assoc($res1)){
		$registration_id = $row1["registrationid"];
		$device_type = $row1["device_type"];
		if($device_type=="IOS"){
			$sent_sts_prod = pushNotificationInIOS("",$the_message,$the_title,$registration_id,$the_m_image_link);
			//$sent_sts_dev = pushNotificationInIOS("development",$the_message,$the_title,$registration_id,$the_m_image_link);
			if($sent_sts_prod=="TRUE"){
				$sent_done[]="TRUE";
			}
		}else if($device_type=="ANDROID"){			
			$sent_sts = send_push_notification_in_android($registration_id,$android_message);
			if($sent_sts=="TRUE"){
			$sent_done[]=$sent_sts;
			}			
		}
	}
$total_sent = count($sent_done);
$total_sending_count = ($total_sending_count + $total_sent);
if($noty_id!=''){
 $sql_noty_updt = "update $notification_message set `sending_count`='$total_sending_count' where `id`='$noty_id'";
$res_noty_updt = mysql_query($sql_noty_updt);
}
$new_pno = ($pno+1);
sentNotificationToAll($new_pno,$the_title,$the_message,$the_branch_code,$the_m_image_link,$noty_id,$total_sending_count);	
}else{
	if($noty_id!=''){
	$sql_noty_updt = "update $notification_message set `status`='END' where `id`='$noty_id'";
	$res_noty_updt = mysql_query($sql_noty_updt);
	}
}


}	
}

$new_gen_noty_id_enc = $argv[1];
$noty_id_decrspt = base64_decode($new_gen_noty_id_enc);
if($noty_id_decrspt!=""){
$sql1 = "select * from $notification_message where `id`='$noty_id_decrspt'";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	$row1=mysql_fetch_assoc($res1);
	$title = $row1["title"];
	$message = $row1["message"];
	$file_type = $row1["file_type"] ? trim($row1["file_type"]) : "NONE";
	$image_name = $row1["image_name"] ? trim($row1["image_name"]) : "";
	if($file_type=="NONE" || $file_type=="PDF"){
		$m_image_link ="";
	}else{
	if($image_name!=""){
	if(file_exists($img_dir.$image_name)){
	$m_image_link = $image_link_url.$image_name;
	}else{
	$m_image_link ="";
	}
	}else{
	$m_image_link ="";
	}
	}
	$branch_code = $row1["branch_code"] ? trim($row1["branch_code"]) : "ALL";
	if($branch_code!=""){
		if(strtolower($branch_code)=="all"){
			$branch_code = "ALL";
		}else{
			$branch_code_arr = explode(",",$branch_code);
			$branch_code = implode("','",$branch_code_arr);
		}		
	}else{
		$branch_code = "ALL";
	}
	sentNotificationToAll(1,$title,$message,$branch_code,$m_image_link,$noty_id_decrspt,0);
}

}
?>
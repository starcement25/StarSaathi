<?php
error_reporting(E_STRICT);
ini_set('memory_limit', '99999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
include "star_connection.php";

$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$ledger = "ledger";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";
$product_master = "product_master";
$changepassword = "changepassword";
$cron_update_table = "cron_update_table";
$prev_date = date('Y-m-d',strtotime("-12 days"));
$res_data = array();

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
	echo $result;
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
	
function base64($data){
return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
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


function process_update_tracking($process_name,$process_start_end_flag,$remark){
$sync_cron = "cron_update_table";
$curr_datetime = date("Y-m-d H:i:s");
$process_name = $process_name ? $process_name : "";
$process_start_end_flag = $process_start_end_flag ? $process_start_end_flag : "";
$remark = $remark ? trim(addslashes($remark)) : "";
if($process_name!="" && $process_start_end_flag!=""){
	if($process_start_end_flag=="START"){
		$sql = "update $sync_cron set `last_start_datetime`='$curr_datetime',`remark`='$remark' where `process_name`='$process_name'";
		mysql_query($sql);
	}else if($process_start_end_flag=="END"){
		$sql = "update $sync_cron set `last_end_datetime`='$curr_datetime',`remark`='$remark' where `process_name`='$process_name'";
		mysql_query($sql);
	}
}
return;	
}


function show_dealer_pn_details_by_cust_id($the_customer_code){
$changepassword = "changepassword";
$pn_data = array("sts"=>"NO","device_type"=>"","registrationid"=>"");

if($the_customer_code!=""){
$sql_cp = "SELECT `device_type`,`registrationid` FROM $changepassword WHERE `customer_code` = '$the_customer_code' ";
$res_cp = mysql_query($sql_cp);
$totres_cp = mysql_num_rows($res_cp);
if($totres_cp>0){
$row_cp=mysql_fetch_assoc($res_cp);
$the_device_type = $row_cp["device_type"] ? trim($row_cp["device_type"]) : "";
$the_registrationid = $row_cp["registrationid"] ? trim($row_cp["registrationid"]) : "";
if($the_device_type!="" && $the_registrationid!=""){
	$pn_data = array("sts"=>"YES","device_type"=>$the_device_type,"registrationid"=>$the_registrationid);
}
}
}

return $pn_data;	
}


function show_dealer_pn_details_by_dealer_id($the_dns_customer_code){
$changepassword = "changepassword";
$pn_data = array("sts"=>"NO","device_type"=>"","registrationid"=>"");

if($the_dns_customer_code!=""){
$sql_cp = "SELECT `device_type`,`registrationid` FROM $changepassword WHERE `dns_customer_code` = '$the_dns_customer_code' ";
$res_cp = mysql_query($sql_cp);
$totres_cp = mysql_num_rows($res_cp);
if($totres_cp>0){
$row_cp=mysql_fetch_assoc($res_cp);
$the_device_type = $row_cp["device_type"] ? trim($row_cp["device_type"]) : "";
$the_registrationid = $row_cp["registrationid"] ? trim($row_cp["registrationid"]) : "";
if($the_device_type!="" && $the_registrationid!=""){
	$pn_data = array("sts"=>"YES","device_type"=>$the_device_type,"registrationid"=>$the_registrationid);
}
}
}

return $pn_data;	
}


function show_prod_name_by_prod_id($the_prod_code){
$product_master = "product_master";
$prod_name = "";

if($the_prod_code!=""){
$sql_cp = "SELECT `prod_desc` FROM $product_master WHERE `prod_code` = '$the_prod_code' ";
$res_cp = mysql_query($sql_cp);
$totres_cp = mysql_num_rows($res_cp);
if($totres_cp>0){
$row_cp=mysql_fetch_assoc($res_cp);
$prod_name = $row_cp["prod_desc"] ? trim($row_cp["prod_desc"]) : "";
}
}

return $prod_name;	
}

function show_prod_details_by_dns_prod_id($the_dns_prod_code){
$product_master = "product_master";
$prod_name = "";

if($the_dns_prod_code!=""){
$sql_cp = "SELECT `prod_desc` FROM $product_master WHERE `dns_prod_code` = '$the_dns_prod_code' ";
$res_cp = mysql_query($sql_cp);
$totres_cp = mysql_num_rows($res_cp);
if($totres_cp>0){
$row_cp=mysql_fetch_assoc($res_cp);
$prod_name = $row_cp["prod_desc"] ? trim($row_cp["prod_desc"]) : "";
}
}

return $prod_name;	
}
process_update_tracking($the_process_name,"START","Process Start");
$server_url1 = "https://" . $_SERVER['SERVER_NAME']."/";
$the_m_image_link = $server_url1."SAP/admin/images/logo.png";
$curr_date = date("Y-m-d");
for($i=1; $i<=10; $i++) {
    $the_date_time = date('Y-m-d\T00:00:00', strtotime("-$i days", strtotime($curr_date)));

    $the_process_name = "make_order_status_cancel";
    
    $starfiori_port_no = $GLOBALS['starfiori_port_no'];

    $the_filter = "&$filter=(erdat eq datetime'".$the_date_time."' and status eq 'CANCEL')";
    $url_ck1 = "https://starfiori.starcement.co.in:".$starfiori_port_no."/sap/opu/odata/sap/ZSD_PARKLOT_SO_SERV_CDS/ZSD_PARKLOT_SO_SERV?\$format=json".str_replace(" ","%20",$the_filter);
    echo $url_ck1."<br>";

    $body_for_mcode1 = get_data_from_cserver($url_ck1);

    if(isJsonCk($body_for_mcode1)){
        $json_decoded = json_decode($body_for_mcode1,true);
        //echo "<pre>"; print_r($json_decoded);

	if(count($json_decoded)>0){
	if(array_key_exists("d",$json_decoded)){
		
	if(array_key_exists("results",$json_decoded["d"])){
		$the_cancel_order_data_arr = $json_decoded["d"]["results"];
		//echo"<pre>";print_r($the_cancel_order_data_arr);
		if(count($the_cancel_order_data_arr)>0){
		foreach($the_cancel_order_data_arr as $the_co_dv){
			$app_ref_no = $the_co_dv["app_ref_no"] ? trim($the_co_dv["app_ref_no"]) : "";
			$material = $the_co_dv["material"] ? trim($the_co_dv["material"]) : "";
			$so_num = $the_co_dv["so_num"] ? trim($the_co_dv["so_num"]) : "";
			$erdat = $the_co_dv["erdat"] ? trim($the_co_dv["erdat"]) : "";
			$erzet = $the_co_dv["erzet"] ? trim($the_co_dv["erzet"]) : "";
			$upd_tmstmp = $the_co_dv["upd_tmstmp"] ? trim($the_co_dv["upd_tmstmp"]) : "";
			$kwmeng = $the_co_dv["kwmeng"] ? trim($the_co_dv["kwmeng"]) : "";
			$vrkme = $the_co_dv["vrkme"] ? trim($the_co_dv["vrkme"]) : "";
			$vstel = $the_co_dv["vstel"] ? trim($the_co_dv["vstel"]) : "";
			$status = $the_co_dv["status"] ? trim($the_co_dv["status"]) : "";
			$remarks = $the_co_dv["remarks"] ? trim($the_co_dv["remarks"]) : "";
			if($app_ref_no!=""){
				$app_order_id_arr[] = $app_ref_no;
			}
			
		}
if(count($app_order_id_arr)>0){
$app_order_ids_str = implode("','",$app_order_id_arr);
echo $sql_upd = "update $t_apperpdo set `STATUS`='Order canceled' where `APPORDERNO` in('".$app_order_ids_str."') ";
$res_upd = mysql_query($sql_upd);	
}
		
		
		
		
		
		
			
		}
	}
	

	
	}
	
	}
	}
	
}

process_update_tracking($the_process_name,"END","Process End");

$res_data = array("process_status"=>"YES","process_message"=>"DONE");
echo json_encode($res_data);
mysql_close();
?>
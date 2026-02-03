<?php
error_reporting(E_STRICT);
ini_set('memory_limit', '99999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
include "star_connection.php";

$img_dir = "noty_images/";
$server_url1 = "http://" . $_SERVER['SERVER_NAME']."/";
$image_link_url = $server_url1."admin/noty_images/";

function send_push_notification_in_android($registration_ids,$message) {
$pn_data_arr = array("sts"=>"","error_msg"=>"");
$error_msg = "";
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
	$error_msg = curl_error($ch);
	$error_msg = stripslashes($error_msg);
	} 
	// Close connection
	curl_close($ch);
	$result_str = json_decode($result,true);
	if($result_str["success"]==1){
	$pn_data_arr = array("sts"=>"TRUE","error_msg"=>"");
	}else{
	$pn_data_arr = array("sts"=>"FALSE","error_msg"=>$error_msg);
	}
	return $pn_data_arr;
    }

function base64($data){
return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
}
function pushNotificationInIOS($noti_mode,$message_body,$noty_title,$deviceToken,$noty_image){
$pn_data_arr = array("sts"=>"","error_msg"=>"");
$error_msg = "";
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
//$arr_msg["aps"]["badge"] = 1;
$arr_msg["aps"]["mutable-content"] = 1;
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
$error_msg = curl_error($http2ch);
$error_msg = stripslashes($error_msg);
$pn_data_arr = array("sts"=>"FALSE","error_msg"=>$error_msg);
}else{
$pn_status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);
if($pn_status==200){
$pn_data_arr = array("sts"=>"TRUE","error_msg"=>"");	
}else{
$error_msg = curl_error($http2ch);
$error_msg = stripslashes($error_msg);
$pn_data_arr = array("sts"=>"FALSE","error_msg"=>$error_msg);	
}
}

return $pn_data_arr;
  
}
function send_process_complete_mail(){
require_once('class.phpmailer.php');
$mail             = new PHPMailer();
$date_time_val = date("Y-m-d H:i:s");
$body             = "<br>Cron process completed at ".$date_time_val;
$body             = eregi_replace("[\]",'',$body);
$mail->IsSMTP(); // telling the class to use SMTP
$mail->Host       = "103.87.174.95"; // SMTP server
$mail->SMTPDebug  = "";                     // enables SMTP debug information (for testing)
                                           // 1 = errors and messages
                                           // 2 = messages only
$mail->SMTPAuth   = true;                  // enable SMTP authentication
$mail->Host       = "103.87.174.95"; // sets the SMTP server
$mail->Port       = 587;                    // set the SMTP port for the GMAIL server
$mail->Username   = "dev@starsaathi.com"; // SMTP account username
$mail->Password   = "google3d33#";        // SMTP account password
$mail->SetFrom('dev@starsaathi.com', 'Starsaathi');
$mail->AddReplyTo('dev@starsaathi.com', 'Starsaathi');
$mail->Subject    = "Starsaathi Dispatch order Cron status";
$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
$mail->MsgHTML($body);
$mail->AddAddress("suranjitd@coral.in", "Suranjit Das");
$mail->AddAddress("mriduj@coral.in", "Mridu");
$mlsts = $mail->Send();	
}
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$ledger = "ledger";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";
$product_master = "product_master";
$cron_update_table = "cron_update_table";
$changepassword = "changepassword";

$curr_date = date("Y-m-d");
$from_hour = "07:15:00";
$to_hour = "22:00:00";

$curr_datetime_from_hour = $curr_date." ".$from_hour;
$curr_datetime_from_hour_timestamp = strtotime($curr_datetime_from_hour);
$curr_datetime_to_hour = $curr_date." ".$to_hour;
$curr_datetime_to_hour_timestamp = strtotime($curr_datetime_to_hour);


$curr_date_time = date("Y-m-d H:i:s");
$curr_date_timestamp = strtotime($curr_date_time);
$prev_date = date('Y-m-d',strtotime("-2 days"));
$res_data = array();
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
function show_product_code_by_dns_product_code($dns_prod_code){
	$product_master = "product_master";
	$prod_code = "";
	$dns_prod_code = $dns_prod_code ? addslashes(trim($dns_prod_code)) : "";
	if($dns_prod_code!=""){
	$sqlcc = "select `prod_code` from $product_master where `dns_prod_code`='$dns_prod_code'";
	$rescc = mysql_query($sqlcc);
	$totrescc = mysql_num_rows($rescc);
	if($totrescc>0){
	$rowcc=mysql_fetch_assoc($rescc);
	$prod_code = addslashes($rowcc["prod_code"]);
	}
	}
	return $prod_code;
	
}
function show_customer_code_by_dns_customer_code($dns_cust_code){
	$customer_master = "customer_master";
	$cust_code = "";
	$dns_cust_code = $dns_cust_code ? addslashes(trim($dns_cust_code)) : "";
	if($dns_cust_code!=""){
		$sqlcc = "select `customer_code` from $customer_master where `dns_customer_code`='$dns_cust_code'";
		$rescc = mysql_query($sqlcc);
		$totrescc = mysql_num_rows($rescc);
		if($totrescc>0){
			$rowcc=mysql_fetch_assoc($rescc);
			$cust_code = addslashes($rowcc["customer_code"]);
		}
}
	return $cust_code;
}

function get_customer_name_by_dealer_id($dns_cust_code){
	$customer_master = "customer_master";
	$customernm = "";
	$dns_cust_code = $dns_cust_code ? addslashes(trim($dns_cust_code)) : "";
	if($dns_cust_code!=""){
		$sqlcc = "select `customer_name` from $customer_master where `dns_customer_code`='$dns_cust_code'";
		$rescc = mysql_query($sqlcc);
		$totrescc = mysql_num_rows($rescc);
		if($totrescc>0){
			$rowcc=mysql_fetch_assoc($rescc);
			$customernm = $rowcc["customer_name"] ? trim($rowcc["customer_name"]) : "";
		}
}
	return $customernm;
}

function get_noty_data_by_dns_customer_code($dns_cust_code){
	$changepassword = "changepassword";
	$noty_data = array("sts"=>"NO","device_type"=>"","reg_id"=>"");
	$dns_cust_code = $dns_cust_code ? addslashes(trim($dns_cust_code)) : "";
	if($dns_cust_code!=""){
		$sqlcc = "select `device_type`,`registrationid` from $changepassword where `dns_customer_code`='$dns_cust_code'";
		$rescc = mysql_query($sqlcc);
		$totrescc = mysql_num_rows($rescc);
		if($totrescc>0){
			$rowcc=mysql_fetch_assoc($rescc);
			$device_type = $rowcc["device_type"] ? trim($rowcc["device_type"]) : "";
			$registrationid = $rowcc["registrationid"] ? trim($rowcc["registrationid"]) : "";
			$noty_data = array("sts"=>"YES","device_type"=>$device_type,"reg_id"=>$registrationid);
		}
}
	return $noty_data;
}

/*-------------------UPDATE ORDER STATUS IN T_APPERPDO TABLE FUNCTION START-------------------------------------------*/
function send_pn_for_dispatch_order($pgno,$prev_date){
$send_pn_for_dispatch_order = "send_pn_for_dispatch_order";
$res_msg = array();
$total_added_data = array();
$limit = 100;
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$auto_notification_log = "auto_notification_log";
$last_updateds = date("Y-m-d H:i:s");
$pno = $pgno ? $pgno : 1;
$start_from = (($pno-1)*$limit);




if($pno!=''){

$sql1 = "SELECT $t_dochallan.*,$t_apperpdo.`destination_name`,DATE_FORMAT($t_dochallan.`LMDT`,'%Y-%m-%d') as `lmdate` FROM $t_dochallan left join $t_apperpdo on $t_dochallan.`APPORDERNO`=$t_apperpdo.`APPORDERNO` WHERE $t_apperpdo.`STATUS`='Dispatched' and $t_dochallan.`APPORDERNO`!='' and $t_dochallan.`APPORDERNO` is not null and $t_apperpdo.`APPORDERNO`!='' and $t_apperpdo.`APPORDERNO` is not null having `lmdate`>='".$prev_date."' order by $t_dochallan.`id` asc limit $start_from,$limit";


$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
	$thechln_ai_id = $row1["id"] ? addslashes(trim($row1["id"])) : "";
	$the_challanqty = $row1["CHALLANQTY"] ? trim($row1["CHALLANQTY"]) : "";
	$the_prod_display_name = $row1["prod_display_name"] ? trim($row1["prod_display_name"]) : "";
	$the_challanno = $row1["CHALLANNO"] ? trim($row1["CHALLANNO"]) : "";
	$is_dispatch_pn_sent = $row1["is_dispatch_pn_sent"] ? $row1["is_dispatch_pn_sent"] : "NO";
	$the_challandt = $row1["LMDT"] ? trim($row1["LMDT"]) : "";
	if($the_challandt!=""){
		$the_challandt = date("d-m-Y h:i A",strtotime($the_challandt));
	}
	$the_truckno = $row1["TRUCKNO"] ? trim($row1["TRUCKNO"]) : "";
	$the_driverno = $row1["DRIVERNO"] ? trim($row1["DRIVERNO"]) : "";
	$the_destination_name = $row1["destination_name"] ? trim($row1["destination_name"]) : "";
	$the_dns_customer_code = $row1["dns_customer_code"] ? trim($row1["dns_customer_code"]) : "";
	$customer_name = get_customer_name_by_dealer_id($the_dns_customer_code);
	$the_pn_msg = "";
	$sent_datetime = date("Y-m-d H:i:s");
	if($is_dispatch_pn_sent=="NO"){
		$the_noty_data = get_noty_data_by_dns_customer_code($the_dns_customer_code);
if($the_noty_data["sts"]=="YES"){
	if($the_noty_data["reg_id"]!=""){
$the_pn_msg = "Your challan has been generated.

".$the_challanqty." MT ".$the_prod_display_name." FOR DISPATCHED WITH CHALLAN NO.: ".$the_challanno." DATED ".$the_challandt.", TRUCK NO.".$the_truckno.", DRIVER DETAILS: ".$the_driverno.", DESTINATION: ".$the_destination_name;

$nt_title = "Product dispatch from Star Saathi";
$the_m_image_link = "";
if($the_noty_data["device_type"]=="ANDROID"){

$android_message = array();
$curr_timestamp = date('Y-m-d H:i:s');
$curr_date_time = date("Y-m-d H:i:s");
$payload = array();
$payload['team'] = 'India';
$payload['score'] = '5.6';
$app_noty_image = $the_m_image_link;	
$android_message['data'] = array("title"=>$nt_title,"is_background"=>FALSE,"message"=>$the_pn_msg,"image"=>$app_noty_image,"payload"=>$payload,"timestamp"=>$curr_timestamp);			
$body['aps'] = array("alert"=> array('body'=>$the_pn_msg),"sound"=>"default");
$sent_msg_arr = send_push_notification_in_android($the_noty_data["reg_id"],$android_message);
$sent_sts = $sent_msg_arr["sts"];
$error_message = $sent_msg_arr["error_msg"];
if($sent_sts=="TRUE"){
$dispatch_pn_sent_remark = "Notification sent.";
}else{
$dispatch_pn_sent_remark = $error_message;	
}

$sql_msglog = "insert into $auto_notification_log (`cust_dns_code`,`cust_name`,`message_type`,`message_sent_status`,`error_message`,`device_type`,`message_text`,`sent_datetime`) values ('$the_dns_customer_code','$customer_name','PN','$sent_sts','$error_message','ANDROID','".addslashes($the_pn_msg)."','$sent_datetime')";
$res_msglog = mysql_query($sql_msglog);

}else if($the_noty_data["device_type"]=="IOS"){
$sent_msg_arr = pushNotificationInIOS("",$the_pn_msg,$nt_title,$the_noty_data["reg_id"],$the_m_image_link);
$sent_sts_prod = $sent_msg_arr["sts"];
$error_message = $sent_msg_arr["error_msg"];
//$sent_sts_dev = pushNotificationInIOS("development",$the_pn_msg,$nt_title,$the_noty_data["reg_id"],$the_m_image_link);
if($sent_sts_prod=="TRUE"){
$dispatch_pn_sent_remark = "Notification sent.";
}else{
$dispatch_pn_sent_remark = $error_message;	
}

$sql_msglog = "insert into $auto_notification_log (`cust_dns_code`,`cust_name`,`message_type`,`message_sent_status`,`error_message`,`device_type`,`message_text`,`sent_datetime`) values ('$the_dns_customer_code','$customer_name','PN','$sent_sts_prod','$error_message','IOS','".addslashes($the_pn_msg)."','$sent_datetime')";
$res_msglog = mysql_query($sql_msglog);

}else{
	$dispatch_pn_sent_remark = "Notification details missing.";	
}

	}else{
		$dispatch_pn_sent_remark = "Notification details missing.";	
	}

}else{
$dispatch_pn_sent_remark = "Notification details missing.";	
}
		
$sqlupdpn = "update $t_dochallan set `is_dispatch_pn_sent`='YES',`dispatch_pn_sent_remark`='$dispatch_pn_sent_remark' where `id`='$thechln_ai_id'";
$resupdpn = mysql_query($sqlupdpn);
	}
	
	}
	
$newpgno = ($pno+1);
send_pn_for_dispatch_order($newpgno,$prev_date);	
}else{
	process_update_tracking($send_pn_for_dispatch_order,"END","Process End");
}
}
return;
}
/*-------------------UPDATE ORDER STATUS IN T_APPERPDO TABLE FUNCTION END-------------------------------------------*/


if(($curr_date_timestamp>=$curr_datetime_from_hour_timestamp) && ($curr_date_timestamp<$curr_datetime_to_hour_timestamp)){
	/*--------------CRON PROCESS NAMES--------------------*/
	$send_pn_for_dispatch_order = "send_pn_for_dispatch_order";
	process_update_tracking($send_pn_for_dispatch_order,"START","Process Start");
	send_pn_for_dispatch_order(1,$prev_date);
	
	send_process_complete_mail();
}else{
	/*--------------Do not send PN between 10PM to 7:15AM--------------------*/
}







$res_data = array("process_status"=>"YES","process_message"=>"DONE");
echo json_encode($res_data);
include "cron_page_start.php";
mysql_close();
?>
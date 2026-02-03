<?php
error_reporting(E_STRICT);
ini_set('memory_limit', '99999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");	
include "star_connection.php";
include "cron_page_start.php";
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
$mail->Subject    = "Starsaathi Cron status";
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
$curr_date_time = date("Y-m-d H:i:s");
$prev_date = date('Y-m-d',strtotime("-10 days"));
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
function get_pn_data_by_dns_customer_code($dns_cust_code){
	$cust_pn_data = array("sts"=>"NO","device_type"=>"","registrationid"=>"");
	$changepassword = "changepassword";
	
	$dns_cust_code = $dns_cust_code ? addslashes(trim($dns_cust_code)) : "";
	if($dns_cust_code!=""){
		$sqlcc = "select `device_type`,`registrationid` from $changepassword where `dns_customer_code`='$dns_cust_code'";
		$rescc = mysql_query($sqlcc);
		$totrescc = mysql_num_rows($rescc);
		if($totrescc>0){
			$rowcc=mysql_fetch_assoc($rescc);
			$device_type = $rowcc["device_type"] ? trim($rowcc["device_type"]) : "";
			$registrationid = $rowcc["registrationid"] ? trim($rowcc["registrationid"]) : "";
			if($device_type!="" && $registrationid!=""){
			$cust_pn_data = array("sts"=>"YES","device_type"=>$device_type,"registrationid"=>$registrationid);
			}
		}
}
	return $cust_pn_data;
}

/*--------------CRON PROCESS NAMES--------------------*/
$pn_for_updating_material_receive = "pn_for_updating_material_receive";
process_update_tracking($pn_for_updating_material_receive,"START","Process Start");
send_pn_for_update_material_receive(1,$prev_date);

/*-------------------UPDATE ORDER STATUS IN T_APPERPDO TABLE FUNCTION START-------------------------------------------*/
function send_pn_for_update_material_receive($pgno,$prev_date){
$res_msg = array();
$total_added_data = array();
$limit = 1000;
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$pn_for_updating_material_receive = "pn_for_updating_material_receive";
$last_updateds = date("Y-m-d H:i:s");
$pno = $pgno ? $pgno : 1;
$start_from = (($pno-1)*$limit);
$DO_approval_array=array();
if($pno!=''){
$sql1 = "SELECT `id`,`CHALLANNO`,`dns_customer_code`,DATE_FORMAT(STR_TO_DATE(`CHALLANDT`,'%m/%d/%Y'),'%Y-%m-%d') as `the_challan_date` FROM $t_dochallan having `the_challan_date`='".$prev_date."' order by `id` asc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
	$the_ch_no = $row1["CHALLANNO"] ? addslashes(trim($row1["CHALLANNO"])) : "";
	$dns_customer_code = $row1["dns_customer_code"] ? addslashes(trim($row1["dns_customer_code"])) : "";
	$the_challan_date = $row1["the_challan_date"] ? addslashes(trim($row1["the_challan_date"])) : "";
	if($the_ch_no!="" && $the_challan_date!="" && $dns_customer_code!=""){
		$the_pn_data = get_pn_data_by_dns_customer_code($dns_customer_code);
		if($the_pn_data["sts"=="YES"]){
			$cust_device_type = $the_pn_data["device_type"];
			$cust_registrationid = $the_pn_data["registrationid"];
			$the_challan_date_show = date("jS M, Y",strtotime($the_challan_date));
$android_message = array();
$curr_timestamp = date('Y-m-d H:i:s');
$curr_date_time = date("Y-m-d H:i:s");			
$nt_title = "Star Saathi";
$the_message = "Receiving for ".$the_ch_no." dated ".$the_challan_date_show." is pending. Please submit";
$payload = array();
$payload['team'] = 'India';
$payload['score'] = '5.6';
$app_noty_image = "";	
$android_message['data'] = array("title"=>$nt_title,"is_background"=>FALSE,"message"=>$the_message,"image"=>$app_noty_image,"payload"=>$payload,"timestamp"=>$curr_timestamp);			
$body['aps'] = array("alert"=> array('body'=>$noty_msg),"sound"=>"default");
			
			if($cust_device_type=="IOS"){
				$sent_sts_prod = pushNotificationInIOS("",$the_message,$nt_title,$cust_registrationid,"");
			//$sent_sts_dev = pushNotificationInIOS("development",$the_message,$nt_title,$cust_registrationid,"");
			}else if($cust_device_type=="ANDROID"){
				$sent_sts = send_push_notification_in_android($cust_registrationid,$android_message);
			}
		}
	}
	
	
	}
$newpgno = ($pno+1);
send_pn_for_update_material_receive($newpgno,$prev_date);	
}else{
	process_update_tracking($pn_for_updating_material_receive,"END","Process End");
}
}
return;
}
/*-------------------UPDATE ORDER STATUS IN T_APPERPDO TABLE FUNCTION END-------------------------------------------*/
//send_process_complete_mail();
$res_data = array("process_status"=>"YES","process_message"=>"DONE");
echo json_encode($res_data);
include "cron_page_end.php";
mysql_close();
?>
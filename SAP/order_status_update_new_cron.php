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
$curr_date_time = date("Y-m-d H:i:s");
$prev_date = date('Y-m-d',strtotime("-12 days"));
$res_data = array();
$auto_notification_log="auto_notification_log";
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

$server_url1 = "https://" . $_SERVER['SERVER_NAME']."/";
$the_m_image_link = $server_url1."SAP/admin/images/logo.png";
/*$sql1 = "SELECT `id`,`APPORDERNO`,`ERPORDERNO`,`STATUS`,`QTY`,`customer_code`,`dns_customer_code`,`prod_code`,`dns_prod_code`,`freight`,`destination_code`,`destination_name` FROM $t_apperpdo where `STATUS` in('Order authorized','Order received','CREDIT CHECK FAILED') and `APPORDERNO`!='' AND SUBSTRING(`order_date`,1,10) >='2022-12-01' AND `ERPORDERNO`='' order by `id` asc";*/
$curr_date = date("Y-m-d");
$prev_date_val = date('Y-m-d', strtotime("-7 days,$curr_date"));
$sql1 = "SELECT `id`,`APPORDERNO`,`ERPORDERNO`,`STATUS`,`QTY`,`customer_code`,`dns_customer_code`,`prod_code`,`dns_prod_code`,`freight`,`destination_code`,`destination_name` FROM $t_apperpdo where `STATUS` in('Order authorized','Order received','CREDIT CHECK FAILED') and `APPORDERNO`!='' AND `STATUS`!='Order canceled' AND SUBSTRING(`order_date`,1,10) >'$prev_date_val'  order by `id` asc";
//echo"<pre>";print_r($sql1);die;
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
	$the_id = $row1["id"] ? addslashes(trim($row1["id"])) : "";
	$the_app_ord_no = $row1["APPORDERNO"] ? addslashes(trim($row1["APPORDERNO"])) : "";
	$the_app_ord_status = $row1["STATUS"] ? addslashes(trim($row1["STATUS"])) : "";
	$the_qty = trim($row1["QTY"]);
	$the_customer_code = $row1["customer_code"] ? addslashes(trim($row1["customer_code"])) : "";
	$the_dns_customer_code = $row1["dns_customer_code"] ? addslashes(trim($row1["dns_customer_code"])) : "";
	$customer_name = get_customer_name_by_dealer_id($the_dns_customer_code);

	
	$the_prod_code = $row1["prod_code"] ? addslashes(trim($row1["prod_code"])) : "";
	$the_dns_prod_code = $row1["dns_prod_code"] ? addslashes(trim($row1["dns_prod_code"])) : "";
	$the_freight = $row1["freight"] ? addslashes(trim($row1["freight"])) : "";
	$the_destination_code = $row1["destination_code"] ? trim($row1["destination_code"]) : "";
	$the_destination_name = $row1["destination_name"] ? trim($row1["destination_name"]) : "";
$pn_text_title = "";
$pn_text_msg = "";
	if($the_app_ord_no!=""){
		$starfiori_port_no = $GLOBALS['starfiori_port_no'];
	/*$url_ck1 = 'https://prdapp1.starcement.co.in:44310/sap/opu/odata/sap/ZSD_PARKLOT_SO_SERV_CDS/ZSD_PARKLOT_SO_SERV(\''.$the_app_ord_no.'\')?$format=json&sap-client=900';*/
	echo $url_ck1 = 'https://starfiori.starcement.co.in:'.$starfiori_port_no.'/sap/opu/odata/sap/ZSD_PARKLOT_SO_SERV_CDS/ZSD_PARKLOT_SO_SERV(\''.$the_app_ord_no.'\')?$format=json&sap-client=900';
	$body_for_mcode1 = get_data_from_cserver($url_ck1); 
	if(isJsonCk($body_for_mcode1)){
	$json_decoded = json_decode($body_for_mcode1,true);
	if(count($json_decoded)>0){
	if(array_key_exists("d",$json_decoded)){
	$app_ref_no = $json_decoded["d"]["app_ref_no"];
	$material = $json_decoded["d"]["material"];
	$so_num = $json_decoded["d"]["so_num"];
	$erdat = $json_decoded["d"]["erdat"];
	$erdat_date_format = "";
	if($erdat!=""){
	$erdat_str = str_replace("/","",$erdat);
	$erdat_str = str_replace("Date","",$erdat_str);	
	$erdat_str = str_replace("(","",$erdat_str);
	$erdat_str = str_replace(")","",$erdat_str);
	$erdat_str = ($erdat_str / 1000);
	//$erdat_date_format = date("Y-m-d H:i:s",$erdat_str);
	$erdat_date_format = date("Y-m-d",$erdat_str);
	}
	$erzet = $json_decoded["d"]["erzet"];
	if($erzet!='')
	{
		$erzet_str = str_replace("PT","",$erzet);
		$erzet_str = str_replace("H","",$erzet_str);
		$erzet_str = str_replace("M","",$erzet_str);
		$erzet_str = str_replace("S","",$erzet_str);
	}
	$erdat_date_format=$erdat_date_format.' '.$erzet_str;
	$erdat_date_format = date("Y-m-d H:i:s",strtotime($erdat_date_format));
	$upd_tmstmp = $json_decoded["d"]["upd_tmstmp"];
	$upd_tmstmp_format = "";
	if($upd_tmstmp!=""){
	$upd_tmstmp_str = str_replace("/","",$upd_tmstmp);
	$upd_tmstmp_str = str_replace("Date","",$upd_tmstmp_str);	
	$upd_tmstmp_str = str_replace("(","",$upd_tmstmp_str);
	$upd_tmstmp_str = str_replace(")","",$upd_tmstmp_str);
	$upd_tmstmp_str = ($upd_tmstmp_str / 1000);
	$upd_tmstmp_format = date("Y-m-d H:i:s",$upd_tmstmp_str);
	}
	$kwmeng = $json_decoded["d"]["kwmeng"];
	$vrkme = $json_decoded["d"]["vrkme"];
	$vstel = $json_decoded["d"]["vstel"];
	$status_val = trim($json_decoded["d"]["status"]);
	$remarks = trim($json_decoded["d"]["remarks"]);
	if($so_num!=""){
		$is_sent_pn = "NO";
		if($remarks==""){
		$upd_sts_qry = ",`STATUS`='DO approved',`sale_order_prod_code`='$material',`sale_order_qty`='$kwmeng',`sale_order_dump`='$vstel'";
		$is_sent_pn = "YES";
		$the_prod_name = show_prod_details_by_dns_prod_id($the_dns_prod_code);
		if($the_prod_name==""){
			$the_prod_name = show_prod_name_by_prod_id($the_prod_code);
		}
		$pn_text_title = "STAR SAATHI";
		$pn_text_msg = "DO Approved for ".$the_qty." MT ".$the_prod_name." ".$the_destination_name." ".$the_freight;
		
		}else if(strtolower($remarks)=="credit check failed"){
		$upd_sts_qry = ",`STATUS`='CREDIT CHECK FAILED'";
		if($the_app_ord_status=="CREDIT CHECK FAILED"){
			$is_sent_pn = "NO";
		}else{		
		$is_sent_pn = "YES";
		}
		$pn_text_title = "STAR SAATHI";
		$pn_text_msg = "Order no ".$the_app_ord_no." not placed due to credit check failed. Please make payment.";
		
		}else{
		$upd_sts_qry = "";
		}
		$sql_upd = "update $t_apperpdo set `ERPORDERNO`='$so_num',`ERPORDERDT`='$erdat_date_format'$upd_sts_qry where `APPORDERNO`='$the_app_ord_no' ";
		$res_upd = mysql_query($sql_upd);
		$the_device_type = "";
		$the_registrationid = "";
		/*if($is_sent_pn=="YES"){
			$dlr_pn_data = show_dealer_pn_details_by_dealer_id($the_dns_customer_code);
			if($dlr_pn_data["sts"]=="YES"){
				$the_device_type = $dlr_pn_data["device_type"];
				$the_registrationid = $dlr_pn_data["registrationid"];
			}else{
				$dlr_pn_data = show_dealer_pn_details_by_cust_id($the_customer_code);
				if($dlr_pn_data["sts"]=="YES"){
				$the_device_type = $dlr_pn_data["device_type"];
				$the_registrationid = $dlr_pn_data["registrationid"];
				}
			}
			
			if($the_device_type!="" && $the_registrationid!=""){
				
				if($the_device_type=="IOS"){
				$sent_sts_prod = pushNotificationInIOS("",$pn_text_msg,$pn_text_title,$the_registrationid,$the_m_image_link);
				$sent_sts_dev = pushNotificationInIOS("development",$pn_text_msg,$pn_text_title,$the_registrationid,$the_m_image_link);
				
				$sent_sts_ios_pn = $sent_sts_prod;
				$error_message_ios_pn = '';
				//$sent_sts_dev = pushNotificationInIOS("development",$the_pn_msg,$nt_title,$the_noty_data["reg_id"],$the_m_image_link);
				if($sent_sts_ios_pn=="TRUE"){
				$pn_sent_remark_ios = "Notification sent.";
				}else{
				$pn_sent_remark_ios = $error_message;	
				}
				$sent_datetime = date("Y-m-d H:i:s");
				
				$sql_msglog = "insert into $auto_notification_log (`cust_dns_code`,`cust_name`,`message_type`,`message_sent_status`,`error_message`,`device_type`,`message_text`,`sent_datetime`) values ('$the_dns_customer_code','$customer_name','PN','$sent_sts_ios_pn','$error_message_ios_pn','IOS','".addslashes($pn_text_msg)."','$sent_datetime')";
				$res_msglog = mysql_query($sql_msglog);
				
				}else if($the_device_type=="ANDROID"){
					
				$android_message = array();
				$curr_timestamp = date('Y-m-d H:i:s');
				$curr_date_time = date("Y-m-d H:i:s");
				$nt_title = $pn_text_title;
				$the_message = $pn_text_msg;
				$payload = array();
				$payload['team'] = 'India';
				$payload['score'] = '5.6';
				$app_noty_image = "";	
				$android_message['data'] = array("title"=>$nt_title,"is_background"=>FALSE,"message"=>$the_message,"image"=>$the_m_image_link,"payload"=>$payload,"timestamp"=>$curr_timestamp);
					
							
				$sent_sts = send_push_notification_in_android($the_registrationid,$android_message);
				$sent_sts_pn = $sent_sts;
				$error_message ='';
				if($sent_sts_pn=="TRUE"){
					$pn_sent_remark = "Notification sent.";
				}else{
					$pn_sent_remark = $error_message;	
				}
				$sent_datetime = date("Y-m-d H:i:s");

				
				$sql_msglog = "insert into $auto_notification_log (`cust_dns_code`,`cust_name`,`message_type`,`message_sent_status`,`error_message`,`device_type`,`message_text`,`sent_datetime`) values ('$the_dns_customer_code','$customer_name','PN','$sent_sts_pn','$error_message','ANDROID','".addslashes($pn_text_msg)."','$sent_datetime')";
				$res_msglog = mysql_query($sql_msglog);
							
				}
			
			}
			
			
		}*/
		
		
		
	}
	
	}
	
	}
	}
	}
	
	
	}
}
$res_data = array("process_status"=>"YES","process_message"=>"DONE");
echo json_encode($res_data);
mysql_close();

?>
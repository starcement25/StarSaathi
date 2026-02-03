<?php
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
date_default_timezone_set('Asia/Kolkata');
include "../star_connection.php";
include '../admin/APNSBase.php';
include '../admin/APNotification.php';
include '../admin/APFeedback.php';
include('Crypto.php');

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

function pushNotificationInIOS_custom_development($deviceToken,$noty_title,$noty_message){
	$today_date = date("Y_m_d");
	$notification2 = new APNotification('development');
	$notification2->setDeviceToken($deviceToken);
	$notification2->setTitle($noty_title);
	$notification2->setBadge(1);
	$notification2->setMessage($noty_message);
	$notification2->setSound("default");
	$notification2->setPrivateKey('../admin/ss_dev.pem');
	$notification2->setPrivateKeyPassphrase('C0rali0s');
	$res = $notification2->send();
	$notification2 = NULL;
    if($res){
		return "TRUE";
	}else{
		return "FALSE";
	}    
}

function pushNotificationInIOS_custom_production($deviceToken,$noty_title,$noty_message){
	$today_date = date("Y_m_d");
	$notification2 = new APNotification('production');
	$notification2->setDeviceToken($deviceToken);
	$notification2->setTitle($noty_title);
	$notification2->setBadge(1);
	$notification2->setMessage($noty_message);
	$notification2->setSound("default");
	$notification2->setPrivateKey('../admin/ss_dis.pem');
	$notification2->setPrivateKeyPassphrase('C0rali0s');
	$res = $notification2->send();
	$notification2 = NULL;
    if($res){
		return "TRUE";
	}else{
		return "FALSE";
	}    
}


$ledger_transaction_table = "ledger_transaction_table";
$customer_master = "customer_master";
$changepassword = "changepassword";
error_reporting(0);
$workingKey='324077D9320ADDFBBC0607E18D7FBE4C';		//Working Key should be provided here.
$encResponse=$_POST["encResp"];			//This is the response sent by the CCAvenue Server
$rcvdString=decrypt($encResponse,$workingKey);		//Crypto Decryption used as per the specified working key.
$order_status="";
$order_id = "";
$tracking_id = "";
$payment_mode = "";
$card_name = "";
$bank_ref_no = "";
$failure_message = "";
$status_message = "";
$tran_datetime = "";
$bin_country = "";
$response_code= "";
$status_code= "";
$currency= "";
$vault= "";
$offer_type= "";
$offer_code= "";
$discount_value= "";
$mer_amount= "";
$eci_value= "";
$retry= "";


$decryptValues=explode('&', $rcvdString);
$dataSize=sizeof($decryptValues);

for($i = 0; $i < $dataSize; $i++) 
{
$information=explode('=',$decryptValues[$i]);
if($i==3){	$order_status=$information[1]; }
if($information[0]=="order_id"){ $order_id = $information[1]; }
if($information[0]=="tracking_id"){ $tracking_id = $information[1]; }
if($information[0]=="payment_mode"){ $payment_mode = $information[1]; }
if($information[0]=="card_name"){ $card_name = $information[1]; }
if($information[0]=="bank_ref_no"){ $bank_ref_no = $information[1]; }
if($information[0]=="failure_message"){ $failure_message = $information[1]; }
if($information[0]=="status_message"){ $status_message = $information[1]; }
if($information[0]=="trans_date"){ $tran_datetime = $information[1]; }
if($information[0]=="bin_country"){ $bin_country = trim($information[1]); }

if($information[0]=="response_code"){ $response_code = trim($information[1]); }
if($information[0]=="status_code"){ $status_code = trim($information[1]); }
if($information[0]=="currency"){ $currency = trim($information[1]); }
if($information[0]=="vault"){ $vault = trim($information[1]); }
if($information[0]=="offer_type"){ $offer_type = trim($information[1]); }
if($information[0]=="offer_code"){ $offer_code = trim($information[1]); }
if($information[0]=="discount_value"){ $discount_value = trim($information[1]); }
if($information[0]=="mer_amount"){ $mer_amount = trim($information[1]); }
if($information[0]=="eci_value"){ $eci_value = trim($information[1]); }
if($information[0]=="retry"){ $retry = trim($information[1]); }


}

if($currency==""){
	$currency = "INR";
}

if($order_id!=""){
	$sql_upd = "update $ledger_transaction_table set `order_status`='$order_status',`tracking_id`='$tracking_id',`payment_mode`='$payment_mode',`card_name`='$card_name',`bank_ref_no`='$bank_ref_no',`failure_message`='$failure_message',`status_message`='$status_message',`tran_datetime`='$tran_datetime',`bin_country`='$bin_country',`response_code`='$response_code',`status_code`='$status_code',`currency`='$currency',`vault`='$vault',`offer_type`='$offer_type',`offer_code`='$offer_code',`discount_value`='$discount_value',`mer_amount`='$mer_amount',`eci_value`='$eci_value',`retry`='$retry' where `lt_order_id`='$order_id' and `order_status`='Pending'";
	$res_upd = mysql_query($sql_upd);
	if(strtolower($order_status)=="success"){
		$sms_message = "Your ledger transaction is successfull.";
		$sql_cc = "select `customer_code` from $ledger_transaction_table where `lt_order_id`='$order_id'";
		$res_cc = mysql_query($sql_cc);
		$row_cc = mysql_fetch_assoc($res_cc);
		$the_customer_code = $row_cc["customer_code"] ? trim($row_cc["customer_code"]) : "";
		if($the_customer_code!=""){
		
		$sql_cpn = "select `registrationid`,`device_type` from $changepassword where `customer_code`='$the_customer_code'";
		$res_cpn = mysql_query($sql_cpn);
		$row_cpn = mysql_fetch_assoc($res_cpn);
		$registrationid = $row_cpn["registrationid"] ? trim($row_cpn["registrationid"]) : "";
		$device_type = $row_cpn["device_type"] ? trim($row_cpn["device_type"]) : "";
		if($device_type!="" && $registrationid!=""){
			
		$android_message = array();
		$curr_timestamp = date('Y-m-d H:i:s');
		$curr_date_time = date("Y-m-d H:i:s");
		$nt_title = "Ledger transaction";
		$payload = array();
		$payload['team'] = 'India';
		$payload['score'] = '5.6';
		$app_noty_image = "";
		$the_message = "Your ledger transaction is successfull.";
		$android_message['data'] = array("title"=>$nt_title,"is_background"=>FALSE,"message"=>$the_message,"image"=>$app_noty_image,"payload"=>$payload,"timestamp"=>$curr_timestamp);			
		$body['aps'] = array("alert"=> array('body'=>$noty_msg),"sound"=>"default");
		if($device_type=="IOS"){
		$sent_sts_prod = pushNotificationInIOS_custom_production($registrationid,$nt_title,$the_message);
		$sent_sts_dev = pushNotificationInIOS_custom_development($registrationid,$nt_title,$the_message);
		
		}else if($device_type=="ANDROID"){			
		$sent_sts = send_push_notification_in_android($registrationid,$android_message);
					
		}
			
		}
		
		$sql_cp = "select `phone_no` from $customer_master where `customer_code`='$the_customer_code'";
		$res_cp = mysql_query($sql_cp);
		$row_cp = mysql_fetch_assoc($res_cp);
		$phonenumber = $row_cp["phone_no"] ? trim($row_cp["phone_no"]) : "";
		if($phonenumber!=""){
		$lipl_uri = "http://www.myvaluefirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to=".$phonenumber."&from=starcm&text=".urlencode($sms_message)."&dlr-mask=19&dlr-url";
		//$lipl_uri = str_replace(" ", '%20', $lipl_uri);
		$lipl_ch = curl_init();
		curl_setopt($lipl_ch, CURLOPT_URL, $lipl_uri);
		curl_setopt($lipl_ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($lipl_ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($lipl_ch, CURLOPT_HEADER,0);
		curl_setopt($lipl_ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($lipl_ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
		$lipl_return_val = curl_exec($lipl_ch);
		curl_close($lipl_ch);
		}
		}
		
		
	}
}

echo "payment_".$order_status;
exit;

/*echo "<table cellspacing=4 cellpadding=4>";
for($i = 0; $i < $dataSize; $i++) 
{
$information=explode('=',$decryptValues[$i]);
echo '<tr><td>'.$information[0].'</td><td>'.$information[1].'</td></tr>';
}

echo "</table><br>";
echo "</center>";*/
?>

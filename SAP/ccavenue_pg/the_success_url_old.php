<?php
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
date_default_timezone_set('Asia/Kolkata');
include '../star_connection.php';
include('Crypto.php');
$ledger_transaction_table = "ledger_transaction_table";
$customer_master = "customer_master";
error_reporting(0);
$order_res_arr = array();
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
	
	$workingKey='3D1F16BEE3CF170414FEB722F34A00E2';		//Working Key should be provided here.
	//$the_orderNo=$_REQUEST["orderNo"] ? addslashes(trim($_REQUEST["orderNo"])) : "";
	$encResponse=$_REQUEST["encResp"];			//This is the response sent by the CCAvenue Server
	$rcvdString=decrypt($encResponse,$workingKey);		//Crypto Decryption used as per the specified working key.
	$order_status="";
	$decryptValues=explode('&', $rcvdString);
	$dataSize=sizeof($decryptValues);
	echo "<center>";

	for($i = 0; $i < $dataSize; $i++) 
	{
		$information=explode('=',$decryptValues[$i]);
		$order_res_arr[$information[0]] = $information[1];
		if($i==3)	$order_status=$information[1];
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
	$payment_status = strtoupper($order_status);
	$sql_upd = "update $ledger_transaction_table set `order_status`='$order_status',`tracking_id`='$tracking_id',`payment_mode`='$payment_mode',`card_name`='$card_name',`bank_ref_no`='$bank_ref_no',`failure_message`='$failure_message',`status_message`='$status_message',`tran_datetime`='$tran_datetime',`bin_country`='$bin_country',`response_code`='$response_code',`status_code`='$status_code',`currency`='$currency',`vault`='$vault',`offer_type`='$offer_type',`offer_code`='$offer_code',`discount_value`='$discount_value',`mer_amount`='$mer_amount',`eci_value`='$eci_value',`retry`='$retry'$ordr_sts_qry where `lt_order_id`='$order_id'";
	$res_upd = mysql_query($sql_upd);
		
}

	if($order_status==="Success")
	{
		echo "<br>Thank you for shopping with us. Your credit card has been charged and your transaction is successful. We will be shipping your order to you soon.";
		
	}else if($order_status==="Initiated")
	{
		echo "<br>The transaction is initiated.";
		
	}
	else if($order_status==="Aborted")
	{
		echo "<br>The transaction is aborted.";
	
	}
	else if($order_status==="Failure")
	{
		echo "<br>The transaction has been declined.";
	}
	else
	{
		echo "<br>Security Error. Illegal access detected";
	
	}

	echo "<br><br>";

	/*echo "<table cellspacing=4 cellpadding=4>";
	for($i = 0; $i < $dataSize; $i++) 
	{
		$information=explode('=',$decryptValues[$i]);
	    	echo '<tr><td>'.$information[0].'</td><td>'.$information[1].'</td></tr>';
	}

	echo "</table><br>";*/
	echo "</center>";
?>

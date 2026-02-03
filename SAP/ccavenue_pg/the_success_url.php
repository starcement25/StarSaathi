<?php
set_time_limit(0);
//error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
date_default_timezone_set('Asia/Kolkata');
include '../star_connection.php';
include "../function-sfa.php";
include('Crypto.php');
$ledger_transaction_table = "ledger_transaction_table";
$customer_master = "customer_master";
//error_reporting(0);
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
	
	$workingKey='3D1F16BEE3CF170414FEB722F34A00E2';
	//$workingKey='8DC5475A737EB3055F5BDDD6C1212E0C';
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
		//if($i==3)	$order_status=$information[1];
if($information[0]=="order_status"){ $order_status = $information[1]; }
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
//$order_id='657';
//$order_status='Success';
if($order_id!=""){
	$payment_status = strtoupper($order_status);
	$sql_upd = "update $ledger_transaction_table set `order_status`='$order_status',`tracking_id`='$tracking_id',`payment_mode`='$payment_mode',`card_name`='$card_name',`bank_ref_no`='$bank_ref_no',`failure_message`='$failure_message',`status_message`='$status_message',`tran_datetime`='$tran_datetime',`bin_country`='$bin_country',`response_code`='$response_code',`status_code`='$status_code',`currency`='$currency',`vault`='$vault',`offer_type`='$offer_type',`offer_code`='$offer_code',`discount_value`='$discount_value',`mer_amount`='$mer_amount',`eci_value`='$eci_value',`retry`='$retry' where `lt_order_id`='$order_id'";
	$res_upd = mysql_query($sql_upd);
	
if($order_status==="Success"){
$cust_email = "";	
$sql_em = "select `customer_code` from $ledger_transaction_table where `lt_order_id`='$order_id'";
$res_em = mysql_query($sql_em);
$totres_em = mysql_num_rows($res_em);
	if($totres_em>0){
		$row_em=mysql_fetch_assoc($res_em);
		$the_customer_code = $row_em["customer_code"];
		$sql_cust_ck = "select `email`,`dns_customer_code` from $customer_master where `customer_code`='$the_customer_code'";
		$res_cust_ck = mysql_query($sql_cust_ck);
		$totres_cust_ck = mysql_num_rows($res_cust_ck);
		if($totres_cust_ck>0){
		$row_cust_ck=mysql_fetch_assoc($res_cust_ck);
		$cust_dns_customer_code = trim($row_cust_ck["dns_customer_code"]);
		$cust_email = trim($row_cust_ck["email"]);
		/*if($cust_dns_customer_code=="WBB037"){
		if($cust_email!=""){
		$subject = "THIS IS A TEST PAYMENT GENERATED EMAIL";
		$bodyml = "THIS IS A TEST PAYMENT GENERATED EMAIL.<br>";
		$cres = send_the_mail($cust_email,$subject,$bodyml);	
		}
		}*/
	}
 }
 	$sql_sel = "SELECT tran_datetime,payment_mode,mobile,email,amount,bank_ref_no FROM $ledger_transaction_table where `lt_order_id`='$order_id'";
	$res_sel = mysql_query($sql_sel);
	$row_sel=mysql_fetch_array($res_sel);
	
	$tran_datetime=$row_sel['tran_datetime'];
	$payment_mode=$row_sel['payment_mode'];
	$mobile=$row_sel['mobile'];
	$email=$row_sel['email'];
	$amount=$row_sel['amount'];
	$bank_ref_no=$row_sel['bank_ref_no'];
}		
}
	if($order_status==="Success")
	{
		echo "<br>Thank you . Your transaction is successful.";
		
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
	define("SERVERREMOTE","103.242.119.68");
	define("USERREMOTE","acedns_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234#");
	define("DBREMOTE","acedns_STAR");
		
	$link=mysql_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die("Database Connection Error.");
	mysql_select_db(DBREMOTE,$link) or die("could not connect the database");
	if($order_status==="Success"){
		$sqlcustcode="SELECT customer_code,branch_code,customer_name,address FROM customer_master WHERE dns_customer_code='".$cust_dns_customer_code."'";
		$rscustcode=mysql_query($sqlcustcode,$link);
		$rowcustcode=mysql_fetch_array($rscustcode);
		$customer_code = $rowcustcode['customer_code'];
		$branch_code = $rowcustcode['branch_code'];
		$customer_name=$rowcustcode['customer_name'];
		$address_dealer=$rowcustcode['address'];
		$sqlbranch = "select branch_name from branch_master where branch_code='".$branch_code."'";	
		$resbranch = mysql_query($sqlbranch);
		$rowbranch=mysql_fetch_array($resbranch);
		$branch_name=$rowbranch['branch_name'];
		$ownempmailstring='';
		$reportingmailstring='';
		$email_hierarchy='';
		$registrationid_array=array();
		$emp_code_array=array();
		$sqldealeremp="SELECT emp_code FROM customer_route_emp_relation WHERE acedns='Y' AND customer_code='".$customer_code."'";
		$rsdealeremp=mysql_query($sqldealeremp,$link);
		while($rowdealeremp=mysql_fetch_array($rsdealeremp))
		{
			$emp_code_db=$rowdealeremp['emp_code'];
			$employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code_db);
		//$emp_hierarchy_condition='c1.emp_code IN('.$employee_hierarchy.')';
			$sqlemailhierarchy="SELECT email,designation FROM employee_master WHERE emp_code IN (".$employee_upper_hierarchy.") AND UPPER(sale_access)='PRIMARY' AND acedns='Y'";
			$rsemailhierarchy=mysql_query($sqlemailhierarchy);
			while($rowemailhierarchy=mysql_fetch_array($rsemailhierarchy))
			{
				$email_hierarchy=$email_hierarchy.$rowemailhierarchy['email'].',';
			}
			/*$sqlregistrationid="SELECT registrationid FROM changepassword WHERE emp_code='".$emp_code_db."' AND 
			emp_code IN(SELECT emp_code FROM employee_master WHERE UPPER(sale_access)='PRIMARY' AND acedns='Y') ";
			$rsregistrationid=mysql_query($sqlregistrationid,$link);
			$rowregistrationid=mysql_fetch_array($rsregistrationid);
			$registrationid=$rowregistrationid['registrationid'];
			if($registrationid!='')
			{
				if(!in_array($registrationid,$registrationid_array))
				{
					array_push($registrationid_array,$registrationid);
					array_push($emp_code_array,$emp_code_db);
				}
			}*/
		}
		if($cust_email!='')
		{
			$final_email=substr($email_hierarchy,0,-1).','.$cust_email.','.'manishranjan@starcement.co.in'.','.'dipankarc@coral.in';
		}
		else
		{
			$final_email=substr($email_hierarchy,0,-1).','.'manishranjan@starcement.co.in'.','.'dipankarc@coral.in';
		}
		$subject = "PAYMENT GENERATED FROM ".strtoupper($customer_name)." ";
		$message="PAYMENT GENERATED FROM $customer_name\n";
		$message.= '<br><br><b>Date: </b> '.$tran_datetime.'<br>
					<b>Branch: </b> '.$branch_name.'<br>
					<b>Dealer name: </b> '.$customer_name.'<br>
					<b>Dealer code: </b> '.$cust_dns_customer_code.'<br>
					<b>Phone No.: </b> '.$mobile.'<br>
					<b>Email: </b> '.$email.'<br>
					<b>Amount: </b> '.number_format($amount,2, '.', '').'<br>
					<b>Payment method: </b> '.$payment_mode.'<br>
					<b>Bank Ref no/UTR: </b> '.$bank_ref_no.'<br><br>';
		$message.= 'Payment will reflect in ledger within 24 hours.';			
		$send_mail = send_the_mail($final_email,$subject,$message);
	}
	echo "</center>";
?>

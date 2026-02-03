<?php
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
date_default_timezone_set('Asia/Kolkata');
include "../star_connection.php";
$t_main_order_pop = "T_MAIN_ORDER_POP";
$customer_master = "customer_master";
include('Crypto.php');
$orderId = $_REQUEST["order_id"] ? trim($_REQUEST["order_id"]) : "";
if($orderId==""){
echo "Something went wrong. Please try later.";
exit;	
}
$sub_total_order_price = "";
$cust_name = "";
$cust_name_val = "";
$cust_address = "";
$cust_pin = "";
$cust_phone = "";
$dealer_code = "";
$payment_option_arr["creditcard"] = "OPTCRDC";
$payment_option_arr["debitcard"] = "OPTDBCRD";
$payment_option_arr["netbanking"] = "OPTNBK";
//$payment_option_arr["Cash Card"] = "OPTCASHC";
//$payment_option_arr["Mobile Payments"] = "OPTMOBP";
$payment_option_arr["upi"] = "OPTUPI";
$sql_fetch = "select * from $t_main_order_pop where `order_id`='$orderId'";
$res_fetch = mysql_query($sql_fetch);
$tot_res_fetch = mysql_num_rows($res_fetch);
if($tot_res_fetch>0){
$row_fetch = mysql_fetch_assoc($res_fetch);
$the_amount = $row_fetch["amount"];
$customer_name = $row_fetch["customer_name"];
$dealer_code = $row_fetch["dns_customer_code"];
$dealer_sap_code = $row_fetch["customer_sap_code"] ? trim($row_fetch["customer_sap_code"]) : "";
$customer_address = $row_fetch["address"];
$customer_mobile = $row_fetch["mobile"] ? trim($row_fetch["mobile"]) : "";
$customer_email = $row_fetch["email"] ? trim($row_fetch["email"]) : "";
$payment_by = $row_fetch["payment_by"] ? trim($row_fetch["payment_by"]) : "";
}else{
echo "The order doesn't exist.";
exit;	
}
$merchant_id = "2308286";
$working_key='FF9525898F3AA1A2A1202798734257C9';
$access_code='AVJB50KD09AZ38BJZA';


$randno = rand(1,9).rand(0,9).rand(0,9).rand(1,9).rand(1,9);
$the_tid = time().$randno;
$order_param_data = array();
$order_param_data["merchant_id"] = $merchant_id;
$order_param_data["language"] = "EN";
$order_param_data["tid"] = $the_tid;
$order_param_data["order_id"] = $orderId;
$order_param_data["amount"] = $the_amount;
$order_param_data["currency"] = "INR";
$order_param_data["redirect_url"] = "https://starsaathi.com/SAP/ccavenue_pg/the_pop_success_url_ne.php";
$order_param_data["cancel_url"] = "https://starsaathi.com/SAP/ccavenue_pg/the_pop_cancel_url_ne.php";
$order_param_data["billing_name"] = $customer_name;
$order_param_data["billing_tel"] = $customer_mobile;
$order_param_data["billing_email"] = $customer_email;
$order_param_data["billing_address"] = $customer_address;
$order_param_data["merchant_param1"] = $dealer_sap_code;
$order_param_data["merchant_param2"] = $customer_name;
$order_param_data["merchant_param3"] = $customer_mobile;
if($payment_by!=""){
	if(array_key_exists($payment_by,$payment_option_arr)){
		$payment_option_val = $payment_option_arr[$payment_by];
		$order_param_data["payment_option"] = $payment_option_val;
	}
}
?>
<html>
<head>
<title>Make Payment</title>
</head>
<body onLoad="submitCCForm()">
<center>
<h2>Please wait....</h2>
<?php
$merchant_data='';
foreach ($order_param_data as $key => $value){
$merchant_data.=$key.'='.$value.'&';
}
$encrypted_data=encrypt($merchant_data,$working_key); // Method for encrypting the data.
// https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction
// https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction
?>
<form method="post" name="redirect" action="https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction"> 
<?php
echo "<input type=hidden name=encRequest value=$encrypted_data>";
echo "<input type=hidden name=access_code value=$access_code>";
?>
</form>
</center>
<script language='javascript'>
function submitCCForm() {
var CCForm = document.forms.redirect;
CCForm.submit();
}
</script>
</body>
</html>

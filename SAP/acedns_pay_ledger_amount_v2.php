<?php
include "star_connection.php";
$ledger_transaction_table = "ledger_transaction_table";
$customer_master = "customer_master";
$app_service_track_log="app_service_track_log";

$customer_code = $_POST["customer_code"] ? addslashes(trim($_POST["customer_code"])) : "";

function get_customer_name_from_id($cust_id){
	$customer_master = "customer_master";
	$custname = "";
	$cust_id = $cust_id ? addslashes(trim($cust_id)) : "";
	if($cust_id!=''){
		$sqls = "select `customer_name` from $customer_master where `customer_code`='$cust_id'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if($totress>0){
			$rows = mysql_fetch_assoc($ress);
			$custname = $rows["customer_name"] ? trim($rows["customer_name"]) : "";
		}
	}
	return $custname;
}
function show_dns_customer_from_customer_code_code($the_customer_code){
$the_cust_code = "";
$customer_master = "customer_master";
if($the_customer_code!=""){
$sql1 = "select `dns_customer_code` from $customer_master where `customer_code`='$the_customer_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_cust_code = $row1["dns_customer_code"] ? addslashes(trim($row1["dns_customer_code"])) : "";
	}
}

return $the_cust_code;
}


$customer_code = $_POST["customer_code"] ? addslashes(trim($_POST["customer_code"])) : "";
$amount = $_POST["the_amount"] ? addslashes(trim($_POST["the_amount"])) : "";
$payment_by = $_POST["payment_by"] ? addslashes(trim($_POST["payment_by"])) : "";
$order_datetime = date("Y-m-d H:i:s");

if($customer_code!="" && $amount!=""){

$sql_cm = "select * from $customer_master where `customer_code`='$customer_code'";
$res_cm = mysql_query($sql_cm);
$totres_cm = mysql_num_rows($res_cm);
if($totres_cm>0){
$row_cm = mysql_fetch_assoc($res_cm);
$customer_name = $row_cm["customer_name"] ? addslashes(trim($row_cm["customer_name"])) : "";
$dns_customer_code = $row_cm["dns_customer_code"] ? addslashes(trim($row_cm["dns_customer_code"])) : "";
$customer_id = $row_cm["customer_id"] ? addslashes(trim($row_cm["customer_id"])) : "";
$mobile = $row_cm["phone_no"] ? addslashes(trim($row_cm["phone_no"])) : "";
$email = $row_cm["email"] ? addslashes(trim($row_cm["email"])) : "";
$address = $row_cm["address"] ? addslashes(trim($row_cm["address"])) : "";
$zone = $row_cm["zone"] ? trim($row_cm["zone"]) : "";
$region = $row_cm["region"] ? trim($row_cm["region"]) : "";
if($region==""){
$res_data = array("process_status"=>"NO","process_message"=>"Region not set yet.");	
}else{
$amount = round($amount,0);
$sql2 = "insert into $ledger_transaction_table (`dns_customer_code`,`customer_code`,`customer_sap_code`,`customer_name`,`mobile`,`email`,`address`,`amount`,`payment_by`,`order_datetime`) values ('$dns_customer_code','$customer_code','$customer_id','$customer_name','$mobile','$email','$address','$amount','$payment_by','$order_datetime')";
$res2 = mysql_query($sql2);
if($res2){
$the_order_id = mysql_insert_id();
$the_payment_link = "";
$process_status = "YES";
$process_message = "Order successfully generated.";
$region_ck = strtolower($region);
if($region_ck=="ne"){
	//$the_payment_link = $server_url."razorpay_pg/ledger_payment.php?order_id=".$the_order_id;
	$the_payment_link = $server_url."ccavenue_pg/make_ledger_payment_with_ccavenue_ne.php?order_id=".$the_order_id;
}else{

if($region_ck=="roe"){
	$the_payment_link = $server_url."ccavenue_pg/make_ledger_payment_with_ccavenue.php?order_id=".$the_order_id;
}else{
$process_status = "NO";
$process_message = "Unknown region set.";	
}
	
}
$res_data = array("process_status"=>$process_status,"process_message"=>$process_message,"the_order_id"=>$the_order_id,"the_payment_link"=>$the_payment_link);
}else{
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Failed to generate order.");	
}
}


}else{
$res_data = array("process_status"=>"NO","process_message"=>"Your details not found.");
}
}else{	
$res_data = array("process_status"=>"NO","process_message"=>"All fields are mandatory.");
}
echo json_encode($res_data);
mysql_close();
?>
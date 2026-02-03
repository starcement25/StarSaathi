<?php
include "edms_connection.php";
$ledger = "ledger";
$ledger_balance = "ledger_balance";
$verify_ledger_details = "verify_ledger_details";

$customer_code = $_POST["customer_code"] ? addslashes(trim($_POST["customer_code"])) : "";
$dns_customer_code = $_POST["dns_customer_code"] ? addslashes(trim($_POST["dns_customer_code"])) : "";
$total_amount_dr = $_POST["total_amount_dr"] ? addslashes(trim($_POST["total_amount_dr"])) : "";
$total_amount_cr = $_POST["total_amount_cr"] ? addslashes(trim($_POST["total_amount_cr"])) : "";
$comment = $_POST["comment"] ? addslashes(trim($_POST["comment"])) : "";
$status = $_POST["status"] ? addslashes(trim($_POST["status"])) : "REJECTED";
$ledger_year_month_day = $_POST["ledger_year_month_day"] ? addslashes(trim($_POST["ledger_year_month_day"])) : "";

if($customer_code!="" && $dns_customer_code!="" && $status!="" && $ledger_year_month_day!=""){
$current_dt = date("Y-m-d H:m:s");
$sqlall = "select * from $verify_ledger_details where `customer_code`='$customer_code' and `ledger_year_month_day`='$ledger_year_month_day'";
$resall = mysql_query($sqlall);
$totall = mysql_num_rows($resall);
if($totall>0){
	$sql="update $verify_ledger_details set `dns_customer_code`=$dns_customer_code,`total_amount_dr`='$total_amount_dr',`total_amount_cr`='$total_amount_cr',`status`='$status',`saved_datetime`='$current_dt',`comment`='$comment' where `customer_code`='$customer_code' and `ledger_year_month_day`='$ledger_year_month_day'";
	$res=mysql_query($sql);
	$res_data = array("process_status"=>"YES","process_message"=>"Ledger status successfully updated.");
}else{
	$sql="insert into $verify_ledger_details (`customer_code`,`dns_customer_code`,`ledger_year_month_day`,`total_amount_dr`,`total_amount_cr`,`status`,`comment`,`saved_datetime`) values ('$customer_code','$dns_customer_code','$ledger_year_month_day','$total_amount_dr','$total_amount_cr','$status','$comment','$current_dt')";
	$res=mysql_query($sql);
	$res_data = array("process_status"=>"YES","process_message"=>"Ledger status successfully saved.");
}
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"All fields are mandatory.");
}	
echo json_encode($res_data);
mysql_close();
?>
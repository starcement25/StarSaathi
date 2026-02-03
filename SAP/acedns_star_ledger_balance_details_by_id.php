<?php
include "star_connection.php";
$ledger = "ledger";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";
$branch_credit_limit_status = "branch_credit_limit_status";
$ledger_data = array();
$ledger_balance_data = array();
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
if($the_id!=""){
$is_active_branch_credit_limit_process = "NO";
$sql3 = "select `dns_customer_code`,`branch_code` from $customer_master where `customer_code`='$the_id'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
$row3 = mysql_fetch_assoc($res3);
$the_dealer_id = addslashes(trim($row3["dns_customer_code"]));
$the_branch_code = addslashes(trim($row3["branch_code"]));
if($the_branch_code!=""){
$sql_bclck = "select `branch_code` from $branch_credit_limit_status where `branch_code`='$the_branch_code' and `credit_limit_status`='Y'";
$res_bclck = mysql_query($sql_bclck);
$totres_bclck = mysql_num_rows($res_bclck);
if($totres_bclck>0){
$is_active_branch_credit_limit_process = "YES";	
}
}

$dlr_code_qry = "  or `dns_customer_code`='$the_dealer_id'";
$dlr_code_qry2 = "  or `dns_customer_code`='$the_dealer_id'";	
}else{
$dlr_code_qry = "";	
$dlr_code_qry2 = "";
}

$credit_limit = "";
$credit_days = "";
$current_balance = "";
$pending_orders = "";

$sqlall2 = "select * from $ledger_balance where `customer_code`='$the_id' $dlr_code_qry2";
$resall2 = mysql_query($sqlall2);
$totall2 = mysql_num_rows($resall2);
if($totall2>0){
$row112=mysql_fetch_assoc($resall2);
$credit_limit = $row112["credit_limit"] ? trim($row112["credit_limit"]) : "";
$credit_days = $row112["credit_days"] ? trim($row112["credit_days"]) : "";
$current_balance = $row112["current_balance"] ? trim($row112["current_balance"]) : "";
$pending_orders = $row112["pending_orders"] ? trim($row112["pending_orders"]) : "";
}

if($is_active_branch_credit_limit_process=="NO"){
$credit_limit = "NA";
$credit_days = "NA";
$current_balance = "NA";
$pending_orders = "NA";	
}


$res_data = array("process_status"=>"YES","process_message"=>"Success.","credit_limit"=>$credit_limit,"credit_days"=>$credit_days,"current_balance"=>$current_balance,"pending_orders"=>$pending_orders);

}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"The id is mandatory.");
}	
echo json_encode($res_data);
mysql_close();
?>
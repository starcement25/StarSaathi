<?php
include "star_connection.php";
$t_subdealer_order = "T_SUBDEALER_ORDER";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";
$subdealer_order_data = array();
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
$sub_dealer_order_id = $_REQUEST["sub_dealer_order_id"] ? addslashes(trim($_REQUEST["sub_dealer_order_id"])) : "";
$the_status = $_REQUEST["the_status"] ? addslashes(trim($_REQUEST["the_status"])) : "";

if($the_id!="" && $the_status!="" && $sub_dealer_order_id!=""){

$sql3 = "select `dns_customer_code` from $customer_master where `customer_code`='$the_id'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
$row3 = mysql_fetch_assoc($res3);
$the_dealer_id = addslashes(trim($row3["dns_customer_code"]));


$sql_subord_utd = "update $t_subdealer_order set `STATUS`='$the_status' where `sub_dealer_order_id`='$sub_dealer_order_id' and `belong_dealer_dns_code`='$the_dealer_id'";
$res_subord_utd = mysql_query($sql_subord_utd);

$res_data = array("process_status"=>"YES","process_message"=>"Status successfully updated.");

}else{
$res_data = array("process_status"=>"NO","process_message"=>"Dealer details not found.");
}
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"The id is mandatory.");
}	
echo json_encode($res_data);
mysql_close();
?>
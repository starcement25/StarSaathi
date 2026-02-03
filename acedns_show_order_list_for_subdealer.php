<?php
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";
$subdealer_order_data = array();
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
$the_status = $_REQUEST["the_status"] ? addslashes(trim($_REQUEST["the_status"])) : "";
$page_no = $_POST["page_no"] ? $_POST["page_no"] : 1;
$limit = 10;
$start_from = (($page_no-1)*$limit);
if($the_id!=""){
$sql3 = "select `dns_customer_code` from $customer_master where `customer_code`='$the_id'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
$row3 = mysql_fetch_assoc($res3);
$the_sub_dealer_id = addslashes(trim($row3["dns_customer_code"]));

if($the_status!=""){
	$status_qry = " and $t_apperpdo.`STATUS`='$the_status' ";
}else{
	$status_qry = "";
}

$sqlall2 = "select $t_apperpdo.*,$customer_master.`customer_name` from $t_apperpdo left join $customer_master on $t_apperpdo.`dns_sub_dealer_code`=$customer_master.`dns_customer_code`  where $t_apperpdo.`dns_sub_dealer_code`='$the_sub_dealer_id' $status_qry order by $t_apperpdo.`order_date` desc limit $start_from,$limit";
$resall2 = mysql_query($sqlall2);
$totall2 = mysql_num_rows($resall2);
if($totall2>0){
while($row112=mysql_fetch_assoc($resall2)){
$order_id = $row112["APPORDERNO"] ? trim($row112["APPORDERNO"]) : "";
$order_date = $row112["order_date"] ? trim($row112["order_date"]) : "";
$consignee_name = $row112["consignee_name"] ? trim($row112["consignee_name"]) : "";
$consignee_address = $row112["consignee_address"] ? trim($row112["consignee_address"]) : "";
$sub_dealer_code = $row112["sub_dealer_code"] ? trim($row112["sub_dealer_code"]) : "";
$dns_sub_dealer_code = $row112["dns_sub_dealer_code"] ? trim($row112["dns_sub_dealer_code"]) : "";
$belong_dealer_code = $row112["customer_code"] ? trim($row112["customer_code"]) : "";
$belong_dealer_dns_code = $row112["dns_customer_code"] ? trim($row112["dns_customer_code"]) : "";
$prod_code = $row112["prod_code"] ? trim($row112["prod_code"]) : "";
$dns_prod_code = $row112["dns_prod_code"] ? trim($row112["dns_prod_code"]) : "";
$prod_display_name = $row112["prod_display_name"] ? trim($row112["prod_display_name"]) : "";
$qty = $row112["QTY"] ? trim($row112["QTY"]) : "";
$status = $row112["STATUS"] ? trim($row112["STATUS"]) : "";
$subdealer_order_data[] = array("order_id"=>$order_id,"order_date"=>$order_date,"consignee_name"=>$consignee_name,"consignee_address"=>$consignee_address,"sub_dealer_code"=>$sub_dealer_code,"dns_sub_dealer_code"=>$dns_sub_dealer_code,"belong_dealer_code"=>$belong_dealer_code,"belong_dealer_dns_code"=>$belong_dealer_dns_code,"prod_code"=>$prod_code,"dns_prod_code"=>$dns_prod_code,"prod_display_name"=>$prod_display_name,"qty"=>$qty,"status"=>$status);
}
$res_data = array("process_status"=>"YES","process_message"=>"Success.","subdealer_order_data"=>$subdealer_order_data);
}else{
$res_data = array("process_status"=>"NO","process_message"=>"No new order found.");
}
}else{
$res_data = array("process_status"=>"NO","process_message"=>"Dealer details not found.");
}
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"The id is mandatory.");
}	
echo json_encode($res_data);
mysql_close();
?>
<?php
include "star_connection.php";
$arc_consumer_reg = "arc_consumer_reg";
$arc_dealer_cust_point_table = "arc_dealer_cust_point_table";
$the_ac_id = $_POST["the_ac_id"] ? addslashes(trim($_POST["the_ac_id"])) : "";
$sel_arc_status = $_POST["sel_arc_status"] ? addslashes(trim($_POST["sel_arc_status"])) : "PENDING";
$arc_no_of_bags = $_POST["arc_no_of_bags"] ? addslashes(trim($_POST["arc_no_of_bags"])) : "";
$arc_consumer_name = $_POST["arc_consumer_name"] ? addslashes(trim(urldecode($_POST["arc_consumer_name"]))) : "";
if($the_ac_id!="" && $sel_arc_status!=""){
if($sel_arc_status=="PENDING" || $sel_arc_status=="APPROVED" || $sel_arc_status=="REJECT"){
if($arc_no_of_bags==""){
	$res_data = array("process_status"=>"NO","process_message"=>"Please enter the number of bags.");
}else if($arc_no_of_bags<60){
	$res_data = array("process_status"=>"NO","process_message"=>"The number of bags should be greater than equal 60.");
}else if($arc_consumer_name==""){
	$res_data = array("process_status"=>"NO","process_message"=>"Please enter consumer name.");
}else{

$sql_acrc = "select * from $arc_consumer_reg where `ac_id`='$the_ac_id'";
$res_acrc = mysql_query($sql_acrc);
$totres_acrc = mysql_num_rows($res_acrc);
if($totres_acrc>0){
$row_acrc=mysql_fetch_assoc($res_acrc);
$the_status = $row_acrc["status"];
if($the_status=="APPROVED"){
$res_data = array("process_status"=>"NO","process_message"=>"The record has already beed approved.");	
}else if($the_status=="REJECT"){
$res_data = array("process_status"=>"NO","process_message"=>"The record has already beed rejected.");	
}else{
$sql="update $arc_consumer_reg set `status`='$sel_arc_status',`no_of_bags`='$arc_no_of_bags',`name`='$arc_consumer_name' where `ac_id`='$the_ac_id'";
$res=mysql_query($sql);
if($sel_arc_status=="APPROVED"){
$sql_get = "select * from $arc_consumer_reg where `ac_id`='$the_ac_id'";
$res_get = mysql_query($sql_get);
$totres_get = mysql_num_rows($res_get);
if($totres_get>0){
$row_get=mysql_fetch_assoc($res_get);
$customer_code = $row_get["customer_code"] ? trim($row_get["customer_code"]) : "";
$dns_customer_code = $row_get["dns_customer_code"] ? trim($row_get["dns_customer_code"]) : "";
$name = $row_get["name"] ? addslashes(trim($row_get["name"])) : "";
$mobile = $row_get["mobile"] ? trim($row_get["mobile"]) : "";
$no_of_bags = $row_get["no_of_bags"] ? trim($row_get["no_of_bags"]) : "0";
if($no_of_bags==""){
$no_of_bags = "0";	
}

if($customer_code!="" && $mobile!=""){
$sql_cdcp = "select `adcpt_id`,`points` from $arc_dealer_cust_point_table where `customer_code`='$customer_code' and `persone_mobile`='$mobile'";
$res_cdcp = mysql_query($sql_cdcp);
$totres_cdcp = mysql_num_rows($res_cdcp);
if($totres_cdcp>0){
$row_cdcp=mysql_fetch_assoc($res_cdcp);
$adcpt_id = $row_cdcp["adcpt_id"] ? trim($row_cdcp["adcpt_id"]) : "";
$old_points = $row_cdcp["points"] ? trim($row_cdcp["points"]) : "0";
$new_point = ($old_points + $no_of_bags);
$sql_cdcpup = "update $arc_dealer_cust_point_table set `points`='$new_point',`persone_name`='$name',`dns_customer_code`='$dns_customer_code' where `adcpt_id`='$adcpt_id'";
$res_cdcpup = mysql_query($sql_cdcpup);
}else{
$sql_cdcpin = "insert into $arc_dealer_cust_point_table (`customer_code`,`dns_customer_code`,`persone_name`,`persone_mobile`,`points`) values ('$customer_code','$dns_customer_code','$name','$mobile','$no_of_bags')";
$res_cdcpin = mysql_query($sql_cdcpin);
}
}
}
}
$res_data = array("process_status"=>"YES","process_message"=>"Status updated successfully.");
}
}else{
$res_data = array("process_status"=>"NO","process_message"=>"This record not found.");	
}
}
}else{
$res_data = array("process_status"=>"NO","process_message"=>"Only PENDING,APPROVED and REJECT status require.");	
}
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");
}	
echo json_encode($res_data);
mysql_close();
?>
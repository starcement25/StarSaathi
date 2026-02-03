<?php
include "star_connection.php";
$arc_consumer_reg = "arc_consumer_reg";
$login_user_id = $_POST["login_user_id"] ? addslashes(trim($_POST["login_user_id"])) : "";
$user_type = $_POST["user_type"] ? addslashes(trim($_POST["user_type"])) : "";
$mobile = $_POST["mobile"] ? addslashes(trim($_POST["mobile"])) : "";
$name = $_POST["name"] ? addslashes(trim($_POST["name"])) : "";
$bag = $_POST["bag"] ? addslashes(trim($_POST["bag"])) : "";

if($user_type!="" && $login_user_id!=""){
if($user_type=="dealer"){
$customer_data_arr = get_customer_data_check_by_id($login_user_id);
$the_sts = $customer_data_arr["sts"];
$is_branch_arc = $customer_data_arr["is_branch_arc"];
if($the_sts=="YES"){
if($is_branch_arc=="YES"){
$dns_customer_code = $customer_data_arr["dns_customer_code"];

if($mobile==""){
$res_data = array("process_sts"=>"NO","process_msg"=>"Please enter mobile.");	
}else if(strlen($mobile)<10){
$res_data = array("process_sts"=>"NO","process_msg"=>"Please enter 10 digit mobile number.");	
}else if($name==""){
$res_data = array("process_sts"=>"NO","process_msg"=>"Please enter name.");	
}else if($bag==""){
$res_data = array("process_sts"=>"NO","process_msg"=>"Please enter the number of bag.");	
}else if($bag<60){
$res_data = array("process_sts"=>"NO","process_msg"=>"Number of bag should be greater than equal 60.");	
}else{
$curr_datetime = date("Y-m-d H:i:s");
$sql_in = "insert into $arc_consumer_reg (`name`,`mobile`,`no_of_bags`,`customer_code`,`dns_customer_code`,`entry_datetime`,`last_updated_datetime`) values ('$name','$mobile','$bag','$login_user_id','$dns_customer_code','$curr_datetime','$curr_datetime')";
$res_in = mysql_query($sql_in);
if($res_in){
$res_data = array("process_sts"=>"YES","process_msg"=>"SALE REGISTRATION COMPLETED");	
}else{
$res_data = array("process_sts"=>"NO","process_msg"=>"Something went wrong.");	
}	
}
}else{
$res_data = array("process_sts"=>"NO","process_msg"=>"You can't perform this action.");
}		
}else{
$res_data = array("process_sts"=>"NO","process_msg"=>"Your details are missing.");
}
}else{
$res_data = array("process_sts"=>"NO","process_msg"=>"You can't perform this action.");
}
}else{
$res_data = array("process_sts"=>"NO","process_msg"=>"Something went wrong.");	
}	
echo json_encode($res_data);
mysql_close();
?>
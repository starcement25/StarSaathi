<?php
session_start();
set_time_limit(0);
include "star_connection.php";
$res_msg			= array();
$customer_master = "customer_master";
$dealer_id	= $_POST["dealer_id"] ? addslashes(trim($_POST["dealer_id"])) : "";
$user_type	= $_POST["user_type"] ? addslashes(trim($_POST["user_type"])) : "";
$sms_res = "";
if(!isset($_SESSION["sswa_user_id"])){
$res_msg = array("process_sts"=>"NO","process_msg"=>"Your session is expired. Please login then make order.");	
}else {
if($dealer_id!="" && $user_type!=""){
$utype_qry = "";
if($user_type=="dealer"){
	$utype_qry = " and `cust_type`='Dealer'";
}else if($user_type=="subdealer"){
	$utype_qry = " and `cust_type`='Sub Dealer'";
}
$sql1 = "select `customer_name`,`address`,`phone_no` from $customer_master where `customer_code`='$dealer_id' $utype_qry";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
$row1=mysql_fetch_assoc($res1);
$customer_name = trim($row1["customer_name"]);
$address = trim($row1["address"]);
$phone_no = trim($row1["phone_no"]);

$res_msg = array("process_sts"=>"YES","process_msg"=>"Success.","user_name"=>$customer_name,"user_address"=>$address,"user_phone"=>$phone_no);

}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"No record found.");	
}
}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"Something went wrong. Please try later.");
}
}
echo json_encode($res_msg);
mysql_close();
?>
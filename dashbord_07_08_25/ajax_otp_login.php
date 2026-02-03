<?php
session_start();
set_time_limit(0);
include "star_connection.php";
$res_msg			= array();
$customer_master = "customer_master";
$broker_master = "broker_master";
$customer_broker_relation = "customer_broker_relation";
$dealer_id	= $_POST["dealer_id"] ? addslashes(trim($_POST["dealer_id"])) : "";
$mob_no	= $_POST["mob_no"] ? addslashes(trim($_POST["mob_no"])) : "";
$dealer_otp	= $_POST["dealer_otp"] ? trim($_POST["dealer_otp"]) : "";
$sms_res = "";
if($dealer_otp!=""){
if($dealer_id!="" && $mob_no!=""){

echo $sql1 = "select * from $customer_master where `customer_id`='".$dealer_id."' and `phone_no`='".$mob_no."' and `cust_type`='Dealer' and `acedns` = 'Y'";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
$row1 = mysql_fetch_assoc($res1);
$dns_customer_code = trim($row1["dns_customer_code"]);
$customer_code = trim($row1["customer_code"]);
$customer_name = trim($row1["customer_name"]);
$sms_otp = trim($row1["sms_otp"]);
if($sms_otp==$dealer_otp){
$user_type = "DEALER";

$_SESSION["sswa_user_type"]= $user_type;
$_SESSION["sswa_user_name"]= $customer_name;
$_SESSION["sswa_user_id"]= $customer_code;
$_SESSION["sswa_user_dns_id"]= $dns_customer_code;
$_SESSION["sswa_selected_dealer_name"]= $customer_name;
$_SESSION["sswa_selected_dealer_code"]= $dns_customer_code;
$_SESSION["sswa_selected_customer_code"]= $customer_code;

$res_msg = array("process_sts"=>"YES","process_msg"=>"OTP has been sent to your mobile number.");
}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"Wrong OTP");	
}
}else{
	
$sql2 = "select * from $broker_master where `dns_broker_id`='".$dealer_id."' and `phone_no`='".$mob_no."' and `acedns` = 'Y'";
$res2 = mysql_query($sql2);
$totres2 = mysql_num_rows($res2);
if($totres2>0){
$row2 = mysql_fetch_assoc($res2);
$broker_id = trim($row2["broker_id"]);
$dns_broker_id = trim($row2["dns_broker_id"]);
$broker_name = trim($row2["broker_name"]);
$sms_otp = trim($row2["sms_otp"]);
if($sms_otp==$dealer_otp){
$sql24 = "select $customer_broker_relation.`broker_code`,$customer_master.`customer_name`,$customer_master.`customer_code`,$customer_master.`dns_customer_code` from $customer_broker_relation left join $customer_master on $customer_broker_relation.`customer_code`=$customer_master.`customer_code` where $customer_broker_relation.`broker_code`='$broker_id' and $customer_master.`customer_code` is not null order by $customer_master.`customer_name` asc limit 0,1";
$res24 = mysql_query($sql24);
$totres24 = mysql_num_rows($res24);
if($totres24>0){
$row24 = mysql_fetch_assoc($res24);
$selected_customer_name = trim($row24["customer_name"]);
$selected_customer_code = trim($row24["customer_code"]);
$selected_dns_customer_code = trim($row24["dns_customer_code"]);

$user_type = "SP";
$_SESSION["sswa_user_type"]= $user_type;
$_SESSION["sswa_user_name"]= $broker_name;
$_SESSION["sswa_user_id"]= $broker_id;
$_SESSION["sswa_user_dns_id"]= $dns_broker_id;
$_SESSION["sswa_selected_dealer_name"]= $selected_customer_name;
$_SESSION["sswa_selected_dealer_code"]= $selected_dns_customer_code;
$_SESSION["sswa_selected_customer_code"]= $selected_customer_code;

$res_msg = array("process_sts"=>"YES","process_msg"=>"OTP has been sent to your mobile number.");

}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"No dealers are assigned under this sales promoter.");
}
}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"Wrong OTP");
}
}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"NOT VALID USER");
}

}
}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"Something went wrong. Please try later.");
}
}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"Please enter OTP.");
}
echo json_encode($res_msg);
mysql_close();
?>
<?php
session_start();
set_time_limit(0);
include "star_connection.php";
$res_msg			= array();
$customer_master = "customer_master";
$destination_master = "destination_master";
$customer_destination= "customer_destination";
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
//$sql1 = "select `customer_name`,`address`,`phone_no` from $customer_master where `customer_code`='$dealer_id' $utype_qry";
$sql1 = "select `customer_name`,`address`,`phone_no` from $customer_master where `customer_code`='$dealer_id'";
$res1 = mysql_query($sql1);
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
$row1=mysql_fetch_assoc($res1);
$customer_name = trim($row1["customer_name"]);
$address = trim($row1["address"]);
$phone_no = trim($row1["phone_no"]);

$sql_dst="SELECT DM.destination_code,DM.destination_name FROM $destination_master DM,$customer_destination CD		
		WHERE DM.destination_code=CD.destination_code AND CD.customer_code='".$dealer_id."'";
$res_dst= mysql_query($sql_dst);
$totres_ds = mysql_num_rows($res_dst);
$dest_select= "<select name=\"sl_des_ex_id\" id=\"sl_des_ex_id\"  class=\"form-control sl_des_ex_id\" >";
//$dest_select.="<option value=\"\">Select</option>";
while($row_dist = mysql_fetch_array($res_dst)){
	$the_destination_code = $row_dist["destination_code"];
	$the_destination_name = $row_dist["destination_name"];
	$dest_select.="<option value=".$the_destination_code.">".$the_destination_name."</option>";
}
$dest_select.="</select>";



$res_msg = array("process_sts"=>"YES","process_msg"=>"Success.","user_name"=>$customer_name,"user_address"=>$address,"user_phone"=>$phone_no,"user_destination"=>$dest_select);

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
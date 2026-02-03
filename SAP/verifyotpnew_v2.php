<?php
// check error
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "star_connection.php";
$customer_master = "customer_master";
$changepassword = "changepassword";
$table_structure_updation = "table_structure_updation";
$broker_master = "broker_master";
$server_url1 = "http://" . $_SERVER['SERVER_NAME'] . "/SAP/";
		$img_dir = "profile_image/";
$res_data = array();
$nick_name = $_POST["nickname"] ? trim($_POST["nickname"]) : "";
$dealer_id = $_POST["dealer_id"] ? trim($_POST["dealer_id"]) : "";
$phonenumber = $_POST["phonenumber"] ? trim($_POST["phonenumber"]) : "";
$deviceid = $_POST["deviceid"] ? trim($_POST["deviceid"]) : "";
$the_otp = $_POST["the_otp"] ? trim($_POST["the_otp"]) : "";

if($dealer_id!="" && $phonenumber!="" && $deviceid!="" && $the_otp!=""){
	
$sql_ckcust="select `customer_code`,`dns_customer_code` from $customer_master where `customer_id`='$dealer_id'";
$res_ckcust=mysqli_query($link,$sql_ckcust);
$sqlquery_ckcust = mysqli_num_rows($res_ckcust);
if($sqlquery_ckcust>0){
$row_ckcust = mysqli_fetch_assoc($res_ckcust);
$the_customer_code = $row_ckcust["customer_code"];	
}

$verificationtoken='';
$emp_code='';
$newpassword='';
	
$sqlquery_survey_form = "select `sf_cust_code` from survey_form where `sf_cust_code`='$the_customer_code'";
$res_survey_form = mysqli_query($link,$sqlquery_survey_form);
$totres_survey_form = mysqli_num_rows($res_survey_form);
	if ($totres_survey_form > 0) {
		$is_survey_form_submitted = "YES";
	}
	else
	{
		$is_survey_form_submitted = "NO";
	}
	

$sql_cust_data_ck = "select $customer_master.`customer_code`,$customer_master.`dns_customer_code`,$customer_master.`customer_name`,$changepassword.`newpassword`,
$changepassword.`deviceid`,$customer_master.`acedns`,$customer_master.`phone_no`,$customer_master.`sms_otp`,
$customer_master.`cust_type`,$customer_master.`profile_image`,$customer_master.`rds_tag` from $customer_master left join $changepassword on  $customer_master.`customer_id`= $changepassword.`dns_customer_code` where $customer_master.`phone_no`='$phonenumber' and $customer_master.`customer_id`='$dealer_id'";
$res_cust_data_ck = mysqli_query($link,$sql_cust_data_ck);
$totres_cust_data_ck = mysqli_num_rows($res_cust_data_ck);
if($totres_cust_data_ck>0){
$row_cust_data_ck = mysqli_fetch_assoc($res_cust_data_ck);
$emp_code = $row_cust_data_ck["customer_code"];
$emp_customer_name = $row_cust_data_ck["customer_name"];
$emp_phone_no = $row_cust_data_ck["phone_no"];
$emp_newpassword = $row_cust_data_ck["newpassword"];
$dns_emp_code = $row_cust_data_ck["dns_customer_code"];
$device_id_database = $row_cust_data_ck["deviceid"];
$acedns = $row_cust_data_ck["acedns"];
$sms_otp = $row_cust_data_ck["sms_otp"];
$cust_type = $row_cust_data_ck["cust_type"];
$cust_type = strtolower($cust_type);
$profile_image = $row_cust_data_ck["profile_image"];
$rds_tag = $row_cust_data_ck["rds_tag"];
	
$belong_dealer_code = $rds_tag ? $rds_tag : "";
$belong_dealer_dns_code = "";
$belong_dealer_name = "";
if ($belong_dealer_code != "") {
	$sqlquery_bdck = "select `dns_customer_code`,customer_name from $customer_master where `customer_code`='$belong_dealer_code'";
$res_bdck = mysqli_query($link,$sqlquery_bdck);
$totres_bdck = mysqli_num_rows($res_bdck);
	if ($totres_bdck > 0) {
		$row_bdck=mysqli_fetch_assoc($res_bdck);
		$belong_dealer_dns_code = $row_bdck['dns_customer_code'];
		$belong_dealer_name = $row_bdck['customer_name'];
	}
}
$the_profile_image =$row_cust_data_ck["profile_image"];
$the_profile_image_url = $server_url1 . $img_dir . $the_profile_image;	
if(strtoupper($acedns)=='Y'){
	if($the_otp==$sms_otp){

$sql_tstr_ck = "select `emp_code`,`device_id` from $table_structure_updation where `device_id`='$deviceid' and `emp_code`='$dealer_id'";
$res_tstr_ck = mysqli_query($link,$sql_tstr_ck);
$totres_tstr_ck = mysqli_num_rows($res_tstr_ck);
if($totres_tstr_ck==0) {
$sql_tstr_upd = "update $table_structure_updation set `emp_code`='$dealer_id' where `device_id`='$deviceid' and `emp_code`=''";
$res_tstr_upd = mysqli_query($link,$sql_tstr_upd);
}

$location_date = date("Y-m-d H:i:s");

$sql_cngpwd_upd = "update $changepassword set `deviceid`='$deviceid',`loggedin_date_time`='$location_date' where `dns_customer_code`='$dealer_id'";
$res_cngpwd_upd = mysqli_query($link,$sql_cngpwd_upd);
		

$res_data = array("process_status"=>"YES","process_message"=>"OTP IS SUCCESSFULLY VERIFIED.","user_type"=>$cust_type,"emp_code"=>$emp_code,"customer_code"=>$emp_code,"dns_emp_code"=>$dns_emp_code,"emp_name"=>$emp_customer_name,"sale_access"=>"PRIMARY","newpassword"=>$emp_newpassword,"deviceid"=>$device_id_database,"phonenumber"=>$emp_phone_no,"acedns"=>$acedns,"broker_id"=>"","dns_broker_id"=>"","contact_person"=>"","mail_id"=>"","brokerage_cost"=>"","state_code"=>"","the_profile_image_url" => $the_profile_image_url, "belong_dealer_code" => $belong_dealer_code, "belong_dealer_dns_code" => $belong_dealer_dns_code, "belong_dealer_name" => $belong_dealer_name, "is_survey_form_submitted" => $is_survey_form_submitted);


	}else{
		$res_data = array("process_status"=>"NO","process_message"=>"OTP DOESN'T MATCH. PLEASE ENTER CORRECT OTP.");
	}
	
}else{
	$res_data = array("process_status"=>"NO","process_message"=>"NOT LICENSED USER");

}

}
else{
	
	$sqlquery_ckbk = "select $broker_master.`broker_id`,$broker_master.`dns_broker_id`,$broker_master.`broker_name`,$broker_master.`contact_person`,
$broker_master.`mail_id`,$broker_master.`phone_no`,$broker_master.`brokerage_cost`,$broker_master.acedns,
					$broker_master.state_code,$broker_master.profile_image,$broker_master.sms_otp from $broker_master  where $broker_master.`phone_no`='$phonenumber' and $broker_master.`dns_broker_id`='$dealer_id'";
$res_ckbk = mysqli_query($link,$sqlquery_ckbk);
$totres_ckbk  = mysqli_num_rows($res_ckbk);
if($totres_ckbk>0){
$row_ckbk = mysqli_fetch_assoc($res_ckbk);
					$brokerage_cost = $row_ckbk['brokerage_cost'] ? trim($row_ckbk['brokerage_cost']) : "";
					$state_code = $row_ckbk['state_code'] ? trim($row_ckbk['state_code']) : "";
					$dns_broker_id = $row_ckbk['dns_broker_id'] ? trim($row_ckbk['dns_broker_id']) : "";
	$broker_id = $row_ckbk['broker_id'] ? trim($row_ckbk['broker_id']) : "";
	$broker_name = $row_ckbk['broker_name'] ? trim($row_ckbk['broker_name']) : "";
	$acedns = $row_ckbk['acedns'] ? trim($row_ckbk['acedns']) : "";
	$phone_no = $row_ckbk['phone_no'] ? trim($row_ckbk['phone_no']) : "";
	$contact_person = $row_ckbk['contact_person'] ? trim($row_ckbk['contact_person']) : "";
	$mail_id = $row_ckbk['mail_id'] ? trim($row_ckbk['mail_id']) : "";
	$profile_image = $row_ckbk['profile_image'] ? trim($row_ckbk['profile_image']) : "";
	$the_profile_image_url = $server_url1 . $img_dir . $the_profile_image;	
	$sms_otp = $row_ckbk['sms_otp'] ? trim($row_ckbk['sms_otp']) : "";
	if($the_otp==$sms_otp){
		
		$res_data = array("process_status" => "YES", "process_message" => "OTP IS SUCCESSFULLY VERIFIED.", "user_type" => "broker", "emp_code" => $dns_broker_id, "customer_code" => "", "dns_emp_code" => $dns_broker_id, "emp_name" => $broker_name, "sale_access" => "PRIMARY", "newpassword" => "", "deviceid" => "", "phonenumber" => $phone_no, "acedns" => $acedns, "broker_id" => $broker_id, "dns_broker_id" => $dns_broker_id, "contact_person" => $contact_person, "mail_id" => $mail_id, "brokerage_cost" => $brokerage_cost, "state_code" => $state_code, "the_profile_image_url" => $the_profile_image_url, "belong_dealer_code" => "", "belong_dealer_dns_code" => "", "belong_dealer_name" => "", "is_survey_form_submitted" => $is_survey_form_submitted);
		
	}
	else{
		$res_data = array("process_status"=>"NO","process_message"=>"OTP DOESN'T MATCH. PLEASE ENTER CORRECT OTP.");
	}
  }
	else{
$res_data = array("process_status"=>"NO","process_message"=>"NOT VALID USER");
}
}	
	
}else{
$res_data = array("process_status"=>"NO","process_message"=>"All fields are mandatory.");
}

$json_encoded = json_encode($res_data);
echo $json_encoded;	

if($conn!=""){
mysqli_close($conn);
}
?>
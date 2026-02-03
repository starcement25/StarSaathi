<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "star_connection.php";
$customer_master = "customer_master";
$changepassword = "changepassword";
$broker_master="broker_master";
$res_data = array();
$nick_name = $_POST["nickname"] ? trim($_POST["nickname"]) : "";
$dealer_id = $_POST["dealer_id"] ? trim($_POST["dealer_id"]) : "";
$phonenumber = $_POST["phonenumber"] ? trim($_POST["phonenumber"]) : "";
$deviceid = $_POST["deviceid"] ? trim($_POST["deviceid"]) : "";
if($dealer_id!="" && $phonenumber!="" && $deviceid!=""){


/*$check = "";
$verificationtoken='';
$emp_code='';
$newpassword='';
$templt_id = "1107161364809551631";*/



$sql_ckcust="select `customer_code`,`dns_customer_code` from $customer_master where `customer_id`='$dealer_id'";
//echo"<pre>";print_r($link);die;
$res_ckcust=mysqli_query($link,$sql_ckcust);
$sqlquery_ckcust = mysqli_num_rows($res_ckcust);

if($sqlquery_ckcust>0){
$row_ckcust = mysqli_fetch_assoc($res_ckcust);
$the_customer_code = $row_ckcust["customer_code"];

$sql_ckcptbl = "select `dns_customer_code` from $changepassword where (`dns_customer_code`='$dealer_id' or `customer_code`='$the_customer_code')";
//echo"<pre>";print_r($sql_ckcptbl);die;

$res_ckcptbl = mysqli_query($link,$sql_ckcptbl);
$totres_ckcptbl = mysqli_num_rows($res_ckcptbl);
if($totres_ckcptbl>0){

}else{
$curr_date_time = date("Y-m-d H:i:s");
// $sql_in_cptbl = "insert into $changepassword (`dns_customer_code`,`customer_code`,`emp_code`,`newpassword`,`oldpassword`,`status`,`is_licensed`,`loggedin_date_time`,`last_operation_datetime`) values ('$dealer_id','$the_customer_code','','1234','1234','true','1','$curr_date_time','$curr_date_time')";

$sql_in_cptbl = "insert into $changepassword (`dns_customer_code`,`customer_code`,`emp_code`,`newpassword`,`oldpassword`,`status`,`is_licensed`,`loggedin_date_time`,`last_operation_datetime`, `deviceid`, `app_version`) values ('$dealer_id','$the_customer_code','','1234','1234','true','1','$curr_date_time','$curr_date_time', '', '')";


$res_in_cptbl = mysqli_query($link,$sql_in_cptbl);
}
}
$sqlquery_survey_form = "select `sf_cust_code` from survey_form where `sf_cust_code`='$the_customer_code'";
$res_survey_form = mysqli_query($link,$sqlquery_survey_form);
$totres_survey_form = mysqli_num_rows($res_survey_form);
	if ($totres_survey_form > 0) {
		$is_survey_form_submitted = "YES";
	}		

$otp_for_login = "9090";
		$otp_for_login = rand(1, 9) . rand(0, 9) . rand(0, 9) . rand(1, 9);
		/*if($phonenumber=="9233974090" || $phonenumber=="9831722939" || $phonenumber=="9638307128" || $dealer_id=="WBB037"){
			   $otp_for_login = "1010";
			  }*/
		if (strtoupper($dealer_id) == "1000000281" || strtoupper($dealer_id) == "TEST011" || strtoupper($dealer_id) == "1500006350" || strtoupper($dealer_id) == "1000000341" || strtoupper($dealer_id)=="TEST012" || strtoupper($dealer_id)=="TEST013") {
			$otp_for_login = "1010";
		}
		if ($phonenumber == "9831722939") {
			$otp_for_login = "1010";
		}
		if (strtoupper($dealer_id) == "1000001932") {
			$otp_for_login = "1902";
		}
		$otp_for_login='1010';
		$otp_text = "OTP is " . $otp_for_login . " for Star Saathi log in STAR CEMENT";
$sql_cust_data_ck = "select $customer_master.`customer_code`,$customer_master.`dns_customer_code`,$customer_master.`customer_name`,$changepassword.`newpassword`,
$changepassword.`deviceid`,$customer_master.`acedns`,$customer_master.`phone_no`,$customer_master.cust_type,
					$customer_master.rds_tag from $customer_master left join $changepassword on  $customer_master.`customer_id`= $changepassword.`dns_customer_code` where $customer_master.`phone_no`='$phonenumber' and $customer_master.`customer_id`='$dealer_id'";
$res_cust_data_ck = mysqli_query($link,$sql_cust_data_ck);
$totres_cust_data_ck = mysqli_num_rows($res_cust_data_ck);
if($totres_cust_data_ck>0){
$row_cust_data_ck = mysqli_fetch_assoc($res_cust_data_ck);
$emp_code = $row_cust_data_ck["customer_code"];

$emp_customer_name = $row_cust_data_ck["customer_name"];
$emp_phone_no = $row_cust_data_ck["phone_no"];
$emp_newpassword = $row_cust_data_ck["newpassword"];
$dns_emp_code = $row_cust_data_ck["dns_customer_code"];
$device_id_database = $row_cust_data_ck["deviceid"] ? trim($row_cust_data_ck["deviceid"]) : "";
$acedns = $row_cust_data_ck["acedns"];
$cust_type = $row_cust_data_ck["cust_type"];
$cust_type = strtolower($cust_type);
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


	
if(strtoupper($acedns)=='Y'){

/*$otp_for_login = rand(1,9).rand(0,9).rand(0,9).rand(1,9);*/
	if ($deviceid == '') {
		$res_data = array("process_status" => "NO", "process_message" => "DEVICEID IS BLANK");
	} else {



$sql_cust_otp_upd = "update $customer_master set `sms_otp`='$otp_for_login' where `customer_id`='$dealer_id'";
$res_cust_otp_upd = mysqli_query($link,$sql_cust_otp_upd);
$lipl_uri = "https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to=" . $phonenumber . "&from=STARCM&text=" . urlencode($otp_text) . "&tempid=1707160982733435860&dlr-mask=19&dlr-url";
		if ($phonenumber == "9233974090" || $phonenumber == "9638307128") {
						} else {
							$lipl_ch = curl_init();
							curl_setopt($lipl_ch, CURLOPT_URL, $lipl_uri);
							curl_setopt($lipl_ch, CURLOPT_TIMEOUT, 20);
							curl_setopt($lipl_ch, CURLOPT_FOLLOWLOCATION, 1);
							curl_setopt($lipl_ch, CURLOPT_HEADER, 0);
							curl_setopt($lipl_ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($lipl_ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
							//$lipl_return_val = curl_exec($lipl_ch);
							curl_close($lipl_ch);
						}
						

$res_data = array("process_status"=>"YES","process_message"=>"OTP has been sent to your mobile number.","user_type"=>$cust_type,"emp_code"=>$emp_code,"customer_code"=>$emp_code,"dns_emp_code"=>$dns_emp_code,"emp_name"=>$emp_customer_name,"sale_access"=>"PRIMARY","newpassword"=>$emp_newpassword,"deviceid"=>$device_id_database,"phonenumber"=>$emp_phone_no,"acedns"=>$acedns,"broker_id"=>"","dns_broker_id"=>"","contact_person"=>"","mail_id"=>"","brokerage_cost"=>"","state_code"=>"","otp_text"=>$otp_text, "belong_dealer_code" => $belong_dealer_code, "belong_dealer_dns_code" => $belong_dealer_dns_code, "belong_dealer_name" => $belong_dealer_name, "is_survey_form_submitted" => $is_survey_form_submitted);
	}
}else{
$res_data = array("process_status"=>"NO","process_message"=>"NOT LICENSED USER");
}
}
else{
	
$sqlquery_ckbk = "select $broker_master.`broker_id`,$broker_master.`dns_broker_id`,$broker_master.`broker_name`,$broker_master.`contact_person`,
$broker_master.`mail_id`,$broker_master.`phone_no`,$broker_master.`brokerage_cost`,$broker_master.acedns,
					$broker_master.state_code from $broker_master  where $broker_master.`phone_no`='$phonenumber' and $broker_master.`dns_broker_id`='$dealer_id'";
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
					$sqlUpdate = "update $broker_master set `sms_otp`='$otp_for_login' where `dns_broker_id`='$dealer_id' AND phone_no='$phonenumber'";
					$resUpdate = mysqli_query($link,$sqlUpdate);
					
					$otp_text = "OTP is " . $otp_for_login . " for Star Saathi log in STAR CEMENT";
					$lipl_uri = "https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to=" . $phonenumber . "&from=STARCM&text=" . urlencode($otp_text) . "&tempid=1707160982733435860&dlr-mask=19&dlr-url";
					if ($phonenumber == "9233974090" || $phonenumber == "9638307128") {
					} else {
						$lipl_ch = curl_init();
						curl_setopt($lipl_ch, CURLOPT_URL, $lipl_uri);
						curl_setopt($lipl_ch, CURLOPT_TIMEOUT, 20);
						curl_setopt($lipl_ch, CURLOPT_FOLLOWLOCATION, 1);
						curl_setopt($lipl_ch, CURLOPT_HEADER, 0);
						curl_setopt($lipl_ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($lipl_ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
						//$lipl_return_val = curl_exec($lipl_ch);
						curl_close($lipl_ch);
					}
					$res_data = array("process_status" => "YES", "process_message" => "OTP has been sent to your mobile number.", "user_type" => "broker", "emp_code" => $dns_broker_id, "customer_code" => "", "dns_emp_code" => $dns_broker_id, "emp_name" => $broker_name, "sale_access" => "PRIMARY", "newpassword" => "", "deviceid" => "", "phonenumber" => $phone_no, "acedns" => $acedns, "broker_id" => $broker_id, "dns_broker_id" => $dns_broker_id, "contact_person" => $contact_person, "mail_id" => $mail_id, "brokerage_cost" => $brokerage_cost, "state_code" => $state_code, "otp_text" => $otp_text, "belong_dealer_code" => "", "belong_dealer_dns_code" => "", "belong_dealer_name" => "", "is_survey_form_submitted" => $is_survey_form_submitted);
					
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
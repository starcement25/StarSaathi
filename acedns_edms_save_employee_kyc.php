<?php
include "edms_connection.php";
$server_url1 = "http://" . $_SERVER['SERVER_NAME']."/";
$employee_kyc_master = "employee_kyc_master";
$employee_master = "employee_master";
$customer_master = "customer_master";

function get_customer_details_by_id($theid){
$employee_master = "employee_master";
$customer_master = "customer_master";
$cust_dtls_arr = array("emp_code"=>"","customer_code"=>"","dns_customer_code"=>"");
$theid = $theid ? trim($theid) : "";
if($theid!=""){
$sqlsrc = "select `customer_code`,`dns_customer_code` from $customer_master where (`customer_code`='$theid' or `dns_customer_code`='$theid')";
$ressrc = mysql_query($sqlsrc);
$totsrc = mysql_num_rows($ressrc);
if($totsrc>0){
	$rowsrc = mysql_fetch_assoc($ressrc);
	$the_customer_code = addslashes(trim($rowsrc["customer_code"]));
	$the_dns_customer_code = addslashes(trim($rowsrc["dns_customer_code"]));
	$cust_dtls_arr["customer_code"] = $the_customer_code;
	$cust_dtls_arr["dns_customer_code"] = $the_dns_customer_code;
	if($the_dns_customer_code!=""){
		$sqlck = "select `emp_code`,`dns_emp_code` from $employee_master where `dns_emp_code`='$the_dns_customer_code'";
		$resck = mysql_query($sqlck);
		$totresck = mysql_num_rows($resck);
		if($totresck>0){
		$rowck = mysql_fetch_assoc($resck);
		$the_emp_code = $rowck["emp_code"] ? addslashes(trim($rowck["emp_code"])) : "";
		$cust_dtls_arr["emp_code"] = $the_emp_code;
		}
	}
	
}else{
	$sqlck = "select `emp_code`,`dns_emp_code` from $employee_master where (`emp_code`='$theid' or `dns_emp_code`='$theid')";
	$resck = mysql_query($sqlck);
	$totresck = mysql_num_rows($resck);
	if($totresck>0){
		$rowck = mysql_fetch_assoc($resck);
		$the_emp_code = $rowck["emp_code"] ? addslashes(trim($rowck["emp_code"])) : "";
		$the_dns_emp_code = $rowck["dns_emp_code"] ? addslashes(trim($rowck["dns_emp_code"])) : "";
		$cust_dtls_arr["emp_code"] = $the_emp_code;
		$cust_dtls_arr["dns_customer_code"] = $the_dns_emp_code;
		$sqlsrc = "select `customer_code`,`dns_customer_code` from $customer_master where `dns_customer_code`='$the_dns_emp_code'";
		$ressrc = mysql_query($sqlsrc);
		$totsrc = mysql_num_rows($ressrc);
		if($totsrc>0){
		$rowsrc = mysql_fetch_assoc($ressrc);
		$the_customer_code = addslashes(trim($rowsrc["customer_code"]));
		$cust_dtls_arr["customer_code"] = $the_customer_code;
		}
	}
}

}
return $cust_dtls_arr;
}

$the_emp_code = $_POST["emp_code"] ? addslashes(trim($_POST["emp_code"])) : "";
$the_whatsapp_no = $_POST["whatsapp_no"] ? addslashes(trim($_POST["whatsapp_no"])) : "";
$the_dob = $_POST["dob"] ? addslashes(trim($_POST["dob"])) : "";
$the_dom = $_POST["dom"] ? addslashes(trim($_POST["dom"])) : "";
$the_email_id = $_POST["email_id"] ? addslashes(trim($_POST["email_id"])) : "";

if($the_emp_code!="" && $the_whatsapp_no!="" && $the_dob!="" && $the_email_id!=""){
$customer_details_arr = get_customer_details_by_id($the_emp_code);
$get_emp_code = $customer_details_arr["emp_code"];
$get_customer_code = $customer_details_arr["customer_code"];
$get_dns_customer_code = $customer_details_arr["dns_customer_code"];

$curr_date_time  = date("Y-m-d H:i:s");
$sqlall = "select `id`,`emp_code` from $employee_kyc_master where (`emp_code`='$the_emp_code' or `customer_code`='$the_emp_code' or `dns_customer_code`='$the_emp_code')";
$resall = mysql_query($sqlall);
$totall = mysql_num_rows($resall);
if($totall>0){
	$rowall = mysql_fetch_assoc($resall);
	$the_id = $rowall["id"];
	$sql2 = "update $employee_kyc_master set `emp_code`='$get_emp_code',`customer_code`='$get_customer_code',`dns_customer_code`='$get_dns_customer_code',`whatsapp_no`='$the_whatsapp_no',`dob`='$the_dob',`dom`='$the_dom',`email_id`='$the_email_id',`last_updated_datetime`='$curr_date_time' where `id`='$the_id'";
	$res2 = mysql_query($sql2);
	$res_data = array("process_status"=>"YES","process_message"=>"KYC details successfully updated.");
}else{
	$sql2 = "insert into $employee_kyc_master (`emp_code`,`customer_code`,`dns_customer_code`,`whatsapp_no`,`dob`,`dom`,`email_id`,`last_updated_datetime`) values ('$get_emp_code','$get_customer_code','$get_dns_customer_code','$the_whatsapp_no','$the_dob','$the_dom','$the_email_id','$curr_date_time')";
	$res2 = mysql_query($sql2);
	$res_data = array("process_status"=>"YES","process_message"=>"KYC details successfully saved.");
}
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Whatsapp no.,DOB,Email are mandatory.");
}	
echo json_encode($res_data);
mysql_close();
?>
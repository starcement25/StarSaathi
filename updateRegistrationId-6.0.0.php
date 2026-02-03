<?php
require("include/config.php");
require("include/dbcon.php");
$res_data = array();
$changepassword = "changepassword";
$deviceId=$_POST['deviceId'] ? addslashes(trim($_POST['deviceId'])) : "";
$emp_code=$_POST['emp_code'] ? addslashes(trim($_POST['emp_code'])) : "";
$device_type=$_POST['device_type'] ? addslashes(trim($_POST['device_type'])) : ""; /*ANDROID or IOS*/
$registrationid=$_POST['registrationid'] ? addslashes(trim($_POST['registrationid'])) : "";
$app_version=$_POST['app_version'] ? addslashes(trim($_POST['app_version'])) : "";
$last_operation_datetime =  date('Y-m-d H:m:s');
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/updateRegistrationId-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&registrationid=$registrationid&deviceId=$deviceId";
insertapilog($datetime,$emp_code,$url,$nick_name);
if($deviceId!="" && $emp_code!="" && $device_type!="" && $registrationid!=""){
	$sql1 = "select * from $changepassword where (`emp_code`='$emp_code' or `customer_code`='$emp_code' or `dns_customer_code`='$emp_code')";
	$res1 = mysql_query($sql1);
	$tot_res1 = mysql_num_rows($res1);
	if($tot_res1>0){
		$row1 = mysql_fetch_assoc($res1);
		$customer_code = $row1["customer_code"] ? trim($row1["customer_code"]) : "";
		$old_deviceid = $row1["deviceid"] ? trim($row1["deviceid"]) : "";
		if($old_deviceid!=""){
			if($old_deviceid==$deviceId){
				$sqlUpdate="UPDATE $changepassword SET `deviceid`='$deviceId',`device_type`='$device_type',`last_operation_datetime`='$last_operation_datetime',`registrationid`='$registrationid',`app_version`='$app_version' WHERE `customer_code`='$customer_code'";
				$resUpdate = mysql_query($sqlUpdate);
				$res_data = array("process_status"=>"YES","force_logout"=>"NO","process_message"=>"Successfully updated.");
			}else{
				$res_data = array("process_status"=>"NO","force_logout"=>"YES","process_message"=>"Already allocated.");
			}			
		}else{
			$sqlUpdate="UPDATE $changepassword SET `deviceid`='$deviceId',`device_type`='$device_type',`last_operation_datetime`='$last_operation_datetime',`registrationid`='$registrationid',`app_version`='$app_version' WHERE `customer_code`='$customer_code'";
			$resUpdate = mysql_query($sqlUpdate);
			$res_data = array("process_status"=>"YES","force_logout"=>"NO","process_message"=>"Successfully updated.");
		}
	}else{
		$res_data = array("process_status"=>"NO","force_logout"=>"YES","process_message"=>"Something went wrong. Please try later.");
	}	
}else{
	$res_data = array("process_status"=>"NO","force_logout"=>"NO","process_message"=>"Something went wrong. Please try later.");
}
echo json_encode($res_data);
mysql_close($link);
?>
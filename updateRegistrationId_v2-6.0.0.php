<?php
require("include/config.php");
require("include/dbcon.php");
$res_data = array();
$changepassword = "changepassword";
$broker_master = "broker_master";
$customer_master = "customer_master";
$branch_PGstatus = "branch_PGstatus";
$branch_wise_pg_rollout = "INACTIVE";
$deviceId=$_POST['deviceId'] ? addslashes(trim($_POST['deviceId'])) : "";
$emp_code=$_POST['emp_code'] ? addslashes(trim($_POST['emp_code'])) : "";
$user_type = $_POST['user_type'] ? strtolower(trim($_POST['user_type'])) : "";
$device_type=$_POST['device_type'] ? addslashes(trim($_POST['device_type'])) : ""; /*ANDROID or IOS*/
$registrationid=$_POST['registrationid'] ? addslashes(trim($_POST['registrationid'])) : "";
$app_version=$_POST['app_version'] ? addslashes(trim($_POST['app_version'])) : "";
$last_operation_datetime =  date('Y-m-d H:m:s');
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/updateRegistrationId-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&registrationid=$registrationid&deviceId=$deviceId";
insertapilog($datetime,$emp_code,$url,$nick_name);
if($deviceId!="" && $emp_code!="" && $device_type!="" && $registrationid!=""){
if($user_type=="broker"){
	$sql1 = "select `dns_broker_id` from $broker_master where `dns_broker_id`='$emp_code'";
	$res1 = mysql_query($sql1);
	$tot_res1 = mysql_num_rows($res1);
	if($tot_res1>0){
$sqlUpdate="UPDATE $broker_master SET `deviceid`='$deviceId',`device_type`='$device_type',`registrationid`='$registrationid',`app_version`='$app_version' WHERE `dns_broker_id`='$emp_code'";
$resUpdate = mysql_query($sqlUpdate);
$res_data = array("process_status"=>"YES","force_logout"=>"NO","process_message"=>"Successfully updated.","branch_wise_pg_rollout"=>$branch_wise_pg_rollout);
	}else{
		$res_data = array("process_status"=>"NO","force_logout"=>"YES","process_message"=>"Something went wrong. Please try later.","branch_wise_pg_rollout"=>$branch_wise_pg_rollout);
	}
	
}else{
$sql3 = "select `dns_customer_code`,`customer_code`,`acedns`,`branch_code` from $customer_master where `customer_code`='$emp_code' or `dns_customer_code`='$emp_code'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
$row3 = mysql_fetch_assoc($res3);
$the_dealer_id = addslashes(trim($row3["dns_customer_code"]));
$the_customer_code = addslashes(trim($row3["customer_code"]));
$branch_code = $row3["branch_code"] ? trim($row3["branch_code"]) : "";
$the_acedns = $row3["acedns"] ? trim($row3["acedns"]) : "N";
if($the_acedns==""){
$the_acedns = "N";	
}
if($the_acedns=="Y"){

if($branch_code!=""){
$sql_bwps = "select `pg_status` from $branch_PGstatus where `branch_code`='$branch_code'";
$res_bwps = mysql_query($sql_bwps);
$totres_bwps = mysql_num_rows($res_bwps);
if($totres_bwps>0){
$row_bwps = mysql_fetch_assoc($res_bwps);
$branch_wise_pg_rollout = $row_bwps["pg_status"] ? trim($row_bwps["pg_status"]) : "INACTIVE";
if($branch_wise_pg_rollout==""){
$branch_wise_pg_rollout = "INACTIVE";	
}
}
}



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
				$res_data = array("process_status"=>"YES","force_logout"=>"NO","process_message"=>"Successfully updated.","branch_wise_pg_rollout"=>$branch_wise_pg_rollout);
			}else{
				$res_data = array("process_status"=>"NO","force_logout"=>"YES","process_message"=>"Already allocated.","branch_wise_pg_rollout"=>$branch_wise_pg_rollout);
			}			
		}else{
			$sqlUpdate="UPDATE $changepassword SET `deviceid`='$deviceId',`device_type`='$device_type',`last_operation_datetime`='$last_operation_datetime',`registrationid`='$registrationid',`app_version`='$app_version' WHERE `customer_code`='$customer_code'";
			$resUpdate = mysql_query($sqlUpdate);
			$res_data = array("process_status"=>"YES","force_logout"=>"NO","process_message"=>"Successfully updated.","branch_wise_pg_rollout"=>$branch_wise_pg_rollout);
		}
	}else{
		$res_data = array("process_status"=>"NO","force_logout"=>"YES","process_message"=>"Something went wrong. Please try later.","branch_wise_pg_rollout"=>$branch_wise_pg_rollout);
	}

}else{
	$res_data = array("process_status"=>"NO","force_logout"=>"YES","process_message"=>"User record inactive.","branch_wise_pg_rollout"=>$branch_wise_pg_rollout);
}
	
	
}else{
	$res_data = array("process_status"=>"NO","force_logout"=>"YES","process_message"=>"User record not found.","branch_wise_pg_rollout"=>$branch_wise_pg_rollout);
}



}
}else{
	$res_data = array("process_status"=>"NO","force_logout"=>"NO","process_message"=>"Something went wrong. Please try later.","branch_wise_pg_rollout"=>$branch_wise_pg_rollout);
}
echo json_encode($res_data);
mysql_close($link);
?>
<?php
include "edms_connection.php";
$changepassword = "changepassword";
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
if($the_id!=""){
	$last_datetime = date("Y-m-d H:i:s");
	$sql="update $changepassword set `deviceid`='',`registrationid`='',`last_operation_datetime`='$last_datetime' where (`emp_code`='$the_id' or `customer_code`='$the_id' or `dns_customer_code`='$the_id')";
	$res=mysql_query($sql,$conn);
	$res_data = array("process_status"=>"YES","process_message"=>"Successfully Log out.","last_datetime"=>$last_datetime);
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"The id is mandatory.");
}	
echo json_encode($res_data);
mysql_close();
?>
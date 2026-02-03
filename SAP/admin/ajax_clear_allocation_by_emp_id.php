<?php
include "star_connection.php";
$changepassword = "changepassword";
$the_id = $_REQUEST["clemplyid"] ? addslashes(trim($_REQUEST["clemplyid"])) : "";
if($the_id!=""){
	$sql="update $changepassword set `deviceid`='',`registrationid`='' where (`emp_code`='$the_id' or `customer_code`='$the_id' or `dns_customer_code`='$the_id')";
	$res=mysql_query($sql);
$res_data = array("process_status"=>"YES","process_message"=>"Employee device allocation has been cleared successfully.");
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");
}	
echo json_encode($res_data);
mysql_close();
?>
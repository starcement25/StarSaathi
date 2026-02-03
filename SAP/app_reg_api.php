<?php
ini_set('memory_limit', '99999M');
set_time_limit(0);
include "star_connection.php";
$dummy_registration_for_app_approval = "dummy_registration_for_app_approval";

$name = $_REQUEST["name"] ? addslashes(trim($_REQUEST["name"])) : "";
$mobile = $_REQUEST["mobile"] ? addslashes(trim($_REQUEST["mobile"])) : "";

if($name!="" && $mobile!=""){

$sql3 = "select `mobile` from $dummy_registration_for_app_approval where `mobile`='$mobile'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
$res_data = array("process_status"=>"YES","process_message"=>"Thank you for registration. You may able to login after confirmation.");
}else{
$sql32 = "insert into $dummy_registration_for_app_approval (`name`,`mobile`) values ('$name','$mobile')";
$res32 = mysql_query($sql32);	
$res_data = array("process_status"=>"YES","process_message"=>"Thank you for registration. You may able to login after confirmation.");
}
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"All fields mandatory.");
}
echo json_encode($res_data);
mysql_close();
?>
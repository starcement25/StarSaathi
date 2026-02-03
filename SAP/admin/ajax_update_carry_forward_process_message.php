<?php
include "star_connection.php";
$app_setting_master = "app_setting_master";
$res = array();
$str_carry_forward_process_message = $_POST["str_carry_forward_process_message"] ? addslashes(trim($_POST["str_carry_forward_process_message"])) : "";
$last_updated_datetime = date('Y-m-d H:i:s');
$sqlckrds = "select `the_value` from $app_setting_master where `the_key_name`='carry_forward_process_message'";
$resckrds = mysql_query($sqlckrds);
$totresckrds = mysql_num_rows($resckrds);
if($totresckrds>0){
	$sqlupd = "update $app_setting_master set `the_value`='$str_carry_forward_process_message',`last_updated_datetime`='$last_updated_datetime' where `the_key_name`='carry_forward_process_message'";
	$resupd = mysql_query($sqlupd);
}else{
	$sqlin = "insert into $app_setting_master (`the_key_name`,`the_value`,`last_updated_datetime`) values ('carry_forward_process_message','$str_carry_forward_process_message','$last_updated_datetime')";
	$resin = mysql_query($sqlin);
}

$res = array("process_sts"=>"YES","process_message"=>"Success");
mysql_close();
echo json_encode($res);
?>
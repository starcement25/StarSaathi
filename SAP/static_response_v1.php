<?php
ini_set('memory_limit', '9999M');
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
include "star_connection.php";
$app_setting_master = "app_setting_master";
/*---gst verification flag is maintained from admin > app setting---*/
$gst_verification = false;
$sqlgv = "select `the_value` from $app_setting_master where `the_key_name`='gst_verification' limit 1";
$resgv = mysql_query($sqlgv);
if($resgv && mysql_num_rows($resgv)>0){
	$rowgv = mysql_fetch_assoc($resgv);
	$the_value = $rowgv["the_value"] ? strtolower(trim($rowgv["the_value"])) : "";
	$gst_verification = ($the_value=="true") ? true : false;
}
$res_data = array("process_status"=>"YES","process_message"=>$gst_verification);
echo json_encode($res_data);
mysql_close();
?>

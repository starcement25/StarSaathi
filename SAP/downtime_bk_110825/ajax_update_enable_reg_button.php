<?php
include "star_connection.php";
$app_setting_master = "app_setting_master";
$res = array();
$is_show_reg_btn = $_POST["is_show_reg_btn"] ? addslashes(trim($_POST["is_show_reg_btn"])) : "YES";
if($is_show_reg_btn!=""){
$last_updated_datetime = date('Y-m-d H:i:s');
$sqlckrds = "select `the_value` from $app_setting_master where `the_key_name`='is_show_reg_btn'";
$resckrds = mysql_query($sqlckrds);
$totresckrds = mysql_num_rows($resckrds);
if($totresckrds>0){
	$sqlupd = "update $app_setting_master set `the_value`='$is_show_reg_btn',`last_updated_datetime`='$last_updated_datetime' where `the_key_name`='is_show_reg_btn'";
	$resupd = mysql_query($sqlupd);
}else{
	$sqlin = "insert into $app_setting_master (`the_key_name`,`the_value`,`last_updated_datetime`) values ('is_show_reg_btn','$is_show_reg_btn','$last_updated_datetime')";
	$resin = mysql_query($sqlin);
}

$res = array("process_sts"=>"YES","process_message"=>"Success");
}else{
$res = array("process_sts"=>"NO","process_message"=>"Something went wrong.");	
}
mysql_close();
echo json_encode($res);
?>
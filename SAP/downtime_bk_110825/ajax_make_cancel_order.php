<?php
session_start();
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$do_order_cancel_by_log = "do_order_cancel_by_log";
$res_msg = array();

if(!isset($_SESSION["start_report_admin"])){
	$res_msg = array("process_sts"=>"NO","process_msg"=>"Session is expired. Please login and try again.");
}else{

$ordr_cncl_id = $_POST["ordr_cncl_id"] ? addslashes(trim($_POST["ordr_cncl_id"])) : "";
$cncl_remark = $_POST["cncl_remark"] ? addslashes(trim($_POST["cncl_remark"])) : "";
if($ordr_cncl_id!='' && $cncl_remark!=''){

$sql_cncl = "update $t_apperpdo set `STATUS`='Order canceled' where `APPORDERNO`='$ordr_cncl_id' and (`STATUS`='Order received' or `STATUS`='Order authorized')";
$res_cncl = mysql_query($sql_cncl);
if($res_cncl){
$cancel_datetime = date("Y-m-d H:i:s");
$cancel_by = $_SESSION["start_report_admin"];
$sql_in_log = "insert into $do_order_cancel_by_log (`order_id`,`cancel_by`,`c_remark`,`cancel_datetime`) values ('$ordr_cncl_id','$cancel_by','$cncl_remark','$cancel_datetime')";
$res_in_log = mysql_query($sql_in_log);
}

$res_msg = array("process_sts"=>"YES","process_msg"=>"The order successfuly cancelled.");
}else{
	$res_msg = array("process_sts"=>"NO","process_msg"=>"Something went wrong.");
}
}
echo json_encode($res_msg);
mysql_close();
?>
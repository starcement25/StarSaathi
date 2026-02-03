<?php
ini_set('memory_limit', '9999M');
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
include "star_connection.php";
$table_name = "notification";
$notification_read_table = "notification_read_table";
$directory = "directory";
$association_code_str = "";
$server_url1 = "http://" . $_SERVER['SERVER_NAME']."/";
$res_data = array();
$notification_history = array();
$member_id = $_POST["the_id"] ? addslashes(trim($_POST["the_id"])) : "";
$noti_id = $_POST["noti_id"] ? addslashes(trim($_POST["noti_id"])) : "";
if($member_id!='' && $noti_id!=''){

$sql = "select `nrt_id` from $notification_read_table where `noti_id`='$noti_id'  and `member_id`='$member_id' ";
$res = mysql_query($sql);
$totnorows = mysql_num_rows($res);
if($totnorows>0){
	$res_data = array("process_status"=>"YES","process_message"=>"You have already read this message.");
}else{
	$datetime = date("Y-m-d H:i:s");
$sql2 = "insert into $notification_read_table (`noti_id`,`member_id`,`datetime`) values ('$noti_id','$member_id','$datetime') ";
$res2 = mysql_query($sql2);
if($res2){
	$res_data = array("process_status"=>"YES","process_message"=>"Success.");
}else{
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.");
}
}

}else{
	$res_data = array("process_status"=>"NO","process_message"=>"Please provide all values.");
}
echo json_encode($res_data);
mysql_close();
?>
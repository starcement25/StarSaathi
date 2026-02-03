<?php
error_reporting(E_STRICT);
ini_set('memory_limit', '99999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
include "star_connection.php";
include "cron_page_start.php";

$t_apperpdo = "T_APPERPDO";


$res_data = array();
function process_update_tracking($process_name,$process_start_end_flag,$remark){
$sync_cron = "cron_update_table";
$curr_datetime = date("Y-m-d H:i:s");
$process_name = $process_name ? $process_name : "";
$process_start_end_flag = $process_start_end_flag ? $process_start_end_flag : "";
$remark = $remark ? trim(addslashes($remark)) : "";
if($process_name!="" && $process_start_end_flag!=""){
	if($process_start_end_flag=="START"){
		$sql = "update $sync_cron set `last_start_datetime`='$curr_datetime',`remark`='$remark' where `process_name`='$process_name'";
		mysql_query($sql);
	}else if($process_start_end_flag=="END"){
		$sql = "update $sync_cron set `last_end_datetime`='$curr_datetime',`remark`='$remark' where `process_name`='$process_name'";
		mysql_query($sql);
	}
}
return;	
}


/*---------REMOVE OLD ORDER STATUS('Order received','Order canceled') T_APPERPDO TABLE FUNCTION START----*/
function remove_old_order(){
$old_date = date('Y-m-d',strtotime("-15 days"));
$remove_old_order = "remove_old_order";
$t_apperpdo = "T_APPERPDO";

$sql1 = "SELECT `APPORDERNO`,DATE_FORMAT(`order_date`,'%Y-%m-%d') as `ord_dt` FROM $t_apperpdo WHERE `STATUS` in('Order received','Order canceled') and `APPORDERNO`!='' and `APPORDERNO` is not null having `ord_dt`<='".$old_date."' order by `APPORDERNO` asc";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
	$apporderno = $row1["APPORDERNO"] ? addslashes(trim($row1["APPORDERNO"])) : "";
	$sql_dlt = "delete from $t_apperpdo WHERE `APPORDERNO`='$apporderno'";
	$res_dlt = mysql_query($sql_dlt);
	}	
}
process_update_tracking($remove_old_order,"END","Process End");
return;
}
/*---------REMOVE OLD ORDER STATUS('Order received','Order canceled') T_APPERPDO TABLE FUNCTION END----*/

	/*--------------CRON PROCESS NAMES--------------------*/
	$remove_old_order = "remove_old_order";
	process_update_tracking($remove_old_order,"START","Process Start");
	remove_old_order();


$res_data = array("process_status"=>"YES","process_message"=>"DONE");
echo json_encode($res_data);
include "cron_page_end.php";

mysql_close();
?>
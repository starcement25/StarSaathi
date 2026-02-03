<?php
error_reporting(E_STRICT);
ini_set('memory_limit', '99999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
include "star_connection.php";
$t_main_order_pop = "T_MAIN_ORDER_POP";
$t_order_pop = "T_ORDER_POP";
$useragent = $_SERVER['HTTP_USER_AGENT'];
$cron_update_table = "cron_update_table";

$res_data = array();
function isJSON_pop($string){
   return is_string($string) && is_array(json_decode($string, true)) ? true : false;
}
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

$prev_date = date('Y-m-d',strtotime("-12 days"));
$prev_date_time = $prev_date." 00:00:00";
$the_process_name = "make_pop_order_pg_status";
process_update_tracking($the_process_name,"START","Process Start");


$sql_ord_ck = "select * from $t_main_order_pop where `order_status` not in('Aborted','Failure','Invalid','Shipped','Timeout','Unsuccessful') and `order_datetime`>='$prev_date_time' order by `order_id` asc ";
$res_ord_ck = mysql_query($sql_ord_ck);
$totres_ord_ck = mysql_num_rows($res_ord_ck);
if($totres_ord_ck>0){
while($row_ord_ck = mysql_fetch_assoc($res_ord_ck)){
$mordpop_id = $row_ord_ck["order_id"] ? trim($row_ord_ck["order_id"]) : "";
$customer_region = $row_ord_ck["customer_region"] ? trim($row_ord_ck["customer_region"]) : "";

$the_url = "";
$the_url_fail_safe = "";
if($customer_region=="NE"){
$the_url = BASE_URL . "ccavenue_pg/check_pop_order_payment_status_ccavenue_ne.php?order_id=".$mordpop_id;
$the_url_fail_safe = BASE_URL . "ccavenue_pg/check_pop_order_payment_status_ccavenue.php?order_id=".$mordpop_id;		
}else if($customer_region=="ROE"){
$the_url = BASE_URL . "ccavenue_pg/check_pop_order_payment_status_ccavenue.php?order_id=".$mordpop_id;	
$the_url_fail_safe = BASE_URL . "ccavenue_pg/check_pop_order_payment_status_ccavenue_ne.php?order_id=".$mordpop_id;
}

if($the_url!=""){

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $the_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
$result = curl_exec($ch);
curl_close($ch);

if($result==""){

}else{

if(isJSON_pop($result)=== FALSE){
	
}else{
$poptdata_arr = json_decode($result,true);
if(array_key_exists("process_status",$poptdata_arr)){
if($poptdata_arr["process_status"]=="YES"){
$the_order_status = addslashes($poptdata_arr["the_order_status"]);
$the_order_card_name = addslashes($poptdata_arr["the_order_card_name"]);
$the_tracking_id = addslashes($poptdata_arr["the_tracking_id"]);
$the_bank_ref_no = addslashes($poptdata_arr["the_bank_ref_no"]);


$sql_mord_upd = "update $t_main_order_pop set `order_status`='$the_order_status',`tracking_id`='$the_tracking_id',`card_name`='$the_order_card_name',`bank_ref_no`='$the_bank_ref_no' where `order_id`='$mordpop_id'";
$res_mord_upd = mysql_query($sql_mord_upd);

$sql_mord_upd2 = "update $t_order_pop set `status`='$the_order_status',`the_tracking_id`='$the_tracking_id' where `the_order_id`='$mordpop_id'";
$res_mord_upd2 = mysql_query($sql_mord_upd2);

}else{
	
/*---FAIL SAFE---*/

if($the_url_fail_safe!=""){

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $the_url_fail_safe);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
$result = curl_exec($ch);
curl_close($ch);

if($result==""){
	
}else{
if(isJSON_pop($result)=== FALSE){
	
}else{
	
$poptdata_arr2 = json_decode($result,true);
if(array_key_exists("process_status",$poptdata_arr2)){
if($poptdata_arr2["process_status"]=="YES"){
$the_order_status = addslashes($poptdata_arr2["the_order_status"]);
$the_order_card_name = addslashes($poptdata_arr2["the_order_card_name"]);
$the_tracking_id = addslashes($poptdata_arr2["the_tracking_id"]);
$the_bank_ref_no = addslashes($poptdata_arr2["the_bank_ref_no"]);


$sql_mord_upd = "update $t_main_order_pop set `order_status`='$the_order_status',`tracking_id`='$the_tracking_id',`card_name`='$the_order_card_name',`bank_ref_no`='$the_bank_ref_no' where `order_id`='$mordpop_id'";
$res_mord_upd = mysql_query($sql_mord_upd);

$sql_mord_upd2 = "update $t_order_pop set `status`='$the_order_status',`the_tracking_id`='$the_tracking_id' where `the_order_id`='$mordpop_id'";
$res_mord_upd2 = mysql_query($sql_mord_upd2);
		
}

}
}

	
	
}

}


}
	
}


}

}

}

}


}
	

process_update_tracking($the_process_name,"END","Process End");
$res_data = array("process_status"=>"YES","process_message"=>"DONE");
echo json_encode($res_data);
mysql_close();
?>
<?php
error_reporting(E_STRICT);
ini_set('memory_limit', '99999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$ledger = "ledger";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";
$product_master = "product_master";
$cron_update_table = "cron_update_table";
$curr_date_time = date("Y-m-d H:i:s");
$curr_date = date("Y-m-d");
$prev_date = date('Y-m-d',strtotime("-12 days"));
$res_data = array();
$process_message = "";
$in_cnt = 0;
$upd_cnt = 0;
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
function get_order_data($the_erporderno){
$t_apperpdo = "T_APPERPDO";
$order_data_arr = array("APPORDERNO"=>"","prod_code"=>"","dns_prod_code"=>"","prod_display_name"=>"","customer_code"=>"","dns_customer_code"=>"","status"=>"");
$sql1 = "SELECT `APPORDERNO`,`prod_code`,`dns_prod_code`,`prod_display_name`,`customer_code`,`dns_customer_code`,`STATUS` FROM $t_apperpdo where `ERPORDERNO`='$the_erporderno'";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
$row1=mysql_fetch_assoc($res1);
$gd_apporderno = $row1["APPORDERNO"] ? addslashes(trim($row1["APPORDERNO"])) : "";
$gd_prod_code = $row1["prod_code"] ? addslashes(trim($row1["prod_code"])) : "";
$gd_dns_prod_code = $row1["dns_prod_code"] ? addslashes(trim($row1["dns_prod_code"])) : "";
$gd_prod_display_name = $row1["prod_display_name"] ? addslashes(trim($row1["prod_display_name"])) : "";
$gd_customer_code = $row1["customer_code"] ? addslashes(trim($row1["customer_code"])) : "";
$gd_dns_customer_code = $row1["dns_customer_code"] ? addslashes(trim($row1["dns_customer_code"])) : "";
$gd_status = $row1["STATUS"] ? addslashes(trim($row1["STATUS"])) : "";
$order_data_arr = array("APPORDERNO"=>$gd_apporderno,"prod_code"=>$gd_prod_code,"dns_prod_code"=>$gd_dns_prod_code,"prod_display_name"=>$gd_prod_display_name,"customer_code"=>$gd_customer_code,"dns_customer_code"=>$gd_dns_customer_code,"status"=>$gd_status);
}
return $order_data_arr;	
}

$the_process_name = "update_appord_id_prod_cast_details_in_challan_table";
process_update_tracking($the_process_name,"START","Process Start");

$cnt = 0;
/*$sql1 = "SELECT `ERPORDERNO` FROM $t_dochallan where (`ERPORDERNO`!='' || `ERPORDERNO` is not null) group by `ERPORDERNO` order by `ERPORDERNO` asc";*/
/*$sql1 = "SELECT `ERPORDERNO` FROM $t_dochallan where (`ERPORDERNO`!='' || `ERPORDERNO` is not null) and (`APPORDERNO`='' || `APPORDERNO` IS NULL)  group by `ERPORDERNO` order by `ERPORDERNO` asc";*/
$sql1 = "SELECT `ERPORDERNO` FROM $t_dochallan where ((`ERPORDERNO`!='' || `ERPORDERNO` is not null) and `ERPORDERNO` IN(SELECT `ERPORDERNO` FROM T_APPERPDO where STATUS IN('DO APPROVED','CREDIT CHECK FAILED'))) OR (`APPORDERNO`='' || `APPORDERNO` IS NULL)  group by `ERPORDERNO` order by `ERPORDERNO` asc";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
while($row1=mysql_fetch_assoc($res1)){
$the_erporderno = $row1["ERPORDERNO"] ? addslashes(trim($row1["ERPORDERNO"])) : "";
if($the_erporderno!=""){
$ord_data = array();
$ord_data = get_order_data($the_erporderno);
$the_apporderno = $ord_data["APPORDERNO"];
if($the_apporderno!=""){
$the_prod_code = $ord_data["prod_code"];
$the_dns_prod_code = $ord_data["dns_prod_code"];
$the_prod_display_name = $ord_data["prod_display_name"];
$the_customer_code = $ord_data["customer_code"];
$the_dns_customer_code = $ord_data["dns_customer_code"];
$the_status = $ord_data["status"];
$sql_upd_chaln = "update $t_dochallan set `APPORDERNO`='$the_apporderno',`prod_code`='$the_prod_code',`dns_prod_code`='$the_dns_prod_code',`prod_display_name`='$the_prod_display_name',`customer_code`='$the_customer_code',`dns_customer_code`='$the_dns_customer_code' where `ERPORDERNO`='$the_erporderno'";
$res_upd_chaln = mysql_query($sql_upd_chaln);
if($the_status!="Dispatched" && $the_status!="Order canceled"){
$sql_upd_ord = "update $t_apperpdo set `STATUS`='Dispatched' where `APPORDERNO`='$the_apporderno'";
$res_upd_ord = mysql_query($sql_upd_ord);

}
	
}

}
$cnt++;
}
}

process_update_tracking($the_process_name,"END","Process End");
$process_message = $cnt." record updated";


$res_data = array("process_status"=>"YES","process_message"=>$process_message);
echo json_encode($res_data);
mysql_close();
?>
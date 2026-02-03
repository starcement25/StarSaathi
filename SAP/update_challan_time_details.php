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
//$prev_date = date('Y-m-d',strtotime("-12 days"));
$prev_date_time = date('Y-m-d H:i:s', strtotime('-1 hour'));
$the_date = date("Y-m-d",strtotime($prev_date_time));
$the_hour = date("H",strtotime($prev_date_time));
$the_minute = date("i",strtotime($prev_date_time));
$res_data = array();
$process_message = "";
$in_cnt = 0;
$upd_cnt = 0;
$sql1 = "select * from $t_dochallan";
if($sql1!=""){
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	$updatecnt=1;
while($row1=mysql_fetch_assoc($res1)){
		$apporder_no_chtl = $row1["APPORDERNO"];
		$erporder_no_chtl = $row1["ERPORDERNO"];
		$erporder_date_chtl = $row1["ERPORDERDT"];
		$SoTime = $row1["SoTime"];
		$challanno_chtl = $row1["CHALLANNO"];
		$challandt_chtl = $row1["CHALLANDT"];
		$InvTime = $row1["InvTime"];
		$truckno_chtl= $row1["TRUCKNO"];
		$driverno_chtl= $row1["DRIVERNO"];
		$prod_qty_chtl = $row1["QTY"];
		$challanqty_chtl = $row1["CHALLANQTY"];
		$the_id = $row1["id"];
		if($SoTime!='')
		{
			$erporder_date_chtl = date("Y-m-d",strtotime($erporder_date_chtl));
			$SoTime_str = str_replace("PT","",$SoTime);
			$SoTime_str = str_replace("H","",$SoTime_str);
			$SoTime_str = str_replace("M","",$SoTime_str);
			$SoTime_str = str_replace("S","",$SoTime_str);
		}
		$erporder_date_chtl=$erporder_date_chtl.' '.$SoTime_str;
		$erporder_date_chtl = date("Y-m-d H:i:s",strtotime($erporder_date_chtl));
		
		if($InvTime!='')
		{
			$challandt_chtl = date("Y-m-d",strtotime($challandt_chtl));
			$InvTime_str = str_replace("PT","",$InvTime);
			$InvTime_str = str_replace("H","",$InvTime_str);
			$InvTime_str = str_replace("M","",$InvTime_str);
			$InvTime_str = str_replace("S","",$InvTime_str);
		}
		$challandt_chtl=$challandt_chtl.' '.$InvTime_str;
		$challandt_chtl = date("Y-m-d H:i:s",strtotime($challandt_chtl));
		echo $sql_clupd = "update $t_dochallan set `ERPORDERDT`='$erporder_date_chtl',`CHALLANDT`='$challandt_chtl' where `id`='$the_id'";
		$res_clupd = mysql_query($sql_clupd);
		echo $updatecnt++;

	}
 }
}
mysql_close();
?>
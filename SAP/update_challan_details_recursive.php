<?php
error_reporting(E_STRICT);
ini_set('memory_limit', '99999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$delivery_status_log = "delivery_status_log";
$ledger = "ledger";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";
$product_master = "product_master";
$cron_update_table = "cron_update_table";
$curr_date_time = date("Y-m-d H:i:s");
$curr_date = date("Y-m-d");
//$curr_date='2023-03-10';
//$prev_date = date('Y-m-d',strtotime("-12 days"));
$prev_date_time = date('Y-m-d H:i:s', strtotime('-1 hour'));
$the_date = date("Y-m-d",strtotime($prev_date_time));
$the_hour = date("H",strtotime($prev_date_time));
$the_minute = date("i",strtotime($prev_date_time));
$res_data = array();
$process_message = "";
$in_cnt = 0;
$upd_cnt = 0;
//$the_date = "2023-03-10T00:00:00";
$the_date = $the_date."T00:00:00";
for($i=1;$i<=270;$i++)
{
	echo $prev_date_time_val = date('Y-m-d', strtotime("-$i days,$curr_date"));
	//$prev_date_time_val="2022-09-29";
	//echo '<br />';
//$the_date = "2022-09-12T00:00:00";
$the_date = $prev_date_time_val."T00:00:00";
$the_from_time = "PT00H00M00S";
$the_to_time = "PT23H59M59S";
$the_filter = '&$filter=(InvDate eq datetime\''.$the_date.'\' and (InvTime ge time\''.$the_from_time.'\' and InvTime le time\''.$the_to_time.'\') )';
echo $url_ck1 = 'https://PRDAPP1.starcement.co.in:44310/sap/opu/odata/sap/ZSD_CUSTOMER_DELIVERY_SRV/ZDELV_OUTSet?$format=json'.str_replace(" ","%20",$the_filter);

$body_for_mcode1 = get_data_from_cserver($url_ck1);
/*$filename='delivery_log'.$prev_date_time_val.'.txt';
$fp = fopen("/home/starsaathi/public_html/SAP/deliverylog/$filename","wb");
	fwrite($fp,$body_for_mcode1);
	fclose($fp);
	$file_url="http://starsaathi.com/SAP/deliverylog/".$filename;
$sqlinsertlog="insert into $delivery_status_log 
				SET `date_time`=CURRENT_TIMESTAMP(),`request_params`='".addslashes($url_ck1)."',`response_value`='".addslashes($file_url)."'";
$resinsertlog = mysql_query($sqlinsertlog);*/
//exit();
if(isJsonCk($body_for_mcode1)){
$json_decoded = json_decode($body_for_mcode1,true);
if(count($json_decoded)>0){
if(array_key_exists("d",$json_decoded)){
	$app_results_arr = $json_decoded["d"]["results"];
if(count($app_results_arr)>0){
	foreach($app_results_arr as $app_results_aval){
		$Mandt = $app_results_aval["Mandt"] ? trim($app_results_aval["Mandt"]) : "";
		$InvNo = $app_results_aval["InvNo"] ? trim($app_results_aval["InvNo"]) : "";
		$InvItem = $app_results_aval["InvItem"] ? trim($app_results_aval["InvItem"]) : "";
		$InvDate = $app_results_aval["InvDate"] ? trim($app_results_aval["InvDate"]) : "";
		$InvDate_format = "";
		if($InvDate!=""){
		$InvDate_str = str_replace("/","",$InvDate);
		$InvDate_str = str_replace("Date","",$InvDate_str);	
		$InvDate_str = str_replace("(","",$InvDate_str);
		$InvDate_str = str_replace(")","",$InvDate_str);
		$InvDate_str = ($InvDate_str / 1000);
		$InvDate_format = date("Y-m-d H:i:s",$InvDate_str);
		}
		$InvTime = $app_results_aval["InvTime"] ? trim($app_results_aval["InvTime"]) : "";
		$InvQty = $app_results_aval["InvQty"] ? trim($app_results_aval["InvQty"]) : "";
		$SoNo = $app_results_aval["SoNo"] ? trim($app_results_aval["SoNo"]) : "";
		$SoItem = $app_results_aval["SoItem"] ? trim($app_results_aval["SoItem"]) : "";
		$SoDate = $app_results_aval["SoDate"] ? trim($app_results_aval["SoDate"]) : "";
		$SoDate_format = "";
		if($SoDate!=""){
		$SoDate_str = str_replace("/","",$SoDate);
		$SoDate_str = str_replace("Date","",$SoDate_str);	
		$SoDate_str = str_replace("(","",$SoDate_str);
		$SoDate_str = str_replace(")","",$SoDate_str);
		$SoDate_str = ($SoDate_str / 1000);
		$SoDate_format = date("Y-m-d H:i:s",$SoDate_str);
		}
		$SoTime = $app_results_aval["SoTime"] ? trim($app_results_aval["SoTime"]) : "";
		
		if($SoTime!='')
		{
			$SoDate_format = date("Y-m-d",strtotime($SoDate_format));
			$SoTime_str = str_replace("PT","",$SoTime);
			$SoTime_str = str_replace("H","",$SoTime_str);
			$SoTime_str = str_replace("M","",$SoTime_str);
			$SoTime_str = str_replace("S","",$SoTime_str);
		}
		$SoDate_format=$SoDate_format.' '.$SoTime_str;
		$SoDate_format = date("Y-m-d H:i:s",strtotime($SoDate_format));
		
		if($InvTime!='')
		{
			$InvDate_format = date("Y-m-d",strtotime($InvDate_format));
			$InvTime_str = str_replace("PT","",$InvTime);
			$InvTime_str = str_replace("H","",$InvTime_str);
			$InvTime_str = str_replace("M","",$InvTime_str);
			$InvTime_str = str_replace("S","",$InvTime_str);
		}
		$InvDate_format=$InvDate_format.' '.$InvTime_str;
		$InvDate_format = date("Y-m-d H:i:s",strtotime($InvDate_format));
		
		$SoQty = $app_results_aval["SoQty"] ? trim($app_results_aval["SoQty"]) : "";
		$TruckNo = $app_results_aval["TruckNo"] ? trim($app_results_aval["TruckNo"]) : "";
		$DriverNo = $app_results_aval["DriverNo"] ? trim($app_results_aval["DriverNo"]) : "";
		$TransporterName = $app_results_aval["TransporterName"] ? trim($app_results_aval["TransporterName"]) : "";
		$Plant = $app_results_aval["Plant"] ? trim($app_results_aval["Plant"]) : "";
		
		
if($InvNo!=""){
$sql1 = "SELECT `id`,`CHALLANNO` FROM $t_dochallan where `CHALLANNO`='$InvNo'";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
$row1=mysql_fetch_assoc($res1);
$the_id = $row1["id"] ? addslashes(trim($row1["id"])) : "";

$sql_clupd = "update $t_dochallan set `ERPORDERNO`='$SoNo',`ERPORDERDT`='$SoDate_format',`SoTime`='$SoTime',`InvItem`='$InvItem',`CHALLANDT`='$InvDate_format',`InvTime`='$InvTime',`QTY`='$SoQty',`CHALLANQTY`='$InvQty',`TRUCKNO`='$TruckNo',`DRIVERNO`='$DriverNo',`LMDT`='$curr_date_time',`transporter_name`='$TransporterName',`plant`='$Plant'   where `id`='$the_id'";
$res_clupd = mysql_query($sql_clupd);
$upd_cnt++;
}else{
$sql_clin = "insert into $t_dochallan (`ERPORDERNO`,`ERPORDERDT`,`SoTime`,`InvItem`,`CHALLANNO`,`CHALLANDT`,`InvTime`,`QTY`,`CHALLANQTY`,`TRUCKNO`,`DRIVERNO`,`LMDT`,`transporter_name`,`plant`) values ('$SoNo','$SoDate_format','$SoTime','$InvItem','$InvNo','$InvDate_format','$InvTime','$SoQty','$InvQty','$TruckNo','$DriverNo','$curr_date_time','$TransporterName','$Plant')";
$res_clin = mysql_query($sql_clin);
if($res_clin){
$in_cnt++;
}

}

}
		
		
		
	}
}
}
}
}
}

$process_message = $in_cnt." record inserted , ".$upd_cnt." record updated";


$res_data = array("process_status"=>"YES","process_message"=>$process_message);
echo json_encode($res_data);
mysql_close();
?>
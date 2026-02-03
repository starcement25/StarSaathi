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
$curr_date_time_formated=str_replace(':','_',$curr_date_time);
$curr_date_time_formated=str_replace(' ','_',$curr_date_time_formated);
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
$the_date = "2024-08-21T00:00:00";
$the_from_time = "PT00H00M00S";
//$the_from_time = "PT12H00M01S";
$the_to_time = "PT23H59M59S";
//$the_to_time = "PT13H00M59S";
//$the_date = "2023-04-24T00:00:00";

//https://PRDAPP1.starcement.co.in:44310/sap/opu/odata/sap/ZOVW_DELIVERY_CDS/ZOVW_DELIVERY(p_Dt=datetime'2023-04-18T00:00:00',p_tmFrm=time'PT12H00M01S',p_tmTo=time'PT13H00M59S')/Set?$format=json&sap-client=900

echo $url_ck1 = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/ZOVW_DELIVERY_CDS/ZOVW_DELIVERY(p_Dt=datetime\''.$the_date.'\',p_tmFrm=time\''.$the_from_time.'\',p_tmTo=time\''.$the_to_time.'\')/Set?$format=json&sap-client=900';

echo $body_for_mcode1 = get_data_from_cserver($url_ck1);

if(isJsonCk($body_for_mcode1)){
$json_decoded = json_decode($body_for_mcode1,true);
if(count($json_decoded)>0){
if(array_key_exists("d",$json_decoded)){
	$app_results_arr = $json_decoded["d"]["results"];
if(count($app_results_arr)>0){
	foreach($app_results_arr as $app_results_aval){
		
						 
		//$Mandt = $app_results_aval["Mandt"] ? trim($app_results_aval["Mandt"]) : "";
		$InvNo = $app_results_aval["inv_no"] ? trim($app_results_aval["inv_no"]) : "";
		$InvItem = $app_results_aval["inv_item"] ? trim($app_results_aval["inv_item"]) : "";
		$InvDate = $app_results_aval["inv_dt"] ? trim($app_results_aval["inv_dt"]) : "";
		$InvDate_format = "";
		if($InvDate!=""){
		$InvDate_str = str_replace("/","",$InvDate);
		$InvDate_str = str_replace("Date","",$InvDate_str);	
		$InvDate_str = str_replace("(","",$InvDate_str);
		$InvDate_str = str_replace(")","",$InvDate_str);
		$InvDate_str = ($InvDate_str / 1000);
		$InvDate_format = date("Y-m-d H:i:s",$InvDate_str);
		}
		$InvTime = $app_results_aval["inv_time"] ? trim($app_results_aval["inv_time"]) : "";
		$InvQty = $app_results_aval["inv_qty"] ? trim($app_results_aval["inv_qty"]) : "";
		${'InvQty'.$InvNo}=${'InvQty'.$InvNo}+$InvQty;
		${'InvQty'.$InvNo}=number_format(${'InvQty'.$InvNo},3);
		$SoNo = $app_results_aval["sono"] ? trim($app_results_aval["sono"]) : "";
		$SoItem = $app_results_aval["so_item"] ? trim($app_results_aval["so_item"]) : "";
		$SoDate = $app_results_aval["so_dt"] ? trim($app_results_aval["so_dt"]) : "";
		$SoDate_format = "";
		if($SoDate!=""){
		$SoDate_str = str_replace("/","",$SoDate);
		$SoDate_str = str_replace("Date","",$SoDate_str);	
		$SoDate_str = str_replace("(","",$SoDate_str);
		$SoDate_str = str_replace(")","",$SoDate_str);
		$SoDate_str = ($SoDate_str / 1000);
		$SoDate_format = date("Y-m-d H:i:s",$SoDate_str);
		}
		$SoTime = $app_results_aval["so_tm"] ? trim($app_results_aval["so_tm"]) : "";
		
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
		
		$SoQty = $app_results_aval["so_qty"] ? trim($app_results_aval["so_qty"]) : "";
		$TruckNo = $app_results_aval["truckno"] ? trim($app_results_aval["truckno"]) : "";
		$DriverNo = $app_results_aval["driverno"] ? trim($app_results_aval["driverno"]) : "";
		$TransporterName = $app_results_aval["transporter"] ? trim($app_results_aval["transporter"]) : "";
		$Plant = $app_results_aval["plant"] ? trim($app_results_aval["plant"]) : "";
		
		
if($InvNo!=""){
$sql1 = "SELECT `id`,`CHALLANNO` FROM $t_dochallan where `CHALLANNO`='$InvNo'";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
$row1=mysql_fetch_assoc($res1);
$the_id = $row1["id"] ? addslashes(trim($row1["id"])) : "";

echo $sql_clupd = "update $t_dochallan set `ERPORDERNO`='$SoNo',`ERPORDERDT`='$SoDate_format',`SoTime`='$SoTime',`InvItem`='$InvItem',`CHALLANDT`='$InvDate_format',`InvTime`='$InvTime',`QTY`='$SoQty',`CHALLANQTY`='${"InvQty".$InvNo}',`TRUCKNO`='$TruckNo',`DRIVERNO`='$DriverNo',`LMDT`='$curr_date_time',`transporter_name`='$TransporterName',`plant`='$Plant'   where `id`='$the_id'";
$res_clupd = mysql_query($sql_clupd);
$upd_cnt++;
}else{
$sql_clin = "insert into $t_dochallan (`ERPORDERNO`,`ERPORDERDT`,`SoTime`,`InvItem`,`CHALLANNO`,`CHALLANDT`,`InvTime`,`QTY`,`CHALLANQTY`,`TRUCKNO`,`DRIVERNO`,`LMDT`,`transporter_name`,`plant`) values ('$SoNo','$SoDate_format','$SoTime','$InvItem','$InvNo','$InvDate_format','$InvTime','$SoQty','${"InvQty".$InvNo}','$TruckNo','$DriverNo','$curr_date_time','$TransporterName','$Plant')";

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


$process_message = $in_cnt." record inserted , ".$upd_cnt." record updated";


$res_data = array("process_status"=>"YES","process_message"=>$process_message);
echo json_encode($res_data);
mysql_close();
?>
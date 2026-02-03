<?php
error_reporting(E_STRICT);
ini_set('memory_limit', '99999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$t_doinvoice = "T_DOINVOICE";
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
$curr_date = date("Ymd");
//$curr_date='20240930';
//$the_start_date_invoice = date('Ymd', strtotime("-30 days,$curr_date"));
$the_start_date_invoice='20240401';
$the_end_date_invoice = $curr_date;
$sqlcust = "SELECT `customer_id` FROM $customer_master where `acedns`='Y' and cust_type='dealer'";
$rescust = mysql_query($sqlcust);
$totcust = mysql_num_rows($rescust);
if($totcust>0){
while($rowcust=mysql_fetch_assoc($rescust)){
//$the_customer_id='1000000021';
$the_customer_id=$rowcust['customer_id'];	
		
//$the_filter2 = '&$filter=(Kunnr eq \''.$the_customer_id.'\' and DocDate eq datetime\''.$the_date_time.'\')';
$the_filter = '&$filter=(CustCo eq \''.$the_customer_id.'\' and ( InvoiceDt ge \''.$the_start_date_invoice.'\' and InvoiceDt le \''.$the_end_date_invoice.'\') )&sap-client=900';
echo $url_ck1 = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/ZSD_CUSTOMER_BULK_INVOICE_SRV/ZSD_CUSTOMER_INVOICESet?$format=json'.str_replace(" ","%20",$the_filter);
// echo $url_ck1;
$body_for_mcode10 = get_data_from_cserver($url_ck1);
// echo $body_for_mcode10;
if(isJsonCk($body_for_mcode10)){
$json_decoded21 = json_decode($body_for_mcode10,true);
if(count($json_decoded21)>0){
if(array_key_exists("d",$json_decoded21)){
	$app_results_arr = $json_decoded21["d"]["results"];
// 	print_r($app_results_arr);
// 	echo "count of app_results_arr= ".count($app_results_arr);
if(count($app_results_arr)>0){
	foreach($app_results_arr as $app_results_aval){
		$InvoiceNo = $app_results_aval["InvoiceNo"];
		$InvoiceDt = $app_results_aval["InvoiceDt"];
		
		$InvoiceDt_format = date("Y-m-d",strtotime($InvoiceDt));
		$InvoiceDt = $app_results_aval["InvoiceDt"];
		$ChallanNo = $app_results_aval["ChallanNo"];
		$Description = $app_results_aval["Description"];
		$Qty = $app_results_aval["Qty"];
		$ConDest = $app_results_aval["ConDest"];
		$VehicleNo = $app_results_aval["VehicleNo"];
		$sale_order_no = $app_results_aval["DoNo"];
		$APPORDERNO =$app_results_aval["CustPo"];
		
		if($InvoiceNo!=""){
$sql1 = "SELECT `id`,`INVNO` FROM $t_doinvoice where `INVNO`='$InvoiceNo'";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	$row1=mysql_fetch_assoc($res1);
	$the_id = $row1["id"] ? addslashes(trim($row1["id"])) : "";

	$sql_clupd = "update $t_doinvoice set `APPORDERNO`='$APPORDERNO',`ERPORDERNO`='$sale_order_no',`ERPORDERDT`='',`CHALLANDT`='',`CHALLANNO`='$ChallanNo',`INVQTY`='$Qty',`customer_code`='$the_customer_id',`TRUCKNO`='$VehicleNo',`LMDT`=CURRENT_TIMESTAMP(),`prod_display_name`='$Description',`INVNO`='$InvoiceNo',`INVDT`='$InvoiceDt',`destination`='$ConDest'   where `id`='$the_id'";
	$res_clupd = mysql_query($sql_clupd);
	$upd_cnt++;
	}else{
	echo $sql_clin = "insert into $t_doinvoice set `APPORDERNO`='$APPORDERNO',`ERPORDERNO`='$sale_order_no',`ERPORDERDT`='',`CHALLANDT`='',`CHALLANNO`='$ChallanNo',`INVQTY`='$Qty',`customer_code`='$the_customer_id',`TRUCKNO`='$VehicleNo',`LMDT`=CURRENT_TIMESTAMP(),`prod_display_name`='$Description',`INVNO`='$InvoiceNo',`INVDT`='$InvoiceDt',`destination`='$ConDest' ";
	$res_clin = mysql_query($sql_clin);
	if($res_clin){
	$in_cnt++;
	}

}
$sqlupdateorderstat="update $t_apperpdo SET `status`='Dispatched' where `APPORDERNO`='$APPORDERNO'";
$res_updateorderstat = mysql_query($sqlupdateorderstat);			
 }// end of process
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
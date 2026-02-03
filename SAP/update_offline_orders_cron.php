<?php
error_reporting(E_STRICT);
ini_set('memory_limit', '99999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
include "star_connection.php";
include "../cron_page_start.php";
$t_apperpdo_offline = "T_APPERPDO_OFFLINE";
$t_dochallan = "T_DOCHALLAN";
$customer_master = "customer_master";
$product_master = "product_master";
$cron_update_table = "cron_update_table";
$curr_date_time = date("Y-m-d H:i:s");
$curr_date_time_formated=str_replace(':','_',$curr_date_time);
$curr_date_time_formated=str_replace(' ','_',$curr_date_time_formated);
$curr_date = date("Y-m-d");
$next_date = date('Y-m-d',strtotime("+1 days, $curr_date"));
$the_hour = date("H",strtotime($prev_date_time));
$the_minute = date("i",strtotime($prev_date_time));
$res_data = array();
$process_message = "";
$in_cnt = 0;
$upd_cnt = 0;
//$the_date = "2022-08-28T00:00:00";
for($i=1;$i<=210;$i++)
{
	$prev_date_val = date('Y-m-d', strtotime("-$i days,$next_date"));
	//exit();
	//$prev_date_time_val="2022-09-29";
	//echo '<br />';
	//$the_date = "2022-09-12T00:00:00";
	$the_date = $prev_date_val."T00:00:00";
	//$the_date = $the_date."T00:00:00";
	$the_filter = '&$filter=(erdat eq datetime\''.$the_date.'\')';
$starfiori_port_no = $GLOBALS['starfiori_port_no'];
$url_ck1 = 'https://starfiori.starcement.co.in:'.$starfiori_port_no.'/sap/opu/odata/sap/ZSD_VBAK_SO_SERV_CDS/ZSD_VBAK_SO_SERV?$format=json'.str_replace(" ","%20",$the_filter);
$body_for_mcode1 = get_data_from_cserver($url_ck1);
if(isJsonCk($body_for_mcode1)){
$json_decoded = json_decode($body_for_mcode1,true);
if(count($json_decoded)>0){
if(array_key_exists("d",$json_decoded)){
	$app_results_arr = $json_decoded["d"]["results"];
if(count($app_results_arr)>0){
	foreach($app_results_arr as $app_results_aval){
		$vbeln = $app_results_aval["vbeln"] ? trim($app_results_aval["vbeln"]) : "";
		$erdat = $app_results_aval["erdat"] ? trim($app_results_aval["erdat"]) : "";
		$erdat_format = "";
		if($erdat!=""){
		$erdat_str = str_replace("/","",$erdat);
		$erdat_str = str_replace("Date","",$erdat_str);	
		$erdat_str = str_replace("(","",$erdat_str);
		$erdat_str = str_replace(")","",$erdat_str);
		$erdat_str = ($erdat_str / 1000);
		$erdat_str_format = date("Y-m-d",$erdat_str);
		}
		$erzet = $app_results_aval["erzet"] ? trim($app_results_aval["erzet"]) : "";
		if($erzet!='')
		{
			//$SoDate_format = date("Y-m-d",strtotime($SoDate_format));
			$erzet_str = str_replace("PT","",$erzet);
			$erzet_str = str_replace("H",":",$erzet_str);
			$erzet_str = str_replace("M",":",$erzet_str);
			$erzet_str = str_replace("S","",$erzet_str);
		}
		$erdat_time_str_format=$erdat_str_format.' '.$erzet_str;
		$matnr = $app_results_aval["matnr"] ? trim($app_results_aval["matnr"]) : "";
		if($matnr=='14000260'){
			continue;
		}
		$werks = $app_results_aval["werks"] ? trim($app_results_aval["werks"]) : "";

		$kunwe_ana = $app_results_aval["kunwe_ana"] ? trim($app_results_aval["kunwe_ana"]) : "";
		$inco1 = $app_results_aval["inco1"] ? trim($app_results_aval["inco1"]) : "";
		$kwmeng = $app_results_aval["kwmeng"] ? trim($app_results_aval["kwmeng"]) : "";
		$vrkme = $app_results_aval["vrkme"] ? trim($app_results_aval["vrkme"]) : "";
		$kunnr = $app_results_aval["kunnr"] ? trim($app_results_aval["kunnr"]) : "";
		$upd_tmstmp = $app_results_aval["upd_tmstmp"] ? trim($app_results_aval["upd_tmstmp"]) : "";

		$upd_format = "";
		if($upd_tmstmp!=""){
		$upd_str = str_replace("/","",$upd_format);
		$upd_str = str_replace("Date","",$upd_str);	
		$upd_str = str_replace("(","",$upd_str);
		$upd_str = str_replace(")","",$upd_str);
		$upd_str = ($upd_str / 1000);
		$upd_format = date("Y-m-d H:i:s",$upd_str);
		}
		
if($vbeln!=""){
	$sql1 = "select `prod_code`,`prod_desc` from product_master where `dns_prod_code`='$matnr'";	
	$res1 = mysql_query($sql1);
	$row1 = mysql_fetch_assoc($res1);
	$prod_code = $row1["prod_code"] ? addslashes(trim($row1["prod_code"])) : "";
	$prod_desc = $row1["prod_desc"] ? addslashes(trim($row1["prod_desc"])) : "";
	
	$sqldump = "select `dump_name` from branch_dump where `dump_code`='$werks'";	
	$resdump = mysql_query($sqldump);
	$rowdump = mysql_fetch_assoc($resdump);
	$the_dump_name = $rowdump["dump_name"] ? addslashes(trim($rowdump["dump_name"])) : "";
	
	$sqlconsignee = "select customer_code,customer_name,dns_customer_code from $customer_master where `customer_id`='$kunwe_ana'";	
	$resconsignee = mysql_query($sqlconsignee);
	$rowconsignee= mysql_fetch_assoc($resconsignee);
	$consignee_name = $rowconsignee["customer_name"] ? addslashes(trim($rowconsignee["customer_name"])) : "";
	$consignee_code = $rowconsignee["customer_code"] ? addslashes(trim($rowconsignee["customer_code"])) : "";
	$dns_consignee_code = $rowconsignee["dns_customer_code"] ? addslashes(trim($rowconsignee["dns_customer_code"])) : "";
	
	$sqldealer = "select customer_code,customer_name,dns_customer_code from $customer_master where `customer_id`='$kunnr'";	
	$resdealer = mysql_query($sqldealer);
	$rowdealer= mysql_fetch_assoc($resdealer);
	$dealer_name = $rowdealer["customer_name"] ? addslashes(trim($rowdealer["customer_name"])) : "";
	$dealer_code = $rowdealer["customer_code"] ? addslashes(trim($rowdealer["customer_code"])) : "";
	$dns_dealer_code = $rowdealer["dns_customer_code"] ? addslashes(trim($rowdealer["dns_customer_code"])) : "";			

echo $sqlchkdelivery = "SELECT `id`,`ERPORDERNO` FROM $t_dochallan where `ERPORDERNO`='$vbeln'";
$reschkdelivery = mysql_query($sqlchkdelivery);
$totchkdelivery = mysql_num_rows($reschkdelivery);
	if($totchkdelivery > 0)
	{	
		$current_status='Dispatched';
	}
	else
	{	
		$current_status='DO approved';
	}
	
$sql1 = "SELECT `id`,`ERPORDERNO` FROM $t_apperpdo_offline where `ERPORDERNO`='$vbeln'";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
$row1=mysql_fetch_assoc($res1);
$the_id = $row1["id"] ? addslashes(trim($row1["id"])) : "";

$sql_clupd = "update $t_apperpdo_offline set `STATUS`='$current_status'   where `id`='$the_id'";
$res_clupd = mysql_query($sql_clupd);
$upd_cnt++;
}else{
$sql_clin = "insert into $t_apperpdo_offline (`ERPORDERNO`,`ERPORDERDT`,`consignee_SAP_code`,`consignee_name`,`consignee_code`,`dns_consignee_code`,`customer_code`,`dns_customer_code`,`customer_SAP_code`,`customer_name`,`prod_code`,`dns_prod_code`,`prod_display_name`,`QTY`,`STATUS`,`freight`,`dump_code`,`dump_name`,`last_update_datetime`,`unit_of_measurement`) values ('$vbeln','$erdat_time_str_format','$kunwe_ana','$consignee_name','$consignee_code','$dns_consignee_code','$dealer_code','$dns_dealer_code','$kunnr','$dealer_name','$prod_code','$matnr','$prod_desc','$kwmeng','$current_status','$inco1','$werks','$the_dump_name','$upd_format','$vrkme')";
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
include "../cron_page_end.php";
mysql_close();
?>
<?php
error_reporting(E_STRICT);
ini_set('memory_limit', '99999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
include "star_connection.php";

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

$customer_code=$_REQUEST['customer_code'];
$the_date=$_REQUEST['dateval'];
$the_date_time=$curr_date."T00:00:00";



/*$url_ck1 = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/ZOVW_CUSTTGTACH_CDS/ZOVW_CUSTTGTACH(p_kunnr=\''.$customer_code.'\',p_yr=\''.$year.'\',p_mnth=\''.$month.'\')/Set?$format=json&sap-client=900';*/
$url_ck1 = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/ZOVW_CUSTOPNITM_CDS/ZOVW_CUSTOPNITM(p_KUNNR=\''.$customer_code.'\',p_DATE=datetime\''.$the_date_time.'\')/Set?$format=json&sap-client=900';

$body_for_mcode1 = get_data_from_cserver($url_ck1);

if(isJsonCk($body_for_mcode1)){
$json_decoded = json_decode($body_for_mcode1,true);
if(count($json_decoded)>0){
if(array_key_exists("d",$json_decoded)){
	$app_results_arr = $json_decoded["d"]["results"];
if(count($app_results_arr)>0){
	foreach($app_results_arr as $app_results_aval){
		
		//$Mandt = $app_results_aval["Mandt"] ? trim($app_results_aval["Mandt"]) : "";
		$kunnr = $app_results_aval["kunnr"] ? trim($app_results_aval["kunnr"]) : "";
		$awref = $app_results_aval["awref"] ? trim($app_results_aval["awref"]) : "";
		$bldat = $app_results_aval["bldat"] ? trim($app_results_aval["bldat"]) : "";
		$blart = $app_results_aval["blart"] ? trim($app_results_aval["blart"]) : "";
		$wsl = $app_results_aval["wsl"] ? trim($app_results_aval["wsl"]) : "";
		
		$blDate_format = "";
		if($bldat!=""){
		$bldat_str = str_replace("/","",$bldat);
		$bldat_str = str_replace("Date","",$bldat_str);	
		$bldat_str = str_replace("(","",$bldat_str);
		$bldat_str = str_replace(")","",$bldat_str);
		$bldat_str = ($bldat_str / 1000);
		$bldat_format = date("Y-m-d",$bldat_str);
		}
		
		
		$ageing_data[]=array("customercode"=>$kunnr,"document_no"=>$awref,"document_date"=>$bldat_format,"inv_amount"=>number_format($wsl,2));
	}
	 $res_data = array("process_status"=>"YES","process_message"=>"Success.","ageing_data"=>$ageing_data);
	}
  }
}
else{	
	$res_data = array("process_status"=>"NO","process_message"=>"No Records Found");
}		
}
else{	
	$res_data = array("process_status"=>"NO","process_message"=>"No Records Found");
}	
echo json_encode($res_data);
	
?>
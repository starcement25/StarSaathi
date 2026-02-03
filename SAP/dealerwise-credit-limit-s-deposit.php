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
$from_date=$_REQUEST['from_date'];
$to_date=$_REQUEST['to_date'];


 $url_ck1='https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/ZFI_CREDIT_LIMIT_CDS/ZFI_Credit_limit(kunnr=\''.$customer_code.'\')?$format=json&sap-client=900';

$body_for_mcode1 = get_data_from_cserver($url_ck1);
//echo $body_for_mcode1;
if(isJsonCk($body_for_mcode1)){
$json_decoded = json_decode($body_for_mcode1,true);
if(count($json_decoded)>0){
if(array_key_exists("d",$json_decoded)){
	$app_results_arr = $json_decoded["d"];
		if(count($app_results_arr)>0){
			//print_r($app_results_arr);\
				$kunnr = $app_results_arr["kunnr"] ? trim($app_results_arr["kunnr"]) : "";
				$name1 = $app_results_arr["name1"] ? trim($app_results_arr["name1"]) : "";
				$credit_limit = $app_results_arr["credit_limit"] ? trim($app_results_arr["credit_limit"]) : "";

				$credit_expose = $app_results_arr["credit_expose"] ? trim($app_results_arr["credit_expose"]) : "";
		}
		else{
				$kunnr = "";
				$name1 ="";
				$credit_limit = "";
				$credit_expose ="";

		}	
	}
  }
}

$the_start_date_time=$from_date."T00:00:00";
$the_end_date_time=$to_date."T00:00:00";

$url_ck2 = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/ZOVW_LEDG_SECDEP_CDS/ZOVW_LEDG_SECDEP(p_Code=\''.$customer_code.'\',p_Frm=datetime\''.$the_start_date_time.'\',p_To=datetime\''.$the_end_date_time.'\')/Set?$format=json&sap-client=900';
	
$body_for_mcode10 = get_data_from_cserver($url_ck2);
//echo $body_for_mcode10;
if(isJsonCk($body_for_mcode10)){
$json_decoded21 = json_decode($body_for_mcode10,true);
if(count($json_decoded21)>0){
if(array_key_exists("d",$json_decoded21)){
	$app_results_arr = $json_decoded21["d"]["results"];
	//print_r($app_results_arr);
if(count($app_results_arr)>0){
	foreach($app_results_arr as $app_results_aval){
		$billno  = $app_results_aval["billno"];
		$billdt = $app_results_aval["billdt"];
		$amtdr  = $app_results_aval["amtdr"];
		$amtcr  = $app_results_aval["amtcr"];
		$narration = $app_results_aval["narration"];
		$tdsamt = $app_results_aval["tdsamt"];
		
		$total_CrAmount=$total_CrAmount+$amtcr;
		$total_DrAmount=$total_DrAmount+$amtdr;
		
	}
	$balance=$total_CrAmount-$total_DrAmount;
}
else{
	$total_CrAmount = 0.00;
	$total_DrAmount =0.00;
	$balance=$total_CrAmount-$total_DrAmount;
	
}
}
  }
}
	 $res_data = array("process_status"=>"YES","process_message"=>"Success.","credit_limit"=>number_format($credit_limit,0),"credit_expose"=>number_format($credit_expose,0),"security_deposit"=>number_format($balance,0));

echo json_encode($res_data);
	
?>
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
$curr_date = date("Ymd");
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
 
$ccod_10='1010';
$ccod_17='1017';
$the_filter_10 = '&$filter=(Cocd eq \''.$ccod_10.'\'and Customer eq \''.$customer_code.'\' and Docdt eq \''.$curr_date.'\' and Spgl eq \'N\')';
$the_filter_17 = '&$filter=(Cocd eq \''.$ccod_17.'\'and Customer eq \''.$customer_code.'\' and Docdt eq \''.$curr_date.'\' and Spgl eq \'N\')';

$url_ck1 = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/ZSD_CUSTOPENITEM_SRV/ZSD_CUSTOPENITEM001Set?$format=json'.str_replace(" ","%20",$the_filter_10);

/*$url_ck1="https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/ZSD_CUSTOPENITEM_SRV/ZSD_CUSTOPENITEM001Set
?$filter=(Cocd eq '1010' and Customer eq '1000001384' and Docdt eq '2024-03-31' and Spgl eq 'N')&$format=json";*/


$body_for_mcode1 = get_data_from_cserver($url_ck1);
//echo $body_for_mcode1;
if(isJsonCk($body_for_mcode1)){
$json_decoded = json_decode($body_for_mcode1,true);
if(count($json_decoded)>0){
if(array_key_exists("d",$json_decoded)){
	$app_results_arr = $json_decoded["d"]["results"];
	//print_r($app_results_arr);
		if(count($app_results_arr)>0){
			foreach($app_results_arr as $app_results_aval){
			//print_r($app_results_arr);\
				$Cocd = $app_results_aval["Cocd"] ? trim($app_results_aval["Cocd"]) : "";
				$Drcr = $app_results_aval["Drcr"] ? trim($app_results_aval["Drcr"]) : "";
				$Fyr = $app_results_aval["Fyr"] ? trim($app_results_aval["Fyr"]) : "";
				$Docno = $app_results_aval["Docno"] ? trim($app_results_aval["Docno"]) : "";
				$Refdocno	 = $app_results_aval["Refdocno"] ? trim($app_results_aval["Refdocno"]) : "";
				$Clrdocno = $app_results_aval["Clrdocno"] ? trim($app_results_aval["Clrdocno"]) : "";

				$Lcamt_10 = $app_results_aval["Lcamt"] ? trim($app_results_aval["Lcamt"]) : "";
				$finallcamt_10=$finallcamt_10+$Lcamt_10;
			}
		}
		else{
				$Lcamt_10 ="0";
		}	
	}
  }
}
$url_ck2 = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/ZSD_CUSTOPENITEM_SRV/ZSD_CUSTOPENITEM001Set?$format=json'.str_replace(" ","%20",$the_filter_17);

/*$url_ck2="https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/ZSD_CUSTOPENITEM_SRV/ZSD_CUSTOPENITEM001Set
?$filter=(Cocd eq '1017' and Customer eq '1000001384' and Docdt eq '2024-03-31' and Spgl eq 'N')&$format=json";*/


$body_for_mcode2 = get_data_from_cserver($url_ck2);
//echo $body_for_mcode1;
if(isJsonCk($body_for_mcode2)){
$json_decoded = json_decode($body_for_mcode2,true);
if(count($json_decoded)>0){
if(array_key_exists("d",$json_decoded)){
	$app_results_arr = $json_decoded["d"]["results"];
	//print_r($app_results_arr);
		if(count($app_results_arr)>0){
			foreach($app_results_arr as $app_results_aval){
			//print_r($app_results_arr);\
				$Cocd = $app_results_aval["Cocd"] ? trim($app_results_aval["Cocd"]) : "";
				$Drcr = $app_results_aval["Drcr"] ? trim($app_results_aval["Drcr"]) : "";
				$Fyr = $app_results_aval["Fyr"] ? trim($app_results_aval["Fyr"]) : "";
				$Docno = $app_results_aval["Docno"] ? trim($app_results_aval["Docno"]) : "";
				$Refdocno	 = $app_results_aval["Refdocno"] ? trim($app_results_aval["Refdocno"]) : "";
				$Clrdocno = $app_results_aval["Clrdocno"] ? trim($app_results_aval["Clrdocno"]) : "";

				$Lcamt_17 = $app_results_aval["Lcamt"] ? trim($app_results_aval["Lcamt"]) : "";
				$finallcamt_17=$finallcamt_17+$Lcamt_17;
			}
		}
		else{
				$Lcamt_17 ="0";
		}	
	}
  }
}
//echo $finallcamt=$Lcamt_10+$Lcamt_17;
	 $res_data = array("process_status"=>"YES","process_message"=>"Success.","Cocd_1010"=>$ccod_10,"Lcamt_1010"=>number_format($finallcamt_10,0),"Cocd_1017"=>$ccod_17,"Lcamt_1017"=>number_format($finallcamt_17,0));

echo json_encode($res_data);
	
?>
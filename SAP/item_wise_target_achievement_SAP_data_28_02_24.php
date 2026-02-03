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
$year=$_REQUEST['year'];
$month=$_REQUEST['month'];


$url_ck1 = 'https://starfiori.starcement.co.in:44300/sap/opu/odata/sap/ZOVW_CUSTTGTACH_CDS/ZOVW_CUSTTGTACH(p_kunnr=\''.$customer_code.'\',p_yr=\''.$year.'\',p_mnth=\''.$month.'\')/Set?$format=json&sap-client=900';
// echo "url: ".$url_ck1;
$body_for_mcode1 = get_data_from_cserver($url_ck1);
echo "data: ".$body_for_mcode1;
if(isJsonCk($body_for_mcode1)){
$json_decoded = json_decode($body_for_mcode1,true);
if(count($json_decoded)>0){
if(array_key_exists("d",$json_decoded)){
	$app_results_arr = $json_decoded["d"]["results"];
if(count($app_results_arr)>0){
	foreach($app_results_arr as $app_results_aval){
		
		//$Mandt = $app_results_aval["Mandt"] ? trim($app_results_aval["Mandt"]) : "";
		$bukrs = $app_results_aval["bukrs"] ? trim($app_results_aval["bukrs"]) : "";
		$customercode = $app_results_aval["customercode"] ? trim($app_results_aval["customercode"]) : "";
		$itemcode = $app_results_aval["itemcode"] ? trim($app_results_aval["itemcode"]) : "";
		
		$yr = $app_results_aval["yr"] ? trim($app_results_aval["yr"]) : "";
		$MNTH = $app_results_aval["MNTH"] ? trim($app_results_aval["MNTH"]) : "";
		$TGTQTY = $app_results_aval["TGTQTY"] ? trim($app_results_aval["TGTQTY"]) : "";
		$ACHQTY = $app_results_aval["ACHQTY"] ? trim($app_results_aval["ACHQTY"]) : "";
		
		$sqlproduct="SELECT prod_desc FROM $product_master WHERE  dns_prod_code='".$itemcode."'";
		$rspopproduct=mysql_query($sqlproduct);
		$rowproduct=mysql_fetch_array($rspopproduct);
		$countpopproduct=mysql_num_rows($rspopproduct);
		if($countpopproduct > 0)  $prod_desc=$rowproduct['prod_desc'];
		else $prod_desc='';
		//$countpopproduct=mysql_num_rows($rspopproduct);
		
		$target_ach_data[]=array("bukrs"=>$bukrs,"customercode"=>$customercode,"itemcode"=>$itemcode,"yr"=>$yr,"MNTH"=>$MNTH,"TGTQTY"=>$TGTQTY,"ACHQTY"=>$ACHQTY,"itemname"=>$prod_desc);
	}
	 $res_data = array("process_status"=>"YES","process_message"=>"Success.","target_ach_data"=>$target_ach_data);
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
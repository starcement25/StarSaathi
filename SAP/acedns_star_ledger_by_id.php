<?php
// check error
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "star_connection.php";
$ledger = "ledger";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";
$app_service_track_log='app_service_track_log';
$ledger_data = array();
$ledger_balance_data = array();
$curr_date = date("Y-m-d");

date_default_timezone_set('Asia/Kolkata'); // Set the timezone to IST
$date = date('Y-m-d H:i', time());

$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
if($the_id!=""){

$sql4="select `customer_id` from $customer_master where `customer_code`='$emp_code'";

$query4=mysql_query($sql4);

$result4=mysql_fetch_assoc($query4);

$app_customer_code=$result4['customer_id'];

$sql5="insert into $app_service_track_log(`appservice_name`,`customer_code`,`datetime`) values('LEDGER','$app_customer_code','$date')";

mysql_query($sql5);

$sql3 = "select `dns_customer_code`,`customer_id` from $customer_master where `customer_code`='$the_id'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
$row3 = mysql_fetch_assoc($res3);
$the_dealer_id = trim($row3["dns_customer_code"]);
$the_customer_id = trim($row3["customer_id"]);
$dlr_code_qry = "  or `dns_customer_code`='$the_dealer_id'";
$dlr_code_qry2 = "  or `dns_customer_code`='$the_dealer_id'";	
}else{
$dlr_code_qry = "";	
$dlr_code_qry2 = "";
$the_customer_id = "";
}

if($the_customer_id!=""){
$the_date_time = $curr_date."T00:00:00";
$the_filter2 = '&$filter=(Kunnr eq \''.$the_customer_id.'\' and DocDate eq datetime\''.$the_date_time.'\')';
$url_ck2 = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/ZFI_LEDGER_ODATA_SRV/LedgerSet?$format=json'.str_replace(" ","%20",$the_filter2);

$body_for_mcode10 = get_data_from_cserver($url_ck2);
// echo $body_for_mcode10;
if(isJsonCk($body_for_mcode10)){
$json_decoded21 = json_decode($body_for_mcode10,true);
if(count($json_decoded21)>0){
if(array_key_exists("d",$json_decoded21)){
	$app_results_arr = $json_decoded21["d"]["results"];
if(count($app_results_arr)>0){
	foreach($app_results_arr as $app_results_aval){
		$Bukrs = $app_results_aval["Bukrs"] ? trim($app_results_aval["Bukrs"]) : "";
		$Kunnr = $app_results_aval["Kunnr"] ? trim($app_results_aval["BuKunnrkrs"]) : "";
		$DocDate = $app_results_aval["DocDate"] ? trim($app_results_aval["DocDate"]) : "";
		$DocDate_format = "";
		if($DocDate!=""){
		$DocDate_str = str_replace("/","",$DocDate);
		$DocDate_str = str_replace("Date","",$DocDate_str);	
		$DocDate_str = str_replace("(","",$DocDate_str);
		$DocDate_str = str_replace(")","",$DocDate_str);
		$DocDate_str = ($DocDate_str / 1000);
		//$DocDate_format = date("m/d/Y h:i:s A",$DocDate_str);
			$DocDate_format = date("m/d/Y",$DocDate_str);
		}
		$VoucherNo = $app_results_aval["VoucherNo"] ? trim($app_results_aval["VoucherNo"]) : "";
		$Menge = $app_results_aval["Menge"] ? trim($app_results_aval["Menge"]) : "";
		$Uom = $app_results_aval["Uom"] ? trim($app_results_aval["Uom"]) : "";
		$DrAmount = $app_results_aval["DrAmount"] ? trim($app_results_aval["DrAmount"]) : "";
		$CrAmount = $app_results_aval["CrAmount"] ? trim($app_results_aval["CrAmount"]) : "";
		$BalText = $app_results_aval["BalText"] ? trim($app_results_aval["BalText"]) : "";
		$converted_voucher_date = "";
		$balance = "";
		$quantity = "";

/*"Bukrs": "1010",
"Kunnr": "1000000021",
"DocDate": "\/Date(1659225600000)\/",
"VoucherNo": "0040003983",
"Menge": "0.000",
"Uom": "",
"DrAmount": "819735.000",
"CrAmount": "0.000",
"BalText": "AJOY BHATTACHARJEE"
*/

$ledger_data[] = array("customer_code"=>$the_id,"dns_customer_code"=>$the_dealer_id,"voucher_date"=>$DocDate_format,"voucher_no"=>$VoucherNo,"quantity"=>number_format($Menge,2),"amount_dr"=>number_format($DrAmount,2),"amount_cr"=>number_format($CrAmount,2),"balance"=>number_format($balance,2),"narration"=>$BalText,"entry_date"=>$DocDate_format,"converted_voucher_date"=>$converted_voucher_date,"company_code"=>$Bukrs);
		
	}
}
}
}
}




}

/*$sqlall = "select *,DATE_FORMAT(STR_TO_DATE(`voucher_date`, '%m/%d/%Y %h:%i:%s %p'), '%Y-%m-%d %H:%i:%s') as `e_date` from $ledger where `customer_code`='$the_id' $dlr_code_qry order by `e_date` desc limit 0,50";
$resall = mysql_query($sqlall);
$totall = mysql_num_rows($resall);
if($totall>0){
	while($row11=mysql_fetch_assoc($resall)){
		$customer_code = $row11["customer_code"];
		$dns_customer_code = $row11["dns_customer_code"];
		$voucher_date = $row11["voucher_date"] ? trim($row11["voucher_date"]) : "";
		if($voucher_date!=""){
			$voucher_date = date("m/d/Y h:i:s A",strtotime($voucher_date));
		}
		$voucher_no = $row11["voucher_no"];
		$quantity = $row11["quantity"]." MT";
		$amount_dr = $row11["amount_dr"];
		$amount_cr = $row11["amount_cr"];
		$balance = $row11["balance"];
		$entry_date = $row11["entry_date"];
		$converted_voucher_date = "";
		$ledger_data[] = array("customer_code"=>$customer_code,"dns_customer_code"=>$dns_customer_code,"voucher_date"=>$voucher_date,"voucher_no"=>$voucher_no,"quantity"=>$quantity,"amount_dr"=>$amount_dr,"amount_cr"=>$amount_cr,"balance"=>$balance,"narration"=>$balance,"entry_date"=>$entry_date,"converted_voucher_date"=>$converted_voucher_date);
	}	
}*/


$date = date("m/d/Y");
$link = "";
$name1 = "";
$name2 = "";
$name3 = "";
$credit_limit = "";
$credit_expose = "";

$url_ck1 = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/ZFI_CREDIT_LIMIT_CDS/ZFI_Credit_limit(kunnr=\''.$the_customer_id.'\')?$format=json&sap-client=900';
$body_for_mcode1 = get_data_from_cserver($url_ck1);
// echo $body_for_mcode1;
if(isJsonCk($body_for_mcode1)){
	$json_decoded = json_decode($body_for_mcode1,true);
	if(count($json_decoded)>0){
		if(array_key_exists("d",$json_decoded)){
		$kunnr = $json_decoded["d"]["kunnr"];
		$name1 = $json_decoded["d"]["name1"];
		$name2 = $json_decoded["d"]["name2"];
		$name3 = $json_decoded["d"]["name3"];
		$credit_limit = $json_decoded["d"]["credit_limit"];
		$credit_expose = $json_decoded["d"]["credit_expose"];
		
		
		}
		
	}
}

$ledger_balance_data = array("customer_code"=>$the_id,"dns_customer_code"=>$the_dealer_id,"the_customer_id"=>$the_customer_id,"balance"=>number_format($credit_expose,2),"date"=>$date,"link"=>$link,"credit_limit"=>number_format($credit_limit,2),"credit_expose"=>number_format($credit_expose,2),"name1"=>$name1,"name2"=>$name2,"name3"=>$name3);

$res_data = array("process_status"=>"YES","process_message"=>"Success.","ledger_data"=>$ledger_data,"ledger_balance_data"=>$ledger_balance_data);

}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"The id is mandatory.");
}	
echo json_encode($res_data);
mysql_close();
?>

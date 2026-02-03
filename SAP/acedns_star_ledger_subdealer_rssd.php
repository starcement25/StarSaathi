<?php
include "star_connection.php";
$ledger = "ledger";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";
$ledger_data = array();
$ledger_balance_data = array();
$curr_date = date("Y-m-d");
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
// echo $the_id;
$from_dt = $_REQUEST["from_dt"] ? addslashes(trim($_REQUEST["from_dt"])) : "";
$to_dt = $_REQUEST["to_dt"] ? addslashes(trim($_REQUEST["to_dt"])) : "";

if($the_id!=""){

$sql3 = "select `dns_customer_code`,`customer_id` from $customer_master where `customer_code`='$the_id'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
$row3 = mysql_fetch_assoc($res3);
$the_sub_dealer_id = trim($row3["dns_customer_code"]);
$the_customer_id = trim($row3["customer_id"]);
$dlr_code_qry = "  or `dns_customer_code`='$the_dealer_id'";
$dlr_code_qry2 = "  or `dns_customer_code`='$the_dealer_id'";	
}else{
$dlr_code_qry = "";	
$dlr_code_qry2 = "";
$the_customer_id = "";
}

if($the_customer_id!=""){
	$curr_date = date("Y-m-d");
		//$the_start_date_time = date('Y-m-d', strtotime("-7 days,$curr_date"))."T00:00:00";
		//$the_end_date_time = $curr_date."T00:00:00";
		if($from_dt=='' && $to_dt=='')
		{
		$the_start_date_time = date('Y-m-d', strtotime("-7 days,$curr_date"))."T00:00:00";
			$the_start_date_time = "2022-01-01"."T00:00:00";
		$the_end_date_time = $curr_date."T00:00:00";
		}
		else
		{
			$the_start_date_time=date('Y-m-d', strtotime($from_dt))."T00:00:00";
			$the_end_date_time=date('Y-m-d', strtotime($to_dt))."T00:00:00";
		}
$url_ck1 = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/YVW_CPSDLEDGER_CDS/YVW_CPSDLEDGER(p_Code=\''.$the_customer_id.'\',p_Frm=datetime\''.$the_start_date_time.'\',p_To=datetime\''.$the_end_date_time.'\')/Set?$format=json&sap-client=900';
//echo"<pre>";print_r($url_ck1);die;

$body_for_mcode10 = get_data_from_cserver($url_ck1);
if(isJsonCk($body_for_mcode10)){
$json_decoded21 = json_decode($body_for_mcode10,true);
if(count($json_decoded21)>0){
if(array_key_exists("d",$json_decoded21)){
	$app_results_arr = $json_decoded21["d"]["results"];
if(count($app_results_arr)>0){
	foreach($app_results_arr as $app_results_aval){
		$SUBDEALERCODE = $app_results_aval["SUBDEALERCODE"];
		$SUBDEALER = $app_results_aval["SUBDEALER"];
		$DOCDT = $app_results_aval["DOCDT"];
		$NARRATION = $app_results_aval["NARRATION"];
		$DOCNO = $app_results_aval["DOCNO"];
		$AMOUNT = $app_results_aval["AMOUNT"];
		
		$DOCDT_format = "";
		if($DOCDT!=""){
		$DOCDT_str = str_replace("/","",$DOCDT);
		$DOCDT_str = str_replace("Date","",$DOCDT_str);	
		$DOCDT_str = str_replace("(","",$DOCDT_str);
		$DOCDT_str = str_replace(")","",$DOCDT_str);
		$DOCDT_str = ($DOCDT_str / 1000);
		//$DocDate_format = date("m/d/Y h:i:s A",$DocDate_str);
		$DOCDT_format = date("m/d/Y",$DOCDT_str);
		}
		//$balance = "00.00";


$ledger_data[] = array("customer_code"=>$the_id,"dns_customer_code"=>$SUBDEALERCODE,"voucher_date"=>$DOCDT_format,"voucher_no"=>$DOCNO,"amount"=>number_format($AMOUNT,2),"narration"=>$NARRATION,"entry_date"=>$DOCDT_format);
		
	}
}
}
}
}




}


/*$date = date("m/d/Y");
$link = "";
$name1 = "";
$name2 = "";
$name3 = "";
$credit_limit = "";
$credit_expose = "";

$url_ck1 = 'https://prdapp1.starcement.co.in:44310/sap/opu/odata/sap/ZFI_CREDIT_LIMIT_CDS/ZFI_Credit_limit(kunnr=\''.$the_customer_id.'\')?$format=json&sap-client=900';
$body_for_mcode1 = get_data_from_cserver($url_ck1);
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
}*/
$balance = "00.00";
	if(count($ledger_data)==0) $message='Empty Ledger.';
	else 						$message='Success.';
$res_data = array("process_status"=>"YES","process_message"=>"$message","balance"=>number_format($balance,2),"ledger_data"=>$ledger_data);

}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"The id is mandatory.");
}	
echo json_encode($res_data);
mysql_close();
?>
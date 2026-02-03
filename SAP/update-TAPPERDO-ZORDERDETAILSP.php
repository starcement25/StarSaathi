<?php
ini_set('memory_limit', '99999M');
set_time_limit(0);
include "star_connection.php";
include "function-sfa.php";
$t_subdealer_order = "T_SUBDEALER_ORDER";
$t_apperpdo_temp = "T_APPERPDO_TEMP";
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";
$product_master = "product_master";
$app_service_track_log="app_service_track_log";
$broker_master = "broker_master";
$branch_master = "branch_master";
$app_setting_master = "app_setting_master";
$destination_wise_price = "destination_wise_price";
$branch_credit_limit_status = "branch_credit_limit_status";
$zorderdetails = "zorderdetailsp";

function show_SAP_code_from_customer_code_code($the_customer_code){
$the_cust_code = "";
$customer_master = "customer_master";
if($the_customer_code!=""){
$sql1 = "select customer_id from $customer_master where `customer_code`='$the_customer_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_SAP_cust_code = $row1["customer_id"] ? addslashes(trim($row1["customer_id"])) : "";
	}
}
return $the_SAP_cust_code;
}
function show_SAP_LZONE_from_customer_code($the_customer_code){
$the_cust_code = "";
$SAP_customer_master = "ptblcustomermaster";
$customer_master = "customer_master";
if($the_customer_code!=""){
$sql1 = "select LZONE from $SAP_customer_master where KUNNR=(select customer_id from $customer_master where `customer_code`='$the_customer_code') ORDER BY AEDAT DESC,ADDITIONAL_DATA1 DESC LIMIT 0,1";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_SAP_destination = $row1["LZONE"] ? addslashes(trim($row1["LZONE"])) : "";
	}
}
return $the_SAP_destination;
}
function show_SAP_plant_from_customer_code($the_customer_code){
$the_cust_code = "";
$SAP_customer_master = "ptblcustomermaster";
$customer_master = "customer_master";
if($the_customer_code!=""){
$sql1 = "select VWERK from $SAP_customer_master where KUNNR=(select customer_id from $customer_master where `customer_code`='$the_customer_code') ORDER BY AEDAT DESC,ADDITIONAL_DATA1 DESC LIMIT 0,1";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_SAP_plant = $row1["VWERK"] ? addslashes(trim($row1["VWERK"])) : "";
	}
}
return $the_SAP_plant;
}

$sql2 = "SELECT * FROM `T_APPERPDO` WHERE `APPORDERNO` NOT IN(SELECT `APPORDERNO` FROM `zorderdetailsp`) AND `order_date` like '%2023-11-02%' ORDER BY `T_APPERPDO`.`order_date` DESC";	
$res2 = mysql_query($sql2);
$totres2 = mysql_num_rows($res2);
$countexe=0;
while($row2 = mysql_fetch_array($res2))
{
	$countexe++;
	$apporderno=$row2['APPORDERNO'];
	$order_date=$row2['order_date'];
	$SAP_order_date=substr($order_date,0,10);
	$SAP_order_time=substr($order_date,11,5);
	$customer_code=$row2['customer_code'];
	$sub_dealer_code=$row2['sub_dealer_code'];
	$SAP_customer_code = show_SAP_code_from_customer_code_code($customer_code);
	$SAP_sub_dealer_code = show_SAP_code_from_customer_code_code($sub_dealer_code);
	$dump_code=$row2["dump_code"];
	$dns_prod_code = $row2["dns_prod_code"];
	$qty = $row2["QTY"];
	$remarks='';		
/*if($SAP_sub_dealer_code!='' && strtoupper($freight)=='FOR')
{
	$the_SAP_destination=show_SAP_LZONE_from_customer_code($sub_dealer_code);
}
if(strtoupper($freight)=='EX')
{
	$the_SAP_destination=$destination_code;
}*/
	
	$freight=$row2['freight'];
	$the_SAP_destination=show_SAP_LZONE_from_customer_code($customer_code);
	$the_SAP_plant=show_SAP_plant_from_customer_code($customer_code);
if($SAP_sub_dealer_code!=''){
$the_SAP_destination=show_SAP_LZONE_from_customer_code($sub_dealer_code);
$the_SAP_plant_consignee=show_SAP_plant_from_customer_code($sub_dealer_code);
}
	

if(strtoupper($freight)=='FOR')
{
	if($the_SAP_plant_consignee!='')
	{
		$the_SAP_plant=$the_SAP_plant_consignee;
	}
	else
	{
		$the_SAP_plant=$the_SAP_plant;
	}
}
if(strtoupper($freight)=='EXW')
{
	$the_SAP_plant=$dump_code;
}
	
	if($SAP_sub_dealer_code!='') $SAP_sub_dealer_code=$SAP_sub_dealer_code;
	else 						  $SAP_sub_dealer_code=$SAP_customer_code;
	
	
	echo $sqlinSAP="insert into $zorderdetails (`APPORDERNO`,`DATE`,`time`,`Cust_Code`,`Consignee_Code`,`Freight`,`DestinationCode`,`ProductCode`,`Qty`,`Unit`,`PLANT`,`OrderNo`,`Remarks`) 
values('$apporderno','$SAP_order_date','$SAP_order_time','$SAP_customer_code','$SAP_sub_dealer_code','$freight',
'$the_SAP_destination','$dns_prod_code','$qty','TO','$the_SAP_plant','','$remarks')";
	//exit();
	//$resinSAP = mysql_query($sqlinSAP);
}
$countexe .'rows EXECUTED';
?>
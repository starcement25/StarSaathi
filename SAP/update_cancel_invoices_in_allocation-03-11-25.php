<?php
ini_set('memory_limit', '99999M');
set_time_limit(0);
include "star_connection.php";
include "../cron_page_start.php";
$allocation_details_invoicewise  = "allocation_details_invoicewise";
$curr_date = date("Y-m-d");
//for ($i = 0; $i <=40; $i++) 
	{
    //$prev_date_3days = date('Y-m-d', strtotime("-$i days", strtotime($curr_date)));

$prev_date_3days = date('Y-m-d',strtotime("-3 days"));
//$prev_date_3days='2025-08-22';

//$sql2 = "SELECT inv_no,customer_id,APPORDERNO  FROM $allocation_details_invoicewise WHERE SUBSTRING(date_and_time,1,10)='".$prev_date_3days."' AND customer_id='1000000646' ORDER BY `date_and_time` ASC";
$sql2 = "SELECT inv_no,customer_id,APPORDERNO  FROM $allocation_details_invoicewise WHERE SUBSTRING(date_and_time,1,10)='".$prev_date_3days."'  ORDER BY `date_and_time` ASC";		
$res2 = mysql_query($sql2);
$totres2 = mysql_num_rows($res2);
$countexe=0;
$inv_no_list='';
$customer_array=array();
while($row2 = mysql_fetch_array($res2))
{
	$countexe++;
	$inv_no=$row2['inv_no'];
	$customer_id=$row2['customer_id'];
	$APPORDERNO=$row2['APPORDERNO'];
	
	$inv_no_list=$inv_no_list."'".$inv_no."'".',';
	
	if(!in_array($customer_id,$customer_array))
	{
		array_push($customer_array,$customer_id);
	}
}

$inv_no_list=substr($inv_no_list,0,-1);
	
	$selinvdate="SELECT min(`INVDT`) as start_date, max(`INVDT`) as end_date FROM T_DOINVOICE  where INVNO IN(".$inv_no_list.")";
	$rsinvdate=mysql_query($selinvdate);
		while($rowinvdat=mysql_fetch_array($rsinvdate)){
			$start_date=$rowinvdat['start_date'];
			$end_date=$rowinvdat['end_date'];
		}
	
	$curr_date = date("Ymd");
	if($start_date!='' && $end_date!=''){
			foreach($customer_array as $customer_id_val){
				${'all_inv_arr_'.$customer_id_val} = array();
			$the_customer_id=$customer_id_val;
			$the_start_date_invoice=$start_date;
			$the_end_date_invoice=$end_date;
			$starfiori_port_no = $GLOBALS['starfiori_port_no'];
//$the_filter2 = '&$filter=(Kunnr eq \''.$the_customer_id.'\' and DocDate eq datetime\''.$the_date_time.'\')';
$the_filter = '&$filter=(CustCo eq \''.$the_customer_id.'\' and ( InvoiceDt ge \''.$the_start_date_invoice.'\' and InvoiceDt le \''.$the_end_date_invoice.'\') )&sap-client=900';
$url_ck1 = 'https://starfiori.starcement.co.in:'.$starfiori_port_no.'/sap/opu/odata/sap/ZSD_CUSTOMER_BULK_INVOICE_SRV/ZSD_CUSTOMER_INVOICESet?$format=json'.str_replace(" ","%20",$the_filter);
echo $url_ck1;
$body_for_mcode10 = get_data_from_cserver($url_ck1);
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
				if($InvoiceNo!=""){
					if(!in_array($InvoiceNo,${'all_inv_arr_'.$customer_id_val}))
					{
						array_push(${'all_inv_arr_'.$customer_id_val},$InvoiceNo);
					}
				}
			}
		}
	 }
   }
 }
}
}
}
$invoice_no_list_array=str_replace("'","",$inv_no_list);
$invoice_no_list_array=explode(",",$invoice_no_list_array);
//print_r(${'all_inv_arr_1000000948'});

$countexe=0;
foreach($invoice_no_list_array as $invoice_no_list_val){
	$sqlselcustomerid="SELECT customer_id FROM allocation_details_invoicewise where inv_no='".$invoice_no_list_val."'";
	$rsselcustomerid=mysql_query($sqlselcustomerid);
	$rowselcustomerid=mysql_fetch_array($rsselcustomerid);
			$customer_id=$rowselcustomerid['customer_id'];
			if(!in_array($invoice_no_list_val,${'all_inv_arr_'.$customer_id}))
			{
		  		echo $sqlinactivate="UPDATE allocation_details_invoicewise set `inv_cancl`='yes' where 
						inv_no='".$invoice_no_list_val."' and is_offline='Online' ";
				mysql_query($sqlinactivate);
				$countexe++;
			}
}
	
echo $countexe .' Invoices  Deactivated';
?>
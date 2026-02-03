<?php
ini_set('memory_limit', '99999M');
set_time_limit(0);
include "star_connection.php";
include "../cron_page_start.php";
$allocation_details_invoicewise  = "allocation_details_invoicewise";
$curr_date = date("Y-m-d");
$countexe = 0;
$foundexe = 0;
//for ($i = 0; $i <=30; $i++) 
	{
   // $prev_date_3days = date('Y-m-d', strtotime("-$i days", strtotime($curr_date)));

$prev_date_3days = date('Y-m-d',strtotime("-3 days"));
//echo $prev_date_3days='2025-11-05';

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
//echo"<pre>";print_r($sql);die;
$inv_no_list=substr($inv_no_list,0,-1);
	$year1=date('Y');
	$year="2025";
	  $selinvdate="SELECT min(`INVDT`) as start_date, max(`INVDT`) as end_date FROM T_DOINVOICE  where INVNO IN(".$inv_no_list.") and (LEFT(INVDT, 4) ='$year' OR LEFT(INVDT, 4) ='$year1')";
	//die;
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
//echo $url_ck1;
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
				// if($InvoiceNo!=""){
				// 	if(!in_array($InvoiceNo,${'all_inv_arr_'.$customer_id_val}))
				// 	{
				// 		array_push(${'all_inv_arr_'.$customer_id_val},$InvoiceNo);
				// 	}
				// }
				if($InvoiceNo != ""){
					$CustPo = isset($app_results_aval["CustPo"]) ? trim($app_results_aval["CustPo"]) : "";
					${'all_inv_arr_'.$customer_id_val}[] = [
						'InvoiceNo' => $InvoiceNo,
						'CustPo' => $CustPo
					];
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
//echo"<pre>";print_r($invoice_no_list_array);die;
//print_r(${'all_inv_arr_1000000948'});
///-----start comment line sk 03-11-25---------
/*
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
			*/
///-----end comment line sk 03-11-25---------	
///-----start add line sk 03-11-25---------	
date_default_timezone_set('Asia/Kolkata');
$curr_date = date("Y-m-d H:i:s");  // 2025-11-03 14:30:00 (IST)
$countexe = 0;
$foundexe = 0;
//echo"<pre>";print_r($invoice_no_list_array);die;

foreach ($invoice_no_list_array as $invoice_no_list_val) {

    $sqlselcustomerid = "SELECT customer_id, APPORDERNO 
                         FROM allocation_details_invoicewise 
                         WHERE inv_no='" . mysql_real_escape_string($invoice_no_list_val) . "' ORDER BY allocation_id DESC LIMIT 1";
    $rsselcustomerid = mysql_query($sqlselcustomerid);
    $rowselcustomerid = mysql_fetch_array($rsselcustomerid);

    $customer_id = $rowselcustomerid['customer_id'];
     $apporderno  = $rowselcustomerid['APPORDERNO'];

    //echo"<pre>";print_r($customer_id);die;
    // Prevent undefined variable errors
    if (!isset(${'all_inv_arr_' . $customer_id})) {
        echo " Missing SAP data for customer $customer_id<br>";
        continue;
    }

    // --- Create combined array of InvoiceNo + CustPo pairs from SAP ---
    $sap_invoice_pairs = array();
    foreach (${'all_inv_arr_' . $customer_id} as $sap_invoice_obj) {
        // If array stores only InvoiceNo (older code), skip gracefully
        if (is_array($sap_invoice_obj) && isset($sap_invoice_obj['InvoiceNo']) && isset($sap_invoice_obj['CustPo'])) {
            $sap_invoice_pairs[] = ltrim($sap_invoice_obj['InvoiceNo'], '0') . '|' . trim($sap_invoice_obj['CustPo']);
        }
    }

    // --- Create normalized local pair for matching ---
    $normalized_pair = ltrim($invoice_no_list_val, '0') . '|' . trim($apporderno);
/*
    echo "<table border='1' cellpadding='4' cellspacing='0'>";
    echo "<tr><th>Customer</th><th>Local Invoice</th><th>Normalized</th><th>SAP Invoices</th></tr>";
    echo "<tr>";
    echo "<td>".$customer_id."</td>";
    echo "<td>".$invoice_no_list_val."</td>";
    echo "<td>".$normalized_inv."</td>";
    echo "<td><pre>".print_r($sap_invoices, true)."</pre></td>";
    echo "</tr>";
    echo "</table><br>";*/
    if (!in_array($normalized_pair, $sap_invoice_pairs)) {
        // Not found in SAP → mark canceled
        $sqlinactivate = "UPDATE allocation_details_invoicewise 
                          SET `inv_cancl`='yes' 
                          WHERE inv_no='" . mysql_real_escape_string($invoice_no_list_val) . "' 
                          AND APPORDERNO='" . mysql_real_escape_string($apporderno) . "'
                          AND is_offline='Online'";
        mysql_query($sqlinactivate);

        // Log it
        $reason = 'Invoice + APPORDERNO pair missing from SAP API response';
        $sqllog = "INSERT INTO invoice_cancel_log (inv_no, customer_id, apporderno, reason, api_checked_date, status)
                   VALUES ('" . mysql_real_escape_string($invoice_no_list_val) . "',
                           '" . mysql_real_escape_string($customer_id) . "',
                           '" . mysql_real_escape_string($apporderno) . "',
                           '" . mysql_real_escape_string($reason) . "',
                           '".$curr_date."',
                           'CANCELED')";
        mysql_query($sqllog);

        $countexe++;
    } else {
        // Found in SAP → ensure active
        $sqlactivate = "UPDATE allocation_details_invoicewise 
                        SET `inv_cancl`='no' 
                        WHERE inv_no='" . mysql_real_escape_string($invoice_no_list_val) . "' 
                        AND APPORDERNO='" . mysql_real_escape_string($apporderno) . "'";
        mysql_query($sqlactivate);

        $reason = 'Invoice + APPORDERNO confirmed in SAP API response';
        $sqllog = "INSERT INTO invoice_cancel_log (inv_no, customer_id, apporderno, reason, api_checked_date, status)
                   VALUES ('" . mysql_real_escape_string($invoice_no_list_val) . "',
                           '" . mysql_real_escape_string($customer_id) . "',
                           '" . mysql_real_escape_string($apporderno) . "',
                           '" . mysql_real_escape_string($reason) . "',
                           '".$curr_date."',
                           'FOUND')";
        //mysql_query($sqllog);

        $foundexe++;
    }
}
	}
///-----end add line sk 03-11-25---------	
echo "<br><b>$countexe Invoices Deactivated</b><br>";
echo "<b>$foundexe Invoices Marked Active</b><br>";
?>
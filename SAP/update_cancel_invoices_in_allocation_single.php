<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include "star_connection.php";
date_default_timezone_set('Asia/Kolkata');

$inv_no_to_check = "F21800013849";
$apporder_no_to_check = "SS0738587";
$customer_id = "1000003935"; // 👈 set this to correct customer_id if known

$curr_date = date("Y-m-d H:i:s");
$start_date = date('Ymd', strtotime('-60 days'));
$end_date   = date('Ymd'); // today

// 🔹 SAP API URL
$starfiori_port_no = $GLOBALS['starfiori_port_no'];
$the_filter = "&$filter=(CustCo eq '$customer_id' and (InvoiceDt ge '$start_date' and InvoiceDt le '$end_date'))&sap-client=900";
$url_ck1 = "https://starfiori.starcement.co.in:$starfiori_port_no/sap/opu/odata/sap/ZSD_CUSTOMER_BULK_INVOICE_SRV/ZSD_CUSTOMER_INVOICESet?\$format=json" . str_replace(" ", "%20", $the_filter);
echo"<pre>";print_r($url_ck1);die;

// 🔹 Fetch API data
$body_for_mcode10 = get_data_from_cserver($url_ck1);

if (!isJsonCk($body_for_mcode10)) {
    die("Invalid API Response");
}

$json_decoded21 = json_decode($body_for_mcode10, true);
$app_results_arr = $json_decoded21["d"]["results"] ?? [];

$found_in_sap = false;

// 🔹 Loop through SAP results to find match
foreach ($app_results_arr as $app_results_aval) {
    $InvoiceNo = ltrim($app_results_aval["InvoiceNo"], '0');
    $CustPo    = trim($app_results_aval["CustPo"]);

    if ($InvoiceNo == ltrim($inv_no_to_check, '0') && $CustPo == $apporder_no_to_check) {
        $found_in_sap = true;
        break;
    }
}

// 🔹 Update & Log
if ($found_in_sap) {
    echo "<b>✅ Found in SAP:</b> Invoice $inv_no_to_check | APPORDERNO $apporder_no_to_check<br>";

    $sql = "UPDATE allocation_details_invoicewise 
            SET inv_cancl='no' 
            WHERE inv_no='" . mysql_real_escape_string($inv_no_to_check) . "' 
            AND APPORDERNO='" . mysql_real_escape_string($apporder_no_to_check) . "'";
    mysql_query($sql);

    $reason = 'Invoice confirmed present in SAP API response';
    $status = 'FOUND';
} else {
    echo "<b>❌ Not found in SAP:</b> Invoice $inv_no_to_check | APPORDERNO $apporder_no_to_check<br>";

    $sql = "UPDATE allocation_details_invoicewise 
            SET inv_cancl='yes' 
            WHERE inv_no='" . mysql_real_escape_string($inv_no_to_check) . "' 
            AND APPORDERNO='" . mysql_real_escape_string($apporder_no_to_check) . "' 
            AND is_offline='Online'";
    mysql_query($sql);

    $reason = 'Invoice + APPORDERNO pair missing from SAP API response';
    $status = 'CANCELED';
}

// 🔹 Log into invoice_cancel_log
$sqllog = "INSERT INTO invoice_cancel_log (inv_no, customer_id, apporderno, reason, api_checked_date, status)
           VALUES ('" . mysql_real_escape_string($inv_no_to_check) . "',
                   '" . mysql_real_escape_string($customer_id) . "',
                   '" . mysql_real_escape_string($apporder_no_to_check) . "',
                   '" . mysql_real_escape_string($reason) . "',
                   '$curr_date',
                   '$status')";
mysql_query($sqllog);

echo "<br><b>Log entry added successfully.</b><br>";
?>

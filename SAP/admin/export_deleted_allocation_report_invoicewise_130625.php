<?php
include "star_connection.php";
// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=allocation_details_invoicewise_log.csv');

// Open output stream
$output = fopen('php://output', 'w');

// Column headers
fputcsv($output, array(
    'Allocation ID',
    'Entry Date & Time',
    'Invoice Date',
    'Order ID',
    'App Order No',
    'Invoice No',
    'Dealer ID',
    'Dealer Name',
    'Sub Dealer ID',
    'DNS Product Code',
    'Product Description',
    'Invoice Quantity',
    'Allocation Quantity',
    'Invoice Cancelled',
    'Is Offline',
    'Deleted At',
    'Deleted By (ID)',
    'Deleted By (User Name)' // Proper readable name
));

// Fetch records with JOIN
$query = "
    SELECT
        log.allocation_id,
        log.date_and_time,
        log.inv_date,
        log.order_id,
        log.APPORDERNO,
        log.inv_no,
        log.customer_id,
        cust.customer_name,
        log.sub_dealer_id,
        log.dns_prod_code,
        log.prod_desc,
        log.inv_qty,
        log.allocation_qty,
        log.inv_cancl,
        log.is_offline,
        log.deleted_at,
        log.delete_by_id,
        admin.user_name
    FROM allocation_details_invoicewise_log AS log
    LEFT JOIN startreport_admin AS admin ON admin.id = log.delete_by_id
    LEFT JOIN customer_master AS cust ON cust.customer_id = log.customer_id
";
//echo"<pre>";print_r($query);die;
$result = mysql_query($query);

// Output rows
while ($row = mysql_fetch_assoc($result)) {
    fputcsv($output, $row);
}

// Close file and connection
fclose($output);
mysqli_close($conn);
exit;
?>

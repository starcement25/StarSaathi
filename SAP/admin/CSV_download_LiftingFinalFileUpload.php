<?php
include "star_connection.php";
// Fetch original uploaded filename
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid or missing ID.");
}
$file_upload_id = (int) $_GET['id'];
$sql_filename = "SELECT CSV_file_name,zone,year,month FROM lifting_final_file_upload_csv WHERE id = $file_upload_id LIMIT 1";
$result_filename = mysql_query($sql_filename);
//echo"<pre>";print_r($sql_filename);die;
if (mysql_num_rows($result_filename) === 0) {
    die("File ID not found.");
}

$row = mysql_fetch_assoc($result_filename);
//$originalFileName = basename($row['CSV_file_name']); // sanitize
$fname='Download_'.$row['zone'].'_'.$row['month'].'_'.$row['year']. '.csv';
$originalFileName = basename($fname); // sanitize
//echo"<pre>";print_r($originalFileName);die;

// Set headers with original upload name
header('Content-Type: text/csv');
header("Content-Disposition: attachment; filename=\"$originalFileName\"");
// Set headers to download as CSV

$output = fopen('php://output', 'w');


// Output the column headers
// fputcsv($output, [
//     'Allocation Date Time',
//     'APPORDERNO',
//     'Inv Date',
//     'Linked Dealer Code',
//     'Linked Dealer SAP Code',
//     'Linked Dealer Name',
//     'Sub Dealer/RSSD Code',
//     'Sub Dealer/RSSD SAP Code',
//     'Sub Dealer/RSSD Name',
//     'Branch',
//     'Month',
//     'Product Name',
//     'Total Inv qty',
//     'Allocated qty',
//     'Remaining Allocation qty',
//     'Inv no',
//     'Order Type'
// ]);
fputcsv($output, [
    'Allocation/Approved Date Time',
    'APPORDERNO',
    'Inv/Challan Date',
    'Linked Dealer Code',
    'Linked Dealer SAP Code',
    'Linked Dealer Name',
    'Sub Dealer/RSSD Code',
    'Sub Dealer/RSSD SAP Code',
    'Sub Dealer/RSSD Name',
    'Branch',
    'Month',
    'Product Name',
    'Total Inv/Challan qty',
    'Allocated qty',
    'Remaining Allocation qty',
    'Inv/Challan no',
    'Order Type'
]);
// Fetch data from the table
$sql = "SELECT
    allocation_datetime,
    app_order_no,
    inv_date,
    linked_dealer_code,
    linked_dealer_sap_code,
    linked_dealer_name,
    sub_dealer_code,
    sub_dealer_sap_code,
    sub_dealer_name,
    branch,
    month,
    product_name,
    total_inv_qty,
    allocated_qty,
    remaining_allocation_qty,
    inv_no,
    is_offline
    FROM lifting_final_allocation_data
     WHERE file_upload_id = $file_upload_id";

$result = mysql_query($sql);
//echo"<pre>";print_r($result);die;
// Output each row
while ($row = mysql_fetch_assoc($result)) {
    fputcsv($output, $row);
}

fclose($output);
?>

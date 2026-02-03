<?php
include "star_connection.php";
ini_set('auto_detect_line_endings', TRUE);

if (isset($_FILES['import_file']['tmp_name'])) {

    $file = $_FILES['import_file']['tmp_name'];
    $handle = fopen($file, "r");

    if ($handle === FALSE) {
        die("Error opening file.");
    }

    // Read header
    $header = fgetcsv($handle);

    // Required columns (no Deleted column)
    $required = ['Invoice Number', 'Sub Dealer ID'];

    if (array_diff($required, $header)) {
        die("Invalid CSV headers. Required: " . implode(', ', $required));
    }

    // Column indexes
    $invIndex  = array_search('Invoice Number', $header);
    $subIndex  = array_search('Sub Dealer ID', $header);

    $count = 0;

    session_start();
    $user_id = $_SESSION["start_report_admin"];

    while (($data = fgetcsv($handle)) !== FALSE) {

        $inv_no        = trim($data[$invIndex]);
        $sub_dealer_id = trim($data[$subIndex]);

        if ($inv_no != '' && $sub_dealer_id != '') {

            // Insert into log table before deletion
            $log_sql = "
                INSERT INTO allocation_details_invoicewise_log (
                    allocation_id,
                    date_and_time,
                    inv_date,
                    order_id,
                    APPORDERNO,
                    inv_no,
                    customer_id,
                    sub_dealer_id,
                    dns_prod_code,
                    prod_desc,
                    inv_qty,
                    allocation_qty,
                    inv_cancl,
                    is_offline,
                    deleted_at,
                    delete_by_id
                )
                SELECT
                    allocation_id,
                    date_and_time,
                    inv_date,
                    order_id,
                    APPORDERNO,
                    inv_no,
                    customer_id,
                    sub_dealer_id,
                    dns_prod_code,
                    prod_desc,
                    inv_qty,
                    allocation_qty,
                    inv_cancl,
                    is_offline,
                    NOW() AS deleted_at,
                    '".$user_id."' AS delete_by_id
                FROM allocation_details_invoicewise
                WHERE inv_no = '".$inv_no."' 
                  AND sub_dealer_id = '".$sub_dealer_id."'
                  AND delete_at = '0'
            ";
           // echo"<pre>";print_r($log_sql);die;

            mysql_query($log_sql);

            // Soft delete
            $update_sql = "
                UPDATE allocation_details_invoicewise
                SET delete_at = '1'
                WHERE inv_no = '".$inv_no."' 
                  AND sub_dealer_id = '".$sub_dealer_id."'
            ";

            if (mysql_query($update_sql)) {
                $count++;
            }
        }
    }

    fclose($handle);

    echo "<script>
            alert('Import completed. $count records marked as deleted.');
            window.location='allocation_details_report_invoicewise.php';
          </script>";
}
else {
    echo "No file uploaded.";
}
?>

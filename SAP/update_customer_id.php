<?php
header("Content-Type: application/json");
date_default_timezone_set('Asia/Kolkata');

include_once("star_connection.php"); // mysql connection

$response = array();
$updated_count = 0;
$not_found = array();

/* ===== STEP 1: FETCH DATA ===== */
$sql = "
    SELECT allocation_id, sub_dealer_id, customer_id 
    FROM allocation_details_invoicewise 
    WHERE sub_dealer_id LIKE '%C%' 
       OR customer_id LIKE '%C%'
";

$res = mysql_query($sql);

if (!$res) {
    echo json_encode([
        "status" => "error",
        "message" => mysql_error()
    ]);
    exit;
}

/* ===== STEP 2: LOOP DATA ===== */
while ($row = mysql_fetch_assoc($res)) {

    $allocation_id   = $row['allocation_id'];
    $sub_dealer_id   = trim($row['sub_dealer_id']);
    $customer_id     = trim($row['customer_id']);

    /* ===== CASE 1: SUB DEALER ID ===== */
    if (strpos($sub_dealer_id, 'C') !== false) {

        $sqlCust = "
            SELECT customer_id 
            FROM customer_master 
            WHERE customer_code = '$sub_dealer_id'
            LIMIT 1
        ";
        $resCust = mysql_query($sqlCust);

        if ($rowCust = mysql_fetch_assoc($resCust)) {

            $new_id = $rowCust['customer_id'];

            $update = "
                UPDATE allocation_details_invoicewise 
                SET sub_dealer_id = '$new_id'
                WHERE allocation_id = '$allocation_id'
            ";

            mysql_query($update);
            $updated_count++;

        } else {
            $not_found[] = "SubDealer Code Not Found: $sub_dealer_id";
        }
    }

    /* ===== CASE 2: CUSTOMER ID ===== */
    if (strpos($customer_id, 'C') !== false) {

        $sqlCust = "
            SELECT customer_id 
            FROM customer_master 
            WHERE customer_code = '$customer_id'
            LIMIT 1
        ";
        $resCust = mysql_query($sqlCust);

        if ($rowCust = mysql_fetch_assoc($resCust)) {

            $new_id = $rowCust['customer_id'];

            $update = "
                UPDATE allocation_details_invoicewise 
                SET customer_id = '$new_id'
                WHERE allocation_id = '$allocation_id'
            ";

            mysql_query($update);
            $updated_count++;

        } else {
            $not_found[] = "Customer Code Not Found: $customer_id";
        }
    }
}

/* ===== RESPONSE ===== */
$response = array(
    "status" => "success",
    "updated_records" => $updated_count,
    "not_found" => $not_found
);

echo json_encode($response, JSON_PRETTY_PRINT);
?>
<?php
header("Content-Type: application/json");
date_default_timezone_set('Asia/Kolkata');
ini_set('memory_limit', '99999M');
set_time_limit(0);
include "star_connection.php";

/* ================= READ RAW POST ================= */
$rawInput = file_get_contents("php://input");

/*
 Example rawInput:
 ['1000000120','1000000529','1000000281']
*/

/* ================= CONVERT TO PHP ARRAY ================= */
$rawInput = trim($rawInput);

// Replace single quotes with double quotes
$json = str_replace("'", '"', $rawInput);

// Decode JSON
$customer_ids = json_decode($json, true);

/* ================= VALIDATION ================= */
if (!is_array($customer_ids) || empty($customer_ids)) {
    echo json_encode(array(
        "status" => false,
        "message" => "Invalid customer_id data"
    ));
    exit;
}

/* ================= SANITIZE ================= */
$customer_ids = array_map('intval', $customer_ids);

/* ================= CHUNK (IMPORTANT) ================= */
$chunks = array_chunk($customer_ids, 500);

$data = array();

foreach ($chunks as $ids) {

    $id_list = implode(",", $ids);

    $sql = "
        SELECT 
            customer_id AS dealer_id,
            acedns
        FROM customer_master
        WHERE customer_id IN ($id_list)
    ";

    $res = mysql_query($sql);

    if ($res) {
        while ($row = mysql_fetch_assoc($res)) {
            $data[] = $row;
        }
    }
}

/* ================= RESPONSE ================= */
echo json_encode(array(
    "status" => true,
    "count"  => count($data),
    "data"   => $data
));
exit;
?>

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

    $con="AND
    (
        (cm.`cust_type` = 'Dealer' AND cm.`customer_id` LIKE '10%')
        OR
        (cm.`cust_type` = 'RSSD' AND cm.`customer_id` LIKE '15%')
    
        OR
        (cm.`cust_type` = 'Ship to Party-dealer' AND cm.`customer_id` LIKE '14%')
        OR
        (cm.`cust_type` = 'ShiptoParty-Subdeale' AND cm.`customer_id` LIKE '14%')
    )  ";

    $sql = "
            SELECT 
            cm.customer_id AS dealer_id,
            cm.acedns,
            cm.customer_name,
            cm.address,
            cm.pin,
            cm.phone_no,
            cm.landline_no, 
            cm.route_code, 
            cm.emp_code,
            cm.zone,
            cm.region,
            cm.whatsapp_no,
            cm.plant,
            cm.appointment_date,
            cm.lattitude,
            cm.longitude,
            cm.DOB,
            cm.ANV_DATE,
            cm.email,
            cm.dns_customer_code,
            cm.branch_code,
            cm.cust_type,
            bm.branch_name
        FROM customer_master cm
        LEFT JOIN branch_master bm 
            ON bm.branch_code = cm.branch_code
        WHERE cm.customer_id IN ($id_list) $con; 
    ";

    $res = mysql_query($sql);

    if ($res) {
        while ($row = mysql_fetch_assoc($res)) {
            $data[] = $row;
        }
    }
}
function utf8ize($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } elseif (is_string($mixed)) {
        // Step 1: Replace common NBSP bytes with regular space (fixes �)
        $mixed = str_replace("\xC2\xA0", ' ', $mixed);
        $mixed = str_replace("\xA0", ' ', $mixed);
        
        // Step 2: Safe UTF-8 conversion (detect source encoding first)
        $encoding = mb_detect_encoding($mixed, ['UTF-8', 'ISO-8859-1', 'ISO-8859-15'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $mixed = mb_convert_encoding($mixed, 'UTF-8', $encoding);
        }
    }
    return $mixed;
}
/* ================= RESPONSE ================= */
//echo"<pre>";print_r($data);die;
$data = utf8ize($data);
echo json_encode(array(
    "status" => true,
    "count"  => count($data),
    "data"   => $data
));
exit;
?>

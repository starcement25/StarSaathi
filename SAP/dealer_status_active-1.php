<?php
header("Content-Type: application/json");
date_default_timezone_set('Asia/Kolkata');

include "star_connection.php"; // must return $conn (mysqli)

/* ================= UTF8 FIX ================= */
function utf8ize($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } elseif (is_string($mixed)) {

        // Replace NBSP with normal space
        $mixed = str_replace("\xC2\xA0", ' ', $mixed);
        $mixed = str_replace("\xA0", ' ', $mixed);

        // Convert encoding safely
        $encoding = mb_detect_encoding(
            $mixed,
            ['UTF-8', 'ISO-8859-1', 'ISO-8859-15'],
            true
        );

        if ($encoding && $encoding !== 'UTF-8') {
            $mixed = mb_convert_encoding($mixed, 'UTF-8', $encoding);
        }
    }

    return $mixed;
}

/* ================= PAGINATION ================= */
$page  = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 500;

if ($page < 1) $page = 1;
if ($limit < 1) $limit = 500;

$offset = ($page - 1) * $limit;

/* ================= OPTIONAL SEARCH ================= */
$customer_id = isset($_GET['customer_id']) 
    ? trim($_GET['customer_id']) 
    : '';

$search_con = "";

if ($customer_id != "") {
    $customer_id = mysql_real_escape_string($customer_id);

    $search_con = " AND cm.customer_id LIKE '%$customer_id%' ";
}

/* ================= MAIN CONDITION ================= */
$con = "
AND
(
    (cm.`cust_type` = 'Dealer' AND cm.`customer_id` LIKE '10%')
    OR
    (cm.`cust_type` = 'RSSD' AND cm.`customer_id` LIKE '15%')
    OR
    (cm.`cust_type` = 'Ship to Party-dealer' AND cm.`customer_id` LIKE '14%')
    OR
    (cm.`cust_type` = 'ShiptoParty-Subdeale' AND cm.`customer_id` LIKE '14%')
)
";

/* ================= QUERY ================= */
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
        bm.branch_name,

        (
            SELECT customer_id 
            FROM customer_master 
            WHERE customer_code = cm.rds_tag 
            LIMIT 1
        ) AS link_dealer

    FROM customer_master cm

    LEFT JOIN branch_master bm 
        ON bm.branch_code = cm.branch_code

    WHERE 
         cm.black_list = 'N'
        AND (cm.customer_id LIKE '15%' OR cm.customer_id LIKE '10%')
        $con
        $search_con

    ORDER BY cm.cust_type ASC

    LIMIT $limit OFFSET $offset
";

/* ================= EXECUTE ================= */
$result = mysql_query($sql);

if (!$result) {

    echo json_encode([
        "status" => false,
        "message" => "Query failed",
        "error" => mysql_error()
    ]);

    exit;
}

/* ================= FETCH DATA ================= */
$data = [];

while ($row = mysql_fetch_assoc($result)) {
    $data[] = $row;
}

/* ================= TOTAL COUNT ================= */
$count_sql = "
    SELECT COUNT(*) as total
    FROM customer_master cm
    WHERE 
        cm.acedns = 'Y'
        AND cm.black_list = 'N'
        AND (cm.customer_id LIKE '15%' OR cm.customer_id LIKE '10%')
        $con
        $search_con
";

$count_res = mysql_query($count_sql);

$total_row = mysql_fetch_assoc($count_res);

$total = $total_row['total'];

/* ================= RESPONSE ================= */
$data = utf8ize($data);

echo json_encode([
    "status" => true,
    "page"   => $page,
    "limit"  => $limit,
    "total"  => intval($total),
    "count"  => count($data),
    "search_customer_id" => $customer_id,
    "data"   => $data
]);

exit;
?>
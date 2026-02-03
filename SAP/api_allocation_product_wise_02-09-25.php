<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include "star_connection.php"; // your DB connection file

// Get request params
$month = isset($_GET['month']) ? intval($_GET['month']) : date("m");
$year  = isset($_GET['year']) ? intval($_GET['year']) : date("Y");
$sub_dealer_sap_code = isset($_GET['sub_dealer_sap_code']) ? trim($_GET['sub_dealer_sap_code']) : "";

// Build query
/*$sql = "SELECT 
            sub_dealer_sap_code,
            product_name,
            SUM(allocated_qty) AS total_allocated_qty
        FROM lifting_final_allocation_data
        WHERE YEAR(allocation_datetime) = '$year'
          AND MONTH(allocation_datetime) = '$month'";

if ($sub_dealer_sap_code != "") {
    $sql .= " AND sub_dealer_sap_code = '" . mysql_real_escape_string($sub_dealer_sap_code) . "'";
}

$sql .= " GROUP BY sub_dealer_sap_code, product_name ORDER BY sub_dealer_sap_code, product_name";*/

// $sql = "
// SELECT 
//     p.product_name,
//     '$sub_dealer_sap_code' AS sub_dealer_sap_code,
//     COALESCE(SUM(l.allocated_qty), 0) AS total_allocated_qty
// FROM (
//     SELECT DISTINCT product_name
//     FROM lifting_final_allocation_data 
//     WHERE product_name IS NOT NULL AND product_name <> ''
// ) p
// LEFT JOIN lifting_final_allocation_data l
//     ON l.product_name = p.product_name
//    AND YEAR(l.allocation_datetime) = '$year'
//    AND MONTH(l.allocation_datetime) = '$month'
//    AND l.sub_dealer_sap_code = '" . mysql_real_escape_string($sub_dealer_sap_code) . "'
// GROUP BY p.product_name
// ORDER BY p.product_name";
$sql="SELECT 
    p.product_name,
    '$sub_dealer_sap_code' AS sub_dealer_sap_code,
    COALESCE(SUM(l.allocated_qty), 0) AS total_allocated_qty
FROM (
    SELECT DISTINCT product_name
    FROM lifting_final_allocation_data 
    WHERE product_name IS NOT NULL AND product_name <> ''
) p
LEFT JOIN lifting_final_allocation_data l
    ON l.product_name = p.product_name
   AND l.sub_dealer_sap_code = '" . mysqli_real_escape_string($link, $sub_dealer_sap_code) . "'
LEFT JOIN lifting_final_file_upload_csv f
    ON f.id = l.file_upload_id
   AND f.year = '$year'
   AND f.month = '$month'
GROUP BY p.product_name
ORDER BY p.product_name;";
//echo"<pre>";print_r($sql);die;
$result = mysql_query($sql);

$data = [];
if ($result && mysql_num_rows($result) > 0) {
    while ($row = mysql_fetch_assoc($result)) {
        $data[] = [
            "sub_dealer_sap_code"   => $row['sub_dealer_sap_code'],
            "product_name"      => $row['product_name'],
            "allocated_qty"     => $row['total_allocated_qty']
        ];
    }
    echo json_encode([
        "status" => "success",
        "month"  => $month,
        "year"   => $year,
        "data"   => $data
    ]);
} else {
    echo json_encode([
        "status" => "no_data",
        "message" => "No records found for the given filters"
    ]);
}
?>

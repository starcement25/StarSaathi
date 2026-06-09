<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

include "star_connection.php";

$customer_id = isset($_GET['customer_id']) ? trim($_GET['customer_id']) : '';

if ($customer_id == '') {
    echo json_encode([
        "status" => false,
        "message" => "Customer ID is required"
    ]);
    exit;
}

/* ===============================
   GET CUSTOMER DATA
=================================*/
$sql = "SELECT customer_id, DOB, customer_name 
        FROM customer_master 
        WHERE customer_id = '$customer_id' 
        LIMIT 1";

$result = mysql_query($sql);

if (!$result || mysql_num_rows($result) == 0) {
    echo json_encode([
        "status" => false,
        "message" => "Customer not found"
    ]);
    exit;
}

$row = mysql_fetch_assoc($result);
$dob_raw = $row['DOB'];

/* ===============================
   VALIDATE DOB
=================================*/
if ($dob_raw == '' || $dob_raw == '00000000' || strlen($dob_raw) != 8) {
    echo json_encode([
        "status" => false,
        "message" => "Birthday Not Found"
    ]);
    exit;
}

/* ===============================
   CHECK TODAY IS BIRTHDAY
=================================*/
$today_md = date("md");
$customer_md = date("md", strtotime($dob_raw));

if ($today_md != $customer_md) {
    echo json_encode([
        "status" => false,
        "message" => "Today is not birthday"
    ]);
    exit;
}

/* ===============================
   CHECK SEEN STATUS (YEAR WISE)
=================================*/
$current_year = date("Y");

$seen_check = "
    SELECT id 
    FROM birthday_wish_seen_log 
    WHERE customer_id = '$customer_id'
    AND seen_year = '$current_year'
";

$seen_res = mysql_query($seen_check);

if ($seen_res && mysql_num_rows($seen_res) > 0) {
    echo json_encode([
        "status" => false,
        "message" => "Birthday wish already shown"
    ]);
    exit;
}

/* ===============================
   GET DYNAMIC BIRTHDAY CONTENT
=================================*/
$content_sql = "
    SELECT type, title, message, img 
    FROM birthday_master 
    WHERE status = 1 
    ORDER BY id DESC 
    LIMIT 1
";

$content_res = mysql_query($content_sql);

if (!$content_res || mysql_num_rows($content_res) == 0) {
    echo json_encode([
        "status" => false,
        "message" => "Birthday content not configured"
    ]);
    exit;
}

$content = mysql_fetch_assoc($content_res);

/* ===============================
   FORMAT DOB
=================================*/
$dob_formatted = date("d-m-Y", strtotime($dob_raw));

/* ===============================
   REPLACE DYNAMIC VARIABLE
=================================*/
$message = str_replace(
    "{{customer_name}}",
    $row['customer_name'],
    $content['message']
);

/* ===============================
   FINAL RESPONSE
=================================*/
echo json_encode([
    "status" => true,
    "customer_id" => $row['customer_id'],
    "customer_name" => $row['customer_name'],
    "type" => $content['type'],
    "title" => $content['title'],
    "message" => $message,
    "img" => $content['img'],
    "dob" => $dob_formatted
]);

<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Kolkata');
include "star_connection.php";

$customer_id = isset($_POST['customer_id']) ? trim($_POST['customer_id']) : '';

if ($customer_id == '') {
    echo json_encode([
        "status" => false,
        "message" => "Customer ID is required"
    ]);
    exit;
}

$today = date("Y-m-d");
$current_year = date("Y");

// Check already seen
$check_sql = "
    SELECT id 
    FROM birthday_wish_seen_log 
    WHERE customer_id = '$customer_id' 
    AND seen_year = '$current_year'
";
$check_res = mysql_query($check_sql);

if (mysql_num_rows($check_res) > 0) {
    echo json_encode([
        "status" => true,
        "message" => "Birthday wish already seen"
    ]);
    exit;
}

// Insert seen log
$insert_sql = "
    INSERT INTO birthday_wish_seen_log
    (customer_id, seen_date, seen_year)
    VALUES
    ('$customer_id', '$today', '$current_year')
";

if (mysql_query($insert_sql)) {
    echo json_encode([
        "status" => true,
        "message" => "Birthday wish marked as seen"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Failed to save seen status"
    ]);
}

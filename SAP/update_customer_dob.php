<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

include "star_connection.php";

/* ===============================
   GET INPUT (POST recommended)
=================================*/
$customer_id = isset($_POST['customer_id']) ? trim($_POST['customer_id']) : '';
$dob_input   = isset($_POST['dob']) ? trim($_POST['dob']) : ''; 
// Expected format: dd-mm-YYYY

if ($customer_id == '' || $dob_input == '') {
    echo json_encode([
        "status" => false,
        "message" => "Customer ID and DOB are required"
    ]);
    exit;
}

/* ===============================
   VALIDATE DOB FORMAT (dd-mm-YYYY)
=================================*/
if (!preg_match("/^\d{2}-\d{2}-\d{4}$/", $dob_input)) {
    echo json_encode([
        "status" => false,
        "message" => "Invalid DOB format. Use dd-mm-YYYY"
    ]);
    exit;
}

// Convert to DateTime
$dob_obj = DateTime::createFromFormat('d-m-Y', $dob_input);

if (!$dob_obj) {
    echo json_encode([
        "status" => false,
        "message" => "Invalid DOB value"
    ]);
    exit;
}

// Future date check
if ($dob_obj > new DateTime()) {
    echo json_encode([
        "status" => false,
        "message" => "DOB cannot be future date"
    ]);
    exit;
}

/* ===============================
   CONVERT TO YYYYMMDD FORMAT
=================================*/
$dob_store = $dob_obj->format('Ymd'); // 19980629

/* ===============================
   CHECK CUSTOMER EXIST
=================================*/
$check_sql = "SELECT DOB FROM customer_master WHERE customer_id = '$customer_id' LIMIT 1";
$check_res = mysql_query($check_sql);

if (mysql_num_rows($check_res) == 0) {
    echo json_encode([
        "status" => false,
        "message" => "Customer not found"
    ]);
    exit;
}

$row = mysql_fetch_assoc($check_res);

/* ===============================
   IF DOB ALREADY EXISTS
=================================*/
if (!empty($row['DOB'])) {
    echo json_encode([
        "status" => false,
        "message" => "DOB already exists. Contact admin to modify."
    ]);
    exit;
}

/* ===============================
   UPDATE DOB
=================================*/
$update_sql = "
    UPDATE customer_master 
    SET DOB = '$dob_store'
    WHERE customer_id = '$customer_id'
";

if (mysql_query($update_sql)) {
    echo json_encode([
        "status" => true,
        "message" => "DOB updated successfully",
        "customer_id" => $customer_id,
        "dob" => $dob_input
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Failed to update DOB"
    ]);
}

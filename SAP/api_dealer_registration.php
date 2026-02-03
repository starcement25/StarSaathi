
// header("Content-Type: application/json");


// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//     http_response_code(405);
//     echo json_encode(["error" => "Only POST method is allowed"]);
//     exit;
// }


// include "admin/web_check.php";
// include "admin/star_connection.php";
// $table_name = "customer_master";


// $data = json_decode(file_get_contents("php://input"), true);

// echo json_encode(["raw_data" => $data]);
// exit;
// $required = ['customer_name', 'address', 'phone_no', 'cust_type'];
// foreach ($required as $field) {
//     if (empty($data[$field])) {
//         http_response_code(400);
//         echo json_encode(["error" => "$field is required"]);
//         exit;
//     }
// }


// $customer_code = "Test" . uniqid();
// $dns_customer_code = "TEST-DNS" . uniqid();
// $customer_id = uniqid();


// $customer_name = mysqli_real_escape_string($conn, $data['customer_name']);
// $address = mysqli_real_escape_string($conn, $data['address']);
// $phone_no = mysqli_real_escape_string($conn, $data['phone_no']);
// $cust_type = mysqli_real_escape_string($conn, $data['cust_type']);


// $sql = "INSERT INTO $table_name (
//             customer_code, dns_customer_code, customer_id, customer_name, address, phone_no, cust_type
//         ) VALUES (
//             '$customer_code', '$dns_customer_code', '$customer_id', '$customer_name', '$address', '$phone_no', '$cust_type'
//         )";


// if (mysqli_query($conn, $sql)) {
//     echo json_encode([
//         "success" => true,
//         "message" => "Customer inserted successfully",
//         "customer_code" => $customer_code
//     ]);
// } else {
//     http_response_code(500);
//     echo json_encode(["error" => "Insert failed: " . mysqli_error($conn)]);
// }

// mysqli_close($conn);




<?php
header('Content-Type: application/json');
require_once('star_connection.php'); 


$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['error' => 'Invalid JSON input']);
    exit;
}


$required = ['customer_name', 'address', 'phone_no', 'cust_type'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(["error" => "$field is required"]);
        exit;
    }
}


$customer_code = "Test" . uniqid();
$dns_customer_code = "TEST-DNS" . uniqid();
$customer_id = uniqid();


$customer_name = mysql_real_escape_string($data['customer_name']);
$address = mysql_real_escape_string($data['address']);
$phone_no = mysql_real_escape_string($data['phone_no']);
$cust_type = mysql_real_escape_string($data['cust_type']);

// $dealer_name = mysql_real_escape_string($data['dealer_name']);
// $email = mysql_real_escape_string($data['email']);
// $mobile = mysql_real_escape_string($data['mobile']);
// $state = mysql_real_escape_string($data['state']);
// $city = mysql_real_escape_string($data['city']);
// $pincode = mysql_real_escape_string($data['pincode']);
// $created_at = date("Y-m-d H:i:s");


$sql = "INSERT INTO customer_master  (
            customer_code, dns_customer_code, customer_id, customer_name, address, phone_no, cust_type
        ) VALUES (
            '$customer_code', '$dns_customer_code', '$customer_id', '$customer_name', '$address', '$phone_no', '$cust_type'
        )";

$result = mysql_query($sql);

if ($result) {
    $last_id = mysql_insert_id(); 

   
    // $fetch_sql = "SELECT * FROM customer_master WHERE id = $last_id";
    // $fetch_result = mysql_query($fetch_sql);
    // $dealer_data = mysql_fetch_assoc($fetch_result);

    echo json_encode(['success' => true, 'customer_code' => $customer_code]);
} else {
    echo json_encode(['error' => 'Insert failed: ' . mysql_error()]);
}

?>

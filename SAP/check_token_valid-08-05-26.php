<?php
header("Content-Type: application/json");

include_once "star_connection.php"; // mysql connection

// Get headers
$headers = function_exists('getallheaders') ? getallheaders() : array();

$token = "";
$emp_code = "";

// Get token from header
if(isset($headers['Authorization'])){
    $token = str_replace("Bearer ", "", $headers['Authorization']);
}

// Fallback: request
if($token == "" && isset($_REQUEST['auth_token'])){
    $token = $_REQUEST['auth_token'];
}

// Get emp_code
if(isset($_REQUEST['emp_code'])){
    $emp_code = $_REQUEST['emp_code'];
}
//echo"<pre>";print_r($token);die;

// Validate input
if($token == "" || $emp_code == ""){
    echo json_encode(array(
        "process_status" => "NO",
        "process_message" => "AUTH TOKEN OR EMP CODE MISSING"
    ));
    exit;
}

// Escape (important)
$token = mysql_real_escape_string($token);
$emp_code = mysql_real_escape_string($emp_code);

// Query
$sql = "SELECT dns_customer_code, loggedin_date_time 
        FROM changepassword 
        WHERE token = '$token' 
        AND dns_customer_code = '$emp_code' 
        LIMIT 1";

$res = mysql_query($sql);

// Invalid
if(!$res || mysql_num_rows($res) == 0){
    echo json_encode(array(
        "process_status" => "NO",
        "process_message" => "INVALID TOKEN",
        "is_valid" => false
    ));
    exit;
}

$row = mysql_fetch_assoc($res);

// Optional: Expiry check
$login_time = strtotime($row['loggedin_date_time']);
$current_time = time();
/*
if(($current_time - $login_time) > 86400){
    echo json_encode(array(
        "process_status" => "NO",
        "process_message" => "TOKEN EXPIRED",
        "is_valid" => false
    ));
    exit;
}
*/
// ✅ Valid token
echo json_encode(array(
    "process_status" => "YES",
    "process_message" => "TOKEN VALID",
    "is_valid" => true,
    "emp_code" => $row['dns_customer_code'],
    "token" => $token,
    "login_time" => $row['loggedin_date_time']
));
?>
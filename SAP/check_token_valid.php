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

// ==========================================
// CHECK token_check FROM changepassword TABLE
// ==========================================

$check_sql = "SELECT token_check 
              FROM changepassword 
              WHERE dns_customer_code = '$emp_code'
              LIMIT 1";

$check_res = mysql_query($check_sql);

$token_check = 1; // default

if($check_res && mysql_num_rows($check_res) > 0){
    $check_row = mysql_fetch_assoc($check_res);
    $token_check = intval($check_row['token_check']);
}

// ==========================================
// IF token_check = 0 => BYPASS TOKEN CHECK
// ==========================================

if($token_check == 0){

    echo json_encode(array(
        "process_status" => "YES",
        "process_message" => "TOKEN VALID",
        "is_valid" => true,
        "emp_code" => $emp_code,
        "token_check" => 0
    ));
    exit;
}
// ==========================================
// NORMAL TOKEN VALIDATION
// ==========================================
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
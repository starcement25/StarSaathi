<?php
header('Content-Type: application/json');
require_once 'starsaathi_connection.php';
include 'star_connection.php';
$db = new starsaathi_connection();
$conn = $db->conn;

// $conn = mysql_connect("172.17.0.2", "root", "Passw0rd123#$");
// mysql_select_db("starsaathi_STARS", $conn);


if (!isset($_GET['customer_id'])) {
    $res_data = array("process_status" => "No", "process_message" => "Validation Failed!", 'error' => 'customer_id is required');
    echo json_encode($res_data);
    exit;
}

$customer_id = intval($_GET['customer_id']); 


$customer_sql = "SELECT * FROM customer_master WHERE customer_id = $customer_id";
// echo $customer_sql ;die;
$customer_result = mysql_query($customer_sql, $conn);

if (!$customer_result || mysql_num_rows($customer_result) == 0) {
     $res_data = array("process_status" => "No", "process_message" => " Failed!", 'error' => 'Customer not found');
    echo json_encode($res_data);
    exit;
}

$customer = mysql_fetch_assoc($customer_result);
// print_r($customer);die;
$branch_code = $customer['branch_code'];


$branch_sql = "SELECT branch_name FROM branch_master WHERE branch_code = '$branch_code'";
$branch_result = mysql_query($branch_sql, $conn);

$branch_name = null;
if ($branch_result && mysql_num_rows($branch_result) > 0) {
    $branch = mysql_fetch_assoc($branch_result);
    $branch_name = $branch['branch_name'];
}else{
     $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => 'Branch Not Found!');
    echo json_encode($res_data);
}
// echo  $branch_name;die;
$res_data = array("process_status" => "YES", "process_message" => "success", "customer_name" => $customer['customer_name'] ,"branch_name"=>$branch_name);
array_walk_recursive($res_data, function (&$value) {
    if (is_string($value) && !mb_check_encoding($value, 'UTF-8')) {
        $value = utf8_encode($value);
    }
});
echo json_encode($res_data, JSON_UNESCAPED_UNICODE);
exit;
?>

<?php
header('Content-Type: application/json');


// $conn = mysql_connect("172.17.0.2", "root", "Passw0rd123#$");
// mysql_select_db("starsaathi_STARS", $conn);
// echo 64734673;
// die;
require_once 'starsaathi_connection.php';

$db = new starsaathi_connection();
$conn = $db->conn;


if (!isset($_GET['customer_id'])) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Validation Failed!",
        "error" => "customer_id is required"
    ]);
    exit;
}

if (!isset($_GET['month'])) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Validation Failed!",
        "error" => "Month is required"
    ]);
    exit;
}


// $customer_id = intval($_GET['customer_id']);
// $month = intval($_GET['month']);


// $customer_sql = "SELECT * FROM customer_master WHERE customer_id = $customer_id";
// $customer_result = mysqli_query($conn, $customer_sql); // ✅ use mysqli_query

// if (!$customer_result || mysqli_num_rows($customer_result) == 0) {
//     echo json_encode([
//         "process_status" => "No",
//         "process_message" => "Failed!",
//         "error" => "Customer not found"
//     ]);
//     exit;
// }

// $customer = mysqli_fetch_assoc($customer_result); // ✅ use mysqli_fetch_assoc
// $customerId = $customer['customer_id'];

//$currentMonth = (int)date('m');

//    $check_sql = "SELECT COUNT(*) as count FROM dealer_exclusice WHERE customer_id = '$customerId' AND current_year = '$currentYear' AND month = '$month'";

// $check_result = mysql_query($check_sql);
// $check_row = mysql_fetch_assoc($check_result);

// $cutoff_spcl_sql = "SELECT * FROM dealer_exclusive_special_cutoff 
//                     WHERE for_month = '$month' 
//                     AND for_year = '$currentYear' 
//                     AND customer_id = '$customerId'";

// $cutoff_spcl_result = mysql_query($cutoff_spcl_sql);
// $row_spcl = mysql_fetch_assoc($cutoff_spcl_result);
// $current_date = date('Y-m-d');
// $cutoff_date = date('Y-m-d', strtotime($row_spcl['cutoff_date']));
// if (!$row_spcl  || $current_date > $cutoff_date) {
//     $cutoff_global_sql = "SELECT * FROM dealer_exclusive_common_cutoff WHERE for_month = '$month' AND for_year = '$year'";
//     $cutoff_global_result = mysql_query($cutoff_global_sql);


//     if (!$cutoff_global_result) {
//         $res_data = array("process_status" => "No", "process_message" => "Failed!", "is_applied"=>"False","error" => "Query execution failed: " . mysql_error());
//         echo json_encode($res_data);
//         exit;
//     }


//     $row_global = mysql_fetch_assoc($cutoff_global_result);
//     if (!$row_global) {
//         $res_data = array("process_status" => "No", "process_message" => "Failed!","is_applied"=>"False", "error" => "No cutoff data found for selected month/year.");
//         echo json_encode($res_data);
//         exit;
//     }
// }elseif($check_row['count'] > 0) {
//     echo json_encode([
//         "process_status" => "No",
//         "process_message" => "Failed!",
//         "is_applied"=>"True",
//         "error" => "Exclusive Dealer application for the selected month is already applied."
//     ]);
//     exit;
// }
    
// }

// $res_data = [
//     "process_status" => "YES",
//     "process_message" => "success",
   
//     "months" => $monthsList
// ];

// echo json_encode($res_data);

?>

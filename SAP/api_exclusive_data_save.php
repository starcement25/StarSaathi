<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

header('Content-Type: application/json');

date_default_timezone_set('Asia/Kolkata');
// $conn = mysql_connect("172.17.0.2", "root", "Passw0rd123#$");
require_once("starsaathi_connection.php");
$localDB = new starsaathi_connection();
$conn = $localDB->conn;
//mysql_select_db("starsaathi_STARS", $conn);



// /////



if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => 'Only POST method is allowed');
    echo json_encode($res_data);
    //echo json_encode(["error" => "Only POST method is allowed"]);
    exit;
}


$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => 'Invalid JSON input');
    echo json_encode($res_data);
    //echo json_encode(['error' => 'Invalid JSON input']);
    exit;
}


$required = ['dealer_name', 'branch', 'lifting_qty', 'month', 'customer_id'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => "$field is required");
        echo json_encode($res_data);
        // echo json_encode(["error" => "$field is required"]);
        exit;
    }
}


$dealer_name = mysql_real_escape_string($data['dealer_name']);
//$branch = mysql_real_escape_string($data['branch']);
$lifting_qty = mysql_real_escape_string($data['lifting_qty']);
$month = mysql_real_escape_string($data['month']);
$customer_id = mysql_real_escape_string($data['customer_id']);

$customer_sql = "SELECT * FROM customer_master WHERE customer_id = $customer_id";
$customer_res = mysql_query($customer_sql);
$row = mysql_fetch_assoc($customer_res);
$branch_code=$row['branch_code'];

$branch_sql = "SELECT branch_name FROM branch_master WHERE branch_code = '$branch_code'";
$branch_res = mysql_query($branch_sql);
$branch_row = mysql_fetch_assoc($branch_res);
$branch= $branch_row['branch_name'];

// $cutoff_sql = "SELECT the_value FROM app_setting_master WHERE the_key_name = 'cuttoff_date'";
// $cutoff_result = mysql_query($cutoff_sql);
// $row_cut = mysql_fetch_assoc($cutoff_result);

// if (!$row_cut['the_value']) {
//     $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => "Cutoff date not found");
//     echo json_encode($res_data);
//     //echo json_encode(['error' => 'Cutoff date not found']);
//     exit;
// }

//$cutoff_row = mysql_fetch_assoc($cutoff_result);
//$cutoff_day = (int)$row_cut['the_value'];

// if ($cutoff_day > 31) {
//     $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => "Invalid cutoff day from database");
//     echo json_encode($res_data);
//     //echo json_encode(['error' => 'Invalid cutoff day from database']);
//     exit;
// }
//if ($cutoff_day > 0) {
//  $cutoff_day = str_pad($cutoff_day, 2, '0', STR_PAD_LEFT);

$current_month = date('m');
$month = trim($data['month']);
// $year = date('Y'); 




// $month=str_pad($month , 2, '0', STR_PAD_LEFT);
// if ( (int)$month < (int)$current_month) {
//     $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => "Selected month is in the past.");
//     echo json_encode($res_data);
//     //echo json_encode(['error' => 'Selected month is in the past.']);
//     exit;
// }

// list($month, $year) = explode('-', trim($data['month']));
// $month = str_pad($month, 2, '0', STR_PAD_LEFT);
$month_input = trim($data['month']);


if (strpos($month_input, '-') !== false) {
    list($month, $year) = explode('-', $month_input);
    $month = str_pad($month, 2, '0', STR_PAD_LEFT);
} else {
    // New format: "March 2026"
    $dateObj = DateTime::createFromFormat('F Y', $month_input);

    if (!$dateObj) {
        echo json_encode([
            "process_status" => "No",
            "process_message" => "Failed!",
            "error" => "Invalid month format"
        ]);
        exit;
    }

    $month = $dateObj->format('m'); // 03
    $year  = $dateObj->format('Y'); // 2026
}

$year = (int)$year;



// $cutoff_global_sql="SELECT * FROM dealer_exclusive_common_cutoff WHERE for_month = '$month' AND for_year='$year'";

// $cutoff_global_result = mysql_query($cutoff_global_sql);
// $row_global = mysql_fetch_assoc($cutoff_global_result);

// // echo $row_global['cutoff_date'];
// // die;
// $current_month = date('m');
// $current_year = date('Y');
// $current_date = date('Y-m-d');


// $start_date= $year.'-'.$month.'-01';

// if ((int)$year < (int)$current_year || ((int)$year == (int)$current_year && (int)$month < (int)$current_month)) {
//     $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => "Selected month/year is in the past.");
//     echo json_encode($res_data);
//     exit;
// }

// $cutoff_spcl_sql = "SELECT * FROM dealer_exclusive_special_cutoff WHERE for_month = '$month' AND for_year = '$year' AND customer_id= '$customer_id'";

// $cutoff_spcl_result = mysql_query($cutoff_spcl_sql);
// $row_spcl = mysql_fetch_assoc($cutoff_spcl_result);
// $cutoff_date=date('Y-m-d', strtotime($row_spcl['cutoff_date']));
// $current_month = date('m');
// $current_year = date('Y');
// $current_date = date('Y-m-d');
// if (!$row_spcl  || $current_date > $cutoff_date ) {
//     $res_data = array("process_status" => "No", "process_message" => "Failed!", "error" => "No cutoff data found for selected month/year.");
//     echo json_encode($res_data);
//     die;
// }else{
//      echo $row_spcl['cutoff_date'];
//     die;
// }


$cutoff_spcl_sql = "SELECT * FROM dealer_exclusive_special_cutoff 
                    WHERE for_month = '$month' 
                    AND for_year = '$year' 
                    AND customer_id = '$customer_id'";

$cutoff_spcl_result = mysql_query($cutoff_spcl_sql);
$row_spcl = mysql_fetch_assoc($cutoff_spcl_result);
 $start_date = date('Y-m-d', strtotime($year . '-' . $month . '-01'));
//$start_date = date('Y-m-d', strtotime($year . '-06-01'));
$current_date = date('Y-m-d');
$cutoff_date = date('Y-m-d', strtotime($row_spcl['cutoff_date']));
if (!$row_spcl  || $current_date > $cutoff_date) {
    $cutoff_global_sql = "SELECT * FROM dealer_exclusive_common_cutoff WHERE for_month = '$month' AND for_year = '$year'";
    $cutoff_global_result = mysql_query($cutoff_global_sql);


    if (!$cutoff_global_result) {
        $res_data = array("process_status" => "No", "process_message" => "Failed!", "error" => "Query execution failed: " . mysql_error());
        echo json_encode($res_data);
        exit;
    }

// echo $cutoff_global_sql;die;
    $row_global = mysql_fetch_assoc($cutoff_global_result);
    if (!$row_global) {
        $res_data = array("process_status" => "No", "process_message" => "Failed!", "error" => "No cutoff data found for selected month/year.");
        echo json_encode($res_data);
        exit;
    }





    $cutoff_date = $row_global['cutoff_date'];
    $cutoff_date = date('Y-m-d', strtotime($cutoff_date));
} else {

    // $res_data = array(
    //     "process_status" => "Yes",
    //     "process_message" => "Valid",
    //     "cutoff_date" => $cutoff_date
    // );
    // echo json_encode($res_data);
    // die;
    $cutoff_date = date('Y-m-d', strtotime($row_spcl['cutoff_date']));
}




// if ($current_date > $cutoff_date) {
//     $res_data = array(
//         "process_status" => "No",
//         "process_message" => "Failed!",
//         "error" => "Current date ($current_date) is past the cutoff date ($cutoff_date)."
//     );
//     echo json_encode($res_data);
//     die;
// }







if ( $current_date > $cutoff_date) {
    $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => "Current date is outside the allowed cutoff period.");
    echo json_encode($res_data);
    die;
}


// $res_data = array("process_status" => "Yes", "process_message" => "Valid date range.");
// echo json_encode($res_data);
// die;




//$cutoff_date = new DateTime("$year-$month-$cutoff_day");
//$date_string = sprintf('%04d-%02d-%02d', $year, $month, $cutoff_day);

//$cutoff_date = new DateTime($date_string);




// $cutoff_date = $cutoff_date->format('Y-m-d');
// $end_of_month = new DateTime("$year-$month-01");
// $end_of_month->modify('last day of this month');
// $end_date = $end_of_month->format('Y-m-d');
// } else {
//     $cutoff_date = 0;
// }


$check_sql = "SELECT COUNT(*) as count FROM dealer_exclusice WHERE customer_id = '$customer_id' AND current_year = '$year' AND month = '$month'";
$check_result = mysql_query($check_sql);
$check_row = mysql_fetch_assoc($check_result);

if ($check_row['count'] > 0) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Exclusive Dealer application for the selected month is already applied."
    ]);
    exit;
}



$created_at = date("Y-m-d H:i:s");



try {
    $insert_sql = "INSERT INTO dealer_exclusice 
        (customer_id,customer_code,dealer_name, branch, lifting_qty, month,current_year, cutoff_date, created_at) 
        VALUES 
        ('$customer_id','" . $row['dns_customer_code'] . "','$dealer_name', '$branch', '$lifting_qty', '$month','$year', '$cutoff_date', '$created_at')";
    // echo $insert_sql;
    // die;
    $result = mysql_query($insert_sql);


    //    $lastid=last_insert_id();
    //    echo $lastid;
    //    die;

} catch (Exception $e) {
    http_response_code(500);
    $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => "$e->getMessage()");
    echo json_encode($res_data);
    //echo json_encode(['error' => $e->getMessage()]);
    exit;
}

if ($result) {
    $lastId = mysql_insert_id();


    $fetch_sql = "SELECT * FROM dealer_exclusice WHERE id = $lastId";
    $fetch_result = mysql_query($fetch_sql);
    $inserted_row = mysql_fetch_assoc($fetch_result);
    $res_data = array("process_status" => "Yes", "process_message" => "Success!", 'message' => 'Exclusive dealer declaration form submitted successfully', "Result" => $inserted_row);
    echo json_encode($res_data);
    //echo json_encode(['success' => true, 'message' => 'Exclusive Dealer information inserted successfully']);
} else {
    $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => 'Insert failed: ' . mysql_error());
    echo json_encode($res_data);
    //echo json_encode(['error' => 'Insert failed: ' . mysql_error()]);
}

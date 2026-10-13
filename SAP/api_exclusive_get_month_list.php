<?php

header('Content-Type: application/json');
date_default_timezone_set("Asia/Kolkata");

require_once 'starsaathi_connection.php';

$db = new starsaathi_connection();
$conn = $db->conn;


/*
|--------------------------------------------------------------------------
| Validate Input
|--------------------------------------------------------------------------
*/
if (!isset($_GET['customer_id'])) {

    echo json_encode([
        "process_status"  => "No",
        "process_message" => "Validation Failed!",
        "error"           => "customer_id is required"
    ]);

    exit;
}

$customer_id = intval($_GET['customer_id']);


/*
|--------------------------------------------------------------------------
| Check Customer
|--------------------------------------------------------------------------
*/
$customer_sql = "SELECT * FROM customer_master 
                 WHERE customer_id = $customer_id";

$customer_result = mysql_query($customer_sql, $conn);

if (!$customer_result || mysql_num_rows($customer_result) == 0) {

    echo json_encode([
        "process_status"  => "No",
        "process_message" => "Failed!",
        "error"           => "Customer not found"
    ]);

    exit;
}

$customer   = mysql_fetch_assoc($customer_result);
$customerId = $customer['customer_id'];


/*
|--------------------------------------------------------------------------
| Generate Months From April 2025 To Current Month
|--------------------------------------------------------------------------
*/

$monthsList = [];

// Starting month = April 2025
$startDate = strtotime("2025-04-01");

// Current month
$endDate = strtotime(date("Y-m-01"));

while ($startDate <= $endDate) {

    $monthNum  = date("m", $startDate);
    $year      = date("Y", $startDate);

    $monthName = date("F Y", $startDate);
    $monthKey  = "$monthNum-$year";

    $isApplied = checkIfApplied(
        $conn,
        $customerId,
        $year,
        $monthNum
    );

    $cutoffInfo = getCutoffMessage(
        $conn,
        $customerId,
        $year,
        $monthNum
    );

    $message = $isApplied
        ? "Already applied for this month"
        : $cutoffInfo['message'];

    $monthsList[] = [
        "month_name" => $monthName,
        "key"        => $monthKey,
        "is_applied" => $isApplied,
        "cutoff"     => $cutoffInfo['valid'],
        "message"    => $message
    ];

    // Next month
    $startDate = strtotime("+1 month", $startDate);
}


/*
|--------------------------------------------------------------------------
| Final Response
|--------------------------------------------------------------------------
*/
echo json_encode([

    "process_status"  => "YES",
    "process_message" => "success",
    "months"          => $monthsList

]);


/*
|--------------------------------------------------------------------------
| Check Already Applied
|--------------------------------------------------------------------------
*/
function checkIfApplied($conn, $customerId, $year, $month)
{
    $month = str_pad($month, 2, '0', STR_PAD_LEFT);

    $sql = "SELECT COUNT(*) as count 
            FROM dealer_exclusice
            WHERE customer_id = '$customerId'
            AND current_year = '$year'
            AND month = '$month'";

    $res = mysql_query($sql, $conn);

    $row = mysql_fetch_assoc($res);

    return ($row && $row['count'] > 0);
}


/*
|--------------------------------------------------------------------------
| Check Cutoff
|--------------------------------------------------------------------------
*/
function getCutoffMessage($conn, $customerId, $year, $month)
{
    $month = str_pad($month, 2, '0', STR_PAD_LEFT);

    $current_date = date('Y-m-d');


    /*
    |--------------------------------------------------------------------------
    | Special Cutoff
    |--------------------------------------------------------------------------
    */
    $special_sql = "SELECT cutoff_date
                    FROM dealer_exclusive_special_cutoff
                    WHERE for_month = '$month'
                    AND for_year = '$year'
                    AND customer_id = '$customerId'";

    $special_res = mysql_query($special_sql, $conn);

    $special = mysql_fetch_assoc($special_res);

    if (
        $special &&
        $current_date <= date(
            'Y-m-d',
            strtotime($special['cutoff_date'])
        )
    ) {

        return [
            "message" => "Cutoff date not yet passed (special): " .
                         $special['cutoff_date'],
            "valid"   => true
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Global Cutoff
    |--------------------------------------------------------------------------
    */
    $global_sql = "SELECT cutoff_date
                   FROM dealer_exclusive_common_cutoff
                   WHERE for_month = '$month'
                   AND for_year = '$year'";

    $global_res = mysql_query($global_sql, $conn);

    $global = mysql_fetch_assoc($global_res);

    if (!$global) {

        return [
            "message" => "Cutoff date is not set",
            "valid"   => false
        ];
    }

    if (
        $current_date <= date(
            'Y-m-d',
            strtotime($global['cutoff_date'])
        )
    ) {

        return [
            "message" => "Cutoff date not yet passed (global): " .
                         $global['cutoff_date'],
            "valid"   => true
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Cutoff Over
    |--------------------------------------------------------------------------
    */
    return [
        "message" => "Cutoff date is over",
        "valid"   => false
    ];
}

?>
<?php
header('Content-Type: application/json');
require_once("starsaathi_connection.php");

$localDB = new starsaathi_connection();
$conn = $localDB->conn;

$customer_id = !empty($_GET['customer_id']) ? mysql_real_escape_string($_GET['customer_id']) : null;
$month_input = !empty($_GET['month']) ? $_GET['month'] : null;

if (empty($customer_id) || empty($month_input)) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Validation Failed!",
        "error" => "customer_id and month are required in MM-YYYY format"
    ]);
    exit;
}

if (!preg_match('/^(0[1-9]|1[0-2])-\d{4}$/', $month_input)) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Validation Failed!",
        "error" => "Month must be in MM-YYYY format"
    ]);
    exit;
}

list($month, $year) = explode('-', $month_input);

$sql = "
    SELECT *
    FROM dealer_exclusice
    WHERE customer_id = '$customer_id'
      AND `month` = '$month'
      AND YEAR(created_at) = '$year'
";

$result = mysql_query($sql);
if (!$result) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Query Failed",
        "error" => mysql_error()
    ]);
    exit;
}

$row = mysql_fetch_assoc($result);
$isApplied = 0;
$status = '';
$dateObj = DateTime::createFromFormat('m-Y', $month_input);
$formattedMonth = $dateObj ? $dateObj->format('F Y') : $month_input;
if ($row) {
    $isApplied = 1;

    if ($row['asm_approve_status'] == 1 && $row['rsm_approve_status'] == 1) {
        $status = "Your request has been approved for Exclusive Dealership for {$formattedMonth}!";
    } elseif ($row['asm_approve_status'] == 2 || $row['rsm_approve_status'] == 2) {
        $status = "Your request for {$formattedMonth} has been rejected, please get in touch with your respective Sales Officer.";
    } else {
        $status = "You have successfully applied to become Exclusive Dealer for {$formattedMonth}, please wait for approval.";
    }
} else {
    $status = "You have not applied for Exclusive Dealership for {$formattedMonth} yet.";
}

$res_data = [
    "process_status" => "YES",
    "process_message" => "success",
    "message" => $status,
    "status" => isset($row['status']) ? $row['status'] : null,
    "isApplied" => $isApplied
];

echo json_encode($res_data);

mysql_close($conn); 
exit;
?>

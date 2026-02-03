<?php
include "star_connection.php";

// Get parameters from the request
$emp_code = isset($_REQUEST['the_customer_code']) ? strtolower(trim($_REQUEST['the_customer_code'])) : "";
$dealer_status = isset($_REQUEST['the_status']) ? $_REQUEST['the_status'] : "";
$all_status = isset($_REQUEST['all_status']) ? $_REQUEST['all_status'] : "";
$cust_type = isset($_REQUEST['cust_type']) ? $_REQUEST['cust_type'] : "";

// Debugging statements
error_log("emp_code: $emp_code");
error_log("dealer_status: $dealer_status");
error_log("all_status: $all_status");
error_log("cust_type: $cust_type");

$response = array("process_status" => "NO", "process_message" => "Failed to update status.");

if (!empty($emp_code) && !empty($dealer_status)) {
    $sql_insert = "INSERT INTO `dealer_reward_status` (`emp_code`, `dealer_status`)
                   VALUES ('$emp_code', '$dealer_status') 
                   ON DUPLICATE KEY UPDATE `dealer_status` = VALUES(`dealer_status`);";

    if (mysql_query($sql_insert)) {
        $response = array("process_status" => "YES", "process_message" => "Success.");
    } else {
        $response = array("process_status" => "NO", "process_message" => "Database query failed.");
    }
} elseif (!empty($all_status) && !empty($cust_type)) {
    // Handle updating all dealers or sub dealers
    $sql_update = "UPDATE `dealer_reward_status` 
                   INNER JOIN `customer_master` ON `dealer_reward_status`.`emp_code` = `customer_master`.`customer_id`
                   SET `dealer_status` = '$all_status'
                   WHERE `customer_master`.`cust_type` = '$cust_type'";

    if (mysql_query($sql_update)) {
        $response = array("process_status" => "YES", "process_message" => "All statuses updated successfully.");
    } else {
        $response = array("process_status" => "NO", "process_message" => "Database query failed.");
    }
} else {
    $response = array("process_status" => "NO", "process_message" => "Choose type first.");
}

echo json_encode($response);
mysql_close();
?>

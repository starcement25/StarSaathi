<?php
include "star_connection.php"; // Include your database connection file

// Define your table name
$t_order_pop = "T_ORDER_POP";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $admin_remarks = $_POST['admin_remarks'];

    // Ensure you validate and sanitize your inputs
    $order_id = mysql_real_escape_string($order_id);
    $admin_remarks = mysql_real_escape_string($admin_remarks);

    // Update the database
    $update_query = "UPDATE $t_order_pop SET admin_remark = '$admin_remarks' WHERE APPORDERNO = '$order_id'";
    $result = mysql_query($update_query);

    if ($result) {
        echo "Admin remark updated successfully";
    } else {
        // Print detailed error message
        echo "Error updating admin remark: " . mysql_error() . ". Data sent: Order ID - $order_id, Admin Remarks - $admin_remarks, Query - $update_query";
    }
} else {
    echo "Error: Invalid request method";
}
?>

<?php
include "star_connection.php"; // Include your database connection file

// Define your table names
$t_order_pop = "T_ORDER_POP";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];

    // Ensure you validate and sanitize your inputs
    $order_id = mysql_real_escape_string($order_id);
    $new_status = mysql_real_escape_string($new_status);

    // Update the database
    $update_query = "UPDATE $t_order_pop SET order_status = '$new_status' WHERE APPORDERNO = '$order_id'";

    $result = mysql_query($update_query);

    if ($result) {
        echo "Status updated successfully";
    } else {
        // Print detailed error message
        echo "Error updating status: " . mysql_error() . ". Data sent: Order ID - $order_id, New Status - $new_status, Query - $update_query";
    }
}
?>

<?php
include "star_connection.php"; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Ensure you validate and sanitize input
    $order_id = mysql_real_escape_string($order_id);

    // Fetch the admin remark from the database
    $query = "SELECT admin_remark FROM $t_order_pop WHERE APPORDERNO = '$order_id'";
    $result = mysql_query($query);

    if ($result && mysql_num_rows($result) > 0) {
        $row = mysql_fetch_assoc($result);
        echo $row['admin_remark'];
    } else {
        echo "Error: Admin remark not found for order ID - $order_id";
    }
} else {
    echo "Error: Invalid request";
}
?>

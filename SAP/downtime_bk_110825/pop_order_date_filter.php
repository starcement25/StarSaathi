<?php
include "star_connection.php";

$pop_order = "pop_order";
$pop_product_master = "pop_product_master";
$customer_master = "customer_master";
$branch_master = "branch_master";
$t_order_pop = "T_ORDER_POP";
$product_master = "product_master";
$broker_master = "broker_master";
$lifting = "lifting";

$startDate = $_POST['startDate'];
$endDate = $_POST['endDate'];

// Assuming you have sanitized the input dates to prevent SQL injection

$startDate = date("Y-m-d", strtotime($startDate));
$endDate = date("Y-m-d", strtotime($endDate));

$sql = "SELECT `date_and_time`,`order_id`,`customer_code`,`customer_name`,`linked_dealer_code`,`linked_dealer_name`,`branch`,`product_name`,`qty`,`price_per_pcs`,`gst_percent`,`net_amount`,`gst_amount`,`total_amount`,`address`,`pin_code`,`remarks` FROM $pop_order WHERE formatted_date BETWEEN '$startDate' AND '$endDate'";

$result = mysql_query($sql);

if (!$result) {
    die("Query failed: " . mysql_error());
}

$resultArray = array();

while ($row = mysql_fetch_assoc($result)) {
    $resultArray[] = $row;
}

echo json_encode($resultArray);

mysql_close();
?>

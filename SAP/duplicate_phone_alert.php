
<?php

include_once("star_connection.php");

header("Content-Type: application/json");
//07-07-26 for static data
/*echo json_encode([
            "status" => true,
            "duplicate" => false,
            "sms_alert" => "No duplicate phone number found",
            "data" => array()
        ]);
        exit;*/
// Get customer_id
$customer_id = isset($_GET['customer_id']) ? trim($_GET['customer_id']) : '';

if ($customer_id == '') {

    echo json_encode([
        "status" => false,
        "sms_alert" => "Customer ID required"
    ]);
    exit;
}


// Get customer details
$get_customer = "
    SELECT customer_id, customer_name, phone_no
    FROM customer_master
    WHERE customer_id = '$customer_id'
    AND acedns = 'Y'
";

$get_result = mysql_query($get_customer);


// If customer not found
if (!$get_result || mysql_num_rows($get_result) == 0) {

    // Check broker_master
    $broker_sql = "
        SELECT dns_broker_id
        FROM broker_master
        WHERE dns_broker_id = '$customer_id'
    ";

    $broker_result = mysql_query($broker_sql);

    // If broker found
    if ($broker_result && mysql_num_rows($broker_result) > 0) {

        echo json_encode([
            "status" => true,
            "duplicate" => false,
            "sms_alert" => "No duplicate phone number found",
            "data" => array()
        ]);
        exit;

    } else {

        echo json_encode([
            "status" => false,
            "sms_alert" => "Customer not found"
        ]);
        exit;
    }
}


$customer = mysql_fetch_assoc($get_result);

$phone_no = $customer['phone_no'];
$customer_name = $customer['customer_name'];


// Find duplicate customers
$sql = "
    SELECT customer_id, customer_name, phone_no
    FROM customer_master
    WHERE phone_no = '$phone_no' AND (cust_type='Dealer' OR cust_type='RSSD')
    AND acedns = 'Y'
";

$result = mysql_query($sql);

$total = mysql_num_rows($result);

$data = array();
$duplicate_names = array();

while ($row = mysql_fetch_assoc($result)) {

    $data[] = $row;

    if ($row['customer_id'] != $customer_id) {
        $duplicate_names[] = $row['customer_name'];
    }
}


// Response
if ($total > 1) {

    $name_list = implode(", ", $duplicate_names);

    $sms_alert = "Your phone number is duplicate for: " . $name_list;

    echo json_encode([
        "status" => true,
        "duplicate" => true,
        "customer_name" => $customer_name,
        "phone_no" => $phone_no,
        "sms_alert" => $sms_alert,
        "total_records" => $total,
        "data" => $data
    ]);

} else {

    echo json_encode([
        "status" => true,
        "duplicate" => false,
        "sms_alert" => "No duplicate phone number found",
        "data" => $data
    ]);
}

?>


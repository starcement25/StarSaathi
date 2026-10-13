<?php

header('Content-Type: application/json');

include_once("star_connection.php");

$response = array();

if (!isset($_REQUEST['customer_id']) || $_REQUEST['customer_id'] == '') {
    $response['process_status'] = "NO";
    $response['process_message'] = "customer_id required";
    echo json_encode($response);
    exit;
}

$customer_id = mysql_real_escape_string($_REQUEST['customer_id']);

$sql = "
SELECT
    customer_id,
    customer_code
FROM customer_master
WHERE customer_id='$customer_id'
LIMIT 1
";

$result = mysql_query($sql);

if(mysql_num_rows($result)>0){

    $row=mysql_fetch_assoc($result);

    $response=array(
        "process_status"=>"YES",
        "process_message"=>"Success",
        "customer_id"=>$row['customer_id'],
        "customer_code"=>$row['customer_code']
    );

}else{

    $response=array(
        "process_status"=>"NO",
        "process_message"=>"Customer not found"
    );

}

echo json_encode($response);
?>
<?php

date_default_timezone_set("Asia/Kolkata");

include_once("star_connection.php");

header('Content-Type: application/json');

if(empty($_REQUEST['customer_id']))
{
    echo json_encode(array(
        "status"=>"NO",
        "message"=>"customer_id required."
    ));
    exit;
}

$customer_id=mysql_real_escape_string($_REQUEST['customer_id']);

$get_customer="SELECT * FROM customer_gst_documents
WHERE customer_id='$customer_id'
ORDER BY id DESC
LIMIT 1";

$get_result=mysql_query($get_customer);

if(mysql_num_rows($get_result)>0)
{

    $row=mysql_fetch_assoc($get_result);

    echo json_encode(array(

        "status"=>"YES",
        "is_present"=>"YES",
        "customer_id"=>$row['customer_id'],
        "gst_no"=>$row['gst_no'],
        "upload_doc"=>$row['upload_doc'],
        "created_date"=>$row['created_date']

    ));

}
else
{

    echo json_encode(array(

        "status"=>"YES",
        "is_present"=>"NO"

    ));

}

?>
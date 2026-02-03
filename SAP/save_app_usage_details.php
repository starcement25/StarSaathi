<?php
include "star_connection.php";

$app_service_track_log="app_service_track_log";

$customer_id=$_POST["customer_id"];
$webservice_name = $_POST["webservice_name"];

$curr_date_time = date("Y-m-d H:i:s");

if($customer_id!='' && $webservice_name!=''){
$sql_app_usage="insert into $app_service_track_log(`appservice_name`,`customer_code`,`datetime`) values('$webservice_name','$customer_id','$curr_date_time')";

if(mysql_query($sql_app_usage))
{
	$res_data = array("process_status"=>"YES","process_message"=>"Success.");
}
else
{
	$res_data = array("process_status"=>"NO","process_message"=>"Failure.");
}
}
else
{
	$res_data = array("process_status"=>"NO","process_message"=>"Customer Id and Webservice Name should not be Blank.");
}

echo json_encode($res_data);
mysql_close();
?>

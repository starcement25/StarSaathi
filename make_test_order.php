<?php
include "star_connection.php";
$test_payment = "test_payment";
$the_amount = $_REQUEST["the_amount"] ? addslashes(trim($_REQUEST["the_amount"])) : "";
$emp_code = $_REQUEST["emp_code"] ? addslashes(trim($_REQUEST["emp_code"])) : "";
if($the_amount!="" && $emp_code!=""){
$order_datetime = date("Y-m-d H:i:s");
$rand_val = rand(1,9).rand(0,9).rand(0,9).rand(1,9);
$track_id = "STAR".time().$rand_val;
$sqlall = "insert into $test_payment (`tracking_ID`,`amount`,`emp_code`,`order_datetime`) values ('$track_id','$the_amount','$emp_code','$order_datetime')";
$resall = mysql_query($sqlall);
if($resall){
$the_order_id = mysql_insert_id();
$res_data = array("process_status"=>"YES","process_message"=>"Success.","the_order_id"=>$the_order_id);
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.");
}
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Amount and emp_code are mandatory.");
}	
echo json_encode($res_data);
mysql_close();
?>
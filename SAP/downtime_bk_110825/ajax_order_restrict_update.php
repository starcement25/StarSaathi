<?php
include "star_connection.php";
$customer_master = "customer_master";
$the_customer_code = $_REQUEST["the_customer_code"] ? addslashes(trim($_REQUEST["the_customer_code"])) : "";
$the_status = $_REQUEST["the_status"] ? addslashes(trim($_REQUEST["the_status"])) : "";
if($the_customer_code!=""){

if($the_status!=""){
	$download_time = date("Y-m-d H:i:s");
	$sql_upd="update $customer_master set `order_restriction`='$the_status' where `customer_id`='$the_customer_code'";
	$res_upd=mysql_query($sql_upd);
	$res_data = array("process_status"=>"YES","process_message"=>"Order restriction updated successfully.");
 }
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");
}	
echo json_encode($res_data);
mysql_close();
?>
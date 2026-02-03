<?php
session_start();
include "star_connection.php";
if(isset($_SESSION["sswa_user_id"])){
	
	$customer_master = "customer_master";
	
$sswa_user_type = $_SESSION["sswa_user_type"];
$sswa_selected_dealer_code = $_SESSION["sswa_selected_dealer_code"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];
	
$sql3 = "select `dns_customer_code`,`customer_id`,customer_name from $customer_master where `customer_code`='$sswa_selected_customer_code'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
$row3 = mysql_fetch_assoc($res3);
$the_customer_id = trim($row3["customer_id"]);


if(strtoupper($sswa_user_type)=='DEALER'){
/*----Tracklog Code Start-----*/
$curr_date_time = date("Y-m-d H:i:s");
$webservice_name = "LOGOUT";
$sqlin_tl = "insert into `webservice_track_log` (`customer_code`,`webservice_name`,`details`,`datetime`) values ('$the_customer_id','$webservice_name','','$curr_date_time')";
$resin_tl = mysql_query($sqlin_tl);
/*----Tracklog Code End-----*/
}

unset($_SESSION["sswa_user_id"]);
unset($_SESSION["sswa_user_type"]);
unset($_SESSION["sswa_user_name"]);
unset($_SESSION["sswa_user_dns_id"]);
unset($_SESSION["sswa_selected_dealer_name"]);
unset($_SESSION["sswa_selected_dealer_code"]);
unset($_SESSION["sswa_selected_customer_code"]);
header("location:index.php");
}else{
header("location:index.php");
}
?>
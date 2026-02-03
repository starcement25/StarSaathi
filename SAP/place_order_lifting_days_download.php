<?php
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$customer_master = "customer_master";
$T_DOINVOICE  = "T_DOINVOICE";
$place_order_lifting_days = "place_order_lifting_days";
$place_order_days = 0;
$curr_date_time  = date("Y-m-d H:i:s");
$order_item_arr = array();
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$curr_date = date("Y-m-d");
$before_30_day_date = date('Y-m-d', strtotime("-30 days"));
$the_customer_code = $_REQUEST["customer_code"] ? addslashes(trim($_REQUEST["customer_code"])) : "";
if ($the_customer_code == "") {
	$res_data = array("process_status" => "NO", "process_message" => "Something went wrong.", "place_order_days" => $place_order_days);
} else {
	$sqlckdays = "select `days` from $place_order_lifting_days where `branch_code`=(SELECT `branch_code` FROM customer_master WHERE customer_id='".$the_customer_code."')";
	$resckdays = mysql_query($sqlckdays);
	$totckdays = mysql_num_rows($resckdays);
	if($totckdays>0){
		$rowckdays=mysql_fetch_array($resckdays);
		$lifting_days=$rowckdays['days'];
		$res_data = array("process_status" => "YES", "process_message" => "Success", "place_order_days" => $lifting_days);
	}else{
		$lifting_days='0';
		$res_data = array("process_status" => "NO", "process_message" => "Dealer record not found.", "place_order_days" => $lifting_days);
	}
}
echo json_encode($res_data);
if ($conn != "") {
	mysql_close($conn);
}
?>

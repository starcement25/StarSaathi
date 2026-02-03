<?php
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$customer_master = "customer_master";
$customer_data = array();
$curr_date_time  = date("Y-m-d H:i:s");
$order_item_arr = array();
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$curr_date = date("Y-m-d");
$before_30_day_date = date('Y-m-d', strtotime("-30 days"));
$the_customer_code = $_REQUEST["customer_code"] ? addslashes(trim($_REQUEST["customer_code"])) : "";
if ($the_customer_code == "") {
	$res_data = array("process_status" => "NO", "process_message" => "Something went wrong.", "customer_data" => $customer_data);
} else {
	$sql_cust = "SELECT `dns_customer_code` FROM $customer_master where `customer_id`='" . $the_customer_code . "'";
	$res_cust = mysql_query($sql_cust);
	$tot_res_cust = mysql_num_rows($res_cust);
	if ($tot_res_cust > 0) {

		$row_cust = mysql_fetch_array($res_cust);
		$dns_customer_code = $row_cust['dns_customer_code'];

		/*if($start_date!="" && $end_date!=""){
		$date_qry = " and `order_date` between '".$start_date." ".$frm_hrs."' and '".$end_date." ".$to_hrs."'";
	}else{
		$date_qry = "";
	}*/
		/*$sqlall2 = "select $t_apperpdo.APPORDERNO,$t_apperpdo.order_date ,$t_apperpdo.QTY,$t_apperpdo.dns_prod_code,$t_apperpdo.prod_display_name,$t_apperpdo.STATUS ,$t_apperpdo.freight,$t_apperpdo.destination_name,$customer_master.`customer_name`,$customer_master.`customer_id` from $t_apperpdo inner join $customer_master on $t_apperpdo.`dns_customer_code`=$customer_master.`dns_customer_code`  
	where $t_apperpdo.dns_customer_code='" . $dns_customer_code . "' and `status`='Dispatched'  order by $t_apperpdo.`order_date` desc";*/
		$sqlall2 = "select * from customer_master where customer_id='".$the_customer_code."'";
		mysql_query("SET SESSION sql_mode = 'TRADITIONAL'");
		$resall2 = mysql_query($sqlall2);
		
		$totall2 = mysql_num_rows($resall2);
		if ($totall2 > 0) {
			while ($row112 = mysql_fetch_assoc($resall2)) {
						//$order_id = $row112["APPORDERNO"] ? trim($row112["APPORDERNO"]) : "";
						$customer_id  = $row112["customer_id"] ? trim($row112["customer_id"]) : "";
						$customer_name = $row112["customer_name"] ? trim($row112["customer_name"]) : "";
						$address = $row112["address"] ? trim($row112["address"]) : "";
						$PAN = $row112["PAN"] ? trim($row112["PAN"]) : "";
						$email = $row112["email"] ? trim($row112["email"]) : "";

						
						$customer_data[] = array("customer_id" => $customer_id,"customer_name" => $customer_name,"address" => $address, "PAN" => $PAN, "email" => $email);
					}
					
			$res_data = array("process_status" => "YES", "process_message" => "Success.", "customer_data" => $customer_data);
		} else {
			$res_data = array("process_status" => "NO", "process_message" => "No Customer Data found.");
		}
	} else {
		$res_data = array("process_status" => "NO", "process_message" => "Dealer record not found.", "customer_data" => $customer_data);
	}
}
echo json_encode($res_data);
if ($conn != "") {
	mysql_close($conn);
}

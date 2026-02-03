<?php
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$customer_master = "customer_master";
$T_DOCHALLAN = "T_DOCHALLAN";
$allocation_details  = "allocation_details";
$dispatched_order_data = array();
$dispatched_challan_data = array();
$curr_date_time  = date("Y-m-d H:i:s");
$order_item_arr = array();
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$curr_date = date("Y-m-d");
$before_30_day_date = date('Y-m-d', strtotime("-30 days"));
$the_customer_code = $_REQUEST["customer_code"] ? addslashes(trim($_REQUEST["customer_code"])) : "";
/*$the_status = $_REQUEST["the_status"] ? addslashes(trim($_REQUEST["the_status"])) : "";
$start_date = $_REQUEST["start_date"] ? addslashes(trim($_REQUEST["start_date"])) : $before_30_day_date;
$end_date = $_REQUEST["end_date"] ? addslashes(trim($_REQUEST["end_date"])) : $curr_date;
$page_no = $_REQUEST["page_no"] ? $_REQUEST["page_no"] : 1;
$limit = 10;
$start_from = (($page_no-1)*$limit);*/
if ($the_customer_code == "") {
	$res_data = array("process_status" => "NO", "process_message" => "Something went wrong.", "dispatched_order_data" => $dispatched_order_data);
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
		/*$sqlall2 = "select $t_apperpdo.*,$customer_master.`customer_name` from $t_apperpdo inner join $customer_master on $t_apperpdo.`dns_customer_code`=$customer_master.`dns_customer_code`  
	where $t_apperpdo.dns_customer_code='".$the_customer_code."' and (`status`='Success' or `status`='Shipped' )  $date_qry order by $t_apperpdo_pop.`order_date` desc limit $start_from,$limit";*/
		$sqlall2 = "select $t_apperpdo.APPORDERNO,$t_apperpdo.order_date ,$t_apperpdo.QTY,$t_apperpdo.dns_prod_code,$t_apperpdo.prod_display_name,$t_apperpdo.STATUS ,$t_apperpdo.freight,$t_apperpdo.destination_name,$customer_master.`customer_name`,$customer_master.`customer_id` from $t_apperpdo inner join $customer_master on $t_apperpdo.`dns_customer_code`=$customer_master.`dns_customer_code`  
	where $t_apperpdo.dns_customer_code='" . $dns_customer_code . "' and `status`='Dispatched'  order by $t_apperpdo.`order_date` desc";
		echo $sqlall2;exit();
		$resall2 = mysql_query($sqlall2);
		$totall2 = mysql_num_rows($resall2);
		if ($totall2 > 0) {
			while ($row112 = mysql_fetch_assoc($resall2)) {
				$order_id = $row112["APPORDERNO"] ? trim($row112["APPORDERNO"]) : "";
				$order_date = $row112["order_date"] ? trim($row112["order_date"]) : "";
				if ($order_date != "") {
					$order_full_date_time = date("jS M Y h:i A", strtotime($order_date));
				} else {
					$order_full_date_time = "";
				}

				$customer_name = $row112["customer_name"] ? trim($row112["customer_name"]) : "";
				$customer_id = $row112["customer_id"] ? trim($row112["customer_id"]) : "";
				$destination_name = $row112["destination_name"] ? trim($row112["destination_name"]) : "";
				$dns_prod_code = $row112["dns_prod_code"] ? trim($row112["dns_prod_code"]) : "";
				$prod_display_name = $row112["prod_display_name"] ? trim($row112["prod_display_name"]) : "";
				$qty = $row112["QTY"] ? trim($row112["QTY"]) : "";
				$freight = $row112["freight"] ? trim($row112["freight"]) : "";
				$STATUS = $row112["STATUS"] ? trim($row112["STATUS"]) : "";
				$allocation_qty = "0";
				$sqlallocation = "select SUM(allocation_qty) AS allocation_qty from $allocation_details where `customer_id`='$customer_id' and `APPORDERNO`='$order_id' AND dns_prod_code='$dns_prod_code'";
				
				$resallocation = mysql_query($sqlallocation);
				$totallocation = mysql_num_rows($resallocation);
				if ($totallocation > 0) {
					while ($rowallocation = mysql_fetch_assoc($resallocation)) {
						$allocation_qty = $rowallocation["allocation_qty"] ? trim($rowallocation["allocation_qty"]) : "0";
					}
				}


				$sqlall3 = "select * from $T_DOCHALLAN where `dns_customer_code`='$dns_customer_code' and `APPORDERNO`='$order_id' 
	AND dns_prod_code='$dns_prod_code'";
				$resall3 = mysql_query($sqlall3);
				$totall3 = mysql_num_rows($resall3);
				if ($totall3 > 0) {
					$dispatched_challan_data = array();
					while ($row113 = mysql_fetch_assoc($resall3)) {
						$ch_challanno = $row113["CHALLANNO"] ? trim($row113["CHALLANNO"]) : "";
						$ch_challandt = $row113["CHALLANDT"] ? trim($row113["CHALLANDT"]) : "";
						$ch_challanqty = $row113["CHALLANQTY"] ? trim($row113["CHALLANQTY"]) : "";

						if ($ch_challandt != "") {
							$challan_full_date_time = date("jS M Y h:i A", strtotime($ch_challandt));
						} else {
							$challan_full_date_time = "";
						}
						$ch_challandt_part =substr($ch_challandt,0,10);
						
						$sqlallocationqty = "select SUM(allocation_qty) tot_allocation_qty from $allocation_details where `customer_id`='$the_customer_code' and `dns_prod_code`='$dns_prod_code' AND  	dispatch_date='$ch_challandt' GROUP BY dns_prod_code,dispatch_date";
						echo $sqlallocation;//exit();
						$resallocationqty = mysql_query($sqlallocationqty);
						$totresallocationqty = mysql_num_rows($resallocationqty);
						$rowallocationqty=mysql_fetch_array($resallocationqty);
						$totresallocationqty=$rowallocationqty['tot_allocation_qty'];
						if($totresallocationqty=='') $totresallocationqty=0;

						// echo "SQL: $sql\n";

						// get the sum of allocation_qty
						/*$sum_allocation_qty = 0;
						if (	$totres > 0) {
							while ($row = mysql_fetch_assoc($res)) {
								$sum_allocation_qty += $row["allocation_qty"];
							}
						}*/
						$available_allocation_qty=$ch_challanqty-$totresallocationqty;

						$dispatched_challan_data[] = array("challanno" => $ch_challanno, "dispatch_date" => $ch_challandt, "dispatch_qty" => $ch_challanqty,"available_allocation_qty" => $available_allocation_qty);
						
					}
				}
					//$ch_challandt
				
				// echo "sum_allocation_qty: $sum_allocation_qty\n";
				// get the dispatch_qty of the latest allocation
				/*$dispatch_qty = 0;
				$sql = "select `dispatch_qty` from $allocation_details where `customer_id`='$customer_id' and `dns_prod_code`='$dns_prod_code' order by `date_and_time` desc limit 1";
				$res = mysql_query($sql);
				$totres = mysql_num_rows($res);
				if ($totres > 0) {
					$row = mysql_fetch_assoc($res);
					$dispatch_qty = $row["dispatch_qty"];
				}*/
				// echo "dispatch_qty: $dispatch_qty\n";
				// get available_allocation_qty
				//$available_allocation_qty = $dispatch_qty - $sum_allocation_qty;
				$dispatched_order_data[] = array("order_id" => $order_id, "order_date" => $order_full_date_time, "customer_name" => $customer_name, "destination_name" => $destination_name, "dns_prod_code" => $dns_prod_code, "prod_display_name" => $prod_display_name, "qty" => $qty, "freight" => $freight, "STATUS" => $STATUS,  "dispatched_challan_data" => $dispatched_challan_data);
			}
			$res_data = array("process_status" => "YES", "process_message" => "Success.", "dispatched_order_data" => $dispatched_order_data);
		} else {
			$res_data = array("process_status" => "NO", "process_message" => "No Dispatched order found.");
		}
	} else {
		$res_data = array("process_status" => "NO", "process_message" => "Dealer record not found.", "dispatched_order_data" => $dispatched_order_data);
	}
}
echo json_encode($res_data);
if ($conn != "") {
	mysql_close($conn);
}

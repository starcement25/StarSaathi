<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$customer_master = "customer_master";
$T_DOINVOICE  = "T_DOINVOICE";
$allocation_details  = "allocation_details_invoicewise";
$branch_rssd_allocation_days = "branch_rssd_allocation_days";
$dispatched_order_data = array();
$dispatched_challan_data = array();
$curr_date_time  = date("Y-m-d H:i:s");
$order_item_arr = array();
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$curr_date = date("Y-m-d");
$before_30_day_date = date('Y-m-d', strtotime("-30 days"));
$the_customer_code = $_REQUEST["customer_code"] ? addslashes(trim($_REQUEST["customer_code"])) : "";
$month_year = $_REQUEST["month_year"] ? addslashes(trim($_REQUEST["month_year"])) : "";
$month_year_val=str_replace('-','',$month_year);

$customer_destination="customer_destination";
$branch_master = "branch_master";
$destination_master = "destination_master";
//$t_apperpdo = "T_APPERPDO_OFFLINE";
/*$the_status = $_REQUEST["the_status"] ? addslashes(trim($_REQUEST["the_status"])) : "";
$start_date = $_REQUEST["start_date"] ? addslashes(trim($_REQUEST["start_date"])) : $before_30_day_date;
$end_date = $_REQUEST["end_date"] ? addslashes(trim($_REQUEST["end_date"])) : $curr_date;
$page_no = $_REQUEST["page_no"] ? $_REQUEST["page_no"] : 1;
$limit = 10;
$start_from = (($page_no-1)*$limit);*/
if ($the_customer_code == "") {
	$res_data = array("process_status" => "NO", "process_message" => "Something went wrong.", "dispatched_order_data" => $dispatched_order_data);
} else {
	$sqlckdays = "select `allocation_days` from $branch_rssd_allocation_days where `branch_code`=(SELECT `branch_code` FROM customer_master WHERE customer_id='".$the_customer_code."')";
	$resckdays = mysql_query($sqlckdays);
	$totckdays = mysql_num_rows($resckdays);
	if($totckdays>0){
		$rowckdays=mysql_fetch_array($resckdays);
		$rssd_allocation_days=$rowckdays['allocation_days'];
	}else{
		$rssd_allocation_days='0';
	}

	$sql_cust = "SELECT `dns_customer_code` FROM $customer_master where `customer_id`='" . $the_customer_code . "'";
	//echo"<pre>";print_r($sql_cust);die;
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

				if($month_year_val!=''){
					$date_condition=" AND SUBSTRING($T_DOINVOICE.INVDT,1,6)='".$month_year_val."'";
				}
				//$sqlall3 = "select * from $T_DOINVOICE where `APPORDERNO`='$order_id' AND INVNO NOT IN(SELECT DISTINCT inv_no FROM $allocation_details WHERE `inv_cancl`='yes' )".$date_condition;
				$sqlall3 = "select * from $T_DOINVOICE where `APPORDERNO`='$order_id' AND INVNO NOT IN(SELECT DISTINCT inv_no FROM $allocation_details WHERE `inv_cancl`='yes' )
				".$date_condition;
				$resall3 = mysql_query($sqlall3);
				$totall3 = mysql_num_rows($resall3);
				if ($totall3 > 0) {
					$dispatched_invoice_data = array();
					while ($row1 = mysql_fetch_assoc($resall3)) {
						$ch_uid = $row1["id"];
						$apporder_no_chtl = $row1["APPORDERNO"];
						$erporder_no_chtl = $row1["ERPORDERNO"];
						$challanno_chtl = $row1["CHALLANNO"];
						$INVNO= $row1["INVNO"];
						$INVDT= $row1["INVDT"];
						$prod_display_name_chtl= $row1["prod_display_name"];
						$INVQTY= $row1["INVQTY"];
						$TRUCKNO = $row1["TRUCKNO"];
						$destination = $row1["destination"];
						$customer_code = $row1["customer_code"];
						$INVDT_formatted=date('Y-m-d',strtotime($INVDT));

						/*if ($INVDT != "") {
							$INVDT_time = date("jS M Y", strtotime($INVDT));
						} else {
							$INVDT_time = "";
						}
						//$ch_challandt_part =substr($ch_challandt,0,10);*/
						//sk190525
						/* $sqlallocationqty = "select SUM(allocation_qty) tot_allocation_qty from $allocation_details where `customer_id`='$the_customer_code' and `prod_desc`='$prod_display_name_chtl' AND  	inv_date='$INVDT_formatted' and inv_no='$INVNO' GROUP BY prod_desc,inv_date";*/

						$sqlallocationqty = "select SUM(allocation_qty) tot_allocation_qty from $allocation_details where `customer_id`='$the_customer_code' and  	inv_date='$INVDT_formatted' and inv_no='$INVNO' and delete_at='0' GROUP BY prod_desc,inv_date";

						$resallocationqty = mysql_query($sqlallocationqty);
						//$current_db = mysql_result(mysql_query("SELECT DATABASE()"), 0);
						//echo "Current database: " . $current_db; die;
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
						$available_allocation_qty=$INVQTY-$totresallocationqty;

						$dispatched_invoice_data[] = array("ch_uid"=>$ch_uid,"apporderno"=>$apporder_no_chtl,"erporderno"=>$erporder_no_chtl,"challanno"=>$challanno_chtl,"invno"=>$INVNO,"invdt"=>$INVDT_formatted,"prod_display_name"=>$prod_display_name_chtl,"invqty"=>$INVQTY,"customer_code"=>$customer_code,"truckno"=>$TRUCKNO,"destination"=>$destination,"available_allocation_qty" => $available_allocation_qty);

					}
					$dispatched_order_data[] = array(
						"order_id" => $order_id, 
						"order_date" => $order_full_date_time, 
						"customer_name" => preg_replace('/[^\x20-\x7E]/', '', $customer_name), 
						"destination_name" => preg_replace('/[^\x20-\x7E]/', '', $destination_name), 
						"dns_prod_code" => $dns_prod_code, 
						"prod_display_name" => $prod_display_name, 
						"qty" => $qty, "freight" => $freight, 
						"STATUS" => $STATUS,  
						"dispatched_invoice_data" => $dispatched_invoice_data);
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

			}
			//get  offline data  sk190525
			// 1. Query the table
			if($month_year!=''){
				$date_condition=" AND SUBSTRING(ERPORDERDT,1,7)='".$month_year."'";
			}
			//echo"<pre>";print_r($date_condition);die;
			//$t_apperpdo_offline="T_APPERPDO";
			$t_apperpdo_offline="T_APPERPDO_OFFLINE";
				$query = "SELECT
				$t_apperpdo_offline.id,
				$t_apperpdo_offline.ERPORDERNO,
				$t_apperpdo_offline.ERPORDERDT,
				$customer_master.customer_name,
				$destination_master.destination_name,
				$t_apperpdo_offline.dns_prod_code,
				$t_apperpdo_offline.prod_display_name,
				$t_apperpdo_offline.QTY,
				$t_apperpdo_offline.freight,
				$t_apperpdo_offline.customer_code,
				$t_apperpdo_offline.STATUS
				FROM $t_apperpdo_offline
				left join $customer_master on $t_apperpdo_offline.`customer_code`=$customer_master.`customer_code`
				 left join $customer_destination ON $customer_destination.customer_code=$customer_master.`customer_code`
				 left join $destination_master ON $customer_destination.destination_code=$destination_master.destination_code
				where $t_apperpdo_offline.STATUS='Dispatched' 
				AND $t_apperpdo_offline.prod_code !=''
				AND $t_apperpdo_offline.dns_customer_code='" . $dns_customer_code . "' $date_condition
				"; // Change to your actual table name
				//echo"<pre>";print_r($query);die;
				$result = mysql_query($query);

				// 2. Prepare array
				//$dispatched_order_data = [];

				if (mysql_num_rows($result) > 0) {
				while ($row = mysql_fetch_array($result)) {
					// Example: You can pull actual invoice data per row if needed
					$dispatched_invoice_data = []; // Optional: Fill this based on another query

					$order_id = $row["APPORDERNO"] ? trim($row["APPORDERNO"]) : "";
					if($month_year_val!=''){
						$date_condition=" AND SUBSTRING($T_DOINVOICE.INVDT,1,6)='".$month_year_val."'";
					}

					
				$order_date_off = $row["ERPORDERDT"] ? trim($row["ERPORDERDT"]) : "";
				if ($order_date_off != "") {
					$order_date_time = date("jS M Y h:i A", strtotime($order_date_off));
				} else {
					$order_date_time = "";
				}
				// 	$sqlall3 = "select * from $T_DOINVOICE where `APPORDERNO`='$order_id' AND INVNO NOT IN(SELECT DISTINCT inv_no FROM $allocation_details WHERE `inv_cancl`='yes'  )
				// ".$date_condition;
				// //echo"<pre>";print_r($sqlall3);die;
				// $resall3 = mysql_query($sqlall3);
				// $totall3 = mysql_num_rows($resall3);
				// if ($totall3 > 0) {
				// 	$dispatched_invoice_data = array();
				// 	while ($row1 = mysql_fetch_assoc($resall3)) {
						$ch_uid = $row["id"];
						$apporder_no_chtl = $row["ERPORDERNO"];
						$erporder_no_chtl = $row["ERPORDERNO"];
						$challanno_chtl = '';
						$INVNO= $row["ERPORDERNO"];
						$INVDT= $row["ERPORDERDT"];
						$prod_display_name_chtl= $row["prod_display_name"];
						$INVQTY= $row["QTY"];
						$TRUCKNO = '';
						$destination = '';
						$customer_code = $row["customer_code"];
						$INVDT_formatted=date('Y-m-d',strtotime($INVDT));
						$sqlallocationqty = "select SUM(allocation_qty) tot_allocation_qty from $allocation_details where `customer_id`='$the_customer_code'  AND  	inv_date='$INVDT_formatted' and inv_no='$INVNO' and delete_at='0' ";
						//echo"<pre>";print_r($sqlallocationqty);die;
						$resallocationqty = mysql_query($sqlallocationqty);
						//$current_db = mysql_result(mysql_query("SELECT DATABASE()"), 0);
						//echo "Current database: " . $current_db; die;
						$totresallocationqty = mysql_num_rows($resallocationqty);
						$rowallocationqty=mysql_fetch_array($resallocationqty);
						$totresallocationqty=$rowallocationqty['tot_allocation_qty'];
						if($totresallocationqty=='') $totresallocationqty=0;

						$available_allocation_qty=$INVQTY-$totresallocationqty;

						$dispatched_invoice_data[] = array("ch_uid"=>$ch_uid,"apporderno"=>$apporder_no_chtl,"erporderno"=>$erporder_no_chtl,"challanno"=>$challanno_chtl,"invno"=>$INVNO,"invdt"=>$INVDT_formatted,"prod_display_name"=>$prod_display_name_chtl,"invqty"=>$INVQTY,"customer_code"=>$customer_code,"truckno"=>$TRUCKNO,"destination"=>$destination,"available_allocation_qty" => $available_allocation_qty);

				// 	}
				// }

					$dispatched_order_data[] = array(
						"order_id" => preg_replace('/[^\x20-\x7E]/', '', $row['ERPORDERNO']),
						//"order_date" => $row['ERPORDERDT'],
						"order_date" => $order_date_time,
						"customer_name" => preg_replace('/[^\x20-\x7E]/', '', $row['customer_name']),
						"destination_name" => preg_replace('/[^\x20-\x7E]/', '', $row['destination_name']),
						"dns_prod_code" => $row['dns_prod_code'],
						"prod_display_name" => $row['prod_display_name'],
						"qty" => $row['QTY'],
						"freight" => $row['freight'],
						"STATUS" => $row['STATUS'],
						"OFFLINE" => '1',
						"dispatched_invoice_data" => $dispatched_invoice_data
					);
				}
				} else {
				//echo "No records found.";
				}

			//end  offline data  sk190525
			
				$res_data = array("process_status" => "YES", "process_message" => "Success.", "rssd_allocation_days" => $rssd_allocation_days, "dispatched_order_data" => $dispatched_order_data);
				
			
			
		} else {
			$res_data = array("process_status" => "NO", "process_message" => "No Dispatched order found.", "dispatched_order_data" => $dispatched_order_data);
		}
	} else {
		$res_data = array("process_status" => "NO", "process_message" => "Dealer record not found.", "dispatched_order_data" => $dispatched_order_data);
	}
}
//print_r($dispatched_order_data);
echo json_encode($res_data);
if ($conn != "") {
	mysql_close($conn);
}

<?php
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$customer_master = "customer_master";
$T_DOINVOICE = "T_DOINVOICE";
$allocation_details  = "allocation_details_invoicewise";
$curr_date_time  = date("Y-m-d H:i:s");
$order_item_arr = array();
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$dispatched_invoice_data=array();
$curr_date = date("Y-m-d");
$before_30_day_date = date('Y-m-d', strtotime("-30 days"));
$invoice_no = $_REQUEST["invoice_no"] ? addslashes(trim($_REQUEST["invoice_no"])) : "";
$app_orderno = $_REQUEST["app_orderno"] ? addslashes(trim($_REQUEST["app_orderno"])) : "";
/*$the_status = $_REQUEST["the_status"] ? addslashes(trim($_REQUEST["the_status"])) : "";
$start_date = $_REQUEST["start_date"] ? addslashes(trim($_REQUEST["start_date"])) : $before_30_day_date;
$end_date = $_REQUEST["end_date"] ? addslashes(trim($_REQUEST["end_date"])) : $curr_date;
$page_no = $_REQUEST["page_no"] ? $_REQUEST["page_no"] : 1;
$limit = 10;
$start_from = (($page_no-1)*$limit);*/
if ($invoice_no == "") {
	$res_data = array("process_status" => "NO", "process_message" => "Something went wrong.", "dispatched_invoice_data" => $dispatched_invoice_data);
} else {
	if($app_orderno == ""){
		$sqlall2 = "select $T_DOINVOICE.INVNO,SUM($T_DOINVOICE.INVQTY) AS TOT_INVQTY from $T_DOINVOICE
	where $T_DOINVOICE.INVNO='".$invoice_no."'  and $T_DOINVOICE.APPORDERNO!=''  and $T_DOINVOICE.APPORDERNO IS NOT NULL GROUP BY $T_DOINVOICE.INVNO";
	
	}else{
		$sqlall2 = "select $T_DOINVOICE.INVNO,SUM($T_DOINVOICE.INVQTY) AS TOT_INVQTY from $T_DOINVOICE
	where $T_DOINVOICE.INVNO='".$invoice_no."' and $T_DOINVOICE.APPORDERNO='".$app_orderno."'  and $T_DOINVOICE.APPORDERNO!=''  and $T_DOINVOICE.APPORDERNO IS NOT NULL GROUP BY $T_DOINVOICE.INVNO";
	}
		
		//mysql_query("SET SESSION sql_mode = 'TRADITIONAL'");
		//echo"<pre>";print_r($sqlall2);die;

		$resall2 = mysql_query($sqlall2);

		$totall2 = mysql_num_rows($resall2);
		if ($totall2 > 0) {
			while ($row112 = mysql_fetch_assoc($resall2)) {
						//$order_id = $row112["APPORDERNO"] ? trim($row112["APPORDERNO"]) : "";
						$ch_invno = $row112["INVNO"] ? trim($row112["INVNO"]) : "";
						$ch_invqty = $row112["TOT_INVQTY"] ? trim($row112["TOT_INVQTY"]) : "";
						if($app_orderno == ""){
						$sqlallocationqty = "select SUM(allocation_qty) tot_allocation_qty from $allocation_details where  inv_no='$ch_invno' and delete_at='0' GROUP BY inv_no";
						}else{
							$sqlallocationqty = "select SUM(allocation_qty) tot_allocation_qty from $allocation_details where  inv_no='$ch_invno' and APPORDERNO='$app_orderno' and delete_at='0' GROUP BY inv_no";	
						}
						//echo"<pre>";print_r($sqlallocationqty);die;
						$resallocationqty = mysql_query($sqlallocationqty);
						$totresallocationqty = mysql_num_rows($resallocationqty);
						$rowallocationqty=mysql_fetch_array($resallocationqty);
						$totresallocationqty=$rowallocationqty['tot_allocation_qty'];
						if($totresallocationqty=='') $totresallocationqty=0;


						$available_allocation_qty=$ch_invqty-$totresallocationqty;

						$dispatched_invoice_data[] = array("INVNO" => $ch_invno, "inv_qty" => $ch_invqty,"available_allocation_qty" => $available_allocation_qty);
					}

			$res_data = array("process_status" => "YES", "process_message" => "Success.", "dispatched_invoice_data" => $dispatched_invoice_data);
		} else {
			// add offline order 06_06_25
			$offline_found=0;
			$T_APPERPDO_OFFLINE="T_APPERPDO_OFFLINE";
			$sqlall2 = "select $T_APPERPDO_OFFLINE.ERPORDERNO,SUM($T_APPERPDO_OFFLINE.QTY) AS TOT_INVQTY from $T_APPERPDO_OFFLINE
			where $T_APPERPDO_OFFLINE.ERPORDERNO='".$invoice_no."' and $T_APPERPDO_OFFLINE.ERPORDERNO!=''  and $T_APPERPDO_OFFLINE.ERPORDERNO IS NOT NULL GROUP BY $T_APPERPDO_OFFLINE.ERPORDERNO";
				//mysql_query("SET SESSION sql_mode = 'TRADITIONAL'");
				$resall2 = mysql_query($sqlall2);

				$totall2 = mysql_num_rows($resall2);
				if ($totall2 > 0) {
					while ($row112 = mysql_fetch_assoc($resall2)) {
								//$order_id = $row112["APPORDERNO"] ? trim($row112["APPORDERNO"]) : "";
								$ch_invno = $row112["ERPORDERNO"] ? trim($row112["ERPORDERNO"]) : "";
								$ch_invqty = $row112["TOT_INVQTY"] ? trim($row112["TOT_INVQTY"]) : "";

								$sqlallocationqty = "select SUM(allocation_qty) tot_allocation_qty from $allocation_details where  inv_no='$ch_invno' and delete_at='0' GROUP BY inv_no";
								//echo"<pre>";print_r($sqlallocationqty);die;
								$resallocationqty = mysql_query($sqlallocationqty);
								$totresallocationqty = mysql_num_rows($resallocationqty);
								$rowallocationqty=mysql_fetch_array($resallocationqty);
								$totresallocationqty=$rowallocationqty['tot_allocation_qty'];
								if($totresallocationqty=='') $totresallocationqty=0;


								$available_allocation_qty=$ch_invqty-$totresallocationqty;

								$dispatched_invoice_data[] = array("INVNO" => $ch_invno, "inv_qty" => $ch_invqty,"available_allocation_qty" => $available_allocation_qty);
							}

					$res_data = array("process_status" => "YES", "process_message" => "Success.", "dispatched_invoice_data" => $dispatched_invoice_data);
					$offline_found=1;
				} else {
					$res_data = array("process_status" => "NO", "process_message" => "No Invoice details found.");
				}
				//online order not found
				if($offline_found==0){
					$res_data = array("process_status" => "NO", "process_message" => "No Invoice details found.");
				}

			//end add offline order 06_06_25


		}
	}
echo json_encode($res_data);
if ($conn != "") {
	mysql_close($conn);
}
?>

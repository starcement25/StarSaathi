<?php
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$T_DOCHALLAN="T_DOCHALLAN";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";
$subdealer_order_data = array();
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$curr_date = date("Y-m-d");
$before_30_day_date = date('Y-m-d',strtotime("-30 days"));
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
$the_status = $_REQUEST["the_status"] ? addslashes(trim($_REQUEST["the_status"])) : "";
$start_date = $_REQUEST["start_date"] ? addslashes(trim($_REQUEST["start_date"])) : $before_30_day_date;
$end_date = $_REQUEST["end_date"] ? addslashes(trim($_REQUEST["end_date"])) : $curr_date;
$limit = 10;
$start_from = (($page_no-1)*$limit);
if($the_id!=""){
	if($start_date!="" && $end_date!=""){
		$date_qry = " and `order_date` between '".$start_date." ".$frm_hrs."' and '".$end_date." ".$to_hrs."'";
	}else{
		$date_qry = "";
	}
$sql3 = "select `dns_customer_code` from $customer_master where `customer_code`='$the_id'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
$row3 = mysql_fetch_assoc($res3);
$the_sub_dealer_id = addslashes(trim($row3["dns_customer_code"]));

if($the_status!=""){
	$status_qry = " and $t_apperpdo.`STATUS`='$the_status' ";
}else{
	$status_qry = "";
}

$sqlall2 = "select $t_apperpdo.*,$customer_master.`customer_name` from $t_apperpdo left join $customer_master on $t_apperpdo.`dns_sub_dealer_code`=$customer_master.`dns_customer_code`  where ($t_apperpdo.`dns_sub_dealer_code`='$the_sub_dealer_id' OR $t_apperpdo.`dns_sub_dealer_code` IN(SELECT $customer_master.dns_customer_code FROM $customer_master WHERE $customer_master.rds_tag='".$the_id."' 
AND $customer_master.cust_type='ShiptoParty-Subdeale')) $status_qry $date_qry order by $t_apperpdo.`order_date` desc";
$resall2 = mysql_query($sqlall2);
$totall2 = mysql_num_rows($resall2);
if($totall2>0){
while($row112=mysql_fetch_assoc($resall2)){
$order_id = $row112["APPORDERNO"] ? trim($row112["APPORDERNO"]) : "";
$erporderno = $row112["ERPORDERNO"] ? trim($row112["ERPORDERNO"]) : "";
$order_date = $row112["order_date"] ? trim($row112["order_date"]) : "";
if($order_date!=""){
			$order_full_date_time = date("jS M Y h:i A",strtotime($order_date));
		}else{
			$order_full_date_time = "";
		}
		
$consignee_name = $row112["consignee_name"] ? trim($row112["consignee_name"]) : "";
$consignee_address = $row112["consignee_address"] ? trim($row112["consignee_address"]) : "";
$destination_address = $row112["destination_address"] ? trim($row112["destination_address"]) : "";
$sub_dealer_code = $row112["sub_dealer_code"] ? trim($row112["sub_dealer_code"]) : "";
$dns_sub_dealer_code = $row112["dns_sub_dealer_code"] ? trim($row112["dns_sub_dealer_code"]) : "";
$belong_dealer_code = $row112["customer_code"] ? trim($row112["customer_code"]) : "";
$belong_dealer_dns_code = $row112["dns_customer_code"] ? trim($row112["dns_customer_code"]) : "";
$prod_code = $row112["prod_code"] ? trim($row112["prod_code"]) : "";
$dns_prod_code = $row112["dns_prod_code"] ? trim($row112["dns_prod_code"]) : "";
$prod_display_name = $row112["prod_display_name"] ? trim($row112["prod_display_name"]) : "";
$qty = $row112["QTY"] ? trim($row112["QTY"]) : "";
$status = $row112["STATUS"] ? trim($row112["STATUS"]) : "";
$freight = $row112["freight"] ? trim($row112["freight"]) : "";
if($freight=='EXW') $freight='EX';

$the_actual_ord_id = $erporderno ? addslashes($erporderno) : addslashes($order_id);
		$subdealer_challan_data = array();
		if($the_actual_ord_id!=""){
			$sqlall3 = "select * from $T_DOCHALLAN where (`APPORDERNO`='$the_actual_ord_id' or `ERPORDERNO`='$the_actual_ord_id')";
			$resall3 = mysql_query($sqlall3);
			$totall3 = mysql_num_rows($resall3);			
			if($totall3>0){
				while($row113=mysql_fetch_assoc($resall3)){
				$ch_uid = $row113["id"];
				$ch_apporderno = $row113["APPORDERNO"] ? trim($row113["APPORDERNO"]) : "";
				$ch_erporderno = $row113["ERPORDERNO"] ? trim($row113["ERPORDERNO"]) : "";
				$ch_erporderdt = $row113["ERPORDERDT"] ? trim($row113["ERPORDERDT"]) : "";
				$ch_challanno = $row113["CHALLANNO"] ? trim($row113["CHALLANNO"]) : "";
				$ch_challandt = $row113["CHALLANDT"] ? trim($row113["CHALLANDT"]) : "";
				if($ch_challandt!=""){
			$challan_full_date_time = date("jS M Y h:i A",strtotime($ch_challandt));
		}else{
			$challan_full_date_time = "";
		}
				$ch_prod_code = $row113["prod_code"] ? trim($row113["prod_code"]) : "";
				$ch_dns_prod_code = $row113["dns_prod_code"] ? trim($row113["dns_prod_code"]) : "";
				$ch_prod_display_name = $row113["prod_display_name"] ? trim($row113["prod_display_name"]) : "";
				$ch_qty = $row113["QTY"] ? trim($row113["QTY"]) : "";
				$ch_challanqty = $row113["CHALLANQTY"] ? trim($row113["CHALLANQTY"]) : "";
				$ch_customer_code = $row113["customer_code"];
				$ch_dns_customer_code = $row113["dns_customer_code"];
				$ch_truckno = $row113["TRUCKNO"] ? trim($row113["TRUCKNO"]) : "";
				$ch_driverno = $row113["DRIVERNO"] ? trim($row113["DRIVERNO"]) : "";
				$ch_ischlnimp = "";
				
				$is_confirmed_challan_material_received = $row113["is_confirmed_challan_material_received"] ? trim($row113["is_confirmed_challan_material_received"]) : "";
				$challan_quantity_checking = $row113["challan_quantity_checking"] ? trim($row113["challan_quantity_checking"]) : "";
				$ch_quantity_no_of_bags = $row113["ch_quantity_no_of_bags"] ? trim($row113["ch_quantity_no_of_bags"]) : "";
				
				$challan_quality_checking = $row113["challan_quality_checking"] ? trim($row113["challan_quality_checking"]) : "";
				$ch_quality_no_of_damaged_bags = $row113["ch_quality_no_of_damaged_bags"] ? trim($row113["ch_quality_no_of_damaged_bags"]) : "";
				
				$challan_remarks = $row113["challan_remarks"] ? trim($row113["challan_remarks"]) : "";
				$ch_transporter_name = $row113["transporter_name"] ? trim($row113["transporter_name"]) : "";
				$ch_ch_status = $row113["ch_status"] ? trim($row113["ch_status"]) : "Pending";
	
		
				$subdealer_challan_data[] = array("ch_uid"=>$ch_uid,"apporderno"=>$ch_apporderno,"erporderno"=>$ch_erporderno,"erporderdt"=>$ch_erporderdt,"challanno"=>$ch_challanno,"challandt"=>$challan_full_date_time,"prod_code"=>$ch_prod_code,"dns_prod_code"=>$ch_dns_prod_code,"prod_display_name"=>$ch_prod_display_name,"qty"=>$ch_qty,"challanqty"=>$ch_challanqty,"customer_code"=>$ch_customer_code,"dns_customer_code"=>$ch_dns_customer_code,"truckno"=>$ch_truckno,"driverno"=>$ch_driverno,"ischlnimp"=>$ch_ischlnimp,"is_confirmed_challan_material_received"=>$is_confirmed_challan_material_received,"challan_quantity_checking"=>$challan_quantity_checking,"ch_quantity_no_of_bags"=>$ch_quantity_no_of_bags,"challan_quality_checking"=>$challan_quality_checking,"ch_quality_no_of_damaged_bags"=>$ch_quality_no_of_damaged_bags,"challan_remarks"=>$challan_remarks,"transporter_name"=>$ch_transporter_name,"ch_status"=>$ch_ch_status);
				
				}
			}			
		}
$subdealer_order_data[] = array("order_id"=>$order_id,"order_date"=>$order_full_date_time,"consignee_name"=>$consignee_name,"consignee_address"=>$consignee_address,"destination_address"=>$destination_address,"freight"=>$freight,"sub_dealer_code"=>$sub_dealer_code,"dns_sub_dealer_code"=>$dns_sub_dealer_code,"belong_dealer_code"=>$belong_dealer_code,"belong_dealer_dns_code"=>$belong_dealer_dns_code,"prod_code"=>$prod_code,"dns_prod_code"=>$dns_prod_code,"prod_display_name"=>$prod_display_name,"qty"=>$qty,"status"=>$status,"subdealer_challan_data"=>$subdealer_challan_data);
}
$res_data = array("process_status"=>"YES","process_message"=>"Success.","subdealer_order_data"=>$subdealer_order_data);
}else{
$res_data = array("process_status"=>"NO","process_message"=>"No new order found.");
}
}else{
$res_data = array("process_status"=>"NO","process_message"=>"Dealer details not found.");
}
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"The id is mandatory.");
}	
echo json_encode($res_data);
mysql_close();
?>
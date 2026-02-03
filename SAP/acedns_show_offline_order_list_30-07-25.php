<?php
header('Content-type: text/html; charset=utf-8');
include "star_connection.php";
mysql_set_charset("UTF8");
$T_APPERPDO_OFFLINE = "T_APPERPDO_OFFLINE";
$T_DOCHALLAN = "T_DOCHALLAN";
$customer_destination="customer_destination";
$destination_master="destination_master";
$customer_master="customer_master";
$res_data = array();
$order_data = array();
$order_challan_data = array();
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$curr_date = date("Y-m-d");
$before_30_day_date = date('Y-m-d',strtotime("-30 days"));
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
$start_date = $_REQUEST["start_date"] ? addslashes(trim($_REQUEST["start_date"])) : $before_30_day_date;
$end_date = $_REQUEST["end_date"] ? addslashes(trim($_REQUEST["end_date"])) : $curr_date;
$user_type = $_REQUEST["user_type"] ? addslashes(trim($_REQUEST["user_type"])) : "";
if($the_id!=""){

	if($start_date!="" && $end_date!=""){
		$date_qry = " and $T_APPERPDO_OFFLINE.`ERPORDERDT` between '".$start_date." ".$frm_hrs."' and '".$end_date." ".$to_hrs."'";
	}else{
		$date_qry = "";
	}


if(strtoupper($user_type)=='DEALER' || strtoupper($user_type)=='BROKER')
{
/*$sqlall = "select $T_APPERPDO_OFFLINE.*,$destination_master.dns_destination_code,$destination_master.destination_name from $T_APPERPDO_OFFLINE left join $customer_destination ON $customer_destination.customer_code=$T_APPERPDO_OFFLINE.`customer_code` left join $destination_master ON $customer_destination.destination_code=$destination_master.destination_code where $T_APPERPDO_OFFLINE.`customer_code`='$the_id' $date_qry order by $T_APPERPDO_OFFLINE.`ERPORDERDT` desc";*/

$sqlall = "select $T_APPERPDO_OFFLINE.*,$destination_master.dns_destination_code,$destination_master.destination_name from $T_APPERPDO_OFFLINE left join $customer_destination ON $customer_destination.customer_code=$T_APPERPDO_OFFLINE.`customer_code` left join $destination_master ON $customer_destination.destination_code=$destination_master.destination_code where $T_APPERPDO_OFFLINE.`consignee_code`='$the_id' $date_qry order by $T_APPERPDO_OFFLINE.`ERPORDERDT` desc";
}
else
{
/*$sqlall = "select $T_APPERPDO_OFFLINE.*,$destination_master.dns_destination_code,$destination_master.destination_name from $T_APPERPDO_OFFLINE left join $customer_destination ON $customer_destination.customer_code=$T_APPERPDO_OFFLINE.`customer_code` left join $destination_master ON $customer_destination.destination_code=$destination_master.destination_code  where ($T_APPERPDO_OFFLINE.`consignee_code`='$the_id' OR $T_APPERPDO_OFFLINE.`dns_consignee_code` IN(SELECT $customer_master.dns_customer_code FROM $customer_master WHERE $customer_master.rds_tag='".$the_id."'
AND $customer_master.cust_type='ShiptoParty-Subdeale')) $date_qry order by $T_APPERPDO_OFFLINE.`ERPORDERDT` desc";*/

$sqlall = "select $T_APPERPDO_OFFLINE.*,$destination_master.dns_destination_code,$destination_master.destination_name from $T_APPERPDO_OFFLINE left join $customer_destination ON $customer_destination.customer_code=$T_APPERPDO_OFFLINE.`customer_code` left join $destination_master ON $customer_destination.destination_code=$destination_master.destination_code  where ($T_APPERPDO_OFFLINE.`consignee_code`='$the_id' OR $T_APPERPDO_OFFLINE.`dns_consignee_code` IN(SELECT $customer_master.dns_customer_code FROM $customer_master WHERE $customer_master.rds_tag='".$the_id."'
AND $customer_master.cust_type='ShiptoParty-Subdeale')) $date_qry order by $T_APPERPDO_OFFLINE.`ERPORDERDT` desc";
}
//echo"<pre>";print_r($sqlall);die;
$resall = mysql_query($sqlall);
$totall = mysql_num_rows($resall);
if($totall>0){
	while($row11=mysql_fetch_assoc($resall)){
		$erporderno = $row11["ERPORDERNO"] ? trim($row11["ERPORDERNO"]) : "";
		$erporderdt = $row11["ERPORDERDT"];
			if($erporderdt!=""){
			$erporderdt_full_date_time = date("jS M Y h:i A",strtotime($erporderdt));
		}else{
			$erporderdt_full_date_time = "";
		}
		$customer_code = $row11["customer_code"];
		$dns_customer_code = $row11["dns_customer_code"];
		$status = $row11["STATUS"];
				$the_destination_code = $row11["dns_destination_code"];
		$destination_name = $row11["destination_name"] ? trim($row11["destination_name"]) : "";


		$prod_code = $row11["prod_code"];
		$dns_prod_code = $row11["dns_prod_code"];
		$prod_display_name = $row11["prod_display_name"];

		$qty = $row11["QTY"];
		$freight = $row11["freight"] ? trim($row11["freight"]) : "";
		$dump = $row11["dump_name"] ? trim($row11["dump_name"]) : "";

		$is_confirmed_material_received = $row11["is_confirmed_material_received"] ? trim($row11["is_confirmed_material_received"]) : "";
		$quantity_checking = $row11["quantity_checking"] ? trim($row11["quantity_checking"]) : "";
		$quality_checking = $row11["quality_checking"] ? trim($row11["quality_checking"]) : "";
		$remarks = $row11["remarks"] ? trim($row11["remarks"]) : "";

		//$the_actual_ord_id = $apporderno ? addslashes($apporderno) : addslashes($erporderno);
		$the_actual_ord_id = $erporderno ? addslashes($erporderno) : addslashes($apporderno);
		$order_challan_data = array();

		if($the_actual_ord_id!=""){
			$sqlall2 = "select * from $T_DOCHALLAN where `ERPORDERNO`='$the_actual_ord_id'";
			$resall2 = mysql_query($sqlall2);
			$totall2 = mysql_num_rows($resall2);
			if($totall2>0){
				while($row112=mysql_fetch_assoc($resall2)){
				$ch_uid = $row112["id"];
				$ch_apporderno = $row112["APPORDERNO"] ? trim($row112["APPORDERNO"]) : "";
				$ch_erporderno = $row112["ERPORDERNO"] ? trim($row112["ERPORDERNO"]) : "";
				$ch_erporderdt = $row112["ERPORDERDT"] ? trim($row112["ERPORDERDT"]) : "";
					if($ch_erporderdt!=""){
						$ch_erporderdt_full_date_time = date("jS M Y h:i A",strtotime($ch_erporderdt));
					}else{
						$ch_erporderdt_full_date_time = "";
					}
				$ch_challanno = $row112["CHALLANNO"] ? trim($row112["CHALLANNO"]) : "";
				$ch_challandt = $row112["CHALLANDT"] ? trim($row112["CHALLANDT"]) : "";
					if($ch_challandt!=""){
						$ch_challandt_full_date_time = date("jS M Y h:i A",strtotime($ch_challandt));
					}else{
						$ch_challandt_full_date_time = "";
					}
				$ch_prod_code = $row112["prod_code"] ? trim($row112["prod_code"]) : "";
				$ch_dns_prod_code = $row112["dns_prod_code"] ? trim($row112["dns_prod_code"]) : "";
				$ch_prod_display_name = $row112["prod_display_name"] ? trim($row112["prod_display_name"]) : "";
				$ch_qty = $row112["QTY"] ? trim($row112["QTY"]) : "";
				$ch_challanqty = $row112["CHALLANQTY"] ? trim($row112["CHALLANQTY"]) : "";
				$ch_customer_code = $row112["customer_code"];
				$ch_dns_customer_code = $row112["dns_customer_code"];
				$ch_truckno = $row112["TRUCKNO"] ? trim($row112["TRUCKNO"]) : "";
				$ch_driverno = $row112["DRIVERNO"] ? trim($row112["DRIVERNO"]) : "";
				$ch_ischlnimp = "";

				$is_confirmed_challan_material_received = $row112["is_confirmed_challan_material_received"] ? trim($row112["is_confirmed_challan_material_received"]) : "";
				$challan_quantity_checking = $row112["challan_quantity_checking"] ? trim($row112["challan_quantity_checking"]) : "";
				$ch_quantity_no_of_bags = $row112["ch_quantity_no_of_bags"] ? trim($row112["ch_quantity_no_of_bags"]) : "";

				$challan_quality_checking = $row112["challan_quality_checking"] ? trim($row112["challan_quality_checking"]) : "";
				$ch_quality_no_of_damaged_bags = $row112["ch_quality_no_of_damaged_bags"] ? trim($row112["ch_quality_no_of_damaged_bags"]) : "";

				$challan_remarks = $row112["challan_remarks"] ? trim($row112["challan_remarks"]) : "";
				$ch_transporter_name = $row112["transporter_name"] ? trim($row112["transporter_name"]) : "";
				$ch_ch_status = $row112["ch_status"] ? trim($row112["ch_status"]) : "Pending";


				$order_challan_data[] = array("ch_uid"=>$ch_uid,"apporderno"=>$ch_apporderno,"erporderno"=>$ch_erporderno,"erporderdt"=>$ch_erporderdt_full_date_time,"challanno"=>$ch_challanno,"challandt"=>$ch_challandt_full_date_time,"prod_code"=>$ch_prod_code,"dns_prod_code"=>$ch_dns_prod_code,"prod_display_name"=>$ch_prod_display_name,"qty"=>$ch_qty,"challanqty"=>$ch_challanqty,"customer_code"=>$customer_code,"dns_customer_code"=>$dns_customer_code,"truckno"=>$ch_truckno,"driverno"=>$ch_driverno,"ischlnimp"=>$ch_ischlnimp,"is_confirmed_challan_material_received"=>$is_confirmed_challan_material_received,"challan_quantity_checking"=>$challan_quantity_checking,"ch_quantity_no_of_bags"=>$ch_quantity_no_of_bags,"challan_quality_checking"=>$challan_quality_checking,"ch_quality_no_of_damaged_bags"=>$ch_quality_no_of_damaged_bags,"challan_remarks"=>$challan_remarks,"transporter_name"=>$ch_transporter_name,"ch_status"=>$ch_ch_status);

				}
			}
		}

$order_data[] = array("erporderno"=>$erporderno,"erporderdt"=>$erporderdt_full_date_time,"customer_code"=>$customer_code,"dns_customer_code"=>$dns_customer_code,"destination_name"=>$destination_name,"status"=>$status,"prod_code"=>$prod_code,"dns_prod_code"=>$dns_prod_code,"prod_display_name"=>$prod_display_name,"qty"=>$qty,"freight"=>$freight,"dump_name"=>$dump,"is_confirmed_material_received"=>$is_confirmed_material_received,"quantity_checking"=>$quantity_checking,"quality_checking"=>$quality_checking,"remarks"=>$remarks,"order_challan_data"=>$order_challan_data);
	}
$res_data = array("process_status"=>"YES","process_message"=>"Success.","order_data"=>$order_data);
}else{
	$res_data = array("process_status"=>"NO","process_message"=>"No offline order data found.");
}
}else{
	$res_data = array("process_status"=>"NO","process_message"=>"The id is mandatory.");
}
echo json_encode($res_data);
mysql_close();
?>

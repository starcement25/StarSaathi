<?php
header('Content-type: text/html; charset=utf-8');
include "star_connection.php";
mysql_set_charset("UTF8");
$T_APPERPDO = "T_APPERPDO_INVOICE";
$T_DOINVOICE = "T_DOINVOICE";
$customer_master="customer_master";
$app_service_track_log="app_service_track_log";
$res_data = array();
$order_data = array();
$order_invoice_data = array();
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$curr_date = date("Y-m-d");
$before_30_day_date = date('Y-m-d',strtotime("-30 days"));

$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
$start_date = $_REQUEST["start_date"] ? addslashes(trim($_REQUEST["start_date"])) : $before_30_day_date;
$end_date = $_REQUEST["end_date"] ? addslashes(trim($_REQUEST["end_date"])) : $curr_date;

$order_date = date("Y-m-d H:i:s");

if($the_id!=""){
	
	if($start_date!="" && $end_date!=""){
		$date_qry = " and `order_date` between '".$start_date." ".$frm_hrs."' and '".$end_date." ".$to_hrs."'";
	}else{
		$date_qry = "";
	}
	
$sqlall = "select * from $T_APPERPDO where `customer_code`='$the_id' $date_qry order by `order_date` desc";
$resall = mysql_query($sqlall);
$totall = mysql_num_rows($resall);
if($totall>0){
	while($row11=mysql_fetch_assoc($resall)){
		$apporderno = $row11["APPORDERNO"] ? trim($row11["APPORDERNO"]) : "";
		$erporderno = $row11["ERPORDERNO"] ? trim($row11["ERPORDERNO"]) : "";
		$erporderdt = $row11["ERPORDERDT"];
		$isdoimp = "";
		$order_for = $row11["order_for"] ? trim($row11["order_for"]) : "";
		if($order_for!=""){
			if( strpos($order_for,",") !== false ) {
				$ofrarr = array();
				$ofrarr = explode(",",$order_for);
				if(count($ofrarr)>0){
					$order_for = $ofrarr[0];
				}
			}
		}
		$customer_code = $row11["customer_code"];
		$dns_customer_code = $row11["dns_customer_code"];
		$status = $row11["STATUS"];
		
		$order_date = $row11["order_date"] ? trim($row11["order_date"]) : "";
		if($order_date!=""){
			$order_full_date_time = date("jS M Y h:i A",strtotime($order_date));
		}else{
			$order_full_date_time = "";
		}
		
		$prod_code = $row11["prod_code"];
		$dns_prod_code = $row11["dns_prod_code"];
		$prod_display_name = $row11["prod_display_name"];
		
		if($status=='DO approved')
		{
			$qty =  $row11["sale_order_qty"];
		}
		else
		{
			$qty = $row11["QTY"];
		}
		$freight = $row11["freight"] ? trim($row11["freight"]) : "";
		$destination_address = $row11["destination_address"] ? trim($row11["destination_address"]) : "";
		
		$is_confirmed_material_received = $row11["is_confirmed_material_received"] ? trim($row11["is_confirmed_material_received"]) : "";
		$quantity_checking = $row11["quantity_checking"] ? trim($row11["quantity_checking"]) : "";
		$quality_checking = $row11["quality_checking"] ? trim($row11["quality_checking"]) : "";
		$remarks = $row11["remarks"] ? trim($row11["remarks"]) : "";
		
		//$the_actual_ord_id = $apporderno ? addslashes($apporderno) : addslashes($erporderno);
		$the_actual_ord_id = $erporderno ? addslashes($erporderno) : addslashes($apporderno);
		$order_invoice_data = array();
		if($the_actual_ord_id!=""){
			$sqlall2 = "select * from $T_DOINVOICE where (`APPORDERNO`='$the_actual_ord_id' or `ERPORDERNO`='$the_actual_ord_id')";
			$resall2 = mysql_query($sqlall2);
			$totall2 = mysql_num_rows($resall2);			
			if($totall2>0){
				while($row1=mysql_fetch_assoc($resall2)){
				$ch_uid = $row1["id"];
				$apporder_no_chtl = $row1["APPORDERNO"];
				$erporder_no_chtl = $row1["ERPORDERNO"];
				$challanno_chtl = $row1["CHALLANNO"];
				$INVNO= $row1["INVNO"];
				$INVDT= $row1["INVDT"];
				$INVDT=date('Y-m-d',strtotime($INVDT));	
				$prod_display_name_chtl= $row1["prod_display_name"];
				$INVQTY= $row1["INVQTY"];
				$TRUCKNO = $row1["TRUCKNO"];
				$destination = $row1["destination"];
				$customer_code = $row1["customer_code"];	
				
		
				$order_invoice_data[] = array("ch_uid"=>$ch_uid,"apporderno"=>$apporder_no_chtl,"erporderno"=>$erporder_no_chtl,"challanno"=>$challanno_chtl,"invno"=>$INVNO,"invdt"=>$INVDT,"prod_display_name"=>$prod_display_name_chtl,"invqty"=>$INVQTY,"customer_code"=>$customer_code,"truckno"=>$TRUCKNO,"destination"=>$destination);
				
				}
			}			
		}
$order_data[] = array("apporderno"=>$apporderno,"erporderno"=>$erporderno,"erporderdt"=>$order_date,"isdoimp"=>$isdoimp,"order_for"=>$order_for,"customer_code"=>$customer_code,"dns_customer_code"=>$dns_customer_code,"status"=>$status,"prod_code"=>$prod_code,"dns_prod_code"=>$dns_prod_code,"prod_display_name"=>$prod_display_name,"qty"=>$qty,"order_full_date_time"=>$order_full_date_time,"freight"=>$freight,"destination_address"=>$destination_address,"is_confirmed_material_received"=>$is_confirmed_material_received,"quantity_checking"=>$quantity_checking,"quality_checking"=>$quality_checking,"remarks"=>$remarks,"order_invoice_data"=>$order_invoice_data);
	}
$res_data = array("process_status"=>"YES","process_message"=>"Success.","order_data"=>$order_data);
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"No order data found.");
}
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"The id is mandatory.");
}	
echo json_encode($res_data);
mysql_close();
?>
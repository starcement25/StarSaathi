<?php
//echo"<pre>";print_r('44');die;
header('Content-type: text/html; charset=utf-8');
include "star_connection.php";
mysql_set_charset("UTF8");
$T_APPERPDO = "T_APPERPDO";
$T_DOINVOICE = "T_DOINVOICE";
$res_data = array();
$order_data = array();
$order_challan_data = array();
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
if($the_id!=""){
$sqlall = "select * from $T_APPERPDO where `dns_customer_code`='$the_id' order by `order_date` desc";

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
		$qty = $row11["QTY"];
		
		$the_actual_ord_id = $apporderno ? addslashes($apporderno) : addslashes($erporderno);
		$order_challan_data = array();
		if($the_actual_ord_id!=""){
			$sqlall2 = "select * from $T_DOINVOICE where (`APPORDERNO`='$the_actual_ord_id' or `ERPORDERNO`='$the_actual_ord_id')";
			$resall2 = mysql_query($sqlall2);
			$totall2 = mysql_num_rows($resall2);			
			if($totall2>0){
				while($row112=mysql_fetch_assoc($resall2)){
				$inv_apporderno = $row112["APPORDERNO"] ? trim($row112["APPORDERNO"]) : "";
				$inv_erporderno = $row112["ERPORDERNO"] ? trim($row112["ERPORDERNO"]) : "";
				//$ch_erporderdt = $row112["ERPORDERDT"] ? trim($row112["ERPORDERDT"]) : "";
				$challan_no = $row112["CHALLANNO"] ? trim($row112["CHALLANNO"]) : "";	
				$inv_no = $row112["INVNO"] ? trim($row112["INVNO"]) : "";
				$inv_date = $row112["INVDT"] ? trim($row112["INVDT"]) : "";
				$inv_date=date('Y-m-d',strtotime($inv_date));	
				//$ch_challandt=substr($ch_challandt,0,10);
				//$ch_challandt = date("jS M Y h:i A",strtotime($ch_challandt));
				$prod_display_name = $row112["prod_display_name"] ? trim($row112["prod_display_name"]) : "";
				$inv_qty = $row112["INVQTY"] ? trim($row112["INVQTY"]) : "";
				$customer_code = $row112["customer_code"];
				$truckno = $row112["TRUCKNO"] ? trim($row112["TRUCKNO"]) : "";
				$ischlnimp = "";
				$order_challan_data[] = array("apporderno"=>$inv_apporderno,"erporderno"=>$inv_erporderno,"challanno"=>$challan_no,"invno"=>$inv_no,"invdt"=>$inv_date,"prod_display_name"=>$prod_display_name,"invqty"=>$inv_qty,"customer_code"=>$customer_code,"truckno"=>$truckno,"ischlnimp"=>$ch_ischlnimp);
				
				}
			}			
		}
$order_data[] = array("apporderno"=>$apporderno,"erporderno"=>$erporderno,"erporderdt"=>$erporderdt,"isdoimp"=>$isdoimp,"order_for"=>$order_for,"customer_code"=>$customer_code,"dns_customer_code"=>$dns_customer_code,"status"=>$status,"prod_code"=>$prod_code,"dns_prod_code"=>$dns_prod_code,"prod_display_name"=>$prod_display_name,"qty"=>$qty,"order_full_date_time"=>$order_full_date_time,"order_challan_data"=>$order_challan_data);

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
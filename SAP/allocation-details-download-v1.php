<?php
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$customer_master = "customer_master";
$allocation_details  = "allocation_details";
$dispatched_order_data = array();
$allocation_details_data= array();
$curr_date_time  = date("Y-m-d H:i:s");
$order_item_arr = array();
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$curr_date = date("Y-m-d");
$before_30_day_date = date('Y-m-d',strtotime("-30 days"));
$the_customer_code = $_REQUEST["customer_code"] ? addslashes(trim($_REQUEST["customer_code"])) : "";
/*$the_status = $_REQUEST["the_status"] ? addslashes(trim($_REQUEST["the_status"])) : "";
$start_date = $_REQUEST["start_date"] ? addslashes(trim($_REQUEST["start_date"])) : $before_30_day_date;
$end_date = $_REQUEST["end_date"] ? addslashes(trim($_REQUEST["end_date"])) : $curr_date;
$page_no = $_REQUEST["page_no"] ? $_REQUEST["page_no"] : 1;
$limit = 10;
$start_from = (($page_no-1)*$limit);*/
if($the_customer_code==""){
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.","dispatched_order_data"=>$dispatched_order_data);
}else{
$sql_cust = "SELECT `customer_id` FROM $customer_master where `customer_id`='".$the_customer_code."'";
$res_cust = mysql_query($sql_cust);
$tot_res_cust = mysql_num_rows($res_cust);
if($tot_res_cust>0){
	
	$row_cust=mysql_fetch_array($res_cust);
	$customer_id=$row_cust['customer_id'];
	
	$allocation_qty="0";
	$sqlallocation = "select dns_prod_code,prod_desc,SUBSTRING(date_and_time,1,10) AS allocation_date,SUM(allocation_qty) AS allocation_qty,dispatch_qty from $allocation_details where `customer_id`='$customer_id' GROUP BY SUBSTRING(date_and_time,1,10),dns_prod_code";
	mysql_query("SET SESSION sql_mode = 'TRADITIONAL'");
	$resallocation = mysql_query($sqlallocation);
	$totallocation = mysql_num_rows($resallocation);			
	if($totallocation>0){
		$available_qty=0;
		while($rowallocation=mysql_fetch_assoc($resallocation)){
		$dns_prod_code = $rowallocation["dns_prod_code"] ? trim($rowallocation["dns_prod_code"]) : "";
		$prod_desc = $rowallocation["prod_desc"] ? trim($rowallocation["prod_desc"]) : "";	
		$allocation_date = $rowallocation["allocation_date"] ? trim($rowallocation["allocation_date"]) : "";		
		$allocation_qty = $rowallocation["allocation_qty"] ? trim($rowallocation["allocation_qty"]) : "0";
		$dispatch_qty = $rowallocation["dispatch_qty"] ? trim($rowallocation["dispatch_qty"]) : "0";	
			
			$available_qty=($dispatch_qty-$allocation_qty);
			
		$allocation_details_data[] =array("dns_prod_code"=>$dns_prod_code,"prod_desc"=>$prod_desc,"allocation_date"=>$allocation_date,"available_qty"=>$available_qty);	
		}
		$res_data = array("process_status"=>"YES","process_message"=>"Success.","allocation_details_data"=>$allocation_details_data);
	}
	
else{
$res_data = array("process_status"=>"NO","process_message"=>"No Allocation details found.");
}
	}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Dealer record not found.","allocation_details_data"=>$allocation_details_data);
}
}
echo json_encode($res_data);
if($conn!=""){
mysql_close($conn);
}
?>
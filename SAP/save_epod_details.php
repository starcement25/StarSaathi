<?php
include "star_connection.php";
include "function-sfa.php";
$epod_details  = "epod_details";
$app_service_track_log="app_service_track_log";
$customer_master = "customer_master";
$res_data=array();

function get_broker_name_from_id($cust_id){
	$broker_master = "broker_master";
	$custname = "";
	$cust_id = $cust_id ? addslashes(trim($cust_id)) : "";
	if($cust_id!=''){
		$sqls = "select `broker_name` from $broker_master where `dns_broker_id`='$cust_id'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if($totress>0){
			$rows = mysql_fetch_assoc($ress);
			$custname = $rows["broker_name"] ? trim($rows["broker_name"]) : "";
		}
	}
	return $custname;
}
function get_customer_name_from_id($cust_id){
	$customer_master = "customer_master";
	$custname = "";
	$cust_id = $cust_id ? addslashes(trim($cust_id)) : "";
	if($cust_id!=''){
		$sqls = "select `customer_name` from $customer_master where `customer_code`='$cust_id'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if($totress>0){
			$rows = mysql_fetch_assoc($ress);
			$custname = $rows["customer_name"] ? trim($rows["customer_name"]) : "";
		}
	}
	return $custname;
}


function show_customer_code_from_dns_customer_code($the_dns_customer_code){
$the_cust_code = "";
$customer_master = "customer_master";
if($the_dns_customer_code!=""){
$sql1 = "select `customer_code` from $customer_master where `dns_customer_code`='$the_dns_customer_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_cust_code = $row1["customer_code"] ? addslashes(trim($row1["customer_code"])) : "";
	}
}
return $the_cust_code;
}
function show_dns_customer_from_customer_code_code($the_customer_code){
$the_cust_code = "";
$customer_master = "customer_master";

if($the_customer_code!=""){
$sql1 = "select `dns_customer_code` from $customer_master where `customer_code`='$the_customer_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_cust_code = $row1["dns_customer_code"] ? addslashes(trim($row1["dns_customer_code"])) : "";
	}
}
return $the_cust_code;
}
function show_product_data_from_prod_code($the_prod_code){
$product_dtls = array("dns_prod_code"=>"","prod_desc"=>"");
$product_master = "product_master";
if($the_prod_code!=""){
$sql1 = "select `dns_prod_code`,`prod_desc` from $product_master where `prod_code`='$the_prod_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$dns_prod_code = $row1["dns_prod_code"] ? addslashes(trim($row1["dns_prod_code"])) : "";
		$prod_desc = $row1["prod_desc"] ? addslashes(trim($row1["prod_desc"])) : "";
		$product_dtls = array("dns_prod_code"=>$dns_prod_code,"prod_desc"=>$prod_desc);
	}
}
return $product_dtls;
}

$customer_id = $_REQUEST["customer_id"] ? addslashes(trim($_REQUEST["customer_id"])) : "";
$epod_date_time = date("Y-m-d H:i:s");

$epod_data = $_REQUEST["epod_data"] ? $_REQUEST["epod_data"] : array();
if(count($epod_data)>0){

foreach($epod_data as $ki=>$epod_data_val){
//$apporderno = $order_data_val["apporderno"] ? addslashes(trim($order_data_val["apporderno"])) : "";
$date_and_time  = $epod_data_val["date_and_time"] ? addslashes(trim($epod_data_val["date_and_time"])) : "";
$challan_no= $epod_data_val["challan_no"] ? addslashes(trim($epod_data_val["challan_no"])) : "";	
$challan_date  = $epod_data_val["challan_date"] ? addslashes(trim($epod_data_val["challan_date"])) : "";
$is_delivered  = $epod_data_val["is_delivered"] ? addslashes(trim($epod_data_val["is_delivered"])) : "";	
$total_order  = $epod_data_val["total_order"] ? addslashes(trim($epod_data_val["total_order"])) : "";	
$dispatched_order  = $epod_data_val["dispatched_order"] ? addslashes(trim($epod_data_val["dispatched_order"])) : "";
$received_order = $epod_data_val["received_order"] ? addslashes(trim($epod_data_val["received_order"])) : "";
$pending_order = $epod_data_val["pending_order"] ? addslashes(trim($epod_data_val["pending_order"])) : "";
$delivery_qty = $epod_data_val["delivery_qty"] ? addslashes(trim($epod_data_val["delivery_qty"])) : "";	
$remarks = $epod_data_val["remarks"] ? addslashes(trim($epod_data_val["remarks"])) : "";		
date_default_timezone_set('Asia/Kolkata'); // Set the timezone to IST

$sqlin = "insert into $epod_details (`date_and_time`,`customer_id`,`challan_no`,`challan_date`,`is_delivered`,`total_order`,`dispatched_order`,`received_order`,`pending_order`,`delivery_qty`,`remarks`) values('$date_and_time','$customer_id','$challan_no','$challan_date','$is_delivered','$total_order','$dispatched_order','$received_order','$pending_order','$delivery_qty','$remarks')";
$resin = mysql_query($sqlin);
/*if($resin){
$created_last_id = mysql_insert_id();
$new_order_id_val = str_pad($created_last_id, 7, "0", STR_PAD_LEFT);
$apporderno = "POP".$new_order_id_val;
${'apporderno'.$cntslno}=$apporderno;
$sqlupd1 = "update $t_apperpdo_pop set `APPORDERNO`='$apporderno' where `id`='$created_last_id'";
$resupd1 = mysql_query($sqlupd1);
$cntslno++;
}*/
}


	
$res_data = array("process_status"=>"YES","process_message"=>"epod details successfully saved.");

}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Failed to save epod details.");
}
echo json_encode($res_data);
//mysql_close();
//For SFA INSERT
mysql_close();
?>

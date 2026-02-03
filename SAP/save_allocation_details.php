<?php
include "star_connection.php";
include "function-sfa.php";
$allocation_details  = "allocation_details";
$app_service_track_log="app_service_track_log";
$customer_master = "customer_master";
$product_master = "product_master";
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
$allocation_date_time = date("Y-m-d H:i:s");
$allocation_data = $_REQUEST["allocation_data"] ? $_REQUEST["allocation_data"] : array();
if(count($allocation_data)>0){

foreach($allocation_data as $ki=>$allocation_data_val){
//$apporderno = $order_data_val["apporderno"] ? addslashes(trim($order_data_val["apporderno"])) : "";
$order_id  = $allocation_data_val["order_id"] ? addslashes(trim($allocation_data_val["order_id"])) : "";
$APPORDERNO= $allocation_data_val["APPORDERNO"] ? addslashes(trim($allocation_data_val["APPORDERNO"])) : "";	
$customer_id  = $allocation_data_val["customer_id"] ? addslashes(trim($allocation_data_val["customer_id"])) : "";	
$sub_dealer_id  = $allocation_data_val["sub_dealer_id"] ? addslashes(trim($allocation_data_val["sub_dealer_id"])) : "";	
$challan_no  = $allocation_data_val["challan_no"] ? addslashes(trim($allocation_data_val["challan_no"])) : "";
$dns_prod_code = $allocation_data_val["dns_prod_code"] ? addslashes(trim($allocation_data_val["dns_prod_code"])) : "";
$prod_desc = $allocation_data_val["prod_desc"] ? addslashes(trim($allocation_data_val["prod_desc"])) : "";
$despatch_qty = $allocation_data_val["despatch_qty"] ? addslashes(trim($allocation_data_val["despatch_qty"])) : "";	
$dispatch_date = $allocation_data_val["dispatch_date"] ? addslashes(trim($allocation_data_val["dispatch_date"])) : "";		
$qty = $allocation_data_val["qty"] ? addslashes(trim($allocation_data_val["qty"])) : "";
date_default_timezone_set('Asia/Kolkata'); // Set the timezone to IST

$sqlin = "insert into $allocation_details (`date_and_time`,`order_id`,`APPORDERNO`,`challan_no`,`customer_id`,`sub_dealer_id`,`dns_prod_code`,`prod_desc`,`dispatch_date`,`dispatch_qty`,`allocation_qty`) values('$allocation_date_time','$order_id','$APPORDERNO','$challan_no','$customer_id','$sub_dealer_id','$dns_prod_code','$prod_desc','$dispatch_date','$despatch_qty','$qty')";
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

$customer_id  = $allocation_data_val["customer_id"] ? addslashes(trim($allocation_data_val["customer_id"])) : "";
	$dns_prod_code = $allocation_data_val["dns_prod_code"] ? addslashes(trim($allocation_data_val["dns_prod_code"])) : "";

	// get latest (sort on date_and_time) allocation details for the customer_id and dns_prod_code
	$sql = "select * from $allocation_details where customer_id='$customer_id' and dns_prod_code='$dns_prod_code' order by date_and_time desc";
	//$res = mysql_query($sql);
	//$totres = mysql_num_rows($res);

	// echo "SQL: $sql\n";

	// get the sum of allocation_qty
	$sum_allocation_qty = 0;
	if ($totres > 0) {
		while ($row = mysql_fetch_assoc($res)) {
			$sum_allocation_qty += $row["allocation_qty"];
		}
	}
	// echo "sum_allocation_qty: $sum_allocation_qty\n";
	// get the dispatch_qty of the latest allocation
	/*$available_allocation_qty = 0;
	$dispatch_qty = 0;
	$sql = "select dispatch_qty from $allocation_details where customer_id='$customer_id' and dns_prod_code='$dns_prod_code' order by date_and_time desc limit 1";
	$res = mysql_query($sql);
	$totres = mysql_num_rows($res);
	if ($totres > 0) {
		$row = mysql_fetch_assoc($res);
		$dispatch_qty = $row["dispatch_qty"];
	}*/
	// echo "dispatch_qty: $dispatch_qty\n";
	// get available_allocation_qty
	//$available_allocation_qty = $dispatch_qty - $sum_allocation_qty;

$res_data = array("process_status"=>"YES","process_message"=>"Allocation successfully saved.");

}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Failed to save Allocation.");
}
echo json_encode($res_data);
//mysql_close();
//For SFA INSERT
mysql_close();
?>

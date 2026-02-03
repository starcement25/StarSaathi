<?php

include "star_connection.php";
include "function-sfa.php";
$allocation_details  = "allocation_details_invoicewise";
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
//echo"<pre>";print_r($allocation_data);die;
if(count($allocation_data)>0){

foreach($allocation_data as $ki=>$allocation_data_val){
//$apporderno = $order_data_val["apporderno"] ? addslashes(trim($order_data_val["apporderno"])) : "";
$order_id  = $allocation_data_val["order_id"] ? addslashes(trim($allocation_data_val["order_id"])) : "";
$APPORDERNO= $allocation_data_val["APPORDERNO"] ? addslashes(trim($allocation_data_val["APPORDERNO"])) : "";
$customer_id  = $allocation_data_val["customer_id"] ? addslashes(trim($allocation_data_val["customer_id"])) : "";
$sub_dealer_id  = $allocation_data_val["sub_dealer_id"] ? addslashes(trim($allocation_data_val["sub_dealer_id"])) : "";
$inv_no  = $allocation_data_val["inv_no"] ? addslashes(trim($allocation_data_val["inv_no"])) : "";
//$dns_prod_code = $allocation_data_val["dns_prod_code"] ? addslashes(trim($allocation_data_val["dns_prod_code"])) : "";
$prod_desc = $allocation_data_val["prod_desc"] ? addslashes(trim($allocation_data_val["prod_desc"])) : "";
$inv_qty = $allocation_data_val["inv_qty"] ? addslashes(trim($allocation_data_val["inv_qty"])) : "";
$inv_date = $allocation_data_val["inv_date"] ? addslashes(trim($allocation_data_val["inv_date"])) : "";
$qty = $allocation_data_val["qty"] ? addslashes(trim($allocation_data_val["qty"])) : "";
//$offline = $allocation_data_val["offline"] ? addslashes(trim($allocation_data_val["offline"])) : "0";
date_default_timezone_set('Asia/Kolkata'); // Set the timezone to IST

// check Retailer lifting allocation restriction as per rssd appointment date sk100725
$customer_master = "customer_master";
if($sub_dealer_id!=''){
 $customer_id_check=$sub_dealer_id;
}else{
 $customer_id_check=$customer_id;	
}
$T_DOINVOICE = "T_DOINVOICE";
	$sqls = "select `appointment_date` from $customer_master where `customer_id`='$customer_id_check'";

		$ress = mysql_query($sqls);

		$totress = mysql_num_rows($ress);
		if($totress>0){

			$rows = mysql_fetch_assoc($ress);

			$appointment_date = $rows["appointment_date"] ? trim($rows["appointment_date"]) : "";

		}
		//echo "$customer_id Raw appointment_date from DB: '$appointment_date'<br>";
		$sqls = "select `INVDT` from $T_DOINVOICE where `APPORDERNO`='$APPORDERNO'";

		$ress = mysql_query($sqls);

		$totress = mysql_num_rows($ress);
		if($totress>0){

			$rows = mysql_fetch_assoc($ress);

			$INVDT = $rows["INVDT"] ? trim($rows["INVDT"]) : "";

		}
		// create DateTime object from d.m.Y format
		$dateObj = DateTime::createFromFormat('d.m.Y', $appointment_date);

		if ($dateObj !== false) {
			$formattedDate = $dateObj->format('Ymd'); // e.g., "20220625"

			if ($formattedDate <= $INVDT) {
				//echo "Appointment date $appointment_date (=$formattedDate) is before or equal to invoice date $INVDT";
			} else {
				//echo "Appointment date $appointment_date (=$formattedDate) is after invoice date $INVDT";
				$res_data = array("process_status"=>"NO","process_message"=>"The RSAR customer appointment date is after invoice date");
			echo json_encode($res_data);
			die;
			}
		} else {
			//echo "Invalid appointment date format: $appointment_date";
			
		}
		//echo"<pre>";print_r('ss');die;
 //end sk100725


///check offline order
$table = "T_APPERPDO_OFFLINE";
$column = "ERPORDERNO";
$data_check = mysql_query("SELECT COUNT(*) as total FROM `$table` WHERE `$column` = '$APPORDERNO' ");
$row = mysql_fetch_assoc($data_check);
//echo"<pre>";print_r($row);die;

if ($row['total'] > 0) {
	$offline = 'Offline';
}else {
	$offline = 'Online';
}
//echo"<pre>";print_r($offline);die;
///end check offline order

	$sqlchk = "select  order_id from $allocation_details where order_id='$order_id' and delete_at='0' and prod_desc='$prod_desc'";
	$reschk = mysql_query($sqlchk);
	$totreschk = mysql_num_rows($reschk);
	if ($totreschk==0) {

		$sqlin = "insert into $allocation_details (`date_and_time`,`order_id`,`APPORDERNO`,`inv_no`,`customer_id`,`sub_dealer_id`,`prod_desc`,`inv_date`,`inv_qty`,`allocation_qty`,`is_offline`) values('$allocation_date_time','$order_id','$APPORDERNO','$inv_no','$customer_id','$sub_dealer_id','$prod_desc','$inv_date','$inv_qty','$qty','$offline')";
		// echo $sqlin;
		//echo"<pre>";print_r($sqlin);die;
		$resin = mysql_query($sqlin);
	 }
	// else{
	// 	$res_data = array("error_message"=>"Failed to save Allocation Prodct name alocated already.");
	// 	echo json_encode($res_data);
	// }

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

//$customer_id  = $allocation_data_val["customer_id"] ? addslashes(trim($allocation_data_val["customer_id"])) : "";
	//$dns_prod_code = $allocation_data_val["dns_prod_code"] ? addslashes(trim($allocation_data_val["dns_prod_code"])) : "";

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

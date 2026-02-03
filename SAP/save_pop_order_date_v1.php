<?php
include "star_connection.php";
include "function-sfa.php";
$t_main_order_pop = "T_MAIN_ORDER_POP";
$app_service_track_log="app_service_track_log";
$t_apperpdo_pop = "T_ORDER_POP";
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
function fetch_emails_by_destination_code($the_des_code){
$email_str = "";
$email_array = array();
$email_array["branch_name"] = "";
$email_array["email"][] = "starsaathi@gmail.com";
$branch_destination_freight = "branch_destination_freight";
$branch_master = "branch_master";
$the_des_code = $the_des_code ? addslashes(trim($the_des_code)) : "";
if($the_des_code!=""){
$sql1 = "select `branch_code` from $branch_destination_freight where `destination_code`='$the_des_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_branch_code = $row1["branch_code"] ? addslashes(trim($row1["branch_code"])) : "";
		$sql2 = "select `branch_name`,`branch_email_id` from $branch_master where `branch_code`='$the_branch_code'";	
		$res2 = mysql_query($sql2);
		$totres2 = mysql_num_rows($res2);
		if($totres2>0){
			$row2 = mysql_fetch_assoc($res2);
			$the_branch_name = trim($row2["branch_name"]);
			if($the_branch_name!=""){
				$email_array["branch_name"] = $the_branch_name;
			}
			$the_branch_email_id = trim($row2["branch_email_id"]);
			if($the_branch_email_id!=""){
				$email_array["email"][] = $the_branch_email_id;
			}
		}
	}
}
return $email_array;
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
function show_dns_destination_code($the_destination_code){
	$dns_dest_code='';
if($the_destination_code!=""){
$sqld = "select `dns_destination_code` from destination_master where destination_code='$the_destination_code'";	
$resd = mysql_query($sqld);
$totresd = mysql_num_rows($resd);
	if($totresd>0){
		$rowd = mysql_fetch_assoc($resd);
		$dns_dest_code = $rowd["dns_destination_code"] ? addslashes(trim($rowd["dns_destination_code"])) : "";
	}
}
return $dns_dest_code;
}

function show_customer_data_by_customer_code($the_customer_code){
$customer_data = array("sts"=>"NO","dns_customer_code"=>"","customer_id"=>"","customer_name"=>"","phone_no"=>"","email"=>"","region"=>"");
$customer_master = "customer_master";
if($the_customer_code!=""){
$sql1 = "select `dns_customer_code`,`customer_id`,`customer_name`,`phone_no`,`email`,`region` from $customer_master where `customer_code`='$the_customer_code'";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_dns_customer_code = $row1["dns_customer_code"] ? addslashes(trim($row1["dns_customer_code"])) : "";
		$the_customer_id = $row1["customer_id"] ? addslashes(trim($row1["customer_id"])) : "";
		$the_customer_name = $row1["customer_name"] ? addslashes(trim($row1["customer_name"])) : "";
		$the_phone_no = $row1["phone_no"] ? addslashes(trim($row1["phone_no"])) : "";
		$the_email = $row1["email"] ? addslashes(trim($row1["email"])) : "";
		$the_region = $row1["region"] ? addslashes(trim($row1["region"])) : "";
		$customer_data = array("sts"=>"YES","dns_customer_code"=>$the_dns_customer_code,"customer_id"=>$the_customer_id,"customer_name"=>$the_customer_name,"phone_no"=>$the_phone_no,"email"=>$the_email,"region"=>$the_region);
	}
}
return $customer_data;
}

function get_product_price($dns_prod_code){
$pop_product_master = "pop_product_master";
$product_data = array("sts"=>"NO","product_rate"=>0,"gst_rate"=>0);
if($dns_prod_code!=""){

$sql_prod = "SELECT `price_per_piece`,`GST_rate` FROM $pop_product_master WHERE status='Y' AND dns_prod_code='".$dns_prod_code."'";
$res_prod = mysql_query($sql_prod);
$tot_res_prod = mysql_num_rows($res_prod);
if($tot_res_prod>0){
$row_prod = mysql_fetch_assoc($res_prod);
$price_per_piece = $row_prod['price_per_piece'] ? trim($row_prod['price_per_piece']) : 0;
$GST_rate = $row_prod['GST_rate'] ? trim($row_prod['GST_rate']) : 0;
$product_data = array("sts"=>"YES","product_rate"=>$price_per_piece,"gst_rate"=>$GST_rate);
}

}

return $product_data;
}

$customer_code = $_REQUEST["customer_code"] ? addslashes(trim($_REQUEST["customer_code"])) : "";

$user_type = $_REQUEST["user_type"] ? strtoupper($_REQUEST["user_type"]) : "";

$payment_by = $_REQUEST["payment_by"] ? addslashes(trim($_REQUEST["payment_by"])) : "";

$ord_dns_customer_code = $_REQUEST["dns_customer_code"] ? addslashes(trim($_REQUEST["dns_customer_code"])) : "";
$ord_address = $_REQUEST["address"] ? addslashes(trim($_REQUEST["address"])) : "";
$ord_pin = $_REQUEST["pin"] ? addslashes(trim($_REQUEST["pin"])) : "";
$ord_remarks = $_REQUEST["remarks"] ? addslashes(trim($_REQUEST["remarks"])) : "";
$ord_printed_address_pin = $_REQUEST["printed_address_pin"] ? addslashes(trim($_REQUEST["printed_address_pin"])) : "";
$ord_contact_num_printed = $_REQUEST["contact_num_printed"] ? addslashes(trim($_REQUEST["contact_num_printed"])) : "";
$order_date_time = date("Y-m-d H:i:s");
$order_data = $_REQUEST["order_data"] ? $_REQUEST["order_data"] : array();
if($payment_by==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please choose payment by option.");
}else{

$ord_customer_sap_code = "";
$ord_customer_name = "";
$ord_phone_no = "";
$ord_email = "";
$ord_region = "";
$customer_data_arr = show_customer_data_by_customer_code($customer_code);
if($customer_data_arr["sts"]=="YES"){
$ord_dns_customer_code = $customer_data_arr["dns_customer_code"];
$ord_customer_sap_code = $customer_data_arr["customer_id"];
$ord_customer_name = $customer_data_arr["customer_name"];
$ord_phone_no = $customer_data_arr["phone_no"];
$ord_email = $customer_data_arr["email"];
$ord_region = $customer_data_arr["region"];
}

if($ord_region==""){
	$res_data = array("process_status"=>"NO","process_message"=>"Unknown region set.");
}else{

if(count($order_data)>0){

$sql_moin = "insert into $t_main_order_pop (`dns_customer_code`,`customer_code`,`customer_sap_code`,`customer_name`,`customer_region`,`mobile`,`email`,`address`,`pin`,`remarks`,`printed_address_pin`,`contact_num_printed`,`payment_by`,`order_datetime`) values ('$ord_dns_customer_code','$customer_code','$ord_customer_sap_code','$ord_customer_name','$ord_region','$ord_phone_no','$ord_email','$ord_address','$ord_pin','$ord_remarks','$ord_printed_address_pin','$ord_contact_num_printed','$payment_by','$order_date_time')";
$res_moin = mysql_query($sql_moin);
if($res_moin){
$the_order_id = mysql_insert_id();
$ord_total_amount = 0;
$cntslno=1;
foreach($order_data as $ki=>$order_data_val){
//$apporderno = $order_data_val["apporderno"] ? addslashes(trim($order_data_val["apporderno"])) : "";
$customer_code = $order_data_val["customer_code"] ? addslashes(trim($order_data_val["customer_code"])) : "";
$dns_customer_code = $order_data_val["dns_customer_code"] ? addslashes(trim($order_data_val["dns_customer_code"])) : "";
$dns_prod_code = $order_data_val["dns_prod_code"] ? addslashes(trim($order_data_val["dns_prod_code"])) : "";
$prod_desc = $order_data_val["prod_desc"] ? addslashes(trim($order_data_val["prod_desc"])) : "";
$address = $order_data_val["address"] ? addslashes(trim($order_data_val["address"])) : "";	
$qty = $order_data_val["qty"] ? addslashes(trim($order_data_val["qty"])) : "";
$pin = $order_data_val["pin"] ? addslashes(trim($order_data_val["pin"])) : "";
$remarks = $order_data_val["remarks"] ? addslashes(trim($order_data_val["remarks"])) : "";
$printed_address_pin = $order_data_val["printed_address_pin"] ? addslashes(trim($order_data_val["printed_address_pin"])) : "";
$contact_num_printed = $order_data_val["contact_num_printed"] ? addslashes(trim($order_data_val["contact_num_printed"])) : "";	

$ord_product_rate = 0;
$ord_gst_rate = 0;
$ord_prod_amount = 0;
$ord_gst_amount = 0;
$ord_prod_total_amount = 0;
$product_data_arr = array();
$product_data_arr = get_product_price($dns_prod_code);
if($product_data_arr["sts"]=="YES"){
$ord_product_rate = $product_data_arr["product_rate"];
$ord_gst_rate = $product_data_arr["gst_rate"];

$ord_prod_amount = ($ord_product_rate * $qty);
if($ord_gst_rate>0){
$ord_gst_amount = (($ord_gst_rate/100) * $ord_prod_amount);
}

$ord_prod_total_amount = ($ord_prod_amount + $ord_gst_amount);

$ord_total_amount = ($ord_total_amount + $ord_prod_total_amount);
}

date_default_timezone_set('Asia/Kolkata'); // Set the timezone to IST

$sqlin = "insert into $t_apperpdo_pop (`the_order_id`,`order_date`,`customer_code`,`dns_customer_code`,`prod_code`,`dns_prod_code`,`prod_display_name`,`QTY`,`prod_rate`,`prod_amount`,`gst_rate`,`gst_amount`,`prod_total_amount`,`address`,`pin`,`remarks`,`printed_address_pin`,`contact_num_printed`) values('$the_order_id','$order_date_time','$customer_code','$dns_customer_code','','$dns_prod_code','$prod_desc','$qty','$ord_product_rate','$ord_prod_amount','$ord_gst_rate','$ord_gst_amount','$ord_prod_total_amount','$address','$pin','$remarks','$printed_address_pin','$contact_num_printed')";
$resin = mysql_query($sqlin);
if($resin){
$created_last_id = mysql_insert_id();
$new_order_id_val = str_pad($created_last_id, 7, "0", STR_PAD_LEFT);
$apporderno = "POP".$new_order_id_val;
${'apporderno'.$cntslno}=$apporderno;
$sqlupd1 = "update $t_apperpdo_pop set `APPORDERNO`='$apporderno' where `id`='$created_last_id'";
$resupd1 = mysql_query($sqlupd1);
$cntslno++;
}
}


$sql_mo_upd = "update $t_main_order_pop set `amount`='$ord_total_amount' where `order_id`='$the_order_id'";
$res_mo_upd = mysql_query($sql_mo_upd);

$the_payment_url = "";
$region_ck = strtolower($ord_region);
if($region_ck=="ne"){
$the_payment_url = $server_url."ccavenue_pg/make_pop_order_payment_with_ccavenue_ne.php?order_id=".$the_order_id;
}else{
$the_payment_url = $server_url."ccavenue_pg/make_pop_order_payment_with_ccavenue.php?order_id=".$the_order_id;
}

$res_data = array("process_status"=>"YES","process_message"=>"The POP order successfully received.","the_order_id"=>$the_order_id,"the_payment_url"=>$the_payment_url);

}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Failed to save order.");
}


}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Please select product then make order.");
}

}

}
echo json_encode($res_data);
//mysql_close();
//For SFA INSERT
mysql_close();

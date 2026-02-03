<?php
include "star_connection.php";
include "function-sfa.php";
$order_query   = "order_query";
$customer_master = "customer_master";
$product_master = "product_master";
$res_data = array();

function get_broker_name_from_id($cust_id)
{
	$broker_master = "broker_master";
	$custname = "";
	$cust_id = $cust_id ? addslashes(trim($cust_id)) : "";
	if ($cust_id != '') {
		$sqls = "select `broker_name` from $broker_master where `dns_broker_id`='$cust_id'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if ($totress > 0) {
			$rows = mysql_fetch_assoc($ress);
			$custname = $rows["broker_name"] ? trim($rows["broker_name"]) : "";
		}
	}
	return $custname;
}
function get_customer_name_from_id($cust_id)
{
	$customer_master = "customer_master";
	$custname = "";
	$cust_id = $cust_id ? addslashes(trim($cust_id)) : "";
	if ($cust_id != '') {
		$sqls = "select `customer_name` from $customer_master where `customer_code`='$cust_id'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if ($totress > 0) {
			$rows = mysql_fetch_assoc($ress);
			$custname = $rows["customer_name"] ? trim($rows["customer_name"]) : "";
		}
	}
	return $custname;
}


function show_customer_code_from_dns_customer_code($the_dns_customer_code)
{
	$the_cust_code = "";
	$customer_master = "customer_master";
	if ($the_dns_customer_code != "") {
		$sql1 = "select `customer_code` from $customer_master where `dns_customer_code`='$the_dns_customer_code'";
		$res1 = mysql_query($sql1);
		$totres1 = mysql_num_rows($res1);
		if ($totres1 > 0) {
			$row1 = mysql_fetch_assoc($res1);
			$the_cust_code = $row1["customer_code"] ? addslashes(trim($row1["customer_code"])) : "";
		}
	}
	return $the_cust_code;
}
function show_dns_customer_from_customer_code_code($the_customer_code)
{
	$the_cust_code = "";
	$customer_master = "customer_master";

	if ($the_customer_code != "") {
		$sql1 = "select `dns_customer_code` from $customer_master where `customer_code`='$the_customer_code'";
		$res1 = mysql_query($sql1);
		$totres1 = mysql_num_rows($res1);
		if ($totres1 > 0) {
			$row1 = mysql_fetch_assoc($res1);
			$the_cust_code = $row1["dns_customer_code"] ? addslashes(trim($row1["dns_customer_code"])) : "";
		}
	}
	return $the_cust_code;
}
function show_product_data_from_prod_code($the_prod_code)
{
	$product_dtls = array("dns_prod_code" => "", "prod_desc" => "");
	$product_master = "product_master";
	if ($the_prod_code != "") {
		$sql1 = "select `dns_prod_code`,`prod_desc` from $product_master where `prod_code`='$the_prod_code'";
		$res1 = mysql_query($sql1);
		$totres1 = mysql_num_rows($res1);
		if ($totres1 > 0) {
			$row1 = mysql_fetch_assoc($res1);
			$dns_prod_code = $row1["dns_prod_code"] ? addslashes(trim($row1["dns_prod_code"])) : "";
			$prod_desc = $row1["prod_desc"] ? addslashes(trim($row1["prod_desc"])) : "";
			$product_dtls = array("dns_prod_code" => $dns_prod_code, "prod_desc" => $prod_desc);
		}
	}
	return $product_dtls;
}

$customer_id = $_REQUEST["customer_id"] ? addslashes(trim($_REQUEST["customer_id"])) : "";
$order_date_time = date("Y-m-d H:i:s");
$order_query_data = $_REQUEST["order_query_data"] ? $_REQUEST["order_query_data"] : array();
if (count($order_query_data) > 0) {

	foreach ($order_query_data as $ki => $order_query_data_val) {
		//$apporderno = $order_data_val["apporderno"] ? addslashes(trim($order_data_val["apporderno"])) : "";
		$order_id  = $order_query_data_val["order_id"] ? addslashes(trim($order_query_data_val["order_id"])) : "";
		$linked_dealer_code  = $order_query_data_val["linked_dealer_code"] ? addslashes(trim($order_query_data_val["linked_dealer_code"])) : "";
		$dns_prod_code  = $order_query_data_val["dns_prod_code"] ? addslashes(trim($order_query_data_val["dns_prod_code"])) : "";
		$prod_name  = $order_query_data_val["prod_name"] ? addslashes(trim($order_query_data_val["prod_name"])) : "";
		$qty_bags = $order_query_data_val["qty_bags"] ? addslashes(trim($order_query_data_val["qty_bags"])) : "";
		$query_date = $order_query_data_val["query_date"] ? addslashes(trim($order_query_data_val["query_date"])) : "";
		$date_of_lifting = $order_query_data_val["date_of_lifting"] ? addslashes(trim($order_query_data_val["date_of_lifting"])) : "";
		$remarks = $order_query_data_val["remarks"] ? addslashes(trim($order_query_data_val["remarks"])) : "";
		date_default_timezone_set('Asia/Kolkata'); // Set the timezone to IST

		$sqlin = "insert into $order_query (`date_and_time`,`order_id`,`customer_code`,`linked_dealer_code`,`dns_prod_code`,`prod_name`,`qty_bags`,`query_date`, `date_of_lifting`, `remarks`) values('$order_date_time','$order_id','$customer_id','$linked_dealer_code','$dns_prod_code','$prod_name','$qty_bags','$query_date', '$date_of_lifting', '$remarks')";
		$resin = mysql_query($sqlin);
		
		$sqlcust = "select customer_name from $customer_master where customer_id='".$customer_id."'";	
		$rescust = mysql_query($sqlcust);
		$rowcust= mysql_fetch_assoc($rescust);
		$customer_name=$rowcust['customer_name'];
		
		$sqllinkedid = "select `customer_id`,customer_name from $customer_master where `customer_code`='$linked_dealer_code'";
		$resllinkedid = mysql_query($sqllinkedid);
		$totreslinkedid = mysql_num_rows($resllinkedid);
		if ($totreslinkedid > 0) {
			$rowllinkedid = mysql_fetch_assoc($resllinkedid);
			$the_cust_code = $rowllinkedid["customer_id"] ? addslashes(trim($rowllinkedid["customer_id"])) : "";
			$the_cust_name = $rowllinkedid["customer_name"] ? addslashes(trim($rowllinkedid["customer_name"])) : "";
		}
		
		$sqldealeremp="SELECT emp_code FROM customer_route_emp_relation WHERE acedns='Y' AND customer_code='".$the_cust_code."'";
		$rsdealeremp=mysql_query($sqldealeremp);
		$email_hierarchy='';
		while($rowdealeremp=mysql_fetch_array($rsdealeremp))
		{
			$emp_code_db=$rowdealeremp['emp_code'];
			//$employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code_db);
			$employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code_db);
			/*$sqlemailhierarchy="SELECT email,designation FROM employee_master WHERE emp_code IN ('".$emp_code_db."')  AND UPPER(sale_access)='PRIMARY' AND acedns='Y'";*/
			$sqlemailhierarchy="SELECT email,designation FROM employee_master WHERE emp_code IN ('".$employee_upper_hierarchy."')  AND UPPER(sale_access)='PRIMARY' AND acedns='Y'";
			$rsemailhierarchy=mysql_query($sqlemailhierarchy);
			while($rowemailhierarchy=mysql_fetch_array($rsemailhierarchy))
			{
				if($rowemailhierarchy['email']!='')
				{
				$email_hierarchy=$email_hierarchy.$rowemailhierarchy['email'].',';
				}
			}
		}
		$final_email=substr($email_hierarchy,0,-1).','.'abhishekd@forcepower.in,antarabanerjee@starcement.co.in';
		/*if($resin){
$created_last_id = mysql_insert_id();
$new_order_id_val = str_pad($created_last_id, 7, "0", STR_PAD_LEFT);
$apporderno = "POP".$new_order_id_val;
${'apporderno'.$cntslno}=$apporderno;
$sqlupd1 = "update $t_apperpdo_pop set `APPORDERNO`='$apporderno' where `id`='$created_last_id'";
$resupd1 = mysql_query($sqlupd1);
$cntslno++;
}*/
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$subject = "Star Saathi - New Order Enquiry from $customer_name @ $hour:$minute\n";

		//$location_date=$year.$month.$date.$hour.$minute.$second;

		$message.= '<br><b>Order Id: </b> '.$order_id.'<br>
					<b>Linked Dealer: </b> '.$the_cust_name.'<br>
					<b>Product Name: </b> '.$prod_name.'<br>
					<b>Quantity in Bags: </b> '.$qty_bags.'<br>
					<b>Date of Lifting: </b> '.$date_of_lifting.'<br>
					<b>Remarks: </b> '.$remarks.'<br>';
		$send_mail = send_the_mail($final_email,$subject,$message);

	}


	$res_data = array("process_status" => "YES", "process_message" => "Order Query successfully saved.");
} else {
	$res_data = array("process_status" => "NO", "process_message" => "Failed to save Order Query.");
}
echo json_encode($res_data);
//mysql_close();
//For SFA INSERT
mysql_close();
?>
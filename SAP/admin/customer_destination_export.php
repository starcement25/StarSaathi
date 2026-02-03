<?php
ini_set('memory_limit', '9999M');
set_time_limit(0);

ini_set('memory_limit', '9999M');
set_time_limit(0);
include "star_connection.php";

$customer_master = "customer_master";
$customer_destination = "customer_destination";
$destination_master = "destination_master";
$get_fetch_type = $get_fetch_type ? strtolower(trim($get_fetch_type)) : "";
$curr_date = date("jS_M_Y_h_m_s_A");
	$the_file_name = "customer_destination_".$curr_date.".csv";
	$where_qry = "";
	//$header = "Customer Code"."\t"."Customer Name"."\t"."Destination Code"."\t"."Destination Name"."\t"."Status";
	$header = "Customer Code".","."Destination Code".","."Status";
$qry = "select $customer_master.customer_code,$customer_master.`customer_name`,$customer_master.dns_customer_code,$destination_master.dns_destination_code,$destination_master.destination_name,
$customer_destination.acedns FROM $customer_master INNER JOIN  $customer_destination on $customer_master.`customer_code`=$customer_destination.`customer_code` INNER JOIN  $destination_master ON $destination_master.destination_code=$customer_destination.destination_code  order by $customer_master.dns_customer_code asc
LIMIT 6000001,1000000";
$sql = mysql_query($qry);

while ($row1 = mysql_fetch_array($sql)) {
	//$dns_customer_code = $row1["dns_customer_code"];
	$dns_customer_code =$row1["dns_customer_code"];
	$customer_code = $row1["customer_code"];
	//$customer_name = $row1["customer_name"];
	$customer_name ='';
	$destination_code = $row1["dns_destination_code"];
	//$destination_name = $row1["destination_name"];
	$destination_name='';
	$acedns = $row1["acedns"];
	$table_data .= $dns_customer_code.",".$destination_code.",".$acedns."\n";
}
// Download the file

$filename = 'customer destination-7.csv';
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');    
header('Expires: 0');
echo ucwords($header)."\n".$table_data;
exit;

mysql_close();
?>
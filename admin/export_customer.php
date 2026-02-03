<?php
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$get_fetch_type = $_GET["get_type"] ? trim($_GET["get_type"]) : "all";
if($get_fetch_type!=""){
	if($get_fetch_type=="dealer" || $get_fetch_type=="subdealer" || $get_fetch_type=="all"){
		startCreatCustomerCsvfile($get_fetch_type);
	}else{
		exit;
	}
}else{
	exit;
}

function startCreatCustomerCsvfile($get_fetch_type){
$customer_master = "customer_master";
$branch_master = "branch_master";
$get_fetch_type = $get_fetch_type ? strtolower(trim($get_fetch_type)) : "";
$curr_date = date("jS_M_Y_h_m_s_A");
if($get_fetch_type=="dealer"){
	$the_file_name = "dealer_customer_".$curr_date.".csv";
	$where_qry = " where $customer_master.`cust_type`='Dealer' and $customer_master.`acedns`='Y' and $customer_master.`cust_type`!='Non Star' ";
}else if($get_fetch_type=="subdealer"){
	$the_file_name = "subdealer_customer_".$curr_date.".csv";
	$where_qry = " where $customer_master.`cust_type`='Sub Dealer' and $customer_master.`acedns`='Y' and $customer_master.`cust_type`!='Non Star' ";
}else{
	$the_file_name = "all_customer_".$curr_date.".csv";
	$where_qry = " where ($customer_master.`cust_type`='Sub Dealer' or $customer_master.`cust_type`='Dealer') and $customer_master.`acedns`='Y' and $customer_master.`cust_type`!='Non Star' ";
}
$output = "";
$qry = "select $customer_master.`customer_code`,$customer_master.`dns_customer_code`,$customer_master.`customer_name`,$customer_master.`address`,$customer_master.`phone_no`,$customer_master.`route_code`,$customer_master.`acedns`,$customer_master.`black_list`,$customer_master.`cust_type`,$customer_master.`rds_tag`,$customer_master.`branch_code`,$branch_master.`branch_name`,$customer_master.`whatsapp_no`,$customer_master.`email` from $customer_master left join $branch_master on $customer_master.`branch_code`=$branch_master.`branch_code` $where_qry order by $customer_master.`customer_code` asc";
$sql = mysql_query($qry);
$columns_total = mysql_num_fields($sql);

// Get The Field Name

for ($i = 0; $i < $columns_total; $i++) {
$heading = mysql_field_name($sql, $i);
$output .= '"'.$heading.'",';
}
$output .="\n";
// Get Records from the table

while ($row = mysql_fetch_array($sql)) {
for ($i = 0; $i < $columns_total; $i++) {
$output .='"'.$row["$i"].'",';
}
$output .="\n";
}

// Download the file

$filename = $the_file_name;
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');    
header('Expires: 0');
echo $output;
exit;
}

mysql_close();
?>
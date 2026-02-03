<?php
ini_set('memory_limit', '9999M');
set_time_limit(0);
$downloadname='customer destination.csv';
$csvname="csv/customer_destination.csv";
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$downloadname.'"');
readfile($csvname);
/*ini_set('memory_limit', '9999M');
set_time_limit(0);
include "star_connection.php";
$get_fetch_type = $_GET["get_type"] ? trim($_GET["get_type"]) : "all";
if($get_fetch_type!=""){
	if($get_fetch_type=="all"){
		startCreatCustomerdestCsvfile($get_fetch_type);
	}else{
		exit;
	}
}else{
	exit;
}

function startCreatCustomerdestCsvfile($get_fetch_type){
$customer_master = "customer_master";
$customer_destination = "customer_destination";
$destination_master = "destination_master";
$get_fetch_type = $get_fetch_type ? strtolower(trim($get_fetch_type)) : "";
$curr_date = date("jS_M_Y_h_m_s_A");
	$the_file_name = "customer_destination_".$curr_date.".csv";
	$where_qry = "";
	$header = "Customer Code"."\t"."Customer Name"."\t"."Destination Code"."\t"."Destination Name"."\t"."Status";
/*$qry = "select $customer_master.customer_code,$customer_master.`customer_name`,$customer_master.dns_customer_code,$destination_master.destination_code,$destination_master.destination_name,
$customer_destination.acedns FROM $customer_master INNER JOIN  $customer_destination on $customer_master.`customer_code`=$customer_destination.`customer_code` INNER JOIN  $destination_master ON $destination_master.destination_code=$customer_destination.destination_code  order by $customer_master.`customer_name` asc";
$sql = mysql_query($qry);*/
/*echo $qry = "select * FROM $customer_destination";
$sql = mysql_query($qry);
//$columns_total = mysql_num_fields($sql);

// Get The Field Name

/*for ($i = 0; $i < $columns_total; $i++) {
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
}*/
/*$table_data='';
while ($row1 = mysql_fetch_array($sql)) {
	//$dns_customer_code = $row1["dns_customer_code"];
	$dns_customer_code ='';
	$customer_code = $row1["customer_code"];
	//$customer_name = $row1["customer_name"];
	$customer_name ='';
	$destination_code = $row1["destination_code"];
	//$destination_name = $row1["destination_name"];
	$destination_name='';
	$acedns = $row1["acedns"];
		
		if($acedns=='Y') $status='ACTIVE';
		if($acedns=='N') $status='INACTIVE';
	$table_data .= $dns_customer_code.",".$customer_name.",".$destination_code.",".$destination_name.",".$status."\n";
}
// Download the file

$filename = $the_file_name;
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');    
header('Expires: 0');
echo ucwords($header)."\n".$table_data;
exit;
}

mysql_close();*/
?>
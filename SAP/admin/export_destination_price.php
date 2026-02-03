<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & E_NOTICE & E_DEPRECATED);
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$destination_wise_price = "destination_wise_price";

$new_qry_string_filtered = "";
$sl_destination = $_GET["sl_destination"] ? addslashes(trim($_GET["sl_destination"])) : "";
$sl_product = $_GET["sl_product"] ? addslashes(trim($_GET["sl_product"])) : "";
$whr_str = "";
$search_array = array("sl_destination"=>$sl_destination,"sl_product"=>$sl_product);
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="sl_destination"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($destination_wise_price.destination_name like '%$search_array_val%') ";
			$new_qry_string_filtered .= "&sl_destination=".$search_array_val;
		}
	}
	if($search_array_key=="sl_product"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($destination_wise_price.product_name like '%$search_array_val%' ) ";
			$new_qry_string_filtered .= "&sl_product=".$search_array_val;
		}
	}
}

if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}

$output = "";
$qry = "select $destination_wise_price.destination_name,$destination_wise_price.product_name,$destination_wise_price.effective_date,
$destination_wise_price.effective_rate,$destination_wise_price.rate from $destination_wise_price $new_whr_str ORDER BY destination_name,product_name ASC";
$sql = mysql_query($qry);
// Get The Field Name
$slno_cnt = 1;
$output .= '"Destination Name","Product Name","Rate","Effective Date","Effective Rate"';
$output .="\n";
// Get Records from the table

while ($row1 = mysql_fetch_array($sql)) {
	$destination_name = $row1["destination_name"];
	$product_name = $row1["product_name"];
	$effective_date = date('d/m/Y H:i:s',strtotime($row1["effective_date"]));
	$effective_rate = $row1["effective_rate"];
	$rate = $row1["rate"];
		
$output .= '"'.$destination_name.'","'.$product_name.'","'.$rate.'","'.$effective_date.'","'.$effective_rate.'"';

$output .="\n";
$slno_cnt++;
}
// Download the file
$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "destination_price".$curr_date.".csv";
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$the_file_name);
header('Pragma: no-cache');    
header('Expires: 0');
echo $output;
exit;

mysql_close();
?>
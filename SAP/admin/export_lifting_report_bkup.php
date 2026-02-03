<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & E_NOTICE & E_DEPRECATED);
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$lifting = "lifting";

$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "lifting_report_" . $curr_date . ".csv";



$new_qry_string_filtered = "";
$export_filtered_str = "";

$sl_branch = $_GET["sl_branch"] ? addslashes(trim($_GET["sl_branch"])) : "";
$srch_linked_dealer = $_GET["srch_linked_dealer"] ? addslashes(trim($_GET["srch_linked_dealer"])) : "";
$srch_sub_dealer = $_GET["srch_sub_dealer"] ? addslashes(trim($_GET["srch_sub_dealer"])) : "";
$month = $_GET["month"] ? addslashes(trim($_GET["month"])) : "";
$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d', strtotime("-1 days"));

$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$whr_str = "";
$search_array = array("sl_branch" => $sl_branch, "srch_linked_dealer" => $srch_linked_dealer, "srch_sub_dealer" => $srch_sub_dealer, "month" => $month);
foreach ($search_array as $search_array_key => $search_array_val) {

	if ($search_array_key == "sl_branch") {
		if ($search_array_val != '') {
			/*if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}*/
			$aand = " and";
			$whr_str .= "$aand $lifting.branch_code='$search_array_val'";
			$export_filtered_str .= "&sl_branch=" . $search_array_val;
		}
	}
	if ($search_array_key == "srch_linked_dealer") {
		if ($search_array_val != '') {
			/*if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}*/
			$aand = " and";
			$whr_str .= "$aand ($lifting.linked_dealer_name like '%$search_array_val%' OR $lifting.linked_dealer_sap_code like '%$search_array_val%') ";
			$export_filtered_str .= "&srch_linked_dealer=" . $search_array_val;
		}
	}
	if ($search_array_key == "srch_sub_dealer") {
		if ($search_array_val != '') {
			/*if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}*/
			$aand = " and";
			$whr_str .= "$aand ($lifting.sub_dealer_rssd_name like '%$search_array_val%' OR $lifting.sub_dealer_rssd_sap_code like '%$search_array_val%') ";
			$export_filtered_str .= "&srch_sub_dealer=" . $search_array_val;
		}
	}
	if ($search_array_key == "month") {
		if ($search_array_val != '') {
			$search_array_formatted = '01-' . $search_array_val;
			$monthNumber = date('n', strtotime($search_array_formatted));
			/*if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}*/
			$aand = " and";
			$whr_str .= "$aand MONTH($lifting.`date_of_lifting`)='$monthNumber'";
			$export_filtered_str .= "&month=" . $search_array_val;
		}
	}
}

if ($whr_str != "") {
	$new_whr_str = " " . $whr_str;
} else {
	$new_whr_str = "";
}


$output = "";
$name_login = trim($_SESSION["start_report_admin_name"]);
// get the data 'order_show_branch' for this name_login from 'startreport_admin'
$sql = "select * from `startreport_admin` where `user_name`='$name_login' limit 1";
$res = mysql_query($sql);
// get the data 'order_show_branch' from 'startreport_admin'
if ($res) {
	$row = mysql_fetch_assoc($res);
	$order_show_branch = $row["order_show_branch"] ? trim($row["order_show_branch"]) : "";
}
// get the list of 'customer_code' where 'region' is 'order_show_branch' from 'customer_master'
$sql = "select `customer_code` from `customer_master` where `region`='$order_show_branch'";
$res = mysql_query($sql);
$totres = mysql_num_rows($res);
if ($totres > 0) {
	$customer_code_arr = array();
	while ($row = mysql_fetch_assoc($res)) {
		$customer_code_arr[] = $row["customer_code"];
	}
}
// get all from $lifting where 'linked_dealer_cust_code' is in $customer_code_arr
$customer_code_arr_str = implode("','", $customer_code_arr);
$whr_str = "where `linked_dealer_cust_code` in ('$customer_code_arr_str')";

// Get the numbers of filtered rows from $lifting
if ($order_show_branch == '') {
	$sql_pg = "select * from $lifting Where 1 $new_whr_str order by `lid` asc";
} else {
	$sql_pg = "select * from $lifting Where 1 $new_whr_str order by `lid` asc";
}
$qry = $sql_pg;
$sql = mysql_query($qry);
// Get The Field Name
$slno_cnt = 1;
$output .= '"Linked Dealer Code","Linked Dealer SAP Code","Linked Dealer Name","Sub Dealer/RSSD Code","Sub Dealer/RSSD SAP Code","Sub Dealer/RSSD Name","Branch","Month","Product Name","Total(Bags)","Date of Lifting","Challan No.","Submit Date & Time","Status (Approved/Pending/Rejected)","Approve/Rejection Date & Time","Reason for Rejection","Total Subdealer/RSSD Sale","Total Dealer Sale","Subdealer/RSSD Sale (%)"';
$output .= "\n";
// Get Records from the table


while ($row1 = mysql_fetch_array($sql)) {


	$linked_dealer_code = $row1["linked_dealer_code"] ? trim($row1["linked_dealer_code"]) : "";
	$linked_dealer_sap_code = $row1["linked_dealer_sap_code"] ? trim($row1["linked_dealer_sap_code"]) : "";
	$linked_dealer_name = $row1["linked_dealer_name"] ? str_replace('"', '""', trim($row1["linked_dealer_name"])) : "";
	$sub_dealer_rssd_code = $row1["sub_dealer_rssd_code"] ? trim($row1["sub_dealer_rssd_code"]) : "";
	$sub_dealer_rssd_sap_code = $row1["sub_dealer_rssd_sap_code"] ? trim($row1["sub_dealer_rssd_sap_code"]) : "";
	$sub_dealer_rssd_name = $row1["sub_dealer_rssd_name"] ? str_replace('"', '""', trim($row1["sub_dealer_rssd_name"])) : "";
	$branch = $row1["branch"] ? str_replace('"', '""', trim($row1["branch"])) : "";
	$prod_display_name = $row1["prod_display_name"] ? str_replace('"', '""', trim($row1["prod_display_name"])) : "";
	$total_bags = $row1["total_bags"] ? trim($row1["total_bags"]) : "";
	$date_of_lifting = $row1["date_of_lifting"] ? trim($row1["date_of_lifting"]) : "";
	$challan_no = $row1["challan_no"] ? trim($row1["challan_no"]) : "";
	$submit_date_time = $row1["submit_date_time"] ? trim($row1["submit_date_time"]) : "";
	$status = $row1["status"] ? trim($row1["status"]) : "";
	$status_date_and_time = $row1["status_date_and_time"] ? trim($row1["status_date_and_time"]) : "";
	$reason_for_rejection = $row1["reason_for_rejection"] ? str_replace('"', '""', trim($row1["reason_for_rejection"])) : "";
	$total_subdealer_rssd_sale = $row1["total_subdealer_rssd_sale"] ? str_replace('"', '""', trim($row1["total_subdealer_rssd_sale"])) : "";
	$total_dealer_sale = $row1["total_dealer_sale"] ? trim($row1["total_dealer_sale"]) : "";
	$subdealer_rssd_sale_percent = $row1["subdealer_rssd_sale_percent"] ? trim($row1["subdealer_rssd_sale_percent"]) : "";
	//$month = $row1["month"] ? trim($row1["month"]) : "";
	$month = "";
	if ($date_of_lifting != "") {
		$month = date("M", strtotime($date_of_lifting));
	}

	$output .= '"' . $linked_dealer_code . '","' . $linked_dealer_sap_code . '","' . $linked_dealer_name . '","' . $sub_dealer_rssd_code . '","' . $sub_dealer_rssd_sap_code . '","' . $sub_dealer_rssd_name . '","' . $branch . '","' . $month . '","' . $prod_display_name . '","' . $total_bags . '","' . $date_of_lifting . '","' . $challan_no . '","' . $submit_date_time . '","' . $status . '","' . $status_date_and_time . '","' . $reason_for_rejection . '","' . $total_subdealer_rssd_sale . '","' . $total_dealer_sale . '","' . $subdealer_rssd_sale_percent . '"';

	$output .= "\n";
	$slno_cnt++;
}
// Download the file

$filename = $the_file_name;
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename=' . $filename);
header('Pragma: no-cache');
header('Expires: 0');
echo $output;
exit;

mysql_close();

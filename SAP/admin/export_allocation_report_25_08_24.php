<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & E_NOTICE & E_DEPRECATED);
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$allocation_details = "allocation_details";
$branch_master = "branch_master";
$customer_master="customer_master";
$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "allocation_report_" . $curr_date . ".csv";
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
			$whr_str .= "$aand $customer_master.branch_code='$search_array_val'";
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
			$whr_str .= "$aand ($customer_master.customer_name like '%$search_array_val%' OR $customer_master.customer_id like '%$search_array_val%') ";
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
			$whr_str .= "$aand ($allocation_details.sub_dealer_id like '%$search_array_val%') ";
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
			$whr_str .= "$aand DATE_FORMAT($allocation_details.`date_and_time`, '%Y-%m')='$search_array_val' ";
			$export_filtered_str .= "&month=" . $search_array_val;
		}
	}
}
$output = "";
/*$name_login = trim($_SESSION["start_report_admin_name"]);
// get the data 'order_show_branch' for this name_login from 'startreport_admin'
$sql = "select * from `startreport_admin` where `user_name`='$name_login' limit 1";
$res = mysql_query($sql);
// get the data 'order_show_branch' from 'startreport_admin'
if ($res) {
	$row = mysql_fetch_assoc($res);
	$order_show_branch = $row["order_show_branch"] ? trim($row["order_show_branch"]) : "";
}*/
// get the list of 'customer_code' where 'region' is 'order_show_branch' from 'customer_master'
/*$sql = "select `customer_code` from `customer_master` where `region`='$order_show_branch'";
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
$whr_str = "where `linked_dealer_cust_code` in ('$customer_code_arr_str')";*/
if ($whr_str != "") {
	$new_whr_str = " " . $whr_str;
} else {
	$new_whr_str = "";
}
// Get the numbers of filtered rows from $lifting
/*if ($order_show_branch == '') {
	$sql_pg = "select * from $lifting Where 1 $new_whr_str order by `lid` asc";
} else {
	$whr_str_region = "AND `linked_dealer_cust_code` in (SELECT customer_code FROM customer_master WHERE region='$order_show_branch')";
	$new_whr_str .= " ".$whr_str_region;
	$sql_pg = "select * from $lifting Where 1 $new_whr_str order by `lid` asc";
}*/
$sql_pg ="select $allocation_details.*,$allocation_details.allocation_qty AS total_allocation_qty,$customer_master.dns_customer_code,$customer_master.customer_name,$branch_master.branch_name from $allocation_details,$customer_master,$branch_master Where $allocation_details.customer_id=$customer_master.customer_id AND $customer_master.branch_code=$branch_master.branch_code $new_whr_str  order by $allocation_details.`customer_id` asc,$allocation_details.`dns_prod_code` asc";
$qry = $sql_pg;
$sql = mysql_query($qry);
// Get The Field Name
$slno_cnt = 1;
$output .= '"Allocation Date Time","Linked Dealer Code","Linked Dealer SAP Code","Linked Dealer Name","Sub Dealer/RSSD Code","Sub Dealer/RSSD SAP Code","Sub Dealer/RSSD Name","Branch","Month","Product Name","Total Dispatch qty","Allocated qty","Remaining Allocation qty","Challan no"';
$output .= "\n";
// Get Records from the table
while ($row1 = mysql_fetch_array($sql)) {
			$date_and_time = $row1["date_and_time"] ? trim($row1["date_and_time"]) : "";
			$linked_dealer_code = $row1["dns_customer_code"] ? trim($row1["dns_customer_code"]) : "";
			$linked_dealer_sap_code = $row1["customer_id"] ? trim($row1["customer_id"]) : "";
			$linked_dealer_name = $row1["customer_name"] ? trim($row1["customer_name"]) : "";
			$sub_dealer_rssd_sap_code = $row1["sub_dealer_id"] ? trim($row1["sub_dealer_id"]) : "";
			$sub_dealer_details="select dns_customer_code,customer_name FROM $customer_master where customer_id='$sub_dealer_rssd_sap_code'";
			$res_sub_dealer_details=mysql_query($sub_dealer_details);
			$row_sub_dealer_details=mysql_fetch_array($res_sub_dealer_details);
			$sub_dealer_rssd_code = $row_sub_dealer_details["dns_customer_code"]? trim($row_sub_dealer_details["dns_customer_code"]) : "";
			$sub_dealer_rssd_name = $row_sub_dealer_details["customer_name"] ? trim($row_sub_dealer_details["customer_name"]) : "";
			$branch = $row1["branch_name"] ? trim($row1["branch_name"]) : "";
			$dns_prod_code = $row1["dns_prod_code"] ? trim($row1["dns_prod_code"]) : "";
			$prod_display_name = $row1["prod_desc"] ? trim($row1["prod_desc"]) : "";

	$monthval=substr($row1["date_and_time"],5,2);		
	/*$sqldespatchqty="select SUM(CHALLANQTY) as total_despatch_qty from $T_DOCHALLAN where `dns_customer_code`='$linked_dealer_code'  AND dns_prod_code='$dns_prod_code' AND substring(CHALLANDT,6,2)='$monthval'";
	$resdespatchqty=mysql_query($sqldespatchqty);
	$rowdespatchqty=mysql_fetch_array($resdespatchqty);
	$total_despatch_qty = $rowdespatchqty["total_despatch_qty"] ? trim($rowdespatchqty["total_despatch_qty"]) : "";*/
	$total_despatch_qty = $row1["dispatch_qty"] ? trim($row1["dispatch_qty"]) : "";		
	${'total_allocation_qty'.$linked_dealer_code.$dns_prod_code.$monthval} =${'total_allocation_qty'.$linked_dealer_code.$dns_prod_code.$monthval}+ $row1["total_allocation_qty"];
	$challan_no = $row1["challan_no"] ? trim($row1["challan_no"]) : "";
	$challan_no=str_replace(',',';',$challan_no);
			// $month = $row1["month"] ? trim($row1["month"]) : "";
			$month = "";
			if ($row1["date_and_time"] != "") {
				$month = date("M-y", strtotime($row1["date_and_time"]));
			}
			$remaining_allocation_qty=($total_despatch_qty-${'total_allocation_qty'.$linked_dealer_code.$dns_prod_code.$monthval} );
	$output .= '"' .$date_and_time. '","' . $linked_dealer_code . '","' . $linked_dealer_sap_code . '","' . $linked_dealer_name . '","' . $sub_dealer_rssd_code . '","' . $sub_dealer_rssd_sap_code . '","' . $sub_dealer_rssd_name . '","' . $branch . '","' . $month . '","' . $prod_display_name . '","' . $total_despatch_qty . '","' . $row1["total_allocation_qty"] . '","' . $remaining_allocation_qty . '","' . $challan_no . '"';
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
?>

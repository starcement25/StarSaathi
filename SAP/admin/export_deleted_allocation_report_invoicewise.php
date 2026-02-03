<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & E_NOTICE & E_DEPRECATED);
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$start_user_type = $_SESSION["start_user_type"];
$allocation_details = "allocation_details_invoicewise_log";
$branch_master = "branch_master";
$customer_master="customer_master";
$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "deleted_allocation_report_" . $curr_date . ".csv";
$new_qry_string_filtered = "";
$export_filtered_str = "";
//$sl_branch = $_GET["sl_branch"] ? addslashes(trim($_GET["sl_branch"])) : "";
//$srch_linked_dealer = $_GET["srch_linked_dealer"] ? addslashes(trim($_GET["srch_linked_dealer"])) : "";
//$srch_sub_dealer = $_GET["srch_sub_dealer"] ? addslashes(trim($_GET["srch_sub_dealer"])) : "";
//$month = $_GET["month"] ? addslashes(trim($_GET["month"])) : "";
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
			//$whr_str .= "$aand DATE_FORMAT($allocation_details.`date_and_time`, '%Y-%m')='$search_array_val' ";
			$whr_str .= "$aand DATE_FORMAT($allocation_details.`inv_date`, '%Y-%m')='$search_array_val' ";
			$export_filtered_str .= "&month=" . $search_array_val;
		}
	}
}
$output = "";

if ($whr_str != "") {
	$new_whr_str = " " . $whr_str;
} else {
	$new_whr_str = "";
}
// Get the numbers of filtered rows from $lifting

if($_SESSION["start_user_type"]=="MANAGER"){
	$order_show_branch = $_SESSION["order_show_branch"] ? trim($_SESSION["order_show_branch"]) : "";

	if($order_show_branch=="NE"){
	$dns_branchcode_master = "north_east_branch";
	}else if($order_show_branch=="NOTNE"){
	$dns_branchcode_master = "not_north_east_branch";
	}
	if($order_show_branch=="NE" || $order_show_branch=="NOTNE"){
	$sqlbm = "select `branch_code` from $dns_branchcode_master";
	$resbm = mysql_query($sqlbm);
	$totresbm = mysql_num_rows($resbm);
	if($totresbm>0){
		$dnsbcarr = array();
		while($rowbm=mysql_fetch_assoc($resbm)){
			$the_dns_bc = $rowbm["branch_code"] ? trim($rowbm["branch_code"]) : "";
			if($the_dns_bc!=""){
				$dnsbcarr[] = $the_dns_bc;
			}
		}
		if(count($dnsbcarr)>0){
			$dnsbcstr = implode("','",$dnsbcarr);
	$sqlabc = "select `branch_code` from $branch_master where `dns_branch_code` in('".$dnsbcstr."')";
	$resabc = mysql_query($sqlabc);
	$totresabc = mysql_num_rows($resabc);
	if($totresabc>0){
		while($rowabc=mysql_fetch_assoc($resabc)){
			$the_bc = $rowabc["branch_code"] ? trim($rowabc["branch_code"]) : "";
			if($the_bc!=""){
				$theactbcarr[] = $the_bc;
			}
		}
		if(count($theactbcarr)>0){
			$theactbcstr = implode("','",$theactbcarr);

		}

	}

		}
	}
	}else{
		if($order_show_branch!=""){
			if($order_show_branch=="MISNE"){
				$whr_qry = " where `branch_state`='NE' ";
			}else if($order_show_branch=="MISROE"){
				$whr_qry = " where `branch_state` in('BIHAR','WB') ";
			}else if($order_show_branch=="MISALL"){
				$whr_qry = " where `branch_state` in('BIHAR','WB','NE') ";
			}else{
				$whr_qry = " where `branch_state`='$order_show_branch' ";
			}

			$sqlabc = "select `branch_code` from $branch_master $whr_qry ";
			$resabc = mysql_query($sqlabc);
			$totresabc = mysql_num_rows($resabc);
			if($totresabc>0){
			while($rowabc=mysql_fetch_assoc($resabc)){
			$the_bc = $rowabc["branch_code"] ? trim($rowabc["branch_code"]) : "";
			if($the_bc!=""){
			$theactbcarr[] = $the_bc;
			}
			}
			if(count($theactbcarr)>0){
			$theactbcstr = implode("','",$theactbcarr);

			}

			}


		}
	}
	if($theactbcstr!=""){
	$sqlftcbrnc = "select `branch_code`,`branch_name` from $branch_master where `branch_code` in('".$theactbcstr."') order by `branch_name` asc";
	$res1dftftcbrnc = mysql_query($sqlftcbrnc);
	$totres1dftftcbrnc = mysql_num_rows($res1dftftcbrnc);
	}else{
	$totres1dftftcbrnc = 0;
	}

}else{
	$sqlftcbrnc = "select `branch_code`,`branch_name` from $branch_master order by `branch_name` asc";
	$res1dftftcbrnc = mysql_query($sqlftcbrnc);
	$totres1dftftcbrnc = mysql_num_rows($res1dftftcbrnc);
}
if($_SESSION["start_user_type"]=="MANAGER" && $order_show_branch!=""){
	/*$sql_pg ="select $allocation_details.*,$allocation_details.allocation_qty AS total_allocation_qty,$customer_master.dns_customer_code,$customer_master.customer_name,$branch_master.branch_name from $allocation_details,$customer_master,$branch_master Where $allocation_details.customer_id=$customer_master.customer_id AND $customer_master.branch_code=$branch_master.branch_code and $customer_master.`branch_code` in('".$theactbcstr."')  $new_whr_str  order by $allocation_details.`customer_id` asc,$allocation_details.`prod_desc` asc,$allocation_details.`inv_date` asc,$allocation_details.date_and_time asc";*/

	$sql_pg ="select $allocation_details.*,$allocation_details.allocation_qty AS total_allocation_qty,$customer_master.dns_customer_code,$customer_master.customer_name,$branch_master.branch_name from $allocation_details,$customer_master,$branch_master
	Where $allocation_details.customer_id=$customer_master.customer_id
	AND $customer_master.branch_code=$branch_master.branch_code
	and $customer_master.`branch_code` in('".$theactbcstr."')



	order by $allocation_details.`customer_id` asc,$allocation_details.`prod_desc` asc,$allocation_details.`inv_date` asc,$allocation_details.date_and_time asc";
}
else{/*
$sql_pg ="select $allocation_details.*,$allocation_details.allocation_qty AS total_allocation_qty,$customer_master.dns_customer_code,$customer_master.customer_name,$branch_master.branch_name from $allocation_details,$customer_master,$branch_master Where $allocation_details.customer_id=$customer_master.customer_id AND $customer_master.branch_code=$branch_master.branch_code $new_whr_str  order by $allocation_details.`customer_id` asc,$allocation_details.`prod_desc` asc,$allocation_details.`inv_date` asc,$allocation_details.date_and_time asc";*/
$startreport_admin='startreport_admin';

$sql_pg ="select $allocation_details.*,$allocation_details.allocation_qty AS total_allocation_qty,$customer_master.dns_customer_code,$customer_master.customer_name,$branch_master.branch_name,$startreport_admin.user_name as admin_user_name from $allocation_details,$customer_master,$branch_master,$startreport_admin
Where $allocation_details.customer_id=$customer_master.customer_id
AND $customer_master.branch_code=$branch_master.branch_code
AND $startreport_admin.id = $allocation_details.delete_by_id

order by $allocation_details.`customer_id` asc,$allocation_details.`prod_desc` asc,$allocation_details.`inv_date` asc,$allocation_details.date_and_time asc";
}
//echo"<pre>";print_r($sql_pg);die;
$qry = $sql_pg;
$sql = mysql_query($qry);
// Get The Field Name
$slno_cnt = 1;
$output .= '"Allocation Date Time","APPORDERNO","Inv Date","Linked Dealer Code","Linked Dealer SAP Code","Linked Dealer Name","Sub Dealer/RSSD Code","Sub Dealer/RSSD SAP Code","Sub Dealer/RSSD Name","Branch","Month","Product Name","Total Inv qty","Allocated qty","Remaining Allocation qty","Inv no","Order Type","Inv Cancel","Deleted By (User Name)","Deleted At"';
$output .= "\n";
// Get Records from the table
while ($row1 = mysql_fetch_array($sql)) {
			$APPORDERNO = $row1["APPORDERNO"] ? trim($row1["APPORDERNO"]) : "";
			$is_offline = $row1["is_offline"] ? trim($row1["is_offline"]) : "";
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
			$inv_cancl = $row1["inv_cancl"] ? trim($row1["inv_cancl"]) : "";
			$admin_user_name = $row1["admin_user_name"] ? trim($row1["admin_user_name"]) : "";
			$deleted_at = $row1["deleted_at"] ? trim($row1["deleted_at"]) : "";

	$monthval=substr($row1["date_and_time"],5,2);
	$total_inv_qty = $row1["inv_qty"] ? trim($row1["inv_qty"]) : "";
	$inv_date = $row1["inv_date"] ? trim($row1["inv_date"]) : "";
	$inv_no = $row1["inv_no"] ? trim($row1["inv_no"]) : "";
	${'total_allocation_qty'.$linked_dealer_code.$APPORDERNO.$inv_no} =${'total_allocation_qty'.$linked_dealer_code.$APPORDERNO.$inv_no}+ $row1["total_allocation_qty"];

			$month = "";
			if ($row1["date_and_time"] != "") {
				$month = date("M-y", strtotime($row1["date_and_time"]));
			}
			$remaining_allocation_qty=($total_inv_qty-${'total_allocation_qty'.$linked_dealer_code.$APPORDERNO.$inv_no} );
	$output .= '"' .$date_and_time. '","' .$APPORDERNO. '","' .$inv_date. '","' . $linked_dealer_code . '","' . $linked_dealer_sap_code . '","' . $linked_dealer_name . '","' . $sub_dealer_rssd_code . '","' . $sub_dealer_rssd_sap_code . '","' . $sub_dealer_rssd_name . '","' . $branch . '","' . $month . '","' . $prod_display_name . '","' . $total_inv_qty . '","' . $row1["total_allocation_qty"] . '","' . $remaining_allocation_qty . '","' . $inv_no . '","' . $is_offline . '","' . $inv_cancl . '","'.$admin_user_name.'","'.$deleted_at.'"';
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

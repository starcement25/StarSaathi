<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & E_NOTICE & E_DEPRECATED);
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$start_user_type = $_SESSION["start_user_type"];
// $sikkim_consumer_scheme = "sikkim_consumer_scheme";
// $customer_master = "customer_master";
// $branch_master="branch_master";
$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "kismat_ki_bori_consumer_scheme_report_" . $curr_date . ".csv";
$dnsbcarr = array();
$dnsbcstr ="";
$theactbcarr = array();
$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$new_qry_string_filtered = "";
$trn_branch = $_GET["trn_branch"] ? addslashes(trim($_GET["trn_branch"])) : "";
$sl_day_wise = $_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : "";
$from_dt = $_GET["from_dt"] ? addslashes(trim($_GET["from_dt"])) : "";
$to_dt = $_GET["to_dt"] ? addslashes(trim($_GET["to_dt"])) : "";
$srch_dtls = $_GET["srch_dtls"] ? addslashes(trim($_GET["srch_dtls"])) : "";
$whr_str = "";
$export_filtered_str = "";
$search_array = array("srch_dtls"=>$srch_dtls,"trn_branch"=>$trn_branch,"daywise"=>array("sl_day_wise"=>$sl_day_wise,"from_dt"=>$from_dt,"to_dt"=>$to_dt));
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand (scs.`customer_id` like '%$search_array_val%' or scs.`house_owner_name` like '%$search_array_val%'  or scs.`house_owner_phone` like '%$search_array_val%'  or cm.`dns_customer_code` like '%$search_array_val%'  or cm.`customer_name` like '%$search_array_val%') ";
			$new_qry_string_filtered .= "&srch_dtls=".$search_array_val;
		}
	}
	if($search_array_key=="trn_branch"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand (bm.branch_code = '$search_array_val') ";
			$new_qry_string_filtered .= "&trn_branch=".$search_array_val;
		}else{
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand (bm.branch_code = 'B0009') ";
			$new_qry_string_filtered .= "&trn_branch=".$search_array_val;
		}
	}
	else if($search_array_key=="daywise"){
		$the_sl_day_wise = $search_array_val["sl_day_wise"];
		$the_from_dt = $search_array_val["from_dt"];
		$the_to_dt = $search_array_val["to_dt"];
		if(trim($whr_str)!=""){
		$aand = " and";
		}else{
		$aand = "";
		}
		if($the_sl_day_wise=="Date_Range"){
			$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
			}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
			}
			if($the_from_dt!="" && $the_to_dt!=""){
			   $whr_str .= "$aand scs.`date_of_purchase` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
			   $new_qry_string_filtered .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}
			}else if($the_from_dt!="" && $the_to_dt==""){
				$whr_str .= "$aand scs.`date_of_purchase` >= '".$the_from_dt." ".$frm_hrs."' ";
				$new_qry_string_filtered .= "&from_dt=".$the_from_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}
			}else if($the_from_dt=="" && $the_to_dt!=""){
				$whr_str .= "$aand scs.`date_of_purchase` <= '".$the_to_dt." ".$to_hrs."' ";
				$new_qry_string_filtered .= "&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&to_dt=".$the_to_dt;
				}
			}
		}else{
			if($the_sl_day_wise=="Today"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand scs.`date_of_purchase` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
			}else if($the_sl_day_wise=="Yesterday"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand scs.`date_of_purchase` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
			
			}
		}
	}
}
if($whr_str!=""){
	$new_whr_str = "and ".$whr_str;
}else{
	$new_whr_str ="";
}



$sql_pg ="SELECT scs.*, cm.dns_customer_code, cm.customer_name, cm.customer_id, cm.region, bm.branch_name, bm.branch_name, cm.branch_code, cm.cust_type FROM sikkim_consumer_scheme scs LEFT JOIN customer_master cm ON scs.customer_id = cm.customer_id LEFT JOIN branch_master bm ON cm.branch_code = bm.branch_code WHERE scs.customer_id!='' $new_whr_str ORDER BY scs.`id` DESC";
$qry = $sql_pg;
$sql = mysql_query($qry);

// Get The Field Name
$slno_cnt = 1;
$output .= '"Dealer SAP code","SFA Code","Dealer name","Linked Dealer code","Linked Dealer name","Branch name","Region","Customer Type","House Owner Name","House Owner Number","Date Of Purchase","Quantity (in Bags)","Has Coupon","Coupon Numbers","Submit Date time"';
$output .= "\n";

// Get Records from the table
while ($row1 = mysql_fetch_array($sql)) {
	$rds_tag = $row['rds_tag'];
	$cmquery = mysql_query("SELECT `customer_id`,`customer_name` FROM `customer_master` WHERE `customer_code`=$rds_tag");
	$cmrow= mysql_fetch_assoc($cmquery);

	$coupons_details = json_decode($row1['coupons_details'], true);

	// Then do your existing logic:
	$coupon_text = '';
	$coupon_values = [];

	if (is_array($coupons_details)) {
		if (array_keys($coupons_details) !== range(0, count($coupons_details) - 1)) {
			// Object format
			$coupon_values = array_values($coupons_details);
		} else {
			// Array of single-key objects
			foreach ($coupons_details as $item) {
				if (is_array($item)) {
					foreach ($item as $code) {
						$coupon_values[] = $code;
					}
				}
			}
		}
	}

	// Remove empty codes if any
	$coupon_values = array_filter($coupon_values, function($v) { return !empty($v); });

	$coupon_text = implode(',', $coupon_values);


	$customer_id = $row1["customer_id"];
	$dns_customer_code = $row1["dns_customer_code"];
	$customer_name = $row1["customer_name"];
	$cmrow_customer_id = $cmrow["customer_id"];
	$cmrow_customer_name = $cmrow["customer_name"];
	$branch_name = $row1["branch_name"];
	$region = $row1["region"];
	$cust_type = $row1["cust_type"];
	$house_owner_name = $row1["house_owner_name"];
	$house_owner_phone = $row1["house_owner_phone"];
	$date_of_purchase = $row1['date_of_purchase'];
	$bags_quantity = $row1["bags_quantity"];
	$has_coupon = $row1["has_coupon"];
	$date_and_time = $row1['date_and_time'];
		

	$output .= '"'.$customer_id.'","'.$dns_customer_code.'","'.$customer_name.'","'.$cmrow_customer_id.'","'.$cmrow_customer_name.'","'.$branch_name.'","'.$region.'","'.$cust_type.'","'.$house_owner_name.'","'.$house_owner_phone.'","'.$date_of_purchase.'","'.$bags_quantity.'","'.$has_coupon.'","'.$coupon_text.'","'.$date_and_time.'"';

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

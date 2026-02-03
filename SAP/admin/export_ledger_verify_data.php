<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & E_NOTICE & E_DEPRECATED);
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$customer_master = "customer_master";
$verify_ledger_details = "verify_ledger_details";
$month_arr = array("01"=>"JAN","02"=>"FEB","03"=>"MAR","04"=>"APR","05"=>"MAY","06"=>"JUN","07"=>"JUL","08"=>"AUG","09"=>"SEP","10"=>"OCT","11"=>"NOV","12"=>"DEC");

$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "ledger_verify_data_".$curr_date.".csv";



$new_qry_string_filtered = "";
$srch_dlr_dtls = $_GET["srch_dlr_dtls"] ? addslashes(trim($_GET["srch_dlr_dtls"])) : "";
$astn_sl_status = $_GET["sl_status"] ? addslashes(trim($_GET["sl_status"])) : "";

$astn_sl_year = $_GET["sl_year"] ? addslashes(trim($_GET["sl_year"])) : "";
$astn_sl_month = $_GET["sl_month"] ? addslashes(trim($_GET["sl_month"])) : "";

$whr_str = "";
$export_filtered_str = "";
$search_array = array("srch_dlr_dtls"=>$srch_dlr_dtls,"astn_sl_status"=>$astn_sl_status,"year_month"=>array("astn_sl_year"=>$astn_sl_year,"astn_sl_month"=>$astn_sl_month));
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_dlr_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ( $customer_master.`dns_customer_code` like '%$search_array_val%' or $customer_master.`customer_name` like '%$search_array_val%' or $customer_master.`customer_code` like '%$search_array_val%' ) ";
			$new_qry_string_filtered .= "&srch_dlr_dtls=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&srch_dlr_dtls=".$search_array_val;
			}else{
				$export_filtered_str .= "&srch_dlr_dtls=".$search_array_val;
			}
		}
	}else if($search_array_key=="astn_sl_status"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand $verify_ledger_details.`status`='$search_array_val' ";
			$new_qry_string_filtered .= "&sl_status=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_status=".$search_array_val;
			}else{
				$export_filtered_str .= "&sl_status=".$search_array_val;
			}
		}
	}else if($search_array_key=="year_month"){
		$the_astn_sl_year = $search_array_val["astn_sl_year"] ? $search_array_val["astn_sl_year"] : "";
		$the_astn_sl_month = $search_array_val["astn_sl_month"] ? $search_array_val["astn_sl_month"] : "";
		if(trim($whr_str)!=""){
		$aand = " and";
		}else{
		$aand = "";
		}
		if($the_astn_sl_year!="" && $the_astn_sl_month!=""){
$whr_str .= "$aand DATE_FORMAT(STR_TO_DATE($verify_ledger_details.`ledger_year_month_day`, '%Y-%m-%d'), '%Y-%m') = '".$the_astn_sl_year."-".$the_astn_sl_month."' ";
$new_qry_string_filtered .= "&sl_year=".$the_astn_sl_year."&sl_month=".$the_astn_sl_month;
if($export_filtered_str!=""){
$export_filtered_str .= "&sl_year=".$the_astn_sl_year."&sl_month=".$the_astn_sl_month;
}else{
$export_filtered_str .= "&sl_year=".$the_astn_sl_year."&sl_month=".$the_astn_sl_month;
}
			}else if($the_astn_sl_year!="" && $the_astn_sl_month==""){
$whr_str .= "$aand DATE_FORMAT(STR_TO_DATE($verify_ledger_details.`ledger_year_month_day`, '%Y-%m-%d'), '%Y') = '".$the_astn_sl_year."' ";
				$new_qry_string_filtered .= "&sl_year=".$the_astn_sl_year;
if($export_filtered_str!=""){
$export_filtered_str .= "&sl_year=".$the_astn_sl_year;
}else{
$export_filtered_str .= "&sl_year=".$the_astn_sl_year;
}
			}else if($the_astn_sl_year=="" && $the_astn_sl_month!=""){
$whr_str .= "$aand DATE_FORMAT(STR_TO_DATE($verify_ledger_details.`ledger_year_month_day`, '%Y-%m-%d'), '%m') = '".$the_astn_sl_month."' ";
				$new_qry_string_filtered .= "&sl_month=".$the_astn_sl_month;
if($export_filtered_str!=""){
$export_filtered_str .= "&sl_month=".$the_astn_sl_month;
}else{
$export_filtered_str .= "&sl_month=".$the_astn_sl_month;
}
			}
			
			
		
	}
}

if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}

$output = "";
$qry = "select $verify_ledger_details.*,$customer_master.`customer_code`,$customer_master.`dns_customer_code`,$customer_master.`customer_name` from $verify_ledger_details left join $customer_master on $verify_ledger_details.`customer_code`=$customer_master.`customer_code` $new_whr_str order by $verify_ledger_details.`saved_datetime` desc";
$sql = mysql_query($qry);
// Get The Field Name
$slno_cnt = 1;
$output .= '"Customer_Code","Dealer_ID","Dealer_Name","Ledger_Month","Debit_Amount","Credit_Amount","Status","Comment","Updated_On"';
$output .="\n";
// Get Records from the table

while ($row1 = mysql_fetch_array($sql)) {
$cust_code = $row1["customer_code"] ? str_replace('"', '""',trim($row1["customer_code"])) : "";
$dns_cust_code = $row1["dns_customer_code"] ? str_replace('"', '""',trim($row1["dns_customer_code"])) : "";
$cust_name = $row1["customer_name"] ? str_replace('"', '""',trim($row1["customer_name"])) : "";
$ledger_year_month_day = $row1["ledger_year_month_day"] ? trim($row1["ledger_year_month_day"]) : "";
if($ledger_year_month_day!=""){
$ledger_month_year = date("M Y",strtotime($ledger_year_month_day));
}else{
$ledger_month_year = "";
}
$ledger_month_year = $ledger_month_year ? str_replace('"', '""',$ledger_month_year) : "";
$total_amount_dr = $row1["total_amount_dr"];
$total_amount_cr = $row1["total_amount_cr"];
$status = $row1["status"];
if($status=="APPROVED"){ $status="LEDGER CONFIRMED";}
$comment = $row1["comment"] ? str_replace('"', '""',trim($row1["comment"])) : "";
$saved_datetime = $row1["saved_datetime"];
		


$output .= '"'.$cust_code.'","'.$dns_cust_code.'","'.$cust_name.'","'.$ledger_month_year.'","'.$total_amount_dr.'","'.$total_amount_cr.'","'.$status.'","'.$comment.'","'.$saved_datetime.'"';

$output .="\n";
$slno_cnt++;
}
// Download the file

$filename = $the_file_name;
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');    
header('Expires: 0');
echo $output;
exit;

mysql_close();
?>
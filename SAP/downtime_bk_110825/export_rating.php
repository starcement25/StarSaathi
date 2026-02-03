<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & E_NOTICE & E_DEPRECATED);
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$start_user_type = $_SESSION["start_user_type"];
$dealer_sales_team_visit_survey = "dealer_sales_team_visit_survey";
$customer_master = "customer_master";
$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "rating_report_" . $curr_date . ".csv";
$dnsbcarr = array();
$dnsbcstr ="";
$theactbcarr = array();
$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$new_qry_string_filtered = "";
$sl_day_wise = $_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : "";
$from_dt = $_GET["from_dt"] ? addslashes(trim($_GET["from_dt"])) : "";
$to_dt = $_GET["to_dt"] ? addslashes(trim($_GET["to_dt"])) : "";
$srch_dtls = $_GET["srch_dtls"] ? addslashes(trim($_GET["srch_dtls"])) : "";
$whr_str = "";
$export_filtered_str = "";
$search_array = array("srch_dtls"=>$srch_dtls,"daywise"=>array("sl_day_wise"=>$sl_day_wise,"from_dt"=>$from_dt,"to_dt"=>$to_dt));
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand ($dealer_sales_team_visit_survey .`sap_customer_code` like '%$search_array_val%' or $dealer_sales_team_visit_survey.`emp_name` like '%$search_array_val%'  or $customer_master.`customer_name` like '%$search_array_val%') ";
			$new_qry_string_filtered .= "&srch_dtls=".$search_array_val;
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
			   $whr_str .= "$aand $dealer_sales_team_visit_survey.`rating_update_datetime` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
			   $new_qry_string_filtered .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}
			}else if($the_from_dt!="" && $the_to_dt==""){
				$whr_str .= "$aand $dealer_sales_team_visit_survey.`rating_update_datetime` >= '".$the_from_dt." ".$frm_hrs."' ";
				$new_qry_string_filtered .= "&from_dt=".$the_from_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}
			}else if($the_from_dt=="" && $the_to_dt!=""){
				$whr_str .= "$aand $dealer_sales_team_visit_survey.`rating_update_datetime` <= '".$the_to_dt." ".$to_hrs."' ";
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
				$whr_str .= "$aand $dealer_sales_team_visit_survey.`rating_update_datetime` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
			}else if($the_sl_day_wise=="Yesterday"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand $dealer_sales_team_visit_survey.`rating_update_datetime` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
			
			}
		}
	}
}
if($whr_str!=""){
	$new_whr_str = "and ".$whr_str;
}else{
	$new_whr_str ="";
}


$sql_pg ="select $dealer_sales_team_visit_survey.*,$customer_master.`customer_name` from $dealer_sales_team_visit_survey left join $customer_master on $dealer_sales_team_visit_survey.`customer_code`=$customer_master.`dns_customer_code` where survey_rating!=''  $new_whr_str ";
$qry = $sql_pg;
$sql = mysql_query($qry);
// Get The Field Name
$slno_cnt = 1;
$output .= '"SL No","Customer Code","SAP Code","Customer Name","Sales&nbspPerson Name","Visit Date Time","Rating","Remarks","Rate Submit Date time"';
$output .= "\n";
// Get Records from the table
while ($row1 = mysql_fetch_array($sql)) {
		$sl_no = $row1["id"];
		$customer_code = $row1["customer_code"];
		$sap_customer_code = $row1["sap_customer_code"];
		$emp_name = $row1["emp_name"];
		$customer_name = $row1["customer_name"];
		$visit_datetime = $row1["visit_datetime"];
		$survey_rating = $row1["survey_rating"];
		$remarks = $row1["remarks"];
		$rating_update_datetime = $row1["rating_update_datetime"];
		

	$output .= '"' .$slno_cnt. '","' .$customer_code. '","' .$sap_customer_code. '","' .$customer_name . '","' . $emp_name . '","' . $visit_datetime . '","' . $survey_rating . '","' . $remarks . '","' . $rating_update_datetime . '"';
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

<?php
ini_set('memory_limit', '9999M');
set_time_limit(0);
include "star_connection.php";
$srch_api_dtls = $_GET["srch_api_dtls"] ? addslashes(trim($_GET["srch_api_dtls"])) : "";
$srch_api_name = $_GET["srch_api_name"] ? addslashes(trim($_GET["srch_api_name"])) : "";
$sl_day_wise = $_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : "";
$from_dt = $_GET["from_dt"] ? addslashes(trim($_GET["from_dt"])) : "";
$to_dt = $_GET["to_dt"] ? addslashes(trim($_GET["to_dt"])) : "";
$astn_branch_code = $_GET["astn_branch_code"] ? addslashes(trim($_GET["astn_branch_code"])) : "";

areaApiLogCsvfile($conn,$srch_api_dtls,$srch_api_name,$sl_day_wise,$from_dt,$to_dt,$astn_branch_code);
function areaApiLogCsvfile($conn,$srch_api_dtls,$srch_api_name,$sl_day_wise,$from_dt,$to_dt,$astn_branch_code){
$app_service_track_log = "app_service_track_log";	
$customer_master = "customer_master";
$branch_master = "branch_master";	
$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "usage_log_list".$curr_date.".csv";


$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$srch_api_dtls = $srch_api_dtls ? trim($srch_api_dtls) : "";
$srch_api_name = $srch_api_name ? trim($srch_api_name) : "";
$sl_day_wise = $sl_day_wise ? trim($sl_day_wise) : "";
$from_dt = $from_dt ? trim($from_dt) : "";
$to_dt = $to_dt ? trim($to_dt) : "";
$astn_branch_code = $astn_branch_code ? trim($astn_branch_code) : "";
$whr_str = "";
$search_array = array("srch_api_dtls"=>$srch_api_dtls,"srch_api_name"=>$srch_api_name,"astn_branch_code"=>$astn_branch_code,"daywise"=>array("sl_day_wise"=>$sl_day_wise,"from_dt"=>$from_dt,"to_dt"=>$to_dt));
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_api_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($app_service_track_log.`appservice_name` like '%$search_array_val%' or $customer_master.`dns_customer_code` like '%$search_array_val%' or $customer_master.`customer_name` like '%$search_array_val%' or $customer_master.`customer_id` like '%$search_array_val%') ";
			$new_qry_string_filtered .= "&srch_api_dtls=".$search_array_val;
		}
	}
	else if($search_array_key=="srch_api_name"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand $app_service_track_log.`appservice_name`='$search_array_val'";
			$new_qry_string_filtered .= "&srch_api_name=".$search_array_val;
		}
	}
	else if($search_array_key=="astn_branch_code"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $customer_master.`branch_code` = '$search_array_val' ";
			$new_qry_string_filtered .= "&astn_branch_code=".$search_array_val;
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
			   $whr_str .= "$aand $app_service_track_log.`datetime` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
			   $new_qry_string_filtered .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}
			}else if($the_from_dt!="" && $the_to_dt==""){
				$whr_str .= "$aand $app_service_track_log.`datetime` >= '".$the_from_dt." ".$frm_hrs."' ";
				$new_qry_string_filtered .= "&from_dt=".$the_from_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}
			}else if($the_from_dt=="" && $the_to_dt!=""){
				$whr_str .= "$aand $app_service_track_log.`datetime` <= '".$the_to_dt." ".$to_hrs."' ";
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
				$whr_str .= "$aand $app_service_track_log.`datetime` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
			}else if($the_sl_day_wise=="Yesterday"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand $app_service_track_log.`datetime` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
			
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
/*$qry = "select $webservice_track_log.`customer_code`,$webservice_track_log.`webservice_name`,count($webservice_track_log.`webservice_name`) as `tot_count`,$customer_master.`dns_customer_code`,$customer_master.`customer_name`,$customer_master.sales_office,$customer_master.sales_office_desc,SUBSTRING($webservice_track_log.datetime,1,10) AS hit_date from 
		$webservice_track_log left join $customer_master on $webservice_track_log.`customer_code`=$customer_master.`customer_code`
		group by SUBSTRING($webservice_track_log.datetime,1,10),$webservice_track_log.`customer_code`,$webservice_track_log.`webservice_name` order by SUBSTRING($webservice_track_log.datetime,1,10) DESC,$webservice_track_log.`customer_code` asc ";
	$qry = "select $webservice_track_log.`customer_code`,$webservice_track_log.`webservice_name`,$customer_master.`customer_name`,$customer_master.region,$branch_master.branch_name,$webservice_track_log.datetime from 
		$webservice_track_log inner join $customer_master on $webservice_track_log.`customer_code`=$customer_master.`customer_id` inner join $branch_master ON $customer_master.`branch_code`=$branch_master.branch_code $new_whr_str 
		 order by $webservice_track_log.datetime DESC,$webservice_track_log.`customer_code` asc ";*/
		
	
	$qry = "select $app_service_track_log.`customer_code`,$app_service_track_log.`appservice_name`,$customer_master.`customer_name`,$customer_master.region,$branch_master.branch_name,$app_service_track_log.datetime from 
		$app_service_track_log inner join $customer_master on $app_service_track_log.`customer_code`=$customer_master.`customer_id` inner join $branch_master ON $customer_master.`branch_code`=$branch_master.branch_code $new_whr_str 
		 order by $app_service_track_log.datetime DESC,$app_service_track_log.`customer_code` asc";
	$sql = mysql_query($qry);
$output .= '"Dealer SAP Code","Dealer Name","Branch","Region","Activity","Date & Time"';
$output .="\n";
// Get Records from the table
while ($row1 = mysql_fetch_assoc($sql)) {
		$dealer_id = $row1["customer_code"];
		$customer_name = $row1["customer_name"];
		$appservice_name = $row1["appservice_name"];
		$branch_name = $row1["branch_name"];
		$region = $row1["region"];
		$datetime = $row1["datetime"];
		
$output .='"'.$dealer_id.'","'.$customer_name.'","'.$branch_name.'","'.$region.'","'.$appservice_name.'","'.$datetime.'"';
$output .="\n";
}
mysql_close();
$filename = $the_file_name;
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
header("Content-Transfer-Encoding: UTF-8");
header('Pragma: no-cache');    
header('Expires: 0');
echo $output;
exit;
}
?>
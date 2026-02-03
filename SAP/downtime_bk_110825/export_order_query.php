<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & E_NOTICE & E_DEPRECATED);
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$start_user_type = $_SESSION["start_user_type"];
$order_query = "order_query";
$customer_master="customer_master";
$branch_master = "branch_master";
$curr_date = date("jS_M_Y_h_m_s_A");
$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$the_file_name = "order_query_report_" . $curr_date . ".csv";
$new_qry_string_filtered = "";
$export_filtered_str = "";
$trn_branch_id = $_GET["trn_branch_id"] ? addslashes(trim($_GET["trn_branch_id"])) : "";
$sl_day_wise = $_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : "";
$from_dt = $_GET["from_dt"] ? addslashes(trim($_GET["from_dt"])) : "";
$to_dt = $_GET["to_dt"] ? addslashes(trim($_GET["to_dt"])) : "";
$srch_order_dtls = $_GET["srch_order_dtls"] ? addslashes(trim($_GET["srch_order_dtls"])) : "";
$whr_str = "";
$export_filtered_str = "";
$search_array = array("trn_branch_id"=>$trn_branch_id,"srch_order_dtls"=>$srch_order_dtls,"daywise"=>array("sl_day_wise"=>$sl_day_wise,"from_dt"=>$from_dt,"to_dt"=>$to_dt));
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_order_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand ($order_query.`order_id` like '%$search_array_val%' or $order_query.`customer_code` like '%$search_array_val%'  or $customer_master.`customer_name` like '%$search_array_val%' or $order_query.`prod_name` like '%$search_array_val%') ";
			$new_qry_string_filtered .= "&srch_cust_dtls=".$search_array_val;
		}
	}
	else if($search_array_key=="trn_branch_id"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $customer_master.`branch_code`='$search_array_val' ";	
			$new_qry_string_filtered .= "&trn_branch_id=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&trn_branch_id=".$search_array_val;
			}else{
				$export_filtered_str .= "&trn_branch_id=".$search_array_val;
			}		
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
			   $whr_str .= "$aand $order_query.`date_and_time` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
			   $new_qry_string_filtered .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}
			}else if($the_from_dt!="" && $the_to_dt==""){
				$whr_str .= "$aand $order_query.`date_and_time` >= '".$the_from_dt." ".$frm_hrs."' ";
				$new_qry_string_filtered .= "&from_dt=".$the_from_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}
			}else if($the_from_dt=="" && $the_to_dt!=""){
				$whr_str .= "$aand $order_query.`date_and_time` <= '".$the_to_dt." ".$to_hrs."' ";
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
				$whr_str .= "$aand $order_query.`date_and_time` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
			}else if($the_sl_day_wise=="Yesterday"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand $order_query.`date_and_time` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
			
			}
		}
	}
}
if($whr_str!=""){
	$new_whr_str = "and ".$whr_str;
}else{
	$new_whr_str ="";
}

$sql_pg ="select $order_query.*,$customer_master.`customer_name`,$customer_master.`branch_code` from $order_query left join $customer_master on $order_query.`customer_code`=$customer_master.`customer_id` where $order_query.`order_id`!=''  $new_whr_str order by $order_query.`id` desc";
$qry = $sql_pg;
$sql = mysql_query($qry);
// Get The Field Name
$slno_cnt = 1;
$output .= '"Order Id","DATE TIME","SAP Code","Customer Name","Linked Dealer Code","Linked Dealer Name","Branch name","Prod name","Date of Requirement","Quantity in Bags","Remarks","Dealer Feedback","Dealer Remarks"';
$output .= "\n";
// Get Records from the table
while ($row1 = mysql_fetch_array($sql)) {
		$sl_no = $row1["id"];
		$order_id = $row1["order_id"];
		$date_and_time = $row1["date_and_time"];
		$customer_code = $row1["customer_code"];
		$customer_name = $row1["customer_name"];
		$linked_dealer_code = $row1["linked_dealer_code"];
		$prod_name = $row1["prod_name"];
		$qty_bags = $row1["qty_bags"];
		$date_of_lifting = $row1["date_of_lifting"];
		$remarks = $row1["remarks"];
		$status_from_app = $row1["status_from_app"];
		$status_remarks = $row1["status_remarks"];
		$branch_code = $row1["branch_code"];
	
	    $sqlftcbrncn = "select `branch_name` from $branch_master where branch_code='".$branch_code."'";
		$res1dftftcbrncn = mysql_query($sqlftcbrncn);
		$rowbranch=mysql_fetch_array($res1dftftcbrncn);
		$branch_name=$rowbranch['branch_name'];
		
		$sqllinkeddealer = "select customer_id,customer_name from $customer_master where customer_code='".$linked_dealer_code."'";	
		$reslinkeddealer = mysql_query($sqllinkeddealer);
		$rowlinkeddealer= mysql_fetch_assoc($reslinkeddealer);
		$linked_dealer_id=$rowlinkeddealer['customer_id'];
		$linked_dealer_name=$rowlinkeddealer['customer_name'];

	$output .= '"' .$order_id. '","' .$date_and_time. '","' . $customer_code . '","' . $customer_name . '","' . $linked_dealer_id . '","' . $linked_dealer_name . '","'. $branch_name . '","' . $prod_name . '","' . $date_of_lifting . '","' . $qty_bags . '","' . $remarks . '","' . $status_from_app . '","' . $status_remarks . '"';
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

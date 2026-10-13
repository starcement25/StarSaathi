<?php
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";

$gst_table = "customer_gst_documents";
$customer_master = "customer_master";
$branch_master = "branch_master";
$customer_destination = "customer_destination";
$destination_master = "destination_master";

/*---------SAME FILTERS AS gst_document_list.php SO THE EXPORT MATCHES THE SCREEN----------*/
$srch_dlr_dtls = $_GET["srch_dlr_dtls"] ? addslashes(trim($_GET["srch_dlr_dtls"])) : "";
$sl_cust_type = $_GET["sl_cust_type"] ? addslashes(trim($_GET["sl_cust_type"])) : "";
$srch_gst_no = $_GET["srch_gst_no"] ? addslashes(trim($_GET["srch_gst_no"])) : "";
$whr_str = "";
$search_array = array("srch_dlr_dtls"=>$srch_dlr_dtls,"sl_cust_type"=>$sl_cust_type,"srch_gst_no"=>$srch_gst_no);
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_dlr_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($gst_table.`customer_id` like '%$search_array_val%' or $customer_master.`customer_name` like '%$search_array_val%' or $customer_master.`phone_no` like '%$search_array_val%') ";
		}
	}else if($search_array_key=="sl_cust_type"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $gst_table.`cust_type`='$search_array_val' ";
		}
	}else if($search_array_key=="srch_gst_no"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $gst_table.`gst_no` like '%$search_array_val%' ";
		}
	}
}
if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}

$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "gst_document_list_".$curr_date.".csv";

$qry = "select
	$gst_table.`customer_id` as `Customer ID`,
	$customer_master.`customer_name` as `Customer Name`,
	$branch_master.`branch_name` as `Branch Name`,
	(select group_concat(distinct $destination_master.`destination_name` separator ', ') from $customer_destination left join $destination_master on $destination_master.`destination_code`=$customer_destination.`destination_code` where $customer_destination.`customer_code`=$customer_master.`customer_code`) as `Destination Name`,
	$customer_master.`region` as `Region`,
	$customer_master.`phone_no` as `Phone`,
	$gst_table.`cust_type` as `Customer Type`,
	$gst_table.`gst_no` as `GST No`,
	(case when trim($gst_table.`pan_no`)!='' then $gst_table.`pan_no` when length(trim($gst_table.`gst_no`))=15 then substring(trim($gst_table.`gst_no`),3,10) else $gst_table.`pan_no` end) as `PAN No`,
	(case when $gst_table.`is_any`='1' then 'GST Available' when $gst_table.`is_any`='0' then 'GST Not Available' else '--' end) as `GST Status`,
	$gst_table.`upload_doc` as `Document`,
	$gst_table.`created_date` as `Created Date`
	from $gst_table
	left join $customer_master on $customer_master.`customer_id`=$gst_table.`customer_id`
	left join $branch_master on $customer_master.`branch_code`=$branch_master.`branch_code`
	$new_whr_str
	order by $gst_table.`created_date` desc";

$sql = mysql_query($qry);
$columns_total = mysql_num_fields($sql);
$output = "";
$headings = array();

// Get The Field Name
for ($i = 0; $i < $columns_total; $i++) {
	$heading = mysql_field_name($sql, $i);
	$headings[$i] = $heading;
	$output .= '"' . str_replace('"', '""', $heading) . '",';
}
$output .= "\n";

// Get Records from the table
while ($row = mysql_fetch_array($sql)) {
	for ($i = 0; $i < $columns_total; $i++) {
		$the_val = $row["$i"];
		if ($headings[$i] == "Created Date" && $the_val != "" && $the_val != "0000-00-00 00:00:00" && $the_val != "0000-00-00") {
			$the_val = date("d-m-Y h:i A", strtotime($the_val));
		}
		if ($headings[$i] == "Document") {
			$the_val = trim($the_val);
			if ($the_val != "") {
				$the_val = '=HYPERLINK("' . $the_val . '","View Doc")';
			} else {
				$the_val = "--";
			}
		}
		$output .= '"' . str_replace('"', '""', $the_val) . '",';
	}
	$output .= "\n";
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

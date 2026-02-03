<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & E_NOTICE & E_DEPRECATED);
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$customer_broker_relation = "customer_broker_relation";
$broker_master = "broker_master";
$employee_master = "employee_master";
$customer_master = "customer_master";
$changepassword = "changepassword";

$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "sales_promoter_list_".$curr_date.".csv";



$new_qry_string_filtered = "";
$export_filtered_str = "";
$srch_dlr_dtls = $_GET["srch_dlr_dtls"] ? addslashes(trim($_GET["srch_dlr_dtls"])) : "";
$whr_str = "";
$search_array = array("srch_dlr_dtls"=>$srch_dlr_dtls);
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_dlr_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand (`contact_person` like '%$search_array_val%' or `dns_broker_id` like '%$search_array_val%' or `broker_id` like '%$search_array_val%' or `broker_name` like '%$search_array_val%' or `phone_no` like '%$search_array_val%' ) ";
			$new_qry_string_filtered .= "&srch_dlr_dtls=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&srch_dlr_dtls=".$search_array_val;
			}else{
				$export_filtered_str .= "&srch_dlr_dtls=".$search_array_val;
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
$qry = "select * from $broker_master $new_whr_str order by `broker_name` asc";
$sql = mysql_query($qry);
// Get The Field Name
$slno_cnt = 1;
$output .= '"SP_ID","Name","Contact_Person","Mobile","Email","App_Version"';
$output .="\n";
// Get Records from the table


while ($row1 = mysql_fetch_array($sql)) {

$broker_id = $row1["broker_id"];
$dns_broker_id = $row1["dns_broker_id"];
$broker_name = $row1["broker_name"] ? str_replace('"', '""',trim($row1["broker_name"])) : "";
$contact_person = $row1["contact_person"] ? str_replace('"', '""',trim($row1["contact_person"])) : "";
$mail_id = $row1["mail_id"] ? str_replace('"', '""',trim($row1["mail_id"])) : "";
$phone_no = $row1["phone_no"] ? str_replace('"', '""',trim($row1["phone_no"])) : "";
$app_version = $row1["app_version"] ? str_replace('"', '""',trim($row1["app_version"])) : "";


$output .= '"'.$dns_broker_id.'","'.$broker_name.'","'.$contact_person.'","'.$phone_no.'","'.$mail_id.'","'.$app_version.'"';

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
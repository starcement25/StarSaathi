<?php
ini_set('memory_limit', '9999M');
set_time_limit(0);
include "star_connection.php";
areaApiLogCsvfile($conn);
function areaApiLogCsvfile($conn){
$webservice_track_log = "webservice_track_log";
$customer_master = "customer_master";
$branch_master = "branch_master";	
$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "usage_log_list".$curr_date.".csv";
$output = "";
/*$qry = "select $webservice_track_log.`customer_code`,$webservice_track_log.`webservice_name`,count($webservice_track_log.`webservice_name`) as `tot_count`,$customer_master.`dns_customer_code`,$customer_master.`customer_name`,$customer_master.sales_office,$customer_master.sales_office_desc,SUBSTRING($webservice_track_log.datetime,1,10) AS hit_date from 
		$webservice_track_log left join $customer_master on $webservice_track_log.`customer_code`=$customer_master.`customer_code`
		group by SUBSTRING($webservice_track_log.datetime,1,10),$webservice_track_log.`customer_code`,$webservice_track_log.`webservice_name` order by SUBSTRING($webservice_track_log.datetime,1,10) DESC,$webservice_track_log.`customer_code` asc ";*/
$qry = "select $webservice_track_log.`customer_code`,$webservice_track_log.`webservice_name`,$customer_master.`customer_name`,$customer_master.region,$branch_master.branch_name,$webservice_track_log.datetime from 
		$webservice_track_log inner join $customer_master on $webservice_track_log.`customer_code`=$customer_master.`customer_id` inner join $branch_master ON $customer_master.`branch_code`=$branch_master.branch_code $new_whr_str 
		 order by $webservice_track_log.datetime DESC,$webservice_track_log.`customer_code` asc ";
		
$sql = mysql_query($qry);
$output .= '"Dealer SAP Code","Dealer Name","Branch","Region","Activity","Date & Time"';
$output .="\n";
// Get Records from the table
while ($row1 = mysql_fetch_assoc($sql)) {
		$dealer_id = $row1["customer_code"];
		$customer_name = $row1["customer_name"];
		$webservice_name = $row1["webservice_name"];
		$branch_name = $row1["branch_name"];
		$region = $row1["region"];
		$datetime = $row1["datetime"];
		

$output .='"'.$dealer_id.'","'.$customer_name.'","'.$branch_name.'","'.$region.'","'.$webservice_name.'","'.$datetime.'"';
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
<?php
ini_set('memory_limit', '9999M');
set_time_limit(0);
include "star_connection.php";
startCreatSPdestCsvfile();
function startCreatSPdestCsvfile(){
$destination_master = "destination_master";
$broker_master = "broker_master";
$sp_destination = "sp_destination";
$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "sp_destination_".$curr_date.".csv";
$output = "";
$output .= '"SP Code","SP Name","Destination Code","Destination Name","Status"';
$output .="\n";
$qry = "select $sp_destination.`sl_no`,
$sp_destination.`acedns` as `acedns_sts`,$broker_master.`broker_name`,$broker_master.`broker_id`,$broker_master.`dns_broker_id`,$destination_master.`destination_code`,$destination_master.`destination_name` from $sp_destination left join $broker_master on $sp_destination.`broker_id`=$broker_master.`broker_id` left join $destination_master ON $sp_destination.`destination_code`=$destination_master.`destination_code` order by $broker_master.`broker_name` asc";
$sql = mysql_query($qry);
while ($row1 = mysql_fetch_array($sql)) {
$dns_broker_id = $row1["dns_broker_id"];
$broker_id = $row1["broker_id"];
$broker_name = $row1["broker_name"];
$destination_code = $row1["destination_code"];
$destination_name = $row1["destination_name"];
$acedns = $row1["acedns_sts"];
	$output .= '"'.$dns_broker_id.'","'.$broker_name.'","'.$destination_code.'","'.$destination_name.'","'.$acedns.'"';
	$output .="\n";
}
// Download the file
$filename = $the_file_name;
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');    
header('Expires: 0');
echo $output;
exit;
}
mysql_close();
?>
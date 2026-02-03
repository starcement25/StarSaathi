<?php

ini_set('memory_limit', '999M');

set_time_limit(0);

include "star_connection.php";

		startnotificationCsvfile($get_fetch_type);

function startnotificationCsvfile($get_fetch_type){

$table_name = "auto_notification_log";

$curr_date = date("jS_M_Y_h_m_s_A");

$qry = "select $table_name.`cust_dns_code`,$table_name.`cust_name`,$table_name.`message_type`,$table_name.`message_sent_status`,$table_name.`error_message`,$table_name.`device_type`,$table_name.`message_text`,$table_name.`sent_datetime` from $table_name order by $table_name.`sent_datetime` asc";

//$qry = "select `dns_customer_code`,`customer_name`,`acedns`,`phone_no` from $customer_master $where_qry order by `customer_name` asc";

$sql = mysql_query($qry);

$columns_total = mysql_num_fields($sql);



// Get The Field Name



for ($i = 0; $i < $columns_total; $i++) {

$heading = mysql_field_name($sql, $i);

$output .= '"'.$heading.'",';

}

$output .="\n";

// Get Records from the table



while ($row = mysql_fetch_array($sql)) {

for ($i = 0; $i < $columns_total; $i++) {

$output .='"'.$row["$i"].'",';

}

$output .="\n";

}



// Download the file

$the_file_name = "auto_notification_log_".$curr_date.".csv";

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
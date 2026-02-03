<?php
//include 'star_connection.php';
// update end time using global last id
$curr_datetime = date('Y-m-d H:i:s');
$remark = 'Process finished';
$last_id = $GLOBALS['cron_last_id'];

$sql = "UPDATE cron_update_table 
        SET last_end_datetime='$curr_datetime', remark='$remark'
        WHERE id='$last_id'";
mysql_query($sql);

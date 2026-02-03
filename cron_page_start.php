<?php
//include 'star_connection.php';

// get current page name
$process_name = basename($_SERVER['PHP_SELF']);

// current datetime
$curr_datetime = date('Y-m-d H:i:s');

// insert new log record when process starts
$remark = 'Process started';
$sql = "INSERT INTO cron_update_table (process_name, last_start_datetime, remark)
        VALUES ('$process_name', '$curr_datetime', '$remark')";
mysql_query($sql);

// get last inserted id and store in global variable
$GLOBALS['cron_last_id'] = mysql_insert_id();
<?php

function log_cron_start($process_name, $conn) {
    $process_name = mysql_real_escape_string($process_name);
    $now = date("Y-m-d H:i:s");

   
    $checkSql = "SELECT idPrimary FROM cron_update_table WHERE process_name = '$process_name' LIMIT 1";
    $result = mysql_query($checkSql, $conn);

    if (mysql_num_rows($result) > 0) {
       
        $updateSql = "UPDATE cron_update_table 
                      SET last_start_datetime = '$now', remark = 'Process Start' 
                      WHERE process_name = '$process_name'";
        mysql_query($updateSql, $conn);
    } else {
       
        $insertSql = "INSERT INTO cron_update_table 
                      (process_name, last_start_datetime, remark) 
                      VALUES ('$process_name', '$now', 'Process Start')";
        mysql_query($insertSql, $conn);
    }
}

function log_cron_end($process_name, $conn) {
    $process_name = mysql_real_escape_string($process_name);
    $now = date("Y-m-d H:i:s");

    $updateSql = "UPDATE cron_update_table 
                  SET last_end_datetime = '$now', remark = 'Process End' 
                  WHERE process_name = '$process_name'";
    mysql_query($updateSql, $conn);
}
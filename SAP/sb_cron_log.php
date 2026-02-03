<?php
//include "star_connection.php";
function log_cron_start($page) {
   

    $page = mysql_real_escape_string($page);
    $sql = "INSERT INTO sb_cron_log (page_name, start_time) VALUES ('$page', NOW())";
    mysql_query($sql);
    $insert_id = mysql_insert_id();
    
    return $insert_id;
   
}
function log_cron_end($log_id) {
   

    $sql = "UPDATE sb_cron_log SET end_time = NOW() WHERE id = $log_id";
    if (!mysqli_query($sql)) {
        die("End log failed: " . mysql_error($conn));
    }

  
}
?>
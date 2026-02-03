<?php
include "star_connection.php"; // your database connection

$allocation_id = $_POST['allocation_id'];
$erporder_no = $_POST['erporder_no'];

$sql = "UPDATE downtime_T_APPERPDO SET ERPORDERNO = '".$erporder_no."' WHERE id = '".$allocation_id."'";
//echo"<pre>";print_r($sql);die;
$res=mysql_query($sql);

if ($res) {
    echo 'success';
} else {
    echo 'error';
}
?>

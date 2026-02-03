<?php
require("include/config.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_POST['emp_code'];
$deviceId=$_POST['deviceId'];
//$sqlquery="select * from employee_master";

$sqlselect="SELECT is_delete FROM dbbackupcheck  WHERE emp_code='".$emp_code."' and device_id='".$deviceId."'";
$rsselect=mysql_query($sqlselect);
$count=mysql_num_rows($rsselect);
$rowselect=mysql_fetch_array($rsselect);
$is_delete=$rowselect['is_delete'];

$sqlupdate="UPDATE dbbackupcheck SET is_delete='0' WHERE emp_code='".$emp_code."' and device_id='".$deviceId."'";
$rsupdate=mysql_query($sqlupdate);

echo $is_delete;
?>

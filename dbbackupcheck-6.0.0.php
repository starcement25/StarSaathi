<?php
require("include/config.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_POST['emp_code'];
$deviceId=$_POST['deviceId'];
//$sqlquery="select * from employee_master";

$sqlselect="SELECT * FROM dbbackupcheck  WHERE device_id='".$deviceId."'";
$rsselect=mysql_query($sqlselect);
$count=mysql_num_rows($rsselect);
$rowselect=mysql_fetch_array($rsselect);
	
if($count==0)
{		
	$sqlInsert="INSERT INTO dbbackupcheck SET
				emp_code='".$emp_code."',
				device_id='".$deviceId."',
				is_checked='0'";
	 if(mysql_query($sqlInsert))
	 {
		 echo '0';
	 }
	 else
	 {
		 echo '3';
	 }
}
else
{
	echo $rowselect['is_checked'];
}
mysql_close($link);
?>

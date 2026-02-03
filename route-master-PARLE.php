<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$sqlquery="select route_code from route_master WHERE route_code NOT LIKE 'N%' ORDER BY route_name ASC";
$result = mysql_query($sqlquery);
$count=mysql_num_rows($result);
	
	while($rowroute = mysql_fetch_array($result))
	{
		$route_code=$rowroute['route_code'];
		$sqlcustomer="SELECT DISTINCT emp_code FROM customer_master WHERE route_code='".$route_code."'";
		$rscustomer=mysql_query($sqlcustomer);
		$rowcustomer=mysql_fetch_array($rscustomer);
		$emp_code=$rowcustomer['emp_code'];
		$sqlupdate="UPDATE route_master SET emp_code='".$emp_code."' WHERE route_code='".$route_code."'";
		mysql_query($sqlupdate);
	}
?>

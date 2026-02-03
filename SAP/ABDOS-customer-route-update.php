<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_ABDOS");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);
$sqlquery="SELECT customer_code FROM customer_master";
$result = mysql_query($sqlquery);
$count=mysql_num_rows($result);
	if($count>0){
		while($rowcustomer = mysql_fetch_array($result))
		{
			$sqlcustomerroute="SELECT route_code FROM customer_route_emp_relation WHERE customer_code='".$rowcustomer['customer_code']."'";
			$rscustomerroute=mysql_query($sqlcustomerroute);
			$cntcustomerroute=mysql_num_rows($rscustomerroute);
			if($cntcustomerroute >0)
			{
				while($rowcustomerroute = mysql_fetch_array($rscustomerroute))
				{
					$sqlupdatecustomerroute="UPDATE customer_master SET  route_code='".$rowcustomerroute['route_code']."' WHERE 
											customer_code='".$rowcustomer['customer_code']."'";
					mysql_query($sqlupdatecustomerroute);						
				}
			}
		}
	}
	echo 'SUCCESS';
?>

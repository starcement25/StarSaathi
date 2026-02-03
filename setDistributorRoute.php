<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_PARLE");
	$link=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysql_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");

	$sqlseldisroute="SELECT route_code,rds_tag,emp_code FROM customer_master WHERE cust_type='R' and emp_code !=''";
	$rsseldisroute=mysql_query($sqlseldisroute);
	while($rowseldisroute=mysql_fetch_array($rsseldisroute))
	{
		$route_code=$rowseldisroute['route_code'];
		$rds_tag=$rowseldisroute['rds_tag'];
		$emp_code=$rowseldisroute['emp_code'];
		$sqlchkdistributorroute="SELECT route_code FROM distributor_route_relation WHERE distributor_code='".$rds_tag."' AND 
					route_code='".$route_code."' AND emp_code='".$emp_code."'";
		$rschkdistributorroute=mysql_query($sqlchkdistributorroute);
		$cntchkdistributorroute=mysql_num_rows($rschkdistributorroute);
		if($cntchkdistributorroute ==0)
		{				
			$sqlinsertdistributorroute="INSERT INTO distributor_route_relation SET distributor_code='".$rds_tag."',
							route_code='".$route_code."',emp_code='".$emp_code."', download_time=CURRENT_TIMESTAMP()";
			//exit();				
			mysql_query($sqlinsertdistributorroute);
		}
	}
	
	
	mysql_close($link);
?>
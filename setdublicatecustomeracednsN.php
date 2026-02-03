<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_EMAMI");
	$link=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysql_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");

	//$sqlupdateproductCP="UPDATE product_master SET TD='5',download_time=current_timestamp() WHERE pack_size='CP' AND vertical_value!='Specialty Fats'";
	$sqlduplicatecustomer="SELECT dns_customer_code,COUNT(dns_customer_code) FROM `customer_master` WHERE cust_type='D' AND acedns='Y' 
							GROUP BY dns_customer_code HAVING COUNT(dns_customer_code) > 1";
	$rsduplicatecustomer=mysql_query($sqlduplicatecustomer);
	while($rowduplicatecustomer=mysql_fetch_array($rsduplicatecustomer))
	{
		$dns_customer_code=$rowduplicatecustomer['dns_customer_code'];
		
		$sqlsorting="SELECT customer_code,emp_code FROM `customer_master` 
					WHERE dns_customer_code='".$dns_customer_code."' AND acedns='Y' 
					order by emp_code,customer_code ASC";
		/*$sqlsorting="SELECT customer_code,emp_code FROM `customer_master` 
					WHERE dns_customer_code='106630' AND acedns='Y' 
					order by emp_code,customer_code ASC";*/			
		$rssorting=mysql_query($sqlsorting);
		while($rowsorting=mysql_fetch_array($rssorting))
		{
			$emp_code=$rowsorting['emp_code'];
			$customer_code=$rowsorting['customer_code'];
			if(($emp_code==$previous_emp_code) && $emp_code!='' && $previous_emp_code!='')
			{
				echo $sqlupdate="UPDATE customer_master SET acedns='N',download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$customer_code."'";
				mysql_query($sqlupdate);
			}
			$previous_emp_code=$rowsorting['emp_code'];
		}
	}
	
	
	
	/*if(mysql_query($sqlupdateproductCP))
	{
		$sqlupdateproductBP="UPDATE product_master SET TD='10',download_time=current_timestamp() WHERE pack_size='BP' 
							AND product_group_code IN('BR2','BR4','BR3','BR9','BR7')";
		if(mysql_query($sqlupdateproductBP))
		{
			echo 'SUCCESS';
		}
		else
		{
			echo 'FAILURE';
		}
	}
	else
	{
		echo 'FAILURE';
	}
	$sqlupdateproductvertical="UPDATE product_master SET TD='0',download_time=current_timestamp() WHERE vertical_value='Specialty Fats'";
	if(mysql_query($sqlupdateproductvertical))
	{
		echo '<br />SUCCESS';
	}
	else
	{
		echo '<br />FAILURE';
	}
	$sqlupdateproductgroup="UPDATE product_master SET TD='0',download_time=current_timestamp() WHERE product_group_code IN('BR18','BR17')";
	if(mysql_query($sqlupdateproductgroup))
	{
		echo '<br />SUCCESS';
	}
	else
	{
		echo '<br />FAILURE';
	}*/
	
	mysql_close($link);
?>
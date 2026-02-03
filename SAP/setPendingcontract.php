<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_EMAMI");
	$link=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysql_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");
		
	$date_val=date('Y-m-d', strtotime(date('Y-m-d') ." -1 day"));
	$sqlcustomer="SELECT customer_code FROM customer_sauda_limit";
	$rscustomer=mysql_query($sqlcustomer);
	while($rowcustomer=mysql_fetch_array($rscustomer))
	{
		$dns_customer_code=$rowcustomer['customer_code'];
		$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".$dns_customer_code."' AND acedns='Y' AND cust_type='D'";
		$rscustomercode=mysql_query($sqlcustomercode);
		$rowcustomercode=mysql_fetch_array($rscustomercode);
		$customer_code=$rowcustomercode['customer_code'];
		$sqlcustomer_booking_qty="SELECT sum(SD.convert_qty_two) as mt_booked FROM sauda_details SD,sauda_header SH 
									WHERE SD.sauda_no=SH.sauda_no AND SH.customer_code='".$customer_code."' 
									AND DATE_FORMAT(SUBSTRING(SD.sauda_no,-14,8),'%Y-%m-%d')='".$date_val."'";
		$rscustomer_booking_qty=mysql_query($sqlcustomer_booking_qty);
		$rowcustomer_booking_qty=mysql_fetch_array($rscustomer_booking_qty);
		
		$sqlupdatesaudalimit="UPDATE customer_sauda_limit SET pending_qty='".$rowcustomer_booking_qty['mt_booked']."',
								download_time=CURRENT_TIMESTAMP() WHERE customer_code='".$dns_customer_code."'";
		mysql_query($sqlupdatesaudalimit);
						
		$sqlupdatecustomer="UPDATE customer_master SET download_time=CURRENT_TIMESTAMP() WHERE 
										dns_customer_code='".$dns_customer_code."'";
		mysql_query($sqlupdatecustomer);
	}
	mysql_close($link);
?>
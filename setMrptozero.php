<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_EMAMI");
	$link=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysql_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");

	$sqlupdatemrp="UPDATE sauda_mrp SET sale_rate=0,basic_rate=0,mrp=0,download_time=CURRENT_TIMESTAMP() WHERE product_code NOT IN(SELECT prod_code FROM product_master WHERE prod_desc LIKE '%LUP%')";
	if(mysql_query($sqlupdatemrp))
	{
		echo 'SUCCESS';
	}
	else
	{
		echo 'FAILURE';
	}
	
	mysql_close($link);
?>
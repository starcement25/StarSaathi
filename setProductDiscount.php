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
	$sqlupdateproductCP="UPDATE product_master SET TD='5',download_time=current_timestamp() WHERE pack_size='CP' 
						AND product_group_code IN('BR2','BR4','BR3','BR9')";
	if(mysql_query($sqlupdateproductCP))
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
	}
	
	mysql_close($link);
?>
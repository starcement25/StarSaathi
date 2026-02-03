<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_SKIPPER");
	$link=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysql_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");
	
	$sqlsel="SELECT trans_id FROM location WHERE length(trans_id) >21";
	$rssel=mysql_query($sqlsel);
	$countsel=mysql_num_rows($rssel);
	while($rowsel=mysql_fetch_array($rssel))
	{
		$trans_id=$rowsel['trans_id'];
		if(substr($trans_id,0,1)=='O')
		{
			$trans_id_part1=substr($trans_id,0,14);
			$trans_id_part2=substr($trans_id,-6,6);
			$trans_id_actual=$trans_id_part1.$trans_id_part2;
			$sqlupdateloc="UPDATE location set trans_id='".$trans_id_actual."' WHERE trans_id='".$trans_id."'";
			mysql_query($sqlupdateloc);
			$sqlupdateoh="UPDATE order_header set order_no='".$trans_id_actual."' WHERE order_no='".$trans_id."'";
			mysql_query($sqlupdateoh);
			$sqlupdateod="UPDATE order_details set order_no='".$trans_id_actual."' WHERE order_no='".$trans_id."'";
			mysql_query($sqlupdateod);
		}
		if(substr($trans_id,0,1)=='N')
		{
			if(substr($trans_id,0,2)=='NO')
			{
				$trans_id_part1=substr($trans_id,0,15);
				$trans_id_part2=substr($trans_id,-6,6);
				$trans_id_actual=$trans_id_part1.$trans_id_part2;
				$sqlupdateloc="UPDATE location set trans_id='".$trans_id_actual."' WHERE trans_id='".$trans_id."'";
				mysql_query($sqlupdateloc);
				$sqlupdateoh="UPDATE order_header set order_no='".$trans_id_actual."' WHERE order_no='".$trans_id."'";
				mysql_query($sqlupdateoh);
			}
			else
			{
				$trans_id_part1=substr($trans_id,0,14);
				$trans_id_part2=substr($trans_id,-6,6);
				$trans_id_actual=$trans_id_part1.$trans_id_part2;
				$sqlupdateloc="UPDATE location set trans_id='".$trans_id_actual."' WHERE trans_id='".$trans_id."'";
				mysql_query($sqlupdateloc);
				$sqlupdatecus="UPDATE customer_master set customer_code='".$trans_id_actual."' WHERE customer_code='".$trans_id."'";
				mysql_query($sqlupdatecus);
				$sqlupdateohcus="UPDATE order_header set customer_code='".$trans_id_actual."' WHERE customer_code='".$trans_id."'";
				mysql_query($sqlupdateohcus);
				$sqlupdatephcus="UPDATE payment_header set customer_code='".$trans_id_actual."' WHERE customer_code='".$trans_id."'";
				mysql_query($sqlupdatephcus);
			}
		}
		if(substr($trans_id,0,1)=='P')
		{
			$trans_id_part1=substr($trans_id,0,14);
			$trans_id_part2=substr($trans_id,-6,6);
			$trans_id_actual=$trans_id_part1.$trans_id_part2;
			$sqlupdateloc="UPDATE location set trans_id='".$trans_id_actual."' WHERE trans_id='".$trans_id."'";
			mysql_query($sqlupdateloc);
			$sqlupdateph="UPDATE payment_header set receipt_id='".$trans_id_actual."' WHERE receipt_id='".$trans_id."'";
			mysql_query($sqlupdateph);
			$sqlupdatepd="UPDATE payment_details set receipt_id='".$trans_id_actual."' WHERE receipt_id='".$trans_id."'";
			mysql_query($sqlupdatepd);

		}
		if(substr($trans_id,0,2)=='CH')
		{
			$trans_id_part1=substr($trans_id,0,15);
			$trans_id_part2=substr($trans_id,-6,6);
			$trans_id_actual=$trans_id_part1.$trans_id_part2;
			$sqlupdateloc="UPDATE location set trans_id='".$trans_id_actual."' WHERE trans_id='".$trans_id."'";
			mysql_query($sqlupdateloc);
		}
		if(substr($trans_id,0,2)=='CI')
		{
			$trans_id_part1=substr($trans_id,0,15);
			$trans_id_part2=substr($trans_id,-6,6);
			$trans_id_actual=$trans_id_part1.$trans_id_part2;
			$sqlupdateloc="UPDATE location set trans_id='".$trans_id_actual."' WHERE trans_id='".$trans_id."'";
			mysql_query($sqlupdateloc);
			$sqlupdatechin="UPDATE check_in_out_details set trans_id='".$trans_id_actual."' WHERE trans_id='".$trans_id."'";
			mysql_query($sqlupdatechin);
		}
	}
	mysql_close($link);
?>
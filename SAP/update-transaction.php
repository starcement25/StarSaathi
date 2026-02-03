<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");

require("include/config-setup.php");
define("DB","acedns_RKBK");
//require("include/dbcon.php");
$link=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysql_select_db(DB,$link) or die("could not connect the database for invalid nick name");

$sqlselect="SELECT order_no,transaction_type FROM `order_header` WHERE order_no LIKE 'O%'";
$rsselect=mysql_query($sqlselect);
while($rowselect=mysql_fetch_array($rsselect))
{
	$order_no=$rowselect['order_no'];
	$transaction_type=$rowselect['transaction_type'];
	$sqlupdate="update order_details set transaction_type='".$transaction_type."' WHERE order_no='".$order_no."' AND transaction_type IS NULL";
	mysql_query($sqlupdate);
}

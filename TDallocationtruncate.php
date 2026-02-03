<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_EMAMI");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);

$sql_truncate_TD = "TRUNCATE table TD_allocation";
$res_truncate_TD = mysql_query($sql_truncate_TD);
if(mysql_query($res_truncate_TD))
	$flag = 1;
else
	$flag = 0;
echo "TD allocation truncated successfully";
?>
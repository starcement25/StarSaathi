<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_acednsproduct");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);

$nick_names = array();

$sql_sauda_carry_forward = "SELECT * FROM sauda_form_details WHERE sauda_allocation_carry_forward = 'no'";
$res_sauda_carry_forward = mysql_query($sql_sauda_carry_forward);
while($row_sauda_carry_forward = mysql_fetch_array($res_sauda_carry_forward))
{
	$nick_names[] = "acedns_".$row_sauda_carry_forward['nick_name'];
}

foreach($nick_names as $key)
{
	if (mysql_select_db($key))
	{
		$sql_truncate_sauda = "TRUNCATE table sauda_allocation";
		$res_truncate_sauda = mysql_query($sql_truncate_sauda);
		if(mysql_query($res_truncate_sauda))
			$flag = 1;
		else
			$flag = 0;
	}
}
echo "sauda_allocation table truncated successfully";
?>
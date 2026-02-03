	<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	
	$linksetup=mysql_connect(SERVER,USER,PASSWORD) or die("Setup Database Connection Error.");
	
	$res = mysql_query("SHOW DATABASES");
	while ($row = mysql_fetch_assoc($res)) {
		//echo $row['Database'] . "\n";
		if($row['Database']!='acedns_acednsproduct' || $row['Database']!='acedns_luxmipouch' || $row['Database']!='acedns_pdf' || 
		$row['Database']!='acedns_emfsurvey' || $row['Database']!='acedns_brcbooking' || $row['Database']!='acedns_automotive')
		{
			mysql_select_db($row['Database'],$linksetup) or die("could not connect the setup database");
			$sqlupdate="ALTER TABLE `attendence` ADD `trans_id` VARCHAR(32) NULL AFTER `emp_code`";//Query need to place here
			mysql_query($sqlupdate,$linksetup);
			mysql_close($linksetup);
		}
	}
?>

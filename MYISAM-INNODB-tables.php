<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("DB","acedns_HALDIRAM");
	define("DESTDB","acedns_HALDIRAMT");	
	$link=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysql_select_db(DB,$link) or die("could not connect the database  source for invalid nick name");

    $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = 'acedns_HALDIRAM' 
        AND ENGINE = 'InnoDB'";

    $rs = mysql_query($sql);
	$tbl_array=array();
    while($row = mysql_fetch_array($rs))
    {
        //$tbl = $row[0];
		array_push($tbl_array,$row[0]);
    }
	mysql_select_db(DESTDB,$link) or die("could not connect the database destination for invalid nick name");
	
	foreach($tbl_array as $tbl)
	{
	  $sql = "ALTER TABLE `$tbl` ENGINE=InnoDB";
      mysql_query($sql);
	}

?>
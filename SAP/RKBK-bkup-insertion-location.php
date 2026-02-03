<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	//define("DBFETCH","acedns_RKBKT");
	//define("DBINSERT","acedns_RKBK");
	$linkfetch=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	$linkinsert=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysql_select_db('acedns_RKBKT',$linkfetch) or die("could not connect the database for invalid nick name");
//E0037 location 20 for 201607 and 11 for 201608
echo $sqllocationbkup="SELECT * FROM location_bkup WHERE emp_code='E0037' AND SUBSTRING(date,1,10) BETWEEN '2016-07-01' AND '2016-08-12'";
$rslocationbkup=mysql_query($sqllocationbkup) or die(mysql_error()." Error in update zero latt longi: ".$sqllocationbkup);
$countlocationbkup=mysql_num_rows($rslocationbkup);
while($rowlocationbkup=mysql_fetch_array($rslocationbkup))
{
	echo $trans_id_bkup=$rowlocationbkup['trans_id'];
	$emp_code_bkup=$rowlocationbkup['emp_code'];
	$date_bkup=$rowlocationbkup['date'];
	$updatetime_bkup=$rowlocationbkup['updatetime'];
	$latt_bkup=$rowlocationbkup['latt'];
	$longi_bkup=$rowlocationbkup['longi'];
	$transferred_bkup=$rowlocationbkup['transferred'];
	mysql_select_db('acedns_RKBK',$linkinsert) or die("could not connect the database for invalid nick name");

	$sqllocationchk="SELECT * from location WHERE trans_id='".$trans_id_bkup."'";
	$rslocationchk=mysql_query($sqllocationchk,$linkinsert) or die(mysql_error()." Error location chk: ".$sqllocationchk);
	$countlocationchk=mysql_num_rows($rslocationchk);

	if($countlocationchk==0)
	{
		$sql  = "insert into location ";
		$sql .= " SET emp_code='".$emp_code_bkup."'";
		$sql .= " , trans_id='".$trans_id_bkup."'";
		$sql .= " , date='".$date_bkup."'";
		$sql .= " , updatetime='".$updatetime_bkup."'";
		$sql .= " , latt='".$latt_bkup."'";
		$sql .= " , longi='".$longi_bkup."'";
		echo $sql .= " , transferred='".$transferred_bkup."'";
		mysql_query($sql,$linkinsert) or die(mysql_error()." location insert: ".$sql);
	}
}
mysql_close($linkfetch);
mysql_close($linkinsert);
?>
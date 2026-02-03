<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	define("DBFETCH","acedns_RKBKT");
	define("DBINSERT","acedns_RKBK");
	$linkfetch=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	$linkinsert=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");

	mysql_select_db(DBFETCH,$linkfetch) or die("could not connect the database for invalid nick name");

$sqllocationbkup="SELECT * FROM goods_in_transit WHERE grn_no LIKE '%E0037%' OR order_no LIKE '%E0037%'";
$rslocationbkup=mysql_query($sqllocationbkup,$linkfetch);
$countlocationbkup=mysql_num_rows($rslocationbkup);
while($rowlocationbkup=mysql_fetch_array($rslocationbkup))
{
	$grn_no_bkup=$rowlocationbkup['grn_no'];
	$despatcher_code_bkup=$rowlocationbkup['despatcher_code'];
	$receiver_code_bkup=$rowlocationbkup['receiver_code'];
	$prod_code_bkup=$rowlocationbkup['prod_code'];
	$despatch_qty_bkup=$rowlocationbkup['despatch_qty'];
	$rec_qty_bkup=$rowlocationbkup['rec_qty'];
	$status_bkup=$rowlocationbkup['status'];
	$transaction_type_bkup=$rowlocationbkup['transaction_type'];
	$order_no_bkup=$rowlocationbkup['order_no'];
	$sale_rate_bkup=$rowlocationbkup['sale_rate'];
	$download_time_bkup=$rowlocationbkup['download_time'];
	
	mysql_select_db(DBINSERT,$linkinsert) or die("could not connect the database for invalid nick name");

	echo $sqllocationchk="SELECT * from goods_in_transit WHERE grn_no='".$grn_no_bkup."' AND prod_code='".$prod_code_bkup."' AND order_no='".$order_no_bkup."'";
	$rslocationchk=mysql_query($sqllocationchk,$linkinsert);
	$countlocationchk=mysql_num_rows($rslocationchk);

	if($countlocationchk==0)
	{
		$sql  = "insert into goods_in_transit ";
		$sql .= " SET grn_no='".$grn_no_bkup."'";
		$sql .= " , despatcher_code='".$despatcher_code_bkup."'";
		$sql .= " , receiver_code='".$receiver_code_bkup."'";
		$sql .= " , prod_code='".$prod_code_bkup."'";
		$sql .= " , despatch_qty='".$despatch_qty_bkup."'";
		$sql .= " , rec_qty='".$rec_qty_bkup."'";
		$sql .= " , status='".$status_bkup."'";
		$sql .= " , transaction_type='".$transaction_type_bkup."'";
		$sql .= " , order_no='".$order_no_bkup."'";
		$sql .= " , sale_rate='".$sale_rate_bkup."'";
		echo $sql .= " , download_time='".$download_time_bkup."'";
		mysql_query($sql,$linkinsert);
	}
}
	
?>
<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	define("DBFETCH","acedns_RKBKT");
	define("DBINSERT","acedns_RKBK");
	$linkfetch=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	$linkinsert=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");

	mysql_select_db(DBFETCH,$linkfetch) or die("could not connect the database for invalid nick name");

$sqllocationbkup="SELECT * FROM order_details WHERE order_no LIKE '%E0037%' AND DATE_FORMAT(SUBSTRING(order_no,-22,8),'%Y-%m-%d') BETWEEN '2016-07-01' AND '2016-08-12'";
$rslocationbkup=mysql_query($sqllocationbkup,$linkfetch);
$countlocationbkup=mysql_num_rows($rslocationbkup);
while($rowlocationbkup=mysql_fetch_array($rslocationbkup))
{
	$row_id_bkup=$rowlocationbkup['row_id'];
	$order_no_bkup=$rowlocationbkup['order_no'];
	$sku_code_bkup=$rowlocationbkup['sku_code'];
	$qty_bkup=$rowlocationbkup['qty'];
	$new_qty_bkup=$rowlocationbkup['new_qty'];
	$mrp_code_bkup=$rowlocationbkup['mrp_code'];
	$TD_bkup=$rowlocationbkup['TD'];
	$premium_bkup=$rowlocationbkup['premium'];
	$VAT_bkup=$rowlocationbkup['VAT'];
	$sale_rate_bkup=$rowlocationbkup['sale_rate'];
	$freight_charge_bkup=$rowlocationbkup['freight_charge'];
	$amount_bkup=$rowlocationbkup['amount'];
	$new_sale_rate_bkup=$rowlocationbkup['new_sale_rate'];
	$transaction_type_bkup=$rowlocationbkup['transaction_type'];
	
	mysql_select_db(DBINSERT,$linkinsert) or die("could not connect the database for invalid nick name");

	$sqllocationchk="SELECT * from order_details WHERE row_id='".$row_id_bkup."'";
	$rslocationchk=mysql_query($sqllocationchk,$linkinsert);
	$countlocationchk=mysql_num_rows($rslocationchk);

	if($countlocationchk==0)
	{
		$sql  = "insert into order_details ";
		$sql .= " SET row_id='".$row_id_bkup."'";
		$sql .= " , order_no='".$order_no_bkup."'";
		$sql .= " , sku_code='".$sku_code_bkup."'";
		$sql .= " , qty='".$qty_bkup."'";
		$sql .= " , new_qty='".$new_qty_bkup."'";
		$sql .= " , mrp_code='".$mrp_code_bkup."'";
		$sql .= " , TD='".$TD_bkup."'";
		$sql .= " , premium='".$premium_bkup."'";
		$sql .= " , VAT='".$VAT_bkup."'";
		$sql .= " , sale_rate='".$sale_rate_bkup."'";
		$sql .= " , freight_charge='".$freight_charge_bkup."'";
		$sql .= " , amount='".$amount_bkup."'";
		$sql .= " , new_sale_rate='".$new_sale_rate_bkup."'";
		$sql .= " , transaction_type='".$transaction_type_bkup."'";
		mysql_query($sql,$linkinsert);
	}
}
	
?>
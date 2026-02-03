<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	define("DBFETCH","acedns_RKBKT");
	define("DBINSERT","acedns_RKBK");
	$linkfetch=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	$linkinsert=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");

	mysql_select_db(DBFETCH,$linkfetch) or die("could not connect the database for invalid nick name");

$sqllocationbkup="SELECT * FROM order_header WHERE order_no LIKE '%E0037%' AND DATE_FORMAT(SUBSTRING(order_no,-22,8),'%Y-%m-%d') BETWEEN '2016-07-01' AND '2016-08-12'";
$rslocationbkup=mysql_query($sqllocationbkup,$linkfetch);
$countlocationbkup=mysql_num_rows($rslocationbkup);
while($rowlocationbkup=mysql_fetch_array($rslocationbkup))
{
	$order_no_bkup=$rowlocationbkup['order_no'];
	$customer_code_bkup=$rowlocationbkup['customer_code'];
	$branch_code_bkup=$rowlocationbkup['branch_code'];
	$destination_code_bkup=$rowlocationbkup['destination_code'];
	$vertical_value_bkup=$rowlocationbkup['vertical_value'];
	$d_instruction_bkup=$rowlocationbkup['d_instruction'];
	$sale_type_bkup=$rowlocationbkup['sale_type'];
	$order_type_bkup=$rowlocationbkup['order_type'];
	$order_value_bkup=$rowlocationbkup['order_value'];
	$TD_bkup=$rowlocationbkup['TD'];
	$tag_distributor_code_bkup=$rowlocationbkup['tag_distributor_code'];
	$transaction_type_bkup=$rowlocationbkup['transaction_type'];
	$VAT_bkup=$rowlocationbkup['VAT'];
	$freight_component_bkup=$rowlocationbkup['freight_component'];
	
		mysql_select_db(DBINSERT,$linkinsert) or die("could not connect the database for invalid nick name");

	$sqllocationchk="SELECT * from order_header WHERE order_no='".$order_no_bkup."'";
	$rslocationchk=mysql_query($sqllocationchk,$linkinsert);
	$countlocationchk=mysql_num_rows($rslocationchk);

	if($countlocationchk==0)
	{
		$sql  = "insert into order_header ";
		$sql .= " SET order_no='".$order_no_bkup."'";
		$sql .= " , customer_code='".$customer_code_bkup."'";
		$sql .= " , branch_code='".$branch_code_bkup."'";
		$sql .= " , destination_code='".$destination_code_bkup."'";
		$sql .= " , vertical_value='".$vertical_value_bkup."'";
		$sql .= " , d_instruction='".$d_instruction_bkup."'";
		$sql .= " , sale_type='".$sale_type_bkup."'";
		$sql .= " , order_type='".$order_type_bkup."'";
		$sql .= " , order_value='".$order_value_bkup."'";
		$sql .= " , TD='".$TD_bkup."'";
		$sql .= " , tag_distributor_code='".$tag_distributor_code_bkup."'";
		$sql .= " , transaction_type='".$transaction_type_bkup."'";
		$sql .= " , VAT='".$VAT_bkup."'";
		$sql .= " , freight_component='".$freight_component_bkup."'";
		mysql_query($sql,$linkinsert);
	}
}
	
?>
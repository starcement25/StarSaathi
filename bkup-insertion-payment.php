<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("SERVERREMOTE","103.241.144.155");
	define("USERREMOTE","acedns_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234");
	
	$db_namearray=array('RUPA','PARLE','ABDOS');
	foreach($db_namearray as $dbval)
	{
		$link=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
		$linkremote=mysql_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE) or die("Database Connection Error remote.");

		//define("DB","acedns_$dbval");	
		//define("DBREMOTE","acedns_$dbval");

		mysql_select_db("acedns_$dbval",$link) or die("could not connect the database for invalid nick name");
		mysql_select_db("acedns_$dbval",$linkremote) or die("could not connect the database for invalid nick name remote");

		if($dbval=='RUPA')
		{
			$date_condition="WHERE DATE_FORMAT(SUBSTRING(receipt_id,-14,14),'%Y-%m-%d %H:%i:%s') >'2016-12-17 23:59:59'";
		}
		if($dbval=='PARLE')
		{
			$date_condition="WHERE DATE_FORMAT(SUBSTRING(receipt_id,-14,14),'%Y-%m-%d %H:%i:%s') >'2016-12-20 23:59:59'";
		}
		if($dbval=='ABDOS')
		{
			$date_condition="WHERE DATE_FORMAT(SUBSTRING(receipt_id,-14,14),'%Y-%m-%d %H:%i:%s') >'2016-12-22 23:59:59'";
		}

		$sqllocationbkup="SELECT * FROM payment_header ".$date_condition;
		$rslocationbkup=mysql_query($sqllocationbkup,$linkremote);
		$countlocationbkup=mysql_num_rows($rslocationbkup);
		while($rowlocationbkup=mysql_fetch_array($rslocationbkup))
		{
			$receipt_id_bkup=$rowlocationbkup['receipt_id'];
			$customer_code_bkup=$rowlocationbkup['customer_code'];
			$amount_bkup=$rowlocationbkup['amount'];
			$cash_cheque_bkup=$rowlocationbkup['cash_cheque'];
			$cheque_no_bkup=$rowlocationbkup['cheque_no'];
			$date_bkup=$rowlocationbkup['date'];
			$bank_bkup=$rowlocationbkup['bank'];
			$rdate_bkup=$rowlocationbkup['rdate'];
			$sale_type_bkup=$rowlocationbkup['sale_type'];
			$p_remark_bkup=$rowlocationbkup['p_remark'];
			$transferred_bkup=$rowlocationbkup['transferred'];
			
			$sqllocationchk="SELECT * from payment_header WHERE receipt_id='".$receipt_id_bkup."'";
			$rslocationchk=mysql_query($sqllocationchk,$link);
			$countlocationchk=mysql_num_rows($rslocationchk);
		
			if($countlocationchk==0)
			{
				$sql  = "insert into payment_header ";
				$sql .= " SET receipt_id='".$receipt_id_bkup."'";
				$sql .= " , customer_code='".$customer_code_bkup."'";
				$sql .= " , amount='".$amount_bkup."'";
				$sql .= " , cash_cheque='".$cash_cheque_bkup."'";
				$sql .= " , cheque_no='".$cheque_no_bkup."'";
				$sql .= " , date='".$date_bkup."'";
				$sql .= " , bank='".$bank_bkup."'";
				$sql .= " , rdate='".$rdate_bkup."'";
				$sql .= " , sale_type='".$sale_type_bkup."'";
				$sql .= " , p_remark='".$p_remark_bkup."'";
				$sql .= " , transferred='".$transferred_bkup."'";
				mysql_query($sql,$link);
			}
		}
		
		$sqlcustomerbkup="SELECT * FROM payment_details ".$date_conditione;
		$rscustomerbkup=mysql_query($sqlcustomerbkup,$linkremote);
		$countcustomerbkup=mysql_num_rows($rscustomerbkup);
		while($rowcustomerbkup=mysql_fetch_array($rscustomerbkup))
		{
			$receipt_id_bkup=$rowcustomerbkup['receipt_id'];
			$invoice_id_bkup=$rowcustomerbkup['invoice_id'];
			$recid_bkup=$rowcustomerbkup['recid'];
			$amount_bkup=$rowcustomerbkup['amount'];
			$discount_bkup=$rowcustomerbkup['discount'];
			
			$sqlcustomerchk="SELECT * from payment_details WHERE receipt_id='".$receipt_id_bkup."'";
			$rslcustomerchk=mysql_query($sqlcustomerchk,$link);
			$countcustomerchk=mysql_num_rows($rslcustomerchk);
			if($countcustomerchk==0)
			{
				$sql  = "insert into payment_details ";
				$sql .= " SET receipt_id='".$receipt_id_bkup."'";
				$sql .= " , invoice_id='".$invoice_id_bkup."'";
				$sql .= " , recid='".addslashes($recid_bkup)."'";
				$sql .= " , amount='".addslashes($amount_bkup)."'";
				$sql .= " , discount='".$discount_bkup."'";
		
				mysql_query($sql,$link);
			}
		}

	mysql_close($link);
	mysql_close($linkremote);
	}
?>
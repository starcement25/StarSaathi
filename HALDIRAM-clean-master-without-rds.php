<?php
    define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_HALDIRAM");
    $link=mysql_connect(SERVER,USER,PASSWORD);
    mysql_select_db(DB,$link);

	$sqlselcustomer="SELECT customer_code FROM `customer_master` WHERE `rds_tag` = '' and `cust_type`='R'";
	$rsselcustomer=mysql_query($sqlselcustomer);
	while($rowselcustomer=mysql_fetch_array($rsselcustomer)){			
		$customer_code=$rowselcustomer['customer_code'];
		
		//Delete order data
		$sqlselorder="SELECT order_no FROM order_header WHERE customer_code='".$customer_code."'";
		$rsselorder=mysql_query($sqlselorder);
		$cntselorder=mysql_num_rows($rsselorder);
		if($cntselorder>0){
			while($rowselorder=mysql_fetch_array($rsselorder))
			{
				$sqldeletelocation="DELETE from location WHERE trans_id='".$rowselorder['order_no']."'";
				mysql_query($sqldeletelocation);
				echo $sqldeleteorderh="DELETE from order_header WHERE order_no='".$rowselorder['order_no']."'";
				mysql_query($sqldeleteorderh);
				$sqldeleteorderp="DELETE from order_details WHERE order_no='".$rowselorder['order_no']."'";
				mysql_query($sqldeleteorderp);
				$sqldeleteprevorder="DELETE from prev_order_counting_master WHERE order_no='".$rowselorder['order_no']."'";
				mysql_query($sqldeleteprevorder);
			}
		}
		//Delete payment data
		$sqlselpayment="SELECT receipt_id FROM payment_header WHERE customer_code='".$customer_code."'";
		$rsselpayment=mysql_query($sqlselpayment);
		$cntselpayment=mysql_num_rows($rsselpayment);
		if($cntselpayment>0){
			while($rowselpayment=mysql_fetch_array($rsselpayment))
			{
				$sqldeletelocationp="DELETE from location WHERE trans_id='".$rowselpayment['receipt_id']."'";
				mysql_query($sqldeletelocationp);
				echo $sqldeletepaymenth="DELETE from payment_header WHERE receipt_id='".$rowselpayment['receipt_id']."'";
				mysql_query($sqldeletepaymenth);
				$sqldeletepaymentp="DELETE from payment_details WHERE receipt_id='".$rowselpayment['receipt_id']."'";
				mysql_query($sqldeletepaymentp);
			}
		}
		//delete self_appraisal
		echo $sqldeleteself="DELETE from self_appraisal_customer_wise WHERE customer_code='".$customer_code."'";
		mysql_query($sqldeleteself);
		//delete customer and distributor route
		echo $sqldeletecustomerroute="delete from customer_route_emp_relation where customer_code='".$customer_code."'";
		mysql_query($sqldeletecustomerroute);
		/*echo $sqldeletedistributorroute="delete from distributor_route_relation where distributor_code='".$customer_code."'";
		mysql_query($sqldeletedistributorroute);*/	
		//delete customer master
		echo $sqldelcustomer="DELETE FROM customer_master WHERE customer_code='".$customer_code."'";
		mysql_query($sqldelcustomer);
		
		$sqlinsertlog="INSERT INTO deleted_customer_log SET customer_code='".$customer_code."',update_time=CURRENT_TIMESTAMP()";
		mysql_query($sqlinsertlog);
	}
	/*else
	{
		echo $successval="Naming convention for Branch master.csv is wrong.";
		exit();
	}*/
	
	echo 'SUCCESS';

?>
<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

$sqlempfunctionality="SELECT functionality,functionality_rel_val FROM employee_master WHERE emp_code='".$emp_code."'";
$rsempfunctionality=mysql_query($sqlempfunctionality);
$rowempfunctionality=mysql_fetch_array($rsempfunctionality);
$functionality_rel_val=$rowempfunctionality['functionality_rel_val'];

$sqlquery="SELECT * FROM customer_product_allocation WHERE customer_code='".$functionality_rel_val."'";
$result = mysql_query($sqlquery);
$count=mysql_num_rows($result);

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

	if($count>0){
		$contentsrowcolumn=$count.'¥'.'7';
		while($rowstockallocation = mysql_fetch_array($result))
		{
			$allocation_id=$rowstockallocation['allocation_id'];
			$customer_code=$rowstockallocation['customer_code'];
			$prod_code=$rowstockallocation['prod_code'];
			$qty=$rowstockallocation['qty'];
			$from_date=$rowstockallocation['from_date'];
			$to_date=$rowstockallocation['to_date'];
			$acedns=$rowstockallocation['acedns'];
			
				$contents  = (($allocation_id!='')?$allocation_id: ' ')."^";
				$contents  .= (($customer_code!='')?$customer_code: ' ')."^";
				$contents  .= (($prod_code!='')?$prod_code: ' ')."^";
				$contents  .= (($qty!='')?$qty: 0)."^";
				$contents  .= (($from_date!='')?$from_date: ' ')."^";
				$contents  .= (($to_date!='')?$to_date: ' ')."^";
				$contents  .= (($acedns!='')?$acedns: ' ');
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/stock-allocation-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=stock_allocation.txt");
	print "$datacontents"; 		
?>

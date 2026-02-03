<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$emp_code=substr($emp_code,0,5);

$sqlempname="SELECT reporting_to,designation FROM employee_master WHERE emp_code='".$emp_code."'";
$rsempname=mysql_query($sqlempname);
$rowempname=mysql_fetch_array($rsempname);
$reporting_to=$rowempname['reporting_to'];
$designation=$rowempname['designation'];

$sqlchkallocationaccess="SELECT flag,get_allocation FROM TD_allocation_access WHERE emp_code='".$emp_code."'";
$rschkallocationaccess=mysql_query($sqlchkallocationaccess);
$rowchkallocationaccess=mysql_fetch_array($rschkallocationaccess);
$allocation_flag=$rowchkallocationaccess['get_allocation'];
//if($allocation_flag=='yes')
//{
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
//}

if($emp_code!='C0007'){
 	$sqlquery="SELECT * FROM TD_allocation WHERE ".$emp_hierarchy_condition."";
 }
 else
 {
	$sqlquery="SELECT * FROM TD_allocation WHERE 1 GROUP BY product_filter_code";
 }
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
		$contentsrowcolumn=$count.'¥'.'3';
		while($rowTD = mysql_fetch_array($result))
		{
			$emp_code=$rowTD['emp_code'];
			$product_filter_code	=$rowTD['product_filter_code'];
			$TD=$rowTD['TD'];
			$contents  = (($emp_code!='')?$emp_code: ' ')."^";
			$contents  .= (($product_filter_code!='')?$product_filter_code: ' ')."^";
			$contents  .= (($TD!='')?$TD: 0);
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		//$datacontents = '0'.'¥'.'0';
		$sqlqueryproductfilter="SELECT product_group_code FROM product_group_master WHERE acedns='Y'";
		$resultproductfilter = mysql_query($sqlqueryproductfilter);
		$countproductfilter=mysql_num_rows($resultproductfilter);
		$contentsrowcolumn=$countproductfilter.'¥'.'3';

		while($rowproductfilter = mysql_fetch_array($resultproductfilter))
		{
			$product_filter_code	=$rowproductfilter['product_group_code'];
			$TD=0;
				$contents  = (($emp_code!='')?$emp_code: ' ')."^";
				$contents  .= (($product_filter_code!='')?$product_filter_code: ' ')."^";
				$contents  .= $TD."^";
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/TD-allocation-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/sauda-allocation-txt.php?nick_name=$nick_name&emp_code=$emp_code";
	$insertPos=0;  // variable for saving 
	while (!feof($file)) {
		$line=fgets($file);
		if (strpos($line, 'http://')!==false) {
			$insertPos=ftell($file);
			$newline =  $newuser;
		}
		else
		{
			$newline.=$line;   // append existing data with new data of user
		}
	}
	fseek($file,$insertPos);   // move pointer to the file position where we saved above 
	fwrite($file, $newline);
	fclose($file);*/	

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=TD_allocation.txt");
	print "$datacontents"; 	
	mysql_close($link);	
?>

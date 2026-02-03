<?php
ini_set('memory_limit', '-1');
set_time_limit(1000);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];

	$sqlquery="SELECT * FROM `prospective_customer_master`";	
	$result = mysql_query($sqlquery);
	$count=mysql_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'9';
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time."\n";
		while($rowprospectivecustomermaster = mysql_fetch_array($result))
		{
			
			$contents  = (($rowprospectivecustomermaster['emp_code']!='')?$rowprospectivecustomermaster['emp_code']: ' ')."^";
			$contents  .= (($rowprospectivecustomermaster['customer_code']!='')?$rowprospectivecustomermaster['customer_code']: ' ')."^";
			$contents  .= (($rowprospectivecustomermaster['customer_name']!='')?$rowprospectivecustomermaster['customer_name']: ' ')."^";
			$contents  .= (($rowprospectivecustomermaster['address']!='')?$rowprospectivecustomermaster['address']: ' ')."^";
			$contents  .= (($rowprospectivecustomermaster['pin']!='')?$rowprospectivecustomermaster['pin']: ' ')."^";
			$contents  .= (($rowprospectivecustomermaster['area']!='')?$rowprospectivecustomermaster['area']: ' ')."^";
			$contents  .= (($rowprospectivecustomermaster['phone_no']!='')?$rowprospectivecustomermaster['phone_no']: ' ')."^";
			$contents  .= (($rowprospectivecustomermaster['cust_type']!='')?$rowprospectivecustomermaster['cust_type']: ' ')."^";
			$contents  .= (($rowprospectivecustomermaster['tagged_customer_code']!='')?$rowprospectivecustomermaster['tagged_customer_code']: ' ');

			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$last_update_time=str_replace('?','',$last_update_time);
		$data_download_time=str_replace('?','',$data_download_time);
		if(strtotime($data_download_time)>=strtotime($last_update_time))
		{
			$datacontents = '0'.'¥'.'0';
		}
		else
		{
			$datacontents = '0'.'¥'.'9';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/prospective_customer_master.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=prospective_customer_master.txt");
	print "$datacontents";	
	mysql_close($link);
?>

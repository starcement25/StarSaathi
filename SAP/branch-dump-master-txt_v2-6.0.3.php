<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];
$user_type = $_REQUEST['user_type'] ? strtolower(trim($_REQUEST['user_type'])) : "";
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);
$branch_dump = "branch_dump";
	
	$sqlempbranch="SELECT branch_code FROM customer_master WHERE customer_code='".$emp_code."'";
	$rsempbranch=mysql_query($sqlempbranch);
	$rowempbranch=mysql_fetch_array($rsempbranch);
	$branch_code=$rowempbranch['branch_code'];
	$branch_value_array=explode(',',$branch_code);
	$branch_value_final = "'".implode("','", $branch_value_array)."'";
	$condition_branch=' and  `branch_code` IN ('.$branch_value_final.')';

$sqlquery="SELECT `branch_code`,`dump_code`,`dump_name`,`acedns`,`is_plant`,`download_time` FROM $branch_dump
 WHERE `acedns`='Y' and `acedns`!='' and `is_plant`!='Y' and `is_plant`!='' ".$condition_branch." GROUP BY dump_name ORDER BY `dump_name` ASC";
	
$result = mysql_query($sqlquery);
$count=mysql_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'6';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

		while($rowdestination = mysql_fetch_array($result))
		{
			$contents  = (($rowdestination['branch_code']!='')?$rowdestination['branch_code']: ' ')."^";
			$contents  .= (($rowdestination['dump_code']!='')?$rowdestination['dump_code']: ' ')."^";
			$contents  .= (($rowdestination['dump_name']!='')?$rowdestination['dump_name']: ' ')."^";
			$contents  .= (($rowdestination['acedns']!='')?$rowdestination['acedns']: ' ')."^";
			$contents  .= (($rowdestination['is_plant']!='')?$rowdestination['is_plant']: ' ')."^";
			$contents  .= (($rowdestination['download_time']!='')?$rowdestination['download_time']: ' ');
			
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
			$datacontents = '0'.'¥'.'2';
		}
	}
	
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/branch-dump-master-txt_v2-6.0.3.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);


	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=branch_dump.txt");
	print "$datacontents"; 
	mysql_close($link);		
?>

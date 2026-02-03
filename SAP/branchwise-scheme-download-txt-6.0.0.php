<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);
$sqlbranch="SELECT branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
$rsbranch=mysql_query($sqlbranch);
$rowbranch=mysql_fetch_array($rsbranch);
$branch_code=$rowbranch['branch_code'];

if($branch_code !='')
{
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
	$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
	$linecontents='';
	/*$sqlquery="SELECT * FROM (SELECT PDF_file_name,branch_code,acedns FROM branch_schemes_PDF WHERE 
			FIND_IN_SET(branch_code,'".$branch_code."') AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')
			ORDER BY download_time DESC) AS SAT GROUP BY 2 ORDER BY 2 ASC ";*/
	/*$sqlquery="SELECT * FROM `branch_schemes_PDF` where `branch_code`='$branch_code' and UNIX_TIMESTAMP(`download_time`) > UNIX_TIMESTAMP('".$last_update_time."') order by `download_time` desc";*/
$sqlquery="SELECT * FROM `branch_schemes_PDF` where `branch_code`='$branch_code' and `acedns`='Y' and CURDATE() between `start_date` and `end_date` order by `download_time` desc";
	$result = mysql_query($sqlquery);
	if(!$result){
	echo mysql_error();
}
	$count=mysql_num_rows($result);
	$cnt=1;
	if($count>0){
		$the_PDF_file_name_arr = array();
		while($rowschemePDF = mysql_fetch_array($result))
		{
			$the_PDF_file_name_arr = array();
			$the_branch_code = $rowschemePDF['branch_code'] ? $rowschemePDF['branch_code'] : ' ';
			$the_PDF_file_name = $rowschemePDF['PDF_file_name'] ? $rowschemePDF['PDF_file_name'] : '';
			$the_acedns = $rowschemePDF['acedns'] ? $rowschemePDF['acedns'] : ' ';			
			$contents  = $the_branch_code."^";
			$contents  .= $the_PDF_file_name."^";
			$contents  .= $the_acedns;
			
			$linecontents  .= $contents."\n";
			$cnt++;
		}
		$contentsrowcolumn=$count.'¥'.'3';
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
}
else
{
	$datacontents = '0'.'¥'.'0';
}
header("Content-type: application/text"); 
header("Content-Disposition: attachment; filename=branch_scheme_PDF.txt");
print "$datacontents"; 		
?>

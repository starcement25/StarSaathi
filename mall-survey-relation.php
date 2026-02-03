<?php
require("include/config.php");
require("include/dbcon.php");
$emp_code = $_REQUEST['emp_code'];
$sql_select_mall_survey = "SELECT * FROM mall_survey_relation";
$res_select_mall_survey = mysql_query($sql_select_mall_survey);
$count=mysql_num_rows($res_select_mall_survey);
$contentsrowcolumn  =$count.'¥'.'4';
if($count>0){
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
	while($row_select_mall_survey = mysql_fetch_array($res_select_mall_survey))
	{
		$menu_id = $row_select_mall_survey['menu_id'];
		$row_id = $row_select_mall_survey['row_id'];
		$mall_info = $row_select_mall_survey['mall_info'];
		$type = $row_select_mall_survey['type'];
		
		$contents  = (($menu_id!='')?$menu_id: ' ')."^";
		$contents  .= (($row_id!='')?$row_id: ' ')."^";
		$contents  .= (($mall_info!='')?$mall_info: ' ')."^";
		$contents  .= (($type!='')?$type: ' ');
		$linecontents  .= $contents."\n";
	}
	$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
}
else
{
  $datacontents = '0'.'¥'.'0';
}
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=mall_survey_relation.txt");
	print "$datacontents"; 	
mysql_close($link);
?>
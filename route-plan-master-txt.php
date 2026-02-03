<?php
require("include/config.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
$visit_date=$_REQUEST['current_date'];
//$emp_code='E0002';
//$visit_date='2014-05-13';
$route_plan_visit_date_month=substr($visit_date,5,2);
$route_plan_visit_date_year=substr($visit_date,0,4);

$sqlquery="SELECT *,DATE_FORMAT(visit_date,'%d-%m-%Y') AS visit_date FROM route_plan WHERE emp_code='".$emp_code."' AND visit_date LIKE '%".$route_plan_visit_date_year.'-'.$route_plan_visit_date_month."%'
		 AND visit_date>='".$visit_date."'";
$result = mysql_query($sqlquery);
$count=mysql_num_rows($result);
	$cnt=1;
	$contentsrowcolumn=$count.'¥'.'5';
	if($count>0){
		while($rowrouteplan = mysql_fetch_array($result))
		{
				$visit_date=$rowrouteplan['visit_date'];
				$visit_date_final=date('d-m-Y',strtotime($visit_date));
				/*$contents.="<data>";
				$contents .='
				<route_plan_trans_id><![CDATA['.mb_convert_encoding($rowrouteplan['route_plan_trans_id'], 'UTF-8', 'UTF-8').']]></route_plan_trans_id>
				<emp_code><![CDATA['.mb_convert_encoding($rowrouteplan['emp_code'], 'UTF-8', 'UTF-8').']]></emp_code>
				<route_code><![CDATA['.mb_convert_encoding($rowrouteplan['route_code'], 'UTF-8', 'UTF-8').']]></route_code>
				<visit_date><![CDATA['.mb_convert_encoding($visit_date_final, 'UTF-8', 'UTF-8').']]></visit_date>
				<create_date><![CDATA['.mb_convert_encoding($rowrouteplan['create_date'], 'UTF-8', 'UTF-8').']]></create_date>
				';
				$contents.="</data>";
				//echo $cnt++;*/
				$contents  = (($rowrouteplan['route_plan_trans_id']!='')?$rowrouteplan['route_plan_trans_id']: ' ')."^";
				$contents  .= (($rowrouteplan['emp_code']!='')?$rowrouteplan['emp_code']: ' ')."^";
				$contents  .= (($rowrouteplan['route_code']!='')?$rowrouteplan['route_code']: ' ')."^";
				$contents  .= (($rowrouteplan['visit_date']!='')?$rowrouteplan['visit_date']: ' ')."^";
				$contents  .= (($rowrouteplan['create_date']!='')?$rowrouteplan['create_date']: ' ');
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	/*$contents .= "</recordset>";			
	echo $contents;*/
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=route_plan_master.txt");
	print "$datacontents"; 		
?>

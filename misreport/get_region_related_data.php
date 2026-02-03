<?php
ob_start();
session_start();
require("adminUtils.php");

/*--------> Employee Hierarchy Condition <--------*/
if($_SESSION['admin_login']=="admin"){
	$emp_hierarchy = '';
	$emp_hierarchy_condition = '';
	$emp_hierarchy_condition_one='';
}
else{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition = " WHERE emp_code IN(".$emp_hierarchy.") ";
	$emp_hierarchy_condition_one = " AND SUBSTRING(survey_id,3,5) IN(".$emp_hierarchy.") ";
}

$zone = $_REQUEST['zone'];
$start_date = $_REQUEST['start_date'];
$start_date = date('Y-m-d',strtotime($_REQUEST['start_date']));
$end_date = $_REQUEST['end_date'];
$end_date  = date('Y-m-d',strtotime($_REQUEST['end_date']));
$type = $_REQUEST['type'];
if(isset($_REQUEST['division_id']))
{
	$division_id = $_REQUEST['division_id'];
}
if($zone != ''){
	if($zone == 'all')
		$zone_condition = "";
	else
		$zone_condition = " AND zone IN(".$zone.") ";
}
if($division_id != ''){
	if($division_id == 'all')
		$division_condition = "";
	else
		$division_condition = " AND division IN(".$division_id.") ";
}

	if(isset($_REQUEST['division_id']))
	{
		$sql_region = "SELECT DISTINCT region FROM kiosk_transaction_details WHERE type='".$type."' AND DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y%-%m-%d') <='".$end_date."' 
					AND DATE_FORMAT(SUBSTRING(survey_id,-14,8),'%Y%-%m-%d') >='".$start_date."'".$emp_hierarchy_condition_one.$zone_condition.
					$division_condition;
		$res_region = mysql_query($sql_region);
		$count_region=mysql_num_rows($res_region);
		if($count_region >0)
		{
			$onclick = "region_CCC('".$type."');";
		
			$region_string='';
			echo "<select name=\"region\" id=\"region\" onchange=\"".$onclick."\">";
			echo "<option value=\"\">Select</option>";
			echo "<option value=\"all\">All</option>";
			while($row_region = mysql_fetch_array($res_region)){
				//$division_id = $row_division['division_id'];
				$region = $row_region['region'];
				
				echo "<option value=\"'".$region."'\">".$region."</option>";
				$region_string .= "'".$region."',";
			}
			echo "</select>";
		}
		else
		{
			echo "<font color='#FF0000'>No region found</font>";
		}
	}
	else
	{
		echo "<font color='#FF0000'>No region found</font>";
	}

mysql_close($link);
?>
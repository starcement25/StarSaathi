<?php
ob_start();
session_start();
require("adminUtils.php");

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$survey_type = $_REQUEST['survey_type'];

$employee = $_REQUEST['employee'];
$employee_arg = str_replace("'","",$employee);
$emp_array = explode(",",$employee_arg);

$sql_date = "SELECT DISTINCT visit_date FROM route_plan WHERE (visit_date BETWEEN '".$start_date."' AND '".$end_date."') AND emp_code IN(".$employee.") ORDER BY visit_date ASC";
$res_date = mysql_query($sql_date);
$total_row_check = mysql_num_rows($res_date);
if($total_row_check>0){
	?>
    <table class="border" width="100%" style="border-collapse:collapse;" border="1">
   	  <tr class="TDHEAD">
      	<td width="10%">Date</td>
        <td>Route Name</td>
      </tr>
    <?php
		foreach($emp_array as $emp_code){
			$sql_visit_date = "SELECT DISTINCT visit_date FROM route_plan WHERE (visit_date BETWEEN '".$start_date."' AND '".$end_date."') AND emp_code = '".$emp_code."' ORDER BY visit_date ASC";
			$res_visit_date = mysql_query($sql_visit_date);
			$visit_date_check = mysql_num_rows($res_visit_date);
			if($visit_date_check>0){
				
				$sql_emp_name = "SELECT emp_name,dns_emp_code FROM employee_master WHERE emp_code = '".$emp_code."'";
				$res_emp_name = mysql_query($sql_emp_name);
				$row_emp_name = mysql_fetch_array($res_emp_name);
				$emp_name = $row_emp_name['emp_name'];
				$dns_emp_code = $row_emp_name['dns_emp_code'];
				
				if(providing_code=='yes')
				{
					echo "<tr><td colspan = '3' align = 'center' class = 'TDHEAD_SUB'>".$emp_name." (".$dns_emp_code.") </td></tr>";
				}
				else
				{
					echo "<tr><td colspan = '3' align = 'center' class = 'TDHEAD_SUB'>".$emp_name."</td></tr>";
				}
						
				$res_visit_date = mysql_query($sql_visit_date);
				while($row_visit_date = mysql_fetch_array($res_visit_date)){
					$visit_date = $row_visit_date['visit_date'];
					
					$sql_get_details = "SELECT route_code FROM route_plan WHERE visit_date = '".$visit_date."' AND emp_code = '".$emp_code."'";
					$res_get_details = mysql_query($sql_get_details);
					$total_route_check = mysql_num_rows($res_get_details);
					if($total_route_check>0){
						
						$res_get_details = mysql_query($sql_get_details);
						while($row_get_details = mysql_fetch_array($res_get_details)){
							$route_code = $row_get_details['route_code'];
							
							$sql_route_name = "SELECT route_name FROM route_master WHERE route_code = '".$route_code."'";
							$res_route_name = mysql_query($sql_route_name);
							$row_route_name = mysql_fetch_array($res_route_name);
							$route_name = $row_route_name['route_name'];
							
							$route_name_string .= $route_name.", ";
						}
						$route_name_string = rtrim($route_name_string," ");
						$route_name_string = rtrim($route_name_string,",");
					}
				echo "<tr>
						<td>".date('d-m-Y',strtotime($visit_date))."</td>
						<td>".$route_name_string."</td>
					  </tr>";
				$route_name_string = '';
				}
			}
		}
	?>
    </table><br />
    <div style="width:100%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
    <?php
}
else{
	echo "No Records";
}
mysql_close($link);
?>



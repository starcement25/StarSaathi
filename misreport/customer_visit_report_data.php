<?php
ob_start();
session_start();
require("adminUtils.php");

$month_data = $_REQUEST['month_data'];
$year_month_split = explode("-",$month_data);
$month_days = cal_days_in_month(CAL_GREGORIAN,$year_month_split[1],$year_month_split[0]);
$monthName = date('M', mktime(0, 0, 0, $year_month_split[1], 10));
$show_month = $monthName."-".$year_month_split[0];
$employee = $_REQUEST['employee'];
$employee_arg = str_replace("#",",",$employee);
$employee_arg = str_replace("^","'",$employee_arg);

$vertical = $_REQUEST['vertical'];
$state = $_REQUEST['state'];
$frequency = $_REQUEST['frequency'];

if(strpos($state,",") == FALSE)	$state = str_replace("'","",$state);
else								$state = "All";

if(strpos($employee,",") == FALSE){
	$new_emp_code = str_replace("'","",$employee);
	$sql_emp_name = "SELECT emp_name FROM employee_master WHERE emp_code = '".$new_emp_code."'";
	$res_emp_name = mysql_query($sql_emp_name);
	$row_emp_name = mysql_fetch_array($res_emp_name);
	$new_emp_name = $row_emp_name['emp_name'];
}
else{
	$new_emp_name = "All";
}
/*$header_string = "Vertical:".$vertical."&nbsp;&nbsp;State:".$state."&nbsp;&nbsp;Branch:".$branch."&nbsp;&nbsp;Department:".$department."&nbsp;&nbsp;Employee:".$new_emp_name."&nbsp;&nbsp;Month:".$month_data;*/
$curdate=date('Y-m-d');
if($frequency=='30')
{
 $previous30day=date('Y-m-d', strtotime("-30 days,$curdate "));
 $date_condition=" AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') >='".$previous30day."' AND 
					 DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') <='".$curdate."'";
}
if($frequency=='60')
{
 $previous60day=date('Y-m-d', strtotime("-60 days,$curdate "));
 $date_condition=" AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') >='".$previous60day."' AND 
					 DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') <='".$curdate."'";
}
if($frequency=='90' )
{
 $previous180day=date('Y-m-d', strtotime("-180 days,$curdate "));
 $date_condition=" AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') >='".$previous180day."' AND 
					 DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') <='".$curdate."'";
}

	$sqlcustomervisit="SELECT CM.customer_code, CM.customer_name, COUNT(OH.order_no) AS no_of_visit,CM.rds_tag,CM.emp_code FROM customer_master CM,order_header OH WHERE OH.customer_code=CM.customer_code AND CM.emp_code IN(".$employee.")".$date_condition." GROUP BY OH.customer_code ORDER BY CM.customer_name ASC";
	$rescustomervisit = mysql_query($sqlcustomervisit);
	$totalcustomervisit = mysql_num_rows($rescustomervisit);
	if($totalcustomervisit>0){
		$count = 1;
		?>
		<table border="1" id="display_table" style="border-collapse:collapse;" class="border" width="100%">
          <tr class="TDHEAD_SUB">
          	<td colspan="7" align="center">Frequency Report(Retailer Wise)</td>
          </tr>
		  <tr class="TDHEAD" align="center">
			<td>SI</td>
			<td>Customer Name</td>
            <td>Employee Name</td>
			<td>Distributor Name</td>
            <?php if($frequency=='30'){?>
			<td>30 days</td>
            <?php }if($frequency=='60'){?>
			<td>60 days</td>
            <?php }if($frequency=='90'){?>
			<td>Upto 180 days</td>
            <?php }?>
		  </tr>
		<?php
		$res_customer = mysql_query($sqlcustomervisit);
		while($row_customer = mysql_fetch_array($res_customer)){
			$emp_code = $row_customer['emp_code'];
			$customer_code = $row_customer['customer_code'];
			$customer_name = $row_customer['customer_name'];
			$no_of_visit=$row_customer['no_of_visit'];
			
				$sql_emp = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
				$res_emp = mysql_query($sql_emp);
				$row_emp = mysql_fetch_array($res_emp);
				$emp_name = $row_emp['emp_name'];
				$rds_tag=$row_customer['rds_tag'];
				
				$sql_rds = "SELECT customer_name FROM customer_master WHERE customer_code = '".$rds_tag."'";
				$res_rds = mysql_query($sql_rds);
				$row_rds = mysql_fetch_array($res_rds);
				$rds_name = $row_rds['customer_name'];

			echo "<tr>
					<td>".$count."</td>
					<td>".$customer_name."</td>
					<td>".$emp_name."</td>
					<td>".$rds_name."</td>
					<td align=\"right\">".$no_of_visit."</td>
				  </tr>";
			
			$count++;
		}
		?>
        </table>
        <?php
	}
	else{
		echo "<span style=\"color:red; font-weight:bold;\">No Record Found</span>";
	}
?>

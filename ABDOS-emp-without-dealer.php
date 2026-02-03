<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");


$sqlquery="SELECT emp_code FROM employee_master WHERE app_access='Y'";
$result = mysql_query($sqlquery);
$count=mysql_num_rows($result);
	if($count>0){
		$emp_code_list='';
		while($rowemp = mysql_fetch_array($result))
		{
			$employee_hierarchy=return_employee_hierarchy($rowemp['emp_code']);
			$emp_hierarchy_condition=' emp_code IN('.$employee_hierarchy.')';
			$sqlemphierarchy="SELECT customer_code FROM customer_master WHERE cust_type='D' AND  ".$emp_hierarchy_condition."";
			$rsemphierarchy=mysql_query($sqlemphierarchy);
			$cntemphierarchy=mysql_num_rows($rsemphierarchy);
			if($cntemphierarchy ==0)
			{
				$emp_code_list=$emp_code_list.$rowemp['emp_code'].',';
			}
		}
		echo $emp_code_list=substr($emp_code_list,0,-1);
	}
?>

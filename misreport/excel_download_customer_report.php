<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$state_name = $_REQUEST['state_name'];
$emp_code = $_REQUEST['emp_code'];
$route_code = $_REQUEST['route_code'];
$submit_data = $_REQUEST['submit_data'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

/*if($state_name != '' && ($emp_code == '' && $route_code ==''))
{
	$condition = " EM.state='".$state_name."'";
}
else if(($state_name != '' && $emp_code != '') && $route_code =='')
{
	if($emp_code == 'all'){
		if(strtoupper($_SESSION['admin_login']) != "ADMIN"){
			$emp_hierarchy = return_employee_hierarchy($_SESSION['admin_login']);
			$condition = " EM.state='".$state_name."' AND EM.emp_code IN (".$emp_hierarchy.") ";
		}
		else{
			$condition = " EM.state='".$state_name."' AND EM.emp_code != '' ";
		}
	}
	else{
		$condition = " EM.state='".$state_name."' AND EM.emp_code='".$emp_code."' ";
	}
}
else if($state_name == ''&& $emp_code != ''){
	$condition = " EM.emp_code='".$emp_code."' ";
}
else
{
	$condition = " EM.state='".$state_name."' AND CM.emp_code='".$emp_code."' AND CM.route_code='".$route_code."'";
}

if($_SESSION['admin_login']=="admin")
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition="";
	
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition=" AND EM.emp_code IN (".$emp_hierarchy.")";
}*/
if($_SESSION['admin_login']=="admin")
{
	$emp_hierarchy="";
	
}
else
{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
}
if(modified_customer_emp_route == 'yes'){
	if(strtoupper($_SESSION['admin_login']) == "ADMIN"){
		if($emp_code == 'all')
			$emp_hierarchy_condition = " AND CRER.emp_code != '' ";
		else
			$emp_hierarchy_condition = " AND CRER.emp_code = '".$emp_code."' ";
	}
	else{
		if($emp_code == 'all')
			$emp_hierarchy_condition = " AND CRER.emp_code IN (".$emp_hierarchy.") ";
		else
			$emp_hierarchy_condition = " AND CRER.emp_code = '".$emp_code."' ";
	}
}
else if(modified_customer_emp_route == 'no'){
	if(strtoupper($_SESSION['admin_login']) == "ADMIN"){
		if($emp_code == 'all')
			$emp_hierarchy_condition = " AND EM.emp_code != '' ";
		else
			$emp_hierarchy_condition = " AND EM.emp_code = '".$emp_code."' ";
	}
	else{
		if($emp_code == 'all')
			$emp_hierarchy_condition = " AND EM.emp_code IN (".$emp_hierarchy.") ";
		else
			$emp_hierarchy_condition = " AND EM.emp_code = '".$emp_code."' ";
	}
}

?>

<?php
$setExcelName = "NewCustomer";

if(modified_customer_emp_route == 'yes'){
	$sql_new_customer = "SELECT CRER.customer_code, CM.customer_name, CM.address, CM.pin, CM.phone_no, EM.emp_code, EM.dns_emp_code, EM.emp_name, EM.designation, RM.route_name, DATE_FORMAT(SUBSTRING(CRER.customer_code,-14,8),'%d-%m-%Y') as date_created, EM.vertical_value,CM.rds_tag,EM.state FROM employee_master EM, customer_master CM, route_master RM, customer_route_emp_relation CRER WHERE CRER.emp_code = EM.emp_code AND CM.customer_code = CRER.customer_code AND CRER.route_code = RM.route_code AND CRER.customer_code LIKE 'N%' ".$condition.$emp_hierarchy_condition." AND (DATE_FORMAT(SUBSTRING(CRER.customer_code,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') ORDER BY DATE_FORMAT(SUBSTRING(CRER.customer_code,-14,8),'%d-%m-%Y'), EM.emp_name ASC, CM.customer_name ASC";
}
else if(modified_customer_emp_route == 'no'){
	$sql_new_customer = "SELECT CM.customer_code, CM.customer_name, CM.address, CM.pin, CM.phone_no, EM.emp_code, EM.dns_emp_code, EM.emp_name, EM.designation, RM.route_name, DATE_FORMAT(SUBSTRING(CM.customer_code,-14,8),'%d-%m-%Y') as date_created, EM.vertical_value,CM.rds_tag,EM.state FROM employee_master EM, customer_master CM, route_master RM WHERE CM.emp_code=EM.emp_code AND CM.route_code=RM.route_code AND CM.customer_code LIKE 'N%' AND RM.emp_code = CM.emp_code ".$condition.$emp_hierarchy_condition." AND (DATE_FORMAT(SUBSTRING(CM.customer_code,-14,8),'%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') ORDER BY DATE_FORMAT(SUBSTRING(CM.customer_code,-14,8),'%d-%m-%Y'), EM.emp_name ASC, CM.customer_name ASC";
}
$res_new_customer = mysql_query($sql_new_customer);
$total_rows = mysql_num_rows($res_new_customer);
if($total_rows>0)
{
	if($submit_data == 'submitdata')
	{
		$count = 1;
		echo "<table width=\"100%\" border=\"1\" style=\"border-collapse:collapse;\" class=\"border\" cellpadding=\"6px\">
				  <tr class=\"TDHEAD\">
				  	<td>SI</td>
					<td>Customer Name</td>"; ?>
                   <?php if(tagged_distributor_for_order=='yes'){?>
            		<td>Distributor Name</td>
            		<?php }?>
                    <?php echo "<td>State</td>
					<td>Area</td>
					<td>Pincode</td>
					<td>Phone No</td>
					<td>Emp Code</td>
					<td>Employee Name</td>
					<td>Designation</td>";
		if(vertical_fields == 'yes'){
			echo "<td>Vertical</td>";
		}
		echo "<td>Date Created</td><td>1st Productive Call</td>
				  </tr>";
		$res_new_customer = mysql_query($sql_new_customer);
		while($row_new_customer = mysql_fetch_array($res_new_customer))
		{
			$customer_dns_code = $row_new_customer['customer_code'];
			$customer_name = $row_new_customer['customer_name'];
			$address = $row_new_customer['address'];
			$pin = $row_new_customer['pin'];
			$phone_no = $row_new_customer['phone_no'];
			$emp_name = $row_new_customer['emp_name'];
			$route_name = $row_new_customer['route_name'];
			$date_created = $row_new_customer['date_created'];
			$vertical_value = $row_new_customer['vertical_value'];
			$rds_tag = $row_new_customer['rds_tag'];
			$state = $row_new_customer['state'];
			$plain_emp_code = $row_new_customer['emp_code'];
			$dns_emp_code = $row_new_customer['dns_emp_code'];
			$designation = $row_new_customer['designation'];
			$sql1stproductivecall="SELECT  DATE_FORMAT(SUBSTRING(order_no,-14,14),'%d-%m-%Y %H:%i:%s') AS producttive_call_date FROM order_header WHERE 
								order_no lIKE 'O%' AND customer_code='".$customer_dns_code."' ORDER BY DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s') DESC LIMIT 0,1";
			$rs1stproductivecall=mysql_query($sql1stproductivecall);
			$row1stproductivecall=mysql_fetch_array($rs1stproductivecall);
			$productivecall1st=$row1stproductivecall['producttive_call_date'];							
			if(providing_code == 'yes'){
				$display_emp_code = $dns_emp_code;
			}
			else if(providing_code == 'no'){
				$display_emp_code = $plain_emp_code;
			}
			
			if(tagged_distributor_for_order=='yes'){
				$sql_rds_name = "SELECT customer_name FROM customer_master WHERE customer_code = '".$rds_tag."'";
				$res_rds_name = mysql_query($sql_rds_name);
				$row_rds_name = mysql_fetch_array($res_rds_name);
				$rds_name = $row_rds_name['customer_name'];
				
				echo "<tr>
					<td>".$count."</td>
					<td>".$customer_name."</td>
					<td>".$rds_name."</td>
					<td>".$state."</td>
					<td>".$route_name."</td>
					<td>".$pin."</td>
					<td>".$phone_no."</td>
					<td>".$display_emp_code."</td>
					<td>".$emp_name."</td>
					<td>".$designation."</td>
					";
			}
			else
			{
				echo "<tr>
					<td>".$count."</td>
					<td>".$customer_name."</td>
					<td>".$state."</td>
					<td>".$route_name."</td>
					<td>".$pin."</td>
					<td>".$phone_no."</td>
					<td>".$display_emp_code."</td>
					<td>".$emp_name."</td>
					<td>".$designation."</td>
					";
			}

			if(vertical_fields == 'yes'){
				echo "<td>".$vertical_value."</td>";
			}
		
			echo "<td>".$date_created."</td><td>".$productivecall1st."</td>
				  </tr>";
			$count++;
		}
		echo "</table>";
		?>
        <br />
    <input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="export" onClick="exporttocsv();">
        <?php
	}
	else
	{
		if(vertical_fields == 'yes'){
			$header = "Customer Name"."\t"."Distributor Name"."\t"."State"."\t"."Area"."\t"."Pincode"."\t"."Phone No"."\t"."Employee Code"."\t"."Employee Name"."\t"."Designation"."\t"."Vertical"."\t"."Date Created"."\t"."1st Productive Call";
		}
		else{
			$header = "Customer Name"."\t"."Distributor Name"."\t"."State"."\t"."Area"."\t"."Pincode"."\t"."Phone No"."\t"."Employee Code"."\t"."Employee Name"."\t"."Designation"."\t"."Date Created"."\t"."Date Created"."\t"."1st Productive Call";
		}
		
		$res_new_customer = mysql_query($sql_new_customer);
		while($row_new_customer = mysql_fetch_array($res_new_customer))
		{
			$customer_dns_code = $row_new_customer['customer_code'];
			$customer_name = $row_new_customer['customer_name'];
			$address = $row_new_customer['address'];
			$pin = $row_new_customer['pin'];
			$phone_no = $row_new_customer['phone_no'];
			$emp_name = $row_new_customer['emp_name'];
			$route_name = $row_new_customer['route_name'];
			$rds_tag = $row_new_customer['rds_tag'];
			$state = $row_new_customer['state'];
			$date_created = $row_new_customer['date_created'];
			$vertical_value = $row_new_customer['vertical_value'];
			$plain_emp_code = $row_new_customer['emp_code'];
			$dns_emp_code = $row_new_customer['dns_emp_code'];
			$designation = $row_new_customer['designation'];
			
			$sql1stproductivecall="SELECT  DATE_FORMAT(SUBSTRING(order_no,-14,14),'%d-%m-%Y %H:%i:%s') AS producttive_call_date FROM order_header WHERE 
								order_no lIKE 'O%' AND customer_code='".$customer_dns_code."' ORDER BY DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s') DESC LIMIT 0,1";
			$rs1stproductivecall=mysql_query($sql1stproductivecall);
			$row1stproductivecall=mysql_fetch_array($rs1stproductivecall);
			$productivecall1st=$row1stproductivecall['producttive_call_date'];				
			if(tagged_distributor_for_order=='yes'){
				$sql_rds_name = "SELECT customer_name FROM customer_master WHERE customer_code = '".$rds_tag."'";
				$res_rds_name = mysql_query($sql_rds_name);
				$row_rds_name = mysql_fetch_array($res_rds_name);
				$rds_name = $row_rds_name['customer_name'];
			}
			
			if(providing_code == 'yes'){
				$display_emp_code = $dns_emp_code;
			}
			else if(providing_code == 'no'){
				$display_emp_code = $plain_emp_code;
			}
			
			if(vertical_fields == 'yes'){
				$content .= $customer_name."\t".$rds_name."\t".$state."\t".$route_name."\t".$pin."\t".$phone_no."\t".$plain_emp_code."\t".$emp_name."\t".$designation."\t".$vertical_value."\t".$date_created."\t".$productivecall1st."\n";
			}
			else{
				$content .= $customer_name."\t".$rds_name."\t".$state."\t".$route_name."\t".$pin."\t".$phone_no."\t".$plain_emp_code."\t".$emp_name."\t".$designation."\t".$date_created."\t".$productivecall1st."\n";
			}
		}
			header("Content-type: application/octet-stream"); 
			header("Content-Disposition: attachment; filename=".$setExcelName."_Report.xls"); 
			header("Pragma: no-cache"); 
			header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
			echo ucwords($header)."\n".$content; //- See more at: http://www.discussdesk.com/download-mysql-data-into-excel-file-in-php.htm#sthash.5bPI72JI.dpuf
	}
}
else
{
	echo "<font color=\"red\"><strong>No records</strong></font>";
}
?>
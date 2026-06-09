<?php
ini_set('memory_limit', '99999M');
set_time_limit(0);
include "star_connection.php";
$get_fetch_type = $_GET["get_type"] ? trim($_GET["get_type"]) : "all";
if($get_fetch_type!=""){
	if($get_fetch_type=="dealer" || $get_fetch_type=="subdealer" || $get_fetch_type=="all"){
		startCreatCustomerCsvfile($get_fetch_type);
	}else{
		exit;
	}
}else{
	exit;
}


function startCreatCustomerCsvfile($get_fetch_type){
$customer_master = "customer_master";
$branch_master = "branch_master";
$customer_destination = "customer_destination";
$destination_master = "destination_master";
$customer_route_emp_relation = "customer_route_emp_relation";
$employee_master = "employee_master";

$get_fetch_type = $get_fetch_type ? strtolower(trim($get_fetch_type)) : "";
$curr_date = date("jS_M_Y_h_m_s_A");
if($get_fetch_type=="dealer"){
	$the_file_name = "dealer_customer_".$curr_date.".csv";
	$where_qry = " where $customer_master.`cust_type`='Dealer' and $customer_master.`acedns`='Y' and $customer_master.`cust_type`!='Non Star' ";
}else if($get_fetch_type=="subdealer"){
	$the_file_name = "subdealer_customer_".$curr_date.".csv";
	$where_qry = " where $customer_master.`cust_type` IN('Sub Dealer','RSSD') and $customer_master.`acedns`='Y' and $customer_master.`cust_type`!='Non Star' ";
}else{
	$the_file_name = "all_customer_".$curr_date.".csv";
	//$where_qry = " where ($customer_master.`cust_type`='Sub Dealer' or $customer_master.`cust_type`='Dealer') and $customer_master.`acedns`='Y' and $customer_master.`cust_type`!='Non Star' ";
	$where_qry = " where  $customer_master.`acedns`='Y'";

}
$output = "";

/*---------PAGINATION RELATED CODE START----------*/
$con="AND
(
    (customer_master.`cust_type` = 'Dealer' AND customer_master.`customer_id` LIKE '10%')
    OR
    (customer_master.`cust_type` = 'RSSD' AND customer_master.`customer_id` LIKE '15%')
   
    OR
    (customer_master.`cust_type` = 'Ship to Party-dealer' AND customer_master.`customer_id` LIKE '14%')
    OR
    (customer_master.`cust_type` = 'ShiptoParty-Subdeale' AND customer_master.`customer_id` LIKE '14%')
)  ";

$qry = "select $customer_master.`customer_id` AS SAP_Code,$customer_master.`dns_customer_code`,$customer_master.`customer_name`,$customer_master.`address`,$customer_master.`phone_no`,$customer_master.`route_code`,$customer_master.`acedns`,$customer_master.`black_list`,$customer_master.`cust_type`,(SELECT CM.customer_id FROM customer_master CM WHERE CM.customer_code=$customer_master.`rds_tag`) AS Linked_Dealer_Code,(SELECT CM.customer_name FROM customer_master CM WHERE CM.customer_code=$customer_master.`rds_tag`) AS Linked_Dealer_Name,$customer_master.`branch_code`,$branch_master.`branch_name`,
		$customer_master.`whatsapp_no`,$customer_master.`email`,$customer_master.region,$destination_master.dns_destination_code,$destination_master.destination_name,
		$customer_master.`order_restriction`,$customer_master.`plant`,$customer_master.`owner_name`,$customer_master.`pin`,$customer_master.`district`,$customer_master.`appointment_date`,
		$customer_master.`lattitude`,$customer_master.`longitude`,$customer_master.`DOB`,$customer_master.`ANV_DATE`,$customer_master.`IFSC`,$customer_master.`zone`,$customer_master.`sub_zone`
		,$customer_master.`bank_account_number`,$customer_master.`credit_limit`,$customer_master.`PAN`,$customer_master.`minimum_stock`,$customer_master.`monthly_potential`
		from $customer_master left join $branch_master on $customer_master.`branch_code`=$branch_master.`branch_code` 
		left JOIN  $customer_destination ON $customer_destination.customer_code=$customer_master.customer_code
		left JOIN  $destination_master ON $destination_master.destination_code=$customer_destination.destination_code
		$where_qry $con order by $customer_master.`customer_code` asc";
		
		// $qry = "select $employee_master.dns_emp_code,$employee_master.emp_name, $customer_master.`customer_id` AS SAP_Code,$customer_master.`dns_customer_code`,$customer_master.`customer_name`,$customer_master.`address`,$customer_master.`phone_no`,$customer_master.`route_code`,$customer_master.`acedns`,$customer_master.`black_list`,$customer_master.`cust_type`,(SELECT CM.customer_id FROM customer_master CM WHERE CM.customer_code=$customer_master.`rds_tag`) AS Linked_Dealer_Code,(SELECT CM.customer_name FROM customer_master CM WHERE CM.customer_code=$customer_master.`rds_tag`) AS Linked_Dealer_Name,$customer_master.`branch_code`,$branch_master.`branch_name`,
		// $customer_master.`whatsapp_no`,$customer_master.`email`,$customer_master.region,$destination_master.dns_destination_code,$destination_master.destination_name,
		// $customer_master.`order_restriction`,$customer_master.`plant`,$customer_master.`owner_name`,$customer_master.`pin`,$customer_master.`district`,$customer_master.`appointment_date`,
		// $customer_master.`lattitude`,$customer_master.`longitude`,$customer_master.`DOB`,$customer_master.`ANV_DATE`,$customer_master.`IFSC`,$customer_master.`zone`,$customer_master.`sub_zone`
		// ,$customer_master.`bank_account_number`,$customer_master.`credit_limit`,$customer_master.`PAN`,$customer_master.`minimum_stock`,$customer_master.`monthly_potential`
		// from $employee_master INNER JOIN $customer_route_emp_relation ON $customer_route_emp_relation.`emp_code`=$employee_master.`emp_code`, $customer_master left join $branch_master on $customer_master.`branch_code`=$branch_master.`branch_code` 
		// left JOIN  $customer_destination ON $customer_destination.customer_code=$customer_master.customer_code
		// left JOIN  $destination_master ON $destination_master.destination_code=$customer_destination.destination_code
		// $where_qry order by $customer_master.`customer_code` asc";
		
		//echo $qry;exit();
$sql = mysql_query($qry);
$columns_total = mysql_num_fields($sql);

// Get The Field Name
$output='"Mapped_Employee_Code"';
$output.=',';
$output.='"Mapped_Employee_Name"';
$output.=',';
for ($i = 0; $i < $columns_total; $i++) {
$heading = mysql_field_name($sql, $i);
$output .= '"'.$heading.'",';
}
$output .="\n";
// Get Records from the table

while ($row = mysql_fetch_array($sql)) {
    
    $customer_id = $row["SAP_Code"];
    	$mapped_emp_name='';
		$mapped_emp_code='';
    $sqlempmapping1="select employee_master.dns_emp_code,employee_master.emp_name FROM employee_master,customer_route_emp_relation WHERE customer_route_emp_relation.emp_code=employee_master.emp_code AND customer_route_emp_relation.customer_code='".$customer_id."'  ORDER BY employee_master.emp_name ASC";

	try{
		$resmapping = mysql_query($sqlempmapping1);
		$totresmapping = mysql_num_rows($resmapping);
	
		if($totresmapping >0){
		    $mapped_emp_name='';
		    $mapped_emp_code='';
			while($rowmapping=mysql_fetch_assoc($resmapping)){
				$mapped_emp_name.=$rowmapping['emp_name'].'-';
				$mapped_emp_code.=$rowmapping['dns_emp_code'].'-';
			}
			$mapped_emp_name=substr($mapped_emp_name,0,-1);
			$mapped_emp_code=substr($mapped_emp_code,0,-1);
		}
    
	}catch(Exception $e){
	    
	}
    $output .='"'.$mapped_emp_code.'",';
    $output .='"'.$mapped_emp_name.'",';
    
for ($i = 0; $i < $columns_total; $i++) {
	 $value = str_replace(array("'", '"'), ' ', $row[$i]); 
$output .='"'.$value.'",';
}

$output .="\n";
}

// Download the file

$filename = $the_file_name;
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');    
header('Expires: 0');
echo $output;
exit;
}

mysql_close();
?>
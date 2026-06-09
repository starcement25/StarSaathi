<?php
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$get_fetch_type = $_GET["get_type"] ? trim($_GET["get_type"]) : "";
if ($get_fetch_type != "") {
	if ($get_fetch_type == "notloggedin" || $get_fetch_type == "loggedin") {
		startCreatDealerCsvfile($get_fetch_type);
	} else {
		exit;
	}
} else {
	exit;
}

function startCreatDealerCsvfile($get_fetch_type)
{
	$employee_master = "employee_master";
	$customer_master = "customer_master";
	$changepassword = "changepassword";
	$customer_route_emp_relation = "customer_route_emp_relation";
	$get_fetch_type = $get_fetch_type ? strtolower(trim($get_fetch_type)) : "";
	$curr_date = date("jS_M_Y_h_m_s_A");
	if ($get_fetch_type == "notloggedin") {
		$the_file_name = "notloggedin_dealer_" . $curr_date . ".csv";
		/*$where_qry = " where $customer_master.`acedns`='Y' and $customer_master.`cust_type`='Dealer' and ($changepassword.`deviceid`='' or $changepassword.`deviceid` is null) ";*/
		$where_qry = " where  $customer_master.`cust_type`='Dealer' and ($changepassword.`deviceid`='' or $changepassword.`deviceid` is null) ";
	} else if ($get_fetch_type == "loggedin") {
		$the_file_name = "loggedin_dealer_" . $curr_date . ".csv";
		/*$where_qry = " where $customer_master.`acedns`='Y' and $customer_master.`cust_type`='Dealer' and ($changepassword.`deviceid`!='' and $changepassword.`deviceid` is not null) ";*/
		$where_qry = " where $customer_master.`cust_type`='Dealer' and ($changepassword.`deviceid`!='' and $changepassword.`deviceid` is not null) ";
	} else {
		$the_file_name = "all_dealer_" . $curr_date . ".csv";
		$where_qry = "";
	}

//main sql query
	/*$qry = "select $customer_master.`dns_customer_code`,$customer_master.customer_id AS SAP_code,$customer_master.`customer_name`,$customer_master.`acedns`,$customer_master.`phone_no`,$changepassword.`device_type`,$changepassword.`app_version`,$customer_master.`whatsapp_no`,$customer_master.`email` from $customer_master left join $changepassword on $customer_master.`customer_code`=$changepassword.`customer_code` $where_qry order by $customer_master.`customer_name` asc";*/

//trail sql query

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

$qry = "SELECT $customer_master.dns_customer_code,$customer_master.customer_id AS SAP_code,$customer_master.customer_name,$customer_master.acedns,$customer_master.phone_no,$changepassword.device_type,$changepassword.app_version,$customer_master.whatsapp_no,$customer_master.email,GROUP_CONCAT(EM.dns_emp_code SEPARATOR ';') AS mapped_emp_code,GROUP_CONCAT(EM.emp_name SEPARATOR ';')  AS mapped_emp_name FROM $customer_master LEFT JOIN $changepassword ON $customer_master.customer_code = $changepassword.customer_code LEFT JOIN customer_route_emp_relation CRR ON $customer_master.customer_id = CRR.customer_code LEFT JOIN employee_master EM ON CRR.emp_code = EM.emp_code $where_qry $con GROUP BY $customer_master.customer_id ORDER BY $customer_master.customer_name ASC";

	//dns_emp_code as mapped_emp_code and emp_name as mapped_emp_name
//echo $qry;

	//$qry = "select `dns_customer_code`,`customer_name`,`acedns`,`phone_no` from $customer_master $where_qry order by `customer_name` asc";
	//mysql_query("SET SESSION sql_mode = 'TRADITIONAL'");
	$sql = mysql_query($qry);
	$columns_total = mysql_num_fields($sql);

	// Get The Field Name

	for ($i = 0; $i < $columns_total; $i++) {
		$heading = mysql_field_name($sql, $i);
		$output .= '"' . $heading . '",';
	}
	$output .= "\n";
	// Get Records from the table

	while ($row = mysql_fetch_array($sql)) {
		for ($i = 0; $i < $columns_total; $i++) {
			$output .= '"' . $row["$i"] . '",';
		}
		$output .= "\n";
	}

	// Download the file

	$filename = $the_file_name;
	header('Content-type: application/csv');
	header('Content-Disposition: attachment; filename=' . $filename);
	header('Pragma: no-cache');
	header('Expires: 0');
	echo $output;
	exit;
}

mysql_close();
?>
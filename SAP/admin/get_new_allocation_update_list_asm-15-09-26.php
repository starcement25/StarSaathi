<?php
/*
====================================================================
 get_related_asm_list.php
 --------------------------------------------------------------
 Purpose : Given a customer (the "created_by" / applier customer_code),
           find the related ASM(s).

 Logic:
   1. customer_route_emp_relation
        WHERE customer_code = <created_by> AND acedns = 'Y'
      -> gives the SO emp_code(s) mapped to this customer.

   2. employee_master
        WHERE emp_code = <SO emp_code>
      -> read `reporting_to` (comma-separated emp_codes this SO
         reports to).

   3. employee_master
        WHERE emp_code IN (<all reporting_to codes from step 2>)
        AND level = 'L3'
        AND acedns = 'Y'
      -> these are the ASM(s) to return.

 Input (POST/GET):
   created_by - customer_code of the applier

 NOTE: This is a first pass — designed to be easy to extend later
 (e.g. more levels, more designations, caching, etc.)
====================================================================
*/

// include "star_connection.php";
// include "function-sfa.php";

// $customer_route_emp_relation = "customer_route_emp_relation";
// $employee_master             = "employee_master";

// $res_data = array();

// $created_by = isset($_REQUEST["created_by"]) ? addslashes(trim($_REQUEST["created_by"])) : "";

// if ($created_by == '') {
// 	$res_data = array("process_status" => "NO", "process_message" => "created_by (customer_code) is required.", "data" => array());
// 	echo json_encode($res_data);
// 	die;
// }

// // --------------------------------------------------------------
// // STEP 1: get the SO emp_code(s) mapped to this customer
// // --------------------------------------------------------------
// $so_emp_codes = array();

// $sql_so = "SELECT DISTINCT `emp_code` FROM $customer_route_emp_relation
//            WHERE `customer_code`='$created_by'
//            AND `acedns`='Y'";
// $res_so = mysql_query($sql_so);

// if ($res_so && mysql_num_rows($res_so) > 0) {
// 	while ($row_so = mysql_fetch_assoc($res_so)) {
// 		$emp_code = trim($row_so["emp_code"]);
// 		if ($emp_code != '') {
// 			$so_emp_codes[] = $emp_code;
// 		}
// 	}
// }

// if (count($so_emp_codes) == 0) {
// 	$res_data = array("process_status" => "NO", "process_message" => "No SO mapped to this customer.", "data" => array());
// 	echo json_encode($res_data);
// 	die;
// }

// // --------------------------------------------------------------
// // STEP 2: for each SO, read reporting_to (comma-separated) from
// // employee_master and collect all the manager codes
// // --------------------------------------------------------------
// $reporting_codes = array();

// $so_list_escaped = array();
// foreach ($so_emp_codes as $so_code) {
// 	$so_list_escaped[] = "'" . addslashes($so_code) . "'";
// }
// $so_in_clause = implode(",", $so_list_escaped);

// $sql_reporting = "SELECT `emp_code`,`reporting_to` FROM $employee_master
//                    WHERE `emp_code` IN ($so_in_clause)";
// $res_reporting = mysql_query($sql_reporting);

// if ($res_reporting && mysql_num_rows($res_reporting) > 0) {
// 	while ($row_rep = mysql_fetch_assoc($res_reporting)) {
// 		$reporting_to_val = trim($row_rep["reporting_to"]);
// 		if ($reporting_to_val != '') {
// 			$split_codes = explode(",", $reporting_to_val);
// 			foreach ($split_codes as $rc) {
// 				$rc = trim($rc);
// 				if ($rc != '' && !in_array($rc, $reporting_codes)) {
// 					$reporting_codes[] = $rc;
// 				}
// 			}
// 		}
// 	}
// }

// if (count($reporting_codes) == 0) {
// 	$res_data = array("process_status" => "NO", "process_message" => "No reporting manager found for the mapped SO(s).", "data" => array());
// 	echo json_encode($res_data);
// 	die;
// }

// // --------------------------------------------------------------
// // STEP 3: from those manager codes, keep only level = 'L3' (ASM)
// // --------------------------------------------------------------
// $rep_list_escaped = array();
// foreach ($reporting_codes as $rc) {
// 	$rep_list_escaped[] = "'" . addslashes($rc) . "'";
// }
// $rep_in_clause = implode(",", $rep_list_escaped);

// $sql_asm = "SELECT `emp_code`,`dns_emp_code`,`emp_name`,`designation`,`level`,`branch_code`,`zone`,`region`,`email`,`phone_no`
//             FROM $employee_master
//             WHERE `emp_code` IN ($rep_in_clause)
//             AND `level`='L3'
//             AND `acedns`='Y'";
// $res_asm = mysql_query($sql_asm);

// $asm_list = array();
// if ($res_asm && mysql_num_rows($res_asm) > 0) {
// 	while ($row_asm = mysql_fetch_assoc($res_asm)) {
// 		$asm_list[] = $row_asm;
// 	}
// }

// if (count($asm_list) > 0) {
// 	$res_data = array("process_status" => "YES", "process_message" => "ASM list fetched successfully.", "data" => $asm_list);
// } else {
// 	$res_data = array("process_status" => "NO", "process_message" => "No ASM (level L3) found in the reporting chain.", "data" => array());
// }

// echo json_encode($res_data);
// mysql_close();


/*
====================================================================
 get_related_asm_list.php
 --------------------------------------------------------------
 Purpose : Given only an ASM's emp_code, find all PENDING sub-dealer
           allocation change requests for every customer under that
           ASM's SO chain.

 Logic (reverse of the earlier customer -> ASM lookup):
   1. employee_master
        WHERE emp_code = <emp_code> AND level = 'L3' AND acedns = 'Y'
      -> confirm this is a valid, active ASM. Reject otherwise.

   2. employee_master
        WHERE FIND_IN_SET(<emp_code>, reporting_to)
      -> all SO emp_code(s) that report to this ASM
         (reporting_to is a comma-separated list per employee).

   3. customer_route_emp_relation
        WHERE emp_code IN (<SO emp_codes from step 2>)
        AND acedns = 'Y'
      -> all customer_code(s) mapped to those SOs.

   4. allocation_details_invoicewise_history
        WHERE customer_id IN (<customer_codes from step 3>)
        AND approval_status = 'Pending'
      -> return this list.

 Input (POST/GET):
   emp_code - emp_code of the ASM (the only input now)
====================================================================
*/

include "star_connection.php";
include "function-sfa.php";

$employee_master             = "employee_master";
$customer_route_emp_relation = "customer_route_emp_relation";
$allocation_details_history  = "allocation_details_invoicewise_history";

$res_data = array();

$emp_code = isset($_REQUEST["emp_code"]) ? addslashes(trim($_REQUEST["emp_code"])) : "";

if ($emp_code == '') {
	$res_data = array("process_status" => "NO", "process_message" => "emp_code is required.", "data" => array());
	echo json_encode($res_data);
	die;
}
// echo 7583738;die;
// --------------------------------------------------------------
// STEP 1: confirm this is a valid, active L3 (ASM) employee
// --------------------------------------------------------------
$sql_asm_check = "SELECT `emp_code`,`emp_name`,`level` FROM $employee_master
                   WHERE `emp_code`='$emp_code'
                   AND `level`='L3'
                   AND `acedns`='Y'
                   LIMIT 1";

$res_asm_check = mysql_query($sql_asm_check);

if (!$res_asm_check || mysql_num_rows($res_asm_check) == 0) {
	$res_data = array("process_status" => "NO", "process_message" => "Invalid ASM code, or this employee is not an active L3.", "data" => array());
	echo json_encode($res_data);
	die;
}

// --------------------------------------------------------------
// STEP 2: find all SO emp_code(s) reporting to this ASM
// (reporting_to is a comma-separated list, so FIND_IN_SET is used
//  after stripping any stray spaces around the commas)
// --------------------------------------------------------------
$so_emp_codes = array();

$sql_so = "SELECT `emp_code` FROM $employee_master
           WHERE FIND_IN_SET('$emp_code', REPLACE(`reporting_to`,' ',''))
           AND `acedns`='Y'";
$res_so = mysql_query($sql_so);

if ($res_so && mysql_num_rows($res_so) > 0) {
	while ($row_so = mysql_fetch_assoc($res_so)) {
		$emp_code = trim($row_so["emp_code"]);
		if ($emp_code != '') {
			$so_emp_codes[] = $emp_code;
		}
	}
}

if (count($so_emp_codes) == 0) {
	$res_data = array("process_status" => "NO", "process_message" => "No SO found reporting to this ASM.", "data" => array());
	echo json_encode($res_data);
	die;
}

// --------------------------------------------------------------
// STEP 3: find all customers mapped to those SO(s)
// --------------------------------------------------------------
$so_list_escaped = array();
foreach ($so_emp_codes as $so_code) {
	$so_list_escaped[] = "'" . addslashes($so_code) . "'";
}
$so_in_clause = implode(",", $so_list_escaped);

$customer_codes = array();

$sql_cust = "SELECT DISTINCT `customer_code` FROM $customer_route_emp_relation
             WHERE `emp_code` IN ($so_in_clause)
             AND `acedns`='Y'";
$res_cust = mysql_query($sql_cust);

if ($res_cust && mysql_num_rows($res_cust) > 0) {
	while ($row_cust = mysql_fetch_assoc($res_cust)) {
		$cc = trim($row_cust["customer_code"]);
		if ($cc != '') {
			$customer_codes[] = $cc;
		}
	}
}

if (count($customer_codes) == 0) {
	$res_data = array("process_status" => "NO", "process_message" => "No customers found under this ASM's SO(s).", "data" => array());
	echo json_encode($res_data);
	die;
}

// --------------------------------------------------------------
// STEP 4: fetch PENDING allocation change requests for those
// customers
// --------------------------------------------------------------
$cust_list_escaped = array();
foreach ($customer_codes as $cc) {
	$cust_list_escaped[] = "'" . addslashes($cc) . "'";
}
$cust_in_clause = implode(",", $cust_list_escaped);

$sql_pending = "SELECT * FROM $allocation_details_history
                WHERE `customer_id` IN ($cust_in_clause)
                AND `approval_status`='Pending'
                ORDER BY `logged_at` DESC";
$res_pending = mysql_query($sql_pending);

$pending_list = array();
if ($res_pending && mysql_num_rows($res_pending) > 0) {
	while ($row_pending = mysql_fetch_assoc($res_pending)) {
		$pending_list[] = $row_pending;
	}
}

$res_data = array(
	"process_status"  => "YES",
	"process_message" => count($pending_list) > 0 ? "Pending allocation update requests fetched successfully." : "No pending allocation update requests found.",
	"emp_code"        => $emp_code,
	"data"            => $pending_list
);

echo json_encode($res_data);
mysql_close();

?>
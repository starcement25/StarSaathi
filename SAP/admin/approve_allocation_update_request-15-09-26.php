<?php
/*
====================================================================
 approve_allocation_change_request.php
 --------------------------------------------------------------
 Purpose : Let an authorized ASM approve a pending sub-dealer
           allocation change request. On approval:
             1. allocation_details_invoicewise_history row is
                marked Approved (approved_by, approved_date set).
             2. The live table (allocation_details_invoicewise)
                finally gets its sub_dealer_id updated to the
                requested new_sub_dealer_id — this is the ONLY
                place that update happens, and only on approval.

 Authorization: same hierarchy check as get_related_asm_list.php —
   1. emp_code must be a valid, active, level='L3' employee.
   2. The pending request's customer_id must fall under this ASM's
      SO chain (SO -> customer_route_emp_relation), otherwise the
      ASM has no authority over this particular request.

 Input (POST/GET):
   emp_code    - emp_code of the ASM approving the request
   history_id  - the pending history row (from
                 allocation_details_invoicewise_history) to approve
====================================================================
*/

include "star_connection.php";
include "function-sfa.php";

date_default_timezone_set('Asia/Kolkata');

$employee_master             = "employee_master";
$customer_route_emp_relation = "customer_route_emp_relation";
$allocation_details_history  = "allocation_details_invoicewise_history";
$allocation_details          = "allocation_details_invoicewise";

$res_data = array();

$asm_code   = isset($_REQUEST["emp_code"])   ? addslashes(trim($_REQUEST["emp_code"]))   : "";
$history_id = isset($_REQUEST["history_id"]) ? addslashes(trim($_REQUEST["history_id"])) : "";

if ($emp_code == '' || $history_id == '') {
	$res_data = array("process_status" => "NO", "process_message" => "emp_code and history_id are both required.");
	echo json_encode($res_data);
	die;
}

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
	$res_data = array("process_status" => "NO", "process_message" => "Invalid ASM code, or this employee is not an active L3.");
	echo json_encode($res_data);
	die;
}

// --------------------------------------------------------------
// STEP 2: fetch the pending request
// --------------------------------------------------------------
$sql_req = "SELECT * FROM $allocation_details_history
            WHERE `history_id`='$history_id'
            AND `approval_status`='Pending'
            LIMIT 1";
$res_req = mysql_query($sql_req);

if (!$res_req || mysql_num_rows($res_req) == 0) {
	$res_data = array("process_status" => "NO", "process_message" => "No pending request found for this history_id.");
	echo json_encode($res_data);
	die;
}

$req_row = mysql_fetch_assoc($res_req);
$req_customer_id = $req_row["customer_id"];

// --------------------------------------------------------------
// STEP 3: AUTHORIZATION — this ASM's SO chain must cover the
// request's customer_id
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

$authorized = false;

if (count($so_emp_codes) > 0) {
	$so_list_escaped = array();
	foreach ($so_emp_codes as $so_code) {
		$so_list_escaped[] = "'" . addslashes($so_code) . "'";
	}
	$so_in_clause = implode(",", $so_list_escaped);

	$sql_cust_check = "SELECT `sl_no` FROM $customer_route_emp_relation
	                    WHERE `customer_code`='$req_customer_id'
	                    AND `emp_code` IN ($so_in_clause)
	                    AND `acedns`='Y'
	                    LIMIT 1";
	$res_cust_check = mysql_query($sql_cust_check);

	if ($res_cust_check && mysql_num_rows($res_cust_check) > 0) {
		$authorized = true;
	}
}

if (!$authorized) {
	$res_data = array("process_status" => "NO", "process_message" => "You are not authorized to approve this request.");
	echo json_encode($res_data);
	die;
}

// --------------------------------------------------------------
// STEP 4: APPROVE — mark history row approved, then update the
// live table's sub_dealer_id
// --------------------------------------------------------------
$approved_date = date("Y-m-d H:i:s");

$sql_approve = "UPDATE $allocation_details_history
                 SET `approval_status`='Approved',
                     `approved_by`='$emp_code',
                     `approved_date`='$approved_date'
                 WHERE `history_id`='$history_id'
                 AND `approval_status`='Pending'";
$res_approve = mysql_query($sql_approve);

if (!$res_approve || mysql_affected_rows() == 0) {
	$res_data = array("process_status" => "NO", "process_message" => "Failed to approve the request (it may have already been processed).");
	echo json_encode($res_data);
	die;
}

$sql_update_live = "UPDATE $allocation_details
                     SET `sub_dealer_id`='".$req_row["new_sub_dealer_id"]."'
                     WHERE `allocation_id`='".$req_row["allocation_id"]."'";
$res_update_live = mysql_query($sql_update_live);

if (!$res_update_live) {
	$res_data = array("process_status" => "NO", "process_message" => "Request marked approved, but failed to update the live allocation record. Please check manually. Error: " . mysql_error());
	echo json_encode($res_data);
	die;
}

$res_data = array(
	"process_status"  => "YES",
	"process_message" => "Request approved and sub dealer updated successfully.",
	"data" => array(
		"history_id"        => $history_id,
		"allocation_id"      => $req_row["allocation_id"],
		"customer_id"        => $req_customer_id,
		"old_sub_dealer_id"  => $req_row["old_sub_dealer_id"],
		"new_sub_dealer_id"  => $req_row["new_sub_dealer_id"],
		"approved_by"        => $emp_code,
		"approval_status"    => "Approved",
		"approved_date"      => $approved_date
	)
);

echo json_encode($res_data);
mysql_close();
?>
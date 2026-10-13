<?php
/**
 * get_authorized_customers.php
 * -----------------------------------------------------------------
 * Deploy on the starsaathi server, same directory as
 * reallocation_history_report.php / export_allocation_details_invoicewise_history.php
 *
 * Purpose:
 *   Given a list of dns_emp_code values (as used on the SFA/acedns side),
 *   resolve them to starsaathi emp_code values via employee_master, then
 *   look up customer_route_emp_relation to find which customer_code
 *   values (dealers) those employees are allowed to see.
 *
 * Request:
 *   GET/POST dns_emp_codes = comma-separated list, e.g. MISZD,MISZE,MISZF
 *
 * Response (JSON):
 *   {
 *     "success": true,
 *     "emp_codes_resolved": ["E001","E002"],
 *     "customer_codes": ["C1001","C1002", ...]
 *   }
 *
 * ASSUMPTION TO VERIFY: this returns customer_route_emp_relation.customer_code
 * as-is. reallocation_history_report.php will need to match these values
 * against h.old_sub_dealer_id / h.new_sub_dealer_id, which are
 * customer_master.customer_id values. If customer_code and customer_id are
 * not the same value space in your data, an extra lookup step is needed
 * here (customer_master WHERE customer_code = ... -> customer_id) before
 * returning.
 *
 * On any failure this returns success:false / customer_codes:[]. Callers
 * MUST treat "empty" as "no access", never as "no filter".
 * -----------------------------------------------------------------
 */

include "web_check.php";
include "star_connection.php";

header('Content-Type: application/json');

function respond($success, $customer_codes, $extra = array()) {
	$out = array_merge(array(
		'success' => $success,
		'customer_codes' => array_values(array_unique($customer_codes)),
	), $extra);
	echo json_encode($out);
	exit;
}

$raw = isset($_REQUEST['dns_emp_codes']) ? trim($_REQUEST['dns_emp_codes']) : "";
$dns_emp_codes_in = array_values(array_filter(array_map('trim', explode(',', $raw))));

if (empty($dns_emp_codes_in)) {
	respond(false, array(), array('message' => 'dns_emp_codes is required'));
}

// legacy mysql_* extension has no prepared statements on this server
// (matching export_allocation_details_invoicewise_history.php), so we
// escape + quote each value for the IN() list.
$dns_emp_codes_escaped = array();
foreach ($dns_emp_codes_in as $code) {
	$dns_emp_codes_escaped[] = "'" . addslashes($code) . "'";
}
$dns_in_list = implode(",", $dns_emp_codes_escaped);

// -----------------------------------------------------------------
// Step 1: dns_emp_code -> emp_code (starsaathi's employee_master)
// -----------------------------------------------------------------
$sql1 = "SELECT emp_code, dns_emp_code FROM employee_master WHERE dns_emp_code IN ($dns_in_list)";
$res1 = mysql_query($sql1);

if (!$res1) {
	respond(false, array(), array('message' => 'Step1 query failed: ' . mysql_error()));
}

$emp_codes = array();
while ($row = mysql_fetch_assoc($res1)) {
	if (!empty($row['emp_code'])) {
		$emp_codes[] = $row['emp_code'];
	}
}
$emp_codes = array_values(array_unique($emp_codes));

if (empty($emp_codes)) {
	// none of the given dns_emp_codes matched a known employee here
	respond(true, array(), array('emp_codes_resolved' => array()));
}

$emp_codes_escaped = array();
foreach ($emp_codes as $code) {
	$emp_codes_escaped[] = "'" . addslashes($code) . "'";
}
$emp_in_list = implode(",", $emp_codes_escaped);

// -----------------------------------------------------------------
// Step 2: emp_code -> customer_code via customer_route_emp_relation
// -----------------------------------------------------------------
$sql2 = "SELECT DISTINCT customer_code FROM customer_route_emp_relation WHERE emp_code IN ($emp_in_list)";
$res2 = mysql_query($sql2);

if (!$res2) {
	respond(false, array(), array('message' => 'Step2 query failed: ' . mysql_error()));
}

$customer_codes = array();
while ($row = mysql_fetch_assoc($res2)) {
	if (!empty($row['customer_code'])) {
		$customer_codes[] = $row['customer_code'];
	}
}

mysql_close();

respond(true, $customer_codes, array('emp_codes_resolved' => $emp_codes));
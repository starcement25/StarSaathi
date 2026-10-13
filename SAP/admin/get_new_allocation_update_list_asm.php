<?php
/*
====================================================================
 get_related_asm_list.php
 --------------------------------------------------------------
 Purpose : Given only an ASM's dns_emp_code, find all PENDING
           sub-dealer allocation change requests where this ASM
           appears in the related_asm column (comma-separated
           dns_emp_code list, populated at request-creation time
           by update_subdealer_allocation.php via the secondary-
           server relay lookup).

 Logic:
   allocation_details_invoicewise_history
     WHERE approval_status = 'Pending'
     AND FIND_IN_SET(<dns_emp_code>, related_asm)
   -> return this list.

 No separate hierarchy re-derivation needed here — related_asm was
 already resolved to the correct L3 ASM(s) when the request was
 created, so matching against it directly both fetches the right
 rows AND acts as the authorization check (only requests where this
 dns_emp_code was actually resolved as an authorized ASM will match).

 Input (POST/GET):
   dns_emp_code - the ASM's dns_emp_code (the only input)
====================================================================
*/

// include "star_connection.php";
// include "function-sfa.php";

// $allocation_details_history = "allocation_details_invoicewise_history";

// $res_data = array();

// $dns_emp_code = isset($_REQUEST["dns_emp_code"]) ? addslashes(trim($_REQUEST["dns_emp_code"])) : "";

// if ($dns_emp_code == '') {
// 	$res_data = array("process_status" => "NO", "process_message" => "dns_emp_code is required.", "data" => array());
// 	echo json_encode($res_data);
// 	die;
// }

// // --------------------------------------------------------------
// // fetch PENDING requests where this dns_emp_code appears in
// // related_asm (comma-separated, so FIND_IN_SET is used after
// // stripping any stray spaces around the commas)
// // --------------------------------------------------------------
// $sql_pending = "SELECT * FROM $allocation_details_history
//                 WHERE `approval_status`='Pending'
//                 AND FIND_IN_SET('$dns_emp_code', REPLACE(`related_asm`,' ',''))
//                 ORDER BY `logged_at` DESC";
// $res_pending = mysql_query($sql_pending);

// $pending_list = array();
// if ($res_pending && mysql_num_rows($res_pending) > 0) {
// 	while ($row_pending = mysql_fetch_assoc($res_pending)) {
// 		$pending_list[] = $row_pending;
// 	}
// }

// $res_data = array(
// 	"process_status"  => "YES",
// 	"process_message" => count($pending_list) > 0 ? "Pending allocation update requests fetched successfully." : "No pending allocation update requests found.",
// 	"dns_emp_code"    => $dns_emp_code,
// 	"data"            => $pending_list
// );

// echo json_encode($res_data);
// mysql_close();

//////////////////////


/*
====================================================================
 get_related_asm_list.php
 --------------------------------------------------------------
 Purpose : Given only an ASM's dns_emp_code, find all sub-dealer
           allocation change requests where this ASM appears in the
           related_asm column (comma-separated dns_emp_code list,
           populated at request-creation time by
           update_subdealer_allocation.php via the secondary-server
           relay lookup).

           Returns requests of ANY approval_status by default
           (Pending, Approved, Rejected) — not just Pending — so the
           ASM can see their full history of requests they've acted
           on or still need to act on. An optional `status` input
           narrows this down to a single status if needed.

 Logic:
   allocation_details_invoicewise_history
     WHERE FIND_IN_SET(<dns_emp_code>, related_asm)
     [AND approval_status = <status>]   -- only if status was passed
   -> return this list.

 No separate hierarchy re-derivation needed here — related_asm was
 already resolved to the correct L3 ASM(s) when the request was
 created, so matching against it directly both fetches the right
 rows AND acts as the authorization check (only requests where this
 dns_emp_code was actually resolved as an authorized ASM will match).

 Input (POST/GET):
   dns_emp_code - the ASM's dns_emp_code (required)
   status       - optional. One of "Pending", "Approved", "Rejected".
                  If omitted, all statuses are returned.
====================================================================
*/

include "star_connection.php";
include "function-sfa.php";

$allocation_details_history = "allocation_details_invoicewise_history";

$res_data = array();

$dns_emp_code = isset($_REQUEST["dns_emp_code"]) ? addslashes(trim($_REQUEST["dns_emp_code"])) : "";
$status       = isset($_REQUEST["status"])       ? trim($_REQUEST["status"])                    : "";

if ($dns_emp_code == '') {
	$res_data = array("process_status" => "NO", "process_message" => "dns_emp_code is required.", "data" => array());
	echo json_encode($res_data);
	die;
}

$allowed_statuses = array("Pending", "Approved", "Rejected");
$status_filter_sql = "";

if ($status != '') {
	// case-insensitive match against the allowed set, then re-cased
	// to the canonical stored value for the SQL comparison
	$matched_status = "";
	foreach ($allowed_statuses as $allowed) {
		if (strcasecmp($status, $allowed) == 0) {
			$matched_status = $allowed;
			break;
		}
	}

	if ($matched_status == "") {
		$res_data = array("process_status" => "NO", "process_message" => "status must be one of: Pending, Approved, Rejected.", "data" => array());
		echo json_encode($res_data);
		die;
	}

	$status_filter_sql = "AND `approval_status`='" . addslashes($matched_status) . "'";
}

// --------------------------------------------------------------
// fetch requests where this dns_emp_code appears in related_asm
// (comma-separated, so FIND_IN_SET is used after stripping any
// stray spaces around the commas). All statuses unless `status`
// was explicitly passed.
// --------------------------------------------------------------
// $sql_list = "SELECT h.*, cm.customer_name AS created_by_name
//              FROM $allocation_details_history h
//              LEFT JOIN customer_master cm
//                     ON cm.customer_id = h.created_by
//              WHERE FIND_IN_SET('$dns_emp_code', REPLACE(h.`related_asm`,' ',''))
//              $status_filter_sql
//              ORDER BY h.`logged_at` DESC";
$sql_list = "
    SELECT 
        h.*,

        cm_old.customer_name AS old_sub_dealer_name,
        cm_new.customer_name AS new_sub_dealer_name,
        cm_created.customer_name AS created_by_name

    FROM `$allocation_details_history` AS h

    LEFT JOIN `customer_master` AS cm_old 
        ON cm_old.customer_id = h.old_sub_dealer_id

    LEFT JOIN `customer_master` AS cm_new 
        ON cm_new.customer_id = h.new_sub_dealer_id

    LEFT JOIN `customer_master` AS cm_created 
        ON cm_created.customer_id = h.created_by

    WHERE FIND_IN_SET(
        '$dns_emp_code',
        REPLACE(COALESCE(h.related_asm, ''), ' ', '')
    )

    $status_filter_sql

    ORDER BY h.logged_at DESC
";
// echo $sql_list;die;
$res_list = mysql_query($sql_list);

$request_list = array();
if ($res_list && mysql_num_rows($res_list) > 0) {
	while ($row_list = mysql_fetch_assoc($res_list)) {
		$request_list[] = $row_list;
	}
}

$res_data = array(
	"process_status"  => "YES",
	"process_message" => count($request_list) > 0 ? "Allocation update requests fetched successfully." : "No allocation update requests found.",
	"dns_emp_code"    => $dns_emp_code,
	"status_filter"   => $status != '' ? $matched_status : "All",
	"data"            => $request_list
);

echo json_encode($res_data);
mysql_close();

?>
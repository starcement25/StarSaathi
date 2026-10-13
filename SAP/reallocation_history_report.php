<?php

header('Content-Type: application/json');

include "web_check.php";
include "star_connection.php";  // this must already establish the DB connection, same as the original report

$allocation_details_history = "allocation_details_invoicewise_history";

// ---- Read & sanitize params (same style as original: addslashes + trim) ----
// NOTE: switched from $_GET to $_REQUEST since the SFA page now sends
// these as POST (customer_ids can be long enough to exceed GET URL
// limits). $_REQUEST covers both GET and POST, so nothing breaks for
// any other caller still using GET.
$srch_apporderno  = isset($_REQUEST["apporderno"])  ? addslashes(trim($_REQUEST["apporderno"]))  : "";
$srch_inv_no      = isset($_REQUEST["inv_no"])      ? addslashes(trim($_REQUEST["inv_no"]))      : "";
$srch_appr_status = isset($_REQUEST["appr_status"]) ? addslashes(trim($_REQUEST["appr_status"])) : "";
$srch_customer_ids = isset($_REQUEST["customer_ids"]) ? trim($_REQUEST["customer_ids"]) : "";
$has_customer_ids_param = isset($_REQUEST["customer_ids"]); // was the param sent at all?

$page  = isset($_REQUEST['page'])  ? max(1, intval($_REQUEST['page']))  : 1;
$limit = isset($_REQUEST['limit']) ? max(1, intval($_REQUEST['limit'])) : 100;
$start_from = ($page - 1) * $limit;

// ---- Build WHERE clause, same pattern as original ----
$whr_str = "";
$search_array = array(
    "srch_apporderno"  => $srch_apporderno,
    "srch_inv_no"      => $srch_inv_no,
    "srch_appr_status" => $srch_appr_status
);

foreach ($search_array as $search_array_key => $search_array_val) {
    if ($search_array_val != '') {
        $aand = (trim($whr_str) != "") ? " and" : "";

        if ($search_array_key == "srch_apporderno") {
            $whr_str .= "$aand h.`APPORDERNO` like '%$search_array_val%' ";
        }
        if ($search_array_key == "srch_inv_no") {
            $whr_str .= "$aand h.`inv_no` like '%$search_array_val%' ";
        }
        if ($search_array_key == "srch_appr_status") {
            $whr_str .= "$aand h.`approval_status` = '$search_array_val' ";
        }
    }
}

// ---- Employee/customer restriction: only records where the dealer
// (old or new sub-dealer) is one this employee is authorized to see.
// customer_ids arrives as a comma-separated list of customer_master.customer_id
// values (resolved upstream on the SFA side via get_authorized_customers.php).
// Admin callers simply won't send this param, so they're unrestricted. ----
if ($has_customer_ids_param) {
    $ids = array_filter(array_map('trim', explode(',', $srch_customer_ids)));
    if (!empty($ids)) {
        $ids_escaped = array();
        foreach ($ids as $id) {
            $ids_escaped[] = "'" . addslashes($id) . "'";
        }
        $ids_in_list = implode(",", $ids_escaped);

        $aand = (trim($whr_str) != "") ? " and" : "";
        $whr_str .= "$aand (h.`old_sub_dealer_id` IN ($ids_in_list) OR h.`new_sub_dealer_id` IN ($ids_in_list)) ";
    } else {
        // customer_ids was sent but had nothing usable in it after
        // cleaning (e.g. empty string) — fail closed instead of
        // silently returning every record.
        $aand = (trim($whr_str) != "") ? " and" : "";
        $whr_str .= "$aand 1=0 ";
    }
}

$new_whr_str = ($whr_str != "") ? "where ".$whr_str : "";

// ---- Count total matching rows ----
$count_sql = "SELECT h.`history_id` FROM $allocation_details_history AS h $new_whr_str";
$count_res = mysql_query($count_sql);
$total_records = mysql_num_rows($count_res);

// ---- Main data query ----
$sql1 = "
    SELECT
        h.`history_id`,
        h.`batch_id`,
        h.`APPORDERNO`,
        h.`inv_no`,
         h.`inv_date`,
   h.`inv_cancl`,
      h.`prod_desc`,
        h.`old_sub_dealer_id`,
        old_sd.`customer_name` AS `old_sub_dealer_name`,

        h.`new_sub_dealer_id`,
        new_sd.`customer_name` AS `new_sub_dealer_name`,

        h.`old_qty`,
        h.`new_qty`,
        h.`month`,
        h.`created_by`,
        created_user.`customer_name` AS `created_by_name`,
        created_user.`branch_code` AS `created_by_branch_code`,
        created_branch.`branch_name` AS `created_by_branch_name`,

        h.`allocation_date`,
        h.`approval_status`,
        h.`approved_by`,
        h.`approved_date`,
        h.`logged_at`

    FROM $allocation_details_history AS h

    LEFT JOIN customer_master AS old_sd
        ON old_sd.`customer_id` = h.`old_sub_dealer_id`

    LEFT JOIN customer_master AS new_sd
        ON new_sd.`customer_id` = h.`new_sub_dealer_id`

    LEFT JOIN customer_master AS created_user
        ON created_user.`customer_id` = h.`created_by`

    LEFT JOIN branch_master AS created_branch
        ON created_branch.`branch_code` = created_user.`branch_code`

    $new_whr_str

    order by h.`history_id` desc
    limit $start_from,$limit
";

$res1 = mysql_query($sql1);

$records = [];
$sr_no = $start_from + 1;

if ($res1 && mysql_num_rows($res1) > 0) {
    while ($row1 = mysql_fetch_assoc($res1)) {
        $records[] = [
            "Sr. No"                  => $sr_no++,
            "Batch ID"                => $row1["batch_id"],
            "APPORDERNO"              => $row1["APPORDERNO"],
            "Invoice No"              => $row1["inv_no"],
            "Old Sub Dealer"          => $row1["old_sub_dealer_id"] . (!empty($row1["old_sub_dealer_name"]) ? " - " . $row1["old_sub_dealer_name"] : ""),
            "New Sub Dealer"          => $row1["new_sub_dealer_id"] . (!empty($row1["new_sub_dealer_name"]) ? " - " . $row1["new_sub_dealer_name"] : ""),
            "Old Qty"                 => $row1["old_qty"],
            "New Qty"                 => $row1["new_qty"],
            "Month"                   => $row1["month"],
            "Created By"              => $row1["created_by"] . (!empty($row1["created_by_name"]) ? " - " . $row1["created_by_name"] : ""),
            "Created By Branch"       => $row1["created_by_branch_code"] . (!empty($row1["created_by_branch_name"]) ? " - " . $row1["created_by_branch_name"] : ""),
            "Allocation Date"         => $row1["allocation_date"],
            "Approval Status"         => $row1["approval_status"],
            "Approved By"             => $row1["approved_by"],
            "Approved Date"           => $row1["approved_date"],
            "Logged At"               => $row1["logged_at"],
             "Invoice Cancel"               => $row1["inv_cancl"],
                      "Product Name"               => $row1["prod_desc"],
                      "Branch"=>$row1["created_by_branch_name"],
                       "Invoice Date"               => $row1["inv_date"],
        ];
    }
}

echo json_encode([
    "data"          => $records,
    "total_records" => (int) $total_records,
    "page"          => $page,
    "limit"         => $limit
]);

mysql_close();
exit;
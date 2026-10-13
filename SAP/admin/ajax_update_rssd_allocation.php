<?php
/*
====================================================================
 update_subdealer_allocation.php
 --------------------------------------------------------------
 Purpose : Create a SUB-DEALER CHANGE REQUEST for a row in
           allocation_details_invoicewise.

           This script does NOT change the live table anymore.
           It only logs a pending request (old value + new value)
           into the history table. The live table's sub_dealer_id
           is only updated later, by a separate approval script,
           once approval_status is set to 'Approved'.

 Inputs (POST/GET):
   APPORDERNO   - order number (match key)
   inv_no       - invoice number (match key)
   created_by   - dealer_id of the person/dealer requesting the change
                  (NOT matched against the table's customer_id column —
                  only stored in the history table for audit purposes)
   sub_dealer_id- NEW sub dealer id being requested

 History table logic (one row per request):
   - allocation_date    = date the request was made (today)
   - old_sub_dealer_id  = current sub_dealer_id on the live row
   - new_sub_dealer_id  = the requested sub_dealer_id
   - approval_status    = 'Pending' (until approved/rejected elsewhere)
   - approved_by        = NULL (set on approval)
   - approved_date      = NULL (set on approval)
====================================================================

 -- Run this once if the history table does not already exist --
 -- (or ALTER an existing one — see notes below the CREATE TABLE) --

CREATE TABLE `allocation_details_invoicewise_history` (
  `history_id`        bigint(20)   NOT NULL AUTO_INCREMENT,
  `allocation_id`      bigint(20)   DEFAULT NULL,   -- reference to live row's id
  `date_and_time`      datetime     DEFAULT NULL,   -- original date_and_time from live row
  `inv_date`           date         DEFAULT NULL,
  `allocation_date`    date         DEFAULT NULL,   -- date this change request was made
  `order_id`           varchar(255) DEFAULT NULL,
  `APPORDERNO`         varchar(50)  DEFAULT NULL,
  `inv_no`             varchar(255) DEFAULT NULL,
  `customer_id`        varchar(255) DEFAULT NULL,
  `old_sub_dealer_id`  varchar(50)  DEFAULT NULL,
  `new_sub_dealer_id`  varchar(50)  DEFAULT NULL,
  `dns_prod_code`      varchar(20)  DEFAULT NULL,
  `prod_desc`          varchar(255) DEFAULT NULL,
  `inv_qty`            varchar(10)  DEFAULT NULL,
  `allocation_qty`     varchar(255) DEFAULT NULL,
  `inv_cancl`          enum('yes','no') DEFAULT 'no',
  `delete_at`          int(2)       DEFAULT 0,
  `is_offline`         varchar(200) DEFAULT 'Online',
  `created_by`         varchar(255) DEFAULT NULL,   -- dealer_id who requested the change
  `month`              varchar(20)  DEFAULT NULL,   -- month extracted from inv_date
  `approved_by`        varchar(255) DEFAULT NULL,   -- set when approved/rejected
  `approval_status`    varchar(20)  DEFAULT 'Pending', -- Pending / Approved / Rejected
  `approved_date`      datetime     DEFAULT NULL,   -- set when approved/rejected
  `logged_at`          datetime     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`history_id`),
  KEY `idx_apporderno` (`APPORDERNO`),
  KEY `idx_allocation_id` (`allocation_id`),
  KEY `idx_approval_status` (`approval_status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

 -- If you already have the OLD version of this table (with a single
 -- `sub_dealer_id` column and OLD-/NEW- prefixed allocation_date rows),
 -- migrate it instead of dropping it, e.g.:
 --
 -- ALTER TABLE allocation_details_invoicewise_history
 --   CHANGE `sub_dealer_id` `old_sub_dealer_id` varchar(50) DEFAULT NULL,
 --   ADD `new_sub_dealer_id` varchar(50) DEFAULT NULL AFTER `old_sub_dealer_id`,
 --   MODIFY `allocation_date` date DEFAULT NULL,
 --   ADD `approved_by` varchar(255) DEFAULT NULL,
 --   ADD `approval_status` varchar(20) DEFAULT 'Pending',
 --   ADD `approved_date` datetime DEFAULT NULL;
====================================================================
*/

include "star_connection.php";
include "function-sfa.php";

date_default_timezone_set('Asia/Kolkata');

$allocation_details         = "allocation_details_invoicewise";
$allocation_details_history = "allocation_details_invoicewise_history";
$app_setting_master         = "app_setting_master";

$res_data = array();

$APPORDERNO        = isset($_REQUEST["APPORDERNO"])   ? addslashes(trim($_REQUEST["APPORDERNO"]))   : "";
$inv_no             = isset($_REQUEST["inv_no"])        ? addslashes(trim($_REQUEST["inv_no"]))        : "";
$created_by         = isset($_REQUEST["created_by"])    ? addslashes(trim($_REQUEST["created_by"]))    : ""; // dealer_id requesting the change (audit only, not a match key)
$new_sub_dealer_id  = isset($_REQUEST["sub_dealer_id"]) ? addslashes(trim($_REQUEST["sub_dealer_id"])) : "";

if ($APPORDERNO == '' || $inv_no == '' || $created_by == '' || $new_sub_dealer_id == '') {
	$res_data = array("process_status" => "NO", "process_message" => "Missing required fields (APPORDERNO, inv_no, created_by, sub_dealer_id).");
	echo json_encode($res_data);
	die;
}

// Fetch the live row the request is being made against
$sqlsel = "SELECT * FROM $allocation_details
           WHERE `APPORDERNO`='$APPORDERNO'
           AND `inv_no`='$inv_no'
           AND `delete_at`='0'
           LIMIT 1";
$ressel = mysql_query($sqlsel);
$totressel = mysql_num_rows($ressel);

if ($totressel > 0) {

	$old_row = mysql_fetch_assoc($ressel);

	if ($old_row["sub_dealer_id"] == $new_sub_dealer_id) {
		$res_data = array("process_status" => "NO", "process_message" => "This sub dealer is already assigned to the record. No change made.");
		echo json_encode($res_data);
		die;
	}

	// block if this record already has a pending request awaiting approval
	$sqlpend = "SELECT `history_id` FROM $allocation_details_history
	            WHERE `allocation_id`='".$old_row["allocation_id"]."'
	            AND `approval_status`='Pending'
	            LIMIT 1";
	$respend = mysql_query($sqlpend);
	if (mysql_num_rows($respend) > 0) {
		$res_data = array("process_status" => "NO", "process_message" => "A sub dealer change request for this record is already pending approval.");
		echo json_encode($res_data);
		die;
	}

	// check allocation change window (rssd_allocation_change_days) as per app_setting_master sk300825
	$sqlset = "SELECT `the_value` FROM $app_setting_master WHERE `the_key_name`='rssd_allocation_change_days' LIMIT 1";
	$resset = mysql_query($sqlset);
	$totresset = mysql_num_rows($resset);
	$rssd_allocation_change_days = 0;
	if ($totresset > 0) {
		$rowset = mysql_fetch_assoc($resset);
		$rssd_allocation_change_days = $rowset["the_value"] ? intval(trim($rowset["the_value"])) : 0;
	}

	if ($rssd_allocation_change_days > 0) {
		$days_gap = (strtotime(date("Y-m-d")) - strtotime(date("Y-m-d", strtotime($old_row["date_and_time"])))) / 86400;
		if ($days_gap > $rssd_allocation_change_days) {
			$res_data = array("process_status" => "NO", "process_message" => "Allocation cannot be changed. The allowed change window of $rssd_allocation_change_days day(s) from the allocation date has expired.");
			echo json_encode($res_data);
			die;
		}
	}
	// end allocation change window check

	$current_date = date("Y-m-d");
	$month = $old_row["inv_date"] ? date("F", strtotime($old_row["inv_date"])) : "";

	// Log the change REQUEST (single row: old value + new value, pending approval)
	$sqlins = "INSERT INTO $allocation_details_history
		(`allocation_id`,`date_and_time`,`inv_date`,`allocation_date`,`order_id`,`APPORDERNO`,`inv_no`,`customer_id`,`old_sub_dealer_id`,`new_sub_dealer_id`,`dns_prod_code`,`prod_desc`,`inv_qty`,`allocation_qty`,`inv_cancl`,`delete_at`,`is_offline`,`created_by`,`month`,`approved_by`,`approval_status`,`approved_date`)
		VALUES
		('".$old_row["allocation_id"]."','".$old_row["date_and_time"]."','".$old_row["inv_date"]."','$current_date','".$old_row["order_id"]."','".$old_row["APPORDERNO"]."','".$old_row["inv_no"]."','".$old_row["customer_id"]."','".$old_row["sub_dealer_id"]."','$new_sub_dealer_id','".$old_row["dns_prod_code"]."','".$old_row["prod_desc"]."','".$old_row["inv_qty"]."','".$old_row["allocation_qty"]."','".$old_row["inv_cancl"]."','".$old_row["delete_at"]."','".$old_row["is_offline"]."','$created_by','$month',NULL,'Pending',NULL)";
	$resins = mysql_query($sqlins);

	// NOTE: the live table (allocation_details_invoicewise) is intentionally
	// NOT updated here. sub_dealer_id only changes once this request is
	// approved via the separate approval script.

	if ($resins) {
		$new_history_id = mysql_insert_id();

		$inserted_row = array(
			"history_id"        => $new_history_id,
			"allocation_id"     => $old_row["allocation_id"],
			"date_and_time"     => $old_row["date_and_time"],
			"inv_date"          => $old_row["inv_date"],
			"allocation_date"   => $current_date,
			"order_id"          => $old_row["order_id"],
			"APPORDERNO"        => $old_row["APPORDERNO"],
			"inv_no"            => $old_row["inv_no"],
			"customer_id"       => $old_row["customer_id"],
			"old_sub_dealer_id" => $old_row["sub_dealer_id"],
			"new_sub_dealer_id" => $new_sub_dealer_id,
			"dns_prod_code"     => $old_row["dns_prod_code"],
			"prod_desc"         => $old_row["prod_desc"],
			"inv_qty"           => $old_row["inv_qty"],
			"allocation_qty"    => $old_row["allocation_qty"],
			"inv_cancl"         => $old_row["inv_cancl"],
			"delete_at"         => $old_row["delete_at"],
			"is_offline"        => $old_row["is_offline"],
			"created_by"        => $created_by,
			"month"             => $month,
			"approved_by"       => null,
			"approval_status"   => "Pending",
			"approved_date"     => null
		);

		$res_data = array(
			"process_status"  => "YES",
			"process_message" => "Sub dealer change request submitted. Awaiting approval.",
			"data"            => $inserted_row
		);
	} else {
		$res_data = array("process_status" => "NO", "process_message" => "Failed to submit sub dealer change request.");
	}

} else {
	$res_data = array("process_status" => "NO", "process_message" => "No matching allocation record found for the given APPORDERNO / inv_no.");
}

echo json_encode($res_data);
mysql_close();
?>
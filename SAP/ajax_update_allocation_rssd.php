<?php
/*
====================================================================
 update_subdealer_allocation.php
 --------------------------------------------------------------
 Purpose : Update the `sub_dealer_id` of an existing row in
           allocation_details_invoicewise, and log both the OLD
           and NEW versions of that row into a history table.

 Inputs (POST/GET):
   APPORDERNO   - order number (match key)
   inv_no       - invoice number (match key)
   created_by   - dealer_id of the person/dealer performing the change
                  (NOT matched against the table's customer_id column —
                  only stored in the history table for audit purposes)
   sub_dealer_id- NEW sub dealer id to assign (the only field changed)

 History table logic:
   - Old row  -> allocation_date = 'OLD-' . inv_date
   - New row  -> allocation_date = 'NEW-' . current date (Y-m-d)
   Both old and new snapshots are inserted into the history table;
   the live table only ever holds the current (updated) row.
====================================================================

 -- Run this once if the history table does not already exist --

CREATE TABLE `allocation_details_invoicewise_history` (
  `history_id`     bigint(20)   NOT NULL AUTO_INCREMENT,
  `allocation_id`  bigint(20)   DEFAULT NULL,   -- reference to live row's id
  `date_and_time`  datetime     DEFAULT NULL,
  `inv_date`       date         DEFAULT NULL,
  `allocation_date` varchar(30) DEFAULT NULL,   -- 'OLD-YYYY-MM-DD' / 'NEW-YYYY-MM-DD'
  `order_id`       varchar(255) DEFAULT NULL,
  `APPORDERNO`     varchar(50)  DEFAULT NULL,
  `inv_no`         varchar(255) DEFAULT NULL,
  `customer_id`    varchar(255) DEFAULT NULL,
  `sub_dealer_id`  varchar(50)  DEFAULT NULL,
  `dns_prod_code`  varchar(20)  DEFAULT NULL,
  `prod_desc`      varchar(255) DEFAULT NULL,
  `inv_qty`        varchar(10)  DEFAULT NULL,
  `allocation_qty` varchar(255) DEFAULT NULL,
  `inv_cancl`      enum('yes','no') DEFAULT 'no',
  `delete_at`      int(2)       DEFAULT 0,
  `is_offline`     varchar(200) DEFAULT 'Online',
  `created_by`     varchar(255) DEFAULT NULL,     -- dealer_id/customer_id who made the change
  `month`          varchar(20)  DEFAULT NULL,     -- month extracted from inv_date
  `logged_at`      datetime     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`history_id`),
  KEY `idx_apporderno` (`APPORDERNO`),
  KEY `idx_allocation_id` (`allocation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
$created_by         = isset($_REQUEST["created_by"])    ? addslashes(trim($_REQUEST["created_by"]))    : ""; // dealer_id making the change (audit only, not a match key)
$new_sub_dealer_id  = isset($_REQUEST["sub_dealer_id"]) ? addslashes(trim($_REQUEST["sub_dealer_id"])) : "";

if ($APPORDERNO == '' || $inv_no == '' || $created_by == '' || $new_sub_dealer_id == '') {
	$res_data = array("process_status" => "NO", "process_message" => "Missing required fields (APPORDERNO, inv_no, created_by, sub_dealer_id).");
	echo json_encode($res_data);
	die;
}

// Fetch the live row to change
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

	// 1. Log OLD version into history
	$old_allocation_date = "OLD-" . $old_row["inv_date"];
	$sqlins_old = "INSERT INTO $allocation_details_history
		(`allocation_id`,`date_and_time`,`inv_date`,`allocation_date`,`order_id`,`APPORDERNO`,`inv_no`,`customer_id`,`sub_dealer_id`,`dns_prod_code`,`prod_desc`,`inv_qty`,`allocation_qty`,`inv_cancl`,`delete_at`,`is_offline`,`created_by`,`month`)
		VALUES
		('".$old_row["allocation_id"]."','".$old_row["date_and_time"]."','".$old_row["inv_date"]."','$old_allocation_date','".$old_row["order_id"]."','".$old_row["APPORDERNO"]."','".$old_row["inv_no"]."','".$old_row["customer_id"]."','".$old_row["sub_dealer_id"]."','".$old_row["dns_prod_code"]."','".$old_row["prod_desc"]."','".$old_row["inv_qty"]."','".$old_row["allocation_qty"]."','".$old_row["inv_cancl"]."','".$old_row["delete_at"]."','".$old_row["is_offline"]."','$created_by','$month')";
	mysql_query($sqlins_old);

	// 2. Apply the actual change on the live table (only sub_dealer_id changes)
	$sqlupd = "UPDATE $allocation_details
	           SET `sub_dealer_id`='$new_sub_dealer_id'
	           WHERE `allocation_id`='".$old_row["allocation_id"]."'";
	$resupd = mysql_query($sqlupd);

	if ($resupd) {

		// 3. Log NEW (post-update) version into history
		$new_allocation_date = "NEW-" . $current_date;
		$sqlins_new = "INSERT INTO $allocation_details_history
			(`allocation_id`,`date_and_time`,`inv_date`,`allocation_date`,`order_id`,`APPORDERNO`,`inv_no`,`customer_id`,`sub_dealer_id`,`dns_prod_code`,`prod_desc`,`inv_qty`,`allocation_qty`,`inv_cancl`,`delete_at`,`is_offline`,`created_by`,`month`)
			VALUES
			('".$old_row["allocation_id"]."','".$old_row["date_and_time"]."','".$old_row["inv_date"]."','$new_allocation_date','".$old_row["order_id"]."','".$old_row["APPORDERNO"]."','".$old_row["inv_no"]."','".$old_row["customer_id"]."','$new_sub_dealer_id','".$old_row["dns_prod_code"]."','".$old_row["prod_desc"]."','".$old_row["inv_qty"]."','".$old_row["allocation_qty"]."','".$old_row["inv_cancl"]."','".$old_row["delete_at"]."','".$old_row["is_offline"]."','$created_by','$month')";
		mysql_query($sqlins_new);

		$res_data = array("process_status" => "YES", "process_message" => "Sub dealer updated successfully.");

	} else {
		$res_data = array("process_status" => "NO", "process_message" => "Failed to update sub dealer.");
	}

} else {
	$res_data = array("process_status" => "NO", "process_message" => "No matching allocation record found for the given APPORDERNO / inv_no.");
}

echo json_encode($res_data);
mysql_close();
?>
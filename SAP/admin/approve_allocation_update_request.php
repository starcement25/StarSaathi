<?php
/*
====================================================================
 approve_allocation_update_request.php
 ... (unchanged header comments) ...
====================================================================
*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__.'/phpmailer_new/PHPMailer.php';
require __DIR__.'/phpmailer_new/SMTP.php';
require __DIR__.'/phpmailer_new/Exception.php';

include "star_connection.php";
include "function-sfa.php";

date_default_timezone_set('Asia/Kolkata');

$allocation_details_history  = "allocation_details_invoicewise_history";
$allocation_details          = "allocation_details_invoicewise";

$res_data = array();

// --------------------------------------------------------------
// METHOD CHECK: POST only
// --------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] != "POST") {
	$res_data = array("process_status" => "NO", "process_message" => "This endpoint only accepts POST requests.");
	echo json_encode($res_data);
	die;
}

$dns_emp_code = isset($_POST["dns_emp_code"]) ? addslashes(trim($_POST["dns_emp_code"])) : "";
$history_id   = isset($_POST["history_id"])   ? addslashes(trim($_POST["history_id"]))   : "";
$action       = isset($_POST["action"])       ? strtolower(trim($_POST["action"]))       : "";

if ($dns_emp_code == '' || $history_id == '' || $action == '') {
	$res_data = array("process_status" => "NO", "process_message" => "dns_emp_code, history_id and action are all required.");
	echo json_encode($res_data);
	die;
}

if (!in_array($action, array("approve", "reject"))) {
	$res_data = array("process_status" => "NO", "process_message" => "action must be either 'approve' or 'reject'.");
	echo json_encode($res_data);
	die;
}

$action = addslashes($action);

// --------------------------------------------------------------
// STEP 1: fetch the pending request AND authorize in one shot
// --------------------------------------------------------------
$sql_req = "SELECT * FROM $allocation_details_history
            WHERE `history_id`='$history_id'
            AND `approval_status`='Pending'
            AND FIND_IN_SET('$dns_emp_code', REPLACE(`related_asm`,' ',''))
            LIMIT 1";
$res_req = mysql_query($sql_req);

if (!$res_req || mysql_num_rows($res_req) == 0) {
	$res_data = array("process_status" => "NO", "process_message" => "No pending request found for this history_id, or you are not authorized to act on it.");
	echo json_encode($res_data);
	die;
}

$req_row = mysql_fetch_assoc($res_req);
$req_created_by = $req_row["created_by"]; // the requester's customer_id, kept in the response for reference

// --------------------------------------------------------------
// STEP 2: ACT — mark history row Approved or Rejected.
// --------------------------------------------------------------
$acted_date = date("Y-m-d H:i:s");
$new_status = ($action == "approve") ? "Approved" : "Rejected";

$sql_action = "UPDATE $allocation_details_history
               SET `approval_status`='$new_status',
                   `approved_by`='$dns_emp_code',
                   `approved_date`='$acted_date'
               WHERE `history_id`='$history_id'
               AND `approval_status`='Pending'";
$res_action = mysql_query($sql_action);

if (!$res_action || mysql_affected_rows() == 0) {
	$res_data = array("process_status" => "NO", "process_message" => "Failed to $action the request (it may have already been processed).");
	echo json_encode($res_data);
	die;
}

if ($action == "approve") {

	// -----------------------------------------------------------
	// APPROVE: soft-delete the ORIGINAL live row and INSERT a new
	// row representing this approved split.
	// -----------------------------------------------------------
	$sql_soft_delete = "UPDATE $allocation_details
	                     SET `delete_at`='1'
	                     WHERE `allocation_id`='".$req_row["allocation_id"]."' AND `auto_allocation`='Yes'";
	$res_soft_delete = mysql_query($sql_soft_delete);

	if (!$res_soft_delete) {
		$res_data = array("process_status" => "NO", "process_message" => "Request marked approved, but failed to deactivate the original allocation record. Please check manually. Error: " . mysql_error());
		echo json_encode($res_data);
		die;
	}

	$sql_insert_new = "INSERT INTO $allocation_details
		(`date_and_time`,`inv_date`,`order_id`,`APPORDERNO`,`inv_no`,`customer_id`,`sub_dealer_id`,`dns_prod_code`,`prod_desc`,`inv_qty`,`allocation_qty`,`inv_cancl`,`delete_at`,`is_offline`,`auto_allocation`)
		VALUES
		(NOW(),'".$req_row["inv_date"]."','".$req_row["order_id"]."','".$req_row["APPORDERNO"]."','".$req_row["inv_no"]."','".$req_row["customer_id"]."','".$req_row["new_sub_dealer_id"]."','".$req_row["dns_prod_code"]."','".$req_row["prod_desc"]."','".$req_row["inv_qty"]."','".$req_row["new_qty"]."','".$req_row["inv_cancl"]."','0','".$req_row["is_offline"]."','No')";
	$res_insert_new = mysql_query($sql_insert_new);

	if (!$res_insert_new) {
		$res_data = array("process_status" => "NO", "process_message" => "Request marked approved and original record deactivated, but failed to insert the new allocation record. Please check manually. Error: " . mysql_error());
		echo json_encode($res_data);
		die;
	}

	$new_allocation_id = mysql_insert_id();
}

// --------------------------------------------------------------
// STEP 3: EMAIL NOTIFICATION — send directly via PHPMailer/SMTP
// (same settings as the queue-processor script) to the requester
// (created_by = customer_master.customer_id). This is sent inline,
// so a slow/unreachable SMTP server will delay this API's response;
// failures are caught and do NOT block the approve/reject result —
// the DB changes above have already been committed by this point.
// --------------------------------------------------------------
$sql_customer = "SELECT `customer_name`,`email`
                  FROM customer_master
                  WHERE `customer_id`='".addslashes($req_created_by)."'
                  LIMIT 1";
	// echo $sql_customer;			 
$res_customer = mysql_query($sql_customer);
// $cust_row = mysql_fetch_assoc($res_customer);
//  echo  $cust_row["customer_name"];die;

$email_send_status = "NOT_ATTEMPTED";

if ($res_customer && mysql_num_rows($res_customer) > 0) {

	$cust_row = mysql_fetch_assoc($res_customer);
	$cust_email = trim($cust_row["email"]);
	$cust_name  = $cust_row["customer_name"];

	if ($cust_email != '' && filter_var($cust_email, FILTER_VALIDATE_EMAIL)) {

		if ($action == "approve") {
			$email_subject = "Allocation Change Request Approved - " . $req_row["APPORDERNO"];
			$email_body = "
				<p>Dear " . htmlspecialchars($cust_name) . ",</p>
				<p>Your sub-dealer allocation change request has been <b>Approved</b>.</p>
				<table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse;'>
					<tr><td><b>APPORDERNO</b></td><td>" . htmlspecialchars($req_row["APPORDERNO"]) . "</td></tr>
					<tr><td><b>Invoice No</b></td><td>" . htmlspecialchars($req_row["inv_no"]) . "</td></tr>
					<tr><td><b>Invoice Date</b></td><td>" . htmlspecialchars($req_row["inv_date"]) . "</td></tr>
					<tr><td><b>Old Sub Dealer</b></td><td>" . htmlspecialchars($req_row["old_sub_dealer_id"]) . "</td></tr>
					<tr><td><b>New Sub Dealer</b></td><td>" . htmlspecialchars($req_row["new_sub_dealer_id"]) . "</td></tr>
					<tr><td><b>Old Qty</b></td><td>" . htmlspecialchars($req_row["old_qty"]) . "MT</td></tr>
					<tr><td><b>New Qty</b></td><td>" . htmlspecialchars($req_row["new_qty"]) . "MT</td></tr>
					<tr><td><b>Approved By</b></td><td>" . htmlspecialchars($dns_emp_code) . "</td></tr>
					<tr><td><b>Approved Date</b></td><td>" . htmlspecialchars($acted_date) . "</td></tr>
				</table>
				<p>Regards,<br>Starsaathi Team</p>
			";
		} else {
			$email_subject = "Allocation Change Request Rejected - " . $req_row["APPORDERNO"];
			$email_body = "
				<p>Dear " . htmlspecialchars($cust_name) . ",</p>
				<p>Your sub-dealer allocation change request has been <b>Rejected</b>.</p>
				<table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse;'>
					<tr><td><b>APPORDERNO</b></td><td>" . htmlspecialchars($req_row["APPORDERNO"]) . "</td></tr>
					<tr><td><b>Invoice No</b></td><td>" . htmlspecialchars($req_row["inv_no"]) . "</td></tr>
						<tr><td><b>Invoice Date</b></td><td>" . htmlspecialchars($req_row["inv_date"]) . "</td></tr>
					<tr><td><b>Old Sub Dealer</b></td><td>" . htmlspecialchars($req_row["old_sub_dealer_id"]) . "</td></tr>
					<tr><td><b>Requested New Sub Dealer</b></td><td>" . htmlspecialchars($req_row["new_sub_dealer_id"]) . "</td></tr>
					<tr><td><b>Old Qty</b></td><td>" . htmlspecialchars($req_row["old_qty"]) . "MT</td></tr>
					<tr><td><b>New Qty</b></td><td>" . htmlspecialchars($req_row["new_qty"]) . "MT</td></tr>
					<tr><td><b>Rejected By</b></td><td>" . htmlspecialchars($dns_emp_code) . "</td></tr>
					<tr><td><b>Rejected Date</b></td><td>" . htmlspecialchars($acted_date) . "</td></tr>
				</table>
				<p>Regards,<br>Starsaathi Team</p>
			";
		}

		$mail = new PHPMailer(true);
		try {
			// SMTP Config — same as email_queue processor
			$mail->isSMTP();
			$mail->Host = "cloudmail2.up99plus.com";
			$mail->Port = 25;
			// $mail->SMTPDebug  = "2s";  
			$mail->SMTPAuth = true;
			$mail->Username = "starcement@cloudmail.up99plus.com";
			$mail->Password = "Nh26sjqgWk";
			$mail->SMTPSecure = false;
			$mail->SMTPAutoTLS = false;

			$mail->setFrom('starcement@cloudmail.up99plus.com', 'Starsaathi');
			$mail->addAddress($cust_email, $cust_name);

			$mail->isHTML(true);
			$mail->Subject = $email_subject;
			$mail->MsgHTML($email_body);

			$mail->send();
			$email_send_status = "SENT";

		} catch (Exception $e) {
			$email_send_status = "FAILED: " . $mail->ErrorInfo;
		}
	} else {
		$email_send_status = "SKIPPED: no valid email on file";
	}
} else {
	$email_send_status = "SKIPPED: customer not found";
}

$res_data = array(
	"process_status"  => "YES",
	"process_message" => $action == "approve" ? "Request approved. Original allocation record deactivated and a new split record created." : "Request rejected successfully.",
	"data" => array(
		"history_id"          => $history_id,
		"allocation_id"       => $req_row["allocation_id"],
		"new_allocation_id"   => ($action == "approve") ? $new_allocation_id : null,
		"customer_id"         => $req_row["customer_id"],
		"created_by"          => $req_created_by,
		"old_sub_dealer_id"   => $req_row["old_sub_dealer_id"],
		"new_sub_dealer_id"   => $req_row["new_sub_dealer_id"],
		"old_qty"             => $req_row["old_qty"],
		"new_qty"             => $req_row["new_qty"],
		"approved_by"         => $dns_emp_code,
		"approval_status"     => $new_status,
		"approved_date"       => $acted_date,
		"email_send_status"   => $email_send_status
	)
);

echo json_encode($res_data);
mysql_close();
?>
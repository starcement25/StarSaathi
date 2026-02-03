<?php
ini_set('memory_limit', '99999M');
set_time_limit(0);
include "star_connection.php";
include "function-sfa.php";
$t_subdealer_order = "T_SUBDEALER_ORDER";
$t_apperpdo_temp = "T_APPERPDO_TEMP";
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";
$product_master = "product_master";
$broker_master = "broker_master";
$branch_master = "branch_master";
$app_setting_master = "app_setting_master";
$destination_wise_price = "destination_wise_price";
$branch_credit_limit_status = "branch_credit_limit_status";
$zorderdetails = "zorderdetailsp";
$lifting_table = "lifting";
$self_appraisal_product_wise = "self_appraisal_product_wise";
$ostotal = 0;
$ledg_pending_orders = 0;
$ledg_cust_code = "";
$process_message = "";


$dealer_cust_code = $_POST["dealer_cust_code"] ? addslashes(trim($_POST["dealer_cust_code"])) : "";
$the_lifting_id = $_POST["the_lifting_id"] ? addslashes(trim($_POST["the_lifting_id"])) : "";
$the_status = $_POST["the_status"] ? addslashes(trim($_POST["the_status"])) : "";
$reason_for_rejection = $_POST["reason_for_rejection"] ? addslashes(trim($_POST["reason_for_rejection"])) : "";

if ($dealer_cust_code == "") {
	$res_data = array("process_status" => "NO", "process_message" => "Please provide dealer code.", "lifting_history_data" => array());
} else if ($the_lifting_id == "") {
	$res_data = array("process_status" => "NO", "process_message" => "Please provide lifting ID.", "lifting_history_data" => array());
} else if ($the_status == "") {
	$res_data = array("process_status" => "NO", "process_message" => "Please choose a status.", "lifting_history_data" => array());
} else if ($the_status == "REJECTED" && $reason_for_rejection == "") {
	$res_data = array("process_status" => "NO", "process_message" => "Please enter reason for rejection.", "lifting_history_data" => array());
} else {
	if ($the_status != "REJECTED") {
		$reason_for_rejection = "";
	}
	$lifting_history_data = array();
	$sql3 = "select * from $lifting_table where `linked_dealer_cust_code`='$dealer_cust_code' and `status`='PENDING' order by `date_of_lifting` desc";
	$res3 = mysql_query($sql3);
	$totres3 = mysql_num_rows($res3);
	if ($totres3 > 0) {
		while ($row3 = mysql_fetch_assoc($res3)) {
			$lid = $row3["lid"];
			$linked_dealer_cust_code = $row3["linked_dealer_cust_code"] ? trim($row3["linked_dealer_cust_code"]) : "";
			$linked_dealer_code = $row3["linked_dealer_code"] ? trim($row3["linked_dealer_code"]) : "";
			$linked_dealer_sap_code = $row3["linked_dealer_sap_code"] ? trim($row3["linked_dealer_sap_code"]) : "";
			$linked_dealer_name = $row3["linked_dealer_name"] ? trim($row3["linked_dealer_name"]) : "";
			$sub_dealer_cust_code = $row3["sub_dealer_cust_code"] ? trim($row3["sub_dealer_cust_code"]) : "";
			$sub_dealer_rssd_code = $row3["sub_dealer_rssd_code"] ? trim($row3["sub_dealer_rssd_code"]) : "";
			$sub_dealer_rssd_sap_code = $row3["sub_dealer_rssd_sap_code"] ? trim($row3["sub_dealer_rssd_sap_code"]) : "";
			$sub_dealer_rssd_name = $row3["sub_dealer_rssd_name"] ? trim($row3["sub_dealer_rssd_name"]) : "";
			$branch_code = $row3["branch_code"] ? trim($row3["branch_code"]) : "";
			$dns_branch_code = $row3["dns_branch_code"] ? trim($row3["dns_branch_code"]) : "";
			$branch = $row3["branch"] ? trim($row3["branch"]) : "";
			$month = $row3["month"] ? trim($row3["month"]) : "";
			$prod_code = $row3["prod_code"] ? trim($row3["prod_code"]) : "";
			$dns_prod_code = $row3["dns_prod_code"] ? trim($row3["dns_prod_code"]) : "";
			$prod_display_name = $row3["prod_display_name"] ? trim($row3["prod_display_name"]) : "";
			$total_bags = $row3["total_bags"] ? trim($row3["total_bags"]) : "";
			$date_of_lifting = $row3["date_of_lifting"] ? trim($row3["date_of_lifting"]) : "";
			$date_of_lifting_show = "";
			if ($date_of_lifting != "") {
				$date_of_lifting_show = date("jS M,Y", strtotime($date_of_lifting));
			}
			$challan_no = $row3["challan_no"] ? trim($row3["challan_no"]) : "";
			$submit_date_time = $row3["submit_date_time"] ? trim($row3["submit_date_time"]) : "";
			$status = $row3["status"] ? trim($row3["status"]) : "";
			$status_date_and_time = $row3["status_date_and_time"] ? trim($row3["status_date_and_time"]) : "";
			$reason_for_rejection = $row3["reason_for_rejection"] ? trim($row3["reason_for_rejection"]) : "";
			$total_subdealer_rssd_sale = $row3["total_subdealer_rssd_sale"] ? trim($row3["total_subdealer_rssd_sale"]) : "";
			$total_dealer_sale = $row3["total_dealer_sale"] ? trim($row3["total_dealer_sale"]) : "";
			$subdealer_rssd_sale_percent = $row3["subdealer_rssd_sale_percent"] ? trim($row3["subdealer_rssd_sale_percent"]) : "";
			$approved_rejection_date = "";
			$approved_rejection_date_show = "";

			if ($status_date_and_time != "") {
				$approved_rejection_date = $status_date_and_time;
				$approved_rejection_date_show = date("jS M,Y", strtotime($approved_rejection_date));
			}
			$lifting_history_data[] = array("lid" => $lid, "product_name" => $prod_display_name, "qty_in_bags" => $total_bags, "date_of_lifting" => $date_of_lifting, "date_of_lifting_show" => $date_of_lifting_show, "challan_number" => $challan_no, "status" => $status, "approved_rejection_date" => $approved_rejection_date, "approved_rejection_date_show" => $approved_rejection_date_show, "reason_for_rejection" => $reason_for_rejection, "sub_dealer_cust_code" => $sub_dealer_cust_code, "sub_dealer_rssd_name" => $sub_dealer_rssd_name, "sub_dealer_rssd_code" => $sub_dealer_rssd_code, "sub_dealer_rssd_sap_code" => $sub_dealer_rssd_sap_code);
		}
	}
	$upd_pros = "YES";
	$upd_pros_msg = "";
	$dealer_achievement_sapw = 0;
	$curr_month_tot_apprv_bags_acv_done = 0;
	if ($the_status == "APPROVED") {

		$sql_led = "select `linked_dealer_code`,`status`,`date_of_lifting`,`total_bags`, `branch_code` from $lifting_table where `lid`='$the_lifting_id' and `linked_dealer_cust_code`='$dealer_cust_code'";
		$res_led = mysql_query($sql_led);
		$totres_led = mysql_num_rows($res_led);
		if ($totres_led > 0) {
			$row_led = mysql_fetch_assoc($res_led);
			$linked_dealer_code_led = $row_led["linked_dealer_code"];
			$status_led = $row_led["status"];
			$date_of_lifting_led = $row_led["date_of_lifting"];
			$total_bags_led = trim($row_led["total_bags"]);
			$branch_code_led = trim($row_led["branch_code"]);

			// check for approval_last_date and current date for lifting_date_validation
			$sql_chk_apprv_last_date = "select `approval_last_date`,`validation_from`,`validation_to` from lifting_date_validation where `branch`='$branch_code_led'";
			$res_chk_apprv_last_date = mysql_query($sql_chk_apprv_last_date);
			$totres_chk_apprv_last_date = mysql_num_rows($res_chk_apprv_last_date);
			if ($totres_chk_apprv_last_date > 0) {
				$row_chk_apprv_last_date = mysql_fetch_assoc($res_chk_apprv_last_date);
				$approval_last_date = $row_chk_apprv_last_date["approval_last_date"];
				$validation_from = $row_chk_apprv_last_date["validation_from"];
				$validation_to = $row_chk_apprv_last_date["validation_to"];
				if ($approval_last_date != "") {
					$approval_last_date = date("Y-m-d", strtotime($approval_last_date));
					$validation_from = date("Y-m-d", strtotime($validation_from));
					$validation_to = date("Y-m-d", strtotime($validation_to));
					$date_of_lifting_led_format = date("Y-m-d", strtotime($date_of_lifting_led));
					$current_date = date("Y-m-d");
					if ($current_date > $approval_last_date || $date_of_lifting_led_format < $validation_from || $date_of_lifting_led_format > $validation_to) {
						$upd_pros = "NO";
						//$upd_pros_msg = "Please contact Admin.";
						$upd_pros_msg = "Your cut-off date for RSSD lifting approvals are over. Please contact your sales officer.";
					}
				}
			}
			if ($upd_pros != "NO") {
				if ($total_bags_led == "") {
					$total_bags_led = 0;
				}
				$lifting_year_month_led = date("Y-m", strtotime($date_of_lifting_led));
				$lifting_month_led = date("m", strtotime($date_of_lifting_led));


				$sql_acv_done = "select sum(`total_bags`) as `curr_month_tot_apprv_bags` from $lifting_table where `linked_dealer_cust_code`='$dealer_cust_code' and `status`='APPROVED' and DATE_FORMAT(`date_of_lifting`,'%Y-%m')='$lifting_year_month_led'";
				$res_acv_done = mysql_query($sql_acv_done);
				$totres_acv_done = mysql_num_rows($res_acv_done);
				if ($totres_acv_done > 0) {
					$row_acv_done = mysql_fetch_assoc($res_acv_done);
					$curr_month_tot_apprv_bags_acv_done = $row_acv_done["curr_month_tot_apprv_bags"];
					if ($curr_month_tot_apprv_bags_acv_done == "") {
						$curr_month_tot_apprv_bags_acv_done = 0;
					}
				}

				$curr_month_tot_apprv_bags_acv_done = ($curr_month_tot_apprv_bags_acv_done + $total_bags_led);
				$curr_month_tot_apprv_bags_acv_done_mt = round(($curr_month_tot_apprv_bags_acv_done / 20), 2);

				$sql_sapw = "select `jan_31_achievement`,`feb_28_achievement`,`mar_31_achievement`,`apr_30_achievement`,`may_31_achievement`,`jun_30_achievement`,`jul_31_achievement`,`aug_31_achievement`,`sep_30_achievement`,`oct_31_achievement`,`nov_30_achievement`,`dec_31_achievement` from $self_appraisal_product_wise where `customer_code`='$linked_dealer_code_led'";
				// echo $sql_sapw;
				// echo "Month:".$lifting_month_led;
				$res_sapw = mysql_query($sql_sapw);
				$totres_sapw = mysql_num_rows($res_sapw);
				if ($totres_sapw > 0) {
					$row_sapw = mysql_fetch_assoc($res_sapw);

					if ($lifting_month_led == 1) {
						$dealer_achievement_sapw = trim($row_sapw['jan_31_achievement']);
					} else if ($lifting_month_led == 2) {
						$dealer_achievement_sapw = trim($row_sapw['feb_28_achievement']);
					} else if ($lifting_month_led == 3) {
						$dealer_achievement_sapw = trim($row_sapw['mar_31_achievement']);
					} else if ($lifting_month_led == 4) {
						$dealer_achievement_sapw = trim($row_sapw['apr_30_achievement']);
					} else if ($lifting_month_led == 5) {
						$dealer_achievement_sapw = trim($row_sapw['may_31_achievement']);
					} else if ($lifting_month_led == 6) {
						$dealer_achievement_sapw = trim($row_sapw['jun_30_achievement']);
					} else if ($lifting_month_led == 7) {
						$dealer_achievement_sapw = trim($row_sapw['jul_31_achievement']);
					} else if ($lifting_month_led == 8) {
						$dealer_achievement_sapw = trim($row_sapw['aug_31_achievement']);
					} else if ($lifting_month_led == 9) {
						$dealer_achievement_sapw = trim($row_sapw['sep_30_achievement']);
					} else if ($lifting_month_led == 10) {
						$dealer_achievement_sapw = trim($row_sapw['oct_31_achievement']);
					} else if ($lifting_month_led == 11) {
						$dealer_achievement_sapw = trim($row_sapw['nov_30_achievement']);
					} else if ($lifting_month_led == 12) {
						$dealer_achievement_sapw = trim($row_sapw['dec_31_achievement']);
					}
					if ($dealer_achievement_sapw == "") {
						$dealer_achievement_sapw = 0;
					}

					if ($curr_month_tot_apprv_bags_acv_done_mt > $dealer_achievement_sapw) {
						$upd_pros = "NO";
						$upd_pros_msg = "Retailer Lifting exceeds your Monthly Sales Volume. Please check your own Monthly Sale and Approved Subdealer/RSSD Sale.";
					}
				} else {
					$upd_pros = "NO";
					$upd_pros_msg = "Dealer monthly achievement not yet set.";
				}
			}
		} else {
			$upd_pros = "NO";
			$upd_pros_msg = "Something went wrong. Please try later.";
		}
	}

	if ($upd_pros == "NO") {
		$process_message = $upd_pros_msg;
		$process_status = $upd_pros;
	} else {

		$status_date_and_time = date("Y-m-d H:i:s");
		$sql_upd = "update $lifting_table set `status`='$the_status',`status_date_and_time`='$status_date_and_time',`reason_for_rejection`='$reason_for_rejection' where `lid`='$the_lifting_id' and `linked_dealer_cust_code`='$dealer_cust_code'";
		$res_upd = mysql_query($sql_upd);
		$process_message = "Status successfully updated.";
		$process_status = "YES";
	}


	$res_data = array("process_status" => $process_status, "process_message" => $process_message, "lifting_history_data" => $lifting_history_data);
}


echo json_encode($res_data);

mysql_close();
?>
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
$ostotal = 0;
$lifting_history_data = array();
$ledg_cust_code = "";
$process_message = "";

function get_branch_data_by_id($the_branch_code){
$branch_data = array("branch_name"=>"","dns_branch_code"=>"");
	$branch_master = "branch_master";
	$the_branch_code = $the_branch_code ? addslashes(trim($the_branch_code)) : "";
	if($the_branch_code!=""){
				$sqls_bd = "select `branch_name`,`dns_branch_code` from $branch_master where `branch_code`='$the_branch_code'";
				$ress_bd = mysql_query($sqls_bd);
				$totress_bd = mysql_num_rows($ress_bd);
				if($totress_bd>0){
					$rows_bd = mysql_fetch_assoc($ress_bd);
					$the_branch_name_ftc = $rows_bd["branch_name"] ? addslashes(trim($rows_bd["branch_name"])) : "";
					$the_dns_branch_code_ftc = $rows_bd["dns_branch_code"] ? addslashes(trim($rows_bd["dns_branch_code"])) : "";
					$branch_data = array("branch_name"=>$the_branch_name_ftc,"dns_branch_code"=>$the_dns_branch_code_ftc);
				}
			}
	return $branch_data;
}

function get_branch_data_from_cuat_id($cust_id){
$branch_data = array("branch_code"=>"","dns_branch_code"=>"");
	$customer_master = "customer_master";
	$branch_master = "branch_master";
	$custname = "";
	$cust_id = $cust_id ? addslashes(trim($cust_id)) : "";
	if($cust_id!=''){
		$sqls = "select `branch_code` from $customer_master where `customer_code`='$cust_id'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if($totress>0){
			$rows = mysql_fetch_assoc($ress);
			$the_branch_code = $rows["branch_code"] ? trim($rows["branch_code"]) : "";
			if($the_branch_code!=""){
				$sqls_bd = "select `branch_code`,`dns_branch_code` from $branch_master where `branch_code`='$the_branch_code'";
				$ress_bd = mysql_query($sqls_bd);
				$totress_bd = mysql_num_rows($ress_bd);
				if($totress_bd>0){
					$rows_bd = mysql_fetch_assoc($ress_bd);
					$the_branch_code_ftc = $rows_bd["branch_code"] ? addslashes(trim($rows_bd["branch_code"])) : "";
					$the_dns_branch_code_ftc = $rows_bd["dns_branch_code"] ? addslashes(trim($rows_bd["dns_branch_code"])) : "";
					$branch_data = array("branch_code"=>$the_branch_code_ftc,"dns_branch_code"=>$the_dns_branch_code_ftc);
				}
			}
		}
	}
	return $branch_data;
}

function get_customer_data_by_customer_code($the_customer_code){
$customer_data = array("sts"=>"NO","dns_customer_code"=>"","customer_id"=>"","customer_name"=>"","branch_code"=>"");
$customer_master = "customer_master";
if($the_customer_code!=""){
$sql1 = "select `dns_customer_code`,`customer_id`,`customer_name`,`branch_code` from $customer_master where `customer_code`='$the_customer_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_dns_customer_code = $row1["dns_customer_code"] ? addslashes(trim($row1["dns_customer_code"])) : "";
		$the_customer_id = $row1["customer_id"] ? addslashes(trim($row1["customer_id"])) : "";
		$the_customer_name = $row1["customer_name"] ? addslashes(trim($row1["customer_name"])) : "";
		$the_branch_code = $row1["branch_code"] ? addslashes(trim($row1["branch_code"])) : "";
		$customer_data = array("sts"=>"YES","dns_customer_code"=>$the_dns_customer_code,"customer_id"=>$the_customer_id,"customer_name"=>$the_customer_name,"branch_code"=>$the_branch_code);
	}
}
return $customer_data;
}


function show_product_data_from_prod_code($the_prod_code){
$product_dtls = array("dns_prod_code"=>"","prod_desc"=>"");
$product_master = "product_master";
if($the_prod_code!=""){
$sql1 = "select `dns_prod_code`,`prod_desc` from $product_master where `prod_code`='$the_prod_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$dns_prod_code = $row1["dns_prod_code"] ? addslashes(trim($row1["dns_prod_code"])) : "";
		$prod_desc = $row1["prod_desc"] ? addslashes(trim($row1["prod_desc"])) : "";
		$product_dtls = array("dns_prod_code"=>$dns_prod_code,"prod_desc"=>$prod_desc);
	}
}
return $product_dtls;
}

$curr_year_month = date("Y-m");
$sub_dealer_cust_code = $_POST["sub_dealer_cust_code"] ? addslashes(trim($_POST["sub_dealer_cust_code"])) : "";
$year_month = $_POST["year_month"] ? addslashes(trim($_POST["year_month"])) : $curr_year_month;
if($year_month==""){
$year_month = $curr_year_month;	
}
$page_no = $_POST["page_no"] ? trim($_POST["page_no"]) : 1;
if($page_no==""){
	$page_no = 1;
}
$limit = 20;
$start_from = (($page_no-1)*$limit);


if($sub_dealer_cust_code==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please provide sub-dealer code.","lifting_history_data"=>$lifting_history_data);
}else{

$ym_qry = "";
if($year_month!=""){
$ym_qry = " having `ym_of_lifting`='$year_month' ";
}

$sql3 = "select *,DATE_FORMAT(`date_of_lifting`,'%Y-%m') as `ym_of_lifting` from $lifting_table where `sub_dealer_cust_code`='$sub_dealer_cust_code' $ym_qry order by `date_of_lifting` desc limit $start_from,$limit";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
while($row3 = mysql_fetch_assoc($res3)){
$lid = $row3["lid"];
$linked_dealer_cust_code = $row3["linked_dealer_cust_code"] ? trim($row3["linked_dealer_cust_code"]) : "";
$linked_dealer_code = $row3["linked_dealer_code"] ? trim($row3["linked_dealer_code"]) : "";
$linked_dealer_sap_code = $row3["linked_dealer_sap_code"] ? trim($row3["linked_dealer_sap_code"]) : "";
$linked_dealer_name = $row3["linked_dealer_name"] ? trim($row3["linked_dealer_name"]) : "";
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
if($date_of_lifting!=""){
$date_of_lifting_show = date("jS M,Y",strtotime($date_of_lifting));	
}
$challan_no = $row3["challan_no"] ? trim($row3["challan_no"]) : "";
$submit_date_time = $row3["submit_date_time"] ? trim($row3["submit_date_time"]) : "";
$status = $row3["status"] ? trim($row3["status"]) : "";
$status_date_and_time = $row3["status_date_and_time"] ? trim($row3["status_date_and_time"]) : "";
$reason_for_rejection = $row3["reason_for_rejection"] ? trim($row3["reason_for_rejection"]) : "";
$total_subdealer_rssd_sale = $row3["total_subdealer_rssd_sale"] ? trim($row3["total_subdealer_rssd_sale"]) : "";
$total_dealer_sale = $row3["total_dealer_sale"] ? trim($row3["total_dealer_sale"]) : "";
$subdealer_rssd_sale_percent = $row3["subdealer_rssd_sale_percent"] ? trim($row3["subdealer_rssd_sale_percent"]) : "";

$approved_by = "";
if($status=="APPROVED"){
$approved_by = $linked_dealer_name;
}

$approved_rejection_date = "";
$approved_rejection_date_show = "";


if($status=="APPROVED" || $status=="REJECTED"){
if($status_date_and_time!=""){
	$approved_rejection_date = $status_date_and_time;
    $approved_rejection_date_show = date("jS M,Y",strtotime($approved_rejection_date));	
}	
}
if($status=="APPROVED" || $status=="PENDING"){
$reason_for_rejection = "";
}

$lifting_history_data[] = array("lid"=>$lid,"product_name"=>$prod_display_name,"qty_in_bags"=>$total_bags,"date_of_lifting"=>$date_of_lifting,"date_of_lifting_show"=>$date_of_lifting_show,"challan_number"=>$challan_no,"status"=>$status,"approved_by"=>$approved_by,"approved_rejection_date"=>$approved_rejection_date,"approved_rejection_date_show"=>$approved_rejection_date_show,"reason_for_rejection"=>$reason_for_rejection,"linked_dealer_name"=>$linked_dealer_name,"linked_dealer_cust_code"=>$linked_dealer_cust_code,"linked_dealer_code"=>$linked_dealer_code,"linked_dealer_sap_code"=>$linked_dealer_sap_code);




}


$res_data = array("process_status"=>"YES","process_message"=>"success","lifting_history_data"=>$lifting_history_data);

}else{
$res_data = array("process_status"=>"NO","process_message"=>"No new record found.","lifting_history_data"=>$lifting_history_data);
}

}


echo json_encode($res_data);

mysql_close();
?>
<?php
// ob_start();
// session_start();

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
$ledg_pending_orders = 0;
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


$linked_dealer_cust_code = $_POST["linked_dealer_cust_code"] ? addslashes(trim($_POST["linked_dealer_cust_code"])) : "";
$sub_dealer_cust_code = $_POST["sub_dealer_cust_code"] ? addslashes(trim($_POST["sub_dealer_cust_code"])) : "";
$prod_code = $_POST["prod_code"] ? addslashes(trim($_POST["prod_code"])) : "";
$tot_bag_qty = $_POST["tot_bag_qty"] ? addslashes(trim($_POST["tot_bag_qty"])) : "";
$lifting_date = $_POST["lifting_date"] ? addslashes(trim($_POST["lifting_date"])) : "";
// $_SESSION['lifting_date']=$lifting_date;
$challan_no = $_POST["challan_no"] ? addslashes(trim($_POST["challan_no"])) : "";

if($linked_dealer_cust_code==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please provide linked dealer code.");
}else if($sub_dealer_cust_code==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please provide sub-dealer code.");
}else if($prod_code==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please choose a product.");
}else if($tot_bag_qty==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please enter total bag quantity.");
}else if($lifting_date==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please enter lifting date.");
}else if($challan_no==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please enter challan number.");
}else{
$linked_dealer_code = "";
$linked_dealer_sap_code = "";
$linked_dealer_name = "";
$linked_dealer_branch_code = "";
$linked_dealer_branch_name = "";
$linked_dealer_dns_branch_code = "";

$sub_dealer_rssd_code = "";
$sub_dealer_rssd_sap_code = "";
$sub_dealer_rssd_name = "";

$dns_prod_code = "";
$prod_display_name = "";

$linked_dealer_cust_data = get_customer_data_by_customer_code($linked_dealer_cust_code);
if($linked_dealer_cust_data["sts"]=="YES"){
$linked_dealer_code = $linked_dealer_cust_data["dns_customer_code"];
$linked_dealer_sap_code = $linked_dealer_cust_data["customer_id"];
$linked_dealer_name = $linked_dealer_cust_data["customer_name"];
$linked_dealer_branch_code = $linked_dealer_cust_data["branch_code"];
if($linked_dealer_branch_code!=""){
$linked_dealer_branch_data = get_branch_data_by_id($linked_dealer_branch_code);
$linked_dealer_branch_name = $linked_dealer_branch_data["branch_name"];
$linked_dealer_dns_branch_code = $linked_dealer_branch_data["dns_branch_code"];
}
}


$sub_dealer_cust_data = get_customer_data_by_customer_code($sub_dealer_cust_code);
if($sub_dealer_cust_data["sts"]=="YES"){
$sub_dealer_rssd_code = $sub_dealer_cust_data["dns_customer_code"];
$sub_dealer_rssd_sap_code = $sub_dealer_cust_data["customer_id"];
$sub_dealer_rssd_name = $sub_dealer_cust_data["customer_name"];
}

$product_data = show_product_data_from_prod_code($prod_code);
$dns_prod_code = $product_data["dns_prod_code"];
$prod_display_name = $product_data["prod_desc"];
$submit_date_time = date("Y-m-d H:i:s");

$sql_in = "insert into $lifting_table (`linked_dealer_cust_code`,`linked_dealer_code`,`linked_dealer_sap_code`,`linked_dealer_name`,`sub_dealer_cust_code`,`sub_dealer_rssd_code`,`sub_dealer_rssd_sap_code`,`sub_dealer_rssd_name`,`branch_code`,`dns_branch_code`,`branch`,`prod_code`,`dns_prod_code`,`prod_display_name`,`total_bags`,`date_of_lifting`,`challan_no`,`submit_date_time`) values ('$linked_dealer_cust_code','$linked_dealer_code','$linked_dealer_sap_code','$linked_dealer_name','$sub_dealer_cust_code','$sub_dealer_rssd_code','$sub_dealer_rssd_sap_code','$sub_dealer_rssd_name','$linked_dealer_branch_code','$linked_dealer_dns_branch_code','$linked_dealer_branch_name','$prod_code','$dns_prod_code','$prod_display_name','$tot_bag_qty','$lifting_date','$challan_no','$submit_date_time')";
$res_in = mysql_query($sql_in);
if($res_in){

$res_data = array("process_status"=>"YES","process_message"=>"Lifting submitted. Pending for approval by linked Dealer.");
}else{
$res_data = array("process_status"=>"NO","process_message"=>"Failed to save details.");
}
}


echo json_encode($res_data);

mysql_close();
?>
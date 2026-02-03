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
$ageing = "ageing";
$ostotal = 0;
$ledg_pending_orders = 0;
$ledg_cust_code = "";
$process_message = "";
$app_setting_key_arr = array("carry_forward_process"=>"ACTIVE","carry_forward_process_message"=>"");
$sql_ckredius = "select `the_key_name`,`the_value` from $app_setting_master where `the_key_name` in('carry_forward_process','carry_forward_process_message')";
$res_ckredius = mysql_query($sql_ckredius);
$totres_ckredius = mysql_num_rows($res_ckredius);
if($totres_ckredius>0){
	while($row_ckredius=mysql_fetch_assoc($res_ckredius)){
		$the_key_name = $row_ckredius["the_key_name"];
		$the_key_value = $row_ckredius["the_value"] ? trim($row_ckredius["the_value"]) : "";
		if($the_key_name!=""){
			$app_setting_key_arr[$the_key_name] = $the_key_value;
		}
	}
}
$carry_forward_process_st = trim($app_setting_key_arr["carry_forward_process"]);
if($carry_forward_process_st==""){
$carry_forward_process_st = "ACTIVE";	
}
$carry_forward_process_message_st = trim($app_setting_key_arr["carry_forward_process_message"]);
function get_os_by_dealer_id($dealer_id){
$ageing = "ageing";
$my_os_amount = 0;
$dealer_id = $dealer_id ? addslashes(trim($dealer_id)) : "";
if($dealer_id!=''){
$sqls = "select `ostotal` from $ageing where `alias`='$dealer_id'";
$ress = mysql_query($sqls);
$totress = mysql_num_rows($ress);
if($totress>0){
$rows = mysql_fetch_assoc($ress);
$my_os_amount = $rows["ostotal"] ? trim($rows["ostotal"]) : 0;
}
}
return $my_os_amount;
}
function get_os_by_dealer_id_and_cr_days($dealer_id,$cr_days){
	$ageing = "ageing";
	$my_os_amount = 0;
	$dealer_id = $dealer_id ? addslashes(trim($dealer_id)) : "";
	$cr_days = $cr_days ? trim($cr_days) : 0;
	if($cr_days==""){
		$cr_days = 0;
	}
	if($dealer_id!=''){
		$sqls = "select * from $ageing where `alias`='$dealer_id'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if($totress>0){
			$rows = mysql_fetch_assoc($ress);
$vl_less_equ_10days = $rows["less_equ_10days"] ? trim($rows["less_equ_10days"]) : 0;
$vl_11_17days = $rows["11_17days"] ? trim($rows["11_17days"]) : 0;
$vl_18_25days = $rows["18_25days"] ? trim($rows["18_25days"]) : 0;
$vl_26_30days = $rows["26_30days"] ? trim($rows["26_30days"]) : 0;
$vl_31_45days = $rows["31_45days"] ? trim($rows["31_45days"]) : 0;
$vl_46_60days = $rows["46_60days"] ? trim($rows["46_60days"]) : 0;
$vl_61_90days = $rows["61_90days"] ? trim($rows["61_90days"]) : 0;
$vl_91_120days = $rows["91_120days"] ? trim($rows["91_120days"]) : 0;
$vl_121_180days = $rows["121_180days"] ? trim($rows["121_180days"]) : 0;
$vl_greater_180days = $rows["greater_180days"] ? trim($rows["greater_180days"]) : 0;
$the_arr = array();
$the_arr[] = array("range_from"=>0,"range_to"=>10,"os_amount"=>$vl_less_equ_10days);
$the_arr[] = array("range_from"=>11,"range_to"=>17,"os_amount"=>$vl_11_17days);
$the_arr[] = array("range_from"=>18,"range_to"=>25,"os_amount"=>$vl_18_25days);
$the_arr[] = array("range_from"=>26,"range_to"=>30,"os_amount"=>$vl_26_30days);
$the_arr[] = array("range_from"=>31,"range_to"=>45,"os_amount"=>$vl_31_45days);
$the_arr[] = array("range_from"=>46,"range_to"=>60,"os_amount"=>$vl_46_60days);
$the_arr[] = array("range_from"=>61,"range_to"=>90,"os_amount"=>$vl_61_90days);
$the_arr[] = array("range_from"=>91,"range_to"=>120,"os_amount"=>$vl_91_120days);
$the_arr[] = array("range_from"=>121,"range_to"=>180,"os_amount"=>$vl_121_180days);
$the_arr[] = array("range_from"=>0,"range_to"=>180,"os_amount"=>$vl_greater_180days);			
if(count($the_arr)>0){
	foreach($the_arr as $the_arr_val){
		$range_to = $the_arr_val["range_to"];
		$os_amount = $the_arr_val["os_amount"];
		if($range_to>$credit_days){
			$my_os_amount = ($my_os_amount + $os_amount);
		}
	}
}	
			
}
}
	return $my_os_amount;
}
function get_broker_name_from_id($cust_id){
	$broker_master = "broker_master";
	$custname = "";
	$cust_id = $cust_id ? addslashes(trim($cust_id)) : "";
	if($cust_id!=''){
		$sqls = "select `broker_name` from $broker_master where `dns_broker_id`='$cust_id'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if($totress>0){
			$rows = mysql_fetch_assoc($ress);
			$custname = $rows["broker_name"] ? trim($rows["broker_name"]) : "";
		}
	}
	return $custname;
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
function get_customer_name_from_id($cust_id){
	$customer_master = "customer_master";
	$custname = "";
	$cust_id = $cust_id ? addslashes(trim($cust_id)) : "";
	if($cust_id!=''){
		$sqls = "select `customer_name` from $customer_master where `customer_code`='$cust_id'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if($totress>0){
			$rows = mysql_fetch_assoc($ress);
			$custname = $rows["customer_name"] ? trim($rows["customer_name"]) : "";
		}
	}
	return $custname;
}
function fetch_emails_by_destination_code($the_des_code){
$email_str = "";
$email_array = array();
$email_array["branch_name"] = "";
$email_array["email"][] = "starsaathi@gmail.com";
$branch_destination_freight = "branch_destination_freight";
$branch_master = "branch_master";
$the_des_code = $the_des_code ? addslashes(trim($the_des_code)) : "";
if($the_des_code!=""){
$sql1 = "select `branch_code` from $branch_destination_freight where `destination_code`='$the_des_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_branch_code = $row1["branch_code"] ? addslashes(trim($row1["branch_code"])) : "";
		$sql2 = "select `branch_name`,`branch_email_id` from $branch_master where `branch_code`='$the_branch_code'";	
		$res2 = mysql_query($sql2);
		$totres2 = mysql_num_rows($res2);
		if($totres2>0){
			$row2 = mysql_fetch_assoc($res2);
			$the_branch_name = trim($row2["branch_name"]);
			if($the_branch_name!=""){
				$email_array["branch_name"] = $the_branch_name;
			}
			$the_branch_email_id = trim($row2["branch_email_id"]);
			if($the_branch_email_id!=""){
				$email_array["email"][] = $the_branch_email_id;
			}
		}
	}
}
return $email_array;
}
function show_customer_code_from_dns_customer_code($the_dns_customer_code){
$the_cust_code = "";
$customer_master = "customer_master";
if($the_dns_customer_code!=""){
$sql1 = "select `customer_code` from $customer_master where `dns_customer_code`='$the_dns_customer_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_cust_code = $row1["customer_code"] ? addslashes(trim($row1["customer_code"])) : "";
	}
}
return $the_cust_code;
}
function fetch_emails_by_customer_code($the_customer_code){
$email_str = "";
$email_array = array();
$email_array["branch_name"] = "";
$email_array["email"][] = "starsaathi@gmail.com";
$email_array["email"][] = "kaushikshrivastava@starcement.co.in";
$email_array["email"][] = "mridu@forcepower.in";
$branch_destination_freight = "branch_destination_freight";
$branch_master = "branch_master";
$customer_master = "customer_master";
$the_customer_code = $the_customer_code ? addslashes(trim($the_customer_code)) : "";
if($the_customer_code!=""){
$sql1 = "select `branch_code` from $customer_master where `customer_code`='$the_customer_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_branch_code = $row1["branch_code"] ? addslashes(trim($row1["branch_code"])) : "";
		$sql2 = "select `branch_name`,`branch_email_id` from $branch_master where `branch_code`='$the_branch_code'";	
		$res2 = mysql_query($sql2);
		$totres2 = mysql_num_rows($res2);
		if($totres2>0){
			$row2 = mysql_fetch_assoc($res2);
			$the_branch_name = trim($row2["branch_name"]);
			if($the_branch_name!=""){
				$email_array["branch_name"] = $the_branch_name;
			}
			$the_branch_email_id = trim($row2["branch_email_id"]);
			if($the_branch_email_id!=""){
				$email_array["email"][] = $the_branch_email_id;
			}
		}
	}
}
return $email_array;
}
function show_dns_customer_from_customer_code_code($the_customer_code){
$the_cust_code = "";
$customer_master = "customer_master";
if($the_customer_code!=""){
$sql1 = "select `dns_customer_code` from $customer_master where `customer_code`='$the_customer_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_cust_code = $row1["dns_customer_code"] ? addslashes(trim($row1["dns_customer_code"])) : "";
	}
}
return $the_cust_code;
}
function show_SAP_code_from_customer_code_code($the_customer_code){
$the_cust_code = "";
$customer_master = "customer_master";
if($the_customer_code!=""){
$sql1 = "select customer_id from $customer_master where `customer_code`='$the_customer_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_SAP_cust_code = $row1["customer_id"] ? addslashes(trim($row1["customer_id"])) : "";
	}
}
return $the_SAP_cust_code;
}
function show_SAP_LZONE_from_customer_code($the_customer_code){
$the_cust_code = "";
$SAP_customer_master = "ptblcustomermaster";
$customer_master = "customer_master";
if($the_customer_code!=""){
$sql1 = "select LZONE from $SAP_customer_master where KUNNR=(select customer_id from $customer_master where `customer_code`='$the_customer_code') ORDER BY AEDAT DESC,ADDITIONAL_DATA1 DESC LIMIT 0,1";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_SAP_destination = $row1["LZONE"] ? addslashes(trim($row1["LZONE"])) : "";
	}
}
return $the_SAP_destination;
}
function show_SAP_plant_from_customer_code($the_customer_code){
$the_cust_code = "";
$SAP_customer_master = "ptblcustomermaster";
$customer_master = "customer_master";
if($the_customer_code!=""){
$sql1 = "select VWERK from $SAP_customer_master where KUNNR=(select customer_id from $customer_master where `customer_code`='$the_customer_code') ORDER BY AEDAT DESC,ADDITIONAL_DATA1 DESC LIMIT 0,1";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_SAP_plant = $row1["VWERK"] ? addslashes(trim($row1["VWERK"])) : "";
	}
}
return $the_SAP_plant;
}
function show_dump_name_from_dump_code($the_dump_code,$branch_code){
$the_dump_name = "";
$branch_dump = "branch_dump";
if($the_dump_code!="" && $branch_code!=""){
$sql1 = "select `dump_name` from $branch_dump where `branch_code`='$branch_code' and `dump_code`='$the_dump_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_dump_name = $row1["dump_name"] ? addslashes(trim($row1["dump_name"])) : "";
	}
}
return $the_dump_name;
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
function show_dns_destination_code($the_destination_code){
	$dns_dest_code='';
if($the_destination_code!=""){
$sqld = "select `dns_destination_code` from destination_master where destination_code='$the_destination_code'";	
$resd = mysql_query($sqld);
$totresd = mysql_num_rows($resd);
	if($totresd>0){
		$rowd = mysql_fetch_assoc($resd);
		$dns_dest_code = $rowd["dns_destination_code"] ? addslashes(trim($rowd["dns_destination_code"])) : "";
	}
}
return $dns_dest_code;
}
if($carry_forward_process_st=="INACTIVE"){
$res_data = array("process_status"=>"NO","process_message"=>$carry_forward_process_message_st);
echo json_encode($res_data);
exit;
}else{
$ref_sub_dealer_order_id = $_REQUEST["sub_dealer_order_id"] ? strtoupper($_REQUEST["sub_dealer_order_id"]) : "";
$user_type = $_REQUEST["user_type"] ? strtoupper($_REQUEST["user_type"]) : "";
$login_user_id = $_REQUEST["login_user_id"] ? $_REQUEST["login_user_id"] : "";
if($user_type==""){
$user_type = "DEALER";
}
$order_data = $_REQUEST["order_data"] ? $_REQUEST["order_data"] : array();
$in_dt = date("Y-m-d H:i:s");
foreach($order_data as $ki5=>$order_data_val5){
$customer_code5 = $order_data_val5["customer_code"] ? addslashes(trim($order_data_val5["customer_code"])) : "";	
if($customer_code5!=""){
if($ledg_cust_code==""){
$ledg_cust_code = $customer_code5;
break;
}
}
}
$ledg_dns_cust_code = show_dns_customer_from_customer_code_code($ledg_cust_code);
$ord_tot_amount = 0;
$ord_each_item_total_amount = 0;
$ledg_credit_limit = 0;
$ledg_credit_days = 0;
$ledg_current_balance = 0;
$ledg_balance = 0;
$ledg_pending_orders = 0;
$ledg_branch_details_arr = array();
$ledg_branch_details_arr = get_branch_data_from_cuat_id($ledg_cust_code);
$ledg_branch_code = $ledg_branch_details_arr["branch_code"];
$is_active_branch_credit_limit_process = "NO";
$is_allow_order = "YES";
$is_greater_than_30 = "NO";
$ledg_os_balance = 0;
$sql_bclck = "select `branch_code` from $branch_credit_limit_status where `branch_code`='$ledg_branch_code' and `credit_limit_status`='Y'";
$res_bclck = mysql_query($sql_bclck);
$totres_bclck = mysql_num_rows($res_bclck);
if($totres_bclck>0){
	$is_active_branch_credit_limit_process = "YES";
	
$dlr_code_qry2 = "  or `dns_customer_code`='$ledg_dns_cust_code'";
$sqlall2 = "select * from $ledger_balance where `customer_code`='$ledg_cust_code' $dlr_code_qry2";
$resall2 = mysql_query($sqlall2);
$totall2 = mysql_num_rows($resall2);
if($totall2>0){
$row112=mysql_fetch_assoc($resall2);
$ledg_credit_limit = $row112["credit_limit"] ? trim($row112["credit_limit"]) : 0;
$ledg_credit_days = $row112["credit_days"] ? trim($row112["credit_days"]) : 0;
$ledg_current_balance = $row112["current_balance"] ? trim($row112["current_balance"]) : 0;
$ledg_balance = $row112["balance"] ? trim($row112["balance"]) : 0;
$ledg_pending_orders = $row112["pending_orders"] ? trim($row112["pending_orders"]) : 0;
$sqls = "select * from $ageing where `alias`='$ledg_dns_cust_code'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if($totress>0){
			$rows = mysql_fetch_assoc($ress);
$ostotal = $rows["ostotal"] ? trim($rows["ostotal"]) : 0;
$vl_less_equ_10days = $rows["less_equ_10days"] ? trim($rows["less_equ_10days"]) : 0;
$vl_11_17days = $rows["11_17days"] ? trim($rows["11_17days"]) : 0;
$vl_18_25days = $rows["18_25days"] ? trim($rows["18_25days"]) : 0;
$vl_26_30days = $rows["26_30days"] ? trim($rows["26_30days"]) : 0;
$vl_31_45days = $rows["31_45days"] ? trim($rows["31_45days"]) : 0;
$vl_46_60days = $rows["46_60days"] ? trim($rows["46_60days"]) : 0;
$vl_61_90days = $rows["61_90days"] ? trim($rows["61_90days"]) : 0;
$vl_91_120days = $rows["91_120days"] ? trim($rows["91_120days"]) : 0;
$vl_121_180days = $rows["121_180days"] ? trim($rows["121_180days"]) : 0;
$vl_greater_180days = $rows["greater_180days"] ? trim($rows["greater_180days"]) : 0;
$the_arr_less_30 = array();
$the_arr_greater_30 = array();
$the_arr_less_30[] = array("range_from"=>0,"range_to"=>10,"os_amount"=>$vl_less_equ_10days);
$the_arr_less_30[] = array("range_from"=>11,"range_to"=>17,"os_amount"=>$vl_11_17days);
$the_arr_less_30[] = array("range_from"=>18,"range_to"=>25,"os_amount"=>$vl_18_25days);
$the_arr_less_30[] = array("range_from"=>26,"range_to"=>30,"os_amount"=>$vl_26_30days);
$the_arr_greater_30[] = array("range_from"=>31,"range_to"=>45,"os_amount"=>$vl_31_45days);
$the_arr_greater_30[] = array("range_from"=>46,"range_to"=>60,"os_amount"=>$vl_46_60days);
$the_arr_greater_30[] = array("range_from"=>61,"range_to"=>90,"os_amount"=>$vl_61_90days);
$the_arr_greater_30[] = array("range_from"=>91,"range_to"=>120,"os_amount"=>$vl_91_120days);
$the_arr_greater_30[] = array("range_from"=>121,"range_to"=>180,"os_amount"=>$vl_121_180days);
$the_arr_greater_30[] = array("range_from"=>0,"range_to"=>180,"os_amount"=>$vl_greater_180days);
	foreach($the_arr_greater_30 as $the_arr_greater_30_val){
		$the_arr_greater_30_os_amount = $the_arr_greater_30_val["os_amount"] ? $the_arr_greater_30_val["os_amount"] : 0;
		if($the_arr_greater_30_os_amount>0){
			$is_greater_than_30 = "YES";
			break;
		}
	}
if($is_greater_than_30=="NO"){
	/*foreach($the_arr_greater_30 as $the_arr_greater_30_val){
		$the_arr_greater_30_range_to = $the_arr_greater_30_val["range_to"];
		$the_arr_greater_30_os_amount = $the_arr_greater_30_val["os_amount"];
		if($the_arr_greater_30_range_to<=30){
			$ledg_os_balance = ($ledg_os_balance + $the_arr_greater_30_os_amount);
		}
	}*/
	
	$dlr_available_limit = ($ledg_credit_limit - ($ostotal) - $ledg_pending_orders);
}else{
$is_allow_order = "NO";
$process_message = "Order not placed. Outstanding is more than 30 days. Please make payment.";
}
	
			
}
}else{
$ledg_credit_limit = 0;
$ledg_credit_days = 0;
$ledg_current_balance = 0;
$ledg_balance = 0;
$ledg_pending_orders = 0;
$ledg_os_balance = 0;	
}
foreach($order_data as $ki6=>$order_data_val6){
$ord_each_item_total_amount = 0;
$cur_ord_prod_code = $order_data_val6["prod_code"] ? addslashes(trim($order_data_val6["prod_code"])) : "";
$cur_ord_prod_dtld = array();
$cur_ord_prod_dtld = show_product_data_from_prod_code($cur_ord_prod_code);
$cur_ord_dns_prod_code = $cur_ord_prod_dtld["dns_prod_code"];
$cur_ord_prod_dtld_desc = $cur_ord_prod_dtld["prod_desc"];
$cur_ord_qty = addslashes(trim($order_data_val6["qty"]));
$cur_ord_destination_code = $order_data_val6["destination_code"] ? addslashes(trim($order_data_val6["destination_code"])) : "";
$cur_ord_dns_destination_code = show_dns_destination_code($cur_ord_destination_code);
$cur_ord_destination_name = $order_data_val6["destination_name"] ? addslashes(trim($order_data_val6["destination_name"])) : "";
$sql_dwp1 = "select `rate` from $destination_wise_price where (`dns_prod_code`='$cur_ord_dns_prod_code' and `dns_destination_code`='$cur_ord_dns_destination_code') or (`product_name`='$cur_ord_prod_dtld_desc' and `destination_name`='$cur_ord_destination_name')";
$res_dwp1 = mysql_query($sql_dwp1);
$tot_dwp1 = mysql_num_rows($res_dwp1);
if($tot_dwp1>0){
$row_dwp1=mysql_fetch_assoc($res_dwp1);
$the_ord_prod_rate = $row_dwp1["rate"] ? trim($row_dwp1["rate"]) : 0;
if($the_ord_prod_rate==""){
$the_ord_prod_rate = 0;	
}
}
$ord_each_item_total_amount = ($the_ord_prod_rate * $cur_ord_qty);
$ord_tot_amount = ($ord_tot_amount + $ord_each_item_total_amount);
}
if($is_greater_than_30=="NO"){
if($ord_tot_amount<$dlr_available_limit){
$is_allow_order = "YES";
}else{
$is_allow_order = "NO";	
$process_message = "Order not placed. Order amount is greater than available Credit Limit. Please make payment.";
}
}else{
$is_allow_order = "NO";		
}
}
if($is_active_branch_credit_limit_process == "YES" && $is_allow_order=="NO"){
$res_data = array("process_status"=>"NO","process_message"=>$process_message."OS: RS. ".$ostotal.",Pending Order Amount: RS. ".$ledg_pending_orders.",Order Amount: Rs. ".$ord_tot_amount);
echo json_encode($res_data);
exit;	
}else{
$not_to_use_ref_order_id = array();
if(count($order_data)>0){
$cntslno=1;
foreach($order_data as $ki=>$order_data_val){
$ref_order_id_for_duplicate_ck = $order_data_val["apporderno"] ? trim($order_data_val["apporderno"]) : "";
$sql_ck_reford = "select `ref_order_id_for_duplicate_ck` from $t_apperpdo where `ref_order_id_for_duplicate_ck`='".addslashes($ref_order_id_for_duplicate_ck)."' and `APPORDERNO` !='' and `ref_order_id_for_duplicate_ck` !=''";
$res_ck_reford = mysql_query($sql_ck_reford);
$totres_ck_reford = mysql_num_rows($res_ck_reford);
if($totres_ck_reford>0){
$not_to_use_ref_order_id[] = $ref_order_id_for_duplicate_ck;	
}else{
$erporderno = "";
$erporderdt = $order_data_val["erporderdt"] ? addslashes(trim($order_data_val["erporderdt"])) : "";
$order_for = $order_data_val["order_for"] ? addslashes(trim($order_data_val["order_for"])) : "";
$order_for_type = $order_data_val["order_for_type"] ? addslashes(trim($order_data_val["order_for_type"])) : "";
$customer_code = $order_data_val["customer_code"] ? addslashes(trim($order_data_val["customer_code"])) : "";
$sub_dealer_code = "";
$dns_sub_dealer_code = "";
if(array_key_exists("sub_dealer_code",$order_data_val)){
$sub_dealer_code = addslashes(trim($order_data_val["sub_dealer_code"]));
if($sub_dealer_code!=""){
$dns_sub_dealer_code = show_dns_customer_from_customer_code_code($sub_dealer_code);
${'dns_sub_dealer_code'.$cntslno} = $dns_sub_dealer_code ;
}
}
$prod_code = $order_data_val["prod_code"] ? addslashes(trim($order_data_val["prod_code"])) : "";
$dns_prod_code = "";
$qty = addslashes(trim($order_data_val["qty"]));
$freight = $order_data_val["freight"] ? addslashes(trim($order_data_val["freight"])) : "";
$destination_code = $order_data_val["destination_code"] ? addslashes(trim($order_data_val["destination_code"])) : "";
$destination_name = $order_data_val["destination_name"] ? addslashes(trim($order_data_val["destination_name"])) : "";
$destination_address = $order_data_val["destination_address"] ? addslashes(trim($order_data_val["destination_address"])) : "";
$branch_details_arr = array();
$branch_details_arr = get_branch_data_from_cuat_id($customer_code);
$branch_code = $branch_details_arr["branch_code"];
$dns_branch_code = $branch_details_arr["dns_branch_code"];
$dns_customer_code = show_dns_customer_from_customer_code_code($customer_code);
$SAP_customer_code = show_SAP_code_from_customer_code_code($customer_code);
${'dns_customer_code'.$cntslno}=$dns_customer_code;
$prod_dtld = show_product_data_from_prod_code($prod_code);
$dns_prod_code = $prod_dtld["dns_prod_code"];
${'dns_prod_code'.$cntslno}=$dns_prod_code;
$prod_dtld_desc = $prod_dtld["prod_desc"];
$dns_destination_code=show_dns_destination_code($destination_code);
${'dns_destination_code'.$cntslno}=$dns_destination_code;
$the_SAP_destination=show_SAP_LZONE_from_customer_code($customer_code);
$the_SAP_plant=show_SAP_plant_from_customer_code($customer_code);
$phone_no = $order_data_val["phone_no"] ? addslashes(trim($order_data_val["phone_no"])) : "";
$dump_status = $order_data_val["dump_status"] ? addslashes(trim($order_data_val["dump_status"])) : "NO";
if($dump_status==""){
$dump_status = "NO";	
}
$dump_name = $order_data_val["dump_name"] ? addslashes(trim($order_data_val["dump_name"])) : "";
$dump_code = "";
if(array_key_exists("dump_code",$order_data_val)){
$dump_code = addslashes(trim($order_data_val["dump_code"]));
if($dump_code!=""){
	if($dump_name==""){
		$dump_name = show_dump_name_from_dump_code($dump_code,$branch_code);
	}
}
}
$dealer_truck = $order_data_val["dealer_truck"] ? $order_data_val["dealer_truck"] : "NO";
if($dealer_truck==""){
$dealer_truck = "NO";
}
$order_date = date("Y-m-d H:i:s");
$consignee_name_of = "";
$consignee_address_arr_of = array();
$consignee_address_of = "";
if($order_for!=""){
if( strpos($order_for,",") !== false ) {
$ofrarr_of = array();
$ofrarr_of = explode(",",$order_for);
if(count($ofrarr_of)>0){
for($i=0;$i<count($ofrarr_of);$i++){
if($i==0){
$consignee_name_of = $ofrarr_of[$i];
}else{
$consignee_address_arr_of[] = $ofrarr_of[$i];
}
}
}
}else{
$consignee_name_of = $order_for;	
}
}
if(count($consignee_address_arr_of)>0){
$consignee_address_of = implode(",",$consignee_address_arr_of);
}
if($order_for_type=="Self"){
	$order_for_new_in = "";
}else if($order_for_type=="Sub Dealer"){
	$order_for_new_in = "";
}else if($order_for_type=="Others"){
	$order_for_new_in = $order_for;
}else{
	$order_for_new_in = $order_for;
}
$SAP_sub_dealer_code = show_SAP_code_from_customer_code_code($sub_dealer_code);
/*if($SAP_sub_dealer_code!='' && strtoupper($freight)=='FOR')
{
	$the_SAP_destination=show_SAP_LZONE_from_customer_code($sub_dealer_code);
}
if(strtoupper($freight)=='EX')
{
	$the_SAP_destination=$destination_code;
}*/
if($SAP_sub_dealer_code!=''){
$the_SAP_destination=show_SAP_LZONE_from_customer_code($sub_dealer_code);
//$the_SAP_plant=show_SAP_plant_from_customer_code($sub_dealer_code);
}
if(strtoupper($freight)=='FOR')
{
	$the_SAP_plant=$the_SAP_plant;
}
if(strtoupper($freight)=='EXW')
{
	$the_SAP_plant=$dump_code;
}

$remarks='';
${'prod_dtld_mail'.$cntslno}=$prod_dtld_desc;
$sqlin = "insert into $t_apperpdo (`ref_order_id_for_duplicate_ck`,`ERPORDERNO`,`ERPORDERDT`,`order_date`,`order_for`,`order_for_type`,`consignee_name`,`consignee_address`,`sub_dealer_code`,`dns_sub_dealer_code`,`customer_code`,`dns_customer_code`,`prod_code`,`dns_prod_code`,`prod_display_name`,`QTY`,`freight`,`branch_code`,`dns_branch_code`,`destination_code`,`destination_name`,`destination_address`,`phone_no`,`dump_status`,`dump_code`,`dump_name`,`order_from`,`order_by`,`dealer_truck`,`ref_sub_dealer_order_id`) 
values('$ref_order_id_for_duplicate_ck','$erporderno','$erporderdt','$order_date','$order_for_new_in','$order_for_type','$consignee_name_of','$consignee_address_of','$sub_dealer_code','$dns_sub_dealer_code','$customer_code','$dns_customer_code','$prod_code','$dns_prod_code','$prod_dtld_desc','$qty','$freight','$branch_code','$dns_branch_code','$destination_code','$destination_name','$destination_address','$phone_no','$dump_status','$dump_code','$dump_name','$user_type','$login_user_id','$dealer_truck','$ref_sub_dealer_order_id')";
$resin = mysql_query($sqlin);
if($resin){
	$created_last_id = mysql_insert_id();
$new_order_id_val = str_pad($created_last_id, 7, "0", STR_PAD_LEFT);
$apporderno = "SS".$new_order_id_val;
${'apporderno'.$cntslno}=$apporderno;
$sqlupd1 = "update $t_apperpdo set `APPORDERNO`='$apporderno' where `id`='$created_last_id'";
$resupd1 = mysql_query($sqlupd1);
if($ref_sub_dealer_order_id!=""){
$sql_subord_utd = "update $t_subdealer_order set `STATUS`='APPROVED' where `sub_dealer_order_id`='$ref_sub_dealer_order_id'";
$res_subord_utd = mysql_query($sql_subord_utd);
}
// INSERT INTO SAP ORDER TABLE
	$SAP_order_date=substr($order_date,0,10);
	$SAP_order_time=substr($order_date,11,5);
	if($SAP_sub_dealer_code!='') $SAP_sub_dealer_code=$SAP_sub_dealer_code;
	else 						  $SAP_sub_dealer_code=$SAP_customer_code;
	$sqlinSAP="insert into $zorderdetails (`APPORDERNO`,`DATE`,`time`,`Cust_Code`,`Consignee_Code`,`Freight`,`DestinationCode`,`ProductCode`,`Qty`,`Unit`,`PLANT`,`OrderNo`,`Remarks`) 
values('$apporderno','$SAP_order_date','$SAP_order_time','$SAP_customer_code','$SAP_sub_dealer_code','$freight','$the_SAP_destination','$dns_prod_code','$qty','TO','$the_SAP_plant','','$remarks')";
	$resinSAP = mysql_query($sqlinSAP);

// INSERT INTO TEMP ORDER TABLE
$sqlin_temp = "insert into $t_apperpdo_temp (`ref_order_id_for_duplicate_ck`,`APPORDERNO`,`ERPORDERNO`,`ERPORDERDT`,`order_date`,`order_for`,`order_for_type`,`consignee_name`,`consignee_address`,`sub_dealer_code`,`dns_sub_dealer_code`,`customer_code`,`dns_customer_code`,`prod_code`,`dns_prod_code`,`prod_display_name`,`QTY`,`freight`,`branch_code`,`dns_branch_code`,`destination_code`,`destination_name`,`destination_address`,`phone_no`,`dump_status`,`dump_code`,`dump_name`,`order_from`,`order_by`,`dealer_truck`,`ref_sub_dealer_order_id`) values('$ref_order_id_for_duplicate_ck','$apporderno','$erporderno','$erporderdt','$order_date','$order_for_new_in','$order_for_type','$consignee_name_of','$consignee_address_of','$sub_dealer_code','$dns_sub_dealer_code','$customer_code','$dns_customer_code','$prod_code','$dns_prod_code','$prod_dtld_desc','$qty','$freight','$branch_code','$dns_branch_code','$destination_code','$destination_name','$destination_address','$phone_no','$dump_status','$dump_code','$dump_name','$user_type','$login_user_id','$dealer_truck','$ref_sub_dealer_order_id')";
$resin_temp = mysql_query($sqlin_temp);
$the_fetched_array = array();	
$the_fetched_array = fetch_emails_by_customer_code($customer_code);
if(count($the_fetched_array)>0){
	if(array_key_exists("email",$the_fetched_array)){
		$the_fetched_email_arrays = $the_fetched_array["email"];
		$the_fetched_emails = implode(",",$the_fetched_email_arrays);
		if(array_key_exists("branch_name",$the_fetched_array)){
			$the_fetched_branch_name = $the_fetched_array["branch_name"] ? ucwords(strtolower($the_fetched_array["branch_name"])) : "";
			$subject = "Order No.$apporderno for branch ".$the_fetched_branch_name;
		}else{
			$the_fetched_branch_name = "";
			$subject = "Order No.$apporderno for Starsaathi";
		}
		$curr_date_str = date("jS M, y",strtotime($order_date));
		$consignee_name = "";
		$consignee_address_arr = array();
		$consignee_address = "";
		if($order_for!=""){
			if( strpos($order_for,",") !== false ) {
				$ofrarr = array();
				$ofrarr = explode(",",$order_for);
				if(count($ofrarr)>0){
					for($i=0;$i<count($ofrarr);$i++){
					
					if($i==0){
						$consignee_name = $ofrarr[$i];
					}else{
						$consignee_address_arr[] = $ofrarr[$i];
					}
					
					}
				}
			}
		}
		if(count($consignee_address_arr)>0){
			$consignee_address = implode(",",$consignee_address_arr);
		}
		$fetched_customer_name = get_customer_name_from_id($customer_code);
		
		if($the_fetched_emails!=""){
		$to      = $the_fetched_emails;
		$message = '<br><b>App Order No: </b> '.$apporderno.'<br>
					<b>DATE: </b> '.$curr_date_str.'<br>
					<b>Branch Name: </b> '.$the_fetched_branch_name.'<br>
					<b>Customer Name: </b> '.$fetched_customer_name.'<br>
					<b>Consignee Name: </b> '.$consignee_name.'<br>
					<b>Consignee Address: </b> '.$consignee_address.'<br>
					<b>Freight: </b> '.$freight.'<br>
					<b>Destination: </b> '.$destination_name.'<br>
					<b>Product Name: </b> '.$prod_dtld_desc.'<br>
					<b>qty (MT): </b> '.$qty.'<br>
					<b>Phone No.: </b> '.$phone_no.'<br>
					<b>Dump Status: </b> '.$dump_status.'<br>
					<b>Dump Name: </b> '.$dump_name.'<br>
					<b>Dealer Truck: </b> '.$dealer_truck.'<br>';
					
					if($user_type=="BROKER"){
					$broker_name = get_broker_name_from_id($login_user_id);
					$message .= '<b>Order by broker: </b> '.$broker_name.'<br>';
					}
			
		//$res_mail = send_the_mail($to,$subject,$message);
		}
	}
}
}
$cntslno++;
}
}
$res_data = array("process_status"=>"YES","process_message"=>"The order successfully received.");
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Please select product then make order.");
}
echo json_encode($res_data);
//mysql_close();
//For SFA INSERT
define("SERVERREMOTE","103.242.119.68");
define("USERREMOTE","acedns_dnsprod");
define("PASSWORDREMOTE","dnsprod1234#");
define("DBREMOTE","acedns_STAR");
	
$link=mysql_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die("Database Connection Error.");
mysql_select_db(DBREMOTE,$link) or die("could not connect the database");
$sqlinsert="INSERT INTO orderdata set 	data='".$order_data."',insertdatetime=CURRENT_TIMESTAMP()";
mysql_query($sqlinsert,$link);
$cntordata=1;
if(count($order_data)>0){
	foreach($order_data as $ki=>$order_data_val){
$second_ref_order_id_ck = $order_data_val["apporderno"] ? trim($order_data_val["apporderno"]) : "";
if(in_array($second_ref_order_id_ck,$not_to_use_ref_order_id)){
	
}else{
		$erporderno = "";
		$erporderdt = $order_data_val["erporderdt"] ? addslashes(trim($order_data_val["erporderdt"])) : "";
		$order_for = $order_data_val["order_for"] ? addslashes(trim($order_data_val["order_for"])) : "";
/* ORDER FOR TYPE */
$order_for_type = $order_data_val["order_for_type"] ? addslashes(trim($order_data_val["order_for_type"])) : "";
/*$consignee_name_of = "";
$consignee_address_arr_of = array();
$consignee_address_of = "";
if($order_for!=""){
if( strpos($order_for,",") !== false ) {
$ofrarr_of = array();
$ofrarr_of = explode(",",$order_for);
if(count($ofrarr_of)>0){
for($i=0;$i<count($ofrarr_of);$i++){
if($i==0){
$consignee_name_of = $ofrarr_of[$i];
}else{
$consignee_address_arr_of[] = $ofrarr_of[$i];
}
}
}
}else{
$consignee_name_of = $order_for;	
}
}
if(count($consignee_address_arr_of)>0){
$consignee_address_of = implode(",",$consignee_address_arr_of);
}*/
if($order_for_type=="Self"){
	$order_for_new_in = "";
}else if($order_for_type=="Sub Dealer"){
	$order_for_new_in = "";
}else if($order_for_type=="Others"){
	$order_for_new_in = $order_for;
}else{
	$order_for_new_in = $order_for;
}
$sub_dealer_code = "";
$dns_sub_dealer_code = "";
if(array_key_exists("sub_dealer_code",$order_data_val)){
$sub_dealer_code = addslashes(trim($order_data_val["sub_dealer_code"]));
if($sub_dealer_code!=""){
$sqlsubdealercode="SELECT customer_code,address,customer_name FROM customer_master WHERE dns_customer_code='".${'dns_sub_dealer_code'.$cntordata}."'";
$rssubdealercode=mysql_query($sqlsubdealercode,$link);
$rowsubdealercode=mysql_fetch_array($rssubdealercode);
$sub_dealer_code = $rowsubdealercode['customer_code'];
$address_sub_dealer=$rowcustcode['address'];
$sub_dealer_name=$rowcustcode['customer_name'];
}
}
		//$customer_code = $order_data_val["customer_code"] ? addslashes(trim($order_data_val["customer_code"])) : "";
		//$prod_code = $order_data_val["prod_code"] ? addslashes(trim($order_data_val["prod_code"])) : "";
		$dns_prod_code = "";
		$qty = $order_data_val["qty"] ? addslashes(trim($order_data_val["qty"])) : "";
		$freight = $order_data_val["freight"] ? addslashes(trim($order_data_val["freight"])) : "";
		//$destination_code = $order_data_val["destination_code"] ? addslashes(trim($order_data_val["destination_code"])) : "";
		$destination_name_fetched = $order_data_val["destination_name"] ? addslashes(trim($order_data_val["destination_name"])) : "";
		$destination_address = $order_data_val["destination_address"] ? addslashes(trim($order_data_val["destination_address"])) : "";
		//$dns_customer_code = show_dns_customer_from_customer_code_code($customer_code);
		//$prod_dtld = show_product_data_from_prod_code($prod_code);
		//$dns_prod_code = $prod_dtld["dns_prod_code"];
		//$prod_dtld_desc = $prod_dtld["prod_desc"];
		$dns_prod_code =${'dns_prod_code'.$cntordata};
		$dns_customer_code =${'dns_customer_code'.$cntordata};
		$dns_destination_code =${'dns_destination_code'.$cntordata};
		$apporderno =${'apporderno'.$cntordata};
		$prod_dtld_mail = ${'prod_dtld_mail'.$cntordata};

		$phone_no = $order_data_val["phone_no"] ? addslashes(trim($order_data_val["phone_no"])) : "";
		$dump_status = $order_data_val["dump_status"] ? addslashes(trim($order_data_val["dump_status"])) : "NO";
		if($dump_status==""){
		$dump_status = "NO";	
		}
		$dump_name = $order_data_val["dump_name"] ? addslashes(trim($order_data_val["dump_name"])) : "";
		$dump_code = "";
		if(array_key_exists("dump_code",$order_data_val)){
		$dump_code = addslashes(trim($order_data_val["dump_code"]));
		}
		$dealer_truck = $order_data_val["dealer_truck"] ? $order_data_val["dealer_truck"] : "NO";
		if($dealer_truck==""){
		$dealer_truck = "NO";
		}
		$order_date = date("Y-m-d H:i:s");
		$sqlcustcode="SELECT customer_code,branch_code,customer_name,address FROM customer_master WHERE dns_customer_code='".$dns_customer_code."'";
		$rscustcode=mysql_query($sqlcustcode,$link);
		$rowcustcode=mysql_fetch_array($rscustcode);
		$customer_code = $rowcustcode['customer_code'];
		$branch_code = $rowcustcode['branch_code'];
		$customer_name=$rowcustcode['customer_name'];
		$address_dealer=$rowcustcode['address'];
		
		/*if($dns_sub_dealer_code!='')
		{
		$sqlsubdealercode="SELECT customer_code,address,customer_name FROM customer_master WHERE dns_customer_code='".$dns_sub_dealer_code."'";
		$rssubdealercode=mysql_query($sqlsubdealercode,$link);
		$rowsubdealercode=mysql_fetch_array($rssubdealercode);
		$sub_dealer_code = $rowsubdealercode['customer_code'];
		$address_sub_dealer=$rowcustcode['address'];
		$sub_dealer_name=$rowcustcode['customer_name'];
		}*/
		
		$sqlbranch = "select branch_name from branch_master where branch_code='".$branch_code."'";	
		$resbranch = mysql_query($sqlbranch);
		$rowbranch=mysql_fetch_array($resbranch);
		$branch_name=$rowbranch['branch_name'];
		
		/*$sqlprod = "select prod_code,prod_desc from product_master where `dns_prod_code`='".$dns_prod_code."' AND branch_code='".$branch_code."'";	
		$resprod = mysql_query($sqlprod,$link);
		$rowprod = mysql_fetch_array($resprod);
		$prod_code = $rowprod["prod_code"];
		$prod_desc = $rowprod["prod_desc"];*/
		
		$sqldestination = "select destination_code,destination_name from destination_master where dns_destination_code='".$dns_destination_code."'";	
		$resdestination= mysql_query($sqldestination,$link);
		$rowdestination = mysql_fetch_array($resdestination);
		$destination_code = $rowdestination["destination_code"];
		$destination_name = $rowdestination["destination_name"];
		$destination_address = $rowdestination["destination_name"];
		
		$consignee_name = "";
		$consignee_address_arr = array();
		$consignee_address = "";
		if($order_for!=""){
			if( strpos($order_for,",") !== false ) {
				$ofrarr = array();
				$ofrarr = explode(",",$order_for);
				if(count($ofrarr)>0){
					for($i=0;$i<count($ofrarr);$i++){
					if($i==0){
						$consignee_name = $ofrarr[$i];
					}else{
						$consignee_address_arr[] = $ofrarr[$i];
					}
					}
				}
			}
		if(count($consignee_address_arr)>0){
			$consignee_address = implode(",",$consignee_address_arr);
		}
		}
		else if($order_for=='' && $sub_dealer_code==''){
			$consignee_name=$customer_name;
			$consignee_address=$address_dealer;
		}
		else if($order_for=='' && $sub_dealer_code!=''){
			$consignee_name=$sub_dealer_name;
			$consignee_address=$address_sub_dealer;
		}
		
		$sqlin = "insert into $t_apperpdo (`APPORDERNO`,`ERPORDERNO`,`ERPORDERDT`,`order_date`,`order_for`,`consignee_name`,`consignee_address`,`sub_dealer_code`,`dns_sub_dealer_code`,`customer_code`,`dns_customer_code`,`prod_code`,`dns_prod_code`,`prod_display_name`,`QTY`,`freight`,`destination_code`,`destination_name`,`destination_address`,`phone_no`,`dump_status`,`dump_code`,`dump_name`,`order_from`,`order_by`,`dealer_truck`) values('$apporderno','$erporderno','$erporderdt','$order_date','$order_for_new_in','$consignee_name','$consignee_address','$sub_dealer_code','${dns_sub_dealer_code.$cntordata}','$customer_code','$dns_customer_code','$prod_code','$dns_prod_code','$prod_desc',
	'$qty','$freight','$destination_code','$destination_name','$destination_address','$phone_no','$dump_status','$dump_code','$dump_name','$user_type','$login_user_id','$dealer_truck')";
		$resin = mysql_query($sqlin,$link);
		$sqlinapproval = "insert into T_APPERPDO_APPROVAL(`APPORDERNO`,`ERPORDERNO`,`ERPORDERDT`,`order_date`,`order_for`,`consignee_name`,`consignee_address`,`sub_dealer_code`,`dns_sub_dealer_code`,`customer_code`,`dns_customer_code`,`prod_code`,`dns_prod_code`,`prod_display_name`,`QTY`,`freight`,`destination_code`,`destination_name`,`destination_address`,`phone_no`,`dump_status`,`dump_code`,`dump_name`,`order_from`,`order_by`,`dealer_truck`) values('$apporderno','$erporderno','$erporderdt','$order_date','$order_for_new_in','$consignee_name','$consignee_address','$sub_dealer_code','${dns_sub_dealer_code.$cntordata}','$customer_code','$dns_customer_code','$prod_code','$dns_prod_code','$prod_desc',
	'$qty','$freight','$destination_code','$destination_name','$destination_address','$phone_no','$dump_status','$dump_code','$dump_name','$user_type','$login_user_id','$dealer_truck')";
		$resinapproval = mysql_query($sqlinapproval,$link);
		$ownempmailstring='';
		$reportingmailstring='';
		$email_hierarchy='';
		$registrationid_array=array();
		$emp_code_array=array();
		$sqldealeremp="SELECT emp_code FROM customer_route_emp_relation WHERE acedns='Y' AND customer_code='".$customer_code."'";
		$rsdealeremp=mysql_query($sqldealeremp,$link);
		while($rowdealeremp=mysql_fetch_array($rsdealeremp))
		{
			$emp_code_db=$rowdealeremp['emp_code'];
			$employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code_db);
		//$emp_hierarchy_condition='c1.emp_code IN('.$employee_hierarchy.')';
			$sqlemailhierarchy="SELECT email,designation FROM employee_master WHERE emp_code IN (".$employee_upper_hierarchy.") AND UPPER(sale_access)='PRIMARY' AND acedns='Y'";
			$rsemailhierarchy=mysql_query($sqlemailhierarchy);
			while($rowemailhierarchy=mysql_fetch_array($rsemailhierarchy))
			{
				$email_hierarchy=$email_hierarchy.$rowemailhierarchy['email'].',';
			}
			$sqlregistrationid="SELECT registrationid FROM changepassword WHERE emp_code='".$emp_code_db."' AND 
			emp_code IN(SELECT emp_code FROM employee_master WHERE UPPER(sale_access)='PRIMARY' AND acedns='Y') ";
			$rsregistrationid=mysql_query($sqlregistrationid,$link);
			$rowregistrationid=mysql_fetch_array($rsregistrationid);
			$registrationid=$rowregistrationid['registrationid'];
			if($registrationid!='')
			{
				if(!in_array($registrationid,$registrationid_array))
				{
					array_push($registrationid_array,$registrationid);
					array_push($emp_code_array,$emp_code_db);
				}
			}
		}
		/*$sqlregistrationid="SELECT registrationid FROM changepassword WHERE emp_code='E0555'";
		$rsregistrationid=mysql_query($sqlregistrationid,$link);
		$rowregistrationid=mysql_fetch_array($rsregistrationid);
		$registrationid=$rowregistrationid['registrationid'];*/
		$email_broker='';
		$sqlbroker="SELECT broker_code FROM customer_broker_relation WHERE acedns='Y' AND customer_code='".$customer_code."'";
		$rsbroker=mysql_query($sqlbroker,$link);
		while($rowbroker=mysql_fetch_array($rsbroker))
		{
			$sqlemailbroker="SELECT mail_id FROM broker_master WHERE broker_id='".$rowbroker['broker_code']."' AND acedns='Y'";
			$rsemailbroker=mysql_query($sqlemailbroker,$link);
			while($rowemailbroker=mysql_fetch_array($rsemailbroker))
			{
				$email_broker=$email_broker.$rowemailbroker['mail_id'].',';
			}
		}
		
		$curr_date_format = date("jS M, y",strtotime($order_date));
		//$final_email=substr($ownempmailstring,0,-1).','.substr($reportingmailstring,0,-1).','.'dipankarc@coral.in';
$final_email=substr($email_hierarchy,0,-1).','.substr($email_broker,0,-1).','.'dipankarc@coral.in'.','.'emovesfa@starcement.co.in'.','.'manishranjan@starcement.co.in';
		$subject = "Order for branch ".strtoupper($branch_name)." ";
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$location_date=$year.$month.$date.$hour.$minute.$second;
		$message="Order received from $customer_name @ $hour:$minute\n";
		$message.= '<br><b>App Order No: </b> '.$apporderno.'<br>
					<b>DATE: </b> '.$curr_date_format.'<br>
					<b>Branch Name: </b> '.strtoupper($branch_name).'<br>
					<b>Customer Name: </b> '.$customer_name.'<br>
					<b>Consignee Name: </b> '.$consignee_name.'<br>
					<b>Consignee Address: </b> '.$consignee_address.'<br>
					<b>Freight: </b> '.$freight.'<br>
					<b>Destination: </b> '.$destination_name_fetched.'<br>
					<b>Product Name: </b> '.$prod_dtld_mail.'<br>
					<b>qty (MT): </b> '.$qty.'<br>
					<b>Phone No.: </b> '.$phone_no.'<br>
					<b>Dump Status: </b> '.$dump_status.'<br>
					<b>Dump Name: </b> '.$dump_name.'<br>
					<b>Dealer Truck: </b> '.$dealer_truck.'<br>';
		$send_mail = send_the_mail($final_email,$subject,$message);
		
		//For Notification to ASM
		for($k=0;$k<count($registrationid_array);$k++)
		{
		//$emp_code='E0555';
		$emp_code=$emp_code_array[$k];
		$notification_id='PN'.strtoupper($emp_code).$location_date;
		$notification_type='order_punched';
		$apiKey='AAAAUCNxsT8:APA91bE-MLw1RwEHr7sZrKRmRiipBv1NnW-p6Gz1ajW_vOESL7jM3Wx0Rd0z5sUJoEM3DVqo_OS_kN0aQwXt1v1aciNzAir0FAlbCkefWvGFTDjYI-Dm7mTELSwgwCoo9zhqnHEtBkE6';
		$collapseKey=rand();
		//Title of the Notification.
		$title = "";
		//$message='There is an update please login to your APP & Press the notification button';
		
		//Creating the notification array.
		$notification = array('title' =>$title , 'body' => $message);
		
		//This array contains, the token and the notification. The 'to' attribute stores the token.
		$data= 
array('notification_id' =>$notification_id, 'notification_type' => $notification_type, 'sender_id' => 'Dealerapp', 'body' => $message); 
		//$arrayToSend = array('to' => $registrationid, 'notification' => $notification, 'data'=>$data);
		$arrayToSend = array('to' => $registrationid_array[$k], 'data'=>$data);
		
        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );
        // Open connection
         $ch = curl_init();
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayToSend));
        // Execute post
        //$result = curl_exec($ch);
        /*if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }*/
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($httpCode != 200) {    
			//request failed    
			$successval=0; 
		} 
		else
		{
			$successval=1;	
		}
        // Close connection
        curl_close($ch);
		if($successval==1)
		 {
				$sqlnotificationmaster  = "INSERT INTO notification_master ";
				$sqlnotificationmaster .= " SET notification_id='".$notification_id."'";
				$sqlnotificationmaster .= " ,type_of_notification='".$notification_type."'";
				$sqlnotificationmaster .= " ,sender_id='Dealerapp'";
				$sqlnotificationmaster .= " ,message='".$message."'";
				$sqlnotificationmaster .= " ,transferred='YES'";
				mysql_query($sqlnotificationmaster,$link);
				$sqlnotification  = "INSERT INTO notification_ack_relation ";
				$sqlnotification .= " SET notification_id='".$notification_id."'";
				$sqlnotification .= " ,receiver_id='".$emp_code."'";
				mysql_query($sqlnotification,$link);
		 }
		}
		$cntordata++;
	}
	}
}
}
}
mysql_close();
?>
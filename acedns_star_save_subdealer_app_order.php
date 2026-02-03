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
$ledg_cust_code = "";

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


$user_type = $_REQUEST["user_type"] ? strtoupper($_REQUEST["user_type"]) : "";
$login_user_id = $_REQUEST["login_user_id"] ? $_REQUEST["login_user_id"] : "";

$order_data = $_REQUEST["order_data"] ? $_REQUEST["order_data"] : array();
$in_dt = date("Y-m-d H:i:s");


$not_to_use_ref_order_id = array();
if(count($order_data)>0){
$cntslno=1;
foreach($order_data as $ki=>$order_data_val){
$ref_order_id_for_duplicate_ck = $order_data_val["apporderno"] ? trim($order_data_val["apporderno"]) : "";
$sql_ck_reford = "select `ref_order_id_for_duplicate_ck` from $t_subdealer_order where `ref_order_id_for_duplicate_ck`='".addslashes($ref_order_id_for_duplicate_ck)."' and `APPORDERNO` !='' and `ref_order_id_for_duplicate_ck` !=''";
$res_ck_reford = mysql_query($sql_ck_reford);
$totres_ck_reford = mysql_num_rows($res_ck_reford);
if($totres_ck_reford>0){
$not_to_use_ref_order_id[] = $ref_order_id_for_duplicate_ck;	
}else{
$erporderno = "";
$erporderdt = $order_data_val["erporderdt"] ? addslashes(trim($order_data_val["erporderdt"])) : "";
$order_for = $order_data_val["order_for"] ? addslashes(trim($order_data_val["order_for"])) : "";
$order_for_type = $order_data_val["order_for_type"] ? addslashes(trim($order_data_val["order_for_type"])) : "";
$belong_dealer_code = $order_data_val["belong_dealer_code"] ? addslashes(trim($order_data_val["belong_dealer_code"])) : "";
$belong_dealer_dns_code = $order_data_val["belong_dealer_dns_code"] ? addslashes(trim($order_data_val["belong_dealer_dns_code"])) : "";
$sub_dealer_code = "";
$dns_sub_dealer_code = "";
if(array_key_exists("sub_dealer_code",$order_data_val)){
$sub_dealer_code = addslashes(trim($order_data_val["sub_dealer_code"]));
if($sub_dealer_code!=""){
$dns_sub_dealer_code = show_dns_customer_from_customer_code_code($sub_dealer_code);

}
}
$prod_code = $order_data_val["prod_code"] ? addslashes(trim($order_data_val["prod_code"])) : "";
$dns_prod_code = "";
$qty = addslashes(trim($order_data_val["qty"]));

$branch_details_arr = array();
$branch_details_arr = get_branch_data_from_cuat_id($belong_dealer_code);
$branch_code = $branch_details_arr["branch_code"];
$dns_branch_code = $branch_details_arr["dns_branch_code"];
if($belong_dealer_dns_code!=""){
$belong_dealer_dns_code = show_dns_customer_from_customer_code_code($belong_dealer_code);
}
$prod_dtld = show_product_data_from_prod_code($prod_code);
$dns_prod_code = $prod_dtld["dns_prod_code"];

$prod_dtld_desc = $prod_dtld["prod_desc"];

$sub_dealer_phone_no = $order_data_val["sub_dealer_phone_no"] ? addslashes(trim($order_data_val["sub_dealer_phone_no"])) : "";

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
/*if($order_for_type=="Self"){
	$order_for_new_in = "";
}else if($order_for_type=="Sub Dealer"){
	$order_for_new_in = "";
}else if($order_for_type=="Others"){
	$order_for_new_in = $order_for;
}else{
	$order_for_new_in = $order_for;
}*/
$order_for_type = "Sub Dealer";
$order_for_new_in = "";

$sqlin = "insert into $t_subdealer_order (`ref_order_id_for_duplicate_ck`,`order_date`,`order_for`,`order_for_type`,`consignee_name`,`consignee_address`,`sub_dealer_code`,`dns_sub_dealer_code`,`belong_dealer_code`,`belong_dealer_dns_code`,`prod_code`,`dns_prod_code`,`prod_display_name`,`QTY`,`branch_code`,`dns_branch_code`,`sub_dealer_phone_no`,`order_from`,`order_by`) values('$ref_order_id_for_duplicate_ck','$order_date','$order_for_new_in','$order_for_type','$consignee_name_of','$consignee_address_of','$sub_dealer_code','$dns_sub_dealer_code','$belong_dealer_code','$belong_dealer_dns_code','$prod_code','$dns_prod_code','$prod_dtld_desc','$qty','$branch_code','$dns_branch_code','$sub_dealer_phone_no','$user_type','$login_user_id')";
$resin = mysql_query($sqlin);
if($resin){
$created_last_id = mysql_insert_id();
$new_order_id_val = str_pad($created_last_id, 7, "0", STR_PAD_LEFT);
$apporderno = "SUB".$new_order_id_val;

$sqlupd1 = "update $t_subdealer_order set `sub_dealer_order_id`='$apporderno' where `id`='$created_last_id'";
$resupd1 = mysql_query($sqlupd1);

/*$the_fetched_array = array();	
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
					
			
		//$res_mail = send_the_mail($to,$subject,$message);
		}
	}
}*/

}
$cntslno++;
}
}
$res_data = array("process_status"=>"YES","process_message"=>"The order successfully received.");
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Please select product then make order.");
}
echo json_encode($res_data);
mysql_close();
?>
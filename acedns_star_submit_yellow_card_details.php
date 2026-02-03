<?php
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$customer_master = "customer_master";
$product_master = "product_master";
$yellow_card_details = "yellow_card_details";
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


$linked_dealer_code = $_POST["logged_in_customer_code"] ? addslashes(trim($_POST["logged_in_customer_code"])) : "";
$selected_sub_dealer_code = $_POST["selected_sub_dealer_code"] ? addslashes(trim($_POST["selected_sub_dealer_code"])) : "";
$selected_date = $_POST["selected_date"] ? addslashes(trim($_POST["selected_date"])) : "";
$challan_no = $_POST["challan_no"] ? addslashes(trim($_POST["challan_no"])) : "";
$qty_in_bags = $_POST["qty_in_bags"] ? addslashes(trim($_POST["qty_in_bags"])) : "";
$selected_prod_code = $_POST["selected_prod_code"] ? addslashes(trim($_POST["selected_prod_code"])) : "";

if($linked_dealer_code==""){
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");
}else if($selected_sub_dealer_code==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please select a sub-dealer.");
}else if($selected_date==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please select a date.");
}else if($challan_no==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please enter challan number.");
}else if($qty_in_bags==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please enter quantity.");
}else if($selected_prod_code==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please select a product.");
}else{
$dns_customer_code = show_dns_customer_from_customer_code_code($linked_dealer_code);
$selected_sub_dealer_dns_code = show_dns_customer_from_customer_code_code($selected_sub_dealer_code);
$prod_dtld = show_product_data_from_prod_code($selected_prod_code);
$dns_prod_code = $prod_dtld["dns_prod_code"];
$prod_dtld_desc = $prod_dtld["prod_desc"];
$datetime_structure = date("YmdHis");
$yellow_card_no = "Y".$selected_sub_dealer_code.$datetime_structure;

$sqlin = "insert into $yellow_card_details (`yellow_card_no`,`customer_code`,`challan_no`,`challan_date`,`qty`,`qty_UOM`,`linked_dealer_code`) values('$yellow_card_no','$selected_sub_dealer_code','$challan_no','$selected_date','$qty_in_bags','$dns_prod_code','$linked_dealer_code')";
$resin = mysql_query($sqlin);
if($resin){
	$y_card_id = mysql_insert_id();
$res_data = array("process_status"=>"YES","process_message"=>"Successfully saved.","y_card_id"=>$y_card_id);
}else{
$res_data = array("process_status"=>"NO","process_message"=>"Failed to save this details.");	
}
}
echo json_encode($res_data);
mysql_close();
?>
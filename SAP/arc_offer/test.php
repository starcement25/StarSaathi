<?php
ini_set('memory_limit', '99999M');
set_time_limit(0);
include "star_connection.php";

function get_customer_data_check_by_id2($cust_id){
$customer_master = "customer_master";
$branch_credit_limit_status = "branch_credit_limit_status";
$customer_data = array("sts"=>"NO","customer_name"=>"","dns_customer_code"=>"","branch_code"=>"","is_branch_arc"=>"NO");
$custname = "";
$cust_id = $cust_id ? addslashes(trim($cust_id)) : "";
if($cust_id!=''){
	$sqls = "select `customer_name`,`dns_customer_code`,`branch_code` from $customer_master where `customer_code`='$cust_id'";
	$ress = mysql_query($sqls);
	$totress = mysql_num_rows($ress);
	if($totress>0){
		$rows = mysql_fetch_assoc($ress);
		$customer_name = $rows["customer_name"] ? trim($rows["customer_name"]) : "";
		$dns_customer_code = $rows["dns_customer_code"] ? trim($rows["dns_customer_code"]) : "";
		$branch_code = $rows["branch_code"] ? trim($rows["branch_code"]) : "";
		$customer_data["sts"] = "YES";
		$customer_data["customer_name"] = $customer_name;
		$customer_data["dns_customer_code"] = $dns_customer_code;
		$customer_data["branch_code"] = $branch_code;
		if($branch_code!=""){
			$sqls_bc = "select `arc_status` from $branch_credit_limit_status where `branch_code`='$branch_code' and `arc_status`='Y'";
			$ress_bc = mysql_query($sqls_bc);
			$totress_bc = mysql_num_rows($ress_bc);
			if($totress_bc>0){
			$customer_data["is_branch_arc"] = "YES";
			}
		}
		
	}
}
return $customer_data;
}
$cust_id = "C/0000239";
$arr = get_customer_data_check_by_id2($cust_id);

echo "<pre>";
print_r($arr);
?>
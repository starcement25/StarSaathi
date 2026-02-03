<?php
include "star_connection.php";
$arc_consumer_reg = "arc_consumer_reg";
$arc_gift_redeem_table = "arc_gift_redeem_table";
$arc_dealer_cust_point_table = "arc_dealer_cust_point_table";
$customer_master = "customer_master";
function get_cust_code_from_dealer_id($bid){
	$customer_master = "customer_master";
	$cust_code = "";
	$bid = $bid ? addslashes(trim($bid)) : "";
	if($bid!=''){
		$sqls = "select `customer_code` from $customer_master where `dns_customer_code`='$bid'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if($totress>0){
			$rows = mysql_fetch_assoc($ress);
			$cust_code = $rows["customer_code"] ? trim($rows["customer_code"]) : "";
		}
	}
	return $cust_code;
}

/*$sql1 = "SELECT `dns_customer_code` FROM $arc_consumer_reg group by `dns_customer_code` ";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
while($row1 = mysql_fetch_assoc($res1)){
$dns_customer_code = $row1["dns_customer_code"] ? trim($row1["dns_customer_code"]) : "";
if($dns_customer_code!=""){
$customer_code = get_cust_code_from_dealer_id($dns_customer_code);
if($customer_code!=""){
$sql_upd = "update $arc_consumer_reg set `customer_code`='$customer_code' where `dns_customer_code`='$dns_customer_code' ";
$res_upd = mysql_query($sql_upd);	
}
}
}
}*/


/*$sql1 = "SELECT `dns_customer_code` FROM $arc_gift_redeem_table group by `dns_customer_code` ";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
while($row1 = mysql_fetch_assoc($res1)){
$dns_customer_code = $row1["dns_customer_code"] ? trim($row1["dns_customer_code"]) : "";
if($dns_customer_code!=""){
$customer_code = get_cust_code_from_dealer_id($dns_customer_code);
if($customer_code!=""){
$sql_upd = "update $arc_gift_redeem_table set `customer_code`='$customer_code' where `dns_customer_code`='$dns_customer_code' ";
$res_upd = mysql_query($sql_upd);	
}
}
}
}*/



/*
$sql1 = "SELECT `dns_customer_code` FROM $arc_dealer_cust_point_table group by `dns_customer_code` ";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
while($row1 = mysql_fetch_assoc($res1)){
$dns_customer_code = $row1["dns_customer_code"] ? trim($row1["dns_customer_code"]) : "";
if($dns_customer_code!=""){
$customer_code = get_cust_code_from_dealer_id($dns_customer_code);
if($customer_code!=""){
$sql_upd = "update $arc_dealer_cust_point_table set `customer_code`='$customer_code' where `dns_customer_code`='$dns_customer_code' ";
$res_upd = mysql_query($sql_upd);	
}
}
}
}*/



echo "Done";
?>
<?php
header('Content-type: text/html; charset=utf-8');
include "star_connection.php";
mysql_set_charset("UTF8");
$t_main_order_pop = "T_MAIN_ORDER_POP";

function get_region_from_customer_code($the_customer_code){
$the_region = "";
$customer_master = "customer_master";

if($the_customer_code!=""){
$sql1 = "select `region` from $customer_master where `customer_code`='$the_customer_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_region = $row1["region"] ? addslashes(trim($row1["region"])) : "";
	}
}
return $the_region;
}

$sql_ck = "SELECT `customer_code` FROM $t_main_order_pop WHERE (`customer_region`='' or `customer_region` is null) group by `customer_code`";
$res_ck = mysql_query($sql_ck);
$tot_res_ck = mysql_num_rows($res_ck);
if($tot_res_ck>0){
while($row_ck = mysql_fetch_assoc($res_ck)){
$the_customer_code = $row_ck['customer_code'] ? trim($row_ck['customer_code']) : "";
if($the_customer_code!=""){
$fetched_cust_region = get_region_from_customer_code($the_customer_code);
if($fetched_cust_region!=""){
$sqlup = "update $t_main_order_pop set `customer_region`='$fetched_cust_region' where `customer_code`='$the_customer_code'";
$resup = mysql_query($sqlup);
}
}
}
}
echo "done";
mysql_close();
?>
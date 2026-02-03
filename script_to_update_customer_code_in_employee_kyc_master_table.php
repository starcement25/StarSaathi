<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
include "star_connection.php";
$employee_kyc_master = "employee_kyc_master";
$employee_master = "employee_master";
$customer_master = "customer_master";

$sql1 = "select `emp_code` from $employee_kyc_master order by `emp_code` asc";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1 > 0){
while($row1 = mysql_fetch_assoc($res1)){
$emp_code = $row1["emp_code"] ? addslashes(trim($row1["emp_code"])) : "";
if($emp_code!=""){
	$sql3 = "select `emp_code`,`dns_emp_code` from $employee_master where `emp_code`='$emp_code'";
	$res3 = mysql_query($sql3);
	$totres3 = mysql_num_rows($res3);
	if($totres3>0){
		$row3 = mysql_fetch_assoc($res3);
		$dns_emp_code = $row3["dns_emp_code"] ? addslashes(trim($row3["dns_emp_code"])) : "";
		if($dns_emp_code!=""){
		$sql4 = "select `customer_code`,`dns_customer_code` from $customer_master where `dns_customer_code`='$dns_emp_code'";
		$res4 = mysql_query($sql4);
		$totres4 = mysql_num_rows($res4);
		if($totres4>0){
			$row4 = mysql_fetch_assoc($res4);
			$customer_code = $row4["customer_code"] ? addslashes(trim($row4["customer_code"])) : "";
			$dns_customer_code = $row4["dns_customer_code"] ? addslashes(trim($row4["dns_customer_code"])) : "";
			$sql2 = "update $employee_kyc_master set `customer_code`='$customer_code',`dns_customer_code`='$dns_customer_code' where `emp_code`='$emp_code'";
			$res2 = mysql_query($sql2);
		}
		
		}
	}
}
}
}
echo "Done";
mysql_close();
?>
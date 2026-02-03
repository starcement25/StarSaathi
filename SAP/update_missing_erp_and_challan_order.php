<?php
ini_set('memory_limit', '99999M');
set_time_limit(0);
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$missing_erp_orders = "missing_erp_orders";
$missing_erp_challan = "missing_erp_challan";
$curr_date_time = date("Y-m-d H:i:s");
$res_data = array();

//update_erp_order(1);
update_erp_challan_order(1);

function update_erp_challan_order($pgno){
$res_msg = array();
$total_added_data = array();
$limit = 1000;
$t_dochallan = "T_DOCHALLAN";
$missing_erp_challan = "missing_erp_challan";
$last_updateds = date("Y-m-d H:i:s");
$pno = $pgno ? $pgno : 1;
$start_from = (($pno-1)*$limit);
if($pno!=''){
$sql1 = "SELECT * FROM $missing_erp_challan order by `id` asc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
	$the_erporderno = $row1["ERPORDERNO"] ? addslashes(trim($row1["ERPORDERNO"])) : "";
	$the_erporderdt = $row1["ERPORDERDT"] ? addslashes(trim($row1["ERPORDERDT"])) : "";
	if($the_erporderdt!=""){
		$the_erporderdt = date("m/d/Y",strtotime($the_erporderdt))." 12:00:00 AM";
	}	
	$the_challanno = $row1["CHALLANNO"] ? addslashes(trim($row1["CHALLANNO"])) : "";
	$the_challandt = $row1["CHALLANDT"] ? addslashes(trim($row1["CHALLANDT"])) : "";
	if($the_challandt!=""){
		$the_challandt = date("m/d/Y",strtotime($the_challandt))." 12:00:00 AM";
	}
	
	$the_dns_customer_code = $row1["dns_customer_code"] ? addslashes(trim($row1["dns_customer_code"])) : "";
	$the_dns_prod_code = $row1["dns_prod_code"] ? addslashes(trim($row1["dns_prod_code"])) : "";
	$the_prod_display_name = $row1["prod_display_name"] ? addslashes(trim($row1["prod_display_name"])) : "";
	$the_qty = $row1["QTY"] ? addslashes(trim($row1["QTY"])) : "";
	$the_challanqty = $row1["CHALLANQTY"] ? addslashes(trim($row1["CHALLANQTY"])) : "";
	$the_truckno = $row1["TRUCKNO"] ? addslashes(trim($row1["TRUCKNO"])) : "";
	$the_driverno = $row1["DRIVERNO"] ? addslashes(trim($row1["DRIVERNO"])) : "";
	
	$sql2 = "SELECT `ERPORDERNO`,`CHALLANNO` FROM $t_dochallan where `ERPORDERNO`='$the_erporderno' and `CHALLANNO`='$the_challanno'";
	$res2 = mysql_query($sql2);
	$totres2 = mysql_num_rows($res2);
	if($totres2>0){
	$sql4 = "update $t_dochallan set `ERPORDERDT`='$the_erporderdt',`CHALLANDT`='$the_challandt',`dns_prod_code`='$the_dns_prod_code',`prod_display_name`='$the_prod_display_name',`QTY`='$the_qty',`CHALLANQTY`='$the_challanqty',`dns_customer_code`='$the_dns_customer_code',`TRUCKNO`='$the_truckno',`DRIVERNO`='$the_driverno' where `ERPORDERNO`='$the_erporderno' and `CHALLANNO`='$the_challanno' ";
	$res4 = mysql_query($sql4);
	if(!$res4){
		echo mysql_error();
	}
	}else{
		$sql3 = "insert into $t_dochallan (`ERPORDERNO`,`ERPORDERDT`,`CHALLANNO`,`CHALLANDT`,`dns_prod_code`,`prod_display_name`,`QTY`,`CHALLANQTY`,`dns_customer_code`,`TRUCKNO`,`DRIVERNO`) values ('$the_erporderno','$the_erporderdt','$the_challanno','$the_challandt','$the_dns_prod_code','$the_prod_display_name','$the_qty','$the_challanqty','$the_dns_customer_code','$the_truckno','$the_driverno')";
		$res3 = mysql_query($sql3);
			if(!$res3){
			echo mysql_error();
			}
	}
	 
	
	
	}

$newpgno = ($pno+1);
update_erp_challan_order($newpgno);	
}else{
	echo "DONE MISSING ERP CHALLAN ORDER";
}
}
return;
}


function update_erp_order($pgno){
$res_msg = array();
$total_added_data = array();
$limit = 1000;
$t_apperpdo = "T_APPERPDO";
$missing_erp_orders = "missing_erp_orders";
$last_updateds = date("Y-m-d H:i:s");
$pno = $pgno ? $pgno : 1;
$start_from = (($pno-1)*$limit);
if($pno!=''){
$sql1 = "SELECT * FROM $missing_erp_orders order by `id` asc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
	$the_erporderno = $row1["ERPORDERNO"] ? addslashes(trim($row1["ERPORDERNO"])) : "";
	$the_erporderdt = $row1["ERPORDERDT"] ? addslashes(trim($row1["ERPORDERDT"])) : "";
	if($the_erporderdt!=""){
		$the_erporderdt = date("m/d/Y",strtotime($the_erporderdt))." 12:00:00 AM";
	}
	$the_dns_customer_code = $row1["dns_customer_code"] ? addslashes(trim($row1["dns_customer_code"])) : "";
	$the_dns_prod_code = $row1["dns_prod_code"] ? addslashes(trim($row1["dns_prod_code"])) : "";
	$the_prod_display_name = $row1["prod_display_name"] ? addslashes(trim($row1["prod_display_name"])) : "";
	$the_qty = $row1["QTY"] ? addslashes(trim($row1["QTY"])) : "";
	$the_dns_prod_code = $row1["dns_prod_code"] ? addslashes(trim($row1["dns_prod_code"])) : "";
	$sql2 = "SELECT `ERPORDERNO` FROM $t_apperpdo where `ERPORDERNO`='$the_erporderno'";
	$res2 = mysql_query($sql2);
	$totres2 = mysql_num_rows($res2);
	if($totres2>0){
	$sql4 = "update $t_apperpdo set `ERPORDERDT`='$the_erporderdt',`dns_customer_code`='$the_dns_customer_code',`dns_prod_code`='$the_dns_prod_code',`prod_display_name`='$the_prod_display_name',`QTY`='$the_qty' where `ERPORDERNO`='$the_erporderno' ";
	$res4 = mysql_query($sql4);
	}else{
		$sql3 = "insert into $t_apperpdo (`ERPORDERNO`,`ERPORDERDT`,`dns_customer_code`,`dns_prod_code`,`prod_display_name`,`QTY`) values ('$the_erporderno','$the_erporderdt','$the_dns_customer_code','$the_dns_prod_code','$the_prod_display_name','$the_qty')";
		$res3 = mysql_query($sql3);
	}
	 
	
	
	}

$newpgno = ($pno+1);
update_erp_order($newpgno);	
}else{
	echo "DONE MISSING ERP ORDER";
}
}
return;
}

mysql_close();
?>
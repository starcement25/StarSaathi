<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & E_NOTICE & E_DEPRECATED);
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$get_fetch_type = $_GET["get_type"] ? trim($_GET["get_type"]) : "";
if($get_fetch_type!=""){
	if($get_fetch_type=="pending" || $get_fetch_type=="all"){
		//sync_star_details();
		startCreatOrderCsvfile($get_fetch_type);
	}else{
		exit;
	}
}else{
	exit;
}

function sync_star_details(){
	$links = "http://salesmpower.acedns.in/acedns_star_details_update.php";
	$ch_sheader = curl_init();
	curl_setopt($ch_sheader,CURLOPT_URL,$links);
	curl_setopt($ch_sheader,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch_sheader, CURLOPT_FORBID_REUSE, true);
	curl_setopt($ch_sheader, CURLOPT_HTTPHEADER, array('Cache-Control: max-age=0', 'Connection: keep-alive', 'Keep-Alive: 3600'));
	$bodyres=curl_exec($ch_sheader);
	curl_close($ch_sheader);
	return;
}

function startCreatOrderCsvfile($get_fetch_type){
$t_apperpdo = "T_APPERPDO";
$customer_master = "customer_master";
$employee_master = "employee_master";
$branch_master = "branch_master";
$order_show_branch="";

$dnsbcarr = array();
$dnsbcstr ="";
$theactbcarr = array();
$theactbcstr ="";

if($_SESSION["start_user_type"]=="MANAGER"){
	$order_show_branch = $_SESSION["order_show_branch"] ? trim($_SESSION["order_show_branch"]) : "";
	if($order_show_branch=="NE"){
	$dns_branchcode_master = "north_east_branch";
	}else if($order_show_branch=="NOTNE"){
	$dns_branchcode_master = "not_north_east_branch";
	}
	if($order_show_branch=="NE" || $order_show_branch=="NOTNE"){	
	$sqlbm = "select `branch_code` from $dns_branchcode_master";
	$resbm = mysql_query($sqlbm);
	$totresbm = mysql_num_rows($resbm);
	if($totresbm>0){
		$dnsbcarr = array();
		while($rowbm=mysql_fetch_assoc($resbm)){
			$the_dns_bc = $rowbm["branch_code"] ? trim($rowbm["branch_code"]) : "";
			if($the_dns_bc!=""){
				$dnsbcarr[] = $the_dns_bc;
			}
		}
		if(count($dnsbcarr)>0){
			$dnsbcstr = implode("','",$dnsbcarr);
	$sqlabc = "select `branch_code` from $branch_master where `dns_branch_code` in('".$dnsbcstr."')";
	$resabc = mysql_query($sqlabc);
	$totresabc = mysql_num_rows($resabc);
	if($totresabc>0){
		while($rowabc=mysql_fetch_assoc($resabc)){
			$the_bc = $rowabc["branch_code"] ? trim($rowabc["branch_code"]) : "";
			if($the_bc!=""){
				$theactbcarr[] = $the_bc;
			}
		}
		if(count($theactbcarr)>0){
			$theactbcstr = implode("','",$theactbcarr);
			
		}
		
	}
	
		}
	}
	}
	
	
	
	
}


$get_fetch_type = $get_fetch_type ? strtolower(trim($get_fetch_type)) : "";
$curr_date = date("jS_M_Y_h_m_s_A");
if($get_fetch_type=="pending"){
	$the_file_name = "pending_orders_".$curr_date.".csv";
	$where_qry = " and $t_apperpdo.`STATUS`='Order received' ";
}else{
	$the_file_name = "all_orders_".$curr_date.".csv";
	$where_qry = "";
}
$output = "";
if($_SESSION["start_user_type"]=="MANAGER" && ($order_show_branch=="NE" || $order_show_branch=="NOTNE")){
$qry = "select $t_apperpdo.`APPORDERNO`,$t_apperpdo.`ERPORDERNO`,$t_apperpdo.`order_date`,$t_apperpdo.`customer_code`,$t_apperpdo.dns_customer_code,$customer_master.`customer_name`,$t_apperpdo.`order_for` as `consignee_name_and_address`,$t_apperpdo.`prod_display_name`,$t_apperpdo.`QTY`,$t_apperpdo.`STATUS` from $t_apperpdo left join $customer_master on $t_apperpdo.`customer_code`=$customer_master.`customer_code` left join $employee_master on $t_apperpdo.`dns_customer_code`=$employee_master.`dns_emp_code` where $t_apperpdo.`APPORDERNO`!='' and $employee_master.`branch_code` in('".$theactbcstr."') $where_qry order by $t_apperpdo.`order_date` asc";
}else{
$qry = "select $t_apperpdo.`APPORDERNO`,$t_apperpdo.`ERPORDERNO`,$t_apperpdo.`order_date`,$t_apperpdo.`customer_code`,$t_apperpdo.dns_customer_code,$customer_master.`customer_name`,$t_apperpdo.`order_for` as `consignee_name_and_address`,$t_apperpdo.`prod_display_name`,$t_apperpdo.`QTY`,$t_apperpdo.`STATUS` from $t_apperpdo left join $customer_master on $t_apperpdo.`customer_code`=$customer_master.`customer_code` where $t_apperpdo.`APPORDERNO`!='' $where_qry order by $t_apperpdo.`order_date` asc";
}
$sql = mysql_query($qry);
$columns_total = mysql_num_fields($sql);

// Get The Field Name

for ($i = 0; $i < $columns_total; $i++) {
$heading = mysql_field_name($sql, $i);
$output .= '"'.$heading.'",';
}
$output .="\n";
// Get Records from the table

while ($row = mysql_fetch_array($sql)) {
for ($i = 0; $i < $columns_total; $i++) {
$output .='"'.$row["$i"].'",';
}
$output .="\n";
}

// Download the file

$filename = $the_file_name;
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');    
header('Expires: 0');
echo $output;
exit;
}

mysql_close();
?>
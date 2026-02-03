<?php
include "edms_connection.php";
$ledger = "ledger";
$ledger_balance = "ledger_balance";
$verify_ledger_details = "verify_ledger_details";
$ledger_data = array();
$ledger_balance_data = array();
$the_query = "";
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
$current_month_dt = date("Y-m-")."1 00:00:00";
if($the_id!=""){

$sql33 = "SELECT `ledger_year_month_day`,DATE_FORMAT(`ledger_year_month_day`, '%Y-%m-%d') as `mnyr` FROM $verify_ledger_details where `customer_code`='$the_id' order by `mnyr` desc limit 0,1";
$res33 = mysql_query($sql33);
$tot_res33 = mysql_num_rows($res33);
if($tot_res33>0){
	$row33 = mysql_fetch_assoc($res33);
	$the_year_month_day = $row33["ledger_year_month_day"] ? addslashes(trim($row33["ledger_year_month_day"])) : "";
	if($the_year_month_day!=""){
		$the_yr_mn = date("Y-m",strtotime($the_year_month_day));
		$the_query = " and `mnyr`>'$the_yr_mn' ";
	}else{
		$the_query = "";
	}
}else{
	$the_year_month_day = "";
	$the_query = "";
}

/*
select *,DATE_FORMAT(STR_TO_DATE(`voucher_date`, '%m/%d/%Y %h:%i:%s %p'), '%Y-%m-%d %H:%i:%s') as `e_date`,DATE_FORMAT(STR_TO_DATE(`voucher_date`, '%m/%d/%Y %h:%i:%s %p'), '%Y-%m') as `mnyr` from ledger where `customer_code`='C/0007920' and `voucher_date`!='' having DATE(`e_date`)<'2019-04-1 00:00:00' order by `e_date` asc
*/

$sqlall = "select *,DATE_FORMAT(STR_TO_DATE(`voucher_date`, '%m/%d/%Y %h:%i:%s %p'), '%Y-%m-%d %H:%i:%s') as `e_date`,DATE_FORMAT(STR_TO_DATE(`voucher_date`, '%m/%d/%Y %h:%i:%s %p'), '%Y-%m') as `mnyr` from $ledger where `customer_code`='$the_id' and `voucher_date`!='' having DATE(`e_date`)<'$current_month_dt' $the_query order by `e_date` asc";
$resall = mysql_query($sqlall);
$totall = mysql_num_rows($resall);
if($totall>0){
	while($row11=mysql_fetch_assoc($resall)){
		$customer_code = $row11["customer_code"];
		$dns_customer_code = $row11["dns_customer_code"];
		$voucher_date = $row11["voucher_date"] ? trim($row11["voucher_date"]) : "";
		$voucher_date_concerted = date("m/d/Y h:i:s A",strtotime($voucher_date));
		$voucher_month_year = date("m-Y",strtotime($voucher_date));
		$ledger_of = date("M,Y",strtotime($voucher_date));
		$ledger_year_month_day = date("Y-m",strtotime($voucher_date))."-1";
		$voucher_no = $row11["voucher_no"];
		$quantity = $row11["quantity"]." MT";
		$amount_dr = $row11["amount_dr"] ? floatval(trim($row11["amount_dr"])) : 0;
		$amount_cr = $row11["amount_cr"] ? floatval(trim($row11["amount_cr"])) : 0;
		$balance = $row11["balance"];
		$entry_date = $row11["entry_date"];
		$converted_voucher_date = "";
		if(array_key_exists($voucher_month_year,$ledger_data)){
			$prev_amount_cr = $ledger_data[$voucher_month_year]["total_amount_cr"];
			$prev_amount_dr = $ledger_data[$voucher_month_year]["total_amount_dr"];
			$new_amount_cr = ($prev_amount_cr+$amount_cr);
			$new_amount_dr = ($prev_amount_dr+$amount_dr);
			$ledger_data[$voucher_month_year]["total_amount_cr"] = $new_amount_cr;
			$ledger_data[$voucher_month_year]["total_amount_dr"] = $new_amount_dr;		
		}else{
		$ledger_data[$voucher_month_year] = array("customer_code"=>$customer_code,"dns_customer_code"=>$dns_customer_code,"total_amount_dr"=>$amount_dr,"total_amount_cr"=>$amount_cr,"ledger_month_year"=>$voucher_month_year,"ledger_year_month_day"=>$ledger_year_month_day,"ledger_of"=>$ledger_of);
		}
		}
$res_data = array("process_status"=>"YES","process_message"=>"Success.","check_ledger_data"=>$ledger_data);
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"No data found.");
}
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"The id is mandatory.");
}	
echo json_encode($res_data);
mysql_close();
?>
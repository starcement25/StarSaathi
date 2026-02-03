<?php
include "edms_connection.php";
$ledger = "ledger";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";
$ledger_data = array();
$ledger_balance_data = array();
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
if($the_id!=""){

$sql3 = "select `dns_customer_code` from $customer_master where `customer_code`='$the_id'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
$row3 = mysql_fetch_assoc($res3);
$the_dealer_id = addslashes(trim($row3["dns_customer_code"]));
$dlr_code_qry = "  or `dns_customer_code`='$the_dealer_id'";
$dlr_code_qry2 = "  or `dns_customer_code`='$the_dealer_id'";	
}else{
$dlr_code_qry = "";	
$dlr_code_qry2 = "";
}
$sqlall = "select *,DATE_FORMAT(STR_TO_DATE(`voucher_date`, '%m/%d/%Y %h:%i:%s %p'), '%Y-%m-%d %H:%i:%s') as `e_date` from $ledger where `customer_code`='$the_id' $dlr_code_qry order by `e_date` desc limit 0,50";
$resall = mysql_query($sqlall);
$totall = mysql_num_rows($resall);
if($totall>0){
	while($row11=mysql_fetch_assoc($resall)){
		$customer_code = $row11["customer_code"];
		$dns_customer_code = $row11["dns_customer_code"];
		$voucher_date = $row11["voucher_date"] ? trim($row11["voucher_date"]) : "";
		if($voucher_date!=""){
			$voucher_date = date("m/d/Y h:i:s A",strtotime($voucher_date));
		}
		$voucher_no = $row11["voucher_no"];
		$quantity = $row11["quantity"]." MT";
		$amount_dr = $row11["amount_dr"];
		$amount_cr = $row11["amount_cr"];
		$balance = $row11["balance"];
		$entry_date = $row11["entry_date"];
		$converted_voucher_date = "";
		$ledger_data[] = array("customer_code"=>$customer_code,"dns_customer_code"=>$dns_customer_code,"voucher_date"=>$voucher_date,"voucher_no"=>$voucher_no,"quantity"=>$quantity,"amount_dr"=>$amount_dr,"amount_cr"=>$amount_cr,"balance"=>$balance,"narration"=>$balance,"entry_date"=>$entry_date,"converted_voucher_date"=>$converted_voucher_date);
	}	
}

$sqlall2 = "select * from $ledger_balance where `customer_code`='$the_id' $dlr_code_qry2";
$resall2 = mysql_query($sqlall2);
$totall2 = mysql_num_rows($resall2);
if($totall2>0){
$row112=mysql_fetch_assoc($resall2);
$lb_customer_code = $row112["customer_code"];
$lb_dns_customer_code = $row112["dns_customer_code"];
$balance = $row112["balance"];
/*$date = $row112["date"] ? trim($row112["date"]) : "";
if($date!=""){
$date = date("m/d/Y h:i:s A",strtotime($date));
}*/
$date = date("m/d/Y");
$link = $row112["link"];
$ledger_balance_data = array("customer_code"=>$lb_customer_code,"dns_customer_code"=>$lb_dns_customer_code,"balance"=>$balance,"date"=>$date,"link"=>$link);
}

$res_data = array("process_status"=>"YES","process_message"=>"Success.","ledger_data"=>$ledger_data,"ledger_balance_data"=>$ledger_balance_data);

}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"The id is mandatory.");
}	
echo json_encode($res_data);
mysql_close();
?>
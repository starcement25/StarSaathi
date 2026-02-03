<?php
error_reporting(E_STRICT);
ini_set('memory_limit', '99999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
include "function-sfa.php";
	
include "star_connection.php";
function send_process_complete_mail(){
require_once('class.phpmailer.php');
$mail             = new PHPMailer();
$date_time_val = date("Y-m-d H:i:s");
$body             = "<br>Cron process completed at ".$date_time_val;
$body             = eregi_replace("[\]",'',$body);
$mail->IsSMTP(); // telling the class to use SMTP
$mail->Host       = "103.87.174.95"; // SMTP server
$mail->SMTPDebug  = "";                     // enables SMTP debug information (for testing)
                                           // 1 = errors and messages
                                           // 2 = messages only
$mail->SMTPAuth   = true;                  // enable SMTP authentication
$mail->Host       = "103.87.174.95"; // sets the SMTP server
$mail->Port       = 587;                    // set the SMTP port for the GMAIL server
$mail->Username   = "dev@starsaathi.com"; // SMTP account username
$mail->Password   = "google3d33#";        // SMTP account password
$mail->SetFrom('dev@starsaathi.com', 'Starsaathi');
$mail->AddReplyTo('dev@starsaathi.com', 'Starsaathi');
$mail->Subject    = "Starsaathi Cron status";
$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
$mail->MsgHTML($body);
$mail->AddAddress("suranjitd@coral.in", "Suranjit Das");
$mail->AddAddress("mriduj@coral.in", "Mridu");
$mlsts = $mail->Send();	
}
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$ledger = "ledger";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";
$product_master = "product_master";
$cron_update_table = "cron_update_table";
$curr_date_time = date("Y-m-d H:i:s");
$prev_date = date('Y-m-d',strtotime("-12 days"));
$res_data = array();
function process_update_tracking($process_name,$process_start_end_flag,$remark){
$sync_cron = "cron_update_table";
$curr_datetime = date("Y-m-d H:i:s");
$process_name = $process_name ? $process_name : "";
$process_start_end_flag = $process_start_end_flag ? $process_start_end_flag : "";
$remark = $remark ? trim(addslashes($remark)) : "";
if($process_name!="" && $process_start_end_flag!=""){
	if($process_start_end_flag=="START"){
		$sql = "update $sync_cron set `last_start_datetime`='$curr_datetime',`remark`='$remark' where `process_name`='$process_name'";
		mysql_query($sql);
	}else if($process_start_end_flag=="END"){
		$sql = "update $sync_cron set `last_end_datetime`='$curr_datetime',`remark`='$remark' where `process_name`='$process_name'";
		mysql_query($sql);
	}
}
return;	
}
function show_product_code_by_dns_product_code($dns_prod_code){
	$product_master = "product_master";
	$prod_code = "";
	$dns_prod_code = $dns_prod_code ? addslashes(trim($dns_prod_code)) : "";
	if($dns_prod_code!=""){
	$sqlcc = "select `prod_code` from $product_master where `dns_prod_code`='$dns_prod_code'";
	$rescc = mysql_query($sqlcc);
	$totrescc = mysql_num_rows($rescc);
	if($totrescc>0){
	$rowcc=mysql_fetch_assoc($rescc);
	$prod_code = addslashes($rowcc["prod_code"]);
	}
	}
	return $prod_code;
	
}
function show_customer_code_by_dns_customer_code($dns_cust_code){
	$customer_master = "customer_master";
	$cust_code = "";
	$dns_cust_code = $dns_cust_code ? addslashes(trim($dns_cust_code)) : "";
	if($dns_cust_code!=""){
		$sqlcc = "select `customer_code` from $customer_master where `dns_customer_code`='$dns_cust_code'";
		$rescc = mysql_query($sqlcc);
		$totrescc = mysql_num_rows($rescc);
		if($totrescc>0){
			$rowcc=mysql_fetch_assoc($rescc);
			$cust_code = addslashes($rowcc["customer_code"]);
		}
}
	return $cust_code;
}
/*--------------CRON PROCESS NAMES--------------------*/
$update_customer_code_in_t_apperpdo = "update_customer_code_in_t_apperpdo";
$update_customer_code_in_t_dochallan = "update_customer_code_in_t_dochallan";
$update_customer_code_in_ledger = "update_customer_code_in_ledger";
$update_customer_code_in_ledger_balance = "update_customer_code_in_ledger_balance";
$update_prod_code_by_prod_dns_code_in_t_apperpdo = "update_prod_code_by_prod_dns_code_in_t_apperpdo";
$update_prod_code_by_prod_dns_code_in_t_dochallan = "update_prod_code_by_prod_dns_code_in_t_dochallan";
$update_order_status_in_t_apperpdo = "update_order_status_in_t_apperpdo";
$sqlcrck1 = "select `id` from $cron_update_table where `remark`='Process End'";
$rescrck1 = mysql_query($sqlcrck1);
$totrescrck1 = mysql_num_rows($rescrck1);
if($totrescrck1>=7){
process_update_tracking($update_customer_code_in_t_apperpdo,"START","Process Start");
update_customer_code_in_t_apperpdo(1,$prev_date);
}else{
$sqlcrck14 = "select `last_start_datetime` from $cron_update_table order by `id` asc limit 0,1";
$rescrck14 = mysql_query($sqlcrck14);
$totrescrck14 = mysql_num_rows($rescrck14);
if($totrescrck14>0){
	$rowck14 = mysql_fetch_assoc($rescrck14);
	$the_last_start_datetime = $rowck14["last_start_datetime"] ? trim($rowck14["last_start_datetime"]) : "";
	if($the_last_start_datetime!=""){
		$to_time = strtotime($curr_date_time);
		$from_time = strtotime($the_last_start_datetime);
		$mint_diff = round(abs($to_time - $from_time) / 60,2);
		if($mint_diff>35){
		process_update_tracking($update_customer_code_in_t_apperpdo,"START","Process Start");
		update_customer_code_in_t_apperpdo(1,$prev_date);
		}
	}
}
}
/*-------------------UPDATE CUSTOMER CODE IN T_APPERPDO TABLE FUNCTION START-------------------------------------------*/
function update_customer_code_in_t_apperpdo($pgno,$prev_date){
$res_msg = array();
$total_added_data = array();
$limit = 1000;
$update_customer_code_in_t_apperpdo = "update_customer_code_in_t_apperpdo";
$update_customer_code_in_t_dochallan = "update_customer_code_in_t_dochallan";
$t_apperpdo = "T_APPERPDO";
$customer_master = "customer_master";
$last_updateds = date("Y-m-d H:i:s");
$pno = $pgno ? $pgno : 1;
$start_from = (($pno-1)*$limit);
if($pno!=''){
$sql1 = "SELECT `id`,`dns_customer_code`,`customer_code`,DATE_FORMAT(`order_date`,'%Y-%m-%d') as `orddate` FROM $t_apperpdo having `orddate`>='".$prev_date."' order by `order_date` asc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
	$the_id = $row1["id"] ? addslashes(trim($row1["id"])) : "";
	$dns_customer_code = $row1["dns_customer_code"] ? addslashes(trim($row1["dns_customer_code"])) : "";
	$customer_code = $row1["customer_code"] ? addslashes(trim($row1["customer_code"])) : "";
	if($customer_code==""){
	$get_customer_code = show_customer_code_by_dns_customer_code($dns_customer_code);
	$get_customer_code = $get_customer_code ? trim($get_customer_code) : "";
		if($get_customer_code!=""){
			$sqlupd1 = "update $t_apperpdo set `customer_code`='$get_customer_code' where `id`='$the_id'";
			$resupd1 = mysql_query($sqlupd1);
		}
	}
	}
$newpgno = ($pno+1);
update_customer_code_in_t_apperpdo($newpgno,$prev_date);	
}else{
	process_update_tracking($update_customer_code_in_t_apperpdo,"END","Process End");
	process_update_tracking($update_customer_code_in_t_dochallan,"START","Process Start");
	update_customer_code_in_t_dochallan(1,$prev_date);
}
}
return;
}
/*-------------------UPDATE CUSTOMER CODE IN T_APPERPDO TABLE FUNCTION END-------------------------------------------*/
/*-------------------UPDATE CUSTOMER CODE IN T_DOCHALLAN TABLE FUNCTION START-------------------------------------------*/
function update_customer_code_in_t_dochallan($pgno,$prev_date){
$res_msg = array();
$total_added_data = array();
$limit = 1000;
$update_customer_code_in_t_dochallan = "update_customer_code_in_t_dochallan";
$update_customer_code_in_ledger = "update_customer_code_in_ledger";
$t_dochallan = "T_DOCHALLAN";
$customer_master = "customer_master";
$last_updateds = date("Y-m-d H:i:s");
$pno = $pgno ? $pgno : 1;
$start_from = (($pno-1)*$limit);
if($pno!=''){
$sql1 = "SELECT `id`,`dns_customer_code`,`customer_code`,DATE_FORMAT(`LMDT`,'%Y-%m-%d') as `orddate` FROM $t_dochallan having `orddate`>='".$prev_date."' order by `LMDT` asc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
	$the_id = $row1["id"] ? addslashes(trim($row1["id"])) : "";
	$dns_customer_code = $row1["dns_customer_code"] ? addslashes(trim($row1["dns_customer_code"])) : "";
	$customer_code = $row1["customer_code"] ? addslashes(trim($row1["customer_code"])) : "";
	if($customer_code==""){
		$get_customer_code = show_customer_code_by_dns_customer_code($dns_customer_code);
		$get_customer_code = $get_customer_code ? trim($get_customer_code) : "";
		if($get_customer_code!=""){
			$sqlupd1 = "update $t_dochallan set `customer_code`='$get_customer_code' where `id`='$the_id'";
			$resupd1 = mysql_query($sqlupd1);
		}
	}
	}
$newpgno = ($pno+1);
update_customer_code_in_t_dochallan($newpgno,$prev_date);	
}else{
	process_update_tracking($update_customer_code_in_t_dochallan,"END","Process End");
	process_update_tracking($update_customer_code_in_ledger,"START","Process Start");
	update_customer_code_in_ledger(1,$prev_date);
}
}
return;
}
/*-------------------UPDATE CUSTOMER CODE IN T_DOCHALLAN TABLE FUNCTION END-------------------------------------------*/
/*-------------------UPDATE CUSTOMER CODE IN LEDGER TABLE FUNCTION START-------------------------------------------*/
function update_customer_code_in_ledger($pgno,$prev_date){
$res_msg = array();
$total_added_data = array();
$limit = 1000;
$ledger = "ledger";
$update_customer_code_in_ledger = "update_customer_code_in_ledger";
$update_customer_code_in_ledger_balance = "update_customer_code_in_ledger_balance";
$customer_master = "customer_master";
$last_updateds = date("Y-m-d H:i:s");
$pno = $pgno ? $pgno : 1;
$start_from = (($pno-1)*$limit);
if($pno!=''){
$sql1 = "SELECT `ldg_id`,`dns_customer_code`,`customer_code`,DATE_FORMAT(`entry_date`,'%Y-%m-%d') as `orddate` FROM $ledger having `orddate`>='".$prev_date."' order by `orddate` asc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
	$the_id = $row1["ldg_id"] ? addslashes(trim($row1["ldg_id"])) : "";
	$dns_customer_code = $row1["dns_customer_code"] ? addslashes(trim($row1["dns_customer_code"])) : "";
	$customer_code = $row1["customer_code"] ? addslashes(trim($row1["customer_code"])) : "";
	if($customer_code==""){
		$get_customer_code = show_customer_code_by_dns_customer_code($dns_customer_code);
		$get_customer_code = $get_customer_code ? trim($get_customer_code) : "";
		if($get_customer_code!=""){
		$sqlupd1 = "update $ledger set `customer_code`='$get_customer_code' where `ldg_id`='$the_id'";
		$resupd1 = mysql_query($sqlupd1);
		}
	}
	}
$newpgno = ($pno+1);
update_customer_code_in_ledger($newpgno,$prev_date);	
}else{
	process_update_tracking($update_customer_code_in_ledger,"END","Process End");
	process_update_tracking($update_customer_code_in_ledger_balance,"START","Process Start");
	update_customer_code_in_ledger_balance(1,$prev_date);
}
}
return;
}
/*-------------------UPDATE CUSTOMER CODE IN LEDGER TABLE FUNCTION END-------------------------------------------*/
/*-------------------UPDATE CUSTOMER CODE IN LEDGER BALANCE TABLE FUNCTION START-------------------------------------------*/
function update_customer_code_in_ledger_balance($pgno,$prev_date){
$res_msg = array();
$total_added_data = array();
$limit = 1000;
$update_customer_code_in_ledger_balance = "update_customer_code_in_ledger_balance";
$update_prod_code_by_prod_dns_code_in_t_apperpdo = "update_prod_code_by_prod_dns_code_in_t_apperpdo";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";
$last_updateds = date("Y-m-d H:i:s");
$pno = $pgno ? $pgno : 1;
$start_from = (($pno-1)*$limit);
if($pno!=''){
$sql1 = "SELECT `lb_id`,`dns_customer_code`,`customer_code`,DATE_FORMAT(`entry_date`,'%Y-%m-%d') as `orddate` FROM $ledger_balance having `orddate`>='".$prev_date."' order by `orddate` asc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
	$the_id = $row1["lb_id"] ? addslashes(trim($row1["lb_id"])) : "";
	$dns_customer_code = $row1["dns_customer_code"] ? addslashes(trim($row1["dns_customer_code"])) : "";
	$customer_code = $row1["customer_code"] ? addslashes(trim($row1["customer_code"])) : "";
	if($customer_code==""){
		$get_customer_code = show_customer_code_by_dns_customer_code($dns_customer_code);
		$get_customer_code = $get_customer_code ? trim($get_customer_code) : "";
		if($get_customer_code!=""){
			$sqlupd1 = "update $ledger_balance set `customer_code`='$get_customer_code' where `lb_id`='$the_id'";
			$resupd1 = mysql_query($sqlupd1);
		}
	}
	}
$newpgno = ($pno+1);
update_customer_code_in_ledger_balance($newpgno,$prev_date);	
}else{
	process_update_tracking($update_customer_code_in_ledger_balance,"END","Process End");
	process_update_tracking($update_prod_code_by_prod_dns_code_in_t_apperpdo,"START","Process Start");
update_prod_code_by_prod_dns_code_in_t_apperpdo(1,$prev_date);
}
}
return;
}
/*-------------------UPDATE CUSTOMER CODE IN LEDGER BALANCE TABLE FUNCTION END-------------------------------------------*/
/*-------------------UPDATE PRODUCT CODE BY PRODUCT DNS CODE IN T_APPERPDO TABLE FUNCTION START-------------------------------------------*/
function update_prod_code_by_prod_dns_code_in_t_apperpdo($pgno,$prev_date){
$res_msg = array();
$total_added_data = array();
$limit = 1000;
$update_prod_code_by_prod_dns_code_in_t_apperpdo = "update_prod_code_by_prod_dns_code_in_t_apperpdo";
$update_prod_code_by_prod_dns_code_in_t_dochallan = "update_prod_code_by_prod_dns_code_in_t_dochallan";
$t_apperpdo = "T_APPERPDO";
$product_master = "product_master";
$last_updateds = date("Y-m-d H:i:s");
$pno = $pgno ? $pgno : 1;
$start_from = (($pno-1)*$limit);
if($pno!=''){
$sql1 = "SELECT `id`,`dns_prod_code`,`prod_code`,DATE_FORMAT(`order_date`,'%Y-%m-%d') as `orddate` FROM $t_apperpdo having `orddate`>='".$prev_date."' order by `orddate` asc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
	$the_id = $row1["id"] ? addslashes(trim($row1["id"])) : "";
	$dns_prod_code = $row1["dns_prod_code"] ? addslashes(trim($row1["dns_prod_code"])) : "";
	$prod_code = $row1["prod_code"] ? addslashes(trim($row1["prod_code"])) : "";
	if($prod_code==""){
		$get_product_code = show_product_code_by_dns_product_code($dns_prod_code);
		$get_product_code = $get_product_code ? trim($get_product_code) : "";
		if($get_product_code!=""){
			$sqlupd1 = "update $t_apperpdo set `prod_code`='$get_product_code' where `id`='$the_id'";
			$resupd1 = mysql_query($sqlupd1);
		}
	}
	}
$newpgno = ($pno+1);
update_prod_code_by_prod_dns_code_in_t_apperpdo($newpgno,$prev_date);	
}else{
	process_update_tracking($update_prod_code_by_prod_dns_code_in_t_apperpdo,"END","Process End");
	process_update_tracking($update_prod_code_by_prod_dns_code_in_t_dochallan,"START","Process Start");
 update_prod_code_by_prod_dns_code_in_t_dochallan(1,$prev_date);
}
}
return;
}
/*-------------------UPDATE PRODUCT CODE BY PRODUCT DNS CODE IN T_APPERPDO TABLE FUNCTION END-------------------------------------------*/
/*-------------------UPDATE PRODUCT CODE BY PRODUCT DNS CODE IN T_DOCHALLAN TABLE FUNCTION START-------------------------------------------*/
function update_prod_code_by_prod_dns_code_in_t_dochallan($pgno,$prev_date){
$res_msg = array();
$total_added_data = array();
$limit = 1000;
$update_prod_code_by_prod_dns_code_in_t_dochallan = "update_prod_code_by_prod_dns_code_in_t_dochallan";
$update_order_status_in_t_apperpdo = "update_order_status_in_t_apperpdo";
$t_dochallan = "T_DOCHALLAN";
$product_master = "product_master";
$last_updateds = date("Y-m-d H:i:s");
$pno = $pgno ? $pgno : 1;
$start_from = (($pno-1)*$limit);
if($pno!=''){
$sql1 = "SELECT `id`,`dns_prod_code`,`prod_code`,DATE_FORMAT(`LMDT`,'%Y-%m-%d') as `orddate` FROM $t_dochallan having `orddate`>='".$prev_date."' order by `LMDT` asc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
	$the_id = $row1["id"] ? addslashes(trim($row1["id"])) : "";
	$dns_prod_code = $row1["dns_prod_code"] ? addslashes(trim($row1["dns_prod_code"])) : "";
	$prod_code = $row1["prod_code"] ? addslashes(trim($row1["prod_code"])) : "";
	if($prod_code==""){
		$get_product_code = show_product_code_by_dns_product_code($dns_prod_code);
		$get_product_code = $get_product_code ? trim($get_product_code) : "";
		if($get_product_code!=""){
			$sqlupd1 = "update $t_dochallan set `prod_code`='$get_product_code' where `id`='$the_id'";
			$resupd1 = mysql_query($sqlupd1);
		}
	}
	}
$newpgno = ($pno+1);
update_prod_code_by_prod_dns_code_in_t_dochallan($newpgno,$prev_date);	
}else{
	process_update_tracking($update_prod_code_by_prod_dns_code_in_t_dochallan,"END","Process End");
	process_update_tracking($update_order_status_in_t_apperpdo,"START","Process Start");
	//$prev_date = date('Y-m-d',strtotime("-4 days"));
 update_order_status_in_t_apperpdo(1,$prev_date);
}
}
return;
}
/*-------------------UPDATE PRODUCT CODE BY PRODUCT DNS CODE IN T_DOCHALLAN TABLE FUNCTION END-------------------------------------------*/
/*-------------------UPDATE ORDER STATUS IN T_APPERPDO TABLE FUNCTION START-------------------------------------------*/
function update_order_status_in_t_apperpdo($pgno,$prev_date){
	/*$servername = "localhost";
$username = "starsaat_dnsprod";
$password = "dnsprod1234#";
$db_name = "starsaat_START";
	$conn = mysql_connect($servername, $username, $password);
if(!$conn){
   die('Could not connect: ' . mysql_error());
}
$db_selected = mysql_select_db($db_name, $conn);
if (!$db_selected) {
    die ('Can\'t connect to database : ' . mysql_error());
}*/
$res_msg = array();
$total_added_data = array();
$limit = 1000;
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$update_order_status_in_t_apperpdo = "update_order_status_in_t_apperpdo";
$last_updateds = date("Y-m-d H:i:s");
$pno = $pgno ? $pgno : 1;
$start_from = (($pno-1)*$limit);
$DO_approval_array=array();
if($pno!=''){
$sql1 = "SELECT `id`,`APPORDERNO`,`ERPORDERNO`,`STATUS`,DATE_FORMAT(`order_date`,'%Y-%m-%d') as `orddate` FROM $t_apperpdo having `orddate`>='".$prev_date."' order by `order_date` asc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
	$the_id = $row1["id"] ? addslashes(trim($row1["id"])) : "";
	$the_app_ord_no = $row1["APPORDERNO"] ? addslashes(trim($row1["APPORDERNO"])) : "";
	$the_erp_ord_no = $row1["ERPORDERNO"] ? addslashes(trim($row1["ERPORDERNO"])) : "";
	$the_ord_status = $row1["STATUS"] ? strtolower(addslashes(trim($row1["STATUS"]))) : "";
	/*$sqlupd1 = "update $t_dochallan set `prod_code`='$get_product_code' where `id`='$the_id'";
	$resupd1 = mysql_query($sqlupd1);*/
	if($the_app_ord_no!="" && $the_erp_ord_no!=""){
		$sqlck1 = "SELECT `id`,`CHALLANNO` FROM $t_dochallan where `APPORDERNO`='$the_app_ord_no' or `ERPORDERNO`='$the_erp_ord_no' limit 0,1";
		$resck1 = mysql_query($sqlck1);
		$totresck1 = mysql_num_rows($resck1);
		if($totresck1>0){
			$rowck1 = mysql_fetch_assoc($resck1);
			$challanno = $rowck1["CHALLANNO"] ? trim($rowck1["CHALLANNO"]) : "";
			if($challanno!=""){
			$sqlupd1 = "update $t_apperpdo set `STATUS`='Dispatched' where `id`='$the_id' and `STATUS`!='Dispatched'";
			$resupd1 = mysql_query($sqlupd1);
			}else{
				$sqlupd1 = "update $t_apperpdo set `STATUS`='DO approved' where `id`='$the_id' and `STATUS`!='DO approved'";
				$resupd1 = mysql_query($sqlupd1);
				if(!in_array($the_id,$DO_approval_array))
				{
					array_push($DO_approval_array,$the_id);
				}
			}
		}else{
			$sqlupd1 = "update $t_apperpdo set `STATUS`='DO approved' where `id`='$the_id' and `STATUS`!='DO approved'";
			$resupd1 = mysql_query($sqlupd1);
			if(!in_array($the_id,$DO_approval_array))
				{
					array_push($DO_approval_array,$the_id);
				}
		}
	}else if($the_app_ord_no!="" && $the_erp_ord_no==""){
		$sqlck1 = "SELECT `id`,`CHALLANNO`,`ERPORDERNO` FROM $t_dochallan where `APPORDERNO`='$the_app_ord_no'";
		$resck1 = mysql_query($sqlck1);
		$totresck1 = mysql_num_rows($resck1);
		if($totresck1>0){
			$rowck1 = mysql_fetch_assoc($resck1);
			$challanno = $rowck1["CHALLANNO"] ? trim($rowck1["CHALLANNO"]) : "";
			$erporderno = $rowck1["ERPORDERNO"] ? trim($rowck1["ERPORDERNO"]) : "";
			if($erporderno!="" && $challanno!=""){
			$sqlupd1 = "update $t_apperpdo set `STATUS`='Dispatched' where `id`='$the_id' and `STATUS`!='Dispatched'";
			$resupd1 = mysql_query($sqlupd1);
			}else if($erporderno=="" && $challanno!=""){
			$sqlupd1 = "update $t_apperpdo set `STATUS`='Dispatched' where `id`='$the_id' and `STATUS`!='Dispatched'";
			$resupd1 = mysql_query($sqlupd1);
			}else if($erporderno!="" && $challanno==""){
			$sqlupd1 = "update $t_apperpdo set `STATUS`='DO approved' where `id`='$the_id' and `STATUS`!='DO approved'";
			$resupd1 = mysql_query($sqlupd1);
				if(!in_array($the_id,$DO_approval_array))
					{
						array_push($DO_approval_array,$the_id);
					}
			}else{
				/*$sqlupd1 = "update $t_apperpdo set `STATUS`='Order received' where `id`='$the_id' and `STATUS`!='Order received'";
				$resupd1 = mysql_query($sqlupd1);*/
			}
		}else{
			/*$sqlupd1 = "update $t_apperpdo set `STATUS`='Order received' where `id`='$the_id' and `STATUS`!='Order received'";
			$resupd1 = mysql_query($sqlupd1);*/
		}
	}else if($the_app_ord_no=="" && $the_erp_ord_no!=""){
		$sqlck1 = "SELECT `id`,`CHALLANNO` FROM $t_dochallan where `ERPORDERNO`='$the_erp_ord_no'";
		$resck1 = mysql_query($sqlck1);
		$totresck1 = mysql_num_rows($resck1);
		if($totresck1>0){
			$rowck1 = mysql_fetch_assoc($resck1);
			$challanno = $rowck1["CHALLANNO"] ? trim($rowck1["CHALLANNO"]) : "";
			if($challanno!=""){
			$sqlupd1 = "update $t_apperpdo set `STATUS`='Dispatched' where `id`='$the_id' and `STATUS`!='Dispatched'";
			$resupd1 = mysql_query($sqlupd1);
			}else{
				$sqlupd1 = "update $t_apperpdo set `STATUS`='DO approved' where `id`='$the_id' and `STATUS`!='DO approved'";
				$resupd1 = mysql_query($sqlupd1);
				if(!in_array($the_id,$DO_approval_array))
				{
					array_push($DO_approval_array,$the_id);
				}
			}
		}else{
			$sqlupd1 = "update $t_apperpdo set `STATUS`='DO approved' where `id`='$the_id' and `STATUS`!='DO approved'";
			$resupd1 = mysql_query($sqlupd1);
			if(!in_array($the_id,$DO_approval_array))
				{
					array_push($DO_approval_array,$the_id);
				}
		}
	}else{
		
	}
	
/* update order status to "Delivered" code start */
	
if($the_app_ord_no!="" && $the_erp_ord_no!=""){
	$sql_chd = "SELECT `ch_status` FROM $t_dochallan where `APPORDERNO`='$the_app_ord_no' group by `ch_status`";
	$res_chd = mysql_query($sql_chd);
	$totres_chd = mysql_num_rows($res_chd);
	if($totres_chd==1){
		$row_chd = mysql_fetch_assoc($res_chd);
		$the_ch_status = $row_chd["ch_status"];
		if($the_ch_status=="Delivered"){
			$sql_upd_ordsts = "update $t_apperpdo set `STATUS`='Delivered' where `APPORDERNO`='$the_app_ord_no' ";
			$res_upd_ordsts = mysql_query($sql_upd_ordsts);	
		}
	}
}else if($the_app_ord_no!="" && $the_erp_ord_no==""){
	$sql_chd = "SELECT `ch_status` FROM $t_dochallan where `APPORDERNO`='$the_app_ord_no' group by `ch_status`";
	$res_chd = mysql_query($sql_chd);
	$totres_chd = mysql_num_rows($res_chd);
	if($totres_chd==1){
		$row_chd = mysql_fetch_assoc($res_chd);
		$the_ch_status = $row_chd["ch_status"];
		if($the_ch_status=="Delivered"){
			$sql_upd_ordsts = "update $t_apperpdo set `STATUS`='Delivered' where `APPORDERNO`='$the_app_ord_no' ";
			$res_upd_ordsts = mysql_query($sql_upd_ordsts);	
		}
	}
}else if($the_app_ord_no=="" && $the_erp_ord_no!=""){
	$sql_chd = "SELECT `ch_status` FROM $t_dochallan where `ERPORDERNO`='$the_erp_ord_no' group by `ch_status`";
	$res_chd = mysql_query($sql_chd);
	$totres_chd = mysql_num_rows($res_chd);
	if($totres_chd==1){
		$row_chd = mysql_fetch_assoc($res_chd);
		$the_ch_status = $row_chd["ch_status"];
		if($the_ch_status=="Delivered"){
			$sql_upd_ordsts = "update $t_apperpdo set `STATUS`='Delivered' where `ERPORDERNO`='$the_erp_ord_no' ";
			$res_upd_ordsts = mysql_query($sql_upd_ordsts);	
		}
	}
}

/* update order status to "Delivered" code end */	
	
	
	
	}
	//print_r($DO_approval_array);
	//For Email
		/*define("SERVERREMOTE","103.242.119.68");
		define("USERREMOTE","acedns_dnsprod");
		define("PASSWORDREMOTE","dnsprod1234#");
		define("DBREMOTE","acedns_STAR");
		$link=mysql_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die("Database Connection Error.");
		mysql_select_db(DBREMOTE,$link) or die("could not connect the database");
	 foreach($DO_approval_array as $DO_approval_val)
	 {
		  $sqlapporderdetails = "SELECT `id`,`APPORDERNO`,`ERPORDERNO`,`STATUS`,DATE_FORMAT(`order_date`,'%Y-%m-%d') as `orddate`,order_for,
							customer_code,dns_customer_code,prod_code,prod_display_name,QTY,freight,destination_name,phone_no,dump_status,
							dump_name,dealer_truck FROM $t_apperpdo WHERE id='".$DO_approval_val."'";
		  $resapporderdetails = mysql_query($sqlapporderdetails,$conn);
		while($rowapporderdetails=mysql_fetch_array($resapporderdetails)){
			$id=$rowapporderdetails['id'];
			$APPORDERNO=$rowapporderdetails['APPORDERNO'];
			$ERPORDERNO=$rowapporderdetails['ERPORDERNO'];
			$STATUS=$rowapporderdetails['STATUS'];
			$orddate=$rowapporderdetails['orddate'];
			//$curr_date_format = date("jS M, y",strtotime($order_date));
			$order_for=$rowapporderdetails['order_for'];
			$customer_code=$rowapporderdetails['customer_code'];
			$dns_customer_code=$rowapporderdetails['dns_customer_code'];
			$prod_code=$rowapporderdetails['prod_code'];
			$prod_display_name=$rowapporderdetails['prod_display_name'];
			$QTY=$rowapporderdetails['QTY'];
			$freight=$rowapporderdetails['freight'];
			$destination_name=$rowapporderdetails['destination_name'];
			$phone_no=$rowapporderdetails['phone_no'];
			$dump_status=$rowapporderdetails['dump_status'];
			$dump_name=$rowapporderdetails['dump_name'];
			$dealer_truck=$rowapporderdetails['dealer_truck'];
			
			$sqlcustcode="SELECT branch_code,customer_name FROM customer_master WHERE dns_customer_code='".$dns_customer_code."'";
			$rscustcode=mysql_query($sqlcustcode,$link);
			$rowcustcode=mysql_fetch_array($rscustcode);
			//$customer_code = $rowcustcode['customer_code'];
			echo 'dffdfdfdfdf'.$branch_code = $rowcustcode['branch_code'];
			$customer_name=$rowcustcode['customer_name'];
			
			$sqlbranch = "select branch_name from branch_master where branch_code='".$branch_code."'";	
			$resbranch = mysql_query($sqlbranch,$link);
			$rowbranch=mysql_fetch_array($resbranch);
			$branch_name=$rowbranch['branch_name'];
			
			$email_hierarchy='';
			$sqldealeremp="SELECT emp_code FROM customer_route_emp_relation WHERE acedns='Y' AND customer_code='".$customer_code."'";
			$rsdealeremp=mysql_query($sqldealeremp,$link);
			while($rowdealeremp=mysql_fetch_array($rsdealeremp))
			{
				$emp_code_db=$rowdealeremp['emp_code'];
				$employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code_db);
				$sqlemailhierarchy="SELECT email FROM employee_master WHERE emp_code IN (".$employee_upper_hierarchy.") AND UPPER(sale_access)='PRIMARY'";
				$rsemailhierarchy=mysql_query($sqlemailhierarchy,$link);
				while($rowemailhierarchy=mysql_fetch_array($rsemailhierarchy))
				{
					$email_hierarchy=$email_hierarchy.$rowemailhierarchy['email'].',';
				}
			}
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
			$curr_date_format = date("jS M, y",strtotime($order_date));
		//$final_email=substr($ownempmailstring,0,-1).','.substr($reportingmailstring,0,-1).','.'dipankarc@coral.in';
			$final_email=substr($email_hierarchy,0,-1).','.'dipankarc@coral.in'.','.'kaushikshrivastava@starcement.co.in';
		$subject = "DO Approved for branch ".strtoupper($branch_name)." ";
		$message = '<br><b>App Order No: </b> '.$APPORDERNO.'<br>
					<br><b>ERP No: </b> '.$ERPORDERNO.'<br>
					<b>DATE: </b> '.$curr_date_format.'<br>
					<b>Branch Name: </b> '.strtoupper($branch_name).'<br>
					<b>Customer Name: </b> '.$customer_name.'<br>
					<b>Consignee Name: </b> '.$consignee_name.'<br>
					<b>Consignee Address: </b> '.$consignee_address.'<br>
					<b>Freight: </b> '.$freight.'<br>
					<b>Destination: </b> '.$destination_name.'<br>
					<b>Product Name: </b> '.$prod_display_name.'<br>
					<b>qty (MT): </b> '.$qty.'<br>
					<b>Phone No.: </b> '.$phone_no.'<br>
					<b>Dump Status: </b> '.$dump_status.'<br>
					<b>Dump Name: </b> '.$dump_name.'<br>
					<b>Dealer Truck: </b> '.$dealer_truck.'<br>';
			//$send_mail = send_the_mail($final_email,$subject,$message);
	 	}
	 }*/
	//End of mail
$newpgno = ($pno+1);
update_order_status_in_t_apperpdo($newpgno,$prev_date);	
}else{
	process_update_tracking($update_order_status_in_t_apperpdo,"END","Process End");
}
}
return;
}
/*-------------------UPDATE ORDER STATUS IN T_APPERPDO TABLE FUNCTION END-------------------------------------------*/
//send_process_complete_mail();
$res_data = array("process_status"=>"YES","process_message"=>"DONE");
echo json_encode($res_data);
mysql_close();
?>
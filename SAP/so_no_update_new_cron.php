<?php
error_reporting(E_STRICT);
ini_set('memory_limit', '99999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$ledger = "ledger";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";
$product_master = "product_master";
$changepassword = "changepassword";
$cron_update_table = "cron_update_table";
$curr_date_time = date("Y-m-d H:i:s");

$res_data = array();
	
function base64($data){
return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
}
function show_dealer_pn_details_by_cust_id($the_customer_code){
$changepassword = "changepassword";
$pn_data = array("sts"=>"NO","device_type"=>"","registrationid"=>"");
if($the_customer_code!=""){
$sql_cp = "SELECT `device_type`,`registrationid` FROM $changepassword WHERE `customer_code` = '$the_customer_code' ";
$res_cp = mysql_query($sql_cp);
$totres_cp = mysql_num_rows($res_cp);
if($totres_cp>0){
$row_cp=mysql_fetch_assoc($res_cp);
$the_device_type = $row_cp["device_type"] ? trim($row_cp["device_type"]) : "";
$the_registrationid = $row_cp["registrationid"] ? trim($row_cp["registrationid"]) : "";
if($the_device_type!="" && $the_registrationid!=""){
	$pn_data = array("sts"=>"YES","device_type"=>$the_device_type,"registrationid"=>$the_registrationid);
}
}
}
return $pn_data;	
}
function show_dealer_pn_details_by_dealer_id($the_dns_customer_code){
$changepassword = "changepassword";
$pn_data = array("sts"=>"NO","device_type"=>"","registrationid"=>"");
if($the_dns_customer_code!=""){
$sql_cp = "SELECT `device_type`,`registrationid` FROM $changepassword WHERE `dns_customer_code` = '$the_dns_customer_code' ";
$res_cp = mysql_query($sql_cp);
$totres_cp = mysql_num_rows($res_cp);
if($totres_cp>0){
$row_cp=mysql_fetch_assoc($res_cp);
$the_device_type = $row_cp["device_type"] ? trim($row_cp["device_type"]) : "";
$the_registrationid = $row_cp["registrationid"] ? trim($row_cp["registrationid"]) : "";
if($the_device_type!="" && $the_registrationid!=""){
	$pn_data = array("sts"=>"YES","device_type"=>$the_device_type,"registrationid"=>$the_registrationid);
}
}
}
return $pn_data;	
}
function show_prod_name_by_prod_id($the_prod_code){
$product_master = "product_master";
$prod_name = "";
if($the_prod_code!=""){
$sql_cp = "SELECT `prod_desc` FROM $product_master WHERE `prod_code` = '$the_prod_code' ";
$res_cp = mysql_query($sql_cp);
$totres_cp = mysql_num_rows($res_cp);
if($totres_cp>0){
$row_cp=mysql_fetch_assoc($res_cp);
$prod_name = $row_cp["prod_desc"] ? trim($row_cp["prod_desc"]) : "";
}
}
return $prod_name;	
}
function show_prod_details_by_dns_prod_id($the_dns_prod_code){
$product_master = "product_master";
$prod_name = "";
if($the_dns_prod_code!=""){
$sql_cp = "SELECT `prod_desc` FROM $product_master WHERE `dns_prod_code` = '$the_dns_prod_code' ";
$res_cp = mysql_query($sql_cp);
$totres_cp = mysql_num_rows($res_cp);
if($totres_cp>0){
$row_cp=mysql_fetch_assoc($res_cp);
$prod_name = $row_cp["prod_desc"] ? trim($row_cp["prod_desc"]) : "";
}
}
return $prod_name;	
}
function get_customer_name_by_dealer_id($dns_cust_code){
	$customer_master = "customer_master";
	$customernm = "";
	$dns_cust_code = $dns_cust_code ? addslashes(trim($dns_cust_code)) : "";
	if($dns_cust_code!=""){
		$sqlcc = "select `customer_name` from $customer_master where `dns_customer_code`='$dns_cust_code'";
		$rescc = mysql_query($sqlcc);
		$totrescc = mysql_num_rows($rescc);
		if($totrescc>0){
			$rowcc=mysql_fetch_assoc($rescc);
			$customernm = $rowcc["customer_name"] ? trim($rowcc["customer_name"]) : "";
		}
}
	return $customernm;
}

$server_url1 = "https://" . $_SERVER['SERVER_NAME']."/";
$the_m_image_link = $server_url1."SAP/admin/images/logo.png";
$curr_date = date("Y-m-d");
$prev_date_val = date('Y-m-d', strtotime("-2 days,$curr_date"));
$sql1 = "SELECT `id`,`APPORDERNO`,`ERPORDERNO`,`STATUS`,`QTY`,`customer_code`,`dns_customer_code`,`prod_code`,`dns_prod_code`,`freight`,`destination_code`,`destination_name` FROM $t_apperpdo where `STATUS`='DO approved'  AND SUBSTRING(`order_date`,1,10) >='$prev_date_val'  order by `id` asc";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
	$the_id = $row1["id"] ? addslashes(trim($row1["id"])) : "";
	$the_app_ord_no = $row1["APPORDERNO"] ? addslashes(trim($row1["APPORDERNO"])) : "";
	$the_app_ord_status = $row1["STATUS"] ? addslashes(trim($row1["STATUS"])) : "";
	$the_qty = trim($row1["QTY"]);
	$the_customer_code = $row1["customer_code"] ? addslashes(trim($row1["customer_code"])) : "";
	$the_dns_customer_code = $row1["dns_customer_code"] ? addslashes(trim($row1["dns_customer_code"])) : "";
	$customer_name = get_customer_name_by_dealer_id($the_dns_customer_code);

	
	$the_prod_code = $row1["prod_code"] ? addslashes(trim($row1["prod_code"])) : "";
	$the_dns_prod_code = $row1["dns_prod_code"] ? addslashes(trim($row1["dns_prod_code"])) : "";
	$the_freight = $row1["freight"] ? addslashes(trim($row1["freight"])) : "";
	$the_destination_code = $row1["destination_code"] ? trim($row1["destination_code"]) : "";
	$the_destination_name = $row1["destination_name"] ? trim($row1["destination_name"]) : "";
	if($the_app_ord_no!=""){
		$starfiori_port_no = $GLOBALS['starfiori_port_no'];
	/*$url_ck1 = 'https://prdapp1.starcement.co.in:44310/sap/opu/odata/sap/ZSD_PARKLOT_SO_SERV_CDS/ZSD_PARKLOT_SO_SERV(\''.$the_app_ord_no.'\')?$format=json&sap-client=900';*/
	$url_ck1 = 'https://starfiori.starcement.co.in:'.$starfiori_port_no.'/sap/opu/odata/sap/ZSD_PARKLOT_SO_SERV_CDS/ZSD_PARKLOT_SO_SERV(\''.$the_app_ord_no.'\')?$format=json&sap-client=900';
	$body_for_mcode1 = get_data_from_cserver($url_ck1);
	if(isJsonCk($body_for_mcode1)){
	$json_decoded = json_decode($body_for_mcode1,true);
	if(count($json_decoded)>0){
	if(array_key_exists("d",$json_decoded)){
	$app_ref_no = $json_decoded["d"]["app_ref_no"];
	$material = $json_decoded["d"]["material"];
	$so_num = $json_decoded["d"]["so_num"];
	$erdat = $json_decoded["d"]["erdat"];
	$erdat_date_format = "";
	if($erdat!=""){
	$erdat_str = str_replace("/","",$erdat);
	$erdat_str = str_replace("Date","",$erdat_str);	
	$erdat_str = str_replace("(","",$erdat_str);
	$erdat_str = str_replace(")","",$erdat_str);
	$erdat_str = ($erdat_str / 1000);
	//$erdat_date_format = date("Y-m-d H:i:s",$erdat_str);
	$erdat_date_format = date("Y-m-d",$erdat_str);
	}
	$erzet = $json_decoded["d"]["erzet"];
	if($erzet!='')
	{
		$erzet_str = str_replace("PT","",$erzet);
		$erzet_str = str_replace("H","",$erzet_str);
		$erzet_str = str_replace("M","",$erzet_str);
		$erzet_str = str_replace("S","",$erzet_str);
	}
	$erdat_date_format=$erdat_date_format.' '.$erzet_str;
	$erdat_date_format = date("Y-m-d H:i:s",strtotime($erdat_date_format));
	$upd_tmstmp = $json_decoded["d"]["upd_tmstmp"];
	$upd_tmstmp_format = "";
	if($upd_tmstmp!=""){
	$upd_tmstmp_str = str_replace("/","",$upd_tmstmp);
	$upd_tmstmp_str = str_replace("Date","",$upd_tmstmp_str);	
	$upd_tmstmp_str = str_replace("(","",$upd_tmstmp_str);
	$upd_tmstmp_str = str_replace(")","",$upd_tmstmp_str);
	$upd_tmstmp_str = ($upd_tmstmp_str / 1000);
	$upd_tmstmp_format = date("Y-m-d H:i:s",$upd_tmstmp_str);
	}
	$kwmeng = $json_decoded["d"]["kwmeng"];
	$vrkme = $json_decoded["d"]["vrkme"];
	$vstel = $json_decoded["d"]["vstel"];
	$status_val = trim($json_decoded["d"]["status"]);
	$remarks = trim($json_decoded["d"]["remarks"]);
	if($so_num!=""){
		if($remarks==""){
		$upd_sts_qry = ",`STATUS`='DO approved',`sale_order_prod_code`='$material',`sale_order_qty`='$kwmeng',`sale_order_dump`='$vstel'";
		}
		$sql_upd = "update $t_apperpdo set `ERPORDERNO`='$so_num',`ERPORDERDT`='$erdat_date_format' where `APPORDERNO`='$the_app_ord_no' ";
		$res_upd = mysql_query($sql_upd);
		$the_device_type = "";
		$the_registrationid = "";
	   }
	}
	
  }
}
}
}
}
$res_data = array("process_status"=>"YES","process_message"=>"DONE");
echo json_encode($res_data);
mysql_close();
?>
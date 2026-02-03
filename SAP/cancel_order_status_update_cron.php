<?php
error_reporting(E_STRICT);
ini_set('memory_limit', '99999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$customer_master = "customer_master";
$product_master = "product_master";
$changepassword = "changepassword";
$cron_update_table = "cron_update_table";
$curr_date_time = date("Y-m-d H:i:s");
$prev_date = date('Y-m-d',strtotime("-12 days"));
$res_data = array();
$auto_notification_log="auto_notification_log";

function base64($data){
return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
}

$server_url1 = "https://" . $_SERVER['SERVER_NAME']."/";
$the_m_image_link = $server_url1."SAP/admin/images/logo.png";
/*$sql1 = "SELECT `id`,`APPORDERNO`,`ERPORDERNO`,`STATUS`,`QTY`,`customer_code`,`dns_customer_code`,`prod_code`,`dns_prod_code`,`freight`,`destination_code`,`destination_name` FROM $t_apperpdo where `STATUS` in('Order authorized','Order received','CREDIT CHECK FAILED') and `APPORDERNO`!='' AND SUBSTRING(`order_date`,1,10) >='2022-12-01' AND `ERPORDERNO`='' order by `id` asc";*/
$curr_date = date("Y-m-d");
$prev_date_3days = date('Y-m-d',strtotime("-3 days"));
$sql1 = "SELECT `id`,`APPORDERNO`,`ERPORDERNO`,`STATUS`,`QTY`,`customer_code`,`dns_customer_code`,`prod_code`,`dns_prod_code`,`freight`,`destination_code`,`destination_name` FROM $t_apperpdo where `STATUS` in('Order authorized','Order received','CREDIT CHECK FAILED') and `APPORDERNO`!='' AND SUBSTRING(`order_date`,1,10) <='$prev_date_3days'    
order by `id` asc";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	$dataprocessed=0;
		while($row1=mysql_fetch_assoc($res1)){
		$the_id = $row1["id"] ? addslashes(trim($row1["id"])) : "";
		$the_app_ord_no = $row1["APPORDERNO"] ? addslashes(trim($row1["APPORDERNO"])) : "";
		$the_app_ord_status = $row1["STATUS"] ? addslashes(trim($row1["STATUS"])) : "";
		$the_qty = trim($row1["QTY"]);
		$the_customer_code = $row1["customer_code"] ? addslashes(trim($row1["customer_code"])) : "";
		$the_dns_customer_code = $row1["dns_customer_code"] ? addslashes(trim($row1["dns_customer_code"])) : "";
		
			if($the_app_ord_no!=""){
				$upd_sts_qry = " `STATUS`='Order canceled'";
				echo $sql_upd = "update $t_apperpdo set $upd_sts_qry where `APPORDERNO`='$the_app_ord_no' ";
				$res_upd = mysql_query($sql_upd);
			}
			//exit();
			$dataprocessed++;
		}
	}
	echo $dataprocessed;
$res_data = array("process_status"=>"YES","process_message"=>"DONE");
echo json_encode($res_data);
mysql_close();
?>
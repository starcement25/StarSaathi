<?php
ini_set('memory_limit', '9999M');
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
include "star_connection.php";
$notification_message = "notification_message";
$notification_read_table = "notification_read_table";
$curr_date_time  = date("Y-m-d H:i:s");
$pending_count = 0;
/*---reads the customer type and the branch of the logged in user from customer_master---*/
function get_customer_data_by_customer_code($cust_code){
$cust_data = array("cust_type"=>"","branch_code"=>"");
$customer_master = "customer_master";
$cust_code = $cust_code ? addslashes(trim($cust_code)) : "";
if($cust_code!=""){
$sqlut = "select `cust_type`,`branch_code` from $customer_master where `customer_code`='$cust_code' limit 1";
$resut = mysql_query($sqlut);
$totresut = mysql_num_rows($resut);
if($totresut>0){
$rowut = mysql_fetch_assoc($resut);
$cust_data["cust_type"] = $rowut["cust_type"] ? trim($rowut["cust_type"]) : "";
$cust_data["branch_code"] = $rowut["branch_code"] ? trim($rowut["branch_code"]) : "";
}
}
return $cust_data;
}
/*---cust_type is not stored in one fixed format, so map it to DEALER / RSSD---*/
function get_noty_user_type($u_type){
$u_type = $u_type ? strtoupper(trim($u_type)) : "";
$noty_u_type = "";
if($u_type!=""){
if(strpos($u_type,"RSSD")!==false || strpos($u_type,"SUBDEALE")!==false || strpos($u_type,"SUB DEALER")!==false || strpos($u_type,"SUB-DEALER")!==false){
$noty_u_type = "RSSD";
}else if(strpos($u_type,"DEALER")!==false){
$noty_u_type = "DEALER";
}
}
return $noty_u_type;
}
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
if($the_id!=""){
/*---branch code and customer type are taken from customer_master as per the_id---*/
$the_cust_data = get_customer_data_by_customer_code($the_id);
$the_branch_code = $the_cust_data["branch_code"] ? addslashes($the_cust_data["branch_code"]) : "";
$the_noty_user_type = get_noty_user_type($the_cust_data["cust_type"]);

$prev_7_days_datetime = date('Y-m-d H:i:s',strtotime("-7 days"));

if($the_noty_user_type!=""){
	/*---`ALL` notifications are not checked, `DEALER`/`RSSD` ones are matched with the customer type---*/
	$user_type_qry = " and (`user_type` is null or `user_type`='' or `user_type`='ALL' or `user_type`='$the_noty_user_type') ";
}else{
	/*---customer type could not be identified, so count only the notifications meant for everybody---*/
	$user_type_qry = " and (`user_type` is null or `user_type`='' or `user_type`='ALL') ";
}

/*---same eligibility rules as the notification list, minus the ones already marked read by this member---*/
$sqlall = "select count(*) as pending_count from $notification_message nm where (FIND_IN_SET('$the_branch_code',nm.`branch_code`) or nm.`branch_code`='ALL') $user_type_qry and nm.`date_time`>'$prev_7_days_datetime' and not exists (select nrt.`nrt_id` from $notification_read_table nrt where nrt.`noti_id`=nm.`id` and nrt.`member_id`='$the_id')";
$resall = mysql_query($sqlall);
if($resall){
	$rowall = mysql_fetch_assoc($resall);
	$pending_count = $rowall["pending_count"] ? intval($rowall["pending_count"]) : 0;
	$res_data = array("process_status"=>"YES","process_message"=>"Success.","curr_date_time"=>$curr_date_time,"pending_count"=>$pending_count);
}else{
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.","curr_date_time"=>$curr_date_time,"pending_count"=>0);
}
}else{
	$res_data = array("process_status"=>"NO","process_message"=>"Please provide all values.","curr_date_time"=>$curr_date_time,"pending_count"=>0);
}
echo json_encode($res_data);
mysql_close();
?>

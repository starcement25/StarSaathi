<?php
include "star_connection.php";
$server_url1 = "http://" . $_SERVER['SERVER_NAME']."/";
$notification_message = "notification_message";
$notification_read_table = "notification_read_table";
$notification_data = array();
$curr_date_time  = date("Y-m-d H:i:s");
$img_dir = "admin/noty_images/";
$img_url = $server_url1."SAP/admin/noty_images/";
function show_noty_ststus($noti_id,$memb_id){
$sts_val = "UNREAD";
$notification_read_table = "notification_read_table";
$noti_id = $noti_id ? trim($noti_id) : "";
$memb_id = $memb_id ? trim($memb_id) : "";
if($noti_id!="" && $memb_id!=""){
$sqlckn = "select `nrt_id` from $notification_read_table where `noti_id`='$noti_id'  and `member_id`='$memb_id' ";
$resckn = mysql_query($sqlckn);
$totresckn = mysql_num_rows($resckn);
if($totresckn>0){
$sts_val = "READ";	
}
else{
		$datetime = date("Y-m-d H:i:s");
$sql2 = "insert into $notification_read_table (`noti_id`,`member_id`,`datetime`) values ('$noti_id','$memb_id','$datetime') ";
$res2 = mysql_query($sql2);
}
}

return $sts_val;
}
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
/*---branch code and customer type are taken from customer_master as per the_id---*/
$the_cust_data = get_customer_data_by_customer_code($the_id);
$the_branch_code = $the_cust_data["branch_code"] ? addslashes($the_cust_data["branch_code"]) : "";
$the_noty_user_type = get_noty_user_type($the_cust_data["cust_type"]);
//$last_update_datetime = $_REQUEST["last_update_datetime"] ? addslashes(trim($_REQUEST["last_update_datetime"])) : "";
/*if($the_branch_code!=""){*/

$prev_7_days_datetime = date('Y-m-d H:i:s',strtotime("-7 days"));


/*if($last_update_datetime!=""){
	$where_qry = " and `date_time`>'$last_update_datetime' ";
}else{
	$where_qry = "";
}*/

if($the_noty_user_type!=""){
	/*---`ALL` notifications are not checked, `DEALER`/`RSSD` ones are matched with the customer type---*/
	$user_type_qry = " and (`user_type` is null or `user_type`='' or `user_type`='ALL' or `user_type`='$the_noty_user_type') ";
}else{
	/*---customer type could not be identified, so send only the notifications meant for everybody---*/
	$user_type_qry = " and (`user_type` is null or `user_type`='' or `user_type`='ALL') ";
}

 $sqlall = "select * from $notification_message where (FIND_IN_SET('$the_branch_code',`branch_code`) or `branch_code`='ALL') $user_type_qry and `date_time`>'$prev_7_days_datetime' order by `date_time` desc";
$resall = mysql_query($sqlall);
$totall = mysql_num_rows($resall);
if($totall>0){
	while($row11=mysql_fetch_assoc($resall)){
		$nid = $row11["id"];
		$the_noti_sts = show_noty_ststus($nid,$the_id);
		$m_title = $row11["title"];
		$m_message = $row11["message"];
		$m_file_type = $row11["file_type"];
		$m_image_name = $row11["image_name"] ? trim($row11["image_name"]) : "";
		if($m_image_name!=""){
			if(file_exists($img_dir.$m_image_name)){
				$m_image_link = $img_url.$m_image_name;
			}else{
			$m_image_link ="";
		}
		}else{
			$m_image_link ="";
		}
		$n_date_time = $row11["date_time"];
		$notification_data[] = array("nid"=>$nid,"m_title"=>$m_title,"m_message"=>$m_message,"m_file_type"=>$m_file_type,"m_image_link"=>$m_image_link,"n_date_time"=>$n_date_time,"the_noti_sts"=>$the_noti_sts);
	}	

$res_data = array("process_status"=>"YES","process_message"=>"Success.","curr_date_time"=>$curr_date_time,"notification_data"=>$notification_data);
}else{
	$res_data = array("process_status"=>"NO","process_message"=>"No new record found.","curr_date_time"=>$curr_date_time);
}
/*}else{
	$res_data = array("process_status"=>"NO","process_message"=>"All fields are mandatory.","curr_date_time"=>$curr_date_time);
}*/
echo json_encode($res_data);
mysql_close();
?>
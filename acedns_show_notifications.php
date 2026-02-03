<?php
include "star_connection.php";
$server_url1 = "http://" . $_SERVER['SERVER_NAME']."/";
$notification_message = "notification_message";
$notification_read_table = "notification_read_table";
$notification_data = array();
$curr_date_time  = date("Y-m-d H:i:s");
$img_dir = "admin/noty_images/";
$img_url = $server_url1."admin/noty_images/";
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
}

return $sts_val;
}
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
$the_branch_code = $_REQUEST["the_branch_code"] ? addslashes(trim($_REQUEST["the_branch_code"])) : "";
//$last_update_datetime = $_REQUEST["last_update_datetime"] ? addslashes(trim($_REQUEST["last_update_datetime"])) : "";
if($the_branch_code!=""){

$prev_7_days_datetime = date('Y-m-d H:i:s',strtotime("-7 days"));


/*if($last_update_datetime!=""){
	$where_qry = " and `date_time`>'$last_update_datetime' ";
}else{
	$where_qry = "";
}*/

$sqlall = "select * from $notification_message where (FIND_IN_SET('$the_branch_code',`branch_code`) or `branch_code`='ALL') and `date_time`>'$prev_7_days_datetime' order by `date_time` desc";
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
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"All fields are mandatory.","curr_date_time"=>$curr_date_time);
}	
echo json_encode($res_data);
mysql_close();
?>
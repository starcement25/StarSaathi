<?php
include "star_connection.php";
$sp_destination = "sp_destination";
$the_sl_no = $_REQUEST["the_sl_no"] ? addslashes(trim($_REQUEST["the_sl_no"])) : "";
$the_status = $_REQUEST["the_status"] ? addslashes(trim($_REQUEST["the_status"])) : "";
$upd_status = "";
	$upd_status_show = "";
if($the_sl_no!="" && $the_status!=""){
	
if($the_status=="ACTIVE"){
	$upd_status = "N";
	$upd_status_show = "INACTIVE";
}else if($the_status=="INACTIVE"){
	$upd_status = "Y";
	$upd_status_show = "ACTIVE";
}
if($upd_status!=""){
	$download_time = date("Y-m-d H:i:s");
$sql="update $sp_destination set `acedns`='$upd_status',`download_time`='$download_time' where `sl_no`='$the_sl_no'";
$res=mysql_query($sql);
$res_data = array("process_status"=>"YES","process_message"=>"Status updated successfully.","upd_status_show"=>$upd_status_show);
}else{
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.","upd_status_show"=>$upd_status_show);	
	
}
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.","upd_status_show"=>$upd_status_show);
}	
echo json_encode($res_data);
mysql_close();
?>
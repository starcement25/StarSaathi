<?php
include "star_connection.php";
$branch_game_status = "branch_game_status";
$the_brnch_code = $_REQUEST["the_brnch_code"] ? addslashes(trim($_REQUEST["the_brnch_code"])) : "";
$the_status = $_REQUEST["the_status"] ? addslashes(trim($_REQUEST["the_status"])) : "";
if($the_brnch_code!=""){

if($the_status!=""){
	$download_time = date("Y-m-d H:i:s");
$sql_sho = "select `branch_code` from $branch_game_status where `branch_code`='$the_brnch_code'";
$res_sho=mysql_query($sql_sho);
$tot_res_sho = mysql_num_rows($res_sho);
if($tot_res_sho>0){
$sql_upd="update $branch_game_status set `game_status`='$the_status',`download_time`='$download_time' where `branch_code`='$the_brnch_code'";
$res_upd=mysql_query($sql_upd);
}else{
$sql_in="insert into $branch_game_status (`branch_code`,`game_status`,`download_time`) values ('$the_brnch_code','$the_status','$download_time')";
$res_in=mysql_query($sql_in);	
}
$res_data = array("process_status"=>"YES","process_message"=>"Status updated successfully.");
}else{
	$sql_dlt="delete from $branch_game_status where `branch_code`='$the_brnch_code'";
	$res_dlt=mysql_query($sql_dlt);
$res_data = array("process_status"=>"YES","process_message"=>"Data deleted.");
}
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");
}	
echo json_encode($res_data);
mysql_close();
?>
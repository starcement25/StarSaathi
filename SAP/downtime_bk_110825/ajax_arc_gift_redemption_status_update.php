<?php
include "star_connection.php";
$arc_gift_redeem_table = "arc_gift_redeem_table";
$the_ac_id = $_POST["the_ac_id"] ? addslashes(trim($_POST["the_ac_id"])) : "";
$sel_arc_status = $_POST["sel_arc_status"] ? addslashes(trim($_POST["sel_arc_status"])) : "PENDING";
if($the_ac_id!="" && $sel_arc_status!=""){

$sql="update $arc_gift_redeem_table set `status`='$sel_arc_status' where `ac_id`='$the_ac_id'";
$res=mysql_query($sql);
$res_data = array("process_status"=>"YES","process_message"=>"Status updated successfully.");

}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");
}	
echo json_encode($res_data);
mysql_close();
?>
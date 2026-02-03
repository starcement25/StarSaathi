<?php
include "star_connection.php";
$branch_credit_limit_status = "branch_credit_limit_status";
$the_branch_code = $_POST["the_branch_code"] ? addslashes(trim($_POST["the_branch_code"])) : "";
$the_arc_status = $_POST["the_arc_status"] ? addslashes(trim($_POST["the_arc_status"])) : "N";
if($the_arc_status==""){
$the_arc_status = "N";	
}
if($the_branch_code!=""){
$sql="update $branch_credit_limit_status set `arc_status`='$the_arc_status' where `branch_code`='$the_branch_code'";
$res=mysql_query($sql);
$res_data = array("process_status"=>"YES","process_message"=>"Status updated successfully.");
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");
}	
echo json_encode($res_data);
mysql_close();
?>
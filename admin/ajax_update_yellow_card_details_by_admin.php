<?php
include "star_connection.php";
$yellow_card_details = "yellow_card_details";
$res = array();
$the_yellow_card_no = $_POST["the_yellow_card_no"] ? addslashes(trim($_POST["the_yellow_card_no"])) : "";
$the_selected_date = $_POST["the_selected_date"] ? addslashes(trim($_POST["the_selected_date"])) : "";
$the_qty_of_bags = $_POST["the_qty_of_bags"] ? addslashes(trim($_POST["the_qty_of_bags"])) : "";
if($the_yellow_card_no!="" && $the_selected_date!="" && $the_qty_of_bags!=""){
	$yellow_card_no_for_ajx = str_replace("_","/",$the_yellow_card_no);
$sqlupd = "update $yellow_card_details set `challan_date`='$the_selected_date',`qty`='$the_qty_of_bags' where `yellow_card_no`='$yellow_card_no_for_ajx'";
$resupd = mysql_query($sqlupd);
$res = array("process_sts"=>"YES","process_message"=>"Success");
}else{
$res = array("process_sts"=>"NO","process_message"=>"Something went wrong.");	
}
mysql_close();
echo json_encode($res);
?>
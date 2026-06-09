<?php
include "star_connection.php";
$t_main_order_pop = "T_MAIN_ORDER_POP";
$t_order_pop = "T_ORDER_POP";
$useragent = $_SERVER['HTTP_USER_AGENT'];
$res_data = array();
function isJSON_pop($string){
   return is_string($string) && is_array(json_decode($string, true)) ? true : false;
}
$mordpop_id = $_REQUEST["mordpop_id"] ? addslashes(trim($_REQUEST["mordpop_id"])) : "";
if($mordpop_id!=""){

$sql_ord_ck = "select * from $t_main_order_pop where `order_id`='$mordpop_id'";
$res_ord_ck = mysql_query($sql_ord_ck);
$totres_ord_ck = mysql_num_rows($res_ord_ck);
if($totres_ord_ck>0){
$row_ord_ck = mysql_fetch_assoc($res_ord_ck);
$customer_region = $row_ord_ck["customer_region"] ? trim($row_ord_ck["customer_region"]) : "";

$the_url = "";
$the_url_fail_safe = "";
if($customer_region=="NE"){
$the_url = BASE_URL . "ccavenue_pg/check_pop_order_payment_status_ccavenue_ne.php?order_id=".$mordpop_id;
$the_url_fail_safe = BASE_URL . "ccavenue_pg/check_pop_order_payment_status_ccavenue.php?order_id=".$mordpop_id;		
}else if($customer_region=="ROE"){
$the_url = BASE_URL . "ccavenue_pg/check_pop_order_payment_status_ccavenue.php?order_id=".$mordpop_id;	
$the_url_fail_safe = BASE_URL . "ccavenue_pg/check_pop_order_payment_status_ccavenue_ne.php?order_id=".$mordpop_id;
}
//echo $the_url_fail_safe;
if($the_url!=""){

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $the_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
$result = curl_exec($ch);
curl_close($ch);

if($result==""){
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");
}else{

if(isJSON_pop($result)=== FALSE){
$res_data = array("process_status"=>"NO","process_message"=>"Invalid json response from payment server.");	
}else{
$poptdata_arr = json_decode($result,true);
if(array_key_exists("process_status",$poptdata_arr)){
if($poptdata_arr["process_status"]=="YES"){
$the_order_status = addslashes($poptdata_arr["the_order_status"]);
$the_order_card_name = addslashes($poptdata_arr["the_order_card_name"]);
$the_tracking_id = addslashes($poptdata_arr["the_tracking_id"]);
$the_bank_ref_no = addslashes($poptdata_arr["the_bank_ref_no"]);


$sql_mord_upd = "update $t_main_order_pop set `order_status`='$the_order_status',`tracking_id`='$the_tracking_id',`card_name`='$the_order_card_name',`bank_ref_no`='$the_bank_ref_no' where `order_id`='$mordpop_id'";
$res_mord_upd = mysql_query($sql_mord_upd);

$sql_mord_upd2 = "update $t_order_pop set `status`='$the_order_status',`the_tracking_id`='$the_tracking_id' where `the_order_id`='$mordpop_id'";
$res_mord_upd2 = mysql_query($sql_mord_upd2);
	
	
$res_data = array("process_status"=>"YES","process_message"=>"Successfully Updated.","curr_status"=>$the_order_status);


}else{
	
/*---FAIL SAFE---*/

if($the_url_fail_safe!=""){

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $the_url_fail_safe);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
$result = curl_exec($ch);
curl_close($ch);

if($result==""){
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");
}else{
if(isJSON_pop($result)=== FALSE){
$res_data = array("process_status"=>"NO","process_message"=>"Invalid json response from payment server.");	
}else{
	
$poptdata_arr2 = json_decode($result,true);
if(array_key_exists("process_status",$poptdata_arr2)){
if($poptdata_arr2["process_status"]=="YES"){
$the_order_status = addslashes($poptdata_arr2["the_order_status"]);
$the_order_card_name = addslashes($poptdata_arr2["the_order_card_name"]);
$the_tracking_id = addslashes($poptdata_arr2["the_tracking_id"]);
$the_bank_ref_no = addslashes($poptdata_arr2["the_bank_ref_no"]);


$sql_mord_upd = "update $t_main_order_pop set `order_status`='$the_order_status',`tracking_id`='$the_tracking_id',`card_name`='$the_order_card_name',`bank_ref_no`='$the_bank_ref_no' where `order_id`='$mordpop_id'";
$res_mord_upd = mysql_query($sql_mord_upd);

$sql_mord_upd2 = "update $t_order_pop set `status`='$the_order_status',`the_tracking_id`='$the_tracking_id' where `the_order_id`='$mordpop_id'";
$res_mord_upd2 = mysql_query($sql_mord_upd2);
	
	
$res_data = array("process_status"=>"YES","process_message"=>"Successfully Updated.","curr_status"=>$the_order_status);


}else{

if(array_key_exists("process_message",$poptdata_arr2)){
$the_msg = $poptdata_arr2["process_message"];
$res_data = array("process_status"=>"NO","process_message"=>$the_msg);	
}else{
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");	
}
	
}

}else{
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");		
}




}

	
	
}

}else{
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");	
	
}


}
	
}else{
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");		
}


}

}

}else{
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");
}

}else{
$res_data = array("process_status"=>"NO","process_message"=>"No record found.");	
}
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");
}	
echo json_encode($res_data);
mysql_close();
?>
<?php
include "star_connection.php";
$arc_consumer_reg = "arc_consumer_reg";
$arc_dealer_cust_point_table = "arc_dealer_cust_point_table";
$arc_gift_catalogue = "arc_gift_catalogue";
$arc_gift_redeem_table = "arc_gift_redeem_table";
$login_user_id = $_POST["login_user_id"] ? addslashes(trim($_POST["login_user_id"])) : "";
$user_type = $_POST["user_type"] ? addslashes(trim($_POST["user_type"])) : "";
$mobile = $_POST["mobile"] ? addslashes(trim($_POST["mobile"])) : "";
$redeem_content = "";
if($user_type!="" && $login_user_id!=""){
if($user_type=="dealer"){
$customer_data_arr = get_customer_data_check_by_id($login_user_id);
$the_sts = $customer_data_arr["sts"];
$is_branch_arc = $customer_data_arr["is_branch_arc"];
if($the_sts=="YES"){
if($is_branch_arc=="YES"){
$dns_customer_code = $customer_data_arr["dns_customer_code"];

if($mobile==""){
$res_data = array("process_sts"=>"NO","process_msg"=>"Something went wrong.");	
}else if(strlen($mobile)<10){
$res_data = array("process_sts"=>"NO","process_msg"=>"Something went wrong.");	
}else{


$tot_bag_count = 0;
$reccnt = 1;
$consumer_name = ""; 
$eligible_gift_id = ""; 
$eligible_gift = ""; 
$eligible_gift_val = 0;

$sql_mcp = "select * from $arc_dealer_cust_point_table  where `customer_code`='$login_user_id' and `persone_mobile`='$mobile'";
$res_mcp = mysql_query($sql_mcp);
$totres_mcp = mysql_num_rows($res_mcp);
if($totres_mcp>0){
$row_mcp = mysql_fetch_assoc($res_mcp);
$consumer_name = $row_mcp["persone_name"];
$tot_bag_count = $row_mcp["points"] ? trim($row_mcp["points"]) : 0;

$sql_gft = "select * from $arc_gift_catalogue order by `id` asc";
$res_gft = mysql_query($sql_gft);
$totres_gft = mysql_num_rows($res_gft);
if($totres_gft>0){
	while($row_gft = mysql_fetch_assoc($res_gft)){
		$the_gid = $row_gft["id"] ? trim($row_gft["id"]) : "";
		$bag_limit_from = $row_gft["bag_limit_from"] ? trim($row_gft["bag_limit_from"]) : 0;
		$bag_limit_to = $row_gft["bag_limit_to"] ? trim($row_gft["bag_limit_to"]) : 0;
		$gift_name = $row_gft["gift_name"] ? trim($row_gft["gift_name"]) : "";
		if(strtolower($bag_limit_to)=="above" || $bag_limit_to==""){
			if($tot_bag_count>=$bag_limit_from){
				$eligible_gift = $gift_name;
				$eligible_gift_id = $the_gid;
				$eligible_gift_val = $bag_limit_from;
			}
		}else{
			if($tot_bag_count>=$bag_limit_from && $tot_bag_count<=$bag_limit_to){
				$eligible_gift = $gift_name;
				$eligible_gift_id = $the_gid;
				$eligible_gift_val = $bag_limit_from;
			}
		}
		
	}
}
if($eligible_gift_id!=""){
$curr_datetime = date("Y-m-d H:i:s");
$sql_in = "insert into $arc_gift_redeem_table (`name`,`mobile`,`redeemed_bags`,`gift_id`,`gift_name`,`customer_code`,`dns_customer_code`,`entry_datetime`,`last_updated_datetime`) values ('$consumer_name','$mobile','$eligible_gift_val','$eligible_gift_id','$eligible_gift','$login_user_id','$dns_customer_code','$curr_datetime','$curr_datetime')";
$res_in = mysql_query($sql_in);
if($res_in){
$reference_gift_redeem_id = mysql_insert_id();

$available_point = ($tot_bag_count - $eligible_gift_val);
$sql_mcpupd = "update $arc_dealer_cust_point_table set `points`='$available_point'  where `customer_code`='$login_user_id' and `persone_mobile`='$mobile'";
$res_mcpupd = mysql_query($sql_mcpupd);


$res_data = array("process_sts"=>"YES","process_msg"=>"Successfully redeemed.");	
}else{
$res_data = array("process_sts"=>"NO","process_msg"=>"Something went wrong.");	
}
}else{
$res_data = array("process_sts"=>"NO","process_msg"=>"No eligible gift found.");		
}

}else{
$res_data = array("process_sts"=>"NO","process_msg"=>"No Approved bag details found.");		
}	
}
}else{
$res_data = array("process_sts"=>"NO","process_msg"=>"You can't perform this action.");
}		
}else{
$res_data = array("process_sts"=>"NO","process_msg"=>"Your details are missing.");
}
}else{
$res_data = array("process_sts"=>"NO","process_msg"=>"You can't perform this action.");
}
}else{
$res_data = array("process_sts"=>"NO","process_msg"=>"Something went wrong.");	
}	
echo json_encode($res_data);
mysql_close();
?>
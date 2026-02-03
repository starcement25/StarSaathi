<?php
include "star_connection.php";
$arc_consumer_reg = "arc_consumer_reg";
$arc_dealer_cust_point_table = "arc_dealer_cust_point_table";
$arc_gift_catalogue = "arc_gift_catalogue";
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
$res_data = array("process_sts"=>"NO","process_msg"=>"Please enter mobile.","redeem_content"=>$redeem_content);	
}else if(strlen($mobile)<10){
$res_data = array("process_sts"=>"NO","process_msg"=>"Please enter 10 digit mobile number.","redeem_content"=>$redeem_content);	
}else{
$curr_datetime = date("Y-m-d H:i:s");


$tot_bag_count = 0;
$reccnt = 1;
$consumer_name = ""; 
$eligible_gift = "";
$eligible_gift_val = 0;

$sql_mcp = "select * from $arc_dealer_cust_point_table  where `customer_code`='$login_user_id' and `persone_mobile`='$mobile'";
$res_mcp = mysql_query($sql_mcp);
$totres_mcp = mysql_num_rows($res_mcp);
if($totres_mcp>0){
$row_mcp = mysql_fetch_assoc($res_mcp);
$consumer_name = $row_mcp["persone_name"];
$tot_bag_count = $row_mcp["points"] ? trim($row_mcp["points"]) : 0;

$gift_data_array = array();

$sql_gft = "select * from $arc_gift_catalogue order by `id` asc";
$res_gft = mysql_query($sql_gft);
$totres_gft = mysql_num_rows($res_gft);
if($totres_gft>0){
	while($row_gft = mysql_fetch_assoc($res_gft)){
		$gift_id = $row_gft["id"];
		$bag_limit_from = $row_gft["bag_limit_from"] ? trim($row_gft["bag_limit_from"]) : 0;
		$bag_limit_to = $row_gft["bag_limit_to"] ? trim($row_gft["bag_limit_to"]) : 0;
		$gift_name = $row_gft["gift_name"] ? trim($row_gft["gift_name"]) : "";
		if(strtolower($bag_limit_to)=="above" || $bag_limit_to==""){
			if($tot_bag_count>=$bag_limit_from){
				/*$eligible_gift = $gift_name;
				$eligible_gift_val = $bag_limit_from;*/
				$gift_data_array[] = array("the_gift_id"=>$gift_id,"the_gift_name"=>$gift_name,"the_gift_point"=>$bag_limit_from);
			}
		}else{
			
			if($tot_bag_count>=$bag_limit_from){
				$gift_data_array[] = array("the_gift_id"=>$gift_id,"the_gift_name"=>$gift_name,"the_gift_point"=>$bag_limit_from);
			}
			
			/*if($tot_bag_count>=$bag_limit_from && $tot_bag_count<=$bag_limit_to){
				$eligible_gift = $gift_name;
				$eligible_gift_val = $bag_limit_from;
			}*/
		}
		
	}
}

if(count($gift_data_array)>0){

$redeem_content = '<div id="elgbl_gift_content" class="customer_del">
 <p>'.$consumer_name.'</p><hr/>
 <p>Available Bag Quantity: '.$tot_bag_count.'</p><hr/>';

$gcont = 1;
foreach($gift_data_array as $gift_data_array_val){
	$the_gift_id = $gift_data_array_val["the_gift_id"];
	$the_gift_name = $gift_data_array_val["the_gift_name"];
	$the_gift_point = $gift_data_array_val["the_gift_point"];
	if($gcont==1){
		$gftck = ' checked="checked" ';
	}else{
		$gftck = '';
	}
$redeem_content .= '<div class="each_gft_row bd-example">
<div class="form-check">
<input type="radio" class="form-check-input" id="radio_'.$the_gift_id.'" name="gift_idval" value="'.$the_gift_id.'" '.$gftck.'>
<p>Gift Eligible : '.$the_gift_name.'</p>
 <p>Gift Value : '.$the_gift_point.'</p><hr/>
<label class="form-check-label" for="radio_'.$the_gift_id.'"></label>
</div>
</div>';
$gcont++;
}
$redeem_content .= '</div><span style="clear:both;display:block;"></span>';

$redeem_content .= '<a href="javascript:void(0);" class="btn btn-large btn-block btn-success" id="cust_redeem_offer_btn" the_mobile="'.$mobile.'" >REDEEM NOW</a>';


$res_data = array("process_sts"=>"YES","process_msg"=>"Success","redeem_content"=>$redeem_content);	

}else{
$res_data = array("process_sts"=>"NO","process_msg"=>"No eligible gift found.","redeem_content"=>$redeem_content);
}

}else{
$res_data = array("process_sts"=>"NO","process_msg"=>"No Approved bag details found.","redeem_content"=>$redeem_content);
}
	
}
}else{
$res_data = array("process_sts"=>"NO","process_msg"=>"You can't perform this action.","redeem_content"=>$redeem_content);
}		
}else{
$res_data = array("process_sts"=>"NO","process_msg"=>"Your details are missing.","redeem_content"=>$redeem_content);
}
}else{
$res_data = array("process_sts"=>"NO","process_msg"=>"You can't perform this action.","redeem_content"=>$redeem_content);
}
}else{
$res_data = array("process_sts"=>"NO","process_msg"=>"Something went wrong.","redeem_content"=>$redeem_content);	
}	
echo json_encode($res_data);
mysql_close();
?>
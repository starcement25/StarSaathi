<?php
ini_set('memory_limit', '99999M');
set_time_limit(0);
include "star_connection.php";
$arc_consumer_reg = "arc_consumer_reg";
$arc_dealer_cust_point_table = "arc_dealer_cust_point_table";
$arc_gift_catalogue = "arc_gift_catalogue";
$arc_gift_redeem_table = "arc_gift_redeem_table";

function get_gift_id_from_grid($grid){
$arc_gift_redeem_table = "arc_gift_redeem_table";
$gid = "";
$grid = $grid ? trim($grid) : "";
if($grid!=""){
$sql_grt = "select `gift_id` from $arc_gift_redeem_table where `ac_id`='$grid'";
$res_grt = mysql_query($sql_grt);
$totres_grt = mysql_num_rows($res_grt);
if($totres_grt>0){
	$row_grt=mysql_fetch_assoc($res_grt);
	$gid = $row_grt["gift_id"] ? trim($row_grt["gift_id"]) : "";
}
}
return $gid;	
}

$main_dlr_cust_point_arr = array();
$the_gft_arr = array();
$sql_gft = "select * from $arc_gift_catalogue";
$res_gft = mysql_query($sql_gft);
$totres_gft = mysql_num_rows($res_gft);
if($totres_gft>0){
	while($row_gft=mysql_fetch_assoc($res_gft)){
		$the_gft_id = $row_gft["id"];
		$the_gft_point = $row_gft["bag_limit_from"] ? trim($row_gft["bag_limit_from"]) : "0";
		if($the_gft_point==""){
		$the_gft_point = "0";	
		}
		$the_gft_arr[$the_gft_id] = $the_gft_point;
	}
}

$sql1 = "select * from $arc_consumer_reg where `status` not in('PENDING','REJECT') order by `customer_code` asc,`mobile` asc";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$customer_code = $row1["customer_code"] ? trim($row1["customer_code"]) : "";
		$dns_customer_code = $row1["dns_customer_code"] ? trim($row1["dns_customer_code"]) : "";
		
		$name = $row1["name"] ? trim($row1["name"]) : "";
		$mobile = $row1["mobile"] ? trim($row1["mobile"]) : "";
		$no_of_bags = $row1["no_of_bags"] ? trim($row1["no_of_bags"]) : "0";
		if($no_of_bags==""){
		$no_of_bags = "0";	
		}
		$redeemed_bags = $row1["redeemed_bags"] ? trim($row1["redeemed_bags"]) : "0";
		if($redeemed_bags==""){
		$redeemed_bags = "0";	
		}
		$each_status = $row1["status"];
		$reference_gift_redeem_id = $row1["reference_gift_redeem_id"] ? trim($row1["reference_gift_redeem_id"]) : "";
		$the_gift_id = "";
		$gift_point = 0;
		$the_rec_point = 0;
		if($each_status=="APPROVED"){
			$the_rec_point = $no_of_bags;
		}else if($each_status=="REDEEMED"){
			$the_rec_point = $redeemed_bags;
			/*if($reference_gift_redeem_id!=""){
				$the_gift_id = get_gift_id_from_grid($reference_gift_redeem_id);
				if($the_gift_id!=""){
				if(array_key_exists($the_gift_id,$the_gft_arr)){
					$gift_point = $the_gft_arr[$the_gift_id];
				}
				}
			}*/
		}
		if($customer_code!="" && $mobile!=""){
			$arr_gr_ids_arr = array();
		if(array_key_exists($customer_code,$main_dlr_cust_point_arr)){
			
		if(array_key_exists($mobile,$main_dlr_cust_point_arr[$customer_code])){
			$old_total_points = $main_dlr_cust_point_arr[$customer_code][$mobile]["total_points"];
			
			$new_total_points = ($old_total_points + $the_rec_point);
			$main_dlr_cust_point_arr[$customer_code][$mobile]["total_points"] = $new_total_points;
			
			if($reference_gift_redeem_id!=""){
			$arr_gr_ids_arr = $main_dlr_cust_point_arr[$customer_code][$mobile]["arr_gr_ids_arr"];
			$arr_gr_ids_arr[] = $reference_gift_redeem_id;
			$main_dlr_cust_point_arr[$customer_code][$mobile]["arr_gr_ids_arr"] = $arr_gr_ids_arr;
			}
			
		
		}else{
			if($reference_gift_redeem_id!=""){
				$arr_gr_ids_arr[] = $reference_gift_redeem_id;
			}
		$main_dlr_cust_point_arr[$customer_code][$mobile] = array("customer_code"=>$customer_code,"dns_customer_code"=>$dns_customer_code,"name"=>$name,"mobile"=>$mobile,"total_points"=>$the_rec_point,"arr_gr_ids_arr"=>$arr_gr_ids_arr);
		}
			
		}else{
			if($reference_gift_redeem_id!=""){
				$arr_gr_ids_arr[] = $reference_gift_redeem_id;
			}
			$main_dlr_cust_point_arr[$customer_code][$mobile] = array("customer_code"=>$customer_code,"dns_customer_code"=>$dns_customer_code,"name"=>$name,"mobile"=>$mobile,"total_points"=>$the_rec_point,"arr_gr_ids_arr"=>$arr_gr_ids_arr);
		}
		
		}
		
		
	}
}


if(count($main_dlr_cust_point_arr)>0){
	foreach($main_dlr_cust_point_arr as $mdcpa){
		$dealer_arr = $mdcpa;
		if(count($dealer_arr)>0){
		foreach($dealer_arr as $daval){
		//$dealer_arr = $mdcpa;
		
		$tc_customer_code = $daval["customer_code"];
		$tc_dns_customer_code = $daval["dns_customer_code"];
		$tc_name = $daval["name"] ? addslashes(trim($daval["name"])) : "";
		$tc_mobile = $daval["mobile"];
		$tc_total_points = $daval["total_points"] ? trim($daval["total_points"]) : "0";
		$tc_total_used_points = "0";
		$tc_tot_point_available = "0";
		$arr_gr_ids_arr = $daval["arr_gr_ids_arr"];
		if(count($arr_gr_ids_arr)>0){
			$arr_gr_ids_arr = array_unique($arr_gr_ids_arr);
		}
		//$tc_total_used_points = $daval["total_used_points"] ? trim($daval["total_used_points"]) : "0";
		//$tc_tot_point_available = ($tc_total_points - $tc_total_used_points);
		
		echo "customer_code:".$tc_customer_code."<br>";
		echo "dns_customer_code:".$tc_dns_customer_code."<br>";
		echo "name:".$tc_name."<br>";
		echo "mobile:".$tc_mobile."<br>";
		echo "total_points:".$tc_total_points."<br>";
		/*echo "<pre>";
		print_r($arr_gr_ids_arr);
		echo "</pre>";*/
		foreach($arr_gr_ids_arr as $arr_gr_id){
			$the_gift_id = get_gift_id_from_grid($arr_gr_id);
			if($the_gift_id!=""){
			if(array_key_exists($the_gift_id,$the_gft_arr)){
			$gift_point = $the_gft_arr[$the_gift_id];
			$tc_total_used_points = ($tc_total_used_points + $gift_point);
			}
			}
		}
		echo "total_used_points:".$tc_total_used_points."<br>";
		$tc_tot_point_available = ($tc_total_points - $tc_total_used_points);
		echo "tot_point_available:".$tc_tot_point_available."<br><br><br>";
		
		/*if($tc_tot_point_available>0){
		$sql_cdcp = "select `adcpt_id` from $arc_dealer_cust_point_table where `customer_code`='$tc_customer_code' and `persone_mobile`='$tc_mobile'";
		$res_cdcp = mysql_query($sql_cdcp);
		$totres_cdcp = mysql_num_rows($res_cdcp);
		if($totres_cdcp>0){
		$row_cdcp=mysql_fetch_assoc($res_cdcp);
		$adcpt_id = $row_cdcp["adcpt_id"] ? trim($row_cdcp["adcpt_id"]) : "";
		$sql_cdcpup = "update $arc_dealer_cust_point_table set `points`='$tc_tot_point_available',`persone_name`='$tc_name',`dns_customer_code`='$tc_dns_customer_code' where `adcpt_id`='$adcpt_id'";
		$res_cdcpup = mysql_query($sql_cdcpup);
		}else{
		$sql_cdcpin = "insert into $arc_dealer_cust_point_table (`customer_code`,`dns_customer_code`,`persone_name`,`persone_mobile`,`points`) values ('$tc_customer_code','$tc_dns_customer_code','$tc_name','$tc_mobile','$tc_tot_point_available')";
		$res_cdcpin = mysql_query($sql_cdcpin);
		}
		}*/
		
		
		
		
		
		}
		}
		
		
		
	}
	
	
}






mysql_close();
?>
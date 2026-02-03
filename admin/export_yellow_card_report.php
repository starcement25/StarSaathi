<?php
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$employee_master = "employee_master";
$customer_master = "customer_master";
$employee_kyc_master = "employee_kyc_master";
$yellow_card_details = "yellow_card_details";
$branch_master = "branch_master";

function get_customer_name_from_id($cust_id){
$cust_data = array("sts"=>"NO","customer_name"=>"","dns_customer_code"=>"");
$customer_master = "customer_master";
$custname = "";
$cust_id = $cust_id ? addslashes(trim($cust_id)) : "";
if($cust_id!=''){
$sqls = "select `customer_name`,`dns_customer_code` from $customer_master where `customer_code`='$cust_id'";
$ress = mysql_query($sqls);
$totress = mysql_num_rows($ress);
if($totress>0){
$rows = mysql_fetch_assoc($ress);
$customer_name = $rows["customer_name"] ? trim($rows["customer_name"]) : "";
$dns_customer_code = $rows["dns_customer_code"] ? trim($rows["dns_customer_code"]) : "";
$cust_data = array("sts"=>"YES","customer_name"=>$customer_name,"dns_customer_code"=>$dns_customer_code);
}
}
return $cust_data;
}

function show_product_data_from_prod_dns_code($the_prod_dns_code){
$product_dtls = array("prod_code"=>"","prod_desc"=>"");
$product_master = "product_master";
if($the_prod_dns_code!=""){
$sql1 = "select `prod_code`,`prod_desc` from $product_master where `dns_prod_code`='$the_prod_dns_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$prod_code = $row1["prod_code"] ? addslashes(trim($row1["prod_code"])) : "";
		$prod_desc = $row1["prod_desc"] ? addslashes(trim($row1["prod_desc"])) : "";
		$product_dtls = array("prod_code"=>$prod_code,"prod_desc"=>$prod_desc);
	}
}

return $product_dtls;
}

function get_product_short_name_from_prod_dns_code($the_prod_dns_code){
$prod_short_name = "";
$product_master = "product_master";
if($the_prod_dns_code!=""){
$sql1 = "select `prod_code`,`prod_desc` from $product_master where `dns_prod_code`='$the_prod_dns_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$prod_desc = $row1["prod_desc"] ? trim($row1["prod_desc"]) : "";
		if($prod_desc!=""){
			if(strpos($prod_desc,"PSC") !== false){
				$prod_short_name = "PSC";
			}else if(strpos($prod_desc,"PPC") !== false){
				$prod_short_name = "PPC";
			}else if(strpos($prod_desc,"OPC") !== false){
				$prod_short_name = "OPC";
			}else if(strpos($prod_desc,"STAR CEMENT ANTIRUST") !== false){
				$prod_short_name = "ARC";
			}else if(strpos($prod_desc,"STAR ANTI RUST CEMENT") !== false){
				$prod_short_name = "ARC";
			}else if(strpos($prod_desc,"ANTI RUST") !== false){
				$prod_short_name = "ARC";
			}else if(strpos($prod_desc,"ANTIRUST") !== false){
				$prod_short_name = "ARC";
			}
		}
	}
}

return $prod_short_name;
}

function get_branch_name_from_id($brnch_id){
	$branch_master = "branch_master";
	$brnchname = "";
	$brnch_id = $brnch_id ? addslashes(trim($brnch_id)) : "";
	if($brnch_id!=''){
		$sqls = "select `branch_name` from $branch_master where `branch_code`='$brnch_id'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if($totress>0){
			$rows = mysql_fetch_assoc($ress);
			$brnchname = $rows["branch_name"] ? trim($rows["branch_name"]) : "";
		}
	}
	return $brnchname;
}


$trn_branch_id = $_GET["trn_branch_id"] ? addslashes(trim($_GET["trn_branch_id"])) : "";
$sl_day_wise = $_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : "";
$from_dt = $_GET["from_dt"] ? addslashes(trim($_GET["from_dt"])) : "";
$to_dt = $_GET["to_dt"] ? addslashes(trim($_GET["to_dt"])) : "";
$srch_dlr_dtls = $_GET["srch_dlr_dtls"] ? addslashes(trim($_GET["srch_dlr_dtls"])) : "";
startCreatYellowCardCsvfile($trn_branch_id,$sl_day_wise,$from_dt,$to_dt,$srch_dlr_dtls);


function startCreatYellowCardCsvfile($trn_branch_id,$sl_day_wise,$from_dt,$to_dt,$srch_dlr_dtls){
$employee_master = "employee_master";
$customer_master = "customer_master";
$employee_kyc_master = "employee_kyc_master";
$yellow_card_details = "yellow_card_details";
$branch_master = "branch_master";
$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$trn_branch_id = $trn_branch_id;
$sl_day_wise = $sl_day_wise;
$from_dt = $from_dt;
$to_dt = $to_dt;
$srch_dlr_dtls = $srch_dlr_dtls;
$whr_str = "";
$new_qry_string_filtered = "";
$search_array = array("trn_branch_id"=>$trn_branch_id,"daywise"=>array("sl_day_wise"=>$sl_day_wise,"from_dt"=>$from_dt,"to_dt"=>$to_dt),"srch_dlr_dtls"=>$srch_dlr_dtls);
foreach($search_array as $search_array_key=>$search_array_val){
if($search_array_key=="srch_dlr_dtls"){
if($search_array_val!=''){
if(trim($whr_str)!=""){
$aand = " and";
}else{
$aand = "";
}
$whr_str .= "$aand ($customer_master.`customer_code` like '%$search_array_val%' or $customer_master.`dns_customer_code` like '%$search_array_val%' or $customer_master.`customer_name` like '%$search_array_val%'  ) ";
$new_qry_string_filtered .= "&srch_dlr_dtls=".$search_array_val;
}
}else if($search_array_key=="trn_branch_id"){
if($search_array_val!=''){
if(trim($whr_str)!=""){
$aand = " and";
}else{
$aand = "";
}
$whr_str .= "$aand $customer_master.`branch_code`='$search_array_val' ";
}
}else if($search_array_key=="daywise"){
$the_sl_day_wise = $search_array_val["sl_day_wise"];
$the_from_dt = $search_array_val["from_dt"];
$the_to_dt = $search_array_val["to_dt"];
if(trim($whr_str)!=""){
$aand = " and";
}else{
$aand = "";
}
if($the_sl_day_wise=="Date_Range"){
if($the_from_dt!="" && $the_to_dt!=""){
$whr_str .= "$aand $yellow_card_details.`challan_date` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";

}else if($the_from_dt!="" && $the_to_dt==""){
$whr_str .= "$aand $yellow_card_details.`challan_date` >= '".$the_from_dt." ".$frm_hrs."' ";

}else if($the_from_dt=="" && $the_to_dt!=""){
$whr_str .= "$aand $yellow_card_details.`challan_date` <= '".$the_to_dt." ".$to_hrs."' ";
}
}else{
if($the_sl_day_wise=="Today"){

$whr_str .= "$aand $yellow_card_details.`challan_date` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
}else if($the_sl_day_wise=="Yesterday"){

$whr_str .= "$aand $yellow_card_details.`challan_date` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";

}
}
}
}

if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}

$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "yellow_card_list_".$curr_date.".csv";
$qry = "select $yellow_card_details.*,$customer_master.`dns_customer_code`,$customer_master.`customer_name`,$customer_master.`branch_code` from $yellow_card_details left join $customer_master on $yellow_card_details.`linked_dealer_code`=$customer_master.`customer_code` $new_whr_str order by $yellow_card_details.`yellow_card_no` desc";
$sql = mysql_query($qry);
$output .= '"Linked Dealer Code","Linked Dealer Name","Sub Dealer Code","Sub Dealer Name","Branch","Challan Date","Challan No.","PPC","PSC","ARC","OPC","Total(Bags)"';
$output .="\n";

while ($row1 = mysql_fetch_array($sql)) {
$yellow_card_no = $row1["yellow_card_no"];
$emp_code = $row1["linked_dealer_code"];
$dns_emp_code = $row1["dns_customer_code"];
$emp_name = $row1["customer_name"];
$emp_branch_code = trim($row1["branch_code"]);
$emp_branch_name = get_branch_name_from_id($emp_branch_code);

$sub_dealer_code = $row1["customer_code"];
$cust_data = array();
$cust_data = get_customer_name_from_id($sub_dealer_code);
$sub_dealer_name = $cust_data["customer_name"];
$sub_dealer_dns_id = $cust_data["dns_customer_code"];
$selected_date = $row1["challan_date"];
$challan_no = $row1["challan_no"];
$qty_of_bags = $row1["qty"];
$prod_code = $row1["qty_UOM"];
$the_PPC="";
$the_PSC="";
$the_ARC="";
$the_OPC="";
$prod_type = get_product_short_name_from_prod_dns_code($prod_code);
if($prod_type=="PPC"){
$the_PPC = $qty_of_bags;
}else if($prod_type=="PSC"){
$the_PSC = $qty_of_bags;	
}else if($prod_type=="ARC"){
$the_ARC = $qty_of_bags;	
}else if($prod_type=="OPC"){
$the_OPC = $qty_of_bags;	
}
$output .= '"'.$dns_emp_code.'","'.$emp_name.'","'.$sub_dealer_dns_id.'","'.$sub_dealer_name.'","'.$emp_branch_name.'","'.$selected_date.'","'.$challan_no.'","'.$the_PPC.'","'.$the_PSC.'","'.$the_ARC.'","'.$the_OPC.'","'.$qty_of_bags.'"';

$output .="\n";
}

// Download the file

$filename = $the_file_name;
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');    
header('Expires: 0');
echo $output;
exit;
}

mysql_close();
?>
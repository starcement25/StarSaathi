<?php
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$branch_master = "branch_master";
$arc_gift_redeem_table = "arc_gift_redeem_table";
$customer_master = "customer_master";
function get_branch_name_from_id($bid){
	$branch_master = "branch_master";
	$branchname = "";
	$bid = $bid ? addslashes(trim($bid)) : "";
	if($bid!=''){
		$sqls = "select `branch_name` from $branch_master where `branch_code`='$bid'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if($totress>0){
			$rows = mysql_fetch_assoc($ress);
			$branchname = $rows["branch_name"] ? trim($rows["branch_name"]) : "";
		}
	}
	return $branchname;
}

$get_status = $_GET["status"] ? trim($_GET["status"]) : "";
startCreatArcGiftRedeemCsvfile($get_status);


function startCreatArcGiftRedeemCsvfile($get_status){
$branch_master = "branch_master";
$arc_gift_redeem_table = "arc_gift_redeem_table";
$customer_master = "customer_master";
$get_status = $get_status ? trim($get_status) : "";
$curr_date = date("jS_M_Y_h_m_s_A");
if($get_status!=""){
	$the_file_name = "arc_gift_redeem_".$get_status."_".$curr_date.".csv";
	$where_qry = " where $arc_gift_redeem_table.`status`='$get_status' ";
}else{
	$the_file_name = "arc_gift_redeem_".$curr_date.".csv";
	$where_qry = "";
}
$output = '';
$output .= '"Consumer_Name","Consumer_Mobile","Redeemed_Bags","Gift","Status","Dealer_Code","Dealer_Name","Branch","Entry_Date"';
$output .="\n";

$sql = "select $arc_gift_redeem_table.*,$customer_master.`customer_name`,$customer_master.`branch_code` from $arc_gift_redeem_table left join $customer_master on $arc_gift_redeem_table.`customer_code`=$customer_master.`customer_code` $where_qry order by $arc_gift_redeem_table.`entry_datetime` desc";

$res = mysql_query($sql);
$totres = mysql_num_rows($res);
if($totres>0){
while ($row1 = mysql_fetch_assoc($res)) {

$ac_id = $row1["ac_id"];
$consumer_name = $row1["name"];
$consumer_mobile = $row1["mobile"];
$redeemed_bags = $row1["redeemed_bags"];
$each_gift_name = $row1["gift_name"];
$each_status = $row1["status"];
$branch_code = $row1["branch_code"];
$branch_name = get_branch_name_from_id($branch_code);
$ar_customer_code = $row1["customer_code"];
$ar_dns_customer_code = $row1["dns_customer_code"];
$ar_customer_name = $row1["customer_name"];
$entry_datetime = $row1["entry_datetime"];
if($entry_datetime!=""){
$entry_datetime	 = date("jS M'y",strtotime($entry_datetime));
}

$output .= '"'.$consumer_name.'","'.$consumer_mobile.'","'.$redeemed_bags.'","'.$each_gift_name.'","'.$each_status.'","'.$ar_dns_customer_code.'","'.$ar_customer_name.'","'.$branch_name.'","'.$entry_datetime.'"';

$output .="\n";
}
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
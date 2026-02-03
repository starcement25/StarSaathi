<?php
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$fpx_dealer_truck_mapping  = "fpx_dealer_truck_mapping";
$fpx_dealer_list="fpx_dealer_list";
$customer_master  = "customer_master";
$truck_data= array();
$curr_date_time  = date("Y-m-d H:i:s");
$order_item_arr = array();
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$curr_date = date("Y-m-d");
$before_30_day_date = date('Y-m-d',strtotime("-30 days"));
$the_customer_code = $_REQUEST["customer_code"] ? addslashes(trim($_REQUEST["customer_code"])) : "";
/*$page_no = $_REQUEST["page_no"] ? $_REQUEST["page_no"] : 1;
$limit = 10;
$start_from = (($page_no-1)*$limit);*/
if($the_customer_code==""){
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.","truck_data"=>$truck_data);
}else{
$sql_cust = "SELECT `dealer_sap_code` FROM $fpx_dealer_list  where `dealer_sap_code`='".$the_customer_code."'";
//echo $sql_cust;
$res_cust = mysql_query($sql_cust);
$tot_res_cust = mysql_num_rows($res_cust);
if($tot_res_cust>0){
	
	$row_cust=mysql_fetch_array($res_cust);
	$customer_id=$row_cust['dealer_sap_code'];
	
	$sqltrucklist = "select  dealer_name,branch_name,region,truck_no  from $fpx_dealer_truck_mapping where 
	`dealer_sap_code`='$customer_id' ";
	//mysql_query("SET SESSION sql_mode = 'TRADITIONAL'");
	//echo $sqltrucklist;
	$restrucklist = mysql_query($sqltrucklist);
	$tottrucklist = mysql_num_rows($restrucklist);			
	if($tottrucklist>0){
		$available_qty=0;
		while($rowtrucklist=mysql_fetch_assoc($restrucklist)){
		//$dealer_name = $rowtrucklist["dealer_name"] ? trim($rowtrucklist["dealer_name"]) : "";
		//$branch_name = $rowtrucklist["branch_name"] ? trim($rowtrucklist["branch_name"]) : "";	
		//$region = $rowtrucklist["region"] ? trim($rowtrucklist["region"]) : "";		
		$truck_no = $rowtrucklist["truck_no"] ? trim($rowtrucklist["truck_no"]) : "0";
		
			$truck_data[] =array("truck_no"=>$truck_no);	
		}
		$res_data = array("process_status"=>"YES","process_message"=>"Success.","truck_data"=>$truck_data);
	}
	
else{
$res_data = array("process_status"=>"YES","process_message"=>"No truck details found.","truck_data"=>$truck_data);
}
	}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Dealer record not found.","truck_data"=>$truck_data);
}
}
echo json_encode($res_data);
if($conn!=""){
mysql_close($conn);
}
?>
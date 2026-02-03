<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & E_NOTICE & E_DEPRECATED);
ini_set('memory_limit', '99999M');
set_time_limit(0);
include "star_connection.php";
function get_broker_name_from_id($cust_id){
	$broker_master = "broker_master";
	$custname = "";
	$cust_id = $cust_id ? addslashes(trim($cust_id)) : "";
	if($cust_id!=''){
		$sqls = "select `broker_name` from $broker_master where `dns_broker_id`='$cust_id'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if($totress>0){
			$rows = mysql_fetch_assoc($ress);
			$custname = $rows["broker_name"] ? trim($rows["broker_name"]) : "";
		}
	}
	return $custname;
}
function get_customer_name_from_dealer_id($cust_id){
	$customer_master = "customer_master";
	$custname = "";
	$cust_id = $cust_id ? addslashes(trim($cust_id)) : "";
	if($cust_id!=''){
		$sqls = "select `customer_name` from $customer_master where `dns_customer_code`='$cust_id'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if($totress>0){
			$rows = mysql_fetch_assoc($ress);
			$custname = $rows["customer_name"] ? trim($rows["customer_name"]) : "";
		}
	}
	return $custname;
}
function get_customer_name_from_id($cust_id){
	$customer_master = "customer_master";
	$custname = "";
	$cust_id = $cust_id ? addslashes(trim($cust_id)) : "";
	if($cust_id!=''){
		$sqls = "select `customer_name` from $customer_master where `customer_code`='$cust_id'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if($totress>0){
			$rows = mysql_fetch_assoc($ress);
			$custname = $rows["customer_name"] ? trim($rows["customer_name"]) : "";
		}
	}
	return $custname;
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
$order_header = "order_header";
$customer_master = "customer_master";
$employee_master = "employee_master";
$location = "location";
$branch_master = "branch_master";
$destination_master = "destination_master";
$customer_destination = "customer_destination";
$order_show_branch="";
$t_apperpdo = "T_APPERPDO_OFFLINE";
$t_dochallan = "T_DOCHALLAN";
$dnsbcarr = array();
$dnsbcstr ="";
$theactbcarr = array();
$theactbcstr ="";
function get_unbooked_qty($ordered_qtr,$apporder_no,$erporder_no){
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$get_unbooked_qty = "";
	$ordered_qtr = $ordered_qtr ? trim($ordered_qtr) : 0;
	if($ordered_qtr>0){
		if($apporder_no!="" || $erporder_no!=""){
			if($apporder_no!="" && $erporder_no!=""){
			$sql14 = "select sum(`CHALLANQTY`) as `sumchalanqty` from $t_dochallan where (`APPORDERNO`='".addslashes($apporder_no)."' or `ERPORDERNO`='".addslashes($erporder_no)."')";
			}else if($apporder_no!="" && $erporder_no==""){
			$sql14 = "select sum(`CHALLANQTY`) as `sumchalanqty` from $t_dochallan where `APPORDERNO`='".addslashes($apporder_no)."'";
			}else if($apporder_no=="" && $erporder_no!=""){
			$sql14 = "select sum(`CHALLANQTY`) as `sumchalanqty` from $t_dochallan where `ERPORDERNO`='".addslashes($erporder_no)."'";
			}else{
			$sql14 = "";	
			}
if($sql14!=""){
$res14 = mysql_query($sql14);
$totres14 = mysql_num_rows($res14);
if($totres14>0){
$row14=mysql_fetch_assoc($res14);
$sumchalanqty = $row14["sumchalanqty"] ? trim($row14["sumchalanqty"]) : 0;
$get_unbooked_qty = ($ordered_qtr-$sumchalanqty);
} 
}
		
	}
	}
	return $get_unbooked_qty;
}
$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$new_qry_string_filtered = "";
$get_fetch_type = "Dispatched";
$trn_branch_id = $_GET["trn_branch_id"] ? addslashes(trim($_GET["trn_branch_id"])) : "";
$ds_code = $_GET["ds_code"] ? addslashes(trim($_GET["ds_code"])) : "";
$sl_ord_sts = "Dispatched";
$sl_day_wise = $_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : "";
$from_dt = $_GET["from_dt"] ? addslashes(trim($_GET["from_dt"])) : "";
$to_dt = $_GET["to_dt"] ? addslashes(trim($_GET["to_dt"])) : "";
$whr_str = "";
$export_filtered_str = "";
$search_array = array("trn_branch_id"=>$trn_branch_id,"ds_code"=>$ds_code,"sl_ord_sts"=>$sl_ord_sts,"daywise"=>array("sl_day_wise"=>$sl_day_wise,"from_dt"=>$from_dt,"to_dt"=>$to_dt));
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="sl_ord_sts"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
				$whr_str .= "$aand $t_apperpdo.`STATUS`='$search_array_val' ";
			
		}		
	}else if($search_array_key=="trn_branch_id"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $customer_master.`branch_code`='$search_array_val' ";	
			$new_qry_string_filtered .= "&trn_branch_id=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&trn_branch_id=".$search_array_val;
			}else{
				$export_filtered_str .= "&trn_branch_id=".$search_array_val;
			}		
		}		
	}else if($search_array_key=="ds_code"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $t_apperpdo.`destination_code`='$search_array_val' ";	
			$new_qry_string_filtered .= "&ds_code=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&ds_code=".$search_array_val;
			}else{
				$export_filtered_str .= "&ds_code=".$search_array_val;
			}	
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
			$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
			}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
			}
			if($the_from_dt!="" && $the_to_dt!=""){
			   $whr_str .= "$aand $t_apperpdo.`order_date` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
			   $new_qry_string_filtered .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}
			}else if($the_from_dt!="" && $the_to_dt==""){
				$whr_str .= "$aand $t_apperpdo.`order_date` >= '".$the_from_dt." ".$frm_hrs."' ";
				$new_qry_string_filtered .= "&from_dt=".$the_from_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}
			}else if($the_from_dt=="" && $the_to_dt!=""){
				$whr_str .= "$aand $t_apperpdo.`order_date` <= '".$the_to_dt." ".$to_hrs."' ";
				$new_qry_string_filtered .= "&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&to_dt=".$the_to_dt;
				}
			}
		}else{
			if($the_sl_day_wise=="Today"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand $t_apperpdo.`order_date` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
			}else if($the_sl_day_wise=="Yesterday"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand $t_apperpdo.`order_date` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
			
			}
		}
	}
}
if($whr_str!=""){
	$new_whr_str = "and ".$whr_str;
}else{
	$new_whr_str ="";
}
$t_apperpdo = "T_APPERPDO_OFFLINE";
$t_dochallan = "T_DOCHALLAN";
$customer_master = "customer_master";
$employee_master = "employee_master";
$branch_master = "branch_master";
$order_show_branch="";
$dnsbcarr = array();
$dnsbcstr ="";
$theactbcarr = array();
$theactbcstr ="";
if($_SESSION["start_user_type"]=="MANAGER"){
	$order_show_branch = $_SESSION["order_show_branch"] ? trim($_SESSION["order_show_branch"]) : "";
	if($order_show_branch=="NE"){
	$dns_branchcode_master = "north_east_branch";
	}else if($order_show_branch=="NOTNE"){
	$dns_branchcode_master = "not_north_east_branch";
	}
	if($order_show_branch=="NE" || $order_show_branch=="NOTNE"){	
	$sqlbm = "select `branch_code` from $dns_branchcode_master";
	$resbm = mysql_query($sqlbm);
	$totresbm = mysql_num_rows($resbm);
	if($totresbm>0){
		$dnsbcarr = array();
		while($rowbm=mysql_fetch_assoc($resbm)){
			$the_dns_bc = $rowbm["branch_code"] ? trim($rowbm["branch_code"]) : "";
			if($the_dns_bc!=""){
				$dnsbcarr[] = $the_dns_bc;
			}
		}
		if(count($dnsbcarr)>0){
			$dnsbcstr = implode("','",$dnsbcarr);
	$sqlabc = "select `branch_code` from $branch_master where `dns_branch_code` in('".$dnsbcstr."')";
	$resabc = mysql_query($sqlabc);
	$totresabc = mysql_num_rows($resabc);
	if($totresabc>0){
		while($rowabc=mysql_fetch_assoc($resabc)){
			$the_bc = $rowabc["branch_code"] ? trim($rowabc["branch_code"]) : "";
			if($the_bc!=""){
				$theactbcarr[] = $the_bc;
			}
		}
		if(count($theactbcarr)>0){
			$theactbcstr = implode("','",$theactbcarr);
			
		}
		
	}
	
		}
	}
	}else{
		if($order_show_branch!=""){
			if($order_show_branch=="MISNE"){
				$whr_qry = " where `branch_state`='NE' ";
			}else if($order_show_branch=="MISROE"){
				$whr_qry = " where `branch_state` in('BIHAR','WB') ";
			}else if($order_show_branch=="MISALL"){
				$whr_qry = " where `branch_state` in('BIHAR','WB','NE') ";				
			}else{
				$whr_qry = " where `branch_state`='$order_show_branch' ";
			}
			$sqlabc = "select `branch_code` from $branch_master $whr_qry ";
			$resabc = mysql_query($sqlabc);
			$totresabc = mysql_num_rows($resabc);
			if($totresabc>0){
			while($rowabc=mysql_fetch_assoc($resabc)){
			$the_bc = $rowabc["branch_code"] ? trim($rowabc["branch_code"]) : "";
			if($the_bc!=""){
			$theactbcarr[] = $the_bc;
			}
			}
			if(count($theactbcarr)>0){
			$theactbcstr = implode("','",$theactbcarr);
			
			}
			
			}
			
			
		}
	}
}
$get_fetch_type = $get_fetch_type ? strtolower(trim($get_fetch_type)) : "all";
$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "dispatched_orders_with_challan_".$curr_date.".csv";

$output = "";
if($_SESSION["start_user_type"]=="MANAGER" && $order_show_branch!=""){
$qry = "select 		    $t_apperpdo.*,$customer_master.`branch_code`,$customer_master.`customer_name`,$destination_master.dns_destination_code,$destination_master.destination_name from $t_apperpdo left join $customer_master on $t_apperpdo.`dns_customer_code`=$customer_master.`dns_customer_code` left join $customer_destination ON $customer_destination.customer_code=$t_apperpdo.`customer_code` left join $destination_master ON $customer_destination.destination_code=$destination_master.destination_code where $customer_master.`branch_code` in('".$theactbcstr."') $new_whr_str order by $t_apperpdo.`order_date` asc";
}else{

$qry = "select 		    $t_apperpdo.*,$customer_master.`branch_code`,$customer_master.`customer_name`,$destination_master.dns_destination_code,$destination_master.destination_name from $t_apperpdo left join $customer_master on $t_apperpdo.`dns_customer_code`=$customer_master.`dns_customer_code` left join $customer_destination ON $customer_destination.customer_code=$t_apperpdo.`customer_code` left join $destination_master ON $customer_destination.destination_code=$destination_master.destination_code  where 1 $new_whr_str order by $t_apperpdo.`ERPORDERDT` asc ";
}
$sql = mysql_query($qry);
$columns_total = mysql_num_fields($sql);
// Get The Field Name
$slno_cnt = 1;
$output .= '"SL_No","Sale Order No","Sale ORDER DATE","Branch_Name","Cust_Code","Customer_Name","Order_For_Type","Consignee_Name","Consignee_Address","Freight","Destination","Product_Name","qty(MT)","Unbooked_qty(MT)","Phone_No","Dump_Status","Dump_Name","Dealer_Truck","STATUS","Order_From","Sale Order Date","Challan_No","Challan_Date","QTY(MT)","Challan_QTY(MT)","Transporter_Name","Quantity_Checking","No_of_bags_short","Quality_Checking","No_of_damaged_bags","Remarks","Challan_Status","Submitted_On"';
$output .="\n";
// Get Records from the table
while ($row = mysql_fetch_array($sql)) {
$sl_no = $row["id"];
//$apporder_no = $row["APPORDERNO"];
$erporder_no = $row["ERPORDERNO"];
$order_date = $row["ERPORDERDT"];
$customer_branch_code = $row["branch_code"];
$customer_branch_name = get_branch_name_from_id($customer_branch_code);
$dns_customer_code = $row["dns_customer_code"];
$customer_code = $row["customer_code"];
//$customer_name = get_customer_name_from_dealer_id($dns_customer_code);
$customer_name = $row["customer_name"];

$order_consignee_name = $row["consignee_name"] ? trim($row["consignee_name"]) : "";
$order_consignee_code = $row["consignee_code"] ? trim($row["consignee_code"]) : "";	
$sqladdress="SELECT address,cust_type FROM $customer_master WHERE customer_code='$order_consignee_code'";
$rsaddress=mysql_query($sqladdress);	
$rowaddress=mysql_fetch_array($rsaddress);	
	
$order_consignee_address = $rowaddress["address"] ? trim($rowaddress["address"]) : "";	
$consignee_name = $order_consignee_name ? str_replace('"', '""',$order_consignee_name) : "";
$consignee_address = $order_consignee_address ? str_replace('"', '""',$order_consignee_address) : "";
$prod_code = $row["prod_code"];
$prod_display_name = $row["prod_display_name"];
$prod_qty = $row["QTY"];
$freight = $row["freight"];
$destination_name = $row["destination_name"] ? trim($row["destination_name"]) : "";
$destination_name = $destination_name ? str_replace('"', '""',$destination_name) : "";
$prod_code = $row["prod_code"];
$prod_display_name = $row["prod_display_name"];
$prod_display_name = $prod_display_name ? str_replace('"', '""',$prod_display_name) : "";
$prod_qty = $row["QTY"];
$phone_no = $row["phone_no"];
$phone_no = $phone_no ? str_replace('"', '""',$phone_no) : "";
$dump_status = $row["dump_status"];
$dump_name = $row["dump_name"];
$dump_name = $dump_name ? str_replace('"', '""',$dump_name) : "";
$dealer_truck = $row["dealer_truck"];	
$order_from = $row["order_from"] ? trim($row["order_from"]) : "DEFAULT";
$order_by = $row["order_by"] ? trim($row["order_by"]) : "";
if($order_from!=""){
	$broker_name = get_broker_name_from_id($order_by);
}else{
	$broker_name = "";
}
$order_status = $row["STATUS"];
if($order_status=="Dispatched"){
$the_unbooked_qty = get_unbooked_qty($prod_qty,$apporder_no,$erporder_no);	
}else{
$the_unbooked_qty = "";	
}
$authorized_by = $row["authorized_by"] ? str_replace('"', '""',trim($row["authorized_by"])) : "";
$authorization_date = $row["authorization_date"] ? str_replace('"', '""',trim($row["authorization_date"])) : "";
$ref_sub_dealer_order_id = $row["ref_sub_dealer_order_id"] ? trim($row["ref_sub_dealer_order_id"]) : "";
$sub_dealer_code = $row["sub_dealer_code"] ? trim($row["sub_dealer_code"]) : "";

$order_for_type=$rowaddress['cust_type'];
if($order_for_type=='')	$order_for_type='Dealer';


$sql_chalna = "select * from $t_dochallan where `ERPORDERNO`='".addslashes($erporder_no)."'";
$res_chalna = mysql_query($sql_chalna);
$totres_chalna = mysql_num_rows($res_chalna);
if($totres_chalna>0){

	while($row_chalna=mysql_fetch_assoc($res_chalna)){
		//$apporder_no_chtl = $row_chalna["APPORDERNO"];
		$erporder_no_chtl = $row_chalna["ERPORDERNO"];
		$erporder_date_chtl = $row_chalna["ERPORDERDT"];
		$challanno_chtl = $row_chalna["CHALLANNO"];
		$challandt_chtl = $row_chalna["CHALLANDT"];
		$prod_code_chtl= $row_chalna["prod_code"];
		$dns_prod_code_chtl= $row_chalna["dns_prod_code"];
		$prod_display_name_chtl= $row_chalna["prod_display_name"];

		$prod_qty_chtl = $row_chalna["QTY"];
		$challanqty_chtl = $row_chalna["CHALLANQTY"];
		
$is_confirmed_challan_material_received = $row_chalna["is_confirmed_challan_material_received"] ? trim($row_chalna["is_confirmed_challan_material_received"]) : "";
$challan_quantity_checking = $row_chalna["challan_quantity_checking"] ? trim($row_chalna["challan_quantity_checking"]) : "";
$ch_quantity_no_of_bags = $row_chalna["ch_quantity_no_of_bags"] ? trim($row_chalna["ch_quantity_no_of_bags"]) : "";

$challan_quality_checking = $row_chalna["challan_quality_checking"] ? trim($row_chalna["challan_quality_checking"]) : "";
$ch_quality_no_of_damaged_bags = $row_chalna["ch_quality_no_of_damaged_bags"] ? trim($row_chalna["ch_quality_no_of_damaged_bags"]) : "";

$challan_remarks = $row_chalna["challan_remarks"] ? trim($row_chalna["challan_remarks"]) : "";
$transporter_name = $row_chalna["transporter_name"] ? trim($row_chalna["transporter_name"]) : "";

$ch_status = $row_chalna["ch_status"] ? trim($row_chalna["ch_status"]) : "Pending";

$confirmed_challan_material_received_datetime = $row_chalna["confirmed_challan_material_received_datetime"] ? trim($row_chalna["confirmed_challan_material_received_datetime"]) : "";
if($confirmed_challan_material_received_datetime!=""){
$confirmed_challan_material_received_datetime = date("jS M,Y h:i A",strtotime($confirmed_challan_material_received_datetime));	
}

$output .= '"'.$sl_no.'","'.$erporder_no.'","'.$order_date.'","'.$customer_branch_name.'","'.$dns_customer_code.'","'.$customer_name.'","'.$order_for_type.'","'.$consignee_name.'","'.$consignee_address.'","'.$freight.'","'.$destination_name.'","'.$prod_display_name.'","'.$prod_qty.'","'.$the_unbooked_qty.'","'.$phone_no.'","'.$dump_status.'","'.$dump_name.'","'.$dealer_truck.'","'.$order_status.'","'.$order_from.'","'.$erporder_date_chtl.'","'.$challanno_chtl.'","'.$challandt_chtl.'","'.$prod_qty_chtl.'","'.$challanqty_chtl.'","'.$transporter_name.'","'.$challan_quantity_checking.'","'.$ch_quantity_no_of_bags.'","'.$challan_quality_checking.'","'.$ch_quality_no_of_damaged_bags.'","'.$challan_remarks.'","'.$ch_status.'","'.$confirmed_challan_material_received_datetime.'"';
$output .="\n";




	}
}

$slno_cnt++;
}
// Download the file
$filename = $the_file_name;
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');    
header('Expires: 0');
echo $output;
exit;
mysql_close();
?>
<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & E_NOTICE & E_DEPRECATED);
ini_set('memory_limit', '999M');
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
function get_admin_user_name_from_order_id($oid){
	$arr_cncl_data = array("user_name"=>"","remark"=>"");
	$do_order_cancel_by_log = "do_order_cancel_by_log";
	$startreport_admin = "startreport_admin";
	$usrname = "";
	$oid = $oid ? addslashes(trim($oid)) : "";
	if($oid!=''){
		$sqls = "select $startreport_admin.`user_name`,$do_order_cancel_by_log.`c_remark` from $do_order_cancel_by_log left join $startreport_admin on $do_order_cancel_by_log.`cancel_by`=$startreport_admin.`id` where $do_order_cancel_by_log.`order_id`='$oid' limit 0,1";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if($totress>0){
			$rows = mysql_fetch_assoc($ress);
			$usrname = $rows["user_name"] ? trim($rows["user_name"]) : "";
			$c_remark = $rows["c_remark"] ? trim($rows["c_remark"]) : "";
			$arr_cncl_data = array("user_name"=>$usrname,"remark"=>$c_remark);
		}
	}
	return $arr_cncl_data;
}
$do_order_cancel_by_log = "do_order_cancel_by_log";
$startreport_admin = "startreport_admin";
$order_header = "order_header";
$customer_master = "customer_master";
$employee_master = "employee_master";
$location = "location";
$branch_master = "branch_master";
$destination_master = "destination_master";
$order_show_branch="";
$t_apperpdo = "T_APPERPDO";
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
$get_fetch_type = $_GET["get_type"] ? trim($_GET["get_type"]) : "Order canceled";
$trn_branch_id = $_GET["trn_branch_id"] ? addslashes(trim($_GET["trn_branch_id"])) : "";
$ds_code = $_GET["ds_code"] ? addslashes(trim($_GET["ds_code"])) : "";
if($get_fetch_type=="pending"){
	$sl_ord_sts = "Order received";
}else if($get_fetch_type=="Order canceled"){
	$sl_ord_sts = "Order canceled";
}else{
	$sl_ord_sts = "";
}
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
$t_apperpdo = "T_APPERPDO";
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
$the_file_name = "cancelled_orders_".$curr_date.".csv";
$output = "";
if($_SESSION["start_user_type"]=="MANAGER" && $order_show_branch!=""){
$qry = "select $t_apperpdo.*,$customer_master.`branch_code`,$customer_master.`customer_name` from $t_apperpdo left join $customer_master on $t_apperpdo.`dns_customer_code`=$customer_master.`dns_customer_code` where $t_apperpdo.`APPORDERNO`!='' and $customer_master.`branch_code` in('".$theactbcstr."') $new_whr_str order by $t_apperpdo.`order_date` asc";
}else{
$qry = "select $t_apperpdo.*,$customer_master.`branch_code`,$customer_master.`customer_name` from $t_apperpdo left join $customer_master on $t_apperpdo.`dns_customer_code`=$customer_master.`dns_customer_code` where $t_apperpdo.`APPORDERNO`!='' $new_whr_str order by `order_date` asc";
}
$sql = mysql_query($qry);
$columns_total = mysql_num_fields($sql);
// Get The Field Name
$slno_cnt = 1;
$output .= '"CANCELLED_BY","REMARK","SL_No","APPORDERNO","ERPORDERNO","DATE","Branch_Name","Cust_Code","Customer_Name","Order_For_Type","Consignee_Name","Consignee_Address","Freight","Destination","Product_Name","qty(MT)","Unbooked_qty(MT)","Phone_No","Dump_Status","Dump_Name","Dealer_Truck","STATUS","Order_From","Ref_SubDealer_Order","Authorized_By","Authorization_Date"';
$output .="\n";
// Get Records from the table
while ($row = mysql_fetch_array($sql)) {
$sl_no = $row["id"];
$apporder_no = $row["APPORDERNO"];
$erporder_no = $row["ERPORDERNO"];
$order_date = $row["order_date"];
$customer_branch_code = $row["branch_code"];
$customer_branch_name = get_branch_name_from_id($customer_branch_code);
$dns_customer_code = $row["dns_customer_code"];
$customer_code = $row["customer_code"];
//$customer_name = get_customer_name_from_dealer_id($dns_customer_code);
$customer_name = $row["customer_name"];
$consignee_name = "";
$consignee_address = "";
$consignee_address_arr = array();
$order_for_type = $row["order_for_type"] ? trim($row["order_for_type"]) : "";
$order_consignee_name = $row["consignee_name"] ? trim($row["consignee_name"]) : "";
$order_consignee_address = $row["consignee_address"] ? trim($row["consignee_address"]) : "";
$order_for = $row["order_for"] ? trim($row["order_for"]) : "";
if($order_for!=""){
if( strpos($order_for,",") !== false ) {
$ofrarr = array();
$ofrarr = explode(",",$order_for);
if(count($ofrarr)>0){
for($i=0;$i<count($ofrarr);$i++){
if($i==0){
$consignee_name = $ofrarr[$i];
}else{
$consignee_address_arr[] = $ofrarr[$i];
}
}
}
}
}
if(count($consignee_address_arr)>0){
$consignee_address = implode(",",$consignee_address_arr);
unset($consignee_address_arr);
}
if($order_for_type==""){
if($order_for==""){
$consignee_name = $order_consignee_name;
$consignee_address = $order_consignee_address;
}
}else{
$consignee_name = $order_consignee_name;
$consignee_address = $order_consignee_address;
}
$consignee_name = $consignee_name ? str_replace('"', '""',$consignee_name) : "";
$consignee_address = $consignee_address ? str_replace('"', '""',$consignee_address) : "";
$prod_code = $row["prod_code"];
$prod_display_name = $row["prod_display_name"];
$prod_qty = $row["QTY"];
$freight = $row["freight"];
$destination_name = $row["destination_name"] ? trim($row["destination_name"]) : "";
$destination_address = $row["destination_address"] ? trim($row["destination_address"]) : "";
if($destination_name==""){
	$destination_name = $destination_address;
}
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
$arr_cncl_data_val = array();
$arr_cncl_data_val = get_admin_user_name_from_order_id($apporder_no);
$canceled_by_username = $arr_cncl_data_val["user_name"] ? str_replace('"', '""',trim($arr_cncl_data_val["user_name"])) : "";
$canceled_remark = $arr_cncl_data_val["remark"] ? str_replace('"', '""',trim($arr_cncl_data_val["remark"])) : "";
$output .= '"'.$canceled_by_username.'","'.$canceled_remark.'","'.$sl_no.'","'.$apporder_no.'","'.$erporder_no.'","'.$order_date.'","'.$customer_branch_name.'","'.$dns_customer_code.'","'.$customer_name.'","'.$order_for_type.'","'.$consignee_name.'","'.$consignee_address.'","'.$freight.'","'.$destination_name.'","'.$prod_display_name.'","'.$prod_qty.'","'.$the_unbooked_qty.'","'.$phone_no.'","'.$dump_status.'","'.$dump_name.'","'.$dealer_truck.'","'.$order_status.'","'.$order_from.'
'.$broker_name.'","'.$ref_sub_dealer_order_id.'","'.$authorized_by.'","'.$authorization_date.'"';
$output .="\n";
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
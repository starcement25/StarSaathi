<?php
include "web_check.php";
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
$start_user_type = $_SESSION["start_user_type"];
$order_header = "order_header";
$customer_master = "customer_master";
$employee_master = "employee_master";
$location = "location";
$customer_destination="customer_destination";
$branch_master = "branch_master";
$destination_master = "destination_master";
$branch_destination_freight = "branch_destination_freight";
$order_show_branch="";
$t_apperpdo = "T_APPERPDO_OFFLINE";
$t_dochallan = "T_DOCHALLAN";
$do_order_cancel_by_log = "do_order_cancel_by_log";
$SAP_customer_master = "ptblcustomermaster";
$dnsbcarr = array();
$dnsbcstr ="";
$theactbcarr = array();
$theactbcstr ="";
$ord_upd_msg = "";
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
	if($theactbcstr!=""){
	$sqlftcbrnc = "select `branch_code`,`branch_name` from $branch_master where `branch_code` in('".$theactbcstr."') order by `branch_name` asc";
	$res1dftftcbrnc = mysql_query($sqlftcbrnc);
	$totres1dftftcbrnc = mysql_num_rows($res1dftftcbrnc);
	}else{
	$totres1dftftcbrnc = 0;
	}

}else{
	$sqlftcbrnc = "select `branch_code`,`branch_name` from $branch_master order by `branch_name` asc";
	$res1dftftcbrnc = mysql_query($sqlftcbrnc);
	$totres1dftftcbrnc = mysql_num_rows($res1dftftcbrnc);
}
$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$new_qry_string_filtered = "";
$trn_branch_id = $_GET["trn_branch_id"] ? addslashes(trim($_GET["trn_branch_id"])) : "";
$ds_code = $_GET["ds_code"] ? addslashes(trim($_GET["ds_code"])) : "";
$sl_ord_sts = $_GET["sl_ord_sts"] ? addslashes(trim($_GET["sl_ord_sts"])) : "";
$sl_day_wise = $_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : "";
$from_dt = $_GET["from_dt"] ? addslashes(trim($_GET["from_dt"])) : "";
$to_dt = $_GET["to_dt"] ? addslashes(trim($_GET["to_dt"])) : "";
$srch_order_dtls = $_GET["srch_order_dtls"] ? addslashes(trim($_GET["srch_order_dtls"])) : "";
$whr_str = "";
$export_filtered_str = "";
$search_array = array("trn_branch_id"=>$trn_branch_id,"ds_code"=>$ds_code,"sl_ord_sts"=>$sl_ord_sts,"srch_order_dtls"=>$srch_order_dtls,"daywise"=>array("sl_day_wise"=>$sl_day_wise,"from_dt"=>$from_dt,"to_dt"=>$to_dt));
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="sl_ord_sts"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			if($search_array_val=="DO_approved_n_Dispatched"){
				$whr_str .= "$aand ($t_apperpdo.`STATUS`='DO approved' or $t_apperpdo.`STATUS`='Dispatched') ";
			}else{
				$whr_str .= "$aand $t_apperpdo.`STATUS`='$search_array_val' ";
			}
			$new_qry_string_filtered .= "&sl_ord_sts=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_ord_sts=".$search_array_val;
			}else{
				$export_filtered_str .= "&sl_ord_sts=".$search_array_val;
			}

		}
	}
	else if($search_array_key=="srch_order_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand ($t_apperpdo.`ERPORDERNO` like '%$search_array_val%' or $t_apperpdo.`dns_customer_code` like '%$search_array_val%' or $customer_master.`customer_name` like '%$search_array_val%' or $customer_master.`customer_id` like '%$search_array_val%'   or $t_apperpdo.`consignee_name` like '%$search_array_val%' or $t_apperpdo.`destination_name` like '%$search_array_val%') ";
			$new_qry_string_filtered .= "&srch_cust_dtls=".$search_array_val;
		}
	}
	else if($search_array_key=="trn_branch_id"){
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
			$whr_str .= "$aand $destination_master.`destination_code`='$search_array_val' ";
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
			   $whr_str .= "$aand $t_apperpdo.`ERPORDERDT` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
			   $new_qry_string_filtered .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}
			}else if($the_from_dt!="" && $the_to_dt==""){
				$whr_str .= "$aand $t_apperpdo.`ERPORDERDT` >= '".$the_from_dt." ".$frm_hrs."' ";
				$new_qry_string_filtered .= "&from_dt=".$the_from_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}
			}else if($the_from_dt=="" && $the_to_dt!=""){
				$whr_str .= "$aand $t_apperpdo.`ERPORDERDT` <= '".$the_to_dt." ".$to_hrs."' ";
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
				$whr_str .= "$aand $t_apperpdo.`ERPORDERDT` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
			}else if($the_sl_day_wise=="Yesterday"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand $t_apperpdo.`ERPORDERDT` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";

			}
		}
	}
}
if($whr_str!=""){
	$new_whr_str = "and ".$whr_str;
}else{
	$new_whr_str ="";
}
//?sl_branch_code=B0056&sl_ord_sts=Pending&sl_day_wise=Date_Range&from_dt=2018-07-01&to_dt=2018-07-31
//if($trn_branch_id !=""){
	//$sql5dcode = "select $destination_master.`destination_code`,$destination_master.`destination_name` from $branch_destination_freight left join $destination_master on $branch_destination_freight.`destination_code`=$destination_master.`destination_code` where $branch_destination_freight.`branch_code`='$trn_branch_id' order by $destination_master.`destination_name` asc";
	$sql5dcode = "select $destination_master.`destination_code`,$destination_master.`destination_name` from $destination_master order by $destination_master.`destination_name` asc";

	$res5dcode = mysql_query($sql5dcode);
	$tot_res5dcode = mysql_num_rows($res5dcode);
//}
$page_name = "offline_order_list.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/
$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;
/*---------PAGINATION RELATED CODE END----------*/
/*---------PAGINATION RELATED CODE START----------*/
/*if($_SESSION["start_user_type"]=="MANAGER" && $order_show_branch!=""){
//$pgsql = "select $t_apperpdo.*,$customer_master.`branch_code` from $t_apperpdo left join $customer_master on $t_apperpdo.`customer_code`=$customer_master.`customer_code` where $t_apperpdo.`APPORDERNO`!='' and $t_apperpdo.`STATUS`!='Order canceled' and $customer_master.`branch_code` in('".$theactbcstr."') $new_whr_str ";
$pgsql = "select $t_apperpdo.*,$customer_master.`branch_code` from $t_apperpdo left join $customer_master on $t_apperpdo.`customer_code`=$customer_master.`customer_code` where $t_apperpdo.`APPORDERNO`!=''  and $customer_master.`branch_code` in('".$theactbcstr."') $new_whr_str ";

}else{*/
//$pgsql = "select $t_apperpdo.*,$customer_master.`branch_code` from $t_apperpdo left join $customer_master on $t_apperpdo.`customer_code`=$customer_master.`customer_code` where $t_apperpdo.`APPORDERNO`!='' and $t_apperpdo.`STATUS`!='Order canceled' $new_whr_str ";
$pgsql = "select $t_apperpdo.*,$customer_master.`branch_code`,$customer_master.`customer_id`,
	$destination_master.dns_destination_code,$destination_master.destination_name from $t_apperpdo left join $customer_master on $t_apperpdo.`customer_code`=$customer_master.`customer_code`  left join $customer_destination ON $customer_destination.customer_code=$customer_master.`customer_code` left join $destination_master ON $customer_destination.destination_code=$destination_master.destination_code
where 1  $new_whr_str order by $t_apperpdo.`id` desc  ";

//}
$pgres = mysql_query($pgsql);
$total_pgres = mysql_num_rows($pgres);
$start_from = (($page-1)*$limit);
$prev = $page - 1;							//previous page is page - 1
$next = $page + 1;							//next page is page + 1
$lastpage = ceil($total_pgres/$limit);   //lastpage is = total pages / items per page, rounded up.
$lpm1 = $lastpage - 1;
/*---------PAGINATION RELATED CODE START----------*/
include "web_header.php";
?>
<style>
.table-bordered thead tr th{
	padding:7px;
}
.wrapper_scrl{
border: none;
overflow-x: scroll;
overflow-y:hidden;
height: 20px;
}
.wrapper_scrl_div{
height: 20px;
}
.each_mk_cncl_span{
	display:block;
	width:150px;
	margin-bottom: 7px;
margin-top: 7px;
}
</style>
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
            </div>
            <!-- Basic Examples -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                          <h2>Offline Order List (<?php echo $total_pgres;?>) &nbsp;&nbsp;&nbsp;<span class="rpt_loader"></span> &nbsp;&nbsp;<a href="export_offline_order.php?get_type=all<?php echo $export_filtered_str;?>" class="btn bg-red waves-effe">Export&nbsp;all</a> <span class="ord_upd_msg"><?php if($ord_upd_msg!=""){ echo $ord_upd_msg;}?></span>&nbsp;&nbsp; <a href="export_dispatched_offlineorder_with_challan.php?exp=yes<?php echo $export_filtered_str;?>" class="btn bg-red waves-effe">Export&nbsp;dispatched&nbsp;offline order&nbsp;with&nbsp;challan</a>
                          <span class="each_mk_cncl_span" id="sync_msg" style="margin-top:4px;"></span>
</h2>
    <div class="row clearfix">

    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_order_dtls" value="<?php echo $srch_order_dtls;?>" placeholder="Search Order Details">
    </div>
<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
<select name="trn_branch_id" id="trn_branch_id" class="form-control">
<option value="">Select Branch Name</option>
<?php
if($totres1dftftcbrnc>0){
	while($row1dftftcbrnc=mysql_fetch_assoc($res1dftftcbrnc)){
		$branch_code_iddwd = $row1dftftcbrnc["branch_code"];
		$branch_name_iddwd = $row1dftftcbrnc["branch_name"];
?>
<option value="<?php echo $branch_code_iddwd;?>" <?php if($branch_code_iddwd == $trn_branch_id){?> selected="selected" <?php } ?> ><?php echo $branch_name_iddwd;?></option>
<?php
	}
}
?>
</select>
</div>
<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control" id="sl_ord_sts">
<option value="">Select Status</option>
<option value="DO approved" <?php if($sl_ord_sts=="DO approved"){?> selected="selected" <?php } ?>>DO approved</option>
<option value="Dispatched" <?php if($sl_ord_sts=="Dispatched"){?> selected="selected" <?php } ?>>Dispatched</option>
</select>
    </div>
<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
<select name="ds_code" id="ds_code" class="form-control">
<option value="">Select Destination</option>
<?php
if($tot_res5dcode>0){
	while($row1dcode=mysql_fetch_assoc($res5dcode)){
		$destination_code_sdf = $row1dcode["destination_code"];
		$destination_name_sdf = $row1dcode["destination_name"];
?>
<option value="<?php echo $destination_code_sdf;?>" <?php if($destination_code_sdf == $ds_code){?> selected="selected" <?php } ?> ><?php echo $destination_name_sdf;?></option>
<?php
	}
}
?>
</select>
    </div>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 add_top_bottom_padding">
</div>
    </div>
    <div class="row clearfix">


    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control" id="sl_day_wise" >
<option value="">Select Day-Wise</option>
<option value="Today" <?php if($sl_day_wise=="Today"){?> selected="selected" <?php } ?>>Today</option>
<option value="Yesterday" <?php if($sl_day_wise=="Yesterday"){?> selected="selected" <?php } ?>>Yesterday</option>
<option value="Date_Range" <?php if($sl_day_wise=="Date_Range"){?> selected="selected" <?php } ?>>Date Range</option>
</select>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
    <input type="text" class="datepicker form-control" id="from_dt" <?php if($sl_day_wise!="Date_Range"){?> style="display:none;" <?php } ?> value="<?php echo $from_dt;?>" placeholder="Choose from date">
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
    <input type="text" class="datepicker form-control" id="to_dt" <?php if($sl_day_wise!="Date_Range"){?> style="display:none;" <?php } ?> value="<?php echo $to_dt;?>" placeholder="Choose to date">
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_btn" >Search</button>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_reset_btn" >Reset</button>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
    </div>
    </div>
                            <span style="clear:both;display:block;"></span>
                        </div>
                        <div class="body">
<?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>
<div class="wrapper_scrl">
    <div class="wrapper_scrl_div"></div>
</div>
                            <div class="table-responsive  tr_for_scroll">
                                <table class="table table-bordered table-striped table-hover table_for_scroll">
                                    <thead>
                                        <tr>
                                            <th>SL&nbsp;No</th>
                                            <th>Sale&nbsp;Order&nbsp;No</th>
                                            <th>DATE</th>
                                            <!--th>Make&nbsp;Order&nbsp;Cancel</th-->
                                            <th>Branch&nbsp;Name</th>
                                            <th>Cust&nbsp;Code</th>
                                            <th>SAP&nbsp;Code</th>
                                            <th>Customer&nbsp;Name</th>
                                            <th>Consignee&nbsp;Code</th>
                                            <th>Consignee&nbsp;Name</th>
                                            <!--th>Consignee&nbsp;Address</th-->
                                            <th>Freight</th>
                                            <th>Destination Code</th>
                                            <th>Destination</th>
                                            <th>Product&nbsp;Name</th>
                                            <th>qty&nbsp;(MT)</th>
                                            <th>Dump&nbsp;Name</th>
                                            <th>Status</th>
											<th>Show&nbsp;Challan&nbsp;Details</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                      <tr>
                                          <th>SL&nbsp;No</th>
                                            <th>Sale&nbsp;Order&nbsp;No</th>
                                            <th>DATE</th>
                                            <!--th>Make&nbsp;Order&nbsp;Cancel</th-->
                                            <th>Branch&nbsp;Name</th>
                                            <th>Cust&nbsp;Code</th>
                                            <th>SAP&nbsp;Code</th>
                                            <th>Customer&nbsp;Name</th>
                                            <th>Consignee&nbsp;Code</th>
                                            <th>Consignee&nbsp;Name</th>
                                            <!--th>Consignee&nbsp;Address</th-->
                                            <th>Freight</th>
                                            <th>Destination Code</th>
                                            <th>Destination</th>
                                            <th>Product&nbsp;Name</th>
                                            <th>qty&nbsp;(MT)</th>
                                            <th>Dump&nbsp;Name</th>
                                            <th>Status</th>
										  <th>Show&nbsp;Challan&nbsp;Details</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
/*if($_SESSION["start_user_type"]=="MANAGER" && $order_show_branch!=""){
	$sql1 = "select $t_apperpdo.*,$customer_master.`branch_code`,$customer_master.`customer_id` from $t_apperpdo left join $customer_master on $t_apperpdo.`customer_code`=$customer_master.`customer_code`
where $t_apperpdo.`APPORDERNO`!=''  and $customer_master.`branch_code` in('".$theactbcstr."')
$new_whr_str order by $t_apperpdo.`id` desc limit $start_from,$limit";
/*$sql1 = "select $t_apperpdo.*,$customer_master.`branch_code`,$customer_master.`customer_id` from $t_apperpdo left join $customer_master on $t_apperpdo.`customer_code`=$customer_master.`customer_code`
where $t_apperpdo.`APPORDERNO`!='' and $t_apperpdo.`STATUS`!='Order canceled' and $customer_master.`branch_code` in('".$theactbcstr."')
$new_whr_str order by $t_apperpdo.`id` desc limit $start_from,$limit";*/
/*}else{
/*$sql1 = "select $t_apperpdo.*,$customer_master.`branch_code`,$customer_master.`customer_id` from $t_apperpdo left join $customer_master on $t_apperpdo.`customer_code`=$customer_master.`customer_code`
where $t_apperpdo.`APPORDERNO`!='' and $t_apperpdo.`STATUS`!='Order canceled' $new_whr_str order by $t_apperpdo.`id` desc limit $start_from,$limit";*/

$sql1 = "select $t_apperpdo.*,$customer_master.`branch_code`,$customer_master.`customer_id`,
	$destination_master.dns_destination_code,$destination_master.destination_name from $t_apperpdo left join $customer_master on $t_apperpdo.`customer_code`=$customer_master.`customer_code`  left join $customer_destination ON $customer_destination.customer_code=$customer_master.`customer_code` left join $destination_master ON $customer_destination.destination_code=$destination_master.destination_code
where 1  $new_whr_str order by $t_apperpdo.`ERPORDERDT` desc limit $start_from,$limit";

//}
//echo"<pre>";print_r($sql1);die;
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
$the_sl_no = 1;
$the_sl_no = (($limit*($page-1))+1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$sl_no = $row1["id"];

		$erporder_no = $row1["ERPORDERNO"];
		$erporder_date = $row1["ERPORDERDT"];
		$order_status = $row1["STATUS"];
		$freight = $row1["freight"];
		$the_destination_code = $row1["dns_destination_code"];
		$destination_name = $row1["destination_name"] ? trim($row1["destination_name"]) : "";
		$destination_address = $row1["destination_address"] ? trim($row1["destination_address"]) : "";
		if($destination_name==""){
			$destination_name = $destination_address;
		}
		$consignee_name = "";
		$consignee_address = "";
		$consignee_address_arr = array();
		$consignee_name = $row1["consignee_name"] ? trim($row1["consignee_name"]) : "";
		$the_consignee_code = $row1["consignee_SAP_code"] ? trim($row1["consignee_SAP_code"]) : "";
		$order_consignee_address = $row1["consignee_address"] ? trim($row1["consignee_address"]) : "";

		$prod_code = $row1["prod_code"];
		$prod_display_name = $row1["prod_display_name"];
		$prod_qty = $row1["QTY"];

		$dns_customer_code = $row1["dns_customer_code"];
		$customer_code = $row1["customer_code"];
		$customer_name = get_customer_name_from_dealer_id($dns_customer_code);
		$customer_branch_code = $row1["branch_code"];
		$customer_branch_name = get_branch_name_from_id($customer_branch_code);

		//$phone_no = $row1["phone_no"];
		//$dump_status = $row1["dump_status"];
		$dump_name = $row1["dump_name"];


$the_SAP_code=$row1["customer_id"] ? trim($row1["customer_id"]) : "";
		/*if($order_for_type =="Self" && $dns_sub_dealer_code!=''){
			$consignee_code_internal=$dns_sub_dealer_code;
		}
		else if($order_for_type =="Self" && $dns_sub_dealer_code=='')
		{
			$consignee_code_internal=$dns_customer_code;
		}
		else
		{
			$consignee_code_internal=$dns_sub_dealer_code;
		}

		$sqlconsigneedet = "select LZONE,KUNNR from $SAP_customer_master where KUNNR=(select customer_id from $customer_master where `dns_customer_code`='$consignee_code_internal') ORDER BY AEDAT ASC,ADDITIONAL_DATA1 ASC";
		$resconsigneedet = mysql_query($sqlconsigneedet);
		$totconsigneedet = mysql_num_rows($resconsigneedet);
			if($totconsigneedet>0){
				$rowconsigneedet= mysql_fetch_assoc($resconsigneedet);
				$the_destination_code = $rowconsigneedet["LZONE"] ? addslashes(trim($rowconsigneedet["LZONE"])) : "";
				$the_consignee_code = $rowconsigneedet["KUNNR"] ? addslashes(trim($rowconsigneedet["KUNNR"])) : "";
			}
		$sqlordertype = "select cust_type from $customer_master where customer_code='".$sub_dealer_code."'";
		$resordertype = mysql_query($sqlordertype);
		$rowordertype= mysql_fetch_assoc($resordertype);
		$order_for_type=$rowordertype['cust_type'];
		if($order_for_type=='')	$order_for_type='Dealer';*/
?>
<tr id="each_ord_tr_<?php echo $sl_no;?>">
<td><?php echo $sl_no;?></td>
<td><?php echo $erporder_no;?></td>
<td><?php echo $erporder_date;?></td>
<!--td style="padding:2px;">
<?php
/*if($start_user_type=="ADMIN"){
if($order_status=="Order authorized" || $order_status=="Order received"){ ?>
<div>
<span class="each_mk_cncl_span">
<textarea class="form-control" rows="2" id="cncl_remark_<?php echo $sl_no;?>" placeholder="Enter remark here"></textarea>
</span>
<span class="each_mk_cncl_span">
<a href="javascript:void(0);" class="btn bg-red ordr_cncl" the_unc_id="<?php echo $sl_no;?>" ordr_cncl_id="<?php echo $apporder_no;?>">Cancel&nbsp;Order</a>
</span>
<span class="each_mk_cncl_span" id="ordr_cncl_msg_<?php echo $sl_no;?>" style="margin-top:4px;">
</span>
</div>
<?php
}
}*/ ?>
</td-->
<td><?php echo $customer_branch_name;?></td>
<td><?php echo $dns_customer_code;?></td>
<td><?php echo $the_SAP_code;?>
<td><?php echo $customer_name;?></td>
<td><?php echo $the_consignee_code;?>
<td><?php echo $consignee_name;?></td>
<!--td><?php //echo $consignee_address;?></td-->
<td><?php echo $freight;?></td>
<td><?php echo $the_destination_code;?></td>
<td><?php echo $destination_name;?></td>
<td><?php echo $prod_display_name;?></td>
<td><?php echo $prod_qty;?></td>
<td><?php echo $dump_name;?></td>

<td><?php echo $order_status;?></td>
	<td>
<?php if($order_status=="Dispatched"){ ?>
	<a href="ajax_show_challan_details_by_app_erp_id.php?apporder_no=&erporder_no=<?php echo $erporder_no;?>" class="btn bg-red vuChnlDtlsLink">Show&nbsp;Challan</a>
<?php } ?></td>
</tr>
<?php
$the_sl_no++;
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="26">No data found.</td>
</tr>
<?php
}
?>
</tbody>
</table>
                            </div>
<?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Basic Examples -->
            <!-- Exportable Table -->

            <!-- #END# Exportable Table -->
        </div>
</section>
<script type="text/javascript">
jQuery(function(){
jQuery(".vuChnlDtlsLink").colorbox({iframe:true,width:"90%",height:"80%",closeButton: true,scrolling: true});
var tr_for_scroll = jQuery(".tr_for_scroll").width();
var table_for_scroll = jQuery(".table_for_scroll").width();
jQuery(".wrapper_scrl").css("width",tr_for_scroll+"px");
jQuery(".wrapper_scrl_div").css("width",table_for_scroll+"px");
jQuery(".wrapper_scrl").scroll(function(){
jQuery(".tr_for_scroll")
.scrollLeft(jQuery(".wrapper_scrl").scrollLeft());
});
jQuery(".tr_for_scroll").scroll(function(){
jQuery(".wrapper_scrl")
.scrollLeft(jQuery(".tr_for_scroll").scrollLeft());
});

	var imgs = '<img src="images/ajax-loader.gif"/>';
	var done_img = '<img src="images/success_tick.png"/>';

	jQuery('#from_dt').bootstrapMaterialDatePicker({ weekStart : 0, time: false });
	jQuery('#to_dt').bootstrapMaterialDatePicker({ weekStart : 0, time: false });
setTimeout(function(){
	jQuery(".ord_upd_msg").html("");
},8000);
/*jQuery('#trn_branch_id').change(function(){
		var trn_branch_id = jQuery(this).val();
		if(trn_branch_id!=''){
		var img = '<img src="images/ajax-loader.gif">';
				jQuery(".rpt_loader").html(img);
				jQuery.ajax({
				url: 'ajax_show_destination_by_branch_id.php',
				type: 'post',
				dataType: 'json',
				data: "trn_branch_id="+trn_branch_id,
				success: function(response){
				if(response.process_sts=="YES"){
					jQuery("#ds_code").html(response.destination_options);
					jQuery(".rpt_loader").html("");
				}else{
					jQuery("#ds_code").html('<option value="">Select Destination</option>');
					jQuery(".rpt_loader").html(response.process_msg);
					setTimeout(function(){
					jQuery(".rpt_loader").html("");
					},3000);
				}
				}
				});
		}else{
			jQuery("#ds_code").html('<option value="">Select Destination</option>');

		}

	});*/
	jQuery(".pcsts").change(function(){
		var stsval = jQuery(this).val();
		var curr_ordid = jQuery(this).attr("sltoid");
		if(stsval!="" && curr_ordid!=""){
			var theldrid = "os_ldr_"+curr_ordid;
			var for_loader = jQuery("#"+theldrid);
			for_loader.html(imgs);
jQuery.ajax({
url: 'ajax_update_ord_sts_by_ord_id.php',
type: 'post',
dataType: "JSON",
data: "curr_ordid="+curr_ordid+"&stsval="+stsval,
success: function(response){
	if(response.process_status=="YES"){
	for_loader.html(done_img);
	setTimeout(function (){
			for_loader.html("");
		},3000);
	}else{
		for_loader.html("");
		alert(response.process_message);
	}
}
});

		}
	});


	jQuery("#sl_day_wise").change(function(){
		var sl_day_wise = jQuery(this).val();
		if(sl_day_wise==""){
			jQuery('#from_dt').hide();
			jQuery('#to_dt').hide();
			jQuery('#from_dt').val("");
			jQuery('#to_dt').val("");
		}else{
			if(sl_day_wise=="Date_Range"){
				jQuery('#from_dt').show();
				jQuery('#to_dt').show();
				jQuery('#from_dt').val("");
				jQuery('#to_dt').val("");
			}else{
				jQuery('#from_dt').hide();
				jQuery('#to_dt').hide();
				jQuery('#from_dt').val("");
				jQuery('#to_dt').val("");
			}
		}
	});

	jQuery(".srch_btn").click(function(){
		var trn_branch_id = jQuery("#trn_branch_id").val();
		var srch_order_dtls = jQuery("#srch_order_dtls").val();
		var ds_code = jQuery("#ds_code").val();
		var sl_ord_sts = jQuery("#sl_ord_sts").val();
		var sl_day_wise = jQuery("#sl_day_wise").val();
		var from_dt = jQuery("#from_dt").val();
		var to_dt = jQuery("#to_dt").val();
		var qstring ="";
		var dtstring ="";
		var amp = "";
		if(trn_branch_id!="" || sl_ord_sts!="" || sl_day_wise!="" || srch_order_dtls!="" || ds_code!=""){


		if(trn_branch_id!=""){
		if(qstring==""){
		qstring = qstring+"trn_branch_id="+trn_branch_id;
		}else{
		qstring = qstring+"&trn_branch_id="+trn_branch_id;
		}
		}

		if(ds_code!=""){
		if(qstring==""){
		qstring = qstring+"ds_code="+ds_code;
		}else{
		qstring = qstring+"&ds_code="+ds_code;
		}
		}

		if(srch_order_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_order_dtls="+srch_order_dtls;
			}else{
				qstring = qstring+"srch_order_dtls="+srch_order_dtls;
			}
		}
		if(sl_ord_sts!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_ord_sts="+sl_ord_sts;
			}else{
				qstring = qstring+"sl_ord_sts="+sl_ord_sts;
			}
		}

		if(sl_day_wise!=""){
			if(sl_day_wise=="Date_Range"){
				if(from_dt!="" && to_dt!=""){
					dtstring ="&from_dt="+from_dt+"&to_dt="+to_dt;
				}else if(from_dt!="" && to_dt==""){
					dtstring ="&from_dt="+from_dt;
				}else if(from_dt=="" && to_dt!=""){
					dtstring ="&to_dt="+to_dt;
				}else{
					dtstring ="";
				}
			}else{
				dtstring ="";
			}

			if(qstring!=""){
				qstring = qstring+"&sl_day_wise="+sl_day_wise+dtstring;
			}else{
				qstring = qstring+"sl_day_wise="+sl_day_wise+dtstring;
			}
		}
		  if(qstring!=""){
			 qstring = "?"+qstring;
		  }
		window.location = "<?php echo $page_name;?>"+qstring;
		}else{
			alert("Please select atleast one field to search.");
		}
	});

	jQuery(".srch_reset_btn").click(function(){
		window.location = "<?php echo $page_name;?>";
	});
jQuery(document).on('click', '.ordr_cncl', function(event){
var ordr_cncl_id = jQuery(this).attr("ordr_cncl_id");
var the_unc_id = jQuery(this).attr("the_unc_id");
if(ordr_cncl_id!='' && the_unc_id!=''){
	var each_ord_tr_elmnt = jQuery("#each_ord_tr_"+the_unc_id);
	var ordr_cncl_msg_elmnt = jQuery("#ordr_cncl_msg_"+the_unc_id);
	var cncl_remark_elmnt = jQuery("#cncl_remark_"+the_unc_id);
	var cncl_remark = jQuery.trim(cncl_remark_elmnt.val());
if(cncl_remark==""){
	alert("Please enter remark.");
	cncl_remark_elmnt.focus();
	return false;
}else{
var cbr = confirm("Do you want to cancel this order?");
if (cbr == true) {
ordr_cncl_msg_elmnt.html(imgs);
jQuery.ajax({
url: 'ajax_make_cancel_order.php',
type: 'post',
dataType: 'json',
data: "ordr_cncl_id="+ordr_cncl_id+"&cncl_remark="+encodeURIComponent(cncl_remark),
success: function(response){
if(response.process_sts=="YES"){
ordr_cncl_msg_elmnt.html(done_img);
setTimeout(function(){
ordr_cncl_msg_elmnt.html("");
each_ord_tr_elmnt.fadeOut(360).delay(400).remove();
},2000);
}else{
ordr_cncl_msg_elmnt.html("");
alert(response.process_msg);
}
}
});
return false;
}
}
}
});
jQuery("#sync_sap").click(function(){
		var sync_msg_elmnt = jQuery("#sync_msg");
		sync_msg_elmnt.html(imgs);
		jQuery.ajax({
		url: 'http://starsaathi.com/SAP/order_status_update_new_cron.php',
		type: 'post',
		dataType: 'json',
		data: '',
		success: function(response){
		if(response.process_sts=="YES"){
		sync_msg_elmnt.html(done_img);
		window.location = "order_list.php";
		setTimeout(function(){
		sync_msg_elmnt.html("");
		},2000);
		}else{
		sync_msg_elmnt.html("");
		alert(response.process_msg);
		}
		}
		});
	});


});
</script>
<?php
include "web_footer.php";
mysql_close();
?>

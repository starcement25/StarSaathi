<?php
include "web_check.php";
include "star_connection.php";
$start_user_type = $_SESSION["start_user_type"];
$allocation_details = "allocation_details";
$branch_master = "branch_master";
$customer_master="customer_master";
$T_DOCHALLAN="T_DOCHALLAN";
$_session['textValues'] = array();
$export_filtered_str = "";
$sl_branch = $_GET["sl_branch"] ? addslashes(trim($_GET["sl_branch"])) : "";
$srch_linked_dealer = $_GET["srch_linked_dealer"] ? addslashes(trim($_GET["srch_linked_dealer"])) : "";
$srch_sub_dealer = $_GET["srch_sub_dealer"] ? addslashes(trim($_GET["srch_sub_dealer"])) : "";
$month = $_GET["month"] ? addslashes(trim($_GET["month"])) : "";

$eash_year_month_arr = array("01"=>"January","02"=>"February","03"=>"March","04"=>"April","05"=>"May","06"=>"June","07"=>"July","08"=>"August","09"=>"September","10"=>"October","11"=>"November","12"=>"December");
$the_year_month_sl_arr = array();
$from_sel_year = 2023;
$to_sel_year = date("Y");
for($i=$from_sel_year;$i<=$to_sel_year;$i++){

foreach($eash_year_month_arr as $eyma_key=>$eash_year_month_arr_val){
$the_sl_ym_key = $i."-".$eyma_key;
$the_sl_ym_val_text = $eash_year_month_arr_val." ".$i;
$the_year_month_sl_arr[$the_sl_ym_key] = $the_sl_ym_val_text;
	
}
	
}


$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d', strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$whr_str = "";
$search_array = array("sl_branch" => $sl_branch, "srch_linked_dealer" => $srch_linked_dealer, "srch_sub_dealer" => $srch_sub_dealer, "month" => $month);
foreach ($search_array as $search_array_key => $search_array_val) {
	if ($search_array_key == "sl_branch") {
		if ($search_array_val != '') {
			/*if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}*/
			$aand = " and";
			$whr_str .= "$aand $customer_master.branch_code='$search_array_val'";
			$export_filtered_str .= "&sl_branch=" . $search_array_val;
		}
	}
	if ($search_array_key == "srch_linked_dealer") {
		if ($search_array_val != '') {
			/*if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}*/
			$aand = " and";
			$whr_str .= "$aand ($customer_master.customer_name like '%$search_array_val%' OR $customer_master.customer_id like '%$search_array_val%') ";
			$export_filtered_str .= "&srch_linked_dealer=" . $search_array_val;
		}
	}
	if ($search_array_key == "srch_sub_dealer") {
		if ($search_array_val != '') {
			/*if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}*/
			$aand = " and";
			$whr_str .= "$aand ($allocation_details.sub_dealer_id like '%$search_array_val%') ";
			$export_filtered_str .= "&srch_sub_dealer=" . $search_array_val;
		}
	}
	if ($search_array_key == "month") {
		if ($search_array_val != '') {
			$search_array_formatted = '01-' . $search_array_val;
			$monthNumber = date('n', strtotime($search_array_formatted));
			/*if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}*/
			$aand = " and";
			$whr_str .= "$aand DATE_FORMAT($allocation_details.`date_and_time`, '%Y-%m')='$search_array_val' ";
			$export_filtered_str .= "&month=" . $search_array_val;
		}
	}
}
$totres1 = 0;
$page_name = "allocation_details_report.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/
$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;
/*---------PAGINATION RELATED CODE END----------*/
/*---------PAGINATION RELATED CODE START----------*/
$name_login = trim($_SESSION["start_report_admin_name"]);
// get the data 'order_show_branch' for this name_login from 'startreport_admin'
$sql = "select * from `startreport_admin` where `user_name`='$name_login' limit 1";
$res = mysql_query($sql);
// get the data 'order_show_branch' from 'startreport_admin'
if ($res) {
	$row = mysql_fetch_assoc($res);
	$order_show_branch = $row["order_show_branch"] ? trim($row["order_show_branch"]) : "";
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

// get the list of 'customer_code' where 'region' is 'order_show_branch' from 'customer_master'
/*echo $sql = "select `customer_code` from `customer_master` where `region`='$order_show_branch'";
$res = mysql_query($sql);
$totres = mysql_num_rows($res);
if ($totres > 0) {
	$customer_code_arr = array();
	while ($row = mysql_fetch_assoc($res)) {
		$customer_code_arr[] = $row["customer_code"];
	}
}
// get all from $lifting where 'linked_dealer_cust_code' is in $customer_code_arr
$customer_code_arr_str = implode("','", $customer_code_arr);
$whr_str = "where `linked_dealer_cust_code` in ('$customer_code_arr_str')";*/
if ($whr_str != "") {
	$new_whr_str = " " . $whr_str;
} else {
	$new_whr_str = "";
}
// Get the numbers of filtered rows from $lifting
/*if ($order_show_branch == '') {
	$sql_pg = "select * from $lifting Where 1 $new_whr_str order by `lid` asc";
} else {
	$whr_str_region = "AND `linked_dealer_cust_code` in (SELECT customer_code FROM customer_master WHERE region='$order_show_branch')";
	$new_whr_str .= " ".$whr_str_region;
	$sql_pg = "select * from $lifting Where 1 $new_whr_str order by `lid` asc";
}*/
/*$sql_pg = "select $allocation_details.*,SUM($allocation_details.allocation_qty) AS total_allocation_qty,$customer_master.dns_customer_code,$customer_master.customer_name,$branch_master.branch_name from $allocation_details,$customer_master,$branch_master Where $allocation_details.customer_id=$customer_master.customer_id AND $customer_master.branch_code=$branch_master.branch_code $new_whr_str GROUP BY $allocation_details.customer_id,$allocation_details.sub_dealer_id,$allocation_details.dns_prod_code,substring($allocation_details.dispatch_date,6,2) order by $allocation_details.`allocation_id` asc";*/
if($_SESSION["start_user_type"]=="MANAGER" && $order_show_branch!=""){
	$sql_pg = "select $allocation_details.*,$allocation_details.allocation_qty AS total_allocation_qty,$customer_master.dns_customer_code,$customer_master.customer_name,$branch_master.branch_name from $allocation_details,$customer_master,$branch_master Where $allocation_details.customer_id=$customer_master.customer_id AND $customer_master.branch_code=$branch_master.branch_code  and $customer_master.`branch_code` in('".$theactbcstr."') $new_whr_str  order by $allocation_details.`customer_id` asc,$allocation_details.`dns_prod_code` asc";
}
else{
$sql_pg = "select $allocation_details.*,$allocation_details.allocation_qty AS total_allocation_qty,$customer_master.dns_customer_code,$customer_master.customer_name,$branch_master.branch_name from $allocation_details,$customer_master,$branch_master Where $allocation_details.customer_id=$customer_master.customer_id AND $customer_master.branch_code=$branch_master.branch_code $new_whr_str  order by $allocation_details.`customer_id` asc,$allocation_details.`dns_prod_code` asc";
}
mysql_query("SET SESSION sql_mode = 'TRADITIONAL'");
$res_pg = mysql_query($sql_pg);
$totres_pg = mysql_num_rows($res_pg);
$total_pgres = $totres_pg;
$start_from = (($page - 1) * $limit);
$prev = $page - 1;                            //previous page is page - 1
$next = $page + 1;                            //next page is page + 1
$lastpage = ceil($total_pgres / $limit);   //lastpage is = total pages / items per page, rounded up.
$lpm1 = $lastpage - 1;
// get the data from $lifting
/*if ($order_show_branch == '') {
	$sql1 = "select * from $lifting WHERE 1 $new_whr_str order by `lid` desc limit $start_from,$limit";
} else {
	$whr_str_region = "AND `linked_dealer_cust_code` in (SELECT customer_code FROM customer_master WHERE region='$order_show_branch')";
	$new_whr_str .= " ".$whr_str_region;
	$sql1 = "select * from $lifting WHERE 1 $new_whr_str order by `lid` desc limit $start_from,$limit";
}*/
if($_SESSION["start_user_type"]=="MANAGER" && $order_show_branch!=""){
	$sql1 = "select $allocation_details.*,$allocation_details.allocation_qty AS total_allocation_qty,$customer_master.dns_customer_code,$customer_master.customer_name,$branch_master.branch_name from $allocation_details,$customer_master,$branch_master Where $allocation_details.customer_id=$customer_master.customer_id AND $customer_master.branch_code=$branch_master.branch_code and $customer_master.`branch_code` in('".$theactbcstr."')  $new_whr_str  order by $allocation_details.`customer_id` asc,$allocation_details.`APPORDERNO` asc,$allocation_details.`dns_prod_code` asc,$allocation_details.`dispatch_date` asc limit $start_from,$limit";
}
else{
$sql1 = "select $allocation_details.*,$allocation_details.allocation_qty AS total_allocation_qty,$customer_master.dns_customer_code,$customer_master.customer_name,$branch_master.branch_name from $allocation_details,$customer_master,$branch_master Where $allocation_details.customer_id=$customer_master.customer_id AND $customer_master.branch_code=$branch_master.branch_code $new_whr_str  order by $allocation_details.`customer_id` asc,$allocation_details.`APPORDERNO` asc,$allocation_details.`dns_prod_code` asc,$allocation_details.`dispatch_date` asc limit $start_from,$limit";
}

$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
/*---------PAGINATION RELATED CODE START----------*/
include "web_header.php";
?>
<script type="text/javascript">
	jQuery(function() {
	});
</script>
<section class="content">
	<div class="container-fluid">
		<div class="block-header">
		</div>
		<!-- Basic Examples -->
		<div class="row clearfix">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="card">
					<div class="header">
						<h2>Allocation Report (<?php echo $total_pgres; ?>)&nbsp;&nbsp;
							<a href="export_allocation_report.php?get_type=all<?php echo $export_filtered_str; ?>" class="btn bg-red waves-effe">Export&nbsp;Allocation&nbsp;Report</a> &nbsp;
						</h2>
						<br><br>
						<span style="clear:both;display:block;"></span>
					</div>
					<div class="body">
						<div class="card">
							<div class="row">
								<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
									<select class="form-control" id="sl_branch">
										<option value="" selected>Select Branch</option>
										<?php
										$sql3 = "select `branch_code`,`branch_name` from $branch_master where `acedns`='Y' order by `branch_name`";
										$res3 = mysql_query($sql3);
										$totres3 = mysql_num_rows($res3);
										if ($totres3 > 0) {
											while ($row3 = mysql_fetch_assoc($res3)) {
												$the_branch_code = $row3["branch_code"];
												$the_branch_name = $row3["branch_name"];
										?>
												<option value="<?php echo $the_branch_code; ?>" <?php if ($sl_branch == $the_branch_code) { ?> selected="selected" <?php } ?>><?php echo $the_branch_name; ?></option>
										<?php
											}
										}
										?>
									</select>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
									<input type="text" class="form-control" id="srch_linked_dealer" style="width:100%;" value="<?php echo $srch_linked_dealer; ?>" placeholder="Search Linked Dealer Name" title="Search Linked Dealer Name">
								</div>
								<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
									<input type="text" class="form-control" id="srch_sub_dealer" value="<?php echo $srch_sub_dealer; ?>" placeholder="Search Sub Dealer / RSSD Name" title="Search Sub Dealer / RSSD Name">
								</div>
								<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
									<?php
									/*$months = array(
										'January', 'February', 'March', 'April', 'May', 'June',
										'July', 'August', 'September', 'October', 'November', 'December'
									);*/ ?>
									<select name="month" id="month" class="form-control">
										<option value="">Select Month</option>
										<?php
										// Loop through the array to generate options
										foreach ($the_year_month_sl_arr as $tymsa_key=>$tymsa_val) { ?>
											<option value="<?php echo $tymsa_key; ?>" <?php if ($tymsa_key == $month) { ?> selected="selected" <?php } ?>><?php echo $tymsa_val; ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
									<button type="button" class="btn bg-red waves-effect srch_btn">Search</button>
								</div>
								<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
									<button type="button" class="btn bg-red waves-effect srch_reset_btn">Reset</button>
								</div>
							</div>
						</div>
					</div>
					<?php
					echo olcPaging($adjacents, $targetpage, $limit, $page, $prev, $next, $lastpage, $lpm1, "paged", $export_filtered_str);
					?>
					<span style="display:block; clear:both;"></span>
					<div class="table-responsive">
						<table class="table table-bordered table-striped table-hover" id="data_table">
							<thead>
								<tr>
									<th>Allocation&nbsp;Date&nbsp;Time</th>
									<th>APPORDERNO</th>
									<th>Dispatch&nbsp;Date</th>
									<th>Linked&nbsp;Dealer&nbsp;Code</th>
									<th>Linked&nbsp;Dealer&nbsp;SAP&nbsp;Code</th>
									<th>Linked&nbsp;Dealer&nbsp;Name</th>
									<th>Sub&nbsp;Dealer&nbsp;/&nbsp;RSSD&nbsp;Code</th>
									<th>Sub&nbsp;Dealer&nbsp;/&nbsp;RSSD&nbsp;SAP&nbsp;Code</th>
									<th>Sub&nbsp;Dealer&nbsp;/&nbsp;RSSD&nbsp;Name</th>
									<th>Branch</th>
									<th>Month</th>
									<th>Product&nbsp;Name</th>
									<th>Total Dispatch qty</th>
									<th>Allocated qty.</th>
									<th>Remaining Allocation qty.</th>
									<th>Challan&nbsp;No.</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th>Allocation&nbsp;Date&nbsp;Time</th>
									<th>APPORDERNO</th>
									<th>Dispatch&nbsp;Date</th>
									<th>Linked&nbsp;Dealer&nbsp;Code</th>
									<th>Linked&nbsp;Dealer&nbsp;SAP&nbsp;Code</th>
									<th>Linked&nbsp;Dealer&nbsp;Name</th>
									<th>Sub&nbsp;Dealer&nbsp;/&nbsp;RSSD&nbsp;Code</th>
									<th>Sub&nbsp;Dealer&nbsp;/&nbsp;RSSD&nbsp;SAP&nbsp;Code</th>
									<th>Sub&nbsp;Dealer&nbsp;/&nbsp;RSSD&nbsp;Name</th>
									<th>Branch</th>
									<th>Month</th>
									<th>Product&nbsp;Name</th>
									<th>Total Dispatch qty</th>
									<th>Allocated qty.</th>
									<th>Remaining Allocation qty.</th>
									<th>Challan&nbsp;No.</th>
								</tr>
							</tfoot>
							<tbody>
								<?php
								if ($totres1 > 0) {
									while ($row1 = mysql_fetch_assoc($res1)) {
										$APPORDERNO = $row1["APPORDERNO"] ? trim($row1["APPORDERNO"]) : "";
										$date_and_time = $row1["date_and_time"] ? trim($row1["date_and_time"]) : "";
										$linked_dealer_code = $row1["dns_customer_code"] ? trim($row1["dns_customer_code"]) : "";
										$linked_dealer_sap_code = $row1["customer_id"] ? trim($row1["customer_id"]) : "";
										$linked_dealer_name = $row1["customer_name"] ? trim($row1["customer_name"]) : "";
										$sub_dealer_rssd_sap_code = $row1["sub_dealer_id"] ? trim($row1["sub_dealer_id"]) : "";
										$sub_dealer_details="select dns_customer_code,customer_name FROM $customer_master where customer_id='$sub_dealer_rssd_sap_code'";
										$res_sub_dealer_details=mysql_query($sub_dealer_details);
										$row_sub_dealer_details=mysql_fetch_array($res_sub_dealer_details);
										$sub_dealer_rssd_code = $row_sub_dealer_details["dns_customer_code"]? trim($row_sub_dealer_details["dns_customer_code"]) : "";
										$sub_dealer_rssd_name = $row_sub_dealer_details["customer_name"] ? trim($row_sub_dealer_details["customer_name"]) : "";
										$branch = $row1["branch_name"] ? trim($row1["branch_name"]) : "";
										$dns_prod_code = $row1["dns_prod_code"] ? trim($row1["dns_prod_code"]) : "";
										$prod_display_name = $row1["prod_desc"] ? trim($row1["prod_desc"]) : "";
						
								$monthval=substr($row1["date_and_time"],5,2);		
								/*$sqldespatchqty="select SUM(CHALLANQTY) as total_despatch_qty from $T_DOCHALLAN where `dns_customer_code`='$linked_dealer_code'  AND dns_prod_code='$dns_prod_code' AND substring(CHALLANDT,6,2)='$monthval'";
								$resdespatchqty=mysql_query($sqldespatchqty);
								$rowdespatchqty=mysql_fetch_array($resdespatchqty);
								$total_despatch_qty = $rowdespatchqty["total_despatch_qty"] ? trim($rowdespatchqty["total_despatch_qty"]) : "";*/
								$total_despatch_qty = $row1["dispatch_qty"] ? trim($row1["dispatch_qty"]) : "";	
								$dispatch_date = $row1["dispatch_date"] ? trim($row1["dispatch_date"]) : "";			
								${'total_allocation_qty'.$linked_dealer_code.$APPORDERNO.$dns_prod_code.$dispatch_date} =${'total_allocation_qty'.$linked_dealer_code.$APPORDERNO.$dns_prod_code.$dispatch_date}+ $row1["total_allocation_qty"];
								$challan_no = $row1["challan_no"] ? trim($row1["challan_no"]) : "";
										// $month = $row1["month"] ? trim($row1["month"]) : "";
										$month = "";
										if ($row1["date_and_time"] != "") {
											$month = date("M-y", strtotime($row1["date_and_time"]));
										}
										$remaining_allocation_qty=($total_despatch_qty-${'total_allocation_qty'.$linked_dealer_code.$APPORDERNO.$dns_prod_code.$dispatch_date} );
								?>
										<tr>
											<td><?php echo $date_and_time;?></td>
											<td><?php echo $APPORDERNO;?></td>
											<td><?php echo $dispatch_date;?></td>
											<td><?php echo $linked_dealer_code; ?></td>
											<td><?php echo $linked_dealer_sap_code; ?></td>
											<td><?php echo $linked_dealer_name; ?></td>
											<td><?php echo $sub_dealer_rssd_code; ?></td>
											<td><?php echo $sub_dealer_rssd_sap_code; ?></td>
											<td><?php echo $sub_dealer_rssd_name; ?></td>
											<td><?php echo $branch; ?></td>
											<td><?php echo $month; ?></td>
											<td><?php echo $prod_display_name; ?></td>
											<td><?php echo $total_despatch_qty; ?></td>
											<td><?php echo $row1["total_allocation_qty"] ; ?></td>
											<td><?php echo $remaining_allocation_qty; ?></td>
											<td><?php echo $challan_no; ?></td>
											
										</tr>
									<?php
									}
								} else {
									?>
									<tr>
										<td style="text-align:center" colspan="19">No data found.</td>
									</tr>
								<?php
								}
								?>
							</tbody>
						</table>
					</div>
					<?php
					echo olcPaging($adjacents, $targetpage, $limit, $page, $prev, $next, $lastpage, $lpm1, "paged", $export_filtered_str);
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
	jQuery(function() {
		jQuery('#branch').chosen({
			no_results_text: 'Oops, no branch found!',
			search_contains: true,
			placeholder_text_single: 'Select Branches'
		});
		var imgs = '<img src="images/ajax-loader.gif"/>';
		var done_img = '<img src="images/success_tick.png"/>';
		jQuery(".srch_btn").click(function() {
			var sl_branch = jQuery("#sl_branch").val();
			var srch_linked_dealer = jQuery("#srch_linked_dealer").val();
			var srch_sub_dealer = jQuery("#srch_sub_dealer").val();
			var month = jQuery("#month").val();
			var qstring = "";
			var dtstring = "";
			var amp = "";
			if (sl_branch != "" || srch_linked_dealer != "" || srch_sub_dealer != "" || month != "") {
				if (sl_branch != "") {
					if (qstring != "") {
						qstring = qstring + "&sl_branch=" + encodeURIComponent(sl_branch);
					} else {
						qstring = qstring + "sl_branch=" + encodeURIComponent(sl_branch);
					}
				}
				if (srch_linked_dealer != "") {
					if (qstring != "") {
						qstring = qstring + "&srch_linked_dealer=" + encodeURIComponent(srch_linked_dealer);
					} else {
						qstring = qstring + "srch_linked_dealer=" + encodeURIComponent(srch_linked_dealer);
					}
				}
				if (srch_sub_dealer != "") {
					if (qstring != "") {
						qstring = qstring + "&srch_sub_dealer=" + encodeURIComponent(srch_sub_dealer);
					} else {
						qstring = qstring + "srch_sub_dealer=" + encodeURIComponent(srch_sub_dealer);
					}
				}
				if (month != "") {
					if (qstring != "") {
						qstring = qstring + "&month=" + encodeURIComponent(month);
					} else {
						qstring = qstring + "month=" + encodeURIComponent(month);
					}
				}
				if (qstring != "") {
					qstring = "?" + qstring;
				}
				window.location = "<?php echo $page_name; ?>" + qstring;
			} else {
				alert("Please select atleast one field to search.");
			}
		});
		jQuery(".srch_reset_btn").click(function() {
			window.location = "<?php echo $page_name; ?>";
		});
	});
</script>
<?php
include "web_footer.php";
mysql_close();
?>
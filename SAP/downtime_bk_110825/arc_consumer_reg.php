<?php
include "web_check.php";
include "star_connection.php";

$branch_master = "branch_master";
$arc_consumer_reg = "arc_consumer_reg";
$customer_master = "customer_master";
$branch_credit_limit_status = "branch_credit_limit_status";
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
$acr_status_arr = array("PENDING","APPROVED","REDEEMED","REJECT");
$admin_status_arr = array("PENDING","APPROVED","REJECT");
$new_qry_string_filtered = "";
$srch_dtls = $_GET["srch_dtls"] ? addslashes(trim($_GET["srch_dtls"])) : "";
$sl_acr_status = $_GET["sl_acr_status"] ? addslashes(trim($_GET["sl_acr_status"])) : "";
$whr_str = "";
$search_array = array("srch_dtls"=>$srch_dtls,"sl_acr_status"=>$sl_acr_status);
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($arc_consumer_reg.`name` like '%$search_array_val%' or $arc_consumer_reg.`mobile` like '%$search_array_val%' or $arc_consumer_reg.`dns_customer_code` like '%$search_array_val%' or $customer_master.`customer_name` like '%$search_array_val%') ";

// $whr_str .= "$aand ($arc_consumer_reg.`name` like '%$srch_dtls%' or $arc_consumer_reg.`mobile` like '%$srch_dtls%' or $arc_consumer_reg.`dns_customer_code` like '%$srch_dtls%' or $customer_master.`customer_name` like '%$srch_dtls%' or $arc_consumer_reg.`S` like '%$srch_dtls%' or $arc_consumer_reg.`T` like '%$srch_dtls%' or $arc_consumer_reg.`A` like '%$srch_dtls%' or $arc_consumer_reg.`R` like '%$srch_dtls%' or $arc_consumer_reg.`lunch_box` like '%$srch_dtls%' or $arc_consumer_reg.`water bottle` like '%$srch_dtls%')";

			$new_qry_string_filtered .= "&srch_dtls=".$search_array_val;
		}
	}else if($search_array_key=="sl_acr_status"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($arc_consumer_reg.`status`='$search_array_val') ";
			$new_qry_string_filtered .= "&sl_acr_status=".$search_array_val;
		}
	}
}
if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}
$add_page_name = "arc_consumer_reg.php";
$page_name = "arc_consumer_reg.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/
$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;
/*---------PAGINATION RELATED CODE END----------*/
/*---------PAGINATION RELATED CODE START----------*/
$pgsql = "select $arc_consumer_reg.`ac_id` from $arc_consumer_reg left join $customer_master on $arc_consumer_reg.`customer_code`=$customer_master.`customer_code` $new_whr_str";
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
.arc_status_ldr{
	position:absolute;
	bottom: 2px;
	right:2px;
}
.upd_arc_reg_sts_btn{
	margin-top:8px;
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

                          <!-- <h2>ARC Consumer Registration List (<?php echo $total_pgres;?>)&nbsp;&nbsp;
<a href="export_arc_consumer_reg.php?status=<?php echo $sl_acr_status;?>" class="btn bg-red waves-effe">Export</a> &nbsp;&nbsp;&nbsp;</h2> -->

<h2>ARC Consumer Registration List (<?php echo $total_pgres;?>)&nbsp;&nbsp;
<a href="export_arc_consumer_reg.php?status=<?php echo $sl_acr_status;?>&srch_dtls=<?php echo $srch_dtls;?>" class="btn bg-red waves-effe">Export</a> &nbsp;&nbsp;&nbsp;</h2>





                            <div class="row clearfix">
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_dtls" value="<?php echo $srch_dtls;?>" placeholder="Search Details">
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
    <select class="form-control" id="sl_acr_status">
    <option value="">Select Status</option>
    <?php
    if(count($acr_status_arr)>0){
		foreach($acr_status_arr as $acr_status_arr_val){ ?>
		<option value="<?php echo $acr_status_arr_val;?>" <?php if($acr_status_arr_val==$sl_acr_status){?> selected="selected" <?php } ?>><?php echo $acr_status_arr_val;?></option>	
	<?php	}
	}
	?>
    </select>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_btn" >Search</button>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_reset_btn" >Reset</button>
    </div>
         
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
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
 <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Consumer&nbsp;Name</th>
                                            <th>Consumer&nbsp;Mobile</th>
                                            <th>No.&nbsp;of&nbsp;bags(ARC)</th>
                                            <th>Status</th>
                                            <th>Dealer&nbsp;Code</th>
                                            <th>Dealer&nbsp;Name</th>
                                            <th>Branch</th>
                                            <th>Entry&nbsp;Date</th>
                                            <th>Entry&nbsp;Time</th>
											<th>S&nbsp;Coupon</th>
											<th>T&nbsp;Coupon</th>
											<th>A&nbsp;Coupon</th>
											<th>R&nbsp;Coupon</th>
											<th>Lunch&nbsp;Box</th>
											<th>Water&nbsp;Bottle</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Consumer&nbsp;Name</th>
                                            <th>Consumer&nbsp;Mobile</th>
                                            <th>No.&nbsp;of&nbsp;bags(ARC)</th>
                                            <th>Status</th>
                                            <th>Dealer&nbsp;Code</th>
                                            <th>Dealer&nbsp;Name</th>
                                            <th>Branch</th>
                                            <th>Entry&nbsp;Date</th>
                                            <th>Entry&nbsp;Time</th>
											<th>S&nbsp;Coupon</th>
											<th>T&nbsp;Coupon</th>
											<th>A&nbsp;Coupon</th>
											<th>R&nbsp;Coupon</th>
											<th>Lunch&nbsp;Box</th>
											<th>Water&nbsp;Bottle</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select $arc_consumer_reg.*,$customer_master.`customer_name`,$customer_master.`branch_code` from $arc_consumer_reg left join $customer_master on $arc_consumer_reg.`customer_code`=$customer_master.`customer_code` $new_whr_str order by $arc_consumer_reg.`entry_datetime` desc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$ac_id = $row1["ac_id"];
		$consumer_name = $row1["name"];
		$consumer_mobile = $row1["mobile"];
		$no_of_bags = $row1["no_of_bags"];
		//$redeemed_bags = $row1["redeemed_bags"];
		$each_status = $row1["status"];
		$branch_code = $row1["branch_code"];
		$branch_name = get_branch_name_from_id($branch_code);
		$ar_customer_code = $row1["customer_code"];
		$ar_dns_customer_code = $row1["dns_customer_code"];
		$ar_customer_name = $row1["customer_name"];
		$entry_datetime = $row1["entry_datetime"];
		$entry_date = "";
		$entry_time = "";
		if($entry_datetime!=""){
		$entry_date	 = date("d-m-Y",strtotime($entry_datetime));
		$entry_time	 = date("h:i a",strtotime($entry_datetime));
		}
		$col_S = $row1["S"];
		$col_T = $row1["T"];
		$col_A = $row1["A"];
		$col_R = $row1["R"];
		$lunch_box = $row1["lunch_box"];
		$water_bottle = $row1["water_bottle"];
		
?>
<tr>
<td>
<?php
if($each_status=="REDEEMED" || $each_status=="APPROVED" || $each_status=="REJECT"){
	echo $consumer_name;
}else{
?>
<input type="text" value="<?php echo $consumer_name;?>" class="form-control arc_consumer_name" id="arc_consumer_name_<?php echo $ac_id;?>" autocomplete="off" />
<?php } ?>
</td>
<td><?php echo $consumer_mobile;?></td>
<td>
<?php
if($each_status=="REDEEMED" || $each_status=="APPROVED" || $each_status=="REJECT"){
	echo $no_of_bags;
}else{
?>
<input type="text" value="<?php echo $no_of_bags;?>" class="form-control arc_no_of_bags" id="arc_no_of_bags_<?php echo $ac_id;?>" autocomplete="off" />
<?php
} ?>
</td>
<!-- <td><?php echo $redeemed_bags;?></td> -->
<td style="position:relative;">
<?php
if($each_status=="REDEEMED" || $each_status=="APPROVED" || $each_status=="REJECT"){
	echo $each_status;
}else{
?>
<span class="arc_curr_status_val" id="arc_curr_status_val_<?php echo $ac_id;?>" style="display:none;"><?php echo $each_status;?></span>
<select class="form-control sel_arc_status" id="sel_arc_status_<?php echo $ac_id;?>" the_ac_id="<?php echo $ac_id;?>" autocomplete="off">
<?php
if(count($admin_status_arr)>0){
foreach($admin_status_arr as $admin_status_each_val){ ?>
<option value="<?php echo $admin_status_each_val;?>" <?php if($admin_status_each_val==$each_status){?> selected="selected" <?php } ?>><?php echo $admin_status_each_val;?></option>	
<?php	}
}
?>
</select>
<span class="arc_status_ldr" id="arc_status_ldr_<?php echo $ac_id;?>"></span>

<a href="javascript:void(0);" class="btn bg-red upd_arc_reg_sts_btn" the_ac_id="<?php echo $ac_id;?>">Update</a>

<?php } ?>

</td>
<td><?php echo $ar_dns_customer_code;?></td>
<td><?php echo $ar_customer_name;?></td>
<td><?php echo $branch_name;?></td>
<td><?php echo $entry_date;?></td>
<td><?php echo $entry_time;?></td>
<td><?php echo $col_S;?></td>
<td><?php echo $col_T;?></td>
<td><?php echo $col_A;?></td>
<td><?php echo $col_R;?></td>
<td><?php echo $lunch_box;?></td>
<td><?php echo $water_bottle;?></td>

</tr>
<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="10">No data found.</td>
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
	var imgs = '<img src="images/ajax-loader.gif"/>';
	var done_img = '<img src="images/success_tick.png"/>';


jQuery(".arc_no_of_bags").on("keypress keyup blur",function (event) {    
jQuery(this).val(jQuery(this).val().replace(/[^\d].+/, ""));
if ((event.which < 48 || event.which > 57)) {
event.preventDefault();
}
});

jQuery(".upd_arc_reg_sts_btn").click(function(){
		var ancr_elmnt = jQuery(this);
		var the_arc_status = ancr_elmnt.val();
		var the_ac_id = ancr_elmnt.attr("the_ac_id");
		if(the_ac_id!=""){
			var arc_consumer_name = jQuery("#arc_consumer_name_"+the_ac_id).val();
			var arc_no_of_bags = jQuery("#arc_no_of_bags_"+the_ac_id).val();
			var sel_arc_status_elmnt = jQuery("#sel_arc_status_"+the_ac_id);
			var sel_arc_status = sel_arc_status_elmnt.val();
			var arc_curr_status_val_elmnt = jQuery("#arc_curr_status_val_"+the_ac_id);
			var for_loader = jQuery("#arc_status_ldr_"+the_ac_id);
			for_loader.html(imgs);
			jQuery.ajax({
			url: 'ajax_arc_consumer_reg_status_update.php',
			type: 'post',
			dataType: "JSON",
			data: "the_ac_id="+the_ac_id+"&sel_arc_status="+sel_arc_status+"&arc_no_of_bags="+arc_no_of_bags+"&arc_consumer_name="+encodeURIComponent(arc_consumer_name),
			success: function(response){
			if(response.process_status=="YES"){
				if(sel_arc_status=="APPROVED" || sel_arc_status=="REJECT"){
					sel_arc_status_elmnt.hide();
					ancr_elmnt.hide();
					arc_curr_status_val_elmnt.html(sel_arc_status);
					arc_curr_status_val_elmnt.show();
				}
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

	jQuery(".srch_btn").click(function(){
		var srch_dtls = jQuery("#srch_dtls").val();
		var sl_acr_status = jQuery("#sl_acr_status").val();
		var qstring ="";
		var amp = "";
		if(srch_dtls!="" || sl_acr_status!=""){
		if(srch_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_dtls="+encodeURIComponent(srch_dtls);
			}else{
				qstring = qstring+"srch_dtls="+encodeURIComponent(srch_dtls);
			}
		}
		if(sl_acr_status!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_acr_status="+encodeURIComponent(sl_acr_status);
			}else{
				qstring = qstring+"sl_acr_status="+encodeURIComponent(sl_acr_status);
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
});
</script>


<?php
include "web_footer.php";
mysql_close();
?>
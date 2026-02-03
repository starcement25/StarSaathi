<?php
include "web_check.php";
include "star_connection.php";

$branch_master = "branch_master";
$arc_gift_redeem_table = "arc_gift_redeem_table";
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
$acr_status_arr = array("PENDING","DELIVERED");
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
$whr_str .= "$aand ($arc_gift_redeem_table.`name` like '%$search_array_val%' or $arc_gift_redeem_table.`mobile` like '%$search_array_val%' or $customer_master.`dns_customer_code` like '%$search_array_val%' or $customer_master.`customer_name` like '%$search_array_val%') ";
			$new_qry_string_filtered .= "&srch_dtls=".$search_array_val;
		}
	}else if($search_array_key=="sl_acr_status"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($arc_gift_redeem_table.`status`='$search_array_val') ";
			$new_qry_string_filtered .= "&sl_acr_status=".$search_array_val;
		}
	}
}
if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}
$add_page_name = "arc_gift_redeem.php";
$page_name = "arc_gift_redeem.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/
$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;
/*---------PAGINATION RELATED CODE END----------*/
/*---------PAGINATION RELATED CODE START----------*/
$pgsql = "select $arc_gift_redeem_table.`ac_id` from $arc_gift_redeem_table left join $customer_master on $arc_gift_redeem_table.`customer_code`=$customer_master.`customer_code` $new_whr_str";
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
                          <h2>ARC Gift Redemption List (<?php echo $total_pgres;?>)&nbsp;&nbsp;<a href="export_arc_gift_redeem.php?status=<?php echo $sl_acr_status;?>" class="btn bg-red waves-effe">Export</a> &nbsp;&nbsp;&nbsp;</h2>
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
                                            <th>Redeemed&nbsp;Bags</th>
                                            <th>Gift</th>
                                            <th>Status</th>
                                            <th>Dealer&nbsp;Code</th>
                                            <th>Dealer&nbsp;Name</th>
                                            <th>Branch</th>
                                            <th>Entry&nbsp;Date</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Consumer&nbsp;Name</th>
                                            <th>Consumer&nbsp;Mobile</th>
                                            <th>Redeemed&nbsp;Bags</th>
                                            <th>Gift</th>
                                            <th>Status</th>
                                            <th>Dealer&nbsp;Code</th>
                                            <th>Dealer&nbsp;Name</th>
                                            <th>Branch</th>
                                            <th>Entry&nbsp;Date</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select $arc_gift_redeem_table.*,$customer_master.`customer_name`,$customer_master.`branch_code` from $arc_gift_redeem_table left join $customer_master on $arc_gift_redeem_table.`customer_code`=$customer_master.`customer_code` $new_whr_str order by $arc_gift_redeem_table.`entry_datetime` desc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$ac_id = $row1["ac_id"];
		$consumer_name = $row1["name"];
		$consumer_mobile = $row1["mobile"];
		$redeemed_bags = $row1["redeemed_bags"];
		$each_status = $row1["status"];
		$each_gift_name = $row1["gift_name"];
		$branch_code = $row1["branch_code"];
		$branch_name = get_branch_name_from_id($branch_code);
		$ar_customer_code = $row1["customer_code"];
		$ar_dns_customer_code = $row1["dns_customer_code"];
		$ar_customer_name = $row1["customer_name"];
		$entry_datetime = $row1["entry_datetime"];
		if($entry_datetime!=""){
		$entry_datetime	 = date("jS M'y",strtotime($entry_datetime));
		}
		
?>
<tr>
<td><?php echo $consumer_name;?></td>
<td><?php echo $consumer_mobile;?></td>
<td><?php echo $redeemed_bags;?></td>
<td><?php echo $each_gift_name;?></td>
<td style="position:relative;">

<select class="form-control sel_arc_status" id="sel_arc_status_<?php echo $ac_id;?>" the_ac_id="<?php echo $ac_id;?>" autocomplete="off">
<?php
if(count($acr_status_arr)>0){
foreach($acr_status_arr as $admin_status_each_val){ ?>
<option value="<?php echo $admin_status_each_val;?>" <?php if($admin_status_each_val==$each_status){?> selected="selected" <?php } ?>><?php echo $admin_status_each_val;?></option>	
<?php	}
}
?>
</select>
<span class="arc_status_ldr" id="arc_status_ldr_<?php echo $ac_id;?>"></span>

<a href="javascript:void(0);" class="btn bg-red upd_arc_reg_sts_btn" the_ac_id="<?php echo $ac_id;?>">Update</a>


</td>
<td><?php echo $ar_dns_customer_code;?></td>
<td><?php echo $ar_customer_name;?></td>
<td><?php echo $branch_name;?></td>
<td><?php echo $entry_datetime;?></td>

</tr>
<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="9">No data found.</td>
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


jQuery(".upd_arc_reg_sts_btn").click(function(){
		var ancr_elmnt = jQuery(this);
		var the_arc_status = ancr_elmnt.val();
		var the_ac_id = ancr_elmnt.attr("the_ac_id");
		if(the_ac_id!=""){
			var sel_arc_status = jQuery("#sel_arc_status_"+the_ac_id).val();
			var for_loader = jQuery("#arc_status_ldr_"+the_ac_id);
			for_loader.html(imgs);
			jQuery.ajax({
			url: 'ajax_arc_gift_redemption_status_update.php',
			type: 'post',
			dataType: "JSON",
			data: "the_ac_id="+the_ac_id+"&sel_arc_status="+sel_arc_status,
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
<?php
include "web_check.php";
include "star_connection.php";
$branch_master = "branch_master";
$branch_credit_limit_status = "branch_credit_limit_status";
$new_qry_string_filtered = "";
$srch_branch_dtls = $_GET["srch_branch_dtls"] ? addslashes(trim($_GET["srch_branch_dtls"])) : "";
$sl_status = $_GET["sl_status"] ? addslashes(trim($_GET["sl_status"])) : "";
$the_arc_status = $_GET["the_arc_status"] ? addslashes(trim($_GET["the_arc_status"])) : "";
$whr_str = "";
$search_array = array("srch_branch_dtls"=>$srch_branch_dtls,"sl_status"=>$sl_status,"the_arc_status"=>$the_arc_status);
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_branch_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($branch_master.branch_name like '%$search_array_val%' or $branch_master.dns_branch_code like '%$search_array_val%' or $branch_credit_limit_status.branch_code like '%$search_array_val%') ";
			$new_qry_string_filtered .= "&srch_branch_dtls=".$search_array_val;
		}
	}else if($search_array_key=="sl_status"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($branch_credit_limit_status.credit_limit_status='$search_array_val') ";
			$new_qry_string_filtered .= "&sl_status=".$search_array_val;
		}
	}else if($search_array_key=="the_arc_status"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($branch_credit_limit_status.arc_status='$search_array_val') ";
			$new_qry_string_filtered .= "&the_arc_status=".$search_array_val;
		}
	}
}
if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}
$add_page_name = "branch_credit_limit_status.php";
$page_name = "branch_credit_limit_status.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/
$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;
/*---------PAGINATION RELATED CODE END----------*/
/*---------PAGINATION RELATED CODE START----------*/
$pgsql = "select $branch_master.branch_code from $branch_master INNER join $branch_credit_limit_status on $branch_master.branch_code=$branch_credit_limit_status.branch_code $new_whr_str";
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
	top: 0px;
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
                          <h2>Branch wise setup (<?php echo $total_pgres;?>)&nbsp;&nbsp;
                          <?php /*?><a href="export_dealer.php?get_type=loggedin" class="btn bg-red waves-effe">Export&nbsp;loggedin&nbsp;dealer</a> &nbsp; <a href="export_dealer.php?get_type=notloggedin" class="btn bg-red waves-effe">Export&nbsp;not&nbsp;loggedin&nbsp;dealer</a><?php */?>
                          </h2>
                            <div class="row clearfix">
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_branch_dtls" value="<?php echo $srch_branch_dtls;?>" placeholder="Search Branch Details">
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
    <select class="form-control" id="sl_status">
    <option value="">Select Status</option>
     <option value="Y" <?php if($sl_status=='Y'){?> selected="selected" <?php } ?>>ACTIVE</option>
     <option value="N" <?php if($sl_status=='N'){?> selected="selected" <?php } ?>>INACTIVE</option>
    </select>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
    <select class="form-control" id="the_arc_status">
    <option value="">Select ARC Status</option>
     <option value="Y" <?php if($the_arc_status=='Y'){?> selected="selected" <?php } ?>>ACTIVE</option>
     <option value="N" <?php if($the_arc_status=='N'){?> selected="selected" <?php } ?>>INACTIVE</option>
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
                                            <th>Branch&nbsp;Code</th>
                                            <th>Branch&nbsp;Name</th>
                                            <th>Credit&nbsp;Limit&nbsp;Check</th>
                                            <th>ARC&nbsp;Status</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Branch&nbsp;Code</th>
                                            <th>Branch&nbsp;Name</th>
                                            <th>Credit&nbsp;Limit&nbsp;Check</th>
                                            <th>ARC&nbsp;Status</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select $branch_master.dns_branch_code,$branch_master.branch_name,$branch_credit_limit_status.branch_code,$branch_credit_limit_status.credit_limit_status,$branch_credit_limit_status.arc_status from $branch_master INNER join $branch_credit_limit_status on $branch_master.branch_code=$branch_credit_limit_status.branch_code $new_whr_str order by branch_master.branch_name ASC limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$dns_branch_code = $row1["dns_branch_code"];
		$branch_name = $row1["branch_name"];
		$branch_code = $row1["branch_code"];
		$credit_limit_status = $row1["credit_limit_status"];
		if($credit_limit_status=='Y') { $credit_limit_status='ACTIVE'; $credit_limit_status_changed='INACTIVE';}
		if($credit_limit_status=='N') { $credit_limit_status='INACTIVE'; $credit_limit_status_changed='ACTIVE';}
		$branch_arc_status = $row1["arc_status"];
?>
<tr>
<td><?php echo $dns_branch_code;?></td>
<td><?php echo $branch_name;?></td>
<td><a href="javascript:void(0);" class="branchstat" branchid="<?php echo $branch_code;?>" the_dns_branch_code="<?php echo $dns_branch_code;?>" ><?php echo $credit_limit_status;?></a><span class="os_ldr" id="ca_ldr_<?php echo $dns_branch_code;?>"></span></td>
<td style="position:relative;">
<select class="form-control sel_arc_status" id="sel_arc_status_<?php echo $branch_code;?>" the_branch_code="<?php echo $branch_code;?>">
<option value="Y" <?php if($branch_arc_status=="Y"){ ?> selected="selected" <?php }?>>ACTIVE</option>
<option value="N" <?php if($branch_arc_status=="N"){ ?> selected="selected" <?php }?>>INACTIVE</option>
</select>
<span class="arc_status_ldr" id="arc_status_ldr_<?php echo $branch_code;?>"></span>
</td>
</tr>
<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="4">No data found.</td>
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


jQuery(".sel_arc_status").change(function(){
		var ancr_elmnt = jQuery(this);
		var the_arc_status = ancr_elmnt.val();
		var the_branch_code = ancr_elmnt.attr("the_branch_code");
		if(the_branch_code!=""){
			var for_loader = jQuery("#arc_status_ldr_"+the_branch_code);
			for_loader.html(imgs);
			jQuery.ajax({
			url: 'ajax_branch_arc_status_update.php',
			type: 'post',
			dataType: "JSON",
			data: "the_branch_code="+the_branch_code+"&the_arc_status="+the_arc_status,
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

		jQuery(".branchstat").click(function(){
		var ancr_elmnt = jQuery(this);
		var branchcode = ancr_elmnt.attr("branchid");
		var the_dns_branch_code = ancr_elmnt.attr("the_dns_branch_code");
		if(branchcode!=""){
			var theldrid = "ca_ldr_"+the_dns_branch_code;
			var for_loader = jQuery("#"+theldrid);
			//var for_loader = jQuery("#"+branchcode);
			for_loader.html(imgs);
			jQuery.ajax({
			url: 'ajax_branch_credit_limit_stat_update.php',
			type: 'post',
			dataType: "JSON",
			data: "branchcode="+branchcode,
			success: function(response){
			if(response.process_status=="YES"){
				if(response.credit_limit_status=='Y') 
				{
					ancr_elmnt.html('ACTIVE');
				}
				if(response.credit_limit_status=='N') 
				{
					ancr_elmnt.html('INACTIVE');
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
		var srch_branch_dtls = jQuery("#srch_branch_dtls").val();
		var sl_status = jQuery("#sl_status").val();
		var the_arc_status = jQuery("#the_arc_status").val();
		var qstring ="";
		var amp = "";
		if(srch_branch_dtls!="" || sl_status!="" || the_arc_status!=""){
		if(srch_branch_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_branch_dtls="+encodeURIComponent(srch_branch_dtls);
			}else{
				qstring = qstring+"srch_branch_dtls="+encodeURIComponent(srch_branch_dtls);
			}
		}
		if(sl_status!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_status="+encodeURIComponent(sl_status);
			}else{
				qstring = qstring+"sl_status="+encodeURIComponent(sl_status);
			}
		}
		
		if(the_arc_status!=""){
			if(qstring!=""){
				qstring = qstring+"&the_arc_status="+encodeURIComponent(the_arc_status);
			}else{
				qstring = qstring+"the_arc_status="+encodeURIComponent(the_arc_status);
			}
		}
		
		  if(qstring!=""){
			 qstring = "?"+qstring; 
		  }
		window.location = "branch_credit_limit_status.php"+qstring;
		}else{
			alert("Please select atleast one field to search.");
		}
	});
	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "branch_credit_limit_status.php";
	});
});
</script>
<?php
include "web_footer.php";
mysql_close();
?>
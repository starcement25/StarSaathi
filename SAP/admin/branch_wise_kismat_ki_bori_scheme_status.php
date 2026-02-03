<?php
include "web_check.php";
include "star_connection.php";
$branch_master = "branch_master";
$branch_kismat_ki_bori_scheme_status = "branch_kismat_ki_bori_scheme_status";

$pg_sts_arr = array();
$consumer_scheme_sts_arr[] = array("key_val"=>"","title_val"=>"Not Set");
$consumer_scheme_sts_arr[] = array("key_val"=>"ACTIVE","title_val"=>"ACTIVE");
$consumer_scheme_sts_arr[] = array("key_val"=>"INACTIVE","title_val"=>"INACTIVE");

$new_qry_string_filtered = "";
$srch_dlr_dtls = $_GET["srch_dlr_dtls"] ? addslashes(trim($_GET["srch_dlr_dtls"])) : "";
$whr_str = "";
$search_array = array("srch_dlr_dtls"=>$srch_dlr_dtls);
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_dlr_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($branch_master.`dns_branch_code` like '%$search_array_val%' or $branch_master.`branch_name` like '%$search_array_val%' ) ";
			$new_qry_string_filtered .= "&srch_dlr_dtls=".$search_array_val;
		}
	}
}
if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}
$add_page_name = "branch_wise_kismat_ki_bori_scheme_status.php";
$page_name = "branch_wise_kismat_ki_bori_scheme_status.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/
$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;
/*---------PAGINATION RELATED CODE END----------*/
/*---------PAGINATION RELATED CODE START----------*/


$pgsql = "SELECT $branch_master.`branch_code` FROM $branch_master left join $branch_kismat_ki_bori_scheme_status on $branch_master.`branch_code`=$branch_kismat_ki_bori_scheme_status.`branch_code` $new_whr_str";
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
.dlr_prfl_img{
	width:150px;
}
.bwps_sel{
	width:150px;
}
.os_ldr{
	position:absolute;
	right:5px;
	top:5px;
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
                          <h2>Branch Wise Kismat Ki Bori Scheme Status (<?php echo $total_pgres;?>)&nbsp;&nbsp;<!--<a href="export_sp_destination.php?get_type=all" class="btn bg-red waves-effe">Export&nbsp;all</a>-->
                         
                          </h2>
                            <div class="row clearfix">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_dlr_dtls" value="<?php echo $srch_dlr_dtls;?>" placeholder="Search Branch Details">
    </div>
   
    
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_btn" >Search</button>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_reset_btn" >Reset</button>
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
                                            <th>Kismat Ki Bori Scheme Status</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Branch&nbsp;Code</th>
                                            <th>Branch&nbsp;Name</th>
                                            <th>Kismat Ki Bori Scheme Status</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php

$sql1 = "SELECT $branch_master.`branch_code`,$branch_master.`dns_branch_code`,$branch_master.`branch_name`,$branch_kismat_ki_bori_scheme_status.`kismat_ki_bori_scheme_status` FROM $branch_master left join $branch_kismat_ki_bori_scheme_status on $branch_master.`branch_code`=$branch_kismat_ki_bori_scheme_status.`branch_code` $new_whr_str order by $branch_master.`branch_name` asc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$the_branch_code = $row1["branch_code"];
		$the_dns_branch_code = $row1["dns_branch_code"];
		$the_branch_name = $row1["branch_name"];
		$the_kismat_ki_bori_scheme_status = $row1["kismat_ki_bori_scheme_status"] ? trim($row1["kismat_ki_bori_scheme_status"]) : "";
		
?>
<tr>
<td><?php echo $the_dns_branch_code;?></td>
<td><?php echo $the_branch_name;?></td>
<td style="position:relative;">

<select class="form-control bwps_sel" id="bwps_sel_<?php echo $the_branch_code;?>" the_brnch_code="<?php echo $the_branch_code;?>">
<?php
if(count($consumer_scheme_sts_arr)>0){
	foreach($consumer_scheme_sts_arr as $consumer_scheme_sts_arr_val){
		$the_key_val = $consumer_scheme_sts_arr_val["key_val"];
		$the_title_val = $consumer_scheme_sts_arr_val["title_val"]; ?>
        <option value="<?php echo $the_key_val;?>" <?php if($the_key_val==$the_kismat_ki_bori_scheme_status){?> selected="selected" <?php } ?>><?php echo $the_title_val;?></option>
        <?php
	}
	
}

?>
</select>


<span class="os_ldr" id="ca_ldr_<?php echo $the_branch_code;?>"></span></td>


</tr>
<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="3">No data found.</td>
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
	
	jQuery(".bwps_sel").change(function(){
		var ancr_elmnt = jQuery(this);
		var the_status = ancr_elmnt.val();
		var the_brnch_code = ancr_elmnt.attr("the_brnch_code");
		if(the_brnch_code!=""){
			var for_loader = jQuery("#ca_ldr_"+the_brnch_code);
			for_loader.html(imgs);
			jQuery.ajax({
			url: 'ajax_bwkkbcs_status_update.php',
			type: 'post',
			dataType: "JSON",
			data: "the_brnch_code="+the_brnch_code+"&the_status="+the_status,
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
		var srch_dlr_dtls = jQuery("#srch_dlr_dtls").val();
		var qstring ="";
		var amp = "";
		if(srch_dlr_dtls!="" ){
		if(srch_dlr_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}else{
				qstring = qstring+"srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
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
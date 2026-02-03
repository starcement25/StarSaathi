<?php
include "web_check.php";
include "star_connection.php";
$branch_master = "branch_master";
$branch_rssd_allocation_days = "branch_rssd_allocation_days";

	

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
$add_page_name = "branch_wise_rssd_allocation_days.php";
$page_name = "branch_wise_rssd_allocation_days.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/
$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;
/*---------PAGINATION RELATED CODE END----------*/
/*---------PAGINATION RELATED CODE START----------*/


$pgsql = "SELECT $branch_master.`branch_code` FROM $branch_master left join $branch_rssd_allocation_days on $branch_master.`branch_code`=$branch_rssd_allocation_days.`branch_code` $new_whr_str";
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
                          <h2>Branch Wise RSSD Allocation Days (<?php echo $total_pgres;?>)&nbsp;&nbsp;<!--<a href="export_sp_destination.php?get_type=all" class="btn bg-red waves-effe">Export&nbsp;all</a>-->
                         
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
	 <form action="" method="post">
   <input type="hidden" name="mode" value="allocation_days_update" />
<?php
		 if($_REQUEST['mode']=='allocation_days_update')
	{
	$the_branch_code_val=$_REQUEST['the_branch_code_val'];
	//print_r($dono);
	for($i=0;$i<count($the_branch_code_val);$i++)
	{
	  $the_branch_code=$the_branch_code_val[$i];
	  $allocation_days_val=$_REQUEST["allocation_days_".$the_branch_code];
	 if($allocation_days_val=='')	$allocation_days_val=0;
	  //if($allocation_days_val!=''){
		  $sqlselbranch="SELECT branch_code FROM branch_rssd_allocation_days WHERE branch_code='".$the_branch_code."'";
		  $rsselbranch=mysql_query($sqlselbranch);
		  $countselbranch=mysql_num_rows($rsselbranch);
		  if($countselbranch==0)
			 {
			  $ins_sql="INSERT INTO branch_rssd_allocation_days SET branch_code ='$the_branch_code',allocation_days ='".$allocation_days_val."',upload_time =CURRENT_TIMESTAMP()";
			 mysql_query($ins_sql) or die(mysql_error()." Error in inser allocation days.");
		  
		  }
		  else{
			$upd_sql="UPDATE branch_rssd_allocation_days SET allocation_days ='$allocation_days_val',upload_time =CURRENT_TIMESTAMP() WHERE branch_code = '".$the_branch_code."'";
			mysql_query($upd_sql) or die(mysql_error()." Error in update allocation days.");
		  }
	  //}
	}
		?>
         <table class="table table-bordered table-striped table-hover">
				<tr> 
					<td align="center" ><b>Allocation days updated successfully</b></td>
				</tr>
				</table>
        <?php
	//$GLOBALS['err_msg']="DO ".strtoupper($status)." SUCCESSFUL.";
  }?>
		 

                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Branch&nbsp;Code</th>
                                            <th>Branch&nbsp;Name</th>
                                            <th>Allocation Days</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Branch&nbsp;Code</th>
                                            <th>Branch&nbsp;Name</th>
                                            <th>Allocation Days</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php

$sql1 = "SELECT $branch_master.`branch_code`,$branch_master.`dns_branch_code`,$branch_master.`branch_name`,$branch_rssd_allocation_days.`allocation_days` FROM $branch_master left join $branch_rssd_allocation_days on $branch_master.`branch_code`=$branch_rssd_allocation_days.`branch_code` $new_whr_str order by $branch_master.`branch_name` asc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$the_branch_code = $row1["branch_code"];
		$the_dns_branch_code = $row1["dns_branch_code"];
		$the_branch_name = $row1["branch_name"];
		$allocation_days = $row1["allocation_days"] ? trim($row1["allocation_days"]) : "";
		
?>
<tr>
<td><?php echo $the_dns_branch_code;?></td>
<td><?php echo $the_branch_name;?></td>
<td style="position:relative;">

<input type="text" class="form-control" name="allocation_days_<?php echo $the_branch_code;?>" id="allocation_days_<?php echo $the_branch_code;?>" the_brnch_code="<?php echo $the_branch_code;?>" style="width: 50%" value="<?php echo $allocation_days;?>">
<?php echo "<input type=\"hidden\" name=\"the_branch_code_val[]\" value=".$the_branch_code.">";?>

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
<tr>
<td></td>
<td></td>
<td style="text-align:left" ><input type="submit" name="save" name="save" class="btn bg-red waves-effect" /></td>
</tr>										
</tbody>
</table>
	</form>	 
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
			url: 'ajax_bgame_status_update.php',
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
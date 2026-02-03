<?php
include "web_check.php";
include "star_connection.php";
$webservice_track_log = "webservice_track_log";
$customer_master = "customer_master";
$branch_master ="branch_master";

$new_qry_string_filtered = "";
$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$srch_api_dtls = $_GET["srch_api_dtls"] ? addslashes(trim($_GET["srch_api_dtls"])) : "";
$srch_api_name = $_GET["srch_api_name"] ? addslashes(trim($_GET["srch_api_name"])) : "";
$sl_day_wise = $_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : "";
$from_dt = $_GET["from_dt"] ? addslashes(trim($_GET["from_dt"])) : "";
$to_dt = $_GET["to_dt"] ? addslashes(trim($_GET["to_dt"])) : "";
$astn_branch_code = $_GET["astn_branch_code"] ? addslashes(trim($_GET["astn_branch_code"])) : "";


$whr_str = "";
$search_array = array("srch_api_dtls"=>$srch_api_dtls,"srch_api_name"=>$srch_api_name,"astn_branch_code"=>$astn_branch_code,"daywise"=>array("sl_day_wise"=>$sl_day_wise,"from_dt"=>$from_dt,"to_dt"=>$to_dt));
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_api_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($webservice_track_log.`webservice_name` like '%$search_array_val%' or $customer_master.`dns_customer_code` like '%$search_array_val%' or $customer_master.`customer_name` like '%$search_array_val%' or $customer_master.`customer_id` like '%$search_array_val%') ";
			$new_qry_string_filtered .= "&srch_api_dtls=".$search_array_val;
		}
	}
	else if($search_array_key=="srch_api_name"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand $webservice_track_log.`webservice_name`='$search_array_val'";
			$new_qry_string_filtered .= "&srch_api_name=".$search_array_val;
		}
	}
	else if($search_array_key=="astn_branch_code"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $customer_master.`branch_code` = '$search_array_val' ";
			$new_qry_string_filtered .= "&astn_branch_code=".$search_array_val;
		}
	}
	else if($search_array_key=="daywise"){
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
			   $whr_str .= "$aand $webservice_track_log.`datetime` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
			   $new_qry_string_filtered .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}
			}else if($the_from_dt!="" && $the_to_dt==""){
				$whr_str .= "$aand $webservice_track_log.`datetime` >= '".$the_from_dt." ".$frm_hrs."' ";
				$new_qry_string_filtered .= "&from_dt=".$the_from_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}
			}else if($the_from_dt=="" && $the_to_dt!=""){
				$whr_str .= "$aand $webservice_track_log.`datetime` <= '".$the_to_dt." ".$to_hrs."' ";
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
				$whr_str .= "$aand $webservice_track_log.`datetime` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
			}else if($the_sl_day_wise=="Yesterday"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand $webservice_track_log.`datetime` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
			
			}
		}
	}
}

if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}
//$add_page_name = "edit_dealer_list.php";
$brsql = "select `branch_code`,`branch_name` from $branch_master where `acedns`='Y' order by `branch_name` asc";
$brres = mysql_query($brsql);
$total_brres = mysql_num_rows($brres);
$page_name = "customer_usage_log.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select $webservice_track_log.`customer_code`,$webservice_track_log.`webservice_name`,$customer_master.`customer_name`,$customer_master.region,$branch_master.branch_name,$webservice_track_log.datetime from 
		$webservice_track_log inner join $customer_master on $webservice_track_log.`customer_code`=$customer_master.`customer_id` inner join $branch_master ON $customer_master.`branch_code`=$branch_master.branch_code $new_whr_str";
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
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                
            </div>
            <!-- Basic Examples -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                    <div class="header">
                          <h2>Usage Log (<?php echo $total_pgres;?>)&nbsp;&nbsp;<a href="export_usage_log_list.php" class="btn bg-red waves-effe">Export&nbsp;all</a></h2>
                      
                            <div class="row clearfix">
    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_api_dtls" value="<?php echo $srch_api_dtls;?>" placeholder="Search Dealer ID/Name">
    </div>
	<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding2">
<select class="form-control" id="astn_branch_code" name="astn_branch_code" style="padding-left:2px;" data-placeholder="Select Branch Name">
<option value="">Select Branch Name</option>
<?php
if($total_brres>0){
	while($brrow=mysql_fetch_assoc($brres)){
		$the_br_code = $brrow["branch_code"];
		$the_br_name = $brrow["branch_name"];?>
        <option value="<?php echo $the_br_code;?>" <?php if($the_br_code==$astn_branch_code){ ?> selected="selected" <?php } ?>><?php echo $the_br_name." (".$the_br_code.")";?></option>
		<?php
	}
	
}
?>

</select>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control srch_api_name" id="srch_api_name" name="srch_api_name">
<option value="">Activity</option>
<?php $sqlapiname="SELECT DISTINCT $webservice_track_log.webservice_name FROM $webservice_track_log ORDER BY $webservice_track_log.webservice_name ASC"; 
	  $rsapiname=mysql_query($sqlapiname);
	  while($rowapiname=mysql_fetch_assoc($rsapiname))
	  {
?>
<option value="<?php echo $rowapiname['webservice_name'];?>" <?php if($srch_api_name==$rowapiname['webservice_name']){ ?> selected="selected" <?php }?> ><?php echo $rowapiname['webservice_name'];?></option>
<?php }?>
</select>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding" style="padding:5px 0px;">
    <select class="form-control sl_day_wise" id="sl_day_wise" >
<option value="">All Date</option>
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
											<th>Dealer SAP Code</th>
											<th>Dealer&nbsp;Name</th>
											<th>Branch</th>
											<th>Region</th>
											<th>Activity</th>
                                        	<th>Date & Time</th
                                        </tr>
                                    </thead>
                                    <tbody>
<?php
/*$sql1 = "select $webservice_track_log.`customer_code`,$webservice_track_log.`webservice_name`,count($webservice_track_log.`webservice_name`) as `tot_count`,$customer_master.`dns_customer_code`,$customer_master.`customer_name`,$customer_master.sales_office,$customer_master.sales_office_desc,SUBSTRING($webservice_track_log.datetime,1,10) AS hit_date from 
		$webservice_track_log left join $customer_master on $webservice_track_log.`customer_code`=$customer_master.`customer_code` $new_whr_str 
		group by SUBSTRING($webservice_track_log.datetime,1,10),$webservice_track_log.`customer_code`,$webservice_track_log.`webservice_name` order by SUBSTRING($webservice_track_log.datetime,1,10) DESC,$webservice_track_log.`customer_code` asc limit $start_from,$limit";*/
$sql1 = "select $webservice_track_log.`customer_code`,$webservice_track_log.`webservice_name`,$customer_master.`customer_name`,$customer_master.region,$branch_master.branch_name,$webservice_track_log.datetime from 
		$webservice_track_log inner join $customer_master on $webservice_track_log.`customer_code`=$customer_master.`customer_id` inner join $branch_master ON $customer_master.`branch_code`=$branch_master.branch_code $new_whr_str 
		 order by $webservice_track_log.datetime DESC,$webservice_track_log.`customer_code` asc limit $start_from,$limit";										
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$dealer_id = $row1["customer_code"];
		$customer_name = $row1["customer_name"];
		$webservice_name = $row1["webservice_name"];
		$branch_name = $row1["branch_name"];
		$region = $row1["region"];
		$datetime = $row1["datetime"];
		
?>
<tr>
<td><?php echo $dealer_id;?></td>
<td><?php echo $customer_name;?></td>
<td><?php echo $branch_name;?></td>
<td><?php echo $region;?></td>
<td><?php echo $webservice_name;?></td>	
<td><?php echo $datetime;?></td>
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
jQuery( "#from_dt" ).datepicker({
dateFormat: 'yy-mm-dd',
changeMonth:true,
changeYear:true
});
jQuery( "#to_dt" ).datepicker({
dateFormat: 'yy-mm-dd',
changeMonth:true,
changeYear:true
});
	
	jQuery(".srch_btn").click(function(){
		var srch_api_dtls = jQuery("#srch_api_dtls").val();
		var srch_api_name = jQuery("#srch_api_name").val();
		var sl_day_wise = jQuery("#sl_day_wise").val();
		var from_dt = jQuery("#from_dt").val();
		var to_dt = jQuery("#to_dt").val();
		var astn_branch_code = jQuery("#astn_branch_code").val();
		
		var qstring ="";
		var amp = "";
		if(srch_api_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_api_dtls="+encodeURIComponent(srch_api_dtls);
			}else{
				qstring = qstring+"srch_api_dtls="+encodeURIComponent(srch_api_dtls);
			}
		}
		if(srch_api_name!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_api_name="+encodeURIComponent(srch_api_name);
			}else{
				qstring = qstring+"srch_api_name="+encodeURIComponent(srch_api_name);
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
		if(astn_branch_code!=""){
			if(qstring!=""){
				qstring = qstring+"&astn_branch_code="+encodeURIComponent(astn_branch_code);
			}else{
				qstring = qstring+"astn_branch_code="+encodeURIComponent(astn_branch_code);
			}
		}
		  if(qstring!=""){
			 qstring = "?"+qstring; 
		  }
		window.location = "<?php echo $page_name;?>"+qstring;
		
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
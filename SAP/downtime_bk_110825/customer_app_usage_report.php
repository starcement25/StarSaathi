<?php
include "web_check.php";
include "star_connection.php";
$webservice_track_log = "webservice_track_log";
$customer_master = "customer_master";
$branch_master ="branch_master";

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
                          <h2>App Usage Log &nbsp;&nbsp;<a href="export_usage_log_list.php" class="btn bg-red waves-effe">Export&nbsp;all</a></h2>
                      
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
            <th>Dealer ID</th>
            <th>Dealer SAP Code</th>
            <th>Dealer&nbsp;Name</th>
            <th>Branch</th>
            <th>Region</th>
            <th>Activity</th>
            <th>Date & Time</th>
        </tr>
    </thead>
    <tbody>

<tr>

</tr>

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
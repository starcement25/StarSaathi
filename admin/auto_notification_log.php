<?php
include "web_check.php";
include "star_connection.php";
$table_name = "auto_notification_log";
$table_name_one = "customer_master";
$message_type_arr = array("PN","SMS");
$sent_status_arr = array("TRUE","FALSE");
$new_qry_string_filtered = "";
$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$srch_branch_dtls = $_GET["srch_dealer_dtls"] ? addslashes(trim($_GET["srch_dealer_dtls"])) : "";
$sel_message_type = $_GET["sel_message_type"] ? addslashes(trim($_GET["sel_message_type"])) : "";
$sel_sent_status = $_GET["sel_sent_status"] ? addslashes(trim($_GET["sel_sent_status"])) : "";
$sl_day_wise = $_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : "";
$from_dt = $_GET["from_dt"] ? addslashes(trim($_GET["from_dt"])) : "";
$to_dt = $_GET["to_dt"] ? addslashes(trim($_GET["to_dt"])) : "";
$whr_str = "";
$search_array = array("srch_dealer_dtls"=>$srch_branch_dtls,"sel_message_type"=>$sel_message_type,"sel_sent_status"=>$sel_sent_status,"daywise"=>array("sl_day_wise"=>$sl_day_wise,"from_dt"=>$from_dt,"to_dt"=>$to_dt));
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_dealer_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand (`cust_dns_code` like '%$search_array_val%' or `cust_name` like '%$search_array_val%') ";
			$new_qry_string_filtered .= "&srch_dealer_dtls=".$search_array_val;
		}
	}else if($search_array_key=="sel_message_type"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			
				$whr_str .= "$aand `message_type`='".$search_array_val."' ";
			$new_qry_string_filtered .= "&sel_message_type=".$search_array_val;		
		}		
	}else if($search_array_key=="sel_sent_status"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			
				$whr_str .= "$aand `message_sent_status`='".$search_array_val."' ";
			$new_qry_string_filtered .= "&sel_sent_status=".$search_array_val;		
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
			   $whr_str .= "$aand `sent_datetime` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
			   $new_qry_string_filtered .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}
			}else if($the_from_dt!="" && $the_to_dt==""){
				$whr_str .= "$aand `sent_datetime` >= '".$the_from_dt." ".$frm_hrs."' ";
				$new_qry_string_filtered .= "&from_dt=".$the_from_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}
			}else if($the_from_dt=="" && $the_to_dt!=""){
				$whr_str .= "$aand `sent_datetime` <= '".$the_to_dt." ".$to_hrs."' ";
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
				$whr_str .= "$aand `sent_datetime` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
			}else if($the_sl_day_wise=="Yesterday"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand `sent_datetime` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
			
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
$page_name = "auto_notification_log.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select `anl_id` from $table_name  $new_whr_str";
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
.sel_message_type{
	padding:2px;
}
.sel_sent_status{
	padding:2px;
}
.sl_day_wise{
	padding:0px;
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
                          <h2>Auto Notification Log (<?php echo $total_pgres;?>)&nbsp;&nbsp;
                         
                          </h2>
                            <div class="row clearfix">
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_dealer_dtls" value="<?php echo $srch_dealer_dtls;?>" placeholder="Search Dealer Details">
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <select class="form-control sel_message_type" id="sel_message_type">
    <option value="">All Type</option>
    <?php
    if(count($message_type_arr)>0){
		foreach($message_type_arr as $message_type_val){ ?>
			<option value="<?php echo $message_type_val;?>" <?php if($message_type_val==$sel_message_type){ ?> selected="selected" <?php } ?> ><?php echo $message_type_val;?></option>
		<?php }
	}
	?>
    </select>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding" style="padding:5px 2px;">
    <select class="form-control sel_sent_status" id="sel_sent_status">
    <option value="">All Status</option>
    <?php
    if(count($sent_status_arr)>0){
		foreach($sent_status_arr as $sent_status_val){ ?>
			<option value="<?php echo $sent_status_val;?>" <?php if($sent_status_val==$sel_sent_status){ ?> selected="selected" <?php } ?> ><?php echo $sent_status_val;?></option>
		<?php }
	}
	?>
    </select>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding" style="padding:5px 0px;">
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
                                            <th>Dealer&nbsp;Code</th>
                                            <th>Dealer&nbsp;Name</th>
                                            <th>Message&nbsp;Type</th>
                                            <th>Sent&nbsp;Status</th>
                                            <th>Error&nbsp;Message</th>
                                            <th>Device&nbsp;Type</th>
                                            <th>Message&nbsp;Text</th>
                                            <th>Sent&nbsp;ON</th>
                                        </tr>
                                    </thead>
                                     <tfoot>
                                        <tr>
                                            <th>Dealer&nbsp;Code</th>
                                            <th>Dealer&nbsp;Name</th>
                                            <th>Message&nbsp;Type</th>
                                            <th>Sent&nbsp;Status</th>
                                            <th>Error&nbsp;Message</th>
                                            <th>Device&nbsp;Type</th>
                                            <th>Message&nbsp;Text</th>
                                            <th>Sent&nbsp;ON</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select * FROM $table_name $new_whr_str order by `sent_datetime` desc LIMIT $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$anl_id = $row1["anl_id"];
		$cust_dns_code = $row1["cust_dns_code"];
		$cust_name = $row1["cust_name"];
		$message_type = $row1["message_type"];
		$message_sent_status = $row1["message_sent_status"];
		$error_message = $row1["error_message"];
		$device_type = $row1["device_type"];
		$message_text = $row1["message_text"];
		$sent_datetime = $row1["sent_datetime"];
		if($sent_datetime!=""){
		$sent_datetime = date("jS M Y h:i a",strtotime($sent_datetime));	
		}
?>
<tr>
<td><?php echo $cust_dns_code;?></td>
<td><?php echo $cust_name;?></td>
<td><?php echo $message_type;?></td>
<td><?php echo $message_sent_status;?></td>
<td><?php echo $error_message;?></td>
<td><?php echo $device_type;?></td>
<td><?php echo $message_text;?></td>
<td><?php echo $sent_datetime;?></td>
</tr>

<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="8">No data found.</td>
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
		var srch_dealer_dtls = jQuery.trim(jQuery("#srch_dealer_dtls").val());
		var sel_message_type = jQuery("#sel_message_type").val();
		var sel_sent_status = jQuery("#sel_sent_status").val();
		var sl_day_wise = jQuery("#sl_day_wise").val();
		var from_dt = jQuery("#from_dt").val();
		var to_dt = jQuery("#to_dt").val();
		var qstring ="";
		var dtstring ="";
		var amp = "";
			
			if(srch_dealer_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_dealer_dtls="+encodeURIComponent(srch_dealer_dtls);
			}else{
				qstring = qstring+"srch_dealer_dtls="+encodeURIComponent(srch_dealer_dtls);
			}
			}
			
			if(sel_message_type!=""){
			if(qstring!=""){
				qstring = qstring+"&sel_message_type="+encodeURIComponent(sel_message_type);
			}else{
				qstring = qstring+"sel_message_type="+encodeURIComponent(sel_message_type);
			}
			}
			if(sel_sent_status!=""){
			if(qstring!=""){
				qstring = qstring+"&sel_sent_status="+encodeURIComponent(sel_sent_status);
			}else{
				qstring = qstring+"sel_sent_status="+encodeURIComponent(sel_sent_status);
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
		
	});
	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "<?php echo $page_name;?>";
	});
});
</script>
<?php
include "web_footer.php";
?>
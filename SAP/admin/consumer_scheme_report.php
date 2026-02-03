<?php
ini_set('memory_limit', '999M');
set_time_limit(0);
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
$customer_master = "customer_master";
$consumer_scheme= "consumer_scheme";
$branch_master="branch_master";
$dnsbcarr = array();
$dnsbcstr ="";
$theactbcarr = array();
$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$new_qry_string_filtered = "";
$trn_branch_id = $_GET["trn_branch_id"] ? addslashes(trim($_GET["trn_branch_id"])) : "";
$sl_day_wise = $_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : "";
$from_dt = $_GET["from_dt"] ? addslashes(trim($_GET["from_dt"])) : "";
$to_dt = $_GET["to_dt"] ? addslashes(trim($_GET["to_dt"])) : "";
$srch_dtls = $_GET["srch_dtls"] ? addslashes(trim($_GET["srch_dtls"])) : "";
$whr_str = "";
$export_filtered_str = "";
$search_array = array("trn_branch_id"=>$trn_branch_id,"srch_dtls"=>$srch_dtls,"daywise"=>array("sl_day_wise"=>$sl_day_wise,"from_dt"=>$from_dt,"to_dt"=>$to_dt));
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand ($consumer_scheme.`customer_id` like '%$search_array_val%' or $consumer_scheme.`customer_name` like '%$search_array_val%'  or $customer_master.`customer_name` like '%$search_array_val%') ";
			$new_qry_string_filtered .= "&srch_dtls=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&srch_dtls=".$search_array_val;
			}else{
				$export_filtered_str .= "&srch_dtls=".$search_array_val;
			}	
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
			   $whr_str .= "$aand $consumer_scheme.`date_of_purchase` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
			   $new_qry_string_filtered .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}
			}else if($the_from_dt!="" && $the_to_dt==""){
				$whr_str .= "$aand $consumer_scheme.`date_of_purchase` >= '".$the_from_dt." ".$frm_hrs."' ";
				$new_qry_string_filtered .= "&from_dt=".$the_from_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}
			}else if($the_from_dt=="" && $the_to_dt!=""){
				$whr_str .= "$aand $consumer_scheme.`date_of_purchase` <= '".$the_to_dt." ".$to_hrs."' ";
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
				$whr_str .= "$aand $consumer_scheme.`date_of_purchase` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
			}else if($the_sl_day_wise=="Yesterday"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand $consumer_scheme.`date_of_purchase` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
			
			}
		}
	}
}
if($whr_str!=""){
	$new_whr_str = "and ".$whr_str;
}else{
	$new_whr_str ="";
}

$sqlftcbrnc = "select `branch_code`,`branch_name` from $branch_master order by `branch_name` asc";
$res1dftftcbrnc = mysql_query($sqlftcbrnc);
$totres1dftftcbrnc = mysql_num_rows($res1dftftcbrnc);

$page_name = "consumer_scheme_report.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/
$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;
/*---------PAGINATION RELATED CODE END----------*/
/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select $consumer_scheme.*,$customer_master.`customer_name`,$customer_master.`branch_code` from $consumer_scheme left join $customer_master on $consumer_scheme.`customer_id`=$customer_master.`customer_id` where $consumer_scheme.customer_name!=''  $new_whr_str ";
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

.table-container {
  height: 400px; /* Set the height of the container to limit the height of the table */
  overflow-y: auto; /* Enable vertical scrolling */
}

table {
  border-collapse: collapse;
  width: 100%;
}

th {
  background-color: #ddd;
  position: sticky; /* Make the table header fixed */
  top: 0;
}

th,
td {
  padding: 8px;
  text-align: left;
  border-bottom: 1px solid #ddd;
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
                          <h2>Consumer Scheme List (<?php echo $total_pgres;?>) &nbsp;&nbsp;&nbsp;<span class="rpt_loader"></span> &nbsp;&nbsp;<a href="export_consumer_scheme.php?get_type=all<?php echo $export_filtered_str;?>" class="btn bg-red waves-effe">Export&nbsp;Consumer&nbsp;Scheme</a></h2>
    <div class="row clearfix">
    
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_dtls" value="<?php echo $srch_dtls;?>" placeholder="Search Details">
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
                            <div class="table-wrap">
                            <div class="table-responsive table-container">  
                                <table class="table-bordered">
                                    <thead>
                                        <tr>
											<th>Dealer SAP code</th>
											<th>SFA Code</th>
											<th>Dealer name</th>
											<th>Linked Dealer code</th>
											<th>Linked Dealer name</th>
											<th>Branch name</th>
											<th>Region</th>
											<th>cust name</th>
											<th>Customer Type</th>
											<th>cust phn no</th>
											<th>Dhalai Master Qty</th>
                                            <th>Weather Shield Qty</th>
											<th>Date Of Purchase</th>
											<th>Lottery No</th>
                                            <th>Submit Date time</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                      <tr>
											<th>Dealer SAP code</th>
											<th>SFA Code</th>
											<th>Dealer name</th>
										  	<th>Linked Dealer code</th>
											<th>Linked Dealer name</th>
											<th>Branch name</th>
											<th>Region</th>
											<th>cust name</th>
											<th>Customer Type</th>
											<th>cust phn no</th>
											<th>Dhalai Master Qty</th>
                                            <th>Weather Shield Qty</th>
											<th>Date Of Purchase</th>
											<th>Lottery No</th>
                                            <th>Submit Date time</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select $consumer_scheme.*,$customer_master.`customer_name` As dealer_name,$customer_master.`branch_code`,$customer_master.rds_tag from $consumer_scheme left join $customer_master on $consumer_scheme.`customer_id`=$customer_master.`customer_id` where $consumer_scheme.customer_name!=''  $new_whr_str order by $consumer_scheme.`id` desc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
$the_sl_no = 1;
$the_sl_no = (($limit*($page-1))+1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$id = $row1["id"];
		$date_and_time = $row1["date_and_time"];
		$trans_id = $row1["trans_id"];
		$customer_id = $row1["customer_id"];
		$customer_name = $row1["customer_name"];
		$dealer_name = $row1["dealer_name"];
		$customer_phone_no = $row1["customer_phone_no"];
		$dhalai_master_qty = $row1["dhalai_master_qty"];
		$weather_shield_qty = $row1["weather_shield_qty"];
		$date_of_purchase = $row1["date_of_purchase"];
		$lottery_no = $row1["lottery_no"];
		$rds_tag = $row1["rds_tag"];
		
		$branch_code = $row1["branch_code"];
		$sqlftcbrncn = "select `branch_name` from $branch_master where branch_code='".$branch_code."'";
		$res1dftftcbrncn = mysql_query($sqlftcbrncn);
		$rowbranch=mysql_fetch_array($res1dftftcbrncn);
		$branch_name=$rowbranch['branch_name'];
		
		$sqllinkeddealer = "select customer_id,customer_name from $customer_master where customer_code='".$rds_tag."'";	
		$reslinkeddealer = mysql_query($sqllinkeddealer);
		$rowlinkeddealer= mysql_fetch_assoc($reslinkeddealer);
		$linked_dealer_code=$rowlinkeddealer['customer_id'];
		$linked_dealer_name=$rowlinkeddealer['customer_name'];
		
		$sqldealerdet = "select dns_customer_code,region,cust_type from $customer_master where customer_id='".$customer_id."'";	
		$resdealerdet = mysql_query($sqldealerdet);
		$rowdealerdet= mysql_fetch_assoc($resdealerdet);
		$dns_customer_code=$rowdealerdet['dns_customer_code'];
		$region=$rowdealerdet['region'];
		$cust_type=$rowdealerdet['cust_type'];
		
?>
<tr id="each_scheme_tr_<?php echo $sl_no;?>">
<td><?php echo $customer_id;?></td>
<td><?php echo $dns_customer_code;?></td>		
<td><?php echo $dealer_name;?></td>
<td><?php echo $linked_dealer_code;?></td>
<td><?php echo $linked_dealer_name;?></td>	
<td><?php echo $branch_name;?></td>	
<td><?php echo $region;?></td>
<td><?php echo $customer_name;?></td>
<td><?php echo $cust_type;?></td>	
<td><?php echo $customer_phone_no;?></td>
<td><?php echo $dhalai_master_qty;?></td>
<td><?php echo $weather_shield_qty;?></td>
<td><?php echo $date_of_purchase;?></td>	
<td><?php echo $lottery_no;?></td>
<td><?php echo $date_and_time;?></td>
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
		var srch_dtls = jQuery("#srch_dtls").val();
		var sl_day_wise = jQuery("#sl_day_wise").val();
		var from_dt = jQuery("#from_dt").val();
		var to_dt = jQuery("#to_dt").val();
		var qstring ="";
		var dtstring ="";
		var amp = "";
		if(trn_branch_id!="" || sl_day_wise!="" || srch_dtls!=""){
			if(trn_branch_id!=""){
		if(qstring==""){
		qstring = qstring+"trn_branch_id="+trn_branch_id;
		}else{
		qstring = qstring+"&trn_branch_id="+trn_branch_id;  
		}
		}
		if(srch_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_dtls="+srch_dtls;
			}else{
				qstring = qstring+"srch_dtls="+srch_dtls;
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
		window.location = "order_list_invoice.php";
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
<?php
include "web_check.php";
include "star_connection.php";
$employee_master = "employee_master";
$customer_master = "customer_master";
$employee_kyc_master = "employee_kyc_master";
$yellow_card_details = "yellow_card_details";
$branch_master = "branch_master";

$sqlftcbrnc = "select `branch_code`,`branch_name` from $branch_master order by `branch_name` asc";
$res1dftftcbrnc = mysql_query($sqlftcbrnc);
$totres1dftftcbrnc = mysql_num_rows($res1dftftcbrnc);

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

function show_product_data_from_prod_dns_code($the_prod_dns_code){
$product_dtls = array("prod_code"=>"","prod_desc"=>"");
$product_master = "product_master";
if($the_prod_dns_code!=""){
$sql1 = "select `prod_code`,`prod_desc` from $product_master where `dns_prod_code`='$the_prod_dns_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$prod_code = $row1["prod_code"] ? addslashes(trim($row1["prod_code"])) : "";
		$prod_desc = $row1["prod_desc"] ? addslashes(trim($row1["prod_desc"])) : "";
		$product_dtls = array("prod_code"=>$prod_code,"prod_desc"=>$prod_desc);
	}
}

return $product_dtls;
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

$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$trn_branch_id = $_GET["trn_branch_id"] ? addslashes(trim($_GET["trn_branch_id"])) : "";
$sl_day_wise = $_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : "";
$from_dt = $_GET["from_dt"] ? addslashes(trim($_GET["from_dt"])) : "";
$to_dt = $_GET["to_dt"] ? addslashes(trim($_GET["to_dt"])) : "";
$srch_dlr_dtls = $_GET["srch_dlr_dtls"] ? addslashes(trim($_GET["srch_dlr_dtls"])) : "";
$whr_str = "";
$new_qry_string_filtered = "";
$export_filtered_str = "";
$search_array = array("trn_branch_id"=>$trn_branch_id,"daywise"=>array("sl_day_wise"=>$sl_day_wise,"from_dt"=>$from_dt,"to_dt"=>$to_dt),"srch_dlr_dtls"=>$srch_dlr_dtls);
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_dlr_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($customer_master.`customer_code` like '%$search_array_val%' or $customer_master.`dns_customer_code` like '%$search_array_val%' or $customer_master.`customer_name` like '%$search_array_val%'  ) ";
			$new_qry_string_filtered .= "&srch_dlr_dtls=".$search_array_val;
		}
	}else if($search_array_key=="trn_branch_id"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand $customer_master.`branch_code`='$search_array_val' ";
			$new_qry_string_filtered .= "&trn_branch_id=".$search_array_val;
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
			   $whr_str .= "$aand $yellow_card_details.`challan_date` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
			   $new_qry_string_filtered .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}
			}else if($the_from_dt!="" && $the_to_dt==""){
				$whr_str .= "$aand $yellow_card_details.`challan_date` >= '".$the_from_dt." ".$frm_hrs."' ";
				$new_qry_string_filtered .= "&from_dt=".$the_from_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}
			}else if($the_from_dt=="" && $the_to_dt!=""){
				$whr_str .= "$aand $yellow_card_details.`challan_date` <= '".$the_to_dt." ".$to_hrs."' ";
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
				$whr_str .= "$aand $yellow_card_details.`challan_date` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
			}else if($the_sl_day_wise=="Yesterday"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand $yellow_card_details.`challan_date` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
			
			}
		}
	}
}

if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}
$add_page_name = "show_yellow_card_list.php";
$page_name = "show_yellow_card_list.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select $yellow_card_details.`yellow_card_no` from $yellow_card_details left join $customer_master on $yellow_card_details.`linked_dealer_code`=$customer_master.`customer_code` $new_whr_str";
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
.teEachField{
	display:block;
	width:150px;
	word-wrap: break-word;
	font-size: 12px;
}
.selected_date_dt,.qty_of_bags_input{
	text-align:center;
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
                          <h2>Yellow Card List (<?php echo $total_pgres;?>)&nbsp;&nbsp;
<a href="export_yellow_card_report.php?trn_branch_id=<?php echo $trn_branch_id;?>&sl_day_wise=<?php echo $sl_day_wise;?>&from_dt=<?php echo $from_dt;?>&to_dt=<?php echo $to_dt;?>&srch_dlr_dtls=<?php echo $srch_dlr_dtls;?>" class="btn bg-red waves-effe">Export&nbsp;Yellow&nbsp;Card&nbsp;List</a> <?php /*?>&nbsp; <a href="export_dealer.php?get_type=notloggedin" class="btn bg-red waves-effe">Export&nbsp;not&nbsp;loggedin&nbsp;dealer</a><?php */?>
                          </h2>

<div class="row clearfix">

<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
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
</div>
<div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
</div>
<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">

</div>
</div>

<div class="row clearfix">
<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_dlr_dtls" value="<?php echo $srch_dlr_dtls;?>" placeholder="Search Employee/Dealer Details">
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
                                            <th>Linked&nbsp;Dealer</th>
                                            <th>Sub&nbsp;Dealer&nbsp;Details</th>
                                            <th>Date</th>
                                            <th>Challan&nbsp;No.</th>
                                            <th>Qty&nbsp;Of&nbsp;Bags</th>
                                            <th>Product</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Linked&nbsp;Dealer</th>
                                            <th>Sub&nbsp;Dealer&nbsp;Details</th>
                                            <th>Date</th>
                                            <th>Challan&nbsp;No.</th>
                                            <th>Qty&nbsp;Of&nbsp;Bags</th>
                                            <th>Product</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select $yellow_card_details.*,$customer_master.`dns_customer_code`,$customer_master.`customer_name`,$customer_master.`branch_code` from $yellow_card_details left join $customer_master on $yellow_card_details.`linked_dealer_code`=$customer_master.`customer_code` $new_whr_str order by $yellow_card_details.`yellow_card_no` desc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$yellow_card_no = $row1["yellow_card_no"];
		$yellow_card_no_for_ajx = str_replace("/","_",$yellow_card_no);
		$emp_code = $row1["linked_dealer_code"];
		$dns_emp_code = $row1["dns_customer_code"];
		$emp_name = $row1["customer_name"];
		$emp_branch_code = trim($row1["branch_code"]);
		$emp_branch_name = get_branch_name_from_id($emp_branch_code);

$sub_dealer_code = $row1["customer_code"];
$sub_dealer_name = get_customer_name_from_id($sub_dealer_code);
$selected_date = $row1["challan_date"];
$challan_no = $row1["challan_no"];
$qty_of_bags = $row1["qty"];
$prod_code = $row1["qty_UOM"];
$prod_dtld = show_product_data_from_prod_dns_code($prod_code);
$prod_display_name = $prod_dtld["prod_desc"];


?>
<tr>
<td>
<div>
<?php if($emp_name!=""){echo '<span class="teEachField">'.$emp_name.'</span>';}?>
<?php if($dns_emp_code!=""){echo '<span class="teEachField"><b>Dealer ID:</b> '.$dns_emp_code.'</span>';}?>
<?php if($emp_code!=""){echo '<span class="teEachField"><b>Cust Code:</b> '.$emp_code.'</span>';}?>
<?php if($emp_branch_name!=""){echo '<span class="teEachField"><b>Branch:</b> '.$emp_branch_name.'</span>';}?>
</div>
</td>
<td><?php echo $sub_dealer_name;?></td>
<td>
<input type="text" class="datepicker form-control selected_date_dt" id="selected_date_<?php echo $yellow_card_no_for_ajx;?>" value="<?php echo $selected_date;?>" placeholder="Choose date">
</td>
<td><?php echo $challan_no;?></td>
<td>
<input type="text" class="form-control qty_of_bags_input" id="qty_of_bags_<?php echo $yellow_card_no_for_ajx;?>" value="<?php echo $qty_of_bags;?>" placeholder="Qty Of Bags">
</td>
<td><?php echo $prod_display_name;?></td>
<td>
<a href="javascript:void(0);" class="btn bg-red waves-effect sts_upd_btn" the_yellow_card_no="<?php echo $yellow_card_no_for_ajx;?>">Update</a>
<span class="sts_change_ldr" id="sts_change_ldr_<?php echo $yellow_card_no_for_ajx;?>"></span>
</td>
</tr>

<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="7">No data found.</td>
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

var img_for_loader = '<img style="min-width:16px;width:16px;height:16px;margin:0 auto;" src="images/ajax-loader.gif">';
var success_tik = '<img style="min-width:16px;width:16px;height:16px;margin:0 auto;" src="images/success_tick.png">';
	
jQuery('#from_dt').bootstrapMaterialDatePicker({ weekStart : 0, time: false });
jQuery('#to_dt').bootstrapMaterialDatePicker({ weekStart : 0, time: false });

jQuery('.selected_date_dt').bootstrapMaterialDatePicker({ weekStart : 0, time: false });

jQuery(".sts_upd_btn").click(function(){
	var the_yellow_card_no = jQuery(this).attr("the_yellow_card_no");
	if(the_yellow_card_no!=""){
		var ldr_elmnt = jQuery("#sts_change_ldr_"+the_yellow_card_no);
		var the_selected_date_elmnt = jQuery("#selected_date_"+the_yellow_card_no);
		var the_qty_of_bags_elmnt = jQuery("#qty_of_bags_"+the_yellow_card_no);
		var the_selected_date = the_selected_date_elmnt.val();
		var the_qty_of_bags = encodeURIComponent(jQuery.trim(the_qty_of_bags_elmnt.val()));
		if(the_qty_of_bags==0){
			the_qty_of_bags = "";
		}
if(the_selected_date!="" && the_qty_of_bags!=""){
ldr_elmnt.html(img_for_loader);
jQuery.ajax({
url: 'ajax_update_yellow_card_details_by_admin.php',
type: 'post',
dataType: 'json',
data: "the_yellow_card_no="+the_yellow_card_no+"&the_selected_date="+the_selected_date+"&the_qty_of_bags="+the_qty_of_bags,
success: function(response){
if(response.process_sts=="YES"){
ldr_elmnt.html(success_tik);
setTimeout(function(){
ldr_elmnt.html("");
},5000);
}else{
ldr_elmnt.html("");
alert(response.process_msg);	
}
},
timeout : 0
});
}
	
	}
});

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
		var sl_day_wise = jQuery("#sl_day_wise").val();
		var from_dt = jQuery("#from_dt").val();
		var to_dt = jQuery("#to_dt").val();
		var srch_dlr_dtls = jQuery("#srch_dlr_dtls").val();
		var qstring ="";
		var dtstring ="";
		var amp = "";
		if(trn_branch_id!="" || sl_day_wise!="" || srch_dlr_dtls!=""){
			if(trn_branch_id!=""){
			if(qstring==""){
			qstring = qstring+"trn_branch_id="+trn_branch_id;
			}else{
			qstring = qstring+"&trn_branch_id="+trn_branch_id;  
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
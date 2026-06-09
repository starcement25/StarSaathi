<?php
include "web_check.php";
include "star_connection.php";
$table_name = "customer_master";
$branch_master = "branch_master";
$customer_destination = "customer_destination";
$destination_master = "destination_master";
$employee_master = "employee_master";
$customer_route_emp_relation = "customer_route_emp_relation";

$or_sts_arr = array();
$or_sts_arr[] = array("key_val"=>"yes","title_val"=>"yes");
$or_sts_arr[] = array("key_val"=>"no","title_val"=>"no");
function get_branch_name_from_id($branch_id){
	$branch_master = "branch_master";
	$branch_name = "";
	$branch_id = $branch_id ? addslashes(trim($branch_id)) : "";
	if($branch_id!=''){
		$sqls = "select `branch_name` from $branch_master where `branch_code`='$branch_id'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if($totress>0){
			$rows = mysql_fetch_assoc($ress);
			$branch_name = $rows["branch_name"] ? trim($rows["branch_name"]) : "";
		}
	}
	return $branch_name;
}
function get_branch_code_from_emp_master($dns_customer_code){
	$employee_master = "employee_master";
	$branch_code = "";
	$dns_customer_code = $dns_customer_code ? addslashes(trim($dns_customer_code)) : "";
	if($dns_customer_code!=''){
		$sqls = "select `branch_code` from $employee_master where `dns_emp_code`='$dns_customer_code'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if($totress>0){
			$rows = mysql_fetch_assoc($ress);
			$branch_code = $rows["branch_code"] ? trim($rows["branch_code"]) : "";
		}
	}
	return $branch_code;
}
$new_qry_string_filtered = "";
$sl_branch = $_GET["sl_branch"] ? addslashes(trim($_GET["sl_branch"])) : "";
$srch_cust_dtls = $_GET["srch_cust_dtls"] ? addslashes(trim($_GET["srch_cust_dtls"])) : "";
$sl_cust_type = $_GET["sl_cust_type"] ? addslashes(trim($_GET["sl_cust_type"])) : "";
$whr_str = "";
$search_array = array("sl_branch"=>$sl_branch,"srch_cust_dtls"=>$srch_cust_dtls,"sl_cust_type"=>$sl_cust_type,"sl_dlr_actdat"=>"Y","not_show_cust_type"=>"Non Star");
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="sl_branch"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $table_name.`branch_code`='$search_array_val' ";
			$new_qry_string_filtered .= "&sl_branch=".$search_array_val;
		}
	}else if($search_array_key=="srch_cust_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand ($table_name.`dns_customer_code` like '%$search_array_val%' or $table_name.`customer_name` like '%$search_array_val%' or $table_name.`phone_no` like '%$search_array_val%' or $table_name.`customer_id` like '%$search_array_val%' or $destination_master.dns_destination_code like '%$search_array_val%') ";
			$new_qry_string_filtered .= "&srch_cust_dtls=".$search_array_val;
		}
	}else if($search_array_key=="sl_cust_type"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $table_name.`cust_type`='$search_array_val' ";
			$new_qry_string_filtered .= "&sl_cust_type=".$search_array_val;		
		}		
	}else if($search_array_key=="not_show_cust_type"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $table_name.`cust_type`!='$search_array_val' ";	
		}		
	}else if($search_array_key=="sl_dlr_actdat"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $table_name.`acedns`='$search_array_val '";	
			$new_qry_string_filtered .= "&sl_dlr_actdat=".$search_array_val;		
		}		
	}
}

if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}

$sql22 = "select `branch_code`,`branch_name` from $branch_master where `acedns`='Y' order by `branch_name`";
$res22 = mysql_query($sql22);
$totres22 = mysql_num_rows($res22);

$page_name = "customer_list.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select $table_name.`customer_code` from $table_name 
left join $branch_master on $table_name.`branch_code`=$branch_master.`branch_code`
left JOIN  $customer_destination ON $customer_destination.customer_code=$table_name.customer_code
left JOIN  $destination_master ON $destination_master.destination_code=$customer_destination.destination_code $new_whr_str";
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
                          <h2>Customer List (<?php echo $total_pgres;?>)&nbsp; <a href="export_customer.php?get_type=all" class="btn bg-red waves-effe">Export&nbsp;all&nbsp;customer</a> &nbsp; <a href="export_customer.php?get_type=dealer" class="btn bg-red waves-effe">Export&nbsp;Dealer</a> &nbsp; <a href="export_customer.php?get_type=subdealer" class="btn bg-red waves-effe">Export&nbsp;Sub-Dealer</a></h2>

<div class="row clearfix">
<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control" id="sl_branch">
<option value="">Select Branch</option>
<?php
if($totres22>0){
	while($row22=mysql_fetch_assoc($res22)){
		$the_branch_code = $row22["branch_code"];
		$the_branch_name = $row22["branch_name"];
		?>
 <option value="<?php echo $the_branch_code;?>" <?php if($the_branch_code==$sl_branch){?> selected="selected" <?php } ?>><?php echo $the_branch_name;?></option>
        <?php
	}
}
?>
</select>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_cust_dtls" value="<?php echo $srch_cust_dtls;?>" placeholder="Search Customer Details">
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control" id="sl_cust_type" style="padding-left:2px;">
<option value="">Select Cust Type</option>
<option value="Dealer" <?php if($sl_cust_type=="Dealer"){?> selected="selected" <?php } ?>>Dealer</option>
<option value="Sub Dealer" <?php if($sl_cust_type=="Sub Dealer"){?> selected="selected" <?php } ?>>Sub Dealer</option>
<option value="RSSD" <?php if($sl_cust_type=="RSSD"){?> selected="selected" <?php } ?>>RSSD</option>
<option value="Ship to Party-dealer" <?php if($sl_cust_type=="Ship to Party-dealer"){?> selected="selected" <?php } ?>>Ship to Party-dealer</option>
<option value="ShiptoParty-Subdeale" <?php if($sl_cust_type=="ShiptoParty-Subdeale"){?> selected="selected" <?php } ?>>ShiptoParty-Subdeale</option>
</select>
    </div>
    
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_btn" >Search</button>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_reset_btn" >Reset</button>
    </div>
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">

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
											<th>Mapped&nbsp;Employee&nbsp;Code</th>
											<th>Mapped&nbsp;Employee&nbsp;Name</th>
                                            <th>Customer&nbsp;Code</th>
                                            <th>SAP&nbsp;Code</th>
                                            <th>Customer&nbsp;Name</th>
                                            <th>Phone</th>
                                            <th>Whatsapp no</th>
                                            <th>Email</th>
                                            <th>Branch&nbsp;Code</th>
                                            <th>Branch&nbsp;Name</th>
                                            <th>Customer&nbsp;Type</th>
                                            <th>Linked&nbsp;Dealer&nbsp;Code</th>
                                            <th>Linked&nbsp;Dealer&nbsp;Code</th>
                                            <th>Destination Code</th>
                                            <th>Destination Name</th>
                                            <th>Region</th>
											<th>OTP</th>
											<th>Active&nbsp;(Y/N)</th>
                                            <th>Order Restriction</th>
											
                                        </tr>
                                    </thead>
                                    <tfoot>
                                      <tr>
									 		<th>Mapped&nbsp;Employee&nbsp;Code</th>
											<th>Mapped&nbsp;Employee&nbsp;Name</th>
                                            <th>Customer&nbsp;Code</th>
                                            <th>SAP&nbsp;Code</th>
                                            <th>Customer&nbsp;Name</th>
                                            <th>Phone</th>
                                            <th>Whatsapp no</th>
                                            <th>Email</th>
                                            <th>Branch&nbsp;Code</th>
                                            <th>Branch&nbsp;Name</th>
                                            <th>Customer&nbsp;Type</th>
                                            <th>Linked&nbsp;Dealer&nbsp;Code</th>
                                            <th>Linked&nbsp;Dealer&nbsp;Code</th>
                                             <th>Destination Code</th>
                                            <th>Destination Name</th>
                                            <th>Region</th>
											<th>OTP</th>
                                            <th>Active&nbsp;(Y/N)</th>
                                            <th>Order Restriction</th>
											
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select $table_name.*,$branch_master.`branch_name`,$destination_master.dns_destination_code,$destination_master.destination_name from $table_name 
		left join $branch_master on $table_name.`branch_code`=$branch_master.`branch_code`
		left JOIN  $customer_destination ON $customer_destination.customer_code=$table_name.customer_code
		left JOIN  $destination_master ON $destination_master.destination_code=$customer_destination.destination_code
		$new_whr_str order by $table_name.`dns_customer_code` asc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		
		//$the_branch_name = get_branch_name_from_id($branch_code);
		$dns_customer_code = $row1["dns_customer_code"];
		$customer_id = $row1["customer_id"];
		$customer_name = $row1["customer_name"];
		$branch_code = $row1["branch_code"];
		$branch_name = $row1["branch_name"];
		$phone_no = $row1["phone_no"];
		$email = $row1["email"];
		$whatsapp_no = $row1["whatsapp_no"];
		$route_code = $row1["route_code"];
		$cust_type = $row1["cust_type"];
		$acedns = $row1["acedns"];
		$current_balance = $row1["current_balance"];
		$credit_limit = $row1["credit_limit"];
		$credit_days = $row1["credit_days"];
		$rds_tag = $row1["rds_tag"];
		$customer_code = $row1["customer_code"];
		$region = $row1["region"];
		$dns_destination_code = $row1["dns_destination_code"];
		$destination_name = $row1["destination_name"];
		$order_restriction  = $row1["order_restriction"];
		$sms_otp  = $row1["sms_otp"];
		
		$sqllinkeddealer="SELECT customer_id,customer_name FROM customer_master WHERE customer_code='".$rds_tag."'";
		$reslinkeddealer = mysql_query($sqllinkeddealer);
		$rowlinkeddealer=mysql_fetch_assoc($reslinkeddealer);
		$linked_dealer_code=$rowlinkeddealer['customer_id'];
		$linked_dealer_name=$rowlinkeddealer['customer_name'];

		$sqlempmapping="SELECT EM.dns_emp_code,EM.emp_name FROM `customer_route_emp_relation` CRR, employee_master EM WHERE CRR.emp_code=EM.emp_code AND CRR.customer_code='".$customer_id."' and CRR.acedns='Y'  ORDER BY EM.emp_name ASC";
		$resmapping = mysql_query($sqlempmapping);
		$totresmapping = mysql_num_rows($resmapping);
		$mapped_emp_name='';
		$mapped_emp_code='';
		if($totresmapping >0){
			while($rowmapping=mysql_fetch_assoc($resmapping)){
				$mapped_emp_name=$mapped_emp_name.$rowmapping['emp_name'].',';
				$mapped_emp_code=$mapped_emp_code.$rowmapping['dns_emp_code'].',';
			}
			$mapped_emp_name=substr($mapped_emp_name,0,-1);
			$mapped_emp_code=substr($mapped_emp_code,0,-1);
		}
?>
<tr>
<td><?php echo $mapped_emp_code;?></td>
<td><?php echo $mapped_emp_name;?></td>
<td><?php echo $dns_customer_code;?></td>
<td><?php echo $customer_id;?></td>
<td><?php echo $customer_name;?></td>
<td><?php echo $phone_no;?></td>
<td><?php echo $whatsapp_no;?></td>
<td><?php echo $email;?></td>
<td><?php echo $branch_code;?></td>
<td><?php echo $branch_name;?></td>
<td><?php echo $cust_type;?></td>
<td><?php echo $linked_dealer_code;?></td>
<td><?php echo $linked_dealer_name;?></td>
<td><?php echo $dns_destination_code;?></td>
<td><?php echo $destination_name;?></td>
<td><?php echo $region;?></td>
<td><?php echo $sms_otp;?></td>
<td><?php echo $acedns;?></td>
<td style="position:relative;">
<select class="form-control order_restrict" id="order_restrict_<?php echo $customer_id;?>" the_customer_code="<?php echo $customer_id;?>">
<?php
if(count($or_sts_arr)>0){
	foreach($or_sts_arr as $or_sts_arr_val){
		$the_key_val = $or_sts_arr_val["key_val"];
		$the_title_val = $or_sts_arr_val["title_val"]; ?>
     <option value="<?php echo $the_key_val;?>" <?php if($the_key_val==$order_restriction){?> selected="selected" <?php } ?>><?php echo $the_title_val;?></option>
        <?php
	}
	
}
?>
</select>

<span class="os_ldr" id="ca_ldr_<?php echo $customer_id;?>"></span></td>
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

	jQuery(".order_restrict").change(function(){
		var ancr_elmnt = jQuery(this);
		var the_status = ancr_elmnt.val();
		var the_customer_code = ancr_elmnt.attr("the_customer_code");
		if(the_customer_code!=""){
			var for_loader = jQuery("#ca_ldr_"+the_customer_code);
			for_loader.html(imgs);
			jQuery.ajax({
			url: 'ajax_order_restrict_update.php',
			type: 'post',
			dataType: "JSON",
			data: "the_customer_code="+the_customer_code+"&the_status="+the_status,
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
		var sl_branch = jQuery("#sl_branch").val();
		var srch_cust_dtls = jQuery("#srch_cust_dtls").val();
		var sl_cust_type = jQuery("#sl_cust_type").val();
		var qstring ="";
		var amp = "";
		if(sl_branch!="" || srch_cust_dtls!="" || sl_cust_type!=""){
		if(sl_branch!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_branch="+encodeURIComponent(sl_branch);
			}else{
				qstring = qstring+"sl_branch="+encodeURIComponent(sl_branch);
			}
		}
		if(srch_cust_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_cust_dtls="+encodeURIComponent(srch_cust_dtls);
			}else{
				qstring = qstring+"srch_cust_dtls="+encodeURIComponent(srch_cust_dtls);
			}
		}
		if(sl_cust_type!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_cust_type="+sl_cust_type;
			}else{
				qstring = qstring+"sl_cust_type="+sl_cust_type;
			}
		}
		
		  if(qstring!=""){
			 qstring = "?"+qstring; 
		  }
		window.location = "customer_list.php"+qstring;
		}else{
			alert("Please select atleast one field to search.");
		}
	});
	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "customer_list.php";
	});
});
</script>
<?php
include "web_footer.php";
mysql_close();
?>
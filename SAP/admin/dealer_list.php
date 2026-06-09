<?php
include "web_check.php";
include "star_connection.php";
$table_name = "employee_master";
$customer_master = "customer_master";
$changepassword = "changepassword";
$profile_image_dir = "../profile_image/";
$new_qry_string_filtered = "";
$srch_dlr_dtls = $_GET["srch_dlr_dtls"] ? addslashes(trim($_GET["srch_dlr_dtls"])) : "";
$sl_device_type = $_GET["sl_device_type"] ? addslashes(trim($_GET["sl_device_type"])) : "";
$sl_dlr_actdat = $_GET["sl_dlr_actdat"] ? addslashes(trim($_GET["sl_dlr_actdat"])) : "";
$sl_dlr_alocated_type = $_GET["sl_dlr_alocated_type"] ? addslashes(trim($_GET["sl_dlr_alocated_type"])) : "";
$sl_dlr_logedin_type = $_GET["sl_dlr_logedin_type"] ? addslashes(trim($_GET["sl_dlr_logedin_type"])) : "";
$whr_str = "";
$search_array = array("srch_dlr_dtls"=>$srch_dlr_dtls,"sl_device_type"=>$sl_device_type,"sl_dlr_actdat"=>$sl_dlr_actdat,"sl_cust_type"=>"Dealer","sl_dlr_alocated_type"=>$sl_dlr_alocated_type,"sl_dlr_logedin_type"=>$sl_dlr_logedin_type);
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_dlr_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($customer_master.`customer_id` like '%$search_array_val%' or $customer_master.`dns_customer_code` like '%$search_array_val%' or $customer_master.`customer_name` like '%$search_array_val%' or $customer_master.`phone_no` like '%$search_array_val%' or $customer_master.`customer_id` like '%$search_array_val%') ";
			$new_qry_string_filtered .= "&srch_dlr_dtls=".$search_array_val;
		}
	}else if($search_array_key=="sl_device_type"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $changepassword.`device_type`='$search_array_val' ";	
			$new_qry_string_filtered .= "&sl_device_type=".$search_array_val;		
		}		
	}else if($search_array_key=="sl_dlr_actdat"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $customer_master.`acedns`='$search_array_val' ";	
			$new_qry_string_filtered .= "&sl_dlr_actdat=".$search_array_val;		
		}		
	}else if($search_array_key=="sl_cust_type"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $customer_master.`cust_type`='$search_array_val' ";	
			$new_qry_string_filtered .= "&sl_cust_type=".$search_array_val;		
		}		
	}else if($search_array_key=="sl_dlr_alocated_type"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			if($search_array_val=="Allocated"){
				$whr_str .= "$aand ($changepassword.`deviceid`!='' and $changepassword.`deviceid` is not null) ";
			}else if($search_array_val=="Not-Allocated"){
				$whr_str .= "$aand ($changepassword.`deviceid`='' or $changepassword.`deviceid` is null) ";
			}				
			$new_qry_string_filtered .= "&sl_dlr_alocated_type=".$search_array_val;		
		}		
	}else if($search_array_key=="sl_dlr_logedin_type"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			if($search_array_val=='loggedin'){
			$whr_str .= "$aand ($changepassword.`deviceid`!='' and $changepassword.`deviceid` is not null) ";	
			}else if($search_array_val=='notloggedin'){
			$whr_str .= "$aand ($changepassword.`deviceid`='' or $changepassword.`deviceid` is null) ";	
			}
			$new_qry_string_filtered .= "&sl_dlr_logedin_type=".$search_array_val;
			
		}		
	}
}
if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}
$add_page_name = "edit_dealer_list.php";
$page_name = "dealer_list.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/
$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;
/*---------PAGINATION RELATED CODE END----------*/
/*---------PAGINATION RELATED CODE START----------*/
//sk add condition Dealers will start with 10, RSSD will start with 15, shiptopartydealer will start with 14 & shiptopartysubdealer will start with 14

$con="AND
(
    (customer_master.`cust_type` = 'Dealer' AND customer_master.`customer_id` LIKE '10%')
    OR
    (customer_master.`cust_type` = 'RSSD' AND customer_master.`customer_id` LIKE '15%')
    OR
    
    (customer_master.`cust_type` = 'Ship to Party-dealer' AND customer_master.`customer_id` LIKE '14%')
    OR
    (customer_master.`cust_type` = 'ShiptoParty-Subdeale' AND customer_master.`customer_id` LIKE '14%')
)  ";


$pgsql = "select $customer_master.`customer_code`,$changepassword.`deviceid` from $customer_master left join $changepassword on $customer_master.`customer_code`=$changepassword.`customer_code` $new_whr_str $con";
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
                          <h2>Dealer List (<?php echo $total_pgres;?>)&nbsp;&nbsp;
                          <a href="export_dealer.php?get_type=loggedin" class="btn bg-red waves-effe">Export&nbsp;loggedin&nbsp;dealer</a> &nbsp; <a href="export_dealer.php?get_type=notloggedin" class="btn bg-red waves-effe">Export&nbsp;not&nbsp;loggedin&nbsp;dealer</a>&nbsp;<a href="exclusive_dealer_cutoff.php?for_year=<?php echo date('Y'); ?>" class="btn bg-red waves-effe">Exclusive&nbsp;Dealers&nbsp;Cutoff&nbsp;Date</a>&nbsp;
                          <input type="button" class="btn bg-red waves-effect sync_btn" name="sync_sap"  id="sync_sap" value="SYNC SAP" />&nbsp;<font color="#FF0000"><b>( Auto SYNC Time 10 AM , 2:30 PM , 5:30 PM , 7:30 PM and 10:30 PM )</b></font> <span class="each_mk_cncl_span" id="sync_msg" style="margin-top:4px;"></span>
                          </h2>
                            <div class="row clearfix">
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_dlr_dtls" value="<?php echo $srch_dlr_dtls;?>" placeholder="Search Dealer Details">
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
    <select class="form-control" id="sl_device_type">
<option value="">All Device Type</option>
<option value="ANDROID" <?php if($sl_device_type=="ANDROID"){?> selected="selected" <?php } ?>>ANDROID</option>
<option value="IOS" <?php if($sl_device_type=="IOS"){?> selected="selected" <?php } ?>>IOS</option>
</select>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <select class="form-control" id="sl_dlr_actdat">
<option value="">Dealer Status</option>
<option value="Y" <?php if($sl_dlr_actdat=="Y"){?> selected="selected" <?php } ?>>Y</option>
<option value="N" <?php if($sl_dlr_actdat=="N"){?> selected="selected" <?php } ?>>N</option>
</select>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control" id="sl_dlr_alocated_type">
<option value="">Allocation Status</option>
<option value="Allocated" <?php if($sl_dlr_alocated_type=="Allocated"){?> selected="selected" <?php } ?>>Allocated</option>
<option value="Not-Allocated" <?php if($sl_dlr_alocated_type=="Not-Allocated"){?> selected="selected" <?php } ?>>Not-Allocated</option>
</select>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control" id="sl_dlr_logedin_type">
<option value="">Login Status</option>
<option value="loggedin" <?php if($sl_dlr_logedin_type=="loggedin"){?> selected="selected" <?php } ?>>Logged in</option>
<option value="notloggedin" <?php if($sl_dlr_logedin_type=="notloggedin"){?> selected="selected" <?php } ?>>Not Logged in</option>
</select>
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
                                            <th>Dealer&nbsp;Image</th>
                                            <th>Dealer&nbsp;ID</th>
                                            <th>SAP&nbsp;Code</th>
                                            <th>Dealer&nbsp;Name</th>
                                            <th>Phone</th>
											<th>DOB</th>
											<th>Mapped Employee Code</th>
											<th>Mapped Employee Name</th>
                                            <th>Display Balances</th>
                                            <th>Active&nbsp;Status</th>
                                            <th>Device&nbsp;type</th>
                                            <th>App&nbsp;Version</th>
                                            <th>Allocation&nbsp;Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Dealer&nbsp;Image</th>
                                            <th>Dealer&nbsp;ID</th>
                                            <th>SAP&nbsp;Code</th>
                                            <th>Dealer&nbsp;Name</th>
                                            <th>Phone</th>
											<th>DOB</th>
											<th>Mapped Employee Code</th>
											<th>Mapped Employee Name</th>
                                            <th>Display Balances</th>
                                            <th>Active&nbsp;Status</th>
                                            <th>Device&nbsp;type</th>
                                            <th>App&nbsp;Version</th>
                                            <th>Allocation&nbsp;Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select $customer_master.*,$changepassword.`deviceid`,$changepassword.`app_version`,$changepassword.`device_type` from $customer_master left join $changepassword on $customer_master.`customer_code`=$changepassword.`customer_code` $new_whr_str $con order by $customer_master.`customer_name` asc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$emp_code = $row1["customer_code"];
		$dns_emp_code = $row1["dns_customer_code"];
		$customer_id = $row1["customer_id"];
		$emp_name = $row1["customer_name"];
		$acedns = $row1["acedns"];
		$phone_no = $row1["phone_no"];
		$email = $row1["email"];
		$whatsapp_no = $row1["whatsapp_no"];
		$deviceid = $row1["deviceid"] ? trim($row1["deviceid"]) : "";
		$device_type = $row1["device_type"] ? trim($row1["device_type"]) : "";
		$app_version = $row1["app_version"];
		$the_profile_image_link = "";
		$profile_image = $row1["profile_image"] ? trim($row1["profile_image"]) : "";
		$DOB = $row1["DOB"] ? trim($row1["DOB"]) : "";
		$date='';
		if($DOB !=''AND $DOB !='00000000'){
			$date=date("Y-m-d", strtotime($DOB));

		}
		if($profile_image!=""){
			if(file_exists($profile_image_dir.$profile_image)){
				$the_profile_image_link = $profile_image_dir.$profile_image;
			}
		}
		
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
<td>
<?php if($the_profile_image_link!=""){ ?>
<img src="<?php echo $the_profile_image_link;?>" class="dlr_prfl_img" />
<?php } ?>
</td>
<td><?php echo $dns_emp_code;?></td>
<td><?php echo $customer_id;?></td>
<td><?php echo $emp_name;?></td>
<td><?php echo $phone_no;?></td>
<td><?php echo $date;?></td>
	<td><?php echo $mapped_emp_code;?></td>
	<td><?php echo $mapped_emp_name;?></td>
<td><span class="each_mk_save_span">
<a href="javascript:void(0);" class="btn bg-red display_balnace" customer_id="<?php echo $customer_id;?>" >Display&nbsp;Balances</a>
</span>
<br />
<div  id="dislay_balance_<?php echo $customer_id;?>">
</div>
</td>
<td><?php echo $acedns;?></td>
<td><?php echo $device_type;?></td>
<td><?php echo $app_version;?></td>
<td style="text-align:center;"><?php
if($deviceid!=""){
?>
<a href="javascript:void(0);" class="clemply" clemplyid="<?php echo $emp_code;?>" the_dns_emp_code="<?php echo $dns_emp_code;?>">Clear Allocation</a>
<span class="os_ldr" id="ca_ldr_<?php echo $dns_emp_code;?>"></span>
<?php
}else{
	echo '--';
}
?></td>
<td><a href="<?php echo "exclusive_dealer_cutoff_date.php?theempid=".$emp_code."&paged=".$page?>" class="btn bg-green waves-effect">Exclusive Cutoff</a><a href="<?php echo $add_page_name."?theempid=".$emp_code."&paged=".$page?>" class="btn bg-red waves-effect">Edit</a></td>
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
	
	jQuery(".clemply").click(function(){
		var ancr_elmnt = jQuery(this);
		var clemplyid = ancr_elmnt.attr("clemplyid");
		var the_dns_emp_code = ancr_elmnt.attr("the_dns_emp_code");
		if(clemplyid!=""){
			var theldrid = "ca_ldr_"+the_dns_emp_code;
			var for_loader = jQuery("#"+theldrid);
			for_loader.html(imgs);
			jQuery.ajax({
			url: 'ajax_clear_allocation_by_emp_id.php',
			type: 'post',
			dataType: "JSON",
			data: "clemplyid="+clemplyid,
			success: function(response){
			if(response.process_status=="YES"){
				ancr_elmnt.html("--");
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
		var sl_device_type = jQuery("#sl_device_type").val();	
		var sl_dlr_actdat = jQuery("#sl_dlr_actdat").val();	
		var sl_dlr_alocated_type = jQuery("#sl_dlr_alocated_type").val();
		var sl_dlr_logedin_type = jQuery("#sl_dlr_logedin_type").val();	
		var qstring ="";
		var amp = "";
		if(srch_dlr_dtls!="" || sl_device_type!=""  || sl_dlr_actdat!="" || sl_dlr_alocated_type!="" || sl_dlr_logedin_type!=""){
		if(srch_dlr_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}else{
				qstring = qstring+"srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}
		}
		if(sl_device_type!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_device_type="+encodeURIComponent(sl_device_type);
			}else{
				qstring = qstring+"sl_device_type="+encodeURIComponent(sl_device_type);
			}
		}
		if(sl_dlr_actdat!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_dlr_actdat="+sl_dlr_actdat;
			}else{
				qstring = qstring+"sl_dlr_actdat="+sl_dlr_actdat;
			}
		}
		if(sl_dlr_alocated_type!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_dlr_alocated_type="+sl_dlr_alocated_type;
			}else{
				qstring = qstring+"sl_dlr_alocated_type="+sl_dlr_alocated_type;
			}
		}
		
		if(sl_dlr_logedin_type!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_dlr_logedin_type="+sl_dlr_logedin_type;
			}else{
				qstring = qstring+"sl_dlr_logedin_type="+sl_dlr_logedin_type;
			}
		}
		
		  if(qstring!=""){
			 qstring = "?"+qstring; 
		  }
		window.location = "dealer_list.php"+qstring;
		}else{
			alert("Please select atleast one field to search.");
		}
	});
	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "dealer_list.php";
	});
	jQuery("#sync_sap").click(function(){
		var sync_msg_elmnt = jQuery("#sync_msg");
		sync_msg_elmnt.html(imgs);
		jQuery.ajax({
		url: 'http://starsaathi.com/SAP/uploaddata-SAP-STARSAATHI.php',
		type: 'post',
		dataType: 'json',
		data: '',
		success: function(response){				
		if(response.process_sts=="YES"){
		sync_msg_elmnt.html(done_img);
		window.location = "dealer_list.php";
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
	jQuery('.display_balnace').click(function(){
		var customer_id = jQuery(this).attr("customer_id");
		if(customer_id!=''){
			var ldr_elmnt = jQuery("#dislay_balance_"+customer_id);
			ldr_elmnt.html(imgs);
			jQuery.ajax({
			url: 'display_balances.php',
			type: 'post',
			dataType: 'json',
			data: "customer_id="+customer_id,
			success: function(response){
			if(response.process_sts=="YES"){
				ldr_elmnt.html(response.process_msg);
			}else{
				ldr_elmnt.html("No Data");				
			}						
			}
			});
		}
	
	});
});
</script>
<?php
include "web_footer.php";
mysql_close();
?>
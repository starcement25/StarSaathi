<?php
include "web_check.php";
include "star_connection.php";
$customer_master = "customer_master";
$customer_destination = "customer_destination";
$destination_master = "destination_master";
$broker_master = "broker_master";
$sp_destination = "sp_destination";
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
$whr_str .= "$aand ($customer_master.`dns_customer_code` like '%$search_array_val%' or $customer_master.`customer_name` like '%$search_array_val%' or $customer_master.`phone_no` like '%$search_array_val%' ) ";
			$new_qry_string_filtered .= "&srch_dlr_dtls=".$search_array_val;
		}
	}
}
if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}
$add_page_name = "sp_destination_list.php";
$page_name = "sp_destination_list.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/
$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;
/*---------PAGINATION RELATED CODE END----------*/
/*---------PAGINATION RELATED CODE START----------*/


$pgsql = "select $sp_destination.`sl_no` from $sp_destination left join $broker_master on $sp_destination.`broker_id`=$broker_master.`broker_id` left join $destination_master ON $sp_destination.`destination_code`=$destination_master.`destination_code` $new_whr_str";
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
<script type="text/javascript">
jQuery(function () {
	jQuery('.srch_btn').on('click', function () {
		var srch_trm = encodeURIComponent(jQuery.trim(jQuery(".srch_trm").val()));
		if(srch_trm!=""){
			var qstring ="";
			var amp = "";
			var paged=1;
		  var curr_urrl = window.location.href;
		  if (curr_urrl.indexOf("?") >= 0){
		  var fst_url = curr_urrl.split("?");
		  var the_path = fst_url[0];
		  var fst_url_str_val = fst_url[1];
		  var qry_str_arr = fst_url_str_val.split("&");
		  for(i=0;i<qry_str_arr.length;i++){
			  if(qstring==""){
				  amp = "";
				  }else{
				  amp = "&";
				  }
			  var crr_str = qry_str_arr[i].split("=");
			  if(crr_str[0]=="srch_trm"){
				  qstring = qstring+amp+"srch_trm="+srch_trm;
			  }else{
				  qstring = qstring+amp+crr_str[0]+"="+crr_str[1];
			  }
			  
			  }
			  if (qstring.indexOf("srch_trm") < 0){
				  if(qstring==""){
			  qstring = qstring+"srch_trm="+srch_trm;
				  }else{
					qstring = qstring+"&srch_trm="+srch_trm; 
				  }
			  }
			  var new_url = fst_url[0]+"?"+qstring;
		  }else{
			  qstring = 'srch_trm='+srch_trm;
		      var new_url = curr_urrl+'?'+qstring;  
		  }
		  
		   window.location = new_url;
		}
	});
	
	jQuery('.srch_reset_btn').on('click', function () {
		window.location = "sp_destination_list.php";
	});
	
});
</script>
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                
            </div>
            <!-- Basic Examples -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                          <h2>SP Destination (<?php echo $total_pgres;?>)&nbsp;&nbsp;<a href="export_sp_destination.php?get_type=all" class="btn bg-red waves-effe">Export&nbsp;all</a>
                         
                          </h2>
                            <div class="row clearfix">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_dlr_dtls" value="<?php echo $srch_dlr_dtls;?>" placeholder="Search Dealer Details">
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
                                            <th>SP&nbsp;Code</th>
                                            <th>SP&nbsp;Name</th>
                                            <th>Destination Code</th>
                                            <th>Destination Name</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>SP&nbsp;Code</th>
                                            <th>SP&nbsp;Name</th>
                                            <th>Destination Code</th>
                                            <th>Destination Name</th>
                                            <th>Status</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php

$sql1 = "select $sp_destination.`sl_no`,
$sp_destination.`acedns` as `acedns_sts`,$broker_master.`broker_name`,$broker_master.`broker_id`,$broker_master.`dns_broker_id`,$destination_master.`destination_code`,$destination_master.`destination_name` from $sp_destination left join $broker_master on $sp_destination.`broker_id`=$broker_master.`broker_id` left join $destination_master ON $sp_destination.`destination_code`=$destination_master.`destination_code` $new_whr_str order by $broker_master.`broker_name` asc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$the_sl_no = $row1["sl_no"];
		$dns_broker_id = $row1["dns_broker_id"];
		$broker_id = $row1["broker_id"];
		$broker_name = $row1["broker_name"];
		$destination_code = $row1["destination_code"];
		$destination_name = $row1["destination_name"];
		$acedns = $row1["acedns_sts"];
		
		if($acedns=='Y') $status='ACTIVE';
		if($acedns=='N') $status='INACTIVE';
		
?>
<tr>
<td><?php echo $dns_broker_id;?></td>
<td><?php echo $broker_name;?></td>
<td><?php echo $destination_code;?></td>
<td><?php echo $destination_name;?></td>
<td><a href="javascript:void(0);" class="spdeststat" the_sl_no="<?php echo $the_sl_no;?>"><?php echo $status;?></a><span class="os_ldr" id="ca_ldr_<?php echo $the_sl_no;?>"></span></td>
</tr>
<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="5">No data found.</td>
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
	
	jQuery(".spdeststat").click(function(){
		var ancr_elmnt = jQuery(this);
		var the_sl_no = ancr_elmnt.attr("the_sl_no");
		var the_status = ancr_elmnt.html();
		if(the_sl_no!=""){
			var theldrid = "ca_ldr_"+the_sl_no;
			//alert(theldrid);
			var for_loader = jQuery("#"+theldrid);
			//var for_loader = jQuery("#"+branchcode);
			for_loader.html(imgs);
			jQuery.ajax({
			url: 'ajax_sp_destination_status_update.php',
			type: 'post',
			dataType: "JSON",
			data: "the_sl_no="+the_sl_no+"&the_status="+the_status,
			success: function(response){
				//alert(response.acedns_status);
			if(response.process_status=="YES"){
				ancr_elmnt.html(response.upd_status_show);
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
		window.location = "sp_destination_list.php"+qstring;
		}else{
			alert("Please select atleast one field to search.");
		}
	});
	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "sp_destination_list.php";
	});
});
</script>
<?php
include "web_footer.php";
mysql_close();
?>
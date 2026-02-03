<?php
include "web_check.php";
include "star_connection.php";
$customer_master = "customer_master";
$customer_destination = "customer_destination";
$destination_master = "destination_master";

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
$add_page_name = "customer_destination_list.php";
$page_name = "customer_destination_list.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

if($new_whr_str!='')
{
$pgsql = "select $customer_master.`customer_code` from $customer_master INNER JOIN  $customer_destination on $customer_master.`customer_code`=$customer_destination.`customer_code` INNER JOIN  $destination_master ON $destination_master.destination_code=$customer_destination.destination_code $new_whr_str";
$pgres = mysql_query($pgsql);
$total_pgres = mysql_num_rows($pgres);
}
else
{
$pgsql = "select count(*) as total_count from $customer_destination";
$pgres = mysql_query($pgsql);
$rowval=mysql_fetch_array($pgres);
$total_pgres = $rowval['total_count'];
}
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
		window.location = "customer_destination_list.php";
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
                          <h2>Customer Destination (<?php echo $total_pgres;?>)&nbsp;&nbsp;<a href="export_customer_destination.php?get_type=all" class="btn bg-red waves-effe">Export&nbsp;all</a>
                         
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
                                            <th>Customer&nbsp;Code</th>
                                            <th>Customer&nbsp;Name</th>
                                            <th>Destination Code</th>
                                            <th>Destination Name</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Customer&nbsp;Code</th>
                                            <th>Customer&nbsp;Name</th>
                                            <th>Destination Code</th>
                                            <th>Destination Name</th>
                                            <th>Status</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select $customer_master.customer_code,$customer_master.`customer_name`,$customer_master.dns_customer_code,$destination_master.destination_code,$destination_master.destination_name,
$customer_destination.acedns FROM $customer_master INNER JOIN  $customer_destination on $customer_master.`customer_code`=$customer_destination.`customer_code` INNER JOIN  $destination_master ON $destination_master.destination_code=$customer_destination.destination_code $new_whr_str order by $customer_master.`customer_name` asc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$dns_customer_code = $row1["dns_customer_code"];
		$customer_code = $row1["customer_code"];
		$customer_name = $row1["customer_name"];
		$destination_code = $row1["destination_code"];
		$destination_name = $row1["destination_name"];
		$acedns = $row1["acedns"];
		
		if($acedns=='Y') $status='ACTIVE';
		if($acedns=='N') $status='INACTIVE';
		
?>
<tr>
<td><?php echo $dns_customer_code;?></td>
<td><?php echo $customer_name;?></td>
<td><?php echo $destination_code;?></td>
<td><?php echo $destination_name;?></td>
<td><a href="javascript:void(0);" class="customerdeststat" dns_customer_code="<?php echo $dns_customer_code;?>" destination_code="<?php echo $destination_code;?>" ><?php echo $status;?></a><span class="os_ldr" id="ca_ldr_<?php echo $dns_customer_code;?>_<?php echo $destination_code?>"></span></td>

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
	
	jQuery(".customerdeststat").click(function(){
		var ancr_elmnt = jQuery(this);
		var customer_code = ancr_elmnt.attr("dns_customer_code");
		var destination_code = ancr_elmnt.attr("destination_code");
		if(customer_code!="" && destination_code!=''){
			var theldrid = "ca_ldr_"+customer_code+"_"+destination_code;
			//alert(theldrid);
			var for_loader = jQuery("#"+theldrid);
			//var for_loader = jQuery("#"+branchcode);
			for_loader.html(imgs);
			jQuery.ajax({
			url: 'ajax_customer_destination_stat_update.php',
			type: 'post',
			dataType: "JSON",
			data: "customer_code="+customer_code+"&destination_code="+destination_code,
			success: function(response){
				//alert(response.acedns_status);
			if(response.process_status=="YES"){
				if(response.acedns_status=='Y') 
				{
					ancr_elmnt.html('ACTIVE');
				}
				if(response.acedns_status=='N') 
				{
					ancr_elmnt.html('INACTIVE');
				}
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
		window.location = "customer_destination_list.php"+qstring;
		}else{
			alert("Please select atleast one field to search.");
		}
	});
	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "customer_destination_list.php";
	});
});
</script>
<?php
include "web_footer.php";
mysql_close();
?>
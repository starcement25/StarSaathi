<?php
include "web_check.php";
include "star_connection.php";
$table_name = "employee_master";
$customer_master = "customer_master";
$changepassword = "changepassword";
$notice_2023 = "notice_2023";
$profile_image_dir = "../profile_image/";
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
$whr_str .= "$aand ($notice_2023.`sf_dealer_id` like '%$search_array_val%' or $notice_2023.`sf_dealer_name` like '%$search_array_val%' or $notice_2023.`sf_dealer_mobile` like '%$search_array_val%' ) ";
			$new_qry_string_filtered .= "&srch_dlr_dtls=".$search_array_val;
		}
	}
}
if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}
$add_page_name = "submitted_notice_list.php";
$page_name = "submitted_notice_list.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/
$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;
/*---------PAGINATION RELATED CODE END----------*/
/*---------PAGINATION RELATED CODE START----------*/
$pgsql = "select `sf_id` from $notice_2023 left join $changepassword on $notice_2023.`sf_cust_code`=$changepassword.`customer_code` $new_whr_str";
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
                          <h2>Notice 2023 List (<?php echo $total_pgres;?>)&nbsp;&nbsp;
                          <a href="export_submitted_notice_list.php" class="btn bg-red waves-effe">Export&nbsp;Notice&nbsp;List</a> &nbsp;
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
                                            <th>Dealer&nbsp;ID</th>
                                            <th>Dealer&nbsp;SAP&nbsp;Code</th>
                                            <th>Dealer&nbsp;Name</th>
                                            <th>Phone</th>
                                            <th>Device&nbsp;Type</th>
                                            <th>App&nbsp;Version</th>
                                            <th>Branch&nbsp;Name</th>
                                            <th>Branch&nbsp;Code</th>
                                            <th>Branch&nbsp;DNS&nbsp;Code</th>
                                            <th>is_confirmed_declaration</th>
                                            <th>Submitted&nbsp;On</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Dealer&nbsp;ID</th>
                                            <th>Dealer&nbsp;SAP&nbsp;Code</th>
                                            <th>Dealer&nbsp;Name</th>
                                            <th>Phone</th>
                                            <th>Device&nbsp;Type</th>
                                            <th>App&nbsp;Version</th>
                                            <th>Branch&nbsp;Name</th>
                                            <th>Branch&nbsp;Code</th>
                                            <th>Branch&nbsp;DNS&nbsp;Code</th>
                                            <th>is_confirmed_declaration</th>
                                            <th>Submitted&nbsp;On</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select $notice_2023.*,$changepassword.`device_type`,$changepassword.`app_version` from $notice_2023 left join $changepassword on $notice_2023.`sf_cust_code`=$changepassword.`customer_code` $new_whr_str order by $notice_2023.`sf_submitted_datetime` desc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$sf_id = $row1["sf_id"];
		$sf_cust_code = $row1["sf_cust_code"];
		$sf_dealer_id = $row1["sf_dealer_id"];
		$sf_dealer_sap_code = $row1["sf_dealer_sap_code"];
		$sf_dealer_name = $row1["sf_dealer_name"];
		$sf_dealer_mobile = $row1["sf_dealer_mobile"];
		$sf_branch_name = $row1["sf_branch_name"];
		$sf_branch_code = $row1["sf_branch_code"];
		$sf_dns_branch_code = $row1["sf_dns_branch_code"];
		$sf_is_checked_declaration = $row1["sf_is_checked_declaration"] ? trim($row1["sf_is_checked_declaration"]) : "";
		if($sf_is_checked_declaration=="1"){
			$sf_is_checked_declaration = "YES";
		}else{
			$sf_is_checked_declaration = "NO";
		}
		$sf_submitted_datetime = $row1["sf_submitted_datetime"] ? trim($row1["sf_submitted_datetime"]) : "";
		if($sf_submitted_datetime!=""){
			$sf_submitted_datetime = date("jS M,Y h:i A",strtotime($sf_submitted_datetime));
		}
		
		$sf_device_type = $row1["device_type"];
		$sf_app_version = $row1["app_version"];

?>
<tr>
<td><?php echo $sf_dealer_id;?></td>
<td><?php echo $sf_dealer_sap_code;?></td>
<td><?php echo $sf_dealer_name;?></td>
<td><?php echo $sf_dealer_mobile;?></td>
<td><?php echo $sf_device_type;?></td>
<td><?php echo $sf_app_version;?></td>
<td><?php echo $sf_branch_name;?></td>
<td><?php echo $sf_branch_code;?></td>
<td><?php echo $sf_dns_branch_code;?></td>
<td><?php echo $sf_is_checked_declaration;?></td>
<td><?php echo $sf_submitted_datetime;?></td>
</tr>
<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="11">No data found.</td>
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
	
	
	jQuery(".srch_btn").click(function(){
		var srch_dlr_dtls = jQuery("#srch_dlr_dtls").val();
		var qstring ="";
		var amp = "";
		if(srch_dlr_dtls!=""){
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
			alert("Please enter dealer details to search.");
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
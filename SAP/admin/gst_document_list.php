<?php
include "web_check.php";
include "star_connection.php";
$gst_table = "customer_gst_documents";
$customer_master = "customer_master";
$branch_master = "branch_master";
$customer_destination = "customer_destination";
$destination_master = "destination_master";
$new_qry_string_filtered = "";
$srch_dlr_dtls = $_GET["srch_dlr_dtls"] ? addslashes(trim($_GET["srch_dlr_dtls"])) : "";
$sl_cust_type = $_GET["sl_cust_type"] ? addslashes(trim($_GET["sl_cust_type"])) : "";
$srch_gst_no = $_GET["srch_gst_no"] ? addslashes(trim($_GET["srch_gst_no"])) : "";
$whr_str = "";
$search_array = array("srch_dlr_dtls"=>$srch_dlr_dtls,"sl_cust_type"=>$sl_cust_type,"srch_gst_no"=>$srch_gst_no);
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_dlr_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($gst_table.`customer_id` like '%$search_array_val%' or $customer_master.`customer_name` like '%$search_array_val%' or $customer_master.`phone_no` like '%$search_array_val%') ";
			$new_qry_string_filtered .= "&srch_dlr_dtls=".$search_array_val;
		}
	}else if($search_array_key=="sl_cust_type"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $gst_table.`cust_type`='$search_array_val' ";
			$new_qry_string_filtered .= "&sl_cust_type=".$search_array_val;
		}
	}else if($search_array_key=="srch_gst_no"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $gst_table.`gst_no` like '%$search_array_val%' ";
			$new_qry_string_filtered .= "&srch_gst_no=".$search_array_val;
		}
	}
}
if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}
$page_name = "gst_document_list.php";
/*---------PAGINATION RELATED CODE START----------*/
$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;
/*---------PAGINATION RELATED CODE END----------*/
$pgsql = "select $gst_table.`id` from $gst_table left join $customer_master on $customer_master.`customer_id`=$gst_table.`customer_id` $new_whr_str";
$pgres = mysql_query($pgsql);
$total_pgres = mysql_num_rows($pgres);
$start_from = (($page-1)*$limit);
$prev = $page - 1;							//previous page is page - 1
$next = $page + 1;							//next page is page + 1
$lastpage = ceil($total_pgres/$limit);   //lastpage is = total pages / items per page, rounded up.
$lpm1 = $lastpage - 1;
/*---------PAGINATION RELATED CODE END----------*/
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
                          <h2>GST Document List (<?php echo $total_pgres;?>)&nbsp;&nbsp;
                          <a href="export_gst_document.php?export=1<?php echo $new_qry_string_filtered;?>" class="btn bg-red waves-effe">Export&nbsp;CSV</a>
                          </h2>
                            <div class="row clearfix">
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_dlr_dtls" value="<?php echo $srch_dlr_dtls;?>" placeholder="Search Dealer Details">
    </div>
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
    <select class="form-control" id="sl_cust_type">
<option value="">All Customer Type</option>
<?php
$ct_sql = "select distinct `cust_type` from $gst_table where `cust_type`!='' and `cust_type` is not null order by `cust_type` asc";
$ct_res = mysql_query($ct_sql);
if($ct_res){
	while($ct_row=mysql_fetch_assoc($ct_res)){
		$the_cust_type = $ct_row["cust_type"];
?>
<option value="<?php echo $the_cust_type;?>" <?php if($sl_cust_type==$the_cust_type){?> selected="selected" <?php } ?>><?php echo $the_cust_type;?></option>
<?php
	}
}
?>
</select>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_gst_no" value="<?php echo $srch_gst_no;?>" placeholder="Search GST No">
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
                                            <th>Sl&nbsp;No.</th>
                                            <th>Customer&nbsp;ID</th>
                                            <th>Customer&nbsp;Name</th>
                                            <th>Branch&nbsp;Name</th>
                                            <th>Destination&nbsp;Name</th>
                                            <th>Region</th>
                                            <th>Phone</th>
                                            <th>Customer&nbsp;Type</th>
                                            <th>GST&nbsp;No</th>
                                            <th>PAN&nbsp;No</th>
                                            <th>GST&nbsp;Status</th>
                                            <th>Document</th>
                                            <th>Created&nbsp;Date</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Sl&nbsp;No.</th>
                                            <th>Customer&nbsp;ID</th>
                                            <th>Customer&nbsp;Name</th>
                                            <th>Branch&nbsp;Name</th>
                                            <th>Destination&nbsp;Name</th>
                                            <th>Region</th>
                                            <th>Phone</th>
                                            <th>Customer&nbsp;Type</th>
                                            <th>GST&nbsp;No</th>
                                            <th>PAN&nbsp;No</th>
                                            <th>GST&nbsp;Status</th>
                                            <th>Document</th>
                                            <th>Created&nbsp;Date</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select $gst_table.*,$customer_master.`customer_name`,$customer_master.`phone_no`,$customer_master.`region`,$branch_master.`branch_name`,(select group_concat(distinct $destination_master.`destination_name` separator ', ') from $customer_destination left join $destination_master on $destination_master.`destination_code`=$customer_destination.`destination_code` where $customer_destination.`customer_code`=$customer_master.`customer_code`) as destination_name from $gst_table left join $customer_master on $customer_master.`customer_id`=$gst_table.`customer_id` left join $branch_master on $customer_master.`branch_code`=$branch_master.`branch_code` $new_whr_str order by $gst_table.`created_date` desc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	$countrow = $start_from + 1;
	while($row1=mysql_fetch_assoc($res1)){
		$customer_id = $row1["customer_id"];
		$customer_name = $row1["customer_name"];
		$branch_name = $row1["branch_name"];
		$destination_name = $row1["destination_name"];
		$region = $row1["region"];
		$phone_no = $row1["phone_no"];
		$cust_type = $row1["cust_type"];
		$gst_no = $row1["gst_no"];
		$pan_no = $row1["pan_no"];
		if(trim($pan_no)=="" && strlen(trim($gst_no))==15){
			$pan_no = substr(trim($gst_no),2,10);
		}
		$is_any = trim($row1["is_any"]);
		if($is_any=="1"){
			$gst_status = "GST Available";
		}else if($is_any=="0"){
			$gst_status = "GST Not Available";
		}else{
			$gst_status = "--";
		}
		$upload_doc = $row1["upload_doc"] ? trim($row1["upload_doc"]) : "";
		$created_date = $row1["created_date"];
		$the_created_date = "";
		if($created_date!="" && $created_date!="0000-00-00 00:00:00" && $created_date!="0000-00-00"){
			$the_created_date = date("d-m-Y h:i A", strtotime($created_date));
		}
?>
<tr>
<td><?php echo $countrow;?></td>
<td><?php echo $customer_id;?></td>
<td><?php echo $customer_name;?></td>
<td><?php echo $branch_name;?></td>
<td><?php echo $destination_name;?></td>
<td><?php echo $region;?></td>
<td><?php echo $phone_no;?></td>
<td><?php echo $cust_type;?></td>
<td><?php echo $gst_no;?></td>
<td><?php echo $pan_no;?></td>
<td><?php echo $gst_status;?></td>
<td style="text-align:center;"><?php
if($upload_doc!=""){
?>
<a href="<?php echo htmlspecialchars($upload_doc);?>" target="_blank" rel="noopener noreferrer" class="btn bg-green waves-effect">View&nbsp;Doc</a>
<?php
}else{
	echo '--';
}
?></td>
<td><?php echo $the_created_date;?></td>
</tr>
<?php
	$countrow++;
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="13">No data found.</td>
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
        </div>
    </section>
<script type="text/javascript">
jQuery(function(){
	jQuery(".srch_btn").click(function(){
		var srch_dlr_dtls = jQuery("#srch_dlr_dtls").val();
		var sl_cust_type = jQuery("#sl_cust_type").val();
		var srch_gst_no = jQuery("#srch_gst_no").val();
		var qstring ="";
		if(srch_dlr_dtls!="" || sl_cust_type!="" || srch_gst_no!=""){
		if(srch_dlr_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}else{
				qstring = qstring+"srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}
		}
		if(sl_cust_type!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_cust_type="+encodeURIComponent(sl_cust_type);
			}else{
				qstring = qstring+"sl_cust_type="+encodeURIComponent(sl_cust_type);
			}
		}
		if(srch_gst_no!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_gst_no="+encodeURIComponent(srch_gst_no);
			}else{
				qstring = qstring+"srch_gst_no="+encodeURIComponent(srch_gst_no);
			}
		}

		  if(qstring!=""){
			 qstring = "?"+qstring;
		  }
		window.location = "gst_document_list.php"+qstring;
		}else{
			alert("Please select atleast one field to search.");
		}
	});

	jQuery(".srch_reset_btn").click(function(){
		window.location = "gst_document_list.php";
	});
});
</script>
<?php
include "web_footer.php";
mysql_close();
?>

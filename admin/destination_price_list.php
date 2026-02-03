<?php
include "web_check.php";
include "star_connection.php";
$destination_wise_price = "destination_wise_price";

$new_qry_string_filtered = "";
$export_filtered_str = "";
$sl_destination = $_GET["sl_destination"] ? addslashes(trim($_GET["sl_destination"])) : "";
$sl_product = $_GET["sl_product"] ? addslashes(trim($_GET["sl_product"])) : "";
$whr_str = "";
$search_array = array("sl_destination"=>$sl_destination,"sl_product"=>$sl_product);
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="sl_destination"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($destination_wise_price.destination_name like '%$search_array_val%') ";
			$new_qry_string_filtered .= "&sl_destination=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_destination=".$search_array_val;
			}else{
				$export_filtered_str .= "&sl_destination=".$search_array_val;
			}
		}
	}
	if($search_array_key=="sl_product"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($destination_wise_price.product_name like '%$search_array_val%' ) ";
			$new_qry_string_filtered .= "&sl_product=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_product=".$search_array_val;
			}else{
				$export_filtered_str .= "&sl_product=".$search_array_val;
			}
		}
	}
}

if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}
$add_page_name = "destination_price_list.php";
$page_name = "destination_price_list.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select $destination_wise_price.destination_name from $destination_wise_price $new_whr_str ORDER BY destination_name,product_name ASC";
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
                          <h2>Destination Price Details (<?php echo $total_pgres;?>)&nbsp;&nbsp;
                          <a href="export_destination_price.php?get_type=all<?php echo $export_filtered_str;?>" class="btn bg-red waves-effe">Export All</a> &nbsp; 
                          </h2>
                            <div class="row clearfix">
   <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control" id="sl_destination">
<option value="">Select Destination</option>
<?php
$sqldestination = "select DISTINCT destination_name from $destination_wise_price order by destination_name ASC";
$resdestination = mysql_query($sqldestination);
$totresdestination = mysql_num_rows($resdestination);
if($totresdestination>0){
	while($rowdestination=mysql_fetch_assoc($resdestination)){
		$destination_name = $rowdestination["destination_name"];
		?>
 <option value="<?php echo $destination_name;?>" <?php if($destination_name==$sl_destination){?> selected="selected" <?php } ?>><?php echo $destination_name;?></option>
        <?php
	}
}
?>
</select>
    </div>
     <div class="col-lg-2 col-md-2 col-sm-14 col-xs-14 add_top_bottom_padding">
<select class="form-control" id="sl_product">
<option value="">Select Product</option>
<?php
$sqlproduct = "select DISTINCT product_name from $destination_wise_price order by product_name ASC";
$resproduct = mysql_query($sqlproduct);
$totproduct = mysql_num_rows($resproduct);
if($totproduct>0){
	while($rowproduct=mysql_fetch_assoc($resproduct)){
		$product_name = $rowproduct["product_name"];
		?>
 <option value="<?php echo $product_name;?>" <?php if($product_name==$sl_product){?> selected="selected" <?php } ?>><?php echo $product_name;?></option>
        <?php
	}
}
?>
</select>
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
                                            <th>Destination&nbsp;Name</th>
                                            <th>Product&nbsp;Name</th>
                                            <th>Rate</th>
                                            <th>Effective&nbsp;Date</th>
                                            <th>Effective&nbsp;Rate</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Destination&nbsp;Name</th>
                                            <th>Product&nbsp;Name</th>
                                            <th>Rate</th>
                                            <th>Effective&nbsp;Date</th>
                                            <th>Effective&nbsp;Rate</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select $destination_wise_price.destination_name,$destination_wise_price.product_name,$destination_wise_price.effective_date,
$destination_wise_price.effective_rate,$destination_wise_price.rate from $destination_wise_price $new_whr_str ORDER BY destination_name,product_name ASC limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$destination_name = $row1["destination_name"];
		$product_name = $row1["product_name"];
		$effective_date = date('d/m/Y H:i:s',strtotime($row1["effective_date"]));
		$effective_rate = $row1["effective_rate"];
		$rate = $row1["rate"];
?>
<tr>
<td><?php echo $destination_name;?></td>
<td><?php echo $product_name;?></td>
<td><?php echo $rate;?></td>
<td><?php echo $effective_date;?></td>
<td><?php echo $effective_rate;?></td>
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
	
		jQuery(".srch_btn").click(function(){
		var sl_destination = jQuery("#sl_destination").val();
		var sl_product = jQuery("#sl_product").val();
		var qstring ="";
		var amp = "";
		if(sl_destination!="" || sl_product!=""){
		if(sl_destination!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_destination="+encodeURIComponent(sl_destination);
			}else{
				qstring = qstring+"sl_destination="+encodeURIComponent(sl_destination);
			}
		}
		if(sl_product!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_product="+encodeURIComponent(sl_product);
			}else{
				qstring = qstring+"sl_product="+encodeURIComponent(sl_product);
			}
		}
		
		  if(qstring!=""){
			 qstring = "?"+qstring; 
		  }
		window.location = "destination_price_list.php"+qstring;
		}else{
			alert("Please select atleast one field to search.");
		}
	});

	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "destination_price_list.php";
	});
});
</script>
<?php
include "web_footer.php";
mysql_close();
?>
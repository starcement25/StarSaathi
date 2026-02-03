<?php
include "web_check.php";
include "star_connection.php";
$ageing = "ageing";

$new_qry_string_filtered = "";
$export_filtered_str = "";
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
$whr_str .= "$aand ($ageing.party like '%$search_array_val%') ";
			$new_qry_string_filtered .= "&srch_dlr_dtls=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&srch_dlr_dtls=".$search_array_val;
			}else{
				$export_filtered_str .= "&srch_dlr_dtls=".$search_array_val;
			}
		}
	}
	/*if($search_array_key=="sl_product"){
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
	}*/
}

if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}
$add_page_name = "ageing_list.php";
$page_name = "ageing_list.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select $ageing.id from $ageing $new_whr_str ORDER BY zone,party ASC";
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
                          <h2>Ageing (<?php echo $total_pgres;?>)&nbsp;&nbsp;
                          <a href="export_ageing.php?get_type=all<?php echo $export_filtered_str;?>" class="btn bg-red waves-effe">Export All</a> &nbsp; 
                          </h2>
                            <div class="row clearfix">
  <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_dlr_dtls" value="<?php echo $srch_dlr_dtls;?>" placeholder="Search Party">
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
                                            <th>Zone</th>
                                            <th>Class&nbsp;Desc</th>
                                            <th>Alias</th>
                                            <th>Party</th>
                                            <th>Security&nbsp;Deposit</th>
                                            <th>Credit&nbsp;Limit</th>
                                            <th>Collection&nbsp;Date</th>
                                            <th>Challan&nbsp;Date</th>
                                            <th>Balance</th>
                                            <th>MTD&nbsp;Sales</th>
                                            <th>MTD&nbsp;Collection</th>
                                            <th>O/S&nbsp;Total</th>
                                            <th><=10 Days</th>
                                            <th>11 to 17 Days</th>
                                            <th>18 to 25 Days</th>
                                            <th>26 to 30 Days</th>
                                            <th>31 to 45 Days</th>
                                            <th>46 to 60 Days</th>
                                            <th>61 to 90 Days</th>
                                            <th>91 to 120 Days</th>
                                            <th>121 to 180 Days</th>
                                            <th>> 180 Days</th>
                                            <th>ONACC</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                             <th>Zone</th>
                                            <th>Class&nbsp;Desc</th>
                                            <th>Alias</th>
                                            <th>Party</th>
                                            <th>Security&nbsp;Deposit</th>
                                            <th>Credit&nbsp;Limit</th>
                                            <th>Collection&nbsp;Date</th>
                                            <th>Challan&nbsp;Date</th>
                                            <th>Balance</th>
                                            <th>MTD&nbsp;Sales</th>
                                            <th>MTD&nbsp;Collection</th>
                                            <th>O/S&nbsp;Total</th>
                                            <th><=10 Days</th>
                                            <th>11 to 17 Days</th>
                                            <th>18 to 25 Days</th>
                                            <th>26 to 30 Days</th>
                                            <th>31 to 45 Days</th>
                                            <th>46 to 60 Days</th>
                                            <th>61 to 90 Days</th>
                                            <th>91 to 120 Days</th>
                                            <th>121 to 180 Days</th>
                                            <th>> 180 Days</th>
                                            <th>ONACC</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select $ageing.* FROM $ageing $new_whr_str ORDER BY zone,party ASC limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$zone = $row1["zone"];
		$classdescr = $row1["classdescr"];
		$alias = $row1["alias"];
		$party = $row1["party"];
		$securitydeposit = $row1["securitydeposit"];
		$crlim = $row1["crlim"];
		$colldt = date('d/m/Y H:i:s',strtotime($row1["colldt"]));
		$chllndt = date('d/m/Y H:i:s',strtotime($row1["chllndt"]));
		$mobal = $row1["mobal"];
		$mtdsales = $row1["mtdsales"];
		$mtdcoll = $row1["mtdcoll"];
		$ostotal = $row1["ostotal"];
		$less_equ_10days = $row1["less_equ_10days"];
		$eleven_17 = $row1["11_17days"];
		$eighteen_25 = $row1["18_25days"];
		$twentysix_30 = $row1["26_30days"];
		$thirtyone_45 = $row1["31_45days"];
		$fortysix_60 = $row1["46_60days"];
		$sixtyone_90 = $row1["61_90days"];
		$nintyone_120 = $row1["91_120days"];
		$onetwentyone_180 = $row1["121_180days"];
		$greater_180 = $row1["greater_180days"];
		$onacc = $row1["onacc"];
?>
<tr>
<td><?php echo $zone;?></td>
<td><?php echo $classdescr;?></td>
<td><?php echo $alias;?></td>
<td><?php echo $party;?></td>
<td><?php echo $securitydeposit;?></td>
<td><?php echo $crlim;?></td>
<td><?php echo $colldt;?></td>
<td><?php echo $chllndt;?></td>
<td><?php echo $mobal;?></td>
<td><?php echo $mtdsales;?></td>
<td><?php echo $mtdcoll;?></td>
<td><?php echo $ostotal;?></td>
<td><?php echo $less_equ_10days;?></td>
<td><?php echo $eleven_17;?></td>
<td><?php echo $eighteen_25;?></td>
<td><?php echo $twentysix_30;?></td>
<td><?php echo $thirtyone_45;?></td>
<td><?php echo $fortysix_60;?></td>
<td><?php echo $sixtyone_90;?></td>
<td><?php echo $nintyone_120;?></td>
<td><?php echo $onetwentyone_180;?></td>
<td><?php echo $greater_180;?></td>
<td><?php echo $onacc;?></td>
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
		window.location = "ageing_list.php"+qstring;
		}else{
			alert("Please select atleast one field to search.");
		}
	});

	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "ageing_list.php";
	});
});
</script>
<?php
include "web_footer.php";
mysql_close();
?>
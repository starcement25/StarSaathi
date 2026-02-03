<?php
include "web_check.php";
include "star_connection.php";
//echo"<pre>";print_r($_SESSION["start_report_admin_name"]);die;
if(trim($_SESSION["start_report_admin_name"])=='OSNE1'){
	$zone='ROE';
}
else{
	$zone='NE';
}
$whr_str = "";
if($_GET['zone']){
	$whr_str=" AND zone='".$_GET['zone']."' ";
	$zone=$_GET['zone'];
}
if($_GET['year']){
	$whr_str=" AND year='".$_GET['year']."' ";
	$year=$_GET['year'];

}
if($_GET['month']){
	$whr_str=" AND month='".$_GET['month']."' ";
	$month=$_GET['month'];

}
if($whr_str!=""){
	$new_whr_str = "where zone = '$zone'   ".$whr_str;
}else{
	$new_whr_str ="where zone = '$zone'";
}
$add_page_name = "add_new_LiftingFinalFileUpload.php";
$page_name = "LiftingFinalFileUpload.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "10";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "SELECT * FROM `lifting_final_file_upload_csv`  $new_whr_str order by `date_time` desc  ";
//echo"<pre>";print_r($pgsql);die;
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
.add_top_bottom_padding2{
padding: 5px 8px;	
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
                          <h2>Approved RSSD Lifting (<?php echo $total_pgres;?>)&nbsp;&nbsp;&nbsp;&nbsp;
                          <!-- <a href="add_new_LiftingFinalFileUpload.php" class="btn bg-red waves-effe">ADD NEW File</a> -->
                          <?php /*?><a href="export_dealer.php?get_type=loggedin" class="btn bg-red waves-effe">Export&nbsp;loggedin&nbsp;dealer</a> &nbsp; <a href="export_dealer.php?get_type=notloggedin" class="btn bg-red waves-effe">Export&nbsp;not&nbsp;loggedin&nbsp;dealer</a><?php */?>
                          </h2>
                            <div class="row clearfix">
							<!-- <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 add_top_bottom_padding2">
								<select class="form-control" id="zone" name="zone" style="padding-left:2px;" data-placeholder="Select Branch Name">
								<option value="">Select Zone</option>
								 <option <?php echo ($zone=='NE')?'selected': ''; ?> >NE</option> 
								<option <?php echo ($zone=='ROE')?'selected': ''; ?> >ROE</option>

								</select>
							</div> -->
							<div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 add_top_bottom_padding2">
								<select class="form-control" id="year" name="year" style="padding-left:2px;" data-placeholder="Select Year">
								<option value="">Select Year</option>
									<?php
									$currentYear = date("Y");
									for ($i = 0; $i <= 10; $i++) {
										$year_data = $currentYear - $i;
										$select_year= ($year==$year_data)?'selected': ''; 
										echo "<option $select_year value=\"$year_data\">$year_data</option>";
									}
								?>

								</select>
							</div>
							<div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 add_top_bottom_padding2">
								<select class="form-control" id="month" name="month" style="padding-left:2px;" data-placeholder="Select Month">
								<option value="">Select Month</option>
								<option <?php echo ($month=='January')?'selected': ''; ?>  value="January">January</option>
								<option <?php echo ($month=='February')?'selected': ''; ?> value="February">February</option>
								<option <?php echo ($month=='March')?'selected': ''; ?> value="March">March</option>
								<option <?php echo ($month=='April')?'selected': ''; ?> value="April">April</option>
								<option <?php echo ($month=='May')?'selected': ''; ?> value="May">May</option>
								<option <?php echo ($month=='June')?'selected': ''; ?> value="June">June</option>
								<option <?php echo ($month=='July')?'selected': ''; ?> value="July">July</option>
								<option <?php echo ($month=='August')?'selected': ''; ?> value="August">August</option>
								<option <?php echo ($month=='September')?'selected': ''; ?> value="September">September</option>
								<option <?php echo ($month=='October')?'selected': ''; ?> value="October">October</option>
								<option <?php echo ($month=='November')?'selected': ''; ?> value="November">November</option>
								<option <?php echo ($month=='December')?'selected': ''; ?> value="December">December</option>

								</select>
							</div>
							<div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
							<button type="button" class="btn bg-red waves-effect srch_btn1" >Search</button>
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
                                            <th>Zone</th>
                                            <th>Year</th>
                                            <th>Month</th>
                                            <th>CSV&nbsp;File</th>
                                            <!-- <th>Action</th> -->
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
											<th>Zone</th>
                                            <th>Year</th>
                                            <th>Month</th>
                                            <th>CSV&nbsp;File</th>
                                            <!-- <th>Action</th> -->
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$the_PDF_file_name_arr = array();
$the_PDF_file_link_arr = array();
$file_dir = "lifting_csv/";

$sql1 = "SELECT * FROM `lifting_final_file_upload_csv` $new_whr_str order by `date_time` desc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$the_PDF_file_name_arr = array();
		$the_PDF_file_link_arr = array();
		$id = $row1["id"];
		$zone = $row1["zone"];
		$year = $row1["year"];
		$month = $row1["month"];
		$CSV_file_name = $row1["CSV_file_name"] ? trim($row1["CSV_file_name"]) : "";
		//$file_detail=$file_dir.$CSV_file_name;
		
?>
<tr>
<td><?php echo $zone; ?></td>
<td><?php echo $year; ?></td>
<td><?php echo $month; ?></td>
<td><?php 
//if(file_exists($file_detail)){
?>
<a class='btn bg-red waves-effect ' href="CSV_download_LiftingFinalFileUpload.php?id=<?php echo $id;?>&name=<?php echo 'Download_'.$zone.'_'.$year.'_'.$month;?>" rel="group">Download</a>
<?php
/*
}else{
	echo "NONE";
}*/
?></td>

<!-- <td>
<a href="add_new_LiftingFinalFileUpload.php?id=<?php echo $id;?>" class="btn bg-red waves-effect update_sl_no" style="display:inline-block;margin-right:5px;float:left;" >Edit</a>


</td> -->
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


 

	jQuery(".srch_btn1").click(function(){
		var zone = jQuery("#zone").val();
		var month = jQuery("#month").val();
		var year = jQuery("#year").val();
		
		var qstring ="";
		var amp = "";
		
		if(month!=""){
			if(qstring!=""){
				qstring = qstring+"&month="+encodeURIComponent(month);
			}else{
				qstring = qstring+"month="+encodeURIComponent(month);
			}
		}
		
		if(zone!=""){
			if(qstring!=""){
				qstring = qstring+"&zone="+encodeURIComponent(zone);
			}else{
				qstring = qstring+"zone="+encodeURIComponent(zone);
			}
		}
		if(year!=""){
			if(qstring!=""){
				qstring = qstring+"&year="+encodeURIComponent(year);
			}else{
				qstring = qstring+"year="+encodeURIComponent(year);
			}
		}
		
		  if(qstring!=""){
			 qstring = "LiftingFinalFileUploadFilter.php?"+qstring; 
		  }
		 // alert(qstring);
		window.location = qstring;
		
	});
	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "LiftingFinalFileUploadFilter.php";
	});
});
</script>
<?php
include "web_footer.php";
mysql_close();
?>
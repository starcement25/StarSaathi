<?php
include "web_check.php";
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$employee_master = "employee_master";
$customer_master = "customer_master";
$branch_master = "branch_master";
$broker_master = "broker_master";
$customer_broker_relation = "customer_broker_relation";
$ledger_balance = "ledger_balance";
$branch_schemes_PDF = "branch_schemes_PDF";
$notification_message = "notification_message";

$file_dir = "../schemes/";
$file_url_prefix = "http://starsaathi.com/SAP/schemes/";
$add_page_name = "show_scheme.php";
$page_name = "show_scheme.php";

$sswa_user_type = $_SESSION["sswa_user_type"];
$sswa_selected_dealer_code = $_SESSION["sswa_selected_dealer_code"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];

$sql3 = "select `customer_id` from $customer_master where `customer_code`='$sswa_selected_customer_code'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
$row3 = mysql_fetch_assoc($res3);
$the_customer_id = trim($row3["customer_id"]);

if(strtoupper($sswa_user_type)=='DEALER'){
/*----Tracklog Code Start-----*/
$curr_date_time = date("Y-m-d H:i:s");
$webservice_name = "SCHEME";
$sqlin_tl = "insert into `webservice_track_log` (`customer_code`,`webservice_name`,`details`,`datetime`) values ('$the_customer_id','$webservice_name','','$curr_date_time')";
$resin_tl = mysql_query($sqlin_tl);
/*----Tracklog Code End-----*/
}

$the_branch_raw_arr = array();
$pgsql_brnc = "select `branch_code` from $customer_master where `customer_code`='$sswa_selected_customer_code'";
$pgres_brnc = mysql_query($pgsql_brnc);
$total_pgres_brnc = mysql_num_rows($pgres_brnc);
if($total_pgres_brnc>0){
	$row_brnc=mysql_fetch_assoc($pgres_brnc);
	$the_branch_raw = $row_brnc["branch_code"] ? trim($row_brnc["branch_code"]) : "";
	if($the_branch_raw!=""){	
	$the_branch_raw_arr = explode(",",$the_branch_raw);
	foreach($the_branch_raw_arr as $the_branch_raw_arr_val){
	$tagged_cust_branch_arr[] = $the_branch_raw_arr_val;
	}
	}
}

$tagged_cust_branch_arr_str = implode("','",$tagged_cust_branch_arr);

$sql1 = "select `sl_no`,`PDF_file_name` from $branch_schemes_PDF where `branch_code` in('".$tagged_cust_branch_arr_str."') and `acedns`='Y' and CURDATE() between `start_date` and `end_date` order by `start_date` asc";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);



include "web_header.php";
?>
<style>
.show_pdf_scheme, .inner_div{
	cursor:pointer;
}
</style>
<section class="content">
        <div class="container-fluid">
            
<div class="row clearfix">
<?php
$cnt = 1;
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$the_sl_no = $row1["sl_no"];
		$PDF_file_name = $row1["PDF_file_name"] ? trim($row1["PDF_file_name"]) : "";
		$pdf_file_url ="";
		if($PDF_file_name!=""){
			if(file_exists(($file_dir.$PDF_file_name))){
			$pdf_file_url = $file_url_prefix.$PDF_file_name;
			}
		}
if($pdf_file_url!=""){
		
?>
<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
<a class='show_pdf_scheme' href="<?php echo $pdf_file_url;?>" target="_blank">
<div class="info-box bg-pink hover-expand-effect inner_div">
<div class="icon">
<i class="material-icons">playlist_add_check</i>
</div>
<div class="content">
<div class="text">SCHEME <?php echo $cnt;?></div>
</div>
</div></a>
</div>
<?php
if($cnt%3==0){?>
</div><div class="row clearfix">	
<?php }
$cnt++;	
}
} 
}else{
	echo "No record found";
}
?>
</div>

</div>
</section>
<script type="text/javascript">
jQuery(function(){
	
//jQuery(".show_pdf_scheme").colorbox({iframe:true, width:"90%", height:"95%"});

});
</script>
<?php
include "web_footer.php";
mysql_close();
?>
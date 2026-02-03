<?php
error_reporting(0);
include "star_connection.php";
$survey_form = "survey_form";
$customer_master = "customer_master";
$branch_master = "branch_master";
$the_dealer_id = "";
$the_dealer_name = "";
$the_dealer_mobile = "";
$the_branch_name = "";
$the_sf_pan = "";
$the_sf_is_return_fy18_19 = "NO";
$the_sf_is_return_fy19_20 = "NO";
$the_sf_is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20 = "NO";
$the_sf_is_turnover_exceeds_10_crores_fy20_21 = "NO";
$the_sf_submitted_datetime = "";

function get_branch_data_by_id($bid){
$branch_data = array("branch_name"=>"","dns_branch_code"=>"");
	$branch_master = "branch_master";
	$bid = $bid ? addslashes(trim($bid)) : "";
	if($bid!=''){
		$sqls_bd = "select `branch_name`,`dns_branch_code` from $branch_master where `branch_code`='$bid'";
		$ress_bd = mysql_query($sqls_bd);
		$totress_bd = mysql_num_rows($ress_bd);
		if($totress_bd>0){
		$rows_bd = mysql_fetch_assoc($ress_bd);
		$the_branch_name_ftc = $rows_bd["branch_name"] ? addslashes(trim($rows_bd["branch_name"])) : "";
		$the_dns_branch_code_ftc = $rows_bd["dns_branch_code"] ? addslashes(trim($rows_bd["dns_branch_code"])) : "";
		$branch_data = array("branch_name"=>$the_branch_name_ftc,"dns_branch_code"=>$the_dns_branch_code_ftc);
		}
	}
	return $branch_data;
}

$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
if($the_id!=""){
$sql3 = "select * from $survey_form where `sf_cust_code`='$the_id'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
$row3 = mysql_fetch_assoc($res3);
$the_dealer_id = $row3["sf_dealer_id"] ? trim($row3["sf_dealer_id"]) : "";
$the_dealer_name = $row3["sf_dealer_name"] ? trim($row3["sf_dealer_name"]) : "";
$the_dealer_mobile = $row3["sf_dealer_mobile"] ? trim($row3["sf_dealer_mobile"]) : "";
$the_branch_name = $row3["sf_branch_name"] ? trim($row3["sf_branch_name"]) : "";
$the_sf_is_return_fy18_19 = $row3["sf_is_return_fy18_19"] ? trim($row3["sf_is_return_fy18_19"]) : "NO";
$the_sf_is_return_fy19_20 = $row3["sf_is_return_fy19_20"] ? trim($row3["sf_is_return_fy19_20"]) : "NO";
$the_sf_is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20 = $row3["sf_is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20"] ? trim($row3["sf_is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20"]) : "NO";
$the_sf_is_turnover_exceeds_10_crores_fy20_21 = $row3["sf_is_turnover_exceeds_10_crores_fy20_21"] ? trim($row3["sf_is_turnover_exceeds_10_crores_fy20_21"]) : "NO";
$the_sf_pan = $row3["sf_pan"] ? trim($row3["sf_pan"]) : "";
$the_sf_submitted_datetime = $row3["sf_submitted_datetime"] ? trim($row3["sf_submitted_datetime"]) : "";
if($the_sf_submitted_datetime!=""){
$the_sf_submitted_datetime = date("jS M,Y h:i A",strtotime($the_sf_submitted_datetime));	
}

}
	
	
	
}
require "MPDF57/mpdf.php";
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Star Saathi - DECLARATION FOR COMPLIANCE UNDER NEWLY INSERTED SECTIONS 206AB/ 206CCA/ 194Q/ 206C (1H) IN THE IT ACT, 1961</title>
<script src="js/jquery.min2.js"></script>
  <script src="js/bootstrap.min.js"></script>
<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
<style>
.lavel_class{
	width:100%;
	display:block;
	font-weight:bold;
}
</style>
</head>
<body>
<div class="container-fluid">
<div class="row" style="background-color:#ED2128; height:150px;text-align:center;">
<img class="img-responsive" style="margin:0 auto; padding-top:0px;" src="images/logo_header.png" alt="logo">
</div>
<div class="panel panel-default">
  <div class="panel-body">
  <h6>DECLARATION FOR COMPLIANCE UNDER NEWLY INSERTED SECTIONS 206AB/ 206CCA/ 194Q/ 206C (1H) IN THE IT ACT, 1961 </h6>
  </div>
</div>
<form action="" method="POST" class="survey_form" id="survey_form" name="survey_form">
<div class="panel panel-default" style="margin-bottom:10px !important;">
  <div class="panel-body" style="padding:5px;">
  <div class="form-group" style="margin-bottom:0px;">
  <label for="usr" style="font-size:10px !important;font-weight: 700;" >Dealer Code</label><br />
  <?php echo $the_dealer_id;?>
  </div>
  </div>
</div>
<div class="panel panel-default" style="margin-bottom:10px !important;">
  <div class="panel-body" style="padding:5px;">
  <div class="form-group" style="margin-bottom:0px;">
  <label for="usr" style="font-size:10px !important;"  >Dealer Name</label><br />
  <?php echo $the_dealer_name;?>
  </div>
  </div>
</div>
<div class="panel panel-default" style="margin-bottom:10px !important;">
  <div class="panel-body" style="padding:5px;">
  <div class="form-group" style="margin-bottom:0px;">
  <label for="usr" style="font-size:10px !important;">Mobile no </label><br />
  <?php echo $the_dealer_mobile;?>
  </div>
  </div>
</div>


<div class="panel panel-default" style="margin-bottom:10px !important;">
  <div class="panel-body" style="padding:5px;">
  <div class="form-group" style="margin-bottom:0px;">
  <label for="sel1" style="font-size:10px !important;" >Branch</label><br />
 <?php echo $the_branch_name;?>
</div>
  </div>
</div>

<div class="panel panel-default" style="margin-bottom:10px !important;">
  <div class="panel-body" style="padding:5px;">
  <div class="form-group" style="margin-bottom:0px;">
  <label for="usr" style="font-size:10px !important;">Kindly confirm if you have filed return for FY18-19 for your Dealership firm/ Proprietorship:</label><br />
 <?php echo $the_sf_is_return_fy18_19;?>
  </div>
  </div>
</div>
<div class="panel panel-default" style="margin-bottom:10px !important;">
  <div class="panel-body" style="padding:5px;">
  <div class="form-group" style="margin-bottom:0px;">
  <label for="usr" style="font-size:10px !important;">Kindly confirm if you have filed return for FY19-20 for your Dealership firm/ Proprietorship:</label><br />
 <?php echo $the_sf_is_return_fy19_20;?>
  </div>
  </div>
</div>
<div class="panel panel-default" style="margin-bottom:10px !important;">
  <div class="panel-body" style="padding:5px;">
  <div class="form-group" style="margin-bottom:0px;">
  <label for="usr" style="font-size:10px !important;">Kindly confirm if you have claimed more than Rs. 50,000 in TDS/TCS either in FY18-19 or FY19-20 for your Dealership firm/ Proprietorship:</label>
   <br />
 <?php echo $the_sf_is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20;?>
  </div>
  </div>
</div>
<div class="panel panel-default" style="margin-bottom:10px !important;">
  <div class="panel-body" style="padding:5px;">
  <div class="form-group" style="margin-bottom:0px;">
  <label for="usr" style="font-size:10px !important;">Kindly confirm if Turnover of your Dealership firm/ Proprietorship exceeds Rs. 10 Crores for FY20-21 :</label>
   <br />
 <?php echo $the_sf_is_turnover_exceeds_10_crores_fy20_21;?>
  </div>
  </div>
</div>

<div class="panel panel-default" style="margin-bottom:10px !important;">
  <div class="panel-body" style="padding:5px;">
  <div class="form-group" style="margin-bottom:0px;">
  <label for="usr" style="font-size:10px !important;">My PAN is</label><br />
  <?php echo $the_sf_pan;?>
  </div>
  </div>
</div>
<div class="panel panel-default" style="margin-bottom:10px !important;">
  <div class="panel-body" style="padding:5px;">
  <div class="form-group" style="margin-bottom:0px;">
  <label for="usr" style="font-size:10px !important;">Declaration:</label><br />
    <label style="font-size:10px !important;">I hearby declare that the information furnished above is true to the best of my knowledge.</label>
  </div>
  </div>
</div>

<div class="panel panel-default" style="margin-bottom:10px !important;">
  <div class="panel-body" style="padding:5px;">
  <div class="form-group" style="margin-bottom:0px;">
  <label for="usr" style="font-size:10px !important;">Submitted on Starsaathi Mobile App on:</label><br />
    <label style="font-size:10px !important;"><?php echo $the_sf_submitted_datetime;?></label>
  </div>
  </div>
</div>

</form>
</div>
</body>
</html>
<?php
$body = ob_get_clean();
$mpdf = new mPDF('utf-8','A4','12','chelvetica','5','5','5','5','5','5','P');
$mpdf->writeHTML($body);
if($the_dealer_id!=""){
	$the_filr_name = "survey_pdf_".$the_dealer_id.".pdf";
}else{
	$the_filr_name = "survey_pdf.pdf";
}
$dd = $mpdf->Output($the_filr_name,'I');
?>
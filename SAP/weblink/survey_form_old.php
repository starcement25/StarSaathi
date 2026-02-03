<?php
include "star_connection.php";
$survey_form = "survey_form";
$customer_master = "customer_master";
$branch_master = "branch_master";
$success_page_name = "survey_success_page.php";

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


$state_arr = array("Andhra Pradesh","Arunachal Pradesh","Assam","Bihar","Chhattisgarh","Goa","Gujarat","Haryana","Himachal Pradesh","Jharkhand","Karnataka","Kerala","Madhya Pradesh","Maharashtra","Manipur","Meghalaya","Mizoram","Nagaland","Odisha","Punjab","Rajasthan","Sikkim","Tamil Nadu","Telangana","Tripura","Uttar Pradesh","Uttarakhand","West Bengal","Andaman and Nicobar Islands","Chandigarh","Dadra and Nagar Haveli","Daman and Diu","Delhi","Jammu and Kashmir","Ladakh","Lakshadweep","Puducherry");

$ck_msg = "";
$res_msg = "";
$the_dealer_id = "";
$the_dealer_name = "";
$the_dealer_mobile = "";
$the_cust_type = "";
$the_branch_code = "";
$totres_brnc = 0;

$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
if($the_id!=""){

$sql_brnc = "select `branch_code`,`branch_name`,`acedns` from $branch_master where `acedns`='Y' order by `branch_name` asc";
$res_brnc = mysql_query($sql_brnc);
$totres_brnc = mysql_num_rows($res_brnc);


$sql3 = "select `dns_customer_code`,`customer_name`,`phone_no`,`cust_type`,`branch_code` from $customer_master where `customer_code`='$the_id'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
$row3 = mysql_fetch_assoc($res3);
$the_dealer_id = $row3["dns_customer_code"] ? trim($row3["dns_customer_code"]) : "";
$the_dealer_name = $row3["customer_name"] ? trim($row3["customer_name"]) : "";
$the_dealer_mobile = $row3["phone_no"] ? trim($row3["phone_no"]) : "";
$the_cust_type = $row3["cust_type"] ? trim($row3["cust_type"]) : "";
$the_branch_code = $row3["branch_code"] ? trim($row3["branch_code"]) : "";
if($the_cust_type!="Dealer"){
$the_id = "";	
$ck_msg = "This survey form is for Dealer.";
}else{
if($_POST["sf_submit"]=="SUBMIT"){
	$dl_code = $_POST["dl_code"] ? addslashes(trim($_POST["dl_code"])) : "";
	$dl_name = $_POST["dl_name"] ? addslashes(trim($_POST["dl_name"])) : "";
	$dl_mob = $_POST["dl_mob"] ? addslashes(trim($_POST["dl_mob"])) : "";
	$dl_branch_code = $_POST["dl_branch"] ? addslashes(trim($_POST["dl_branch"])) : "";
	$is_return_fy18_19 = $_POST["is_return_fy18_19"] ? addslashes(trim($_POST["is_return_fy18_19"])) : "";
	$is_return_fy19_20 = $_POST["is_return_fy19_20"] ? addslashes(trim($_POST["is_return_fy19_20"])) : "";
	$is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20 = $_POST["is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20"] ? addslashes(trim($_POST["is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20"])) : "";
	$is_turnover_exceeds_10_crores_fy20_21 = $_POST["is_turnover_exceeds_10_crores_fy20_21"] ? addslashes(trim($_POST["is_turnover_exceeds_10_crores_fy20_21"])) : "";
	$dl_pan = $_POST["dl_pan"] ? addslashes(trim($_POST["dl_pan"])) : "";
	$is_checked_declaration = $_POST["is_checked_declaration"] ? addslashes(trim($_POST["is_checked_declaration"])) : "";
if($is_return_fy18_19==""){
	$res_msg = "Kindly confirm if you have filed return for FY18-19 for your Dealership Firm / Proprietorship.";
}else if($is_return_fy19_20==""){
	$res_msg = "Kindly confirm if you have filed return for FY19-20 for your Dealership Firm / Proprietorship.";
}else if($is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20==""){
	$res_msg = "Kindly confirm if you have claimed more than Rs. 50,000 in TDS / TCS either in FY18-19 or FY19-20.";
}else if($is_turnover_exceeds_10_crores_fy20_21==""){
	$res_msg = "Kindly confirm if Turnover of your Dealership Firm / Proprietorship exceeds Rs. 10 crores for FY20-21.";
}else if($dl_pan==""){
	$res_msg = "Please enter PAN.";
}else if(strlen($dl_pan)!=10){
	$res_msg = "PAN number should be 10 characters.";
}else if($is_checked_declaration==""){
	$res_msg = "Please check declaration option.";
}else{
	
	$dl_branch_data = get_branch_data_by_id($dl_branch_code);
	$dl_dns_branch_code = $dl_branch_data["dns_branch_code"];
	$dl_branch_name = $dl_branch_data["branch_name"];
	
	$sql_ck = "select `sf_id`,`sf_cust_code` from $survey_form where `sf_cust_code`='$the_id'";
	$res_ck = mysql_query($sql_ck);
	$totres_ck = mysql_num_rows($res_ck);
	if($totres_ck>0){
	$row_ck = mysql_fetch_assoc($res_ck);
	$sf_id = $row_ck["sf_id"];
	$sf_submitted_datetime = date("Y-m-d H:i:s");
	$sql_up = "update $survey_form set `sf_dealer_id`='$dl_code',`sf_dealer_name`='$dl_name',`sf_dealer_mobile`='$dl_mob',`sf_branch_code`='$dl_branch_code',`sf_dns_branch_code`='$dl_dns_branch_code',`sf_branch_name`='$dl_branch_name',`sf_is_return_fy18_19`='$is_return_fy18_19',`sf_is_return_fy19_20`='$is_return_fy19_20',`sf_is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20`='$is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20',`sf_is_turnover_exceeds_10_crores_fy20_21`='$is_turnover_exceeds_10_crores_fy20_21',`sf_pan`='$dl_pan',`sf_is_checked_declaration`='$is_checked_declaration',`sf_submitted_datetime`='$sf_submitted_datetime' where `sf_id`='$sf_id'";
	$res_up = mysql_query($sql_up);
	$the_message = "Thank you for submitting survey form.";
	header("location:".$success_page_name."?the_message=".$the_message);
	}else{
		$sf_submitted_datetime = date("Y-m-d H:i:s");
		$sql_in = "insert into $survey_form (`sf_cust_code`,`sf_dealer_id`,`sf_dealer_name`,`sf_dealer_mobile`,`sf_branch_code`,`sf_dns_branch_code`,`sf_branch_name`,`sf_is_return_fy18_19`,`sf_is_return_fy19_20`,`sf_is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20`,`sf_is_turnover_exceeds_10_crores_fy20_21`,`sf_pan`,`sf_is_checked_declaration`,`sf_submitted_datetime`) values ('$the_id','$dl_code','$dl_name','$dl_mob','$dl_branch_code','$dl_dns_branch_code','$dl_branch_name','$is_return_fy18_19','$is_return_fy19_20','$is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20','$is_turnover_exceeds_10_crores_fy20_21','$dl_pan','$is_checked_declaration','$sf_submitted_datetime')";
		$res_in = mysql_query($sql_in);
		if($res_in){
			$the_message = "Thank you for submitting survey form.";
			header("location:".$success_page_name."?the_message=".$the_message);
		}else{
			$res_msg = "Something went wrong.";
		}
	}
}
	
}
	
}
}else{
$the_id = "";
$ck_msg = "Something went wrong.";
}

}else{
$ck_msg = "Something went wrong.";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Star Saathi - DECLARATION FOR COMPLIANCE UNDER NEWLY INSERTED SECTIONS 206AB/ 206CCA/ 194Q/ 206C (1H) IN THE IT ACT, 1961</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<style>
.mand_cls{
	color: #F00;
	display:block;
	margin-top:2px;
}
.msg_cls{
	color: #F00;
	display:block;
	margin-top:2px;
}
</style>
</head>
<body>
<?php if($the_id==""){ ?>
	<h3 style="margin-top:30px;text-align:center;"><?php echo $ck_msg;?></h3>
<?php }else{
?>
<div class="container-fluid">
<div class="row" style="background-color:#ED2128; height:150px;">
<img class="img-responsive" style="margin:0 auto; padding-top:25px;" src="img/logo.png" alt="logo">
</div>
<div class="panel panel-default">
  <div class="panel-body">
  <h4>DECLARATION FOR COMPLIANCE UNDER NEWLY INSERTED SECTIONS 206AB/ 206CCA/ 194Q/ 206C (1H) IN THE IT ACT, 1961 </h4>
  <p style="color:#ED2128">* Required</p>
  <span class="msg_cls"><?php if($res_msg!=""){ echo $res_msg;}?></span>
  </div>
</div>
<form action="" method="POST" class="survey_form" id="survey_form" name="survey_form">
<div class="panel panel-default">
  <div class="panel-body">
  <div class="form-group">
  <label for="usr">Dealer Code <span style="color:#ED2128;">*</span></label>
  <input type="text" class="form-control" id="dl_code" name="dl_code" value="<?php echo $the_dealer_id;?>" readonly="readonly">
  </div>
  <span class="mand_cls" id="dl_code_error"></span>
  </div>
</div>
<div class="panel panel-default">
  <div class="panel-body">
  <div class="form-group">
  <label for="usr">Dealer Name <span style="color:#ED2128;">*</span></label>
  <input type="text" class="form-control" id="dl_name" name="dl_name" value="<?php echo $the_dealer_name;?>" readonly="readonly">
  </div>
  <span class="mand_cls" id="dl_name_error"></span>
  </div>
</div>
<div class="panel panel-default">
  <div class="panel-body">
  <div class="form-group">
  <label for="usr">Mobile no <span style="color:#ED2128;">*</span></label>
  <input type="tel" class="form-control" id="dl_mob" name="dl_mob" value="<?php echo $the_dealer_mobile;?>" readonly="readonly">
  </div>
  <span class="mand_cls" id="dl_mob_error"></span>
  </div>
</div>


<div class="panel panel-default">
  <div class="panel-body">
  <div class="form-group">
  <label for="sel1">Branch <span style="color:#ED2128;">*</span></label>
  <select class="form-control" id="dl_branch" name="dl_branch" readonly="readonly">
  <?php 
  if($totres_brnc>0){
	while($row_brnc=mysql_fetch_assoc($res_brnc)){
		$branch_code_each = $row_brnc["branch_code"];
		$branch_name_each = $row_brnc["branch_name"];
		 ?>
		<option value="<?php echo $branch_code_each;?>" <?php if($branch_code_each==$the_branch_code){?> selected="selected" <?php }else { ?> disabled="disabled" <?php } ?>><?php echo $branch_name_each;?></option>
	<?php }
	  
  }
  ?>
  </select>
</div>
<span class="mand_cls" id="dl_branch_error"></span>
  </div>
</div>


<?php /*?><div class="panel panel-default">
  <div class="panel-body">
  <div class="form-group">
  <label for="sel1">State <span style="color:#ED2128;">*</span></label>
  <select class="form-control" id="dl_state" name="dl_state">
  <option value="">Choose State</option>
  <?php 
  if(count($state_arr)>0){
	foreach($state_arr as $state_arr_val){ ?>
		<option value="<?php echo $state_arr_val;?>"><?php echo $state_arr_val;?></option>
	<?php }
	  
  }
  ?>
  </select>
</div>
<span class="mand_cls" id="dl_state_error"></span>
  </div>
</div><?php */?>

<div class="panel panel-default">
  <div class="panel-body">
  <div class="form-group">
  <label for="usr">Kindly confirm if you have filed return for FY18-19 for your Dealership firm/ Proprietorship:<span style="color:#ED2128;">*</span></label>
    <div class="radio">
    <label class="radio-inline"><input type="radio" name="is_return_fy18_19" value="YES">YES</label>
    <label class="radio-inline"><input type="radio" name="is_return_fy18_19" value="NO">NO</label>
    </div>
  </div>
  <span class="mand_cls" id="is_return_fy18_19_error"></span>
  </div>
</div>
<div class="panel panel-default">
  <div class="panel-body">
  <div class="form-group">
  <label for="usr">Kindly confirm if you have filed return for FY19-20 for your Dealership firm/ Proprietorship:<span style="color:#ED2128;">*</span></label>
    <div class="radio">
    <label class="radio-inline"><input type="radio" name="is_return_fy19_20" value="YES">YES</label>
    <label class="radio-inline"><input type="radio" name="is_return_fy19_20" value="NO">NO</label>
    </div>
  </div>
  <span class="mand_cls" id="is_return_fy19_20_error"></span>
  </div>
</div>
<div class="panel panel-default">
  <div class="panel-body">
  <div class="form-group">
  <label for="usr">Kindly confirm if you have claimed more than Rs. 50,000 in TDS/TCS either in FY18-19 or FY19-20 for your Dealership firm/ Proprietorship:<span style="color:#ED2128;">*</span></label>
    <div class="radio">
    <label class="radio-inline"><input type="radio" name="is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20" value="YES">YES</label>
    <label class="radio-inline"><input type="radio" name="is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20" value="NO">NO</label>
    </div>
  </div>
  <span class="mand_cls" id="is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20_error"></span>
  </div>
</div>
<div class="panel panel-default">
  <div class="panel-body">
  <div class="form-group">
  <label for="usr">Kindly confirm if Turnover of your Dealership firm/ Proprietorship exceeds Rs. 10 Crores for FY20-21 :<span style="color:#ED2128;">*</span></label>
    <div class="radio">
    <label class="radio-inline"><input type="radio" name="is_turnover_exceeds_10_crores_fy20_21" value="YES">YES</label>
    <label class="radio-inline"><input type="radio" name="is_turnover_exceeds_10_crores_fy20_21" value="NO">NO</label>
    </div>
  </div>
  <span class="mand_cls" id="is_turnover_exceeds_10_crores_fy20_21_error"></span>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-body">
  <div class="form-group">
  <label for="usr">My PAN is <span style="color:#ED2128;">*</span></label>
  <input type="text" class="form-control" id="dl_pan" name="dl_pan" >
  </div>
  <span class="mand_cls" id="dl_pan_error"></span>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-body">
  <div class="form-group">
  <label for="usr">Declaration: <span style="color:#ED2128;">*</span></label>
    <div class="checkbox">
    <label><input type="checkbox" name="is_checked_declaration" class="is_checked_declaration" id="is_checked_declaration" value="1">I hearby declare that the information furnished above is true to the best of my knowledge.</label>
    </div>
  </div>
  <span class="mand_cls" id="is_checked_declaration_error"></span>
  </div>
</div>
<input type="submit" name="sf_submit" value="SUBMIT" class="btn btn-info btn-lg btn-block" style="background-color:#ED2128;" />
</form>
</div>
<script type="text/javascript">
jQuery(document).ready(function () {

setTimeout(function(){
jQuery(".msg_cls").html("");	
},6000);

jQuery("#dl_mob").on("keypress keyup blur",function (event) {    
jQuery(this).val(jQuery(this).val().replace(/[^\d].+/, ""));
if ((event.which < 48 || event.which > 57)) {
event.preventDefault();
}
});

jQuery("form#survey_form").submit(function(){
	var dl_pan = jQuery.trim(jQuery("#dl_pan").val());
	
	if (!jQuery("input[name='is_return_fy18_19']:checked").val()) {
		jQuery("#is_return_fy18_19_error").html("Please choose this option.");
		setTimeout(function(){
		jQuery("#is_return_fy18_19_error").html("");	
		},6000);
        return false;
    }else if (!jQuery("input[name='is_return_fy19_20']:checked").val()) {
		jQuery("#is_return_fy19_20_error").html("Please choose this option.");
		setTimeout(function(){
		jQuery("#is_return_fy19_20_error").html("");	
		},6000);
        return false;
    }else if (!jQuery("input[name='is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20']:checked").val()) {
		jQuery("#is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20_error").html("Please choose this option.");
		setTimeout(function(){
		jQuery("#is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20_error").html("");	
		},6000);
        return false;
    }else if (!jQuery("input[name='is_turnover_exceeds_10_crores_fy20_21']:checked").val()) {
		jQuery("#is_turnover_exceeds_10_crores_fy20_21_error").html("Please choose this option.");
		setTimeout(function(){
		jQuery("#is_turnover_exceeds_10_crores_fy20_21_error").html("");	
		},6000);
        return false;
    }else if(dl_pan==""){
		jQuery("#dl_pan").focus();
		jQuery("#dl_pan_error").html("Please enter PAN.");
		setTimeout(function(){
		jQuery("#dl_pan_error").html("");	
		},6000);
		return false;
	}else if(dl_pan.length!=10){
		jQuery("#dl_pan").focus();
		jQuery("#dl_pan_error").html("PAN number should be 10 characters.");
		setTimeout(function(){
		jQuery("#dl_pan_error").html("");	
		},6000);
		return false;
	}else if(jQuery("#is_checked_declaration").prop("checked") == false){
		jQuery("#is_checked_declaration_error").html("Please choose this option.");
		setTimeout(function(){
		jQuery("#is_checked_declaration_error").html("");	
		},6000);
        return false;
	}else{
		return true;
	}
	
});

});

</script>
<?php } ?>
</body>
</html>

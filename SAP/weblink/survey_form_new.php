<?php
include "star_connection.php";
$survey_form = "survey_form";
$customer_master = "customer_master";
$branch_master = "branch_master";
$nye_branch = "nye_branch";
$survey_for_new_year_eve_carnival_concert = "survey_for_new_year_eve_carnival_concert";
$success_page_name = "survey_success_page.php";

$enabled_branch_array = array();
$sql_enbld_brnc = "select * from $nye_branch";
$res_enbld_brnc = mysql_query($sql_enbld_brnc);
$totres_enbld_brnc = mysql_num_rows($res_enbld_brnc);
if($totres_enbld_brnc>0){
while($row_enbld_brnc = mysql_fetch_assoc($res_enbld_brnc)){
	$the_nb_branch_code = $row_enbld_brnc["nb_branch_code"] ? trim($row_enbld_brnc["nb_branch_code"]) : "";
	if($the_nb_branch_code!=""){
		$enabled_branch_array[] = $the_nb_branch_code;
	}
}
}

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
$the_dl_branch_name = "";
$totres_brnc = 0;

$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
if($the_id!=""){

$sql_brnc = "select `branch_code`,`branch_name`,`acedns` from $branch_master where `acedns`='Y' order by `branch_name` asc";
$res_brnc = mysql_query($sql_brnc);
$totres_brnc = mysql_num_rows($res_brnc);


$sql3 = "select `dns_customer_code`,`customer_name`,`phone_no`,`cust_type`,`branch_code`,`customer_id` from $customer_master where `customer_code`='$the_id'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
$row3 = mysql_fetch_assoc($res3);
$the_dealer_id = $row3["dns_customer_code"] ? trim($row3["dns_customer_code"]) : "";
$the_dealer_name = $row3["customer_name"] ? trim($row3["customer_name"]) : "";
$the_dealer_mobile = $row3["phone_no"] ? trim($row3["phone_no"]) : "";
$the_cust_type = $row3["cust_type"] ? trim($row3["cust_type"]) : "";
$the_branch_code = $row3["branch_code"] ? trim($row3["branch_code"]) : "";

$the_dl_branch_data = get_branch_data_by_id($the_branch_code);
$the_dl_branch_name = $the_dl_branch_data["branch_name"];

$the_customer_id = $row3["customer_id"] ? trim($row3["customer_id"]) : "";
if($the_cust_type!="Dealer"){
$the_id = "";	
$ck_msg = "This survey form is for Dealer.";
}else{

if(in_array($the_branch_code,$enabled_branch_array)){



if($_POST["sf_submit"]=="SUBMIT"){
	$dl_sap_code = $_POST["dl_sap_code"] ? addslashes(trim($_POST["dl_sap_code"])) : "";
	$dl_name = $_POST["dl_name"] ? addslashes(trim($_POST["dl_name"])) : "";
	$dl_mob = $_POST["dl_mob"] ? addslashes(trim($_POST["dl_mob"])) : "";
	$dl_branch_code = $_POST["dl_branch"] ? addslashes(trim($_POST["dl_branch"])) : "";
	
$sf_1_attending = $_POST["sf_1_attending"] ? addslashes(trim($_POST["sf_1_attending"])) : "No";
$sf_a_alan_walker = $_POST["sf_a_alan_walker"] ? addslashes(trim($_POST["sf_a_alan_walker"])) : "N";
$sf_b_badhshah = $_POST["sf_b_badhshah"] ? addslashes(trim($_POST["sf_b_badhshah"])) : "N";
$sf_c_david_guetta = $_POST["sf_c_david_guetta"] ? addslashes(trim($_POST["sf_c_david_guetta"])) : "N";
$sf_d_sunidhi_chauhan = $_POST["sf_d_sunidhi_chauhan"] ? addslashes(trim($_POST["sf_d_sunidhi_chauhan"])) : "N";
$sf_e_lucky_ali = $_POST["sf_e_lucky_ali"] ? addslashes(trim($_POST["sf_e_lucky_ali"])) : "N";
$sf_f_zubeen_garg = $_POST["sf_f_zubeen_garg"] ? addslashes(trim($_POST["sf_f_zubeen_garg"])) : "N";
$sf_g_pitbull = $_POST["sf_g_pitbull"] ? addslashes(trim($_POST["sf_g_pitbull"])) : "N";
$sf_h_mika_singh = $_POST["sf_h_mika_singh"] ? addslashes(trim($_POST["sf_h_mika_singh"])) : "N";
$sf_i_arijit_singh = $_POST["sf_i_arijit_singh"] ? addslashes(trim($_POST["sf_i_arijit_singh"])) : "N";
$sf_j_papon = $_POST["sf_j_papon"] ? addslashes(trim($_POST["sf_j_papon"])) : "N";
$sf_k_dj_nucleya = $_POST["sf_k_dj_nucleya"] ? addslashes(trim($_POST["sf_k_dj_nucleya"])) : "N";
$sf_l_ankit_tiwari = $_POST["sf_l_ankit_tiwari"] ? addslashes(trim($_POST["sf_l_ankit_tiwari"])) : "N";
$sf_m_others = $_POST["sf_m_others"] ? addslashes(trim($_POST["sf_m_others"])) : "";
$sf_3a_2000 = "N";
$sf_3b_3000 = "N";
$sf_3c_4000 = "N";
$sf_3d_5000 = "N";
$sf_3e_7000 = "N";

$sf_pay_ticket = $_POST["sf_pay_ticket"] ? addslashes(trim($_POST["sf_pay_ticket"])) : "";
if($sf_pay_ticket=="2000"){
$sf_3a_2000 = "Y";
}else if($sf_pay_ticket=="3000"){
$sf_3b_3000 = "Y";
}else if($sf_pay_ticket=="4000"){
$sf_3c_4000 = "Y";
}else if($sf_pay_ticket=="5000"){
$sf_3d_5000 = "Y";
}else if($sf_pay_ticket=="7000"){
$sf_3e_7000 = "Y";
}

$sf_4a_spouse_only = $_POST["sf_4a_spouse_only"] ? addslashes(trim($_POST["sf_4a_spouse_only"])) : "N";
$sf_4b_spouse_and_kid = $_POST["sf_4b_spouse_and_kid"] ? addslashes(trim($_POST["sf_4b_spouse_and_kid"])) : "N";
$sf_4c_friends_and_colleagues = $_POST["sf_4c_friends_and_colleagues"] ? addslashes(trim($_POST["sf_4c_friends_and_colleagues"])) : "N";
$sf_4d_parents_and_siblings = $_POST["sf_4d_parents_and_siblings"] ? addslashes(trim($_POST["sf_4d_parents_and_siblings"])) : "N";
$sf_4e_solo = $_POST["sf_4e_solo"] ? addslashes(trim($_POST["sf_4e_solo"])) : "N";
$sf_5_liquour = $_POST["sf_5_liquour"] ? addslashes(trim($_POST["sf_5_liquour"])) : "No";
$sf_6_30th_december  = $_POST["sf_6_30th_december"] ? addslashes(trim($_POST["sf_6_30th_december"])) : "No";
$sf_7_31st_december  = $_POST["sf_7_31st_december"] ? addslashes(trim($_POST["sf_7_31st_december"])) : "No";

	
	$is_checked_declaration = $_POST["is_checked_declaration"] ? addslashes(trim($_POST["is_checked_declaration"])) : "";
if($sf_1_attending==""){
	$res_msg = "Please select attending option.";
}else{
	
	$dl_branch_data = get_branch_data_by_id($dl_branch_code);
	$dl_dns_branch_code = $dl_branch_data["dns_branch_code"];
	$dl_branch_name = $dl_branch_data["branch_name"];
	
	$sql_ck = "select `sf_id`,`sf_cust_code` from $survey_for_new_year_eve_carnival_concert where `sf_cust_code`='$the_id'";
	$res_ck = mysql_query($sql_ck);
	$totres_ck = mysql_num_rows($res_ck);
	if($totres_ck>0){
	$row_ck = mysql_fetch_assoc($res_ck);
	$sf_id = $row_ck["sf_id"];
	$sf_submitted_datetime = date("Y-m-d H:i:s");
	$sql_up = "update $survey_for_new_year_eve_carnival_concert set `sf_dealer_id`='".addslashes($the_dealer_id)."',`sf_dealer_sap_code`='$dl_sap_code',`sf_dealer_name`='$dl_name',`sf_dealer_mobile`='$dl_mob',`sf_branch_code`='$dl_branch_code',`sf_dns_branch_code`='$dl_dns_branch_code',`sf_branch_name`='$dl_branch_name',`sf_1_attending`='$sf_1_attending',`sf_a_alan_walker`='$sf_a_alan_walker',`sf_b_badhshah`='$sf_b_badhshah',`sf_c_david_guetta`='$sf_c_david_guetta',`sf_d_sunidhi_chauhan`='$sf_d_sunidhi_chauhan',`sf_e_lucky_ali`='$sf_e_lucky_ali',`sf_f_zubeen_garg`='$sf_f_zubeen_garg',`sf_g_pitbull`='$sf_g_pitbull',`sf_h_mika_singh`='$sf_h_mika_singh',`sf_i_arijit_singh`='$sf_i_arijit_singh',`sf_j_papon`='$sf_j_papon',`sf_k_dj_nucleya`='$sf_k_dj_nucleya',`sf_l_ankit_tiwari`='$sf_l_ankit_tiwari',`sf_m_others`='$sf_m_others',`sf_3a_2000`='$sf_3a_2000',`sf_3b_3000`='$sf_3b_3000',`sf_3c_4000`='$sf_3c_4000',`sf_3d_5000`='$sf_3d_5000',`sf_3e_7000`='$sf_3e_7000',`sf_4a_spouse_only`='$sf_4a_spouse_only',`sf_4b_spouse_and_kid`='$sf_4b_spouse_and_kid',`sf_4c_friends_and_colleagues`='$sf_4c_friends_and_colleagues',`sf_4d_parents_and_siblings`='$sf_4d_parents_and_siblings',`sf_4e_solo`='$sf_4e_solo',`sf_5_liquour`='$sf_5_liquour',`sf_6_30th_december`='$sf_6_30th_december',`sf_7_31st_december`='$sf_7_31st_december',`sf_submitted_datetime`='$sf_submitted_datetime' where `sf_id`='$sf_id'";
	$res_up = mysql_query($sql_up);
	$the_message = "Thank you for submitting.";
	header("location:".$success_page_name."?the_message=".$the_message);
	}else{
		$sf_submitted_datetime = date("Y-m-d H:i:s");
		$sql_in = "insert into $survey_for_new_year_eve_carnival_concert (`sf_cust_code`,`sf_dealer_id`,`sf_dealer_sap_code`,`sf_dealer_name`,`sf_dealer_mobile`,`sf_branch_code`,`sf_dns_branch_code`,`sf_branch_name`,`sf_1_attending`,`sf_a_alan_walker`,`sf_b_badhshah`,`sf_c_david_guetta`,`sf_d_sunidhi_chauhan`,`sf_e_lucky_ali`,`sf_f_zubeen_garg`,`sf_g_pitbull`,`sf_h_mika_singh`,`sf_i_arijit_singh`,`sf_j_papon`,`sf_k_dj_nucleya`,`sf_l_ankit_tiwari`,`sf_m_others`,`sf_3a_2000`,`sf_3b_3000`,`sf_3c_4000`,`sf_3d_5000`,`sf_3e_7000`,`sf_4a_spouse_only`,`sf_4b_spouse_and_kid`,`sf_4c_friends_and_colleagues`,`sf_4d_parents_and_siblings`,`sf_4e_solo`,`sf_5_liquour`,`sf_6_30th_december`,`sf_7_31st_december`,`sf_submitted_datetime`) values ('$the_id','".addslashes($the_dealer_id)."','$dl_sap_code','$dl_name','$dl_mob','$dl_branch_code','$dl_dns_branch_code','$dl_branch_name','$sf_1_attending','$sf_a_alan_walker','$sf_b_badhshah','$sf_c_david_guetta','$sf_d_sunidhi_chauhan','$sf_e_lucky_ali','$sf_f_zubeen_garg','$sf_g_pitbull','$sf_h_mika_singh','$sf_i_arijit_singh','$sf_j_papon','$sf_k_dj_nucleya','$sf_l_ankit_tiwari','$sf_m_others','$sf_3a_2000','$sf_3b_3000','$sf_3c_4000','$sf_3d_5000','$sf_3e_7000','$sf_4a_spouse_only','$sf_4b_spouse_and_kid','$sf_4c_friends_and_colleagues','$sf_4d_parents_and_siblings','$sf_4e_solo','$sf_5_liquour','$sf_6_30th_december','$sf_7_31st_december','$sf_submitted_datetime')";
		$res_in = mysql_query($sql_in);
		if($res_in){
			$the_message = "Thank you for submitting.";
			header("location:".$success_page_name."?the_message=".$the_message);
		}else{
			$res_msg = "Something went wrong.";
		}
	}
}
	
}


}else{
$the_id = "";
$ck_msg = "Your branch is not enabled.";	
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
<title>Star Saathi - IMPORTANT NOTICE 2023</title>
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
.artist_ul li{
list-style: none;
font-size: 18px;
padding-bottom: 7px;
}
.pay_ticket_ul{
list-style: none;
font-size: 18px;
padding-bottom: 7px;
}
.who_gowith_ul{
list-style: none;
font-size: 18px;
padding-bottom: 7px;
}
.show_hide_div{
	display:none;
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
<img class="img-responsive" style="margin:0 auto; padding-top:4px;width: 138px;" src="img/logo.jpeg" alt="logo">
</div>
<div class="panel panel-default">
  <div class="panel-body">
  <h4>IMPORTANT NOTICE </h4>
  <h4>Survey for New Year Eve Carnival/Concert </h4>
  <p style="color:#ED2128">* Required</p>
  <span class="msg_cls"><?php if($res_msg!=""){ echo $res_msg;}?></span>
  </div>
</div>
<form action="" method="POST" class="survey_form" id="survey_form" name="survey_form">

<input type="hidden" id="dl_sap_code" name="dl_sap_code" value="<?php echo $the_customer_id;?>">
<input type="hidden" id="dl_name" name="dl_name" value="<?php echo $the_dealer_name;?>">
<input type="hidden" id="dl_mob" name="dl_mob" value="<?php echo $the_dealer_mobile;?>">
<input type="hidden" id="dl_branch" name="dl_branch" value="<?php echo $the_branch_code;?>">

<div class="panel panel-default">
<div class="panel-body">
<div class="form-group">
<label for="usr">1.	Will you be interested in attending, if one of the Biggest New Year Carnival/Concert in the Country is organized in North East on 31st December-New Year Eve? <span style="color:#ED2128;">*</span></label>
<div class="radio">
<label class="radio-inline"><input type="radio" name="sf_1_attending" value="Yes" autocomplete="off">Yes</label>
<label class="radio-inline"><input type="radio" name="sf_1_attending" value="No" autocomplete="off">No</label>
<label class="radio-inline"><input type="radio" name="sf_1_attending" value="Maybe" autocomplete="off">Maybe</label>
</div>
</div>
<span class="mand_cls" id="sf_1_attending_error"></span>
</div>
</div>


<div class="panel panel-default artist_div show_hide_div">
  <div class="panel-body">
  <div class="form-group">
  <label for="usr">2. Which Artist would you like to perform for the New Year Eve Carnival/Concert? (You can select Multiple Artists) <span style="color:#ED2128;">*</span></label>
    
<ul class="artist_ul">

<li>
<div class="checkbox">
<label><input type="checkbox" name="sf_a_alan_walker" class="sf_a_alan_walker" id="sf_a_alan_walker" value="Y">a. Alan Walker</label>
</div>
</li>

<li>
<div class="checkbox">
<label><input type="checkbox" name="sf_b_badhshah" class="sf_b_badhshah" id="sf_b_badhshah" value="Y">b. Badhshah</label>
</div>
</li>

<li>
<div class="checkbox">
<label><input type="checkbox" name="sf_c_david_guetta" class="sf_c_david_guetta" id="sf_c_david_guetta" value="Y">c. David Guetta</label>
</div>
</li>

<li>
<div class="checkbox">
<label><input type="checkbox" name="sf_d_sunidhi_chauhan" class="sf_d_sunidhi_chauhan" id="sf_d_sunidhi_chauhan" value="Y">d. Sunidhi Chauhan</label>
</div>
</li>

<li>
<div class="checkbox">
<label><input type="checkbox" name="sf_e_lucky_ali" class="sf_e_lucky_ali" id="sf_e_lucky_ali" value="Y">e. Lucky Ali</label>
</div>
</li>

<li>
<div class="checkbox">
<label><input type="checkbox" name="sf_f_zubeen_garg" class="sf_f_zubeen_garg" id="sf_f_zubeen_garg" value="Y">f. Zubeen Garg</label>
</div>
</li>

<li>
<div class="checkbox">
<label><input type="checkbox" name="sf_g_pitbull" class="sf_g_pitbull" id="sf_g_pitbull" value="Y">g. Pitbull</label>
</div>
</li>

<li>
<div class="checkbox">
<label><input type="checkbox" name="sf_h_mika_singh" class="sf_h_mika_singh" id="sf_h_mika_singh" value="Y">h. Mika Singh</label>
</div>
</li>

<li>
<div class="checkbox">
<label><input type="checkbox" name="sf_i_arijit_singh" class="sf_i_arijit_singh" id="sf_i_arijit_singh" value="Y">i. Arijit Singh</label>
</div>
</li>

<li>
<div class="checkbox">
<label><input type="checkbox" name="sf_j_papon" class="sf_j_papon" id="sf_j_papon" value="Y">j. Papon</label>
</div>
</li>

<li>
<div class="checkbox">
<label><input type="checkbox" name="sf_k_dj_nucleya" class="sf_k_dj_nucleya" id="sf_k_dj_nucleya" value="Y">k. DJ Nucleya</label>
</div>
</li>

<li>
<div class="checkbox">
<label><input type="checkbox" name="sf_l_ankit_tiwari" class="sf_l_ankit_tiwari" id="sf_l_ankit_tiwari" value="Y">l. Ankit Tiwari</label>
</div>
</li>


<li>m. Others (Please Specify)</li>
<li>
<div class="form-group">
<input type="text" class="form-control" id="sf_m_others" name="sf_m_others" >
</div>
<span class="mand_cls" id="sf_m_others_error"></span>
</li>


</ul>
    
  </div>
  </div>
</div>


<div class="panel panel-default pay_ticket_div show_hide_div">
  <div class="panel-body">
  <div class="form-group">
  <label for="usr">3. How much are you willing to pay per Ticket for one of the Biggest New Year Carnival/Concert in the Country being organized in North East on 31st December-New Year Eve?<span style="color:#ED2128;">*</span></label>
    
<ul class="pay_ticket_ul">
<li>
<label class="radio-inline"><input type="radio" name="sf_pay_ticket" autocomplete="off" value="2000">a. Less than Rs. 2000 (Entry only and seating at a distant Gallery from the Stage)</label>
</li>

<li>
<label class="radio-inline"><input type="radio" name="sf_pay_ticket" autocomplete="off" value="3000">b. Rs. 3000 (Entry only and standing in the last rows of the Ground)</label>
</li>

<li>
<label class="radio-inline"><input type="radio" name="sf_pay_ticket" autocomplete="off" value="4000">c. Rs. 4000 (Entry only and seating in the middle rows of the Ground)</label>
</li>

<li>
<label class="radio-inline"><input type="radio" name="sf_pay_ticket" autocomplete="off" value="5000">d. Rs. 5000 (Entry only and seating in front rows of the Ground)</label>
</li>

<li>
<label class="radio-inline"><input type="radio" name="sf_pay_ticket" autocomplete="off" value="7000">e. Rs. 7000 (Entry with Full Course Dinner and seating in front rows of the Ground)</label>
</li>

</ul>
    
  </div>
<span class="mand_cls" id="sf_pay_ticket_error"></span>
  </div>
</div>


<div class="panel panel-default who_gowith_div show_hide_div">
  <div class="panel-body">
  <div class="form-group">
  <label for="usr">4. For this Biggest New Year Carnival/Concert in the Country being organized in North East on 31st December-New Year Eve,who would you like to go with? (You can select Multiple Option) <span style="color:#ED2128;">*</span></label>
    
<ul class="who_gowith_ul">



<li>
<div class="checkbox">
<label><input type="checkbox" name="sf_4a_spouse_only" class="sf_4a_spouse_only" id="sf_4a_spouse_only" value="Y">a. With my Spouse only</label>
</div>
</li>

<li>
<div class="checkbox">
<label><input type="checkbox" name="sf_4b_spouse_and_kid" class="sf_4b_spouse_and_kid" id="sf_4b_spouse_and_kid" value="Y">b. With my Spouse & Kid(s)</label>
</div>
</li>




<li>
<div class="checkbox">
<label><input type="checkbox" name="sf_4c_friends_and_colleagues" class="sf_4c_friends_and_colleagues" id="sf_4c_friends_and_colleagues" value="Y">c. With my Friends & Colleagues</label>
</div>
</li>

<li>
<div class="checkbox">
<label><input type="checkbox" name="sf_4d_parents_and_siblings" class="sf_4d_parents_and_siblings" id="sf_4d_parents_and_siblings" value="Y">d. With my Parents & Siblings</label>
</div>
</li>


<li>
<div class="checkbox">
<label><input type="checkbox" name="sf_4e_solo" class="sf_4e_solo" id="sf_4e_solo" value="Y">e. Solo (Alone)</label>
</div>
</li>

</ul>
    
  </div>
  </div>
</div>


<div class="panel panel-default liquor_div show_hide_div">
<div class="panel-body">
<div class="form-group">
<label for="usr">5. Do you want Liquor to be available in the New Year Carnival/Concert? <span style="color:#ED2128;">*</span></label>
<div class="radio">
<label class="radio-inline"><input type="radio" name="sf_5_liquour" autocomplete="off" value="Yes">Yes</label>
<label class="radio-inline"><input type="radio" name="sf_5_liquour" autocomplete="off" value="No">No</label>
</div>
</div>
<span class="mand_cls" id="sf_5_liquour_error"></span>
</div>
</div>

<div class="panel panel-default next_concert_div show_hide_div">
<div class="panel-body">
<div class="form-group">
<label for="usr">6. If the same Concert happens on 30th December (Saturday), will you be interested to attend? <span style="color:#ED2128;">*</span></label>
<div class="radio">
<label class="radio-inline"><input type="radio" name="sf_6_30th_december" autocomplete="off" value="Yes">Yes</label>
<label class="radio-inline"><input type="radio" name="sf_6_30th_december" autocomplete="off" value="No">No</label>
</div>
</div>
<span class="mand_cls" id="sf_6_30th_december_error"></span>
</div>
</div>


<div class="panel panel-default next_concert_31_div show_hide_div">
<div class="panel-body">
<div class="form-group">
<label for="usr">7. Will you still be interested to attend the New Year Carnival/Concert on 31st Dec if Liquor is not available? <span style="color:#ED2128;">*</span></label>
<div class="radio">
<label class="radio-inline"><input type="radio" name="sf_7_31st_december" autocomplete="off" value="Yes">Yes</label>
<label class="radio-inline"><input type="radio" name="sf_7_31st_december" autocomplete="off" value="No">No</label>
</div>
</div>
<span class="mand_cls" id="sf_7_31st_december_error"></span>
</div>
</div>


<input type="submit" name="sf_submit" value="SUBMIT" class="btn btn-info btn-lg btn-block" style="background-color:#ED2128;" />
</form>
</div>
<script type="text/javascript">
jQuery(document).ready(function () {

jQuery('input[name="sf_1_attending"]').change(function(){
var sf_1_attending = jQuery(this).val();
if(sf_1_attending=="Yes"){
	jQuery(".show_hide_div").show();
}else if(sf_1_attending=="Maybe"){
	jQuery(".show_hide_div").show();
}else if(sf_1_attending=="No"){
	jQuery(".show_hide_div").hide();
}else{
	jQuery(".show_hide_div").hide();
}
});


setTimeout(function(){
jQuery(".msg_cls").html("");	
},6000);


jQuery("form#survey_form").submit(function(){
	var sf_m_others = jQuery.trim(jQuery("#sf_m_others").val());
var sf_1_attending_sts = jQuery("input:radio[name='sf_1_attending']").is(":checked");
var sf_pay_ticket_sts = jQuery("input:radio[name='sf_pay_ticket']").is(":checked");

var sf_5_liquour_sts = jQuery("input:radio[name='sf_5_liquour']").is(":checked");
var sf_6_30th_december_sts = jQuery("input:radio[name='sf_6_30th_december']").is(":checked");
var sf_7_31st_december_sts = jQuery("input:radio[name='sf_7_31st_december']").is(":checked");


if (sf_1_attending_sts==false) {
jQuery("#sf_1_attending_error").html("Please choose this option.");
setTimeout(function(){
jQuery("#sf_1_attending_error").html("");	
},8000);
return false;
}else{
var sf_1_attending = jQuery('input[name="sf_1_attending"]:checked').val();
alert(sf_1_attending);
if(sf_1_attending=="Yes" || sf_1_attending=="Maybe"){

if (sf_pay_ticket_sts==false) {
jQuery("#sf_pay_ticket_error").html("Please choose this option.");
setTimeout(function(){
jQuery("#sf_pay_ticket_error").html("");	
},8000);
return false;
}else if (sf_5_liquour_sts==false) {
jQuery("#sf_5_liquour_error").html("Please choose this option.");
setTimeout(function(){
jQuery("#sf_5_liquour_error").html("");	
},8000);
return false;
}else if (sf_6_30th_december_sts==false) {
jQuery("#sf_6_30th_december_error").html("Please choose this option.");
setTimeout(function(){
jQuery("#sf_6_30th_december_error").html("");	
},8000);
return false;
}else if (sf_7_31st_december_sts==false) {
jQuery("#sf_7_31st_december_error").html("Please choose this option.");
setTimeout(function(){
jQuery("#sf_7_31st_december_error").html("");	
},8000);
return false;
}else{
return true;
}	
}else{
return true;
}


}
	
});

});

</script>
<?php } ?>
</body>
</html>

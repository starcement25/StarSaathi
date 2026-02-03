<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & E_NOTICE & E_DEPRECATED);
if(isset($_SESSION["sswa_user_id"])){
header("location:main.php");
}
include "star_connection.php";

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Star Cement Customer Portal</title>
    <!-- Favicon-->
    <link rel="icon" href="images/star_logo.jpg" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="css/style.css" rel="stylesheet">
<!-- Jquery Core Js -->
    <script src="plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="plugins/node-waves/waves.js"></script>

    <!-- Validation Plugin Js -->
    <script src="plugins/jquery-validation/jquery.validate.js"></script>

    <!-- Custom Js -->
    <script src="js/admin.js"></script>
    <script src="js/pages/examples/sign-in.js"></script>
<style>
#dealer_login_btn_part,#dealer_otp_part,#dealer_back_btn_part{
	display:none;
}
.dealer_otp{
	text-align:center;
}
</style>
</head>

<body class="login-page">
<h3 style="text-align:center; color:#ffffff;">Star Cement Customer Portal</h3>
    <div class="login-box">
    
        <div class="card">
            <div class="body" style="padding-top:20px !important;">
<form id="sign_in" method="POST" action="">
<div class="msg" style="background-color:#ed2726; margin-top:10px;"><img style="text-align:center;" src="images/star_logo.jpg" width="160px"></div>

<div class="input-group" id="dealer_id_part">
<span class="input-group-addon">
<i class="material-icons">person</i>
</span>
<div class="form-line">
<input type="text" class="form-control dealer_id" name="dealer_id" id="dealer_id" placeholder="Enter Dealer ID (Example - 10******01)" autocomplete="off" required autofocus>
</div>
</div>

<div class="input-group" id="dealer_mobile_part">
<span class="input-group-addon">
<i class="material-icons">dialpad</i>
</span>
<div class="form-line">
<input type="tel" class="form-control mob_no" name="mob_no" id="mob_no" maxlength="10" placeholder="Enter Mobile No." autocomplete="off" required>
</div>
</div>

<div class="input-group" id="dealer_otp_part">
<div class="form-line">
<input type="tel" class="form-control dealer_otp" name="dealer_otp" id="dealer_otp" placeholder="Enter OTP" autocomplete="off" required>
</div>
</div>

<div class="input-group" id="dealer_continue_btn_part">
<input type="button" class="btn btn-block bg-red btn-lg waves-effect continue_btn" value="CONTINUE" name="continue_btn" id="continue_btn" />
</div>

<div class="input-group" id="dealer_login_btn_part">
<input type="button" class="btn btn-block bg-red btn-lg waves-effect login_btn" value="LOG IN" name="login_btn" id="login_btn" />
</div>

<div class="input-group" id="dealer_back_btn_part">
<input type="button" class="btn btn-block bg-red btn-lg waves-effect the_back_btn" value="BACK" name="the_back_btn" id="the_back_btn" />
</div>

<div class="input-group" style="display: block;width: 100%;height: 35px;padding-bottom: 10px;text-align:center;">
<span class="msg_resp" id="msg_resp"></span>
</div>

</form>
            </div>
        </div>
    </div>
<script type="text/javascript">
jQuery(document).ready(function() {
var xhrlgin,xhrconflgin;
var img1 = '<img src="images/ajax-loader.gif">';
jQuery("#dealer_back_btn_part").click(function(){
jQuery("#dealer_continue_btn_part").show();
jQuery("#dealer_id_part").show();
jQuery("#dealer_mobile_part").show();

jQuery("#dealer_otp_part").hide();
jQuery("#dealer_otp").val("");
jQuery("#dealer_login_btn_part").hide();
jQuery("#dealer_back_btn_part").hide();

jQuery("#msg_resp").html("");	
});


jQuery("#continue_btn").click(function(){
		var dealer_id = jQuery.trim(jQuery("#dealer_id").val());
		var mob_no = jQuery.trim(jQuery("#mob_no").val());
		var msg_resp_elmnt = jQuery("#msg_resp");
	//alert(dealer_id);
		if(dealer_id==""){
			msg_resp_elmnt.html("Please enter Dealer ID.");
			jQuery("#dealer_id").focus();
			setTimeout(function(){
				msg_resp_elmnt.html("");
			},5000);
		}else if(mob_no==""){
			msg_resp_elmnt.html("Please enter Mobile Number.");
			jQuery("#mob_no").focus();
			setTimeout(function(){
				msg_resp_elmnt.html("");
			},5000);
		}else if(mob_no.length<10){
			msg_resp_elmnt.html("Mobile Number should be 10 digits.");
			jQuery("#mob_no").focus();
			setTimeout(function(){
				msg_resp_elmnt.html("");
			},5000);
		}else if(!mob_no.match('[0-9]{10}')){
			msg_resp_elmnt.html("Please enter 10 digit mobile number");
			jQuery("#mob_no").focus();
			setTimeout(function(){
				msg_resp_elmnt.html("");
			},5000);
		}else{
			msg_resp_elmnt.html(img1);
			if(xhrlgin && xhrlgin.readystate != 4){
			xhrlgin.abort();
			}
			xhrlgin = jQuery.ajax({
			url: 'ajax_generate_otp.php',
			type: 'post',
			dataType: 'json',
			data: "dealer_id="+dealer_id+"&mob_no="+mob_no,
			success: function(response){
			if(response.process_sts=="YES"){
			jQuery("#dealer_continue_btn_part").hide();
			jQuery("#dealer_id_part").hide();
			jQuery("#dealer_mobile_part").hide();
			
			jQuery("#dealer_otp_part").show();
			jQuery("#dealer_login_btn_part").show();
			jQuery("#dealer_back_btn_part").show();
			msg_resp_elmnt.html("");
				
			}else{
			msg_resp_elmnt.html(response.process_msg);
			setTimeout(function(){
			msg_resp_elmnt.html("");
			},8000);
			}					
			},
			timeout : 0
			});
			
		}
	});

jQuery("#login_btn").click(function(){
		var dealer_id = jQuery.trim(jQuery("#dealer_id").val());
		var mob_no = jQuery.trim(jQuery("#mob_no").val());
		var dealer_otp = jQuery.trim(jQuery("#dealer_otp").val());
		var msg_resp_elmnt = jQuery("#msg_resp");
	//alert(dealer_id);
		if(dealer_otp==""){
			msg_resp_elmnt.html("Please enter OTP.");
			jQuery("#dealer_otp").focus();
			setTimeout(function(){
				msg_resp_elmnt.html("");
			},5000);
		}else{
			msg_resp_elmnt.html(img1);
			if(xhrconflgin && xhrconflgin.readystate != 4){
			xhrconflgin.abort();
			}
			xhrconflgin = jQuery.ajax({
			url: 'ajax_otp_login.php',
			type: 'post',
			dataType: 'json',
			data: "dealer_id="+dealer_id+"&mob_no="+mob_no+"&dealer_otp="+dealer_otp,
			success: function(response){

			if(response.process_sts=="YES"){
			msg_resp_elmnt.html("");
				window.location = "main.php";
			}else{
			msg_resp_elmnt.html(response.process_msg);
			setTimeout(function(){
			msg_resp_elmnt.html("");
			},8000);
			}					
			},
			timeout : 0
			});
			
		}
	});


});
</script>
    
</body>

</html>
<?php
mysql_close();
?>
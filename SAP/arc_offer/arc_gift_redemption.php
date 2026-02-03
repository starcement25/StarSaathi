<?php
ini_set('memory_limit', '99999M');
set_time_limit(0);
include "star_connection.php";

$is_show = "NO";
$show_message = "";

$user_type = $_GET["user_type"] ? strtolower($_GET["user_type"]) : "";
$login_user_id = $_GET["login_user_id"] ? urldecode($_GET["login_user_id"]) : "";

if($user_type!="" && $login_user_id!=""){
if($user_type=="dealer"){
	$customer_data_arr = get_customer_data_check_by_id($login_user_id);
	$the_sts = $customer_data_arr["sts"];
	$is_branch_arc = $customer_data_arr["is_branch_arc"];
	if($the_sts=="YES"){
		if($is_branch_arc=="YES"){
			$is_show = "YES";
		}else{
			$show_message = "Feature not available.";
		}		
	}else{
		$show_message = "Your details are missing.";
	}
}else{
$show_message = "Feature not available.";
}
}else{
$show_message = "Something went wrong.";	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="js/jquery.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<title>GIFT REDEMPTION</title>

<style>
.mand_field{
color:#F00;
font-weight:bold;
font-size:12px;
padding-left:5px;
}
.alrt_msg{
color:#F00;
font-size: 12px;
height:22px;
display:inline-block;
}
.resp_msg{
font-weight:bold;
height:22px;
display:block;
margin:3px 0px;
text-align:center;
}
.redem_msg{
font-weight:bold;
height:22px;
display:block;
margin:3px 0px;
text-align:center;
}
form.arc_gift_redemption_form div.form-group{
	margin-bottom:0px;
}

.customer_del { background-color:#CCC; padding:15px; margin-top:10px; margin-bottom:20px;}

.each_gft_row p{
	padding-left: 10px;
}
.form-check-input{
width: 20px !important;
height: 20px !important;	
}
</style>


</head>

<body>
<div class="container-fluid">
<?php
if($is_show=="NO" && $show_message!=""){?>
<h4 style="text-align:center;margin-top:30px;"><?php echo $show_message;?></h4>
<?php
exit;	
}
?>

<h5 style="text-align:center">GIFT REDEMPTION</h5>

<form action="" method="POST" class="arc_gift_redemption_form" id="arc_gift_redemption_form">
<div class="form-group">
<label for="mobile">CUSTOMER MOBILE NUMBER:<span class="mand_field">*</span></label>
<input type="tel" class="form-control" placeholder="Enter Mobile" id="mobile" maxlength="10" autocomplete="OFF">
</div>
<div class="form-group">
<span class="resp_msg"></span>
</div>
<input class="btn btn-large btn-block btn-success" type="submit" value="Submit" />
</form>

<div class="row" style="margin:20px 0px;">
<a href="index.php?login_user_id=<?php echo urlencode($login_user_id);?>&user_type=<?php echo $user_type;?>" class="btn btn-large btn-block btn-success">BACK</a>
</div>


<div class="row" style="margin:20px 0px;" id="cust_show_redeem_cont">

</div>

<div class="row" style="margin:0px; padding:0px;">
<span class="redem_msg"></span>
</div>





</div>
<script type="text/javascript">
jQuery(document).ready(function(){
var xhrarc_consumer_redem_check,xhrarc_gift_redem;
var img_ldr = '<img src="img/ajax-loader.gif" style="width:20px;">';

var login_user_id = "<?php echo $login_user_id;?>";
var user_type = "<?php echo $user_type;?>";

jQuery("#mobile").on("keypress keyup blur",function (event) {    
jQuery(this).val(jQuery(this).val().replace(/[^\d].+/, ""));
if ((event.which < 48 || event.which > 57)) {
event.preventDefault();
}
});

jQuery('body').on('click','#cust_show_redeem_cont #cust_redeem_offer_btn',function(){
var mobile = jQuery.trim(jQuery(this).attr("the_mobile"));

var gift_idval = "";
var selected = jQuery("input[type='radio'][name='gift_idval']:checked");



var resp_msg_elmnt = jQuery('.redem_msg');
var cust_show_redeem_cont_elmnt = jQuery('#cust_show_redeem_cont');
if(mobile!=""){	
if (selected.length > 0) {
    gift_idval = selected.val();
resp_msg_elmnt.html(img_ldr);
if(xhrarc_gift_redem && xhrarc_gift_redem.readystate != 4){
xhrarc_gift_redem.abort();
}

xhrarc_gift_redem = jQuery.ajax({
url: 'ajax_arc_gift_redemption.php',
type: 'post',
dataType: 'json',
data: "login_user_id="+encodeURIComponent(login_user_id)+"&user_type="+user_type+"&mobile="+mobile+"&gift_idval="+gift_idval,
success: function(response){
if(response.process_sts=="YES"){
jQuery('#mobile').val("");
cust_show_redeem_cont_elmnt.html("");
resp_msg_elmnt.html(response.process_msg);
setTimeout(function(){
resp_msg_elmnt.html("");
},10000);
}else{
resp_msg_elmnt.html(response.process_msg);
setTimeout(function(){
resp_msg_elmnt.html("");
},6000);
}					
},
timeout : 0
});
}else{
resp_msg_elmnt.html("Please choose a gift.");
setTimeout(function(){
resp_msg_elmnt.html("");
},6000);
	
}

}else{
	
resp_msg_elmnt.html("Something went wrong.");
setTimeout(function(){
resp_msg_elmnt.html("");
},6000);
	
}
});

jQuery("form#arc_gift_redemption_form").submit(function(){
var mobile = jQuery.trim(jQuery('#mobile').val());
var resp_msg_elmnt = jQuery('.resp_msg');
var cust_show_redeem_cont_elmnt = jQuery('#cust_show_redeem_cont');
if(mobile==""){
jQuery('#mobile').focus();
resp_msg_elmnt.html("Please enter mobile");
setTimeout(function(){
resp_msg_elmnt.html("");
},6000);
}else if(mobile.length<10){
jQuery('#mobile').focus();
resp_msg_elmnt.html("Please enter 10 digit mobile number");
setTimeout(function(){
resp_msg_elmnt.html("");
},6000);
}else{

resp_msg_elmnt.html(img_ldr);
if(xhrarc_consumer_redem_check && xhrarc_consumer_redem_check.readystate != 4){
xhrarc_consumer_redem_check.abort();
}

xhrarc_consumer_redem_check = jQuery.ajax({
url: 'ajax_arc_consumer_redemption_check.php',
type: 'post',
dataType: 'json',
data: "login_user_id="+encodeURIComponent(login_user_id)+"&user_type="+user_type+"&mobile="+mobile,
success: function(response){
if(response.process_sts=="YES"){
cust_show_redeem_cont_elmnt.html(response.redeem_content);
resp_msg_elmnt.html("");
}else{
resp_msg_elmnt.html(response.process_msg);
setTimeout(function(){
resp_msg_elmnt.html("");
},6000);
}					
},
timeout : 0
});
return false;	
}

return false;
});


});
</script>
</body>
</html>
<?php
mysql_close();
?>
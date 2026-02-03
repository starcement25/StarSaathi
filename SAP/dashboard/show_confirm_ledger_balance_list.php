<?php
include "web_check.php";
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$employee_master = "employee_master";
$customer_master = "customer_master";
$branch_master = "branch_master";
$broker_master = "broker_master";
$customer_broker_relation = "customer_broker_relation";
$branch_schemes_PDF = "branch_schemes_PDF";
$notification_message = "notification_message";
$ledger = "ledger";
$ledger_balance = "ledger_balance";
$verify_ledger_details = "verify_ledger_details";
$ledger_data = array();
$current_month_dt = date("Y-m-")."1 00:00:00";

$sswa_user_type = $_SESSION["sswa_user_type"];
$sswa_selected_dealer_code = $_SESSION["sswa_selected_dealer_code"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];


$sql33 = "SELECT `ledger_year_month_day`,DATE_FORMAT(`ledger_year_month_day`, '%Y-%m-%d') as `mnyr` FROM $verify_ledger_details where `customer_code`='$sswa_selected_customer_code' order by `mnyr` desc limit 0,1";
$res33 = mysql_query($sql33);
$tot_res33 = mysql_num_rows($res33);
if($tot_res33>0){
	$row33 = mysql_fetch_assoc($res33);
	$the_year_month_day = $row33["ledger_year_month_day"] ? addslashes(trim($row33["ledger_year_month_day"])) : "";
	if($the_year_month_day!=""){
		$the_yr_mn = date("Y-m",strtotime($the_year_month_day));
		$the_query = " and `mnyr`>'$the_yr_mn' ";
	}else{
		$the_query = "";
	}
}else{
	$the_year_month_day = "";
	$the_query = "";
}

$sqlall = "select *,DATE_FORMAT(STR_TO_DATE(`voucher_date`, '%m/%d/%Y %h:%i:%s %p'), '%Y-%m-%d %H:%i:%s') as `e_date`,DATE_FORMAT(STR_TO_DATE(`voucher_date`, '%m/%d/%Y %h:%i:%s %p'), '%Y-%m') as `mnyr` from $ledger where `customer_code`='$sswa_selected_customer_code' and `voucher_date`!='' having DATE(`e_date`)<'$current_month_dt' $the_query order by `e_date` asc";
$resall = mysql_query($sqlall);
$totall = mysql_num_rows($resall);
if($totall>0){
	while($row11=mysql_fetch_assoc($resall)){
		$voucher_date = $row11["voucher_date"] ? trim($row11["voucher_date"]) : "";
		$voucher_date_concerted = date("m/d/Y h:i:s A",strtotime($voucher_date));
		$voucher_month_year = date("m-Y",strtotime($voucher_date));
		$ledger_of = date("M,Y",strtotime($voucher_date));
		$ledger_year_month_day = date("Y-m",strtotime($voucher_date))."-1";
		$voucher_no = $row11["voucher_no"];
		$quantity = $row11["quantity"]." MT";
		$amount_dr = $row11["amount_dr"] ? floatval(trim($row11["amount_dr"])) : 0;
		$amount_cr = $row11["amount_cr"] ? floatval(trim($row11["amount_cr"])) : 0;
		$balance = $row11["balance"];
		$entry_date = $row11["entry_date"];
		$converted_voucher_date = "";
		if(array_key_exists($voucher_month_year,$ledger_data)){
			$prev_amount_cr = $ledger_data[$voucher_month_year]["total_amount_cr"];
			$prev_amount_dr = $ledger_data[$voucher_month_year]["total_amount_dr"];
			$new_amount_cr = ($prev_amount_cr+$amount_cr);
			$new_amount_dr = ($prev_amount_dr+$amount_dr);
			$ledger_data[$voucher_month_year]["total_amount_cr"] = $new_amount_cr;
			$ledger_data[$voucher_month_year]["total_amount_dr"] = $new_amount_dr;		
		}else{
		$ledger_data[$voucher_month_year] = array("total_amount_dr"=>$amount_dr,"total_amount_cr"=>$amount_cr,"ledger_month_year"=>$voucher_month_year,"ledger_year_month_day"=>$ledger_year_month_day,"ledger_of"=>$ledger_of);
		}
		}
		
}


$add_page_name = "show_confirm_ledger_balance_list.php";
$page_name = "show_confirm_ledger_balance_list.php";
$prev_page_name = "ledger_balance.php";
include "web_header.php";
?>

<style>
.teEachField1_off_ord{
	display:block;
	width:100%;
}
.teEachField2_off_ord{
	display:block;
	width:100%;
}
.teEachField3_off_ord{
	display:block;
	width:100%;
}
.row .card .table-responsive table.table tbody tr td{
	padding:5px;
}
@media only screen and (max-width: 700px) {

.teEachField1_off_ord{
display:block;
width:auto;
word-wrap: break-word;
font-size: 12px;
white-space: normal;
padding-bottom: 2px;
}
.teEachField2_off_ord{
display:block;
width:auto;
word-wrap: break-word;
font-size: 12px;
white-space: normal;
padding-bottom: 2px;
}
.teEachField3_off_ord{
display:block;
width:auto;
word-wrap: break-word;
font-size: 12px;
white-space: normal;
padding-bottom: 2px;
}


}
</style>

<section class="content">
        <div class="container-fluid">
            <div class="row clearfix">


<?php

if(count($ledger_data)>0){
	foreach($ledger_data as $ledger_data_key=>$ledger_data_val){
		$total_amount_dr = $ledger_data_val["total_amount_dr"] ? trim($ledger_data_val["total_amount_dr"]) : "";
		$total_amount_cr = $ledger_data_val["total_amount_cr"] ? trim($ledger_data_val["total_amount_cr"]) : "";
		$ledger_month_year = $ledger_data_val["ledger_month_year"] ? trim($ledger_data_val["ledger_month_year"]) : "";
		$ledger_year_month_day = $ledger_data_val["ledger_year_month_day"] ? trim($ledger_data_val["ledger_year_month_day"]) : "";
		$ledger_of = $ledger_data_val["ledger_of"] ? trim($ledger_data_val["ledger_of"]) : "";
		
?>
<div class="card" style="margin-bottom:15px;">
<div class="table-responsive">
<table class="table table-bordered">
<tbody>
<tr>
<td>
<div>
<span class="teEachField1_off_ord">
DATE
</span>
</div>
</td>
<td>
<div>
<span class="teEachField2_off_ord">
<?php echo $ledger_of;?>
</span>
</div>
</td>
<td>
<div>
<span class="teEachField3_off_ord">
<a href="javascript:void(0);" class="btn btn-primary btn-block waves-effect the_confirm_btn" ledger_year_month_day="<?php echo $ledger_year_month_day;?>" total_amount_dr="<?php echo $total_amount_dr;?>" total_amount_cr="<?php echo $total_amount_cr;?>" >CONFIRM</a>
</span>
</div>
</td>
</tr>
<tr>
<td>
<div>
<span class="teEachField1_off_ord">
AMOUNT (dr)
</span>
</div>
</td>
<td>
<div>
<span class="teEachField2_off_ord" style="text-align:right;">
<?php echo $total_amount_dr;?>
</span>
</div>
</td>
<td></td>
</tr>
<tr>
<td>
<div>
<span class="teEachField1_off_ord">
AMOUNT (cr)
</span>
</div>
</td>
<td>
<div>
<span class="teEachField2_off_ord" style="text-align:right;">
<?php echo $total_amount_cr;?>
</span>
</div>
</td>
<td>
<div>
<span class="teEachField3_off_ord">
<a href="javascript:void(0);" class="btn btn-primary btn-block waves-effect the_reject_btn" ledger_year_month_day="<?php echo $ledger_year_month_day;?>" total_amount_dr="<?php echo $total_amount_dr;?>" total_amount_cr="<?php echo $total_amount_cr;?>" >REJECT</a>
</span>
</div>
</td>
</tr>
</tbody>
</table>
</div>
</div>
<?php } 
}else{
?>
<div class="card">
<div class="table-responsive">
<table class="table table-bordered">
<tbody>
<tr>
<td align="center" colspan="3">No data found.&nbsp;&nbsp;&nbsp; <a href="<?php echo $prev_page_name;?>" class="btn btn-primary waves-effect">Back</a></td>
</tr>
</tbody>
</table>
</div>
</div>
<?php } ?>
              
             

<?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>


</div>

<input type="button" id="open_model_btn" data-toggle="modal" data-target="#myModal" style="display:none;" >
            
<div id="myModal" class="modal fade" role="dialog">
<div class="modal-dialog">

<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">&times;</button>
<h4 class="modal-title">Ledger</h4>
</div>
<div class="modal-body">
<div class="form-group">
<label for="Write your remarks">Write your remarks</label>
<div class="form-line">
<textarea name="lb_wrt_cmt" class="form-control lb_wrt_cmt" id="lb_wrt_cmt" placeholder="Write your remarks" rows="5" style="margin-top:10px;border:1px solid #cccccc;padding:8px;"></textarea>
</div>
</div>

<div class="form-group" style="text-align:center;margin-bottom:5px">
<input type="hidden" name="ledger_year_month_day" id="ledger_year_month_day">
<input type="hidden" name="total_amount_dr" id="total_amount_dr">
<input type="hidden" name="total_amount_cr" id="total_amount_cr">
<input type="submit" class="btn bg-red waves-effect lb_wrt_upd_btn" name="lb_wrt_upd_btn" id="lb_wrt_upd_btn" style="margin-bottom:10px;" value="UPDATE">
</div>
<div class="form-group" id="lb_msg_sho" style="text-align:center;margin-bottom:5px">

</div>

</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" id="lb_the_close_btn" data-dismiss="modal">Close</button>
</div>
</div>

</div>
</div>


</div>
</section>


<script type="text/javascript">
jQuery(document).ready(function() {
var xhrconfirm,xhrreject;

jQuery(".the_confirm_btn").click(function(){
var ledger_year_month_day = jQuery.trim(jQuery(this).attr("ledger_year_month_day"));
var total_amount_dr = jQuery.trim(jQuery(this).attr("total_amount_dr"));
var total_amount_cr = jQuery.trim(jQuery(this).attr("total_amount_cr"));
var status = "APPROVED";
if(ledger_year_month_day==""){
window.location = "<?php echo $prev_page_name;?>";
}else{
if(xhrconfirm && xhrconfirm.readystate != 4){
xhrconfirm.abort();
}
xhrconfirm = jQuery.ajax({
url: 'ajax_update_this_month_ledger_status.php',
type: 'post',
dataType: 'json',
data: "ledger_year_month_day="+ledger_year_month_day+"&status="+status+"&total_amount_dr="+total_amount_dr+"&total_amount_cr="+total_amount_cr,
success: function(response){
if(response.process_sts=="YES"){
window.location = "<?php echo $prev_page_name;?>";
}else{
window.location = "<?php echo $prev_page_name;?>";
}					
},
timeout : 0
});

}
});


jQuery(".the_reject_btn").click(function(){
var ledger_year_month_day = jQuery.trim(jQuery(this).attr("ledger_year_month_day"));
var total_amount_dr = jQuery.trim(jQuery(this).attr("total_amount_dr"));
var total_amount_cr = jQuery.trim(jQuery(this).attr("total_amount_cr"));
jQuery("#ledger_year_month_day").val(ledger_year_month_day);
jQuery("#total_amount_dr").val(total_amount_dr);
jQuery("#total_amount_cr").val(total_amount_cr);
jQuery("#open_model_btn").trigger("click");
});


jQuery("#lb_wrt_upd_btn").click(function(){
var ledger_year_month_day = jQuery.trim(jQuery("#ledger_year_month_day").val());
var total_amount_dr = jQuery.trim(jQuery("#total_amount_dr").val());
var total_amount_cr = jQuery.trim(jQuery("#total_amount_cr").val());
var lb_wrt_cmt = jQuery.trim(jQuery("#lb_wrt_cmt").val());
var status = "REJECTED";
if(ledger_year_month_day==""){
window.location = "<?php echo $prev_page_name;?>";
}else{
if(lb_wrt_cmt==""){
jQuery("#lb_msg_sho").html("Please enter remark.");
setTimeout(function(){
jQuery("#lb_msg_sho").html("");
},5000);
}else{
if(xhrreject && xhrreject.readystate != 4){
xhrreject.abort();
}
xhrreject = jQuery.ajax({
url: 'ajax_update_this_month_ledger_status.php',
type: 'post',
dataType: 'json',
data: "ledger_year_month_day="+ledger_year_month_day+"&status="+status+"&total_amount_dr="+total_amount_dr+"&total_amount_cr="+total_amount_cr+"&comment="+encodeURIComponent(lb_wrt_cmt),
success: function(response){
if(response.process_sts=="YES"){
jQuery("#lb_the_close_btn").trigger("click");
window.location = "<?php echo $prev_page_name;?>";
}else{
window.location = "<?php echo $prev_page_name;?>";
}					
},
timeout : 0
});
}

}
});



});
</script>

<?php
include "web_footer.php";
mysql_close();
?>
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
$ledger = "ledger";
$ledger_balance = "ledger_balance";
$curr_date = date("m/d/Y");

$sswa_user_type = $_SESSION["sswa_user_type"];
$sswa_selected_dealer_code = $_SESSION["sswa_selected_dealer_code"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];

$ledger_total_balance = 0;
$ledger_link = "";

$sqlall2 = "select `balance`,`link` from $ledger_balance where (`customer_code`='$sswa_selected_customer_code' or `dns_customer_code`='$sswa_selected_dealer_code')";
$resall2 = mysql_query($sqlall2);
$totall2 = mysql_num_rows($resall2);
if($totall2>0){
$row112=mysql_fetch_assoc($resall2);
$ledger_total_balance = $row112["balance"];
$ledger_link = trim($row112["link"]);
}

$sqlall = "select *,DATE_FORMAT(STR_TO_DATE(`voucher_date`, '%m/%d/%Y %h:%i:%s %p'), '%Y-%m-%d %H:%i:%s') as `e_date` from $ledger where (`customer_code`='$sswa_selected_customer_code' or `dns_customer_code`='$sswa_selected_dealer_code') order by `e_date` desc limit 0,50";
$resall = mysql_query($sqlall);
$totall = mysql_num_rows($resall);



$add_page_name = "ledger_balance.php";
$page_name = "ledger_balance.php";
$cnt = 0;
$countrow = 1;
include "web_header.php";
?>
<style>
    
	thead,
	tbody {
	display: block;
	}
	tbody {
	overflow-y: scroll;
	overflow-x: hidden;
	height: 500px;
	}
	td,
	th {
	min-width: 160px;
	max-width: 160px;
	overflow: hidden;
	text-overflow: ellipsis;
	text-align:center !important;
	}
	.well-lg { padding:0px !important;}
.hide_narr{
	display:none;
}
</style>

<section class="content">
        <div class="container-fluid">
            <div class="row clearfix">
            <div class="well well-lg">
            <h3 style="text-align:center">&#8377; <?php echo $ledger_total_balance;?></h3>
            <p style="text-align:center">Outstanding Balance</p>
            <p style="text-align:center">*Last 50 Transaction as on <?php echo $curr_date;?></p>
<?php
if($sswa_user_type=="SP"){

}else{
?>
            <ul class="list-inline" style="text-align:center">
            <li><a href="show_confirm_ledger_balance_list.php" class="btn btn-primary waves-effect" data-type="basic">CONFIRM BALANCE</a></li>
            <!--<li><button class="btn btn-primary waves-effect" data-type="basic">MAKE PAYMENT</button></li>-->
            </ul>
<?php } ?>
            </div>
            <div class="card">
              <div class="table-responsive">
              <table class="table table-bordered">
              <thead>
                <tr>
                <th>VOUCHER DATE</th>
                <th>VOUCHER NO</th>
                <th>QUANTITY</th>
                <th>AMMOUNT DR</th>
                <th>AMMOUNT CR</th>
                <th>NARRATION</th>
                </tr>
              </thead>
              <tbody>

<?php
if($totall>0){
	while($row11=mysql_fetch_assoc($resall)){
		$ldg_id = $row11["ldg_id"];
		$voucher_date = $row11["voucher_date"] ? trim($row11["voucher_date"]) : "";
		if($voucher_date!=""){
			$voucher_date = date("m/d/Y",strtotime($voucher_date));
		}
		$voucher_no = $row11["voucher_no"];
		$quantity = $row11["quantity"];
		$amount_dr = $row11["amount_dr"];
		$amount_cr = $row11["amount_cr"];
		$narration = $row11["balance"];
		
?>
<tr>
<td><?php echo $voucher_date;?></td>
<td><?php echo $voucher_no;?></td>
<td><?php echo $quantity;?> MT</td>
<td>&#8377; <?php echo $amount_dr;?></td>
<td>&#8377; <?php echo $amount_cr;?></td>
<td><img src="images/info.png" style="margin:0 auto" class="img-responsive lb_img_btn" the_lgr_id="<?php echo $ldg_id;?>"  >
<span class="hide_narr" id="hide_narr_<?php echo $ldg_id;?>"><?php echo $narration;?></span>
</td>
</tr>
<?php
}	
}else{ ?>
<tr>
<td colspan="6" align="center"> No record found</td>
</tr>
<?php } ?>
         
</tbody>
</table>
</div>
</div>
</div>
            
<input type="button" id="open_model_btn" data-toggle="modal" data-target="#myModal" style="display:none;" >
            
<div id="myModal" class="modal fade" role="dialog">
<div class="modal-dialog">

<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal">&times;</button>
<h4 class="modal-title">NARRATION</h4>
</div>
<div class="modal-body">
<p id="the_contn_model_p"></p>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
</div>

</div>
</div>
              
        </div>
    </section>

<script type="text/javascript">
jQuery(function(){

jQuery(".lb_img_btn").click(function(){
	var the_lgr_id = jQuery(this).attr("the_lgr_id");
	if(the_lgr_id!=""){
		var the_narr_val = jQuery("#hide_narr_"+the_lgr_id).html();
		jQuery("#the_contn_model_p").html(the_narr_val);
		jQuery("#open_model_btn").trigger("click");
	}
});
	
});
</script>

<?php
include "web_footer.php";
mysql_close();
?>
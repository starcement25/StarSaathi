<?php
// check error
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
$curr_datetime = date("m/d/Y H:i:s");

$sswa_user_type = $_SESSION["sswa_user_type"];
$sswa_selected_dealer_code = $_SESSION["sswa_selected_dealer_code"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];

$ledger_total_balance = 0;
$ledger_link = "";

$sql3 = "select `dns_customer_code`,`customer_id` from $customer_master where `customer_code`='$sswa_selected_customer_code'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
$row3 = mysql_fetch_assoc($res3);
$the_dealer_id = trim($row3["dns_customer_code"]);
$the_customer_id = trim($row3["customer_id"]);
}else{
$the_customer_id = "";
}

/*----Tracklog Code Start-----*/
$curr_date_time = date("Y-m-d H:i:s");
$webservice_name = "MINI STATEMENT";
$sqlin_tl = "insert into `webservice_track_log` (`customer_code`,`webservice_name`,`details`,`datetime`) values ('$the_customer_id','$webservice_name','','$curr_date_time')";
$resin_tl = mysql_query($sqlin_tl);
/*----Tracklog Code End-----*/

/*$sqlall2 = "select `balance`,`link` from $ledger_balance where (`customer_code`='$sswa_selected_customer_code' or `dns_customer_code`='$sswa_selected_dealer_code')";
$resall2 = mysql_query($sqlall2);
$totall2 = mysql_num_rows($resall2);
if($totall2>0){
$row112=mysql_fetch_assoc($resall2);
$ledger_total_balance = $row112["balance"];
$ledger_link = trim($row112["link"]);
}

$sqlall = "select *,DATE_FORMAT(STR_TO_DATE(`voucher_date`, '%m/%d/%Y %h:%i:%s %p'), '%Y-%m-%d %H:%i:%s') as `e_date` from $ledger where (`customer_code`='$sswa_selected_customer_code' or `dns_customer_code`='$sswa_selected_dealer_code') order by `e_date` desc limit 0,50";
$resall = mysql_query($sqlall);
$totall = mysql_num_rows($resall);*/

if($the_customer_id!=""){

$add_page_name = "ledger_balance.php";
$page_name = "ledger_balance.php";
$cnt = 0;
$countrow = 1;
include "web_header.php";
$url_ck1 = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/ZFI_CREDIT_LIMIT_CDS/ZFI_Credit_limit(kunnr=\''.$the_customer_id.'\')?$format=json&sap-client=900';
$body_for_mcode1 = get_data_from_cserver($url_ck1);
if(isJsonCk($body_for_mcode1)){
	$json_decoded = json_decode($body_for_mcode1,true);
	if(count($json_decoded)>0){
		if(array_key_exists("d",$json_decoded)){
		$kunnr = $json_decoded["d"]["kunnr"];
		$name1 = $json_decoded["d"]["name1"];
		$name2 = $json_decoded["d"]["name2"];
		$name3 = $json_decoded["d"]["name3"];
		$credit_limit = $json_decoded["d"]["credit_limit"];
		$credit_expose = $json_decoded["d"]["credit_expose"];
		
		
		}
		
	}
}
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
            <h3 style="text-align:center">&#8377; <?php echo $credit_expose;?></h3>
            <p style="text-align:center">Outstanding Balance</p>
            <p style="text-align:center">*Last 50 Transaction as on <?php echo $curr_datetime;?></p>
<?php
if($sswa_user_type=="SP"){

}else{
?>
            <!--ul class="list-inline" style="text-align:center">
            <li><a href="show_confirm_ledger_balance_list.php" class="btn btn-primary waves-effect" data-type="basic">CONFIRM BALANCE</a></li>
            <!--<li><button class="btn btn-primary waves-effect" data-type="basic">MAKE PAYMENT</button></li>-->
            <!--/ul-->
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
                <th>AMOUNT DR(Rs.)</th>
                <th>AMOUNT CR(Rs.)</th>
                <th>NARRATION</th>
                </tr>
              </thead>
              <tbody>

<?php
/*if($totall>0){
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
		$narration = $row11["balance"];*/
		$curr_date = date("Y-m-d");
		$the_date_time = $curr_date."T00:00:00";
$the_filter2 = '&$filter=(Kunnr eq \''.$the_customer_id.'\' and DocDate eq datetime\''.$the_date_time.'\')';
$url_ck2 = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/ZFI_LEDGER_ODATA_SRV/LedgerSet?$format=json'.str_replace(" ","%20",$the_filter2);

$body_for_mcode10 = get_data_from_cserver($url_ck2);
if(isJsonCk($body_for_mcode10)){
$json_decoded21 = json_decode($body_for_mcode10,true);
if(count($json_decoded21)>0){
if(array_key_exists("d",$json_decoded21)){
	$app_results_arr = $json_decoded21["d"]["results"];
if(count($app_results_arr)>0){
	foreach($app_results_arr as $app_results_aval){
		$Bukrs = $app_results_aval["Bukrs"] ? trim($app_results_aval["Bukrs"]) : "";
		$Kunnr = $app_results_aval["Kunnr"] ? trim($app_results_aval["BuKunnrkrs"]) : "";
		$DocDate = $app_results_aval["DocDate"] ? trim($app_results_aval["DocDate"]) : "";
		$DocDate_format = "";
		if($DocDate!=""){
		$DocDate_str = str_replace("/","",$DocDate);
		$DocDate_str = str_replace("Date","",$DocDate_str);	
		$DocDate_str = str_replace("(","",$DocDate_str);
		$DocDate_str = str_replace(")","",$DocDate_str);
		$DocDate_str = ($DocDate_str / 1000);
		//$DocDate_format = date("m/d/Y h:i:s A",$DocDate_str);
		$DocDate_format = date("m/d/Y",$DocDate_str);
		}
		$VoucherNo = $app_results_aval["VoucherNo"] ? trim($app_results_aval["VoucherNo"]) : "";
		$Menge = $app_results_aval["Menge"] ? trim($app_results_aval["Menge"]) : "";
		$Uom = $app_results_aval["Uom"] ? trim($app_results_aval["Uom"]) : "";
		$DrAmount = $app_results_aval["DrAmount"] ? trim($app_results_aval["DrAmount"]) : "";
		$CrAmount = $app_results_aval["CrAmount"] ? trim($app_results_aval["CrAmount"]) : "";
		$BalText = $app_results_aval["BalText"] ? trim($app_results_aval["BalText"]) : "";
		$converted_voucher_date = "";
		$balance = "";
		$quantity = "";

		
	?>
	<tr>
	<td><?php echo $DocDate_format;?></td>
	<td><?php echo $VoucherNo;?></td>
	<td><?php echo number_format($Menge,2);?> MT</td>
	<td>&#8377; <?php echo number_format($DrAmount,2);?></td>
	<td>&#8377; <?php echo number_format($CrAmount,2);?></td>
	<td><img src="images/info.png" style="margin:0 auto" class="img-responsive lb_img_btn" the_lgr_id="<?php echo $VoucherNo;?>"  >
	<span class="hide_narr" id="hide_narr_<?php echo $VoucherNo;?>"><?php echo $BalText;?></span>
	</td>
	</tr>
	<?php
	   }	
	 }
	}
   }
  }
}
	else{ ?>
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
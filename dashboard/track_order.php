<?php
include "web_check.php";
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$T_APPERPDO_OFFLINE = "T_APPERPDO_OFFLINE";
$t_dochallan = "T_DOCHALLAN";
$destination_master="destination_master";
$customer_destination="customer_destination";
$employee_master = "employee_master";
$customer_master = "customer_master";
$branch_master = "branch_master";
$broker_master = "broker_master";
$customer_broker_relation = "customer_broker_relation";
$ledger_balance = "ledger_balance";
$branch_schemes_PDF = "branch_schemes_PDF";
$notification_message = "notification_message";


$sswa_user_type = $_SESSION["sswa_user_type"];
$sswa_selected_dealer_code = $_SESSION["sswa_selected_dealer_code"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];

$sql3 = "select `customer_id` from $customer_master where `customer_code`='$sswa_selected_customer_code'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
$row3 = mysql_fetch_assoc($res3);
$the_customer_id = trim($row3["customer_id"]);

/*----Tracklog Code Start-----*/
$curr_date_time = date("Y-m-d H:i:s");
$webservice_name = "TRACK ORDER";
$sqlin_tl = "insert into `webservice_track_log` (`customer_code`,`webservice_name`,`details`,`datetime`) values ('$the_customer_id','$webservice_name','','$curr_date_time')";
$resin_tl = mysql_query($sqlin_tl);
/*----Tracklog Code End-----*/


$sql_on_ord = "select `APPORDERNO`,`STATUS`,`order_date`,`prod_display_name`,`QTY`,`freight`,destination_address  from $t_apperpdo where `dns_customer_code`='$sswa_selected_dealer_code' and `APPORDERNO`!='' order by `order_date` desc";
$res_on_ord = mysql_query($sql_on_ord);
$totres_on_ord = mysql_num_rows($res_on_ord);

if(strtoupper($sswa_user_type)=='DEALER' || strtoupper($sswa_user_type)=='SP')
{ 	
$sql_off_ord = "select $T_APPERPDO_OFFLINE.*,$destination_master.dns_destination_code,$destination_master.destination_name from $T_APPERPDO_OFFLINE left join $customer_destination ON $customer_destination.customer_code=$T_APPERPDO_OFFLINE.`customer_code` left join $destination_master ON $customer_destination.destination_code=$destination_master.destination_code where $T_APPERPDO_OFFLINE.`dns_customer_code`='$sswa_selected_dealer_code'  order by $T_APPERPDO_OFFLINE.`ERPORDERDT` desc";
}
else
{
$sql_off_ord = "select $T_APPERPDO_OFFLINE.*,$destination_master.dns_destination_code,$destination_master.destination_name from $T_APPERPDO_OFFLINE left join $customer_destination ON $customer_destination.customer_code=$T_APPERPDO_OFFLINE.`customer_code` left join $destination_master ON $customer_destination.destination_code=$destination_master.destination_code  where ($T_APPERPDO_OFFLINE.`dns_consignee_code`='$sswa_selected_dealer_code' OR $T_APPERPDO_OFFLINE.`dns_consignee_code` IN(SELECT $customer_master.dns_customer_code FROM $customer_master WHERE $customer_master.rds_tag='".$sswa_selected_customer_code."' 
AND $customer_master.cust_type='ShiptoParty-Subdeale'))  order by $T_APPERPDO_OFFLINE.`ERPORDERDT` desc";

}

/*$sql_off_ord = "select * from $t_apperpdo where `dns_customer_code`='$sswa_selected_dealer_code' and `APPORDERNO`='' and `ERPORDERNO`!='' order by `order_date` desc";*/
$res_off_ord = mysql_query($sql_off_ord);
$totres_off_ord = mysql_num_rows($res_off_ord);


$add_page_name = "track_order.php";
$page_name = "track_order.php";
$cnt = 0;
$countrow = 1;
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

.teEachField1_on_ord{
	display:block;
	width:100%;
}
.teEachField2_on_ord{
	display:block;
	width:100%;
}

@media only screen and (max-width: 700px) {

.teEachField1_off_ord{
display:block;
width:200px;
word-wrap: break-word;
font-size: 12px;
white-space: normal;
padding-bottom: 2px;
}
.teEachField2_off_ord{
display:block;
font-size: 12px;
white-space: normal;
padding-bottom: 2px;
}

.teEachField1_on_ord{
display:block;
width:200px;
word-wrap: break-word;
font-size: 12px;
white-space: normal;
padding-bottom: 2px;
}
.teEachField2_on_ord{
display:block;
font-size: 12px;
white-space: normal;
padding-bottom: 2px;
}

}

.order_received{
color:grey;	
}
.do_approved{
color:#edbe00;	
}
.dispatched{
color:#3a8a00;	
}
.order_authorized{
color: blue;
}
.order_canceled{
color:red;		
}
.default_sts_colour{
color:#000000;	
}
</style>
<section class="content">
<div class="container-fluid" style="padding:0px 2px;">
<div class="row clearfix">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding:0px;">
<div class="card">

<div class="body">
<!-- Nav tabs -->
<ul class="nav nav-tabs tab-nav-right" role="tablist">
<li role="presentation" class="active"><a href="#app_online_order" data-toggle="tab">APP ORDER</a></li>
<li role="presentation"><a href="#offline_order" data-toggle="tab">OFFLINE ORDER</a></li>
</ul>
<!-- Tab panes -->
<div class="tab-content">
<div role="tabpanel" class="tab-pane fade in active" id="app_online_order">
<div class="row clearfix">
<div class="table-responsive">
<table class="table table-bordered">
<tbody>
<?php
if($totres_on_ord>0){
	while($row_on_ord = mysql_fetch_assoc($res_on_ord)){
		$apporderno_on_ord = $row_on_ord["APPORDERNO"] ? trim($row_on_ord["APPORDERNO"]) : "";
		$erporderno_on_ord = $row_on_ord["ERPORDERNO"] ? trim($row_on_ord["ERPORDERNO"]) : "";
		$status_on_ord = $row_on_ord["STATUS"] ? trim($row_on_ord["STATUS"]) : "";
		$order_date_on_ord = $row_on_ord["order_date"] ? trim($row_on_ord["order_date"]) : "";
		$freight = $row_on_ord["freight"] ? trim($row_on_ord["freight"]) : "";
		$destination_address = $row_on_ord["destination_address"] ? trim($row_on_ord["destination_address"]) : "";
		if($order_date_on_ord!=""){
			$order_date_on_ord = date("jS M Y h:i A",strtotime($order_date_on_ord));
		}else{
			$order_date_on_ord = "";
		}
		$prod_display_name_on_ord = $row_on_ord["prod_display_name"] ? trim($row_on_ord["prod_display_name"]) : "";
		$qty_on_ord = $row_on_ord["QTY"] ? trim($row_on_ord["QTY"]) : "";
		if($status_on_ord=="Order received"){
			$ord_sts_clor_cls = "order_received";
		}else if($status_on_ord=="DO approved"){
			$ord_sts_clor_cls = "do_approved";
		}else if($status_on_ord=="Dispatched"){
			$ord_sts_clor_cls = "dispatched";
		}else if($status_on_ord=="Order authorized"){
			$ord_sts_clor_cls = "order_authorized";
		}else if($status_on_ord=="Order canceled"){
			$ord_sts_clor_cls = "order_canceled";
		}else{
			$ord_sts_clor_cls = "default_sts_colour";
		}
		
?>
<tr>
<td>
<div>
<span class="teEachField1_on_ord"><b><?php echo $apporderno_on_ord;?></b> x <?php echo $qty_on_ord;?></span>
<span class="teEachField1_on_ord"><?php echo $prod_display_name_on_ord;?></span>
<span class="teEachField1_on_ord"><?php echo $order_date_on_ord;?></span>
<span class="teEachField1_on_ord"><?php echo $destination_address;?></span>
<span class="teEachField1_on_ord"><?php echo $freight;?></span>
</td>
<td>
<div>
<span class="teEachField2_on_ord">
<strong class="<?php echo $ord_sts_clor_cls;?>"><?php echo strtoupper($status_on_ord); ?></strong>
</span>
<?php if(strtoupper($status_on_ord)=="DISPATCHED"){ ?>
<a href="ajax_show_challan_details_by_app_erp_id_for_web_app.php?apporder_no=<?php echo $apporderno_on_ord;?>&erporder_no=<?php echo $erporderno_on_ord;?>" class="btn  btn-primary waves-effect vuChnlDtlsLink">DETAILS</a>
<?php } ?>
</div>
</td>
</tr>

<?php 
	}
}else{ ?>
<tr>
<td colspan="2" align="center">No record found.</td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
</div>

</div>
<div role="tabpanel" class="tab-pane fade" id="offline_order">
<div class="row clearfix">
<div class="table-responsive">
<table class="table table-bordered">
<tbody>

<?php
if($totres_off_ord>0){
	while($row_off_ord = mysql_fetch_assoc($res_off_ord)){
		$erporderno_off_ord = $row_off_ord["ERPORDERNO"] ? trim($row_off_ord["ERPORDERNO"]) : "";
		
		$status_off_ord = $row_off_ord["STATUS"] ? trim($row_off_ord["STATUS"]) : "";
		$order_date_off_ord = $row_off_ord["ERPORDERDT"] ? trim($row_off_ord["ERPORDERDT"]) : "";
		if($order_date_off_ord!=""){
			$order_date_off_ord = date("jS M Y h:i A",strtotime($order_date_off_ord));
		}else{
			$order_date_off_ord = "";
		}
		$prod_display_name_off_ord = $row_off_ord["prod_display_name"] ? trim($row_off_ord["prod_display_name"]) : "";
		$qty_off_ord = $row_off_ord["QTY"] ? trim($row_off_ord["QTY"]) : "";
		
		if($status_off_ord=="Order received"){
			$ord_sts_clor_cls_off = "order_received";
		}else if($status_off_ord=="DO approved"){
			$ord_sts_clor_cls_off = "do_approved";
		}else if($status_off_ord=="Dispatched"){
			$ord_sts_clor_cls_off = "dispatched";
		}else if($status_off_ord=="Order authorized"){
			$ord_sts_clor_cls_off = "order_authorized";
		}else if($status_off_ord=="Order canceled"){
			$ord_sts_clor_cls_off = "order_canceled";
		}else{
			$ord_sts_clor_cls_off = "default_sts_colour";
		}
		
?>
<tr>
<td>
<div>
<span class="teEachField1_off_ord"><b><?php echo $erporderno_off_ord;?></b> x <?php echo $qty_off_ord;?></span>
<span class="teEachField1_off_ord"><?php echo $prod_display_name_off_ord;?></span>
<span class="teEachField1_off_ord"><?php echo $order_date_off_ord;?></span>
</div>
</td>
<td>
<div>
<span class="teEachField2_off_ord">
<strong class="<?php echo $ord_sts_clor_cls_off;?>"><?php echo strtoupper($status_off_ord); ?></strong>
</span>
<?php if(strtoupper($status_off_ord)=="DISPATCHED"){ ?>
<a href="ajax_show_challan_details_by_app_erp_id_for_web_app.php?apporder_no=&erporder_no=<?php echo $erporderno_off_ord;?>" class="btn  btn-primary waves-effect vuChnlDtlsLink">DETAILS</a>
<?php } ?>
</div>
</td>
</tr>

<?php 
	}
}else{ ?>
<tr>
<td colspan="2" align="center">No Offline record found.</td>
</tr>
<?php } ?>

</tbody>
</table>
</div>
</div>

</div>

</div>
</div>
</div>
</div>
</div>



</div>
</section>
<script type="text/javascript">
jQuery(function(){
jQuery(".vuChnlDtlsLink").colorbox({iframe:true,width:"100%",height:"99%",closeButton: true,scrolling: true});

});
</script>
<?php
include "web_footer.php";
mysql_close();
?>
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
$T_DOINVOICE  = "T_DOINVOICE";
$allocation_details  = "allocation_details_invoicewise";
$branch_rssd_allocation_days = "branch_rssd_allocation_days";

$sswa_user_type = $_SESSION["sswa_user_type"];
$sswa_selected_dealer_code = $_SESSION["sswa_selected_dealer_code"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];

if($_GET['year']){
	$whr_str=" AND year='".$_GET['year']."' ";
	$year=$_GET['year'];
}
if($_GET['month']){
	$whr_str=" AND month='".$_GET['month']."' ";
	$month=$_GET['month'];
}
$the_customer_code=$_SESSION['sswa_selected_customer_code'];
$sqlckdays = "select `allocation_days` from $branch_rssd_allocation_days where `branch_code`=(SELECT `branch_code` FROM customer_master WHERE customer_code='".$the_customer_code."')";
//echo"<pre>";print_r($sqlckdays);die;

	$resckdays = mysql_query($sqlckdays);
	$totckdays = mysql_num_rows($resckdays);
	if($totckdays>0){
		$rowckdays=mysql_fetch_array($resckdays);
		$rssd_allocation_days=$rowckdays['allocation_days'];
	}else{
		$rssd_allocation_days='0';
	}
//echo"<pre>";print_r($rssd_allocation_days);die;
// fetch customer_id
$sql3 = "select `customer_id` from $customer_master where `customer_code`='$sswa_selected_customer_code'";
$res3 = mysql_query($sql3);
$row3 = mysql_fetch_assoc($res3);
$the_customer_id = trim($row3["customer_id"]);



// Online APP orders
$sql_on_ord = "select `APPORDERNO`,`ERPORDERNO`,`STATUS`,`order_date`,`prod_display_name`,`QTY`,`freight`,destination_address  
	from $t_apperpdo 
	where `dns_customer_code`='$sswa_selected_dealer_code'  
	and `status`='Dispatched' 
	order by `order_date` desc LIMIT 500";
$res_on_ord = mysql_query($sql_on_ord);

// Offline orders
// if($month_year!=''){
// 				$date_condition=" AND SUBSTRING(ERPORDERDT,1,7)='".$month_year."'";
// 			}
if(strtoupper($sswa_user_type)=='DEALER' || strtoupper($sswa_user_type)=='SP'){ 

	if($month!='') $date_condition.=" AND SUBSTRING($T_APPERPDO_OFFLINE.ERPORDERDT,6,2)='".$month."'";
	if($year!='') $date_condition.=" AND SUBSTRING($T_APPERPDO_OFFLINE.ERPORDERDT,1,4)='".$year."'";

	$sql_off_ord = "select $T_APPERPDO_OFFLINE.*,$destination_master.dns_destination_code,$destination_master.destination_name 
		from $T_APPERPDO_OFFLINE 
		left join $customer_destination ON $customer_destination.customer_code=$T_APPERPDO_OFFLINE.`customer_code` 
		left join $destination_master ON $customer_destination.destination_code=$destination_master.destination_code 
		where $T_APPERPDO_OFFLINE.`dns_customer_code`='$sswa_selected_dealer_code'  $date_condition
		order by $T_APPERPDO_OFFLINE.`ERPORDERDT` desc";
}
//echo"<pre>";print_r($sql_off_ord);die;
$res_off_ord = mysql_query($sql_off_ord);

include "web_header.php";
?>

<style>
.order-card {
  background: #fff;
  border-radius: 10px;
  padding: 14px;
  margin-bottom: 15px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.order-header {
  display: flex;
  justify-content: space-between;
  font-weight: bold;
  font-size: 14px;
  margin-bottom: 6px;
}
.order-status {
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 12px;
}
.dispatched { color: green; font-weight: bold; }
.order-received { color: grey; }
.do-approved { color: #edbe00; }
.order-canceled { color: red; }
.invoice-section {
  border-top: 1px solid #eee;
  margin-top: 8px;
  padding-top: 8px;
}
.invoice-card {
  padding: 8px;
  background: #f9f9f9;
  margin-bottom: 8px;
  border-radius: 6px;
}
.btn-allocate {
  display: inline-block;
  margin-top: 5px;
  padding: 6px 12px;
  background: #ff4444;
  color: #fff;
  border-radius: 4px;
  text-decoration: none;
  font-size: 12px;
}
</style>

<section class="content">
<div class="container-fluid" style="padding:0px 2px;">

<div class="card">
	<div class="header">
		<h2>Retailer Lifting</h2>
		<div class="row clearfix">
			<div class="col-lg-3 col-md-4 col-sm-12">
				<select class="form-control" id="year" name="year">
					<option value="">Select Year</option>
					<?php
					$currentYear = date("Y");
					for ($i = 0; $i <= 3; $i++) {
						$year_data = $currentYear - $i;
						$select_year= ($year==$year_data)?'selected': ''; 
						echo "<option $select_year value=\"$year_data\">$year_data</option>";
					}
					?>
				</select>
			</div>
			<div class="col-lg-3 col-md-4 col-sm-12">
				<select class="form-control" id="month" name="month">
					<option value="">Select Month</option>
					<?php 
					$months = ["01"=>"January","02"=>"February","03"=>"March","04"=>"April","05"=>"May","06"=>"June",
							   "07"=>"July","08"=>"August","09"=>"September","10"=>"October","11"=>"November","12"=>"December"];
					foreach($months as $mnum=>$mname){
						$sel = ($month==$mnum)?'selected':'';
						echo "<option $sel value=\"$mnum\">$mname</option>";
					}
					?>
				</select>
			</div>
			<div class="col-lg-1 col-md-1 col-sm-12">
				<button type="button" class="btn bg-red waves-effect srch_btn1">Search</button>
			</div>
			<div class="col-lg-1 col-md-1 col-sm-12">
				<button type="button" class="btn bg-red waves-effect srch_reset_btn">Reset</button>
			</div>
		</div>
	</div>

	<div class="body">
		<ul class="nav nav-tabs tab-nav-right" role="tablist">
			<li role="presentation" class="active"><a href="#app_online_order" data-toggle="tab">APP ORDER</a></li>
			<li role="presentation"><a href="#offline_order" data-toggle="tab">OFFLINE ORDER</a></li>
		</ul>

		<div class="tab-content">

			<!-- APP ORDER -->
			<div role="tabpanel" class="tab-pane fade in active" id="app_online_order">
			<?php
			if(mysql_num_rows($res_on_ord)>0){
				while($row_on_ord = mysql_fetch_assoc($res_on_ord)){
					$apporderno = trim($row_on_ord["APPORDERNO"]);
					$erporderno = trim($row_on_ord["ERPORDERNO"]);
					$status = trim($row_on_ord["STATUS"]);
					$order_date = $row_on_ord["order_date"] ? date("jS M Y h:i A",strtotime($row_on_ord["order_date"])) : "";
					$prod_name = trim($row_on_ord["prod_display_name"]);
					$qty = trim($row_on_ord["QTY"]);
					$freight = trim($row_on_ord["freight"]);
					$destination = trim($row_on_ord["destination_address"]);

					// status color
					$sts_cls = strtolower(str_replace(" ","-",$status));

					// filter invoices
					$date_condition="";
					if($month!='') $date_condition.=" AND SUBSTRING($T_DOINVOICE.INVDT,5,2)='".$month."'";
					if($year!='') $date_condition.=" AND SUBSTRING($T_DOINVOICE.INVDT,1,4)='".$year."'";
					
					 $sqlinv = "select * from $T_DOINVOICE where `APPORDERNO`='$apporderno' 
								AND INVNO NOT IN(SELECT DISTINCT inv_no FROM $allocation_details WHERE `inv_cancl`='yes' ) 
								$date_condition";
					$resinv = mysql_query($sqlinv);
					if(mysql_num_rows($resinv)>0){

						// Convert dates to timestamps (seconds)
					$currDate = strtotime(date("Y-m-d")); // current server date (midnight today)
					$thisDate = strtotime($order_date);        // dispatch date

					// Convert days into seconds
					$days_count = $rssd_allocation_days * 86400; // 1 day = 86400 sec

					// Calculate cutoff
					$calculate = $currDate - $days_count;

					// Show/Hide logic
					$showAllocateButton = false;
					if ($thisDate >= $calculate) {
						$showAllocateButton = true;
						
					} 

					
			?>
				<div class="order-card">
					<div class="order-header">
						<span><?php echo $apporderno; ?> (Qty: <?php echo $qty;?>)</span>
						<!-- <span class="order-status <?php echo $sts_cls;?>"><?php echo strtoupper($status);?></span> -->
					</div>
					<div class="order-body">
						<p><b><?php echo $prod_name;?></b></p>
						<p><?php echo $order_date;?></p>
						<p><?php echo $destination;?></p>
						<p>Freight: <?php echo $freight;?></p>
					</div>
					<div class="invoice-section">
					<?php while($inv=mysql_fetch_assoc($resinv)){ 
						$invno=$inv["INVNO"];
						$invdt=date('Y-m-d',strtotime($inv["INVDT"]));
						$invqty=$inv["INVQTY"];
						// Calculate available allocation qty
						$INVDT_formatted = $invdt;
						$sqlallocationqty = "SELECT SUM(allocation_qty) tot_allocation_qty 
											FROM $allocation_details 
											WHERE customer_id='$the_customer_id' 
											AND inv_date='$INVDT_formatted' 
											AND inv_no='$invno' 
											AND delete_at='0' 
											GROUP BY prod_desc,inv_date";

						$resallocationqty = mysql_query($sqlallocationqty);
						$rowallocationqty = mysql_fetch_array($resallocationqty);
						$totresallocationqty = isset($rowallocationqty['tot_allocation_qty']) ? $rowallocationqty['tot_allocation_qty'] : 0;

						$available_allocation_qty = $invqty - $totresallocationqty;

						// Color condition: if qty > 0 => BLACK else GREEN
						//$status_color = ($available_allocation_qty < 0) ? "black" : "green";
						
						if ($available_allocation_qty <= 0) {
							$status_color = "green";
								} else {
								$status_color = "black";
									
								}

					?>
						<div class="invoice-card">
						<p >Invoice No: <span style="color:<?php echo $status_color;?>; font-weight:bold;"><?php echo $invno; //echo'/'; echo $available_allocation_qty;?> </span> </p>
						<p>Date: <?php echo $invdt;?></p>
						<p>Qty: <?php echo $invqty;?> MT</p>
						

						<?php //if ($showAllocateButton && $available_allocation_qty > 0) { ?>
						<?php if ($showAllocateButton) { ?>
						<a href="add_retailer_lifting.php?apporder_no=<?php echo $apporderno;?>&erporder_no=<?php echo $erporderno;?>&prod_name=<?php echo urlencode($prod_name);?>&invoice_no=<?php echo $invno;?>&invoice_qty=<?php echo $invqty;?>&invdt=<?php echo $invdt;?>" 
						class="btn-allocate">Allocate</a>
						<?php } ?>
					</div>
				<?php } ?>
					</div>
				</div>
			<?php
					}
				}
			}else{
				echo "<p align='center'>No record found.</p>";
			}
			?>
			</div>

			<!-- OFFLINE ORDER -->
			<div role="tabpanel" class="tab-pane fade" id="offline_order">
			<?php
			if(mysql_num_rows($res_off_ord)>0){
				while($row_off=mysql_fetch_assoc($res_off_ord)){
					$erporderno = trim($row_off["ERPORDERNO"]);
					$status = trim($row_off["STATUS"]);
					$freight = trim($row_off["freight"]);
					$order_date = $row_off["ERPORDERDT"] ? date("jS M Y h:i A",strtotime($row_off["ERPORDERDT"])) : "";
					$prod_name = trim($row_off["prod_display_name"]);
					$qty = trim($row_off["QTY"]);
					$sts_cls = strtolower(str_replace(" ","-",$status));

					// Convert dates to timestamps (seconds)
					$currDate = strtotime(date("Y-m-d")); // current server date (midnight today)
					$thisDate = strtotime($order_date);        // dispatch date

					// Convert days into seconds
					$days_count = $rssd_allocation_days * 86400; // 1 day = 86400 sec

					// Calculate cutoff
					$calculate = $currDate - $days_count;

					// Show/Hide logic
					$showAllocateButton = false;
					if ($thisDate >= $calculate) {
						$showAllocateButton = true;
						
					} 
			?>
				<div class="order-card">
					<div class="order-header">
						<span><?php echo $erporderno;?> (Qty: <?php echo $qty;?>)</span>
						<span class="order-status <?php echo $sts_cls;?>"><?php echo strtoupper($status);?></span>
					</div>
					<div class="order-body">
						<p><b><?php echo $prod_name;?></b></p>
						<p><?php echo $order_date;?></p>
						<p>Freight: <?php echo $freight;?></p>
						<?php if ($showAllocateButton) { ?>
						<a href="add_retailer_lifting.php?apporder_no=<?php echo $erporderno;?>&erporder_no=<?php echo $erporderno;?>&prod_name=<?php echo urlencode($prod_name);?>&invoice_no=<?php echo $erporderno;?>&invoice_qty=<?php echo $qty;?>&invdt=<?php echo $order_date;?>" class="btn-allocate">Allocate</a>
						<?php } ?>
					</div>
					<!-- <?php if(strtoupper($status)=="DISPATCHED"){ ?>
					<div class="invoice-section">
						<a href="ajax_show_challan_details_by_app_erp_id_for_web_app.php?apporder_no=&erporder_no=<?php echo $erporderno;?>" class="btn-allocate">Details</a>
					</div>
					<?php } ?> -->
				</div>
			<?php
				}
			}else{
				echo "<p align='center'>No Offline record found.</p>";
			}
			?>
			</div>

		</div>
	</div>
</div>

</div>
</section>

<script type="text/javascript">
jQuery(function(){
	jQuery(".srch_btn1").click(function(){
		var month = jQuery("#month").val();
		var year = jQuery("#year").val();
		var qstring ="";
		if(month!=""){ qstring+="month="+encodeURIComponent(month); }
		if(year!=""){ qstring+=(qstring!=""?"&":"")+"year="+encodeURIComponent(year); }
		if(qstring!=""){ qstring = "retailer_lifting.php?"+qstring; }
		window.location = qstring;
	});
	jQuery(".srch_reset_btn").click(function(){
		window.location = "retailer_lifting.php";
	});
});
</script>

<?php
include "web_footer.php";
mysql_close();
?>

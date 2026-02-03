<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

include "web_check.php";
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$employee_master = "employee_master";
$customer_master = "customer_master";
$branch_master = "branch_master";

$curr_date = date("m/d/Y");

$sswa_user_type = $_SESSION["sswa_user_type"];
$sswa_selected_dealer_code = $_SESSION["sswa_selected_dealer_code"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];

$ledger_total_balance = 0;
$ledger_link = "";

$company_sts_arr = array();
$company_sts_arr[] = array("key_val"=>"1010","title_val"=>"SCL");
$company_sts_arr[] = array("key_val"=>"1017","title_val"=>"SCNEL");

$sql3 = "select `dns_customer_code`,`customer_id`,customer_name from $customer_master where `customer_code`='$sswa_selected_customer_code'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
$row3 = mysql_fetch_assoc($res3);
$the_dealer_id = trim($row3["dns_customer_code"]);
$the_customer_id = trim($row3["customer_id"]);
}else{
$the_customer_id = "";
}

if(strtoupper($sswa_user_type)=='DEALER'){
/*----Tracklog Code Start-----*/
$curr_date_time = date("Y-m-d H:i:s");
$webservice_name = "DETAILED STATEMENT";
$sqlin_tl = "insert into `webservice_track_log` (`customer_code`,`webservice_name`,`details`,`datetime`) values ('$the_customer_id','$webservice_name','','$curr_date_time')";
$resin_tl = mysql_query($sqlin_tl);
/*----Tracklog Code End-----*/
}

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
	$from_dt=$_REQUEST['from_dt'];
		$to_dt=$_REQUEST['to_dt'];
		if($from_dt=='' && $to_dt=='')
		{
		$the_start_date = date('d/m/Y', strtotime("-7 days,$curr_date"));
		$the_end_date =  date('d/m/Y');
		}
		else
		{
			$the_start_date=date('d/m/Y', strtotime($from_dt));
			$the_end_date=date('d/m/Y', strtotime($to_dt));
		}
	$the_company_code=$_REQUEST['astn_comp_code'];

$add_page_name = "customer_ledger_scl_scln.php";
$page_name = "customer_ledger_scl_scln.php";
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
<form action="" method="post">
<section class="content">
        <div class="container-fluid">
            <div class="row clearfix">
            <!--div class="well well-lg">
            <h3 style="text-align:center">Customer Ledger</p>
            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
    <input type="text" class="datepicker form-control" id="from_dt"  value="<?php echo $from_dt;?>" placeholder="Choose from date">
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
    <input type="text" class="datepicker form-control" id="to_dt"  value="<?php echo $to_dt;?>" placeholder="Choose to date">
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_btn" >Search</button>
    </div>
            <p style="text-align:center">Period From : <?php echo $the_start_date?>  To : <?php echo $the_end_date?></p>

            </div-->
            <div class="card">           
            <div class="header">
            <h3 style="text-align:center">Company Wise Ledger (1st Aug 2022 onwards)</h3>
			<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
				<span style="text-align:center">&nbsp;</span>
            <select class="form-control" id="astn_comp_code" name="astn_comp_code" style="padding-left:2px;">
            <option value="">Select Company</option>
                <?php
					$the_company_sts=$_REQUEST['astn_comp_code'];	
	
                       foreach($company_sts_arr as $company_sts_arr_val){
		$the_key_val = $company_sts_arr_val["key_val"];
		$the_title_val = $company_sts_arr_val["title_val"]; ?>
        <option value="<?php echo $the_key_val;?>" <?php if($the_key_val==$the_company_sts){?> selected="selected" <?php } ?>><?php echo $the_title_val;?></option>
        <?php
	}
                ?>

		</select>
		</div>	
            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
				<span style="text-align:center">From Date</span>
    <input type="date" class="form-control" id="from_dt" name="from_dt"  value="<?php echo $from_dt;?>" placeholder="Choose from date" min="2022-08-01">
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
		<span style="text-align:center">To Date</span>
    <input type="date" class="form-control" id="to_dt" name="to_dt" value="<?php echo $to_dt;?>" placeholder="Choose to date">
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
		<span style="text-align:center">&nbsp;</span>
    <button type="submit" class="btn bg-red waves-effect srch_btn" >Search</button>
    </div>
     <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
		 <span style="text-align:center">&nbsp;</span>
    <a href="export_customer_ledger_scl_scln.php?the_start_date=<?php echo $from_dt;?>&the_end_date=<?php echo $to_dt;?>&customer_code=<?php echo $sswa_selected_customer_code;?>&the_company_code=<?php echo $the_company_code;?>" class="btn bg-red waves-effe">Export</a>
    </div>
				<br />
				<br />
            <h3 style="text-align:center">Period From : <?php echo $the_start_date?>  To : <?php echo $the_end_date?></h3>
            </div>
            
              <div class="table-responsive">
              <table class="table table-bordered">
              <thead>
                <tr>
                <th>COMPANY</th>
                <th>VOUCHERDT</th>
                <th>PARTICULARS</th>
                <th>QTY</th>
                <th>AMTDR(Rs.)</th>
                <th>AMTCR(Rs.)</th>
                <th>BALANCE(Rs.)</th>
                </tr>
              </thead>
              <tbody>

<?php

		$curr_date = date("Y-m-d");
		if($from_dt=='' && $to_dt=='')
		{
		$the_start_date_time = date('Y-m-d', strtotime("-7 days,$curr_date"))."T00:00:00";
		$the_end_date_time = $curr_date."T00:00:00";
		}
		else
		{
			$the_start_date_time=date('Y-m-d', strtotime($from_dt))."T00:00:00";
			$the_end_date_time=date('Y-m-d', strtotime($to_dt))."T00:00:00";
		}
//$the_filter2 = '&$filter=(Kunnr eq \''.$the_customer_id.'\' and DocDate eq datetime\''.$the_date_time.'\')';
//$the_filter = '&$filter=(Kunnr eq \''.$the_customer_id.'\' and ( Bldat ge datetime\''.$the_start_date_time.'\' and Bldat le datetime\''.$the_end_date_time.'\') )';
//$the_filter = '&$filter=(Kunnr eq \''.$the_customer_id.'\' and ( Bldat ge datetime\'2022-12-12T00:00:00\' and Bldat le datetime\'2022-12-19T00:00:00\') )';
	if($the_company_code=='') $the_company_code='1010';
	$the_filter = '&$filter=(Bukrs eq \''.$the_company_code.'\' and Kunnr eq \''.$the_customer_id.'\' and ( Bldat ge datetime\''.$the_start_date_time.'\' and Bldat le datetime\''.$the_end_date_time.'\') )';
	
	/*https://starfiori.starcement.co.in:44300/sap/opu/odata/sap/ZFI_CUST_LEDGER_ODATA_SRV/ZFI_CUST_LEDGER_STSet?%24format=json&%24filter=(Bukrs%20eq%20%271017%27%20and%20Kunnr%20eq%20%271000000312%27%20and%20(%20Bldat%20ge%20datetime%272024-10-01T00:00:00%27%20and%20Bldat%20le%20datetime%272024-11-30T00:00:00%27)%20)&sap-client=900*/



$url_ck1 = 'https://starfiori.starcement.co.in:44300/sap/opu/odata/sap/ZFI_CUST_LEDGER_ODATA_SRV/ZFI_CUST_LEDGER_STSet?$format=json'.str_replace(" ","%20",$the_filter).'&sap-client=900';
// echo $url_ck1;
$body_for_mcode10 = get_data_from_cserver($url_ck1);
// echo $body_for_mcode10;
if(isJsonCk($body_for_mcode10)){
$json_decoded21 = json_decode($body_for_mcode10,true);
if(count($json_decoded21)>0){
if(array_key_exists("d",$json_decoded21)){
	$app_results_arr = $json_decoded21["d"]["results"];
	//print_r($app_results_arr);
if(count($app_results_arr)>0){
	foreach($app_results_arr as $app_results_aval){
		$kunnr = $app_results_aval["kunnr"];
		$Butxt = $app_results_aval["Butxt"];
		$name1 = $app_results_aval["name1"];
		$Belnr = $app_results_aval["Belnr"];
		$Belnr2 = $app_results_aval["Belnr2"];
		$Ltext = $app_results_aval["Ltext"];
		$Bldat = $app_results_aval["Bldat"];
		$quayntity = $app_results_aval["Menge"];
		$CrAmount = $app_results_aval["CrAmount"];
		$DrAmount = $app_results_aval["DrAmount"];
		$Balance = $app_results_aval["Balance"];
		$OpBalance = $app_results_aval["OpBalance"];
		
		$Bldat_format = "";
		if($Bldat!=""){
		$Bldat_str = str_replace("/","",$Bldat);
		$Bldat_str = str_replace("Date","",$Bldat_str);	
		$Bldat_str = str_replace("(","",$Bldat_str);
		$Bldat_str = str_replace(")","",$Bldat_str);
		$Bldat_str = ($Bldat_str / 1000);
		//$DocDate_format = date("m/d/Y h:i:s A",$DocDate_str);
		$Bldat_format = date("d/m/Y",$Bldat_str);
		}
		//if($OpBalance > 0)
		if($OpBalance !='0.000')
		{
			$particulars='opening';
			$Balance=$OpBalance;
		}
		else
		{
			$particulars=$Belnr.' / '.$Belnr2.'<br />'.$Ltext;
			$Balance=$CrAmount-$DrAmount;
		}
		$total_balance=$total_balance+$Balance;
		
		if($Butxt=='SCNE') $Butxt='SCNEL';
	?>
	<tr>
	<td><?php echo $Butxt;?></td>
	<td><?php echo $Bldat_format;?></td>
    <td><?php echo $particulars;?> </td>
	<td><?php echo number_format($quayntity,2);?></td>
	<td><?php echo number_format($CrAmount,2);?></td>
	<td><?php echo number_format($DrAmount,2);?></td>
    <td><?php echo number_format($total_balance,2);?></td>
	
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
        </div>
    </section>
    </form>

<script type="text/javascript">
jQuery(function(){
//jQuery('#from_dt').bootstrapMaterialDatePicker({ weekStart : 0, time: false });
	//jQuery('#to_dt').bootstrapMaterialDatePicker({ weekStart : 0, time: false });
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
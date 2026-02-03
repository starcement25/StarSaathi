<?php
include "web_check.php";
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$employee_master = "employee_master";
$customer_master = "customer_master";
$branch_master = "branch_master";

function sort_by_date($a, $b) {
    $a = strtotime($a['billdt']);
    $b = strtotime($b['billdt']);
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;
	//return strtotime($a) - strtotime($b);
}

$curr_date = date("m/d/Y");

$sswa_user_type = $_SESSION["sswa_user_type"];
$sswa_selected_dealer_code = $_SESSION["sswa_selected_dealer_code"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];

$ledger_total_balance = 0;
$ledger_link = "";

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
$webservice_name = "SECURITY DEPOSIT LEDGER";
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
		$curr_month = date("m");
		if($from_dt=='' && $to_dt=='')
		{
			$curr_date_time = date("Y-m-d H:i:s");
			if($curr_month > 3)
			{
				$monthyearstart=date("Y",strtotime($curr_date_time))-1;
				$monthyearend=date("Y",strtotime($curr_date_time));
			}
			else
			{
				$monthyearstart=date("Y",strtotime($curr_date_time))-2;
				$monthyearend=date("Y",strtotime($curr_date_time))-1;
			}

			$the_start_date='31/07/'.$monthyearstart;
			$the_end_date =  '31/03/'.$monthyearend;
		}
		else
		{
			$the_start_date=date('d/m/Y', strtotime($from_dt));
			$the_end_date=date('d/m/Y', strtotime($to_dt));
		}

$astn_fy=$_REQUEST['astn_fy'];
	if($astn_fy==''){
		$the_start_date=$the_start_date;
		$the_end_date=$the_end_date;
	}
	else
	{
		$the_start_date_ac = $astn_fy."-04-01";	
		$the_end_date_ac = ($astn_fy+1)."-03-31";
		$from_dt=$the_start_date_ac;
		$to_dt=	$the_end_date_ac;
		
		$the_start_date=date('d/m/Y', strtotime($the_start_date_ac));
		$the_end_date=date('d/m/Y', strtotime($the_end_date_ac));
		

	}
$add_page_name = "customer_security_ledger.php";
$page_name = "customer_security_ledger.php";
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
            <h3 style="text-align:center">Security Deposit Ledger</h3>
            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
				<span style="text-align:center">&nbsp;</span>
           <select class="form-control" id="astn_fy" name="astn_fy" style="padding-left:2px;">
            <option value="">Select FY</option>
           <option value="2022" <?php if($astn_fy=='2022'){ ?> selected="selected" <?php } ?>><?php echo 'FY22-23';?>
			   </option>
		<option value="2023" <?php if($astn_fy=='2023'){ ?> selected="selected" <?php } ?>><?php echo 'FY23-24';?>
			   </option>   
			</select>
				</div>	
            <!--div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
				<span style="text-align:center">From Date</span>
    <input type="date" class="form-control" id="from_dt" name="from_dt"  value="<?php echo $from_dt;?>" placeholder="Choose from date" min="2022-04-01" value="2022-04-01">
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
		<span style="text-align:center">To Date</span>
    <input type="date" class="form-control" id="to_dt" name="to_dt" value="<?php echo $to_dt;?>" placeholder="Choose to date">
    </div-->
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
		<span style="text-align:center">&nbsp;</span>
    <button type="submit" class="btn bg-red waves-effect srch_btn" >Search</button>
    </div>
     <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
		 <span style="text-align:center">&nbsp;</span>
    <a href="export_security_ledger.php?the_start_date=<?php echo $from_dt;?>&the_end_date=<?php echo $to_dt;?>&customer_code=<?php echo $sswa_selected_customer_code;?>" class="btn bg-red waves-effe">Export</a>
    </div>
				<br />
				<br />
            <h3 style="text-align:center">Period From : <?php echo $the_start_date?>  To : <?php echo $the_end_date?></h3>
            </div>
            
              <div class="table-responsive">
              <table class="table table-bordered">
              <thead>
                <tr>
                <th>Document No</th>
                <th>Document Date</th>
                <th>AMTDR (Rs.)</th>
                <th>AMTCR (Rs.)</th>
				<th>TDS (Rs.)</th>	
                <th>Narration</th>
                </tr>
              </thead>
              <tbody>

<?php

		$curr_date = date("Y-m-d");
		if($astn_fy==''){
			if($from_dt=='' && $to_dt=='')
			{
			//$the_start_date_time = date('Y-m-d', strtotime("-7 days,$curr_date"))."T00:00:00";
				$curr_date_time = date("Y-m-d H:i:s");
				if($curr_month > 3)
				{
					$monthyearstart=date("Y",strtotime($curr_date_time))-1;
					$monthyearend=date("Y",strtotime($curr_date_time));
				}
				else
				{
					$monthyearstart=date("Y",strtotime($curr_date_time))-2;
					$monthyearend=date("Y",strtotime($curr_date_time))-1;
				}

			$the_start_date_time = $monthyearstart."-07-31T00:00:00";	
			$the_end_date_time = $monthyearend."-03-31T00:00:00";
			}
			else
			{
				$the_start_date_time=date('Y-m-d', strtotime($from_dt))."T00:00:00";
				$the_end_date_time=date('Y-m-d', strtotime($to_dt))."T00:00:00";
			}
	}
	else
	{
		$the_start_date_time = $astn_fy."-04-01T00:00:00";	
		$the_end_date_time = ($astn_fy+1)."-03-31T00:00:00";

	}
$the_filter = '&$filter=(Kunnr eq \''.$the_customer_id.'\' and ( Bldat ge datetime\''.$the_start_date_time.'\' and Bldat le datetime\''.$the_end_date_time.'\') )';
	
	/*https://starfiori.starcement.co.in:44300/sap/opu/odata/sap/ZOVW_LEDG_SECDEP_CDS/ZOVW_LEDG_SECDEP(p_Code='1000001336',p_Frm=datetime'2022-04-01T00:00:00',p_To=datetime'2023-03-31T00:00:00')/Set?$format=json&sap-client=900*/

$url_ck1 = 'https://starfiori.starcement.co.in:44300/sap/opu/odata/sap/ZOVW_LEDG_SECDEP_CDS/ZOVW_LEDG_SECDEP(p_Code=\''.$the_customer_id.'\',p_Frm=datetime\''.$the_start_date_time.'\',p_To=datetime\''.$the_end_date_time.'\')/Set?$format=json&sap-client=900';
	

$body_for_mcode10 = get_data_from_cserver($url_ck1);
	//$body_for_mcode10='';
if(isJsonCk($body_for_mcode10)){
$json_decoded21 = json_decode($body_for_mcode10,true);
if(count($json_decoded21)>0){
if(array_key_exists("d",$json_decoded21)){
	$app_results_arr = $json_decoded21["d"]["results"];
	//print_r($app_results_arr);
if(count($app_results_arr)>0){
	foreach($app_results_arr as $app_results_aval){
		$billno  = $app_results_aval["billno"];
		$billdt = $app_results_aval["billdt"];
		$amtdr  = $app_results_aval["amtdr"];
		$amtcr  = $app_results_aval["amtcr"];
		$narration = $app_results_aval["narration"];
		$tdsamt = $app_results_aval["tdsamt"];
		
		$Bldat_format = "";
		if($billdt!=""){
		$Bldat_str = str_replace("/","",$billdt);
		$Bldat_str = str_replace("Date","",$Bldat_str);	
		$Bldat_str = str_replace("(","",$Bldat_str);
		$Bldat_str = str_replace(")","",$Bldat_str);
		$Bldat_str = ($Bldat_str / 1000);
		//$DocDate_format = date("m/d/Y h:i:s A",$DocDate_str);
			$Bldat_format = date("Y-m-d",$Bldat_str);

		}
		
	$security_ledger_data[]=array("billno"=>$billno,"billdt"=>$Bldat_format,"amtdr"=>$amtdr,"amtcr"=>$amtcr,"narration"=>$narration,"tdsamt"=>$tdsamt);
		
	}
	//print_r($credit_note_data);
	usort($security_ledger_data, 'sort_by_date');
	foreach($security_ledger_data as $security_ledger_data_val)
	{
		$billno  = $security_ledger_data_val["billno"];
		$billdt = $security_ledger_data_val["billdt"];
		$Bldat_format = date("d/m/Y",strtotime($billdt));
		$amtdr  = round($security_ledger_data_val["amtdr"],2);
		$amtcr  = round($security_ledger_data_val["amtcr"],2);
		$narration = $security_ledger_data_val["narration"];
		$tdsamt = round($security_ledger_data_val["tdsamt"],2);
		
		$total_CrAmount=$total_CrAmount+$amtcr;
		$total_DrAmount=$total_DrAmount+$amtdr;
		
	?>
	<tr>
	<td><?php echo $billno;?></td>
	<td><?php echo $Bldat_format;?></td>
    <td align="right" style="text-align:right;"><?php echo number_format($amtdr,2);?> </td>
	<td align="right" style="text-align:right;"><?php echo number_format($amtcr,2);?></td>
	<td align="right" style="text-align:right;"><?php echo number_format($tdsamt,2);?></td>	
	<td><?php echo $narration;?></td>
	
	</tr>
	<?php
	   }
	?>
	<tr>
	<td colspan="2" align="center"><b>TOTAL</b></td>
		<td><b><?php echo $total_DrAmount;?></b> </td>
	<td><b><?php echo $total_CrAmount;?></b></td>
		<td></td>
	<td><b>Balance - <?php echo ($total_CrAmount-$total_DrAmount);?></b></td>
	</tr>
	<?php			  
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
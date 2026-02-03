<?php
include "web_check.php";
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$employee_master = "employee_master";
$customer_master = "customer_master";
$branch_master = "branch_master";

function sort_by_date($a, $b) {
    $a = strtotime($a['CRDT']);
    $b = strtotime($b['CRDT']);
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
$webservice_name = "CREDIT NOTE";
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


$add_page_name = "customer_ledger.php";
$page_name = "customer_ledger.php";
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
	min-width: 180px;
	max-width: 220px;
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
            <h3 style="text-align:center">Credit Note Register</p>
            <p style="text-align:center">Period From : <?php //echo $the_start_date?>  To : <?php //echo $the_end_date?></p>

            </div-->
            <div class="card">
            <div class="header">
            <h3 style="text-align:center">Credit Note Register</h3>
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
    <a href="export_credit_note.php?the_start_date=<?php echo $from_dt;?>&the_end_date=<?php echo $to_dt;?>&customer_code=<?php echo $sswa_selected_customer_code;?>" class="btn bg-red waves-effe">Export</a>
    </div>
				<br />
				<br />
            <h3 style="text-align:center">Period From : <?php echo $the_start_date?>  To : <?php echo $the_end_date?></h>

            </div>
              <div class="table-responsive">
              <table class="table table-bordered">
              <thead>
                <tr>
                <th>GSTIN</th>
				<th>VOUCHERNO</th>	
                <th>VOUCHERDT</th>
                <th>PARTICULARS</th>
                <th>QTY</th>
                <th>BASIC</th>
                <th>CGST</th>
                <th>SGST</th>
                <th>IGST</th>
                <th>VAT/CST</th>
                <th>ROFF</th>
                <th>AMOUNT(Rs.)</th>
                </tr>
              </thead>
              <tbody>

<?php

		$curr_date = date("Y-m-d");
		//$the_start_date_time = date('Y-m-d', strtotime("-7 days,$curr_date"))."T00:00:00";
		//$the_end_date_time = $curr_date."T00:00:00";
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
//$the_filter = '&$filter=(Kunnr eq \''.$the_customer_id.'\' and ( Bldat ge datetime\'2022-12-12T00:00:00\' and Bldat le datetime\'2022-12-19T00:00:00\') )';
//$the_customer_id='1000002808';
//$the_start_date_time = '2022-08-01T00:00:00';
//$the_end_date_time = '2022-08-31T00:00:00';

$url_ck1 = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/YCP_SCNDN_CDS/YCP_SCNDN(p_Code=\''.$the_customer_id.'\',p_Frm=datetime\''.$the_start_date_time.'\',p_To=datetime\''.$the_end_date_time.'\')/Set?$format=json&sap-client=900';

$body_for_mcode10 = get_data_from_cserver($url_ck1);
if(isJsonCk($body_for_mcode10)){
$json_decoded21 = json_decode($body_for_mcode10,true);
if(count($json_decoded21)>0){
if(array_key_exists("d",$json_decoded21)){
	$app_results_arr = $json_decoded21["d"]["results"];
	//print_r($app_results_arr);
	
if(count($app_results_arr)>0){
	//$credit_note_arr=array();
	foreach($app_results_arr as $app_results_aval){
		$kunnr = $app_results_aval["p_Code"];
		$GSTNO = $app_results_aval["GSTNO"];
		$CRNO = $app_results_aval["CRNO"];
		$CRDT = $app_results_aval["CRDT"];
		$NARRATION = $app_results_aval["NARRATION"];
		$QTY = $app_results_aval["QTY"];
		$BASIC = $app_results_aval["BASIC"];
		$CGST = $app_results_aval["CGST"];
		$SGST = $app_results_aval["SGST"];
		$IGST = $app_results_aval["IGST"];
		$TCS = $app_results_aval["TCS"];
		$ROFF = $app_results_aval["ROFF"];
		$AMOUNT = $app_results_aval["AMOUNT"];
		
		$CRDT_format = "";
		if($CRDT!=""){
		$CRDT_str = str_replace("/","",$CRDT);
		$CRDT_str = str_replace("Date","",$CRDT_str);	
		$CRDT_str = str_replace("(","",$CRDT_str);
		$CRDT_str = str_replace(")","",$CRDT_str);
		$CRDT_str = ($CRDT_str / 1000);
		//$DocDate_format = date("m/d/Y h:i:s A",$DocDate_str);
		$CRDT_format = date("Y-m-d",$CRDT_str);
		}
		
		$credit_note_data[]= array("kunnr"=>$kunnr,"GSTNO"=>$GSTNO,"CRDT"=>$CRDT_format,"NARRATION"=>$NARRATION,"QTY"=>$QTY,"BASIC"=>$BASIC,"CGST"=>$CGST,"SGST"=>
		$SGST,"IGST"=>$IGST,"TCS"=>$TCS,"ROFF"=>$ROFF,"AMOUNT"=>$AMOUNT,"CRNO"=>$CRNO );
		
	}
	//print_r($credit_note_data);
	usort($credit_note_data, 'sort_by_date');
	foreach($credit_note_data as $credit_note_data_val)
	{
		$kunnr = $credit_note_data_val["p_Code"];
		$GSTNO = $credit_note_data_val["GSTNO"];
		$CRNO = $credit_note_data_val["CRNO"];
		$CRDT_format = $credit_note_data_val["CRDT"];
		$NARRATION = $credit_note_data_val["NARRATION"];
		$QTY = $credit_note_data_val["QTY"];
		$BASIC = $credit_note_data_val["BASIC"];
		$CGST = $credit_note_data_val["CGST"];
		$SGST = $credit_note_data_val["SGST"];
		$IGST = $credit_note_data_val["IGST"];
		$TCS = $credit_note_data_val["TCS"];
		$ROFF = $credit_note_data_val["ROFF"];
		$AMOUNT = $credit_note_data_val["AMOUNT"];
		
		$CRDT_format = date("d/m/Y",strtotime($CRDT_format));
	?>
	<tr>
	<td><?php echo $GSTNO;?></td>
	<td><?php echo $CRNO;?></td>
	<td><?php echo $CRDT_format;?></td>
    <td><?php echo $NARRATION;?> </td>
	<td><?php echo number_format($QTY,2);?></td>
	<td><?php echo number_format($BASIC,2);?></td>
	<td><?php echo number_format($CGST,2);?></td>
    <td><?php echo number_format($SGST,2);?></td>
    <td><?php echo number_format($IGST,2);?></td>
    <td><?php echo number_format($TCS,2);?></td>
    <td><?php echo number_format($ROFF,2);?></td>
    <td><?php echo number_format($AMOUNT,2);?></td>
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
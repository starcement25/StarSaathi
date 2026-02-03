<?php
//echo"<pre>";print_r('ss');die;

include "web_check.php";
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$employee_master = "employee_master";
$customer_master = "customer_master";
$branch_master = "branch_master";

$curr_date = date("m/d/Y");
function sort_by_date($a, $b) {
    $a = strtotime($a['DOCDT']);
    $b = strtotime($b['DOCDT']);
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;
	//return strtotime($a) - strtotime($b);
}


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
$webservice_name = "SUBDEALER LEDGER";
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

$astn_dlsbdl_code=$_REQUEST['astn_dlsbdl_code'];
$add_page_name = "customer_debit_note.php";
$page_name = "customer_debit_note.php";
$cnt = 0;
if($astn_dlsbdl_code!=''){
	$the_subdealer_id=$astn_dlsbdl_code;
}
else
{
$the_subdealer_id=$the_br_dns_customer_code;
}
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
            <h3 style="text-align:center">Credit Note Register</p>
            <p style="text-align:center">Period From : <?php //echo $the_start_date?>  To : <?php //echo $the_end_date?></p>

            </div-->
            <div class="card">
            <div class="header">
            <h3 style="text-align:center">Sub dealer Daily Transaction Details</h3>
            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
				<span style="text-align:center">&nbsp;</span>
            <select class="form-control" id="astn_dlsbdl_code" name="astn_dlsbdl_code" style="padding-left:2px;">
            <option value="">Select Sub Dealer</option>
                <?php
				$brsql = "select `dns_customer_code`,`customer_name`,customer_id from $customer_master where `rds_tag` = '$sswa_selected_customer_code'
					and `acedns`='Y' AND cust_type IN('Sub Dealer','RSSD')  order by `customer_name` asc";
				$brres = mysql_query($brsql);
				$total_brres = mysql_num_rows($brres);
				if($total_brres>0){
                    while($brrow=mysql_fetch_assoc($brres)){
                        $the_br_dns_customer_code = $brrow["customer_id"];
                        $the_br_customer_name = $brrow["customer_name"];?>
                        <option value="<?php echo $the_br_dns_customer_code;?>" <?php if(($the_br_dns_customer_code==$sel_sdvl) || ($the_br_dns_customer_code==$astn_dlsbdl_code)){ ?> selected="selected" <?php } ?>><?php echo $the_br_customer_name;?></option>
                        <?php
                    }

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
    <a href="export_sub_dealer_ledger.php?the_start_date=<?php echo $from_dt;?>&the_end_date=<?php echo $to_dt;?>&the_subdealer_id=<?php echo $the_subdealer_id;?>" class="btn bg-red waves-effe">Export</a>
    </div>
    <br />

            </div>
				<br />
                        <h3 style="text-align:center">Period From : <?php echo $the_start_date?>  To : <?php echo $the_end_date?></h3>

              <div class="table-responsive">
              <table class="table table-bordered">
              <thead>
                <tr>
                <th width="14%">Sub dealer Code</th>
                 <th width="18%">Sub dealer Name</th>
                <th width="10%">DOCDT</th>
                <th width="9%">DOCNO</th>
                <th width="40%">NARRATION</th>
                <th width="9%">AMOUNT(Rs.)</th>
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

//https://devqasapp.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/YCP_SDN_CDS/YCP_SDN(p_Code='1000000021',p_Frm=datetime'2022-01-01T00:00:00',p_To=datetime'2022-12-01T00:00:00')/Set?sap-client=150$format=xml



 $url_ck1 = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/YVW_CPSDLEDGER_CDS/YVW_CPSDLEDGER(p_Code=\''.$the_subdealer_id.'\',p_Frm=datetime\''.$the_start_date_time.'\',p_To=datetime\''.$the_end_date_time.'\')/Set?$format=json&sap-client=900';
echo"<pre>";print_r($url_ck1);die;

$body_for_mcode10 = get_data_from_cserver($url_ck1);
//exit();
if(isJsonCk($body_for_mcode10)){
$json_decoded21 = json_decode($body_for_mcode10,true);
if(count($json_decoded21)>0){
if(array_key_exists("d",$json_decoded21)){
	$app_results_arr = $json_decoded21["d"]["results"];
	//print_r($app_results_arr);
if(count($app_results_arr)>0){
	foreach($app_results_arr as $app_results_aval){
	$SUBDEALERCODE = $app_results_aval["SUBDEALERCODE"];
	$SUBDEALER = $app_results_aval["SUBDEALER"];
	$DOCDT = $app_results_aval["DOCDT"];
	$NARRATION = $app_results_aval["NARRATION"];
	$DOCNO = $app_results_aval["DOCNO"];
	$AMOUNT = $app_results_aval["AMOUNT"];

	$DOCDT_format = "";
	if($DOCDT!=""){
	$DOCDT_str = str_replace("/","",$DOCDT);
	$DOCDT_str = str_replace("Date","",$DOCDT_str);
	$DOCDT_str = str_replace("(","",$DOCDT_str);
	$DOCDT_str = str_replace(")","",$DOCDT_str);
	$DOCDT_str = ($DOCDT_str / 1000);
	//$DocDate_format = date("m/d/Y h:i:s A",$DocDate_str);
	$DOCDT_format = date("Y-m-d",$DOCDT_str);
	}
	$sub_dealer_ledger_data[]= array("SUBDEALERCODE"=>$SUBDEALERCODE,"SUBDEALER"=>$SUBDEALER,"DOCDT"=>$DOCDT_format,"NARRATION"=>$NARRATION,"DOCNO"=>$DOCNO,"AMOUNT"=>$AMOUNT);

	}
	//print_r($credit_note_data);
	usort($sub_dealer_ledger_data, 'sort_by_date');
	foreach($sub_dealer_ledger_data as $sub_dealer_ledger_data_val)
	{
		$SUBDEALERCODE = $sub_dealer_ledger_data_val["SUBDEALERCODE"];
		$SUBDEALER = $sub_dealer_ledger_data_val["SUBDEALER"];
		$DOCDT = $sub_dealer_ledger_data_val["DOCDT"];
		$NARRATION = $sub_dealer_ledger_data_val["NARRATION"];
		$DOCNO = $sub_dealer_ledger_data_val["DOCNO"];
		$AMOUNT = $sub_dealer_ledger_data_val["AMOUNT"];

		$DOCDT_format = date("d/m/Y",strtotime($DOCDT));

	?>
	<tr>
	<td width="14%"><?php echo $SUBDEALERCODE;?></td>
	<td width="18%"><?php echo $SUBDEALER;?></td>
    <td width="10%"><?php echo $DOCDT_format;?> </td>
	<td width="9%"><?php echo $DOCNO;?></td>
	<td width="40%"><?php echo $NARRATION;?></td>
    <td width="9%"><?php echo number_format($AMOUNT,2);?></td>
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

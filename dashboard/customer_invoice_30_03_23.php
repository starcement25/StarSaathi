<?php
include "web_check.php";
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$employee_master = "employee_master";
$customer_master = "customer_master";
$branch_master = "branch_master";

$curr_date = date("m/d/Y");
function sort_by_date($a, $b) {
    $a = strtotime($a['InvoiceDt']);
    $b = strtotime($b['InvoiceDt']);
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;
	//return strtotime($a) - strtotime($b);
}

ob_start();
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
<form action="" method="post" name="searchform">
<section class="content">
        <div class="container-fluid">
            <div class="row clearfix">
            <!--div class="well well-lg">
            <h3 style="text-align:center">Invoice List</p>
            <p style="text-align:center">Period From : <?php //echo $the_start_date?>  To : <?php //echo $the_end_date?></p>

            </div-->
            <div class="card">
            <div class="header">
            		<h3 style="text-align:center">Invoice List</h3>
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
				<br />
				<br />
                <h3 style="text-align:center">Period From : <?php echo $the_start_date?>  To : <?php echo $the_end_date?></h3>
                 </div>
                 </form>
                <form action="" method="post" name="invoiceform">
	<input type="hidden" name="mode" id="modeval" value="" />
	<input type="hidden" name="invoice_no" id="invoice_no"  value="" />
    <input type="hidden" name="from_dt" id="from_dt"  value="<?php echo $from_dt;?>" />
    <input type="hidden" name="to_dt" id="to_dt"  value="<?php echo $to_dt;?>" />
    

              <div class="table-responsive">
              <table class="table table-bordered">
              <thead>
                <tr>
                <th>Invoice date</th>
                <th>Invoice no.</th>
				<th>Delivery no.</th>
				<th>Product Name</th>
				<th>Invoice Qty. (MT)</th>
				<th>Destination</th>
				<th>Truck No.</th>	
                </tr>
              </thead>
              <tbody>

<?php
	
	$curr_date = date("Ymd");
		if($from_dt=='' && $to_dt=='')
		{
		$the_start_date_invoice = date('Ymd', strtotime("-7 days,$curr_date"));
		$the_end_date_invoice = $curr_date;
		}
		else
		{
			$the_start_date_invoice=date('Ymd', strtotime($from_dt));
			$the_end_date_invoice=date('Ymd', strtotime($to_dt));
		}
//$the_filter2 = '&$filter=(Kunnr eq \''.$the_customer_id.'\' and DocDate eq datetime\''.$the_date_time.'\')';

$the_filter = '&$filter=(CustCo eq \''.$the_customer_id.'\' and ( InvoiceDt ge \''.$the_start_date_invoice.'\' and InvoiceDt le \''.$the_end_date_invoice.'\') )&sap-client=900';
$url_ck1 = 'https://PRDAPP1.starcement.co.in:44310/sap/opu/odata/sap/ZSD_CUSTOMER_BULK_INVOICE_SRV/ZSD_CUSTOMER_INVOICESet?$format=json'.str_replace(" ","%20",$the_filter);

$body_for_mcode10 = get_data_from_cserver($url_ck1);

if(isJsonCk($body_for_mcode10)){
$json_decoded21 = json_decode($body_for_mcode10,true);
if(count($json_decoded21)>0){
if(array_key_exists("d",$json_decoded21)){
	$app_results_arr = $json_decoded21["d"]["results"];
	//print_r($app_results_arr);
if(count($app_results_arr)>0){
	foreach($app_results_arr as $app_results_aval){
		$InvoiceNo = $app_results_aval["InvoiceNo"];
		$InvoiceDt = $app_results_aval["InvoiceDt"];
		
		$InvoiceDt_format = date("Y-m-d",strtotime($InvoiceDt));
		$InvoiceDt = $app_results_aval["InvoiceDt"];
		$ChallanNo = $app_results_aval["ChallanNo"];
		$Description = $app_results_aval["Description"];
		$Qty = $app_results_aval["Qty"];
		$ConDest = $app_results_aval["ConDest"];
		$VehicleNo = $app_results_aval["VehicleNo"];
		
	$CompName1 = $app_results_aval["CompName1"];
	$CompName2 = $app_results_aval["CompName2"];
	$Plant = $app_results_aval["Plant"];
	$CinNo = $app_results_aval["CinNo"];
	$WorksOff = $app_results_aval["WorksOff"];
	$PlantAdd = $app_results_aval["PlantAdd"];
	$EwayBill = $app_results_aval["EwayBill"];
	$Irn = $app_results_aval["Irn"];
		$VehicleNo = $app_results_aval["VehicleNo"];
	$VendLocation = $app_results_aval["VendLocation"];
	$VendAddress = $app_results_aval["VendAddress"];
	$VendGstin = $app_results_aval["VendGstin"];
	$VendPin = $app_results_aval["VendPin"];
	$VendStCode = $app_results_aval["VendStCode"];
	$VendState = $app_results_aval["VendState"];
	$DoNo = $app_results_aval["DoNo"];
	$DoDate = $app_results_aval["DoDate"];
	$CustPo = $app_results_aval["CustPo"];
	$CustPoDt = $app_results_aval["CustPoDt"];
	//$CustPoDtfinal=substr($CustPoDt,6,2).'/'.substr($CustPoDt,4,2).'/'.substr($CustPoDt,0,4);
	$OurRefNo = $app_results_aval["OurRefNo"];
	$ChallanDt = $app_results_aval["ChallanDt"];
	$ShipmentNo = $app_results_aval["ShipmentNo"];
	$ShipmentDt = $app_results_aval["ShipmentDt"];
	$CustName = $app_results_aval["CustName"];
	$CustCo = $app_results_aval["CustCo"];
	$CustAdd = $app_results_aval["CustAdd"];
	$CustPin = $app_results_aval["CustPin"];
	$CustGstin = $app_results_aval["CustGstin"];
	$CustSt = $app_results_aval["CustSt"];
	$CustStCode = $app_results_aval["CustStCode"];
	$ConName = $app_results_aval["ConName"];
	$ConCo = $app_results_aval["ConCo"];
	$ConAdd = $app_results_aval["ConAdd"];
	$WeekNo = $app_results_aval["WeekNo"];
	$ConPin = $app_results_aval["ConPin"];
	$ConGstin = $app_results_aval["ConGstin"];
	$ConStCode = $app_results_aval["ConStCode"];
	$ConSt = $app_results_aval["ConSt"];	
	$Freight = $app_results_aval["Freight"];	
	$Cgst = $app_results_aval["Cgst"];	
	$CgstAmt = $app_results_aval["CgstAmt"];	
	$Sgst = $app_results_aval["Sgst"];	
	$SgstAmt = $app_results_aval["SgstAmt"];	
	$Igst = $app_results_aval["Igst"];	
	$IgstAmt = $app_results_aval["IgstAmt"];		
	$Tcs = $app_results_aval["Tcs"];	
	$TcsAmt = $app_results_aval["TcsAmt"];	
	$Cess = $app_results_aval["Cess"];	
	$CessAmt = $app_results_aval["CessAmt"];	
	$ROff = $app_results_aval["ROff"];	
	$Total = $app_results_aval["Total"];	
	$TotInWords = $app_results_aval["TotInWords"];	
	$TransMode = $app_results_aval["TransMode"];	
	$TransCode = $app_results_aval["TransCode"];
	$Transporter = $app_results_aval["Transporter"];
	$LrRr = $app_results_aval["LrRr"];
	$LrRrDt = $app_results_aval["LrRrDt"];
	$Inco1 = $app_results_aval["Inco1"];
	$Inco2 = $app_results_aval["Inco2"];
	$RouteCode = $app_results_aval["RouteCode"];
	$RouteDesc = $app_results_aval["RouteDesc"];
	$ForName = $app_results_aval["ForName"];
	$OffAdd1 = $app_results_aval["OffAdd1"];
	$OffAdd2 = $app_results_aval["OffAdd2"];
	$Juri = $app_results_aval["Juri"];
	$CurrKey = $app_results_aval["CurrKey"];
	$UomHead = $app_results_aval["UomHead"];
	$JuriState = $app_results_aval["JuriState"];
	$Vkgrp = $app_results_aval["Vkgrp"];
	$SlNo = $app_results_aval["SlNo"];
	$Description = $app_results_aval["Description"];
	$Hsn = $app_results_aval["Hsn"];
	$PackageDesc = $app_results_aval["PackageDesc"];
	$TotPackage = $app_results_aval["TotPackage"];
	$Uom = $app_results_aval["Uom"];
	$Rate = $app_results_aval["Rate"];
	$Tax = $app_results_aval["Tax"];
	$FreightAmt = $app_results_aval["FreightAmt"];
	$QrCode1 = $app_results_aval["QrCode1"];
	
		$customer_invoice_array[]=	array("InvoiceNo" =>$InvoiceNo,"InvoiceDt" =>$InvoiceDt_format,"ChallanNo" =>$ChallanNo,"WorksOff" =>$WorksOff,"Description" =>$Description,"Qty" =>$Qty,"ConDest" =>$ConDest,"ConName" =>$ConName,"ConAdd" =>$ConAdd,"VehicleNo" =>$VehicleNo,"CompName1" =>$CompName1,"CompName2" =>$CompName2,"Plant" =>$Plant,"CinNo" =>$CinNo,"PlantAdd" =>$PlantAdd,"EwayBill" =>$EwayBill,"Irn" =>$Irn,"VendLocation" =>$VendLocation,"VendAddress" =>$VendAddress,"VendGstin" =>$VendGstin,"VendPin" =>$VendPin,"VendStCode" =>$VendStCode,"VendState" =>$VendState,"DoNo" =>$DoNo,"DoDate" =>$DoDate,"CustPo" =>$CustPo,"CustPoDt" =>$CustPoDt,"OurRefNo" =>$OurRefNo,"ChallanDt" =>$ChallanDt,"ShipmentNo" =>$ShipmentNo,"ShipmentDt" =>$ShipmentDt,"CustName" =>$CustName,"CustCo" =>$CustCo,"CustAdd" =>$CustAdd,"CustPin" =>$CustPin,"CustGstin" =>$CustGstin,"CustSt" =>$CustSt,"CustStCode" =>$CustStCode,"ConCo" =>$ConCo,"WeekNo" =>$WeekNo,"ConPin" =>$ConPin,"ConGstin" =>$ConGstin,"ConStCode" =>$ConStCode,"ConSt" =>$ConSt,"Freight" =>$Freight,"Cgst" =>$Cgst,"CgstAmt" =>$CgstAmt,"Sgst" =>$Sgst,"SgstAmt" =>$SgstAmt,"Igst" =>$Igst,"IgstAmt" =>$IgstAmt,"Tcs" =>$Tcs,"TcsAmt" =>$TcsAmt,"Cess" =>$Cess,"CessAmt" =>$CessAmt,"ROff" =>$ROff,"Total" =>$Total,"TotInWords" =>$TotInWords,"TransMode" =>$TransMode,"TransCode" =>$TransCode,"Transporter" =>$Transporter,"LrRr" =>$LrRr,"LrRrDt" =>$LrRrDt,"Inco1" =>$Inco1,"Inco2" =>$Inco2,"RouteCode" =>$RouteCode,"RouteDesc"=>$RouteDesc,"ForName" =>$ForName,"OffAdd1" =>$OffAdd1,"OffAdd2" =>$OffAdd2,"Juri" =>$Juri,"CurrKey" =>$CurrKey,"UomHead" =>$UomHead,"JuriState" =>$JuriState,"Vkgrp" =>$Vkgrp,"SlNo" =>$SlNo,"Description" =>$Description,"Hsn" =>$Hsn,"PackageDesc" =>$PackageDesc,"TotPackage" =>$TotPackage,"Uom" =>$Uom,"Rate" =>$Rate,"Tax" =>$Tax,"FreightAmt" =>$FreightAmt,"QrCode1" =>$QrCode1);

	}
	usort($customer_invoice_array, 'sort_by_date');
	foreach($customer_invoice_array as $customer_invoice_data_val)
	{
		$InvoiceNo = $customer_invoice_data_val["InvoiceNo"];
		$InvoiceDt = $customer_invoice_data_val["InvoiceDt"];
		
		$InvoiceDt_format = date("d-m-Y",strtotime($InvoiceDt));
		$InvoiceDt = $customer_invoice_data_val["InvoiceDt"];
		$ChallanNo = $customer_invoice_data_val["ChallanNo"];
		$Description = $customer_invoice_data_val["Description"];
		$Qty = $customer_invoice_data_val["Qty"];
		$ConDest = $customer_invoice_data_val["ConDest"];
		$VehicleNo = $customer_invoice_data_val["VehicleNo"];
		
	$CompName1 = $customer_invoice_data_val["CompName1"];
	$CompName2 = $customer_invoice_data_val["CompName2"];
	$Plant = $customer_invoice_data_val["Plant"];
	$CinNo = $customer_invoice_data_val["CinNo"];
	$WorksOff = $customer_invoice_data_val["WorksOff"];
	$PlantAdd = $customer_invoice_data_val["PlantAdd"];
	$EwayBill = $customer_invoice_data_val["EwayBill"];
	$Irn = $customer_invoice_data_val["Irn"];
		$VehicleNo = $customer_invoice_data_val["VehicleNo"];
	$VendLocation = $customer_invoice_data_val["VendLocation"];
	$VendAddress = $customer_invoice_data_val["VendAddress"];
	$VendGstin = $customer_invoice_data_val["VendGstin"];
	$VendPin = $customer_invoice_data_val["VendPin"];
	$VendStCode = $customer_invoice_data_val["VendStCode"];
	$VendState = $customer_invoice_data_val["VendState"];
	$DoNo = $customer_invoice_data_val["DoNo"];
	$DoDate = $customer_invoice_data_val["DoDate"];
	$CustPo = $customer_invoice_data_val["CustPo"];
	$CustPoDt = $customer_invoice_data_val["CustPoDt"];
	//$CustPoDtfinal=substr($CustPoDt,6,2).'/'.substr($CustPoDt,4,2).'/'.substr($CustPoDt,0,4);
	$OurRefNo = $customer_invoice_data_val["OurRefNo"];
	$ChallanDt = $customer_invoice_data_val["ChallanDt"];
	$ShipmentNo = $customer_invoice_data_val["ShipmentNo"];
	$ShipmentDt = $customer_invoice_data_val["ShipmentDt"];
	$CustName = $customer_invoice_data_val["CustName"];
	$CustCo = $customer_invoice_data_val["CustCo"];
	$CustAdd = $customer_invoice_data_val["CustAdd"];
	$CustPin = $customer_invoice_data_val["CustPin"];
	$CustGstin = $customer_invoice_data_val["CustGstin"];
	$CustSt = $customer_invoice_data_val["CustSt"];
	$CustStCode = $customer_invoice_data_val["CustStCode"];
	$ConName = $customer_invoice_data_val["ConName"];
	$ConCo = $customer_invoice_data_val["ConCo"];
	$ConAdd = $customer_invoice_data_val["ConAdd"];
	$WeekNo = $customer_invoice_data_val["WeekNo"];
	$ConPin = $customer_invoice_data_val["ConPin"];
	$ConGstin = $customer_invoice_data_val["ConGstin"];
	$ConStCode = $customer_invoice_data_val["ConStCode"];
	$ConSt = $customer_invoice_data_val["ConSt"];	
	$Freight = $customer_invoice_data_val["Freight"];	
	$Cgst = $customer_invoice_data_val["Cgst"];	
	$CgstAmt = $customer_invoice_data_val["CgstAmt"];	
	$Sgst = $customer_invoice_data_val["Sgst"];	
	$SgstAmt = $customer_invoice_data_val["SgstAmt"];	
	$Igst = $customer_invoice_data_val["Igst"];	
	$IgstAmt = $customer_invoice_data_val["IgstAmt"];		
	$Tcs = $customer_invoice_data_val["Tcs"];	
	$TcsAmt = $customer_invoice_data_val["TcsAmt"];	
	$Cess = $customer_invoice_data_val["Cess"];	
	$CessAmt = $customer_invoice_data_val["CessAmt"];	
	$ROff = $customer_invoice_data_val["ROff"];	
	$Total = $customer_invoice_data_val["Total"];	
	$TotInWords = $customer_invoice_data_val["TotInWords"];	
	$TransMode = $customer_invoice_data_val["TransMode"];	
	$TransCode = $customer_invoice_data_val["TransCode"];
	$Transporter = $customer_invoice_data_val["Transporter"];
	$LrRr = $customer_invoice_data_val["LrRr"];
	$LrRrDt = $customer_invoice_data_val["LrRrDt"];
	$Inco1 = $customer_invoice_data_val["Inco1"];
	$Inco2 = $customer_invoice_data_val["Inco2"];
	$RouteCode = $customer_invoice_data_val["RouteCode"];
	$RouteDesc = $customer_invoice_data_val["RouteDesc"];
	$ForName = $customer_invoice_data_val["ForName"];
	$OffAdd1 = $customer_invoice_data_val["OffAdd1"];
	$OffAdd2 = $customer_invoice_data_val["OffAdd2"];
	$Juri = $customer_invoice_data_val["Juri"];
	$CurrKey = $customer_invoice_data_val["CurrKey"];
	$UomHead = $customer_invoice_data_val["UomHead"];
	$JuriState = $customer_invoice_data_val["JuriState"];
	$Vkgrp = $customer_invoice_data_val["Vkgrp"];
	$SlNo = $customer_invoice_data_val["SlNo"];
	$Description = $customer_invoice_data_val["Description"];
	$Hsn = $customer_invoice_data_val["Hsn"];
	$PackageDesc = $customer_invoice_data_val["PackageDesc"];
	$TotPackage = $customer_invoice_data_val["TotPackage"];
	$Uom = $customer_invoice_data_val["Uom"];
	$Rate = $customer_invoice_data_val["Rate"];
	$Tax = $customer_invoice_data_val["Tax"];
	$FreightAmt = $customer_invoice_data_val["FreightAmt"];
	$QrCode1 = $customer_invoice_data_val["QrCode1"];
		
	
				${invoice_print_array_.$InvoiceNo}[]=	array("InvoiceNo" =>$InvoiceNo,"InvoiceDt" =>$InvoiceDt_format,"ChallanNo" =>$ChallanNo,"WorksOff" =>$WorksOff,"Description" =>$Description,"Qty" =>$Qty,"ConDest" =>$ConDest,"ConName" =>$ConName,"ConAdd" =>$ConAdd,"VehicleNo" =>$VehicleNo,"CompName1" =>$CompName1,"CompName2" =>$CompName2,"Plant" =>$Plant,"CinNo" =>$CinNo,"PlantAdd" =>$PlantAdd,"EwayBill" =>$EwayBill,"Irn" =>$Irn,"VendLocation" =>$VendLocation,"VendAddress" =>$VendAddress,"VendGstin" =>$VendGstin,"VendPin" =>$VendPin,"VendStCode" =>$VendStCode,"VendState" =>$VendState,"DoNo" =>$DoNo,"DoDate" =>$DoDate,"CustPo" =>$CustPo,"CustPoDt" =>$CustPoDt,"OurRefNo" =>$OurRefNo,"ChallanDt" =>$ChallanDt,"ShipmentNo" =>$ShipmentNo,"ShipmentDt" =>$ShipmentDt,"CustName" =>$CustName,"CustCo" =>$CustCo,"CustAdd" =>$CustAdd,"CustPin" =>$CustPin,"CustGstin" =>$CustGstin,"CustSt" =>$CustSt,"CustStCode" =>$CustStCode,"ConCo" =>$ConCo,"WeekNo" =>$WeekNo,"ConPin" =>$ConPin,"ConGstin" =>$ConGstin,"ConStCode" =>$ConStCode,"ConSt" =>$ConSt,"Freight" =>$Freight,"Cgst" =>$Cgst,"CgstAmt" =>$CgstAmt,"Sgst" =>$Sgst,"SgstAmt" =>$SgstAmt,"Igst" =>$Igst,"IgstAmt" =>$IgstAmt,"Tcs" =>$Tcs,"TcsAmt" =>$TcsAmt,"Cess" =>$Cess,"CessAmt" =>$CessAmt,"ROff" =>$ROff,"Total" =>$Total,"TotInWords" =>$TotInWords,"TransMode" =>$TransMode,"TransCode" =>$TransCode,"Transporter" =>$Transporter,"LrRr" =>$LrRr,"LrRrDt" =>$LrRrDt,"Inco1" =>$Inco1,"Inco2" =>$Inco2,"RouteCode" =>$RouteCode,"RouteDesc"=>$RouteDesc,"ForName" =>$ForName,"OffAdd1" =>$OffAdd1,"OffAdd2" =>$OffAdd2,"Juri" =>$Juri,"CurrKey" =>$CurrKey,"UomHead" =>$UomHead,"JuriState" =>$JuriState,"Vkgrp" =>$Vkgrp,"SlNo" =>$SlNo,"Description" =>$Description,"Hsn" =>$Hsn,"PackageDesc" =>$PackageDesc,"TotPackage" =>$TotPackage,"Uom" =>$Uom,"Rate" =>$Rate,"Tax" =>$Tax,"FreightAmt" =>$FreightAmt,"QrCode1" =>$QrCode1);

		//$invoice_print_data=http_build_query($invoice_print_array);
	?>
	<tr>
     <!--td><input type="checkbox" name="invoiceval" id="invoiceval<?php //echo $InvoiceNo;?>" value="<?php //echo $InvoiceNo;?>" onchange="invoice_print();"/></td-->

	<td><?php echo $InvoiceDt_format;?></td>
	<td><!--a href="export_invoice_check.php?invoice_print_data=<?php echo $invoice_print_data;?>" class="btn bg-red waves-effe"><?php //echo $InvoiceNo;?></a--><a href="javascript:void(0);" onClick="javascript:invoiceprint('<?php echo $InvoiceNo;?>');"><?php echo $InvoiceNo;?></a></td>
	<td><?php echo $ChallanNo;?></td>
	<td><?php echo $Description;?></td>
	<td><?php echo $Qty;?></td>
	<td><?php echo $ConDest;?></td>
	<td><?php echo $VehicleNo;?></td>	
	</tr>		  
	<?php
	   }
	 }
	}
   }
  }
	if($_REQUEST['mode']=='invoiceprint'  && $_REQUEST['invoice_no']!='')
	{
		//echo 'sdsdssssssssssvfgffffffffffffffffffffffffffffffffffffffffffffffffffff'.$invoicevalue=$_REQUEST[invoice_no];
		//exit();
		
			//print_r(${'invoice_print_array_'.$_REQUEST['invoice_no']});
		//exit();
		
		foreach(${'invoice_print_array_'.$_REQUEST['invoice_no']} as $invoice_details_val)
		{
			
			$InvoiceDt = $invoice_details_val["InvoiceDt"];
			$InvoiceDt_format=date('d/m/Y',strtotime($InvoiceDt));
			$ChallanNo = $invoice_details_val["ChallanNo"];
			$Description = $invoice_details_val["Description"];
			$Qty = round($invoice_details_val["Qty"],2);
			$ConDest = $invoice_details_val["ConDest"];
			$VehicleNo = $invoice_details_val["VehicleNo"];
			$CompName1 = $invoice_details_val["CompName1"];
			$CompName2 = $invoice_details_val["CompName2"];
			$Plant = $invoice_details_val["Plant"];
			
			$CinNo = $invoice_details_val["CinNo"];
			$WorksOff = $invoice_details_val["WorksOff"];
			$PlantAdd = $invoice_details_val["PlantAdd"];
			$EwayBill = $invoice_details_val["EwayBill"];
			$Irn = $invoice_details_val["Irn"];
				$VehicleNo = $invoice_details_val["VehicleNo"];
			$VendLocation = $invoice_details_val["VendLocation"];
			$VendAddress = $invoice_details_val["VendAddress"];
			$VendGstin = $invoice_details_val["VendGstin"];
			$VendPin = $invoice_details_val["VendPin"];
			$VendStCode = $invoice_details_val["VendStCode"];
			$VendState = $invoice_details_val["VendState"];
			$DoNo = $invoice_details_val["DoNo"];
			$DoDate = $invoice_details_val["DoDate"];
			$DoDate_format=date("d/m/Y",strtotime($DoDate));
			$CustPo = $invoice_details_val["CustPo"];
			$CustPoDt = $invoice_details_val["CustPoDt"];
			$CustPoDt_format=date("d/m/Y",strtotime($CustPoDt));
			$OurRefNo = $invoice_details_val["OurRefNo"];
			$ChallanDt = $invoice_details_val["ChallanDt"];
			$ChallanDt_format = date("d/m/Y",strtotime($ChallanDt));
			$ShipmentNo = $invoice_details_val["ShipmentNo"];
			$ShipmentDt = $invoice_details_val["ShipmentDt"];
			$ShipmentDt_format = date("d/m/Y",strtotime($ShipmentDt));
			$CustName = $invoice_details_val["CustName"];
			$CustCo = $invoice_details_val["CustCo"];
			$CustAdd = $invoice_details_val["CustAdd"];
			$CustPin = $invoice_details_val["CustPin"];
			$CustGstin = $invoice_details_val["CustGstin"];
			$CustSt= $invoice_details_val["CustSt"];
			$CustStCode = $invoice_details_val["CustStCode"];
			$ConName = $invoice_details_val["ConName"];
			$ConCo = $invoice_details_val["ConCo"];
			$ConAdd = $invoice_details_val["ConAdd"];
			$WeekNo = $invoice_details_val["WeekNo"];
			$ConPin = $invoice_details_val["ConPin"];
			$ConGstin = $invoice_details_val["ConGstin"];
			$ConStCode = $invoice_details_val["ConStCode"];
			$ConSt = $invoice_details_val["ConSt"];	
			$Freight = number_format(round($invoice_details_val["Freight"],2),2);	
			$Cgst = $invoice_details_val["Cgst"];
			if($Cgst=='') 	$Cgststring='';
			else 			 $Cgststring=$Cgst;
			$CgstAmt = round($invoice_details_val["CgstAmt"],2);
			if($Cgst=='') 	$CgstAmtstring='0.00';	
			else 			$CgstAmtstring=$CgstAmt;	
			$Sgst = $invoice_details_val["Sgst"];
			if($Sgst=='') 	$Sgststring='';
			else 			 $Sgststring=$Sgst;	
			$SgstAmt = round($invoice_details_val["SgstAmt"],2);
			if($Sgst=='') 	$SgstAmtstring='0.00';	
			else 			$SgstAmtstring=$SgstAmt;		
			$Igst = $invoice_details_val["Igst"];	
			if($Igst=='') 	$Igststring='';
			else 			 $Igststring=$Igst;
			$IgstAmt = round($invoice_details_val["IgstAmt"],2);
			if($Igst=='') 	$IgstAmtstring='0.00';
			else 			 $IgstAmtstring=$IgstAmt;		
			$Tcs = $invoice_details_val["Tcs"];
			if($Tcs=='' ||  $Tcs==0)     $Tcs='0.00';	
			$TcsAmt = round($invoice_details_val["TcsAmt"],2);	
			$Cess = $invoice_details_val["Cess"];	
			$CessAmt = $invoice_details_val["CessAmt"];	
			$ROff = round($invoice_details_val["ROff"],2);
			if($ROff=='' ||  $ROff==0)     $ROff='0.00';		
			$Total = number_format(round($invoice_details_val["Total"],2),2);	
			$TotInWords = $invoice_details_val["TotInWords"];	
			$TransMode = $invoice_details_val["TransMode"];	
			$TransCode = $invoice_details_val["TransCode"];
			$Transporter = $invoice_details_val["Transporter"];
			$LrRr = $invoice_details_val["LrRr"];
			$LrRrDt = $invoice_details_val["LrRrDt"];
			if($LrRrDt !='')
			{
			$LrRrDt_format = date("d/m/Y",strtotime($LrRrDt));
			}
			else $LrRrDt_format='';
			
			$Inco1 = $invoice_details_val["Inco1"];
			$Inco2 = $invoice_details_val["Inco2"];
			$RouteCode = $invoice_details_val["RouteCode"];
			$RouteDesc = $invoice_details_val["RouteDesc"];
			$ForName = $invoice_details_val["ForName"];
			$OffAdd1 = $invoice_details_val["OffAdd1"];
			$OffAdd2 = $invoice_details_val["OffAdd2"];
			$Juri = $invoice_details_val["Juri"];
			$CurrKey = $invoice_details_val["CurrKey"];
			$UomHead = $invoice_details_val["UomHead"];
			$JuriState = $invoice_details_val["JuriState"];
			$Vkgrp = $invoice_details_val["Vkgrp"];
			$SlNo = $invoice_details_val["SlNo"];
			$Description = $invoice_details_val["Description"];
			$Hsn = $invoice_details_val["Hsn"];
			$PackageDesc = $invoice_details_val["PackageDesc"];
			$TotPackage = $invoice_details_val["TotPackage"];
			$Uom = $invoice_details_val["Uom"];
			$Rate = round($invoice_details_val["Rate"],2);
			$Tax = round($invoice_details_val["Tax"],2);
			$FreightAmt = round($invoice_details_val["FreightAmt"],2);
			$QrCode1 = $invoice_details_val["QrCode1"];
		}
		/*<img src=\"images/qr.jpg\" alt=\"Star\" width=\"50px\" style=\"position: absolute; top:10px; right:10px;\"/>
		<img src=\"https://chart.googleapis.com/chart?chs=70x70&cht=qr&chl=http%3A%2F%2Fwww.google.com%2F&choe=UTF-8\" alt=\"Star\" width=\"50px\" style=\"position: absolute; top:10px; right:10px;\"/>*/ 
		$output .= "<html><body style=\"border:1px solid #333;\">
<div class=\"container\" style=\"width:100%;\">
	<table style=\"width:100%; text-align:left; border:1px solid #333;\" cellpadding=\"2\">
  <tr>
   <td style=\"width:20%;text-align:left;\"><img src=\"images/star-pdf-logo.jpeg\" width=\"50px\" style=\"position: absolute; top:10px; left:10px;\" /></td>
   <td style=\"width:60%;text-align:center;\"> <p style=\"text-align:center; font-size:10px;margin: 0px; padding-top:2px;\"><strong>$CompName1</strong></p>
  		<p style=\"text-align:center;font-size:8px;margin: 0px; padding-top:2px;\">$CinNo</p>
  		<p style=\"text-align:center; font-size:8px;margin: 0px; padding-top:2px;\">$WorksOff</p>
  		<p style=\"text-align:center;font-size:8px;padding-bottom:10px;\"><strong>$CompName2</strong><br /></p></td>
   	<td style=\"width:20%;text-align:right;\" ><img src=\"https://chart.googleapis.com/chart?chs=90x90&cht=qr&chl='".$Irn."'&choe=UTF-8\" alt=\"Star\" width=\"50px\" style=\"position: absolute; top:10px; right:10px;\"/></td>
   </tr>
   </table>
  <table style=\"width:100%; text-align:left; border:1px solid #333;\" cellpadding=\"4\">
  <tr>
  <td style=\"padding-left:10px;border-top:1px solid #333;\" colspan=\"2\"><p style=\"padding-left:10px; margin:0px; font-size:8px;\">E-Way Bill No: $EwayBill</p></td>
  </tr>
  <tr>
  <td style=\"padding-left:10px;border-top:1px solid #333;border-bottom:1px solid #333;\" colspan=\"2\"><p style=\"padding-left:10px; margin:0px; font-size:8px;\">IRN No: $Irn</p></td>
  </tr>
  <tr>
  <td style=\"width:50%; padding-left:10px;font-size:10px;\" >$PlantAdd
  </td>
  <td style=\"width:50%;font-size:8px; border-left:1px solid #333;padding-left:10px;\">Invoice No: ".$_REQUEST['invoice_no']."<br/>
  Invoice Dt:$InvoiceDt_format<br/><br/>
  Cust PO No: $CustPo <span style=\"padding-left:70px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cust PO Dt: $CustPoDt_format</span>
  </td>
  </tr>
  <tr>
  <td style=\"width:50%; padding-left:10px;font-size:8px; border-top:1px solid #333;\">GSTIN: $VendGstin <span style=\"padding-left:70px;\">PIN No: $VendPin</span><br/>
  State: $VendState <span style=\"padding-left:100px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;State Code: $VendStCode</span><br/>
  SO No: $DoNo <span style=\"padding-left:70px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SO Dt: $DoDate_format</span>
  </td>
  <td style=\"width:50%;font-size:8px; border-left:1px solid #333; padding-left:10px;border-top:1px solid #333;\">Our Ref No: $OurRefNo<br/>
  Delivery No: $ChallanNo <span style=\"padding-left:90px\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Delivery Dt: $ChallanDt_format</span><br/>
  Shipment No: $ShipmentNo <span style=\"padding-left:90px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Shipment Dt: $ShipmentDt_format</span>
  </td>
  </tr>
  <tr>
  <td style=\"width:50%; padding-left:10px; border-top:1px solid #333; font-size:10px\"><strong>Name & Address of the Customer (Billed To):</strong></td>
  <td style=\"width:50%; padding-left:10px; border-top:1px solid #333;border-left:1px solid #333; font-size:10px;\"><strong>Delivery Address of Consignee (Ship To):</strong></td>
  </tr>
   <tr>
  <td style=\"width:50%; padding-left:10px; border-top:1px solid #333;\">
  $CustName<br />
  <p style=\"font-size:8px; margin:0px;\">$CustAdd</p>
  <p style=\"font-size:8px; margin:0px; margin-top:10px;\">GSTIN: $CustGstin <span style=\"padding-left:70px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PIN No: $CustPin</span></p>
  <p style=\"font-size:8px; margin:0px;\">State: $CustSt <span style=\"padding-left:70px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;State Code: $CustStCode</span></p>
  </td>
  <td style=\"width:50%; padding-left:10px; border-top:1px solid #333;border-left:1px solid #333;\">
  $ConName<br />
  <p style=\"font-size:8px; margin:0px;\">$ConAdd</p>
  <p style=\"font-size:8px; margin:0px; margin-top:10px;\">Batch No: $WeekNo</p>
  <p style=\"font-size:8px; margin:0px;\">Destination: $ConDest <span style=\"padding-left:70px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PIN No: $ConPin</span></p>
  <p style=\"font-size:8px; margin:0px;\">State: $ConSt <span style=\"padding-left:70px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;State Code: $ConStCode</span></p>
  </td>
  </tr>
  </table>
  <table style=\"width:100%; text-align:left; border:1px solid #333; margin:0px;\" cellpadding=\"1\">
  <tr>
  <td style=\"width:7%;padding-left:8px; text-align:center; font-size:8px;\"><strong>SL NO</strong></td>
  <td style=\"width:25%;padding-left:8px;border-left:1px solid #333;text-align:center;font-size:8px;\"><strong>Description of Goods</strong></td>
  <td style=\"width:7%;padding-left:8px;border-left:1px solid #333;text-align:center;font-size:8px;\"><strong>HSN</strong></td>
  <td style=\"width:17%;padding-left:8px;border-left:1px solid #333;text-align:center;font-size:8px;\"><strong>Description of Package</strong></td>
  <td style=\"width:10%;padding-left:8px;border-left:1px solid #333;text-align:center;font-size:8px;\"><strong>No of Bags</strong></td>
  <td style=\"width:7%;padding-left:8px;border-left:1px solid #333;text-align:center;font-size:8px;\"><strong>UOM</strong></td>
  <td style=\"width:7%;padding-left:8px;border-left:1px solid #333;text-align:center;font-size:8px;\"><strong>QTY<br/>(MT)</strong></td>
  <td style=\"width:10%;padding-left:8px;border-left:1px solid #333;text-align:center;font-size:8px;\"><strong>Basic Rate /MT <br/><span style=\"font-family:dejavusans;\">&#8377;</span></strong></td>
  <td style=\"width:10%;padding-left:8px;border-left:1px solid #333;text-align:center;font-size:8px;\"><strong>Taxable Amount <br/><span style=\"font-family:dejavusans;\">&#8377;</span></strong></td>
  </tr>
  <tr>
  <td style=\"width:7%;padding-left:8px; text-align:center;border-top:1px solid #333;\">1</td>
  <td style=\"width:25%;padding-left:8px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;\">$Description</td>
  <td style=\"width:7%;padding-left:8px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;\">$Hsn</td>
  <td style=\"width:17%;padding-left:8px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;\">$PackageDesc</td>
  <td style=\"width:10%;padding-left:8px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;\">$TotPackage</td>
  <td style=\"width:7%;padding-left:8px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;\">$Uom</td>
  <td style=\"width:7%;padding-left:8px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;\">$Qty</td>
  <td style=\"width:10%;padding-left:8px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;\">$Rate</td>
  <td style=\"width:10%;padding-left:8px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px; text-align:right\">$Tax</td>
  </tr>
  <tr>
  <td colspan=\"6\" rowspan=\"3\" style=\"font-size:10px;padding-left:10px;border-top:1px solid #333;\">
  <strong>Declaration</strong><br/>
  Verified that the particulars given above are true and correct and the amount indicated presents the price actually charged and that there is no flow of additional consideration directly or indirectly from the buyer.
  </td>
 
  <td colspan=\"2\" style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px; margin:0px;\">Freight:</td>
  <td style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333; text-align:right;font-size:8px; margin:0px;\">$Freight</td>
  </tr>
  <tr>
  <td colspan=\"2\" style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px; margin:0px;\">CGST: $Cgststring</td>
  <td style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333; text-align:right;font-size:8px; margin:0px;\">$CgstAmtstring</td>
  </tr>
  <tr>
  <td colspan=\"2\" style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px; margin:0px;\">SGST: $Sgststring</td>
  <td style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333; text-align:right;font-size:8px; margin:0px;\">$SgstAmtstring</td>
  </tr>
  <tr>
  <td colspan=\"6\" rowspan=\"2\" style=\"padding-left:10px;border-top:1px solid #333;font-size:8px;\">
  Payment to be made electronically through RTGS/NEFT/IMPS & also online UPI payment mode on our website www.starcement.co.in.
  </td>
  <td colspan=\"2\" style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px; margin:0px;\">IGST: $Igststring</td>
  <td style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333; text-align:right;font-size:8px; margin:0px;\">$IgstAmtstring</td>
  </tr>
  <tr>
  <td colspan=\"2\" style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px; margin:0px;\">TCS</td>
  <td style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333; text-align:right;font-size:8px; margin:0px;\">$Tcs</td>
  </tr>
  <tr>
  <td colspan=\"6\" rowspan=\"2\" style=\"padding-left:10px;border-top:1px solid #333;font-size:10px;\">
  <strong>Amount in Words</strong><br />
  $TotInWords<br />
  </td>
  <td colspan=\"2\" style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px; margin:0px;\">ROFF:</td>
  <td style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333; text-align:right;font-size:8px; margin:0px;\">$ROff</td>
  </tr>
  <tr>
  <td colspan=\"2\" style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px; margin:0px;\"><strong>Total:</strong></td>
  <td style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333; text-align:right;font-size:8px; margin:0px;\"><strong>$Total</strong></td>
  </tr>
  <tr>
  <td colspan=\"2\" style=\"padding-left:10px;border-top:1px solid #333;font-size:8px;\">
  Mode Of Transport
  </td>
  <td colspan=\"4\" style=\"padding-left:10px; border-top:1px solid #333;font-size:8px; margin:0px;\">$TransMode</td>
  <td colspan=\"3\" style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;\"></td>
  </tr>
  <tr>
  <td colspan=\"2\" style=\"padding-left:10px;border-top:1px solid #333;font-size:8px;\">
  Transporter Code & Name
  </td>

  <td colspan=\"4\" style=\"padding-left:10px; border-top:1px solid #333;font-size:8px; margin:0px;\">$TransCode <span style=\"padding-left:70px;\">$Transporter</span></td>
  <td colspan=\"3\" style=\"padding-left:10px;border-left:1px solid #333;\"></td>
  </tr>
  <tr>
  <td colspan=\"2\" style=\"padding-left:10px;border-top:1px solid #333;font-size:8px;\">
  Vehicle Registration No
  </td>
  <td colspan=\"4\" style=\"padding-left:10px; border-top:1px solid #333;font-size:8px; margin:0px;\">$VehicleNo</td>
  <td colspan=\"3\" style=\"padding-left:10px;border-left:1px solid #333; text-align:center;font-size:8px; margin:0px;\"><strong>E & O E,</strong></td>
  </tr>
  <tr>
  <td colspan=\"2\" style=\"padding-left:10px;border-top:1px solid #333;font-size:8px;\">
  LR/RR No & Date
  </td>
  <td colspan=\"4\" style=\"padding-left:10px; border-top:1px solid #333;font-size:8px; margin:0px;\">$LrRr <span style=\"padding-left:70px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$LrRrDt_format</span></td>
  <td colspan=\"3\" style=\"padding-left:10px;border-left:1px solid #333; text-align:center;font-size:8px; margin:0px;\"><strong>$ForName</strong></td>
  </tr>
  <tr>
  <td colspan=\"2\" style=\"padding-left:10px;border-top:1px solid #333;font-size:8px;\">
  Route Name
  </td>
  <td colspan=\"4\" style=\"padding-left:10px; border-top:1px solid #333;font-size:8px; margin:0px;\">$RouteCode <span style=\"padding-left:70px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$RouteDesc</span></td>
  <td colspan=\"3\" style=\"padding-left:10px;border-left:1px solid #333; text-align:center;\"></td>
  </tr>
  <tr>
  <td colspan=\"2\" style=\"padding-left:10px;border-top:1px solid #333;font-size:8px;\">
  Inco Terms
  </td>
  <td colspan=\"4\" style=\"padding-left:10px; border-top:1px solid #333;font-size:8px; margin:0px;\">$Inco1 <span style=\"padding-left:70px;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$Inco2</span></td>
  <td colspan=\"3\" style=\"padding-left:10px;border-left:1px solid #333; text-align:center;\"></td>
  </tr>
  <tr>
  <td colspan=\"6\" style=\"padding-left:10px;border-top:1px solid #333;\">
  <strong>Terms & Conditions</strong>
  <p style=\"font-size:8px; margin:0px;\">FOR TERMS & CONDITIONS SEE OVERLEAF</p>
  <p style=\"font-size:8px; margin:0px;\">Amount of TAX Subject to Reverse Charge: NO<br /></p>
  </td>
  <td colspan=\"3\" style=\"padding-left:10px;border-left:1px solid #333; text-align: center;font-size:8px; padding-top:10px;\" valign=\"bottom\"><p style=\"font-size:8px; margin:50px;\"><strong>Authorised Signatory</strong></p></td>
  </tr>
   <tr>
  <td colspan=\"9\" style=\"padding-left:10px;border-top:1px solid #333; font-size:8px;text-align:center;\">
  Guwahati Off: Mayur Garden, 2nd Floor, GS Road, Bhangarh, Guwahati Assam, 781005<br/>Kolkata Off: Century House, Star Cement Limited, p15/1, Taratala Road Kolkata, 700088<br/>
  SUBJECT TO $JuriState JURISDICTION<br />
  </td>
  </tr>
  </table>
  <p style=\"padding-left:10px; margin:0px; font-size:10px;text-align:center;\">Downloaded from Star Cement Customer Portal on ".date('d-m-Y')." at ".date('H:i:s')."</p>
</div>
</body></html>";
	
require_once('tcpdf/tcpdf.php');
class LongTableTCPDF extends TCPDF {
private $longTableHeader = '';
private $longTableFooter = '';

public function Header() {
$this->writeHTML($this->longTableHeader);
}

public function Footer() {
$this->writeHTML($this->longTableFooter);
}

public function setLongTableHeader($html) {
$this->longTableHeader = $html;
}

public function setLongTableFooter($html) {
$this->longTableFooter = $html;
}
}
//creditnotePDFfile($output);
//function creditnotePDFfile($output){
	$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "invoice_".$curr_date.".pdf";


		//define ('PDF_MARGIN_RIGHT', 4);
$pdf=new LongTableTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetPrintHeader(false);
		$pdf->SetPrintfooter(false);
		$pdf->SetTopMargin(0);
		//$pdf->SetLeftMargin(0);
//$pdf->SetFont('helvetica', '',5);
$pdf->AddPage('P',"A4");
$pdf->SetAutoPageBreak(false);
//$pdf->setLongTableFooter($footerTableHTML);
$pdf->writeHTML($output);
//Use 'D' for download
$_REQUEST['mode']='';
$_REQUEST['invoice_no']='';
ob_end_clean();
$tcpdf=$pdf->Output($the_file_name, 'D');

		
		//end 

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
  </form>
</div>
</div>
        </div>
    </section>
  

<script type="text/javascript">
	function invoiceprint(invoiceno)
	{
		//alert(invoiceno);
		document.getElementById('modeval').value='invoiceprint';
		document.getElementById('invoice_no').value=invoiceno;
		document.invoiceform.submit();
	}
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
//ob_end_flush();
include "web_footer.php";
mysql_close();
?>
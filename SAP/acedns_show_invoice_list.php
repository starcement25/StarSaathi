<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$employee_master = "employee_master";
$customer_master = "customer_master";
$branch_master = "branch_master";
$customer_invoice_table = "customer_invoice_table";

try {
	// $sql1="select distinct `customer_code` from $customer_invoice_table";
	// // echo $sql1;
	// $query1=mysql_query($sql1);

	// while($result=mysql_fetch_array($query1)){
	$sswa_selected_customer_code = $_REQUEST["customer_code"] ? addslashes(trim($_REQUEST["customer_code"])) : "";

	$sql3 = "select `dns_customer_code`,`customer_id`,`customer_name` from $customer_master where `customer_code`='$sswa_selected_customer_code'";
	$res3 = mysql_query($sql3);
	$totres3 = mysql_num_rows($res3);
	if ($totres3 > 0) {
		$row3 = mysql_fetch_assoc($res3);
		$the_dealer_id = trim($row3["dns_customer_code"]);
		$the_customer_id = trim($row3["customer_id"]);
	} else {
		$the_customer_id = "";
	}


	// echo $sswa_selected_customer_code;
	$ledger_total_balance = 0;
	$ledger_link = "";

	$from_dt = $_REQUEST["from_date"] ? addslashes(trim($_REQUEST["from_date"])) : "";
	// echo $from_dt;
	$to_dt = $_REQUEST["to_date"] ? addslashes(trim($_REQUEST["to_date"])) : "";
	// echo $to_dt;
	$the_start_date_invoice = date('Ymd', strtotime($from_dt));
	// echo $the_start_date_invoice;
	$the_end_date_invoice = date('Ymd', strtotime($to_dt));
	// echo $the_end_date_invoice;
	//$the_filter2 = '&$filter=(Kunnr eq \''.$the_customer_id.'\' and DocDate eq datetime\''.$the_date_time.'\')';
	// echo $the_customer_id;

	$the_filter = '&$filter=(CustCo eq \'' . $the_customer_id . '\' and ( InvoiceDt ge \'' . $the_start_date_invoice . '\' and InvoiceDt le \'' . $the_end_date_invoice . '\') )&sap-client=900';
	$starfiori_port_no=$GLOBALS['starfiori_port_no'];
	$url_ck1 = 'https://starfiori.starcement.co.in:'.$starfiori_port_no.'/sap/opu/odata/sap/ZSD_CUSTOMER_BULK_INVOICE_SRV/ZSD_CUSTOMER_INVOICESet?$format=json' . str_replace(" ", "%20", $the_filter);
	// echo $url_ck1."<br/>";
	$body_for_mcode10 = get_data_from_cserver($url_ck1);
	// echo $body_for_mcode10;
	if (isJsonCk($body_for_mcode10)) {
		$json_decoded21 = json_decode($body_for_mcode10, true);
		// echo "count of json_decoded21= ".count($json_decoded21);
		if (count($json_decoded21) > 0) {
			if (array_key_exists("d", $json_decoded21)) {
				$app_results_arr = $json_decoded21["d"]["results"];
				//print_r($app_results_arr);
				// echo "count of app_results_arr= ".count($app_results_arr);
				if (count($app_results_arr) > 0) {
					foreach ($app_results_arr as $app_results_aval) {
						$InvoiceNo = $app_results_aval["InvoiceNo"];
						$InvoiceDt = $app_results_aval["InvoiceDt"];
						$DoNo = $app_results_aval["DoNo"];
						$CustPo = $app_results_aval["CustPo"];
						$InvoiceDt_format = date("Y-m-d", strtotime($InvoiceDt));
						$InvoiceDt = $app_results_aval["InvoiceDt"];
						// $InvoiceDt_format1=date("Y-m-d",strtotime($InvoiceDt));
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

						$customer_invoice_array[] =	array("InvoiceNo" => $InvoiceNo, "InvoiceDt" => $InvoiceDt_format, "ChallanNo" => $ChallanNo, "WorksOff" => $WorksOff, "Description" => $Description, "Qty" => $Qty, "ConDest" => $ConDest, "ConName" => $ConName, "ConAdd" => $ConAdd, "VehicleNo" => $VehicleNo, "CompName1" => $CompName1, "CompName2" => $CompName2, "Plant" => $Plant, "CinNo" => $CinNo, "PlantAdd" => $PlantAdd, "EwayBill" => $EwayBill, "Irn" => $Irn, "VendLocation" => $VendLocation, "VendAddress" => $VendAddress, "VendGstin" => $VendGstin, "VendPin" => $VendPin, "VendStCode" => $VendStCode, "VendState" => $VendState, "DoNo" => $DoNo, "DoDate" => $DoDate, "CustPo" => $CustPo, "CustPoDt" => $CustPoDt, "OurRefNo" => $OurRefNo, "ChallanDt" => $ChallanDt, "ShipmentNo" => $ShipmentNo, "ShipmentDt" => $ShipmentDt, "CustName" => $CustName, "CustCo" => $CustCo, "CustAdd" => $CustAdd, "CustPin" => $CustPin, "CustGstin" => $CustGstin, "CustSt" => $CustSt, "CustStCode" => $CustStCode, "ConCo" => $ConCo, "WeekNo" => $WeekNo, "ConPin" => $ConPin, "ConGstin" => $ConGstin, "ConStCode" => $ConStCode, "ConSt" => $ConSt, "Freight" => $Freight, "Cgst" => $Cgst, "CgstAmt" => $CgstAmt, "Sgst" => $Sgst, "SgstAmt" => $SgstAmt, "Igst" => $Igst, "IgstAmt" => $IgstAmt, "Tcs" => $Tcs, "TcsAmt" => $TcsAmt, "Cess" => $Cess, "CessAmt" => $CessAmt, "ROff" => $ROff, "Total" => $Total, "TotInWords" => $TotInWords, "TransMode" => $TransMode, "TransCode" => $TransCode, "Transporter" => $Transporter, "LrRr" => $LrRr, "LrRrDt" => $LrRrDt, "Inco1" => $Inco1, "Inco2" => $Inco2, "RouteCode" => $RouteCode, "RouteDesc" => $RouteDesc, "ForName" => $ForName, "OffAdd1" => $OffAdd1, "OffAdd2" => $OffAdd2, "Juri" => $Juri, "CurrKey" => $CurrKey, "UomHead" => $UomHead, "JuriState" => $JuriState, "Vkgrp" => $Vkgrp, "SlNo" => $SlNo, "Description" => $Description, "Hsn" => $Hsn, "PackageDesc" => $PackageDesc, "TotPackage" => $TotPackage, "Uom" => $Uom, "Rate" => $Rate, "Tax" => $Tax, "FreightAmt" => $FreightAmt, "QrCode1" => $QrCode1);
					}
					usort($customer_invoice_array, 'sort_by_date');
					$invoice_data = [];
					foreach ($customer_invoice_array as $customer_invoice_data_val) {
						$InvoiceNo = $customer_invoice_data_val["InvoiceNo"];
						$InvoiceDt = $customer_invoice_data_val["InvoiceDt"];

						$InvoiceDt_format = date("d-m-Y", strtotime($InvoiceDt));
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
						$InvoiceDt_format = date("d-m-Y", strtotime($InvoiceDt));
						$url = BASE_URL . "customer_invoice_download.php?from_date=" . urlencode($from_dt) . "&to_date=" . urlencode($to_dt) . "&customer_code=" . urlencode($sswa_selected_customer_code) . "&invoice_no=" . urlencode($InvoiceNo);
						
						$customer_data = array(
							"from_date" => $from_dt,
							"to_date" => $to_dt,
							"invoice_no" => $InvoiceNo,
							"invoice_date" => $InvoiceDt,
							"delivery_no" => $ChallanNo,
							"sale_order_no" => $DoNo,
							"app_order_no" => $CustPo,
							"product_name" => $Description,
							"invoice_qty" => $Qty,
							"destination" => $ConDest,
							"truck_no" => $VehicleNo,
							"dwd_url" => $url
						);

						// add customer data to invoice data array
						array_push($invoice_data, $customer_data);
					}
					if (!empty($invoice_data)) {

						$res_data = array("process_status"=>"YES","process_message"=>"Invoice details sent.","data"=>$invoice_data);
						// $res_data = array("process_status" => "YES", "process_message" => "Invoice details sent.");
					} else {

						$res_data = array("process_status" => "NO", "process_message" => "Failed to retrieve Invoice data.");
					}

					echo json_encode($res_data);
				}
			}
		}
	}


	mysql_close();
} catch (Exception $e) {
	// Handle exceptions or errors
	$error_message = $e->getMessage();

	// You can log the error message for debugging purposes
	error_log("Error: " . $error_message);

	// Return an error response or handle the error gracefully
	$res_data = array(
		"process_status" => "NO",
		"process_message" => "An error occurred: " . $error_message
	);
	echo json_encode($res_data);
}

<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

include "star_connection.php";

$dealer_sap_code = $_REQUEST["p_KUNNR"] ? addslashes(trim($_REQUEST["p_KUNNR"])) : "";
$year = $_REQUEST["p_YR"] ? addslashes(trim($_REQUEST["p_YR"])) : "";
$month = $_REQUEST["p_MNTH"] ? addslashes(trim($_REQUEST["p_MNTH"])) : "";
$username = 'MOBILEAPP';
$password = 'Star@#2021';

// $the_filter = '(p_KUNNR eq \'' . $dealer_sap_code . '\' and ( p_YR eq \'' . $year . '\' and p_MNTH eq \'' . $month . '\') )';

$base_url = "https://starfiori.starcementco.in:44301/sap/opu/odata/sap/ZOVW_CUSTTGTACH_CDS/ZOVW_CUSTTGTACH(p_KUNNR='$dealer_sap_code',p_YR='$year',p_MNTH='$month')/Set?$format=json&sap-client=900";

$context = stream_context_create([
    'http' => [
        'header' => "Authorization: Basic " . base64_encode("$username:$password")
    ]
]);

$response = @file_get_contents($base_url, false, $context);

if ($response !== false) {
    // Handle the response here
    echo $response;
} else {
    // Handle the case where the request failed
    $error = error_get_last();
    echo "Request failed with error: " . $error['message'];
}

$body_for_mcode10 = get_data_from_cserver($base_url);
// echo $body_for_mcode10;
if (isJsonCk($body_for_mcode10)) {
		$json_decoded21 = json_decode($body_for_mcode10, true);
		if (count($json_decoded21) > 0) {
			if (array_key_exists("d", $json_decoded21)) {
				$app_results_arr = $json_decoded21["d"]["results"];
				if (count($app_results_arr) > 0) {
					foreach ($app_results_arr as $app_results_aval) {
						$customercode = $app_results_aval["customercode"];
						$companycode = $app_results_aval["companycode"];
                        $branch_code = $app_results_aval["vkgrp"];
						$branch_name = $app_results_aval["sales_group"];
						$region = $app_results_aval["region"];
						$zone = $app_results_aval["zzone"];
						$subzone = $app_results_aval["subzone"];
						$dealer_name = $app_results_aval["customer"];
						$year = $app_results_aval["yr"];
						$month = $app_results_aval["mnth"];
                        $antirust_target = $app_results_aval["tgt_antirust"];
						$antirust_achv = $app_results_aval["ach_antirust"];
						$ppc_manipur_tgt = $app_results_aval["tgt_ppc_manipur"];
						$ppc_manipur_achv = $app_results_aval["ach_ppc_manipur"];
						$opc43_trade_target = $app_results_aval["tgt_opc43_trade"];
						$opc43_trade_achv = $app_results_aval["ach_opc43_trade"];
						$opc53_trade_target = $app_results_aval["tgt_opc53_trade"];
						$opc53_trade_achv = $app_results_aval["ach_opc53_trade"];
						$pcc_adstar_target = $app_results_aval["tgt_pcc_adstar"];
						$pcc_adstar_achv = $app_results_aval["ach_pcc_adstar"];
						$pcc_trade_target = $app_results_aval["tgt_pcc_trade"];
						$pcc_trade_achv = $app_results_aval["ach_pcc_trade"];
						$ppc_adstar_target = $app_results_aval["tgt_ppc_adstar"];
						$ppc_adstar_achv = $app_results_aval["ach_ppc_adstar"];
						$wthr_shield_target = $app_results_aval["tgt_wthr_shield"];
						$wthr_shield_achv = $app_results_aval["ach_wthr_shield"];
						$tottgtqty = $app_results_aval["tottgtqty"];
						$totachqty = $app_results_aval["totachqty"];
						
						$dealer_details_array[] =	array("customercode" => $customercode, "companycode" => $companycode, "zzone" => $zone, "subzone" => $subzone, "customer" => $dealer_name, "yr" => $year, "mnth" => $month, "tgt_antirust" => $antirust_target, "ach_antirust" => $antirust_achv, "tgt_ppc_manipur" => $ppc_manipur_tgt, "ach_ppc_manipur" => $ppc_manipur_achv, "tgt_opc43_trade" => $opc43_trade_target, "ach_opc43_trade" => $opc43_trade_achv, "tgt_opc53_trade" => $opc53_trade_target, "ach_opc53_trade" => $opc53_trade_achv, "pcc_adstar_target" => $tgt_pcc_adstar, "pcc_adstar_achv" => $ach_pcc_adstar, "tgt_pcc_trade" => $pcc_trade_target, "ach_pcc_trade" => $pcc_trade_achv, "tgt_ppc_adstar" => $ppc_adstar_target, "ach_ppc_adstar" => $ppc_adstar_achv, "tgt_wthr_shield" => $wthr_shield_target, "ach_wthr_shield" => $wthr_shield_achv, "tottgtqty" => $tottgtqty, "totachqty" => $totachqty);
					}
					$dealer_data = [];
					// usort($dealer_details_array, 'sort_by_date');
					
					// foreach ($dealer_details_array as $dealer_details_data_val) {
					// 	$customercode = $dealer_details_data_val["customercode"];
					// 	$companycode = $dealer_details_data_val["companycode"];
                    //     $branch_code = $dealer_details_data_val["vkgrp"];
					// 	$branch_name = $dealer_details_data_val["sales_group"];
					// 	$region = $dealer_details_data_val["region"];
					// 	$zone = $dealer_details_data_val["zzone"];
					// 	$subzone = $dealer_details_data_val["subzone"];
					// 	$dealer_name = $dealer_details_data_val["customer"];
					// 	$year = $dealer_details_data_val["yr"];
					// 	$month = $dealer_details_data_val["mnth"];
                    //     $antirust_target = $dealer_details_data_val["tgt_antirust"];
					// 	$antirust_achv = $dealer_details_data_val["ach_antirust"];
					// 	$ppc_manipur_tgt = $dealer_details_data_val["tgt_ppc_manipur"];
					// 	$ppc_manipur_achv = $dealer_details_data_val["ach_ppc_manipur"];
					// 	$opc43_trade_target = $dealer_details_data_val["tgt_opc43_trade"];
					// 	$opc43_trade_achv = $dealer_details_data_val["ach_opc43_trade"];
					// 	$opc53_trade_target = $dealer_details_data_val["tgt_opc53_trade"];
					// 	$opc53_trade_achv = $dealer_details_data_val["ach_opc53_trade"];
					// 	$pcc_adstar_target = $dealer_details_data_val["tgt_pcc_adstar"];
					// 	$pcc_adstar_achv = $dealer_details_data_val["ach_pcc_adstar"];
					// 	$pcc_trade_target = $dealer_details_data_val["tgt_pcc_trade"];
					// 	$pcc_trade_achv = $dealer_details_data_val["ach_pcc_trade"];
					// 	$ppc_adstar_target = $dealer_details_data_val["tgt_ppc_adstar"];
					// 	$ppc_adstar_achv = $dealer_details_data_val["ach_ppc_adstar"];
					// 	$wthr_shield_target = $dealer_details_data_val["tgt_wthr_shield"];
					// 	$wthr_shield_achv = $dealer_details_data_val["ach_wthr_shield"];
					// 	$tottgtqty = $dealer_details_data_val["tottgtqty"];
					// 	$totachqty = $dealer_details_data_val["totachqty"];
						
						$single_dealer_data = array(
							"customercode" => $customercode,
                            "companycode" => $companycode,
                            "zzone" => $zone,
                            "subzone" => $subzone,
                            "customer" => $dealer_name,
                            "yr" => $year,
                            "mnth" => $month,
                            "tgt_antirust" => $antirust_target,
                            "ach_antirust" => $antirust_achv,
                            "tgt_ppc_manipur" => $ppc_manipur_tgt,
                            "ach_ppc_manipur" => $ppc_manipur_achv,
                            "tgt_opc43_trade" => $opc43_trade_target,
                            "ach_opc43_trade" => $opc43_trade_achv,
                            "tgt_opc53_trade" => $opc53_trade_target,
                            "ach_opc53_trade" => $opc53_trade_achv,
                            "pcc_adstar_target" => $tgt_pcc_adstar,
                            "pcc_adstar_achv" => $ach_pcc_adstar,
                            "tgt_pcc_trade" => $pcc_trade_target,
                            "ach_pcc_trade" => $pcc_trade_achv,
                            "tgt_ppc_adstar" => $ppc_adstar_target,
                            "ach_ppc_adstar" => $ppc_adstar_achv,
                            "tgt_wthr_shield" => $wthr_shield_target,
                            "ach_wthr_shield" => $wthr_shield_achv,
                            "tottgtqty" => $tottgtqty,
                            "totachqty" => $totachqty
						);

						// add customer data to invoice data array
						array_push($dealer_data, $single_dealer_data);

                        if (!empty($dealer_data)) {

                            $res_data = array("process_status"=>"YES","process_message"=>"Dealer data passed successfully.","data"=>$dealer_data);
                        }
                        else{	
            $res_data = array("process_status"=>"NO","process_message"=>"Sorry! No data passed.");
        }
    }
}
}
}
echo json_encode($res_data);

mysql_close();

?>

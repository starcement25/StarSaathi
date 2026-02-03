<?php
error_reporting(E_STRICT);
ini_set('memory_limit', '99999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include "star_connection.php";



$customer_master = "customer_master";
$product_master = "product_master";
$cron_update_table = "cron_update_table";
$curr_date_time = date("Y-m-d H:i:s");
$curr_date = date("Y-m-d");
//$curr_date='2023-03-10';
//$prev_date = date('Y-m-d',strtotime("-12 days"));
$prev_date_time = date('Y-m-d H:i:s', strtotime('-1 hour'));
$the_date = date("Y-m-d", strtotime($prev_date_time));
$the_hour = date("H", strtotime($prev_date_time));
$the_minute = date("i", strtotime($prev_date_time));
$res_data = array();
$process_message = "";
$in_cnt = 0;
$upd_cnt = 0;

$customer_code = $_REQUEST['customer_code'];
$year = $_REQUEST['year'];
$month = $_REQUEST['month'];


$cust_type = "SELECT cust_type FROM `customer_master` WHERE customer_id='$customer_code'";
$result = mysql_query($cust_type);
$row = mysql_fetch_assoc($result);
// echo $row['cust_type'];die;
if ($row['cust_type'] == 'Dealer') {



	$url_ck1 = 'https://starfiori.starcement.co.in:' . SRARFIORI_PORT_NO . '/sap/opu/odata/sap/ZOVW_CUSTTGTACH_CDS/ZOVW_CUSTTGTACH(p_kunnr=\'' . $customer_code . '\',p_yr=\'' . $year . '\',p_mnth=\'' . $month . '\')/Set?$format=json&sap-client=900';
	// echo "url: ".$url_ck1;
	$body_for_mcode1 = get_data_from_cserver($url_ck1);
	//echo "data: ".$body_for_mcode1;
	if (isJsonCk($body_for_mcode1)) {
		$json_decoded = json_decode($body_for_mcode1, true);
		if (count($json_decoded) > 0) {
			if (array_key_exists("d", $json_decoded)) {
				$app_results_arr = $json_decoded["d"]["results"];
				if (count($app_results_arr) > 0) {
					$target_ach_data = array();
					foreach ($app_results_arr as $app_results_aval) {

						//$Mandt = $app_results_aval["Mandt"] ? trim($app_results_aval["Mandt"]) : "";
						$bukrs = $app_results_aval["bukrs"] ? trim($app_results_aval["bukrs"]) : "";
						$customercode = $app_results_aval["customercode"] ? trim($app_results_aval["customercode"]) : "";
						$itemcode = $app_results_aval["itemcode"] ? trim($app_results_aval["itemcode"]) : "";

						$yr = $app_results_aval["yr"] ? trim($app_results_aval["yr"]) : "";
						$MNTH = $app_results_aval["MNTH"] ? trim($app_results_aval["MNTH"]) : "";
						$TGTQTY = $app_results_aval["TGTQTY"] ? trim($app_results_aval["TGTQTY"]) : "";
						$ACHQTY = $app_results_aval["ACHQTY"] ? trim($app_results_aval["ACHQTY"]) : "";

						$sqlproduct = "SELECT prod_desc FROM $product_master WHERE  dns_prod_code='" . $itemcode . "'";
						$rspopproduct = mysql_query($sqlproduct);
						$rowproduct = mysql_fetch_array($rspopproduct);
						$countpopproduct = mysql_num_rows($rspopproduct);
						if ($countpopproduct > 0)  $prod_desc = $rowproduct['prod_desc'];
						else $prod_desc = '';
						//$countpopproduct=mysql_num_rows($rspopproduct);

						// $bukrs = '1010' => $itemcode+(SCL)
						// $bukrs = '1017' => itemcode+(SCNEL)
						$suffix_itemcode = '';
						if ($bukrs == '1010') $suffix_itemcode = ' (SCL)';
						if ($bukrs == '1017') $suffix_itemcode = ' (SCNEL)';
						$itemcode = $itemcode . $suffix_itemcode;

						$item_type = "";
						if ($bukrs == "1010") $item_type = "SCL";
						if ($bukrs == "1017") $item_type = "SCNEL";

						$target_ach_data[] = array("bukrs" => $bukrs, "customercode" => $customercode, "itemcode" => $itemcode, "yr" => $yr, "MNTH" => $MNTH, "TGTQTY" => $TGTQTY, "ACHQTY" => $ACHQTY, "itemname" => $prod_desc, "item_type" => $item_type);
					}
					if ($target_ach_data != null && count($target_ach_data) > 0) {
						// sort by itemcode
						usort($target_ach_data, function ($a, $b) {
							return $a['itemcode'] > $b['itemcode'];
							// echo $a['itemcode'];
							// echo $b['itemcode'];
						});
					}
					$res_data = array("process_status" => "YES", "process_message" => "Success.", "target_ach_data" => $target_ach_data);
				} else {
					$res_data = array("process_status" => "NO", "process_message" => "No Records Found");
				}
			} else {
				$res_data = array("process_status" => "NO", "process_message" => "No Records Found");
			}
		} else {
			$res_data = array("process_status" => "NO", "process_message" => "No Records Found");
		}
	} else {
		$res_data = array("process_status" => "NO", "process_message" => "No Records Found");
	}
	echo json_encode($res_data);
} else {

	//"bukrs": "1010",
	// "itemcode": "14000087 (SCL)",
	// "item_type": "SCL"

	// "customercode": "1000001497",
	// "yr": "2025",
	// "MNTH": "08",
	// "TGTQTY": "0.000",
	// "ACHQTY": "0.000",
	// "itemname": "STAR CEMENT PPC TRADE",

	$sub_dealer_sap_code = $customer_code;
// 	$sql = "
// SELECT 
//     p.product_name,
//     '$sub_dealer_sap_code' AS sub_dealer_sap_code,
//     COALESCE(SUM(l.allocated_qty), 0) AS total_allocated_qty
// FROM (
//     SELECT DISTINCT product_name
//     FROM lifting_final_allocation_data 
//     WHERE product_name IS NOT NULL AND product_name <> ''
// ) p
// LEFT JOIN lifting_final_allocation_data l
//     ON l.product_name = p.product_name
//    AND YEAR(l.allocation_datetime) = '$year'
//    AND MONTH(l.allocation_datetime) = '$month'
//    AND l.sub_dealer_sap_code = '" . mysql_real_escape_string($sub_dealer_sap_code) . "'
// GROUP BY p.product_name
// ORDER BY p.product_name";
$monthNum = isset($_REQUEST['month']) ? intval($_REQUEST['month']) : date("n"); // 1-12
$month = date("F", mktime(0, 0, 0, $monthNum, 1)); // "January", "February", etc.
//  	$sql = "
// SELECT 
//     p.product_name,
//     '$sub_dealer_sap_code' AS sub_dealer_sap_code,
//     COALESCE(SUM(l.allocated_qty), 0) AS total_allocated_qty
// FROM (
//     SELECT DISTINCT product_name
//     FROM lifting_final_allocation_data 
//     WHERE product_name IS NOT NULL AND product_name <> ''
// ) p
// LEFT JOIN lifting_final_allocation_data l
//     ON l.product_name = p.product_name
//    AND l.sub_dealer_sap_code = '" . mysql_real_escape_string($sub_dealer_sap_code) . "'
//    AND file_upload_id IN(SELECT `id` FROM `lifting_final_file_upload_csv` WHERE `year`='$year' and `month` ='$month')
// GROUP BY p.product_name
// ORDER BY p.product_name";
$sql = "
SELECT 
    p.product_name,
    '$sub_dealer_sap_code' AS sub_dealer_sap_code,
    COALESCE(SUM(l.allocated_qty), 0) AS total_allocated_qty
FROM (
    SELECT DISTINCT product_name
    FROM lifting_final_allocation_data 
    WHERE product_name IS NOT NULL 
      AND product_name <> ''
) p
LEFT JOIN lifting_final_allocation_data l
    ON l.product_name = p.product_name
   AND l.sub_dealer_sap_code = '" . mysql_real_escape_string($sub_dealer_sap_code) . "'
   AND file_upload_id IN (
        SELECT id 
        FROM lifting_final_file_upload_csv 
        WHERE year = '$year' 
          AND month = '$month'
    )
GROUP BY p.product_name
HAVING total_allocated_qty > 0
ORDER BY p.product_name
";
	//echo"<pre>";print_r($sql);die;
	$result = mysql_query($sql);

	$data = [];
	if ($result && mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_assoc($result)) {
			$data[] = [
				"bukrs"=>'',
				"itemcode"=>'',
				"item_type"=>'',
				"customercode"=>$row['sub_dealer_sap_code'],
				"yr"=>$year,
				"MNTH"=>$month,
				"TGTQTY"=>'0.00',
				"ACHQTY"=>$row['total_allocated_qty'],
				"itemname"=>$row['product_name']
			];
		}
		echo json_encode([
			"process_status" => "YES",
			"month"  => $month,
			"year"   => $year,
			'process_message'=>'Success.',
			"target_ach_data"   => $data
		]);
	} else {
		echo json_encode([
			"process_status" => "NO",
			"process_message" => "No records found for the given filters"
		]);
	}
}

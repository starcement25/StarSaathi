<?php
ini_set('memory_limit', '999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
include "star_connection.php";



$qry = "SELECT zone AS 'Zone', branch_code AS 'Branch',customer_name AS 'Name of the firm',dns_customer_code AS 'SFA Code', customer_code AS 'SAP ID', credit_limit AS 'Credit limit amount',customer_id FROM customer_master WHERE cust_type LIKE 'Dealer' OR cust_type LIKE 'RSSD' limit 10";


	$sql = mysql_query($qry);
	$columns_total = mysql_num_fields($sql);

	// Get The Field Name

	for ($i = 0; $i < $columns_total; $i++) {
		$heading = mysql_field_name($sql, $i);
		$output .= '"' . $heading . '",';
	}
	$output .= "Security deposit amount";
	$output .= "\n";
	// Get Records from the table

	while ($row = mysql_fetch_array($sql)) {
		for ($i = 0; $i < $columns_total; $i++) {
			$output .= '"' . $row["$i"] . '",';
		}
		
		
		
	    	$customer_code = $row["customer_id"];
	    
		    $from_date = "2022-07-31";
            $to_date = "2024-10-21";
		    
		    $the_start_date_time = $from_date . "T00:00:00";
$the_end_date_time = $to_date . "T00:00:00";

$url_ck2 = 'https://starfiori.starcement.co.in:44300/sap/opu/odata/sap/ZOVW_LEDG_SECDEP_CDS/ZOVW_LEDG_SECDEP(p_Code=\'' . $customer_code . '\',p_Frm=datetime\'' . $the_start_date_time . '\',p_To=datetime\'' . $the_end_date_time . '\')/Set?$format=json&sap-client=900';

$body_for_mcode10 = get_data_from_cserver($url_ck2);
//echo $body_for_mcode10;exit();
if (isJsonCk($body_for_mcode10)) {
	$json_decoded21 = json_decode($body_for_mcode10, true);
	if (count($json_decoded21) > 0) {
		if (array_key_exists("d", $json_decoded21)) {
			$app_results_arr = $json_decoded21["d"]["results"];
			//print_r($app_results_arr);
			if (count($app_results_arr) > 0) {
				foreach ($app_results_arr as $app_results_aval) {
					$billno  = $app_results_aval["billno"];
					$billdt = $app_results_aval["billdt"];
					$amtdr  = $app_results_aval["amtdr"];
					$amtcr  = $app_results_aval["amtcr"];
					$narration = $app_results_aval["narration"];
					$tdsamt = $app_results_aval["tdsamt"];

					$total_CrAmount = $total_CrAmount + $amtcr;
					$total_DrAmount = $total_DrAmount + $amtdr;
				}
				$balance = $total_CrAmount - $total_DrAmount;
			} else {
				$total_CrAmount = 0.00;
				$total_DrAmount = 0.00;
				$balance = $total_CrAmount - $total_DrAmount;
			}
		}
	}
}
		
	    //echo "  ".$balance;exit();
	    $output .= '"' . $balance. '",';
		
		$output .= "\n";
	    
	    
	    
	}
		
		
	

	// Download the file

	$filename = "data_";
    header('Content-type: application/csv');
    header('Content-Disposition: attachment; filename='.$filename);
    header('Pragma: no-cache');    
    header('Expires: 0');
	echo $output;
	exit;


mysql_close();
?>
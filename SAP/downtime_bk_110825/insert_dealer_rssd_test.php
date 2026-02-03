<?php
ini_set('memory_limit', '999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
include "star_connection.php";
session_start();
$_SESSION['attnum']++;
$tttttt=13630;
$limit = 100;
//$output = "";
$page = 1;

 $start_from = (($page-1)*$limit);
 //get_data_csv($limit,$start_from);
 get_data_update($limit,$start_from);
 


function isJsonCk($str)

{

    $json = json_decode($str);

    return $json && $str != $json;

}

function get_data_from_cserver($url_ck)

{

    $useragent = $_SERVER['HTTP_USER_AGENT'];

    $username = "STARSAATHI";

    $password = "Srikrishna@93933";

    $ch_sheader = curl_init();

    curl_setopt($ch_sheader, CURLOPT_URL, $url_ck);

    curl_setopt($ch_sheader, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch_sheader, CURLOPT_SSL_VERIFYHOST, false);

    curl_setopt($ch_sheader, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch_sheader, CURLOPT_USERPWD, "$username:$password");

    curl_setopt($ch_sheader, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

    curl_setopt($ch_sheader, CURLOPT_USERAGENT, $useragent);

    $body_for_mcode = curl_exec($ch_sheader);

    if (curl_errno($ch_sheader)) {

        echo 'Curl error: ' . curl_error($ch_sheader);

    }

    $info = curl_getinfo($ch_sheader);

    // print_r($info);

    curl_close($ch_sheader);

    return $body_for_mcode;

}



function get_data_csv($limit,$start_from)

{

$qry = "SELECT zone, branch_code,customer_name,dns_customer_code, customer_code, credit_limit,customer_id FROM customer_master WHERE acedns = 'Y' AND cust_type LIKE 'Dealer' OR cust_type LIKE 'RSSD'";

//echo $qry;exit();
	$sql = mysql_query($qry);

	while ($row = mysql_fetch_assoc($sql)) {
		
	    	$customer_code = $row["customer_id"];
	    
	    
	    $qry1 = "INSERT INTO `dealer_rssd_test`(`zone`, `branch_code`, `customer_name`, `dns_customer_code`, `customer_code`, `credit_limit`, `customer_id`, `security_deposit_amount`) VALUES ('".$row["zone"]."','".$row["branch_code"]."','".$row["customer_name"]."','".$row["dns_customer_code"]."','".$row["customer_code"]."','".$row["credit_limit"]."','".$row["customer_id"]."','')";
//echo $qry;
	    $sql1 = mysql_query($qry1);
   
	    
	}
		
echo "done_insert";
}




function get_data_update1($limit,$start_from)

{

$qry = "SELECT zone, branch_code,customer_name,dns_customer_code, customer_code, credit_limit,customer_id FROM dealer_rssd_test";

//echo $qry;exit();
	$sql = mysql_query($qry);

	while ($row = mysql_fetch_assoc($sql)) {
		
	    	$customer_code = $row["customer_id"];
	    
	    
	    $qry1 = "INSERT INTO `dealer_rssd_test`(`zone`, `branch_code`, `customer_name`, `dns_customer_code`, `customer_code`, `credit_limit`, `customer_id`, `security_deposit_amount`) VALUES ('".$row["zone"]."','".$row["branch_code"]."','".$row["customer_name"]."','".$row["dns_customer_code"]."','".$row["customer_code"]."','".$row["credit_limit"]."','".$row["customer_id"]."','')";
//echo $qry;
	    $sql1 = mysql_query($qry1);
   
	    
	}
		
echo "done_insert";
}


function get_data_update($limit,$start_from)

{

$qry = "SELECT zone, branch_code,customer_name,dns_customer_code, customer_code, credit_limit,customer_id FROM dealer_rssd_test";

//echo $qry;exit();
	$sql = mysql_query($qry);
	
	
	while ($row = mysql_fetch_assoc($sql)) {
	
		
	    	$customer_code = $row["customer_id"];
	    
		    $from_date = "2022-07-31";
            $to_date = "2024-10-21";
		    
		    $the_start_date_time = $from_date . "T00:00:00";
$the_end_date_time = $to_date . "T00:00:00";
$balance = 0;
$total_CrAmount = 0;
$total_DrAmount = 0;
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
		
	    $qry1="UPDATE `dealer_rssd_test` SET `security_deposit_amount`='".$balance."' WHERE `customer_id`='".$customer_code."'";
	    $sql11 = mysql_query($qry1);
	    
	    
	}
		
echo "done_update";
}




mysql_close();
?>





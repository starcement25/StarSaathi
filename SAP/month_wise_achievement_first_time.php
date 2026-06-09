<?php
set_time_limit(0);
ini_set('memory_limit', '-1');
date_default_timezone_set('Asia/Kolkata');

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("star_connection.php"); // DB connection

/* ================= HELPER FUNCTIONS ================= */
/*
function get_data_from_cserver($url) {
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_SSL_VERIFYPEER => false
    ));
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "CURL Error: " . curl_error($ch) . "\n";
    }

    curl_close($ch);
    return $response;
}

function isJsonCk($string) {
    json_decode($string);
    return (json_last_error() === JSON_ERROR_NONE);
}*/

/* ================= MAIN CRON ================= */

$current_year  = date('Y');
$current_month = date('n'); // 1-12

echo "Cron Start: " . date('Y-m-d H:i:s') . "\n";

/* ===== GET CUSTOMERS ===== */
$sql_cust = "SELECT customer_id, customer_code 
             FROM customer_master 
             WHERE customer_id != '' 
             AND cust_type='Dealer' 
             AND acedns='Y' 
             AND customer_id='1000000281'";

$res_cust = mysql_query($sql_cust);

if (!$res_cust) {
    die("Customer query failed");
}

while ($cust = mysql_fetch_assoc($res_cust)) {

    $customer_id   = $cust['customer_id'];
    $customer_code = $cust['customer_code'];

    echo "Processing Customer: $customer_code\n";

    /* ===== LOOP MONTHS ===== */
    for ($m = 1; $m <= 12; $m++) {

        // 🔹 Dynamic Year Logic
        if ($m <= $current_month) {
            $year = $current_year;
            $type = 'current';
        } else {
            $year = $current_year - 1;
            $type = 'previous';
        }

        $month = str_pad($m, 2, '0', STR_PAD_LEFT);

        /* ===== API URL ===== */
        $url = 'https://starfiori.starcement.co.in:' . SRARFIORI_PORT_NO .
            '/sap/opu/odata/sap/ZOVW_CUSTTGTACH_CDS/ZOVW_CUSTTGTACH(p_kunnr=\'' . $customer_id .
            '\',p_yr=\'' . $year . '\',p_mnth=\'' . $month . '\')/Set?$format=json&sap-client=900';

        echo $url . "\n";

        $response = get_data_from_cserver($url);

        if (!isJsonCk($response)) {
            echo "Invalid JSON for $customer_code - $year-$month\n";
            continue;
        }

        $json = json_decode($response, true);

        if (!isset($json['d']['results']) || count($json['d']['results']) == 0) {
            echo "No Data for $customer_code - $year-$month\n";
            continue;
        }

        /* ===== SUM MULTIPLE TOTAL QTY ===== */
        $total_target = 0;
        $total_ach    = 0;

        foreach ($json['d']['results'] as $row) {

            if (trim($row['itemcode']) == 'Total Qty') {
                $total_target += floatval($row['TGTQTY']);
                $total_ach    += floatval($row['ACHQTY']);
            }
        }

        /* ===== INSERT AFTER SUM ===== */
        if ($total_target > 0 || $total_ach > 0) {

            $sql_insert = "
            INSERT INTO month_wise_achievement_new 
            (customer_id, customer_code, year, month, type, target_qty, achievement_qty, created_at, updated_at)
            VALUES 
            ('$customer_id', '$customer_code', '$year', '$m', '$type', '$total_target', '$total_ach', NOW(), NOW())

            ON DUPLICATE KEY UPDATE
                target_qty = '$total_target',
                achievement_qty = '$total_ach',
                updated_at = NOW()
            ";

            $res_insert = mysql_query($sql_insert);

            if (!$res_insert) {
                echo "DB Error: " . mysql_error() . "\n";
            } else {
                echo "Updated (Summed): $customer_code - $year-$month\n";
            }

        } else {
            echo "No Total Qty found for $customer_code - $year-$month\n";
        }
    }
}

echo "Cron End: " . date('Y-m-d H:i:s') . "\n";
?>
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
$current_month = date('n'); // 1–12
$month_padded  = str_pad($current_month, 2, '0', STR_PAD_LEFT);

echo "Cron Start: " . date('Y-m-d H:i:s') . "\n";

/* ===== GET CUSTOMERS ===== */
$sql_cust = "SELECT customer_id, customer_code 
             FROM customer_master 
             WHERE customer_id != '' 
             AND cust_type='Dealer' 
             AND acedns='Y'  ";

$res_cust = mysql_query($sql_cust);

if (!$res_cust) {
    die("Customer query failed: " . mysql_error());
}

while ($cust = mysql_fetch_assoc($res_cust)) {

    $customer_id   = $cust['customer_id'];
    $customer_code = $cust['customer_code'];

    echo "Processing Customer: $customer_code\n";

    /* ===== API URL (ONLY CURRENT MONTH) ===== */
    $url = 'https://starfiori.starcement.co.in:' . SRARFIORI_PORT_NO .
        '/sap/opu/odata/sap/ZOVW_CUSTTGTACH_CDS/ZOVW_CUSTTGTACH(p_kunnr=\'' . $customer_id .
        '\',p_yr=\'' . $current_year . '\',p_mnth=\'' . $month_padded . '\')/Set?$format=json&sap-client=900';

    $response = get_data_from_cserver($url);

    if (!isJsonCk($response)) {
        echo "Invalid JSON for $customer_code - $current_year-$month_padded\n";
        continue;
    }

    $json = json_decode($response, true);

    if (!isset($json['d']['results']) || count($json['d']['results']) == 0) {
        echo "No Data for $customer_code - $current_year-$month_padded\n";
        continue;
    }

    foreach ($json['d']['results'] as $row) {

        // ✅ Only Total Qty
        if (trim($row['itemcode']) != 'Total Qty') continue;

        $target = floatval($row['TGTQTY']);
        $ach    = floatval($row['ACHQTY']);

        /* ===== UPSERT ===== */
        $sql_insert = "
        INSERT INTO month_wise_achievement_new 
        (customer_id, customer_code, year, month, type, target_qty, achievement_qty, created_at, updated_at)
        VALUES 
        ('$customer_id', '$customer_code', '$current_year', '$current_month', 'current', '$target', '$ach', NOW(), NOW())

        ON DUPLICATE KEY UPDATE
            target_qty = '$target',
            achievement_qty = '$ach',
            updated_at = NOW()
        ";

        $res_insert = mysql_query($sql_insert);

        if (!$res_insert) {
            echo "DB Error: " . mysql_error() . "\n";
        } else {
            echo "Updated: $customer_code - $current_year-$month_padded\n";
        }
    }
}

echo "Cron End: " . date('Y-m-d H:i:s') . "\n";
?>
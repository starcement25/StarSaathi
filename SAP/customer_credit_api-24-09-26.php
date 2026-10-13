<?php
header("Content-Type: application/json");

date_default_timezone_set('Asia/Kolkata');

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "star_connection.php";

/* ================= INPUT ================= */

if (!isset($_GET['customer_code']) || trim($_GET['customer_code']) == '') {

    echo json_encode(array(
        "status" => false,
        "message" => "customer_code is required"
    ));
    exit;
}

$customer_code = trim($_GET['customer_code']);

/* ================= GET CUSTOMER ID ================= */

$check_sql = "
    SELECT customer_id 
    FROM customer_master 
    WHERE customer_code = '".$customer_code."'
    LIMIT 1
";

$check_query = mysql_query($check_sql);

if (!$check_query) {

    echo json_encode(array(
        "status" => false,
        "message" => mysql_error()
    ));
    exit;
}

if (mysql_num_rows($check_query) == 0) {

    echo json_encode(array(
        "status" => false,
        "message" => "Customer not found"
    ));
    exit;
}

$row_customer = mysql_fetch_assoc($check_query);

$customer_id = trim($row_customer['customer_id']);

/* ================= API URL ================= */

$url = 'https://starfiori.starcement.co.in:' . SRARFIORI_PORT_NO .
    '/sap/opu/odata/sap/YOCUST_CREXPOSURE_CDS/YOCUST_CREXPOSURE?$filter=kunnr%20eq%20%27' .
    $customer_id .
    '%27&$format=json';

/* ================= API CALL ================= */

$response = get_data_from_cserver($url);

/* ================= JSON CHECK ================= */

if (!isJsonCk($response)) {

    echo json_encode(array(
        "status" => false,
        "message" => "Invalid API Response"
    ));
    exit;
}

$json = json_decode($response, true);

/* ================= FINAL RESPONSE ================= */

$final_data = array();

if (isset($json['d']['results']) && count($json['d']['results']) > 0) {

    foreach ($json['d']['results'] as $row) {

        // $final_data[] = array(
        //     "status" => true,
        //     "kunnr"  => $row['kunnr'],
        //     "CL"     => $row['CL'],
        //     "CREXP"  => $row['CREXP']
        // );
      $credit_limit   = floatval($row['CL']);
        $utilized_limit = floatval($row['CREXP']);

        if ($utilized_limit >= 0) {

            // Normal utilized amount
            $total_available =  $utilized_limit;

        } else {

            // Negative exposure adjustment
            $total_available = $credit_limit + ($utilized_limit * -1);
        }

        $final_data[] = array(
            "status" => true,
            "kunnr"  => $row['kunnr'],
            "CL"     => number_format($credit_limit, 2, '.', ''),
            "CREXP"  => number_format($utilized_limit, 2, '.', ''),
            "utilized"  => number_format($total_available, 2, '.', '')
        );
    }
} else {

    $final_data[] = array(
        "status" => false,
        "message" => "No data found"
    );
}

/* ================= OUTPUT ================= */

echo json_encode($final_data);

?>
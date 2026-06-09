<?php
header("Content-Type: application/json");

date_default_timezone_set('Asia/Kolkata');

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("star_connection.php");

/* =========================================================
   INPUT PARAMETER
========================================================= */

if (!isset($_GET['customer_code']) || trim($_GET['customer_code']) == '') {

    echo json_encode(array(
        "status" => false,
        "message" => "customer_code is required"
    ));
    exit;
}

$customer_code = trim($_GET['customer_code']);

/* =========================================================
   GET CUSTOMER DETAILS
========================================================= */

$sql_customer = "
    SELECT customer_id, region
    FROM customer_master
    WHERE customer_code = '".mysql_real_escape_string($customer_code)."'
    LIMIT 1
";

$res_customer = mysql_query($sql_customer);

if (!$res_customer) {

    echo json_encode(array(
        "status" => false,
        "message" => mysql_error()
    ));
    exit;
}

if (mysql_num_rows($res_customer) == 0) {

    echo json_encode(array(
        "status" => false,
        "message" => "Customer not found"
    ));
    exit;
}

$row_customer = mysql_fetch_assoc($res_customer);

$customer_id = trim($row_customer['customer_id']);
$region      = strtoupper(trim($row_customer['region']));

/* =========================================================
   GET VALID VKORG ONLY (1010,1017)
========================================================= */

$vkorg_array = array();

$sql_vkorg = "
    SELECT DISTINCT VKORG
    FROM ptblcustomermaster
    WHERE KUNNR = '".mysql_real_escape_string($customer_id)."'
";

$res_vkorg = mysql_query($sql_vkorg);

while ($row_vkorg = mysql_fetch_assoc($res_vkorg)) {

    $vkorg = trim($row_vkorg['VKORG']);

    /* ONLY ALLOW 1010 AND 1017 */

    if ($vkorg == '1010' || $vkorg == '1017') {

        $vkorg_array[] = $vkorg;
    }
}

$vkorg_array = array_unique($vkorg_array);

if (count($vkorg_array) == 0) {

    echo json_encode(array(
        "status" => false,
        "message" => "Valid VKORG not found"
    ));
    exit;
}

/* =========================================================
   REGION WISE API + VARIABLES
========================================================= */

if ($region == 'ROE') {

    $base_url = 'https://starfiori.starcement.co.in:44300/sap/opu/odata/sap/YCUSTAGEROE_SRV/YCUSTAGEROESet';

    $final = array(
        "Day3"  => 0,
        "Day7"  => 0,
        "Day12" => 0,
        "Day25" => 0,
        "Day30" => 0,
        "Day45" => 0,
        "Day60" => 0,
        "Day90" => 0,
        "Abv90" => 0
    );

} else {

    $base_url = 'https://starfiori.starcement.co.in:44300/sap/opu/odata/sap/YCUSTAGENE_SRV/YCUSTAGENESet';

    $final = array(
        "Day3"  => 0,
        "Day10" => 0,
        "Day17" => 0,
        "Day25" => 0,
        "Day30" => 0,
        "Day45" => 0,
        "Day60" => 0,
        "Day90" => 0,
        "Abv90" => 0
    );
}

/* =========================================================
   LOOP API FOR EACH VKORG
========================================================= */
//echo"<pre>";print_r($base_url);die;

foreach ($vkorg_array as $vkorg) {

    $url = $base_url .
        '?$filter=Cocd%20eq%20%27'.$vkorg.
        '%27%20and%20Kunnr%20eq%20%27'.$customer_id.
        '%27&$format=json';
//echo"<pre>";print_r($url);die;

      $response = get_data_from_cserver($url);

    if (!isJsonCk($response)) {
        continue;
    }

    $json = json_decode($response, true);
    // echo"<pre>";print_r($url);die;
    // echo"<pre>";print_r($json);die;

    if (!isset($json['d']['results'][0])) {
        continue;
    }

    $data = $json['d']['results'][0];

    foreach ($final as $key => $value) {

        if (isset($data[$key])) {

            $final[$key] += (float)$data[$key];
        }
    }
}

/* =========================================================
   RESPONSE FORMAT
========================================================= */

$response_data = array();

$type_no = 1;

foreach ($final as $key => $value) {

    $response_data[] = array(
        "type"  => "type".$type_no,
        "title" => $key,
        "value" => number_format($value, 3, '.', '')
    );

    $type_no++;
}

/* =========================================================
   FINAL RESPONSE
========================================================= */

$response_array = array(
    "status" => true,
    "customer_code" => $customer_code,
    "customer_id" => $customer_id,
    "region" => $region,
    "vkorg" => implode(",", $vkorg_array),
    "data" => $response_data
);

/* =========================================================
   OUTPUT
========================================================= */

echo json_encode($response_array);

?>

<?php
header("Content-Type: application/json");

date_default_timezone_set('Asia/Kolkata');

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("star_connection.php");

/* =========================================================
   CURL FUNCTION
========================================================= */

// function get_data_from_cserver_staging($url_ck)
// {
//     $useragent = $_SERVER['HTTP_USER_AGENT'];

//     $username = "STARSAATHI2";
//     $password = "Krishna@9021";

//     $ch = curl_init();

//     curl_setopt($ch, CURLOPT_URL, $url_ck);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//     curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
//     curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//     curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

//     $response = curl_exec($ch);

//     if (curl_errno($ch)) {

//         echo json_encode(array(
//             "status" => false,
//             "message" => curl_error($ch)
//         ));
//         exit;
//     }

//     curl_close($ch);

//     return $response;
// }

/* =========================================================
   JSON CHECK FUNCTION
========================================================= */
/*
function isJsonCk($str)
{
    json_decode($str);
    return (json_last_error() == JSON_ERROR_NONE);
}*/

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
   TYPE PARAMETER
========================================================= */

$type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : 'combined';

/* =========================================================
   TYPE WISE VKORG
========================================================= */

if ($type == 'scl') {

    $vkorg_array = array('1010');

} else if ($type == 'scnel') {

    $vkorg_array = array('1017');

} else if ($type == 'combined') {

    $vkorg_array = array('1010', '1017');

} else {

    echo json_encode(array(
        "status" => false,
        "message" => "Invalid type. Allowed: scl, scnel, combined"
    ));
    exit;
}

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
        "Day15" => 0,
        "Day25" => 0,
        // "Day30" => 0,
        // "Day45" => 0,
        // "Day60" => 0,
        // "Day90" => 0,
        "Abv25" => 0
    );
}

/* =========================================================
   LOOP API FOR EACH VKORG
========================================================= */

foreach ($vkorg_array as $vkorg) {

    $url = $base_url .
        '?$filter=Cocd%20eq%20%27'.$vkorg.
        '%27%20and%20Kunnr%20eq%20%27'.$customer_id.
        '%27&$format=json';

    $response = get_data_from_cserver($url);

    if (!isJsonCk($response)) {
        continue;
    }

    $json = json_decode($response, true);

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
    "type" => $type,
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


<?php

header("Content-Type: application/json");

date_default_timezone_set('Asia/Kolkata');

error_reporting(E_ALL);
ini_set('display_errors', 1);

//include_once("star_connection.php");

/* =========================================================
   INPUT
========================================================= */

function get_data_from_cserver($url_ck)

{

    $useragent = $_SERVER['HTTP_USER_AGENT'];
   //
    //$username = "STARSAATHI"; //port using 44300

    //$password = "Srikrishna@93933";  //port using 44300
    $username = "STARSAATHI2";  //port using 44301

    $password = "Krishna@9021";  //port using 44301
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
function isJsonCk($str)

{

    $json = json_decode($str);

    return $json && $str != $json;

}
if (!isset($_GET['customer_id']) || trim($_GET['customer_id']) == '') {

    echo json_encode(array(
        "status" => false,
        "message" => "customer_id required"
    ));
    exit;
}

if (!isset($_GET['day']) || trim($_GET['day']) == '') {

    echo json_encode(array(
        "status" => false,
        "message" => "day required"
    ));
    exit;
}

$customer_id = trim($_GET['customer_id']);
$day         = trim($_GET['day']);

/* =========================================================
   DAY WISE COCD
========================================================= */

$cocd = "1010";

if (
    $day == "Day3"  ||
    $day == "Day7"  ||
    $day == "Day12" ||
    $day == "Day25" ||
    $day == "Day30"
) {

    $cocd = "1010";

} else {

    $cocd = "1017";
}

/* =========================================================
   API URL
========================================================= */

 $url = "https://starfiori.starcement.co.in:44301/sap/opu/odata/sap/YOCUST_AGEDTLS_CDS/YOCUST_AGEDTLS?\$filter=cocd%20eq%20'".$cocd."'%20and%20customercode%20eq%20'".$customer_id."'&\$format=json";

/* =========================================================
   API CALL
========================================================= */

$response = get_data_from_cserver($url);

if (!isJsonCk($response)) {

    echo json_encode(array(
        "status" => false,
        "message" => "Invalid API Response",
        "raw" => $response
    ));
    exit;
}

$json = json_decode($response, true);

if (!isset($json['d']['results'])) {

    echo json_encode(array(
        "status" => false,
        "message" => "No Data Found"
    ));
    exit;
}

/* =========================================================
   FORMAT DATA
========================================================= */

$output = array();

foreach ($json['d']['results'] as $row) {

    $posting_date = '';

    if (isset($row['postingdate'])) {

        preg_match('/\d+/', $row['postingdate'], $matches);

        if (isset($matches[0])) {

            $timestamp = $matches[0] / 1000;

            $posting_date = date('d-m-Y', $timestamp);
        }
    }

    $output[] = array(

        "cocd"         => isset($row['cocd']) ? $row['cocd'] : "",
        "customercode" => isset($row['customercode']) ? $row['customercode'] : "",
        "documentno"   => isset($row['documentno']) ? $row['documentno'] : "",
        "postingdate"  => $posting_date,
        "assignment"   => isset($row['assignment']) ? $row['assignment'] : "",
        "reference"    => isset($row['reference']) ? $row['reference'] : "",
        "doctype"      => isset($row['doctype']) ? $row['doctype'] : "",
        "narration"    => isset($row['narration']) ? $row['narration'] : "",
        "os"           => isset($row['os']) ? $row['os'] : "0.000",
        "onacc"        => isset($row['onacc']) ? $row['onacc'] : "0.000"

    );
}

/* =========================================================
   FINAL RESPONSE
========================================================= */

echo json_encode(array(
    "status" => true,
    "customer_id" => $customer_id,
    "day" => $day,
    "cocd" => $cocd,
    "count" => count($output),
    "data" => $output
));

?>
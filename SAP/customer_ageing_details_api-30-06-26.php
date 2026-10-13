
<?php

header("Content-Type: application/json");

date_default_timezone_set('Asia/Kolkata');

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* =========================================================
   FUNCTION
========================================================= */

function get_data_from_cserver($url_ck)
{
    $useragent = $_SERVER['HTTP_USER_AGENT'];

    $username = "STARSAATHI2";
    $password = "Krishna@9021";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url_ck);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Curl error : ' . curl_error($ch);
    }

    curl_close($ch);

    return $response;
}

function isJsonCk($str)
{
    json_decode($str);
    return (json_last_error() == JSON_ERROR_NONE);
}

/* =========================================================
   VALIDATION
========================================================= */

if (!isset($_GET['customer_id']) || trim($_GET['customer_id']) == '') {

    echo json_encode(array(
        "status" => false,
        "message" => "customer_id required"
    ));
    exit;
}

if (!isset($_GET['startday']) || trim($_GET['startday']) == '') {

    echo json_encode(array(
        "status" => false,
        "message" => "startday required"
    ));
    exit;
}

if (!isset($_GET['endday']) || trim($_GET['endday']) == '') {

    echo json_encode(array(
        "status" => false,
        "message" => "endday required"
    ));
    exit;
}

$customer_id = trim($_GET['customer_id']);

$startday = (int) trim($_GET['startday']);
$endday   = (int) trim($_GET['endday']);

/* =========================================================
   API URL
========================================================= */

$url = "https://starfiori.starcement.co.in:44301/sap/opu/odata/sap/YOCUST_AGEDTLS_CDS/YOCUST_AGEDTLS?\$filter=customercode%20eq%20'".$customer_id."'&\$format=json";

/* =========================================================
   API CALL
========================================================= */

$response = get_data_from_cserver($url);

if (!isJsonCk($response)) {

    echo json_encode(array(
        "status" => false,
        "message" => "Invalid API Response"
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
   DATE WISE ARRAY
========================================================= */

$final_data = array();

/* =========================================
   LOOP DAY TO DAY
========================================= */

for ($i = $startday; $i <= $endday; $i++) {

    $search_date = date('d-m-Y', strtotime("-$i day"));

    $day_data = array();

    foreach ($json['d']['results'] as $row) {

        /* =========================================
           POSTING DATE
        ========================================= */

        $posting_date = '';

        if (isset($row['postingdate'])) {

            preg_match('/\d+/', $row['postingdate'], $matches);

            if (isset($matches[0])) {

                $timestamp = $matches[0] / 1000;

                $posting_date = date('d-m-Y', $timestamp);
            }
        }

        /* =========================================
           DATE FILTER
        ========================================= */

        if ($posting_date != $search_date) {
            continue;
        }

        /* =========================================
           DR / CR LOGIC
        ========================================= */

        $dr = "0.000";
        $cr = "0.000";

        if (isset($row['os']) && $row['os'] != "0.000") {
            $dr = $row['os'];
        }

        if (isset($row['onacc']) && $row['onacc'] != "0.000") {
            $cr = $row['onacc'];
        }

        /* =========================================
           COCD LOGIC
        ========================================= */

        $company_name = "";

        if ($row['cocd'] == "1010") {
            $company_name = "SCL";
        }

        if ($row['cocd'] == "1017") {
            $company_name = "SCNEL";
        }

        /* =========================================
           DOCTYPE LOGIC
        ========================================= */

        $doctype_name = "";

        switch ($row['doctype']) {

            case "AB":
                $doctype_name = "Clearing document";
                break;

            case "DA":
                $doctype_name = "Customer collection";
                break;

            case "DF":
                $doctype_name = "Clearing";
                break;

            case "DG":
                $doctype_name = "Credit note";
                break;

            case "DR":
                $doctype_name = "Debit note";
                break;

            case "DZ":
                $doctype_name = "Customer Payment";
                break;

            case "RV":
                $doctype_name = "Sales Invoice";
                break;

            case "SA":
                $doctype_name = "Journal";
                break;

            case "UE":
                $doctype_name = "ERP to SAP amount";
                break;

            case "ZD":
                $doctype_name = "Customer Refund";
                break;
        }

        /* =========================================
           DATA ARRAY
        ========================================= */

        $day_data[] = array(

            "cocd"            => isset($row['cocd']) ? $row['cocd'] : "",

            "company_name"    => $company_name,

            "customercode"    => isset($row['customercode']) ? $row['customercode'] : "",

            "documentno"      => isset($row['documentno']) ? $row['documentno'] : "",

            "postingdate"     => $posting_date,

            "assignment"      => isset($row['assignment']) ? $row['assignment'] : "",

            "reference"       => isset($row['reference']) ? $row['reference'] : "",

            "doctype"         => isset($row['doctype']) ? $row['doctype'] : "",

            "doctype_name"    => $doctype_name,

            "narration"       => isset($row['narration']) ? $row['narration'] : "",

            "dr"              => $dr,

            "cr"              => $cr
        );
    }

    /* =========================================
       TITLE ARRAY
    ========================================= */

    $final_data[] = array(

        "title" => $i . " day",

        "date"  => $search_date,

        "count" => count($day_data),

        "data"  => $day_data
    );
}

/* =========================================================
   FINAL RESPONSE
========================================================= */

echo json_encode(array(

    "status"       => true,

    "customer_id"  => $customer_id,

    "startday"     => $startday,

    "endday"       => $endday,

    "total_days"   => ($endday - $startday) + 1,

    "data"         => $final_data

), JSON_PRETTY_PRINT);

?>


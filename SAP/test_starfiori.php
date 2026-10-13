<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 0);
date_default_timezone_set("Asia/Kolkata");
include_once("star_connection.php");

header('Content-Type: application/json');
// echo "Hi";die;
// ---------- SAP CONFIG ----------
$sap_base_url   = "https://starfiori.starcement.co.in:44301/sap/opu/odata/sap/ZSD_CUSTGSTIN_API_SRV/";
$sap_entity_url = $sap_base_url . "CUSTGSTINSet";
$sap_username   = "STARSAATHI2";
$sap_password   = "Krishna@9021";

function sapGetCsrfTokenAndCookies($url, $username, $password)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-CSRF-Token: Fetch',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $err = curl_error($ch);
        curl_close($ch);
        return ['token' => null, 'cookies' => '', 'error' => $err];
    }

    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headerText = substr($response, 0, $headerSize);
    curl_close($ch);

    $headers = [];
    foreach (explode("\r\n", $headerText) as $line) {
        if (strpos($line, ':') !== false) {
            list($k, $v) = explode(':', $line, 2);
            $headers[strtolower(trim($k))] = trim($v);
        }
    }

    preg_match_all('/^Set-Cookie:\s*([^;]+)/mi', $headerText, $matches);
    $cookies = implode('; ', $matches[1]);

    return [
        'token'   => isset($headers['x-csrf-token']) ? $headers['x-csrf-token'] : null,
        'cookies' => $cookies,
        'error'   => null
    ];
}

function sapPostCustGstin($url, $username, $password, $token, $cookies, $payload)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'X-CSRF-Token: ' . $token,
        'Cookie: ' . $cookies
    ]);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    $headerSz = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($response, $headerSz);
    curl_close($ch);

    return [
        'http_code' => $httpCode,
        'body' => $body,
        'error' => $err
    ];
}

function extractPanFromGstin($gstin)
{
    $gstin = trim($gstin);
    if (strlen($gstin) < 12)
        return "";
    return substr($gstin, 2, 10);
}


$sql = "SELECT id, customer_id, gst_no, is_any, pan_no FROM customer_gst_documents WHERE cust_type <> 'Dealer' ORDER BY id ASC";
$res = mysql_query($sql);

if (!$res) {
    echo json_encode(array("status" => "NO", "message" => mysql_error()));
    exit;
}

$total_rows = mysql_num_rows($res);

if ($total_rows == 0) {
    echo json_encode(array("status" => "NO", "message" => "No rows found in customer_gst_documents."));
    exit;
}

$csrf = sapGetCsrfTokenAndCookies($sap_base_url, $sap_username, $sap_password);

if ($csrf['error'] !== null || empty($csrf['token'])) {
    echo json_encode(array(
        "status" => "NO",
        "message" => "Could not fetch initial CSRF token: " . ($csrf['error'] ? $csrf['error'] : 'token missing in response')
    ));
    exit;
}

$token = $csrf['token'];
$cookies = $csrf['cookies'];

$results = array();
$success_count = 0;
$fail_count = 0;

while ($row = mysql_fetch_assoc($res)) {

    $row_id = $row['id'];
    $customer_id = $row['customer_id'];
    $gst_no = trim($row['gst_no']);
    $is_any = trim($row['is_any']);
    $pan_no = trim($row['pan_no']);

    // Pan: prefer stored pan_no, else derive from GSTIN
    $sap_pan = $pan_no;
    if ($sap_pan === "" && $gst_no !== "") {
        $sap_pan = extractPanFromGstin($gst_no);
    }

    $payload = array(
        "Id" => (int) $row_id,
        "Kunnr" => (string) $customer_id,
        "Pan" => (string) $sap_pan,
        "Gstin" => (string) $gst_no,
        "Status" => (string) $is_any
    );

    $post_result = sapPostCustGstin($sap_entity_url, $sap_username, $sap_password, $token, $cookies, $payload);

    // If token expired/invalid mid-run, refresh once and retry this same row
    if ($post_result['http_code'] == 403) {
        $csrf_retry = sapGetCsrfTokenAndCookies($sap_base_url, $sap_username, $sap_password);
        if ($csrf_retry['token']) {
            $token = $csrf_retry['token'];
            $cookies = $csrf_retry['cookies'];
            $post_result = sapPostCustGstin($sap_entity_url, $sap_username, $sap_password, $token, $cookies, $payload);
        }
    }

    $row_success = ($post_result['http_code'] >= 200 && $post_result['http_code'] < 300);

    if ($row_success) {
        $success_count++;
    } else {
        $fail_count++;
    }

    $results[] = array(
        "row_id" => $row_id,
        "customer_id" => $customer_id,
        "success" => $row_success,
        "http_code" => $post_result['http_code'],
        "response" => $post_result['body']
    );

    echo json_encode(array(
        "progress" => count($results) . "/" . $total_rows,
        "customer_id" => $customer_id,
        "success" => $row_success
    )) . "\n";
    @ob_flush();
    @flush();
}

// ---------- Final summary ----------
echo "\n===== FINAL SUMMARY =====\n";
echo json_encode(array(
    "status" => "DONE",
    "total_rows" => $total_rows,
    "success_count" => $success_count,
    "fail_count" => $fail_count,
    "results" => $results
), JSON_PRETTY_PRINT);
?>
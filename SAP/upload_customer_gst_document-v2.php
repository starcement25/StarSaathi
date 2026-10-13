<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set("Asia/Kolkata");

include_once("star_connection.php");

require 'aws-autoloader.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

header('Content-Type: application/json');

if (
    empty($_POST['customer_id'])

) {
    echo json_encode(array(
        "status" => "NO",
        "message" => "Required fields missing."
    ));
    exit;
}

$sap_base_url = "https://starfiori.starcement.co.in:44301/sap/opu/odata/sap/ZSD_CUSTGSTIN_API_SRV/";
$sap_entity_url = $sap_base_url . "CUSTGSTINSet";
$sap_username = "STARSAATHI2";
$sap_password = "Krishna@9021";

$customer_id = mysql_real_escape_string($_POST['customer_id']);
$gst_no = isset($_POST['gst_no']) ? mysql_real_escape_string(trim($_POST['gst_no'])) : "";
$is_any = isset($_POST['is_any']) ? mysql_real_escape_string(trim($_POST['is_any'])) : "";
$decleration = isset($_POST['decleration']) ? mysql_real_escape_string(trim($_POST['decleration'])) : "";

$raw_customer_id = trim($_POST['customer_id']);
$mention_name = isset($_POST['mention_name']) ? trim($_POST['mention_name']) : "";
$pan_no = isset($_POST['pan_no']) ? trim($_POST['pan_no']) : "";

$mention_name_db = mysql_real_escape_string($mention_name);
$pan_no_db = mysql_real_escape_string($pan_no);

$sql_cust = "SELECT cust_type FROM customer_master WHERE customer_id='" . $customer_id . "' LIMIT 1";
$res_cust = mysql_query($sql_cust);

if (!$res_cust || mysql_num_rows($res_cust) == 0) {
    echo json_encode(array(
        "status" => "NO",
        "message" => "Invalid customer id."
    ));
    exit;
}

$row_cust = mysql_fetch_assoc($res_cust);
$cust_type = mysql_real_escape_string(trim($row_cust['cust_type']));

$file_url = "";

// ---------- S3 CONFIG ----------
$bucket_name = 'starcement1-sbinfo-upload';
include(dirname(__FILE__)."/aws_credentials.php");
$access_key_id = $aws_access_key_id;
$secret = $aws_secret;
$folderName = "gst_documents";
// --------------------------------

if (isset($_FILES['upload_doc']) && $_FILES['upload_doc']['error'] == 0 && !empty($_FILES['upload_doc']['tmp_name'])) {

    $file = $_FILES['upload_doc'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    $allowed = array('jpg', 'jpeg', 'png', 'gif', 'pdf');
    if (!in_array($extension, $allowed)) {
        echo json_encode(array(
            "status" => "NO",
            "message" => "Invalid file type. Allowed: " . implode(', ', $allowed)
        ));
        exit;
    }

    $file_name = "GST_DOC_" . $customer_id . "_" . time() . "." . $extension;
    $temp_file = $file['tmp_name'];

    try {

        $s3 = new S3Client([
            'region' => 'ap-south-1',
            'version' => 'latest',
            'credentials' => [
                'key' => $access_key_id,
                'secret' => $secret,
            ]
        ]);

        $result = $s3->putObject([
            'Bucket' => $bucket_name,
            'Key' => $folderName . '/' . $file_name,
            'SourceFile' => $temp_file,
            'ACL' => 'private'
        ]);

        $file_url = $result['ObjectURL'];

    } catch (AwsException $e) {

        echo json_encode(array(
            "status" => "NO",
            "message" => $e->getAwsErrorMessage()
        ));
        exit;

    } catch (Exception $e) {

        echo json_encode(array(
            "status" => "NO",
            "message" => $e->getMessage()
        ));
        exit;

    }
} else if (isset($_FILES['upload_doc']) && $_FILES['upload_doc']['error'] != 0 && $_FILES['upload_doc']['error'] != 4) {
    echo json_encode(array(
        "status" => "NO",
        "message" => "File upload failed."
    ));
    exit;
}

// ---------- AUTO-GENERATE DECLARATION PDF WHEN is_any = 0 (GST Not Available) ----------
$declaration_pdf_url = "";
$declaration_pdf_msg = "";

if (trim($is_any) === "0") {

    $decl_url = "https://starsaathi.com/SAP/gst_declaration.php?action=get_customer"
        . "&customer_id=" . urlencode($raw_customer_id)
        . "&mention_name=" . urlencode($mention_name)
        . "&pan_no=" . urlencode($pan_no)
        . "&for_pdf=1";

    $ch = curl_init($decl_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $decl_html = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_err = curl_error($ch);
    curl_close($ch);

    if ($decl_html !== false && $http_code == 200 && trim($decl_html) != "") {

        $pdf_css = '<style>
  body { font-family:helvetica; font-size:9pt; color:#000000; }
  table.cust { width:100%; border-collapse:collapse; font-size:9pt; }
  table.cust th, table.cust td { border:0.5px solid #999999; }
  table.cust th { background-color:#f2f4f6; font-weight:bold; text-align:left; width:32%; }
  table.cust td { text-align:left; width:68%; }
  h2.title { text-align:center; font-size:11pt; font-weight:bold; text-decoration:underline; }
  p.to      { font-weight:bold; font-size:9pt; }
  p.dear    { font-size:9pt; }
  p.subject { font-weight:bold; font-size:9pt; text-align:justify; }
  p.decl    { font-size:9pt; text-align:justify; }
  .decl-list p { font-size:9pt; text-align:justify; }
  .sig p.body  { font-size:9pt; font-weight:bold; }
  .err-box { font-size:9pt; color:#8a1c14; }
</style>';

        $decl_html = preg_replace('#<style\b[^>]*>.*?</style>#is', '', $decl_html);
        $decl_html = str_replace('<table class="cust">', '<table class="cust" cellpadding="5" cellspacing="0">', $decl_html);
        if (stripos($decl_html, '</head>') !== false) {
            $decl_html = str_ireplace('</head>', $pdf_css . '</head>', $decl_html);
        } else {
            $decl_html = $pdf_css . $decl_html;
        }

        try {

            require_once('tcpdf/tcpdf.php');

            ob_start();

            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('Star Saathi');
            $pdf->SetTitle('Declaration for Unregistered Person (URP)');
            $pdf->SetPrintHeader(false);
            $pdf->SetPrintFooter(false);
            $pdf->SetMargins(12, 10, 12);
            $pdf->SetAutoPageBreak(true, 10);
            $pdf->setCellHeightRatio(1.1);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->AddPage();
            $pdf->writeHTML($decl_html, true, false, true, false, '');

            $pdf_name = "GST_DECL_" . $raw_customer_id . "_" . time() . ".pdf";
            $tmp_pdf = rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR . $pdf_name;
            $pdf->Output($tmp_pdf, 'F');

            ob_end_clean();

            if (file_exists($tmp_pdf)) {

                $s3_decl = new S3Client([
                    'region' => 'ap-south-1',
                    'version' => 'latest',
                    'credentials' => [
                        'key' => $access_key_id,
                        'secret' => $secret,
                    ]
                ]);

                $result_decl = $s3_decl->putObject([
                    'Bucket' => $bucket_name,
                    'Key' => $folderName . '/' . $pdf_name,
                    'SourceFile' => $tmp_pdf,
                    'ACL' => 'private'
                ]);

                $declaration_pdf_url = $result_decl['ObjectURL'];
                @unlink($tmp_pdf);

            } else {
                $declaration_pdf_msg = "PDF could not be written to temp folder.";
            }

        } catch (AwsException $e) {
            if (ob_get_length() !== false) {
                @ob_end_clean();
            }
            $declaration_pdf_msg = "Declaration PDF upload failed: " . $e->getAwsErrorMessage();
        } catch (Exception $e) {
            if (ob_get_length() !== false) {
                @ob_end_clean();
            }
            $declaration_pdf_msg = "Declaration PDF failed: " . $e->getMessage();
        }

    } else {
        $declaration_pdf_msg = "Could not fetch declaration page (HTTP " . $http_code . ") " . $curl_err;
    }

    if ($file_url == "" && $declaration_pdf_url != "") {
        $file_url = $declaration_pdf_url;
    }
}
// ---------------------------------------------------------------------------

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
        'token' => isset($headers['x-csrf-token']) ? $headers['x-csrf-token'] : null,
        'cookies' => $cookies,
        'error' => null
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

$file_url = mysql_real_escape_string($file_url);

// multiple rows per customer_id are allowed : every submit is a new record.
// consumers must read the latest row (highest id) for a customer.
{
    $query = "
    INSERT INTO customer_gst_documents
    (
    customer_id,
    cust_type,
    gst_no,
    upload_doc,
    is_any,
    decleration,
    mention_name,
    pan_no,
    created_date
    )
    VALUES
    (
    '$customer_id',
    '$cust_type',
    '$gst_no',
    '$file_url',
    '$is_any',
    '$decleration',
    '$mention_name_db',
    '$pan_no_db',
    NOW()
    )
    ";

    $action_msg = "Document uploaded successfully.";
}

if (mysql_query($query)) {
    $inserted_id = mysql_insert_id();

    $sap_pan = trim($pan_no);
    if ($sap_pan === "" && trim($gst_no) !== "") {
        $sap_pan = extractPanFromGstin($gst_no);
    }

    $sap_payload = array(
        "Id" => (int) $inserted_id,
        "Kunnr" => (string) $raw_customer_id,
        "Pan" => (string) $sap_pan,
        "Gstin" => (string) trim($gst_no),
        "Status" => (string) $is_any
    );

    $sap_status = "NO";
    $sap_message = "";

    $csrf = sapGetCsrfTokenAndCookies($sap_base_url, $sap_username, $sap_password);

    if ($csrf['error'] !== null || empty($csrf['token'])) {
        $sap_message = "Could not fetch CSRF token: " . ($csrf['error'] ? $csrf['error'] : 'token missing in response');
    } else {
        $sap_result = sapPostCustGstin(
            $sap_entity_url,
            $sap_username,
            $sap_password,
            $csrf['token'],
            $csrf['cookies'],
            $sap_payload
        );

        if ($sap_result['http_code'] >= 200 && $sap_result['http_code'] < 300) {
            $sap_status = "YES";
            $sap_message = "Pushed to SAP successfully.";
        } else {
            $sap_message = "SAP push failed (HTTP " . $sap_result['http_code'] . "): " . $sap_result['body'];
        }
    }

    echo json_encode(array(
        "status" => "YES",
        "message" => $action_msg,
        "cust_type" => $cust_type,
        "document_url" => $file_url,
        "declaration_pdf_url" => $declaration_pdf_url,
        "declaration_pdf_message" => $declaration_pdf_msg,
        "sap_status" => $sap_status,
        "sap_message" => $sap_message
    ));
} else {
    echo json_encode(array(
        "status" => "NO",
        "message" => mysql_error()
    ));
}

?>
<?php
/* =========================================================
   CRON : pull GSTIN from SAP for every active Dealer and
   store it in customer_gst_documents.

   Source : ZOVW_CUSTGSTIN_CDS/ZOVW_CUSTGSTIN  (CUSTOMERCODE = customer_id)

   A new row is written ONLY when the GSTIN differs from the
   latest row already stored for that customer, so running this
   daily does not bloat the history table.

   Optional query params (handy for a manual test run):
     ?limit=25          -> process only the first 25 dealers
     ?customer_id=100.. -> process just one dealer
========================================================= */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '999M');
set_time_limit(0);
date_default_timezone_set('Asia/Kolkata');

include_once("star_connection.php");

/* =========================================================
   FUNCTIONS
========================================================= */
/*
function get_data_from_cserver($url_ck)
{
    // cron may run from CLI where HTTP_USER_AGENT does not exist
    $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "StarSaathi-Cron";

    // port 44300 credentials
    $username = "STARSAATHI";
    $password = "Srikrishna@93933";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url_ck);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $response = false;
    }

    curl_close($ch);

    return $response;
}

function isJsonCk($str)
{
    json_decode($str);
    return (json_last_error() == JSON_ERROR_NONE);
}
*/
/* =========================================================
   FETCH DEALERS
========================================================= */

$sap_base = "https://starfiori.starcement.co.in:44300/sap/opu/odata/sap/ZOVW_CUSTGSTIN_CDS/ZOVW_CUSTGSTIN";

$one_customer = isset($_REQUEST['customer_id']) ? mysql_real_escape_string(trim($_REQUEST['customer_id'])) : "";
$run_limit    = isset($_REQUEST['limit']) ? (int)$_REQUEST['limit'] : 0;

$sql = "SELECT customer_id, cust_type
        FROM customer_master
        WHERE cust_type='Dealer' AND acedns='Y'
        AND customer_id!='' AND customer_id IS NOT NULL";

if ($one_customer != "") {
    $sql .= " AND customer_id='".$one_customer."'";
}

$sql .= " ORDER BY customer_id ASC";

if ($run_limit > 0) {
    $sql .= " LIMIT ".$run_limit;
}

$res = mysql_query($sql);

if (!$res) {
    echo "Query failed : ".mysql_error();
    exit;
}

/* =========================================================
   LOOP + STORE
========================================================= */

$total    = 0;   // dealers processed
$inserted = 0;   // new gst rows written
$same     = 0;   // gstin unchanged, nothing written
$no_gst   = 0;   // SAP returned no GSTIN
$failed   = 0;   // api / db error
$log      = array();

while ($row = mysql_fetch_assoc($res)) {

    $customer_id = trim($row['customer_id']);
    if ($customer_id === '') { continue; }

    $total++;
    $cust_type = mysql_real_escape_string(trim($row['cust_type']));
    $cid_db    = mysql_real_escape_string($customer_id);

    // single quotes: $filter / $format must NOT be parsed as php variables
    $url = $sap_base
         . '?$filter=CUSTOMERCODE%20eq%20%27' . urlencode($customer_id) . '%27'
         . '&$format=json';

    $response = get_data_from_cserver($url);

    if ($response === false || trim($response) === '' || !isJsonCk($response)) {
        $failed++;
        $log[] = $customer_id." : api failed / invalid json";
        continue;
    }

    $data    = json_decode($response, true);
    $results = isset($data['d']['results']) ? $data['d']['results'] : array();

    if (empty($results)) {
        $no_gst++;
        $log[] = $customer_id." : no record in sap";
        continue;
    }

    $gstin = isset($results[0]['GSTIN']) ? trim($results[0]['GSTIN']) : '';

    if ($gstin === '') {
        $no_gst++;
        $log[] = $customer_id." : blank gstin";
        continue;
    }

    // compare against the newest row already stored for this customer
    $last_gst  = '';
    $chk_sql   = "SELECT gst_no FROM customer_gst_documents
                  WHERE customer_id='".$cid_db."' ORDER BY id DESC LIMIT 1";
    $chk_res   = mysql_query($chk_sql);

    if ($chk_res && mysql_num_rows($chk_res) > 0) {
        $chk_row  = mysql_fetch_assoc($chk_res);
        $last_gst = trim($chk_row['gst_no']);
    }

    if ($last_gst !== '' && strcasecmp($last_gst, $gstin) == 0) {
        $same++;
        continue;
    }

    $gst_db = mysql_real_escape_string($gstin);

    $ins_sql = "INSERT INTO customer_gst_documents
                (customer_id, cust_type, gst_no, upload_doc, is_any, decleration, mention_name, pan_no, created_date)
                VALUES
                ('".$cid_db."', '".$cust_type."', '".$gst_db."', '', '1', '', '', '', NOW())";

    if (mysql_query($ins_sql)) {
        $inserted++;
        $log[] = $customer_id." : saved ".$gstin;
    } else {
        $failed++;
        $log[] = $customer_id." : db error ".mysql_error();
    }
}

/* =========================================================
   SUMMARY
========================================================= */

echo "GST sync finished ".date('d-m-Y H:i:s')."\n";
echo "Dealers processed : ".$total."\n";
echo "New GST saved     : ".$inserted."\n";
echo "Unchanged         : ".$same."\n";
echo "No GST in SAP     : ".$no_gst."\n";
echo "Failed            : ".$failed."\n";
echo "-----------------------------------\n";
echo implode("\n", $log)."\n";

mysql_close();
?>

<?php
/* =========================================================
   CREDIT LIMIT UTILIZATION SMS CRON
   RUN DAILY AT 9 AM
========================================================= */

date_default_timezone_set('Asia/Kolkata');

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("star_connection.php");
//include_once("sms_function.php"); // send_sms_new function
//include_once("common_function.php"); // get_data_from_cserver()

/* ================= SAP AUTH ================= */
function send_sms_new($tmp_id,$to_mobile,$message){
$sms_res = "";
$to_mobile = $to_mobile ? trim($to_mobile) : "";
$message = $message ? trim($message) : "";
if($tmp_id!="" && $to_mobile!="" && $message!=""){
$lipl_uri = "https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to=".$to_mobile."&from=STARCM&text=".urlencode($message)."&tempid=".$tmp_id."&dlr-mask=19&dlr-url";
$lipl_ch = curl_init();
curl_setopt($lipl_ch, CURLOPT_URL, $lipl_uri);
curl_setopt($lipl_ch, CURLOPT_TIMEOUT, 20);
curl_setopt($lipl_ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($lipl_ch, CURLOPT_HEADER,0);
curl_setopt($lipl_ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($lipl_ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
$sms_res = curl_exec($lipl_ch);
curl_close($lipl_ch);

}
return $sms_res;
}

$username = "STARSAATHI2";
$password = "Krishna@9021";

/* ================= SMS TEMPLATE ID ================= */

$tmp_id = "1707160982733435860";

/* ================= GET DEALERS ================= */

$sql = "
    SELECT 
        customer_id,
        customer_name,
        customer_code,
        phone_no
    FROM customer_master
    WHERE acedns='Y'
    AND customer_id!=''
    AND phone_no!='' and customer_id='1000000024'
";
// and customer_id='1000000024'
$res = mysql_query($sql);

while ($row = mysql_fetch_assoc($res)) {

    $customer_id   = trim($row['customer_id']);
    $customer_name = trim($row['customer_name']);
    $mob_no        = trim($row['phone_no']);

    if ($mob_no == '') {
        continue;
    }

    /* =========================================================
       API URL
    ========================================================= */

    $url = 'https://starfiori.starcement.co.in:' . SRARFIORI_PORT_NO .
        '/sap/opu/odata/sap/YOCUST_CREXPOSURE_CDS/YOCUST_CREXPOSURE?$filter=kunnr%20eq%20%27' .
        $customer_id .
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

    $CL    = isset($data['CL']) ? (float)$data['CL'] : 0;
    $CREXP = isset($data['CREXP']) ? (float)$data['CREXP'] : 0;

    if ($CL <= 0) {
        continue;
    }

    /* =========================================================
       UTILIZATION %
    ========================================================= */

    $utilization = ($CREXP / $CL) * 100;

    $utilization = round($utilization, 2);

    /* =========================================================
       SEND SMS IF ABOVE 75%
    ========================================================= */

    if ($utilization >= 75) {

        $today_date = date('d-m-Y');

        $otp_text = "Dear ".$customer_name.
        ", your current credit limit utilisation has reached :".
        $utilization."% as on ".$today_date.
        ". Kindly ensure timely payment and monitor your available balance to avoid transaction interruptions. - Star Cement";

        /* ================= SEND SMS ================= */
        $mob_no='7044497293';
        $sms_res = send_sms_new($tmp_id, $mob_no, $otp_text);
        /*echo "<pre>";
        print_r($sms_res);
        echo "</pre>";*/
        /* ================= SMS LOG INSERT ================= */
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $log_sql = "
            INSERT INTO credit_limit_sms_log
            SET
                customer_id   = '".mysql_real_escape_string($customer_id)."',
                customer_name = '".mysql_real_escape_string($customer_name)."',
                mob_no        = '".mysql_real_escape_string($mob_no)."',
                cl_amount     = '".mysql_real_escape_string($CL)."',
                crexp_amount  = '".mysql_real_escape_string($CREXP)."',
                utilization   = '".mysql_real_escape_string($utilization)."',
                sms_text      = '".mysql_real_escape_string($otp_text)."',
                sms_response  = '".mysql_real_escape_string($sms_res)."',
                created_at    = '".$created_at."'
        ";

        mysql_query($log_sql);
        /* ================= LOG ================= */

        echo "<br>";
        echo "SMS Sent To : ".$customer_name;
        echo "SMS Sent To ID : ".$customer_id;
        echo "sms text : ".$otp_text;
        echo " (".$mob_no.")";
        echo " Utilization : ".$utilization."%";
        echo "<br>"; die;
    }
}

echo "<br><b>CRON COMPLETED</b>";

?>
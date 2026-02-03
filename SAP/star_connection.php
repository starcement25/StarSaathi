<?php

ini_set('memory_limit', '1024M');

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);

date_default_timezone_set("Asia/Kolkata");

/*$servername = "localhost";

$username = "starsaat_dnsprod";

$password = "dnsprod1234#";
$db_name = "starsaathi_STARS";
*/
/*
$servername = "52.66.97.178";
$username = "root";
$password = "UZzRXN4CMJtOaUq7";
$db_name = "starsaathi_STARS";
*/
$servername = "starsaathi-rds-server.clcy6zb4izp8.ap-south-1.rds.amazonaws.com";
$username = "admin";
$password = "zwPB6L65ZC}p8L89";
$db_name = "starsaathi_STARS";

$GLOBALS['starfiori_port_no'] = "44300";
define ('SRARFIORI_PORT_NO','44300');
 //$username = "STARSAATHI"; //port using 44300

    //$password = "Srikrishna@93933";  //port using 44300
define ('username','STARSAATHI');
define ('password','Srikrishna@93933');
// define ('username','STARSAATHI2');44301
// define ('password','Krishna@9021');44301
$GLOBALS['send_email'] = ""; //if blank value then go live customer 

$conn = mysql_connect($servername, $username, $password);

if (!$conn) {

    die('Could not connect: ' . mysql_error());

}

$db_selected = mysql_select_db($db_name, $conn);

if (!$db_selected) {

    die('Can\'t connect to database : ' . mysql_error());

}

$server_url = "https://" . $_SERVER['SERVER_NAME'] . "/SAP/";
define ('BASE_URL','https://starsaathi.com/SAP/');
function get_value_by_setting_key($keyname)

{

    $keyvalue = "";

    $setting_master = "app_setting_master";

    if ($keyname != "") {

        $sql_gapc = "select `the_value` from $setting_master where `the_key_name`='$keyname'";

        $res_gapc = mysql_query($sql_gapc);

        $totres_gapc = mysql_num_rows($res_gapc);

        if ($totres_gapc > 0) {

            $row_gapc = mysql_fetch_assoc($res_gapc);

            $keyvalue = trim($row_gapc["the_value"]);

        }

    }

    return $keyvalue;

}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function send_the_mail($to_email, $subject, $bodyml){
    //echo"<pre>";print_r($bodyml);die;
error_reporting(E_STRICT);
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
require __DIR__.'/phpmailer_new/PHPMailer.php';
require __DIR__.'/phpmailer_new/SMTP.php';
require __DIR__.'/phpmailer_new/Exception.php';
    
    $sts = "FALSE";
    $to_email_arr = array();
    $to_email = $to_email ? trim($to_email) : "";
    $subject = $subject ? trim($subject) : "";
    $bodyml = $bodyml ? trim($bodyml) : "";
    if ($to_email != "" && $subject != "" && $bodyml != "") {
        $to_email_arr = explode(",", $to_email);
        if (count($to_email_arr) > 0) {
$mail = new PHPMailer(true);
try {
    // SMTP configuration
    $mail->isSMTP();
    //$mail->SMTPDebug  = 2;  
    $mail->Host = "cloudmail2.up99plus.com";
    $mail->Port = 25;
    $mail->SMTPAuth = true;
    $mail->Username = "starcement@cloudmail.up99plus.com";
    $mail->Password = "K2TTvLxATyULV2um"; // Insert your actual password here
    $mail->SMTPSecure = false; // Explicit TLS encryption is not used
    $mail->SMTPAutoTLS = false; // Disable automatic TLS upgrade
    // Sender information
    $mail->setFrom('starcement@cloudmail.up99plus.com', 'Starsaathi');
    // Recipient
	foreach ($to_email_arr as $to_email_arr_val) {
	if (trim($to_email_arr_val) != "") {
	if (filter_var(trim($to_email_arr_val), FILTER_VALIDATE_EMAIL)) {
	$mail->addAddress(trim($to_email_arr_val), $to_email_arr_val);
	}
	}
	}
			
    // Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    //$mail->Body = $bodyml;
	$mail->MsgHTML($bodyml);
    $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
    // Send the email
   $mlsts =  $mail->send();    
//    echo"<pre>";print_r($mlsts);die;
    $sts = "TRUE";
} catch (Exception $e) {
    $sts = "FALSE";
}

        }
    }
    return $sts;
}
function send_the_mail_old($to_email, $subject, $bodyml)

{

    error_reporting(E_STRICT);

    set_time_limit(0);

    date_default_timezone_set("Asia/Kolkata");

    require_once('class.phpmailer.php');

    require_once('class.smtp.php');

    $sts = "FALSE";

    $to_email_arr = array();

    $to_email = $to_email ? trim($to_email) : "";

    $subject = $subject ? trim($subject) : "";

    $bodyml = $bodyml ? trim($bodyml) : "";

    if ($to_email != "" && $subject != "" && $bodyml != "") {

        $to_email_arr = explode(",", $to_email);

        if (count($to_email_arr) > 0) {

            $mail = new PHPMailer();

            $bodyml = $bodyml;

            //$bodyml             = eregi_replace("[\]",'',$bodyml);

            $mail->IsSMTP(); // telling the class to use SMTP

            // $mail->Host       = "mail.starcement.co.in"; // SMTP server (For gmail "mail.coral.in")

            // $mail->Host           = "mail.google.com";

            $mail->SMTPDebug  = SMTP::DEBUG_OFF;                     // enables SMTP debug information (for testing)
		

            // 1 = errors and messages

            // 2 = messages only

            $mail->SMTPAuth   = true;                  // enable SMTP authentication

            $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier

            //$mail->Host       = "103.87.174.95";      // sets GMAIL as the SMTP server (For gmail "mail.coral.in")

            //$mail->Host       = "96.45.76.75";       

            $mail->Host       = "smtp.gmail.com";         // sets GMAIL as the SMTP server (For gmail "mail.coral.in")

            $mail->Port       = 465;                     // set the SMTP port for the GMAIL server (For gmail 465 )

            //$mail->Username   = "dev@starsaathi.com";  // GMAIL username

            //$mail->Username   = "starsaathi-starcement";

            $mail->Username   = "starsaathi@starcement.co.in";

            //$mail->Password   = "google3d33#";            // GMAIL password

            //$mail->Password   = "BVhf@_745hw";

            //$mail->Password   = "Star@2023";

            //$mail->Password   = "Domain@5467";

            //$mail->Password   = "Domain@5469";	

            $mail->Password   = "Domain@5470";



            $mail->SetFrom('starsaathi@starcement.co.in', 'Starsaathi');

            $mail->Subject    = $subject;

            $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

            $mail->MsgHTML($bodyml);

            foreach ($to_email_arr as $to_email_arr_val) {

                if (trim($to_email_arr_val) != "") {

                    if (filter_var(trim($to_email_arr_val), FILTER_VALIDATE_EMAIL)) {

                        $mail->AddAddress(trim($to_email_arr_val), $to_email_arr_val);

                    }

                }

            }

            $mlsts = $mail->Send();

            if (!$mlsts) {

                $sts = "FALSE";

            } else {

                $sts = "TRUE";

            }

        }

    }

    return $sts;

}

function isJsonCk($str)

{

    $json = json_decode($str);

    return $json && $str != $json;

}

function get_data_from_cserver($url_ck)

{

    $useragent = $_SERVER['HTTP_USER_AGENT'];
   //
    //$username = "STARSAATHI"; //port using 44300

    //$password = "Srikrishna@93933";  //port using 44300
    $username = username;  //port using 44301

    $password = password;  //port using 44301
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

?>
<?php
set_time_limit(0);

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include "star_connection.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer_new/PHPMailer.php';
require '../phpmailer_new/SMTP.php';
require '../phpmailer_new/Exception.php';

$res_msg = array();

$customer_master = "customer_master";
$broker_master = "broker_master";
$customer_broker_relation = "customer_broker_relation";

$dealer_id = isset($_POST["dealer_id"]) ? addslashes(trim($_POST["dealer_id"])) : "";
$mob_no    = isset($_POST["mob_no"]) ? addslashes(trim($_POST["mob_no"])) : "";

$sms_res   = "";
$email_res = "";
$smtp_debug = "";

if ($dealer_id != "" && $mob_no != "") {

    // Generate OTP
    $otp_for_login = rand(1,9).rand(0,9).rand(0,9).rand(1,9);

    // Test OTP
    if ($mob_no == "9233974090" || $mob_no == "9638307128") {
        $otp_for_login = "1010";
    }

    if (
        strtoupper($dealer_id) == "TEST011" ||
        strtoupper($dealer_id) == "TEST012" ||
        strtoupper($dealer_id) == "TEST013" ||
        strtoupper($dealer_id) == "TEST029"
    ) {
        $otp_for_login = "1010";
    }

    if (strtoupper($dealer_id) == "1000001932") {
        $otp_for_login = "1902";
    }

    $otp_text = "OTP is ".$otp_for_login." for Star Saathi log in STAR CEMENT";

    // =========================
    // CHECK DEALER
    // =========================
    $sql1 = "SELECT dns_customer_code,email 
             FROM $customer_master 
             WHERE customer_id='".$dealer_id."' 
             AND phone_no='".$mob_no."' 
             AND cust_type='Dealer'";

    $res1 = mysql_query($sql1);
    $totres1 = mysql_num_rows($res1);

    if ($totres1 > 0) {

        $row1 = mysql_fetch_assoc($res1);

        $email = isset($row1['email']) ? trim($row1['email']) : "";

        // Update OTP
        $sql1_upd = "UPDATE $customer_master 
                     SET sms_otp='$otp_for_login' 
                     WHERE customer_id='".$dealer_id."' 
                     AND phone_no='".$mob_no."'";

        mysql_query($sql1_upd);

        // ================= SMS =================
        if ($mob_no != "9233974090" && $mob_no != "9638307128") {

            $tmp_id = "1707160982733435860";
            $sms_res = send_sms_new($tmp_id, $mob_no, $otp_text);
        }

        // ================= EMAIL =================
        $email_res = "EMAIL NOT AVAILABLE";

        if ($email != "" && filter_var($email, FILTER_VALIDATE_EMAIL)) {

            try {

                $mail = new PHPMailer(true);

                // // DEBUG
                // $mail->SMTPDebug = 3;

                // $mail->Debugoutput = function($str, $level) use (&$smtp_debug) {
                //     $smtp_debug .= "Level $level : $str\n";
                // };

                // SMTP
                $mail->isSMTP();

                $mail->Host = "cloudmail2.up99plus.com";
                $mail->Port = 25;

                $mail->SMTPAuth = true;

                $mail->Username = "starcement@cloudmail.up99plus.com";
                $mail->Password = "Nh26sjqgWk";

                $mail->SMTPSecure = false;
                $mail->SMTPAutoTLS = false;

                $mail->Timeout = 60;

                // FROM
                $mail->setFrom(
                    'starcement@cloudmail.up99plus.com',
                    'Starsaathi'
                );

                // TO
                $mail->addAddress(trim($email));

                // CONTENT
                $mail->isHTML(true);

                $mail->Subject = "Star Saathi Login OTP";

                $mail->Body = "
                <html>
                <body>
                    <p>Dear User,</p>

                    <p>Your OTP for Star Saathi Login is:</p>

                    <h2>".$otp_for_login."</h2>

                    <p>Please do not share this OTP with anyone.</p>

                    <br>

                    <p>Regards,<br>
                    Star Cement</p>
                </body>
                </html>";

                // SEND
                $mail->send();

                $email_res = "EMAIL SENT";

            } catch (Exception $e) {

                $email_res = "EMAIL FAILED : ".$mail->ErrorInfo;
            }
        }

        $res_msg = array(
            "process_sts" => "YES",
            "process_msg" => "OTP has been sent successfully.",
            "sms_res"     => $sms_res,
            "email_res"   => $email_res,
            "smtp_debug"  => $smtp_debug,
            "email"       => $email
        );

    } else {

        // =========================
        // CHECK BROKER
        // =========================
        $sql2 = "SELECT dns_broker_id,broker_id 
                 FROM $broker_master 
                 WHERE dns_broker_id='".$dealer_id."' 
                 AND phone_no='".$mob_no."' 
                 AND acedns='Y'";

        $res2 = mysql_query($sql2);
        $totres2 = mysql_num_rows($res2);

        if ($totres2 > 0) {

            $row2 = mysql_fetch_assoc($res2);

            $broker_id = trim($row2["broker_id"]);

            $sql24 = "SELECT customer_code 
                      FROM $customer_broker_relation 
                      WHERE broker_code='$broker_id'";

            $res24 = mysql_query($sql24);
            $totres24 = mysql_num_rows($res24);

            if ($totres24 > 0) {

                // Update OTP
                $sql1_upd = "UPDATE $broker_master 
                             SET sms_otp='$otp_for_login' 
                             WHERE dns_broker_id='".$dealer_id."' 
                             AND phone_no='".$mob_no."'";

                mysql_query($sql1_upd);

                // Send SMS
                if ($mob_no != "9233974090" && $mob_no != "9638307128") {

                    $tmp_id = "1707160982733435860";
                    $sms_res = send_sms_new($tmp_id, $mob_no, $otp_text);
                }

                $res_msg = array(
                    "process_sts" => "YES",
                    "process_msg" => "OTP has been sent successfully.",
                    "sms_res"     => $sms_res
                );

            } else {

                $res_msg = array(
                    "process_sts" => "NO",
                    "process_msg" => "No dealers are assigned under this sales promoter."
                );
            }

        } else {

            $res_msg = array(
                "process_sts" => "NO",
                "process_msg" => "NOT VALID USER"
            );
        }
    }

} else {

    $res_msg = array(
        "process_sts" => "NO",
        "process_msg" => "Please enter Dealer ID and Mobile Number."
    );
}

echo json_encode($res_msg);

mysql_close();
?>
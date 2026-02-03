<?php
ini_set('memory_limit', '99999M');
set_time_limit(0);
function send_the_mail2($to_email, $subject, $bodyml)
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
            $mail->IsSMTP();
            //$mail->SMTPDebug  = SMTP::DEBUG_OFF;
			$mail->SMTPDebug  = 1;
            // 1 = errors and messages
            // 2 = messages only
            $mail->SMTPAuth   = true;                  // enable SMTP authentication
            $mail->SMTPSecure = "tls";
            $mail->Host       = "cloudmail2.up99plus.com";
            $mail->Port       = 587;
            $mail->Username   = "starcement@cloudmail.up99plus.com";	
            $mail->Password   = "K2TTvLxATyULV2um";
            $mail->SetFrom('starcement@cloudmail.up99plus.com', 'Starsaathi');
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
$to_email = "mridu@forcepower.in";
$subject = "test subject star";
$bodyml = "Test Body";
$send_mail = send_the_mail2($to_email, $subject, $bodyml);
?>
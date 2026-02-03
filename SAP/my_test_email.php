<?php
function send_the_mail($to_email, $subject, $bodyml)
{
    error_reporting(E_STRICT);
    set_time_limit(0);
    date_default_timezone_set("Asia/Kolkata");
    require_once('class.phpmailer.php');
    require_once('class.smtp.php');
    $sts = "FALSE";
    $to_email_arr = array();
   // $to_email = $to_email ? trim($to_email) : "";
   $to_email = "test@yopmail.com";
    $subject = $subject ? trim($subject) : "";
    $bodyml = $bodyml ? trim($bodyml) : "";
    if ($to_email != "" && $subject != "" && $bodyml != "") {
        $to_email_arr = explode(",", $to_email);
        if (count($to_email_arr) > 0) {
            $mail             = new PHPMailer();
            $bodyml             = $bodyml;
            //$bodyml             = eregi_replace("[\]",'',$bodyml);
            $mail->IsSMTP(); // telling the class to use SMTP
            $mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)                                       // 2 = messages only
            $mail->SMTPAuth   = true;                  // enable SMTP authentication                // sets the prefix to the servier
            $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server (For gmail "mail.coral.in")     // sets GMAIL as the SMTP server (For gmail "mail.coral.in")
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;                  // set the SMTP port for the GMAIL server (For gmail 465 )
            $mail->Username   = 'test.sbinfowaves@gmail.com';
            $mail->Password   = 'dzltchhdafyfnqhh';
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
			echo"<pre>";print_r($mlsts);die;
            if (!$mlsts) {
                $sts = "FALSE";
            } else {
                $sts = "TRUE";
            }
        }
    }
    return $sts;
}
 
$email_body = '<br><b>App Order No: </b> 1243243<br>
					<b>DATE: </b> srgsgf<br>
					<b>Branch Name: </b> xffgdf<br>
					<b>Customer Name: </b> 346dfgdf<br>
					<b>Consignee Name: </b> retret555<br>
					<b>Consignee Address: </b> xfgfg4646<br>
					<b>Freight: </b> fgfg4545<br>
					<b>Destination: </b> fxgxfg5656<br>
					<b>Product Name: </b> fxgxfg5656<br>
					<b>qty (MT): </b> fxgxfg5656<br>
					<b>Phone No.: </b> fxgxfg5656<br>
					<b>Dump Status: </b> fxgxfg5656<br>
					<b>Dump Name: </b> fxgxfg5656<br>
					<b>Dealer Truck: </b> fxgxfg5656<br>';
					$email_body = "Test mail body";
//$to_email = "suranjitd@coral.in";
//$to_email = "suman.koley@sbinfowaves.com";
$to_email = "souvik.pal@sbinfowaves.in";
//$to_email = "";

$email_subject = "Test email STAR";		
$res_mail = send_the_mail($to_email,$email_subject,$email_body);
echo $res_mail;
?>
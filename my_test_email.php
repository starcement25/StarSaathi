<?php
function send_the_mail($to_email, $subject, $bodyml, $attachment_path = null)
{
    $sts = "FALSE";
    date_default_timezone_set("Asia/Kolkata");
 
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->SMTPDebug  = 2;
        $mail->Debugoutput = 'html';
        $mail->SMTPAuth   = true;
        $mail->Host       = "smtp.gmail.com";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->Username   = 'test.sbinfowaves@gmail.com';
        $mail->Password   = 'dzltchhdafyfnqhh'; // ⚠️ Consider using env vars
        $mail->setFrom('sfa@starcement.co.in', 'SFA');
 
        $to_email_arr = explode(",", $to_email);
        foreach ($to_email_arr as $email) {
            if (filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                $mail->addAddress(trim($email));
            }
        }
 
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $bodyml;
        $mail->AltBody = strip_tags($bodyml);
 
        if ($attachment_path && file_exists($attachment_path)) {
            $mail->addAttachment($attachment_path);
        }
 
        $mail->send();
        $sts = "TRUE";
    } catch (Exception $e) {
        echo " PHPMailer Error: " . $mail->ErrorInfo . "<br>";
    }
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
//$to_email = "suranjitd@coral.in";
$to_email = "suman.koley@sbinfowaves.com";
$email_subject = "Test email STAR";		
$res_mail = send_the_mail($to_email,$email_subject,$email_body);

echo $res_mail;
?>
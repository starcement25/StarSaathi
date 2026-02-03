<?php
// ERROR REPORTING
error_reporting(E_ALL);
ini_set('display_errors', '1');

include "star_connection.php";

$res = send_the_mail("soumikh@coral.in", "Test EMail", "Test Email Body");
echo $res;

// include "emails/PHPMailer/PHPMailerAutoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


    //Create a new PHPMailer instance
    $mail = new PHPMailer(); 

    $mail->IsSMTP(); 
    $mail->SMTPDebug = 1; 
    $mail->SMTPAuth = true; 
    $mail->SMTPSecure = 'tls';
    $mail->Host = "smtp.gmail.com";

    $mail->Port = 587; 
    $mail->IsHTML(true);
    //Username to use for SMTP authentication
    $mail->Username = "starsaathi@starcement.co.in";
    $mail->Password = "Domain@5470";
    //Set who the message is to be sent from
    $mail->setFrom('starsaathi@starcement.co.in', 'Starcement Admin');
    //Set an alternative reply-to address
    $mail->addReplyTo('starsaathi@starcement.co.in', 'Starcement Admin');
    //Set who the message is to be sent to
    $mail->addAddress('soumikh@coral.in', 'Soumik');
    //Set the subject line
    $mail->Subject = 'PHPMailer SMTP test';
    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    $mail->msgHTML("convert HTML into a basic plain-text alternative body");
    //Replace the plain text body with one created manually
    $mail->AltBody = 'This is a plain-text message body';

    //send the message, check for errors
    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        echo "Message sent!";
}
?>

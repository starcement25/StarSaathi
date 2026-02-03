<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


include "star_connection.php";
$message='Test Email 05-08-25';
$subject='Test';
$final_email='suman.koley@sbinfowaves.com';
//echo $send_mail = send_the_mail($final_email,$subject,$message);

$to = "suman.koley@sbinfowaves.com";
$subject = "Test Email from PHP";
$message = "Hello,\nThis is a test email sent using PHP mail().";
$headers = "From: noreply@dev.starsaathi.com";

if(mail($to, $subject, $message, $headers)) {
    echo "Email sent successfully!";
} else {
    echo "Email sending failed.";
}
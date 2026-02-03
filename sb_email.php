<?php 
$to      = 'suman.koley@sbinfowaves.com';
$subject = 'Test email from dev.starsaathi.com';
$message = 'This is a test email sent directly from PHP mail().';
$headers = 'From: noreply@dev.starsaathi.com' . "\r\n" .
           'Reply-To: noreply@dev.starsaathi.com' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

$mail_sent = mail($to, $subject, $message, $headers);

if ($mail_sent) {
    echo "Mail sent successfully!";
} else {
    echo "Mail sending failed.";
}
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__.'/phpmailer_new/PHPMailer.php';
require __DIR__.'/phpmailer_new/SMTP.php';
require __DIR__.'/phpmailer_new/Exception.php';

// DB connect (PHP 5.6 mysql)
include "star_connection.php";

// Fetch 10 pending emails
$result = mysql_query("SELECT * FROM email_queue WHERE status='PENDING' LIMIT 50");

while ($row = mysql_fetch_assoc($result)) {
    $mail = new PHPMailer(true);
    try {
        // SMTP Config
        $mail->isSMTP();
        $mail->Host = "cloudmail2.up99plus.com";
        $mail->Port = 25;
        $mail->SMTPAuth = true;
        $mail->Username = "starcement@cloudmail.up99plus.com";
        $mail->Password = "K2TTvLxATyULV2um";
        $mail->SMTPSecure = false;
        $mail->SMTPAutoTLS = false;

        // From & To
        $mail->setFrom('starcement@cloudmail.up99plus.com', 'Starsaathi');
        $mail->addAddress($row['recipient']);

        // Message
        $mail->isHTML(true);
        $mail->Subject = $row['subject'];
        $mail->MsgHTML($row['body']);

        // Send
        $mail->send();

        // Update status + send time
        $id = intval($row['id']);
         date_default_timezone_set("Asia/Kolkata");
        $sent_at    = date("Y-m-d H:i:s");
        mysql_query("UPDATE email_queue SET status='SENT', sent_at='$sent_at' WHERE id=$id");

    } catch (Exception $e) {
        $id = intval($row['id']);
        $err = mysql_real_escape_string($mail->ErrorInfo);
        mysql_query("UPDATE email_queue SET status='FAILED', error_message='$err' WHERE id=$id");
    }
}
echo date('Y-m-d H:i:s'); 
echo 'Process done';
mysql_close($link);

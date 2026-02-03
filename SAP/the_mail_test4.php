<?php
include 'star_connection.php';
$to_email = "soumikh@coral.in";
$subject = "Test Mail Subject from Star Saathi";
$bodyml = "Test Mail Body from Star Saathi";
$resml = send_the_mail($to_email, $subject, $bodyml);
echo $resml;
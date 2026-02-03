<?php
// test mail function
include 'star_connection.php';

$email = "mridu@forcepower.in";

$subject = "Test Mail TLS";

$message = "This is a test mail TLS";

send_the_mail($email, $subject, $message);

echo "Mail sent";
?>
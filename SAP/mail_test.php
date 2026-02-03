<?php
error_reporting(E_STRICT);
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\Exception;	
function send_the_mail($to_email,$subject,$bodyml){

//require_once('class.phpmailer.php');
//require_once('class.smtp.php');
//require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';	

$sts = "FALSE";
$to_email_arr = array();
$to_email = $to_email ? trim($to_email) : "";
$subject = $subject ? trim($subject) : "";
$bodyml = $bodyml ? trim($bodyml) : "";
if($to_email!="" && $subject!="" && $bodyml!=""){
$to_email_arr = explode(",",$to_email);
if(count($to_email_arr)>0){
$mail             = new PHPMailer();
$bodyml             = $bodyml;
$mail->IsSMTP(); // telling the class to use SMTP
$mail->SMTPDebug = 1; 				   // enables SMTP debug information (for testing)
										   // 1 = errors and messages
										   // 2 = messages only
//$mail->Host       	= "mail.google.com"; 
$mail->SMTPKeepAlive = true;	
$mail->SMTPDebug  = "0";                     // enables SMTP debug information (for testing)
                                           // 1 = errors and messages
$mail->SMTPAuth   = true;                  // enable SMTP authentication
$mail->SMTPSecure = "TLS";                 // sets the prefix to the servier
$mail->Host       = "smtp.gmail.com"; 		// sets GMAIL as the SMTP server (For gmail "mail.coral.in")
$mail->Port       = 587;     				// set the SMTP port for the GMAIL server (For gmail 465 )
$mail->Username   = "starsaathi2@starcement.co.in";
$mail->Password   = "Domain@5470";
$mail->SetFrom('starsaathi2@starcement.co.in', 'Starsaathi');
$mail->Subject    = $subject;
$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
$mail->MsgHTML($bodyml);
foreach($to_email_arr as $to_email_arr_val){
	if(trim($to_email_arr_val)!=""){
	if (filter_var(trim($to_email_arr_val), FILTER_VALIDATE_EMAIL)) {
		$mail->AddAddress(trim($to_email_arr_val), $to_email_arr_val);
	}
	}
}
$mlsts = $mail->Send();
if(!$mlsts) {
  $sts = "FALSE";
} else {
 $sts = "TRUE";
}
// show all phpmailer configurations
echo "<pre>";
print_r($mail);
echo "</pre>";
	$mail->SmtpClose();
}
}
return $sts;
}
//$to_email = "suranjitd@coral.in";
//$to_email = "manishranjan@starcement.co.in";
// $to_email = "dipankarc@coral.in";
$to_email = "abhishekd@coral.in";
$subject = "Test Subject STARSAATHI2";
$bodyml = "Test Message STARSAATHI";
$start_time = date("Y-m-d H:i:s");
$mres = send_the_mail($to_email,$subject,$bodyml);
$end_time = date("Y-m-d H:i:s");
echo $mres;
// show start and end time
echo "<br>Start Time : ".$start_time;
echo "<br>End Time : ".$end_time;
// total time taken
$start_time = strtotime($start_time);
$end_time = strtotime($end_time);
$total_time = $end_time - $start_time;
echo "<br>Total Time : ".$total_time." Seconds";
//echo $order_date = date("Y-m-d H:i:s");
?>
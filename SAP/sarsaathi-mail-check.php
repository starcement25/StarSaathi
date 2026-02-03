<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include "star_connection.php";
function return_employee_upper_hierarchy($emp_code) {
    $emphierarchy = array();
    employee_upper_hierarchy_details($emp_code, $emphierarchy);
	foreach($emphierarchy as $hierarchyval)
	{
		$emphierarchystring.=$hierarchyval.',';
	}
	$emphierarchystring=substr($emphierarchystring,0,-1);
	$emphierarchystring=$emphierarchystring.','."'".$emp_code."'";
    return $emphierarchystring;
}
function employee_upper_hierarchy_details($emp_code,&$emphierarchy){
  $sqlemphierarchy="SELECT reporting_to FROM employee_master WHERE emp_code='".$emp_code."' AND reporting_to <>''";
   $rsemphierarchy=mysql_query($sqlemphierarchy);
   $cntemphierarchy=mysql_num_rows($rsemphierarchy);
	if($cntemphierarchy>0)
	{
		while($rowemphierarchy=mysql_fetch_array($rsemphierarchy))
		{
			$reporting_to=$rowemphierarchy['reporting_to'];
			if(strpos($reporting_to,',')!=false){
				$reporting_to_Arr=explode(',',$reporting_to);
			
				for($cn=0;$cn<count($reporting_to_Arr);$cn++)
				{
					$emphierarchy[] = "'".$reporting_to_Arr[$cn]."'";
					employee_upper_hierarchy_details($reporting_to_Arr[$cn],$emphierarchy);
				}
			}
			else
			{
				$emphierarchy[] = "'".$reporting_to."'";
				employee_upper_hierarchy_details($reporting_to,$emphierarchy);
			}
		}
	}
	else
	{
		if(!in_array("'".$emp_code."'",$emphierarchy))
		{
			$emphierarchy[] ="'".$emp_code."'";
		}
	}
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
/*function send_the_mail($to_email, $subject, $bodyml){
error_reporting(E_STRICT);
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
require __DIR__.'/phpmailer_new/PHPMailer.php';
require __DIR__.'/phpmailer_new/SMTP.php';
require __DIR__.'/phpmailer_new/Exception.php';
    
    $sts = "FALSE";
    $to_email_arr = array();
    $to_email = $to_email ? trim($to_email) : "";
    $subject = $subject ? trim($subject) : "";
    $bodyml = $bodyml ? trim($bodyml) : "";
    if ($to_email != "" && $subject != "" && $bodyml != "") {
        $to_email_arr = explode(",", $to_email);
        if (count($to_email_arr) > 0) {
$mail = new PHPMailer(true);
try {
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host = "cloudmail2.up99plus.com";
    $mail->Port = 25;
    $mail->SMTPAuth = true;
    $mail->Username = "starcement@cloudmail.up99plus.com";
    $mail->Password = "K2TTvLxATyULV2um"; // Insert your actual password here
    $mail->SMTPSecure = false; // Explicit TLS encryption is not used
    $mail->SMTPAutoTLS = false; // Disable automatic TLS upgrade
    // Sender information
    $mail->setFrom('starcement@cloudmail.up99plus.com', 'Starsaathi');
    // Recipient
	foreach ($to_email_arr as $to_email_arr_val) {
	if (trim($to_email_arr_val) != "") {
	if (filter_var(trim($to_email_arr_val), FILTER_VALIDATE_EMAIL)) {
	$mail->addAddress(trim($to_email_arr_val), $to_email_arr_val);
	}
	}
	}
			
    // Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    //$mail->Body = $bodyml;
	$mail->MsgHTML($bodyml);
    $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
    // Send the email
    $mail->send();    
    $sts = "TRUE";
} catch (Exception $e) {
    $sts = "FALSE";
}

        }
    }
    return $sts;
}*/
$SAP_customer_code='1000001734';
$customer_code='C/0001437';
echo $sqldealeremp="SELECT emp_code FROM customer_route_emp_relation WHERE acedns='Y' AND customer_code='".$SAP_customer_code."'";
		$rsdealeremp=mysql_query($sqldealeremp);
		$email_hierarchy='';
		while($rowdealeremp=mysql_fetch_array($rsdealeremp))
		{
			echo $emp_code_db=$rowdealeremp['emp_code'];

			echo $employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code_db);
		//$emp_hierarchy_condition='c1.emp_code IN('.$employee_hierarchy.')';
			echo $sqlemailhierarchy="SELECT email,designation FROM employee_master WHERE emp_code IN (".$employee_upper_hierarchy.") AND UPPER(sale_access)='PRIMARY' AND acedns='Y'";
			$rsemailhierarchy=mysql_query($sqlemailhierarchy);
			while($rowemailhierarchy=mysql_fetch_array($rsemailhierarchy))
			{
				if($rowemailhierarchy['email']!='')
				{
				$email_hierarchy=$email_hierarchy.$rowemailhierarchy['email'].',';
				}
			}
		}
		$email_broker='';

		$sqlbroker="SELECT broker_code FROM customer_broker_relation WHERE acedns='Y' AND customer_code='".$customer_code."'";
		$rsbroker=mysql_query($sqlbroker);
		while($rowbroker=mysql_fetch_array($rsbroker))
		{
			$sqlemailbroker="SELECT mail_id FROM broker_master WHERE broker_id='".$rowbroker['broker_code']."' AND acedns='Y'";
			$rsemailbroker=mysql_query($sqlemailbroker);
			while($rowemailbroker=mysql_fetch_array($rsemailbroker))
			{
				$email_broker=$email_broker.$rowemailbroker['mail_id'].',';
			}
		}
		$curr_date_format = date("jS M, y",strtotime($order_date));
		//$final_email=substr($ownempmailstring,0,-1).','.substr($reportingmailstring,0,-1).','.'dipankarc@coral.in'.','.'manishranjan@starcement.co.in'.','.'dipankarc@coral.in'.','.'mridu@forcepower.in';

$final_email=substr($email_hierarchy,0,-1).','.substr($email_broker,0,-1).','.'samirdas@starcement.co.in,antarabanerjee@starcement.co.in,pratipbhunia@starcement.co.in';
		
	/*if($email_hierarchy!=''){
	$final_email=substr($email_hierarchy,0,-1).','.substr($email_broker,0,-1).','.'samirdas@starcement.co.in,antarabanerjee@starcement.co.in,pratipbhunia@starcement.co.in';
	}
	else 	$final_email='samirdas@starcement.co.in,abhishekd@coral.in';*/
	//$final_email='dipankarc@coral.in';
echo $final_email;
		$curr_date_format = date("jS M, y",strtotime($order_date));
		//$final_email=substr($ownempmailstring,0,-1).','.substr($reportingmailstring,0,-1).','.'dipankarc@coral.in';
		//echo $final_email=substr($email_hierarchy,0,-1).','.','.','.substr($email_broker,0,-1).','.'dipankarc@coral.in';
		/*$subject = "Order for branch ".strtoupper($branch_name)." ";
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$location_date=$year.$month.$date.$hour.$minute.$second;
		$message="Order received from $customer_name @ $hour:$minute\n";
		$message.= '<br><b>App Order No: </b> '.$apporderno.'<br>
					<b>DATE: </b> '.$curr_date_format.'<br>
					<b>Branch Name: </b> '.strtoupper($branch_name).'<br>
					<b>Customer Name: </b> '.$customer_name.'<br>
					<b>Consignee Name: </b> '.$consignee_name.'<br>
					<b>Consignee Address: </b> '.$consignee_address.'<br>
					<b>Freight: </b> '.$freight.'<br>
					<b>Destination: </b> '.$destination_name.'<br>
					<b>Product Name: </b> '.$prod_desc.'<br>
					<b>qty (MT): </b> '.$qty.'<br>
					<b>Phone No.: </b> '.$phone_no.'<br>
					<b>Dump Status: </b> '.$dump_status.'<br>
					<b>Dump Name: </b> '.$dump_name.'<br>
					<b>Dealer Truck: </b> '.$dealer_truck.'<br>';
		$send_mail = send_the_mail($final_email,$subject,$message);*/



$subject='Test mail from STARSAATHI';
$message='Message Test mail from STARSAATHI';
//$final_email='dipankarc@coral.in,';
//$final_email='dipsome2006@gmail.com';
//send_the_mail($final_email,$subject,$message);
?>
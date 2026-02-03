<?php

$to      = "suranjitd@coral.in";
$message = '<br><b>App Order No: </b> '.$apporderno.'<br>
<b>DATE: </b> '.$curr_date_str.'<br>
<b>Branch Name: </b> '.$the_fetched_branch_name.'<br>
<b>Customer Name: </b> '.$fetched_customer_name.'<br>
<b>Consignee Name: </b> '.$consignee_name.'<br>
<b>Consignee Address: </b> '.$consignee_address.'<br>
<b>Destination: </b> '.$destination_name.'<br>
<b>Product Name: </b> '.$prod_dtld_desc.'<br>
<b>qty (MT): </b> '.$qty.'<br>';
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\n";
$headers .= "From: Starsaathi <starsaathi@starcement.co.in> \r\n" ."CC: starsaathi@gmail.com ".' X-Mailer: PHP/' . phpversion();
$ml = mail($to, $subject, $message, $headers);
if($ml){
	echo "Sent";
}else{
	echo "Not sent";
}
?>
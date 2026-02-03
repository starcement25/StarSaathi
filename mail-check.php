<?php 
	require("include/config-email-setup.php");
	$date=gmdate('d',strtotime('+329 minute'));
	$month=gmdate('m',strtotime('+329 minute'));
	$year=gmdate('Y',strtotime('+329 minute'));

	$hour=gmdate('H',strtotime('+329 minute'));
	$minute=gmdate('i',strtotime('+329 minute'));
	$second=gmdate('s',strtotime('+329 minute'));
	$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;  
   	//$email   ='export@tttextiles.com'; 
	//$subject ='TT mail checking again on'.$location_date;
	$email   ='dipankarc@coral.in'; 
	$subject ='Testing checkbox in mail';
	
	$message="<html><head><title>Order approval</title></head>
				<body>
				<form name='orderapproval' action='http://www.acedns.in/acednsproduct/update-order.php' method='POST'>
				<table wiidth='100%' border=1 style=background-color:AliceBlue>
				<tr>
				<th style='width:350px;min-height:21px;text-align:center'><strong><span style='font-size:10pt;font-family:Arial 
				CE'>Order Approval</span></strong></th>
				</tr>
				<tr>
					<td style='width:350px;text-align:center;min-height:91px;background-color:white;padding-left:10 px;'>
						<input type='radio' name='approval' value='approved' /> Approved
						<input type='radio' name='approval' value='notapproved' /> Not Approved
						
						<input type='submit' name='Submit' value='save' />
					</td>
				</tr>
				</table></form></body></html>";   

			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Reply-To:".FROMEMAIL." \r\n" .
						'X-Mailer: PHP/' . phpversion();
	//echo $message;

	$flgSend=mail($email, $subject, $message, $headers,'-facedns@coral.in');
	if($flgSend)
		{
			echo "success.";
		}
		else
		{
			echo "failure.";
		} 
?>
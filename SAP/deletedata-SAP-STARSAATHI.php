<?php
	set_time_limit(0);
	ini_set('memory_limit', '-1');

	define("SERVERREMOTE","103.87.174.95");
	define("USERREMOTE","starsaat_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234#");
	define("DBREMOTE","starsaathi_STARS");
		
	$link=mysql_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die(mysql_error()."Database Connection Error.");
	mysql_select_db(DBREMOTE,$link) or die(mysql_error()."could not connect the database");
	
	function send_the_mail($to_email,$subject,$bodyml){
		error_reporting(E_STRICT);
		set_time_limit(0);
		date_default_timezone_set("Asia/Kolkata");
		require_once('class.phpmailer.php');
		require_once('class.smtp.php');
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
		//$bodyml             = eregi_replace("[\]",'',$bodyml);
		$mail->IsSMTP(); // telling the class to use SMTP
		//$mail->Host       = "mail.starsaathi.com"; // SMTP server (For gmail "mail.coral.in")
		$mail->Host       = "mail.starcement.co.in";
		$mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
												   // 1 = errors and messages
												   // 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		//$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
		//$mail->Host       = "103.87.174.95";      // sets GMAIL as the SMTP server (For gmail "mail.coral.in")
		$mail->Host       = "96.45.76.75";      // sets GMAIL as the SMTP server (For gmail "mail.coral.in")
		$mail->Port       = 587;                   // set the SMTP port for the GMAIL server (For gmail 465 )
		$mail->Username   = "starsaathi-starcement";  // GMAIL username
		$mail->Password   = "BVhf@_745hw";            // GMAIL password
		
		$mail->SetFrom('starsaathi@starcement.co.in', 'Starsaathi');
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
		}
		}
		return $sts;
	}

	
	$dns_customer_code_array=array();
	$sqlcustomerSAP="SELECT KUNNR,ALTKN,NAME1,NAME2,NAME3,STRAS,STREET1,STREET2,STREET3,ORT01,ORT02,PSTLZ,TELF2,
					CREDIT_LIMIT,SMTP_ADDR,VKGRP,KDGRP,PAN_NUMBER,MIN_STOCK_QTY,ACCOUNT_NO,CITY2,COUNTER_POT,ZZONE,LZONE,VTEXT,`SEARCH_TERM2`,
					AUFSD,`REGION`,AEDAT,ADDITIONAL_DATA1 
					FROM ptblcustomermaster  ORDER BY KUNNR,AEDAT ASC,ADDITIONAL_DATA1 ASC ";
	$rscustomerSAP=mysql_query($sqlcustomerSAP);
	$countcustomerSAP=mysql_num_rows($rscustomerSAP);
	if($countcustomerSAP >0)
	{
		while($rowcustomerSAP=mysql_fetch_array($rscustomerSAP))
		{
			$dns_customer_code=$rowcustomerSAP['KUNNR'];
			if(!in_array($dns_customer_code,$dns_customer_code_array))
				{
					array_push($dns_customer_code_array,$dns_customer_code);
				}
			${AEDAT.$dns_customer_code}=$rowcustomerSAP['AEDAT'];
			${ADDITIONAL_DATA1.$dns_customer_code}=$rowcustomerSAP['ADDITIONAL_DATA1'];
		}
		foreach($dns_customer_code_array as $dns_customer_code_val){
				$customer_id=$dns_customer_code_val;
				//echo 'dfdfdfdfdfdf'.$dns_customer_code;
				$AEDAT=${AEDAT.$dns_customer_code_val};
				$ADDITIONAL_DATA1=${ADDITIONAL_DATA1.$dns_customer_code_val};
				
				$sqldeletedatewise="DELETE FROM ptblcustomermaster WHERE KUNNR='".$customer_id."' AND AEDAT!='".$AEDAT."' 
								AND ADDITIONAL_DATA1!='".$ADDITIONAL_DATA1."'";
				$rsdeletedatewise=mysql_query($sqldeletedatewise);
				$sqldeletetimewise="DELETE FROM ptblcustomermaster WHERE KUNNR='".$customer_id."' AND ADDITIONAL_DATA1!='".$ADDITIONAL_DATA1."'";
				$rsdeletetimewise=mysql_query($sqldeletetimewise);
				//exit();
			}
		$res_msg = array("process_sts"=>"YES","process_msg"=>"Deletion Successful.");
		
		$subject='STARSAATHI Data clean mail';
		$message='STARSAATHI Ptblcustomermaster data clean done';
		$final_email='dipankarc@coral.in';
		//$final_email='dipsome2006@gmail.com';
		send_the_mail($final_email,$subject,$message);
	}
	else {
		$res_msg = array("process_sts"=>"NO","process_msg"=>"SOMETHING WENT WRONG.");
	}
	 echo json_encode($res_msg);
	mysql_close();
?>

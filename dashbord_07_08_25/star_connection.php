<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
date_default_timezone_set("Asia/Kolkata");
$servername = "52.66.97.178";
$username = "root";
$password = "UZzRXN4CMJtOaUq7";
$db_name = "starsaathi_STARS";

$conn = mysql_connect($servername, $username, $password);
if(!$conn){
   die('Could not connect: ' . mysql_error());
}

$db_selected = mysql_select_db($db_name, $conn);
if (!$db_selected) {
    die ('Can\'t connect to database : ' . mysql_error());
}
function olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,$page_qury_stn_ky_nm,$filtered_query_string)
{
$pagination = "";
if($page_qury_stn_ky_nm!="page"){
$page_query_string_name = $page_qury_stn_ky_nm;
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($page > 1) 
			$pagination.= "<a href=\"$targetpage?$page_query_string_name=$prev$filtered_query_string\">PREV</a>";
		else
			$pagination.= "<span class=\"disabled_pg\">PREV</span>";
		//pages	
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination.= "<span class=\"current_pg\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage?$page_query_string_name=$counter$filtered_query_string\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current_pg\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?$page_query_string_name=$counter$filtered_query_string\">$counter</a>";					
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=$lpm1$filtered_query_string\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=$lastpage$filtered_query_string\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=1$filtered_query_string\">1</a>";
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=2$filtered_query_string\">2</a>";
				$pagination.= "...";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current_pg\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?$page_query_string_name=$counter$filtered_query_string\">$counter</a>";					
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=$lpm1$filtered_query_string\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=$lastpage$filtered_query_string\">$lastpage</a>";	
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=1$filtered_query_string\">1</a>";
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=2$filtered_query_string\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current_pg\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?$page_query_string_name=$counter$filtered_query_string\">$counter</a>";					
				}
			}
		}
		//next button
		if ($page < $counter - 1)
			$pagination.= "<a href=\"$targetpage?$page_query_string_name=$next$filtered_query_string\">NEXT</a>";
		else
			$pagination.= "<span class=\"disabled_pg\">NEXT</span>";
		$pagination.= "</div>\n";
	}
}else{
	$pagination.="Dont use query string key=page";
}
return $pagination;
}
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
$mail->Host       = "mail.starsaathi.com"; // SMTP server (For gmail "mail.coral.in")
$mail->SMTPDebug  = "";                     // enables SMTP debug information (for testing)
                                           // 1 = errors and messages
                                           // 2 = messages only
$mail->SMTPAuth   = true;                  // enable SMTP authentication
//$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
$mail->Host       = "103.87.174.95";      // sets GMAIL as the SMTP server (For gmail "mail.coral.in")
$mail->Port       = 587;                   // set the SMTP port for the GMAIL server (For gmail 465 )
$mail->Username   = "dev@starsaathi.com";  // GMAIL username
$mail->Password   = "google3d33#";            // GMAIL password

$mail->SetFrom('starsaathi@starcement.co.in', 'Starsaathi');
$mail->Subject    = $subject;
$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
$mail->MsgHTML($bodyml);
foreach($to_email_arr as $to_email_arr_val){
$mail->AddAddress($to_email_arr_val, $to_email_arr_val);
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

function send_sms_new($tmp_id,$to_mobile,$message){
$sms_res = "";
$to_mobile = $to_mobile ? trim($to_mobile) : "";
$message = $message ? trim($message) : "";
if($tmp_id!="" && $to_mobile!="" && $message!=""){
$lipl_uri = "https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to=".$to_mobile."&from=STARCM&text=".urlencode($message)."&tempid=".$tmp_id."&dlr-mask=19&dlr-url";
$lipl_ch = curl_init();
curl_setopt($lipl_ch, CURLOPT_URL, $lipl_uri);
curl_setopt($lipl_ch, CURLOPT_TIMEOUT, 20);
curl_setopt($lipl_ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($lipl_ch, CURLOPT_HEADER,0);
curl_setopt($lipl_ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($lipl_ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
$sms_res = curl_exec($lipl_ch);
curl_close($lipl_ch);

}
return $sms_res;
}

function send_sms($to_mobile,$message){
$sms_res = "";
$to_mobile = $to_mobile ? trim($to_mobile) : "";
$message = $message ? trim($message) : "";
if($to_mobile!="" && $message!=""){
$lipl_uri = "http://www.myvaluefirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to=".$to_mobile."&from=starcm&text=".urlencode($message)."&dlr-mask=19&dlr-url";
$lipl_ch = curl_init();
curl_setopt($lipl_ch, CURLOPT_URL, $lipl_uri);
curl_setopt($lipl_ch, CURLOPT_TIMEOUT, 20);
curl_setopt($lipl_ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($lipl_ch, CURLOPT_HEADER,0);
curl_setopt($lipl_ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($lipl_ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
$sms_res = curl_exec($lipl_ch);
curl_close($lipl_ch);

}
return $sms_res;
}
?>
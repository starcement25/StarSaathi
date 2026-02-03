<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
date_default_timezone_set("Asia/Kolkata");
$servername = "localhost";
$username = "starsaat_dnsprod";
$password = "dnsprod1234#";
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
function get_value_by_setting_key($keyname){
$keyvalue = "";
$setting_master = "app_setting_master";
if($keyname!=""){
$sql_gapc = "select `the_value` from $setting_master where `the_key_name`='$keyname'";
$res_gapc = mysql_query($sql_gapc);
$totres_gapc = mysql_num_rows($res_gapc);
if($totres_gapc>0){
$row_gapc=mysql_fetch_assoc($res_gapc);
$keyvalue = trim($row_gapc["the_value"]);
}
}
return $keyvalue; 
}

function get_customer_data_check_by_id($cust_id){
$customer_master = "customer_master";
$branch_credit_limit_status = "branch_credit_limit_status";
$customer_data = array("sts"=>"NO","customer_name"=>"","dns_customer_code"=>"","branch_code"=>"","is_branch_arc"=>"NO");
$custname = "";
$cust_id = $cust_id ? addslashes(trim($cust_id)) : "";
if($cust_id!=''){
	$sqls = "select `customer_name`,`dns_customer_code`,`branch_code` from $customer_master where `customer_code`='$cust_id'";
	$ress = mysql_query($sqls);
	$totress = mysql_num_rows($ress);
	if($totress>0){
		$rows = mysql_fetch_assoc($ress);
		$customer_name = $rows["customer_name"] ? trim($rows["customer_name"]) : "";
		$dns_customer_code = $rows["dns_customer_code"] ? trim($rows["dns_customer_code"]) : "";
		$branch_code = $rows["branch_code"] ? trim($rows["branch_code"]) : "";
		$customer_data["sts"] = "YES";
		$customer_data["customer_name"] = $customer_name;
		$customer_data["dns_customer_code"] = $dns_customer_code;
		$customer_data["branch_code"] = $branch_code;
		if($branch_code!=""){
			$sqls_bc = "select `arc_status` from $branch_credit_limit_status where `branch_code`='$branch_code' and `arc_status`='Y'";
			$ress_bc = mysql_query($sqls_bc);
			$totress_bc = mysql_num_rows($ress_bc);
			if($totress_bc>0){
			$customer_data["is_branch_arc"] = "YES";
			}
		}
		
	}
}
return $customer_data;
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
$mail->Host       = "mail.starcement.co.in"; // SMTP server (For gmail "mail.coral.in")
$mail->SMTPDebug  = "";                     // enables SMTP debug information (for testing)
                                           // 1 = errors and messages
                                           // 2 = messages only
$mail->SMTPAuth   = true;                  // enable SMTP authentication
//$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
//$mail->Host       = "103.87.174.95";      // sets GMAIL as the SMTP server (For gmail "mail.coral.in")
$mail->Host       = "96.45.76.75";       // sets GMAIL as the SMTP server (For gmail "mail.coral.in")
$mail->Port       = 587;                   // set the SMTP port for the GMAIL server (For gmail 465 )
//$mail->Username   = "dev@starsaathi.com";  // GMAIL username
$mail->Username   = "starsaathi-starcement";
//$mail->Password   = "google3d33#";            // GMAIL password
$mail->Password   = "BVhf@_745hw";
//$mail->SetFrom('starsaathi@starcement.co.in', 'Starsaathi');
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
?>
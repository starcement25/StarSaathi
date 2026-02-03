<?php
	
	// define("SERVERREMOTE","103.242.119.68");
	// define("USERREMOTE","acedns_dnsprod");
	// define("PASSWORDREMOTE","dnsprod1234#");
	// define("DBREMOTE","acedns_STAR");
	define("SERVERREMOTE","3.7.158.23");
	define("USERREMOTE","root");
	define("PASSWORDREMOTE","Passw0rd123#$");
	define("DBREMOTE","acedns_STAR");

	// define("SERVER","localhost");
	// define("USER","starsaat_dnsprod");
	// define("PASSWORD","dnsprod1234#");
	// define("DB","starsaat_START");
	define("SERVER","52.66.97.178");
	define("USER","admin");
	define("PASSWORD","zwPB6L65ZC}p8L89");
	define("DB","starsaat_START");

$linkremote=mysql_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die("Database Connection Error REMOTE.");
mysql_select_db(DBREMOTE,$linkremote) or die("could not connect the database");
//include "function-sfa.php";
echo"<pre>";print_r('ss');die;
$link=mysql_connect(SERVER,USER,PASSWORD,TRUE) or die("Database Connection Error.");
mysql_select_db(DB,$link) or die("could not connect the database");

function send_the_mail($to_email,$subject,$bodyml){
error_reporting(E_STRICT);
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
require_once('class.phpmailer.php');
require_once('class.smtp.php');
$sts = "FALSE";
$to_email_arr = array();
$to_email = $to_email ? trim($to_email) : "";
if($GLOBALS['send_email']!=''){
	$to_email =$GLOBALS['send_email'];
}
$subject = $subject ? trim($subject) : "";
$bodyml = $bodyml ? trim($bodyml) : "";
if($to_email!="" && $subject!="" && $bodyml!=""){
$to_email_arr = explode(",",$to_email);
if(count($to_email_arr)>0){
$mail             = new PHPMailer();
$bodyml             = $bodyml;
//$bodyml             = eregi_replace("[\]",'',$bodyml);
$mail->IsSMTP(); // telling the class to use SMTP
// $mail->Host       = "mail.starcement.co.in"; // SMTP server (For gmail "mail.coral.in")
$mail->Host       = "SMTP.gmail.com";
$mail->SMTPDebug  = "1";                     // enables SMTP debug information (for testing)
                                           // 1 = errors and messages
                                           // 2 = messages only
$mail->SMTPAuth   = true;                  // enable SMTP authentication
//$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
//$mail->Host       = "103.87.174.95";      // sets GMAIL as the SMTP server (For gmail "mail.coral.in")
// $mail->Host       = "96.45.76.75";      // sets GMAIL as the SMTP server (For gmail "mail.coral.in")
// $mail->Port       = 587;
$mail->Port       = 465;                   // set the SMTP port for the GMAIL server (For gmail 465 )	
//$mail->Username   = "dev@starsaathi.com";  // GMAIL username
//$mail->Password   = "google3d33#";            // GMAIL password
$mail->Username   = "starsaathi-starcement";
$mail->Password   = "BVhf@_745hw";
// $mail->Username   = "starsaathi@starcement.co.in";
// $mail->Password   = "Domain@5467";
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

$debugLog = 'debug.log'; // Replace with the actual path
$mail->Debugoutput = function($str, $level) use ($debugLog) {
    file_put_contents($debugLog, date('Y-m-d H:i:s') . ' ' . $str . PHP_EOL, FILE_APPEND);
};


if(!$mlsts) {
  $sts = "FALSE";
} else {
 $sts = "TRUE";
}
}
}
return $sts;
}
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
	define("SERVERREMOTE","103.242.119.68");
	define("USERREMOTE","acedns_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234#");
	define("DBREMOTE","acedns_STAR");
	$linkremote=mysql_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die("Database Connection Error REMOTE.");
	mysql_select_db(DBREMOTE,$linkremote) or die("could not connect the database");
  $sqlemphierarchy="SELECT reporting_to FROM employee_master WHERE emp_code='".$emp_code."' AND reporting_to <>''";
   $rsemphierarchy=mysql_query($sqlemphierarchy,$linkremote);
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

$sqlapporderdetails = "SELECT `id`,`APPORDERNO`,`ERPORDERNO`,`STATUS`,DATE_FORMAT(`order_date`,'%Y-%m-%d') as `orddate`,order_for,
							customer_code,dns_customer_code,prod_code,prod_display_name,QTY,freight,destination_name,phone_no,dump_status,
							dump_name,dealer_truck FROM T_APPERPDO WHERE `STATUS`='DO approved' AND  mail_sent='NO' AND SUBSTRING(order_date,1,10) >'2020-05-06'  ORDER BY `order_date` DESC";
$resapporderdetails = mysql_query($sqlapporderdetails,$link);
while($rowapporderdetails=mysql_fetch_array($resapporderdetails)){
	$id=$rowapporderdetails['id'];
	$APPORDERNO=$rowapporderdetails['APPORDERNO'];
	$ERPORDERNO=$rowapporderdetails['ERPORDERNO'];
	$STATUS=$rowapporderdetails['STATUS'];
	$orddate=$rowapporderdetails['orddate'];
	//$curr_date_format = date("jS M, y",strtotime($order_date));
	$order_for=$rowapporderdetails['order_for'];
	$customer_code=$rowapporderdetails['customer_code'];
	$dns_customer_code=$rowapporderdetails['dns_customer_code'];
	$prod_code=$rowapporderdetails['prod_code'];
	$prod_display_name=$rowapporderdetails['prod_display_name'];
	$QTY=$rowapporderdetails['QTY'];
	$freight=$rowapporderdetails['freight'];
	$destination_name=$rowapporderdetails['destination_name'];
	$phone_no=$rowapporderdetails['phone_no'];
	$dump_status=$rowapporderdetails['dump_status'];
	$dump_name=$rowapporderdetails['dump_name'];
	$dealer_truck=$rowapporderdetails['dealer_truck'];
	
	$sqlcustcode="SELECT branch_code,customer_name,customer_code FROM customer_master WHERE dns_customer_code='".$dns_customer_code."'";
	$rscustcode=mysql_query($sqlcustcode,$linkremote);
	$rowcustcode=mysql_fetch_array($rscustcode);
	$customer_code = $rowcustcode['customer_code'];
	$branch_code = $rowcustcode['branch_code'];
	$customer_name=$rowcustcode['customer_name'];
	
	$sqlbranch = "select branch_name from branch_master where branch_code='".$branch_code."'";	
	$resbranch = mysql_query($sqlbranch,$linkremote);
	$rowbranch=mysql_fetch_array($resbranch);
	$branch_name=$rowbranch['branch_name'];
	
	$email_hierarchy='';
	$email_hierarchy_array=array();
	$sqldealeremp="SELECT emp_code FROM customer_route_emp_relation WHERE acedns='Y' AND customer_code='".$customer_code."'";
	$rsdealeremp=mysql_query($sqldealeremp,$linkremote);
	while($rowdealeremp=mysql_fetch_array($rsdealeremp))
	{
		$emp_code_db=$rowdealeremp['emp_code'];
		$employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code_db);
		//$employee_upper_hierarchy='';
		$sqlemailhierarchy="SELECT DISTINCT email FROM employee_master WHERE emp_code IN (".$employee_upper_hierarchy.") AND UPPER(sale_access)='PRIMARY' AND acedns='Y' AND email!=''";
		$rsemailhierarchy=mysql_query($sqlemailhierarchy,$linkremote);
		while($rowemailhierarchy=mysql_fetch_array($rsemailhierarchy))
		{
			if(!in_array($rowemailhierarchy['email'],$email_hierarchy_array))
			{
				$email_hierarchy=$email_hierarchy.$rowemailhierarchy['email'].',';
				array_push($email_hierarchy_array,$rowemailhierarchy['email']);
			}
			
		}
	}
	$email_broker='';
		$sqlbroker="SELECT broker_code FROM customer_broker_relation WHERE acedns='Y' AND customer_code='".$customer_code."'";
		$rsbroker=mysql_query($sqlbroker,$linkremote);
		while($rowbroker=mysql_fetch_array($rsbroker))
		{
			$sqlemailbroker="SELECT mail_id FROM broker_master WHERE broker_id='".$rowbroker['broker_code']."' AND acedns='Y'";
			$rsemailbroker=mysql_query($sqlemailbroker,$linkremote);
			while($rowemailbroker=mysql_fetch_array($rsemailbroker))
			{
				$email_broker=$email_broker.$rowemailbroker['mail_id'].',';
			}
		}
	$consignee_name = "";
	$consignee_address_arr = array();
	$consignee_address = "";
	if($order_for!=""){
		if( strpos($order_for,",") !== false ) {
			$ofrarr = array();
			$ofrarr = explode(",",$order_for);
			if(count($ofrarr)>0){
				for($i=0;$i<count($ofrarr);$i++){
				
				if($i==0){
					$consignee_name = $ofrarr[$i];
				}else{
					$consignee_address_arr[] = $ofrarr[$i];
				}
				
				}
			}
		}
	}
	if(count($consignee_address_arr)>0){
		$consignee_address = implode(",",$consignee_address_arr);
	}
	$curr_date_format = date("jS M, y",strtotime($orddate));
  //$final_email=substr($ownempmailstring,0,-1).','.substr($reportingmailstring,0,-1).','.'dipankarc@coral.in';
	$final_email=substr($email_hierarchy,0,-1).','.substr($email_broker,0,-1);
	//$final_email='dipankarc@coral.in';

	$subject = "DO Approved for branch ".strtoupper($branch_name)." ";
	$message = '<br><b>App Order No: </b> '.$APPORDERNO.'<br>
				<b>ERP No: </b> '.$ERPORDERNO.'<br>
				<b>DATE: </b> '.$curr_date_format.'<br>
				<b>Branch Name: </b> '.strtoupper($branch_name).'<br>
				<b>Customer Name: </b> '.$customer_name.'<br>
				<b>Consignee Name: </b> '.$consignee_name.'<br>
				<b>Consignee Address: </b> '.$consignee_address.'<br>
				<b>Freight: </b> '.$freight.'<br>
				<b>Destination: </b> '.$destination_name.'<br>
				<b>Product Name: </b> '.$prod_display_name.'<br>
				<b>qty (MT): </b> '.$QTY.'<br>
				<b>Phone No.: </b> '.$phone_no.'<br>
				<b>Dump Status: </b> '.$dump_status.'<br>
				<b>Dump Name: </b> '.$dump_name.'<br>
				<b>Dealer Truck: </b> '.$dealer_truck.'<br>';
	if(send_the_mail($final_email,$subject,$message))
	{
		$update="UPDATE T_APPERPDO SET mail_sent='YES',mail_sent_date_time=CURRENT_TIMESTAMP() WHERE ERPORDERNO='".$ERPORDERNO."'";
		$rsemailbroker=mysql_query($update,$link);
	}
	//exit();			
}
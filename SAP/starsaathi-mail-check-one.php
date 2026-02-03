<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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

define("SERVERREMOTE","103.233.25.204");

define("USERREMOTE","acedns_dnsprod");

define("PASSWORDREMOTE","dnsprod1234#");

define("DBREMOTE","acedns_STAR");

	

$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,DBREMOTE) or die("Database Connection Error.");

//mysql_select_db(DBREMOTE,$link) or die("could not connect the database");

/*$sqlinsert="INSERT INTO orderdata set 	data='".$order_data."',insertdatetime=CURRENT_TIMESTAMP()";

mysqli_query($sqlinsert,$link);*/

$cntordata=1;

/*if(count($order_data)>0){

	foreach($order_data as $ki=>$order_data_val){

$second_ref_order_id_ck = $order_data_val["apporderno"] ? trim($order_data_val["apporderno"]) : "";

if(in_array($second_ref_order_id_ck,$not_to_use_ref_order_id)){

	

}else{*/

		$erporderno = "";

		$erporderdt = $order_data_val["erporderdt"] ? addslashes(trim($order_data_val["erporderdt"])) : "";

		$order_for = $order_data_val["order_for"] ? addslashes(trim($order_data_val["order_for"])) : "";

/* ORDER FOR TYPE */

$order_for_type = $order_data_val["order_for_type"] ? addslashes(trim($order_data_val["order_for_type"])) : "";

/*$consignee_name_of = "";

$consignee_address_arr_of = array();

$consignee_address_of = "";

if($order_for!=""){

if( strpos($order_for,",") !== false ) {

$ofrarr_of = array();

$ofrarr_of = explode(",",$order_for);

if(count($ofrarr_of)>0){

for($i=0;$i<count($ofrarr_of);$i++){

if($i==0){

$consignee_name_of = $ofrarr_of[$i];

}else{

$consignee_address_arr_of[] = $ofrarr_of[$i];

}

}

}

}else{

$consignee_name_of = $order_for;	

}

}

if(count($consignee_address_arr_of)>0){

$consignee_address_of = implode(",",$consignee_address_arr_of);

}*/

if($order_for_type=="Self"){

	$order_for_new_in = "";

}else if($order_for_type=="Sub Dealer"){

	$order_for_new_in = "";

}else if($order_for_type=="Others"){

	$order_for_new_in = $order_for;

}else{

	$order_for_new_in = $order_for;

}

$sub_dealer_code = "";

$dns_sub_dealer_code = "";

if(array_key_exists("sub_dealer_code",$order_data_val)){

$sub_dealer_code = addslashes(trim($order_data_val["sub_dealer_code"]));

if($sub_dealer_code!=""){

$sqlsubdealercode="SELECT customer_code,address,customer_name FROM customer_master WHERE dns_customer_code='".${'dns_sub_dealer_code'.$cntordata}."'";

$rssubdealercode=mysqli_query($link,$sqlsubdealercode);

$rowsubdealercode=mysqli_fetch_array($rssubdealercode);

$sub_dealer_code = $rowsubdealercode['customer_code'];

$address_sub_dealer=$rowcustcode['address'];

$sub_dealer_name=$rowcustcode['customer_name'];

}

}

		//$customer_code = $order_data_val["customer_code"] ? addslashes(trim($order_data_val["customer_code"])) : "";

		//$prod_code = $order_data_val["prod_code"] ? addslashes(trim($order_data_val["prod_code"])) : "";

		$dns_prod_code = "";

		$qty = $order_data_val["qty"] ? addslashes(trim($order_data_val["qty"])) : "";

		$freight = $order_data_val["freight"] ? addslashes(trim($order_data_val["freight"])) : "";

		//$destination_code = $order_data_val["destination_code"] ? addslashes(trim($order_data_val["destination_code"])) : "";

		$destination_name_fetched = $order_data_val["destination_name"] ? addslashes(trim($order_data_val["destination_name"])) : "";

		$destination_address = $order_data_val["destination_address"] ? addslashes(trim($order_data_val["destination_address"])) : "";

		//$dns_customer_code = show_dns_customer_from_customer_code_code($customer_code);

		//$prod_dtld = show_product_data_from_prod_code($prod_code);

		//$dns_prod_code = $prod_dtld["dns_prod_code"];

		//$prod_dtld_desc = $prod_dtld["prod_desc"];

		$dns_prod_code =${'dns_prod_code'.$cntordata};

		$dns_customer_code =${'dns_customer_code'.$cntordata};

		$dns_destination_code =${'dns_destination_code'.$cntordata};

		$apporderno =${'apporderno'.$cntordata};

		$prod_dtld_mail = ${'prod_dtld_mail'.$cntordata};



		$phone_no = $order_data_val["phone_no"] ? addslashes(trim($order_data_val["phone_no"])) : "";

		$dump_status = $order_data_val["dump_status"] ? addslashes(trim($order_data_val["dump_status"])) : "NO";

		if($dump_status==""){

		$dump_status = "NO";	

		}

		$dump_name = $order_data_val["dump_name"] ? addslashes(trim($order_data_val["dump_name"])) : "";

		$dump_code = "";

		if(array_key_exists("dump_code",$order_data_val)){

		$dump_code = addslashes(trim($order_data_val["dump_code"]));

		}

		$dealer_truck = $order_data_val["dealer_truck"] ? $order_data_val["dealer_truck"] : "NO";

		if($dealer_truck==""){

		$dealer_truck = "NO";

		}

		$order_date = date("Y-m-d H:i:s");
$dns_customer_code='S179';

		$sqlcustcode="SELECT customer_code,branch_code,customer_name,address FROM customer_master WHERE dns_customer_code='".$dns_customer_code."'";

		$rscustcode=mysqli_query($link,$sqlcustcode);

		$rowcustcode=mysqli_fetch_array($rscustcode);

		$customer_code = $rowcustcode['customer_code'];

		$branch_code = $rowcustcode['branch_code'];

		$customer_name=$rowcustcode['customer_name'];

		$address_dealer=$rowcustcode['address'];

		

		/*if($dns_sub_dealer_code!='')

		{

		$sqlsubdealercode="SELECT customer_code,address,customer_name FROM customer_master WHERE dns_customer_code='".$dns_sub_dealer_code."'";

		$rssubdealercode=mysql_query($sqlsubdealercode,$link);

		$rowsubdealercode=mysql_fetch_array($rssubdealercode);

		$sub_dealer_code = $rowsubdealercode['customer_code'];

		$address_sub_dealer=$rowcustcode['address'];

		$sub_dealer_name=$rowcustcode['customer_name'];

		}*/

		

		$sqlbranch = "select branch_name from branch_master where branch_code='".$branch_code."'";	

		$resbranch = mysqli_query($link,$sqlbranch);

		$rowbranch=mysqli_fetch_array($resbranch);

		$branch_name=$rowbranch['branch_name'];

		

		/*$sqlprod = "select prod_code,prod_desc from product_master where `dns_prod_code`='".$dns_prod_code."' AND branch_code='".$branch_code."'";	

		$resprod = mysql_query($sqlprod,$link);

		$rowprod = mysql_fetch_array($resprod);

		$prod_code = $rowprod["prod_code"];

		$prod_desc = $rowprod["prod_desc"];*/

		

		$sqldestination = "select destination_code,destination_name from destination_master where dns_destination_code='".$dns_destination_code."'";	

		$resdestination= mysqli_query($link,$sqldestination);

		$rowdestination = mysqli_fetch_array($resdestination);

		$destination_code = $rowdestination["destination_code"];

		$destination_name = $rowdestination["destination_name"];

		$destination_address = $rowdestination["destination_name"];

		

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

		if(count($consignee_address_arr)>0){

			$consignee_address = implode(",",$consignee_address_arr);

		}

		}

		else if($order_for=='' && $sub_dealer_code==''){

			$consignee_name=$customer_name;

			$consignee_address=$address_dealer;

		}

		else if($order_for=='' && $sub_dealer_code!=''){

			$consignee_name=$sub_dealer_name;

			$consignee_address=$address_sub_dealer;

		}

		

		$sqlin = "insert into $t_apperpdo (`APPORDERNO`,`ERPORDERNO`,`ERPORDERDT`,`order_date`,`order_for`,`consignee_name`,`consignee_address`,`sub_dealer_code`,`dns_sub_dealer_code`,`customer_code`,`dns_customer_code`,`prod_code`,`dns_prod_code`,`prod_display_name`,`QTY`,`freight`,`destination_code`,`destination_name`,`destination_address`,`phone_no`,`dump_status`,`dump_code`,`dump_name`,`order_from`,`order_by`,`dealer_truck`) values('$apporderno','$erporderno','$erporderdt','$order_date','$order_for_new_in','$consignee_name','$consignee_address','$sub_dealer_code','${'dns_sub_dealer_code'.$cntordata}','$customer_code','$dns_customer_code','$prod_code','$dns_prod_code','$prod_desc',

	'$qty','$freight','$destination_code','$destination_name','$destination_address','$phone_no','$dump_status','$dump_code','$dump_name','$user_type','$login_user_id','$dealer_truck')";

		//$resin = mysql_query($sqlin,$link);

		$sqlinapproval = "insert into T_APPERPDO_APPROVAL(`APPORDERNO`,`ERPORDERNO`,`ERPORDERDT`,`order_date`,`order_for`,`consignee_name`,`consignee_address`,`sub_dealer_code`,`dns_sub_dealer_code`,`customer_code`,`dns_customer_code`,`prod_code`,`dns_prod_code`,`prod_display_name`,`QTY`,`freight`,`destination_code`,`destination_name`,`destination_address`,`phone_no`,`dump_status`,`dump_code`,`dump_name`,`order_from`,`order_by`,`dealer_truck`) values('$apporderno','$erporderno','$erporderdt','$order_date','$order_for_new_in','$consignee_name','$consignee_address','$sub_dealer_code','${dns_sub_dealer_code.$cntordata}','$customer_code','$dns_customer_code','$prod_code','$dns_prod_code','$prod_desc',

	'$qty','$freight','$destination_code','$destination_name','$destination_address','$phone_no','$dump_status','$dump_code','$dump_name','$user_type','$login_user_id','$dealer_truck')";

		//$resinapproval = mysql_query($sqlinapproval,$link);

		$ownempmailstring='';

		$reportingmailstring='';

		$email_hierarchy='';

		$registrationid_array=array();

		$emp_code_array=array();

		$sqldealeremp="SELECT emp_code FROM customer_route_emp_relation WHERE acedns='Y' AND customer_code='".$customer_code."'";

		$rsdealeremp=mysqli_query($link,$sqldealeremp);

		while($rowdealeremp=mysqli_fetch_array($rsdealeremp))

		{

			$emp_code_db=$rowdealeremp['emp_code'];

			$employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code_db,$link);

		//$emp_hierarchy_condition='c1.emp_code IN('.$employee_hierarchy.')';

			$sqlemailhierarchy="SELECT email,designation FROM employee_master WHERE emp_code IN (".$employee_upper_hierarchy.") AND UPPER(sale_access)='PRIMARY' AND acedns='Y'";

			$rsemailhierarchy=mysqli_query($link,$sqlemailhierarchy);

			while($rowemailhierarchy=mysqli_fetch_array($rsemailhierarchy))

			{

				$email_hierarchy=$email_hierarchy.$rowemailhierarchy['email'].',';

			}

			$sqlregistrationid="SELECT registrationid FROM changepassword WHERE emp_code='".$emp_code_db."' AND 

			emp_code IN(SELECT emp_code FROM employee_master WHERE UPPER(sale_access)='PRIMARY' AND acedns='Y') ";

			$rsregistrationid=mysqli_query($link,$sqlregistrationid);

			$rowregistrationid=mysqli_fetch_array($rsregistrationid);

			$registrationid=$rowregistrationid['registrationid'];

			if($registrationid!='')

			{

				if(!in_array($registrationid,$registrationid_array))

				{

					array_push($registrationid_array,$registrationid);

					array_push($emp_code_array,$emp_code_db);

				}

			}

		}

		/*$sqlregistrationid="SELECT registrationid FROM changepassword WHERE emp_code='E0555'";

		$rsregistrationid=mysql_query($sqlregistrationid,$link);

		$rowregistrationid=mysql_fetch_array($rsregistrationid);

		$registrationid=$rowregistrationid['registrationid'];*/

		$email_broker='';

		$sqlbroker="SELECT broker_code FROM customer_broker_relation WHERE acedns='Y' AND customer_code='".$customer_code."'";

		$rsbroker=mysqli_query($link,$sqlbroker);

		while($rowbroker=mysqli_fetch_array($rsbroker))

		{

			$sqlemailbroker="SELECT mail_id FROM broker_master WHERE broker_id='".$rowbroker['broker_code']."' AND acedns='Y'";

			$rsemailbroker=mysqli_query($link,$sqlemailbroker);

			while($rowemailbroker=mysqli_fetch_array($rsemailbroker))

			{

				$email_broker=$email_broker.$rowemailbroker['mail_id'].',';

			}

		}

		

		$curr_date_format = date("jS M, y",strtotime($order_date));

		//$final_email=substr($ownempmailstring,0,-1).','.substr($reportingmailstring,0,-1).','.'dipankarc@coral.in'.','.'manishranjan@starcement.co.in'.','.'dipankarc@coral.in'.','.'mridu@forcepower.in';

/*$final_email=substr($email_hierarchy,0,-1).','.substr($email_broker,0,-1).','.'emovesfa@starcement.co.in'.','.'antarabanerjee@starcement.co.in';*/
	echo $final_email=substr($email_hierarchy,0,-1).','.substr($email_broker,0,-1).','.'samirdas@starcement.co.in,abhishekd@coral.in';

		$subject = "Order for branch ".strtoupper($branch_name)." ";

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

					<b>Destination: </b> '.$destination_name_fetched.'<br>

					<b>Product Name: </b> '.$prod_dtld_mail.'<br>

					<b>qty (MT): </b> '.$qty.'<br>

					<b>Phone No.: </b> '.$phone_no.'<br>

					<b>Dump Status: </b> '.$dump_status.'<br>

					<b>Dump Name: </b> '.$dump_name.'<br>

					<b>Dealer Truck: </b> '.$dealer_truck.'<br>';

		//$send_mail = send_the_mail($final_email,$subject,$message);

		

		//For Notification to ASM

		for($k=0;$k<count($registrationid_array);$k++)

		{

		//$emp_code='E0555';

		$emp_code=$emp_code_array[$k];

		$notification_id='PN'.strtoupper($emp_code).$location_date;

		$notification_type='order_punched';

		$apiKey='AAAAUCNxsT8:APA91bE-MLw1RwEHr7sZrKRmRiipBv1NnW-p6Gz1ajW_vOESL7jM3Wx0Rd0z5sUJoEM3DVqo_OS_kN0aQwXt1v1aciNzAir0FAlbCkefWvGFTDjYI-Dm7mTELSwgwCoo9zhqnHEtBkE6';

		$collapseKey=rand();

		//Title of the Notification.

		$title = "";

		//$message='There is an update please login to your APP & Press the notification button';

		

		//Creating the notification array.

		$notification = array('title' =>$title , 'body' => $message);

		

		//This array contains, the token and the notification. The 'to' attribute stores the token.

		$data= 

array('notification_id' =>$notification_id, 'notification_type' => $notification_type, 'sender_id' => 'Dealerapp', 'body' => $message); 

		//$arrayToSend = array('to' => $registrationid, 'notification' => $notification, 'data'=>$data);

		$arrayToSend = array('to' => $registrationid_array[$k], 'data'=>$data);

		

        // Set POST variables

        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = array(

            'Authorization: key=' . $apiKey,

            'Content-Type: application/json'

        );

        // Open connection

         $ch = curl_init();

        // Set the url, number of POST vars, POST data

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayToSend));

        // Execute post

        //$result = curl_exec($ch);

        /*if ($result === FALSE) {

            die('Curl failed: ' . curl_error($ch));

        }*/

		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($httpCode != 200) {    

			//request failed    

			$successval=0; 

		} 

		else

		{

			$successval=1;	

		}

        // Close connection

        curl_close($ch);

		if($successval==1)

		 {

				$sqlnotificationmaster  = "INSERT INTO notification_master ";

				$sqlnotificationmaster .= " SET notification_id='".$notification_id."'";

				$sqlnotificationmaster .= " ,type_of_notification='".$notification_type."'";

				$sqlnotificationmaster .= " ,sender_id='Dealerapp'";

				$sqlnotificationmaster .= " ,message='".$message."'";

				$sqlnotificationmaster .= " ,transferred='YES'";

				//mysql_query($sqlnotificationmaster,$link);

				$sqlnotification  = "INSERT INTO notification_ack_relation ";

				$sqlnotification .= " SET notification_id='".$notification_id."'";

				$sqlnotification .= " ,receiver_id='".$emp_code."'";

				//mysql_query($sqlnotification,$link);

		 }

		}

		/*$cntordata++;

	}
	}
}*/

mysqli_close($link);
?>
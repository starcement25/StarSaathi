<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

//echo "a".BCCEMAIL;die;

function getReverseGeo($latitude,$longitude)
{
	// format this string with the appropriate latitude longitude
	$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true";
	// make the HTTP request
	$data = @file_get_contents($url);
	// parse the json response
	$jsondata = json_decode($data,true);
	
	//print_r($jsondata);
	// if we get a formatted_address array and the status was OK, get the addres
	if(is_array($jsondata )&& $jsondata['status']=='OK')
	{
		  $addr = $jsondata['results']['0']['formatted_address'];
	}		
	return  $addr;	
}
function updateLatlong($emp_code)
{
	//For check and update in the location table
	$sqlselectlatlong="SELECT * FROM location WHERE emp_code='".$emp_code."' AND latt='0' AND longi='0'";
	$rsselectlatlong=mysql_query($sqlselectlatlong) or die(mysql_error()." Error in select zero latt longi: ".$sqlselectlatlong);
	$countselectlatlong=mysql_num_rows($rsselectlatlong);
	
	if($countselectlatlong>0)
	{
		$sqllastlatlong="SELECT latt,longi FROM location WHERE emp_code='".$emp_code."' AND latt<>'0' AND longi<>'0' ORDER BY date DESC LIMIT 0,1";
		$rslastlatlong=mysql_query($sqllastlatlong) or die(mysql_error()." Error in select last not zero latt longi: ".$sqllastlatlong);
		$rowlastlatlong=mysql_fetch_array($rslastlatlong);
		$lastlatt=$rowlastlatlong['latt'];
		$lastlongi=$rowlastlatlong['longi'];
		
		$sqlupdatelatlong="UPDATE location set latt='".$lastlatt."',longi='".$lastlongi."' WHERE emp_code='".$emp_code."' AND latt='0' AND longi='0'";
		$rsupdatelatlong=mysql_query($sqlupdatelatlong) or die(mysql_error()." Error in update zero latt longi: ".$sqlupdatelatlong);
	}
}
function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);

  $latDelta = $latTo - $latFrom;
  $lonDelta = $lonTo - $lonFrom;
  $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
	cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
  return $angle * $earthRadius;
}

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

if($nick_name=='AMPL' || $nick_name=='TT')
{
  $spam_filter='-facedns@coral.in';
}
else
{
  $spam_filter='-facedns@acedns.in';
}
/*$body=file_get_contents('php://input');
$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysql_query($sqlinsert_xml_data);*/
$body='<?xml version="1.0" encoding="UTF-8"?><root><checkout><location><emp_code><![CDATA[E0001]]></emp_code><trans_id><![CDATA[CHE000120161107134354]]></trans_id><latt><![CDATA[22.5641587]]></latt><longi><![CDATA[88.3564508]]></longi><date><![CDATA[2016-11-07 13:43:54]]></date></location><checkout_data><emp_code><![CDATA[E0001]]></emp_code><date><![CDATA[2016-11-07]]></date></checkout_data></checkout></root>';

$checkout_emp_code = "*ROOT*CHECKOUT*LOCATION*EMP_CODE";
$checkout_trans_id = "*ROOT*CHECKOUT*LOCATION*TRANS_ID";
$checkout_latt = "*ROOT*CHECKOUT*LOCATION*LATT";
$checkout_longi = "*ROOT*CHECKOUT*LOCATION*LONGI";
$checkout_date = "*ROOT*CHECKOUT*LOCATION*DATE";
$checkoutdata_emp_code = "*ROOT*CHECKOUT*CHECKOUT_DATA*EMP_CODE";
$checkoutdata_date = "*ROOT*CHECKOUT*CHECKOUT_DATA*DATE";

$checkout_array = array();

$counter = 0;

class xml_checkout{
    var $checkout_emp_code, $checkout_trans_id,$checkout_latt,$checkout_longi,$checkout_date,$checkoutdata_emp_code,$checkoutdata_date;
}
function startTag($parser, $data){
    global $current_tag;
    $current_tag .= "*$data";
}
function endTag($parser, $data){
    global $current_tag;
    $tag_key = strrpos($current_tag, '*');
    $current_tag = substr($current_tag, 0, $tag_key);
}
function contents($parser, $data){
    global $current_tag, $checkout_emp_code, $checkout_trans_id,$checkout_latt,$checkout_longi,$checkout_date,$checkoutdata_emp_code,
			$checkoutdata_date, $counter,$checkout_array;
	//echo $current_tag.'<br />';
	//echo $data;
	if(substr($current_tag,0,14)=='*ROOT*CHECKOUT')
	{
		switch($current_tag){
			case $checkout_emp_code:
				$checkout_array[$counter] = new xml_checkout();
				$checkout_array[$counter]->checkout_emp_code = $data;
				break;
			case $checkout_trans_id:
				$checkout_array[$counter]->checkout_trans_id = $data;
				break;
			case $checkout_latt:
				$checkout_array[$counter]->checkout_latt = $data;
				break;
			case $checkout_longi:
				$checkout_array[$counter]->checkout_longi = $data;
				break;
			case $checkout_date:
				$checkout_array[$counter]->checkout_date = $data;
				break;
			case $checkoutdata_emp_code:
				$checkout_array[$counter]->checkoutdata_emp_code = $data;
				break;
			case $checkoutdata_date:
				$checkout_array[$counter]->checkoutdata_date = $data;
				$counter++;
				break;
		}
	}
}
$xml_parser = xml_parser_create();
xml_set_element_handler($xml_parser, "startTag", "endTag");
xml_set_character_data_handler($xml_parser, "contents");
$data = $body;

if(!(xml_parse($xml_parser, $data, LIBXML_PARSEHUGE))){
    die("Error on line " . xml_get_current_line_number($xml_parser));
}
xml_parser_free($xml_parser);
//print_r($checkout_array);
mysql_query("SET AUTOCOMMIT=0");
mysql_query("START TRANSACTION");

$flag=1;
/* ------------------------------------------------START QUERY FOR CHECKOUT-----------------------------------------------------------------------------*/
if(count($checkout_array)>0)
{
	$checkout_date_array=array();
	for($x=0;$x<count($checkout_array);$x++){
		$checkout_emp_code=$checkout_array[$x]->checkout_emp_code;
		$checkout_trans_id=$checkout_array[$x]->checkout_trans_id;
		$checkout_latt=$checkout_array[$x]->checkout_latt;
		$checkout_longi=$checkout_array[$x]->checkout_longi;
		$checkout_date=$checkout_array[$x]->checkout_date;
		$checkoutdata_emp_code=$checkout_array[$x]->checkoutdata_emp_code;
		$checkoutdata_date=$checkout_array[$x]->checkoutdata_date;
		$checkout_date_DCR=substr($checkout_date,0,10);
		if(!in_array($checkout_date_DCR,$checkout_date_array))
		{
			array_push($checkout_date_array,$checkout_date_DCR);
		}
		//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
		if($latt>0 && $longi>0)
		{
			$sqlupdatelatlongzero="UPDATE location SET latt='".$checkout_latt."',longi='".$checkout_longi."' 
									WHERE emp_code='".$checkoutdata_emp_code."' AND latt='0' AND longi='0'";
			$resupdatelatlongzero= mysql_query($sqlupdatelatlongzero) or die(mysql_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
		}
		
		//For checking that trans id exist or not
		$sqlchkchecklocation="SELECT * FROM location WHERE trans_id='".$checkout_trans_id."'";
		$reschkchecklocation = mysql_query($sqlchkchecklocation) or die(mysql_error()." Error in check checkout location: ".$sqlchkchecklocation); 
		$rowchkchecklocation = mysql_fetch_array($reschkchecklocation);
		$countchkchecklocation=mysql_num_rows($reschkchecklocation);
		
		//For update the location table for existing trans id
		if($countchkchecklocation>0)
		{
			$sqlupdatechecklocation="UPDATE location SET emp_code='".$checkout_emp_code."',
									latt='".$checkout_latt."',
									longi='".$checkout_longi."'
									WHERE trans_id='".$checkout_trans_id."'";
			$rsupdatechecklocation=mysql_query($sqlupdatechecklocation) or die(mysql_error()." Error in update checkout location: ".$sqlupdatechecklocation);
			if($rsupdatechecklocation)
			{
				$flag=6;
			}
			else
			{
				echo $flag=0;
			}
		}
		else
		{
			// create the data for location table date field , by checking the current date and time and the actual date and time of checkout
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;

			//For Insert into the location table for new trans id			
			$sqlinsertchecklocation="INSERT INTO location SET emp_code='".$checkout_emp_code."',
									trans_id='".$checkout_trans_id."',
									latt='".$checkout_latt."',
									longi='".$checkout_longi."',
									date='".$checkout_date."',
									updatetime='".$location_date."'";
			
			//For Insert into the checkout table for new trans id
			$sqlinsertcheckout="INSERT INTO attendence SET emp_code='".$checkout_emp_code."',
								trans_id='".$checkout_trans_id."',
								 date='".$checkoutdata_date."'";	
			if(mysql_query($sqlinsertchecklocation) && mysql_query($sqlinsertcheckout))
				{
					$flag=5;
					
					$last_operation_datetime=$checkout_date;
					// For Sending email to recipents for checkout
				 	$sqlempname="SELECT emp_name,vertical_value,branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
					$rsempname=mysql_query($sqlempname);
					$rowempname=mysql_fetch_array($rsempname);
					$emp_name=title_case_emp($rowempname['emp_name']);
					$vertical_value=$rowempname['vertical_value'];
					$branch_code=$rowempname['branch_code'];
					
					if(branch_vertical_operation_wise_email=='yes')
					{
						$operation_type='Attendance';
						$checkout_email=fetch_corresponding_emails($operation_type,$vertical_value,$branch_code);
					}
					else
					{
						$checkout_email=ATTENDANCEEMAILRECIPENTS;
					}
					$address=getReverseGeo($checkout_latt,$checkout_longi);
					$checkoutemailsubj="$nick_name - Check out - ".$emp_name." on ".date('d-m-Y',strtotime($checkout_date))." @".date('H:i:s',strtotime($checkout_date)).' hrs.';
					$checkoutmailbody = "<html><head><title>Checkout</title></head>
										<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
										.$emp_name. "</b><br><br>".$emp_name." marked as cheked out on <b>".date('d-m-Y H:i:s',strtotime($checkout_date))."</b> 
										at <b>".$address."</b></table><br><br>Powered By aceDNS</body></html>";
					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=UTF-8\n";
					$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
								"Reply-To:".FROMEMAIL." \r\n" .
								"Bcc: ".BCCEMAIL." \r\n".
								'X-Mailer: PHP/' . phpversion();
					if(mail($checkout_email, $checkoutemailsubj, $checkoutmailbody, $headers,$spam_filter))
					{
						$flag=5;
					}
					else
					{
						mysql_query("ROLLBACK");
						echo $flag=0;
						return;
					}
				}
				else
				{
					mysql_query("ROLLBACK");
					echo $flag=0;
					return;
				}
			
		}// End of else
	}// End for loop
}// End checkout array if 

 /* ---------------------------------------------END QUERY FOR CHECKOUT-----------------------------------------------------------------------------------*/

if($flag==5 || $flag==6)
{
	 $sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	 $rsupdatelastoperationtime=mysql_query($sqlupdatelastoperationtime);	 
	 mysql_query("COMMIT");
	 echo $flag=1;
	 //For DCR
	$curdateserver=gmdate('Y-m-d',strtotime('+330 minute'));
	if(need_DCR=='yes' && DCR_checkout=='yes' && in_array($curdateserver,$checkout_date_array))
	{
		if($nick_name == 'PARLET'){
			$sql_emp_details = "SELECT emp_name, email FROM employee_master WHERE emp_code = '".$emp_code."'";
			$res_emp_details = mysql_query($sql_emp_details);
			$row_emp_details = mysql_fetch_array($res_emp_details);
			$emp_name = $row_emp_details['emp_name'];
			$email = $row_emp_details['email'];
			
			$sql_order_header = "SELECT order_no, SUBSTRING(order_no,2,5) as emp_code, DATE_FORMAT(SUBSTRING(order_no,-14,8),'%d-%m-%Y') as order_date, 
								customer_code,tag_distributor_code,d_instruction FROM order_header WHERE SUBSTRING(order_no,-14,8) = '".date('Ymd')."' AND order_no LIKE 'O%' AND SUBSTRING(order_no,2,5) = '".$emp_code."'";
			$res_order_header = mysql_query($sql_order_header);
			$order_header_total_rows = mysql_num_rows($res_order_header);
			if($order_header_total_rows>0){
				
				$table_data = "<table width=\"100%\" class=\"border\" border=\"1\" style=\"border-collapse:collapse;\" id=\"display_table\">
				  <tr class=\"TDHEAD\" align=\"center\">
					<td width=\"9%\">WS Name</td>
					<td width=\"8%\">Route Name</td>
					<td width=\"9%\">Customer Name</td>
					<td width=\"8%\">Order Date</td>
					<td width=\"8%\">Brand</td>
					<td width=\"8%\">Sub Brand</td>
					<td width=\"8%\">Description</td>
					<td width=\"7%\">Qty</td>
					<td width=\"7%\">Sale Rate</td>
					<td width=\"7%\">Order Value</td>
					<td width=\"\">Remarks</td>
				  </tr>";
				
				$res_order_header = mysql_query($sql_order_header);
				while($row_order_header = mysql_fetch_array($res_order_header)){
					$order_no = $row_order_header['order_no'];
					$emp_code = $row_order_header['emp_code'];
					$order_date = $row_order_header['order_date'];
					$customer_code = $row_order_header['customer_code'];
					$transaction_type = $row_order_header['transaction_type'];
					$tag_distributor_code=$row_order_header['tag_distributor_code'];
					$d_instruction=str_replace(",",";",$row_order_header['d_instruction']);
					
					$sql_customer_details = "SELECT dns_customer_code, customer_name,route_code FROM customer_master WHERE customer_code = '".$customer_code."'";
					$res_customer_details = mysql_query($sql_customer_details);
					$row_customer_details = mysql_fetch_array($res_customer_details);
					$dns_customer_code = $row_customer_details['dns_customer_code'];
					$route_code = $row_customer_details['route_code'];
					$customer_name = str_replace(",",";",$row_customer_details['customer_name']);
					
					$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route_code."'";
					$rsroutename=mysql_query($sqlroutename);
					$total_row_check = mysql_num_rows($rsroutename);
					if($total_row_check>0){
						$rsroutename=mysql_query($sqlroutename);
						$rowroutename=mysql_fetch_array($rsroutename);
						$route_name=$rowroutename['route_name'];
					}
					else{
						$sql_route_code = "SELECT route_code FROM customer_master WHERE customer_code = '".$tag_distributor_code."'";
						$res_route_code = mysql_query($sql_route_code);
						$row_route_code = mysql_fetch_array($res_route_code);
						$route_code = $row_route_code['route_code'];
						
						$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route_code."'";
						$rsroutename=mysql_query($sqlroutename);
						$rowroutename=mysql_fetch_array($rsroutename);
						$route_name=$rowroutename['route_name'];
					}
					$route_name = str_replace(",",";",$route_name);
		
					$sqldistributorname="SELECT customer_name FROM customer_master WHERE customer_code='".$tag_distributor_code."'";
					$rsdistributorname=mysql_query($sqldistributorname);
					$rowdistributorname=mysql_fetch_array($rsdistributorname);
					$distributor_name=str_replace(",",";",$rowdistributorname['customer_name']);
		
					$sql_order_details = "SELECT sku_code, qty, sale_rate, amount FROM order_details WHERE order_no = '".$order_no."'";
					$res_order_details = mysql_query($sql_order_details);
					while($row_order_details = mysql_fetch_array($res_order_details)){
						$sku_code = $row_order_details['sku_code'];
						$qty = $row_order_details['qty'];
						$sale_rate = $row_order_details['sale_rate'];
						$amount = $row_order_details['amount'];
						
						$sql_sku_name = "SELECT dns_prod_code, product_group_code, product_sub_group_code, prod_desc FROM product_master WHERE prod_code = '".$sku_code."'";
						$res_sku_name = mysql_query($sql_sku_name);
						$row_sku_name = mysql_fetch_array($res_sku_name);
						$sku_name = str_replace(",",";",$row_sku_name['prod_desc']);
						$dns_prod_code = $row_sku_name['dns_prod_code'];
						$product_group_code = $row_sku_name['product_group_code'];
						$product_sub_group_code = $row_sku_name['product_sub_group_code'];
						
						$sql_prod_group_name = "SELECT product_group_name FROM product_group_master WHERE product_group_code = '".$product_group_code."'";
						$res_prod_group_name = mysql_query($sql_prod_group_name);
						$row_prod_group_name = mysql_fetch_array($res_prod_group_name);
						$prod_group_name = str_replace(",",";",$row_prod_group_name['product_group_name']);
						
						$sql_product_subgroupname = "SELECT product_sub_group_name FROM product_sub_group_master WHERE product_sub_group_code = '".$product_sub_group_code."'";
						$res_product_subgroupname = mysql_query($sql_product_subgroupname);
						$row_product_subgroupname = mysql_fetch_array($res_product_subgroupname);
						$prod_subgroup_name = $row_product_subgroupname['product_sub_group_name'];
						
						$table_data .= "<tr>
											<td>".$distributor_name."</td>
											<td>".$route_name."</td>
											<td>".$customer_name."</td>
											<td>".$order_date."</td>
											<td>".$prod_group_name."</td>
											<td>".$prod_subgroup_name."</td>
											<td>".$sku_name."</td>
											<td align=\"right\">".$qty."</td>
											<td align=\"right\">".$sale_rate."</td>
											<td>".$amount."</td>
											<td>".$d_instruction."</td>
										  </tr>";
						$count++;
					}
				}
				$table_data .= "</table>";
			}
			else{
				$table_data = "<strong><font color=\"red\">No Records Found</font></strong>";
			}
			
						
			$address=getReverseGeo($checkout_latt,$checkout_longi);	
			$orderdetailsemailsubj="$nick_name - Order Details - ".$emp_name." on ".date('d-m-Y',strtotime($checkout_date))." @".date('H:i:s',strtotime($checkout_date)).' hrs.';
			$orderdetailsmailbody = "<html><head><title>Order Details</title></head>
								<body>This is an auto generated mail from <b>".$nick_name." aceDNS</b> mobile application from <b>"
								.$emp_name. "</b><br><br>".$emp_name." marked as cheked out on <b>".date('d-m-Y H:i:s',strtotime($checkout_date))."</b> 
								at <b>".$address."</b></table><br><br>Powered By aceDNS</body></html>";
			$strSid = md5(uniqid(time()));
			$headers='';
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Bcc: ".BCCEMAIL." \r\n" .
						'X-Mailer: PHP/' . phpversion();
			$headers .= "MIME-Version: 1.0\r\n";			
			$headers .= "Content-Type: multipart/mixed; boundary=\"".$strSid."\"\n";
			$headers .= "This is a multi-part message in MIME format.\n";
			$headers .= "--".$strSid."\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n"; // or UTF-8 //
			$headers .= "Content-Transfer-Encoding: 7bit\n";
			$strContent1 = base64_encode($table_data);
			$headers .= "--".$strSid."\n";
			$headers .= "Content-Type: application/octet-stream; name=\"order_data.xls\"\n";
			$headers .= "Content-Transfer-Encoding: base64\n";
			$headers .= "Content-Disposition: attachment; filename=\"order_data.xls\"\n";
			$headers .= $strContent1."\n";
			$mail = mail($email,$orderdetailsemailsubj,$orderdetailsmailbody,$headers,$spam_filter);
			echo "Mail Delivered";die;
		}
		
		$sql_chk_DCR_generation="SELECT emp_code FROM DCR_generation_log WHERE emp_code='".$emp_code."' AND SUBSTRING(DCR_date_time,1,10)='".$curdateserver."'";
		$rs_chk_DCR_generation=mysql_query($sql_chk_DCR_generation);
		$count_chk_DCR_generation=mysql_num_rows($rs_chk_DCR_generation);
		if($count_chk_DCR_generation==0)
		{
		$operation_type_dcr='DCR';
			$sqlempname="SELECT emp_name,branch_code,vertical_value,reporting_to FROM employee_master WHERE emp_code='".$emp_code."'";
			$rsempname=mysql_query($sqlempname);
			$rowempname=mysql_fetch_array($rsempname);
			$emp_name=$rowempname['emp_name'];
			$branch_code=$rowempname['branch_code'];
			$vertical_value=$rowempname['vertical_value'];
			$reporting_to_immediate=$rowempname['reporting_to'];

		//$dateprevious=date('Y-m-d', strtotime("-1 days,$curdateserver "));
		updateLatlong($emp_code);
		$sqlpoint="(SELECT latt, longi,trans_id,DATE_FORMAT(date,'%H:%i:%s') as trantime FROM location WHERE emp_code ='".$emp_code."' AND 
					DATE LIKE '%".$curdateserver."%' AND (SUBSTRING(trans_id,1,1) IN('A','O','P','D') OR SUBSTRING(trans_id,1,2) IN('NO','NC')) ORDER BY DATE_FORMAT(date,'%Y-%m-%d %H:%i:%s') ASC)";			
		$rspoint=mysql_query($sqlpoint);
		$countpoint=mysql_num_rows($rspoint);
		$pointcoords=array();
		$customer_name_array=array();
		$d_instruction_array=array();
		$activity_array=array();
		$total_qty_array=array();
		$total_amount_array=array();
		$trantime_array=array();
		$pathpointsArr=array();
		$order_value_array=array();
		$markersArr[]='';
		$pathpointsArr[]='';
		$pointlabel=1;
		$sqlnotes="(SELECT latt, longi,trans_id,DATE_FORMAT(date,'%H:%i:%s') as trantime FROM location WHERE emp_code ='".$emp_code."' AND 
					DATE LIKE '%".$curdateserver."%' AND SUBSTRING(trans_id,1,2) IN('NI') ORDER BY DATE_FORMAT(date,'%Y-%m-%d %H:%i:%s') ASC)";
		$rsnotes=mysql_query($sqlnotes);
		$countnotes=mysql_num_rows($rsnotes);				
		if($countpoint>0 || $countnotes > 0)
		{
			$total_amount_rawdata_array=array();
			while($rowpoint=mysql_fetch_array($rspoint))
			{
				$point=$rowpoint['latt'].','.$rowpoint['longi'];
				$markersArr[]="markers=color:red|label:$pointlabel|$point";
				$markers=implode('&',$markersArr);
				//$pathpointsArr[]=$point;
				array_push($pathpointsArr,$point);
				$pathpoints=implode('|',$pathpointsArr);
				//$pathpoints=substr($pathpoints,1);
				array_push($pointcoords,$point);
				
				$trans_id=$rowpoint['trans_id'];
				$operation_type=substr($trans_id,0,1);
				$time=$rowpoint['trantime'];
				if($operation_type=='A' && $operation_type!='N')
				{
					$activity='Attendance';
					$customer_name='----';
					$total_amount='----';
					$total_qty='----';
					$order_value='----';
				}
				if($operation_type=='O')
					{
						$activity='Order';
						$order_no=$trans_id;
						/*if(mrp=='yes'){ $mrp_salerate_condition=" SUM(OD.qty*MRP.mrp) AS order_value";}
						if(sale_rate=='yes' && sale_rate_input_dropdown=='dropdown'){$mrp_salerate_condition=" SUM(OD.qty*MRP.sale_rate) AS order_value";}*/
						$sqlcustomer="SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received,SUM(amount) AS order_value,
										OH.d_instruction FROM order_header OH,customer_master CM,order_details OD
									  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' 
									  AND OH.order_no=OD.order_no GROUP BY OD.order_no
									  UNION
									  SELECT CM.customer_name,CM.customer_code,SUM(OD.qty) AS total_order_received,SUM(amount) AS order_value,
									  OH.d_instruction FROM 
									  order_header OH,prospective_customer_master CM,order_details OD
									  WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."' 
									  AND OH.order_no=OD.order_no GROUP BY OD.order_no";
						$rscustomer=mysql_query($sqlcustomer) or die(mysql_error()." Error in select customer: ".$sqlcustomer);
						
						/*if(mrp=='yes' || (sale_rate=='yes' && sale_rate_input_dropdown=='dropdown')){
							$sqlordervalue="SELECT '".$mrp_salerate_condition."' FROM order_details OD,mrp MRP WHERE OD.sku_code=MRP.product_code 
											AND OD.order_no='".$order_no."' GROUP BY OD.order_no";
							$rsordervalue=mysql_query($sqlordervalue);
							$rowordervalue=mysql_fetch_array($rsordervalue);
							$order_value=$rowordervalue['order_value'];
						}
						else
						{
							$order_value=0;
						}*/
						$rowcustomer=mysql_fetch_array($rscustomer);
						$customer_name=$rowcustomer['customer_name'];
						$order_value=$rowcustomer['order_value'];
						$total_qty=$rowcustomer['total_order_received'];
						$total_order_value=$total_order_value+$order_value;
	
						$total_amount='----';
						$order_value=$order_value;
						$d_instruction=$rowcustomer['d_instruction'];
					}
					if($operation_type=='P')
					{
						$activity='Payment';
						$receipt_id=$trans_id;
						$sqlcustomerpayment="SELECT CM.customer_name,CM.customer_code,SUM(PD.amount) AS total_collection_received,
											PH.d_instruction FROM payment_header PH,customer_master CM,payment_details PD
											WHERE CM.customer_code=PH.customer_code AND PH.receipt_id=PD.receipt_id AND 
											PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id
											UNION
											SELECT CM.customer_name,CM.customer_code,SUM(PD.amount) 
											AS total_collection_received,PH.d_instruction 
											FROM payment_header PH,prospective_customer_master CM,payment_details PD
											WHERE CM.customer_code=PH.customer_code AND PH.receipt_id=PD.receipt_id 
											AND PH.receipt_id='".$receipt_id."' GROUP BY PD.receipt_id";
						$rscustomerpayment=mysql_query($sqlcustomerpayment) or die(mysql_error()." Error in select customer payment: 
												".$sqlcustomerpayment);
						
						$rowcustomerpayment=mysql_fetch_array($rscustomerpayment);
						$customer_name=$rowcustomerpayment['customer_name'];
						$total_amount_rawdata=$total_amount_rawdata+$rowcustomerpayment['total_collection_received'];
						$total_amount='Rs/- '.number_format($rowcustomerpayment['total_collection_received'],2);
						$total_qty='----';
						$order_value='----';
						$d_instruction=$rowcustomerpayment['d_instruction'];
					}
					if($operation_type=='N')
					{
						$operation_type_no=substr($trans_id,1,1);
						if($operation_type_no=='O')
						{
							$activity='No Order';
							$order_no=$trans_id;
							$sqlcustomernoorder="SELECT CM.customer_name,CM.customer_code,OH.d_instruction FROM order_header OH,customer_master CM 
												WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."'
												UNION
												SELECT CM.customer_name,CM.customer_code,OH.d_instruction FROM order_header OH,prospective_customer_master CM 
												WHERE CM.customer_code=OH.customer_code AND OH.order_no='".$order_no."'";
							$rscustomernoorder=mysql_query($sqlcustomernoorder) or die(mysql_error()." Error in select customer for no order: ".$sqlcustomernoorder);
							$rowcustomernoorder=mysql_fetch_array($rscustomernoorder);
	
					
							$customer_name=$rowcustomernoorder['customer_name'];
							$total_amount='----';
							$total_qty='----';
							$order_value='----';
							$d_instruction=$rowcustomernoorder['d_instruction'];
						}
						if($operation_type_no=='C')
						{
							$activity='No Collection';
							$receipt_id=$trans_id;
							$sqlcustomernocollection="SELECT CM.customer_name,CM.customer_code,PH.d_instruction FROM payment_header PH,customer_master CM 
													   WHERE CM.customer_code=PH.customer_code AND PH.receipt_id='".$receipt_id."'
													   UNION
													   SELECT CM.customer_name,CM.customer_code,PH.d_instruction FROM payment_header PH,prospective_customer_master CM 
													   WHERE CM.customer_code=PH.customer_code AND PH.receipt_id='".$receipt_id."'";
							$rscustomernocollection=mysql_query($sqlcustomernocollection) or die(mysql_error()." 
														Error in select customer for no collection: ".$sqlcustomernocollection);
							$rowcustomernocollection=mysql_fetch_array($rscustomernocollection);
							
							$customer_name=$rowcustomernocollection['customer_name'];
							$total_amount='----';
							$total_qty='----';
							$order_value='----';
							$d_instruction=$rowcustomernocollection['d_instruction'];
						}
					}
					if($operation_type=='D')
					{
						$operation_type_no=substr($trans_id,1,1);
						$sqlprospective="SELECT PCH.customer_name FROM prospective_customer_header PCH WHERE PCH.trans_id='".$trans_id."'";
						$rsprospective=mysql_query($sqlprospective) or die(mysql_error()." 
										Error in select prospective customer or mechanic: ".$sqlprospective);
						$rowprospective=mysql_fetch_array($rsprospective);
						$customer_name=$rowprospective['customer_name'];
						if($operation_type_no=='M')
							{
								$activity='Visit Mechanic';
								$total_amount='----';
								$total_qty='----';
								$order_value='----';
							}
						if($operation_type_no=='C')
							{
								$activity='Visit Customer';
								$total_amount='----';
								$total_qty='----';
								$order_value='----';
							}	
					}
					
					array_push($customer_name_array,$customer_name);
					array_push($d_instruction_array,$d_instruction);
					array_push($trantime_array,$time);
					array_push($total_qty_array,$total_qty);
					array_push($total_amount_array,$total_amount);
					array_push($activity_array,$activity);
					array_push($order_value_array,$order_value);
				$pointlabel++;
			}
			//echo $markers;
			//echo $pathpoints;
			//print_r($pointcoords);
			if(branch_vertical_operation_wise_email=='yes')
			{
				$operation_type='DCR';
				$DCR_email=fetch_corresponding_emails($operation_type,$vertical_value,$branch_code);
				if($nick_name=='AMPL')
				{
					$DCR_email=$DCR_email.',rajuyk@automotiveml.com,vaman@automotiveml.com';
				}
			}
			else
			{
				if(email_hierarchywise=='yes')
				{
				  if(email_hierarchy_level==1)
				   {
					$DCR_email='';
				   }
				  else
				  {
					$DCR_email=$email_hierarchy.','.$corresponding_emails;
				  }
				}
				else
				{
					$DCR_email=$corresponding_emails;
				}
				if($nick_name=='TT')
				{
				$DCR_email=$DCR_email.',sjain@ttlimited.co.in,jyoti@ttlimited.co.in,manojkalra@ttlimited.co.in,bcjain@ttlimited.co.in,yogesh@ttlimited.co.in';
				}
				if($nick_name=='LALANI')
				{ 
					$DCR_email=$DCR_email.',ritesh@lalaniinfotech.in';	
				}
				if($nick_name=='NAPTC')
				{
					$DCR_email=$DCR_email.',rajesh@purbanchal.in';
				}
				if($nick_name=='PARLE' || $nick_name=='PARLET')
				{
					$sqlimmediatemail="SELECT email FROM employee_master WHERE FIND_IN_SET(emp_code, '".$reporting_to_immediate."')";
					$rsimmediatemail=mysql_query($sqlimmediatemail);
					$rowimmediatemail=mysql_fetch_array($rsimmediatemail);
					$mail_immediate=$rowimmediatemail['email'];
					
					$DCR_email=$mail_immediate;
				}
			}
			
			//$DCR_email='kuntald@coral.in';
			$datesubject=date('d-m-Y', strtotime("$curdateserver "));
			if($nick_name=='RUPA')
			{
				$subject='DSR of '.$emp_name.' on '.$datesubject.' for '.$nick_name;
			}
			else
			{
				$subject='DCR of '.$emp_name.' on '.$datesubject.' for '.$nick_name;
			}
			if($nick_name=='RUPA')
			{
				$bodytext='DSR';
			}
			else
			{
				$bodytext='DCR';
			}
			if(route_plan=='yes')
			{
				
				$visit_date=date('Y-m-d', strtotime($datesubject));
				$sqlrouteplandetails="SELECT GROUP_CONCAT(DISTINCT RM.route_name SEPARATOR ',') AS route_name_details FROM route_plan RP,route_master RM 
									WHERE RP.route_code=RM.route_code AND RP.visit_date ='".$visit_date."' AND RP.emp_code='".$emp_code."'";
				$rsrouteplandetails=mysql_query($sqlrouteplandetails);
				$rowrouteplandetails=mysql_fetch_array($rsrouteplandetails);
				$route_name_details=$rowrouteplandetails['route_name_details'];				
				$routeplantext="<br /><tr><th style='min-height:21px;text-align:left;' colspan=9><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Submitted Route Plan:&nbsp;".$route_name_details."</span></strong></th></tr>";
			}
			else
			{
				$routeplantext='';
			}
	
			$body="<table>
					<tr>
					<th style='min-height:21px;text-align:center;' colspan='4'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>".$bodytext.":&nbsp;".$emp_name."</span></strong></th>
					<th style='width:70px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>&nbsp;</span></strong></th>
					<th style='width:60px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>&nbsp;</span></strong></th>
					<th style='min-height:21px;text-align:right;' colspan='2'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>DATE:&nbsp;".$datesubject."</span></strong></th>
					</tr>".$routeplantext."</table><br />";
			$body.='<img src="http://maps.googleapis.com/maps/api/staticmap?size=700x600&path=color:0xff0000ff|weight:2'.$pathpoints.'&sensor=false&'.$markers.'" alt=""><br /> <br />';
			
			for($i=0;$i<count($pointcoords);$i++)
			{
				$point=($i+1);
				
				$Arrpointcoordsfrom =explode(',',$pointcoords[$i]);
				$latitudeFrom=$Arrpointcoordsfrom[0];
				$longitudeFrom=$Arrpointcoordsfrom[1];
				
				$address=getReverseGeo($latitudeFrom,$longitudeFrom);
				
				if($i==0)
				{
					$distancetxt='---------';
				}
				else
				{
					$Arrpointcoordsfrom =explode(',',$pointcoords[$i-1]);
					$latitudeFrom=$Arrpointcoordsfrom[0];
					$longitudeFrom=$Arrpointcoordsfrom[1];
					
					$Arrpointcoordsto =explode(',',$pointcoords[$i]);
					$latitudeTo=$Arrpointcoordsto[0];
					$longitudeTo=$Arrpointcoordsto[1];
					$distance=round((haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000))/1000,2);
					$distancetxt=$distance." k.m";
				}
				$bodydetails.="<tr><td style='width:50px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$point."</span>&nbsp;</td>	
								<td style='width:100px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$customer_name_array[$i]."</span>&nbsp;</td>	
								<td style='width:150px;text-align:left;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$d_instruction_array[$i]."</span>&nbsp;</td>
								<td style='width:70px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$distancetxt."</span>&nbsp;</td>
								<td style='width:70px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$activity_array[$i]."</span>&nbsp;</td>
								<td style='width:60px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$total_qty_array[$i]."</span>&nbsp;</td>
								<td style='width:60px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".@number_format($order_value_array[$i],2)."</span>&nbsp;</td>
								<td style='width:60px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$total_amount_array[$i]."</span>&nbsp;</td>
								<td style='width:70px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$trantime_array[$i]."</span>&nbsp;</td>			
							  </tr>";
				$total_qty_val=$total_qty_val+$total_qty_array[$i];
			}
			if(notes_and_info=='yes')
			{
				$datepreviousnotesinfo=date('Ymd', strtotime("$curdateserver"));
				//$datecurrentnotesinfo=date('Ymd');
				$sqlnotesinfo="SELECT feedback FROM notes_info_details WHERE SUBSTRING(notes_info_id,3,5)='".$emp_code."' AND 
								SUBSTRING(notes_info_id,-14,8)='".$datepreviousnotesinfo."'";
				$rsnotesinfo=mysql_query($sqlnotesinfo);
				$countnotesinfo=mysql_num_rows($rsnotesinfo);
				while($rownotesinfo=mysql_fetch_array($rsnotesinfo))
				{
					$note_info_details_string.="<tr><td style='width:400px;text-align:left;min-height:21px;background-color:white'>
					<span style='font-family:Arial CE;font-size:10pt'>".$rownotesinfo['feedback']."</span>&nbsp;</td></tr>";
				}
				if($countnotesinfo >0)
				{
					$note_info_string="<table><tr><td style='width:400px;text-align:left;min-height:21px;background-color:white'>
					<span style='font-family:Arial CE;font-size:10pt'><u><b>N.B:</b></u></span>&nbsp;</td></tr>".$note_info_details_string."</table>";
				}
				else
				{
					$note_info_string='';
				}
			}
			else
			{
				$note_info_string='';
			}
			$body.="<table border=1 style=background-color:AliceBlue>
					<tr>
					<th style='width:50px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Point</span></strong></th>
					<th style='width:100px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Customer Name</span></strong></th>
					<th style='width:150px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Remarks</span></strong></th>
					<th style='width:70px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Distance</span></strong></th>
					<th style='width:70px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Activity</span></strong></th>
					<th style='width:60px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Qty</span></strong></th>
					<th style='width:60px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Order Value</span></strong></th>
					<th style='width:60px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Amount</span></strong></th>
					<th style='width:70px;min-height:21px;text-align:center'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Time</span></strong></th>
					</tr>
					".$bodydetails."<tr><td style='width:50px;min-height:21px;text-align:center' colspan='5'><strong>
					<span style='font-size:10pt;font-family:Arial CE'>Total</span></strong></td><td style='width:60px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>".$total_qty_val."</span>&nbsp;</td>
								<td style='width:60px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>Rs/- ".number_format($total_order_value,2)."</span>&nbsp;</td>
								<td style='width:60px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'>Rs/- ".number_format($total_amount_rawdata,2)."</span>&nbsp;</td><td style='width:70px;text-align:center;min-height:21px;background-color:white'>
								<span style='font-family:Arial CE;font-size:10pt'></span>&nbsp;</td></tr></table>".$note_info_string."
								<br /><br /><br />Powered By aceDNS";
			//echo $emp_name.'<br />-----------'.$body.'<br /><br /><br />';
			$sql_insert_DCR_generation="INSERT INTO DCR_generation_log SET emp_code='".$emp_code."',DCR_date_time=CURRENT_TIMESTAMP()";
			$rs_insert_DCR_generation=mysql_query($sql_insert_DCR_generation);
		
			$headersdcr  = "MIME-Version: 1.0\r\n";
			$headersdcr .= "Content-type: text/html; charset=UTF-8\n";
			$headersdcr .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Reply-To:".FROMEMAIL." \r\n" .
						"Bcc: ".BCCEMAIL." \r\n" .
						'X-Mailer: PHP/' . phpversion();
			if(mail($DCR_email, $subject, $body, $headersdcr,$spam_filter))
				{
					$pointcoords='';
					unset($pointcoords);
					$customer_name_array='';
					unset($customer_name_array);
					$d_instruction_array='';
					unset($d_instruction_array);
					$activity_array='';
					unset($activity_array);
					$total_qty_array='';
					unset($total_qty_array);
					$order_value_array='';
					unset($order_value_array);
					$total_amount_array='';
					unset($total_amount_array);
					$trantime_array='';
					unset($trantime_array);
					$markersArr='';
					unset($markersArr);
					$pathpointsArr='';
					unset($pathpointsArr);
					$body='';
					$bodydetails='';
				}
			}//End of if
		}
	}
}
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = "http://www.acedns.in/acednsproduct/operationdb-checkout-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/operationdb-checkout-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time"."\r\n";
	$insertPos=0;  // variable for saving 
	while (!feof($file)) {
		$line=fgets($file);
		if (strpos($line, 'http://')!==false) {
			$insertPos=ftell($file);
			$newline =  $newuser;
		}
		else
		{
			$newline.=$line;   // append existing data with new data of user
		}

	}
	fseek($file,$insertPos);   // move pointer to the file position where we saved above 
	fwrite($file, $newline);
	fclose($file);*/
?>

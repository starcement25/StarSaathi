<?php
include "star_connection.php";
$emp_code=$_REQUEST['emp_code'];
$user_type = $_REQUEST['user_type'] ? strtolower(trim($_REQUEST['user_type'])) : ""; /* sub dealer/dealer/broker  */
$condition_branch="";
$dealer_tour_status  ="dealer_tour_status";
$customer_master = "customer_master";

$sqldealertour="SELECT dns_customer_code FROM customer_master WHERE customer_code='".$emp_code."'";
$rsdealertour=mysql_query($sqldealertour);
$rowdealertour=mysql_fetch_array($rsdealertour);
$dns_customer_code=$rowdealertour['dns_customer_code'];

if($dns_customer_code!='')
{
	$sqlquery="SELECT * FROM $dealer_tour_status where customer_code='".$dns_customer_code."'";
	$result = mysql_query($sqlquery);
	$count=mysql_num_rows($result);
	if($count>0){
		$rowtour=mysql_fetch_array($result);
		$tourstatus=$rowtour['tour_status'];
		
		if($tourstatus=='ACTIVE')
		{
			// $tour_link='http://masonbackend.myvtd.site/star-sathi/public/tour/dealer/authenticate?dealer_code='.$dns_customer_code;
			$tour_link='http://tourinfo.starcement.co.in/tour/dealer/authenticate?dealer_code='.$dns_customer_code;
			$tour_status_data[] = array("customer_code"=>$dns_customer_code,"tour_link"=>$tour_link);
			$res_data = array("process_status"=>"YES","process_message"=>"Success.","tour_data"=>$tour_status_data);
		}
		else
		{
			$res_data = array("process_status"=>"NO","process_message"=>"Coming Soon.");
		}
		
	}
	else
	{
		$res_data = array("process_status"=>"NO","process_message"=>"Coming Soon.");
	}
}
else
{
	$res_data = array("process_status"=>"NO","process_message"=>"Dealer not found.");
}
echo json_encode($res_data);
	mysql_close($link);			
?>

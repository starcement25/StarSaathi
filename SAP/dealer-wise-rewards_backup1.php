<?php
include "star_connection.php";
$emp_code=$_REQUEST['emp_code'];
$dealer_status = $_REQUEST['the_status'];
// error_log("Received AJAX request with emp_code: " . $emp_code . " and the_status: " . $dealer_status);

$condition_branch="";
$dealer_reward_status  ="dealer_reward_status";
$customer_master = "customer_master";

// $sql_insert="INSERT INTO `dealer_reward_status` (`emp_code`, `dealer_status`)
// VALUES ('$emp_code', '$dealer_status')
// ON DUPLICATE KEY UPDATE `dealer_status` = VALUES(`dealer_status`);";

// mysql_query($sql_insert);

$sqldealertour="SELECT `dns_customer_code` FROM $customer_master WHERE `dns_customer_code`='".$emp_code."'";
$rsdealertour=mysql_query($sqldealertour);
$rowdealertour=mysql_fetch_array($rsdealertour);
$dns_customer_code=$rowdealertour['dns_customer_code'];

if($dns_customer_code!='')
{
	// echo "dns_customer_code: ".$dns_customer_code;
	$sqlquery="SELECT * FROM $dealer_reward_status where `emp_code`='".$emp_code."'";
	$result = mysql_query($sqlquery);
	$count=mysql_num_rows($result);
	// if($count>0){
		$rowtour=mysql_fetch_array($result);
		$tourstatus=$rowtour['dealer_status'];
		// echo $tourstatus;
		if($tourstatus=='ACTIVE')
		{
			$condn="YES";
			// $tour_link='http://masonbackend.myvtd.site/star-sathi/public/tour/dealer/authenticate?dealer_code='.$dns_customer_code;
			$reward_link='https://dev4.myvtd.site/laravel/star-sathi-rewards/public/tour/dealer/authenticate?dealer_code='.$dns_customer_code;
			$reward_status_data[] = array("customer_code"=>$dns_customer_code,"reward_link"=>$reward_link,"condn"=>$condn);
			$res_data = array("process_status"=>"YES","process_message"=>"Success.","reward_data"=>$reward_status_data);
		}
		else
		{
			$condn="NO";
			$res_data = array("process_status"=>"NO","process_message"=>"Coming Soon.");
		}
		
	}
	// else
	// {
	// 	$res_data = array("process_status"=>"NO","process_message"=>"Coming Soon.");
	// }
// }
else
{
	$res_data = array("process_status"=>"NO","process_message"=>"Dealer not found.");
}

// Log the response data
error_log("Response Data: " . json_encode($res_data));

echo json_encode($res_data);
mysql_close();			
?>

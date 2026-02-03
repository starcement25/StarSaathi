<?php
include "star_connection.php";
$consumer_scheme   ="consumer_scheme";
$customer_master = "customer_master";
$res_data=array();
$total_qty=0;

$customer_id = $_REQUEST["customer_id"] ? addslashes(trim($_REQUEST["customer_id"])) : "";
$trans_id = $_REQUEST["trans_id"] ? addslashes(trim($_REQUEST["trans_id"])) : "";
$datetime = $_REQUEST["datetime"] ? addslashes(trim($_REQUEST["datetime"])) : "";
$customer_name = $_REQUEST["customer_name"] ? addslashes(trim($_REQUEST["customer_name"])) : "";
$customer_phone_no = $_REQUEST["customer_phone_no"] ? addslashes(trim($_REQUEST["customer_phone_no"])) : "";
$dhalai_master_qty = $_REQUEST["dhalai_master_qty"] ? addslashes(trim($_REQUEST["dhalai_master_qty"])) : "";
$weather_shield_qty = $_REQUEST["weather_shield_qty"] ? addslashes(trim($_REQUEST["weather_shield_qty"])) : "";
$date_of_purchase = $_REQUEST["date_of_purchase"] ? addslashes(trim($_REQUEST["date_of_purchase"])) : "";
if($customer_name!='' && $customer_phone_no!=''){
date_default_timezone_set('Asia/Kolkata'); // Set the timezone to IST

$sqlinsert = "INSERT INTO $consumer_scheme set `customer_id`='$customer_id',
			`date_and_time`='$datetime',
			`trans_id`='$trans_id',
			`customer_name`='$customer_name',
			`customer_phone_no`='$customer_phone_no',
			`dhalai_master_qty`='$dhalai_master_qty',
			`weather_shield_qty`='$weather_shield_qty',
			 `date_of_purchase`='$date_of_purchase'";
$resinsert = mysql_query($sqlinsert);
	
$total_qty=$dhalai_master_qty+$weather_shield_qty;
	if($total_qty!='' && $total_qty >100){
	$sellottery="SELECT (MAX(lottery_no)+1) as max_lottery_no FROM lottery_master where is_active='N'";
	$rslottery=mysql_query($sellottery);
	$rowlottery=mysql_fetch_array($rslottery);
	$max_lottery_no=$rowlottery['max_lottery_no'];
	$sms_text = "Dear Patron,

Congratulations on purchasing Star Cement Premium Product! 
We are thrilled to announce that you are eligible for our lucky draw, where you have the chance to win exciting prizes.
Please find your lottery number: $max_lottery_no";
$sms_uri = "https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to=".$customer_phone_no."&from=STARCM&text=".urlencode($sms_text)."&tempid=1707160982733435860&dlr-mask=19&dlr-url";
$sms_ch = curl_init();
curl_setopt($sms_ch, CURLOPT_URL, $sms_uri);
curl_setopt($sms_ch, CURLOPT_TIMEOUT, 20);
curl_setopt($sms_ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($sms_ch, CURLOPT_HEADER,0);
curl_setopt($sms_ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($sms_ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
$lipl_return_val = curl_exec($sms_ch);
curl_close($sms_ch);

//echo $lipl_return_val;
	$sqlupdatescheme="UPDATE $consumer_scheme SET lottery_no='$max_lottery_no' WHERE `trans_id`='$trans_id'";
	$rsupdatescheme=mysql_query($sqlupdatescheme);
	$sqlupdatelotterym="UPDATE lottery_master SET is_active='N' WHERE `lottery_no`='$max_lottery_no'";
	$rsupdatelotterym=mysql_query($sqlupdatelotterym);
	}
$res_data = array("process_status"=>"YES","process_message"=>"Consumer scheme successfully saved.");
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Failed to save Consumer scheme.");
}
echo json_encode($res_data);
//mysql_close();
//For SFA INSERT
mysql_close();
?>

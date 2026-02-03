<?php
include "star_connection.php";
$mobileNumber=$_POST['mobileNumber'];
$sessionToken=$_POST['sessionToken'];
//$mobileNumber='7002297705';
//$sessionToken='M1nvmv6F27sckAA6';
$branch_game_status = "branch_game_status";
$customer_master = "customer_master";
$app_service_track_log="app_service_track_log";

$sqlcustomer="SELECT customer_code,branch_code FROM $customer_master WHERE phone_no='".$mobileNumber."'";
// echo $sqlcustomer;
$rscustomer=mysql_query($sqlcustomer);
$rowcustomer=mysql_fetch_array($rscustomer);
$customer_code=$rowcustomer['customer_code'];
$branch_code=$rowcustomer['branch_code'];

	$sqlquery="SELECT * FROM $branch_game_status where branch_code='".$branch_code."'";
	$result = mysql_query($sqlquery);
	$count=mysql_num_rows($result);
	$rowgame=mysql_fetch_array($result);
	$gamestatus=$rowgame['game_status'];
	
	if($gamestatus=='ACTIVE')
	{
		$url_ck1 = 'https://hellotournament.pokermoogley.com:5051/gameMgmt/authenticateUser';
		
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		$headr = array();
		//$headr[] = 'Content-length: 0';
		$headr[] = 'Content-type: application/json';
		$headr[] = 'Authorization: LEpClrF9zbfxFm58';
		$payload = json_encode( array( "mobileNumber"=> $mobileNumber,"sessionToken"=> $sessionToken ) );
		$ch_sheader = curl_init();
		curl_setopt($ch_sheader, CURLOPT_URL,$url_ck1);
		curl_setopt($ch_sheader, CURLOPT_HTTPHEADER,$headr);
		curl_setopt($ch_sheader, CURLOPT_POST,true);
		curl_setopt( $ch_sheader, CURLOPT_POSTFIELDS, $payload );
		curl_setopt($ch_sheader, CURLOPT_RETURNTRANSFER,true);
		//curl_setopt($ch_sheader, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch_sheader, CURLOPT_USERAGENT, $useragent);
		$body_for_mcode = curl_exec($ch_sheader);
		//echo "<pre>";
		$info = curl_getinfo($ch_sheader);
		//print_r($info);
		curl_close($ch_sheader);
		//return $body_for_mcode;
		echo $body_for_mcode;
	}
	else
	{
		$res_data = array("process_status"=>"NO","process_message"=>"Coming Soon.");
		echo json_encode($res_data);
	}

?>
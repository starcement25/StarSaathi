<?php
$mobileNumber=$_POST['mobileNumber'];
$sessionToken=$_POST['sessionToken'];
//$mobileNumber='7002297705';
//$sessionToken='M1nvmv6F27sckAA6';

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




?>
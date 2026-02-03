<?php

$title14 = "<b>1. ZFI_CREDIT_LIMIT_CDS URL</b><br>";
/*$todays_datetime_format = "2020-09-09T23:59:59";
$ex_qry14 = "Kunnr eq 'A101000020' and Erdat eq datetime'".$todays_datetime_format."'";
$url_ck14 = 'http://ececcprd.emamicement.com:8000/sap/opu/odata/sap/ZLB_SRV/LedBalSet?$format=json&$filter='.urlencode($ex_qry14);*/

$url_ck14 = "http://devqasapp.starcement.co.in:8000/sap/opu/odata/sap/ZFI_CREDIT_LIMIT_CDS/ZFI_Credit_limit(kunnr='1000000013')?$format=json&sap-client=200";




function show_data_new($url_ck){
$useragent = $_SERVER['HTTP_USER_AGENT'];
$username = "SCL_MRIDU";
$password = "iNIT@12345";
$ch_sheader = curl_init();
curl_setopt($ch_sheader, CURLOPT_URL,$url_ck);
curl_setopt($ch_sheader, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch_sheader, CURLOPT_USERPWD, "$username:$password");
curl_setopt($ch_sheader, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch_sheader, CURLOPT_USERAGENT, $useragent);
$body_for_mcode = curl_exec($ch_sheader);
//echo "<pre>";
$info = curl_getinfo($ch_sheader);
//print_r($info);
curl_close($ch_sheader);
return $body_for_mcode;
}

$username = "SCL_MRIDU";
$password = "iNIT@12345";
echo "<br>Using this credentials<br>User ID: ".$username;
echo "<br>Password: ".$password;
echo "<br><br><br>";



echo $title14;
echo "Link: ".$url_ck14;
echo "<br>Json Response:<br>";
$body_for_mcode14 = show_data_new($url_ck14);
echo "--------------------------------<br>";
echo $body_for_mcode14;
echo "<br>--------------------------------<br><br>";


?>
<?php
$did = "1000000021";
$dt = "2022-08-17T00:00:00";
$title1 = "<b>1.LEDGER LIST [ZFI_LEDGER_ODATA_SRV]</b><br>";
$the_filter = '&$filter=(Kunnr eq \''.$did.'\' and DocDate eq datetime\''.$dt.'\')';
$url_ck1 = 'https://PRDAPP1.starcement.co.in:44310/sap/opu/odata/sap/ZFI_LEDGER_ODATA_SRV/LedgerSet?$format=json'.str_replace(" ","%20",$the_filter);

$url_show = 'https://PRDAPP1.starcement.co.in:44310/sap/opu/odata/sap/ZFI_LEDGER_ODATA_SRV/LedgerSet?$format=json&$filter=(Kunnr eq \'1000000021\' and DocDate eq datetime\'2022-08-17T00:00:00\')';

function show_data_new($url_ck){
$useragent = $_SERVER['HTTP_USER_AGENT'];
$username = "MOBILEAPP";
$password = "Star@#2021";
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
function isJsonCk($str) {
    $json = json_decode($str);
    return $json && $str != $json;
}
$username = "MOBILEAPP";
$password = "Star@#2021";
echo "<br>Using this credentials<br>User ID: ".$username;
echo "<br>Password: ".$password;
echo "<br><br><br>";
echo $title1;
echo "Link: ".$url_show;
echo "<br>Json Response:<br>";
$body_for_mcode1 = show_data_new($url_ck1);
echo "--------------------------------<br>";
echo $body_for_mcode1;
echo "<br>--------------------------------<br><br>";
?>
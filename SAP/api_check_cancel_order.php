<?php
$the_date = "2022-08-03T00:00:00";
$title1 = "<b>1.Cancel Order [ZSD_PARKLOT_SO_SERV_CDS]</b><br>";

$the_filter = '&$filter=(erdat eq datetime\''.$the_date.'\'and status eq \'CANCEL\')';
$url_ck1 = 'https://PRDAPP1.starcement.co.in:44310/sap/opu/odata/sap/ZSD_PARKLOT_SO_SERV_CDS/ZSD_PARKLOT_SO_SERV?$format=json'.str_replace(" ","%20",$the_filter);

$url_show = 'https://PRDAPP1.starcement.co.in:44310/sap/opu/odata/sap/ZSD_PARKLOT_SO_SERV_CDS/ZSD_PARKLOT_SO_SERV?$format=json&$filter=(erdat eq datetime\''.$the_date.'\'and status eq \'CANCEL\')';

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


echo "<br>InvDate: ".$so_date;
echo "<br>".$title1;
echo "Link: ".$url_show;
echo "<br>Json Response:<br>";
$body_for_mcode1 = show_data_new($url_ck1);
echo "--------------------------------<br>";
echo $body_for_mcode1;
echo "<br>--------------------------------<br><br>";
/*if(isJsonCk($body_for_mcode1)){
	$json_decoded = json_decode($body_for_mcode1,true);
	print_r($json_decoded);
}*/
/*if(isJsonCk($body_for_mcode1)){
	$json_decoded = json_decode($body_for_mcode1,true);
	if(count($json_decoded)>0){
		if(array_key_exists("d",$json_decoded)){
		$kunnr = $json_decoded["d"]["kunnr"];
		$name1 = $json_decoded["d"]["name1"];
		$name2 = $json_decoded["d"]["name2"];
		$name3 = $json_decoded["d"]["name3"];
		$credit_limit = $json_decoded["d"]["credit_limit"];
		$credit_expose = $json_decoded["d"]["credit_expose"];
		
		echo "<br>kunnr: ".$kunnr;
		echo "<br>name1: ".$name1;
		echo "<br>name2: ".$name2;
		echo "<br>name3: ".$name3;
		echo "<br>credit_limit: ".$credit_limit;
		echo "<br>credit_expose: ".$credit_expose;
		}
		
	}
}*/

?>
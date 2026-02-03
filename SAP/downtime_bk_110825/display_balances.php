<?php
//$did = "1000000013";
$did =$_REQUEST['customer_id'];
//$did='1000003100';
$title1 = "<b>1.OUTSTANDING [ZFI_CREDIT_LIMIT_CDS]</b><br>";
//$url_ck1 = 'https://devqasapp.starcement.co.in:44301/sap/opu/odata/sap/ZFI_CREDIT_LIMIT_CDS/ZFI_Credit_limit(kunnr=\''.$did.'\')?$format=json&sap-client=300';
$url_ck1 = 'https://starfiori.starcement.co.in:44300/sap/opu/odata/sap/ZFI_CREDIT_LIMIT_CDS/ZFI_Credit_limit(kunnr=\''.$did.'\')?$format=json&sap-client=900';
        
//$url_ck1 = 'https://salesmpower.acedns.in/';
function show_data_new($url_ck){
$useragent = $_SERVER['HTTP_USER_AGENT'];
$username = "STARSAATHI";
$password = "Srikrishna@93933";//"q9Z!f3Hx@7Lw";//Star@#2021
$ch_sheader = curl_init();
curl_setopt($ch_sheader, CURLOPT_URL,$url_ck);
curl_setopt($ch_sheader, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch_sheader, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch_sheader, CURLOPT_SSL_VERIFYPEER, false);	
curl_setopt($ch_sheader, CURLOPT_USERPWD, "$username:$password");
curl_setopt($ch_sheader, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch_sheader, CURLOPT_USERAGENT, $useragent);
$body_for_mcode = curl_exec($ch_sheader);
	
	//print_r($body_for_mcode);
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
$username = "STARSAATHI";
$password = "Srikrishna@93933";
$body_for_mcode1 = show_data_new($url_ck1);
if(isJsonCk($body_for_mcode1)){
	$json_decoded = json_decode($body_for_mcode1,true);
	if(count($json_decoded)>0){
		if(array_key_exists("d",$json_decoded)){
		$kunnr = $json_decoded["d"]["kunnr"];
		$name1 = $json_decoded["d"]["name1"];
		$name2 = $json_decoded["d"]["name2"];
		$name3 = $json_decoded["d"]["name3"];
		$credit_limit = $json_decoded["d"]["credit_limit"];
		$credit_expose = $json_decoded["d"]["credit_expose"];
		//echo "<br>credit_limit: ".$credit_limit;
		//echo "<br>credit_expose: ".$credit_expose;
		$res_msg = array("process_sts"=>"YES","process_msg"=>"<b>credit_limit:</b> $credit_limit <br> <b>credit_expose:</b> $credit_expose");
		}
	}
	else
	{
		$res_msg = array("process_sts"=>"NO","process_msg"=>"SOMETHING WENT WRONG.");
	}
}
echo json_encode($res_msg);
mysql_close();
?>
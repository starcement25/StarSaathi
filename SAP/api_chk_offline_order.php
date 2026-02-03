<?php

//$did = "SS0038568";

$title1 = "<b>1.Ledger [OFFLINE_ORDER]</b><br>";



/*https://PRDAPP1.starcement.co.in:44310/sap/opu/odata/sap/ZFI_CUST_LEDGER_ODATA_SRV/ZFI_CUST_LEDGER_STSet?$filter=(Kunnr eq '1000002808' and ( Bldat ge datetime'2022-8-1T00:00:00' and Bldat le datetime'2022-8-31T00:00:00') )&$format=json&sap-client=900*/
$the_filter = '&$filter=(erdat eq datetime\'2022-12-15T00:00:00\')';
//echo $url_ck1 = 'https://PRDAPP1.starcement.co.in:44310/sap/opu/odata/sap/ZFI_CUST_LEDGER_ODATA_SRV/ZFI_CUST_LEDGER_STSet?$format=xml'.str_replace(" ","%20",$the_filter);

//$the_filter=str_replace(" ","%20",$the_filter);

$url_ck1 = 'https://PRDAPP1.starcement.co.in:44310/sap/opu/odata/sap/ZSD_VBAK_SO_SERV_CDS/ZSD_VBAK_SO_SERV?$format=json'.str_replace(" ","%20",$the_filter);









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

echo "Link: ".$url_ck1;

echo "<br>Json Response:<br>";

$body_for_mcode1 = show_data_new($url_ck1);

echo "--------------------------------<br>";

echo $body_for_mcode1;

/*$json_decoded = json_decode($body_for_mcode1,true);
	$app_ref_no = $json_decoded["d"]["app_ref_no"];
	$material = $json_decoded["d"]["material"];
	$so_num = $json_decoded["d"]["so_num"];
	$erdat = $json_decoded["d"]["erdat"];
	$erzet = $json_decoded["d"]["erzet"];
	$erdat_date_format = "";
	if($erdat!=""){
	$erdat_str = str_replace("/","",$erdat);
	$erdat_str = str_replace("Date","",$erdat_str);	
	$erdat_str = str_replace("(","",$erdat_str);
	$erdat_str = str_replace(")","",$erdat_str);
	$erdat_str = ($erdat_str / 1000);
	$erdat_date_format = date("Y-m-d",$erdat_str);
	}
	if($erzet!='')
	{
		$erzet_str = str_replace("PT","",$erzet);
		$erzet_str = str_replace("H","",$erzet_str);
		$erzet_str = str_replace("M","",$erzet_str);
		$erzet_str = str_replace("S","",$erzet_str);
	}
	echo '<br />';
	$erdat_date_format=$erdat_date_format.' '.$erzet_str;
	echo $erzetf = date("Y-m-d H:i:s",strtotime($erdat_date_format));

	$upd_tmstmp = $json_decoded["d"]["upd_tmstmp"];
	$upd_tmstmp_format = "";
	if($upd_tmstmp!=""){
	$upd_tmstmp_str = str_replace("/","",$upd_tmstmp);
	$upd_tmstmp_str = str_replace("Date","",$upd_tmstmp_str);	
	$upd_tmstmp_str = str_replace("(","",$upd_tmstmp_str);
	$upd_tmstmp_str = str_replace(")","",$upd_tmstmp_str);
	$upd_tmstmp_str = ($upd_tmstmp_str / 1000);
	$upd_tmstmp_format = date("Y-m-d H:i:s",$upd_tmstmp_str);
	//echo strtotime($upd_tmstmp_format);
	}

echo "<br>--------------------------------<br><br>";

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
<?php
header("Content-type: text/plain");
include "../star_connection.php";
$test_payment = "test_payment";
$response = $_REQUEST;
$resonse_from_pg = addslashes(json_encode($response));
if(array_key_exists("orderStatus",$response)){
	$orderStatus = trim($response["orderStatus"]);
	if(array_key_exists("orderNo",$response)){
		$orderNo = trim($response["orderNo"]);
	}else{
		$orderNo = "";
	}
	if(array_key_exists("encResp",$response)){
		$encResp = trim($response["encResp"]);
	}else{
		$encResp = "";
	}
	if(array_key_exists("settingIntegrationType",$response)){
		$settingIntegrationType = trim($response["settingIntegrationType"]);
	}else{
		$settingIntegrationType = "";
	}
	
	if(array_key_exists("returnUrl",$response)){
		$returnUrl = trim($response["returnUrl"]);
	}else{
		$returnUrl = "";
	}
	
	if(array_key_exists("crossSellUrl",$response)){
		$crossSellUrl = trim($response["crossSellUrl"]);
	}else{
		$crossSellUrl = "";
	}
	
	
	
	if($orderNo!=""){
		$sql2 = "select `order_id` from $test_payment where `order_id`='$orderNo'";
		$res2 = mysql_query($sql2);
		$totres2 = mysql_num_rows($res2);
		if($totres2>0){
			$sql4 = "update $test_payment set `encresp`='$encResp',`setinttupe`='$settingIntegrationType',`order_status`='$orderStatus',`return_url`='$returnUrl',`cross_sell_url`='$crossSellUrl',`resonse_from_pg`='$resonse_from_pg' where `order_id`='$orderNo' and `order_status`='Pending'";
			$res4 = mysql_query($sql4);
		}
	}
	echo "payment_".strtolower($orderStatus);
}
mysql_close();
//echo json_encode($response);
?>
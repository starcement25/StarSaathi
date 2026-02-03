<?php
include "star_connection.php";
$dealer_app_version = "dealer_app_version";
$app_version_data = array("ANDROID"=>"1.1","IOS"=>"1.1");
$branch_consumer_scheme_status="branch_consumer_scheme_status";

$current_date=date('Y-m-d');
$the_customer_code = $_REQUEST["customer_code"] ? addslashes(trim($_REQUEST["customer_code"])) : "";
$sqlckstatus = "select `consumer_scheme_status` from $branch_consumer_scheme_status where `branch_code`=(SELECT `branch_code` FROM customer_master WHERE customer_id='".$the_customer_code."')";
	$resckstatus = mysql_query($sqlckstatus);
	$totckstatus = mysql_num_rows($resckstatus);
	if($totckstatus>0){
		$rowckstatus=mysql_fetch_array($resckstatus);
		$consumer_scheme_status=$rowckstatus['consumer_scheme_status'];
	}else{
		$consumer_scheme_status='INACTIVE';
	}

$sqlall = "select * from $dealer_app_version";
$resall = mysql_query($sqlall);
$totall = mysql_num_rows($resall);
if($totall>0){
	while($rowall=mysql_fetch_assoc($resall)){
		$the_device_type = $rowall["device_type"] ? trim($rowall["device_type"]) : "";
		$the_app_version = $rowall["app_version"] ? trim($rowall["app_version"]) : "";
		if($the_device_type!="" && $the_app_version!=""){
			$the_device_type = strtoupper(strtolower($the_device_type));
			if(array_key_exists($the_device_type,$app_version_data)){
				$app_version_data[$the_device_type] = $the_app_version;
			}
		}
	}
	$res_data = array("process_status"=>"YES","process_message"=>"New version available.","android_app_version"=>$app_version_data["ANDROID"],"ios_app_version"=>$app_version_data["IOS"],"current_date"=>$current_date,"consumer_scheme_status"=>$consumer_scheme_status);
}else{
	$res_data = array("process_status"=>"YES","process_message"=>"Success.","android_app_version"=>$app_version_data["ANDROID"],"ios_app_version"=>$app_version_data["IOS"],"current_date"=>$current_date,"consumer_scheme_status"=>$consumer_scheme_status );
}
	
echo json_encode($res_data);
mysql_close();
?>
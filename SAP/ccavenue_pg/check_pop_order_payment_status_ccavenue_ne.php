<?php
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
date_default_timezone_set('Asia/Kolkata');
include "../star_connection.php";
$t_main_order_pop = "T_MAIN_ORDER_POP";
$t_order_pop = "T_ORDER_POP";
$customer_master = "customer_master";
include('Crypto.php');
$useragent = $_SERVER['HTTP_USER_AGENT'];
$merchant_id = "2308286";
$working_key='FF9525898F3AA1A2A1202798734257C9';
$access_code='AVJB50KD09AZ38BJZA';
$res_data = array();
function isJSON_pop($string){
   return is_string($string) && is_array(json_decode($string, true)) ? true : false;
}
$orderId = $_REQUEST["order_id"] ? trim($_REQUEST["order_id"]) : "";
if($orderId==""){
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");
}else{


$merchant_json_data =
array(
	'order_no' => $orderId,
	'reference_no' =>''
);

/* For Testing: */

//$the_url = "https://apitest.ccavenue.com/apis/servlet/DoWebTrans";

/* For Production: */

$the_url = "https://api.ccavenue.com/apis/servlet/DoWebTrans";

$merchant_data = json_encode($merchant_json_data);

$encrypted_data = encrypt($merchant_data, $working_key);
$final_data = 'enc_request='.$encrypted_data.'&access_code='.$access_code.'&command=orderStatusTracker&request_type=JSON&response_type=JSON';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $the_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $final_data);
// Get server response ...
$result = curl_exec($ch);
curl_close($ch);
/*echo $result;
exit;*/

if($result==""){
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");
}else{
$status = '';
$order_status = '';
$order_tran_data = '';
$information = explode('&', $result);

$dataSize = sizeof($information);
for ($i = 0; $i < $dataSize; $i++) {
	$info_value = explode('=', $information[$i]);
	 if ($info_value[0] == 'status') {
		$status = trim($info_value[1]);
	}else if ($info_value[0] == 'enc_response') {
		$order_tran_data = decrypt(trim($info_value[1]), $working_key);
		
	}
}



if(isJSON_pop($order_tran_data)=== FALSE){
$res_data = array("process_status"=>"NO","process_message"=>"Invalid json response from payment server.");	
}else{
$poptdata_arr = json_decode($order_tran_data,true);
/*echo "<pre>";
print_r($poptdata_arr);
echo "</pre>";*/
if(array_key_exists("Order_Status_Result",$poptdata_arr)){
$the_arr = $poptdata_arr["Order_Status_Result"];
if(array_key_exists("order_status",$the_arr)){
$the_order_status = $the_arr["order_status"];
$the_order_card_name = $the_arr["order_card_name"];
$the_tracking_id = $the_arr["reference_no"];
$the_bank_ref_no = $the_arr["order_bank_ref_no"];


$res_data = array("process_status"=>"YES","process_message"=>"Done","the_order_status"=>$the_order_status,"the_order_card_name"=>$the_order_card_name,"the_tracking_id"=>$the_tracking_id,"the_bank_ref_no"=>$the_bank_ref_no);
}else{
if(array_key_exists("error_desc",$the_arr)){
$error_desc = $the_arr["error_desc"];
}else{
$error_desc = "Something went wrong. Please try later.";	
}
$res_data = array("process_status"=>"NO","process_message"=>$error_desc);	
}

}else{
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");	
}
	
}



}
	
}




echo json_encode($res_data);
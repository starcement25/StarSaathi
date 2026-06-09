<?php
session_start();
set_time_limit(0);
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include "star_connection.php";
if (!function_exists('random_bytes')) {
    function random_bytes($length) {
        if ($length <= 0) return false;

        // Best option (Linux/Unix)
        if (is_readable('/dev/urandom')) {
            $file = fopen('/dev/urandom', 'rb');
            $bytes = fread($file, $length);
            fclose($file);
            if ($bytes !== false) {
                return $bytes;
            }
        }

        // OpenSSL fallback
        if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($length, $strong);
            if ($bytes !== false && $strong === true) {
                return $bytes;
            }
        }

        // Last fallback (not secure)
        $bytes = '';
        for ($i = 0; $i < $length; $i++) {
            $bytes .= chr(mt_rand(0, 255));
        }

        return $bytes;
    }
}

$res_msg			= array();
$customer_master = "customer_master";
$broker_master = "broker_master";
$customer_broker_relation = "customer_broker_relation";
$dealer_id	= $_POST["dealer_id"] ? addslashes(trim($_POST["dealer_id"])) : "";
$mob_no	= $_POST["mob_no"] ? addslashes(trim($_POST["mob_no"])) : "";
$dealer_otp	= $_POST["dealer_otp"] ? trim($_POST["dealer_otp"]) : "";
$sms_res = "";
if($dealer_otp!=""){
if($dealer_id!="" && $mob_no!=""){


//comment sk 29-05-25
//$sql1 = "select * from $customer_master where `customer_id`='".$dealer_id."' and `phone_no`='".$mob_no."' and `cust_type`='Dealer' and `acedns` = 'Y'";
$sql1 = "select * from $customer_master where `customer_id`='".$dealer_id."' and `phone_no`='".$mob_no."' and `cust_type`='Dealer' ";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	/*----Tracklog Code Start-----*/
$curr_date_time = date("Y-m-d H:i:s");
$webservice_name = "Login";
$sqlin_tl = "insert into `webservice_track_log` (`customer_code`,`webservice_name`,`details`,`datetime`) values ('$dealer_id','$webservice_name','','$curr_date_time')";
$resin_tl = mysql_query($sqlin_tl);
/*----Tracklog Code End-----*/

$row1 = mysql_fetch_assoc($res1);
$dns_customer_code = trim($row1["dns_customer_code"]);
$customer_code = trim($row1["customer_code"]);
$customer_name = trim($row1["customer_name"]);
$sms_otp = trim($row1["sms_otp"]);
if($sms_otp==$dealer_otp){
$user_type = "DEALER";
	//auth  add 23-04-26
		$token = bin2hex(random_bytes(16));
		$datetime = date('YmdHis');

		$rawToken = $dealer_id . '|' . $datetime . '|' . $token;
		$token = hash('sha256', $rawToken);

		// Escape values
		$dealer_id     = mysql_real_escape_string($dealer_id);
		$deviceid      = "web";
		$location_date = date('Y-m-d H:i');
		$token         = mysql_real_escape_string($token);

		// FIXED SQL (removed extra comma)
		$sql = "UPDATE changepassword 
				SET deviceid = '$deviceid',
					loggedin_date_time = '$location_date',
					token = '$token'
				WHERE dns_customer_code = '$dealer_id'
				LIMIT 1";

		$result = mysql_query($sql);
//auth end 23-04-26

$_SESSION["sswa_user_type"]= $user_type;
$_SESSION["sswa_user_name"]= $customer_name;
$_SESSION["sswa_user_id"]= $customer_code;
$_SESSION["sswa_user_dns_id"]= $dns_customer_code;
$_SESSION["sswa_selected_dealer_name"]= $customer_name;
$_SESSION["sswa_selected_dealer_code"]= $dns_customer_code;
$_SESSION["sswa_selected_customer_code"]= $customer_code;
$_SESSION["token"]= $token;
$_SESSION["dealer_id"]= $dealer_id;

$res_msg = array("process_sts"=>"YES","process_msg"=>"OTP has been sent to your mobile number.");
}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"Wrong OTP");
}
}else{

//$sql2 = "select * from $broker_master where `dns_broker_id`='".$dealer_id."' and `phone_no`='".$mob_no."' and `acedns` = 'Y'";
$sql2 = "select * from $broker_master where `dns_broker_id`='".$dealer_id."' and `phone_no`='".$mob_no."' and `acedns` = 'Y'";
$res2 = mysql_query($sql2);
$totres2 = mysql_num_rows($res2);
if($totres2>0){
$row2 = mysql_fetch_assoc($res2);
$broker_id = trim($row2["broker_id"]);
$dns_broker_id = trim($row2["dns_broker_id"]);
$broker_name = trim($row2["broker_name"]);
$sms_otp = trim($row2["sms_otp"]);
if($sms_otp==$dealer_otp){
$sql24 = "select $customer_broker_relation.`broker_code`,$customer_master.`customer_name`,$customer_master.`customer_code`,$customer_master.`dns_customer_code` from $customer_broker_relation left join $customer_master on $customer_broker_relation.`customer_code`=$customer_master.`customer_code` where $customer_broker_relation.`broker_code`='$broker_id' and $customer_master.`customer_code` is not null order by $customer_master.`customer_name` asc limit 0,1";
$res24 = mysql_query($sql24);
$totres24 = mysql_num_rows($res24);
if($totres24>0){
$row24 = mysql_fetch_assoc($res24);
$selected_customer_name = trim($row24["customer_name"]);
$selected_customer_code = trim($row24["customer_code"]);
$selected_dns_customer_code = trim($row24["dns_customer_code"]);

//auth  add 23-04-26
		// Generate token
		// $dealer_id=$dns_broker_id;//
		$token = bin2hex(random_bytes(16));
		$datetime = date('YmdHis');

		$rawToken = $dealer_id . '|' . $datetime . '|' . $token;
		$token = hash('sha256', $rawToken);

		// Escape
		$dealer_id     = mysql_real_escape_string($dealer_id);
		$deviceid      = "web";
		$location_date = date('Y-m-d H:i');
		$token         = mysql_real_escape_string($token);

		// Check if record exists
		$checkSql = "SELECT 1 FROM changepassword 
					WHERE customer_code = '$broker_id' 
					LIMIT 1";

		$checkRes = mysql_query($checkSql);

		if ($checkRes && mysql_num_rows($checkRes) > 0) {

			// ✅ UPDATE
			$sql = "UPDATE changepassword 
					SET deviceid = '$deviceid',
						loggedin_date_time = '$location_date',
						token = '$token'
					WHERE customer_code = '$broker_id'
					LIMIT 1";

		} else {

			// ✅ INSERT
			$sql = "INSERT INTO changepassword 
					(customer_code,dns_customer_code, deviceid, loggedin_date_time, token)
					VALUES 
					('$dealer_id','$dns_broker_id', '$deviceid', '$location_date', '$token')";
		}
	//echo"<pre>";print_r($sql);die;

		$result = mysql_query($sql);
//auth end 23-04-26

$user_type = "SP";
$_SESSION["sswa_user_type"]= $user_type;
$_SESSION["sswa_user_name"]= $broker_name;
$_SESSION["sswa_user_id"]= $broker_id;
$_SESSION["sswa_user_dns_id"]= $dns_broker_id;
$_SESSION["sswa_selected_dealer_name"]= $selected_customer_name;
$_SESSION["sswa_selected_cust_type"]= "Dealer";
$_SESSION["sswa_selected_dealer_code"]= $selected_dns_customer_code;
$_SESSION["sswa_selected_customer_code"]= $selected_customer_code;
$_SESSION["token"]= $token;
$_SESSION["dealer_id"]= $dns_broker_id;

$res_msg = array("process_sts"=>"YES","process_msg"=>"OTP has been sent to your mobile number.");

}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"No dealers are assigned under this sales promoter.");
}
}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"Wrong OTP");
}
}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"NOT VALID USER");
}

}
}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"Something went wrong. Please try later.");
}
}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"Please enter OTP.");
}
echo json_encode($res_msg);
mysql_close();
?>

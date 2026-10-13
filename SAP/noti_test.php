<?php
/*---------------------------------------------------------------------------
 FCM v1 test send.
 The access token is no longer hard coded - fcm_token.php signs the service
 account JWT and fetches a fresh token (cached for its 1 hour lifetime).
---------------------------------------------------------------------------*/
include "fcm_token.php";

$device_token = "fWLwMZf1RMmq4Co7SH2bVH:APA91bGGKcpsiWjLk-0IPTxj5_oEBHHEOqY4sZq_0phKvgGdyGl_bbh_QTntBcbbBB8R8MMOF4tP2moJfvfWSeCvKPwOa4fIUozjzTVwo-m9p7LmddOdiSc";

$message = array(
	"token" => $device_token,
	"notification" => array(
		"title" => "Test Notification",
		"body"  => "Hello from Postman"
	)
);

$result = fcm_send_message($message);

if($result["error"]!=""){
	echo json_encode(array("process_status"=>"NO","process_message"=>$result["error"]));
}else{
	echo $result["response"];
}
?>

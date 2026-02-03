<?php
/*$requestId = "260120191530230";
$workingKey = "324077D9320ADDFBBC0607E18D7FBE4C";
$merchantId="239656";
$the_string = $requestId.$workingKey.$merchantId;
$reqHash = hash('sha512', $the_string);
echo $reqHash;*/
$order_id = "GKG_8226698";
$st = "1581410832568YPCZA";
$currency = "INR"; 
$amount = "100"; 
$workingKey = "324077D9320ADDFBBC0607E18D7FBE4C";
$merchantId="239656";
$the_string = $order_id.$currency.$amount.$st;
$reqHash = hash('sha512', $the_string);
echo $reqHash;
?>
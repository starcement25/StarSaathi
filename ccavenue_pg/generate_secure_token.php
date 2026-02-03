<?php
$accessCode = $_POST["accessCode"] ? trim($_POST["accessCode"]) : "";
$requestId = $_POST["requestId"] ? trim($_POST["requestId"]) : "";
$requestHash = $_POST["requestHash"] ? trim($_POST["requestHash"]) : "";
if($accessCode!="" && $requestId!="" && $requestHash!=""){
//$url = "https://test.ccavenue.com/TransCcAvenue/v2/getSecureToken";
$url = "https://secure.ccavenue.com/TransCcAvenue/v2/getSecureToken";

$fields = array(
'requestId'=>$requestId,
'accessCode'=>$accessCode,
'requestHash'=>$requestHash
);
$postvars='';
$sep='';
foreach($fields as $key=>$value)
{
        $postvars.= $sep.urlencode($key).'='.urlencode($value);
        $sep='&';
}
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_POST,count($fields));
//curl_setopt($ch, CURLOPT_CAINFO, 'replace this with your cacert.pem file path here');
//curl_setopt($ch, CURLOPT_CAINFO, $_SERVER['DOCUMENT_ROOT']."/cacert.pem");
curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
echo $result;
}
?>
<?php
$soapUrl = "devqasapp.starcement.co.in:8000/sap/opu/odata/sap/ZFI_CREDIT_LIMIT_CDS/ZFI_Credit_limit(kunnr='1000000013')?$format=json";
$soapUser = "SCL_MRIDU";  //  username
$soapPassword = "Init@12345"; // password
$xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<ZFI_CREDIT_LIMIT_CDS xmlns="https://ws.campaigner.com/2013/01">
<authentication>
<Username>'.$soapUser.'</Username>
<Password>'.$soapPassword.'</Password>
</authentication>
</ZFI_CREDIT_LIMIT_CDS>
</soap:Body>
</soap:Envelope>';   // data from the form, e.g. some ID number

           $headers = array(
"Content-type: text/xml;charset=\"utf-8\"",
"Accept: text/xml",
"Cache-Control: no-cache",
"Pragma: no-cache",
"SOAPAction: 65.0.150.55", 
"Content-length: ".strlen($xml_post_string),
); //SOAPAction: your op URL

            $url = $soapUrl;

            // PHP cURL  for https connection with auth
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // converting
$response = curl_exec($ch); 
curl_close($ch);

            //echo $response;

            // converting
$response1 = str_replace("<soap:Body>","",$response);
$response2 = str_replace("</soap:Body>","",$response1);

            // convertingc to XML
$parser = simplexml_load_string($response2);
// user $parser to get your data out of XML response and to display it.
$objJsonDocument = json_encode($parser);
$arrOutput = json_decode($objJsonDocument, TRUE);

print_r($arrOutput);





?>
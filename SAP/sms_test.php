<?php
$otp = 1010;
$mobile = "9832225403";
/*https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109 &to= Mobile_No &from=STARCM &text=&dlr-mask=19&dlr-url*/
//$otp_text = "OTP is ".$otp." for Star Saathi log in STAR CEMENT";
$otp_text = "Dear Patron,

Congratulations on purchasing Star Cement Premium Product! 
We are thrilled to announce that you are eligible for our lucky draw, where you have the chance to win exciting prizes.
Please find your lottery number: {103}";
$lipl_uri = "https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to=".$mobile."&from=STARCM&text=".urlencode($otp_text)."&tempid=1707160982733435860&dlr-mask=19&dlr-url";
$lipl_ch = curl_init();
curl_setopt($lipl_ch, CURLOPT_URL, $lipl_uri);
curl_setopt($lipl_ch, CURLOPT_TIMEOUT, 20);
curl_setopt($lipl_ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($lipl_ch, CURLOPT_HEADER,0);
curl_setopt($lipl_ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($lipl_ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
$lipl_return_val = curl_exec($lipl_ch);
curl_close($lipl_ch);

echo $lipl_return_val;
?>
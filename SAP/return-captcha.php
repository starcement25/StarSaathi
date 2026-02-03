<?php
	$cookie='cookie.txt';
	if(!file_exists($cookie)){
			$fh = fopen($cookie, "w");
			fwrite($fh, "");
			fclose($fh);
	}
	$ch = curl_init();   
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);   
	curl_setopt($ch, CURLOPT_URL, "https://incometaxindiaefiling.gov.in/e-Filing/CreateCaptcha.do?0.6396598849903246");  
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE);   
	curl_setopt($ch, CURLOPT_COOKIE,1);         
	curl_setopt($ch, CURLOPT_COOKIEJAR,$cookie);
	curl_setopt($ch, CURLOPT_COOKIEFILE,$cookie);      
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Keep-Alive: 115',  'Connection: keep-alive',  'Content-type: application/x-www-form-urlencoded', 'Content-length: 6'));
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:2.0.1) Gecko/20100101 Firefox/4.0.1');

	$result = curl_exec($ch);   
	curl_close($ch);  
	$ImageCaptcha=base64_encode($result);
	echo '<img src="data:image/jpeg;base64,'.$ImageCaptcha.'" />'; 
	/*define("COOKIE_FILE", "cookie.txt");
	/*$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);*/
	/*$ch = curl_init("https://incometaxindiaefiling.gov.in/e-Filing/Services/KnowYourJurisdiction.html");
    curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIE_FILE); 
	curl_setopt ($ch, CURLOPT_COOKIEFILE, COOKIE_FILE); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	/*$content = curl_exec($ch);
	print_r($content);
	curl_close($ch);
	
	exit();*/
	/*if(!curl_exec($ch))
	{
		echo 'Curl error: ' . curl_error($ch);
	}
	else
	{
		$response=@curl_exec($ch);
		$document = new DOMDocument();
		@$document->loadHTML($response);
		$elements = $document->getElementsByTagName('img');
		
		foreach($elements as $imagetag){
			//if($imagetag->getElementById('captchaImg') == true)
			$src = $imagetag->attributes->getNamedItem('src')->nodeValue;
			if(stristr($src,'.do')!=false)
			{
				echo "<img src='https://incometaxindiaefiling.gov.in".$src."' />";
			}
			//$imagearray[] = $src;
		}
	}
	//echo $response['http_code'];	
	curl_close ($ch);*/	
?>
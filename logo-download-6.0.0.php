<?php
require("include/config.php");
require("include/dbcon.php");

$sqlquery="SELECT logo FROM user_details WHERE nick_name='".$nick_name."'";
$result = mysql_query($sqlquery);
$count=mysql_num_rows($result);
	if($count>0){
		$rowlogo=mysql_fetch_array($result);
		$logo=$rowlogo['logo'];
		$logourl='logo/'.$logo;
		/*$handle = "http://www.acedns.in/acednsproduct/$logourl";*/
		//$im = imagecreatefrompng("$logourl");
		/*$handle = file_get_contents("http://www.acedns.in/acednsproduct/$logourl",);
		$contents = stream_get_contents($handle); 
		fclose($handle);
		print_r($contents);*/
		header("Content-type: image/png"); 
		header("Content-Disposition: attachment; filename=$logo");
		//imagepng($im);
		//imagedestroy($im);
		readfile("$logourl");
	}
	else
	{
		echo '0';
	}
	mysql_close($link);
?>

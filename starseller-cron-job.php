<?php
//Update Db backup
$urldbbackup="https://www.starstellar.com/admin/dbup/export_db.php";
	$chdbbackup = curl_init();
	curl_setopt($chdbbackup, CURLOPT_URL, $urldbbackup);
	curl_setopt($chdbbackup, CURLOPT_TIMEOUT, 100);
	curl_setopt($chdbbackup, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chdbbackup, CURLOPT_RETURNTRANSFER, 1);
$resultdbbackup=curl_exec($chdbbackup);

//Update birthday notification
$urlnotification="https://www.starstellar.com/admin/birthday_anniversary_point_notification_process.php";
	$chnotification = curl_init();
	curl_setopt($chnotification, CURLOPT_URL, $urlnotification);
	curl_setopt($chnotification, CURLOPT_TIMEOUT, 100);
	curl_setopt($chnotification, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($chnotification, CURLOPT_RETURNTRANSFER, 1);
$resultnotification=curl_exec($chnotification);

echo 'SUCCESS';
?>	
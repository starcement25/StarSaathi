<?php
include "star_connection.php";
include "cron_page_start.php";
$destination_wise_price = "destination_wise_price";

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('n',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
$contentsdatetime =$month.'/'.$date.'/'.$year;

$sql1 = "select id from $destination_wise_price WHERE (SUBSTRING(effective_date,1,9)='".$contentsdatetime."' OR SUBSTRING(effective_date,1,10)='".$contentsdatetime."')";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$id = $row1["id"];
		$squpdate="UPDATE $destination_wise_price SET rate=effective_rate WHERE id='".$id."'";
		mysql_query($squpdate);
	}
	$success=1;
	if($success==1) echo 'SUCCESS';
	else			echo 'FAILURE';
}
else
{
	echo 'No Data exists for Current Date.';
}
include "cron_page_end.php";
?>
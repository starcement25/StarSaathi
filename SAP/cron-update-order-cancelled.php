<?php
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$do_order_cancel_by_log = "do_order_cancel_by_log";
$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('n',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
$todaydate =$year.'-'.$month.'-'.$date;
$dayscount='15';
$numericprevdate=date('Y-m-d', strtotime("-$dayscount days,$todaydate "));

$sqlselcancel="SELECT APPORDERNO FROM $t_apperpdo WHERE SUBSTRING(order_date,1,10) <'".$numericprevdate."' and `STATUS`='Order received'";
$res_sel_cncl = mysql_query($sqlselcancel);
$countrows=mysql_num_rows($res_sel_cncl);
if($countrows > 0)
{
	while($rowcancl=mysql_fetch_array($res_sel_cncl)){
$sql_cncl = "update $t_apperpdo set `STATUS`='Order canceled' where SUBSTRING(order_date,1,10) <'".$numericprevdate."' and `STATUS`='Order received' AND APPORDERNO='".$rowcancl['APPORDERNO']."'";
//$res_cncl = mysql_query($sql_cncl);
	if(mysql_query($sql_cncl)){
		$cancel_datetime = date("Y-m-d H:i:s");
		$cancel_by = 'SCRIPT';
		$cncl_remark='MORE THAN 15 DAYS';
		$sql_in_log = "insert into $do_order_cancel_by_log (`order_id`,`cancel_by`,`c_remark`,`cancel_datetime`) values ('$rowcancl[APPORDERNO]','$cancel_by','$cncl_remark','$cancel_datetime')";
		$res_in_log = mysql_query($sql_in_log);
		$sucess=1;
	}
	else
	{
		$sucess=0;
		echo 'Failure';
		break;
	}
}

}
else
{
	$sucess=2;
}
if($sucess==1)
{
echo 'SUCCESS';		
printf("Total Order Cancelled: %d\n", $countrows);
}
else if($sucess==2)
{
	echo 'No Records';
}
else
{
	echo 'Failure';
}


?>
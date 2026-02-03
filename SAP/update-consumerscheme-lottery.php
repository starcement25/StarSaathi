<?php
ini_set('memory_limit', '99999M');
set_time_limit(0);
include "star_connection.php";

$consumer_scheme = "consumer_scheme";

$sql2 = "SELECT trans_id,dhalai_master_qty,weather_shield_qty,lottery_no FROM $consumer_scheme WHERE `lottery_no` LIKE '%2576%' ORDER BY `consumer_scheme`.`date_and_time` ASC limit 1,277";	
$res2 = mysql_query($sql2);
$totres2 = mysql_num_rows($res2);
$countexe=0;
while($row2 = mysql_fetch_array($res2))
{
	$countexe++;
	$trans_id=$row2['trans_id'];
	$dhalai_master_qty=$row2['dhalai_master_qty'];
	$weather_shield_qty=$row2['weather_shield_qty'];
	$lottery_no=$row2['lottery_no'];
	
	$total_qty=$dhalai_master_qty+$weather_shield_qty;
	$lottery_no_set='';
if($total_qty!='' && $total_qty >=50){
	if($dhalai_master_qty >= 100 && $weather_shield_qty >=100){
	$sellottery="SELECT lottery_no FROM lottery_master where is_active='Y' ORDER BY lottery_no ASC LIMIT 0,4";
	$rslottery=mysql_query($sellottery);
		while($rowlottery=mysql_fetch_array($rslottery)){
		$lottery_no_set=$lottery_no_set."'".$rowlottery[lottery_no]."'".',';
		}
		$lottery_no_set=substr($lottery_no_set,0,-1);
	}
	else if($weather_shield_qty >=100 && $dhalai_master_qty < 100 ){
	$sellottery="SELECT lottery_no FROM lottery_master where is_active='Y' ORDER BY lottery_no ASC LIMIT 0,3";
	$rslottery=mysql_query($sellottery);
		while($rowlottery=mysql_fetch_array($rslottery)){
		$lottery_no_set=$lottery_no_set."'".$rowlottery[lottery_no]."'".',';
		}
	 $lottery_no_set=substr($lottery_no_set,0,-1);
	}
	else
	{
	$sellottery="SELECT (MAX(lottery_no)+1) as max_lottery_no FROM lottery_master where is_active='N'";
	$rslottery=mysql_query($sellottery);
	$rowlottery=mysql_fetch_array($rslottery);
	$lottery_no_set=$rowlottery['max_lottery_no'];
	}
	$sqlupdatescheme="UPDATE $consumer_scheme SET lottery_no='".str_replace("'","",$lottery_no_set)."',is_duplicate='yes' WHERE `trans_id`='$trans_id'";
	$rsupdatescheme=mysql_query($sqlupdatescheme);
		$sqlupdatelotterym="UPDATE lottery_master SET is_active='N' WHERE `lottery_no` IN(".$lottery_no_set.")";
	$rsupdatelotterym=mysql_query($sqlupdatelotterym);
	
	//exit();
}
}
echo $countexe .'rows EXECUTED';
?>
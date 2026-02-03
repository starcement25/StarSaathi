<?php
include "star_connection.php";
$emp_code=$_REQUEST['emp_code'];
$user_type = $_REQUEST['user_type'] ? strtolower(trim($_REQUEST['user_type'])) : ""; /* sub dealer/dealer/broker  */
$condition_branch="";
$branch_game_status = "branch_game_status";
$customer_master = "customer_master";

$sqlempbranch="SELECT branch_code FROM customer_master WHERE customer_code='".$emp_code."'";
$rsempbranch=mysql_query($sqlempbranch);
$rowempbranch=mysql_fetch_array($rsempbranch);
$branch_code=$rowempbranch['branch_code'];

if($branch_code!='')
{
	$sqlquery="SELECT * FROM $branch_game_status where branch_code='".$branch_code."'";
	$result = mysql_query($sqlquery);
	$count=mysql_num_rows($result);
	if($count>0){
		$rowgame=mysql_fetch_array($result);
		$gamestatus=$rowgame['game_status'];
		
		if($gamestatus=='ACTIVE')
		{
			$banner_link= BASE_URL .'banner/banner.jpeg';
			$game_status_data[] = array("customer_code"=>$customer_code,"banner_link"=>$banner_link);
			$res_data = array("process_status"=>"YES","process_message"=>"Success.","banner_data"=>$game_status_data);
		}
		else
		{
			$res_data = array("process_status"=>"NO","process_message"=>"Game status not active for this dealer.");
		}
		
	}
	else
	{
		$res_data = array("process_status"=>"NO","process_message"=>"Game status not active for this dealer.");
	}
}
else
{
	$res_data = array("process_status"=>"NO","process_message"=>"Active dealer not found.");
}
echo json_encode($res_data);
	mysql_close($link);			
?>

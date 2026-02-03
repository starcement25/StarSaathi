<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$user_type = $_REQUEST['user_type'] ? strtolower(trim($_REQUEST['user_type'])) : "";
$the_broker_dns_id = $_REQUEST['broker_id'] ? strtolower(trim($_REQUEST['broker_id'])) : "";
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);
$broker_master = "broker_master";
$sp_destination = "sp_destination";
$customer_broker_relation = "customer_broker_relation";
$destination_master = "destination_master";
$customer_destination = "customer_destination";
$tagged_cust_code_arr = array();
$tagged_cust_code_str = "";
$tagged_cust_branch_arr = array();
$tagged_cust_branch_str = "";
$totres_spdes = 0;
if($user_type=="broker"){
	$sql1 = "select `broker_id` from $broker_master where `dns_broker_id`='$emp_code' ";
	$res1 = mysql_query($sql1);
	$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_array($res1);
		$broker_id = $row1["broker_id"];
		
		$sql_spdes = "select $destination_master.destination_code,$destination_master.destination_name,$destination_master.ex_for_type from $sp_destination left join $destination_master on $sp_destination.`destination_code`=$destination_master.`destination_code` and $sp_destination.`broker_id`='$broker_id' and $sp_destination.`acedns`='Y' ORDER BY $destination_master.`destination_name` ASC ";
		$res_spdes = mysql_query($sql_spdes);
		$totres_spdes = mysql_num_rows($res_spdes);
		
		
		$sql2 = "select `customer_code` from $customer_broker_relation where `broker_code`='$broker_id' ";
		$res2 = mysql_query($sql2);
		$totres2 = mysql_num_rows($res2);
		if($totres2>0){
			while($row2 = mysql_fetch_array($res2)){
				$customer_code_ftc = $row2["customer_code"] ? trim($row2["customer_code"]) : "";
				if($customer_code_ftc!=""){
					$tagged_cust_code_arr[] = $customer_code_ftc;
				}
			}
			if(count($tagged_cust_code_arr)>0){
				$tagged_cust_code_str = implode("','",$tagged_cust_code_arr);
				
$sql3 = "SELECT branch_code FROM customer_master WHERE customer_code in('".$tagged_cust_code_str."') group by branch_code";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
while($row3=mysql_fetch_array($res3)){
$the_branch_raw = $row3['branch_code'] ? trim($row3["branch_code"]) : "";
if($the_branch_raw!=""){
	$the_branch_raw_arr = array();
	$the_branch_raw_arr = explode(",",$the_branch_raw);
	foreach($the_branch_raw_arr as $the_branch_raw_arr_val){
		$tagged_cust_branch_arr[] = $the_branch_raw_arr_val;
	}
}
}
if(count($tagged_cust_branch_arr)>0){
	$tagged_cust_branch_str = implode("','",$tagged_cust_branch_arr);
}
}
				
			}
		}
	}
}else{

if($the_broker_dns_id!=""){
$sql1 = "select `broker_id` from $broker_master where `dns_broker_id`='$the_broker_dns_id' ";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
$row1 = mysql_fetch_array($res1);
$broker_id = $row1["broker_id"];

$sql_spdes = "select $destination_master.destination_code,$destination_master.destination_name,$destination_master.ex_for_type from $sp_destination left join $destination_master on $sp_destination.`destination_code`=$destination_master.`destination_code` and $sp_destination.`broker_id`='$broker_id' and $sp_destination.`acedns`='Y' ORDER BY $destination_master.`destination_name` ASC ";
$res_spdes = mysql_query($sql_spdes);
$totres_spdes = mysql_num_rows($res_spdes);

}
}
	
}
if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(BDF.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
if(destination=='yes' && branch_wise_destination=='yes')
{
	if(employeewise_hierarchy=='yes'){
		$employee_hierarchy=return_employee_hierarchy($emp_code);
		$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
	}
	else
	{
		$emp_hierarchy_condition="emp_code='".$emp_code."'";
	}
	//$sqlempbranch="SELECT branch_code FROM employee_master WHERE ".$emp_hierarchy_condition;
	
	if($user_type=="broker"){
		
		/*$condition_branch=" AND branch_code IN ('".$tagged_cust_branch_str."')";
		$sqlquery="SELECT DM.destination_code,DM.destination_name,DM.ex_for_type FROM destination_master DM,branch_destination_freight BDF
WHERE DM.destination_code=BDF.destination_code and BDF.acedns='Y' and BDF.acedns!=''  ".$condition_branch.$login_condition." ORDER BY destination_name ASC";*/
if(count($tagged_cust_code_arr)>0){
$tagged_cust_code_str = implode("','",$tagged_cust_code_arr);
}
$sqlquery= "select DISTINCT $destination_master.destination_code,$destination_master.destination_name,$destination_master.ex_for_type from $customer_destination left join $destination_master on $customer_destination.`destination_code`=$destination_master.`destination_code` where $customer_destination.`customer_code` in('".$tagged_cust_code_str."') and $customer_destination.`destination_code`!='' and $customer_destination.`acedns`='Y' ORDER BY $destination_master.`destination_name` ASC";

	}else{
	/*$sqlempbranch="SELECT branch_code FROM customer_master WHERE customer_code='".$emp_code."'";
	$rsempbranch=mysql_query($sqlempbranch);
	$rowempbranch=mysql_fetch_array($rsempbranch);
	$branch_code=$rowempbranch['branch_code'];
	$branch_value_array=explode(',',$branch_code);
	$branch_value_final = "'".implode("','", $branch_value_array)."'";
	$condition_branch=' AND branch_code IN ('.$branch_value_final.')';
$sqlquery="SELECT DM.destination_code,DM.destination_name,DM.ex_for_type FROM destination_master DM,branch_destination_freight BDF
WHERE DM.destination_code=BDF.destination_code and BDF.acedns='Y' and BDF.acedns!='' ".$condition_branch.$login_condition." ORDER BY destination_name ASC";*/
$sqlquery= "select $destination_master.destination_code,$destination_master.destination_name,$destination_master.ex_for_type from $customer_destination left join $destination_master on $customer_destination.`destination_code`=$destination_master.`destination_code` where $customer_destination.`customer_code`='".$emp_code."' and $customer_destination.`destination_code`!='' and $customer_destination.`acedns`='Y' ORDER BY $destination_master.`destination_name` ASC";
	}
}
else
{
	$sqlquery="SELECT BDF.destination_code,BDF.destination_name,BDF.ex_for_type FROM destination_master BDF WHERE 1 ".$login_condition." ORDER BY BDF.destination_name ASC";
}

$des_ids = array();
$result = mysql_query($sqlquery);
$count=mysql_num_rows($result);
	$cnt=1;
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowdestination = mysql_fetch_array($result))
		{
			$the_del_des_code = $rowdestination['destination_code'];
			$des_ids[] = $the_del_des_code;
			$contents  = (($the_del_des_code!='') ? $the_del_des_code : ' ')."^";
			$contents  .= (($rowdestination['destination_name']!='')?$rowdestination['destination_name']: ' ')."^";
			$contents  .= (($rowdestination['ex_for_type']!='')?$rowdestination['ex_for_type']: ' ');
			
			$linecontents  .= $contents."\n";
		}
		$tot_sp_cnt = 0;
		if($totres_spdes>0){
			while($row_spdes = mysql_fetch_array($res_spdes))
			{
				$the_sp_des_code = $row_spdes['destination_code'] ? $row_spdes['destination_code'] : "";
				if(!in_array($the_sp_des_code,$des_ids)){
					$tot_sp_cnt++;
			$contents  = (($the_sp_des_code!='') ? $the_sp_des_code : ' ')."^";
			$contents  .= (($row_spdes['destination_name']!='')?$row_spdes['destination_name']: ' ')."^";
			$contents  .= (($row_spdes['ex_for_type']!='')?$row_spdes['ex_for_type']: ' ');
			$linecontents  .= $contents."\n";
				}
			}
		}
		
		$tot_cnt = ($count+$tot_sp_cnt);
		$contentsrowcolumn  =$tot_cnt.'¥'.'3';
		
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		
		if($totres_spdes>0){
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
			while($row_spdes = mysql_fetch_array($res_spdes))
			{
			$contents  = (($row_spdes['destination_code']!='') ? $row_spdes['destination_code'] : ' ')."^";

			$contents  .= (($row_spdes['destination_name']!='') ? $row_spdes['destination_name'] : ' ')."^";
			$contents  .= (($row_spdes['ex_for_type']!='') ? $row_spdes['ex_for_type'] : ' ');
			
			$linecontents  .= $contents."\n";
			}
			
		$contentsrowcolumn  =$totres_spdes.'¥'.'3';
			$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
		}else{
			$last_update_time=str_replace('?','',$last_update_time);
			$data_download_time=str_replace('?','',$data_download_time);
			if(strtotime($data_download_time)>=strtotime($last_update_time))
			{
			$datacontents = '0'.'¥'.'0';
			}
			else
			{
			$datacontents = '0'.'¥'.'2';
			}
		}
		
		
		
	}
	
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/destination-master-txt_v2-6.0.4.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=destination_master.txt");
	print "$datacontents"; 
	mysql_close($link);		
?>

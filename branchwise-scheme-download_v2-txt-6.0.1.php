<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
$user_type = $_REQUEST['user_type'] ? strtolower(trim($_REQUEST['user_type'])) : "";
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);
$condition_branch="";
$broker_master = "broker_master";
$customer_broker_relation = "customer_broker_relation";
$tagged_cust_code_arr = array();
$tagged_cust_code_str = "";
$tagged_cust_branch_arr = array();
$tagged_cust_branch_str = "";

if($user_type=="broker"){
	$sql1 = "select `broker_id` from $broker_master where `dns_broker_id`='$emp_code' ";
	$res1 = mysql_query($sql1);
	$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_array($res1);
		$broker_id = $row1["broker_id"];
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
	$condition_branch=" AND branch_code IN ('".$tagged_cust_branch_str."')";
}

}
				
			}
		}
	}
}else{


$sqlempbranch="SELECT branch_code FROM customer_master WHERE customer_code='".$emp_code."'";
$rsempbranch=mysql_query($sqlempbranch);
$rowempbranch=mysql_fetch_array($rsempbranch);
$branch_code=$rowempbranch['branch_code'];
$tagged_cust_branch_arr=explode(',',$branch_code);
$branch_value_final = "'".implode("','", $tagged_cust_branch_arr)."'";
$condition_branch=' AND branch_code IN ('.$branch_value_final.')';

}

if(count($tagged_cust_branch_arr)>0)
{
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
	$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
	$linecontents='';
	/*$sqlquery="SELECT * FROM (SELECT PDF_file_name,branch_code,acedns FROM branch_schemes_PDF WHERE 
			FIND_IN_SET(branch_code,'".$branch_code."') AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')
			ORDER BY download_time DESC) AS SAT GROUP BY 2 ORDER BY 2 ASC ";*/
	/*$sqlquery="SELECT * FROM `branch_schemes_PDF` where `branch_code`='$branch_code' and UNIX_TIMESTAMP(`download_time`) > UNIX_TIMESTAMP('".$last_update_time."') order by `download_time` desc";*/
$sqlquery="SELECT * FROM `branch_schemes_PDF` where `acedns`='Y' ".$condition_branch." and CURDATE() 
			between `start_date` and `end_date` order by `download_time` desc";
	$result = mysql_query($sqlquery);
	if(!$result){
	echo mysql_error();
}
	$count=mysql_num_rows($result);
	$cnt=1;
	if($count>0){
		$the_PDF_file_name_arr = array();
		while($rowschemePDF = mysql_fetch_array($result))
		{
			$the_PDF_file_name_arr = array();
			$the_branch_code = $rowschemePDF['branch_code'] ? $rowschemePDF['branch_code'] : ' ';
			$the_PDF_file_name = $rowschemePDF['PDF_file_name'] ? $rowschemePDF['PDF_file_name'] : '';
			$the_acedns = $rowschemePDF['acedns'] ? $rowschemePDF['acedns'] : ' ';			
			$contents  = $the_branch_code."^";
			$contents  .= $the_PDF_file_name."^";
			$contents  .= $the_acedns;
			
			$linecontents  .= $contents."\n";
			$cnt++;
		}
		$contentsrowcolumn=$count.'¥'.'3';
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
}
else
{
	$datacontents = '0'.'¥'.'0';
}
header("Content-type: application/text"); 
header("Content-Disposition: attachment; filename=branch_scheme_PDF.txt");
print "$datacontents"; 		
?>

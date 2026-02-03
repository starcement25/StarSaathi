<?php
ini_set('memory_limit', '-1');
set_time_limit(1000);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
$user_type = $_REQUEST['user_type'] ? strtolower(trim($_REQUEST['user_type'])) : "";
//$emp_code='100017206';
$incremental_download=$_REQUEST['incremental_download'];
//$last_update_time='2014-06-06 13:40:25';
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

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
}

}
				
			}
		}
	}
}

if(vertical_fields=='yes'){
	$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempvertical=mysql_query($sqlempvertical);
	$rowempvertical=mysql_fetch_array($rsempvertical);
	$emp_vertical_value=$rowempvertical['vertical_value'];
	$emp_vertical_value_array=explode(',',$emp_vertical_value);
	$condition_one=" AND (";
	$condition_two='';
	foreach($emp_vertical_value_array as $emp_vertical_values)
	{
		$condition_two.=" FIND_IN_SET( '".$emp_vertical_values."',PM.vertical_value) OR";
	}
	$condition_two=substr($condition_two,0,-2);
	$condition_one.=$condition_two.")";
}
else
{
	$condition_one="";
}
if($incremental_download=='no')
{
		$login_condition="AND PM.acedns='Y' AND PM.black_list='N'";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(PM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
$rsbranches=mysql_query($sqlbranches);
$countbranches=mysql_num_rows($rsbranches);
if($countbranches>1)
{
	if(branch_wise_product=='yes' || $nick_name=='DNV')
	{
		if($user_type=="broker"){
			$condition_branch=" AND PM.branch_code IN ('".$tagged_cust_branch_str."')";
			$sqlquery="SELECT DISTINCT PM.* FROM product_master PM
                   WHERE PM.prod_desc <>''  
					 ".$condition_branch.$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC";
			
		}else{
		$sqlempbranch="SELECT branch_code FROM customer_master WHERE customer_code='".$emp_code."'";
		$rsempbranch=mysql_query($sqlempbranch);
		$rowempbranch=mysql_fetch_array($rsempbranch);
		$branch_value=$rowempbranch['branch_code'];
		$state=$rowempbranch['state'];
		$branch_value_array=explode(',',$branch_value);
		$branch_value = "'".implode("','", $branch_value_array)."'";
		$condition_branch=' AND PM.branch_code IN ('.$branch_value.')';
		$sqlquery="SELECT DISTINCT PM.* FROM product_master PM
                   WHERE PM.prod_desc <>''  
					 ".$condition_branch.$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC";
		}
	}
	else
	{
		$sqlquery="SELECT DISTINCT PM.* FROM product_master PM WHERE PM.prod_desc <>'' ".$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC";
	}
}
else
{
	$sqlquery="SELECT DISTINCT PM.* FROM product_master PM WHERE PM.prod_desc <>'' ".$condition_one." ".$login_condition." ORDER BY PM.prod_desc ASC";
}
			
$result = mysql_query($sqlquery);
$count=mysql_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'26';
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time;
		while($rowproduct = mysql_fetch_array($result))
		{
				$TD=$rowproduct['TD'];
			$contents  = (($rowproduct['prod_code']!='')?$rowproduct['prod_code']: ' ')."^";
			$contents  .= (($rowproduct['product_group_code']!='')?$rowproduct['product_group_code']: ' ')."^";
			$contents  .= (($rowproduct['product_group_name']!='')?$rowproduct['product_group_name']: ' ')."^";
			$contents  .= (($rowproduct['product_sub_group_code']!='')?$rowproduct['product_sub_group_code']: ' ')."^";
			$contents  .= (($rowproduct['product_sub_group_name']!='')?$rowproduct['product_sub_group_name']: ' ')."^";
			$contents  .= (($rowproduct['product_brand_code']!='')?$rowproduct['product_brand_code']: ' ')."^";
			$contents  .= (($rowproduct['product_brand_name']!='')?$rowproduct['product_brand_name']: ' ')."^";
			$contents  .= (($rowproduct['prod_desc']!='')?$rowproduct['prod_desc']: ' ')."^";
			$contents  .= (($rowproduct['black_list']!='')?$rowproduct['black_list']: ' ')."^";
			$contents  .= (($rowproduct['acedns']!='')?$rowproduct['acedns']: ' ')."^";
			$contents  .= (($rowproduct['UOM1']!='')?$rowproduct['UOM1']: ' ')."^";
			$contents  .= (($rowproduct['UOM2']!='')?$rowproduct['UOM2']: ' ')."^";
			$contents  .= (($rowproduct['conversion_factor']!='')?$rowproduct['conversion_factor']: ' ')."^";
			$contents  .= (($rowproduct['pack_size']!='')?$rowproduct['pack_size']: ' ')."^";
			$contents  .= (($rowproduct['UOM3']!='')?$rowproduct['UOM3']: ' ')."^";
			$contents  .= (($rowproduct['conversion_factor_two']!='')?$rowproduct['conversion_factor_two']: ' ')."^";
			$contents  .= (($TD!='')?$TD: 0)."^";
			$contents  .= (($rowproduct['branch_code']!='')?$rowproduct['branch_code']: ' ')."^";
			$contents  .= (($rowproduct['vertical_value']!='')?$rowproduct['vertical_value']: ' ')."^";
			$contents  .= (($rowproduct['secondary_unit']!='')?$rowproduct['secondary_unit']: ' ')."^";
			$contents  .= (($rowproduct['dns_prod_code']!='')?$rowproduct['dns_prod_code']: ' ')."^";
			$contents  .= (($rowproduct['focus']!='')?$rowproduct['focus']: ' ')."^";
			$contents  .= (($rowproduct['weightage']!='')?$rowproduct['weightage']: ' ')."^";
			$contents  .= (($rowproduct['vat']!='')?$rowproduct['vat']: ' ')."^";
			$contents  .= (($rowproduct['addl_vat']!='')?$rowproduct['addl_vat']: ' ')."^";
			$contents  .= (($rowproduct['freight_cost']!='')?$rowproduct['freight_cost']: ' ');
			$linecontents  .= "\n".$contents;
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$last_update_time=str_replace('?','',$last_update_time);
		$data_download_time=str_replace('?','',$data_download_time);
		if(strtotime($data_download_time)>=strtotime($last_update_time))
		{
			$datacontents = '0'.'¥'.'0';
		}
		else
		{
			$datacontents = '0'.'¥'.'26';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://starsaathi.com/SAP/product-master-txt-incremental_v2-6.0.10.php?nick_name=$nick_name&emp_code=$emp_code&user_type=$user_type&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=product_master.txt");
	print "$datacontents";	
	mysql_close($link);
?>

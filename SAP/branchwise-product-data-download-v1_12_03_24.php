<?php
ini_set('memory_limit', '-1');
set_time_limit(1000);
$nick_name='START';
require("include/config.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
$user_type = $_REQUEST['user_type'] ? strtolower(trim($_REQUEST['user_type'])) : "";

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
	$condition_branch=" AND PM.branch_code IN ('".$tagged_cust_branch_str."')";
	$sqlquery="SELECT DISTINCT PM.prod_code,PM.prod_desc,PM.dns_prod_code,PM.product_group_code,PM.branch_code FROM product_master PM WHERE PM.prod_desc <>'' AND PM.acedns='Y' 
			".$condition_branch." ORDER BY PM.prod_desc ASC";
}
else{
		$sqlempbranch="SELECT branch_code FROM customer_master WHERE customer_code='".$emp_code."'";
		$rsempbranch=mysql_query($sqlempbranch);
		$rowempbranch=mysql_fetch_array($rsempbranch);
		$branch_value=$rowempbranch['branch_code'];
		$state=$rowempbranch['state'];
		$branch_value_array=explode(',',$branch_value);
		$branch_value = "'".implode("','", $branch_value_array)."'";
		$condition_branch=' AND PM.branch_code IN ('.$branch_value.')';
		$sqlquery="SELECT DISTINCT PM.prod_code,PM.prod_desc,PM.dns_prod_code,PM.product_group_code,PM.branch_code 
					FROM product_master PM
                   WHERE PM.prod_desc <>'' AND PM.acedns='Y'  
					 ".$condition_branch." ORDER BY PM.prod_desc ASC";
}
$result = mysql_query($sqlquery);
$count=mysql_num_rows($result);
	$cnt=1;
	if($count>0){
		while($rowproduct = mysql_fetch_array($result))
		{
			$prod_code=$rowproduct['prod_code'];
			$prod_desc =  preg_replace('/[\x80-\xFF]/', '', $rowproduct["prod_desc"]);
			$dns_prod_code=$rowproduct['dns_prod_code'];
			$product_group_code=$rowproduct['product_group_code'];
			$acedns=$rowproduct['acedns'];
			$black_list=$rowproduct['black_list'];
			$branch_code=$rowproduct['branch_code'];
			
			
			$product_date[] = array("prod_code"=>$prod_code,"prod_desc"=>$prod_desc,"dns_prod_code"=>$dns_prod_code,"product_group_code"=>$product_group_code,"branch_code"=>$branch_code);
			$res_data = array("process_status"=>"YES","process_message"=>"Success.","product_date"=>$product_date);

		}
	}
	else
	{
		$res_data = array("process_status"=>"NO","process_message"=>"No  product data found.");
	}
	/*$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://starsaathi.com/SAP/product-master-txt-incremental_v2-6.0.10.php?nick_name=$nick_name&emp_code=$emp_code&user_type=$user_type&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);*/
	echo json_encode($res_data);
	mysql_close($link);
?>

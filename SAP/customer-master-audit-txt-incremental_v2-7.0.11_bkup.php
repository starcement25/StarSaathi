<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$user_type = $_REQUEST['user_type'] ? strtolower(trim($_REQUEST['user_type'])) : "";
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);
$broker_master = "broker_master";
$customer_broker_relation = "customer_broker_relation";
$tagged_cust_code_arr = array();
$tagged_cust_code_str = "";
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
			}
		}
	}
}

if($incremental_download=='no')
{
	$login_condition=" AND acedns='Y'";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
$rsbranches=mysql_query($sqlbranches);
$countbranches=mysql_num_rows($rsbranches);

		if($user_type=="broker"){
			
		$sqlquerycustomerroute="SELECT customer_code,customer_name,emp_code,current_balance,credit_limit,black_list,acedns,TD,cust_type,rds_tag,sauda_validity_period,address,phone_no,pin,landline_no,owner_name,owner_phone,cust_class,weekly_closing_day,coverage_type,TIN,PAN,minimum_stock,branch_code,visit_day,email,sauda_limit,pending_qty,route_code,customer_id 
		FROM customer_master WHERE 
		1 ".$login_condition." AND customer_code IN('".$tagged_cust_code_str."') or customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag in('".$tagged_cust_code_str."')  AND acedns='Y') ORDER BY route_code DESC,acedns DESC";
		
		}else{
		
		/*$sqlquerycustomerroute="SELECT customer_code,customer_name,emp_code,current_balance,credit_limit,black_list,acedns,
							TD,cust_type,rds_tag,sauda_validity_period,address,phone_no,pin,landline_no,owner_name,owner_phone,
							 cust_class,weekly_closing_day,coverage_type,TIN,PAN,minimum_stock,branch_code,visit_day,email,sauda_limit,pending_qty,route_code 
							 FROM customer_master WHERE 
							 1 ".$login_condition." AND (
							 customer_code='".$emp_code."' 
							 OR customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$emp_code."') AND acedns='Y' 
							 )
							ORDER BY route_code DESC,acedns DESC";*/
			$sqlquerycustomerroute="SELECT customer_code,customer_name,emp_code,current_balance,credit_limit,black_list,acedns,
							TD,cust_type,rds_tag,sauda_validity_period,address,phone_no,pin,landline_no,owner_name,owner_phone,
							 cust_class,weekly_closing_day,coverage_type,TIN,PAN,minimum_stock,branch_code,visit_day,email,sauda_limit,pending_qty,route_code,customer_id
							 FROM customer_master WHERE 
							 1 ".$login_condition." AND (
							 customer_code='".$emp_code."' 
							 OR customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$emp_code."' AND order_restriction='no') AND acedns='Y' 
							 ) AND cust_type!='Ship to Party-dealer' AND cust_type!='ShiptoParty-Subdeale'
							ORDER BY route_code DESC,acedns DESC";				
		}
		$resultcustomer = mysql_query($sqlquerycustomerroute);
		$countcustomer=mysql_num_rows($resultcustomer);
		if($countcustomer>0){
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
			
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
			$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second;
	
			$customer_count=0;
			$customebr_code_array=array();
			while($rowcustomer = mysql_fetch_array($resultcustomer))
			{
				$customer_code=$rowcustomer['customer_code'];
				$route_code=$rowcustomer['route_code'];
				$acedns=$rowcustomer['acedns'];
				
				if(!in_array($customer_code,$customebr_code_array))
				{
					if($rowcustomer['customer_name']!=''){
					$contents  = (($customer_code!='')?$customer_code: ' ')."^";
					$contents  .= (($rowcustomer['customer_name']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcustomer['customer_name'])): ' ')."^";
					$contents  .= (($route_code!='')?$route_code: ' ')."^";
					$contents  .= (($rowcustomer['emp_code']!='')?$rowcustomer['emp_code']: ' ')."^";
					$contents  .= (($rowcustomer['current_balance']!='')?$rowcustomer['current_balance']: ' ')."^";
					$contents  .= (($rowcustomer['credit_limit']!='')?$rowcustomer['credit_limit']: ' ')."^";
					$contents  .= (($acedns!='')?$acedns: ' ')."^";
					$contents  .= (($rowcustomer['black_list']!='')?$rowcustomer['black_list']: ' ')."^";
					$contents  .= (($rowcustomer['TD']!='')?$rowcustomer['TD']: '0')."^";
					$contents  .= (($rowcustomer['cust_type']!='')?$rowcustomer['cust_type']: ' ')."^";
					$contents  .= (($rowcustomer['rds_tag']!='')?$rowcustomer['rds_tag']: ' ')."^";
					$contents  .= (($rowcustomer['sauda_validity_period']!='')?$rowcustomer['sauda_validity_period']: ' ')."^";
					$contents  .= (($rowcustomer['address']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcustomer['address'])): ' ')."^";
					$contents  .= (($rowcustomer['pin']!='')?$rowcustomer['pin']: ' ')."^";
					$contents  .= (($rowcustomer['phone_no']!='')?$rowcustomer['phone_no']: ' ')."^";
					$contents  .= (($customer_code!='')?$customer_code: ' ')."^";// For dns customer code forcefully given the original customer code
					$contents  .= (($rowcustomer['landline_no']!='')?$rowcustomer['landline_no']: ' ')."^";
					$contents  .= (($rowcustomer['owner_name']!='')?$rowcustomer['owner_name']: ' ')."^";
					$contents  .= (($rowcustomer['owner_phone']!='')?$rowcustomer['owner_phone']: ' ')."^";
					$contents  .= (($rowcustomer['cust_class']!='')?$rowcustomer['cust_class']: ' ')."^";
					$contents  .= (($rowcustomer['weekly_closing_day']!='')?$rowcustomer['weekly_closing_day']: ' ')."^";
					$contents  .= (($rowcustomer['coverage_type']!='')?$rowcustomer['coverage_type']: ' ')."^";
					$contents  .= (($rowcustomer['TIN']!='')?$rowcustomer['TIN']: ' ')."^";
					$contents  .= (($rowcustomer['PAN']!='')?$rowcustomer['PAN']: ' ')."^";
					$contents  .= (($rowcustomer['minimum_stock']!='')?$rowcustomer['minimum_stock']: ' ')."^";
					$contents  .= (($rowcustomer['branch_code']!='')?$rowcustomer['branch_code']: ' ')."^";
					$contents  .= (($rowcustomer['visit_day']!='')?$rowcustomer['visit_day']: ' ')."^";
					$contents  .= (($rowcustomer['email']!='')?$rowcustomer['email']: ' ')."^";
					$contents  .= (($rowcustomer['sauda_limit']!='')?$rowcustomer['sauda_limit']: ' ')."^";
					$contents  .= (($rowcustomer['pending_qty']!='')?$rowcustomer['pending_qty']: ' ')."^";
					$contents  .= (($rowcustomer['customer_id']!='')?$rowcustomer['customer_id']: ' ');	
					$linecontents  .= "\n".$contents;
					//For secondary sales all distributor downloading
					/*if(!in_array($rowcustomer['rds_tag'],$customebr_code_array))
					{
						$sqlrdswisecustomer="SELECT  customer_name,route_code,current_balance,credit_limit,black_list,
											acedns,TD,cust_type,rds_tag,sauda_validity_period,address,phone_no,pin,landline_no,owner_name,
											owner_phone,cust_class,weekly_closing_day,coverage_type,TIN,PAN,minimum_stock,branch_code,visit_day,email,sauda_limit 
											FROM customer_master WHERE customer_code='".$rowcustomer['rds_tag']."'";
						$rsrdswisecustomer=mysql_query($sqlrdswisecustomer);
						$rowrdswisecustomer=mysql_fetch_array($rsrdswisecustomer);
						
						$sqlacednsselect="SELECT acedns FROM customer_route_emp_relation WHERE customer_code='".$rowcustomer['rds_tag']."' AND emp_code='".$emp_code."'";
						$rsacednsselect=mysql_query($sqlacednsselect);
						$rowacednsselect=mysql_fetch_array($rsacednsselect);
						$cntacednsselect=mysql_num_rows($rsacednsselect);
						if($cntacednsselect >0)  $acednsrds=$rowacednsselect['acedns'];
						else					 $acednsrds='Y';
						
						if($rowrdswisecustomer['customer_name']!=''){
						$contents  = (($rowcustomer['rds_tag']!='')?$rowcustomer['rds_tag']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['customer_name']!='')?trim(preg_replace('/[\r\n]+/', '',$rowrdswisecustomer['customer_name'])): ' ')."^";
						$contents  .= (($rowrdswisecustomer['route_code']!='')?$rowrdswisecustomer['route_code']: ' ')."^";
						$contents  .= (($emp_code!='')?$emp_code: ' ')."^";
						$contents  .= (($rowrdswisecustomer['current_balance']!='')?$rowrdswisecustomer['current_balance']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['credit_limit']!='')?$rowrdswisecustomer['credit_limit']: ' ')."^";
						$contents  .= (($acednsrds!='')?$acednsrds: ' ')."^";
						$contents  .= (($rowrdswisecustomer['black_list']!='')?$rowrdswisecustomer['black_list']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['TD']!='')?$rowrdswisecustomer['TD']: '0')."^";
						$contents  .= (($rowrdswisecustomer['cust_type']!='')?$rowrdswisecustomer['cust_type']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['rds_tag']!='')?$rowrdswisecustomer['rds_tag']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['sauda_validity_period']!='')?$rowrdswisecustomer['sauda_validity_period']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['address']!='')?trim(preg_replace('/[\r\n]+/', '',$rowrdswisecustomer['address'])): ' ')."^";
						$contents  .= (($rowrdswisecustomer['pin']!='')?$rowrdswisecustomer['pin']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['phone_no']!='')?$rowrdswisecustomer['phone_no']: ' ')."^";
						$contents  .= (($rowcustomer['rds_tag']!='')?$rowcustomer['rds_tag']: ' ')."^";// For dns customer code forcefully given the original customer code
						$contents  .= (($rowrdswisecustomer['landline_no']!='')?$rowrdswisecustomer['landline_no']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['owner_name']!='')?$rowrdswisecustomer['owner_name']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['owner_phone']!='')?$rowrdswisecustomer['owner_phone']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['cust_class']!='')?$rowrdswisecustomer['cust_class']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['weekly_closing_day']!='')?$rowrdswisecustomer['weekly_closing_day']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['coverage_type']!='')?$rowrdswisecustomer['coverage_type']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['TIN']!='')?$rowrdswisecustomer['TIN']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['PAN']!='')?$rowrdswisecustomer['PAN']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['minimum_stock']!='')?$rowrdswisecustomer['minimum_stock']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['branch_code']!='')?$rowrdswisecustomer['branch_code']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['visit_day']!='')?$rowrdswisecustomer['visit_day']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['email']!='')?$rowrdswisecustomer['email']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['sauda_limit']!='')?$rowrdswisecustomer['sauda_limit']: ' ')."^";
						$contents  .= (($rowrdswisecustomer['pending_qty']!='')?$rowrdswisecustomer['pending_qty']: ' ');

						array_push($customebr_code_array,$rowcustomer['rds_tag']);
						$linecontents  .= $contents."\n";
						$customer_count++;

						}
					}*/
					
					 array_push($customebr_code_array,$customer_code);
					 $customer_count++;
					}
				}
			}
			$contentsrowcolumn=$customer_count.'¥'.'31';
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
				$datacontents = '0'.'¥'.'31';
			}
		}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/customer-master-audit-txt-incremental_v2-7.0.11.phpp?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/customer-master-audit-txt-incremental-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
	$insertPos=0;  // variable for saving 
	while (!feof($file)) {
		$line=fgets($file);
		if (strpos($line, 'http://')!==false) {
			$insertPos=ftell($file);
			$newline =  $newuser;
		}
		else
		{
			$newline.=$line;   // append existing data with new data of user
		}

	}
	fseek($file,$insertPos);   // move pointer to the file position where we saved above 
	fwrite($file, $newline);
	fclose($file);*/	

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=customer_master.txt");
	print "$datacontents"; 
	mysql_close($link);		
?>

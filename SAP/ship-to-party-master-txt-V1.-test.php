<?php
include "star_connection.php";
$emp_code=$_REQUEST['emp_code'];

$user_type = $_REQUEST['user_type'] ? strtolower(trim($_REQUEST['user_type'])) : "";
$the_broker_dns_id = $_REQUEST['broker_id'] ? strtolower(trim($_REQUEST['broker_id'])) : "";
$broker_master = "broker_master";
$customer_master = "customer_master";
$sp_destination = "sp_destination";
$customer_broker_relation = "customer_broker_relation";
$destination_master = "destination_master";
$customer_destination = "customer_destination";
$qZSDCUST = "pZSDCUST";
$qtblcustomermaster = "ptblcustomermaster";
$tagged_cust_code_arr = array();
$tagged_cust_code_str = "";
$tagged_cust_branch_arr = array();
$tagged_cust_branch_str = "";
$totres_spdes = 0;
$user_type;

$sqlcustcode="SELECT customer_id FROM $customer_master WHERE  customer_code='".$emp_code."'";
$rscustcode=mysql_query($sqlcustcode);
$rowcustcode=mysql_fetch_array($rscustcode);
$customer_id=$rowcustcode['customer_id'];

if($user_type=="dealer"){
	
	/*$sqlshiptoparty="SELECT DISTINCT $customer_master.dns_customer_code,$customer_master.customer_name,$destination_master.destination_name 
					FROM $qZSDCUST,$customer_master,$customer_destination,$destination_master  WHERE $qZSDCUST.SOLD_PARTY=$customer_master.customer_id 
					AND $customer_master.customer_code=$customer_destination.customer_code AND $destination_master.destination_code=$customer_destination.destination_code   
					AND $qZSDCUST.PARTY='".$customer_id."' AND $customer_master.cust_type='Ship to Party-dealer'";*/
	$sqlshiptoparty="SELECT DISTINCT $customer_master.dns_customer_code,$customer_master.customer_code,$customer_master.customer_name,
					$customer_master.address,$customer_master.phone_no,$destination_master.destination_name 
					FROM $customer_master,$customer_destination,$destination_master  WHERE $customer_master.customer_code=$customer_destination.customer_code 
					AND $destination_master.destination_code=$customer_destination.destination_code   
					AND $customer_master.rds_tag='".$emp_code."' AND $customer_master.cust_type='Ship to Party-dealer'";				
	$rsshiptoparty=mysql_query($sqlshiptoparty);
	$countshiptoparty=mysql_num_rows($rsshiptoparty);
	if($countshiptoparty >0)
	{
		$sqldealerdestination="SELECT $customer_master.dns_customer_code,$customer_master.customer_code,$customer_master.customer_name,
					$customer_master.address,$customer_master.phone_no,$destination_master.destination_name 
					FROM $customer_master,$customer_destination,$destination_master WHERE 
					$customer_master.customer_code=$customer_destination.customer_code AND
					$destination_master.destination_code=$customer_destination.destination_code AND $customer_destination.customer_code='".$emp_code."'";
		$rsdealerdestination=mysql_query($sqldealerdestination);
		$rowdealerdestination=mysql_fetch_array($rsdealerdestination);
		$dealer_destination_name=$rowdealerdestination['destination_name'];
		$dealer_dns_customer_code=$rowdealerdestination['dns_customer_code'];
			$dealer_customer_code=$rowdealerdestination['customer_code'];
			$dealer_customer_name=$rowdealerdestination['customer_name'];
			$dealer_customer_name_final=$dealer_customer_name.' - '.$dealer_destination_name;
			$dealer_address=$rowdealerdestination['address'];
			$dealer_phone_no=$rowdealerdestination['phone_no'];
			//$dealer_address='';
			//$dealer_customer_name_final='';
								
	$dealer_data[] = array("customer_code"=>$dealer_customer_code,"dns_customer_code"=>$dealer_dns_customer_code,"customer_name"=>$dealer_customer_name_final,"address"=>$dealer_address,"phone_no"=>$dealer_phone_no);
	//$dealer_data[]=array();

		while($rowshiptoparty=mysql_fetch_array($rsshiptoparty))
		{
			$dns_customer_code=$rowshiptoparty['dns_customer_code'];
			$customer_code=$rowshiptoparty['customer_code'];
			$customer_name=$rowshiptoparty['customer_name'];
			$destination_name=$rowshiptoparty['destination_name'];
			$customer_name_final=$customer_name.' - '.$destination_name;
			$address=$rowshiptoparty['address'];
			$phone_no=$rowshiptoparty['phone_no'];
			//$address='';
			//$customer_name_final='';

			$dealer_data[] = array("customer_code"=>$customer_code,"dns_customer_code"=>$dns_customer_code,"customer_name"=>$customer_name_final,"address"=>$address,"phone_no"=>$phone_no);
			//$dealer_data[]=array();

		}
		$res_data = array("process_status"=>"YES","process_message"=>"Success.","dealer_data"=>$dealer_data);

	}
	else
	{
	$res_data = array("process_status"=>"NO","process_message"=>"No ship to party dealer data found.");
	}
	
}
else if($user_type=="sub dealer"){
	
	$sqlshiptoparty="SELECT DISTINCT $customer_master.dns_customer_code,$customer_master.customer_code,$customer_master.customer_name,
					$customer_master.address,$customer_master.phone_no,$destination_master.destination_name 
					FROM $qZSDCUST,$customer_master,$customer_destination,$destination_master  WHERE $qZSDCUST.SOLD_PARTY=$customer_master.customer_id 
					AND $customer_master.customer_code=$customer_destination.customer_code AND $destination_master.destination_code=$customer_destination.destination_code   
					AND $qZSDCUST.PARTY='".$customer_id."' AND $customer_master.cust_type='ShiptoParty-Subdeale'";
	$rsshiptoparty=mysql_query($sqlshiptoparty);
	$countshiptoparty=mysql_num_rows($rsshiptoparty);
	if($countshiptoparty >0)
	{
		while($rowshiptoparty=mysql_fetch_array($rsshiptoparty))
		{
			$dns_customer_code=$rowshiptoparty['dns_customer_code'];
			$customer_code=$rowshiptoparty['customer_code'];
			$customer_name=$rowshiptoparty['customer_name'];
			$destination_name=$rowshiptoparty['destination_name'];
			$customer_name_final=$customer_name.' - '.$destination_name;
			$address=$rowshiptoparty['address'];
			$phone_no=$rowshiptoparty['phone_no'];

			$sub_dealer_data[] = array("customer_code"=>$customer_code,"dns_customer_code"=>$dns_customer_code,"customer_name"=>$customer_name_final,"address"=>$address,"phone_no"=>$phone_no);

		}
		$res_data = array("process_status"=>"YES","process_message"=>"Success.","sub_dealer_data"=>$sub_dealer_data);

	}
	else
	{
	$res_data = array("process_status"=>"NO","process_message"=>"No ship to party sub dealer data found.");
	}
	
}
else
{
$res_data = array("process_status"=>"NO","process_message"=>"User type should not be Blank.");
}
print_r($dealer_data);
//echo json_encode($res_data);
	mysql_close($link);		
?>

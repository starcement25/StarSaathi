<?php
include "star_connection.php";
$emp_code=$_REQUEST['emp_code'];

$user_type = $_REQUEST['user_type'] ? strtolower(trim($_REQUEST['user_type'])) : "";
$user_type=str_replace('-',' ',$user_type);
$the_broker_dns_id = $_REQUEST['broker_id'] ? strtolower(trim($_REQUEST['broker_id'])) : "";
$login_type=$_REQUEST['login_type'] ? strtolower(trim($_REQUEST['login_type'])) : "";
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

//sk add condition Dealers will start with 10, RSSD will start with 15, shiptopartydealer will start with 14 & shiptopartysubdealer will start with 14

$con="AND
(
    (customer_master.`cust_type` = 'Dealer' AND customer_master.`customer_id` LIKE '10%')
    OR
    (customer_master.`cust_type` = 'RSSD' AND customer_master.`customer_id` LIKE '15%')
    OR
  
    (customer_master.`cust_type` = 'Ship to Party-dealer' AND customer_master.`customer_id` LIKE '14%')
    OR
    (customer_master.`cust_type` = 'ShiptoParty-Subdeale' AND customer_master.`customer_id` LIKE '14%')
)  ";


function accent2ascii($str)
{
    $charset = 'utf-8';
	$str = htmlentities($str, ENT_NOQUOTES, $charset);

    //$str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '', $str);
    //$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
    //$str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères

    return $str;
}

$sqlcustcode="SELECT customer_id FROM $customer_master WHERE  customer_code='".$emp_code."'";
$rscustcode=mysql_query($sqlcustcode);
$rowcustcode=mysql_fetch_array($rscustcode);
$customer_id=$rowcustcode['customer_id'];
if($login_type=='dealer')
{
	$order_restrict_cond=" AND $customer_master.order_restriction='no'";
}
else
{
	$order_restrict_cond='';
}

if($user_type=="dealer"){
	
	/*$sqlshiptoparty="SELECT DISTINCT $customer_master.dns_customer_code,$customer_master.customer_name,$destination_master.destination_name 
					FROM $qZSDCUST,$customer_master,$customer_destination,$destination_master  WHERE $qZSDCUST.SOLD_PARTY=$customer_master.customer_id 
					AND $customer_master.customer_code=$customer_destination.customer_code AND $destination_master.destination_code=$customer_destination.destination_code   
					AND $qZSDCUST.PARTY='".$customer_id."' AND $customer_master.cust_type='Ship to Party-dealer'";*/
	// $sqlshiptoparty="SELECT DISTINCT $customer_master.dns_customer_code,$customer_master.customer_code,$customer_master.customer_name,
	// 				$customer_master.address,$customer_master.phone_no,$destination_master.destination_name 
	// 				FROM $customer_master,$customer_destination,$destination_master  WHERE $customer_master.customer_code=$customer_destination.customer_code 
	// 				AND $destination_master.destination_code=$customer_destination.destination_code   
	// 				AND $customer_master.rds_tag='".$emp_code."' AND $customer_master.cust_type='Ship to Party-dealer' 
					
	// 				$order_restrict_cond";	
				 $sqlshiptoparty = "
					SELECT DISTINCT 
						$customer_master.dns_customer_code,
						$customer_master.customer_code,
						$customer_master.customer_name,
						$customer_master.address,
						$customer_master.phone_no,
						$destination_master.destination_name
					FROM 
						$customer_master
					JOIN $customer_destination 
						ON $customer_master.customer_code = $customer_destination.customer_code
					JOIN $destination_master 
						ON $destination_master.destination_code = $customer_destination.destination_code
					WHERE 
						$customer_master.rds_tag = '".$emp_code."'
						AND $customer_master.cust_type = 'Ship to Party-dealer'
						AND $customer_master.dns_customer_code IN (
							SELECT KUNNR
							FROM ptblcustomermaster p1
							WHERE (p1.VKORG = '1010' OR p1.VKORG = '1017')
							AND p1.ERDAT = (
								SELECT MAX(p2.ERDAT)
								FROM ptblcustomermaster p2
								WHERE p2.KUNNR = p1.KUNNR
									AND (p2.VKORG = '1010' OR p2.VKORG = '1017')
							)
						)
						$con
						$order_restrict_cond 
					";
					//echo"<pre>";print_r($sqlshiptoparty);die;			
	$rsshiptoparty=mysql_query($sqlshiptoparty);
	$countshiptoparty=mysql_num_rows($rsshiptoparty);
	if($countshiptoparty >0)
	{
		$sqldealerdestination="SELECT $customer_master.dns_customer_code,$customer_master.customer_code,$customer_master.customer_name,
					$customer_master.address,$customer_master.phone_no,$destination_master.destination_name 
					FROM $customer_master,$customer_destination,$destination_master WHERE 
					$customer_master.customer_code=$customer_destination.customer_code AND
					$destination_master.destination_code=$customer_destination.destination_code AND 
					$customer_destination.customer_code='".$emp_code."' $order_restrict_cond";
		$rsdealerdestination=mysql_query($sqldealerdestination);
		$countdealerdestination=mysql_num_rows($rsdealerdestination);
		if($countdealerdestination >0)
		{
			$rowdealerdestination=mysql_fetch_array($rsdealerdestination);
			$dealer_destination_name=$rowdealerdestination['destination_name'];
			$dealer_dns_customer_code=$rowdealerdestination['dns_customer_code'];
			$dealer_customer_code=$rowdealerdestination['customer_code'];
			$dealer_customer_name=$rowdealerdestination['customer_name'];
			$dealer_customer_name_final=$dealer_customer_name.' - '.$dealer_destination_name;
			$dealer_customer_name_final=accent2ascii($dealer_customer_name_final);
			$dealer_address=$rowdealerdestination['address'];
			$dealer_address=accent2ascii($dealer_address);
			$dealer_phone_no=$rowdealerdestination['phone_no'];
								
		$dealer_data[] = array("customer_code"=>$dealer_customer_code,"dns_customer_code"=>$dealer_dns_customer_code,"customer_name"=>$dealer_customer_name_final,"address"=>$dealer_address,"phone_no"=>$dealer_phone_no);
		}

		while($rowshiptoparty=mysql_fetch_array($rsshiptoparty))
		{
			$dns_customer_code=$rowshiptoparty['dns_customer_code'];
			$customer_code=$rowshiptoparty['customer_code'];
			$customer_name=$rowshiptoparty['customer_name'];
			$destination_name=$rowshiptoparty['destination_name'];
			$customer_name_final=$customer_name.' - '.$destination_name;
			$customer_name_final=accent2ascii($customer_name_final);
			$address=$rowshiptoparty['address'];
			$address=accent2ascii($address);
			$phone_no=$rowshiptoparty['phone_no'];
			if($customer_name_final!='')
			{
				$dealer_data[] = array("customer_code"=>$customer_code,"dns_customer_code"=>$dns_customer_code,"customer_name"=>$customer_name_final,"address"=>$address,"phone_no"=>$phone_no);
			}
		}
		$res_data = array("process_status"=>"YES","process_message"=>"Success.","dealer_data"=>$dealer_data);

	}
	else
	{
	$res_data = array("process_status"=>"NO","process_message"=>"No ship to party dealer data found.");
	}
	
}
else if($user_type=="sub dealer"){
	
	/*$sqlshiptoparty="SELECT DISTINCT $customer_master.dns_customer_code,$customer_master.customer_code,$customer_master.customer_name,
					$customer_master.address,$customer_master.phone_no,$destination_master.destination_name 
					FROM $qZSDCUST,$customer_master,$customer_destination,$destination_master  WHERE $qZSDCUST.SOLD_PARTY=$customer_master.customer_id 
					AND $customer_master.customer_code=$customer_destination.customer_code AND $destination_master.destination_code=$customer_destination.destination_code   
					AND $qZSDCUST.PARTY='".$customer_id."' AND $customer_master.cust_type='ShiptoParty-Subdeale'";*/
	// $sqlshiptoparty="SELECT DISTINCT $customer_master.dns_customer_code,$customer_master.customer_code,$customer_master.customer_name,
	// 				$customer_master.address,$customer_master.phone_no,$destination_master.destination_name 
	// 				FROM $customer_master,$customer_destination,$destination_master  WHERE 
	// 				$customer_master.customer_code=$customer_destination.customer_code AND $destination_master.destination_code=$customer_destination.destination_code   
	// 				AND $customer_master.customer_id IN(SELECT $customer_master.customer_id FROM $customer_master WHERE rds_tag IN
	// 				(SELECT $customer_master.customer_code FROM $customer_master WHERE $customer_master.rds_tag='".$emp_code."' AND cust_type IN('RSSD','Sub Dealer')))
	// 				 AND $customer_master.cust_type='ShiptoParty-Subdeale' $order_restrict_cond";

		$sqlshiptoparty = "
				SELECT DISTINCT 
					$customer_master.dns_customer_code,
					$customer_master.customer_code,
					$customer_master.customer_name,
					$customer_master.address,
					$customer_master.phone_no,
					$destination_master.destination_name 
				FROM 
					$customer_master,
					$customer_destination,
					$destination_master  
				WHERE 
					$customer_master.customer_code = $customer_destination.customer_code 
					AND $destination_master.destination_code = $customer_destination.destination_code   
					AND $customer_master.customer_id IN (
						SELECT $customer_master.customer_id 
						FROM $customer_master 
						WHERE rds_tag IN (
							SELECT $customer_master.customer_code 
							FROM $customer_master 
							WHERE $customer_master.rds_tag = '".$emp_code."' 
							AND cust_type IN ('RSSD','Sub Dealer')
						)
					)
					AND $customer_master.cust_type = 'ShiptoParty-Subdeale'
					AND $customer_master.dns_customer_code IN (
						SELECT KUNNR
						FROM ptblcustomermaster p1
						WHERE (p1.VKORG = '1010' OR p1.VKORG = '1017')
						AND p1.ERDAT = (
							SELECT MAX(p2.ERDAT)
							FROM ptblcustomermaster p2
							WHERE p2.KUNNR = p1.KUNNR
								AND (p2.VKORG = '1010' OR p2.VKORG = '1017')
						)
					)
					$con
					$order_restrict_cond
				";
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
			$customer_name_final=accent2ascii($customer_name_final);
			$address=accent2ascii($address);
			$phone_no=$rowshiptoparty['phone_no'];
			
			if($customer_name_final!='')
			{
				$sub_dealer_data[] = array("customer_code"=>$customer_code,"dns_customer_code"=>$dns_customer_code,"customer_name"=>$customer_name_final,"address"=>$address,"phone_no"=>$phone_no);
			}

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
//$res_data=array_map('utf8_encode',$res_data);
//header('Content-Type: application/json');
echo json_encode($res_data);
	mysql_close($link);		
?>

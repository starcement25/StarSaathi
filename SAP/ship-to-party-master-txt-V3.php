<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

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
$customer_gst_documents = "customer_gst_documents";
/* customer_gst_documents now keeps a full history (multiple rows per customer),
   so joining the raw table would duplicate ship-to rows. Join this derived table
   instead -- it exposes only the newest row per customer_id, aliased back to the
   original table name so every existing column reference keeps working. */
$customer_gst_documents_latest = "(SELECT cgd.* FROM customer_gst_documents cgd
		INNER JOIN (SELECT customer_id, MAX(id) AS max_id FROM customer_gst_documents GROUP BY customer_id) cgd_last
		ON cgd_last.max_id = cgd.id) AS customer_gst_documents";

/* Addresses often carry blank or repeated comma-separated parts,
   e.g. "Barbatta, Barbatta, 854315" -> "Barbatta, 854315". */
if(!function_exists('dedupe_address')){
	function dedupe_address($the_address){
		$addr_parts = array_map('trim', explode(',', $the_address));
		$addr_clean = array();
		foreach ($addr_parts as $addr_part) {
			if ($addr_part !== '' && !in_array($addr_part, $addr_clean)) {
				$addr_clean[] = $addr_part;
			}
		}
		return implode(', ', $addr_clean);
	}
}
$qZSDCUST = "pZSDCUST";
$qtblcustomermaster = "ptblcustomermaster";
$tagged_cust_code_arr = array();
$tagged_cust_code_str = "";
$tagged_cust_branch_arr = array();
$tagged_cust_branch_str = "";
$totres_spdes = 0;
$user_type;

function accent2ascii($str)
{
    $charset = 'utf-8';
	$str = htmlentities($str, ENT_NOQUOTES, $charset);
    return $str;
}

function utf8ize($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } elseif (is_string($mixed)) {
        $mixed = str_replace("\xC2\xA0", ' ', $mixed);
        $mixed = str_replace("\xA0", ' ', $mixed);

        $encoding = mb_detect_encoding($mixed, ['UTF-8', 'ISO-8859-1', 'ISO-8859-15'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $mixed = mb_convert_encoding($mixed, 'UTF-8', $encoding);
        }
    }
    return $mixed;
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

$con="AND
(
    (customer_master.`cust_type` = 'Dealer' AND customer_master.`customer_id` LIKE '10%')
    OR
    (customer_master.`cust_type` = 'RSSD' AND customer_master.`customer_id` LIKE '15%')
    OR
  
    (customer_master.`cust_type` = 'Ship to Party-dealer' AND customer_master.`customer_id` LIKE '14%')
    OR
    (customer_master.`cust_type` = 'ShiptoParty-Subdeale' AND customer_master.`customer_id` LIKE '14%')
)   ";
// echo $user_type;die;
if($user_type=="dealer"){

	 $sqlshiptoparty = "
		SELECT DISTINCT 
			$customer_master.dns_customer_code,
			$customer_master.customer_code,
			$customer_master.customer_name,
			$customer_master.address,
			$customer_master.phone_no,
			$customer_master.cust_type,
			$destination_master.destination_name,
			IFNULL($customer_gst_documents.gst_no,'')      AS gst_no,
			IFNULL($customer_gst_documents.upload_doc,'')  AS upload_doc,
			IFNULL($customer_gst_documents.is_any,'')      AS is_any,
			IFNULL($customer_gst_documents.decleration,'') AS decleration
		FROM 
			$customer_master
		JOIN $customer_destination 
			ON $customer_master.customer_code = $customer_destination.customer_code
		JOIN $destination_master 
			ON $destination_master.destination_code = $customer_destination.destination_code
		LEFT JOIN $customer_gst_documents_latest
			ON $customer_gst_documents.customer_id = $customer_master.customer_id
		WHERE 
			$customer_master.rds_tag = '".$emp_code."'
			AND customer_master.acedns = 'Y'
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

	$rsshiptoparty=mysql_query($sqlshiptoparty);
	$countshiptoparty=mysql_num_rows($rsshiptoparty);
	if($countshiptoparty >0)
	{
		$sqldealerdestination="SELECT $customer_master.dns_customer_code,$customer_master.customer_code,$customer_master.customer_name,
					$customer_master.address,$customer_master.phone_no,$destination_master.destination_name, $customer_master.cust_type,
					IFNULL($customer_gst_documents.gst_no,'') AS gst_no,
					IFNULL($customer_gst_documents.upload_doc,'') AS upload_doc,
					IFNULL($customer_gst_documents.is_any,'') AS is_any,
					IFNULL($customer_gst_documents.decleration,'') AS decleration
					FROM $customer_master
					JOIN $customer_destination ON $customer_master.customer_code=$customer_destination.customer_code
					JOIN $destination_master ON $destination_master.destination_code=$customer_destination.destination_code
					LEFT JOIN $customer_gst_documents_latest ON $customer_gst_documents.customer_id=$customer_master.customer_id
					WHERE $customer_destination.customer_code='".$emp_code."' $order_restrict_cond";
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
			$dealer_address=dedupe_address($rowdealerdestination['address']);
			$dealer_address=accent2ascii($dealer_address);
			$dealer_phone_no=$rowdealerdestination['phone_no'];
			$dealer_cust_type=$rowdealerdestination['cust_type'];
			$dealer_gst_no=$rowdealerdestination['gst_no'];
			$dealer_upload_doc=$rowdealerdestination['upload_doc'];
			$dealer_is_any=$rowdealerdestination['is_any'];
			$dealer_decleration=$rowdealerdestination['decleration'];

			$dealer_data[] = array(
				"customer_code"=>$dealer_customer_code,
				"dns_customer_code"=>$dealer_dns_customer_code,
				"customer_name"=>$dealer_customer_name_final,
				"address"=>$dealer_address,
				"phone_no"=>$dealer_phone_no,
				"cust_type"=>$dealer_cust_type,
				"gst_no"=>$dealer_gst_no,
				"upload_doc"=>$dealer_upload_doc,
				"is_any"=>$dealer_is_any,
				"decleration"=>$dealer_decleration
			);
		}

		while($rowshiptoparty=mysql_fetch_array($rsshiptoparty))
		{
			$dns_customer_code=$rowshiptoparty['dns_customer_code'];
			$customer_code=$rowshiptoparty['customer_code'];
			$customer_name=$rowshiptoparty['customer_name'];
			$destination_name=$rowshiptoparty['destination_name'];
			$customer_name_final=$customer_name.' - '.$destination_name;
			$customer_name_final=accent2ascii($customer_name_final);
			$address=dedupe_address($rowshiptoparty['address']);
			$address=accent2ascii($address);
			$phone_no=$rowshiptoparty['phone_no'];
			$cust_type=$rowshiptoparty['cust_type'];
			$gst_no=$rowshiptoparty['gst_no'];
			$upload_doc=$rowshiptoparty['upload_doc'];
			$is_any=$rowshiptoparty['is_any'];
			$decleration=$rowshiptoparty['decleration'];
			if($customer_name_final!='')
			{
				$dealer_data[] = array(
					"customer_code"=>$customer_code,
					"dns_customer_code"=>$dns_customer_code,
					"customer_name"=>$customer_name_final,
					"address"=>$address,
					"phone_no"=>$phone_no,
					"cust_type"=>$cust_type,
					"gst_no"=>$gst_no,
					"upload_doc"=>$upload_doc,
					"is_any"=>$is_any,
					"decleration"=>$decleration
				);
			}
		}
		$dealer_data = utf8ize($dealer_data);
		$res_data = array("process_status"=>"YES","process_message"=>"Success.","dealer_data"=>$dealer_data);

	}
	else
	{
		$sql1 = "SELECT $customer_master.customer_name,$customer_master.address,$customer_master.phone_no,
				$customer_master.dns_customer_code,$customer_master.customer_code,$customer_master.cust_type,
				IFNULL($customer_gst_documents.gst_no,'') AS gst_no,
				IFNULL($customer_gst_documents.upload_doc,'') AS upload_doc,
				IFNULL($customer_gst_documents.is_any,'') AS is_any,
				IFNULL($customer_gst_documents.decleration,'') AS decleration
				FROM $customer_master
				LEFT JOIN $customer_gst_documents_latest ON $customer_gst_documents.customer_id=$customer_master.customer_id
				WHERE $customer_master.customer_code='".$emp_code."'";
		$res1 = mysql_query($sql1);
		$totres1 = mysql_num_rows($res1);
		if($totres1>0){
			$row1=mysql_fetch_assoc($res1);
			$customer_name = trim($row1["customer_name"]);
			$address = trim($row1["address"]);
			$phone_no = trim($row1["phone_no"]);
			$dns_customer_code = trim($row1["dns_customer_code"]);
			$customer_code = trim($row1["customer_code"]);
			$cust_type = trim($row1["cust_type"]);
			$gst_no = trim($row1["gst_no"]);
			$upload_doc = trim($row1["upload_doc"]);
			$is_any = trim($row1["is_any"]);
			$decleration = trim($row1["decleration"]);

			$dealer_data[] = array(
				"customer_code"=>$customer_code,
				"dns_customer_code"=>$dns_customer_code,
				"customer_name"=>$customer_name,
				"address"=>$address,
				"phone_no"=>$phone_no,
				"cust_type"=>$cust_type,
				"gst_no"=>$gst_no,
				"upload_doc"=>$upload_doc,
				"is_any"=>$is_any,
				"decleration"=>$decleration
			);

			$dealer_data = utf8ize($dealer_data);
			$res_data = array("process_status"=>"YES","process_message"=>"Success.","dealer_data"=>$dealer_data);
		}else{
			$res_data = array("process_status"=>"NO","process_message"=>"No ship to party dealer data found.");
		}
	}

}
else if($user_type=="sub dealer" || $user_type=="sub%20dealer"){

	if($login_type=='dealer')
	{
		$sqlshiptoparty = "
			SELECT DISTINCT 
				$customer_master.dns_customer_code,
				$customer_master.customer_code,
				$customer_master.customer_name,
				$customer_master.address,
				$customer_master.phone_no,
				$customer_master.cust_type,
				$destination_master.destination_name,
				IFNULL($customer_gst_documents.gst_no,'')      AS gst_no,
				IFNULL($customer_gst_documents.upload_doc,'')  AS upload_doc,
				IFNULL($customer_gst_documents.is_any,'')      AS is_any,
				IFNULL($customer_gst_documents.decleration,'') AS decleration
			FROM 
				$customer_master
			JOIN $customer_destination
				ON $customer_master.customer_code = $customer_destination.customer_code
			JOIN $destination_master
				ON $destination_master.destination_code = $customer_destination.destination_code
			LEFT JOIN $customer_gst_documents_latest
				ON $customer_gst_documents.customer_id = $customer_master.customer_id
			WHERE 
				$customer_master.customer_id IN (
					SELECT cm2.customer_id 
					FROM $customer_master cm2
					WHERE cm2.rds_tag IN (
						SELECT cm3.customer_code 
						FROM $customer_master cm3
						WHERE cm3.rds_tag = '".$emp_code."' 
						AND cm3.cust_type IN ('RSSD','Sub Dealer')
					)
				)
					AND $customer_master.acedns = 'Y'
				AND $customer_master.cust_type IN ('RSSD','ShiptoParty-Subdeale')
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

		$sqlshiptoparty1 = "
			SELECT DISTINCT 
				$customer_master.customer_id,
				$customer_master.customer_code,
				$customer_master.customer_name,
				$customer_master.address,
				$customer_master.phone_no,
				$customer_master.cust_type,
				$destination_master.destination_name,
				IFNULL($customer_gst_documents.gst_no,'')      AS gst_no,
				IFNULL($customer_gst_documents.upload_doc,'')  AS upload_doc,
				IFNULL($customer_gst_documents.is_any,'')      AS is_any,
				IFNULL($customer_gst_documents.decleration,'') AS decleration
			FROM 
				$customer_master
			JOIN $customer_destination
				ON $customer_master.customer_code = $customer_destination.customer_code
			JOIN $destination_master
				ON $destination_master.destination_code = $customer_destination.destination_code
			LEFT JOIN $customer_gst_documents_latest
				ON $customer_gst_documents.customer_id = $customer_master.customer_id
			WHERE 
			
				$customer_master.rds_tag='".$emp_code."' AND  $customer_master.cust_type='RSSD'
				AND $customer_master.acedns = 'Y'
			";

		$rsshiptoparty1=mysql_query($sqlshiptoparty1);
		$countshiptoparty1=mysql_num_rows($rsshiptoparty1);


		if($countshiptoparty >0 || $countshiptoparty1>0 )
		{
			while($rowshiptoparty=mysql_fetch_array($rsshiptoparty1))
			{
				$dns_customer_code=$rowshiptoparty['customer_id'];
				$customer_code=$rowshiptoparty['customer_code'];
				$customer_name=$rowshiptoparty['customer_name'];
				$destination_name=$rowshiptoparty['destination_name'];
				$customer_name_final=$customer_name.' - '.$destination_name;
				$address=dedupe_address($rowshiptoparty['address']);
				$customer_name_final=accent2ascii($customer_name_final);
				$address=accent2ascii($address);
				$phone_no=$rowshiptoparty['phone_no'];
				$cust_type=$rowshiptoparty['cust_type'];
				$gst_no=$rowshiptoparty['gst_no'];
				$upload_doc=$rowshiptoparty['upload_doc'];
				$is_any=$rowshiptoparty['is_any'];
				$decleration=$rowshiptoparty['decleration'];
				if($customer_name_final!='')
				{
					$sub_dealer_data[] = array(
						"customer_code"=>$customer_code,
						"dns_customer_code"=>$dns_customer_code,
						"customer_name"=>$customer_name_final,
						"address"=>$address,
						"phone_no"=>$phone_no,
						"cust_type"=>$cust_type,
						"gst_no"=>$gst_no,
						"upload_doc"=>$upload_doc,
						"is_any"=>$is_any,
						"decleration"=>$decleration
					);
				}
			}
			while($rowshiptoparty=mysql_fetch_array($rsshiptoparty))
			{
				$dns_customer_code=$rowshiptoparty['dns_customer_code'];
				$customer_code=$rowshiptoparty['customer_code'];
				$customer_name=$rowshiptoparty['customer_name'];
				$destination_name=$rowshiptoparty['destination_name'];
				$customer_name_final=$customer_name.' - '.$destination_name;
				$address=dedupe_address($rowshiptoparty['address']);
				$customer_name_final=accent2ascii($customer_name_final);
				$address=accent2ascii($address);
				$phone_no=$rowshiptoparty['phone_no'];
				$cust_type=$rowshiptoparty['cust_type'];
				$gst_no=$rowshiptoparty['gst_no'];
				$upload_doc=$rowshiptoparty['upload_doc'];
				$is_any=$rowshiptoparty['is_any'];
				$decleration=$rowshiptoparty['decleration'];
				if($customer_name_final!='')
				{
					$sub_dealer_data[] = array(
						"customer_code"=>$customer_code,
						"dns_customer_code"=>$dns_customer_code,
						"customer_name"=>$customer_name_final,
						"address"=>$address,
						"phone_no"=>$phone_no,
						"cust_type"=>$cust_type,
						"gst_no"=>$gst_no,
						"upload_doc"=>$upload_doc,
						"is_any"=>$is_any,
						"decleration"=>$decleration
					);
				}
			}
			$sub_dealer_data = utf8ize($sub_dealer_data);
			$res_data = array("process_status"=>"YES","process_message"=>"Success.","sub_dealer_data"=>$sub_dealer_data);

		}
		else
		{
			$res_data = array("process_status"=>"NO","process_message"=>"No ship to party sub dealer data found.");
		}
	}else{
		$sqlshiptoparty = "
			SELECT DISTINCT 
				$customer_master.dns_customer_code,
				$customer_master.customer_code,
				$customer_master.customer_name,
				$customer_master.address,
				$customer_master.phone_no,
				$customer_master.cust_type,
				$destination_master.destination_name,
				IFNULL($customer_gst_documents.gst_no,'')      AS gst_no,
				IFNULL($customer_gst_documents.upload_doc,'')  AS upload_doc,
				IFNULL($customer_gst_documents.is_any,'')      AS is_any,
				IFNULL($customer_gst_documents.decleration,'') AS decleration
			FROM 
				$customer_master
			JOIN $customer_destination
				ON $customer_master.customer_code = $customer_destination.customer_code
			JOIN $destination_master
				ON $destination_master.destination_code = $customer_destination.destination_code
			LEFT JOIN $customer_gst_documents_latest
				ON $customer_gst_documents.customer_id = $customer_master.customer_id
			WHERE 
				$customer_master.customer_code = '".$emp_code."'
			";

		$rsshiptoparty=mysql_query($sqlshiptoparty);
		$countshiptoparty=mysql_num_rows($rsshiptoparty);

		$sqlshiptoparty1 = "
			SELECT DISTINCT 
				$customer_master.dns_customer_code,
				$customer_master.customer_code,
				$customer_master.customer_name,
				$customer_master.address,
				$customer_master.phone_no,
				$customer_master.cust_type,
				$destination_master.destination_name,
				IFNULL($customer_gst_documents.gst_no,'')      AS gst_no,
				IFNULL($customer_gst_documents.upload_doc,'')  AS upload_doc,
				IFNULL($customer_gst_documents.is_any,'')      AS is_any,
				IFNULL($customer_gst_documents.decleration,'') AS decleration
			FROM 
				$customer_master
			JOIN $customer_destination
				ON $customer_master.customer_code = $customer_destination.customer_code
			JOIN $destination_master
				ON $destination_master.destination_code = $customer_destination.destination_code
			LEFT JOIN $customer_gst_documents_latest
				ON $customer_gst_documents.customer_id = $customer_master.customer_id
			WHERE 
				$customer_master.rds_tag = '".$emp_code."'
				AND $customer_master.acedns = 'Y'
			";

		$rsshiptoparty1=mysql_query($sqlshiptoparty1);
		$countshiptoparty1=mysql_num_rows($rsshiptoparty1);


		while($rowshiptoparty=mysql_fetch_array($rsshiptoparty))
			{
				$dns_customer_code=$rowshiptoparty['dns_customer_code'];
				$customer_code=$rowshiptoparty['customer_code'];
				$customer_name=$rowshiptoparty['customer_name'];
				$destination_name=$rowshiptoparty['destination_name'];
				$customer_name_final=$customer_name.' - '.$destination_name;
				$address=dedupe_address($rowshiptoparty['address']);
				$customer_name_final=accent2ascii($customer_name_final);
				$address=accent2ascii($address);
				$phone_no=$rowshiptoparty['phone_no'];
				$cust_type=$rowshiptoparty['cust_type'];
				$gst_no=$rowshiptoparty['gst_no'];
				$upload_doc=$rowshiptoparty['upload_doc'];
				$is_any=$rowshiptoparty['is_any'];
				$decleration=$rowshiptoparty['decleration'];
				if($customer_name_final!='')
				{
					$sub_dealer_data[] = array(
						"customer_code"=>$customer_code,
						"dns_customer_code"=>$dns_customer_code,
						"customer_name"=>$customer_name_final,
						"address"=>$address,
						"phone_no"=>$phone_no,
						"cust_type"=>$cust_type,
						"gst_no"=>$gst_no,
						"upload_doc"=>$upload_doc,
						"is_any"=>$is_any,
						"decleration"=>$decleration
					);
				}
			}

		
		while($rowshiptoparty=mysql_fetch_array($rsshiptoparty1))
			{
				$dns_customer_code=$rowshiptoparty['dns_customer_code'];
				$customer_code=$rowshiptoparty['customer_code'];
				$customer_name=$rowshiptoparty['customer_name'];
				$destination_name=$rowshiptoparty['destination_name'];
				$customer_name_final=$customer_name.' - '.$destination_name;
				$address=dedupe_address($rowshiptoparty['address']);
				$customer_name_final=accent2ascii($customer_name_final);
				$address=accent2ascii($address);
				$phone_no=$rowshiptoparty['phone_no'];
				$cust_type=$rowshiptoparty['cust_type'];
				$gst_no=$rowshiptoparty['gst_no'];
				$upload_doc=$rowshiptoparty['upload_doc'];
				$is_any=$rowshiptoparty['is_any'];
				$decleration=$rowshiptoparty['decleration'];
				if($customer_name_final!='')
				{
					$sub_dealer_data[] = array(
						"customer_code"=>$customer_code,
						"dns_customer_code"=>$dns_customer_code,
						"customer_name"=>$customer_name_final,
						"address"=>$address,
						"phone_no"=>$phone_no,
						"cust_type"=>$cust_type,
						"gst_no"=>$gst_no,
						"upload_doc"=>$upload_doc,
						"is_any"=>$is_any,
						"decleration"=>$decleration
					);
				}
			}
		$sub_dealer_data = utf8ize($sub_dealer_data);
		$res_data = array("process_status"=>"YES","process_message"=>"Success.","sub_dealer_data"=>$sub_dealer_data);
	}

}
else
{
	$res_data = array("process_status"=>"NO","process_message"=>"User type should not be Blank.");
}

echo json_encode($res_data);
mysql_close($link);		
?>
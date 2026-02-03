<?php
include "star_connection.php";
$emp_code=$_REQUEST['emp_code'];

$user_type = $_REQUEST['user_type'] ? strtolower(trim($_REQUEST['user_type'])) : "";
$the_customer_code=$_REQUEST['customer_code'] ? trim($_REQUEST['customer_code']) : "";

$lifting_date_validation  = "lifting_date_validation ";
$customer_master = "customer_master";
$branch_master = "branch_master";

function accent2ascii($str)
{
    $charset = 'utf-8';
	$str = htmlentities($str, ENT_NOQUOTES, $charset);

    //$str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '', $str);
    //$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
    //$str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères

    return $str;
}

if($the_customer_code!=''){
	
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	$contentsdate =$year.'-'.$month.'-'.$date;
	
	$sqlbranchcode="SELECT branch_code FROM $customer_master WHERE  dns_customer_code='".$the_customer_code."'";
	$rsbranchcode=mysql_query($sqlbranchcode);
	$rowbranchcode=mysql_fetch_array($rsbranchcode);
	$branch_code=$rowbranchcode['branch_code'];
	
	$sqlliftingdata="SELECT * FROM $lifting_date_validation WHERE branch='".$branch_code."'";				
	$rslliftingdata=mysql_query($sqlliftingdata);
	$countlliftingdata=mysql_num_rows($rslliftingdata);
	if($countlliftingdata >0)
	{
		while($rowliftingdata=mysql_fetch_array($rslliftingdata))
		{
			$validation_from=$rowliftingdata['validation_from'];
			$validation_to=$rowliftingdata['validation_to'];
			$validation_last_date=$rowliftingdata['validation_last_date'];
			$lifting_validation_data[] = array("validation_from"=>$validation_from,"validation_to"=>$validation_to,"validation_last_date"=>$validation_last_date,"current_date"=>$contentsdate);

		}
		$res_data = array("process_status"=>"YES","process_message"=>"Success.","lifting_validation_data"=>$lifting_validation_data);

	}
	else
	{
	$res_data = array("process_status"=>"NO","process_message"=>"No validation data found.");
	}
	
}
else
{
$res_data = array("process_status"=>"NO","process_message"=>"Customer code should not be Blank.");
}
//$res_data=array_map('utf8_encode',$res_data);
//header('Content-Type: application/json');
echo json_encode($res_data);
	mysql_close($link);		
?>

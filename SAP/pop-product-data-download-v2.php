<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "star_connection.php";
$emp_code=$_REQUEST['emp_code'] ? strtolower(trim($_REQUEST['emp_code'])) : "";
$user_type = $_REQUEST['user_type'] ? strtolower(trim($_REQUEST['user_type'])) : "";
$the_customer_code=$_REQUEST['customer_code'] ? trim($_REQUEST['customer_code']) : "";

$app_service_track_log="app_service_track_log";
$pop_product_master  = "pop_product_master";
$customer_master = "customer_master";
$branch_master = "branch_master";
$t_apperpdo_pop = "T_ORDER_POP";
$pop_order="pop_order";

date_default_timezone_set('Asia/Kolkata');
$log_date = date("Y-m-d H:i:s");

/*$sql_app_track="insert into $app_service_track_log(`appservice_name`,`customer_code`,`datetime`) values('POP ORDER','$emp_code','$log_date')";

$reslon=mysql_query($sql_app_track);

if($reslon){
	$res_data=array("process_status"=>"YES","process_message"=>"POP Order submitted.");
}*/

/*$sql4 = "select * from $customer_master where `customer_code`='$emp_code'";

$query4=mysql_query($sql4);

$result4=mysql_fetch_assoc($query4);

$customer_id=$result4['customer_id'];*/

$sql_app_track1="insert into $app_service_track_log(`appservice_name`,`customer_code`,`datetime`) values('POP ORDER','$the_customer_code','$log_date')";

$reslon1=mysql_query($sql_app_track1);

if($reslon1){
	$res_data=array("process_status"=>"YES","process_message"=>"POP Order submitted.");
}

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
	
	if(strtolower($user_type)=="rssd"){
	$condition_login_type=" AND sp_login='Y'";
	}
	if(strtolower($user_type)=="dealer" || strtolower($user_type)=="broker"){
		$condition_login_type=" AND dealer_login='Y'";
	}
	
	$sqlbranchcode="SELECT dns_branch_code FROM $branch_master WHERE branch_code IN(SELECT branch_code FROM $customer_master WHERE  customer_id='".$the_customer_code."')";
	
	$rsbranchcode=mysql_query($sqlbranchcode);
	$rowbranchcode=mysql_fetch_array($rsbranchcode);
	$dns_branch_code=$rowbranchcode['dns_branch_code'];
	
	$prod_img_URL= BASE_URL ."pop_prod_img/";

	$sqlpopproduct="SELECT * FROM $pop_product_master WHERE `status`='Y' AND dns_prod_code IN(SELECT dns_prod_code FROM branch_pop_product  where branch_code='".$dns_branch_code."' and `status`='Y') ".$condition_login_type."";
	$rspopproduct=mysql_query($sqlpopproduct);
	$countpopproduct=mysql_num_rows($rspopproduct);

	
	if($countpopproduct > 0)
	{
		while($rowpopproduct=mysql_fetch_array($rspopproduct))
		{
			$dns_prod_code=$rowpopproduct['dns_prod_code'];
			$prod_desc =  preg_replace('/[\x80-\xFF]/', '', $rowpopproduct["prod_desc"]);
			$prod_desc=accent2ascii($prod_desc);
			$prod_image=$rowpopproduct['prod_image'];
			$min_order_qty=accent2ascii($rowpopproduct['min_order_qty']);
			$price_per_piece =accent2ascii($rowpopproduct['price_per_piece']);
			$GST_rate=accent2ascii($rowpopproduct['GST_rate']);
			$upload_date_time=accent2ascii($rowpopproduct['upload_date_time']);
			$status=accent2ascii($rowpopproduct['status']);
			$payment_gateway=accent2ascii($rowpopproduct['payment_gateway']);

			$pop_product_date[] = array("dns_prod_code"=>$dns_prod_code,"prod_desc"=>$prod_desc,"prod_image"=>$prod_img_URL.$prod_image,"min_order_qty"=>$min_order_qty,"price_per_piece"=>$price_per_piece,"GST_rate"=>$GST_rate,"payment_gateway"=>$payment_gateway);
			
		}
		$res_data = array("process_status"=>"YES","process_message"=>"Success.","pop_product_date"=>$pop_product_date);
	}
	else
	{
	$res_data = array("process_status"=>"NO","process_message"=>"No pop product data found.");
	}
	
}
else
{
$res_data = array("process_status"=>"NO","process_message"=>"Customer code should not be Blank.");
}
//$res_data=array_map('utf8_encode',$res_data);
//header('Content-Type: application/json');
echo json_encode($res_data);
mysql_close();		
?>

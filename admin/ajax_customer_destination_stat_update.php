<?php
include "star_connection.php";
$dns_customer_code = $_REQUEST["customer_code"] ? addslashes(trim($_REQUEST["customer_code"])) : "";
$destination_code = $_REQUEST["destination_code"] ? addslashes(trim($_REQUEST["destination_code"])) : "";

if($dns_customer_code!="" && $destination_code!=""){
	$sqlcutomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".$dns_customer_code."'";
		$rscutomercode=mysql_query($sqlcutomercode);
	$rowcutomercode=mysql_fetch_array($rscutomercode);
	$customer_code=$rowcutomercode['customer_code'];

	$selcreditlimitstat="SELECT acedns FROM customer_destination WHERE customer_code='".$customer_code."' AND destination_code='".$destination_code."' ";
	$rscreditlimitstat=mysql_query($selcreditlimitstat);
	$rowcreditlimitstat=mysql_fetch_array($rscreditlimitstat);
	$acedns=$rowcreditlimitstat['acedns'];
	if($acedns=='Y')
	{
		$acedns_status_changed='N';
		$sql="update customer_destination set acedns='".$acedns_status_changed."',download_time=CURRENT_TIMESTAMP() where 
					customer_code='".$customer_code."' AND destination_code='".$destination_code."'";
	}
	if($acedns=='N')
	{
		$acedns_status_changed='Y';
		$sql="update customer_destination set acedns='".$acedns_status_changed."',download_time=CURRENT_TIMESTAMP() where 
					customer_code='".$customer_code."' AND destination_code='".$destination_code."'";
	}
	
	$res=mysql_query($sql);
$res_data = array("acedns_status"=>$acedns_status_changed,"process_status"=>"YES","process_message"=>"Status updated successfully.");
}else{	
	$res_data = array("acedns_status"=>'',"process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");
}	
echo json_encode($res_data);
mysql_close();
?>
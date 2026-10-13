<?php
header('Content-Type: application/json');
include "star_connection.php";

$broker_master = "broker_master";
$sp_broker_permision = "sp_broker_permision";
$selected_sp_broker_permision = "selected_sp_broker_permision";

$broker_code = @$_REQUEST["broker_code"] ? addslashes(trim($_REQUEST["broker_code"])) : "";

if($broker_code==""){
	$res_data = array("process_status"=>"NO","process_message"=>"broker_code is required.","permissions"=>array());
	echo json_encode($res_data);
	mysql_close();
	exit;
}

$sql_broker = "select `broker_id`,`dns_broker_id`,`broker_name` from $broker_master where `broker_id`='$broker_code' or `dns_broker_id`='$broker_code' limit 1";
$res_broker = mysql_query($sql_broker);
$tot_broker = mysql_num_rows($res_broker);

if($tot_broker==0){
	$res_data = array("process_status"=>"NO","process_message"=>"Broker not found.","permissions"=>array());
	echo json_encode($res_data);
	mysql_close();
	exit;
}

$row_broker = mysql_fetch_assoc($res_broker);
$broker_id = trim($row_broker["broker_id"]);
$dns_broker_id = trim($row_broker["dns_broker_id"]);
$broker_name = trim($row_broker["broker_name"]);
$permission_data = array();

 $sql_permision = "select distinct sp.`permision_type`,sp.`permision_name`
	from $selected_sp_broker_permision ssp
	inner join $sp_broker_permision sp on sp.`id`=ssp.`permision_id` or sp.`permision_type`=ssp.`permision_id`
	where ssp.`user_id` in('$broker_id','$dns_broker_id','$broker_code') and sp.`status`='Y'
	order by sp.`permision_name` asc";
$res_permision = mysql_query($sql_permision);
$tot_permision = mysql_num_rows($res_permision);

if($tot_permision>0){
	while($row_permision=mysql_fetch_assoc($res_permision)){
		$permission_data[] = array(
			"permision_type"=>trim($row_permision["permision_type"]),
			"permision_name"=>trim($row_permision["permision_name"])
		);
	}
}

$res_data = array(
	"process_status"=>"YES",
	"process_message"=>"success",
	"broker_id"=>$broker_id,
	"dns_broker_id"=>$dns_broker_id,
	"broker_name"=>$broker_name,
	"permision_list"=>$permission_data
	
);

echo json_encode($res_data);
mysql_close();
?>

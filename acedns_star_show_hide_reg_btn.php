<?php
date_default_timezone_set("Asia/Kolkata");
include "star_connection.php";

$is_show_reg_btn = get_value_by_setting_key("is_show_reg_btn");
if($is_show_reg_btn!=""){
	
}else{
$is_show_reg_btn = "YES";	
}
$res_data = array("process_status"=>"YES","process_message"=>"DONE","is_show_reg_btn"=>$is_show_reg_btn);
echo json_encode($res_data);
mysql_close();
?>
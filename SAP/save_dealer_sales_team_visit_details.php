<?php
include "star_connection.php";
include "function-sfa.php";
$dealer_sales_team_visit_survey   ="dealer_sales_team_visit_survey";
$customer_master = "customer_master";
$res_data=array();


$customer_id = $_REQUEST["customer_id"] ? addslashes(trim($_REQUEST["customer_id"])) : "";
$emp_code = $_REQUEST["emp_code"] ? addslashes(trim($_REQUEST["emp_code"])) : "";
$visit_datetime = $_REQUEST["visit_datetime"] ? addslashes(trim($_REQUEST["visit_datetime"])) : "";
$survey_rating = $_REQUEST["survey_rating"] ? addslashes(trim($_REQUEST["survey_rating"])) : "";
$remarks = $_REQUEST["remarks"] ? addslashes(trim($_REQUEST["remarks"])) : "";
$dealer_site_visit_date_time = date("Y-m-d H:i:s");
if($survey_rating!='' && $remarks!=''){
date_default_timezone_set('Asia/Kolkata'); // Set the timezone to IST

$sqlupdate = "UPDATE $dealer_sales_team_visit_survey set `survey_rating`='$survey_rating',`remarks`='$remarks',rating_update_datetime=CURRENT_TIMESTAMP() 
where `emp_code`='$emp_code' AND  visit_datetime='$visit_datetime' AND sap_customer_code='$customer_id'";
$resupdate = mysql_query($sqlupdate);
$res_data = array("process_status"=>"YES","process_message"=>"Dealer rating successfully saved.");
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Failed to save Dealer rating.");
}
echo json_encode($res_data);
//mysql_close();
//For SFA INSERT
mysql_close();
?>

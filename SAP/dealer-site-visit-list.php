<?php
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$dealer_sales_team_visit_survey  = "dealer_sales_team_visit_survey";
$sales_team_visit_data= array();
$curr_date_time  = date("Y-m-d H:i:s");
$order_item_arr = array();
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$curr_date = date("Y-m-d");
$before_30_day_date = date('Y-m-d',strtotime("-30 days"));
$the_customer_code = $_REQUEST["customer_code"] ? addslashes(trim($_REQUEST["customer_code"])) : "";
/*$page_no = $_REQUEST["page_no"] ? $_REQUEST["page_no"] : 1;
$limit = 10;
$start_from = (($page_no-1)*$limit);*/
if($the_customer_code==""){
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.","sales_team_visit_data"=>$sales_team_visit_data);
}else{
$sql_visit = "SELECT `emp_code`,`emp_name`,`visit_datetime` FROM $dealer_sales_team_visit_survey  where `sap_customer_code`='".$the_customer_code."' and (survey_rating IS NULL OR survey_rating='') ";
//echo $sql_cust;
$res_visit = mysql_query($sql_visit);
$tot_res_visit = mysql_num_rows($res_visit);
if($tot_res_visit>0){
	while($row_visit=mysql_fetch_array($res_visit)){
		$emp_code=$row_visit['emp_code'];
		$emp_name=$row_visit['emp_name'];
		$visit_datetime=$row_visit['visit_datetime'];
		$sales_team_visit_data[] =array("emp_code"=>$emp_code,"emp_name"=>$emp_name,"visit_datetime"=>$visit_datetime);	
	}
	$res_data = array("process_status"=>"YES","process_message"=>"Success.","sales_team_visit_data"=>$sales_team_visit_data);
}
else{
$res_data = array("process_status"=>"NO","process_message"=>"No visit data found.","sales_team_visit_data"=>$sales_team_visit_data);
}
}
echo json_encode($res_data);
if($conn!=""){
mysql_close($conn);
}
?>
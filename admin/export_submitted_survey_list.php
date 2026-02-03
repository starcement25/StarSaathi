<?php
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
startCreatSurveyListCsvfile();

function startCreatSurveyListCsvfile(){
$survey_form = "survey_form";
$changepassword = "changepassword";
$get_fetch_type = $get_fetch_type ? strtolower(trim($get_fetch_type)) : "";
$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "surveyList_".$curr_date.".csv";
$server_url = "http://".$_SERVER['SERVER_NAME']."/";
$home_url = $server_url."admin/";
$output ="";
$output .= '"Dealer_ID","Dealer_Name","Phone","Device_Type","App_Version","Branch_Name","Branch_Code","Branch_DNS_Code","is_return_fy18_19","is_return_fy19_20","is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20","is_turnover_exceeds_10_crores_fy20_21","PAN","Submitted_On","PDF Link"';
$output .="\n";
$qry = "select $survey_form.*,$changepassword.`device_type`,$changepassword.`app_version` from $survey_form left join $changepassword on $survey_form.`sf_cust_code`=$changepassword.`customer_code` order by $survey_form.`sf_submitted_datetime` desc";
$sql = mysql_query($qry);
$totres = mysql_num_rows($sql);
if($totres>0){
while($row1=mysql_fetch_assoc($sql)){
$sf_cust_code = $row1["sf_cust_code"];
$sf_dealer_id = $row1["sf_dealer_id"];
$sf_dealer_name = $row1["sf_dealer_name"];
$sf_dealer_mobile = $row1["sf_dealer_mobile"];
$sf_branch_name = $row1["sf_branch_name"];
$sf_branch_code = $row1["sf_branch_code"];
$sf_dns_branch_code = $row1["sf_dns_branch_code"];
$sf_is_return_fy18_19 = $row1["sf_is_return_fy18_19"] ? trim($row1["sf_is_return_fy18_19"]) : "";
$sf_is_return_fy19_20 = $row1["sf_is_return_fy19_20"] ? trim($row1["sf_is_return_fy19_20"]) : "";
$sf_is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20 = $row1["sf_is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20"] ? trim($row1["sf_is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20"]) : "";
$sf_is_turnover_exceeds_10_crores_fy20_21 = $row1["sf_is_turnover_exceeds_10_crores_fy20_21"] ? trim($row1["sf_is_turnover_exceeds_10_crores_fy20_21"]) : "";
$sf_pan = $row1["sf_pan"] ? trim($row1["sf_pan"]) : "";
$sf_submitted_datetime = $row1["sf_submitted_datetime"] ? trim($row1["sf_submitted_datetime"]) : "";
if($sf_submitted_datetime!=""){
$sf_submitted_datetime = date("jS M,Y h:i A",strtotime($sf_submitted_datetime));
}

$the_pdf_link = $home_url."survey_pdf.php?the_id=".$sf_cust_code;

$sf_device_type = $row1["device_type"];
$sf_app_version = $row1["app_version"];

$output .= '"'.$sf_dealer_id.'","'.$sf_dealer_name.'","'.$sf_dealer_mobile.'","'.$sf_device_type.'","'.$sf_app_version.'","'.$sf_branch_name.'","'.$sf_branch_code.'","'.$sf_dns_branch_code.'","'.$sf_is_return_fy18_19.'","'.$sf_is_return_fy19_20.'","'.$sf_is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20.'","'.$sf_is_turnover_exceeds_10_crores_fy20_21.'","'.$sf_pan.'","'.$sf_submitted_datetime.'","'.$the_pdf_link.'"';


$output .="\n";
}

}

// Download the file

$filename = $the_file_name;
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');    
header('Expires: 0');
echo $output;
exit;
}

mysql_close();
?>
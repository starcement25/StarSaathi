<?php
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
startCreatNYEListCsvfile();

function startCreatNYEListCsvfile(){
$survey_for_new_year_eve_carnival_concert = "survey_for_new_year_eve_carnival_concert";
$changepassword = "changepassword";
$get_fetch_type = $get_fetch_type ? strtolower(trim($get_fetch_type)) : "";
$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "NYEList_".$curr_date.".csv";
$server_url = "http://".$_SERVER['SERVER_NAME']."/SAP/";
$home_url = $server_url."admin/";
$output ="";
$output .= '"Dealer_ID","Dealer_SAP_Code","Dealer_Name","Phone","Device_Type","App_Version","Branch_Name","Branch_Code","Branch_DNS_Code","1._Attending","a._Alan_Walker","b._Badhshah","c._David_Guetta","d._Sunidhi_Chauhan","e._Lucky_Ali","f._Zubeen_Garg","g._Pitbull","h._Mika_Singh","i._Arijit_Singh","j._Papon","k._DJ_Nucleya","l._Ankit_Tiwari","m._Others","3a._2000","3b._3000","3c._4000","3d._5000","3e._7000","4a._Spouse_only","4b._Spouse_&_Kid(s)","4c._Friends_&_Colleagues","4d._Parents_&_Siblings","4e._Solo","5._Liquour","6._30th_December","7._31st_December","Submitted_On"';
$output .="\n";
$qry = "select $survey_for_new_year_eve_carnival_concert.*,$changepassword.`device_type`,$changepassword.`app_version` from $survey_for_new_year_eve_carnival_concert left join $changepassword on $survey_for_new_year_eve_carnival_concert.`sf_cust_code`=$changepassword.`customer_code` order by $survey_for_new_year_eve_carnival_concert.`sf_submitted_datetime` desc";
$sql = mysql_query($qry);
$totres = mysql_num_rows($sql);
if($totres>0){
while($row1=mysql_fetch_assoc($sql)){
$sf_cust_code = $row1["sf_cust_code"];
$sf_dealer_id = $row1["sf_dealer_id"];
$sf_sf_dealer_sap_code = $row1["sf_dealer_sap_code"];
$sf_dealer_name = $row1["sf_dealer_name"];
$sf_dealer_mobile = $row1["sf_dealer_mobile"];
$sf_branch_name = $row1["sf_branch_name"];
$sf_branch_code = $row1["sf_branch_code"];
$sf_dns_branch_code = $row1["sf_dns_branch_code"];

$sf_1_attending = $row1["sf_1_attending"] ? trim($row1["sf_1_attending"]) : "";
$sf_a_alan_walker = $row1["sf_a_alan_walker"] ? trim($row1["sf_a_alan_walker"]) : "";
$sf_b_badhshah = $row1["sf_b_badhshah"] ? trim($row1["sf_b_badhshah"]) : "";
$sf_c_david_guetta = $row1["sf_c_david_guetta"] ? trim($row1["sf_c_david_guetta"]) : "";
$sf_d_sunidhi_chauhan = $row1["sf_d_sunidhi_chauhan"] ? trim($row1["sf_d_sunidhi_chauhan"]) : "";
$sf_e_lucky_ali = $row1["sf_e_lucky_ali"] ? trim($row1["sf_e_lucky_ali"]) : "";
$sf_f_zubeen_garg = $row1["sf_f_zubeen_garg"] ? trim($row1["sf_f_zubeen_garg"]) : "";
$sf_g_pitbull = $row1["sf_g_pitbull"] ? trim($row1["sf_g_pitbull"]) : "";
$sf_h_mika_singh = $row1["sf_h_mika_singh"] ? trim($row1["sf_h_mika_singh"]) : "";
$sf_i_arijit_singh = $row1["sf_i_arijit_singh"] ? trim($row1["sf_i_arijit_singh"]) : "";
$sf_j_papon = $row1["sf_j_papon"] ? trim($row1["sf_j_papon"]) : "";
$sf_k_dj_nucleya = $row1["sf_k_dj_nucleya"] ? trim($row1["sf_k_dj_nucleya"]) : "";
$sf_l_ankit_tiwari = $row1["sf_l_ankit_tiwari"] ? trim($row1["sf_l_ankit_tiwari"]) : "";
$sf_m_others = $row1["sf_m_others"] ? str_replace('"', '""', trim($row1["sf_m_others"])) : "";
$sf_3a_2000 = $row1["sf_3a_2000"] ? trim($row1["sf_3a_2000"]) : "";
$sf_3b_3000 = $row1["sf_3b_3000"] ? trim($row1["sf_3b_3000"]) : "";
$sf_3c_4000 = $row1["sf_3c_4000"] ? trim($row1["sf_3c_4000"]) : "";
$sf_3d_5000 = $row1["sf_3d_5000"] ? trim($row1["sf_3d_5000"]) : "";
$sf_3e_7000 = $row1["sf_3e_7000"] ? trim($row1["sf_3e_7000"]) : "";
$sf_4a_spouse_only = $row1["sf_4a_spouse_only"] ? trim($row1["sf_4a_spouse_only"]) : "";
$sf_4b_spouse_and_kid = $row1["sf_4b_spouse_and_kid"] ? trim($row1["sf_4b_spouse_and_kid"]) : "";
$sf_4c_friends_and_colleagues = $row1["sf_4c_friends_and_colleagues"] ? trim($row1["sf_4c_friends_and_colleagues"]) : "";
$sf_4d_parents_and_siblings = $row1["sf_4d_parents_and_siblings"] ? trim($row1["sf_4d_parents_and_siblings"]) : "";
$sf_4e_solo = $row1["sf_4e_solo"] ? trim($row1["sf_4e_solo"]) : "";
$sf_5_liquour = $row1["sf_5_liquour"] ? trim($row1["sf_5_liquour"]) : "";
$sf_6_30th_december = $row1["sf_6_30th_december"] ? trim($row1["sf_6_30th_december"]) : "";
$sf_7_31st_december = $row1["sf_7_31st_december"] ? trim($row1["sf_7_31st_december"]) : "";		



$sf_submitted_datetime = $row1["sf_submitted_datetime"] ? trim($row1["sf_submitted_datetime"]) : "";
if($sf_submitted_datetime!=""){
$sf_submitted_datetime = date("jS M,Y h:i A",strtotime($sf_submitted_datetime));
}

$sf_device_type = $row1["device_type"];
$sf_app_version = $row1["app_version"];

$output .= '"'.$sf_dealer_id.'","'.$sf_sf_dealer_sap_code.'","'.$sf_dealer_name.'","'.$sf_dealer_mobile.'","'.$sf_device_type.'","'.$sf_app_version.'","'.$sf_branch_name.'","'.$sf_branch_code.'","'.$sf_dns_branch_code.'","'.$sf_1_attending.'","'.$sf_a_alan_walker.'","'.$sf_b_badhshah.'","'.$sf_c_david_guetta.'","'.$sf_d_sunidhi_chauhan.'","'.$sf_e_lucky_ali.'","'.$sf_f_zubeen_garg.'","'.$sf_g_pitbull.'","'.$sf_h_mika_singh.'","'.$sf_i_arijit_singh.'","'.$sf_j_papon.'","'.$sf_k_dj_nucleya.'","'.$sf_l_ankit_tiwari.'","'.$sf_m_others.'","'.$sf_3a_2000.'","'.$sf_3b_3000.'","'.$sf_3c_4000.'","'.$sf_3d_5000.'","'.$sf_3e_7000.'","'.$sf_4a_spouse_only.'","'.$sf_4b_spouse_and_kid.'","'.$sf_4c_friends_and_colleagues.'","'.$sf_4d_parents_and_siblings.'","'.$sf_4e_solo.'","'.$sf_5_liquour.'","'.$sf_6_30th_december.'","'.$sf_7_31st_december.'","'.$sf_submitted_datetime.'"';


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
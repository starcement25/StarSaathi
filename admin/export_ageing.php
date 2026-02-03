<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & E_NOTICE & E_DEPRECATED);
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$ageing = "ageing";

$new_qry_string_filtered = "";
$export_filtered_str = "";
$srch_dlr_dtls = $_GET["srch_dlr_dtls"] ? addslashes(trim($_GET["srch_dlr_dtls"])) : "";
$whr_str = "";
$search_array = array("srch_dlr_dtls"=>$srch_dlr_dtls);
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_dlr_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($ageing.party like '%$search_array_val%') ";
			$new_qry_string_filtered .= "&srch_dlr_dtls=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&srch_dlr_dtls=".$search_array_val;
			}else{
				$export_filtered_str .= "&srch_dlr_dtls=".$search_array_val;
			}
		}
	}
	/*if($search_array_key=="sl_product"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($destination_wise_price.product_name like '%$search_array_val%' ) ";
			$new_qry_string_filtered .= "&sl_product=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_product=".$search_array_val;
			}else{
				$export_filtered_str .= "&sl_product=".$search_array_val;
			}
		}
	}*/
}

if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}

$output = "";
$qry = "select $ageing.* FROM $ageing $new_whr_str ORDER BY zone,party ASC ";
$sql = mysql_query($qry);
// Get The Field Name
$slno_cnt = 1;
$output .= '"Zone","Class Desc","Alias","Party","Security Deposit","Credit Limit","Collection Date","Challan Date","Balance","MTD Sales","MTD Collection","O/S Total","<=10 Days","11 to 17 Days","18 to 25 Days","26 to 30 Days","31 to 45 Days","46 to 60 Days","61 to 90 Days","91 to 120 Days","121 to 180 Days","> 180 Days","ONACC"';
$output .="\n";
// Get Records from the table

while ($row1 = mysql_fetch_array($sql)) {
		$zone = $row1["zone"];
		$classdescr = $row1["classdescr"];
		$alias = $row1["alias"];
		$party = $row1["party"];
		$securitydeposit = $row1["securitydeposit"];
		$crlim = $row1["crlim"];
		$colldt = date('d/m/Y H:i:s',strtotime($row1["colldt"]));
		$chllndt = date('d/m/Y H:i:s',strtotime($row1["chllndt"]));
		$mobal = $row1["mobal"];
		$mtdsales = $row1["mtdsales"];
		$mtdcoll = $row1["mtdcoll"];
		$ostotal = $row1["ostotal"];
		$less_equ_10days = $row1["less_equ_10days"];
		$eleven_17 = $row1["11_17days"];
		$eighteen_25 = $row1["18_25days"];
		$twentysix_30 = $row1["26_30days"];
		$thirtyone_45 = $row1["31_45days"];
		$fortysix_60 = $row1["46_60days"];
		$sixtyone_90 = $row1["61_90days"];
		$nintyone_120 = $row1["91_120days"];
		$onetwentyone_180 = $row1["121_180days"];
		$greater_180 = $row1["greater_180days"];
		$onacc = $row1["onacc"];
		
$output .= '"'.$zone.'","'.$classdescr.'","'.$alias.'","'.$party.'","'.$securitydeposit.'","'.$crlim.'","'.$colldt.'","'.$chllndt.'","'.$mobal.'","'.$mtdsales.'","'.$mtdcoll.'","'.$ostotal.'","'.$less_equ_10days.'","'.$eleven_17.'","'.$eighteen_25.'","'.$twentysix_30.'","'.$thirtyone_45.'","'.$fortysix_60.'","'.$sixtyone_90.'","'.$nintyone_120.'","'.$onetwentyone_180.'","'.$greater_180.'","'.$onacc.'"';

$output .="\n";
$slno_cnt++;
}
// Download the file
$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "ageing".$curr_date.".csv";
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$the_file_name);
header('Pragma: no-cache');    
header('Expires: 0');
echo $output;
exit;

mysql_close();
?>
<?php
include "star_connection.php";
$ledger = "ledger";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";
$ledger_data = array();
$ledger_balance_data = array();
$the_order_id = $_REQUEST["the_order_id"] ? addslashes(trim($_REQUEST["the_order_id"])) : "";
if($the_order_id!=""){


$app_ref_no = "";
$material = "";
$so_num = "";
$erdat = "";
$erzet = "";
$upd_tmstmp = "";
$kwmeng = "";
$vrkme = "";
$vstel = "";
$remarks = "";


/*"app_ref_no": "SS0000160",
"material": "14000092",
"so_num": "3000000521",
"erdat": "/Date(1655942400000)/",
"erzet": "PT16H22M54S",
"upd_tmstmp": "/Date(1655981574359+0000)/",
"kwmeng": "25.000",
"vrkme": "TO",
"vstel": "",
"remarks": ""
*/

$url_ck1 = 'https://prdapp1.starcement.co.in:44310/sap/opu/odata/sap/ZSD_PARKLOT_SO_SERV_CDS/ZSD_PARKLOT_SO_SERV(\''.$the_order_id.'\')?$format=json&sap-client=900';
$body_for_mcode1 = get_data_from_cserver($url_ck1);
if(isJsonCk($body_for_mcode1)){
	$json_decoded = json_decode($body_for_mcode1,true);
	if(count($json_decoded)>0){
		if(array_key_exists("d",$json_decoded)){
		$app_ref_no = $json_decoded["d"]["app_ref_no"];
		$material = $json_decoded["d"]["material"];
		$so_num = $json_decoded["d"]["so_num"];
		$erdat = $json_decoded["d"]["erdat"];
		$erdat_date_format = "";
		if($erdat!=""){
		$erdat_str = str_replace("/","",$erdat);
		$erdat_str = str_replace("Date","",$erdat_str);	
		$erdat_str = str_replace("(","",$erdat_str);
		$erdat_str = str_replace(")","",$erdat_str);
		$erdat_str = ($erdat_str / 1000);
		$erdat_date_format = date("jS M Y",$erdat_str);
		}
		$erzet = $json_decoded["d"]["erzet"];
		$upd_tmstmp = $json_decoded["d"]["upd_tmstmp"];
		$upd_tmstmp_format = "";
		if($upd_tmstmp!=""){
		$upd_tmstmp_str = str_replace("/","",$upd_tmstmp);
		$upd_tmstmp_str = str_replace("Date","",$upd_tmstmp_str);	
		$upd_tmstmp_str = str_replace("(","",$upd_tmstmp_str);
		$upd_tmstmp_str = str_replace(")","",$upd_tmstmp_str);
		$upd_tmstmp_str = ($upd_tmstmp_str / 1000);
		$upd_tmstmp_format = date("jS M Y",$upd_tmstmp_str);
		}
		$kwmeng = $json_decoded["d"]["kwmeng"];
		$vrkme = $json_decoded["d"]["vrkme"];
		$vstel = $json_decoded["d"]["vstel"];
		$remarks = $json_decoded["d"]["remarks"];
		
		
		}
		
	}
}


$res_data = array("process_status"=>"YES","process_message"=>"Success.","app_ref_no"=>$app_ref_no,"material"=>$material,"so_num"=>$so_num,"erdat"=>$erdat_date_format,"erzet"=>$erzet,"upd_tmstmp"=>$upd_tmstmp_format,"kwmeng"=>$kwmeng,"vrkme"=>$vrkme,"vstel"=>$vstel,"remarks"=>$remarks);

}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"The id is mandatory.");
}	
echo json_encode($res_data);
mysql_close();
?>
<?php
set_time_limit(0);
	ini_set('memory_limit', '-1');

include "web_check.php";
include "star_connection.php";

function sort_by_date($a, $b) {
    $a = strtotime($a['billdt']);
    $b = strtotime($b['billdt']);
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;
	//return strtotime($a) - strtotime($b);
}


$the_start_date = $_GET["the_start_date"] ? addslashes(trim($_GET["the_start_date"])) :'';
$the_end_date = $_GET["the_end_date"] ? addslashes(trim($_GET["the_end_date"])) : "";
$sswa_user_type = $_SESSION["sswa_user_type"];
$sswa_selected_dealer_code = $_SESSION["sswa_selected_dealer_code"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];
//echo $sswa_selected_customer_code=$_GET["customer_code"];

$curr_date = date("Y-m-d");
$curr_month = date("m");
$initial_year='2022';

if($the_start_date=='' && $the_end_date=='')
		{
		$curr_date_time = date("Y-m-d H:i:s");
			if($curr_month > 3)
			{
				$monthyearstart=date("Y",strtotime($curr_date_time))-1;
				$monthyearend=date("Y",strtotime($curr_date_time));
			}
			else
			{
				$monthyearstart=date("Y",strtotime($curr_date_time))-2;
				$monthyearend=date("Y",strtotime($curr_date_time))-1;
			}

			//$the_start_date='31/07/'.$monthyearstart;
			//$the_end_date =  '31/03/'.$monthyearend;
			$the_start_date='31/07/'.$initial_year;
			$the_end_date =  date('d/m/Y',strtotime($curr_date));
	
		//$the_start_date_time = date('Y-m-d', strtotime("-7 days,$curr_date"))."T00:00:00";
		
		//$the_start_date_time = $monthyearstart."-07-31T00:00:00";	
		//$the_end_date_time = $monthyearend."-03-31T00:00:00";
		$the_start_date_time=$initial_year."-07-31T00:00:00";
		$the_end_date_time =  date('Y-m-d',strtotime($curr_date))."T00:00:00";
		}
		else
		{
	
			$the_start_date_time=date('Y-m-d', strtotime($the_start_date))."T00:00:00";
			$the_end_date_time=date('Y-m-d', strtotime($the_end_date))."T00:00:00";
			
						$the_start_date=date('d/m/Y', strtotime($the_start_date));
			$the_end_date =  date('d/m/Y',strtotime($the_end_date));

		}

$customer_master='customer_master';

$ledger_total_balance = 0;
$ledger_link = "";

$sql3 = "select `dns_customer_code`,`customer_id`,customer_name from $customer_master where `customer_code`='$sswa_selected_customer_code'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
$row3 = mysql_fetch_assoc($res3);
$the_dealer_id = trim($row3["dns_customer_code"]);
$customer_name = trim($row3["customer_name"]);
$the_customer_id = trim($row3["customer_id"]);
}else{
$the_customer_id = "";
}

$output = "<html><body><div  class=\"container\" style=\"width:100%;\"><img src=\"images/star-pdf-logo.jpeg\" alt=\"Star\" width=\"100px\" style=\"position:absolute; top:0px;\"/><h3 style=\"text-align:center;\">STAR CEMENT LIMITED</h3><h4 style=\"text-align:center;\">SECURITY DEPOSIT LEDGER</h4><p style=\"text-align:center;\">Period from ".$the_start_date." To ".$the_end_date."</p> <p><strong>Party Code</strong> : ".$the_customer_id." </p><p><strong>Party Name</strong> : ".$customer_name."</p><table style=\"width:100%; text-align:left;border:1px solid #333; \"><thead>
        <tr style=\"height: 50px;\">
          <th style=\"border-left: 1px solid #dee2e6; padding:5px;  width:15%\"><b>Document No</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:15%\"><b>Document Date</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:10%\"><b>AMTDR (Rs.)</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:10%\"><b>AMTCR (Rs.)</b></th>
		  <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:10%\"><b>TDS (Rs.)</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:30%\"><b>Narration</b></th>
        </tr>
      </thead>
      <tbody>";
	  $url_ck1 = 'https://starfiori.starcement.co.in:44300/sap/opu/odata/sap/ZOVW_LEDG_SECDEP_CDS/ZOVW_LEDG_SECDEP(p_Code=\''.$the_customer_id.'\',p_Frm=datetime\''.$the_start_date_time.'\',p_To=datetime\''.$the_end_date_time.'\')/Set?$format=json&sap-client=900';

$body_for_mcode10 = get_data_from_cserver($url_ck1);
if(isJsonCk($body_for_mcode10)){
$json_decoded21 = json_decode($body_for_mcode10,true);
if(count($json_decoded21)>0){
if(array_key_exists("d",$json_decoded21)){
	$app_results_arr = $json_decoded21["d"]["results"];
	//print_r($app_results_arr);
if(count($app_results_arr)>0){
	foreach($app_results_arr as $app_results_aval){
		$billno  = $app_results_aval["billno"];
		$billdt = $app_results_aval["billdt"];
		$amtdr  = $app_results_aval["amtdr"];
		$amtcr  = $app_results_aval["amtcr"];
		$narration = $app_results_aval["narration"];
		$tdsamt = $app_results_aval["tdsamt"];
		
		$Bldat_format = "";
		if($billdt!=""){
		$Bldat_str = str_replace("/","",$billdt);
		$Bldat_str = str_replace("Date","",$Bldat_str);	
		$Bldat_str = str_replace("(","",$Bldat_str);
		$Bldat_str = str_replace(")","",$Bldat_str);
		$Bldat_str = ($Bldat_str / 1000);
		//$DocDate_format = date("m/d/Y h:i:s A",$DocDate_str);
		$Bldat_format = date("Y-m-d",$Bldat_str);
		}
		$security_ledger_data[]= array("billno"=>$billno,"billdt"=>$Bldat_format,"amtdr"=>$amtdr,"amtcr"=>$amtcr,"narration"=>$narration,"tdsamt"=>$tdsamt);
		
	}
	//print_r($credit_note_data);
	usort($security_ledger_data, 'sort_by_date');
	foreach($security_ledger_data as $security_ledger_data_val)
	{
		
		$billno = $security_ledger_data_val["billno"];
		$billdt = $security_ledger_data_val["billdt"];
		$amtdr = round($security_ledger_data_val["amtdr"],2);
		$amtcr = round($security_ledger_data_val["amtcr"],2);
		$narration = $security_ledger_data_val["narration"];
		$tdsamt = round($security_ledger_data_val["tdsamt"],2);
		
		$Bldat_format = date("d/m/Y",strtotime($billdt));
		
		$output .= "<tr>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:15%\">".$billno."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:15%\">".$Bldat_format."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; text-align: right;width:10%\">".number_format($amtdr,2)."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; text-align: right;width:10%\">".number_format($amtcr,2)."</td>
		  <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; text-align: right;width:10%\">".number_format($tdsamt,2)."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:30%\">".$narration."</td>
        </tr>";
		//$total_qty=$total_qty+$quayntity;
		$total_CrAmount=$total_CrAmount+$amtcr;
		$total_DrAmount=$total_DrAmount+$amtdr;
		//$final_balance=$final_balance+$total_balance;
		
	  }
   }
  }
 }
 $output .="</tbody>";
 $output .= "<tfoot><tr>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:15%\"><b>Total</b></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:15%\"></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; text-align: right;padding:5px; width:10%\"><b>".number_format($total_DrAmount,2)."</b></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; text-align: right;padding:5px; width:10%\"><b>".number_format($total_CrAmount,2)."</b></td>
		  <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:10%\"></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:30%\"><b>Balance - ".number_format(($total_CrAmount-$total_DrAmount),2)."</b></td>
        </tr></tfoot>";
 $output .= "</table></div></body></html>";
}
else
{
	$output .= "<tr><td colspan=\"13\">No Records<td></td></tr></table></body></html>";
}

creditnotePDFfile($output);
function creditnotePDFfile($output){
	$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "security_ledger_".$curr_date.".pdf";
//$output .='"'.$booking_id.'","'.$booking_date.'","'.$booking_slot_timing.'","'.$facility_tee_type.'","'.$booking_status.'","'.$primary_member_id.'","'.$primary_member_name.'","'.$primary_member_mobile.'","'.$pair_member2_id.'","'.$pair_member2_name.'","'.$pair_member2_mobile.'","'.$pair_member3_id.'","'.$pair_member3_name.'","'.$pair_member3_mobile.'","'.$pair_member4_id.'","'.$pair_member4_name.'","'.$pair_member4_mobile.'","'.$carts_needed.'","'.$caddies_needed.'","'.$release_choice.'","'.$created_date.'"';
//$output .="\n";
require_once('tcpdf/tcpdf.php');
		//define ('PDF_MARGIN_RIGHT', 4);
$pdf=new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetPrintHeader(false);
		$pdf->SetPrintfooter(false);
		$pdf->SetTopMargin(0);
		//$pdf->SetLeftMargin(0);
$pdf->SetFont('helvetica', '', 9);
$pdf->AddPage('L',"A4");
$pdf->writeHTML($output);
//Use 'D' for download
$tcpdf=$pdf->Output($the_file_name, 'D');
	
if($conn!=""){
mysqli_close($conn);
}
exit;
}
?>
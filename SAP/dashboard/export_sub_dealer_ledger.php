<?php
include "web_check.php";
include "star_connection.php";

$the_start_date = $_GET["the_start_date"] ? addslashes(trim($_GET["the_start_date"])) :'';
$the_end_date = $_GET["the_end_date"] ? addslashes(trim($_GET["the_end_date"])) : "";
$the_subdealer_id = $_GET["the_subdealer_id"] ? addslashes(trim($_GET["the_subdealer_id"])) : "";
$sswa_user_type = $_SESSION["sswa_user_type"];
$sswa_selected_dealer_code = $_SESSION["sswa_selected_dealer_code"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];
//echo $sswa_selected_customer_code=$_GET["customer_code"];

$curr_date = date("m/d/Y");
function sort_by_date($a, $b) {
    $a = strtotime($a['DOCDT']);
    $b = strtotime($b['DOCDT']);
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;
	//return strtotime($a) - strtotime($b);
}

if($the_start_date=='' && $the_end_date=='')
		{
		$the_start_date_time = date('Y-m-d', strtotime("-7 days,$curr_date"))."T00:00:00";
		$the_end_date_time = $curr_date."T00:00:00";
		}
		else
		{
			$the_start_date_time=date('Y-m-d', strtotime($the_start_date))."T00:00:00";
			$the_end_date_time=date('Y-m-d', strtotime($the_end_date))."T00:00:00";
		}

$customer_master='customer_master';

$ledger_total_balance = 0;
$ledger_link = "";

$sql3 = "select `dns_customer_code`,`customer_id`,customer_name,customer_code from $customer_master where `customer_id`='$the_subdealer_id'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
$row3 = mysql_fetch_assoc($res3);
$the_dealer_id = trim($row3["dns_customer_code"]);
$customer_name = trim($row3["customer_name"]);
$customer_code = trim($row3["customer_code"]);
}else{
$the_customer_id = "";
}

$output = "<html><body><div  class=\"container\" style=\"width:100%;\"><img src=\"images/star-pdf-logo.jpeg\" alt=\"Star\" width=\"100px\" style=\"position:absolute; top:0px;\"/><h3 style=\"text-align:center;\">STAR CEMENT LIMITED</h3><h4 style=\"text-align:center;\">SUB DEALER LEDGER</h4><p style=\"text-align:center;\">Period from ".$the_start_date." To ".$the_end_date."</p> <p><strong>Party Code</strong> : ".$the_subdealer_id." </p><p><strong>Sub Dealer Name</strong> : ".$customer_name."</p><table style=\"width:100%; text-align:left;border:1px solid #333; \"><thead>

        <tr style=\"height: 50px;\">
          <th style=\"border-left: 1px solid #dee2e6; padding:5px;  width:15%\"><b>Sub dealer Code</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:20%\"><b>Sub dealer Name</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:20%\"><b>DOCDT</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:15%\"><b>DOCNO</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:20%\"><b>NARRATION</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:10%\"><b>AMOUNT(Rs.)</b></th>
        </tr>
      </thead>
      <tbody>";
	  $the_filter = '&$filter=(Kunnr eq \''.$the_customer_id.'\' and ( Bldat ge datetime\''.$the_start_date_time.'\' and Bldat le datetime\''.$the_end_date_time.'\') )';

$url_ck1 = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/YVW_CPSDLEDGER_CDS/YVW_CPSDLEDGER(p_Code=\''.$the_subdealer_id.'\',p_Frm=datetime\''.$the_start_date_time.'\',p_To=datetime\''.$the_end_date_time.'\')/Set?$format=json&sap-client=900';

$body_for_mcode10 = get_data_from_cserver($url_ck1);
if(isJsonCk($body_for_mcode10)){
$json_decoded21 = json_decode($body_for_mcode10,true);
if(count($json_decoded21)>0){
if(array_key_exists("d",$json_decoded21)){
	$app_results_arr = $json_decoded21["d"]["results"];
	//print_r($app_results_arr);
if(count($app_results_arr)>0){
	foreach($app_results_arr as $app_results_aval){
		$SUBDEALERCODE = $app_results_aval["SUBDEALERCODE"];
		$SUBDEALER = $app_results_aval["SUBDEALER"];
		$DOCDT = $app_results_aval["DOCDT"];
		$NARRATION = $app_results_aval["NARRATION"];
		$DOCNO = $app_results_aval["DOCNO"];
		$AMOUNT = $app_results_aval["AMOUNT"];
		
		$DOCDT_format = "";
		if($DOCDT!=""){
		$DOCDT_str = str_replace("/","",$DOCDT);
		$DOCDT_str = str_replace("Date","",$DOCDT_str);	
		$DOCDT_str = str_replace("(","",$DOCDT_str);
		$DOCDT_str = str_replace(")","",$DOCDT_str);
		$DOCDT_str = ($DOCDT_str / 1000);
		//$DocDate_format = date("m/d/Y h:i:s A",$DocDate_str);
		$DOCDT_format = date("Y-m-d",$DOCDT_str);
		}
		$sub_dealer_ledger_data[]= array("SUBDEALERCODE"=>$SUBDEALERCODE,"SUBDEALER"=>$SUBDEALER,"DOCDT"=>$DOCDT_format,"NARRATION"=>$NARRATION,"DOCNO"=>$DOCNO,"AMOUNT"=>$AMOUNT);
		
	}
	//print_r($credit_note_data);
	usort($sub_dealer_ledger_data, 'sort_by_date');
	foreach($sub_dealer_ledger_data as $sub_dealer_ledger_data_val)
	{
		$SUBDEALERCODE = $sub_dealer_ledger_data_val["SUBDEALERCODE"];
		$SUBDEALER = $sub_dealer_ledger_data_val["SUBDEALER"];
		$DOCDT = $sub_dealer_ledger_data_val["DOCDT"];
		$NARRATION = $sub_dealer_ledger_data_val["NARRATION"];
		$DOCNO = $sub_dealer_ledger_data_val["DOCNO"];
		$AMOUNT = $sub_dealer_ledger_data_val["AMOUNT"];
		
		$DOCDT_format = date("d/m/Y",strtotime($DOCDT));
		
		$output .= "<tr>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:15%\">".$SUBDEALERCODE."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:20%\">".$SUBDEALER."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:20%\">".$DOCDT_format."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:15%\">".$DOCNO."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:20%\">".$NARRATION."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6;text-align: right; padding:5px; width:10%\">".number_format($AMOUNT,2)."</td>
        </tr>";
		$total_AMOUNT=$total_AMOUNT+$AMOUNT;
	  }
   }
  }
 }
 $output .="</tbody>";
 $output .= "<tfoot><tr>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:15%\"><b>Total</b></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:20%\"></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:20%\"></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:15%\"><b></b></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:20%\"><b></b></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:10%\"><b>".number_format($total_AMOUNT,2)."</b></td>
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
$the_file_name = "sub_dealer_ledger_".$curr_date.".pdf";
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
<?php
include "web_check.php";
include "star_connection.php";

$the_start_date = $_GET["the_start_date"] ? addslashes(trim($_GET["the_start_date"])) :'';
$the_end_date = $_GET["the_end_date"] ? addslashes(trim($_GET["the_end_date"])) : "";
$sswa_user_type = $_SESSION["sswa_user_type"];
$sswa_selected_dealer_code = $_SESSION["sswa_selected_dealer_code"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];
//echo $sswa_selected_customer_code=$_GET["customer_code"];

$curr_date = date("m/d/Y");
function sort_by_date($a, $b) {
    $a = strtotime($a['DNDT']);
    $b = strtotime($b['DNDT']);
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

$output = "<html><body><div  class=\"container\" style=\"width:100%;\"><img src=\"images/star-pdf-logo.jpeg\" alt=\"Star\" width=\"100px\" style=\"position:absolute; top:0px;\"/><h3 style=\"text-align:center;\">STAR CEMENT LIMITED</h3><h4 style=\"text-align:center;\">Debit Note Register</h4><p style=\"text-align:center;\">Period from ".$the_start_date." To ".$the_end_date."</p> <p><strong>Party Code</strong> : ".$the_customer_id." </p><p><strong>Party Name</strong> : ".$customer_name."</p><table style=\"width:100%; text-align:left;border:1px solid #333; \"><thead>

        <tr style=\"height: 50px;\">
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:10%\"><b>GSTIN</b></th>
		  <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:9%\"><b>DNNO</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:8%\"><b>DNDT</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:12%\"><b>PARTICULARS</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:6%\"><b>QTY</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:8%\"><b>BASIC</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:8%\"><b>CGST</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:8%\"><b>SGST</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:8%\"><b>IGST</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:7%\"><b>VAT/CST</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:7%\"><b>ROFF</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px; width:9%\"><b>AMOUNT(Rs.)</b></th>
        </tr>
      </thead>
      <tbody>";
	$url_ck1 = 'https://starfiori.starcement.co.in:44300/sap/opu/odata/sap/YCP_SDN_CDS/YCP_SDN(p_Code=\''.$the_customer_id.'\',p_Frm=datetime\''.$the_start_date_time.'\',p_To=datetime\''.$the_end_date_time.'\')/Set?$format=json&sap-client=900';

$body_for_mcode10 = get_data_from_cserver($url_ck1);
if(isJsonCk($body_for_mcode10)){
$json_decoded21 = json_decode($body_for_mcode10,true);
if(count($json_decoded21)>0){
if(array_key_exists("d",$json_decoded21)){
	$app_results_arr = $json_decoded21["d"]["results"];
	//print_r($app_results_arr);
if(count($app_results_arr)>0){
	foreach($app_results_arr as $app_results_aval){
		$kunnr = $app_results_aval["p_Code"];
		$GSTNO = $app_results_aval["GSTNO"];
		$DNNO = $app_results_aval["DNNO"];
		$DNDT = $app_results_aval["DNDT"];
		$NARRATION = $app_results_aval["NARRATION"];
		$QTY = $app_results_aval["QTY"];
		$BASIC = $app_results_aval["BASIC"];
		$CGST = $app_results_aval["CGST"];
		$SGST = $app_results_aval["SGST"];
		$IGST = $app_results_aval["IGST"];
		$TCS = $app_results_aval["TCS"];
		$ROFF = $app_results_aval["ROFF"];
		$AMOUNT = $app_results_aval["AMOUNT"];
		
		$DNDT_format = "";
		if($DNDT!=""){
		$DNDT_str = str_replace("/","",$DNDT);
		$DNDT_str = str_replace("Date","",$DNDT_str);	
		$DNDT_str = str_replace("(","",$DNDT_str);
		$DNDT_str = str_replace(")","",$DNDT_str);
		$DNDT_str = ($DNDT_str / 1000);
		//$DocDate_format = date("m/d/Y h:i:s A",$DocDate_str);
		$DNDT_format = date("Y-m-d",$DNDT_str);
		}
		$debit_note_data[]= array("kunnr"=>$kunnr,"GSTNO"=>$GSTNO,"DNDT"=>$DNDT_format,"NARRATION"=>$NARRATION,"QTY"=>$QTY,"BASIC"=>$BASIC,"CGST"=>$CGST,"SGST"=>
		$SGST,"IGST"=>$IGST,"TCS"=>$TCS,"ROFF"=>$ROFF,"AMOUNT"=>$AMOUNT,"DNNO"=>$DNNO);
		
	}
	//print_r($credit_note_data);
	usort($debit_note_data, 'sort_by_date');
	foreach($debit_note_data as $debit_note_data_val)
	{
		$kunnr = $debit_note_data_val["p_Code"];
		$GSTNO = $debit_note_data_val["GSTNO"];
		$DNNO = $debit_note_data_val["DNNO"];
		$DNDT_format = $debit_note_data_val["DNDT"];
		$NARRATION = $debit_note_data_val["NARRATION"];
		$QTY = $debit_note_data_val["QTY"];
		$BASIC = $debit_note_data_val["BASIC"];
		$CGST = $debit_note_data_val["CGST"];
		$SGST = $debit_note_data_val["SGST"];
		$IGST = $debit_note_data_val["IGST"];
		$TCS = $debit_note_data_val["TCS"];
		$ROFF = $debit_note_data_val["ROFF"];
		$AMOUNT = $debit_note_data_val["AMOUNT"];
		
		$DNDT_format = date("d/m/Y",strtotime($DNDT_format));
		 
		$output .= "<tr>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:10%\">".$GSTNO."</td>
		  <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:9%\">".$DNNO."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:8%\">".$DNDT_format."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:12%\">".$NARRATION."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:6%\">".number_format($QTY,2)."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:8%\">".number_format($BASIC,2)."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:8%\">".number_format($CGST,2)."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:8%\">".number_format($SGST,2)."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:8%\">".number_format($IGST,2)."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:7%\">".number_format($TCS,2)."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:7%\">".number_format($ROFF,2)."</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:9%\">".number_format($AMOUNT,2)."</td>
        </tr>";
		$total_qty=$total_qty+$QTY;
		$total_basic=$total_basic+$BASIC;
		$total_CGST=$total_CGST+$CGST;
		$total_SGST=$total_SGST+$SGST;
		$total_IGST=$total_IGST+$IGST;
		$total_TCS=$total_TCS+$TCS;
		$total_ROFF=$total_ROFF+$ROFF;
		$total_AMOUNT=$total_AMOUNT+$AMOUNT;
		
	  }
   }
  }
 }
 $output .="</tbody>";
 $output .= "<tfoot><tr>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:10%\"></td>
		  <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:9%\"></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:8%\"></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px; width:12%\"></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:6%\"><b>".number_format($total_qty,2)."</b></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:8%\"><b>".number_format($total_basic,2)."</b></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:8%\"><b>".number_format($total_CGST,2)."</b></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:8%\"><b>".number_format($total_SGST,2)."</b></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:8%\"><b>".number_format($total_IGST,2)."</b></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:7%\"><b>".number_format($total_TCS,2)."</b></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:7%\"><b>".number_format($total_ROFF,2)."</b></td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px;text-align: right; width:9%\"><b>".number_format($total_AMOUNT,2)."</b></td>
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
$the_file_name = "debit_note_".$curr_date.".pdf";
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
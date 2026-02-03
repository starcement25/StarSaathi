<?php
//include "web_check.php";
include "star_connection.php";

$the_start_date = $_GET["the_start_date"] ? addslashes(trim($_GET["the_start_date"])) :'';
$the_end_date = $_GET["the_end_date"] ? addslashes(trim($_GET["the_end_date"])) : "";
$sswa_user_type = $_SESSION["sswa_user_type"];
$sswa_selected_dealer_code = $_SESSION["sswa_selected_dealer_code"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];

$ledger_total_balance = 0;
$ledger_link = "";

$sql3 = "select `dns_customer_code`,`customer_id`,customer_name from $customer_master where `customer_code`='$sswa_selected_customer_code'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
$row3 = mysql_fetch_assoc($res3);
$the_dealer_id = trim($row3["dns_customer_code"]);
$the_customer_id = trim($row3["customer_id"]);
}else{
$the_customer_id = "";
}

$output = "<html><body><h3 style=\"text-align:center;\">STAR CEMENT LIMITED</h3><h4 style=\"text-align:center;\">Credit Note Register</h4><p style=\"text-align:center;\">Period from 01/02/2023 To 20/02/2023</p> <p><strong>Party Code</strong> : 100000281 <span style=\"float:right\">20/02/2023</span></p><p><strong>Party Name</strong> : ADHIKARI ENTERPRIZE</p><table style=\"width:100%; text-align:left;border:1px solid #333; \"><thead>
        <tr>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px\"><b>GSTIN</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px\"><b>VOUCHERDT</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px\"><b>PARTICULARS</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px\"><b>QTY</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px\"><b>BASIC</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px\"><b>CGST</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px\"><b>SGST</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px\"><b>IGST</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px\"><b>VAT/CST</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px\"><b>ROFF</b></th>
          <th style=\"border-left: 1px solid #dee2e6; padding:5px\"><b>AMOUNT</b></th>
        </tr>
      </thead>
      <tbody>";
	$url_ck1 = 'https://PRDAPP1.starcement.co.in:44310/sap/opu/odata/sap/YCP_SCNDN_CDS/YCP_SCNDN(p_Code=\''.$the_customer_id.'\',p_Frm=datetime\''.$the_start_date_time.'\',p_To=datetime\''.$the_end_date_time.'\')/Set?$format=json&sap-client=900';

/*$body_for_mcode10 = get_data_from_cserver($url_ck1);
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
		$CRDT = $app_results_aval["CRDT"];
		$NARRATION = $app_results_aval["NARRATION"];
		$QTY = $app_results_aval["QTY"];
		$BASIC = $app_results_aval["BASIC"];
		$CGST = $app_results_aval["CGST"];
		$SGST = $app_results_aval["SGST"];
		$IGST = $app_results_aval["IGST"];
		$TCS = $app_results_aval["TCS"];
		$ROFF = $app_results_aval["ROFF"];
		$AMOUNT = $app_results_aval["AMOUNT"];
		
		$CRDT_format = "";
		if($CRDT!=""){
		$CRDT_str = str_replace("/","",$CRDT);
		$CRDT_str = str_replace("Date","",$CRDT_str);	
		$CRDT_str = str_replace("(","",$CRDT_str);
		$CRDT_str = str_replace(")","",$CRDT_str);
		$CRDT_str = ($CRDT_str / 1000);
		//$DocDate_format = date("m/d/Y h:i:s A",$DocDate_str);
		$CRDT_format = date("d/m/Y",$CRDT_str);
		}
	}
}
}*/
for($i=0;$i<50;$i++)
{
	$output .= "<tr>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px\">18AACCC1465A4Z3</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px\">04/02/2023</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px\">0160044480/G21600017662 <br/> JAN 23 (02)</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px\">3240.000</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px\">759375.00</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px\">106312.50</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px\">106312.50</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px\">0.00</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px\">0.00</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px\">0.00</td>
          <td style=\"border-top: 1px solid #dee2e6;border-left:1px solid #dee2e6; padding:5px\">972000.00</td>
        </tr>";
}

        $output.="</tbody><tfoot><tr>
          <th style=\"border-top: 1px solid #dee2e6; border-left:1px solid #dee2e6; padding:5px\"></th>
          <th style=\"border-top: 1px solid #dee2e6; border-left:1px solid #dee2e6; padding:5px\"></th>
          <th style=\"border-top: 1px solid #dee2e6; border-left:1px solid #dee2e6; padding:5px\"></th>
          <th style=\"border-top: 1px solid #dee2e6; border-left:1px solid #dee2e6; padding:5px\">9140.250</th>
          <th style=\"border-top: 1px solid #dee2e6; border-left:1px solid #dee2e6; padding:5px\">1386765.20</th>
          <th style=\"border-top: 1px solid #dee2e6; border-left:1px solid #dee2e6; padding:5px\">19414292.92</th>
          <th style=\"border-top: 1px solid #dee2e6; border-left:1px solid #dee2e6; padding:5px\">19414292.92</th>
          <th style=\"border-top: 1px solid #dee2e6; border-left:1px solid #dee2e6; padding:5px\">0.00</th>
          <th style=\"border-top: 1px solid #dee2e6; border-left:1px solid #dee2e6; padding:5px\">0.00</th>
          <th style=\"border-top: 1px solid #dee2e6; border-left:1px solid #dee2e6; padding:5px\">0.00</th>
          <th style=\"border-top: 1px solid #dee2e6; border-left:1px solid #dee2e6; padding:5px\">1775021.00</th>
        </tr>
      </tfoot>";
 //}
 $output .= "</table></body></html>";
//}
/*else
{
	$output .= "<tr><td colspan=\"13\">No Records<td></td></tr></table></tbody></body></html>";
}*/
require_once('tcpdf/tcpdf.php');
class LongTableTCPDF extends TCPDF {
private $longTableHeader = '';
private $longTableFooter = '';

public function Header() {
$this->writeHTML($this->longTableHeader);
}

public function Footer() {
$this->writeHTML($this->longTableFooter);
}

public function setLongTableHeader($html) {
$this->longTableHeader = $html;
}

public function setLongTableFooter($html) {
$this->longTableFooter = $html;
}
}
//creditnotePDFfile($output);
//function creditnotePDFfile($output){
	$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "credit_note_".$curr_date.".pdf";
//$output .='"'.$booking_id.'","'.$booking_date.'","'.$booking_slot_timing.'","'.$facility_tee_type.'","'.$booking_status.'","'.$primary_member_id.'","'.$primary_member_name.'","'.$primary_member_mobile.'","'.$pair_member2_id.'","'.$pair_member2_name.'","'.$pair_member2_mobile.'","'.$pair_member3_id.'","'.$pair_member3_name.'","'.$pair_member3_mobile.'","'.$pair_member4_id.'","'.$pair_member4_name.'","'.$pair_member4_mobile.'","'.$carts_needed.'","'.$caddies_needed.'","'.$release_choice.'","'.$created_date.'"';
//$output .="\n";

		//define ('PDF_MARGIN_RIGHT', 4);
$pdf=new LongTableTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetPrintHeader(false);
		$pdf->SetPrintfooter(false);
		$pdf->SetTopMargin(5);
		//$pdf->SetLeftMargin(0);
$pdf->SetFont('helvetica', '', 9);
$pdf->AddPage('L',"A4");
//$pdf->SetAutoPageBreak(true);
//$pdf->setLongTableFooter($footerTableHTML);
$pdf->writeHTML($output);
//Use 'D' for download
$tcpdf=$pdf->Output($the_file_name, 'D');
	
if($conn!=""){
mysqli_close($conn);
//}
exit;
}
?>

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
for($i=0;$i<=10;$i++)
{
$output .= "<html><body style=\"border:1px solid #333;\">
<div class=\"container\" style=\"width:100%;\">
 <table style=\"width:100%; text-align:left; border:1px solid #333;\">
  <tr>
   <td style=\"width:30%;text-align:left;\"><img src=\"images/star-pdf-logo.jpeg\" width=\"50px\" style=\"position: absolute; top:10px; left:10px;\" /></td>
   <td style=\"width:40%;text-align:center;\"> <p style=\"text-align:center; font-size:10px;margin: 0px; padding-top:2px;\">CompName1</p>
  		<p style=\"text-align:center;font-size:8px;margin: 0px; padding-top:2px;\">CinNo</p>
  		<p style=\"text-align:center; font-size:8px;margin: 0px; padding-top:2px;\">WorksOff</p>
  		<p style=\"text-align:center;font-size:8px;\"><strong>CompName2</strong></p></td>
   	<td style=\"width:30%;text-align:right;\" > <img src=\"images/qr.jpg\" alt=\"Star\" width=\"50px\" style=\"position: absolute; top:10px; right:10px;\"/>  </td>
   </tr>
   </table>
  <table style=\"width:100%; text-align:left; border:1px solid #333;\">
  <tr>
  <td style=\"padding-left:10px;font-size:8px;border-top:1px solid #333;\" colspan=\"2\"><p style=\"padding-left:10px; margin:0px; font-size:12px;\">E-Way Bill No: 881289640460</p></td>
  </tr>
  <tr>
  <td style=\"padding-left:10px;font-size:8px;border-top:1px solid #333;border-bottom:1px solid #333;\" colspan=\"2\"><p style=\"padding-left:10px; margin:0px; font-size:12px;\">IRN No: 7cd1a31beb593226f0b9b803d87c603e33c708b9228641646848ef08b3d2239f</p></td>
  </tr>
  <tr>
  <td style=\"width:50%; padding-left:10px;font-size:10px;\">
  <p style=\"font-size:10px; margin:0px;\">LOKHRA CPW-SCL-LUMS</p><br/><br/>
  D012 LOKHRA CPW-SCL-LUMS C/O- NRD LOGISTIC PARK, BEHIND<br/> DTO OFFICE, BETKUCHI,BALAJI NAGAR GUWAHATI
  </td>
  <td style=\"width:50%;font-size:8px; border-left:1px solid #333; padding:10px\">
  <p style=\"font-size:10px; margin:0px;\">Invoice No: F21700017261</p><br/>
  Invoice Dt:18/02/2023<br/><br/>
  Cust PO No: SS0108197 <span style=\"padding-left:70px;\">Cust PO Dt: 16/02/2023</span>
  </td>
  </tr>
  <tr>
  <td style=\"width:50%; padding-left:10px;font-size:8px; border-top:1px solid #333;\">
  <p style=\"font-size:10px; margin:0px;\">GSTIN: 18AACCC1465A1Z6 <span style=\"padding-left:70px;\">PIN No: 781031</span></p><br/>
  State: ASSAM <span style=\"padding-left:70px;\">State CD: 18</span><br/>
  SO No: 3000108110 <span style=\"padding-left:70px;\">SO Dt: 18/02/2023</span>
  </td>
  <td style=\"width:50%;font-size:8px; border-left:1px solid #333; padding-left:10px;border-top:1px solid #333;\">
  <p style=\"font-size:10px; margin:0px;\">Our Ref No: 9000205105</p><br/>
  Delivery No: 8000174384 <span style=\"padding-left:70px\">Delivery Dt: 18/02/2023</span><br/>
  Shipment No: 7200033392 <span style=\"padding-left:70px;\">Shipment Dt: 16/02/2023</span>
  </td>
  </tr>
  <tr>
  <td style=\"width:50%; padding-left:10px; border-top:1px solid #333; font-size:10px\"><strong>Customer Details:</strong></td>
  <td style=\"width:50%; padding-left:10px; border-top:1px solid #333;border-left:1px solid #333; font-size:10px;\"><strong>Consignee Details:</strong></td>
  </tr>
   <tr>
  <td style=\"width:50%; padding-left:10px; border-top:1px solid #333;\">
  <p style=\"font-size:10px; margin:0px;\">ADHIKARY ENTERPRISE 1000000281</p>
  <p style=\"font-size:8px; margin:0px;\">MAIN BUS STAND BOKO ASSAM BOKO KAMRUP</p>
  <p style=\"font-size:8px; margin:0px; margin-top:10px;\">GSTIN: 18AFRPA9064J1ZY <span style=\"padding-left:70px;\">PIN No: 781123</span></p>
  <p style=\"font-size:8px; margin:0px;\">State:: ASSAM <span style=\"padding-left:70px;\">State CD: 18</span></p>
  </td>
  <td style=\"width:50%; padding-left:10px; border-top:1px solid #333;border-left:1px solid #333;\">
  <p style=\"font-size:10px; margin:0px; margin-top:10px;\">1000000281-TOPAMARI 1400001921</p>
  <p style=\"font-size:8px; margin:0px;\">TOPAMARU TOPAMARU KAMRUP</p>
  <p style=\"font-size:8px; margin:0px; margin-top:10px;\">Batch No: LR07230550/LR07230550/LR07230550/LR07230550</p>
  <p style=\"font-size:8px; margin:0px;\">Destination: 7811360007 TOPAMARI <span style=\"padding-left:70px;\">PIN No: 781136</span></p>
  <p style=\"font-size:8px; margin:0px;\">State: ASSAM <span style=\"padding-left:70px;\">State CD: 18</span></p>
  </td>
  </tr>
  </table>
  <table style=\"width:100%; text-align:left; border:1px solid #333; margin:0px;\">
  <tr>
  <td style=\"width:10%;padding-left:10px; text-align:center; font-size:8px;\"><strong>SL NO</strong></td>
  <td style=\"width:15%;padding-left:10px;border-left:1px solid #333;text-align:center;font-size:8px;\"><strong>Description of Goods</strong></td>
  <td style=\"width:10%;padding-left:10px;border-left:1px solid #333;text-align:center;font-size:8px;\"><strong>HSN</strong></td>
  <td style=\"width:15%;padding-left:10px;border-left:1px solid #333;text-align:center;font-size:8px;\"><strong>Description of Package</strong></td>
  <td style=\"width:10%;padding-left:10px;border-left:1px solid #333;text-align:center;font-size:8px;\"><strong>No of Bags</strong></td>
  <td style=\"width:10%;padding-left:10px;border-left:1px solid #333;text-align:center;font-size:8px;\"><strong>UOM</strong></td>
  <td style=\"width:10%;padding-left:10px;border-left:1px solid #333;text-align:center;font-size:8px;\"><strong>QTY<br/>(MT)</strong></td>
  <td style=\"width:10%;padding-left:10px;border-left:1px solid #333;text-align:center;font-size:8px;\"><strong>Basic Rate /MT <br/>&#8377;</strong></td>
  <td style=\"width:10%;padding-left:10px;border-left:1px solid #333;text-align:center;font-size:8px;\"><strong>Taxable Amount <br/>&#8377;</strong></td>
  </tr>
  <tr>
  <td style=\"width:10%;padding-left:10px; text-align:center;border-top:1px solid #333;\">1</td>
  <td style=\"width:15%;padding-left:10px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;\">STAR CEMENT PPC TRADE</td>
  <td style=\"width:10%;padding-left:10px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;\">25232930</td>
  <td style=\"width:15%;padding-left:10px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;\">BAG</td>
  <td style=\"width:10%;padding-left:10px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;\">150.00</td>
  <td style=\"width:10%;padding-left:10px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;\">TO</td>
  <td style=\"width:10%;padding-left:10px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;\">7.50</td>
  <td style=\"width:10%;padding-left:10px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;\">7328.13</td>
  <td style=\"width:10%;padding-left:10px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px; text-align:right\">54960.94</td>
  </tr>
  <tr>
  <td colspan=\"6\" rowspan=\"3\" style=\"font-size:10px;padding-left:10px;border-top:1px solid #333;\">
  <strong>Declaration</strong><br/>
  Verified that the particulars given above are true and correct and the amount indicated presents the price actually charged and that there is no flow of additional consideration directly or indirectly from the buyer
  </td>
  <td colspan=\"2\" style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px; margin:0px;\">Freight:</td>
  <td style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333; text-align:right;font-size:8px; margin:0px;\">0.00</td>
  </tr>
  <tr>
  <td colspan=\"2\" style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px; margin:0px;\">CGST: @14.00%</td>
  <td style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333; text-align:right;font-size:8px; margin:0px;\">7694.53</td>
  </tr>
  <tr>
  <td colspan=\"2\" style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px; margin:0px;\">SGST: @14.00%</td>
  <td style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333; text-align:right;font-size:8px; margin:0px;\">7694.53</td>
  </tr>
  <tr>
  <td colspan=\"6\" rowspan=\"2\" style=\"padding-left:10px;border-top:1px solid #333;font-size:8px;\">
  Payment to be made electronically through RTGS/NEFT/IMPS & also online UPI payment mode on our
  </td>
  <td colspan=\"2\" style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px; margin:0px;\">IGST:</td>
  <td style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333; text-align:right;font-size:8px; margin:0px;\">0.00</td>
  </tr>
  <tr>
  <td colspan=\"2\" style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px; margin:0px;\">TCS</td>
  <td style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333; text-align:right;font-size:8px; margin:0px;\">0.00</td>
  </tr>
  <tr>
  <td colspan=\"6\" rowspan=\"2\" style=\"padding-left:10px;border-top:1px solid #333;font-size:10px;\">
  <strong>Amount in Words</strong><br />
  SEVENTY THOUSAND THREE HUNDRED FIFTY RUPEES ONLY<br />
  </td>
  <td colspan=\"2\" style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px; margin:0px;\">ROFF:</td>
  <td style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333; text-align:right;font-size:8px; margin:0px;\">0.00</td>
  </tr>
  <tr>
  <td colspan=\"2\" style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px; margin:0px;\"><strong>Total:</strong></td>
  <td style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333; text-align:right;font-size:8px; margin:0px;\"><strong>70350.00</strong></td>
  </tr>
  <tr>
  <td colspan=\"2\" style=\"padding-left:10px;border-top:1px solid #333;font-size:8px;\">
  Mode Of Transport
  </td>
  <td colspan=\"4\" style=\"padding-left:10px; border-top:1px solid #333;font-size:8px; margin:0px;\">Road</td>
  <td colspan=\"3\" style=\"padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;\"></td>
  </tr>
  <tr>
  <td colspan=\"2\" style=\"padding-left:10px;border-top:1px solid #333;font-size:8px;\">
  Transporter Code & Name
  </td>
  <td colspan=\"4\" style=\"padding-left:10px; border-top:1px solid #333;font-size:8px; margin:0px;\">55000345 <span style=\"padding-left:70px;\">CHOUDHURY ENTERPRISE</span></td>
  <td colspan=\"3\" style=\"padding-left:10px;border-left:1px solid #333;\"></td>
  </tr>
  <tr>
  <td colspan=\"2\" style=\"padding-left:10px;border-top:1px solid #333;font-size:8px;\">
  Vehicle Registration No
  </td>
  <td colspan=\"4\" style=\"padding-left:10px; border-top:1px solid #333;font-size:8px; margin:0px;\">AS01FC4547</td>
  <td colspan=\"3\" style=\"padding-left:10px;border-left:1px solid #333; text-align:center;font-size:8px; margin:0px;\"><strong>E & O E,</strong></td>
  </tr>
  <tr>
  <td colspan=\"2\" style=\"padding-left:10px;border-top:1px solid #333;font-size:8px;\">
  LR/RR No & Date
  </td>
  <td colspan=\"4\" style=\"padding-left:10px; border-top:1px solid #333;font-size:8px; margin:0px;\">1779 <span style=\"padding-left:70px;\">18/02/2023</span></td>
  <td colspan=\"3\" style=\"padding-left:10px;border-left:1px solid #333; text-align:center;font-size:8px; margin:0px;\"><strong>STAR CEMENT LIMITED</strong></td>
  </tr>
  <tr>
  <td colspan=\"2\" style=\"padding-left:10px;border-top:1px solid #333;font-size:8px;\">
  Route Name
  </td>
  <td colspan=\"4\" style=\"padding-left:10px; border-top:1px solid #333;font-size:8px; margin:0px;\">R50090 <span style=\"padding-left:70px;\">LOKHARA DUMP to TOPAMARI</span></td>
  <td colspan=\"3\" style=\"padding-left:10px;border-left:1px solid #333; text-align:center;\"></td>
  </tr>
  <tr>
  <td colspan=\"2\" style=\"padding-left:10px;border-top:1px solid #333;font-size:8px;\">
  Inco Terms
  </td>
  <td colspan=\"4\" style=\"padding-left:10px; border-top:1px solid #333;font-size:8px; margin:0px;\">FOR <span style=\"padding-left:70px;\">Free On Road</span></td>
  <td colspan=\"3\" style=\"padding-left:10px;border-left:1px solid #333; text-align:center;\"></td>
  </tr>
  <tr>
  <td colspan=\"6\" style=\"padding-left:10px;border-top:1px solid #333;\">
  <p style=\"font-size:8px; margin:0px;\"><strong>Terms & Conditions</strong></p>
  <p style=\"font-size:8px; margin:0px;\">FOR TERMS & CONDITIONS SEE OVERLEAF</p>
  <p style=\"font-size:8px; margin:0px;\">Amount of TAX Subject to Reverse Charge: NO</p>
  </td>
  <td colspan=\"3\" style=\"padding-left:10px;border-left:1px solid #333; text-align:center; font-size:8px;\"><strong>Authorised Signatory</strong></td>
  </tr>
   <tr>
  <td colspan=\"9\" style=\"padding-left:10px;border-top:1px solid #333;\">
  <p style=\"text-align:center; font-size:8px;\">Guwahati Off: Mayur Garden, 2nd Floor, GS Road, Bhangarh, Guwahati Assam, 781005<br/>Kolkata Off: Century House, Star Cement Limited, p15/1, Taratala Road Kolkata, 700088<br/>
  SUBJECT TO JOWAI JURISDICTION</p>
  </td>
  </tr>
  </table>
</div>
</body></html>";
}
	
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
$the_file_name = "invoice_".$curr_date.".pdf";
//$output .='"'.$booking_id.'","'.$booking_date.'","'.$booking_slot_timing.'","'.$facility_tee_type.'","'.$booking_status.'","'.$primary_member_id.'","'.$primary_member_name.'","'.$primary_member_mobile.'","'.$pair_member2_id.'","'.$pair_member2_name.'","'.$pair_member2_mobile.'","'.$pair_member3_id.'","'.$pair_member3_name.'","'.$pair_member3_mobile.'","'.$pair_member4_id.'","'.$pair_member4_name.'","'.$pair_member4_mobile.'","'.$carts_needed.'","'.$caddies_needed.'","'.$release_choice.'","'.$created_date.'"';
//$output .="\n";

		//define ('PDF_MARGIN_RIGHT', 4);
$pdf=new LongTableTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetPrintHeader(false);
		$pdf->SetPrintfooter(false);
		$pdf->SetTopMargin(0);
		//$pdf->SetLeftMargin(0);
//$pdf->SetFont('helvetica', '',5);
$pdf->AddPage('P',"A4");
$pdf->SetAutoPageBreak(false);
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

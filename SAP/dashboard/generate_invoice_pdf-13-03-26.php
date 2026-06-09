<?php
set_time_limit(0);
ini_set('memory_limit', '-1');


// -----------------------------------------------------------------------
// session_start MUST be first — before any output
// -----------------------------------------------------------------------
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
//echo"<pre>";print_r($_SESSION);die;


include "web_check.php";
include "star_connection.php";

require_once('tcpdf/tcpdf.php');

// -----------------------------------------------------------------------
// TCPDF subclass
// -----------------------------------------------------------------------
class LongTableTCPDF extends TCPDF {
    private $longTableHeader = '';
    private $longTableFooter = '';
    public function Header() { $this->writeHTML($this->longTableHeader); }
    public function Footer() { $this->writeHTML($this->longTableFooter); }
    public function setLongTableHeader($html) { $this->longTableHeader = $html; }
    public function setLongTableFooter($html)  { $this->longTableFooter = $html; }
}

// -----------------------------------------------------------------------
// Read GET params
// -----------------------------------------------------------------------
$mode           = isset($_GET['mode'])           ? trim($_GET['mode'])           : '';
$invoice_no     = isset($_GET['invoice_no'])     ? trim($_GET['invoice_no'])     : '';
$all_invoice_no = isset($_GET['all_invoice_no']) ? trim($_GET['all_invoice_no']) : '';

// -----------------------------------------------------------------------
// Helper: build one invoice HTML block
// -----------------------------------------------------------------------
function buildInvoiceHTML($inv, $inv_no){

    $InvoiceDt_format  = date('d/m/Y', strtotime($inv["InvoiceDt"]));
    $DoDate_format     = ($inv["DoDate"]    != '') ? date("d/m/Y", strtotime($inv["DoDate"]))    : '';
    $CustPoDt_format   = ($inv["CustPoDt"]  != '') ? date("d/m/Y", strtotime($inv["CustPoDt"]))  : '';
    $ChallanDt_format  = ($inv["ChallanDt"] != '') ? date("d/m/Y", strtotime($inv["ChallanDt"])) : '';
    $ShipmentDt_format = ($inv["ShipmentDt"]!= '') ? date("d/m/Y", strtotime($inv["ShipmentDt"])): '';
    $LrRrDt_format     = ($inv["LrRrDt"]    != '') ? date("d/m/Y", strtotime($inv["LrRrDt"]))    : '';

    $Qty         = round($inv["Qty"],      2);
    $Rate        = round($inv["Rate"],     2);
    $Tax         = round($inv["Tax"],      2);
    $FreightAmt  = round($inv["FreightAmt"],2);
    $ROff        = round($inv["ROff"],     2);
    if($ROff == 0) $ROff = '0.00';

    $Freight     = number_format(round($inv["Freight"],  2), 2);
    $Total       = number_format(round($inv["Total"],    2), 2);

    $Cgst        = $inv["Cgst"];
    $Cgststring  = ($Cgst == '') ? '' : $Cgst;
    $CgstAmt     = round($inv["CgstAmt"], 2);
    $CgstAmtstring = ($Cgst == '') ? '0.00' : $CgstAmt;

    $Sgst        = $inv["Sgst"];
    $Sgststring  = ($Sgst == '') ? '' : $Sgst;
    $SgstAmt     = round($inv["SgstAmt"], 2);
    $SgstAmtstring = ($Sgst == '') ? '0.00' : $SgstAmt;

    $Igst        = $inv["Igst"];
    $Igststring  = ($Igst == '') ? '' : $Igst;
    $IgstAmt     = round($inv["IgstAmt"], 2);
    $IgstAmtstring = ($Igst == '') ? '0.00' : $IgstAmt;

    $Tcs         = $inv["Tcs"];
    if($Tcs == '' || $Tcs == 0) $Tcs = '0.00';
    $TcsAmt      = round($inv["TcsAmt"], 2);
    $TcsAmtstring = ($TcsAmt == '') ? '0.00' : number_format($TcsAmt, 2);

    $CompName1   = $inv["CompName1"];
    $CompName2   = $inv["CompName2"];
    $CinNo       = $inv["CinNo"];
    $WorksOff    = $inv["WorksOff"];
    $PlantAdd    = $inv["PlantAdd"];
    $EwayBill    = $inv["EwayBill"];
    $Irn         = $inv["Irn"];
    $VendGstin   = $inv["VendGstin"];
    $VendPin     = $inv["VendPin"];
    $VendState   = $inv["VendState"];
    $VendStCode  = $inv["VendStCode"];
    $DoNo        = $inv["DoNo"];
    $CustPo      = $inv["CustPo"];
    $OurRefNo    = $inv["OurRefNo"];
    $ChallanNo   = $inv["ChallanNo"];
    $ShipmentNo  = $inv["ShipmentNo"];
    $CustName    = $inv["CustName"];
    $CustAdd     = $inv["CustAdd"];
    $CustPin     = $inv["CustPin"];
    $CustGstin   = $inv["CustGstin"];
    $CustSt      = $inv["CustSt"];
    $CustStCode  = $inv["CustStCode"];
    $ConName     = $inv["ConName"];
    $ConAdd      = $inv["ConAdd"];
    $WeekNo      = $inv["WeekNo"];
    $ConDest     = $inv["ConDest"];
    $ConPin      = $inv["ConPin"];
    $ConSt       = $inv["ConSt"];
    $ConStCode   = $inv["ConStCode"];
    $TotInWords  = $inv["TotInWords"];
    $TransMode   = $inv["TransMode"];
    $TransCode   = $inv["TransCode"];
    $Transporter = $inv["Transporter"];
    $LrRr        = $inv["LrRr"];
    $Inco1       = $inv["Inco1"];
    $Inco2       = $inv["Inco2"];
    $RouteCode   = $inv["RouteCode"];
    $RouteDesc   = $inv["RouteDesc"];
    $ForName     = $inv["ForName"];
    $JuriState   = $inv["JuriState"];
    $Description = $inv["Description"];
    $Hsn         = $inv["Hsn"];
    $PackageDesc = $inv["PackageDesc"];
    $TotPackage  = $inv["TotPackage"];
    $Uom         = $inv["Uom"];
    $VehicleNo   = $inv["VehicleNo"];
    $QrCode1   = $inv["QrCode1"]; 

    $base_url = defined('BASE_URL') ? BASE_URL : '';
    $downloaded_on = date('d-m-Y') . ' at ' . date('H:i:s');
$qr_path = $_SERVER['DOCUMENT_ROOT']."dashboard/qr_images/qr_20260313103859.jpg";
//$qr_path = BASE_URL."dashboard/qr_images/qr_20260313103859.jpg";
//$qr_path = BASE_URL."dashboard/images/qr.jpg";
    $html = '
<div class="container" style="width:100%;">
  <table style="width:100%;text-align:left;border:1px solid #333;" cellpadding="2">
    <tr>
      <td style="width:20%;text-align:left;">
        <img src="images/star-pdf-logo.jpeg" width="50px" style="position:absolute;top:10px;left:10px;" />
      </td>
      <td style="width:60%;text-align:center;">
        <p style="text-align:center;font-size:10px;margin:0px;padding-top:2px;"><strong>'.$CompName1.'</strong></p>
        <p style="text-align:center;font-size:8px;margin:0px;padding-top:2px;">'.$CinNo.'</p>
        <p style="text-align:center;font-size:8px;margin:0px;padding-top:2px;">'.$WorksOff.'</p>
        <p style="text-align:center;font-size:8px;padding-bottom:10px;"><strong>'.$CompName2.'</strong><br/></p>
      </td>
      <td style="width:20%;text-align:right;">
        <img src="'.$qr_path.'" alt="Star" width="50px" style="position:absolute;top:10px;right:10px;"/>
        
      </td>
    </tr>
  </table>

  <table style="width:100%;text-align:left;border:1px solid #333;" cellpadding="4">
    <tr>
      <td style="padding-left:10px;border-top:1px solid #333;" colspan="2">
        <p style="padding-left:10px;margin:0px;font-size:8px;"><strong>E-Way Bill No.: '.$EwayBill.'</strong></p>
      </td>
    </tr>
    <tr>
      <td style="padding-left:10px;border-top:1px solid #333;border-bottom:1px solid #333;" colspan="2">
        <p style="padding-left:10px;margin:0px;font-size:8px;"><strong>IRN No.: '.$Irn.'</strong></p>
      </td>
    </tr>
    <tr>
      <td style="width:50%;padding-left:10px;font-size:8px;">'.$PlantAdd.'</td>
      <td style="width:50%;font-size:8px;border-left:1px solid #333;padding-left:10px;">
        Invoice No.: <strong>'.$inv_no.'</strong><br/>
        Invoice Dt: <strong>'.$InvoiceDt_format.'</strong><br/>
        Cust PO No.: <strong>'.$CustPo.'</strong>
        <span style="padding-left:70px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cust PO Dt: <strong>'.$CustPoDt_format.'</strong></span>
      </td>
    </tr>
    <tr>
      <td style="width:50%;font-size:8px;border-top:1px solid #333;">
        <table style="width:100%;">
          <tr>
            <td style="width:50%;font-size:8px;">GSTIN: <strong>'.$VendGstin.'</strong></td>
            <td style="width:50%;font-size:8px;">PIN No.: <strong>'.$VendPin.'</strong></td>
          </tr>
          <tr>
            <td style="width:50%;font-size:8px;">State: <strong>'.$VendState.'</strong></td>
            <td style="width:50%;font-size:8px;">State Code: <strong>'.$VendStCode.'</strong></td>
          </tr>
          <tr>
            <td style="width:50%;font-size:8px;">SO No.: <strong>'.$DoNo.'</strong></td>
            <td style="width:50%;font-size:8px;">SO Dt: <strong>'.$DoDate_format.'</strong></td>
          </tr>
        </table>
      </td>
      <td style="width:50%;font-size:8px;border-left:1px solid #333;padding-left:10px;border-top:1px solid #333;">
        Our Ref No.: <strong>'.$OurRefNo.'</strong><br/>
        Delivery No.: <strong>'.$ChallanNo.'</strong>
        <span style="padding-left:90px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Delivery Dt: <strong>'.$ChallanDt_format.'</strong></span><br/>
        Shipment No.: <strong>'.$ShipmentNo.'</strong>
        <span style="padding-left:90px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Shipment Dt: <strong>'.$ShipmentDt_format.'</strong></span>
      </td>
    </tr>
    <tr>
      <td style="width:50%;padding-left:10px;border-top:1px solid #333;font-size:10px;">
        <strong>Name &amp; Address of the Customer (Billed To):</strong>
      </td>
      <td style="width:50%;padding-left:10px;border-top:1px solid #333;border-left:1px solid #333;font-size:10px;">
        <strong>Delivery Address of Consignee (Ship To):</strong>
      </td>
    </tr>
    <tr>
      <td style="width:50%;padding-left:10px;border-top:1px solid #333;font-size:8px;">
        '.$CustName.'<br/>
        <p style="font-size:8px;margin:0px;">'.$CustAdd.'</p>
        <table style="width:100%;">
          <tr>
            <td style="width:50%;font-size:8px;"><p style="font-size:8px;margin:0px;margin-top:10px;">GSTIN: '.$CustGstin.'</p></td>
            <td style="width:50%;font-size:8px;"><p style="font-size:8px;margin:0px;margin-top:10px;">PIN No.: '.$CustPin.'</p></td>
          </tr>
          <tr>
            <td style="width:50%;font-size:8px;"><p style="font-size:8px;margin:0px;">State: '.$CustSt.'</p></td>
            <td style="width:50%;font-size:8px;"><p style="font-size:8px;margin:0px;">State Code: '.$CustStCode.'</p></td>
          </tr>
        </table>
      </td>
      <td style="width:50%;padding-left:10px;border-top:1px solid #333;border-left:1px solid #333;font-size:8px;">
        '.$ConName.'<br/>
        <p style="font-size:8px;margin:0px;">'.$ConAdd.'</p>
        <p style="font-size:8px;margin:0px;margin-top:10px;">Batch No.: '.$WeekNo.'</p>
        <p style="font-size:8px;margin:0px;">Destination: '.$ConDest.'
          <span style="padding-left:70px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PIN No.: '.$ConPin.'</span>
        </p>
        <p style="font-size:8px;margin:0px;">State: '.$ConSt.'
          <span style="padding-left:70px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;State Code: '.$ConStCode.'</span>
        </p>
      </td>
    </tr>
  </table>

  <table style="width:100%;text-align:left;border:1px solid #333;margin:0px;" cellpadding="1">
    <tr>
      <td style="width:7%;padding-left:8px;text-align:center;font-size:8px;"><strong>SL NO.</strong></td>
      <td style="width:25%;padding-left:8px;border-left:1px solid #333;text-align:center;font-size:8px;"><strong>Description of Goods</strong></td>
      <td style="width:7%;padding-left:8px;border-left:1px solid #333;text-align:center;font-size:8px;"><strong>HSN</strong></td>
      <td style="width:17%;padding-left:8px;border-left:1px solid #333;text-align:center;font-size:8px;"><strong>Description of Package</strong></td>
      <td style="width:10%;padding-left:8px;border-left:1px solid #333;text-align:center;font-size:8px;"><strong>No. of Bags</strong></td>
      <td style="width:7%;padding-left:8px;border-left:1px solid #333;text-align:center;font-size:8px;"><strong>UOM</strong></td>
      <td style="width:7%;padding-left:8px;border-left:1px solid #333;text-align:center;font-size:8px;"><strong>QTY<br/>(MT)</strong></td>
      <td style="width:10%;padding-left:8px;border-left:1px solid #333;text-align:center;font-size:8px;"><strong>Basic Rate /MT<br/><span style="font-family:dejavusans;">&#8377;</span></strong></td>
      <td style="width:10%;padding-left:8px;border-left:1px solid #333;text-align:center;font-size:8px;"><strong>Taxable Amount<br/><span style="font-family:dejavusans;">&#8377;</span></strong></td>
    </tr>
    <tr>
      <td style="width:7%;padding-left:8px;text-align:center;border-top:1px solid #333;font-size:8px;">1</td>
      <td style="width:25%;padding-left:8px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;">'.$Description.'</td>
      <td style="width:7%;padding-left:8px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;">'.$Hsn.'</td>
      <td style="width:17%;padding-left:8px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;">'.$PackageDesc.'</td>
      <td style="width:10%;padding-left:8px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;">'.$TotPackage.'</td>
      <td style="width:7%;padding-left:8px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;">'.$Uom.'</td>
      <td style="width:7%;padding-left:8px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;">'.$Qty.'</td>
      <td style="width:10%;padding-left:8px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;">'.$Rate.'</td>
      <td style="width:10%;padding-left:8px;border-left:1px solid #333;text-align:center;border-top:1px solid #333;font-size:8px;text-align:right;">'.$Tax.'</td>
    </tr>

    <tr>
      <td colspan="6" rowspan="3" style="font-size:8px;padding-left:10px;border-top:1px solid #333;">
        &nbsp;&nbsp;<strong>Declaration</strong><br/>
        &nbsp;&nbsp;Verified that the particulars given above are true and correct and the amount indicated<br/>
        &nbsp;&nbsp;presents the price actually charged and that there is no flow of additional consideration<br/>
        &nbsp;&nbsp;directly or indirectly from the buyer.
      </td>
      <td colspan="2" style="padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px;">Freight:</td>
      <td style="padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;text-align:right;font-size:8px;">'.$Freight.'</td>
    </tr>
    <tr>
      <td colspan="2" style="padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px;">CGST: '.$Cgststring.'</td>
      <td style="padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;text-align:right;font-size:8px;">'.$CgstAmtstring.'</td>
    </tr>
    <tr>
      <td colspan="2" style="padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px;">SGST: '.$Sgststring.'</td>
      <td style="padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;text-align:right;font-size:8px;">'.$SgstAmtstring.'</td>
    </tr>

    <tr>
      <td colspan="6" rowspan="2" style="padding-left:10px;border-top:1px solid #333;font-size:8px;">
        Payment to be made electronically through RTGS/NEFT/IMPS &amp; also online UPI payment mode on our<br/>
        &nbsp;&nbsp;website www.starcement.co.in.
      </td>
      <td colspan="2" style="padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px;">IGST: '.$Igststring.'</td>
      <td style="padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;text-align:right;font-size:8px;">'.$IgstAmtstring.'</td>
    </tr>
    <tr>
      <td colspan="2" style="padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px;">TCS</td>
      <td style="padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;text-align:right;font-size:8px;">'.$TcsAmtstring.'</td>
    </tr>

    <tr>
      <td colspan="6" rowspan="2" style="padding-left:10px;border-top:1px solid #333;font-size:8px;">
        &nbsp;&nbsp;<strong>Amount in Words</strong><br/>&nbsp;'.$TotInWords.'<br/>
      </td>
      <td colspan="2" style="padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px;">ROFF:</td>
      <td style="padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;text-align:right;font-size:8px;">'.$ROff.'</td>
    </tr>
    <tr>
      <td colspan="2" style="padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;font-size:8px;"><strong>Total:</strong></td>
      <td style="padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;text-align:right;font-size:8px;"><strong>'.$Total.'</strong></td>
    </tr>

    <tr>
      <td colspan="2" style="padding-left:10px;border-top:1px solid #333;font-size:8px;">Mode Of Transport</td>
      <td colspan="4" style="padding-left:10px;border-top:1px solid #333;font-size:8px;"><strong>'.$TransMode.'</strong></td>
      <td colspan="3" style="padding-left:10px;border-left:1px solid #333;border-top:1px solid #333;"></td>
    </tr>
    <tr>
      <td colspan="2" style="padding-left:10px;border-top:1px solid #333;font-size:8px;">Transporter Code &amp; Name</td>
      <td colspan="4" style="padding-left:10px;border-top:1px solid #333;font-size:8px;">
        <strong>'.$TransCode.'</strong>
        <span style="padding-left:70px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>'.$Transporter.'</strong></span>
      </td>
      <td colspan="3" style="padding-left:10px;border-left:1px solid #333;"></td>
    </tr>
    <tr>
      <td colspan="2" style="padding-left:10px;border-top:1px solid #333;font-size:8px;">Vehicle Registration No</td>
      <td colspan="4" style="padding-left:10px;border-top:1px solid #333;font-size:8px;"><strong>'.$VehicleNo.'</strong></td>
      <td colspan="3" style="padding-left:10px;border-left:1px solid #333;text-align:center;font-size:8px;"><strong>E &amp; O E,</strong></td>
    </tr>
    <tr>
      <td colspan="2" style="padding-left:10px;border-top:1px solid #333;font-size:8px;">L.R/R.R No. &amp; Date</td>
      <td colspan="4" style="padding-left:10px;border-top:1px solid #333;font-size:8px;">
        <strong>'.$LrRr.'</strong>
        <span style="padding-left:70px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>'.$LrRrDt_format.'</strong></span>
      </td>
      <td colspan="3" style="padding-left:10px;border-left:1px solid #333;text-align:center;font-size:8px;"><strong>'.$ForName.'</strong></td>
    </tr>
    <tr>
      <td colspan="2" style="padding-left:10px;border-top:1px solid #333;font-size:8px;">Route Name</td>
      <td colspan="4" style="padding-left:10px;border-top:1px solid #333;font-size:8px;">
        <strong>'.$RouteCode.'</strong>
        <span style="padding-left:70px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>'.$RouteDesc.'</strong></span>
      </td>
      <td colspan="3" style="padding-left:10px;border-left:1px solid #333;text-align:center;"></td>
    </tr>
    <tr>
      <td colspan="2" style="padding-left:10px;border-top:1px solid #333;font-size:8px;">Incoterms</td>
      <td colspan="4" style="padding-left:10px;border-top:1px solid #333;font-size:8px;">
        <strong>'.$Inco1.'</strong>
        <span style="padding-left:70px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>'.$Inco2.'</strong></span>
      </td>
      <td colspan="3" style="padding-left:10px;border-left:1px solid #333;text-align:center;"></td>
    </tr>
    <tr>
      <td colspan="6" style="padding-left:10px;border-top:1px solid #333;font-size:8px;">&nbsp;<strong>Terms &amp; Conditions</strong></td>
      <td colspan="3" style="padding-left:10px;border-left:1px solid #333;text-align:center;font-size:8px;padding-top:10px;" valign="bottom"></td>
    </tr>
    <tr>
      <td colspan="6" style="padding-left:10px;font-size:8px;">
        &nbsp;&nbsp;*FOR TERMS &amp; CONDITIONS SEE OVERLEAF<br/>
        &nbsp;&nbsp;Amount of TAX Subject to Reverse Charge: NO
      </td>
      <td colspan="3" style="padding-left:10px;border-left:1px solid #333;text-align:center;font-size:8px;" valign="bottom">
        <strong>Authorised Signatory</strong>
      </td>
    </tr>
    <tr>
      <td colspan="9" style="padding-left:10px;border-top:1px solid #333;font-size:8px;text-align:center;">
        Guwahati Off: Mayur Garden, 2nd Floor, GS Road, Bhangarh, Guwahati Assam, 781005<br/>
        Kolkata Off: Century House, Star Cement Limited, p15/1, Taratala Road Kolkata, 700088<br/>
        SUBJECT TO '.$JuriState.' JURISDICTION<br/>
      </td>
    </tr>
  </table>

  <p style="padding-left:10px;margin:0px;font-size:10px;text-align:center;">
    Downloaded from Star Cement Customer Portal on '.$downloaded_on.'
  </p>
</div>';

    return $html;
}

// -----------------------------------------------------------------------
// Helper: create TCPDF instance and output PDF for download
// -----------------------------------------------------------------------
// function outputPDF($full_html, $filename, $auto_page_break){
//     $pdf = new LongTableTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//     $pdf->SetPrintHeader(false);
//     $pdf->SetPrintfooter(false);
//     $pdf->SetTopMargin(0);
//     $pdf->AddPage('P', 'A4');
//     $pdf->SetAutoPageBreak($auto_page_break, 0);
//     $pdf->writeHTML($full_html);
//     ob_end_clean();
//     $pdf->Output($filename, 'D');
//     exit;
// }
function outputPDF($full_html, $filename, $auto_page_break, $inv_data = null) {
    $pdf = new LongTableTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);
    $pdf->SetTopMargin(0);
    
    // Add first page with QR code using native Image()
    $pdf->AddPage('P', 'A4');
    
    // QR Code (top-right, 50x50px at 10px from edges)
    if ($inv_data && !empty($inv_data['QrCode1'])) {
        $qr_file = $_SERVER['DOCUMENT_ROOT'] . '/dashboard/qr_images/' . basename($inv_data['QrCode1']);
        if (!file_exists($qr_file)) {
            $qr_file = $_SERVER['DOCUMENT_ROOT'] . '/dashboard/images/qr.jpg';
        }
        if (file_exists($qr_file)) {
            $pdf->Image($qr_file, (210-50-10), 10, 50, 50, '', '', '', false, 300);
        }
    }
    
    $pdf->SetAutoPageBreak($auto_page_break, 0);
    $pdf->writeHTML($full_html);
    ob_end_clean();
    $pdf->Output($filename, 'D');
    exit;
}

// -----------------------------------------------------------------------
// ROUTE 1 — Single invoice
// -----------------------------------------------------------------------
if($invoice_no != ''){

    $session_key = 'invoice_print_array_' . $invoice_no;

    // Validate session data exists
    if(!isset($_SESSION[$session_key]) || !is_array($_SESSION[$session_key]) || count($_SESSION[$session_key]) == 0){
        die('<h3 style="color:red;font-family:sans-serif;">Error: Invoice data not found in session.<br/>
             Please <a href="javascript:history.back()">go back</a>, reload the invoice list page and try again.</h3>');
    }

    $output = '<html><body style="border:1px solid #333;">';

    foreach($_SESSION[$session_key] as $inv_row){
        $output .= buildInvoiceHTML($inv_row, $invoice_no);
    }

     $output .= '</body></html>';
//echo $output;die;
    $filename = 'invoice_' . $invoice_no . '_' . date('dmY_His') . '.pdf';
    outputPDF($output, $filename, false);
}

// -----------------------------------------------------------------------
// ROUTE 2 — All / bulk invoices
// -----------------------------------------------------------------------
if($mode == 'allinvoiceprint' && $all_invoice_no != ''){

    $ain_arr  = explode(',', $all_invoice_no);
    $tot_inv  = count($ain_arr);
    $cnt_inv  = 1;
    $output   = '<html><body style="border:1px solid #333;">';
    $found    = 0;

    foreach($ain_arr as $the_inv_no){
        $the_inv_no  = trim($the_inv_no);
        $session_key = 'invoice_print_array_' . $the_inv_no;

        if(!isset($_SESSION[$session_key]) || !is_array($_SESSION[$session_key]) || count($_SESSION[$session_key]) == 0){
            $cnt_inv++;
            continue; // skip missing
        }

        foreach($_SESSION[$session_key] as $inv_row){
            $output .= buildInvoiceHTML($inv_row, $the_inv_no);
            $found++;
        }

        if($cnt_inv < $tot_inv){
            $output .= '<span style="page-break-before:always;">&nbsp;</span>';
        }
        $cnt_inv++;
    }

    $output .= '</body></html>';

    if($found == 0){
        die('<h3 style="color:red;font-family:sans-serif;">Error: No invoice data found in session.<br/>
             Please <a href="javascript:history.back()">go back</a>, reload the invoice list page and try again.</h3>');
    }

    $filename = 'invoices_bulk_' . date('dmY_His') . '.pdf';
    outputPDF($output, $filename, true);
}

// -----------------------------------------------------------------------
// Fallback — bad request
// -----------------------------------------------------------------------
die('<h3 style="color:red;font-family:sans-serif;">Invalid request. Please <a href="javascript:history.back()">go back</a> to the invoice list.</h3>');
?>

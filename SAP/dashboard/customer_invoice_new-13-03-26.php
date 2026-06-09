<?php
set_time_limit(0);
ini_set('memory_limit', '-1');

// session_start MUST be before any output
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

include "web_check.php";
include "star_connection.php";

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

$t_apperpdo             = "T_APPERPDO";
$employee_master        = "employee_master";
$customer_master        = "customer_master";
$branch_master          = "branch_master";
$customer_invoice_table = "customer_invoice_table";
$curr_date              = date("m/d/Y");

function sort_by_date($a, $b) {
    $a = strtotime($a['InvoiceDt']);
    $b = strtotime($b['InvoiceDt']);
    if ($a == $b) { return 0; }
    return ($a < $b) ? -1 : 1;
}

ob_start();

$sswa_user_type              = $_SESSION["sswa_user_type"];
$sswa_selected_dealer_code   = $_SESSION["sswa_selected_dealer_code"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];

$sql1 = "INSERT INTO $customer_invoice_table(`customer_code`) VALUES ('$sswa_selected_customer_code')";
mysql_query($sql1);

$ledger_total_balance = 0;
$ledger_link          = "";

$sql3    = "select `dns_customer_code`,`customer_id`,customer_name from $customer_master where `customer_code`='$sswa_selected_customer_code'";
$res3    = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);

if($totres3 > 0){
    $row3            = mysql_fetch_assoc($res3);
    $the_dealer_id   = trim($row3["dns_customer_code"]);
    $the_customer_id = trim($row3["customer_id"]);
} else {
    $the_customer_id = "";
}

if(strtoupper($sswa_user_type) == 'DEALER'){
    $curr_date_time  = date("Y-m-d H:i:s");
    $webservice_name = "INVOICE";
    $sqlin_tl        = "insert into `webservice_track_log` (`customer_code`,`webservice_name`,`details`,`datetime`) values ('$the_customer_id','$webservice_name','','$curr_date_time')";
    $resin_tl        = mysql_query($sqlin_tl);
}

if($the_customer_id != ""){

    $from_dt = isset($_REQUEST['from_dt']) ? $_REQUEST['from_dt'] : '';
    $to_dt   = isset($_REQUEST['to_dt'])   ? $_REQUEST['to_dt']   : '';

    if($from_dt == '' && $to_dt == ''){
        $the_start_date = date('d/m/Y', strtotime("-7 days,$curr_date"));
        $the_end_date   = date('d/m/Y');
    } else {
        $the_start_date = date('d/m/Y', strtotime($from_dt));
        $the_end_date   = date('d/m/Y', strtotime($to_dt));
    }

    $add_page_name = "customer_ledger.php";
    $page_name     = "customer_ledger.php";
    $cnt           = 0;
    $countrow      = 1;

    include "web_header.php";

    $all_inv_arr = array();
    $curr_date   = date("Ymd");

    if($from_dt == '' && $to_dt == ''){
        $the_start_date_invoice = date('Ymd', strtotime("-7 days,$curr_date"));
        $the_end_date_invoice   = $curr_date;
    } else {
        $the_start_date_invoice = date('Ymd', strtotime($from_dt));
        $the_end_date_invoice   = date('Ymd', strtotime($to_dt));
    }

    $the_filter = '&$filter=(CustCo eq \''.$the_customer_id.'\' and ( InvoiceDt ge \''.$the_start_date_invoice.'\' and InvoiceDt le \''.$the_end_date_invoice.'\') )&sap-client=900';
    $url_ck1    = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/ZSD_CUSTOMER_BULK_INVOICE_SRV/ZSD_CUSTOMER_INVOICESet?$format=json'.str_replace(" ", "%20", $the_filter);

    $body_for_mcode10 = get_data_from_cserver($url_ck1);

    $results_arr_cnt = array();
    if(isJsonCk($body_for_mcode10)){
        $json_tmp = json_decode($body_for_mcode10, true);
        if(count($json_tmp) > 0 && array_key_exists("d", $json_tmp)){
            $results_arr_cnt = $json_tmp["d"]["results"];
        }
    }

    // -----------------------------------------------------------
    // invoice_print_master = plain associative array
    // key   => InvoiceNo  (string)
    // value => array of row(s) for that invoice
    // This replaces variable variables  ${invoice_print_array_.$InvoiceNo}
    // and is fully PHP 5.6 compatible
    // -----------------------------------------------------------
    $invoice_print_master   = array();
    $customer_invoice_array = array();

?>
<style>
thead, tbody { display: block; }
tbody { overflow-y: scroll; overflow-x: hidden; height: 500px; }
td, th { min-width: 160px; max-width: 160px; overflow: hidden; text-overflow: ellipsis; text-align:center !important; }
.well-lg  { padding:0px !important; }
.hide_narr { display:none; }
</style>

<form action="" method="post" name="searchform">
<section class="content">
    <div class="container-fluid">
        <div class="row clearfix">
        <div class="card">
        <div class="header">

            <h3 style="text-align:center">Invoice List</h3>

            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
                <span style="text-align:center">From Date</span>
                <input type="date" class="form-control" id="from_dt" name="from_dt"
                       value="<?php echo $from_dt; ?>" placeholder="Choose from date" min="2022-08-01">
            </div>

            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
                <span style="text-align:center">To Date</span>
                <input type="date" class="form-control" id="to_dt" name="to_dt"
                       value="<?php echo $to_dt; ?>" placeholder="Choose to date">
            </div>

            <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
                <span style="text-align:center;display:block;">&nbsp;</span>
                <button type="submit" class="btn bg-red waves-effect srch_btn">Search</button>
            </div>

            <?php if(count($results_arr_cnt) <= 40){ ?>
            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
                <span style="text-align:center;display:block;">&nbsp;</span>
                <a href="javascript:void(0);" id="dai_btn" class="btn bg-red waves-effect">Download All Invoice</a>
            </div>
            <?php } ?>

            <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
                <span style="text-align:center;display:block;">&nbsp;</span>
                <a href="export_customer_invoice.php?the_start_date=<?php echo $from_dt; ?>&the_end_date=<?php echo $to_dt; ?>&customer_code=<?php echo $sswa_selected_customer_code; ?>"
                   class="btn bg-red waves-effe">Export</a>
            </div>

        </div><!-- /.header -->
        <br /><br />

        <?php if(count($results_arr_cnt) > 40){ ?>
            <h3 style="text-align:center">Please reduce date range for bulk invoice download.</h3>
        <?php } ?>

        <br />
        <h3 style="text-align:center">Period From : <?php echo $the_start_date; ?> &nbsp; To : <?php echo $the_end_date; ?></h3>

        </form><!-- end searchform -->

        <form action="" method="post" name="invoiceform">
        <input type="hidden" name="from_dt" value="<?php echo $from_dt; ?>" />
        <input type="hidden" name="to_dt"   value="<?php echo $to_dt; ?>" />

        <div class="table-responsive">
        <table class="table table-bordered">
        <thead>
            <tr>
                <th>Invoice date</th>
                <th>Invoice no.</th>
                <th>Delivery no.</th>
                <th>Sale Order No.</th>
                <th>App Order No.</th>
                <th>Product Name</th>
                <th>Invoice Qty. (MT)</th>
                <th>Destination</th>
                <th>Truck No.</th>
            </tr>
        </thead>
        <tbody>

<?php

    // -----------------------------------------------------------------------
    // Parse API response
    // -----------------------------------------------------------------------
    if(isJsonCk($body_for_mcode10)){
        $json_decoded21 = json_decode($body_for_mcode10, true);
        if(count($json_decoded21) > 0 && array_key_exists("d", $json_decoded21)){
            $app_results_arr = $json_decoded21["d"]["results"];

            if(count($app_results_arr) > 0){
                foreach($app_results_arr as $app_results_aval){

                    $InvoiceNo = $app_results_aval["InvoiceNo"];
                    if($InvoiceNo != ""){
                        $all_inv_arr[] = $InvoiceNo;
                    }

                    $InvoiceDt_format = date("Y-m-d", strtotime($app_results_aval["InvoiceDt"]));

                    // Build flat row array
                    $row_data = array(
                        "InvoiceNo"    => $InvoiceNo,
                        "InvoiceDt"    => $InvoiceDt_format,
                        "ChallanNo"    => $app_results_aval["ChallanNo"],
                        "WorksOff"     => $app_results_aval["WorksOff"],
                        "Description"  => $app_results_aval["Description"],
                        "Qty"          => $app_results_aval["Qty"],
                        "ConDest"      => $app_results_aval["ConDest"],
                        "ConName"      => $app_results_aval["ConName"],
                        "ConAdd"       => $app_results_aval["ConAdd"],
                        "VehicleNo"    => $app_results_aval["VehicleNo"],
                        "CompName1"    => $app_results_aval["CompName1"],
                        "CompName2"    => $app_results_aval["CompName2"],
                        "Plant"        => $app_results_aval["Plant"],
                        "CinNo"        => $app_results_aval["CinNo"],
                        "PlantAdd"     => $app_results_aval["PlantAdd"],
                        "EwayBill"     => $app_results_aval["EwayBill"],
                        "Irn"          => $app_results_aval["Irn"],
                        "VendLocation" => $app_results_aval["VendLocation"],
                        "VendAddress"  => $app_results_aval["VendAddress"],
                        "VendGstin"    => $app_results_aval["VendGstin"],
                        "VendPin"      => $app_results_aval["VendPin"],
                        "VendStCode"   => $app_results_aval["VendStCode"],
                        "VendState"    => $app_results_aval["VendState"],
                        "DoNo"         => $app_results_aval["DoNo"],
                        "DoDate"       => $app_results_aval["DoDate"],
                        "CustPo"       => $app_results_aval["CustPo"],
                        "CustPoDt"     => $app_results_aval["CustPoDt"],
                        "OurRefNo"     => $app_results_aval["OurRefNo"],
                        "ChallanDt"    => $app_results_aval["ChallanDt"],
                        "ShipmentNo"   => $app_results_aval["ShipmentNo"],
                        "ShipmentDt"   => $app_results_aval["ShipmentDt"],
                        "CustName"     => $app_results_aval["CustName"],
                        "CustCo"       => $app_results_aval["CustCo"],
                        "CustAdd"      => $app_results_aval["CustAdd"],
                        "CustPin"      => $app_results_aval["CustPin"],
                        "CustGstin"    => $app_results_aval["CustGstin"],
                        "CustSt"       => $app_results_aval["CustSt"],
                        "CustStCode"   => $app_results_aval["CustStCode"],
                        "ConCo"        => $app_results_aval["ConCo"],
                        "WeekNo"       => $app_results_aval["WeekNo"],
                        "ConPin"       => $app_results_aval["ConPin"],
                        "ConGstin"     => $app_results_aval["ConGstin"],
                        "ConStCode"    => $app_results_aval["ConStCode"],
                        "ConSt"        => $app_results_aval["ConSt"],
                        "Freight"      => $app_results_aval["Freight"],
                        "Cgst"         => $app_results_aval["Cgst"],
                        "CgstAmt"      => $app_results_aval["CgstAmt"],
                        "Sgst"         => $app_results_aval["Sgst"],
                        "SgstAmt"      => $app_results_aval["SgstAmt"],
                        "Igst"         => $app_results_aval["Igst"],
                        "IgstAmt"      => $app_results_aval["IgstAmt"],
                        "Tcs"          => $app_results_aval["Tcs"],
                        "TcsAmt"       => $app_results_aval["TcsAmt"],
                        "Cess"         => $app_results_aval["Cess"],
                        "CessAmt"      => $app_results_aval["CessAmt"],
                        "ROff"         => $app_results_aval["ROff"],
                        "Total"        => $app_results_aval["Total"],
                        "TotInWords"   => $app_results_aval["TotInWords"],
                        "TransMode"    => $app_results_aval["TransMode"],
                        "TransCode"    => $app_results_aval["TransCode"],
                        "Transporter"  => $app_results_aval["Transporter"],
                        "LrRr"         => $app_results_aval["LrRr"],
                        "LrRrDt"       => $app_results_aval["LrRrDt"],
                        "Inco1"        => $app_results_aval["Inco1"],
                        "Inco2"        => $app_results_aval["Inco2"],
                        "RouteCode"    => $app_results_aval["RouteCode"],
                        "RouteDesc"    => $app_results_aval["RouteDesc"],
                        "ForName"      => $app_results_aval["ForName"],
                        "OffAdd1"      => $app_results_aval["OffAdd1"],
                        "OffAdd2"      => $app_results_aval["OffAdd2"],
                        "Juri"         => $app_results_aval["Juri"],
                        "CurrKey"      => $app_results_aval["CurrKey"],
                        "UomHead"      => $app_results_aval["UomHead"],
                        "JuriState"    => $app_results_aval["JuriState"],
                        "Vkgrp"        => $app_results_aval["Vkgrp"],
                        "SlNo"         => $app_results_aval["SlNo"],
                        "Hsn"          => $app_results_aval["Hsn"],
                        "PackageDesc"  => $app_results_aval["PackageDesc"],
                        "TotPackage"   => $app_results_aval["TotPackage"],
                        "Uom"          => $app_results_aval["Uom"],
                        "Rate"         => $app_results_aval["Rate"],
                        "Tax"          => $app_results_aval["Tax"],
                        "FreightAmt"   => $app_results_aval["FreightAmt"],
                        "QrCode1"      => $app_results_aval["QrCode1"]
                    );

                    $customer_invoice_array[] = $row_data;

                } // end foreach API results
            } // end if count app_results_arr
        }
    }

    // -----------------------------------------------------------------------
    // Sort + render table rows + populate invoice_print_master
    // -----------------------------------------------------------------------
    if(count($customer_invoice_array) > 0){

        usort($customer_invoice_array, 'sort_by_date');

        foreach($customer_invoice_array as $inv_data){

            $InvoiceNo        = $inv_data["InvoiceNo"];
            $InvoiceDt_format = date("d-m-Y", strtotime($inv_data["InvoiceDt"]));
            $ChallanNo        = $inv_data["ChallanNo"];
            $DoNo             = $inv_data["DoNo"];
            $CustPo           = $inv_data["CustPo"];
            $Description      = $inv_data["Description"];
            $Qty              = $inv_data["Qty"];
            $ConDest          = $inv_data["ConDest"];
            $VehicleNo        = $inv_data["VehicleNo"];

            // ----------------------------------------------------------
            // Populate invoice_print_master  (PHP 5.6 safe plain array)
            // ----------------------------------------------------------
            if(!array_key_exists($InvoiceNo, $invoice_print_master)){
                $invoice_print_master[$InvoiceNo] = array();
            }
            // Store the formatted date version for PDF use
            $inv_data_for_pdf               = $inv_data;
            $inv_data_for_pdf["InvoiceDt"]  = $InvoiceDt_format;
            $invoice_print_master[$InvoiceNo][] = $inv_data_for_pdf;
?>
            <tr>
                <td><?php echo $InvoiceDt_format; ?></td>
                <td>
                    <a href="generate_invoice_pdf.php?invoice_no=<?php echo urlencode($InvoiceNo); ?>" target="_blank">
                        <?php echo $InvoiceNo; ?>
                    </a>
                </td>
                <td><?php echo $ChallanNo; ?></td>
                <td><?php echo $DoNo; ?></td>
                <td><?php echo $CustPo; ?></td>
                <td><?php echo $Description; ?></td>
                <td><?php echo number_format($Qty, 2); ?></td>
                <td><?php echo $ConDest; ?></td>
                <td><?php echo $VehicleNo; ?></td>
            </tr>
<?php
        } // end foreach sorted

    } else {
?>
        <tr>
            <td colspan="9" align="center">No record found</td>
        </tr>
<?php
    }

    // -----------------------------------------------------------------------
    // SAVE TO SESSION  — PHP 5.6 safe
    // Clear old keys first, then save fresh data
    // -----------------------------------------------------------------------
    $keys_to_delete = array();
    foreach($_SESSION as $skey => $sval){
        if(strpos($skey, 'invoice_print_array_') === 0){
            $keys_to_delete[] = $skey;
        }
    }
    foreach($keys_to_delete as $del_key){
        unset($_SESSION[$del_key]);
    }

    foreach($invoice_print_master as $inv_no => $inv_rows){
        $_SESSION['invoice_print_array_' . $inv_no] = $inv_rows;
    }
    $_SESSION['all_inv_arr'] = $all_inv_arr;
    // -----------------------------------------------------------------------

?>
        </tbody>
        </table>
        </div>
        </form>

        </div><!-- /.card -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</section>

<script type="text/javascript">
jQuery(function(){

    var all_inv_ids = "<?php echo implode(',', $all_inv_arr); ?>";

    jQuery("#dai_btn").click(function(){
        if(all_inv_ids == ""){
            alert("No invoice record found.");
        } else {
            window.open('generate_invoice_pdf.php?mode=allinvoiceprint&all_invoice_no=' + encodeURIComponent(all_inv_ids), '_blank');
        }
    });

});
</script>

<?php
    include "web_footer.php";
    mysql_close();

} // end if($the_customer_id != "")
?>

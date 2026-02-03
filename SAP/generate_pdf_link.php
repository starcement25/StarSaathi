<?php
// Include necessary files and functions here
ob_start();

include "web_check.php";
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$employee_master = "employee_master";
$customer_master = "customer_master";
$branch_master = "branch_master";
$customer_invoice_table="customer_invoice_table";

// $sql1="select distinct `customer_code` from $customer_invoice_table";
// // echo $sql1;
// $query1=mysql_query($sql1);
// $data=array();
// while($result=mysql_fetch_array($query1)){
    $sswa_selected_customer_code=$_REQUEST["customer_code"] ? addslashes(trim($_REQUEST["customer_code"])) : "";

    $sql3 = "select `dns_customer_code`,`customer_id`,`customer_name` from $customer_master where `customer_code`='$sswa_selected_customer_code'";
    $res3 = mysql_query($sql3);
    $totres3 = mysql_num_rows($res3);
    if($totres3>0){
    $row3 = mysql_fetch_assoc($res3);
    $the_dealer_id = trim($row3["dns_customer_code"]);
    $the_customer_id = trim($row3["customer_id"]);
    }else{
    $the_customer_id = "";
    }

// echo $sswa_selected_customer_code;
$ledger_total_balance = 0;
$ledger_link = "";

$from_dt=$_REQUEST["from_date"] ? addslashes(trim($_REQUEST["from_date"])) : "";
// echo $from_dt;
$to_dt=$_REQUEST["to_date"] ? addslashes(trim($_REQUEST["to_date"])) : "";
// echo $to_dt;
$the_start_date_invoice=date('Ymd', strtotime($from_dt));
// echo $the_start_date_invoice;
$the_end_date_invoice=date('Ymd', strtotime($to_dt));
// echo $the_end_date_invoice;
//$the_filter2 = '&$filter=(Kunnr eq \''.$the_customer_id.'\' and DocDate eq datetime\''.$the_date_time.'\')';
// echo $the_customer_id;

$the_filter = '&$filter=(CustCo eq \''.$the_customer_id.'\' and ( InvoiceDt ge \''.$the_start_date_invoice.'\' and InvoiceDt le \''.$the_end_date_invoice.'\') )&sap-client=900';
// Define the URL to fetch JSON data
$url_ck1 = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/ZSD_CUSTOMER_BULK_INVOICE_SRV/ZSD_CUSTOMER_INVOICESet?$format=json'.str_replace(" ","%20",$the_filter);

// Generate PDF content using a PDF library like TCPDF or wkhtmltopdf
// Replace the following code with your PDF generation logic

$pdf_content = '<!DOCTYPE html>
<html>
<head>
    <title>Generated PDF</title>
</head>
<body>
    <h1>Your PDF Content Goes Here</h1>
    <!-- Replace this with the content you want to include in the PDF -->
</body>
</html>';

// Set the HTTP headers for PDF download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="generated_pdf.pdf"');

// Output the PDF content
echo $pdf_content;

?>

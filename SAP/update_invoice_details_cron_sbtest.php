<?php
$curr_date = date("Ymd");
$the_start_date_invoice = date('Ymd', strtotime("-4 days,$curr_date"));
$the_customer_id = '1000001497'; // fallback

$the_end_date_invoice = $curr_date;
$the_filter = '&$filter=(CustCo eq \''.$the_customer_id.'\' and ( InvoiceDt ge \''.$the_start_date_invoice.'\' and InvoiceDt le \''.$the_end_date_invoice.'\') )&sap-client=900';
$url_ck1 = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/ZSD_CUSTOMER_BULK_INVOICE_SRV/ZSD_CUSTOMER_INVOICESet?$format=json' . str_replace(" ","%20",$the_filter);
echo $url_ck1;

$body_for_mcode10 = get_data_from_cserver($url_ck1);
echo $body_for_mcode10;

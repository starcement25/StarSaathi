<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// --- 2nd DB Connection (Remote/Live) ---
$remote_host = "103.87.174.95"; // or your remote host
$remote_user = "starsaat_dnsprod";
$remote_pass = "dnsprod1234#";
$remote_db   = "starsaathi_STARS";

$conn = mysql_connect($remote_host, $remote_user, $remote_pass, true); // 'true' for new link
if (!$conn) {
    die("Remote DB Connection failed: " . mysql_error());
}
mysql_select_db($remote_db, $conn);

//$live_t_dochallan = "SB_T_APPERPDO";
//$live_zorderdetails = "SB_zorderdetailsp";
$live_t_dochallan = "T_APPERPDO";
$live_zorderdetails = "zorderdetailsp";
// Get JSON data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data && isset($data['main_order']) && isset($data['sap_order'])) {
   

    // Escape and insert into first table
    $main = array_map(function($v) use ($conn) { return mysql_real_escape_string($v, $conn); }, $data['main_order']);

    $sql1 = "INSERT INTO $live_t_dochallan
    (`APPORDERNO`,`ERPORDERNO`,`ERPORDERDT`,`order_date`,`order_for`,`consignee_name`,`consignee_address`,`sub_dealer_code`,`dns_sub_dealer_code`,`customer_code`,`dns_customer_code`,`prod_code`,`dns_prod_code`,`prod_display_name`,`QTY`,`freight`,`destination_code`,`destination_name`,`destination_address`,`phone_no`,`dump_status`,`dump_code`,`dump_name`,`order_from`,`order_by`,`dealer_truck`)
    VALUES (
        '{$main['APPORDERNO']}','{$main['ERPORDERNO']}','{$main['ERPORDERDT']}','{$main['order_date']}','{$main['order_for']}',
        '{$main['consignee_name']}','{$main['consignee_address']}','{$main['sub_dealer_code']}','{$main['dns_sub_dealer_code']}',
        '{$main['customer_code']}','{$main['dns_customer_code']}','{$main['prod_code']}','{$main['dns_prod_code']}',
        '{$main['prod_display_name']}','{$main['QTY']}','{$main['freight']}','{$main['destination_code']}','{$main['destination_name']}',
        '{$main['destination_address']}','{$main['phone_no']}','{$main['dump_status']}','{$main['dump_code']}','{$main['dump_name']}',
        '{$main['order_from']}','{$main['order_by']}','{$main['dealer_truck']}'
    )";

    $ok1 = mysql_query($sql1, $conn);

    // Escape and insert into second table
    $sap = array_map(function($v) use ($conn) { return mysql_real_escape_string($v, $conn); }, $data['sap_order']);

    $sql2 = "INSERT INTO $live_zorderdetails
    (`APPORDERNO`,`DATE`,`time`,`Cust_Code`,`Consignee_Code`,`Freight`,`DestinationCode`,`ProductCode`,`Qty`,`Unit`,`PLANT`,`OrderNo`,`Remarks`,`Delivery_point`,`Remarks_text`,`Additional_data_1`)
    VALUES (
        '{$sap['APPORDERNO']}','{$sap['DATE']}','{$sap['time']}','{$sap['Cust_Code']}','{$sap['Consignee_Code']}',
        '{$sap['Freight']}','{$sap['DestinationCode']}','{$sap['ProductCode']}','{$sap['Qty']}','{$sap['Unit']}',
        '{$sap['PLANT']}','{$sap['OrderNo']}','{$sap['Remarks']}','{$sap['Delivery_point']}','{$sap['Remarks_text']}','{$sap['Additional_data_1']}'
    )";

    $ok2 = mysql_query($sql2, $conn);

    if ($ok1 && $ok2) {
        echo "Both inserts successful";
    } else {
        echo "Error: ".mysql_error($conn);
    }
} else {
    echo "Invalid data received";
}
?>
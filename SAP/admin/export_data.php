<?php
include "web_check.php";
include "star_connection.php";
$pop_order="pop_order";
$pop_product_master="pop_product_master";
$customer_master="customer_master";
$branch_master="branch_master";
$t_order_pop="T_ORDER_POP";
$product_master="product_master";
$broker_master="broker_master";
$lifting="lifting";

function accent2ascii($str)
{
    $charset = 'UTF-8';
	$str = htmlentities($str, ENT_NOQUOTES, $charset);

    //$str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '', $str);
    //$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
    //$str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères

    return $str;
}

$filename = "exported_data.csv";
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=$filename");

// Open the output stream
$output = fopen("php://output", "w");

// Add headers to the CSV file
$headers = array(
    "DATE & TIME",
    "MAIN ORDER DETAILS",
    "ORDER ID",
    "CUSTOMER CODE",
    "CUSTOMER NAME",
    "LINKED DEALER CODE",
    "LINKED DEALER NAME",
    "BRANCH",
    "PRODUCT NAME",
    "QTY",
    "PRICE (PER PCS)",
    "GST (%)",
    "NET AMOUNT",
    "GST AMOUNT",
    "TOTAL AMOUNT",
    "ADDRESS",
    "PIN CODE",
    "REMARKS",
    "ADDRESS & PIN CODE TO BE PRINTED",
    "CONTACT NO. TO BE PRINTED",
    "STATUS",
    "ADMIN REMARKS"
);

fputcsv($output, $headers);

$new_price_format_datetime = "2023-09-09 00:00:00";
$new_price_format_datetime_ts = strtotime($new_price_format_datetime);

$sql1 = "select * from $t_order_pop order by `order_date` desc";

$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
    while($row1=mysql_fetch_array($res1)){
    $main_order_id = $row1["the_order_id"] ? trim($row1["the_order_id"]) : "";
    $order_id = $row1["APPORDERNO"] ? trim($row1["APPORDERNO"]) : "";
    $order_date = $row1["order_date"] ? trim($row1["order_date"]) : "";
    $formatted_date=date("Y-m-d", strtotime($order_date));
	
	$order_date_time = $order_date;
	$order_date_time_ts = strtotime($order_date_time);
	
    // echo $formatted_date;
    $customer_code = $row1["customer_code"] ? trim($row1["customer_code"]) : "";
    $printed_address_pin = $row1["printed_address_pin"] ? trim($row1["printed_address_pin"]) : "";
    $printed_address_pin=accent2ascii($row1["printed_address_pin"]);
    $contact_num_printed = $row1["contact_num_printed"] ? trim($row1["contact_num_printed"]) : "";
    $order_status= $row1["order_status"];
    $admin_remarks = $row1["admin_remark"];
    $sql_customer="select * from $customer_master where `customer_code`='$customer_code'";
    $query_customer=mysql_query($sql_customer);
    $result_customer=mysql_fetch_array($query_customer);
    
    $real_customer_code=$result_customer["customer_id"] ? trim($result_customer["customer_id"]) : "";
    // echo $real_customer_code."<br/>";
    $real_customer_name=$result_customer["customer_name"] ? trim($result_customer["customer_name"]) : "";
    // echo $real_customer_name."<br/>";
    $branch_code = $row1["branch_code"] ? trim($row1["branch_code"]) : "";
    $address = $row1["address"] ? trim($row1["address"]) : "";
    // $address=accent2ascii($row1["address"]);
    $pincode = $row1["pin"] ? trim($row1["pin"]) : "";
    $status = $row1["status"] ? trim($row1["status"]) : "";
    $the_tracking_id = $row1["the_tracking_id"] ? trim($row1["the_tracking_id"]) : "";

    $qty = $row1["QTY"] ? trim($row1["QTY"]) : "";
    $remarks = $row1["remarks"] ? trim($row1["remarks"]) : "";
    $prod_name= $row1["prod_display_name"] ? trim($row1["prod_display_name"]) : "";
    $dns_prod_code= $row1["dns_prod_code"] ? trim($row1["dns_prod_code"]) : "";

    $sql2="select * from $customer_master where `customer_code`='$customer_code'";
    // echo $sql2;
    $res2=mysql_query($sql2);
    $customer=mysql_fetch_array($res2);
    $rds_tag=$customer['rds_tag'];
    $branch_code=$customer['branch_code'];

    $sql3="select `branch_name` from $branch_master where `branch_code`='$branch_code'";
    // echo $sql3;
    $res3=mysql_query($sql3);
    $branch=mysql_fetch_array($res3);
    $branch_name=$branch['branch_name'];

    $sql5="select * from $customer_master where `customer_code`='$rds_tag'";
    // echo $sql3;
    $res5=mysql_query($sql5);
    $all_result2=mysql_fetch_array($res5);
    $dealer_code=$all_result2['customer_id'];
    $dealer_name=$all_result2['customer_name'];
	
if($order_date_time_ts>$new_price_format_datetime_ts)	{
$price_per_piece = $row1["prod_rate"] ? trim($row1["prod_rate"]) : 0;
$gst_rate = $row1["gst_rate"] ? trim($row1["gst_rate"]) : 0;
$gst_amount = $row1["gst_amount"] ? trim($row1["gst_amount"]) : 0;
$net_amount = $row1["prod_amount"] ? trim($row1["prod_amount"]) : 0;
$total_amount = $row1["prod_total_amount"] ? trim($row1["prod_total_amount"]) : 0;
// change Pending -> Pending Payment
if($status=="Pending"){
    $status="Payment Pending";}
$main_order_details=$main_order_id.". Payment Status: ".$status.". TransactionID: ".$the_tracking_id;


}else{
$sql4="select * from $pop_product_master where `dns_prod_code`='$dns_prod_code'";
$res4=mysql_query($sql4);
$all_result=mysql_fetch_array($res4);
$price_per_piece=$all_result['price_per_piece'];
$gst_rate=$all_result['GST_rate'];
$gst_amount=$qty*$all_result['price_per_piece']*$all_result['GST_rate']/100;
// echo $gst_amount."<br/>";
$total_amount=($qty*$all_result['price_per_piece'])+$gst_amount;
// echo $total_amount."<br/>";
$net_amount=$qty*$all_result['price_per_piece'];	
	
// $main_order_details="";

}
	
   $data = array(
    $order_date_time,
    $main_order_details,
    $order_id,
    $real_customer_code,
    $real_customer_name,
    $dealer_code,
    $dealer_name,
    $branch_name,
    $prod_name,
    $qty,
    $price_per_piece,
    $gst_rate,
    $net_amount,
    $gst_amount,
    $total_amount,
    $address,
    $pincode,
    $remarks,
    $printed_address_pin,
    $contact_num_printed,
    $order_status,
    $admin_remarks
    );
    
    fputcsv($output, $data);
}

// Close the output stream
fclose($output);
}
// Close the database connection if needed
mysql_close();

?>

<?php
include "star_connection.php";
include "function-sfa.php";

/* =========================================================
   SUPPORT BOTH JSON AND FORM-DATA INPUT
========================================================= */
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

if (stripos($contentType, 'application/json') !== false) {
    $json_data = file_get_contents("php://input");
    $decoded_data = json_decode($json_data, true);
    if (is_array($decoded_data)) {
        $_REQUEST = array_merge($_REQUEST, $decoded_data);
    }
}

/* =========================================================
   TABLE NAMES
========================================================= */
$t_main_order_pop = "T_MAIN_ORDER_POP";
$t_apperpdo_pop   = "T_ORDER_POP";

/* =========================================================
   SAFE INPUT FETCH
========================================================= */
$customer_code = isset($_REQUEST["customer_code"]) ? addslashes(trim($_REQUEST["customer_code"])) : "";
$user_type     = isset($_REQUEST["user_type"]) ? strtoupper(trim($_REQUEST["user_type"])) : "";
$payment_by    = isset($_REQUEST["payment_by"]) ? addslashes(trim($_REQUEST["payment_by"])) : "";

$ord_dns_customer_code   = isset($_REQUEST["dns_customer_code"]) ? addslashes(trim($_REQUEST["dns_customer_code"])) : "";
$ord_address             = isset($_REQUEST["address"]) ? addslashes(trim($_REQUEST["address"])) : "";
$ord_pin                 = isset($_REQUEST["pin"]) ? addslashes(trim($_REQUEST["pin"])) : "";
$ord_remarks             = isset($_REQUEST["remarks"]) ? addslashes(trim($_REQUEST["remarks"])) : "";
$ord_printed_address_pin = isset($_REQUEST["printed_address_pin"]) ? addslashes(trim($_REQUEST["printed_address_pin"])) : "";
$ord_contact_num_printed = isset($_REQUEST["contact_num_printed"]) ? addslashes(trim($_REQUEST["contact_num_printed"])) : "";

$order_date_time = date("Y-m-d H:i:s");

$order_data = (isset($_REQUEST["order_data"]) && is_array($_REQUEST["order_data"]))
                ? $_REQUEST["order_data"]
                : array();

$res_data = array();

/* =========================================================
   VALIDATION
========================================================= */
if ($payment_by == "") {
    $res_data = array("process_status"=>"NO","process_message"=>"Please choose payment by option.");
    echo json_encode($res_data);
    exit;
}

if ($customer_code == "") {
    $res_data = array("process_status"=>"NO","process_message"=>"Customer code missing.");
    echo json_encode($res_data);
    exit;
}

if (count($order_data) == 0) {
    $res_data = array("process_status"=>"NO","process_message"=>"Please select product then make order.");
    echo json_encode($res_data);
    exit;
}

/* =========================================================
   FETCH CUSTOMER DETAILS
========================================================= */
function show_customer_data_by_customer_code($the_customer_code){
    $customer_data = array("sts"=>"NO");
    $sql1 = "SELECT dns_customer_code,customer_id,customer_name,phone_no,email,region 
             FROM customer_master 
             WHERE customer_code='$the_customer_code'";
    $res1 = mysql_query($sql1);
    if(mysql_num_rows($res1)>0){
        $row1 = mysql_fetch_assoc($res1);
        $customer_data = array(
            "sts"=>"YES",
            "dns_customer_code"=>trim($row1["dns_customer_code"]),
            "customer_id"=>trim($row1["customer_id"]),
            "customer_name"=>trim($row1["customer_name"]),
            "phone_no"=>trim($row1["phone_no"]),
            "email"=>trim($row1["email"]),
            "region"=>trim($row1["region"])
        );
    }
    return $customer_data;
}

$customer_data_arr = show_customer_data_by_customer_code($customer_code);

if ($customer_data_arr["sts"] != "YES") {
    $res_data = array("process_status"=>"NO","process_message"=>"Invalid customer.");
    echo json_encode($res_data);
    exit;
}

$ord_dns_customer_code = $customer_data_arr["dns_customer_code"];
$ord_customer_sap_code = $customer_data_arr["customer_id"];
$ord_customer_name     = $customer_data_arr["customer_name"];
$ord_phone_no          = $customer_data_arr["phone_no"];
$ord_email             = $customer_data_arr["email"];
$ord_region            = $customer_data_arr["region"];

/* =========================================================
   INSERT MAIN ORDER
========================================================= */
$sql_main = "INSERT INTO $t_main_order_pop 
(dns_customer_code,customer_code,customer_sap_code,customer_name,
customer_region,mobile,email,address,pin,remarks,
printed_address_pin,contact_num_printed,payment_by,order_datetime) 
VALUES 
('$ord_dns_customer_code','$customer_code','$ord_customer_sap_code',
'$ord_customer_name','$ord_region','$ord_phone_no','$ord_email',
'$ord_address','$ord_pin','$ord_remarks','$ord_printed_address_pin',
'$ord_contact_num_printed','$payment_by','$order_date_time')";

$res_main = mysql_query($sql_main);

if(!$res_main){
    echo json_encode(array("process_status"=>"NO","process_message"=>"Failed to save order."));
    exit;
}

$the_order_id = mysql_insert_id();
$ord_total_amount = 0;

/* =========================================================
   PRODUCT PRICE FUNCTION
========================================================= */
function get_product_price($dns_prod_code){
    $product_data = array("sts"=>"NO");
    $sql = "SELECT price_per_piece,GST_rate 
            FROM pop_product_master 
            WHERE status='Y' AND dns_prod_code='$dns_prod_code'";
    $res = mysql_query($sql);
    if(mysql_num_rows($res)>0){
        $row = mysql_fetch_assoc($res);
        $product_data = array(
            "sts"=>"YES",
            "product_rate"=>$row["price_per_piece"],
            "gst_rate"=>$row["GST_rate"]
        );
    }
    return $product_data;
}

/* =========================================================
   INSERT ORDER ITEMS
========================================================= */
foreach($order_data as $item){

    $dns_prod_code = isset($item["dns_prod_code"]) ? addslashes(trim($item["dns_prod_code"])) : "";
    $prod_desc     = isset($item["prod_desc"]) ? addslashes(trim($item["prod_desc"])) : "";
    $qty           = isset($item["qty"]) ? intval($item["qty"]) : 0;

    if($dns_prod_code=="" || $qty<=0) continue;

    $price_data = get_product_price($dns_prod_code);

    $rate = 0; $gst = 0; $amount = 0; $gst_amt = 0; $total = 0;

    if($price_data["sts"]=="YES"){
        $rate = $price_data["product_rate"];
        $gst  = $price_data["gst_rate"];

        $amount = $rate * $qty;
        $gst_amt = ($gst>0) ? ($amount * $gst / 100) : 0;
        $total = $amount + $gst_amt;
        $ord_total_amount += $total;
    }

    $sql_item = "INSERT INTO $t_apperpdo_pop 
    (the_order_id,order_date,dns_prod_code,prod_display_name,
     QTY,prod_rate,prod_amount,gst_rate,gst_amount,prod_total_amount)
     VALUES
    ('$the_order_id','$order_date_time','$dns_prod_code','$prod_desc',
     '$qty','$rate','$amount','$gst','$gst_amt','$total')";
    
    mysql_query($sql_item);
}

/* =========================================================
   UPDATE TOTAL
========================================================= */
mysql_query("UPDATE $t_main_order_pop 
             SET amount='$ord_total_amount' 
             WHERE order_id='$the_order_id'");

/* =========================================================
   PAYMENT URL
========================================================= */
$region_ck = strtolower($ord_region);

if($region_ck=="ne"){
    $the_payment_url = $server_url."ccavenue_pg/make_pop_order_payment_with_ccavenue_ne.php?order_id=".$the_order_id;
}else{
    $the_payment_url = $server_url."ccavenue_pg/make_pop_order_payment_with_ccavenue.php?order_id=".$the_order_id;
}

/* =========================================================
   RESPONSE
========================================================= */
$res_data = array(
    "process_status"=>"YES",
    "process_message"=>"The POP order successfully received.",
    "the_order_id"=>$the_order_id,
    "the_payment_url"=>$the_payment_url
);

echo json_encode($res_data);
mysql_close();
?>
<?php
session_start();
require('config.php');
require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
$api = new Api($keyId, $keySecret);
include "../star_connection.php";
$customer_master = "customer_master";
$ledger_transaction_table = "ledger_transaction_table";
$ledger_data = array();
$ledger_balance_data = array();
$orderId = $_REQUEST["order_id"] ? trim($_REQUEST["order_id"]) : "";
if($orderId==""){
echo "Something went wrong. Please try later.";
exit;	
}


$sub_total_order_price = "";
$cust_name = "";
$cust_name_val = "";
$cust_address = "";
$cust_pin = "";
$cust_phone = "";
$dealer_code = "";

$sql_fetch = "select * from $ledger_transaction_table where `lt_order_id`='$orderId'";
$res_fetch = mysql_query($sql_fetch);
$tot_res_fetch = mysql_num_rows($res_fetch);
if($tot_res_fetch>0){
$row_fetch = mysql_fetch_assoc($res_fetch);

$customer_name = $row_fetch["customer_name"];
$dealer_code = $row_fetch["dns_customer_code"];
$dealer_sap_code = $row_fetch["customer_sap_code"] ? trim($row_fetch["customer_sap_code"]) : "";
$customer_address = $row_fetch["address"];
$customer_mobile = $row_fetch["mobile"] ? trim($row_fetch["mobile"]) : "";
$customer_email = $row_fetch["email"] ? trim($row_fetch["email"]) : "";
$payment_by = $row_fetch["payment_by"] ? trim($row_fetch["payment_by"]) : "";
$balance = trim($row_fetch["amount"]);
if($balance==""){
$balance = 0;	
}
$balance_in_paisa = ($balance * 100);
$balance_title = "Ledger for NE Zone";
$balance_description = "NE Zone";

if($balance_in_paisa>0){

$server_order_id = $orderId;
$orderData = [
    'receipt'         => $server_order_id,
    'amount'          => $balance_in_paisa, // 2000 rupees in paise
    'currency'        => 'INR',
    'payment_capture' => 1 // auto capture
];
$razorpayOrder = $api->order->create($orderData);
$razorpayOrderId = $razorpayOrder['id'];
$_SESSION['razorpay_order_id'] = $razorpayOrderId;
$displayAmount = $amount = $orderData['amount'];
//$displayAmount = $amount = ($balns_amount_val*100);
$sql_update1_lgr = "update $ledger_transaction_table set `razorpay_order_id`='$razorpayOrderId' where `lt_order_id`='$server_order_id'";
$res_update1_lgr = mysql_query($sql_update1_lgr);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<title>LEDGER PAYMENT</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<style>
.razorpay-payment-button{
	
}
</style>
<body>
<form action="verify.php" name="dlp_form" id="dlp_form" method="POST">
  <script
    src="https://checkout.razorpay.com/v1/checkout.js"
    data-key="<?php echo $keyId;?>"
    data-amount="<?php echo $amount;?>"
    data-currency="INR"
    data-name="<?php echo $balance_title;?>"
    data-buttontext="PROCEED TO PAY"
    data-description="<?php echo $balance_description;?>"
    data-prefill.name="<?php echo $customer_name;?>"
    data-prefill.contact="<?php echo $customer_mobile;?>"
    data-notes.shopping_order_id="<?php echo $server_order_id;?>"
	data-notes.dealer_name="<?php echo $customer_name;?>"
	data-notes.dealer_code="<?php echo $dealer_sap_code;?>"
    data-order_id="<?php echo $razorpayOrderId;?>"  >
  </script>
  <!-- Any extra fields to be submitted with the form but not sent to Razorpay -->
  <input type="hidden" name="shopping_order_id" value="<?php echo $server_order_id;?>">
</form>
<script type="text/javascript">
jQuery(document).ready(function() {
jQuery( "#dlp_form .razorpay-payment-button" ).trigger( "click" );
});
</script>
</body>
</html>

<?php
}else{
echo "The amount is less than zero.";
exit;	
}
}else{
echo "The order doesn't exist.";
exit;	
}

mysql_close();
?>
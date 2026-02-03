<?php
session_start();
require('config.php');
require('razorpay-php/Razorpay.php');

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
$api = new Api($keyId, $keySecret);

include "../star_connection.php";
$ledger_transaction_table = "ledger_transaction_table";

$success = true;
$razorpay_payment_id = "";
$razorpay_signature = "";
$shopping_order_id = "";
$error = "Payment Failed";

if (trim($_POST['razorpay_payment_id'])!='')
{

    try
    {
        // Please note that the razorpay order ID must
        // come from a trusted source (session here, but
        // could be database or something else)
        $attributes = array(
            'razorpay_order_id' => $_SESSION['razorpay_order_id'],
            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
            'razorpay_signature' => $_POST['razorpay_signature']
        );

        $api->utility->verifyPaymentSignature($attributes);
		
		$razorpay_payment_id = $_POST['razorpay_payment_id'];
		$razorpay_signature = $_POST['razorpay_signature'];
		$shopping_order_id = $_POST['shopping_order_id'];
		
    }
    catch(SignatureVerificationError $e)
    {
        $success = false;
        $error = 'Razorpay Error : ' . $e->getMessage();
    }
}


if ($success === true)
{
$sql_update1_lgr = "update $ledger_transaction_table set `razorpay_payment_id`='$razorpay_payment_id',`tracking_id`='$razorpay_payment_id',`razorpay_signature`='$razorpay_signature',`order_status`='Success' where `lt_order_id`='$shopping_order_id'";
$res_update1_lgr = mysql_query($sql_update1_lgr);

    $html = "<p>Your payment was successful</p>
             <p>Order ID: $shopping_order_id</p>
			 <p>Payment ID: $razorpay_payment_id</p>";
}
else
{
    $html = "<p>Your payment failed</p>
             <p>{$error}</p>";
}

echo $html;

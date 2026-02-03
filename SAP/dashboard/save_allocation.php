<?php 
//cho"<pre>";print_r($_POST);die;
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);





include "web_check.php";
include "star_connection.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid Request");
}

$apporder_no   = isset($_POST['apporder_no']) ? $_POST['apporder_no'] : '';
$invoice_no    = isset($_POST['invoice_no']) ? $_POST['invoice_no'] : '';
$prod_name     = isset($_POST['prod_name']) ? $_POST['prod_name'] : '';
$invoice_qty   = isset($_POST['invoice_qty']) ? $_POST['invoice_qty'] : '';
$invdt         = isset($_POST['invdt']) ? $_POST['invdt'] : '';
$allocations = isset($_POST['allocation']) ? $_POST['allocation'] : array();

$allocation_data = [];
$idx = 0;

if (!empty($invdt)) {
    // convert to timestamp
    $timestamp = strtotime($invdt);

    if ($timestamp !== false) {
        // format to YYYY-MM-DD
        $invdt = date("Y-m-d", $timestamp);
    } else {
        $invdt = '';
    }
} else {
    $invdt = '';
}

function generateOrderId($customer_id, $apporder_no) {
    // current timestamp with microseconds
    $micro_time = microtime(true);
    $micro = sprintf("%06d", ($micro_time - floor($micro_time)) * 1000000);
    $date = date("YmdHis"); // 20250816122743
    $timestamp = $date . $micro; // 20250816122743565999

    // build order_id
    $order_id = "INV_" . $customer_id . "_" . $timestamp . "_" . $apporder_no;
    return $order_id;
}

// Loop dealers and prepare allocation_data
foreach ($allocations as $customer_code => $qty) {
    $qty = trim($qty);
    if ($qty === '' || floatval($qty) <= 0) {
        continue; // skip empty or 0
    }
    //  Fetch sub-dealer as customer_id from customer_master
    $cust_id = "";
    $qry = mysql_query("SELECT customer_id FROM customer_master WHERE customer_code='" . mysql_real_escape_string($customer_code) . "' LIMIT 1");
    if ($qry && mysql_num_rows($qry) > 0) {
        $row = mysql_fetch_assoc($qry);
        $cust_id = $row['customer_id'];
    }
      //  Fetch dealer as customer_id from customer_master
    $dealer_cust_id = "";
    $sswa_selected_customer_code=$_SESSION['sswa_selected_customer_code'];
    $qry = mysql_query("SELECT customer_id FROM customer_master WHERE customer_code='" . mysql_real_escape_string($sswa_selected_customer_code) . "' LIMIT 1");
    if ($qry && mysql_num_rows($qry) > 0) {
        $row = mysql_fetch_assoc($qry);
        $dealer_cust_id = $row['customer_id'];
    }
    //  Generate order_id using customer_id
    $micro_time = microtime(true);
    $micro = sprintf("%06d", ($micro_time - floor($micro_time)) * 1000000);
    $date = date("YmdHis");
    $timestamp = $date . $micro;
    $order_id = "INV_" . $cust_id . "_" . $timestamp . "_" . $apporder_no;

    //  Prepare allocation data
    $allocation_data[$idx]['APPORDERNO']    = $apporder_no;
    $allocation_data[$idx]['order_id']      = $order_id;
    $allocation_data[$idx]['inv_no']        = $invoice_no;
    $allocation_data[$idx]['prod_desc']     = $prod_name;
    $allocation_data[$idx]['qty']           = floatval($qty);
    $allocation_data[$idx]['inv_qty']       = $invoice_qty;
    $allocation_data[$idx]['inv_date']      = $invdt;
    $allocation_data[$idx]['sub_dealer_id'] = $cust_id ;
    $allocation_data[$idx]['customer_id']   = $dealer_cust_id; // still post code for API

    $idx++;
}
//echo"<pre>";print_r($allocation_data);die;
if (empty($allocation_data)) {
    die("No allocation data to save!");
}

// Call API
$api_url = BASE_URL . "save_allocation_details_invoicewise.php";

$post_fields = [
    "allocation_data" => $allocation_data
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
$response = curl_exec($ch);
//echo"<pre>";print_r($response);die;

if (curl_errno($ch)) {
    die("Curl error: " . curl_error($ch));
}
curl_close($ch);

// decode API response
// decode API response
$result = json_decode($response, true);

if ($result && isset($result['process_status']) && $result['process_status'] === "YES") {
    $message = isset($result['process_message']) ? $result['process_message'] : 'Success!';
    echo "<script>
        alert('".addslashes($message)."');
        window.location.href = 'retailer_lifting.php';
    </script>";
    exit;
} else {
    // error handling
    $message = isset($result['process_message']) ? $result['process_message'] : 'Failed to save allocation.';
    echo "<script>
        alert('".addslashes($message)."');
        window.history.back();
    </script>";
    exit;
}
?>

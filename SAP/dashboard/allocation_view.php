<?php
include "web_check.php";
include "star_connection.php";

$apporder_no = $_POST['apporder_no'];
$erporder_no = $_POST['erporder_no'];
$prod_name   = $_POST['prod_name'];
$invoice_no  = $_POST['invoice_no'];
$invoice_qty = $_POST['invoice_qty'];
    $invdt = $_REQUEST['invdt'];
//echo"<pre>";print_r($_POST);die;

if(!isset($_POST['dealer_codes'])){
    die("No dealers selected!");
}

$dealer_codes = $_POST['dealer_codes']; // array
$dealer_code_str = implode("','", array_map('mysql_real_escape_string',$dealer_codes));

// fetch dealers
$sql = "SELECT customer_code, customer_name 
        FROM customer_master 
        WHERE customer_code IN('$dealer_code_str')";
$res = mysql_query($sql);

// === API Call for Remaining Allocation Qty ===
 $api_url = BASE_URL . "dispatched-order-list-download-invoicewise-v2.php?invoice_no=" . urlencode($invoice_no)."&app_orderno=".urlencode($apporder_no);

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

// Disable SSL verify (fix for PHP 5.6 / self-signed cert issues)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

// Set headers (force JSON response)
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Accept: application/json",
    "Content-Type: application/json"
));

$response = curl_exec($ch);
//echo"<pre>";print_r($response);die;
// Check for errors
if (curl_errno($ch)) {
    die("cURL Error: " . curl_error($ch));
}
curl_close($ch);

// Decode JSON
$apiData = json_decode($response, true);
//echo"<pre>";print_r($apiData);die;

// Default qty
$remaining_qty = 0;
if ($apiData && isset($apiData['process_status']) && $apiData['process_status'] === "YES") {
    if (!empty($apiData['dispatched_invoice_data'])) {
        $remaining_qty = floatval($apiData['dispatched_invoice_data'][0]['available_allocation_qty']);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Allocation</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .dealer-btn {
        border: 1px solid #ccc;
        margin: 5px;
        padding: 10px 15px;
        border-radius: 6px;
        cursor: pointer;
        background: #f8f9fa;
        font-weight: 600;
    }
    .dealer-btn.active {
        background: #d32f2f;
        color: #fff;
        border-color: #d32f2f;
    }
    .allocation-card {
        margin-top: 20px;
        display: none;
    }
    .allocation-card.active {
        display: block;
    }
    .btn-submit {
        margin-top: 20px;
        background: #2e2e2e;
        color: #fff;
        width: 100%;
        padding: 10px;
        font-weight: bold;
        border-radius: 8px;
    }
  </style>
</head>
<body class="p-3">

<h4 class="mb-3">ALLOCATION</h4>

<form method="post" action="save_allocation.php" id="allocationForm">

  <input type="hidden" name="remaining_qty" value="<?php echo $remaining_qty; ?>">
  <input type="hidden" name="invoice_no" value="<?php echo htmlspecialchars($invoice_no); ?>">
  <input type="hidden" name="prod_name" value="<?php echo htmlspecialchars($prod_name); ?>">
    <input type="hidden" name="apporder_no" value="<?php echo htmlspecialchars($apporder_no); ?>">
     <input type="hidden" name="invoice_qty" value="<?php echo htmlspecialchars($invoice_qty); ?>">
    <input type="hidden" name="invdt" value="<?php echo htmlspecialchars($invdt); ?>">

  <!-- Dealer buttons -->
  <div class="d-flex flex-wrap">
    <?php 
    $i=0;
    mysql_data_seek($res,0);
    while($row=mysql_fetch_assoc($res)): ?>
      <div class="dealer-btn <?php echo $i==0?'active':''; ?>" 
           onclick="selectDealer('<?php echo preg_replace('/[^A-Za-z0-9_\-]/', '_', $row['customer_code']);?>', this)">
        <?php echo htmlspecialchars($row['customer_name']); ?>
      </div>
    <?php $i++; endwhile; ?>
  </div>

  <!-- Dealer content -->
  <?php 
  mysql_data_seek($res,0);
  $i=0;
  while($row=mysql_fetch_assoc($res)): ?>
    <div class="card allocation-card shadow-sm <?php echo $i==0?'active':''; ?>" 
         id="dealer-<?php echo preg_replace('/[^A-Za-z0-9_\-]/', '_', $row['customer_code']); ?>">

      <div class="card-body text-center">
        <h6><strong><?php echo htmlspecialchars($prod_name); ?></strong></h6>
        <p>Remaining Allocation Qty: <strong class="remaining-qty"><?php echo number_format($remaining_qty,2); ?></strong></p>
        <p>Invoice Qty: <strong><?php echo htmlspecialchars($invoice_qty); ?></strong></p>
        
        <!-- Allocation input -->
        <input type="number" 
               name="allocation[<?php echo $row['customer_code']; ?>]" 
               class="form-control text-center mb-3 allocation-input" 
               placeholder="QTY (MT)" step="0.01" min="0" 
               max="<?php echo $remaining_qty; ?>" style="border: 1px solid #000;">
      </div>
    </div>
  <?php $i++; endwhile; ?>

  <button type="submit" class="btn btn-submit">Submit Allocation</button>
</form>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    const remaining = parseFloat($("input[name='remaining_qty']").val()) || 0;

    // ===== Dealer switching logic =====
    window.selectDealer = function(code, el) {
        $(".dealer-btn").removeClass("active");
        $(el).addClass("active");
        $(".allocation-card").removeClass("active");
        $("#dealer-" + code).addClass("active");
    }

    // ===== Real-time validation =====
    $(".allocation-input").on("input", function(){
        let val = parseFloat($(this).val()) || 0;

        if (val > remaining) {
            $(this).css("border", "2px solid red");
        } else {
            $(this).css("border", "");
        }
    });

    // ===== Submit validation =====
    $("#allocationForm").on("submit", function(e){
        let total = 0;
        let hasValue = false;
        let invalid = false;

        $(".allocation-input").each(function(){
            let val = parseFloat($(this).val()) || 0;
            if (val > 0) hasValue = true;
            if (val > remaining) {
                invalid = true;
                $(this).css("border", "2px solid red");
            }
            total += val;
        });

        if (!hasValue) {
            e.preventDefault();
            alert("Error: Please enter the allocation for at least one Sub-Dealer, along with the Remaining Allocation Quantity.");
            return false;
        }

        if (invalid) {
            e.preventDefault();
            alert("Error: One or more allocations exceed the remaining quantity (<?php echo $remaining_qty; ?>). Please correct them.");
            return false;
        }

        if (total > remaining) {
            e.preventDefault();
            alert("Error: Total Allocation ("+total+") cannot exceed Remaining Allocation Qty ("+remaining+").");
            return false;
        }
    });
});
</script>

</body>
</html>

<?php
date_default_timezone_set("Asia/Kolkata");
include_once("star_connection.php");

// ================= SHARED CSS =================
function decl_css(){
?>
<style>
  * { box-sizing:border-box; }
  html { -webkit-text-size-adjust:100%; text-size-adjust:100%; }
  body { font-family:"Calibri","Segoe UI",Arial,sans-serif; background:#eceff1; margin:0; padding:24px; color:#1a1a1a; font-size:14px; width:100%; overflow-x:hidden; }
  .wrap { max-width:850px; margin:0 auto; background:#fff; padding:45px 55px 60px; border:1px solid #d8d8d8; box-shadow:0 2px 10px rgba(0,0,0,.08); }
  .lookup { background:#f7f9fa; border:1px solid #e0e4e7; border-radius:4px; padding:16px 18px; margin-bottom:32px; }
  .lookup label { font-size:clamp(9px, 1.6vw, 14px); margin-right:8px; }
  .lookup input { padding:8px 10px; width:230px; max-width:60%; border:1px solid #bfc6cc; border-radius:3px; font-size:clamp(9px, 1.6vw, 14px); }
  .lookup button { padding:8px 18px; margin-left:6px; border:1px solid #2c6fb5; background:#2c6fb5; color:#fff; border-radius:3px; cursor:pointer; font-size:clamp(9px, 1.6vw, 14px); }
  .lookup button.sec { background:#fff; color:#2c6fb5; }
  .lookup button:hover { opacity:.9; }

  table.cust { width:100%; border-collapse:collapse; margin-bottom:38px; font-size:16px; }
  table.cust th, table.cust td { border:1px solid #ccd2d6; padding:9px 12px; vertical-align:top; }
  table.cust th { background:#f2f4f6; width:180px; font-weight:bold; text-align:left; }
  table.cust td { text-align:right; }

  h2.title { text-align:center; font-size:16px; margin:0 0 34px; font-weight:bold; text-decoration:underline; }

  p.to      { font-weight:bold; margin:22px 0 4px; }
  p.subject { margin:22px 0; text-align:justify; font-weight:bold; }
  p.decl    { margin:14px 0; text-align:justify; line-height:1.9; }
  .decl-list p { margin:14px 0; text-align:justify; line-height:1.9; padding-left:26px; text-indent:-26px; }
  .hl { background:transparent; padding:0; }
  p.dear { font-size:14px; margin:20px 0; }

  p.body { font-size:clamp(9px, 1.6vw, 14px); line-height:1.9; margin:16px 0; text-align:justify; }
  .val { border-bottom:1px solid #333; padding:0 8px 1px; display:inline-block; min-width:clamp(90px, 28vw, 240px); font-weight:bold; }
  .val.wide { min-width:clamp(130px, 40vw, 340px); }
  .sig { margin-top:70px; }
  .sig p.body { margin:30px 0; font-size:16px; display:flex; align-items:flex-end; gap:10px; text-align:left; }
  .sig p.body .lbl { font-weight:bold; }
  .sig p.body .v { flex:1; min-width:80px; min-height:1.4em; border-bottom:1px solid #333; font-weight:bold; padding-bottom:2px; word-break:break-word; }
  .err-box { padding:12px 14px; background:#fdecea; border:1px solid #e6a8a2; border-radius:3px; font-size:clamp(9px, 1.6vw, 14px); color:#8a1c14; }

  @media print {
    .lookup { display:none !important; }
    body { background:#fff; padding:0; }
    .wrap { border:none; box-shadow:none; max-width:100%; padding:20px; }
  }

  /* Mobile: same desktop layout, only smaller text/spacing */
  @media (max-width:900px){
    body { padding:12px; }
    .wrap { padding:22px 26px 30px; }
    .lookup { padding:10px 12px; margin-bottom:20px; }
    .lookup input { padding:5px 7px; }
    .lookup button { padding:5px 11px; }
    table.cust { margin-bottom:24px; }
    table.cust th, table.cust td { padding:5px 7px; }
    table.cust th { width:110px; }
    h2.title { margin:0 0 26px; }
    h2.title span { padding:2px 5px; }
    p.body { line-height:1.8; margin:10px 0; }
    .val { padding:0 5px 1px; }
    .sig { margin-top:42px; }
    .sig p.body { margin:18px 0; }
    .err-box { padding:8px 10px; }
  }

  @media (max-width:480px){
    body { padding:8px; }
    .wrap { padding:14px 14px 20px; }
    table.cust th, table.cust td { padding:4px 5px; }
    table.cust th { width:80px; }
    .val { padding:0 4px 1px; }
    .sig { margin-top:30px; }
    .sig p.body { margin:14px 0; }
  }
</style>
<?php
}

// ================= API : FILLED HTML FRAGMENT =================
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'get_customer'){

    // when opened directly in a browser, wrap the fragment in a full page
    $standalone = !isset($_SERVER['HTTP_X_REQUESTED_WITH']);

    if($standalone){
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
        echo '<title>Declaration Form for Unregistered Person (URP)</title>';
        decl_css();
        echo '</head><body><div class="wrap">';
    } else {
        // AJAX / WebView fragment: ship the styles inline so it renders correctly
        // even when injected without the parent page (e.g. react-native-webview).
        decl_css();
    }

    if(empty($_REQUEST['customer_id'])){
        echo '<div class="err-box">Customer id required.</div>';
        if($standalone){ echo '</div></body></html>'; }
        exit;
    }

    $customer_id = mysql_real_escape_string(trim($_REQUEST['customer_id']));

    $sql = "SELECT customer_id, customer_name, address, PAN
            FROM customer_master
            WHERE customer_id='".$customer_id."' LIMIT 1";
    $res = mysql_query($sql);

    if(!$res || mysql_num_rows($res) == 0){
        echo '<div class="err-box">No customer found for ID '.htmlspecialchars($customer_id).'.</div>';
        if($standalone){ echo '</div></body></html>'; }
        exit;
    }

    $row = mysql_fetch_assoc($res);

    $c_id   = htmlspecialchars(trim($row['customer_id']));
    $c_pan   = htmlspecialchars(trim($row['PAN']));
    $c_name = htmlspecialchars(trim($row['customer_name']));
    if (strpos($c_name, '-') !== false) {
      $c_name = trim(substr($c_name, strpos($c_name, '-') + 1));
    }
    $c_addr = htmlspecialchars(trim($row['address']));
    $parts = array_map('trim', explode(',', $row['address']));
    $clean = [];
    foreach ($parts as $p) {
        if ($p !== '' && !in_array($p, $clean)) {
            $clean[] = $p;
        }
    }
    $c_addr = htmlspecialchars(implode(', ', $clean));
    $mention_name = isset($_REQUEST['mention_name']) ? htmlspecialchars(trim($_REQUEST['mention_name'])) : '';
    $pan_no = isset($_REQUEST['pan_no']) ? htmlspecialchars(trim($_REQUEST['pan_no'])) : '';
    $today  = date('d-m-Y');
    ?>
    <table class="cust">
      <tr><th>Customer ID</th><td><?php echo $c_id; ?></td></tr>
      <tr><th>Customer Name</th><td><?php echo $c_name; ?></td></tr>
      <tr><th>Address</th><td><?php echo $c_addr; ?></td></tr>
    </table>

    <h2 class="title">Declaration for Unregistered Person (URP)</h2>

    <p class="to">To</p>
    <p class="to">Star Cement Limited / Star Cement Northeast Limited / Star Smart Building Solution Limited</p>

    <p class="dear to">Dear Sir / Madam,</p>

    <p class="subject">
      <span style="font-weight:normal">Subject:</span> Declaration regarding being Unregistered Party (URP) under GST for Bill-To / Ship-To Transactions.
    </p>

    <p class="decl">
      <span class="hl">I/We <b><?php echo $c_id.' &ndash; '.$c_name; ?></b>, address
      <b><?php echo $c_addr; ?></b>,</span> hereby declare that
    </p>

    <div class="decl-list">
      <p>(a) I/We am/are currently not registered under the Goods and Services Tax (GST) Act and do not possess any GSTIN.</p>

      <p>(b) I/We am/are unregistered under GST, our ship-to location should be treated as URP (Unregistered Person) for the
        purpose of E-Way Bill generation and other GST compliance requirements.</p>

      <p>(c) If I/We obtain a GST registration in the future, we will immediately intimate to you.</p>

      <p>(d) This declaration is being issued for compliance with GST and E-Way Bill rules, and may be used by the supplier,
        transporter, and Bill-To party for record-keeping and statutory purposes.</p>

      <p>(e) I/We declare that the information furnished above is true, correct, and complete to the best of my/our knowledge
        and belief. I/We understand and acknowledge that if any information provided herein is found to be false, incorrect,
        incomplete, or misleading, I/We shall be solely responsible for all legal, financial, and statutory consequences
        arising therefrom.</p>
    </div>

    <div class="sig">
      <p class="body"><span class="lbl">Signature of Authorised Signatory: <?php echo $mention_name; ?></span></p>
      <p class="body"><span class="lbl">SAP Code &amp; Name: <?php echo $c_id.' &ndash; '.$c_name; ?></span></p>
      <p class="body"><span class="lbl">PAN (if available): <?php echo $pan_no; ?></span></p>
      <p class="body"><span class="lbl">Date: <?php echo $today; ?></span></p>
    </div>
    <?php

    if($standalone){ echo '</div></body></html>'; }
    exit;
}
// ================= END API =================
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Declaration of GST NON-Enrolment</title>
<?php decl_css(); ?>
</head>
<body>
<div class="wrap">

  <div class="lookup">
    <label><b>Customer ID:</b></label>
    <input type="text" id="lookup_id" placeholder="Enter customer id">
    <button type="button" onclick="fetchCustomer()">Get Details</button>
    <button type="button" class="sec" onclick="window.print()">Print</button>
  </div>

  <div id="result"></div>

</div>

<script>
var API = "gst_declaration.php";   // this same file
var MENTION_NAME = <?php echo json_encode(isset($_REQUEST['mention_name']) ? trim($_REQUEST['mention_name']) : ''); ?>;

function fetchCustomer(){
  var id = document.getElementById('lookup_id').value.trim();
  if(id === ''){
    document.getElementById('result').innerHTML = '<div class="err-box">Please enter a customer id.</div>';
    return;
  }

  var xhr = new XMLHttpRequest();
  xhr.open('GET', API + '?action=get_customer&customer_id=' + encodeURIComponent(id) + '&mention_name=' + encodeURIComponent(MENTION_NAME), true);
  xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');   // marks it as AJAX
  xhr.onload = function(){
    document.getElementById('result').innerHTML = xhr.responseText;
  };
  xhr.onerror = function(){
    document.getElementById('result').innerHTML = '<div class="err-box">Network error.</div>';
  };
  xhr.send();
}

document.getElementById('lookup_id').addEventListener('keydown', function(e){
  if(e.keyCode === 13){ fetchCustomer(); }
});
</script>
</body>
</html>
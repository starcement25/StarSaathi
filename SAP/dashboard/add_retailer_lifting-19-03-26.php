<?php
include "web_check.php";
include "star_connection.php";

    $apporder_no = $_REQUEST['apporder_no'];
    $erporder_no = $_REQUEST['erporder_no'];
    $prod_name   = $_REQUEST['prod_name'];
    $invoice_no  = $_REQUEST['invoice_no'];
    $invoice_qty = $_REQUEST['invoice_qty'];
    $invdt = $_REQUEST['invdt'];


// ================= INPUTS =================
$emp_code   = isset($_SESSION['sswa_selected_customer_code']) ? trim($_SESSION['sswa_selected_customer_code']) : "";
$user_type  = isset($_SESSION['sswa_user_type']) ? strtolower(trim($_SESSION['sswa_user_type'])) : "";
$incremental_download = isset($_REQUEST['incremental_download']) ? strtolower($_REQUEST['incremental_download']) : 'no';
$last_update_time = isset($_REQUEST['last_update_time']) ? $_REQUEST['last_update_time'] : "";
//echo"<pre>";print_r($_REQUEST);die;
//echo"<pre>";print_r($_SESSION);die;

// tables
$broker_master = "broker_master";
$customer_broker_relation = "customer_broker_relation";
$customer_master = "customer_master";

$tagged_cust_code_arr = [];
$tagged_cust_code_str = "";

// ================= BROKER LOGIC =================
if($user_type=="broker"){
    $sql1 = "SELECT broker_id FROM $broker_master WHERE dns_broker_id='".mysql_real_escape_string($emp_code)."' ";
    $res1 = mysql_query($sql1);
    if(mysql_num_rows($res1)>0){
        $row1 = mysql_fetch_array($res1);
        $broker_id = $row1["broker_id"];

        $sql2 = "SELECT customer_code 
                 FROM $customer_broker_relation 
                 WHERE broker_code='".mysql_real_escape_string($broker_id)."' AND acedns='Y'";
        $res2 = mysql_query($sql2);
        while($row2 = mysql_fetch_array($res2)){
            $customer_code_ftc = $row2["customer_code"] ? trim($row2["customer_code"]) : "";
            if($customer_code_ftc!=""){
                $tagged_cust_code_arr[] = $customer_code_ftc;
            }
        }
        if(count($tagged_cust_code_arr)>0){
            $tagged_cust_code_str = implode("','",$tagged_cust_code_arr);
        }
    }
}

// ================= LOGIN CONDITION =================
if($incremental_download=='no'){
    $login_condition=" AND acedns='Y'";
} else {
    $login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

// ================= FINAL QUERY =================
if($user_type=="broker" && $tagged_cust_code_str!=''){
    $sqlquerycustomerroute="
        SELECT customer_code,customer_name,customer_id 
        FROM $customer_master 
        WHERE 1 $login_condition 
          AND (
            customer_code IN('".$tagged_cust_code_str."') 
            OR customer_code IN(
                SELECT customer_code 
                FROM $customer_master 
                WHERE rds_tag IN('".$tagged_cust_code_str."') AND acedns='Y'
            )
          )
        ORDER BY customer_name ,acedns DESC
    ";
} else {
    $sqlquerycustomerroute="
        SELECT customer_code,customer_name ,customer_id
        FROM $customer_master 
        WHERE 1 $login_condition 
          AND (
            customer_code='".mysql_real_escape_string($emp_code)."' 
            OR customer_code IN(
                SELECT customer_code 
                FROM $customer_master 
                WHERE rds_tag='".mysql_real_escape_string($emp_code)."' AND acedns='Y'
            )
          )
          AND cust_type!='Ship to Party-dealer' 
          AND cust_type!='ShiptoParty-Subdeale'
          AND cust_type='RSSD'
        ORDER BY customer_name ,acedns DESC
    ";
}

// run query
//echo"<pre>";print_r($sqlquerycustomerroute);die;

$result = mysql_query($sqlquerycustomerroute);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Select Sub Dealers</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .app-header { background:#e2182b; color:#fff; padding:12px; font-weight:bold; }
    .dealer-row { border-bottom:1px solid #eee; padding:10px; display:flex; align-items:center; gap:10px; cursor: pointer;}
    .dealer-name { flex:1; font-weight:500; }
    .footer-submit { position:fixed; bottom:0; left:0; right:0; background:#444; padding:10px; text-align:center; }
    .footer-submit .btn { width:100%; max-width:600px; }
    .dealer-row.highlight {
    background: #d7f0ff;   /* light blue highlight */
}
  </style>
  <style>
/* The container */
.container {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 2px;
  cursor: pointer;
  font-size: 12px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
 
/* Hide the browser's default checkbox */
.container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}
 
/* Create a custom checkbox */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 20px;
  width: 20px;
  background-color: #eee;
  border: 1px solid #000;
}
 
/* On mouse-over, add a grey background color */
.container:hover input ~ .checkmark {
  background-color: #ccc;
}
 
/* When the checkbox is checked, add a blue background */
.container input:checked ~ .checkmark {
  background-color: #2196F3;
}
 
/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}
 
/* Show the checkmark when checked */
.container input:checked ~ .checkmark:after {
  display: block;
}
 
/* Style the checkmark/indicator */
.container .checkmark:after {
  left: 7px;
  top: 3px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}
</style>
</head>
<body class="bg-light">

  <!-- HEADER -->
  <div class="app-header">PLEASE CHOOSE RSAR</div>

  <!-- SEARCH -->
  <div class="p-2">
    <input type="search" id="searchBox" class="form-control" placeholder="Search By RSAR Name Or RSAR ID">
  </div>
    <!-- LIST -->
    <form id="dealerForm" method="post" action="allocation_view.php">
    <input type="hidden" name="apporder_no" value="<?php echo htmlspecialchars($apporder_no); ?>">
    <input type="hidden" name="erporder_no" value="<?php echo htmlspecialchars($erporder_no); ?>">
    <input type="hidden" name="prod_name" value="<?php echo htmlspecialchars($prod_name); ?>">
    <input type="hidden" name="invoice_no" value="<?php echo htmlspecialchars($invoice_no); ?>">
    <input type="hidden" name="invoice_qty" value="<?php echo htmlspecialchars($invoice_qty); ?>">
    <input type="hidden" name="invdt" value="<?php echo htmlspecialchars($invdt); ?>">

      <div id="listContainer" class="mb-5">
        <?php if(mysql_num_rows($result)>0): ?>
            <?php while($row=mysql_fetch_assoc($result)): ?>
                <div class="dealer-row">
                  <label class="container"> <?php echo htmlspecialchars($row['customer_name']); ?> <?php echo $row['customer_id']; ?>
                      <input type="checkbox" class="form-check-input dealer-check" 
                          name="dealer_codes[]" 
                          value="<?php echo $row['customer_code']; ?>">
                  <span class="checkmark"></span>
                  </label>
                                 
                    <div class="dealer-name">
                       
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="p-3 text-danger">No Sub Dealers Found.</div>
        <?php endif; ?>
    </div>

    <!-- FOOTER -->
    <div class="footer-submit">
        <button type="submit" class="btn btn-dark">SUBMIT</button>
    </div>
    </form>
  <script>
    // Search filter
    document.getElementById('searchBox').addEventListener('keyup', function(){
      let q = this.value.toLowerCase();
      document.querySelectorAll('#listContainer .dealer-row').forEach(function(row){
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(q) ? 'flex' : 'none';
      });
    });

    // Collect selected values on submit
    document.getElementById('submitBtn').addEventListener('click', function(){
      let checked = [];
      document.querySelectorAll('input[name="dealer[]"]:checked').forEach(chk=>{
        checked.push(chk.value);
      });
      if(checked.length===0){
        alert("Please select at least one dealer.");
        return;
      }
      alert("Selected Dealer Codes: " + checked.join(", "));
      // TODO: send via AJAX to server if needed
    });
    $(document).on("click", ".dealer-row", function(e) {
    // Prevent double-trigger when clicking directly on checkbox
    if (!$(e.target).is("input[type=checkbox]")) {
        let chk = $(this).find(".dealer-check");
        chk.prop("checked", !chk.prop("checked"));
    }

    // Add highlight if checked
    if ($(this).find(".dealer-check").is(":checked")) {
        $(this).addClass("highlight");
    } else {
        $(this).removeClass("highlight");
    }
});

  </script>
</body>
</html>
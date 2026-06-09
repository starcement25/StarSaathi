<?php 
session_start();
include "star_connection.php"; // contains mysql_connect + mysql_select_db

$customer_master         = "customer_master";
$dealer_id               = isset($_REQUEST["dealer_id"]) ? trim($_REQUEST["dealer_id"]) : "";
$mob_no                  = isset($_REQUEST["mob_no"]) ? trim($_REQUEST["mob_no"]) : "";
$dealer_otp              = isset($_REQUEST["dealer_otp"]) ? trim($_REQUEST["dealer_otp"]) : "";
$logout_url              = isset($_REQUEST["logout_url"]) ? trim($_REQUEST["logout_url"]) : "";
$protal_type             = isset($_REQUEST["protal_type"]) ? trim($_REQUEST["protal_type"]) : "";

$details = 'login detail '.$logout_url.' / '.$protal_type;

// --------------------------------------------
// Validations
// --------------------------------------------
if ($dealer_id == "") {
    echo "Dealer ID is required.";
    exit;
}

if ($mob_no == "") {
    echo "Mobile number is required.";
    exit;
}

// Mobile must be 10 digits
if (!preg_match("/^[0-9]{10}$/", $mob_no)) {
    echo "Invalid mobile number.";
    exit;
}

// Escape values for mysql
$dealer_id = mysql_real_escape_string($dealer_id);
$mob_no    = mysql_real_escape_string($mob_no);

// --------------------------------------------
// Query to verify dealer
// --------------------------------------------
$sql1 = "SELECT * FROM $customer_master 
         WHERE customer_id = '$dealer_id' 
         AND phone_no = '$mob_no' 
         AND cust_type = 'Dealer' ";

$res1 = mysql_query($sql1);
if (!$res1) {
    echo "Query Error: ".mysql_error();
    exit;
}

$totres1 = mysql_num_rows($res1);

// --------------------------------------------
// If dealer found
// --------------------------------------------
if ($totres1 > 0) {

    // ---------------- Track Log ------------------
    $curr_date_time = date("Y-m-d H:i:s");
    $webservice_name = "Login";

    $sqlin_tl = "INSERT INTO webservice_track_log 
                    (customer_code, webservice_name, details, datetime)
                 VALUES ('$dealer_id', '$webservice_name', '$details', '$curr_date_time')";
    mysql_query($sqlin_tl);

    // --------------------------------------------
    // Fetch dealer info
    // --------------------------------------------
    $row1 = mysql_fetch_assoc($res1);

    $dns_customer_code = trim($row1["dns_customer_code"]);
    $customer_code     = trim($row1["customer_code"]);
    $customer_name     = trim($row1["customer_name"]);

    // --------------------------------------------
    // Set Session Values
    // --------------------------------------------
    $_SESSION["sswa_user_type"]               = "DEALER";
    $_SESSION["sswa_user_name"]               = $customer_name;
    $_SESSION["sswa_user_id"]                 = $customer_code;
    $_SESSION["sswa_user_dns_id"]             = $dns_customer_code;
    $_SESSION["sswa_selected_dealer_name"]    = $customer_name;
    $_SESSION["sswa_selected_dealer_code"]    = $dns_customer_code;
    $_SESSION["sswa_selected_customer_code"]  = $customer_code;
    $_SESSION["protal_type"]  = $protal_type;
    $_SESSION["logout_url"]  = $logout_url;
    //echo"<pre>";print_r($_SESSION);die;

    // --------------------------------------------
    // Redirect to next page
    // --------------------------------------------
    //header("Location: star_make_order.php");
    header("Location: ledger_balance.php");
    exit;

} else {
    echo "Dealer not found OR Invalid credentials.";
    exit;
}
?>

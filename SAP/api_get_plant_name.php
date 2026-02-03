<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

header('Content-Type: application/json');

date_default_timezone_set('Asia/Kolkata');

require_once("starsaathi_connection.php");
$localDB = new starsaathi_connection();
$conn = $localDB->conn;

mysql_select_db("starsaathi_STARS", $conn);


$the_customer_code = isset($_GET['customer_code']) ? $_GET['customer_code'] : '';

if ($the_customer_code == '') {
    echo json_encode(['error' => 'customer_code is required']);
    exit;
}

$SAP_customer_master = "ptblcustomermaster";


$sql1 = "SELECT VWERK 
         FROM $SAP_customer_master 
         WHERE KUNNR = '$the_customer_code' 
           AND (VKORG='1017' OR VKORG='1010') 
           AND VWERK != '' 
         ORDER BY AEDAT DESC, ADDITIONAL_DATA1 DESC 
         LIMIT 1";

$res1 = mysql_query($sql1);

$the_SAP_plant = "";
if ($res1 && mysql_num_rows($res1) > 0) {
    $row1 = mysql_fetch_assoc($res1);
    $the_SAP_plant = trim($row1["VWERK"]);
}

// Get plant name
$plant_name = "";
if ($the_SAP_plant != "") {

    $sql2 = "SELECT plant_name 
             FROM plant_list_cement 
             WHERE plant_code = '$the_SAP_plant'
             LIMIT 1";

    $res2 = mysql_query($sql2);

    if ($res2 && mysql_num_rows($res2) > 0) {
        $row2 = mysql_fetch_assoc($res2);
        $plant_name = trim($row2["plant_name"]);
    }
}

echo json_encode([
    "customer_code" => $the_customer_code,
    "plant_code"    => $the_SAP_plant,
    "plant_name"    => $plant_name
]);
?>
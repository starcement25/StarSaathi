<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
error_reporting(E_STRICT);
ini_set('memory_limit', '99999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
include "star_connection.php";

$customer_master = "customer_master";
$product_master = "product_master";
$cron_update_table = "cron_update_table";
$allocation_details = "allocation_details";

$curr_date_time = date("Ymd H:i:s");
$curr_date = date("Y-m-d");  //changed - -
$prev_30_day_date = date("Y-m-d", strtotime($curr_date . ' -30 day'));
$curr_date_format = date("Y-m-d", strtotime($curr_date));
$prev_30_day_date_format = date("Ymd", strtotime($prev_30_day_date));

$the_customer_code = $_POST["customer_id"] ? addslashes(trim($_POST["customer_id"])) : "";
$from_date = $_POST["from_date"] ? addslashes(trim($_POST["from_date"])) : "";
$to_date = $_POST["to_date"] ? addslashes(trim($_POST["to_date"])) : "";

if ($from_date != "" && $to_date != "") {
    $from_date_format = date("Y-m-d", strtotime($from_date));
    $to_date_format = date("Y-m-d", strtotime($to_date));

    $the_where_condition = " `date_and_time` >= '$from_date_format' AND `date_and_time` <= '$to_date_format' ";
} else {
    $the_where_condition = " `date_and_time` >= '$prev_30_day_date_format' AND `date_and_time` <= '$curr_date_format' ";
}

// $sqldt = "SELECT DISTINCT(SUBSTRING(`date_and_time`,1,10)) as 'date' FROM allocation_details 
// WHERE customer_id = '$the_customer_code' AND  $the_where_condition ORDER BY date DESC";
// $resall = mysql_query($sqldt);
// $totdt = mysql_num_rows($resall);


//main
$sqlall = "SELECT allocation_id, challan_no, customer_id,prod_desc,dns_prod_code, allocation_qty, date_and_time FROM allocation_details WHERE customer_id = '$the_customer_code' AND $the_where_condition ORDER BY date_and_time DESC";


// $sqlall = "SELECT allocation_id, challan_no, customer_id, prod_desc, dns_prod_code, SUM(allocation_qty) AS total_qty, date_and_time 
//            FROM allocation_details 
//            WHERE customer_id = '$the_customer_code' AND $the_where_condition 
//            GROUP BY prod_desc 
//            ORDER BY date_and_time DESC";



//echo $sqlall;

$resall = mysql_query($sqlall);
$totall = mysql_num_rows($resall);
//echo 'total :'+$totall;
if ($totall > 0) {
    while ($row1 = mysql_fetch_assoc($resall)) {
        $allocation_id = $row1["allocation_id"];
        $date_and_time = $row1["date_and_time"];
        $challan_no = $row1["challan_no"];
        $customer_id = $row1["customer_id"];
        $dns_prod_code = $row1["dns_prod_code"];
        $prod_desc = $row1["prod_desc"];
        $allocation_qty = $row1["allocation_qty"];

        $target_ach_data[] = array("allocation_id" => $allocation_id, "customer_id" => $customer_id, "challan_no" => $challan_no, "prod_desc" => $prod_desc, "allocation_qty" => $allocation_qty, "dns_prod_code" => $dns_prod_code, "date_and_time" => $date_and_time);
    }
    // group by date_and_time sub string 1,10 and sum of allocation_qty
    $grouped_data = array();
    foreach ($target_ach_data as $data) {
        $date = substr($data["date_and_time"], 0, 10);
        if (!isset($grouped_data[$date])) {
            $grouped_data[$date] = array(
                "date" => $date,
                "total_qty" => 0,
                "customer_id" => $data["customer_id"],
                "prod_desc" => $data["prod_desc"],
                "dns_prod_code" => $data["dns_prod_code"],
            );
        }
        $grouped_data[$date]["total_qty"] += $data["allocation_qty"];
    }
    $target_ach_data = array_values($grouped_data);
    $res_data = array("process_status" => "YES", "process_message" => "Success.", "target_ach_data" => $target_ach_data);
} else {
    $res_data = array("process_status" => "NO", "process_message" => "No Records Found");
}

echo json_encode($res_data);
?>
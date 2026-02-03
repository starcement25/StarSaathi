<?php
include "star_connection.php";
$t_apperpdo_pop = "T_ORDER_POP";
$customer_master = "customer_master";
$track_pop_order_data = array();
$curr_date_time  = date("Y-m-d H:i:s");
$order_item_arr = array();
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$curr_date = date("Y-m-d");
$before_30_day_date = date('Y-m-d', strtotime("-30 days"));
$the_customer_code = isset($_REQUEST["customer_code"]) ? addslashes(trim($_REQUEST["customer_code"])) : "";
$year_month = isset($_REQUEST["year_month"]) && !empty(trim($_REQUEST["year_month"])) ? addslashes(trim($_REQUEST["year_month"])) : date("Y-m"); // Default to current year and month
$page_no = isset($_REQUEST["page_no"]) ? $_REQUEST["page_no"] : 1;
$limit = 10;
$start_from = (($page_no - 1) * $limit);

// Extract year and month from year_month parameter
list($year, $month) = explode('-', $year_month);

if ($the_customer_code == "") {
    $res_data = array("process_status" => "NO", "process_message" => "Something went wrong.", "track_pop_order_data" => $track_pop_order_data);
} else {
    $sql_cust = "SELECT `dns_customer_code` FROM $customer_master WHERE `customer_code` = '$the_customer_code'";
    $res_cust = mysql_query($sql_cust);
    $tot_res_cust = mysql_num_rows($res_cust);

    if ($tot_res_cust > 0) {
        // Construct the date filter part of the query based on year and month
        $date_filter = "AND YEAR(order_date) = '$year' AND MONTH(order_date) = '$month'";

        $sqlall2 = "SELECT $t_apperpdo_pop.*, $customer_master.`customer_name` 
                    FROM $t_apperpdo_pop 
                    INNER JOIN $customer_master ON $t_apperpdo_pop.`customer_code` = $customer_master.`customer_code`  
                    WHERE $t_apperpdo_pop.customer_code = '$the_customer_code' 
                    AND (`status` = 'Success' OR `status` = 'Shipped')  
                    $date_filter 
                    ORDER BY $t_apperpdo_pop.`order_date` DESC 
                    LIMIT $start_from, $limit";

				//echo $sqlall2;
        $resall2 = mysql_query($sqlall2);
        $totall2 = mysql_num_rows($resall2);

        if ($totall2 > 0) {
            while ($row112 = mysql_fetch_assoc($resall2)) {
                $main_order_id = isset($row112["the_order_id"]) ? trim($row112["the_order_id"]) : "";
                $order_id = isset($row112["APPORDERNO"]) ? trim($row112["APPORDERNO"]) : "";
                $order_date = isset($row112["order_date"]) ? trim($row112["order_date"]) : "";
                if ($order_date != "") {
                    $order_full_date_time = date("jS M Y h:i A", strtotime($order_date));
                } else {
                    $order_full_date_time = "";
                }

                $customer_name = isset($row112["customer_name"]) ? trim($row112["customer_name"]) : "";
                $address = isset($row112["address"]) ? trim($row112["address"]) : "";
                $pin = isset($row112["pin"]) ? trim($row112["pin"]) : "";
                $dns_prod_code = isset($row112["dns_prod_code"]) ? trim($row112["dns_prod_code"]) : "";
                $prod_display_name = isset($row112["prod_display_name"]) ? trim($row112["prod_display_name"]) : "";
                $qty = isset($row112["QTY"]) ? trim($row112["QTY"]) : "";
                $total_amount = isset($row112["prod_total_amount"]) ? trim($row112["prod_total_amount"]) : "";
                $order_status = isset($row112["order_status"]) ? trim($row112["order_status"]) : "";
                $pop_order_data[] = array("order_id" => $order_id, "order_date" => $order_full_date_time, "customer_name" => $customer_name, "address" => $address, "pin" => $pin, "dns_prod_code" => $dns_prod_code, "prod_display_name" => $prod_display_name, "qty" => $qty, "total_amount" => $total_amount, "image" => $prod_img_URL, "main_order_id" => $main_order_id, "order_status" => $order_status);
            }
            $res_data = array("process_status" => "YES", "process_message" => "Success.", "track_pop_order_data" => $pop_order_data);
        } else {
            $res_data = array("process_status" => "NO", "process_message" => "No new pop order found.");
        }
    } else {
        $res_data = array("process_status" => "NO", "process_message" => "Dealer record not found.", "track_pop_order_data" => $track_pop_order_data);
    }
}
echo json_encode($res_data);
if ($conn != "") {
    mysql_close($conn);
}
?>

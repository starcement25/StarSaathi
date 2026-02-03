<?php
include "star_connection.php"; 
$order_query = "order_query";

$response = array();
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";

$customer_code = $_REQUEST["customer_code"] ? addslashes(trim($_REQUEST["customer_code"])) : "";
$start_date = $_REQUEST["start_date"] ? addslashes(trim($_REQUEST["start_date"])) :"";
$end_date = $_REQUEST["end_date"] ? addslashes(trim($_REQUEST["end_date"])) : "";

$order_query_date = date("Y-m-d H:i:s");
$cur_year_month = date("Y-m");
list($year, $month) = explode('-', $cur_year_month);

if ($start_date != "" && $end_date != "") {
    $date_qry = " AND `date_and_time` between '".$start_date." ".$frm_hrs."' and '".$end_date." ".$to_hrs."'";
} else {
    $date_qry = " AND YEAR(date_and_time) = '$year' AND MONTH(date_and_time) = '$month'";
}

if ($customer_code == "") {
    $response["process_status"] = "NO";
    $response["process_message"] = "Customer Code is missing.";
} else {
    $sqlin = "SELECT id, date_and_time, order_id, customer_code, dns_prod_code, prod_name, qty_bags, query_date, date_of_lifting, remarks ,status_from_app, status_remarks
              FROM $order_query 
              WHERE linked_dealer_code='$customer_code' $date_qry 
              ORDER BY date_and_time DESC";

    $resin = mysql_query($sqlin);

    if (!$resin) {
        $response["process_status"] = "ERROR";
        $response["process_message"] = "Error: " . mysql_error();
    } else {
        if (mysql_num_rows($resin) > 0) {
            $response["process_status"] = "YES";
            $response["process_message"] = "Order Query fetched successfully.";
            $order_query_data = array();
            while ($row = mysql_fetch_assoc($resin)) {
                $id = $row['id'];
                $order_id = $row['order_id'];
                $customer_code = $row['customer_code'];
                $dns_prod_code = $row['dns_prod_code'];
                $prod_name = $row['prod_name'];
                $qty_bags = $row['qty_bags'];
                $date_and_time = $row['date_and_time'];
                $query_date = $row['query_date'];
                $date_of_lifting = $row['date_of_lifting'];
                $remarks = $row['remarks'];
                
                $status_from_app = $row['status_from_app'];
                $status_remarks = $row['status_remarks'];

                $sqlcustomer = "SELECT customer_name FROM customer_master WHERE customer_id='".$customer_code."'";
                $rscustomer = mysql_query($sqlcustomer);
                $rowcustomer = mysql_fetch_array($rscustomer);
                $rssd_name = $rowcustomer['customer_name'];
                
                if (is_null($status_from_app)) {
                    $status_from_app="";
                }
                if (is_null($status_remarks)) {
                    $status_remarks="";
                }

                $order_query_data[] = array(
                    "order_query_id" => $id,
                    "order_id" => $order_id,
                    "prod_name" => $prod_name,
                    "qty_bags" => $qty_bags,
                    "date_and_time" => $date_and_time,
                    "rssd_name" => $rssd_name,
                    "query_date" => $query_date,
                    "date_of_lifting" => $date_of_lifting,
                    "remarks" => $remarks,
                    "status_from_app" => $status_from_app,
                    "status_remarks" => $status_remarks
                );
            }

            $response["order_query_data"] = $order_query_data;
        } else {
            $response["process_status"] = "NO";
            $response["process_message"] = "No Order Query data found for the customer.";
        }
    }
}

echo json_encode($response);
?>

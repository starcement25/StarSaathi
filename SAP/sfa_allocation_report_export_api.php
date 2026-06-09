<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

//include "web_check.php";
include "star_connection.php";

ini_set('memory_limit', '512M');
set_time_limit(1000);
// Sanitize input
function getParam($key) {
    return isset($_REQUEST[$key]) ? addslashes(trim($_REQUEST[$key])) : "";
}

// Parameters
$sl_branch = getParam("sl_branch");
$srch_linked_dealer = getParam("srch_linked_dealer");
$srch_sub_dealer = getParam("srch_sub_dealer");
$month = getParam("month");
$emp_code = getParam("emp_code");
//echo"<pre>";print_r($emp_code);die;
//echo"<pre>";print_r($_REQUEST);die;
// Pagination
$page = isset($_REQUEST["page"]) ? max(1, intval($_REQUEST["page"])) : 1;
$limit = isset($_REQUEST["limit"]) ? max(1, intval($_REQUEST["limit"])) : 100;
$start_from = ($page - 1) * $limit;
// Determine if any filters are used
$hasSearch = $sl_branch || $srch_linked_dealer || $srch_sub_dealer || $month;
// Table names
$allocation_details = "allocation_details_invoicewise";
$branch_master = "branch_master";
$customer_master = "customer_master";
/*
// Step 1: Get lower_leaves
$sql = "SELECT lower_leaves FROM employee_master WHERE emp_code = '".$emp_code."' LIMIT 1";
$result = mysql_query($sql);

$lower_leaves = null;
if ($result && mysql_num_rows($result) > 0) {
    $row = mysql_fetch_assoc($result);
    $lower_leaves = $row["lower_leaves"];
}

// Step 2: Convert lower_leaves string to array
$lower_leaves_array = [];
if (!empty($lower_leaves)) {
    $lower_leaves_array = array_map(function($val) {
        return trim($val, "'");
    }, explode(",", $lower_leaves));
}

// Step 3: Build SQL IN clause from array
$in_clause = '';
if (!empty($lower_leaves_array)) {
    $in_clause = "'" . implode("','", $lower_leaves_array) . "'";
}

// Step 4: Run final query
$customer_code = [];
//echo "<pre>"; print_r($emp_code);die;
$sql3 = "SELECT customer_code FROM customer_route_emp_relation WHERE emp_code = '".$emp_code."' AND customer_code != ''";

if (!empty($in_clause)) {
    $sql3 .= " UNION SELECT customer_code FROM customer_route_emp_relation WHERE emp_code IN ($in_clause)";
}

$result3 = mysql_query($sql3);

if ($result3 && mysql_num_rows($result3) > 0) {
    while ($row3 = mysql_fetch_assoc($result3)) {
        $customer_code[] = $row3["customer_code"];
    }
}
*/
function getEmployeeFlatTree($managerCode, &$visited = []) {
    $employees = [];

    // Escape input
    $managerCodeEscaped = mysql_real_escape_string($managerCode);

    // If already visited, skip
    if (in_array($managerCodeEscaped, $visited)) {
        return [];
    }
    $visited[] = $managerCodeEscaped;

    // Get current employee info
    $selfSql = "SELECT emp_code, emp_name, dns_emp_code,level FROM employee_master WHERE emp_code = '$managerCodeEscaped' LIMIT 1";
    $selfResult = mysql_query($selfSql);
    if ($selfRow = mysql_fetch_assoc($selfResult)) {
        $employees[] = [
            'emp_code' => $selfRow['emp_code'],
            'emp_name' => $selfRow['emp_name'],
            'dns_emp_code' => $selfRow['dns_emp_code'],
            'level' => $selfRow['level'],
        ];
    }

    // Get direct reports
    $sql = "SELECT emp_code FROM employee_master WHERE reporting_to LIKE '%$managerCodeEscaped%' AND (level='L2' OR level='L1')";
    $result = mysql_query($sql);

    while ($row = mysql_fetch_assoc($result)) {
        $empCode = $row['emp_code'];
        $employees = array_merge($employees, getEmployeeFlatTree($empCode, $visited));
    }

    return $employees;
}

$tree = getEmployeeFlatTree($emp_code);
$empCodes = array_column($tree, 'emp_code');
$dns_emp_code = array_column($tree, 'dns_emp_code');

$in_clause = '';
if (!empty($empCodes)) {
    $in_clause = "'" . implode("','", $empCodes) . "'";
}

// Step 4: Run final query
$customer_code = [];
//echo "<pre>"; print_r($emp_code);die;
//$sql3 = "SELECT customer_code FROM customer_route_emp_relation WHERE emp_code = '".$emp_code."' AND customer_code != ''";
$sql3 = "SELECT customer_code FROM customer_route_emp_relation WHERE emp_code IN ($in_clause) AND customer_code != ''";
//echo "<pre>"; print_r($sql3);die;

/*
if (!empty($in_clause)) {
    $sql3 .= " UNION SELECT customer_code FROM customer_route_emp_relation WHERE emp_code IN ($in_clause)";
}*/

$result3 = mysql_query($sql3);

if ($result3 && mysql_num_rows($result3) > 0) {
    while ($row3 = mysql_fetch_assoc($result3)) {
        $customer_code[] = $row3["customer_code"];
    }
}
//echo"<pre>";print_r($customer_code);die;
$customer_id_in_clause = '';
if (!empty($customer_code)) {
    $customer_id_in_clause = "'" . implode("','", $customer_code) . "'";
}else{
    echo json_encode([
        'status' => 'success',
        'page' => '0',
        'limit' => '0',
        'total_records' => '0',
        'total_pages' => '0',
        'data' => ''
    ]); exit;
}
//echo "<pre>"; print_r($sql3);
// Output
//echo "<pre>"; print_r($customer_code); die;
// Build WHERE clause

$whr_str = "";
if (count($dns_emp_code) > 0) {
    $whr_str .= " AND $allocation_details.customer_id IN($customer_id_in_clause)";
}
if (count($customer_code) > 0) {
    $whr_str .= " AND $allocation_details.customer_id IN($customer_id_in_clause)";
}
if ($sl_branch != '') {
    $whr_str .= " AND $customer_master.branch_code = '$sl_branch'";
}
if ($srch_linked_dealer != '') {
    $whr_str .= " AND ($customer_master.customer_name LIKE '%$srch_linked_dealer%' OR $customer_master.customer_id LIKE '%$srch_linked_dealer%')";
}
if ($srch_sub_dealer != '') {
    $whr_str .= " AND ($allocation_details.sub_dealer_id LIKE '%$srch_sub_dealer%' OR $allocation_details.sub_dealer_id IN (SELECT `customer_id` FROM `customer_master` WHERE `customer_name` LIKE '%$srch_sub_dealer%' ))";
}
if ($month != '') {
    $whr_str .= " AND DATE_FORMAT($allocation_details.inv_date, '%Y-%m') = '$month'";
}
// Get total count for pagination
$count_sql = "SELECT COUNT(*) AS total FROM $allocation_details, $customer_master, $branch_master
              WHERE $allocation_details.customer_id = $customer_master.customer_id
              AND $customer_master.branch_code = $branch_master.branch_code
              $whr_str";
$count_result = mysql_query($count_sql);
$count_row = mysql_fetch_assoc($count_result);
$total_records = $count_row['total'];
//$total_pages = ceil($total_records / $limit);

// Initialize data

// SQL Query
if ($hasSearch) {
$sql = "SELECT
            $allocation_details.*,
            $allocation_details.allocation_qty AS total_allocation_qty,
            $customer_master.dns_customer_code,
           
            $customer_master.customer_name,
            $branch_master.branch_name
        FROM
            $allocation_details,
            $customer_master,
            $branch_master
        WHERE
            $allocation_details.customer_id = $customer_master.customer_id
            AND $customer_master.branch_code = $branch_master.branch_code
            $whr_str
        ORDER BY
            $allocation_details.customer_id ASC,
            $allocation_details.APPORDERNO ASC,
            $allocation_details.prod_desc ASC,
            $allocation_details.inv_date ASC,
            $allocation_details.date_and_time ASC

        ";
    //echo"<pre>";print_r($sql);
    $result = mysql_query($sql);
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Query failed', 'query' => $sql]);
        exit;
    }

    $data = [];
    //$c=1;
    //echo"<pre>";print_r(mysql_fetch_assoc($result));die;
    while ($row1 = mysql_fetch_assoc($result)) {
    //echo "s1 $c <br>";

        $APPORDERNO = isset($row1["APPORDERNO"]) ? trim($row1["APPORDERNO"]) : "";
        $date_and_time = isset($row1["date_and_time"]) ? trim($row1["date_and_time"]) : "";
        $linked_dealer_code = isset($row1["dns_customer_code"]) ? trim($row1["dns_customer_code"]) : "";
        $linked_dealer_sap_code = isset($row1["customer_id"]) ? trim($row1["customer_id"]) : "";
        $linked_dealer_name = isset($row1["customer_name"]) ? trim($row1["customer_name"]) : "";
        $sub_dealer_rssd_sap_code = isset($row1["sub_dealer_id"]) ? trim($row1["sub_dealer_id"]) : "";
        $allocation_qty = isset($row1["allocation_qty"]) ? trim($row1["allocation_qty"]) : "";
        // Get sub-dealer details
    //echo "s2 $c <br>";
        
        $sub_dealer_rssd_code = "";
        $sub_dealer_rssd_name = "";
        if ($sub_dealer_rssd_sap_code != "") {
             $sub_dealer_details = "SELECT dns_customer_code, customer_name FROM $customer_master WHERE customer_id = '$sub_dealer_rssd_sap_code'";
            $res_sub_dealer_details = mysql_query($sub_dealer_details);
            $row_sub_dealer_details = mysql_fetch_array($res_sub_dealer_details);
            $sub_dealer_rssd_code = isset($row_sub_dealer_details["dns_customer_code"]) ? trim($row_sub_dealer_details["dns_customer_code"]) : "";
            $sub_dealer_rssd_name = isset($row_sub_dealer_details["customer_name"]) ? trim($row_sub_dealer_details["customer_name"]) : "";
        }

        $branch = isset($row1["branch_name"]) ? trim($row1["branch_name"]) : "";
        $dns_prod_code = isset($row1["dns_prod_code"]) ? trim($row1["dns_prod_code"]) : "";
        $prod_display_name = isset($row1["prod_desc"]) ? trim($row1["prod_desc"]) : "";
        $inv_cancl = isset($row1["inv_cancl"]) ? trim($row1["inv_cancl"]) : "";
        $total_inv_qty = isset($row1["inv_qty"]) ? trim($row1["inv_qty"]) : "";
        $inv_date = isset($row1["inv_date"]) ? trim($row1["inv_date"]) : "";
        $inv_no = isset($row1["inv_no"]) ? trim($row1["inv_no"]) : "";
    //echo "s3 $c <br>";

        // Construct dynamic variable name
        $varKey = 'total_allocation_qty' . $linked_dealer_code . $APPORDERNO . $inv_no;
        if (!isset($$varKey)) {
            $$varKey = 0;
        }
        $$varKey += $row1["total_allocation_qty"];

        // Calculate remaining allocation
        $remaining_allocation_qty = $total_inv_qty - $$varKey;

        // Format month if inv_date exists
        $month_display = "";
        if ($inv_date != "") {
            $month_display = date("M-y", strtotime($inv_date));
        }
        // Append result // sk "total_allocation_qty" => $$varKey,
        // echo"<pre>";print_r($APPORDERNO);
        // echo"<pre>";print_r($sub_dealer_rssd_sap_code);
        // echo"<br>";
    // echo $APPORDERNO."<br>";
    // echo $date_and_time."<br>";
    // echo $linked_dealer_code."<br>";
    // echo $linked_dealer_sap_code."<br>";
    // echo $linked_dealer_name."<br>";
    // echo $sub_dealer_rssd_sap_code."<br>";
    // echo $sub_dealer_rssd_code."<br>";
    // echo $sub_dealer_rssd_name."<br>";
    // echo $branch."<br>";
    // echo $dns_prod_code."<br>";
    // echo $prod_display_name."<br>";
    // echo $inv_cancl."<br>";
    // echo $inv_date."<br>";
    // echo $inv_no."<br>";
    // echo $month_display."<br>";
    // echo $total_inv_qty."<br>";
    // echo $allocation_qty."<br>";
    // echo $remaining_allocation_qty."<br>";
    // echo "-----------------------------------<br>";
    //echo "s4 $c <br>";
        $data[] = [
            "APPORDERNO" => $APPORDERNO,
            "date_and_time" => $date_and_time,
            "linked_dealer_code" => $linked_dealer_code,
            "linked_dealer_sap_code" => $linked_dealer_sap_code,
            "linked_dealer_name" => $linked_dealer_name,
            "sub_dealer_rssd_sap_code" => $sub_dealer_rssd_sap_code,
            "sub_dealer_rssd_code" => $sub_dealer_rssd_code,
            "sub_dealer_rssd_name" => $sub_dealer_rssd_name,
            "branch" => $branch,
            "dns_prod_code" => $dns_prod_code,
            "prod_display_name" => $prod_display_name,
            "inv_cancl" => $inv_cancl,
            "inv_date" => $inv_date,
            "inv_no" => $inv_no,
            "month" => $month_display,
            "total_inv_qty" => $total_inv_qty,
            "total_allocation_qty" => $allocation_qty,
            "remaining_allocation_qty" => $remaining_allocation_qty
        ];
       // 
        //echo"<pre>";print_r($data);
       // echo "s5 $c <br>";
        //$c++;
     //echo "-----------------------------------<br>";

    }
    //echo"<pre>";print_r($data);die;

   /* echo json_encode([
        'status' => 'success',
        'data' => $data
    ]);*/
function utf8ize($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, "UTF-8", "UTF-8, ISO-8859-1, ISO-8859-15");
    }
    return $mixed;
}
$data = utf8ize($data);
$json = json_encode([
    'status' => 'success',
    'total_records' => $total_records,
    'data' => $data
], JSON_PRETTY_PRINT);

if ($json === false) {
    echo "JSON Error: " . json_last_error_msg();
} else {
    echo $json;
}
   /*echo json_encode([
        'status' => 'success',
        'total_records' => $total_records,
        'data' => $data
    ]); die;*/
}else{

    echo json_encode([
        'status' => 'idle',
        'data' => []
    ]);
}



// Output with pagination info
/*echo json_encode([
    'status' => 'success',
    'page' => $page,
    'limit' => $limit,
    'total_records' => $total_records,
    'total_pages' => $total_pages,
    'data' => $data
]);*/

?>

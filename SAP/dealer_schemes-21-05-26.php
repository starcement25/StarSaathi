<?php
header("Content-Type: application/json");

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

date_default_timezone_set('Asia/Kolkata');

/* =========================================================
   GET PARAMETERS
========================================================= */

$dealer_id = isset($_GET['dealer_id']) ? trim($_GET['dealer_id']) : "";

if ($dealer_id == "") {

    echo json_encode(array(
        "status"  => "error",
        "code"    => 400,
        "message" => "dealer_id is required"
    ));
    exit;
}

/* =========================================================
   GET CUSTOMER CODE FROM CUSTOMER ID
========================================================= */

$getCustomerSql = "SELECT customer_code 
                   FROM customer_master 
                   WHERE customer_id='$dealer_id'
                   LIMIT 1";

$getCustomerRes = mysql_query($getCustomerSql);

if (mysql_num_rows($getCustomerRes) == 0) {

    echo json_encode(array(
        "status"  => "error",
        "code"    => 404,
        "message" => "Dealer not found"
    ));
    exit;
}

$getCustomerRow = mysql_fetch_array($getCustomerRes);

$customer_code = trim($getCustomerRow['customer_code']);

/* =========================================================
   GET BRANCH CODE
========================================================= */

$sqlempbranch = "SELECT branch_code 
                 FROM customer_master 
                 WHERE customer_code='$customer_code'";

$rsempbranch = mysql_query($sqlempbranch);

$tagged_cust_branch_arr = array();

if (mysql_num_rows($rsempbranch) > 0) {

    $rowempbranch = mysql_fetch_array($rsempbranch);

    $branch_code = $rowempbranch['branch_code'];

    if ($branch_code != "") {

        $tagged_cust_branch_arr = explode(',', $branch_code);
    }
}

/* =========================================================
   REMOVE DUPLICATES
========================================================= */

$tagged_cust_branch_arr = array_unique($tagged_cust_branch_arr);

$scheme_data = array();

if (count($tagged_cust_branch_arr) > 0) {

    $branch_value_final = "'" . implode("','", $tagged_cust_branch_arr) . "'";

    $condition_branch = " AND branch_code IN ($branch_value_final)";

    /* =========================================================
       FETCH SCHEMES
    ========================================================= */

    $sqlquery = "SELECT *
                 FROM branch_schemes_PDF
                 WHERE acedns='Y'
                 $condition_branch
                 AND CURDATE() BETWEEN start_date AND end_date
                 ORDER BY download_time DESC";

    $result = mysql_query($sqlquery);

    if (!$result) {

        echo json_encode(array(
            "status"  => "error",
            "code"    => 500,
            "message" => mysql_error()
        ));
        exit;
    }

    while ($row = mysql_fetch_array($result)) {

        $scheme_id = isset($row['id']) ? $row['id'] : "";

        $pdf_file = isset($row['PDF_file_name']) ? $row['PDF_file_name'] : "";

        $start_date = isset($row['start_date']) ? $row['start_date'] : "";

        $end_date = isset($row['end_date']) ? $row['end_date'] : "";

        $today = date('Y-m-d');

        $days_remaining = 0;

        if (!empty($end_date)) {

            $diff = strtotime($end_date) - strtotime($today);

            $days_remaining = floor($diff / (60 * 60 * 24));
        }

        $scheme_data[] = array(
            "scheme_id"      => (string)$scheme_id,
            "branch_code"    => $row['branch_code'],
            "scheme_name"    => 'Scheme 1',
            "pdf_file"       => $pdf_file,
            "pdf_url"        => "../schemes/" . $pdf_file,
            "start_date"     => $start_date,
            "end_date"       => $end_date,
            "days_remaining" => (int)$days_remaining
        );
    }
}

/* =========================================================
   FINAL RESPONSE
========================================================= */

echo json_encode(array(
    "status"  => "success",
    "code"    => 200,
    "message" => "Data retrieved successfully",
    "data"    => array(
        "dealer_id"     => $dealer_id,
        "customer_code" => $customer_code,
        "total_schemes" => count($scheme_data),
        "schemes"       => $scheme_data
    )
));
?>
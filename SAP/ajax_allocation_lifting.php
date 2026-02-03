<?php
include "star_connection.php"; // Include database connection
include "function-sfa.php";

// Assuming these are your table names
$allocation_details = "allocation_details";
$customer_master = "customer_master";

// Retrieve the customer ID from the POST request
$the_sap_code = isset($_POST["customer_id"]) ? addslashes(trim($_POST["customer_id"])) : "";

// Initialize an empty response array
$response = array();

if ($the_sap_code == "") {
    $response["process_status"] = "NO";
    $response["process_message"] = "Something went wrong.";
} else {
    // Check if the customer ID exists in the database
    $sql_cust = "SELECT `customer_id` FROM $customer_master WHERE `customer_id`='$the_sap_code'";
    $res_cust = mysqli_query($conn, $sql_cust);
    $tot_res_cust = mysqli_num_rows($res_cust);

    if ($tot_res_cust > 0) {
        // Customer ID exists, proceed to fetch allocation data
        $sql_alloc = "SELECT dns_prod_code, prod_desc, allocation_qty FROM $allocation_details WHERE customer_id = $the_sap_code";
        $res_alloc = mysqli_query($conn, $sql_alloc);

        if ($res_alloc) {
            // Fetch allocation data and store in response array
            $response["process_status"] = "YES";
            $response["process_message"] = "Allocation data fetched successfully.";
            while ($row = mysqli_fetch_assoc($res_alloc)) {
                $response["allocation_data"][] = $row;
            }
        } else {
            $response["process_status"] = "NO";
            $response["process_message"] = "Failed to fetch allocation data.";
        }
    } else {
        $response["process_status"] = "NO";
        $response["process_message"] = "Customer not found.";
    }
}

// Return the JSON encoded response
echo json_encode($response);
?>

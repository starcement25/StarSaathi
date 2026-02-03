

<?php
include "star_connection.php"; 

$allocation_details = "allocation_details";

$response = array();

$customer_id = $_REQUEST["customer_id"] ? addslashes(trim($_REQUEST["customer_id"])) : "";

if ($customer_id == "") {
    $response["process_status"] = "NO";
    $response["process_message"] = "Customer ID is missing.";
} else {

    
    //for testing

    $sqlin = "SELECT allocation_id, dns_prod_code, prod_desc, allocation_qty, date_and_time, order_id FROM $allocation_details WHERE customer_id = '$customer_id' order by date_and_time desc";


    //echo
//     SELECT allocation_id, dns_prod_code, prod_desc, allocation_qty, date_and_time, order_id 
// FROM allocation_details 
// WHERE customer_id = '1000000341' 
// ORDER BY date_and_time DESC;

    $resin = mysql_query($sqlin);

    if (!$resin) {
       
        $response["process_status"] = "ERROR";
        $response["process_message"] = "Error: " . mysql_error();
    } else {
       
        if (mysql_num_rows($resin) > 0) {
            
            $response["process_status"] = "YES";
            $response["process_message"] = "Allocation details fetched successfully.";

            
            $allocation_data = array();
            
            while ($row = mysql_fetch_assoc($resin)) {
              
                $allocation_data[] = $row;
            }

           
            $response["allocation_data"] = $allocation_data;
        } else {
           
            $response["process_status"] = "NO";
            $response["process_message"] = "No allocation data found for the customer.";
        }
    }
}


echo json_encode($response);
?>

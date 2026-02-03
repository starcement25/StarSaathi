<?php
header('Content-Type: application/json');
include "star_connection.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Handle contact number validation
    if (isset($input['phone_no']) || isset($input['whatsapp_no'])) {
        $phoneNumber = isset($input['phone_no']) ? $input['phone_no'] : null;
        $whatsappNumber = isset($input['whatsapp_no']) ? $input['whatsapp_no'] : null;
        
        $response = validateContactNumbers($phoneNumber, $whatsappNumber);
        echo json_encode($response);
        exit;
    }
    
    // Handle customer details fetching
    if (isset($input['emp_code'])) {
        $emp_code = strtolower(trim($input['emp_code']));
        $user_type = isset($input['user_type']) ? strtolower(trim($input['user_type'])) : "";
        
        $response = fetchCustomerDetails($emp_code, $user_type);
        echo json_encode($response);
        exit;
    }

    echo json_encode(['error' => 'Invalid request parameters']);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

function validateContactNumbers($phoneNumber, $whatsappNumber) {
    $customer_master = "customer_master";
    $response = [];

    // Check if the phone number is registered
    if ($phoneNumber) {
        $sql = "SELECT `customer_id`, `phone_no`, `whatsapp_no`, `cust_type` FROM `$customer_master` WHERE `phone_no` = '" . mysql_real_escape_string($phoneNumber) . "'";
        $result = mysql_query($sql);

        if ($result && mysql_num_rows($result) > 0) {
            $row = mysql_fetch_assoc($result);
            $response['phone'] = [
                'status' => 'success',
                'message' => 'Phone number validated',
                'data' => [
                    'customer_id' => $row['customer_id'],
                    'phone_no' => $row['phone_no'],
                    'whatsapp_no' => $row['whatsapp_no'],
                    'cust_type' => $row['cust_type']
                ]
            ];
        } else {
            $response['phone'] = [
                'status' => 'failure',
                'message' => 'Phone number not found'
            ];
        }
    }

    // Check if the WhatsApp number is registered
    if ($whatsappNumber) {
        $sql = "SELECT `customer_id`, `phone_no`, `whatsapp_no`, `cust_type` FROM `$customer_master` WHERE `whatsapp_no` = '" . mysql_real_escape_string($whatsappNumber) . "'";
        $result = mysql_query($sql);

        if ($result && mysql_num_rows($result) > 0) {
            $row = mysql_fetch_assoc($result);
            $response['whatsapp'] = [
                'status' => 'success',
                'message' => 'WhatsApp number validated',
                'data' => [
                    'customer_id' => $row['customer_id'],
                    'phone_no' => $row['phone_no'],
                    'whatsapp_no' => $row['whatsapp_no'],
                    'cust_type' => $row['cust_type']
                ]
            ];
        } else {
            $response['whatsapp'] = [
                'status' => 'failure',
                'message' => 'WhatsApp number not found'
            ];
        }
    }

    return $response;
}

function fetchCustomerDetails($emp_code, $user_type) {
    $customer_master = "customer_master";

    // Query to fetch customer details
    $sql = "SELECT `customer_id`, `phone_no`, `whatsapp_no`, `cust_type` FROM `$customer_master` WHERE `customer_id` = '" . mysql_real_escape_string($emp_code) . "' AND `cust_type` = '" . mysql_real_escape_string($user_type) . "'";
    $result = mysql_query($sql);

    if (!$result) {
        return ["process_status" => "NO", "process_message" => "Error fetching customer details: " . mysql_error()];
    }

    if (mysql_num_rows($result) > 0) {
        $customer = mysql_fetch_assoc($result);
        return ["process_status" => "YES", "data" => $customer];
    } else {
        return ["process_status" => "NO", "process_message" => "No record found"];
    }
}
?>

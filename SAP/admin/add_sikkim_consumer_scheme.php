<?php 
include "star_connection.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $raw_data = file_get_contents("php://input");
    $data = json_decode($raw_data, true); // decode JSON into assoc array

    $has_coupon = isset($data['has_coupon']) ? trim($data['has_coupon']) : '';
    $bags_quantity = isset($data['bags_quantity']) ? intval($data['bags_quantity']) : 0;
    $date_of_purchase = isset($data['date_of_purchase']) ? $data['date_of_purchase'] : '';
    $house_owner_name = isset($data['house_owner_name']) ? trim($data['house_owner_name']) : '';
    $house_owner_phone = isset($data['house_owner_phone']) ? trim($data['house_owner_phone']) : '';
    $coupons_details = isset($data['coupons_details']) ? $data['coupons_details'] : [];

    $errors = [];

    // ✅ Combined basic validations
    if (
        !in_array($has_coupon, ['Yes', 'No']) ||
        !is_numeric($bags_quantity) || $bags_quantity <= 0 ||
        !is_numeric($date_of_purchase) ||
        empty($house_owner_name) ||
        empty($house_owner_phone) ||
        ($has_coupon === 'Yes' && (!is_array($coupons_details) || empty($coupons_details)))
    ) {
        if (!in_array($has_coupon, ['Yes', 'No'])) {
            $errors[] = ['has_coupon' => "Coupon must be either 'Yes' or 'No'."];
        }

        if (!is_numeric($bags_quantity) || $bags_quantity <= 0) {
            $errors[] = ['bags_quantity' => "Bags quantity must be a positive number."];
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_of_purchase)) {
            $errors[] = ['date_of_purchase' => "Date of purchase must be in format YYYY-MM-DD."];
        } else {
            // Optional: check if it's a real date
            $parts = explode('-', $date_of_purchase);
            if (!checkdate(intval($parts[1]), intval($parts[2]), intval($parts[0]))) {
                $errors[] = ['date_of_purchase' => "Date of purchase is not a valid calendar date."];
            }
        }


        if (empty($house_owner_name)) {
            $errors[] = ['house_owner_name' => "House owner name is required."];
        }

        if (empty($house_owner_phone)) {
            $errors[] = ['house_owner_phone' => "House owner phone is required."];
        }

        // if ($has_coupon === 'Yes') {
        //     if (!is_array($coupons_details) || empty($coupons_details)) {
        //         $errors[] = ['coupons_details' => "Coupons details must be provided as an object when has_coupon is 'Yes'."];
        //     }
        // }

        // // ✅ Check for repeated values
        // $unique_coupons = array_unique($coupons_details);
        // if (count($unique_coupons) !== count($coupons_details)) {
        //     $errors[] = ['coupons_details' => "Duplicate coupon values are not allowed in coupons_details."];
        // }
        if ($has_coupon === 'Yes') {
            if (!is_array($coupons_details) || empty($coupons_details)) {
                $errors[] = ['coupons_details' => "Coupons details must be provided as object or array when has_coupon is 'Yes'."];
            } else {
                $coupon_values = [];

                // Detect associative array (object) vs numeric array
                if (array_keys($coupons_details) !== range(0, count($coupons_details) - 1)) {
                    // object format → take values
                    $coupon_values = array_values($coupons_details);
                } else {
                    // numeric array → extract values from each object
                    foreach ($coupons_details as $item) {
                        if (is_array($item)) {
                            foreach ($item as $code) {
                                $coupon_values[] = $code;
                            }
                        }
                    }
                }

                // Remove empty coupon codes
                $coupon_values = array_filter($coupon_values, function($v) { return !empty($v); });

                // 1) Check internal duplicates
                $unique_coupons = array_unique($coupon_values);
                if (count($unique_coupons) !== count($coupon_values)) {
                    $errors[] = ['coupons_details' => "Duplicate coupon values are not allowed in coupons_details."];
                }
                
                // 2) Check minimum bags required
                $required_bags = count($coupon_values) * 100;
                if ($bags_quantity < $required_bags) {
                    $errors[] = ['bags_quantity' => "Minimum bags required is $required_bags (number of coupon codes × 100)."];
                }

                // 3) Check in DB for existing codes
                $existing_codes = [];
                if (!empty($coupon_values)) {
                    foreach ($coupon_values as $code) {
                        $safe_code = mysql_real_escape_string($code);

                        $query = "SELECT COUNT(*) as cnt FROM sikkim_consumer_scheme WHERE coupons_details LIKE '%$safe_code%'";
                        $result = mysql_query($query);
                        $row = mysql_fetch_assoc($result);

                        if ($row && $row['cnt'] > 0) {
                            $existing_codes[] = $code;
                        }
                    }

                    if (!empty($existing_codes)) {
                        $codes_str = implode(', ', $existing_codes);
                        $errors[] = ['coupons_details' => "These coupon codes already exist in database: $codes_str"];
                    }
                }

            }
        }
    }

    // ✅ Output if any error
    if (!empty($errors)) {
        http_response_code(422);
        echo json_encode(['status' => 'error', 'errors' => $errors]);
        exit;
    }else{
        $customer_id        = mysql_real_escape_string($data['customer_id']);
        $house_owner_name   = mysql_real_escape_string($data['house_owner_name']);
        $house_owner_phone  = mysql_real_escape_string($data['house_owner_phone']);
        // $date_of_purchase_ts = isset($data['date_of_purchase']) ? intval($data['date_of_purchase']) : 0;
        $date_of_purchase = mysql_real_escape_string($data['date_of_purchase']);
        $bags_quantity      = intval($data['bags_quantity']);
        $has_coupon_raw = isset($data['has_coupon']) ? trim($data['has_coupon']) : 'No';
        $has_coupon = ($has_coupon_raw === 'Yes') ? 'Yes' : 'No';
        
        // ✅ Convert associative array into JSON string
        $coupons_details = '';
        if ($has_coupon === 'Yes' && isset($data['coupons_details']) && is_array($data['coupons_details'])) {
            $coupons_details = mysql_real_escape_string(json_encode($data['coupons_details']));
        }
        date_default_timezone_set('Asia/Kolkata');
        $create_date = date('Y-m-d H:i:s');

        // Insert query
        $query = "INSERT INTO sikkim_consumer_scheme ( customer_id, house_owner_name, house_owner_phone, date_of_purchase, bags_quantity, has_coupon, coupons_details, date_and_time) VALUES ( '$customer_id', '$house_owner_name', '$house_owner_phone', '$date_of_purchase', '$bags_quantity', '$has_coupon', " . ($coupons_details !== '' ? "'$coupons_details'" : "NULL") . ", '$create_date')";

        // Execute query    
        $result = mysql_query($query);

        if ($result) {
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Data inserted successfully.']);
            exit;
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database insert failed: ' . mysql_error()]);
            exit;
        }
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

?>
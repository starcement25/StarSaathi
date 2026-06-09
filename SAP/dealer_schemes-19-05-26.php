<?php
/**
 * Alternative endpoint with better error handling
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include "star_connection.php";

$response = array(
    'status' => 'error',
    'code' => 400,
    'message' => '',
    'data' => null,
    'timestamp' => date('Y-m-d H:i:s')
);

try {
    // Get request data
    $input = file_get_contents('php://input');
    $request = json_decode($input, true);
    
    // Check for GET parameters
    if (empty($request)) {
        $request = $_GET;
    }
    
    // Check for POST parameters
    if (empty($request)) {
        $request = $_POST;
    }
    
    // Validate dealer_id
    if (!isset($request['dealer_id']) || empty($request['dealer_id'])) {
        throw new Exception('dealer_id is required', 400);
    }
    
    $dealer_id = mysql_real_escape_string(trim($request['dealer_id']));
    $status = isset($request['status']) ? $request['status'] : 'active';
    $date = isset($request['date']) ? $request['date'] : date('Y-m-d');
    
    // Validate dealer exists
    $dealer_check = "SELECT COUNT(*) as dealer_count 
                     FROM customer_master 
                     WHERE customer_id = '$dealer_id'";
    $check_result = mysql_query($dealer_check);
    $check_data = mysql_fetch_assoc($check_result);
    
    if ($check_data['dealer_count'] == 0) {
        throw new Exception('Dealer not found in system', 404);
    }
    
    // Build query based on status
    $where_conditions = array();
    $where_conditions[] = "sd.customer_id = '$dealer_id'";
    
    switch($status) {
        case 'active':
            $where_conditions[] = "CURDATE() BETWEEN bs.start_date AND bs.end_date";
            break;
        case 'upcoming':
            $where_conditions[] = "bs.start_date > CURDATE()";
            break;
        case 'expired':
            $where_conditions[] = "bs.end_date < CURDATE()";
            break;
        case 'all':
            // No date restriction
            break;
        default:
            throw new Exception('Invalid status. Use: active, upcoming, expired, or all', 400);
    }
    
    $where_clause = "WHERE " . implode(" AND ", $where_conditions);
    
    // Execute query
    $query = "SELECT 
                bs.sl_no,
                bs.scheme_name,
                bs.PDF_file_name,
                bs.start_date,
                bs.end_date,
                bs.lifting_start_dt,
                bs.lifting_end_dt,
                bs.slab_type,
                bs.single_min_qty,
                bs.single_applicable_qty,
                bs.incentive_type,
                bs.monthly_incentive,
                bs.download_time,
                DATEDIFF(bs.end_date, CURDATE()) as days_left,
                CASE 
                    WHEN CURDATE() < bs.start_date THEN 'upcoming'
                    WHEN CURDATE() BETWEEN bs.start_date AND bs.end_date THEN 'active'
                    ELSE 'expired'
                END as current_status
            FROM scheme_dealers sd
            INNER JOIN branch_schemes_PDF bs ON sd.scheme_id = bs.sl_no
            $where_clause
            ORDER BY bs.start_date DESC";
    
    $result = mysql_query($query);
    
    if (!$result) {
        throw new Exception('Database query failed: ' . mysql_error(), 500);
    }
    
    $schemes = array();
    while ($row = mysql_fetch_assoc($result)) {
        // Get slab details
        $slab_details = array();
        if ($row['slab_type'] == 'Single') {
            $slab_details = array(
                'type' => 'single',
                'min_qty' => floatval($row['single_min_qty']),
                'applicable_qty' => floatval($row['single_applicable_qty']),
                'incentive_type' => $row['incentive_type'],
                'monthly_incentive' => floatval($row['monthly_incentive'])
            );
        } else {
            $slab_query = "SELECT * FROM scheme_slabs 
                          WHERE scheme_id = '{$row['sl_no']}' 
                          ORDER BY lifting_min ASC";
            $slab_result = mysql_query($slab_query);
            $slabs = array();
            
            while ($slab = mysql_fetch_assoc($slab_result)) {
                $slabs[] = array(
                    'category_name' => $slab['category_name'],
                    'lifting_min' => floatval($slab['lifting_min']),
                    'lifting_max' => floatval($slab['lifting_max']),
                    'monthly_incentive' => floatval($slab['monthly_incentive']),
                    'incentive_type' => $slab['monthly_incentive_type']
                );
            }
            
            $slab_details = array(
                'type' => 'multiple',
                'slabs' => $slabs
            );
        }
        
        // Get achievements/calculations
        $achievements = array();
        $calc_query = "SELECT 
                        calculation_date,
                        product_name,
                        lifting_qty,
                        incentive_amount,
                        incentive_type,
                        slab_category
                    FROM scheme_calculation_results
                    WHERE scheme_id = '{$row['sl_no']}'
                    AND customer_id = '$dealer_id'
                    ORDER BY calculation_date DESC
                    LIMIT 10";
        
        $calc_result = mysql_query($calc_query);
        while ($calc = mysql_fetch_assoc($calc_result)) {
            $achievements[] = array(
                'date' => $calc['calculation_date'],
                'product' => $calc['product_name'],
                'lifting_qty' => floatval($calc['lifting_qty']),
                'incentive' => floatval($calc['incentive_amount']),
                'type' => $calc['incentive_type'],
                'slab_category' => $calc['slab_category']
            );
        }
        
        $schemes[] = array(
            'scheme_id' => $row['sl_no'],
            'scheme_name' => $row['scheme_name'],
            'pdf_file' => $row['PDF_file_name'],
            'pdf_url' => '../schemes/' . $row['PDF_file_name'],
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date'],
            'lifting_start' => $row['lifting_start_dt'],
            'lifting_end' => $row['lifting_end_dt'],
            'category' => $row['slab_type'],
            'status' => $row['current_status'],
            'days_remaining' => intval($row['days_left']),
            'created_at' => $row['download_time'],
            'slab_details' => $slab_details,
            'achievements' => $achievements
        );
    }
    
    // Prepare success response
    $response['status'] = 'success';
    $response['code'] = 200;
    $response['message'] = 'Data retrieved successfully';
    $response['data'] = array(
        'dealer_id' => $dealer_id,
        'total_schemes' => count($schemes),
        'schemes' => $schemes,
        'filters' => array(
            'status' => $status,
            'date' => $date
        )
    );
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    $response['code'] = $e->getCode() ?: 500;
}

mysql_close();
echo json_encode($response);
exit;
?>
<?php
// Set response type to JSON
header('Content-Type: application/json');

// Allow only GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Only GET requests are allowed']);
    exit;
}

// Get parameters from the query string
$status = 'start'; //['start', 'stop']
$message = "Star Saathi App is temporarily unavailable due to server downtime.
Please click this link to place orders";
$link="https://dev.starsaathi.com/SAP/downtime/";
$is_link_available='Y';

// Validate input
if (!$status || !$message) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
    exit;
}

if (!in_array($status, ['start', 'stop'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid status value (use start or stop)']);
    exit;
}
if($status=='start'){
   $message=''; 
   $link=''; 
   $is_link_available='';
}
// Response
$response = [
    'status' => 'success',
    'app_status' => $status,
    'body_message' => $message,
    'is_link_available'=>$is_link_available,
    'body_link' => $link
];

echo json_encode($response);
?>

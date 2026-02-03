<?php
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;
// use PHPMailer\PHPMailer\Exception;
header('Content-Type: application/json');

date_default_timezone_set('Asia/Kolkata');
require_once("starsaathi_connection.php");
$localDB = new starsaathi_connection();
$conn = $localDB->conn;
//$conn = mysql_connect("172.17.0.2", "root", "Passw0rd123#$");
mysql_select_db("starsaathi_STARS", $conn);


function send_the_mail($to_email, $subject, $bodyml)
{
    error_reporting(E_STRICT);
    set_time_limit(0);
    date_default_timezone_set("Asia/Kolkata");
    require_once('class.phpmailer.php');
    require_once('class.smtp.php');
    $sts = "FALSE";
    $to_email_arr = array();
   // $to_email = $to_email ? trim($to_email) : "";
   $to_email = "test@yopmail.com";
    $subject = $subject ? trim($subject) : "";
    $bodyml = $bodyml ? trim($bodyml) : "";
    if ($to_email != "" && $subject != "" && $bodyml != "") {
        $to_email_arr = explode(",", $to_email);
        if (count($to_email_arr) > 0) {
            $mail             = new PHPMailer();
            $bodyml             = $bodyml;
            //$bodyml             = eregi_replace("[\]",'',$bodyml);
            $mail->IsSMTP(); // telling the class to use SMTP
            $mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)                                       // 2 = messages only
            $mail->SMTPAuth   = true;                  // enable SMTP authentication                // sets the prefix to the servier
            $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server (For gmail "mail.coral.in")     // sets GMAIL as the SMTP server (For gmail "mail.coral.in")
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;                  // set the SMTP port for the GMAIL server (For gmail 465 )
            $mail->Username   = 'test.sbinfowaves@gmail.com';
            $mail->Password   = 'dzltchhdafyfnqhh';
            $mail->SetFrom('starsaathi@starcement.co.in', 'Starsaathi');
            $mail->Subject    = $subject;
            $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
            $mail->MsgHTML($bodyml);
            foreach ($to_email_arr as $to_email_arr_val) {
                if (trim($to_email_arr_val) != "") {
                    if (filter_var(trim($to_email_arr_val), FILTER_VALIDATE_EMAIL)) {
                        $mail->AddAddress(trim($to_email_arr_val), $to_email_arr_val);
                    }
                }
            }

            $mlsts = $mail->Send();
            if (!$mlsts) {
                $sts = "FALSE";
            } else {
                $sts = "TRUE";
            }
        }
    }
    return $sts;
}





// /////


$created_at = date("Y-m-d H:i:s");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => 'Only POST method is allowed');
    echo json_encode($res_data);
    //echo json_encode(["error" => "Only POST method is allowed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => 'Invalid JSON input');
    echo json_encode($res_data);

    exit;
}
$required = ['dealer_id', 'emp_code', 'status', 'table_id'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => "$field is required");
        echo json_encode($res_data);

        exit;
    }
}

$dealer_id = mysql_real_escape_string($data['dealer_id']);
$emp_code = mysql_real_escape_string($data['emp_code']);
//$status = (int)mysql_real_escape_string($data['status']);
$id = mysql_real_escape_string($data['table_id']);
$input_status = strtolower(trim($data['status'])); 
$reason = strtolower(trim($data['reason'])); 
$status_map = [
    'approve' => 1,
    'approved' => 1,
    'reject' => 2,
    'rejected' => 2,
    'pending' => 0
];

if (!isset($status_map[$input_status])) {
    http_response_code(400);
    $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => "Invalid status value. Allowed: Approve, Reject, Pending");
    echo json_encode($res_data);
    exit;
}

if ($status_map[$input_status] == 2 && empty($reason)) {
    http_response_code(400);
    $res_data = array(
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Reason for rejection is required!"
    );
    echo json_encode($res_data);
    exit;
}

$status = $status_map[$input_status];



$emp_sql = "SELECT * FROM employee_master WHERE emp_code = '$emp_code'";
$emp_res = mysql_query($emp_sql);
$row = mysql_fetch_assoc($emp_res);
// echo '<pre>';
// print_r($row);
//  die;
$dealer_ex = "SELECT * FROM dealer_exclusice WHERE id = '$id'";
$deal = mysql_query($dealer_ex);
$row_deal = mysql_fetch_assoc($deal);
$customer_id = $row_deal['customer_id'];
// echo '<pre>';
// print_r($row_deal);
//  die;
$customer_sql = "SELECT * FROM customer_master WHERE customer_id = '$customer_id'";
$customer_result = mysql_query($customer_sql);
$row_cus = mysql_fetch_assoc($customer_result);
$cus_name=ucwords(strtolower($row_cus['customer_name']));
$reply = '';
if ($status == 1) {
    $reply = 'Exclusive Dealer request is approved successfully';
    $to_email = $row_cus['email'];

    //$to_email = "souvik.pal@sbinfowaves.in";
    $subject = "Dealer Exclusive Request Approval";
    $bodyml = "
<html>
<head>
  <style>
    body { font-family: Arial, sans-serif; font-size: 14px; color: #333; }
    .content { padding: 10px; background: #f9f9f9; border: 1px solid #ddd; }
  </style>
</head>
<body>
  <p>Hi {$cus_name},</p>

  <div class='content'>
    <p>Your request for <strong>Exclusive Dealership</strong> has been <strong>approved</strong> by <strong>{$row['emp_name']}</strong>.</p>
  </div>

  <p>Best regards,<br/>
  Star Saathi Team</p>
</body>
</html>
";


   
} else {
    $reply = 'Exclusive Dealer request has been rejected successfully';

    //$to_email = "souvik.pal@sbinfowaves.in";
     $to_email = $row_cus['email'];
    $subject = "Dealer Exclusive Request Rejection";
    //$bodyml = "Your Request for Exclusive dealeship has been rejected,Please contact Starsathi Team for further details. ";
     $bodyml = "
<html>
<head>
  <style>
    body { font-family: Arial, sans-serif; font-size: 14px; color: #333; }
    .content { padding: 10px; background: #f9f9f9; border: 1px solid #ddd; }
  </style>
</head>
<body>
  <p>Hi {$cus_name},</p>

  <div class='content'>
    <p>Your request for <strong>Exclusive Dealership</strong> has been <strong>rejected</strong> .</p>
  </div>

  <p>Best regards,<br/>
  Star Saathi Team</p>
</body>
</html>
";
}

if ($row['level'] == 'L3' && (int)$row_deal['asm_approve_status'] == 0) {
    
    if ($status == 2) {
      
        $update_sql = "UPDATE dealer_exclusice 
                       SET asm_approve_status = 2,
                           asm_approved_by = '$emp_code',
                           asm_approve_date = '$created_at',
                           status = 2,
                           reject_reason = '$reason'
                       WHERE id = '$id'";
    } else {
      
        $update_sql = "UPDATE dealer_exclusice 
                       SET asm_approve_status = 1,
                           asm_approved_by = '$emp_code',
                           asm_approve_date = '$created_at'
                       WHERE id = '$id'";
    }

    if (mysql_query($update_sql)) {
        $res_data = array("process_status" => "Yes", "process_message" => "Success!", 'message' => $reply);
        // send_the_mail($to_email, $subject, $bodyml); // uncomment if needed
    } else {
        $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => mysql_error());
    }

    echo json_encode($res_data);
    exit;
} elseif ($row['level'] == 'L4' && $row_deal['asm_approve_status'] == 1 && $row_deal['rsm_approve_status'] == 0) {
if($status==1){
     $update_sql = "UPDATE dealer_exclusice 
                   SET rsm_approve_status = '$status',
                       rsm_approved_by = '$emp_code',
                       rsm_approve_date = '$created_at', 
                       status = 1
                   WHERE id = '$id'";
}else{
 $update_sql = "UPDATE dealer_exclusice 
                   SET rsm_approve_status = '$status',
                       rsm_approved_by = '$emp_code',
                       rsm_approve_date = '$created_at' ,
                        reject_reason = '$reason',
                       status = 2
                   WHERE id = '$id'";
                   
}
   

    if (mysql_query($update_sql)) {
        $res_data = array("process_status" => "Yes", "process_message" => "Success!", 'message' => $reply);
       // $resml = send_the_mail($to_email, $subject, $bodyml);
    } else {
        $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => mysql_error());
    }

    echo json_encode($res_data);
    exit;
} elseif ($row['level'] == 'L4' && $row_deal['asm_approve_status'] == 0) {

    $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => 'Need to be approved by Employee level L3!');
    echo json_encode($res_data);
    exit;
} else {

    $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => 'Already response has been stored! ');
    echo json_encode($res_data);
    exit;
}

// if (mysql_query($update_sql)) {
//         $res_data = array("process_status" => "Yes", "process_message" => "Success!", 'message' => 'Exclusive Dealer request approved successfully');
//     echo json_encode($res_data);
//     } else {
//          $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => "Error: " . mysql_error());
//     echo json_encode($res_data);

//     }




?>
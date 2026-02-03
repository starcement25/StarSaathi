<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
date_default_timezone_set('Asia/Kolkata');

require_once("starsaathi_connection.php");
$localDB = new starsaathi_connection();
$conn = $localDB->conn;
$default_email='';
if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

mysql_select_db("starsaathi_STARS", $conn);

$results = [];
$errorLog = [];
 $todayDay = date('j'); // 1–31

if ($todayDay == 1) {
  
    $startDate = date("Y-m-01", strtotime("first day of previous month"));
    $endDate   = date("Y-m-t", strtotime("last day of previous month"));
} else {
    
    $startDate = date("Y-m-01");
    $endDate   = date("Y-m-d", strtotime("-1 day"));
}
// Step 1: Get sub-dealers with pending allocations
$dealerQuery = "
    SELECT DISTINCT 
        adi.sub_dealer_id,
        cm.email AS email,
        cm.customer_name
    FROM allocation_details_invoicewise adi
    INNER JOIN customer_master cm ON cm.customer_id = adi.sub_dealer_id
    WHERE 
        adi.inv_qty IS NOT NULL 
        AND adi.inv_qty != ''
        AND adi.allocation_qty IS NOT NULL 
        AND adi.allocation_qty != ''
        AND adi.delete_at = 0
        AND adi.inv_cancl = 'no'
        AND (CAST(adi.inv_qty AS DECIMAL(10,2)) - CAST(IFNULL(adi.allocation_qty, 0) AS DECIMAL(10,2))) > 0
        AND cm.email IS NOT NULL
        AND cm.email != ''
      
";

// $dealerQuery = "
//     SELECT 
//         MIN(adi.sub_dealer_id) AS sub_dealer_id,
//         MIN(cm.email) AS email,
//         MIN(cm.customer_name) AS customer_name,
//         cm.branch_code
//     FROM allocation_details_invoicewise adi
//     INNER JOIN customer_master cm 
//         ON cm.customer_id = adi.sub_dealer_id
//     WHERE 
//         adi.inv_qty IS NOT NULL
//         AND adi.inv_qty != ''
//         AND adi.allocation_qty IS NOT NULL
//         AND adi.allocation_qty != ''
//         AND adi.delete_at = 0
//         AND adi.inv_cancl = 'no'
//         AND (CAST(adi.inv_qty AS DECIMAL(10,2)) - CAST(IFNULL(adi.allocation_qty, 0) AS DECIMAL(10,2))) > 0
//         AND cm.email IS NOT NULL
//         AND cm.email != ''
//         AND DATE(adi.inv_date) BETWEEN '$startDate' AND '$endDate'
//     GROUP BY cm.branch_code
//     LIMIT 5
// ";
$dealerResult = mysql_query($dealerQuery, $conn);

if (!$dealerResult) {
    echo json_encode([
        "status" => "error", 
        "message" => "Dealer query failed: " . mysql_error()
    ]);
    mysql_close($conn);
    exit;
}

$dealerCount = mysql_num_rows($dealerResult);

if ($dealerCount == 0) {
    echo json_encode([
        "status" => "success", 
        "message" => "No pending allocations found",
        "dealers_processed" => 0
    ]);
    mysql_close($conn);
    exit;
}


while ($dealer = mysql_fetch_assoc($dealerResult)) {
    $sub_dealer_id = mysql_real_escape_string($dealer['sub_dealer_id'], $conn);
    $dealer_email = 'souvik.pal@sbinfowaves.in'; // test email
    $dealer_name = isset($dealer['customer_name']) ? $dealer['customer_name'] : 'Dealer';

    if (!filter_var($dealer_email, FILTER_VALIDATE_EMAIL)) {
        $errorLog[] = array(
            "sub_dealer_id" => $sub_dealer_id,
            "error" => "Invalid email: $dealer_email"
        );
        continue;
    }

    $mappedEmployees = getMappedEmployeesForDealer($sub_dealer_id, $conn);
    
   
    $l3Employees = [];
    foreach ($mappedEmployees as $empCode) {
        $hierarchyEmployees = getEmployeeFlatTree($empCode, $conn);
        foreach ($hierarchyEmployees as $emp) {
            if (isset($emp['level']) && $emp['level'] == 'L3' && isset($emp['email']) && !empty($emp['email'])) {
                $l3Employees[$emp['emp_code']] = $emp['email']; 
            }
        }
    }

   
    $orderQuery = "
        SELECT 
            t.*,
            adi.inv_no,
            DATE_FORMAT(adi.inv_date, '%d-%m-%Y') as inv_date,
            adi.prod_desc,
            CAST(adi.inv_qty AS DECIMAL(10,2)) as inv_qty,
            CAST(IFNULL(adi.allocation_qty, 0) AS DECIMAL(10,2)) as allocation_qty,
            (CAST(adi.inv_qty AS DECIMAL(10,2)) - CAST(IFNULL(adi.allocation_qty, 0) AS DECIMAL(10,2))) AS remaining_qty
        FROM allocation_details_invoicewise adi
        LEFT JOIN T_APPERPDO t ON t.APPORDERNO = adi.APPORDERNO
        WHERE 
            adi.sub_dealer_id = '$sub_dealer_id'
            AND adi.delete_at = 0
            AND adi.inv_cancl = 'no'
            AND adi.inv_qty IS NOT NULL
            AND adi.inv_qty != ''
            AND (CAST(adi.inv_qty AS DECIMAL(10,2)) - CAST(IFNULL(adi.allocation_qty, 0) AS DECIMAL(10,2))) > 0
            AND DATE(adi.inv_date) BETWEEN '$startDate' AND '$endDate'
        ORDER BY adi.inv_date DESC, adi.APPORDERNO
    ";
// echo $orderQuery ;
    $orderResult = mysql_query($orderQuery, $conn);
    if (!$orderResult) {
        $errorLog[] = array(
            "sub_dealer_id" => $sub_dealer_id,
            "error" => "Order query failed: " . mysql_error($conn)
        );
        continue;
    }

    $rowCount = mysql_num_rows($orderResult);
    if ($rowCount == 0) continue;

    
    $fileName = "pending_allocations_" . $sub_dealer_id . "_" . date('Ymd_His') . ".csv";
    $dir = __DIR__ . "/temp/";
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $filePath = $dir . $fileName;

    $fp = fopen($filePath, 'w');
    if (!$fp) {
        $errorLog[] = array(
            "sub_dealer_id" => $sub_dealer_id,
            "error" => "CSV file creation failed"
        );
        continue;
    }

    fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
    $headers = array();
    for ($i = 0; $i < mysql_num_fields($orderResult); $i++) {
        $headers[] = mysql_field_name($orderResult, $i);
    }
    fputcsv($fp, $headers);

    $totalRemaining = 0;
    while ($row = mysql_fetch_assoc($orderResult)) {
        fputcsv($fp, $row);
        $totalRemaining += floatval($row['remaining_qty']);
    }
    fclose($fp);

    //$default_email='samirdas@starcement.co.in';
    $emailList = [$dealer_email,$default_email];
    //$emailList = array_merge($emailList, array_values($l3Employees));
    $toEmails = implode(',', array_unique($emailList));
// print_r($toEmails );die;
    // Send mail
    $mailSent = send_the_mail_with_attachment(
        $toEmails,
        "Pending Allocation Orders - " . date('d-M-Y'),
        getEmailBody($dealer_name, $rowCount, $totalRemaining),
        $filePath,
        $fileName
    );

    if ($mailSent === "TRUE") {
        unlink($filePath);
        $results[] = array(
            "status" => "sent",
            "sub_dealer_id" => $sub_dealer_id,
            "email" => $dealer_email,
            "l3_employees_notified" => count($l3Employees),
            "l3_emails" => array_values($l3Employees),
            "orders_count" => $rowCount,
            "total_remaining_qty" => number_format($totalRemaining, 2)
        );
    } else {
        $errorLog[] = array(
            "sub_dealer_id" => $sub_dealer_id,
            "email" => $dealer_email,
            "error" => "Mail send failed",
            "csv_file" => $filePath
        );
    }
}

mysql_close($conn);

echo json_encode([
    "status" => "completed",
    "total_dealers" => $dealerCount,
    "emails_sent" => count($results),
    "errors" => count($errorLog),
    "results" => $results,
    "error_log" => $errorLog
], JSON_PRETTY_PRINT);

// ===========================
// NEW FUNCTION: Get mapped employees for dealer
// ===========================
function getMappedEmployeesForDealer($sub_dealer_id, $conn)
{
    $empCodes = [];
    $sub_dealer_id = mysql_real_escape_string($sub_dealer_id, $conn);
    
    $sql = "SELECT DISTINCT emp_code 
            FROM customer_route_emp_relation 
            WHERE customer_code = '$sub_dealer_id' 
            AND emp_code IS NOT NULL 
            AND emp_code != ''";
    // echo $sql;die;
    $result = mysql_query($sql, $conn);
    
    if ($result) {
        while ($row = mysql_fetch_assoc($result)) {
            $empCodes[] = $row['emp_code'];
        }
    }
    
    return $empCodes;
}

// ===========================
// UPDATED FUNCTION: Get employee hierarchy with level info
// ===========================
// function getEmployeeFlatTree($managerCode, $conn, &$visited = [])
// {
//     $employees = [];
//     $managerCodeEscaped = mysql_real_escape_string($managerCode, $conn);

//     // If already visited, skip
//     if (in_array($managerCodeEscaped, $visited)) {
//         return [];
//     }
//     $visited[] = $managerCodeEscaped;

//     // Get current employee info with level and email
//     $selfSql = "SELECT emp_code, emp_name, dns_emp_code, level, email 
//                 FROM employee_master 
//                 WHERE emp_code = '$managerCodeEscaped' 
//                 LIMIT 1";
//     $selfResult = mysql_query($selfSql, $conn);
    
//     if ($selfRow = mysql_fetch_assoc($selfResult)) {
//         $employees[] = [
//             'emp_code' => $selfRow['emp_code'],
//             'emp_name' => $selfRow['emp_name'],
//             'dns_emp_code' => $selfRow['dns_emp_code'],
//             'level' => isset($selfRow['level']) ? $selfRow['level'] : null,
//             'email' => isset($selfRow['email']) ? trim($selfRow['email']) : null,
//         ];
//     }

//     // Get subordinates
//     $sql = "SELECT emp_code FROM employee_master WHERE reporting_to LIKE '%$managerCodeEscaped%'";
//     $result = mysql_query($sql, $conn);

//     while ($row = mysql_fetch_assoc($result)) {
//         $empCode = $row['emp_code'];
//         $employees = array_merge($employees, getEmployeeFlatTree($empCode, $conn, $visited));
//     }

//     return $employees;
// }


function getEmployeeFlatTree($empCode, $conn, &$visited = [])
{
    $employees = [];
    $empCodeEscaped = mysql_real_escape_string($empCode, $conn);

    
    if (in_array($empCodeEscaped, $visited)) {
        return [];
    }
    $visited[] = $empCodeEscaped;

   
    $selfSql = "SELECT emp_code, emp_name, dns_emp_code, level, email, reporting_to 
                FROM employee_master 
                WHERE emp_code = '$empCodeEscaped' 
                LIMIT 1";
    $selfResult = mysql_query($selfSql, $conn);
    
    if ($selfRow = mysql_fetch_assoc($selfResult)) {
      
        $employees[] = [
            'emp_code' => $selfRow['emp_code'],
            'emp_name' => $selfRow['emp_name'],
            'dns_emp_code' => $selfRow['dns_emp_code'],
            'level' => isset($selfRow['level']) ? $selfRow['level'] : null,
            'email' => isset($selfRow['email']) ? trim($selfRow['email']) : null,
        ];

      
        $reportingTo = isset($selfRow['reporting_to']) ? trim($selfRow['reporting_to']) : '';
        
        if (!empty($reportingTo)) {
        
            $managerCodes = preg_split('/[,;|]/', $reportingTo);
            
            foreach ($managerCodes as $managerCode) {
                $managerCode = trim($managerCode);
                if (!empty($managerCode)) {
                    
                    $employees = array_merge($employees, getEmployeeFlatTree($managerCode, $conn, $visited));
                }
            }
        }
    }

    return $employees;
}


// ===========================
// MAIL FUNCTIONS
// ===========================
function send_the_mail_with_attachment($to_email, $subject, $bodyml, $file_path = null, $file_name = null)
{
    require_once('class.phpmailer.php');
    require_once('class.smtp.php');

    $sts = "FALSE";
    $to_email_arr = explode(",", $to_email);

    if (count($to_email_arr) > 0) {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->Host = "cloudmail2.up99plus.com";
        $mail->Port = 25;
        $mail->Username = "starcement@cloudmail.up99plus.com";
        $mail->Password = "K2TTvLxATyULV2um";
        $mail->setFrom('starcement@cloudmail.up99plus.com', 'Star Cement');
        $mail->Subject = $subject;
        $mail->MsgHTML($bodyml);

        if ($file_path && file_exists($file_path)) {
            $mail->AddAttachment($file_path, $file_name ?: basename($file_path));
        }

        foreach ($to_email_arr as $addr) {
            if (filter_var(trim($addr), FILTER_VALIDATE_EMAIL)) {
                $mail->AddAddress(trim($addr));
            }
        }

        $sts = $mail->Send() ? "TRUE" : "FALSE";
    }

    return $sts;
}

function getEmailBody($dealer_name, $rowCount, $totalRemaining)
{
    return "
        <html>
        <body style='font-family: Arial;'>
            <h3>Pending Allocation Report</h3>
            <p>Dear <strong>$dealer_name</strong>,</p>
            <p>Please find attached your pending order allocation report.</p>
            <ul>
                <li>Total Pending Orders: <strong>$rowCount</strong></li>
                <li>Total Remaining Quantity: <strong>$totalRemaining</strong></li>
                <li>Report Date: <strong>" . date('d-M-Y H:i:s') . "</strong></li>
            </ul>
            <p>Regards,<br><strong>Team Star Cement</strong></p>
        </body>
        </html>
    ";
}
?>
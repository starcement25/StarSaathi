<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Kolkata');

header('Content-Type: application/json');
date_default_timezone_set('Asia/Kolkata');

require_once("starsaathi_connection.php");
include "function-sfa.php";
$localDB = new starsaathi_connection();
$conn = $localDB->conn;

if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

mysql_select_db("starsaathi_STARS", $conn);

/**
 * Get customer orders with allocation analysis
 */
function getCustomerOrdersWithAllocations($conn, $customer_code)
{
    // customer_code in T_DOINVOICE is same as customer_id in customer_master

    // Calculate date range: from 1st of current month to yesterday
    // INVDT format is YYYYMMDD (e.g., 20251123)
    // $currentDay = date('d');
    $currentDay = date('d');
    if ($currentDay == 1) {

        $firstDayOfRange = date('Ym01', strtotime('first day of previous month'));
        $lastDayOfRange = date('Ymt', strtotime('last day of previous month'));
    } else {

        $firstDayOfRange = date('Ym01');
        $lastDayOfRange = date('Ymd', strtotime('-1 day'));
    }
    // $firstDayOfRange=20251101;
    //  $lastDayOfRange =20251125;
    // Fetch orders with allocations
    // $query = "
    //     SELECT 
    //         t.APPORDERNO,
    //         t.INVNO,
    //         t.INVDT,
    //         cm.customer_name,
    //         cm.email,
    //         SUM(CAST(t.INVQTY AS DECIMAL(10,2))) as total_inv_qty,
    //         COALESCE(SUM(CAST(CASE WHEN a.inv_cancl = 'no' AND a.delete_at = 0 THEN a.allocation_qty ELSE 0 END AS DECIMAL(10,2))), 0) as total_allocation_qty
    //     FROM 
    //         T_DOINVOICE t
    //     LEFT JOIN 
    //         allocation_details_invoicewise a ON t.INVNO = a.inv_no AND t.APPORDERNO = a.APPORDERNO
    //     LEFT JOIN 
    //         customer_master cm ON t.customer_code = cm.customer_id
    //     WHERE 
    //         t.customer_code = '" . mysql_real_escape_string($customer_code) . "' 
    //         AND t.INVDT BETWEEN '" . $firstDayOfRange . "' AND '" . $lastDayOfRange . "'
    //     GROUP BY 
    //         t.APPORDERNO, t.INVNO, t.INVDT
    //     ORDER BY 
    //         t.APPORDERNO, t.INVDT DESC
    // ";
    //  $query = "
    //     SELECT 
    //         t.*,
    //         cm.customer_name,
    //         cm.email,
    //         SUM(CAST(t.INVQTY AS DECIMAL(10,2))) as total_inv_qty,
    //         COALESCE(SUM(CAST(CASE WHEN a.inv_cancl = 'no' AND a.delete_at = 0 THEN a.allocation_qty ELSE 0 END AS DECIMAL(10,2))), 0) as total_allocation_qty
    //     FROM 
    //         T_DOINVOICE t
    //     LEFT JOIN 
    //         allocation_details_invoicewise a ON t.INVNO = a.inv_no AND t.APPORDERNO = a.APPORDERNO
    //     LEFT JOIN 
    //         customer_master cm ON t.customer_code = cm.customer_id
    //     WHERE 
    //         t.customer_code = '" . mysql_real_escape_string($customer_code) . "' 
    //         AND t.INVDT BETWEEN '" . $firstDayOfRange . "' AND '" . $lastDayOfRange . "'
    //     GROUP BY 
    //         t.APPORDERNO, t.INVNO, t.INVDT
    //     ORDER BY 
    //         t.APPORDERNO, t.INVDT DESC 
    // ";

    $query = "
  SELECT 
    t.APPORDERNO,
    erp.ERPORDERNO,
    erp.ERPORDERDT as order_date,
    erp.order_for_type,
    erp.consignee_name,
    erp.consignee_address,
    erp.dns_sub_dealer_code,
    erp.customer_code,
    erp.sub_dealer_code,
    cm_sd.cust_type AS sub_dealer_cust_type,          
    cm_sd.customer_name AS sub_dealer_name,
    erp.dns_customer_code as erp_dns_customer_code,
    erp.prod_display_name,
    erp.QTY as erp_total_qty,
    erp.STATUS as order_status,
    erp.freight,
    erp.order_from,
    erp.order_by,
    bmkr.broker_name AS broker_name,
    erp.Delivery_point,
    GROUP_CONCAT(DISTINCT t.DESTINATION ORDER BY t.DESTINATION SEPARATOR ', ') as destination_combined,
    bm.branch_name,
    cm.customer_name,
    cm.customer_id,
    cm.cust_type,
    cm.dns_customer_code as customer_dns_code,
    cm.email,
    MAX(t.INVDT) as last_invoice_date,
    SUM(CAST(t.INVQTY AS DECIMAL(10,2))) as total_inv_qty,
    COALESCE(SUM(CAST(
        CASE WHEN a.inv_cancl = 'no' AND a.delete_at = 0 
             THEN a.allocation_qty ELSE 0 END
    AS DECIMAL(10,2))), 0) as total_allocation_qty
FROM T_DOINVOICE t
LEFT JOIN T_APPERPDO erp ON t.APPORDERNO = erp.APPORDERNO
LEFT JOIN branch_master bm ON erp.branch_code = bm.branch_code
LEFT JOIN allocation_details_invoicewise a 
       ON t.APPORDERNO = a.APPORDERNO 
       AND a.inv_cancl = 'no' 
       AND a.delete_at = 0
LEFT JOIN customer_master cm ON t.customer_code = cm.customer_id
LEFT JOIN customer_master cm_sd ON erp.sub_dealer_code = cm_sd.customer_code
LEFT JOIN broker_master bmkr ON erp.order_by = bmkr.dns_broker_id 
WHERE 
    t.customer_code = '" . mysql_real_escape_string($customer_code) . "' 
    AND DATE_FORMAT(STR_TO_DATE(erp.ERPORDERDT, '%Y-%m-%d'), '%Y%m%d') >= '" . $firstDayOfRange . "' 
    AND DATE_FORMAT(STR_TO_DATE(erp.ERPORDERDT, '%Y-%m-%d'), '%Y%m%d') <= '" . $lastDayOfRange . "'
GROUP BY 
    t.APPORDERNO,
    erp.ERPORDERNO,
    erp.ERPORDERDT,
    erp.order_for_type,
    erp.consignee_name,
    erp.consignee_address,
    erp.dns_sub_dealer_code,
    erp.customer_code,
    erp.dns_customer_code,
    erp.prod_display_name,
    erp.QTY,
    erp.STATUS,
    erp.freight,
    erp.order_from,
    erp.order_by,
    bmkr.broker_name,
    erp.Delivery_point,
    bm.branch_name,
    cm.customer_name,
    cm.customer_id,
    cm.cust_type,
    cm.dns_customer_code,
    cm.email,
    cm_sd.cust_type,
    cm_sd.customer_name
HAVING
    SUM(CAST(t.INVQTY AS DECIMAL(10,2))) > 
    COALESCE(SUM(CAST(
        CASE WHEN a.inv_cancl = 'no' AND a.delete_at = 0 
             THEN a.allocation_qty ELSE 0 END
    AS DECIMAL(10,2))), 0)
ORDER BY 
    t.APPORDERNO DESC, last_invoice_date DESC";

    //   echo $query;die;
    $result = mysql_query($query, $conn);
    if (!$result) {
        return [];
    }

    $ordersData = [];
    while ($row = mysql_fetch_assoc($result)) {
        $ordersData[] = $row;
    }

    return $ordersData;
}

/**
 * Calculate unallocated quantity and prepare report data
 */

// function send_the_mail_with_attachment($to_email, $subject, $bodyml, $file_path = null, $file_name = null)
// {
//     require_once('class.phpmailer.php');
//     require_once('class.smtp.php');

//     $sts = "FALSE";
//     $to_email_arr = explode(",", $to_email);

//     if (count($to_email_arr) > 0) {
//         $mail = new PHPMailer();
//         $mail->IsSMTP();
//         $mail->SMTPDebug = 0;
//         $mail->SMTPAuth = true;
//         $mail->Host = "cloudmail2.up99plus.com";
//         $mail->Port = 25;
//         $mail->Username = "starcement@cloudmail.up99plus.com";
//         $mail->Password = "K2TTvLxATyULV2um";
//         $mail->setFrom('starcement@cloudmail.up99plus.com', 'Star Cement');
//         $mail->Subject = $subject;
//         $mail->MsgHTML($bodyml);

//         // if ($file_path && file_exists($file_path)) {
//         //     $mail->AddAttachment($file_path, $file_name ?: basename($file_path));
//         // }

//         foreach ($to_email_arr as $addr) {
//             if (filter_var(trim($addr), FILTER_VALIDATE_EMAIL)) {
//                 $mail->AddAddress(trim($addr));
//             }
//         }

//         $sts = $mail->Send() ? "TRUE" : "FALSE";
//     }


//     return $sts;
// }

function send_the_mail_with_attachment($to_email, $subject, $bodyml, $file_path = null, $file_name = null,  $customer_code = null, $conn = null, $cc_emails = '')
{
    require_once('class.phpmailer.php');
    require_once('class.smtp.php');

    $sts = "FALSE";
    $to_email_arr = explode(",", $to_email);
    $cc_email_arr = [];

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

        if (!empty($cc_emails)) {

            $cc_emails = rtrim($cc_emails, ',');
            $cc_email_arr = explode(",", $cc_emails);


            $cc_email_arr = array_unique($cc_email_arr);
            foreach ($cc_email_arr as $cc_addr) {
                $cc_addr = trim($cc_addr);
                if (filter_var($cc_addr, FILTER_VALIDATE_EMAIL)) {

                    if (!in_array($cc_addr, $to_email_arr)) {
                        $mail->AddCC($cc_addr);
                    }
                }
            }
        }

        $sts = $mail->Send() ? "TRUE" : "FALSE";
    }


    if ($conn) {

        $toJson  = mysql_real_escape_string(json_encode($to_email_arr), $conn);
        $ccJson  = mysql_real_escape_string(json_encode($cc_email_arr), $conn);
        $status  = ($sts === "TRUE") ? "success" : "failed";

        $escapedCustomerCode = mysql_real_escape_string($customer_code, $conn);
        $escapedSubject      = mysql_real_escape_string($subject, $conn);

        $query = "
            INSERT INTO mail_log 
            (sent_at, mail_to, mail_cc, status, customer_code, subject)
            VALUES (
                NOW(),
                '{$toJson}',
                '{$ccJson}',
                '{$status}',
                '{$escapedCustomerCode}',
                '{$escapedSubject}'
            )
        ";

        if (!mysql_query($query, $conn)) {
            error_log("Mail log insert failed: " . mysql_error($conn));
        }
    }
    return $sts;
}

// function prepareReportData($ordersData)
// {
//     $reportData = [];
//     $totalDifference = 0;

//     foreach ($ordersData as $row) {
//         $invQty = (float)$row['total_inv_qty'];
//         $allocQty = (float)$row['total_allocation_qty'];
//         $difference = $invQty - $allocQty;

//         if ($difference > 0) {
//             // $reportData[] = [
//             //     'order_no' => $row['APPORDERNO'],
//             //     'invoice_no' => $row['INVNO'],
//             //     'invoice_date' => formatInvoiceDate($row['INVDT']),
//             //     'total_invoice_qty' => $invQty,
//             //     'total_allocation_qty' => $allocQty,
//             //     'unallocated_qty' => $difference,
//             //     'customer_name' => $row['customer_name'],
//             //     'email' => $row['email']
//             // ];
// $temp = $row;    
// $temp['invoice_date'] = formatInvoiceDate($row['INVDT']);
// $temp['total_invoice_qty'] = $invQty;
// $temp['total_allocation_qty'] = $allocQty;
// $temp['unallocated_qty'] = $difference;

// $reportData[] = $temp;
//             $totalDifference += $difference;
//         }
//     }

//     return [
//         'data' => $reportData,
//         'hasUnallocated' => $totalDifference > 0,
//         'totalDifference' => $totalDifference
//     ];
// }

function prepareReportData($ordersData)
{
    $reportData = [];
    $totalDifference = 0;

    foreach ($ordersData as $row) {
        $invQty = (float)$row['total_inv_qty'];
        $allocQty = (float)$row['total_allocation_qty'];
        $difference = $invQty - $allocQty;

        if ($difference > 0) {

            $temp = $row;


            if (isset($row['destination_combined']) && !empty($row['destination_combined'])) {
                $destinations = explode(', ', $row['destination_combined']);
                $destCodes = [];
                $destNames = [];

                foreach ($destinations as $dest) {

                    $dest = trim($dest);

                    $parts = preg_split('/\s{2,}/', $dest, 2);

                    if (count($parts) == 2) {
                        $destCodes[] = trim($parts[0]);
                        $destNames[] = trim($parts[1]);
                    } else {

                        if (preg_match('/^(\d+)\s+(.+)$/', $dest, $matches)) {
                            $destCodes[] = $matches[1];
                            $destNames[] = trim($matches[2]);
                        } else {

                            $destCodes[] = '';
                            $destNames[] = $dest;
                        }
                    }
                }

                $temp['destination_code'] = implode(', ', array_filter($destCodes));
                $temp['destination_name'] = implode(', ', array_filter($destNames));
            } else {
                $temp['destination_code'] = '';
                $temp['destination_name'] = '';
            }


            unset($temp['destination_combined']);


            $temp['total_invoice_qty'] = $invQty;
            $temp['total_allocation_qty'] = $allocQty;
            $temp['unallocated_qty'] = $difference;

            $reportData[] = $temp;
            $totalDifference += $difference;
        }
    }

    return [
        'data' => $reportData,
        'hasUnallocated' => $totalDifference > 0,
        'totalDifference' => $totalDifference
    ];
}


function formatInvoiceDate($dateString)
{
    if (strlen($dateString) == 8 && is_numeric($dateString)) {
        $year = substr($dateString, 0, 4);
        $month = substr($dateString, 4, 2);
        $day = substr($dateString, 6, 2);
        return $day . '-' . $month . '-' . $year;
    }
    return $dateString;
}

/**
 * Generate CSV content
 */

// function generateCSV($reportData)
// {
//     $csvContent = "Order Number,Invoice Number,Invoice Date,Total Invoice Qty,Total Allocation Qty,Unallocated Qty\n";

//     foreach ($reportData as $row) {
//         $csvContent .= sprintf(
//             "%s,%s,%s,%.2f,%.2f,%.2f\n",
//             $row['order_no'],
//             $row['invoice_no'],
//             $row['invoice_date'],
//             $row['total_invoice_qty'],
//             $row['total_allocation_qty'],
//             $row['unallocated_qty']
//         );
//     }

//     return $csvContent;
// }
// function generateCSV($reportData)
// {
//     if (empty($reportData)) {
//         return "";
//     }

//     // Get all column names dynamically
//     $headers = array_keys($reportData[0]);

//     // Create CSV content
//     $csvContent = implode(",", $headers) . "\n";

//     foreach ($reportData as $row) {
//         $line = [];
//         foreach ($headers as $h) {
//             $line[] = isset($row[$h]) ? $row[$h] : '';
//         }
//         $csvContent .= implode(",", $line) . "\n";
//     }

//     return $csvContent;
// }

function generateCSV($reportData)
{
    if (empty($reportData)) {
        return "";
    }


    $headerMapping = [
        'APPORDERNO' => 'App Order Number',
        'ERPORDERNO' => 'Sales Order No',
        'order_date' => 'Date',
        'branch_name' => 'Branch Name',
        'customer_id' => 'Dealer SAP Code',
        'customer_name' => 'Customer Name',
        'erp_dns_customer_code' => 'Customer Code',
        'order_for_type' => 'Order For Type',
        'consignee_name' => 'Consignee Name',
        'consignee_address' => 'Consignee Address',
        'dns_sub_dealer_code' => 'Consignee SAP Code',
        'freight' => 'Freight',

        'destination_code' => 'Destination Code',
        'destination_name' => 'Destination Name',


        // 'email' => 'Customer Email',
        'prod_display_name' => 'Product Name',
        'erp_total_qty' => 'QTY',

        //'destination_address' => 'Destination Address',

        'order_status' => 'Order Status',
        'order_from' => 'Order From',
    ];


    $columnKeys = array_keys($headerMapping);


    $headers = ['Sr. No.'];
    foreach ($columnKeys as $key) {
        $headers[] = $headerMapping[$key];
    }


    $csvContent = implode(",", array_map(function ($h) {
        return '"' . str_replace('"', '""', $h) . '"';
    }, $headers)) . "\n";


    $serialNumber = 1;
    foreach ($reportData as $row) {
        $line = [$serialNumber];


        foreach ($columnKeys as $key) {
            $value = isset($row[$key]) ? $row[$key] : '';


            if ($value === null) {
                $value = '';
            }
            /////////////

            if ($key === 'order_for_type') {
                if (isset($row['sub_dealer_cust_type']) && !empty($row['sub_dealer_cust_type'])) {
                    $value = $row['sub_dealer_cust_type'];
                } else {
                    $value = 'Dealer';
                }
            }


            if ($key === 'order_date' && !empty($value)) {

                if (strpos($value, ' ') !== false) {
                    $dateParts = explode(' ', $value);
                    $value = $dateParts[0];
                }


                if (strpos($value, 'T') !== false) {
                    $dateParts = explode('T', $value);
                    $value = $dateParts[0];
                }
            }


            if ($key === 'order_from') {
                $orderBy = isset($row['broker_name']) ? (string)$row['broker_name'] : '';
                $value = isset($value) ? (string)$value : '';
                if (!empty($orderBy)) {
                    $value = trim($value . ' ' . $orderBy);
                }
            }
            /////////

            $value = (string)$value;


            $value = str_replace('"', '""', $value);


            if (
                strpos($value, ',') !== false ||
                strpos($value, "\n") !== false ||
                strpos($value, '"') !== false
            ) {
                $value = '"' . $value . '"';
            }

            $line[] = $value;
        }

        $csvContent .= implode(",", $line) . "\n";
        $serialNumber++;
    }

    return $csvContent;
}
/**
 * Main function - Process customer and send report
 */
function processCustomerAllocations($conn, $customer_code)
{
    try {

        $ordersData = getCustomerOrdersWithAllocations($conn, $customer_code);

        if (empty($ordersData)) {
            return ['status' => 'error', 'message' => 'No orders found for this customer'];
        }


        $report = prepareReportData($ordersData);

        if (!$report['hasUnallocated']) {
            return ['status' => 'info', 'message' => 'All invoices are fully allocated'];
        }
        ////////////////
        $sqldealeremp = "SELECT emp_code FROM customer_route_emp_relation WHERE acedns='Y' AND customer_code='{$customer_code}'";
        //  print_r($sqldealeremp);die;
        $rsdealeremp = mysql_query($sqldealeremp);
        $email_hierarchy = '';
        while ($rowdealeremp = mysql_fetch_array($rsdealeremp)) {
            $emp_code_db = $rowdealeremp['emp_code'];
            $employee_upper_hierarchy = return_employee_upper_hierarchy($emp_code_db);
            //$emp_hierarchy_condition='c1.emp_code IN('.$employee_hierarchy.')';
            $sqlemailhierarchy = "SELECT email,designation FROM employee_master WHERE emp_code IN (" . $employee_upper_hierarchy . ") AND UPPER(sale_access)='PRIMARY' AND acedns='Y'";
            $rsemailhierarchy = mysql_query($sqlemailhierarchy);
            while ($rowemailhierarchy = mysql_fetch_array($rsemailhierarchy)) {
                if (
                    $rowemailhierarchy['email'] != '' &&
                    in_array(strtoupper($rowemailhierarchy['designation']), ['ASM', 'SO'])
                ) {

                    $email_hierarchy .= $rowemailhierarchy['email'] . ',';
                }
            }
        }

        $email_hierarchy = rtrim($email_hierarchy, ',');
        // $email_hierarchy = 'atanu.sahoo@sbinfowaves.com,souvik.pal@sbinfowaves.in,samirdas@starcement.co.in';
        /////////////////

        $csvContent = generateCSV($report['data']);
        $customerEmail = $report['data'][0]['email'];
        //$customerEmail = 'souvik.pal@sbinfowaves.in';
        //$customerEmail = 'souvik.pal@sbinfowaves.com';
        $customerName = $report['data'][0]['customer_name'];
        $csvFileName = "pending_allocations_" . date('YmdHis') . ".csv";
        $filePath = "/tmp/" . $csvFileName;


        file_put_contents($filePath, $csvContent);


        $emailBody = getEmailBody($customerName, count($report['data']), $report['totalDifference']);
        $currentDay = date('d');
        if ($currentDay == 1) {
            $monthYear = date('M-Y', strtotime('first day of previous month'));
        } else {
            $monthYear = date('M-Y');
        }
        $emailSubject = "RSAR pending allocation for the month of $monthYear - Action required";


        $mailStatus = send_the_mail_with_attachment(
            $customerEmail,
            $emailSubject,
            $emailBody,
            $filePath,
            $csvFileName,
            $customer_code,
            $conn,
            $email_hierarchy
        );


        if (file_exists($filePath)) {
            unlink($filePath);
        }

        if ($mailStatus === "TRUE") {
            return [
                'status' => 'success',
                'message' => 'Report generated and email sent successfully',
                'unallocated_count' => count($report['data']),
                'total_difference' => $report['totalDifference'],
                'customer_name' => $customerName,
                'customer_email' => $customerEmail
            ];
        } else {
            return ['status' => 'error', 'message' => 'Failed to send email'];
        }
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
    }
}


function getEmailBody($dealer_name, $rowCount, $totalRemaining)
{

    $currentDay = date('d');
    if ($currentDay == 1) {

        $monthYear = date('M-Y', strtotime('first day of previous month'));
    } else {

        $monthYear = date('M-Y');
    }
    return "
        <html>
        <body style='font-family: Arial;'>
            <h3>Pending Allocation Report</h3>
            <p>Dear <strong>$dealer_name</strong>,</p>
            <p>Please find attached your pending rsar allocation report for the month of $monthYear. </p>
            <ul>
                <li>Report Date: <strong>" . date('d-M-Y H:i') . "</strong></li>
            </ul>
            <p>Regards,<br><strong>Team Star Cement</strong></p>
        </body>
        </html>
    ";
}

// function getEmailBody($dealer_name, $rowCount, $totalRemaining)
// {
//     return "
//         <html>
//         <body style='font-family: Arial;'>
//             <h3>Please Ignore The Last Email!</h3>
            
//             <p>Regards,<br><strong>Team Star Cement</strong></p>
//         </body>
//         </html>
//     ";
// }

/**
 * Send Mail - Your existing function (ensure this is included from your file)
 */
// Uncomment if not already included:
// require_once('your_mail_functions.php');

// MAIN EXECUTION
// Fetch all customers with cust_type = 'Dealer'
$dealerQuery = "SELECT customer_id, customer_name,customer_code, email FROM customer_master WHERE cust_type = 'Dealer' ORDER BY customer_id";
$dealerResult = mysql_query($dealerQuery, $conn);

if (!$dealerResult) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch dealer customers']);
    exit;
}

$results = [];
$successCount = 0;
$failureCount = 0;
$noUnallocatedCount = 0;

while ($dealer = mysql_fetch_assoc($dealerResult)) {
    $customer_code = $dealer['customer_id']; // customer_code in T_DOINVOICE is same as customer_id in customer_master
    $result = processCustomerAllocations($conn, $customer_code);

    $results[] = [
        'customer_code' => $customer_code,
        'customer_name' => $dealer['customer_name'],
        'customer_email' => $dealer['email'],
        'result' => $result
    ];

    if ($result['status'] === 'success') {
        $successCount++;
    } else if ($result['status'] === 'info') {
        $noUnallocatedCount++;
    } else {
        $failureCount++;
    }
}

// Summary response
$summary = [
    'status' => 'completed',
    'message' => 'Report generation completed for all dealers',
    'summary' => [
        'total_dealers_processed' => $successCount + $failureCount + $noUnallocatedCount,
        'emails_sent' => $successCount,
        'no_unallocated_qty' => $noUnallocatedCount,
        'failed' => $failureCount
    ],
    'details' => $results
];

echo json_encode($summary);

mysql_close($conn);

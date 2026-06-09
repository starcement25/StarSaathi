<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// date_default_timezone_set('Asia/Kolkata');


// $conn = mysqli_connect("172.17.0.2", "root", "Passw0rd123#$", "starsaathi_STARS");

// if (!$conn) {
//     die("Connection failed: " . mysqli_connect_error());
// }


// $month = isset($_GET['month']) ? intval($_GET['month']) : null;
// $year = isset($_GET['year']) ? intval($_GET['year']) : null;
// $approval_status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : null;
// $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : null;
// $where = [];
// if ($month) {
//     $where[] = "d.month = $month";
// }

// if ($approval_status !== null) $where[] = "d.status = '$approval_status'";
// if ($year) {
//     $where[] = "YEAR(d.created_at) = $year";
// }
// if ($search !== null) {
//     $where[] = "(c.customer_name LIKE '%$search%' OR d.customer_id LIKE '%$search%' OR d.customer_code LIKE '%$search%')";
// }
// $whereClause = '';
// if (!empty($where)) {
//     $whereClause = 'WHERE ' . implode(' AND ', $where);
// }


// $filename = "dealer_exclusice_requests.csv";
// if ($month && $year) {
//     $filename = "dealer_exclusice_requests.csv";
// }

// header('Content-Type: text/csv');
// header('Content-Disposition: attachment; filename="' . $filename . '"');


// $output = fopen('php://output', 'w');


// fputcsv($output, [
//     'Sr. No', 'Dealer Code', 'Dealer ID', 'Dealer Name', 'Month',
//     'Branch', 'Lifting Qty', 'ASM Approval Status', 'Approved By ASM',
//     'ASM Approved Date', 'RSM Approval Status', 'Approved By RSM',
//     'RSM Approved Date','Status', 'Created At'
// ]);


// $sql = "SELECT 
//         d.*,
//         COALESCE(asm.emp_name, 'N/A') AS asm_name,
//         COALESCE(rsm.emp_name, 'N/A') AS rsm_name  
//     FROM dealer_exclusice d
//     LEFT JOIN employee_master asm ON d.asm_approved_by = asm.emp_code
//     LEFT JOIN employee_master rsm ON d.rsm_approved_by = rsm.emp_code
//     LEFT JOIN customer_master c ON d.customer_id = c.customer_id
//     $whereClause
//     ORDER BY d.created_at DESC";

// $result = mysqli_query($conn, $sql);
// if (!$result) {
//     die("Query Error: " . mysqli_error($conn));
// }
//  //echo"<pre>";print_r($result);die;

// $i = 0;
// while ($row = mysqli_fetch_assoc($result)) {
    
//     $monthNumber = $row['month'];
//     $yearStr = date('Y', strtotime($row['created_at']));
//     $monthName = date('F', mktime(0, 0, 0, $monthNumber, 1));
//     $displayMonthYear = $monthName . ', ' . $yearStr;

   
//     $asm_status = ($row['asm_approve_status'] == 1) ? 'Approved' : (($row['asm_approve_status'] == 2) ? 'Rejected' : 'Pending');
//     $rsm_status = ($row['rsm_approve_status'] == 1) ? 'Approved' : (($row['rsm_approve_status'] == 2) ? 'Rejected' : 'Pending');
//     $ex_status= ($row['status'] == 1) ? 'Approved' : (($row['status'] == 2) ? 'Not Approved' : 'Pending');

//     fputcsv($output, [
//         ++$i,
//         $row['customer_code'],
//         $row['customer_id'],
//         $row['dealer_name'],
//         $displayMonthYear,
//         $row['branch'],
//         $row['lifting_qty'],
//         $asm_status,
//         $row['asm_name'],
//         !empty($row['asm_approve_date']) ? date('d-m-Y', strtotime($row['asm_approve_date'])) : 'N/A',
//         $rsm_status,
//         $row['rsm_name'],
//         !empty($row['rsm_approve_date']) ? date('d-m-Y', strtotime($row['rsm_approve_date'])) : 'N/A',
//         $ex_status,
//         !empty($row['created_at']) ? date('d-m-Y', strtotime($row['created_at'])) : 'N/A',
//     ]);
// }

// fclose($output);
// mysqli_close($conn);
// exit;




// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// date_default_timezone_set('Asia/Kolkata');

// //$conn = mysqli_connect("172.17.0.2", "root", "Passw0rd123#$", "starsaathi_STARS");
// require_once("starsaathi_connection.php");
// $localDB = new starsaathi_connection();
// $conn = $localDB->conn;
// if (!$conn) {
//     die("Connection failed: " . mysqli_connect_error());
// }


// $month = isset($_GET['month']) && $_GET['month'] !== '' ? intval($_GET['month']) : null;
// $year = isset($_GET['year']) && $_GET['year'] !== '' ? intval($_GET['year']) : null;
// $approval_status = isset($_GET['status']) && $_GET['status'] !== '' ? mysqli_real_escape_string($conn, $_GET['status']) : null;
// $search = isset($_GET['search']) && $_GET['search'] !== '' ? mysqli_real_escape_string($conn, $_GET['search']) : null;


// $where = [];

// if (!is_null($month)) {
//     $where[] = "d.month = $month";
// }

// if (!is_null($year)) {
//     $where[] = "YEAR(d.created_at) = $year";
// }

// if (!is_null($approval_status)) {
//     $where[] = "d.status = '$approval_status'";
// }

// if (!is_null($search)) {
//     $where[] = "(c.customer_name LIKE '%$search%' OR d.customer_id LIKE '%$search%' OR d.customer_code LIKE '%$search%')";
// }

// $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';


// $filename = "dealer_exclusice_requests.csv";


// header('Content-Type: text/csv');
// header('Content-Disposition: attachment; filename="' . $filename . '"');


// $output = fopen('php://output', 'w');


// fputcsv($output, [
//     'Sr. No', 'Dealer Code', 'Dealer ID', 'Dealer Name', 'Month',
//     'Branch', 'Lifting Qty', 'ASM Approval Status', 'Approved By ASM',
//     'ASM Approved Date', 'RSM Approval Status', 'Approved By RSM',
//     'RSM Approved Date', 'Status', 'Created At'
// ]);


// $sql = "
//     SELECT 
//         d.*,
//         COALESCE(c.customer_name, '') AS dealer_name,
//         COALESCE(asm.emp_name, 'N/A') AS asm_name,
//         COALESCE(rsm.emp_name, 'N/A') AS rsm_name
//     FROM dealer_exclusice d
//     LEFT JOIN employee_master asm ON d.asm_approved_by = asm.emp_code
//     LEFT JOIN employee_master rsm ON d.rsm_approved_by = rsm.emp_code
//     LEFT JOIN customer_master c ON d.customer_id = c.customer_id
//     $whereClause
//     ORDER BY d.created_at DESC
// ";

// $result = mysqli_query($conn, $sql);

// if (!$result) {
//     die("Query Error: " . mysqli_error($conn));
// }

// $i = 0;
// while ($row = mysqli_fetch_assoc($result)) {
//     $monthNumber = $row['month'];
//     $yearStr = date('Y', strtotime($row['created_at']));
//     $monthName = date('F', mktime(0, 0, 0, $monthNumber, 1));
//     $displayMonthYear = $monthName . ', ' . $yearStr;

//     $asm_status = ($row['asm_approve_status'] == 1) ? 'Approved' : (($row['asm_approve_status'] == 2) ? 'Rejected' : 'Pending');
//     $rsm_status = ($row['rsm_approve_status'] == 1) ? 'Approved' : (($row['rsm_approve_status'] == 2) ? 'Rejected' : 'Pending');
//     $ex_status = ($row['status'] == 1) ? 'Approved' : (($row['status'] == 2) ? 'Not Approved' : 'Pending');

//     fputcsv($output, [
//         ++$i,
//         $row['customer_code'],
//         $row['customer_id'],
//         $row['dealer_name'],
//         $displayMonthYear,
//         $row['branch'],
//         $row['lifting_qty'],
//         $asm_status,
//         $row['asm_name'],
//         !empty($row['asm_approve_date']) ? date('d-m-Y', strtotime($row['asm_approve_date'])) : 'N/A',
//         $rsm_status,
//         $row['rsm_name'],
//         !empty($row['rsm_approve_date']) ? date('d-m-Y', strtotime($row['rsm_approve_date'])) : 'N/A',
//         $ex_status,
//         !empty($row['created_at']) ? date('d-m-Y', strtotime($row['created_at'])) : 'N/A',
//     ]);
// }

// // Close output stream and DB connection
// fclose($output);
// mysqli_close($conn);
// exit;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED);
date_default_timezone_set('Asia/Kolkata');

require_once("starsaathi_connection.php");
$localDB = new starsaathi_connection();
$conn = $localDB->conn;

if (!$conn) {
    die("Connection failed: " . mysql_error());
}

// Input sanitization using mysql_real_escape_string
$month = isset($_GET['month']) && $_GET['month'] !== '' ? intval($_GET['month']) : null;
$year = isset($_GET['year']) && $_GET['year'] !== '' ? intval($_GET['year']) : null;
$approval_status = isset($_GET['status']) && $_GET['status'] !== '' ? mysql_real_escape_string($_GET['status']) : null;
$search = isset($_GET['search']) && $_GET['search'] !== '' ? mysql_real_escape_string($_GET['search']) : null;
$emp_code = isset($_GET['emp_code']) ? mysql_real_escape_string($_GET['emp_code']) : null;
$where = [];


$tree = getEmployeeFlatTree($emp_code);


$empCodes = array_column($tree, 'emp_code');


$allCustomerCodes = getAllMappedCustomerCodes($empCodes);

// print_r($allCustomerCodes);die;

$allCustomerCodesLookup = array_flip($allCustomerCodes);


if (!empty($allCustomerCodes)) {

    $allCustomerCodes = array_filter($allCustomerCodes);
    $customerCodesStr = "'" . implode("','", array_map('addslashes', $allCustomerCodes)) . "'";
} else {
    $customerCodesStr = "''"; // Empty string if no codes
}




if (!is_null($month)) {
    $where[] = "d.month = $month";
}

if (!is_null($year)) {
    $where[] = "d.current_year = $year";
}

if (!is_null($approval_status)) {
    $where[] = "d.status = '$approval_status'";
}

if (!is_null($search)) {
    $where[] = "(c.customer_name LIKE '%$search%' OR d.customer_id LIKE '%$search%' OR d.customer_code LIKE '%$search%')";
}

if (!empty($allCustomerCodes) && $_GET['emp_code'] !== 'admin') {
    $where[] = "d.customer_id IN ($customerCodesStr)";
}
$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$filename = "dealer_exclusice_requests.csv";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

fputcsv($output, [
    'Sr. No', 'Dealer Code', 'Dealer ID', 'Dealer Name', 'Month',
    'Branch', 'Lifting Qty', 'ASM Approval Status', 'Approved By ASM',
    'ASM Approved Date', 'RSM Approval Status', 'Approved By RSM',
    'RSM Approved Date', 'Status', 'Created At'
]);

$sql = "
    SELECT 
        d.*,
        IFNULL(c.customer_name, '') AS dealer_name,
        IFNULL(asm.emp_name, 'N/A') AS asm_name,
        IFNULL(rsm.emp_name, 'N/A') AS rsm_name
    FROM dealer_exclusice d
    LEFT JOIN employee_master asm ON d.asm_approved_by = asm.emp_code
    LEFT JOIN employee_master rsm ON d.rsm_approved_by = rsm.emp_code
    LEFT JOIN customer_master c ON d.customer_id = c.customer_id
    $whereClause
    ORDER BY d.created_at DESC
";

$result = mysql_query($sql, $conn);

if (!$result) {
    die("Query Error: " . mysql_error());
}

$i = 0;
while ($row = mysql_fetch_assoc($result)) {
    $monthNumber = $row['month'];
    $yearStr = $row['current_year'];
    $monthName = date('F', mktime(0, 0, 0, $monthNumber, 1));
    $displayMonthYear = $monthName . ', ' . $yearStr;

    $asm_status = ($row['asm_approve_status'] == 1) ? 'Approved' : (($row['asm_approve_status'] == 2) ? 'Rejected' : 'Pending');
    $rsm_status = ($row['rsm_approve_status'] == 1) ? 'Approved' : (($row['rsm_approve_status'] == 2) ? 'Rejected' : 'Pending');
    $ex_status = ($row['status'] == 1) ? 'Approved' : (($row['status'] == 2) ? 'Not Approved' : 'Pending');

    fputcsv($output, [
        ++$i,
        $row['customer_code'],
        $row['customer_id'],
        $row['dealer_name'],
        $displayMonthYear,
        $row['branch'],
        $row['lifting_qty'],
        $asm_status,
        $row['asm_name'],
        !empty($row['asm_approve_date']) ? date('d-m-Y', strtotime($row['asm_approve_date'])) : 'N/A',
        $rsm_status,
        $row['rsm_name'],
        !empty($row['rsm_approve_date']) ? date('d-m-Y', strtotime($row['rsm_approve_date'])) : 'N/A',
        $ex_status,
        !empty($row['created_at']) ? date('d-m-Y', strtotime($row['created_at'])) : 'N/A',
    ]);
}

fclose($output);



function getEmployeeFlatTree($managerCode, &$visited = [])
{
    $employees = [];


    $managerCodeEscaped = mysql_real_escape_string($managerCode);

    
    if (in_array($managerCodeEscaped, $visited)) {
        return [];
    }
    $visited[] = $managerCodeEscaped;

    // Get current employee info
    $selfSql = "SELECT emp_code, emp_name, dns_emp_code ,branch_code,level FROM employee_master WHERE emp_code = '$managerCodeEscaped' LIMIT 1";
    $selfResult = mysql_query($selfSql);
    if ($selfRow = mysql_fetch_assoc($selfResult)) {
        $employees[] = [
            'emp_code' => $selfRow['emp_code'],
            'emp_name' => $selfRow['emp_name'],
            'dns_emp_code' => $selfRow['dns_emp_code'],
            'branch_code' => $selfRow['branch_code'],
            'level' => $selfRow['level'],
        ];
    }


    $sql = "SELECT emp_code FROM employee_master WHERE reporting_to LIKE '%$managerCodeEscaped%'";
    $result = mysql_query($sql);

    while ($row = mysql_fetch_assoc($result)) {
        $empCode = $row['emp_code'];
        $employees = array_merge($employees, getEmployeeFlatTree($empCode, $visited));
    }

    return $employees;
}




function displayEmployeeList($employees)
{
    if (empty($employees)) {
        echo "No employees found.";
        return;
    }

    // Print header
    echo "Employee Code ## Employee Name ## DNS Employee Code\n";

    // Print each employee
    foreach ($employees as $employee) {
        echo $employee['emp_code'] . " ## " . $employee['emp_name'] . " ## " . $employee['dns_emp_code'] . " ## " . $employee['branch_code'] . "\n";
    }
}



function getAllMappedCustomerCodes($empCodes)
{
    $customerCodes = [];

    foreach ($empCodes as $empCode) {
        $empCodeEscaped = mysql_real_escape_string($empCode);

        $sql = "SELECT customer_code FROM customer_route_emp_relation WHERE emp_code = '$empCodeEscaped'";
        // $sql = "
        //         SELECT 
        //             r.customer_code,
        //             c.customer_name,
        //             c.branch_code,
        //             b.branch_name
        //         FROM customer_route_emp_relation r
        //         LEFT JOIN customer_master c 
        //             ON r.customer_code = c.customer_id
        //         LEFT JOIN branch_master b
        //             ON c.branch_code = b.branch_code
        //         WHERE r.emp_code = '$empCodeEscaped'
        //     ";
        $result = mysql_query($sql);

        while ($row = mysql_fetch_assoc($result)) {
            $customerCodes[] = $row['customer_code'];
            // $customerCodes[] = $row;
        }
    }

    return array_unique($customerCodes);
    //return array_map('unserialize', array_unique(array_map('serialize', $customerCodes)));
}

mysql_close($conn);
exit;

?>




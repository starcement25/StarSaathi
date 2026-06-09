<?php





ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Kolkata');
header('Content-Type: application/json');
include "star_connection.php";
/*
$conn = mysql_connect("52.66.97.178", "root", "UZzRXN4CMJtOaUq7", "starsaathi_STARS");
// require_once("starsaathi_connection.php");
// $localDB = new starsaathi_connection();
// $conn = $localDB->conn;
if (!$conn) {
    echo json_encode(["process_status" => "No", "process_message" => "DB Connection Failed"]);
    exit;
}
*/
$approval_status = isset($_GET['status']) ? mysql_real_escape_string($_GET['status']) : null;
$month = isset($_GET['month']) ? mysql_real_escape_string($_GET['month']) : null;
$year = isset($_GET['year']) ? mysql_real_escape_string($_GET['year']) : null;
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$limit = isset($_GET['limit']) ? max((int)$_GET['limit'], 1) : 10;
$offset = ($page - 1) * $limit;
$emp_code = isset($_GET['emp_code']) ? mysql_real_escape_string($_GET['emp_code']) : null;
$search = isset($_GET['search']) ? mysql_real_escape_string($_GET['search']) : null;

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

$where = [];
if ($approval_status !== null) $where[] = "d.status = '$approval_status'";
if ($month !== null) $where[] = "d.month = '$month'";
if ($year !== null) $where[] = "d.current_year = '$year'";
if ($search !== null) {
    $where[] = "(c.customer_name LIKE '%$search%' OR d.customer_id LIKE '%$search%' OR d.customer_code LIKE '%$search%')";
}
if (!empty($allCustomerCodes) && $_GET['emp_code'] !== 'admin') {
    $where[] = "d.customer_id IN ($customerCodesStr)";
}
$where_clause = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';


$sql = "
    SELECT 
    d.*,
   
    c.customer_name AS dealer_name,
    COALESCE(asm.emp_name, 'N/A') AS asm_name,
    COALESCE(rsm.emp_name, 'N/A') AS rsm_name
FROM dealer_exclusice d
LEFT JOIN employee_master asm ON d.asm_approved_by = asm.emp_code
LEFT JOIN employee_master rsm ON d.rsm_approved_by = rsm.emp_code
LEFT JOIN customer_master c ON d.customer_id = c.customer_id
$where_clause
ORDER BY d.created_at DESC
LIMIT $limit OFFSET $offset
";
//  echo $sql;die;
$result = mysql_query($sql);
//echo"<pre>";print_r($result);die;

if (!$result) {
    echo json_encode(["process_status" => "No", "error" => mysql_error($conn)]);
    exit;
}

function cleanDateTime($str)
{
    return preg_replace('/\s*:\s*/', ':', trim($str));
}


$data = [];
$sr = $offset + 1;
$asm_status = '';
$rsm_status = '';
while ($row = mysql_fetch_assoc($result)) {
    $monthNumber = $row['month'];
    $year = $row['current_year'];
    $monthName = date('F', mktime(0, 0, 0, $monthNumber, 1));
    $displayMonthYear = $monthName . ', ' . $year;
    if ($row['asm_approve_status'] == 0) {
        $asm_status = 'Pending';
    } elseif ($row['asm_approve_status'] == 1) {
        $asm_status = 'Approved';
    } else {
        $asm_status = 'Rejected';
    }


    if ($row['rsm_approve_status'] == 0) {
        $rsm_status = 'Pending';
    } elseif ($row['rsm_approve_status'] == 1) {
        $rsm_status = 'Approved';
    } else {
        $rsm_status = 'Rejected';
    }

    if ($row['status'] == 0) {
        $status = 'Pending';
    } elseif ($row['status'] == 1) {
        $status = 'Approved';
    } else {
        $status = 'Rejected';
    }
    $data[] = [
        "Sr. No"               => $row['id'],
        "Dealer Code"          => $row['customer_code'],
        "Dealer ID"            => $row['customer_id'],
        "Dealer Name"          => $row['dealer_name'],
        "Month"                => $displayMonthYear,
        "Branch"               => $row['branch'],
        "Lifting Qty"          => $row['lifting_qty'],
        "ASM Approval Status"  => $asm_status,
        "Approved By ASM"      => $row['asm_name'],
        "ASM Approved Date"    => !empty($row['asm_approve_date']) ? date('d-m-Y', strtotime(cleanDateTime($row['asm_approve_date']))) : 'N/A',
        "RSM Approval Status"  => $rsm_status,
        "Approved By RSM"      => $row['rsm_name'],
        "RSM Approved Date"    => !empty($row['rsm_approve_date']) ? date('d-m-Y', strtotime(cleanDateTime($row['rsm_approve_date']))) : 'N/A',
        "Created At"           => !empty($row['created_at']) ? date('d-m-Y', strtotime(cleanDateTime($row['created_at']))) : '',
        "Status"               => $status,
        "Reason for rejection" => $row['reject_reason'],
    ];
}


$count_sql = "SELECT COUNT(*) AS total FROM dealer_exclusice d
              LEFT JOIN customer_master c ON d.customer_id = c.customer_id
              $where_clause";
$count_result = mysql_query($count_sql);
$total_rows = ($count_result && mysql_num_rows($count_result) > 0) ? mysql_fetch_assoc($count_result)['total'] : 0;
$total_pages = ceil($total_rows / $limit);


echo json_encode([
    "process_status" => "YES",
    "process_message" => "Success",
    "current_page" => $page,
    "limit" => $limit,
    "total_pages" => $total_pages,
    "total_records" => $total_rows,
    "data" => $data
]);





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

<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED);
date_default_timezone_set('Asia/Kolkata');

header('Content-Type: application/json');

require_once("starsaathi_connection.php");
$localDB = new starsaathi_connection();
$conn = $localDB->conn;

if (!$conn) {
    echo json_encode([
        "process_status"  => "NO",
        "process_message" => "Connection failed: " . mysql_error()
    ]);
    exit;
}

// Input sanitization using mysql_real_escape_string
$month = isset($_GET['month']) && $_GET['month'] !== '' ? intval($_GET['month']) : null;
$year = isset($_GET['year']) && $_GET['year'] !== '' ? intval($_GET['year']) : null;
$approval_status = isset($_GET['status']) && $_GET['status'] !== '' ? mysql_real_escape_string($_GET['status']) : null;
$search = isset($_GET['search']) && $_GET['search'] !== '' ? mysql_real_escape_string($_GET['search']) : null;
$emp_code = isset($_GET['emp_code']) ? mysql_real_escape_string($_GET['emp_code']) : null;
$where = [];

$dns_emp_code = isset($_GET['dns_emp_code']) 
    ? mysql_real_escape_string($_GET['dns_emp_code']) 
    : null;
    if(!empty($dns_emp_code))
{
    $empQry = "
        SELECT emp_code 
        FROM employee_master 
        WHERE dns_emp_code = '$dns_emp_code'
        LIMIT 1
    ";

    $empRs = mysql_query($empQry);

    if($empRs && mysql_num_rows($empRs) > 0)
    {
        $empRow = mysql_fetch_assoc($empRs);

        $emp_code = $empRow['emp_code'];
    }
}


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

if (!empty($allCustomerCodes) && (!isset($_GET['emp_code']) || $_GET['emp_code'] !== 'admin')) {
    $where[] = "d.customer_id IN ($customerCodesStr)";
}
$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "
    SELECT
        d.*,
        IFNULL(c.customer_name, '') AS dealer_name,
        IFNULL(asm.emp_name, 'N/A') AS asm_name,
        IFNULL(asm.emp_code, 'N/A') AS asm_code,
        IFNULL(asm.dns_emp_code, '') AS asm_dns_emp_code,
        IFNULL(rsm.emp_name, 'N/A') AS rsm_name,
        IFNULL(rsm.emp_code, 'N/A') AS rsm_code,
        IFNULL(rsm.dns_emp_code, '') AS rsm_dns_emp_code
    FROM dealer_exclusice d
    LEFT JOIN employee_master asm ON d.asm_approved_by = asm.emp_code
    LEFT JOIN employee_master rsm ON d.rsm_approved_by = rsm.emp_code
    LEFT JOIN customer_master c ON d.customer_id = c.customer_id
    $whereClause
    ORDER BY d.created_at DESC
";

$result = mysql_query($sql, $conn);

if (!$result) {
    echo json_encode([
        "process_status"  => "NO",
        "process_message" => "Query Error: " . mysql_error()
    ]);
    exit;
}

$data = [];
$i = 0;
while ($row = mysql_fetch_assoc($result)) {
    $monthNumber = $row['month'];
    $yearStr = $row['current_year'];
    $monthName = date('F', mktime(0, 0, 0, $monthNumber, 1));
    $displayMonthYear = $monthName . ', ' . $yearStr;

    $asm_status = ($row['asm_approve_status'] == 1) ? 'Approved' : (($row['asm_approve_status'] == 2) ? 'Rejected' : 'Pending');
    $rsm_status = ($row['rsm_approve_status'] == 1) ? 'Approved' : (($row['rsm_approve_status'] == 2) ? 'Rejected' : 'Pending');
    $ex_status = ($row['status'] == 1) ? 'Approved' : (($row['status'] == 2) ? 'Not Approved' : 'Pending');

    $data[] = [
        "Sr. No"              => ++$i,
        "ID"                  => $row['id'],
        "Dealer Code"         => $row['customer_code'],
        "Dealer ID"           => $row['customer_id'],
        "Dealer Name"         => $row['dealer_name'],
        "Month"               => $displayMonthYear,
        "Branch"              => $row['branch'],
        "Lifting Qty"         => $row['lifting_qty'],
        "ASM Code"            => $row['asm_code'],
        "ASM DNS Emp Code"    => $row['asm_dns_emp_code'],
        "ASM Approval Status" => $asm_status,
        "Approved By ASM"     => $row['asm_name'],
        "ASM Approved Date"   => !empty($row['asm_approve_date']) ? date('d-m-Y', strtotime($row['asm_approve_date'])) : 'N/A',
        "RSM Code"            => $row['rsm_code'],
        "RSM DNS Emp Code"    => $row['rsm_dns_emp_code'],
        "RSM Approval Status" => $rsm_status,
        "Approved By RSM"     => $row['rsm_name'],
        "RSM Approved Date"   => !empty($row['rsm_approve_date']) ? date('d-m-Y', strtotime($row['rsm_approve_date'])) : 'N/A',
        "Status"              => $ex_status,
        "Created At"          => !empty($row['created_at']) ? date('d-m-Y', strtotime($row['created_at'])) : 'N/A',
    ];
}

echo json_encode([
    "process_status"  => "YES",
    "process_message" => "Success",
    "total_records"   => count($data),
    "data"            => $data
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

        $sql = "SELECT customer_code FROM customer_route_emp_relation WHERE emp_code = '$empCodeEscaped' and acedns='Y'";
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




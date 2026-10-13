
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Asia/Kolkata');

header('Content-Type: application/json');

include "star_connection.php";


// ======================================
// CHECK MANDATORY FIELD
// ======================================

if (!isset($_GET['customer_id']) || empty($_GET['customer_id'])) {

    echo json_encode([
        "process_status"  => "NO",
        "process_message" => "customer_id is mandatory"
    ]);

    exit;
}


// ======================================
// GET PARAMETERS
// ======================================

$customer_id = mysql_real_escape_string($_GET['customer_id']);

$month = isset($_GET['month'])
    ? mysql_real_escape_string($_GET['month'])
    : null;

$current_year = isset($_GET['current_year'])
    ? mysql_real_escape_string($_GET['current_year'])
    : null;

$status_filter = isset($_GET['status'])
    ? mysql_real_escape_string($_GET['status'])
    : null;


// ======================================
// WHERE CONDITIONS
// ======================================

$where = [];

$where[] = "d.customer_id = '$customer_id'";

if (!empty($month)) {
    $where[] = "d.month = '$month'";
}

if (!empty($current_year)) {
    $where[] = "d.current_year = '$current_year'";
}

if ($status_filter !== null && $status_filter !== '') {
    $where[] = "d.status = '$status_filter'";
}

$where_clause = "WHERE " . implode(" AND ", $where);


// ======================================
// MAIN QUERY
// ======================================

$sql = "

SELECT 

    d.*,

    c.customer_name AS dealer_name,

    COALESCE(asm.emp_name, 'N/A') AS asm_name,

    COALESCE(rsm.emp_name, 'N/A') AS rsm_name

FROM dealer_exclusice d

LEFT JOIN employee_master asm
    ON d.asm_approved_by = asm.emp_code

LEFT JOIN employee_master rsm
    ON d.rsm_approved_by = rsm.emp_code

LEFT JOIN customer_master c
    ON d.customer_id = c.customer_id

$where_clause

ORDER BY d.created_at DESC

";


$result = mysql_query($sql);


// ======================================
// QUERY ERROR
// ======================================

if (!$result) {

    echo json_encode([
        "process_status"  => "NO",
        "process_message" => mysql_error()
    ]);

    exit;
}


// ======================================
// DATE CLEAN FUNCTION
// ======================================

function cleanDateTime($str)
{
    return preg_replace('/\s+:\s+/', ':', trim($str));
}

// function addUniqueEmployee(&$employees, $level, $empCode, $empName)
// {
//     if (!isset($employees[$level])) {
//         return;
//     }

//     if ($empName != '') {
//         $employees[$level][$empCode] = $empName;
//     }
// }

// function getReportingLevelEmployees($empCode, &$employees, &$visited)
// {
//     $empCodeEscaped = mysql_real_escape_string($empCode);

//     if ($empCodeEscaped == '' || in_array($empCodeEscaped, $visited)) {
//         return;
//     }

//     $visited[] = $empCodeEscaped;

//     $empSql = "
//         SELECT emp_code, emp_name, level, reporting_to
//         FROM employee_master
//         WHERE emp_code = '$empCodeEscaped' AND sale_access != 'BD'
//         LIMIT 1
//     ";

//     $empResult = mysql_query($empSql);

//     if (!$empResult || mysql_num_rows($empResult) == 0) {
//         return;
//     }

//     $empRow = mysql_fetch_assoc($empResult);

//     addUniqueEmployee(
//         $employees,
//         trim($empRow['level']),
//         trim($empRow['emp_code']),
//         trim($empRow['emp_name'])
//     );

//     $reportingTo = trim($empRow['reporting_to']);

//     if ($reportingTo == '') {
//         return;
//     }

//     $reportingCodes = explode(',', $reportingTo);

//     foreach ($reportingCodes as $reportingCode) {
//         $reportingCode = trim($reportingCode);

//         if ($reportingCode != '') {
//             getReportingLevelEmployees1($reportingCode, $employees, $visited);
//         }
//     }
// }
// function getReportingLevelEmployees($empCode, &$employees, &$visited)
// {
//     $empCodeEscaped = mysql_real_escape_string($empCode);

//     if ($empCodeEscaped == '' || in_array($empCodeEscaped, $visited)) {
//         return;
//     }

//     $visited[] = $empCodeEscaped;

//     $empSql = "
//         SELECT emp_code, emp_name, level, reporting_to
//         FROM employee_master
//         WHERE emp_code = '$empCodeEscaped' AND sale_access != 'BD'
//         LIMIT 1
//     ";

//     $empResult = mysql_query($empSql);

//     if (!$empResult || mysql_num_rows($empResult) == 0) {
//         return;
//     }

//     $empRow = mysql_fetch_assoc($empResult);

//     addUniqueEmployee(
//         $employees,
//         trim($empRow['level']),
//         trim($empRow['emp_code']),
//         trim($empRow['emp_name'])
//     );

//     $reportingTo = trim($empRow['reporting_to'], " '\""); // also strips stray quotes like 'E0168'

//     if ($reportingTo == '') {
//         return;
//     }

//     $reportingCodes = explode(',', $reportingTo);

//     foreach ($reportingCodes as $reportingCode) {
//         $reportingCode = trim($reportingCode, " '\"");

//         if ($reportingCode != '' && $reportingCode != $empRow['emp_code']) {
//             // recurse upward — THIS is the fix, keep climbing until top or L3/L4 found
//             getReportingLevelEmployees($reportingCode, $employees, $visited);
//         }
//     }
// }
function addUniqueEmployee(&$employees, $level, $empCode, $empName)
{
    if (!isset($employees[$level])) {
        return;
    }

    if ($empName != '' && !isset($employees[$level][$empCode])) {
        $employees[$level][$empCode] = $empName;
    }
}

// function getReportingLevelEmployees($empCode, &$employees, &$visited, &$l4Found)
// {
//     $empCodeEscaped = mysql_real_escape_string($empCode);

//     if ($empCodeEscaped == '' || in_array($empCodeEscaped, $visited)) {
//         return;
//     }

//     $visited[] = $empCodeEscaped;

//     $empSql = "
//         SELECT emp_code, emp_name, level, reporting_to
//         FROM employee_master
//         WHERE emp_code = '$empCodeEscaped'
//         LIMIT 1
//     ";

//     $empResult = mysql_query($empSql);

//     if (!$empResult || mysql_num_rows($empResult) == 0) {
//         return;
//     }

//     $empRow = mysql_fetch_assoc($empResult);

//     $level = trim($empRow['level']);

//     if ($level == 'L3') {
//         addUniqueEmployee($employees, 'L3', trim($empRow['emp_code']), trim($empRow['emp_name']));
//     }

//     if ($level == 'L4') {
//         if (!$l4Found) {
//             addUniqueEmployee($employees, 'L4', trim($empRow['emp_code']), trim($empRow['emp_name']));
//             $l4Found = true;
//         }
//         return;
//     }

//     $reportingTo = trim($empRow['reporting_to'], " '\"");

//     if ($reportingTo == '') {
//         return;
//     }

//     $reportingCodes = explode(',', $reportingTo);

//     foreach ($reportingCodes as $reportingCode) {

//         // Stop checking further codes in THIS immediate reporting_to list once L4 is found
//         if ($l4Found) {
//             break;
//         }

//         $reportingCode = trim($reportingCode, " '\"");

//         if ($reportingCode == '' || $reportingCode == $empRow['emp_code']) {
//             continue;
//         }

//         getReportingLevelEmployees($reportingCode, $employees, $visited, $l4Found);
//     }
// }

function getReportingLevelEmployees($empCode, &$employees, &$visited, &$l4Found, &$pathLog)
{
    $empCodeEscaped = mysql_real_escape_string($empCode);

    if ($empCodeEscaped == '') {
        return;
    }

    $empSql = "
        SELECT emp_code, emp_name, level, reporting_to
        FROM employee_master
        WHERE emp_code = '$empCodeEscaped' AND sale_access='Primary'
        LIMIT 1
    ";

    $empResult = mysql_query($empSql);

    if (!$empResult || mysql_num_rows($empResult) == 0) {
        $pathLog[] = "-> NOT FOUND: $empCode";
        return;
    }

    $empRow = mysql_fetch_assoc($empResult);

    $pathLog[] = "-> BASE: {$empRow['emp_code']} ({$empRow['emp_name']}) level=" . trim($empRow['level']) . " reporting_to={$empRow['reporting_to']}";

    $reportingTo = trim($empRow['reporting_to'], " '\"");

    if ($reportingTo == '') {
        return;
    }

    $level1Codes = array_filter(array_map(function ($c) {
        return trim($c, " '\"");
    }, explode(',', $reportingTo)));

    $level2Queue = []; // collect reporting_to of level-1 people, in case we need to climb one more level

    // ---------- LEVEL 1: immediate reporting_to ----------
    foreach ($level1Codes as $reportingCode) {

        if ($reportingCode == '' || $reportingCode == $empRow['emp_code']) {
            continue;
        }

        if (in_array($reportingCode, $visited)) {
            continue;
        }

        $visited[] = $reportingCode;

        $repCodeEscaped = mysql_real_escape_string($reportingCode);

        $repSql = "
            SELECT emp_code, emp_name, level, reporting_to
            FROM employee_master
            WHERE emp_code = '$repCodeEscaped' AND sale_access='Primary'
            LIMIT 1
        ";

        $repResult = mysql_query($repSql);

        if (!$repResult || mysql_num_rows($repResult) == 0) {
            $pathLog[] = "  -> NOT FOUND: $reportingCode";
            continue;
        }

        $repRow   = mysql_fetch_assoc($repResult);
        $repLevel = trim($repRow['level']);

        $pathLog[] = "  -> {$repRow['emp_code']} ({$repRow['emp_name']}) level=$repLevel";

        if ($repLevel == 'L3') {
            addUniqueEmployee($employees, 'L3', trim($repRow['emp_code']), trim($repRow['emp_name']));
        }

        if ($repLevel == 'L4') {
            if (!$l4Found) {
                addUniqueEmployee($employees, 'L4', trim($repRow['emp_code']), trim($repRow['emp_name']));
                $l4Found = true;
                $pathLog[] = "     ^^ L4 MATCH HERE (level 1)";
            }
        }

        // stash this person's own reporting_to, in case we need level 2
        $level2Queue[] = [
            'code'         => $repRow['emp_code'],
            'reporting_to' => trim($repRow['reporting_to'], " '\"")
        ];
    }

    // ---------- LEVEL 2: only if no L4 found at level 1 ----------
    if (!$l4Found) {

        foreach ($level2Queue as $level1Person) {

            if ($l4Found) {
                break;
            }

            if ($level1Person['reporting_to'] == '') {
                continue;
            }

            $level2Codes = array_filter(array_map(function ($c) {
                return trim($c, " '\"");
            }, explode(',', $level1Person['reporting_to'])));

            foreach ($level2Codes as $reportingCode2) {

                if ($l4Found) {
                    break;
                }

                if ($reportingCode2 == '' || $reportingCode2 == $level1Person['code']) {
                    continue;
                }

                if (in_array($reportingCode2, $visited)) {
                    continue;
                }

                $visited[] = $reportingCode2;

                $repCodeEscaped2 = mysql_real_escape_string($reportingCode2);

                $repSql2 = "
                    SELECT emp_code, emp_name, level
                    FROM employee_master
                    WHERE emp_code = '$repCodeEscaped2'
                    LIMIT 1
                ";

                $repResult2 = mysql_query($repSql2);

                if (!$repResult2 || mysql_num_rows($repResult2) == 0) {
                    $pathLog[] = "    -> NOT FOUND: $reportingCode2";
                    continue;
                }

                $repRow2   = mysql_fetch_assoc($repResult2);
                $repLevel2 = trim($repRow2['level']);

                $pathLog[] = "    -> {$repRow2['emp_code']} ({$repRow2['emp_name']}) level=$repLevel2 (level 2, from {$level1Person['code']})";

                if ($repLevel2 == 'L4') {
                    addUniqueEmployee($employees, 'L4', trim($repRow2['emp_code']), trim($repRow2['emp_name']));
                    $l4Found = true;
                    $pathLog[] = "       ^^ L4 MATCH HERE (level 2)";
                }
            }
        }
    }
}
// function getReportingLevelEmployees1($empCode, &$employees, &$visited)
// {
//     $empCodeEscaped = mysql_real_escape_string($empCode);

//     if ($empCodeEscaped == '' || in_array($empCodeEscaped, $visited)) {
//         return;
//     }

//     $visited[] = $empCodeEscaped;

//     $empSql = "
//         SELECT emp_code, emp_name, level, reporting_to
//         FROM employee_master
//         WHERE emp_code = '$empCodeEscaped' AND sale_access != 'BD'
//         LIMIT 1
//     ";

//     $empResult = mysql_query($empSql);

//     if (!$empResult || mysql_num_rows($empResult) == 0) {
//         return;
//     }

//     $empRow = mysql_fetch_assoc($empResult);

//     addUniqueEmployee(
//         $employees,
//         trim($empRow['level']),
//         trim($empRow['emp_code']),
//         trim($empRow['emp_name'])
//     );

//     $reportingTo = trim($empRow['reporting_to']);

//     if ($reportingTo == '') {
//         return;
//     }

//     $reportingCodes = explode(',', $reportingTo);

//     foreach ($reportingCodes as $reportingCode) {
//         $reportingCode = trim($reportingCode);

//         if ($reportingCode != '') {
//             //getReportingLevelEmployees($reportingCode, $employees, $visited);
//         }
//     }
// }

// function getCustomerLevelEmployeeNames($customerId)
// {
//     $customerIdEscaped = mysql_real_escape_string($customerId);

//     $employees = [
//         'L3' => [],
//         'L4' => []
//     ];

//     $mappedSql = "
//         SELECT DISTINCT emp_code
//         FROM customer_route_emp_relation
//         WHERE customer_code = '$customerIdEscaped'
//             AND acedns = 'Y'
//     ";

//     $mappedResult = mysql_query($mappedSql);

//     if (!$mappedResult) {
//         return [
//             'L3' => 'N/A',
//             'L4' => 'N/A'
//         ];
//     }

//     while ($mappedRow = mysql_fetch_assoc($mappedResult)) {
//         $visited = [];
//         getReportingLevelEmployees($mappedRow['emp_code'], $employees, $visited);
//     }
    

//     return [
//         'L3' => !empty($employees['L3']) ? implode(', ', array_values($employees['L3'])) : 'N/A',
//         'L4' => !empty($employees['L4']) ? implode(', ', array_values($employees['L4'])) : 'N/A'
//     ];
// }
function getCustomerLevelEmployeeNames($customerId)
{
    $customerIdEscaped = mysql_real_escape_string($customerId);

    $employees = array(
        'L3' => array(),
        'L4' => array()
    );

    $mappedSql = "
        SELECT DISTINCT emp_code
        FROM customer_route_emp_relation
        WHERE customer_code = '$customerIdEscaped'
        AND acedns = 'Y'
    ";

    $mappedResult = mysql_query($mappedSql);

    if (!$mappedResult) {
        return array(
            'L3' => 'N/A',
            'L4' => 'N/A'
        );
    }

 while ($mappedRow = mysql_fetch_assoc($mappedResult)) {

    $visited    = array();
    $stopSearch = false;
    $pathLog    = array();

    getReportingLevelEmployees(
        $mappedRow['emp_code'],
        $employees,
        $visited,
        $stopSearch,
        $pathLog
    );

    // echo "<pre>ROOT: {$mappedRow['emp_code']}\n" . implode("\n", $pathLog) . "\n\n</pre>";
}

    return array(
        'L3' => !empty($employees['L3']) ? implode(', ', array_values($employees['L3'])) : 'N/A',
        'L4' => !empty($employees['L4']) ? implode(', ', array_values($employees['L4'])) : 'N/A'
    );
}



// echo "<pre>ROOT: {$mappedRow['emp_code']}\n" . implode("\n", $pathLog) . "\n\n</pre>";
// $pathLog = []; // reset per root
// ======================================
// RESPONSE DATA
// ======================================

$data = [];

$levelEmployeeNames = getCustomerLevelEmployeeNames($customer_id);

// print_r($levelEmployeeNames );die;

while ($row = mysql_fetch_assoc($result)) {

    // ======================================
    // ASM STATUS
    // ======================================

    if ($row['asm_approve_status'] == 0) {

        $asm_status = 'Pending';

    } elseif ($row['asm_approve_status'] == 1) {

        $asm_status = 'Approved';

    } else {

        $asm_status = 'Rejected';
    }


    // ======================================
    // RSM STATUS
    // ======================================

    if ($row['rsm_approve_status'] == 0) {

        $rsm_status = 'Pending';

    } elseif ($row['rsm_approve_status'] == 1) {

        $rsm_status = 'Approved';

    } else {

        $rsm_status = 'Rejected';
    }


    // ======================================
    // FINAL STATUS
    // ======================================

    if ($row['status'] == 0) {

        $status = 'Pending';

    } elseif ($row['status'] == 1) {

        $status = 'Approved';

    } else {

        $status = 'Rejected';
    }


    // ======================================
    // MONTH FORMAT
    // ======================================

    $monthNumber = $row['month'];

    $year = $row['current_year'];

    $monthName = date('F', mktime(0, 0, 0, $monthNumber, 1));

    $displayMonthYear = $monthName . ', ' . $year;


    // ======================================
    // RESPONSE ARRAY
    // ======================================
    if($levelEmployeeNames['L3']=='N/A'){
        $levelEmployeeNames['L3']='Vacant';
    }
    if($levelEmployeeNames['L4']=='N/A'){
        $levelEmployeeNames['L4']='Vacant';
    }

    $data[] = [

        "Sr_No"               => $row['id'],

        "Dealer_Code"          => $row['customer_code'],

        "Dealer_ID"            => $row['customer_id'],

        "Dealer_Name"          => $row['dealer_name'],

        "L3_Employee_Name"     => $levelEmployeeNames['L3'],

        "L4_Employee_Name"     => $levelEmployeeNames['L4'],

        "Month"                => $displayMonthYear,

        "Branch"               => $row['branch'],

        "Lifting_Qty"          => $row['lifting_qty'],

        "ASM_Approval_Status"  => $asm_status,

        "Approved_By_ASM"      => !empty($row['asm_approve_date'])
            ?  $row['asm_name']
            : $levelEmployeeNames['L3'],
       // "Approved_By_ASM"      => $row['asm_name'],

        "ASM_Approved_Date"    => !empty($row['asm_approve_date'])
            ? date(
                'd-m-Y',
                strtotime(cleanDateTime($row['asm_approve_date']))
            )
            : 'N/A',

        "RSM_Approval_Status"  => $rsm_status,

       // "Approved_By_RSM"      => $row['rsm_name'],
        "Approved_By_RSM"      => !empty($row['rsm_approve_date'])
            ?  $row['rsm_name']
            : $levelEmployeeNames['L4'],

        "RSM_Approved_Date"    => !empty($row['rsm_approve_date'])
            ? date(
                'd-m-Y',
                strtotime(cleanDateTime($row['rsm_approve_date']))
            )
            : 'N/A',

        "Created_At"           => !empty($row['created_at'])
            ? date(
                'd-m-Y',
                strtotime(cleanDateTime($row['created_at']))
            )
            : '',

        "Status"               => $status,

        "Reason_for_rejection" => $row['reject_reason'],
    ];
}


// ======================================
// FINAL RESPONSE
// ======================================

echo json_encode([

    "process_status"  => "YES",

    "process_message" => "Success",

    "total_records"   => count($data),

    "data"            => $data

]);


mysql_close($conn);

exit;

?>


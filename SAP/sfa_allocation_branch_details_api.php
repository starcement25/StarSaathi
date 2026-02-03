<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include "star_connection.php";
header("Content-Type: application/json");
// Query
/*$sql = "SELECT branch_code, branch_name FROM branch_master WHERE acedns = 'Y' ORDER BY branch_name";
$result = mysql_query($sql);

// Prepare output
$data = [];
if ($result && mysql_fetch_assoc($result) > 0) {
    while ($row = mysql_fetch_assoc($result)) {
        $data[] = [
            "code" => $row["branch_code"],
            "name" => $row["branch_name"]
        ];
    }
}*/
// Sanitize input
function getParam($key) {
    return isset($_REQUEST[$key]) ? addslashes(trim($_REQUEST[$key])) : "";
}
$emp_code = getParam("emp_code");


function getEmployeeFlatTree($managerCode, &$visited = []) {
    $employees = [];

    // Escape input
    $managerCodeEscaped = mysql_real_escape_string($managerCode);

    // If already visited, skip
    if (in_array($managerCodeEscaped, $visited)) {
        return [];
    }
    $visited[] = $managerCodeEscaped;

    // Get current employee info
    $selfSql = "SELECT emp_code, emp_name, dns_emp_code,level FROM employee_master WHERE emp_code = '$managerCodeEscaped' LIMIT 1";
    $selfResult = mysql_query($selfSql);
    if ($selfRow = mysql_fetch_assoc($selfResult)) {
        $employees[] = [
            'emp_code' => $selfRow['emp_code'],
            'emp_name' => $selfRow['emp_name'],
            'dns_emp_code' => $selfRow['dns_emp_code'],
            'level' => $selfRow['level'],
        ];
    }

    // Get direct reports
    $sql = "SELECT emp_code FROM employee_master WHERE reporting_to LIKE '%$managerCodeEscaped%' AND (level='L2' OR level='L1')";
    $result = mysql_query($sql);

    while ($row = mysql_fetch_assoc($result)) {
        $empCode = $row['emp_code'];
        $employees = array_merge($employees, getEmployeeFlatTree($empCode, $visited));
    }

    return $employees;
}

$tree = getEmployeeFlatTree($emp_code);
$empCodes = array_column($tree, 'emp_code');
$dns_emp_code = array_column($tree, 'dns_emp_code');
//echo "<pre>";print_r($tree);die;
$in_clause = '';
if (!empty($empCodes)) {
    $in_clause = "'" . implode("','", $empCodes) . "'";
}

// Step 4: Run final query
$customer_code = [];
//echo "<pre>"; print_r($emp_code);die;
//$sql3 = "SELECT customer_code FROM customer_route_emp_relation WHERE emp_code = '".$emp_code."' AND customer_code != ''";
$sql3 = "SELECT customer_code FROM customer_route_emp_relation WHERE emp_code IN ($in_clause) AND customer_code != ''";
//echo "<pre>"; print_r($sql3);die;

/*
if (!empty($in_clause)) {
    $sql3 .= " UNION SELECT customer_code FROM customer_route_emp_relation WHERE emp_code IN ($in_clause)";
}*/

$result3 = mysql_query($sql3);

if ($result3 && mysql_num_rows($result3) > 0) {
    while ($row3 = mysql_fetch_assoc($result3)) {
        $customer_code[] = $row3["customer_code"];
    }
}
//echo"<pre>";print_r($customer_code);die;
$customer_id_in_clause = '';
if (!empty($customer_code)) {
    $customer_id_in_clause = "'" . implode("','", $customer_code) . "'";
}else{
    echo json_encode([
        'status' => 'success',
        'page' => '0',
        'limit' => '0',
        'total_records' => '0',
        'total_pages' => '0',
        'data' => ''
    ]); exit;
}
$whr_str = "";
if (count($dns_emp_code) > 0) {
    $whr_str .= " customer_id IN($customer_id_in_clause)";
}
//echo "<pre>"; print_r($customer_id_in_clause); die;

$sql = "
SELECT bm.branch_code, bm.branch_name
FROM customer_master cm
LEFT JOIN branch_master bm ON cm.branch_code = bm.branch_code
WHERE cm.customer_id IN($customer_id_in_clause)  GROUP BY branch_code

";
//echo "<pre>"; print_r($sql); die;

$result = mysql_query($sql);
while($row = mysql_fetch_assoc($result)) {
    $branches[] = [
        'branch_code' => $row['branch_code'],
        'branch_name' => $row['branch_name']
    ];

}
//echo "<pre>"; print_r($branches); die;

/*
function getEmployeeBranches($managerCode, &$visited = []) {
    $branches = [];

    $managerCodeEscaped = mysql_real_escape_string($managerCode);

    // Avoid cycles
    if (in_array($managerCodeEscaped, $visited)) {
        return [];
    }
    $visited[] = $managerCodeEscaped;

    // Get current employee's branch
    $sql = "
        SELECT bm.branch_code, bm.branch_name
        FROM employee_master em
        LEFT JOIN branch_master bm ON em.branch_code = bm.branch_code
        WHERE em.emp_code = '$managerCodeEscaped'
        LIMIT 1
    ";
    $result = mysql_query($sql);
    if ($row = mysql_fetch_assoc($result)) {
        $branchKey = $row['branch_code']; // Use branch_code to prevent duplicates
        if (!isset($branches[$branchKey])) {
            $branches[$branchKey] = [
                'branch_code' => $row['branch_code'],
                'branch_name' => $row['branch_name']
            ];
        }
    }

    // Get direct reports
    $childSql = "SELECT emp_code FROM employee_master WHERE reporting_to LIKE '%$managerCodeEscaped%'";
    $childResult = mysql_query($childSql);
    while ($childRow = mysql_fetch_assoc($childResult)) {
        $childCode = $childRow['emp_code'];
        $childBranches = getEmployeeBranches($childCode, $visited);

        // Merge unique branches
        foreach ($childBranches as $code => $bdata) {
            if (!isset($branches[$code])) {
                $branches[] = $bdata;
            }
        }
    }

    return $branches;
}
*/
//$branch=getEmployeeBranches($emp_code);
if(!empty($branches)){
echo json_encode([
    "status" => "success",
    "data" => $branches
]);
}else{
    echo json_encode([
        "status" => "success",
        "data" => 'No data found'
    ]);
}

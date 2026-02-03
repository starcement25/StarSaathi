<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

header('Content-Type: application/json');

date_default_timezone_set('Asia/Kolkata');
//$conn = mysql_connect("172.17.0.2", "root", "Passw0rd123#$");
require_once("starsaathi_connection.php");
$localDB = new starsaathi_connection();
$conn = $localDB->conn;
mysql_select_db("starsaathi_STARS", $conn);

if (!isset($_GET['emp_code'])) {
    echo json_encode(['error' => 'emp_code is required']);
    exit;
}

$emp_Code = trim($_GET['emp_code']);
$emp_Code = mysql_real_escape_string($emp_Code);

$employee = "SELECT * FROM employee_master WHERE emp_code = '$emp_Code'";
$emp_result = mysql_query($employee);

if (!$emp_result) {
    die("Query failed: " . mysql_error());
}

$row_emp = mysql_fetch_assoc($emp_result);


function getEmployeeFlatTree($managerCode, &$visited = [])
{
    $employees = [];


    $managerCodeEscaped = mysql_real_escape_string($managerCode);

    // If already visited, skip
    if (in_array($managerCodeEscaped, $visited)) {
        return [];
    }
    $visited[] = $managerCodeEscaped;

    // Get current employee info
    $selfSql = "SELECT emp_code, emp_name, dns_emp_code ,branch_code FROM employee_master WHERE emp_code = '$managerCodeEscaped' LIMIT 1";
    $selfResult = mysql_query($selfSql);
    if ($selfRow = mysql_fetch_assoc($selfResult)) {
        $employees[] = [
            'emp_code' => $selfRow['emp_code'],
            'emp_name' => $selfRow['emp_name'],
            'dns_emp_code' => $selfRow['dns_emp_code'],
             'branch_code' => $selfRow['branch_code'],
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
        echo $employee['emp_code'] . " ## " . $employee['emp_name'] . " ## " . $employee['dns_emp_code'] . " ## " . $employee['branch_code']. "\n";
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





$tree = getEmployeeFlatTree($row_emp['emp_code'] );
// displayEmployeeList($tree);
//print_r($tree );
// die;
$empCodes = array_column($tree, 'emp_code');


$allCustomerCodes = getAllMappedCustomerCodes($empCodes);


$allCustomerCodesLookup = array_flip($allCustomerCodes);
//  echo '<pre>';
// print_r($empCodes);
//  die;


$matchingRows = [];
$exclusive = "SELECT * FROM dealer_exclusice WHERE 1";
$result = mysql_query($exclusive);

if (!$result) {
    $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => 'Something went wrong');
    echo json_encode($res_data);
}
// echo '<pre>';
// print_r($allCustomerCodesLookup['1000002617']);
// die;
while ($row = mysql_fetch_assoc($result)) {
    if (isset($allCustomerCodesLookup[$row['customer_id']])) {

        // if($row['customer_id']=='1000002617'){
        //     print_r($row);
        //     die;
        // }
        if ($row_emp['level'] == 'L4' && ($row['asm_approve_status'] == 0 || $row['asm_approve_status'] == 2)) {
            continue;
        } else {

            if (isset($row['asm_approve_status'])) {
                switch ($row['asm_approve_status']) {
                    case 0:
                        $row['asm_approve_status'] = 'Pending';
                        break;
                    case 1:
                        $row['asm_approve_status'] = 'Approved';
                        break;
                    case 2:
                        $row['asm_approve_status'] = 'Rejected';
                        break;
                    default:
                        $row['asm_approve_status'] = 'Unknown';
                        break;
                }
            }


            if (isset($row['rsm_approve_status'])) {
                switch ($row['rsm_approve_status']) {
                    case 0:
                        $row['rsm_approve_status'] = 'Pending';
                        break;
                    case 1:
                        $row['rsm_approve_status'] = 'Approved';
                        break;
                    case 2:
                        $row['rsm_approve_status'] = 'Rejected';
                        break;
                    default:
                        $row['rsm_approve_status'] = 'Unknown';
                        break;
                }
            }


            if (isset($row['status'])) {
                switch ($row['status']) {
                    case 0:
                        $row['status'] = 'Pending';
                        break;
                    case 1:
                        $row['status'] = 'Approved';
                        break;
                    case 2:
                        $row['status'] = 'Rejected';
                        break;

                    default:
                        $row['status'] = 'Pending';
                        break;
                }
            }
            $matchingRows[] = $row;
        }
    }
}

// echo '<pre>';
// print_r($allCustomerCodesLookup[$row['customer_id']]);
// die;

// if (empty($matchingRows)) {
//       $res_data = array("process_status" => "Yes", "process_message" => "Success!", 'error' => 'Nothing is found');
//     echo json_encode($res_data);
//     exit;
// }
// foreach ($matchingRows as &$row) {
//     $customer_id = $row['customer_id'];

//     $customer_sql = "SELECT * FROM customer_master WHERE customer_id = $customer_id";
//     $customer_result = mysql_query($customer_sql, $conn);

//     if ($customer_result && mysql_num_rows($customer_result) > 0) {
//         $customer_data = mysql_fetch_assoc($customer_result);
//         $row['customer'] = $customer_data;
//     } else {
//         $row['customer'] = null;
//     }
// }
// unset($row);
if ($row_emp['level'] == 'L4' || $row_emp['level'] == 'L3') {
    $res_data = array("process_status" => "Yes", "process_message" => " Success", 'level' => $row_emp['level'], 'result' => $matchingRows);
    echo json_encode($res_data);
    exit;
} else {
    $res_data = array("process_status" => "No", "process_message" => " Failure", 'level' => $row_emp['level'], 'result' => 'No Data');
    echo json_encode($res_data);
}

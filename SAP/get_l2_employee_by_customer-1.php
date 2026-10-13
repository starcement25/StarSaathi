<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

header('Content-Type: application/json');

include "star_connection.php";

if (!isset($_GET['customer_id']) || empty($_GET['customer_id'])) {

    echo json_encode(array(
        "process_status"=>"NO",
        "process_message"=>"customer_id is mandatory"
    ));
    exit;
}

$customer_id = mysql_real_escape_string($_GET['customer_id']);

function getReportingLevelEmployees($empCode, &$employees, &$visited)
{
    $empCodeEscaped = mysql_real_escape_string($empCode);

    if ($empCodeEscaped=='' || in_array($empCodeEscaped,$visited)) {
        return;
    }

    $visited[] = $empCodeEscaped;

    $sql = "
        SELECT
            emp_code,
            emp_name,
            phone_no,
            level,
            reporting_to
        FROM employee_master
        WHERE emp_code='$empCodeEscaped'
        AND sale_access!='BD'
        LIMIT 1
    ";

    $result = mysql_query($sql);

    if (!$result || mysql_num_rows($result)==0) {
        return;
    }

    $row = mysql_fetch_assoc($result);

    if (trim($row['level'])=='L2') {

        $employees[$row['emp_code']] = array(
            "emp_name"=>trim($row['emp_name']),
            "phone_no"=>trim($row['phone_no'])
        );
    }

    $reportingTo = trim($row['reporting_to']);

    if ($reportingTo!='') {

        $codes = explode(',',$reportingTo);

        foreach($codes as $code){

            $code = trim($code);

            if($code!=''){
                //getReportingLevelEmployees($code,$employees,$visited);
            }
        }
    }
}

function getL2Employees($customer_id)
{
    $employees = array();

    $sql = "
        SELECT DISTINCT emp_code
        FROM customer_route_emp_relation
        WHERE customer_code='$customer_id'
        AND acedns='Y'
    ";

    $result = mysql_query($sql);

    if(!$result){
        return array();
    }

    while($row=mysql_fetch_assoc($result)){

        $visited=array();

        getReportingLevelEmployees(
            $row['emp_code'],
            $employees,
            $visited
        );
    }

    return array_values($employees);
}

$data = getL2Employees($customer_id);
//$data =[];
echo json_encode(array(

    "process_status"=>"YES",
    "process_message"=>"Success",
    "total_records"=>count($data),
    "data"=>$data

));

mysql_close($conn);

?>
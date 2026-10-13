
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


// ======================================
// RESPONSE DATA
// ======================================

$data = [];

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

    $data[] = [

        "Sr_No"               => $row['id'],

        "Dealer_Code"          => $row['customer_code'],

        "Dealer_ID"            => $row['customer_id'],

        "Dealer_Name"          => $row['dealer_name'],

        "Month"                => $displayMonthYear,

        "Branch"               => $row['branch'],

        "Lifting_Qty"          => $row['lifting_qty'],

        "ASM_Approval_Status"  => $asm_status,

        "Approved_By_ASM"      => $row['asm_name'],

        "ASM_Approved_Date"    => !empty($row['asm_approve_date'])
            ? date(
                'd-m-Y',
                strtotime(cleanDateTime($row['asm_approve_date']))
            )
            : 'N/A',

        "RSM_Approval_Status"  => $rsm_status,

        "Approved_By_RSM"      => $row['rsm_name'],

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


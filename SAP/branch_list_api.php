<?php
header("Content-Type: application/json");
date_default_timezone_set('Asia/Kolkata');

include_once("star_connection.php");

/* ===== GET PARAMS ===== */
$page  = isset($_REQUEST['page']) ? (int)$_REQUEST['page'] : 1;
$limit = isset($_REQUEST['limit']) ? (int)$_REQUEST['limit'] : 50;

/* ===== VALIDATION ===== */
if ($page <= 0) $page = 1;
if ($limit <= 0) $limit = 50;

$offset = ($page - 1) * $limit;

/* ===== TOTAL COUNT ===== */
$count_sql = "SELECT COUNT(*) as total 
              FROM branch_master 
              WHERE 1";

$count_res = mysql_query($count_sql);
$count_row = mysql_fetch_assoc($count_res);
$total_records = $count_row['total'];

/* ===== MAIN QUERY ===== */
$sql = "SELECT comp_code, branch_code, dns_branch_code, branch_name, branch_state, acedns 
        FROM branch_master 
        WHERE 1
        ORDER BY branch_name ASC
        LIMIT $limit OFFSET $offset";

$res = mysql_query($sql);

/* ===== DATA FETCH ===== */
$data = array();

while ($row = mysql_fetch_assoc($res)) {
    $data[] = array(
        "comp_code"       => $row['comp_code'],
        "branch_code"     => $row['branch_code'],
        "dns_branch_code"=> $row['dns_branch_code'],
        "branch_name"     => $row['branch_name'],
        "acedns"    => $row['acedns'],
        "branch_state"    => $row['branch_state']
    );
}

/* ===== RESPONSE ===== */
if (count($data) > 0) {
    echo json_encode(array(
        "status" => "true",
        "message" => "Branch list fetched successfully",
        "page" => $page,
        "limit" => $limit,
        "total_records" => $total_records,
        "total_pages" => ceil($total_records / $limit),
        "data" => $data
    ));
} else {
    echo json_encode(array(
        "status" => "false",
        "message" => "No branch found",
        "data" => array()
    ));
}
?>
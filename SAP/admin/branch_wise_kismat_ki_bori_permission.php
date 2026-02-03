<?php 
include "star_connection.php"; // $link must be defined inside

header('Content-Type: application/json');

$res_data = array();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (isset($_GET['customer_id']) && $_GET['customer_id'] != '') {
        $customer_id = mysql_real_escape_string($_GET['customer_id']);

        $sql = "SELECT b.kismat_ki_bori_scheme_status, c.cust_type
            FROM customer_master c
            INNER JOIN branch_kismat_ki_bori_scheme_status b 
                ON c.branch_code = b.branch_code
            WHERE c.customer_id = '$customer_id'
            LIMIT 1";
        $result = mysql_query($sql);

        if ($result && mysql_num_rows($result) > 0) {
            $row = mysql_fetch_assoc($result);
            $status = strtoupper($row['kismat_ki_bori_scheme_status']);

            if ($row['cust_type'] == 'Dealer' || $row['cust_type'] == 'RSSD') {
                if ($status === 'ACTIVE') {
                    $res_data = array(
                        "status" => "YES",
                        "message" => "Kismat Ki Bori Scheme is ACTIVE for branch.",
                        "bag_quantity" => 100
                    );
                } else {
                    $res_data = array(
                        "status" => "NO",
                        "message" => "Kismat Ki Bori Scheme is INACTIVE for branch."
                    );
                }
            } else {
                $res_data = array(
                    "status" => "NO",
                    "message" => "Kismat Ki Bori Scheme is not support to this customer type."
                );
            }
        } else {
            $res_data = array(
                "status" => "NO",
                "message" => "No data found for branch."
            );
        }

    } else {
        http_response_code(400);
        $res_data = array(
            "status" => "NO",
            "message" => "Missing or empty branch_code."
        );
    }

} else {
    http_response_code(405);
    $res_data = array(
        "status" => "NO",
        "message" => "Invalid request method. Use GET."
    );
}

echo json_encode($res_data);
mysql_close($link);
?>

<?php 
include "star_connection.php";


if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $sql = "SELECT `bags_quantity_limit` FROM `kismet_ki_bori_bags_quantity` LIMIT 1";
    $result = mysql_query($sql);

    if ($result && mysql_num_rows($result) > 0) {
        $row = mysql_fetch_assoc($result);
        $data = $row['bags_quantity_limit'];

        http_response_code(200);
        $res_data = array(
            "process_status" => "YES",
            "process_message" => "Bags quantity limit retrieved successfully.",
            "bags_quantity_limit" => $data
        );
    } else {
        http_response_code(400);
        $res_data = array(
            "process_status" => "NO",
            "process_message" => "No bags quantity data found."
        );
    }
} else {
    http_response_code(405);
    $res_data = array(
        "process_status" => "NO",
        "process_message" => "Invalid request method."
    );
}
echo json_encode($res_data);
mysql_close($link);

?>
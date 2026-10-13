<?php

session_start();

include "../star_connection.php";
mysql_query("SET time_zone = '+05:30'");

$dealer_table = "dealer_registration_web_form";

$response = array(
    "exists" => "NO"
);

$contact_no = isset($_POST["contact_no"])
    ? trim($_POST["contact_no"])
    : "";

if ($contact_no != "" && preg_match('/^[0-9]{10}$/', $contact_no)) {

    $contact_no_safe = mysql_real_escape_string($contact_no);

    $sql_check = "
        SELECT `id`
        FROM $dealer_table
        WHERE `contact_no` = '$contact_no_safe'
        LIMIT 1
    ";

    $res_check = mysql_query($sql_check);

    if ($res_check && mysql_num_rows($res_check) > 0) {

        $response["exists"] = "YES";
    }
}

echo json_encode($response);

mysql_close();

?>
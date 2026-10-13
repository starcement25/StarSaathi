<?php

include "web_check.php";
include "star_connection.php";

header("Content-Type: application/json");

$response = array(
    "process_sts" => "NO",
    "process_message" => "",
    "data" => array()
);



$sql = "
    SELECT the_key_name, the_value
    FROM app_setting_master
    WHERE the_key_name IN (
        'rssd_allocation_change',
        'rssd_allocation_change_days'
    )
";

$result = mysql_query($sql);

if (!$result) {

    $response["process_message"] = mysql_error();

    echo json_encode($response);
    mysql_close();
    exit;
}




$rssd_allocation_change = "false";
$rssd_allocation_days = 0;




while ($row = mysql_fetch_assoc($result)) {

    $key_name = trim($row["the_key_name"]);
    $value = trim($row["the_value"]);

    if ($key_name == "rssd_allocation_change") {

        $value = strtolower($value);

        if ($value == "true") {
            $rssd_allocation_change = true;
        } else {
            $rssd_allocation_change = false;
        }
    }

    if ($key_name == "rssd_allocation_change_days") {

        if (is_numeric($value)) {
            $rssd_allocation_days = (int)$value;
        } else {
            $rssd_allocation_days = 0;
        }
    }
}




if ($rssd_allocation_change == false) {
    $rssd_allocation_days = 0;
}




$response["process_sts"] = "YES";
$response["process_message"] = "RSSD allocation settings fetched successfully.";

$response["data"] = array(
    "rssd_allocation_change" => $rssd_allocation_change,
    "rssd_allocation_days" => $rssd_allocation_days
);




echo json_encode($response);

mysql_close();

?>


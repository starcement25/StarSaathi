<?php

include "web_check.php";
include "star_connection.php";

header("Content-Type: application/json");


$response = array(
    "process_sts" => "NO",
    "msg" => ""
);


// ============================================================
// VALIDATE INPUT
// ============================================================

if (!isset($_POST["rssd_allocation_change"])) {

    $response["msg"] =
        "RSSD allocation change value is missing.";

    echo json_encode($response);

    mysql_close();

    exit;
}


$rssd_allocation_change =
    strtolower(
        trim(
            $_POST["rssd_allocation_change"]
        )
    );


// ============================================================
// VALIDATE TRUE / FALSE
// ============================================================

if (
    !in_array(
        $rssd_allocation_change,
        array("true", "false")
    )
) {

    $response["msg"] =
        "Invalid RSSD allocation change value.";

    echo json_encode($response);

    mysql_close();

    exit;
}


// ============================================================
// ESCAPE VALUE
// ============================================================

$rssd_allocation_change =
    mysql_real_escape_string(
        $rssd_allocation_change
    );


// ============================================================
// UPDATE RSSD ALLOCATION CHANGE
// ============================================================

$sql = "

    UPDATE app_setting_master

    SET the_value = '$rssd_allocation_change'

    WHERE the_key_name = 'rssd_allocation_change'

";


$result = mysql_query($sql);


// ============================================================
// CHECK UPDATE
// ============================================================

if ($result) {


    // ========================================================
    // FALSE
    //
    // Automatically reset days to 0
    // ========================================================

    if ($rssd_allocation_change == "false") {


        $sql_days = "

            UPDATE app_setting_master

            SET the_value = '0'

            WHERE the_key_name = 'rssd_allocation_days'

        ";


        mysql_query($sql_days);

    }


    $response["process_sts"] =
        "YES";


    $response["msg"] =
        "RSSD allocation change updated successfully.";


} else {


    $response["msg"] =
        mysql_error();

}


echo json_encode($response);

mysql_close();

?>
<?php

include "web_check.php";
include "star_connection.php";

header("Content-Type: application/json");


$response = array(
    "process_sts" => "NO",
    "msg" => ""
);


// ============================================================
// VALIDATE RSSD ALLOCATION CHANGE
// ============================================================

if (!isset($_POST["rssd_allocation_change"])) {

    $response["msg"] =
        "RSSD allocation change value is required.";

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
// ONLY TRUE / FALSE ALLOWED
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
// VALIDATE DAYS
// ============================================================

if (!isset($_POST["rssd_allocation_days"])) {

    $response["msg"] =
        "RSSD allocation days is required.";

    echo json_encode($response);

    mysql_close();

    exit;
}


$rssd_allocation_days =
    trim(
        $_POST["rssd_allocation_days"]
    );


// ============================================================
// FALSE
//
// DAYS MUST BE 0
// ============================================================

if (
    $rssd_allocation_change == "false"
) {

    $rssd_allocation_days = 0;

}


// ============================================================
// TRUE
//
// DAYS IS MANDATORY
// ============================================================

if (
    $rssd_allocation_change == "true"
) {


    // --------------------------------------------------------
    // EMPTY CHECK
    // --------------------------------------------------------

    if (
        $rssd_allocation_days === ""
    ) {

        $response["msg"] =
            "RSSD Allocation change days is mandatory when RSSD Allocation change is TRUE.";

        echo json_encode($response);

        mysql_close();

        exit;
    }


    // --------------------------------------------------------
    // NUMERIC CHECK
    // --------------------------------------------------------

    if (
        !is_numeric(
            $rssd_allocation_days
        )
    ) {

        $response["msg"] =
            "RSSD Allocation change days must be a valid number.";

        echo json_encode($response);

        mysql_close();

        exit;
    }


    // --------------------------------------------------------
    // POSITIVE CHECK
    // --------------------------------------------------------

    if (
        (int)$rssd_allocation_days <= 0
    ) {

        $response["msg"] =
            "RSSD Allocation change days must be greater than 0.";

        echo json_encode($response);

        mysql_close();

        exit;
    }


    // --------------------------------------------------------
    // INTEGER
    // --------------------------------------------------------

    $rssd_allocation_days =
        (int)$rssd_allocation_days;

}


// ============================================================
// ESCAPE VALUES
// ============================================================

$rssd_allocation_change =
    mysql_real_escape_string(
        $rssd_allocation_change
    );


$rssd_allocation_days =
    mysql_real_escape_string(
        (int)$rssd_allocation_days
    );


// ============================================================
// UPDATE RSSD ALLOCATION CHANGE
// ============================================================

$sql_update_change = "

    UPDATE app_setting_master

    SET the_value = '$rssd_allocation_change'

    WHERE the_key_name = 'rssd_allocation_change'

";


$result_change =
    mysql_query(
        $sql_update_change
    );


// ============================================================
// CHECK FIRST UPDATE
// ============================================================

if (!$result_change) {

    $response["msg"] =
        mysql_error();

    echo json_encode($response);

    mysql_close();

    exit;
}


// ============================================================
// UPDATE RSSD ALLOCATION DAYS
// ============================================================

$sql_update_days = "

    UPDATE app_setting_master

    SET the_value = '$rssd_allocation_days'

    WHERE the_key_name = 'rssd_allocation_change_days'

";


$result_days =
    mysql_query(
        $sql_update_days
    );


// ============================================================
// CHECK SECOND UPDATE
// ============================================================

if (!$result_days) {

    $response["msg"] =
        mysql_error();

    echo json_encode($response);

    mysql_close();

    exit;
}


// ============================================================
// SUCCESS
// ============================================================

$response["process_sts"] =
    "YES";


$response["msg"] =
    "RSSD Allocation settings updated successfully.";


echo json_encode($response);


mysql_close();

?>
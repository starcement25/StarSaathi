<?php

include "web_check.php";
include "star_connection.php";

header("Content-Type: application/json");


$response = array(
    "process_sts" => "NO",
    "msg" => ""
);


// ============================================================
// GET RSSD ALLOCATION CHANGE STATUS
// ============================================================

$sql_status = "

    SELECT the_value

    FROM app_setting_master

    WHERE the_key_name = 'rssd_allocation_change'

    LIMIT 1

";


$res_status =
    mysql_query($sql_status);


$row_status =
    mysql_fetch_assoc($res_status);


$rssd_allocation_change =

    isset($row_status["the_value"])

        ? strtolower(
            trim(
                $row_status["the_value"]
            )
        )

        : "false";



// ============================================================
// VALIDATE INPUT
// ============================================================

if (
    !isset(
        $_POST["str_rssd_allocation_days"]
    )
) {


    $response["msg"] =
        "RSSD allocation days is required.";


    echo json_encode($response);

    mysql_close();

    exit;
}



$rssd_allocation_days =

    trim(
        $_POST["str_rssd_allocation_days"]
    );



// ============================================================
// RSSD ALLOCATION CHANGE = FALSE
//
// DAYS MUST ALWAYS BE 0
// ============================================================

if (
    $rssd_allocation_change == "false"
) {


    $rssd_allocation_days =
        0;

}



// ============================================================
// RSSD ALLOCATION CHANGE = TRUE
//
// DAYS IS MANDATORY
// ============================================================

if (
    $rssd_allocation_change == "true"
) {


    // Empty

    if (
        $rssd_allocation_days === ""
    ) {


        $response["msg"] =
            "RSSD Allocation change days is mandatory when RSSD Allocation change is TRUE.";


        echo json_encode($response);

        mysql_close();

        exit;
    }



    // Numeric

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



    // Positive

    if (
        (int)$rssd_allocation_days <= 0
    ) {


        $response["msg"] =
            "RSSD Allocation change days must be greater than 0.";


        echo json_encode($response);

        mysql_close();

        exit;
    }



    // Integer

    $rssd_allocation_days =
        (int)$rssd_allocation_days;

}



// ============================================================
// FINAL VALUE
// ============================================================

$rssd_allocation_days =
    (int)$rssd_allocation_days;



// ============================================================
// ESCAPE
// ============================================================

$rssd_allocation_days =
    mysql_real_escape_string(
        $rssd_allocation_days
    );



// ============================================================
// UPDATE
// ============================================================

$sql_update = "

    UPDATE app_setting_master

    SET the_value = '$rssd_allocation_days'

    WHERE the_key_name = 'rssd_allocation_days'

";


$result =
    mysql_query($sql_update);



// ============================================================
// RESPONSE
// ============================================================

if ($result) {


    $response["process_sts"] =
        "YES";


    $response["msg"] =
        "RSSD allocation days updated successfully.";


} else {


    $response["msg"] =
        mysql_error();

}



echo json_encode($response);

mysql_close();

?>
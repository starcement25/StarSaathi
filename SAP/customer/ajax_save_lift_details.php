<?php

session_start();

include "../star_connection.php";
mysql_query("SET time_zone = '+05:30'");

$res_msg = array();

$lift_table = "lifting_register_qr_code";




$id = isset($_POST["id"])
    ? (int)$_POST["id"]
    : 0;

$state = isset($_POST["state"])
    ? addslashes(trim($_POST["state"]))
    : "";

$district = isset($_POST["district"])
    ? addslashes(trim($_POST["district"]))
    : "";

$address = isset($_POST["address"])
    ? addslashes(trim($_POST["address"]))
    : "";

$order_date = isset($_POST["order_date"])
    ? addslashes(trim($_POST["order_date"]))
    : "";

$order_qty = isset($_POST["order_qty"])
    ? (int)$_POST["order_qty"]
    : 0;

$cons_stage = isset($_POST["cons_stage"])
    ? addslashes(trim($_POST["cons_stage"]))
    : "";

$question = isset($_POST["question"])
    ? addslashes(trim($_POST["question"]))
    : "";




if ($id <= 0) {

    $res_msg = array(
        "process_sts" => "NO",
        "process_msg" => "Invalid request."
    );

    echo json_encode($res_msg);

    mysql_close();

    exit;
}




if (
    !isset($_SESSION["lift_reg_verified"]) ||
    $_SESSION["lift_reg_verified"] != $id
) {

    $res_msg = array(
        "process_sts" => "NO",
        "process_msg" =>
            "Please verify OTP before submitting details."
    );

    echo json_encode($res_msg);

    mysql_close();

    exit;
}




if (
    $state == "" ||
    $district == "" ||
    $address == "" ||
    // $order_date == "" ||
    // $order_qty <= 0 ||
    $cons_stage == "" ||
    $question == ""
) {

    $res_msg = array(
        "process_sts" => "NO",
        "process_msg" =>
            "Please fill all required fields."
    );

    echo json_encode($res_msg);

    mysql_close();

    exit;
}




$image_name = "";

$image_debug = array();




if (!isset($_FILES["image"])) {

    $res_msg = array(
        "process_sts" => "NO",
        "process_msg" => "Image was not received by server.",
        "image_debug" => array(
            "status" => "NO_FILE",
            "files" => $_FILES
        )
    );

    echo json_encode($res_msg);

    mysql_close();

    exit;
}




$upload_err = $_FILES["image"]["error"];


$image_debug["upload_error_code"] =
    $upload_err;

$image_debug["original_name"] =
    $_FILES["image"]["name"];

$image_debug["tmp_name"] =
    $_FILES["image"]["tmp_name"];

$image_debug["file_size"] =
    $_FILES["image"]["size"];




if ($upload_err != UPLOAD_ERR_OK) {

    $res_msg = array(
        "process_sts" => "NO",
        "process_msg" =>
            "Image upload failed. Error code: " . $upload_err,
        "image_debug" => $image_debug
    );

    echo json_encode($res_msg);

    mysql_close();

    exit;
}




$upload_dir = __DIR__ . "/uploads/lift_register/";

$image_debug["script_dir"] = __DIR__;
$image_debug["upload_dir"] = $upload_dir;




if (!is_dir($upload_dir)) {

    if (!mkdir($upload_dir, 0775, true)) {

        $image_debug["status"] =
            "MKDIR_FAILED";

        $image_debug["mkdir_error"] =
            error_get_last();

        $res_msg = array(
            "process_sts" => "NO",
            "process_msg" =>
                "Unable to create image upload folder.",
            "image_debug" => $image_debug
        );

        echo json_encode($res_msg);

        mysql_close();

        exit;
    }
}


if (!is_dir($upload_dir)) {

    $res_msg = array(
        "process_sts" => "NO",
        "process_msg" =>
            "Image upload directory does not exist.",
        "image_debug" => $image_debug
    );

    echo json_encode($res_msg);

    mysql_close();

    exit;
}



if (!is_writable($upload_dir)) {

    $image_debug["status"] =
        "NOT_WRITABLE";

    $image_debug["directory"] =
        $upload_dir;


    $res_msg = array(
        "process_sts" => "NO",
        "process_msg" =>
            "Image folder is not writable by PHP.",
        "image_debug" => $image_debug
    );

    echo json_encode($res_msg);

    mysql_close();

    exit;
}



$ext = strtolower(
    pathinfo(
        $_FILES["image"]["name"],
        PATHINFO_EXTENSION
    )
);


$allowed = array(
    "jpg",
    "jpeg",
    "png"
);


$image_debug["extension"] =
    $ext;




if (!in_array($ext, $allowed)) {

    $res_msg = array(
        "process_sts" => "NO",
        "process_msg" =>
            "Only JPG, JPEG and PNG images are allowed.",
        "image_debug" => $image_debug
    );

    echo json_encode($res_msg);

    mysql_close();

    exit;
}




$image_name =
    "lift_" .
    $id .
    "." .
    $ext;


$target_file =
    $upload_dir . $image_name;


$image_debug["target_file"] =
    $target_file;




if (
    !move_uploaded_file(
        $_FILES["image"]["tmp_name"],
        $target_file
    )
) {

    $image_debug["status"] =
        "MOVE_FAILED";

    $image_debug["move_error"] =
        error_get_last();


    $res_msg = array(
        "process_sts" => "NO",
        "process_msg" =>
            "Unable to save image on server.",
        "image_debug" => $image_debug
    );

    echo json_encode($res_msg);

    mysql_close();

    exit;
}


if (!file_exists($target_file)) {

    $image_debug["status"] =
        "FILE_NOT_FOUND_AFTER_MOVE";


    $res_msg = array(
        "process_sts" => "NO",
        "process_msg" =>
            "Image move completed but file could not be found.",
        "image_debug" => $image_debug
    );

    echo json_encode($res_msg);

    mysql_close();

    exit;
}



$image_debug["status"] =
    "SUCCESS";

$image_debug["saved_file"] =
    $target_file;



$image_sql_part =
    ", `image`='" . $image_name . "'";




$sql_upd = "
    UPDATE $lift_table
    SET
        `state`='$state',
        `district`='$district',
        `address`='$address',
        `order_date`='$order_date',
        `order_qty`='$order_qty',
        `cons_stage`='$cons_stage',
        `question`='$question'
        $image_sql_part
    WHERE `id`='$id'
    LIMIT 1
";


$res_upd = mysql_query($sql_upd);



if ($res_upd) {



    unset($_SESSION["lift_reg_verified"]);

    unset($_SESSION["lift_reg_id"]);


    $res_msg = array(

        "process_sts" =>
            "YES",

        "process_msg" =>
            "Your details have been saved successfully.",

        "image_name" =>
            $image_name,

        "image_debug" =>
            $image_debug,

        "sql" =>
            $sql_upd
    );

} else {

    $res_msg = array(

        "process_sts" =>
            "NO",

        "process_msg" =>
            "Database update failed.",

        "image_name" =>
            $image_name,

        "image_debug" =>
            $image_debug,

        "db_error" =>
            mysql_error(),

        "sql" =>
            $sql_upd
    );
}



echo json_encode($res_msg);

mysql_close();

?>
<?php

session_start();
set_time_limit(0);

include "../star_connection.php";
mysql_query("SET time_zone = '+05:30'");

$res_msg = array();

$dealer_table = "dealer_registration_web_form";

$upload_debug = array();


/* =========================================================
   GET POST VALUES
========================================================= */

$name = isset($_POST["name"])
    ? addslashes(trim($_POST["name"]))
    : "";

// REMOVED (1): Relation
// $relation = isset($_POST["relation"])
//     ? addslashes(trim($_POST["relation"]))
//     : "";

// NOTE (2): firm_name is now labelled "Shop Name" on the form.
// The field name is unchanged, so the DB column stays firm_name.
$firm_name = isset($_POST["firm_name"])
    ? addslashes(trim($_POST["firm_name"]))
    : "";

// REMOVED (3): Area
// $area = isset($_POST["area"])
//     ? addslashes(trim($_POST["area"]))
//     : "";

// REMOVED (4): District
// $district = isset($_POST["district"])
//     ? addslashes(trim($_POST["district"]))
//     : "";

$contact_no = isset($_POST["contact_no"])
    ? addslashes(trim($_POST["contact_no"]))
    : "";

// REMOVED (5): Kurta size
// $kurta_size = isset($_POST["kurta_size"])
//     ? addslashes(trim($_POST["kurta_size"]))
//     : "";

// REMOVED (6): ID proof type
// $id_proof = isset($_POST["id_proof"])
//     ? addslashes(trim($_POST["id_proof"]))
//     : "";


/* =========================================================
   BASIC VALIDATION
========================================================= */

// OLD (before changes):
// if (
//     $name == "" ||
//     $relation == "" ||
//     $firm_name == "" ||
//     $area == "" ||
//     $district == "" ||
//     $contact_no == "" ||
//     $kurta_size == "" ||
//     $id_proof == ""
// ) {

// NEW:
if (
    $name == "" ||
    $firm_name == "" ||
    $contact_no == ""
) {

    $res_msg = array(
        "process_sts" => "NO",
        "process_msg" => "Please fill all required fields."
    );

    echo json_encode($res_msg);

    mysql_close();

    exit;
}


/* =========================================================
   MOBILE VALIDATION
========================================================= */

if (!preg_match('/^[0-9]{10}$/', $contact_no)) {

    $res_msg = array(
        "process_sts" => "NO",
        "process_msg" =>
            "Please enter a valid 10 digit mobile number."
    );

    echo json_encode($res_msg);

    mysql_close();

    exit;
}


/* =========================================================
   MOBILE UNIQUENESS VALIDATION
========================================================= */

$sql_dupe_check = "
    SELECT `id`
    FROM $dealer_table
    WHERE `contact_no` = '$contact_no'
    LIMIT 1
";

$res_dupe_check = mysql_query($sql_dupe_check);

if ($res_dupe_check && mysql_num_rows($res_dupe_check) > 0) {

    $res_msg = array(
        "process_sts" => "NO",
        "process_msg" =>
            "This phone number is already registered."
    );

    echo json_encode($res_msg);

    mysql_close();

    exit;
}


/* =========================================================
   RELATION VALIDATION
   REMOVED (1)
========================================================= */

// $allowed_relations = array(
//     "Self",
//     "Family/Friends"
// );
//
// if (!in_array($relation, $allowed_relations)) {
//
//     $res_msg = array(
//         "process_sts" => "NO",
//         "process_msg" =>
//             "Please select a valid relation."
//     );
//
//     echo json_encode($res_msg);
//
//     mysql_close();
//
//     exit;
// }


/* =========================================================
   KURTA SIZE VALIDATION
   REMOVED (5)
========================================================= */

// $allowed_kurta_sizes = array(
//     "S",
//     "M",
//     "L",
//     "XL",
//     "XXL",
//     "XXXL",
//     "XXXXL"
// );
//
// if (!in_array($kurta_size, $allowed_kurta_sizes)) {
//
//     $res_msg = array(
//         "process_sts" => "NO",
//         "process_msg" =>
//             "Please select a valid kurta size."
//     );
//
//     echo json_encode($res_msg);
//
//     mysql_close();
//
//     exit;
// }


/* =========================================================
   ID PROOF VALIDATION
   REMOVED (6)
========================================================= */

// $allowed_id_proofs = array(
//     "Aadhar Card",
//     "Passport",
//     "Driving License"
// );
//
// if (!in_array($id_proof, $allowed_id_proofs)) {
//
//     $res_msg = array(
//         "process_sts" => "NO",
//         "process_msg" =>
//             "Please select a valid ID proof type."
//     );
//
//     echo json_encode($res_msg);
//
//     mysql_close();
//
//     exit;
// }


/* =========================================================
   DETERMINE WHETHER BACK PHOTO IS REQUIRED
   REMOVED (6): depends on ID proof type
========================================================= */

// $back_photo_required = true;
//
// // Driving License: only front photo is required.
// if ($id_proof == "Driving License") {
//
//     $back_photo_required = false;
// }


/* =========================================================
   SERVER-SIDE PHOTO VALIDATION
========================================================= */


/*
 * SELFIE PHOTO
 * Always required.
 */

if (
    !isset($_FILES["selfie_photo"]) ||
    $_FILES["selfie_photo"]["error"] != UPLOAD_ERR_OK
) {

    $res_msg = array(
        "process_sts" => "NO",
        "process_msg" =>
            "Selfie photo is required."
    );

    echo json_encode($res_msg);

    mysql_close();

    exit;
}


/*
 * ID FRONT PHOTO
 * REMOVED (6)
 */

// if (
//     !isset($_FILES["id_photo_front"]) ||
//     $_FILES["id_photo_front"]["error"] != UPLOAD_ERR_OK
// ) {
//
//     $res_msg = array(
//         "process_sts" => "NO",
//         "process_msg" =>
//             "ID proof photo is required."
//     );
//
//     echo json_encode($res_msg);
//
//     mysql_close();
//
//     exit;
// }


/*
 * ID BACK PHOTO
 * REMOVED (6)
 *
 * Was required for Aadhar Card and Passport,
 * not required for Driving License.
 */

// if ($back_photo_required) {
//
//     if (
//         !isset($_FILES["id_photo_back"]) ||
//         $_FILES["id_photo_back"]["error"] != UPLOAD_ERR_OK
//     ) {
//
//         $res_msg = array(
//             "process_sts" => "NO",
//             "process_msg" =>
//                 "ID proof back photo is required."
//         );
//
//         echo json_encode($res_msg);
//
//         mysql_close();
//
//         exit;
//     }
// }


/* =========================================================
   ALLOWED IMAGE EXTENSIONS
========================================================= */

$allowed_ext = array(
    "jpg",
    "jpeg",
    "png"
);


/* =========================================================
   UPLOAD DIRECTORY
========================================================= */

$upload_dir =
    __DIR__ . "/uploads/dealer_register/";

$upload_debug["script_dir"] =
    __DIR__;

$upload_debug["upload_dir"] =
    $upload_dir;


/* =========================================================
   CREATE UPLOAD DIRECTORY
========================================================= */

if (!is_dir($upload_dir)) {

    if (!mkdir($upload_dir, 0775, true)) {

        $upload_debug["status"] =
            "MKDIR_FAILED";

        $upload_debug["mkdir_error"] =
            error_get_last();

        $res_msg = array(
            "process_sts" => "NO",
            "process_msg" =>
                "Unable to create image upload folder.",
            "upload_debug" =>
                $upload_debug
        );

        echo json_encode($res_msg);

        mysql_close();

        exit;
    }
}


/* =========================================================
   CHECK WRITE PERMISSION
========================================================= */

if (!is_writable($upload_dir)) {

    $upload_debug["status"] =
        "NOT_WRITABLE";

    $res_msg = array(
        "process_sts" => "NO",
        "process_msg" =>
            "Image folder is not writable by PHP.",
        "upload_debug" =>
            $upload_debug
    );

    echo json_encode($res_msg);

    mysql_close();

    exit;
}


/* =========================================================
   INSERT BASIC RECORD
========================================================= */

// OLD (before changes):
// $sql_ins = "
//     INSERT INTO $dealer_table
//         (
//             `name`,
//             `relation`,
//             `firm_name`,
//             `area`,
//             `district`,
//             `contact_no`,
//             `size`,
//             `id_proof`
//         )
//     VALUES
//         (
//             '$name',
//             '$relation',
//             '$firm_name',
//             '$area',
//             '$district',
//             '$contact_no',
//             '$kurta_size',
//             '$id_proof'
//         )
// ";

// NEW: only the fields that are still on the form.
// The removed columns are left out, so they must allow NULL
// or have a default value (see the ALTER TABLE note).
$sql_ins = "
    INSERT INTO $dealer_table
        (
            `name`,
            `firm_name`,
            `contact_no`
        )
    VALUES
        (
            '$name',
            '$firm_name',
            '$contact_no'
        )
";


$res_ins = mysql_query($sql_ins);


if (!$res_ins) {

    $res_msg = array(
        "process_sts" => "NO",
        "process_msg" =>
            "Database insert failed.",
        "db_error" =>
            mysql_error(),
        "sql" =>
            $sql_ins
    );

    echo json_encode($res_msg);

    mysql_close();

    exit;
}


$new_id = mysql_insert_id();


/* =========================================================
   ROLLBACK FUNCTION
========================================================= */

function fail_and_rollback(
    $dealer_table,
    $new_id,
    $process_msg,
    $upload_debug
) {

    mysql_query(
        "DELETE FROM $dealer_table
         WHERE `id`='" . (int)$new_id . "'
         LIMIT 1"
    );


    $res_msg = array(
        "process_sts" => "NO",
        "process_msg" => $process_msg,
        "upload_debug" => $upload_debug
    );


    echo json_encode($res_msg);

    mysql_close();

    exit;
}


/* =========================================================
   IMAGE UPLOAD FUNCTION
========================================================= */

function handle_dealer_image_upload(
    $field_key,
    $filename_no_ext,
    $upload_dir,
    $allowed_ext,
    $dealer_table,
    $new_id,
    &$upload_debug
) {


    if (!isset($_FILES[$field_key])) {

        fail_and_rollback(
            $dealer_table,
            $new_id,
            "Image was not received by server: " . $field_key,
            $upload_debug
        );
    }


    $upload_err =
        $_FILES[$field_key]["error"];


    $upload_debug[$field_key]["upload_error_code"] =
        $upload_err;

    $upload_debug[$field_key]["original_name"] =
        $_FILES[$field_key]["name"];

    $upload_debug[$field_key]["file_size"] =
        $_FILES[$field_key]["size"];


    if ($upload_err != UPLOAD_ERR_OK) {

        fail_and_rollback(
            $dealer_table,
            $new_id,
            "Image upload failed for " .
                $field_key .
                ". Error code: " .
                $upload_err,
            $upload_debug
        );
    }


    $ext = strtolower(
        pathinfo(
            $_FILES[$field_key]["name"],
            PATHINFO_EXTENSION
        )
    );


    $upload_debug[$field_key]["extension"] =
        $ext;


    if (!in_array($ext, $allowed_ext)) {

        fail_and_rollback(
            $dealer_table,
            $new_id,
            "Only JPG, JPEG and PNG images are allowed for " .
                $field_key .
                ".",
            $upload_debug
        );
    }


    $image_name =
        $filename_no_ext .
        "." .
        $ext;


    $target_file =
        $upload_dir .
        $image_name;


    $upload_debug[$field_key]["target_file"] =
        $target_file;


    if (
        !move_uploaded_file(
            $_FILES[$field_key]["tmp_name"],
            $target_file
        )
    ) {

        $upload_debug[$field_key]["status"] =
            "MOVE_FAILED";

        $upload_debug[$field_key]["move_error"] =
            error_get_last();


        fail_and_rollback(
            $dealer_table,
            $new_id,
            "Unable to save image on server: " .
                $field_key,
            $upload_debug
        );
    }


    $upload_debug[$field_key]["status"] =
        "SUCCESS";

    $upload_debug[$field_key]["saved_file"] =
        $target_file;


    return $image_name;
}


/* =========================================================
   ID PROOF SLUG
   REMOVED (6)
========================================================= */

// $id_proof_slug =
//     preg_replace(
//         '/[^A-Za-z0-9]+/',
//         '_',
//         $id_proof
//     );
//
// $id_proof_slug =
//     trim(
//         $id_proof_slug,
//         '_'
//     );


/* =========================================================
   UPLOAD SELFIE
========================================================= */

$selfie_photo_name =
    handle_dealer_image_upload(
        "selfie_photo",
        "selfie_" . $new_id,
        $upload_dir,
        $allowed_ext,
        $dealer_table,
        $new_id,
        $upload_debug
    );


/* =========================================================
   UPLOAD ID FRONT
   REMOVED (6)
========================================================= */

// $id_photo_front_name =
//     handle_dealer_image_upload(
//         "id_photo_front",
//         "id_" . $new_id . "_front",
//         $upload_dir,
//         $allowed_ext,
//         $dealer_table,
//         $new_id,
//         $upload_debug
//     );


/* =========================================================
   UPLOAD ID BACK
   REMOVED (6)
========================================================= */

// $id_photo_back_name = "";
//
// // Only upload ID back photo when required.
// // Aadhar Card -> YES, Passport -> YES, Driving License -> NO
//
// if ($back_photo_required) {
//
//     $id_photo_back_name =
//         handle_dealer_image_upload(
//             "id_photo_back",
//             "id_" . $new_id . "_back",
//             $upload_dir,
//             $allowed_ext,
//             $dealer_table,
//             $new_id,
//             $upload_debug
//         );
// }


/* =========================================================
   UPDATE DATABASE
========================================================= */

// OLD (before changes):
// $sql_upd = "
//     UPDATE $dealer_table
//     SET
//         `selfie_photo`='$selfie_photo_name',
//         `id_photo_front`='$id_photo_front_name',
//         `id_photo_back`='$id_photo_back_name'
//     WHERE `id`='$new_id'
//     LIMIT 1
// ";

// NEW: only the selfie photo is stored now.
$sql_upd = "
    UPDATE $dealer_table
    SET
        `selfie_photo`='$selfie_photo_name'
    WHERE `id`='$new_id'
    LIMIT 1
";


$res_upd =
    mysql_query($sql_upd);


/* =========================================================
   FINAL RESPONSE
========================================================= */

if ($res_upd) {

    $res_msg = array(

        "process_sts" =>
            "YES",

        "process_msg" =>
            "Your details have been saved successfully.",

        "id" =>
            $new_id,

        // REMOVED (6):
        // "id_proof" =>
        //     $id_proof,
        //
        // "back_photo_required" =>
        //     $back_photo_required,

        "upload_debug" =>
            $upload_debug,

        "sql" =>
            $sql_upd
    );

} else {

    /*
     * Database update failed.
     *
     * Delete the inserted record because
     * the complete operation was not successful.
     */

    mysql_query(
        "DELETE FROM $dealer_table
         WHERE `id`='" . (int)$new_id . "'
         LIMIT 1"
    );


    $res_msg = array(

        "process_sts" =>
            "NO",

        "process_msg" =>
            "Database update failed.",

        "upload_debug" =>
            $upload_debug,

        "db_error" =>
            mysql_error(),

        "sql" =>
            $sql_upd
    );
}


/* =========================================================
   OUTPUT JSON
========================================================= */

echo json_encode($res_msg);

mysql_close();

?>
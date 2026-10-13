<?php

include "web_check.php";
include "star_connection.php";


// ============================================================
// RUNTIME LIMITS
//
// This export can touch thousands of files, so the default
// max_execution_time (which produced the HTTP 500) is removed.
// ============================================================

@set_time_limit(0);

@ini_set("max_execution_time", 0);

@ini_set("memory_limit", "512M");

ignore_user_abort(true);


$lift_table = "dealer_registration_web_form";


// ============================================================
// IMAGE FOLDER
//
// The images live on this same server, so they are read
// straight off the disk. The old version fetched every single
// image back over HTTPS from our own host (3 curl calls per
// record); with a large result set that alone took several
// minutes and used up the PHP workers.
// ============================================================

$img_url_path =
    "/SAP/dealer_registration/uploads/dealer_register/";


$doc_root =
    rtrim(
        str_replace(
            "\\",
            "/",
            $_SERVER["DOCUMENT_ROOT"]
        ),
        "/"
    );


$img_dir_fs =
    $doc_root .
    $img_url_path;


// Fallback: resolve relative to this file if DOCUMENT_ROOT
// does not point where we expect.
if (!is_dir($img_dir_fs)) {

    $img_dir_fs =
        dirname(dirname(__FILE__)) .
        "/dealer_registration/uploads/dealer_register/";
}


// ============================================================
// SEARCH FILTERS
// ============================================================

// DATE FROM
$srch_date_from = isset($_GET["srch_date_from"])
    ? addslashes(trim($_GET["srch_date_from"]))
    : "";


// DATE TO
$srch_date_to = isset($_GET["srch_date_to"])
    ? addslashes(trim($_GET["srch_date_to"]))
    : "";


// NAME
$srch_name = isset($_GET["srch_name"])
    ? addslashes(trim($_GET["srch_name"]))
    : "";


// PHONE NO
$srch_phone = isset($_GET["srch_phone"])
    ? addslashes(trim($_GET["srch_phone"]))
    : "";


// ============================================================
// BUILD WHERE CONDITION
// ============================================================

$whr_str = "";


// ============================================================
// FROM DATE
// ============================================================

if ($srch_date_from != "") {

    $aand = (trim($whr_str) != "") ? " AND " : "";

    $whr_str .=
        "$aand $lift_table.created_at >= '$srch_date_from 00:00:00' ";
}


// ============================================================
// TO DATE
// ============================================================

if ($srch_date_to != "") {

    $aand = (trim($whr_str) != "") ? " AND " : "";

    $next_date = date(
        "Y-m-d",
        strtotime($srch_date_to . " +1 day")
    );

    $whr_str .=
        "$aand $lift_table.created_at < '$next_date 00:00:00' ";
}


// ============================================================
// NAME FILTER
// ============================================================

if ($srch_name != "") {

    $aand = (trim($whr_str) != "") ? " AND " : "";

    $whr_str .=
        "$aand $lift_table.name LIKE '%$srch_name%' ";
}


// ============================================================
// PHONE NUMBER FILTER
// ============================================================

if ($srch_phone != "") {

    $aand = (trim($whr_str) != "") ? " AND " : "";

    $whr_str .=
        "$aand $lift_table.contact_no LIKE '%$srch_phone%' ";
}


// ============================================================
// SKIP RECORDS THAT HAVE NO IMAGE AT ALL
// ============================================================

$aand = (trim($whr_str) != "") ? " AND " : "";

$whr_str .=
    "$aand (
        COALESCE($lift_table.selfie_photo, '')   != '' OR
        COALESCE($lift_table.id_photo_front, '') != '' OR
        COALESCE($lift_table.id_photo_back, '')  != ''
     ) ";


// ============================================================
// FINAL WHERE
// ============================================================

$new_whr_str = ($whr_str != "")
    ? "WHERE " . $whr_str
    : "";


// ============================================================
// GET FILTERED DATA
// ============================================================

$sql = "
    SELECT
        id,
        name,
        selfie_photo,
        id_photo_front,
        id_photo_back,
        created_at
    FROM $lift_table
    $new_whr_str
    ORDER BY created_at DESC
";


$res = mysql_query($sql);


if (!$res) {

    die(
        "Database Query Error: " .
        mysql_error()
    );
}


if (mysql_num_rows($res) == 0) {

    die(
        "No records found for the selected filters."
    );
}


// ============================================================
// CHECK ZIP
// ============================================================

if (!class_exists("ZipArchive")) {

    die(
        "ZipArchive extension is not enabled on the server."
    );
}


// ============================================================
// ZIP FILE
// ============================================================

$timestamp = date("Ymd_His");


$zip_file_name =
    "dealer_register_images_" .
    $timestamp .
    ".zip";


// The archive is built in the system temp folder, NOT inside a
// folder that is later scanned, so it can never add itself.
$zip_path =
    rtrim(sys_get_temp_dir(), "/\\") .
    "/" .
    $zip_file_name;


$zip = new ZipArchive();


$result =
    $zip->open(
        $zip_path,
        ZipArchive::CREATE |
        ZipArchive::OVERWRITE
    );


if ($result !== true) {

    die(
        "Unable to create ZIP. Error code: " .
        $result
    );
}


// ============================================================
// ADD ONE IMAGE TO THE ZIP
//
// Adds the file straight from the uploads folder - no HTTP
// request and no temporary copy on disk.
// ============================================================

function add_image_to_zip(
    $zip,
    $img_dir_fs,
    $file_name,
    $record_folder_name,
    $prefix,
    &$added,
    &$missing
) {

    $file_name = trim($file_name);


    if ($file_name == "") {

        return;
    }


    // basename() keeps a stored value like "../../x" from
    // escaping the uploads folder.
    $base_name = basename(
        str_replace("\\", "/", $file_name)
    );


    $source_path =
        $img_dir_fs .
        $base_name;


    if (
        !is_file($source_path) ||
        !is_readable($source_path)
    ) {

        $missing++;

        return;
    }


    $zip_entry =
        $record_folder_name .
        "/" .
        $prefix .
        $base_name;


    if (
        $zip->addFile(
            $source_path,
            $zip_entry
        )
    ) {

        // Photos are already compressed, so storing them
        // instead of deflating saves a lot of CPU time.
        if (
            method_exists($zip, "setCompressionName") &&
            defined("ZipArchive::CM_STORE")
        ) {

            @$zip->setCompressionName(
                $zip_entry,
                ZipArchive::CM_STORE
            );
        }

        $added++;

    } else {

        $missing++;
    }
}


// ============================================================
// COUNTERS
// ============================================================

$total_records = 0;

$total_images = 0;

$failed_images = 0;

$batch_count = 0;


// ZipArchive keeps every added file open until close(), so the
// archive is flushed periodically to stay under the server's
// open-file limit on very large exports.
$batch_limit = 400;


// ============================================================
// PROCESS RECORDS
// ============================================================

while ($row = mysql_fetch_assoc($res)) {

    $total_records++;


    $id = trim($row["id"]);

    $name = trim($row["name"]);


    // ========================================================
    // SAFE RECORD FOLDER NAME
    // ========================================================

    $safe_name =
        preg_replace(
            "/[^A-Za-z0-9_-]/",
            "_",
            $name
        );


    $record_folder_name =
        "ID_" .
        $id .
        "_" .
        $safe_name;


    // ========================================================
    // SELFIE / ID FRONT / ID BACK
    // ========================================================

    $images = array(

        "Selfie_"   => $row["selfie_photo"],

        "ID_Front_" => $row["id_photo_front"],

        "ID_Back_"  => $row["id_photo_back"]

    );


    foreach ($images as $prefix => $file_name) {

        $before = $total_images;


        add_image_to_zip(
            $zip,
            $img_dir_fs,
            $file_name,
            $record_folder_name,
            $prefix,
            $total_images,
            $failed_images
        );


        if ($total_images > $before) {

            $batch_count++;
        }
    }


    // ========================================================
    // FLUSH THE ARCHIVE EVERY FEW HUNDRED FILES
    // ========================================================

    if ($batch_count >= $batch_limit) {

        $zip->close();


        $zip = new ZipArchive();


        if (
            $zip->open(
                $zip_path,
                ZipArchive::CREATE
            ) !== true
        ) {

            die(
                "Unable to re-open ZIP while writing."
            );
        }


        $batch_count = 0;
    }
}


// ============================================================
// CLOSE ZIP
// ============================================================

if (!$zip->close()) {

    @unlink($zip_path);

    die(
        "Unable to finalise the ZIP file."
    );
}


// ============================================================
// CLOSE DATABASE
// ============================================================

mysql_close();


// ============================================================
// NOTHING TO SEND
// ============================================================

if (
    $total_images == 0 ||
    !file_exists($zip_path)
) {

    @unlink($zip_path);

    die(
        "No image files were found on the server for the " .
        $total_records .
        " matching record(s)."
    );
}


// ============================================================
// DOWNLOAD
// ============================================================

while (ob_get_level() > 0) {

    ob_end_clean();
}


header(
    "Content-Description: File Transfer"
);


header(
    "Content-Type: application/zip"
);


header(
    'Content-Disposition: attachment; filename="' .
    $zip_file_name .
    '"'
);


header(
    "Content-Transfer-Encoding: binary"
);


header(
    "Content-Length: " .
    filesize($zip_path)
);


header(
    "Cache-Control: no-cache, no-store, must-revalidate"
);


header(
    "Pragma: no-cache"
);


header(
    "Expires: 0"
);


flush();


// ============================================================
// STREAM IN CHUNKS
//
// readfile() on a very large archive can blow the memory
// limit, so the file is pushed out 1 MB at a time.
// ============================================================

$fh = fopen($zip_path, "rb");


if ($fh) {

    while (!feof($fh)) {

        echo fread($fh, 1024 * 1024);

        flush();
    }


    fclose($fh);

} else {

    readfile($zip_path);
}


// ============================================================
// CLEAN UP
// ============================================================

@unlink($zip_path);


exit;

?>

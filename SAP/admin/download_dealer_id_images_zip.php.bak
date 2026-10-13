<?php

include "web_check.php";
include "star_connection.php";

$lift_table = "dealer_registration_web_form";


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


// ============================================================
// CHECK ZIP
// ============================================================

if (!class_exists("ZipArchive")) {

    die(
        "ZipArchive extension is not enabled on the server."
    );
}


// ============================================================
// ZIP FILE NAME
// ============================================================

$timestamp = date("Ymd_His");

$zip_file_name =
    "dealer_register_images_" .
    $timestamp .
    ".zip";


// ============================================================
// TEMP DIRECTORY
// ============================================================

$temp_dir =
    sys_get_temp_dir() .
    "/dealer_register_images_" .
    $timestamp .
    "_" .
    mt_rand(1000, 9999);


if (!mkdir($temp_dir, 0777, true)) {

    die(
        "Unable to create temporary directory."
    );
}


// ============================================================
// IMAGE BASE URL
// ============================================================

$base_image_url =
    "https://" .
    $_SERVER["HTTP_HOST"] .
    "/SAP/dealer_registration/uploads/dealer_register/";


// ============================================================
// DOWNLOAD IMAGE
// ============================================================

function download_image(
    $url,
    $destination
) {

    // --------------------------------------------------------
    // CURL
    // --------------------------------------------------------

    if (function_exists("curl_init")) {

        $ch = curl_init();

        curl_setopt(
            $ch,
            CURLOPT_URL,
            $url
        );

        curl_setopt(
            $ch,
            CURLOPT_RETURNTRANSFER,
            true
        );

        curl_setopt(
            $ch,
            CURLOPT_FOLLOWLOCATION,
            true
        );

        curl_setopt(
            $ch,
            CURLOPT_CONNECTTIMEOUT,
            10
        );

        curl_setopt(
            $ch,
            CURLOPT_TIMEOUT,
            60
        );

        curl_setopt(
            $ch,
            CURLOPT_SSL_VERIFYPEER,
            false
        );

        curl_setopt(
            $ch,
            CURLOPT_SSL_VERIFYHOST,
            false
        );

        $data = curl_exec($ch);

        $http_code =
            curl_getinfo(
                $ch,
                CURLINFO_HTTP_CODE
            );

        curl_close($ch);


        if (
            $data !== false &&
            $http_code >= 200 &&
            $http_code < 300
        ) {

            if (
                file_put_contents(
                    $destination,
                    $data
                ) !== false
            ) {

                return true;
            }
        }
    }


    // --------------------------------------------------------
    // FALLBACK
    // --------------------------------------------------------

    $context = stream_context_create(
        array(

            "http" => array(

                "timeout" => 60,

                "follow_location" => true

            ),

            "ssl" => array(

                "verify_peer" => false,

                "verify_peer_name" => false

            )

        )
    );


    $data = @file_get_contents(
        $url,
        false,
        $context
    );


    if ($data !== false) {

        if (
            file_put_contents(
                $destination,
                $data
            ) !== false
        ) {

            return true;
        }
    }


    return false;
}


// ============================================================
// COUNTERS
// ============================================================

$total_records = 0;

$total_images = 0;

$failed_images = 0;


// ============================================================
// PROCESS RECORDS
// ============================================================

if (mysql_num_rows($res) > 0) {

    while ($row = mysql_fetch_assoc($res)) {

        $total_records++;


        // ====================================================
        // BASIC DATA
        // ====================================================

        $id =
            trim($row["id"]);


        $name =
            trim($row["name"]);


        // ====================================================
        // SAFE RECORD FOLDER NAME
        // ====================================================

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


        $record_folder =
            $temp_dir .
            "/" .
            $record_folder_name;


        if (!is_dir($record_folder)) {

            mkdir(
                $record_folder,
                0777,
                true
            );
        }


        // ====================================================
        // SELFIE
        // ====================================================

        $selfie_file =
            trim(
                $row["selfie_photo"]
            );


        if ($selfie_file != "") {

            $selfie_url =
                $base_image_url .
                $selfie_file;


            $selfie_destination =
                $record_folder .
                "/Selfie_" .
                basename($selfie_file);


            if (
                download_image(
                    $selfie_url,
                    $selfie_destination
                )
            ) {

                $total_images++;

            } else {

                $failed_images++;
            }
        }


        // ====================================================
        // ID FRONT
        // ====================================================

        $front_file =
            trim(
                $row["id_photo_front"]
            );


        if ($front_file != "") {

            $front_url =
                $base_image_url .
                $front_file;


            $front_destination =
                $record_folder .
                "/ID_Front_" .
                basename($front_file);


            if (
                download_image(
                    $front_url,
                    $front_destination
                )
            ) {

                $total_images++;

            } else {

                $failed_images++;
            }
        }


        // ====================================================
        // ID BACK
        // ====================================================

        $back_file =
            trim(
                $row["id_photo_back"]
            );


        if ($back_file != "") {

            $back_url =
                $base_image_url .
                $back_file;


            $back_destination =
                $record_folder .
                "/ID_Back_" .
                basename($back_file);


            if (
                download_image(
                    $back_url,
                    $back_destination
                )
            ) {

                $total_images++;

            } else {

                $failed_images++;
            }
        }
    }
}


// ============================================================
// ADD FOLDER TO ZIP
// ============================================================

function add_folder_to_zip(
    $folder,
    &$zip,
    $zip_folder = ""
) {

    if (!is_dir($folder)) {

        return;
    }


    $files =
        scandir($folder);


    if ($files === false) {

        return;
    }


    foreach ($files as $file) {

        if (
            $file == "." ||
            $file == ".."
        ) {

            continue;
        }


        $file_path =
            $folder .
            "/" .
            $file;


        $zip_path =
            $zip_folder .
            $file;


        if (
            is_dir($file_path)
        ) {

            $zip->addEmptyDir(
                $zip_path
            );


            add_folder_to_zip(
                $file_path,
                $zip,
                $zip_path . "/"
            );

        } else {

            /*
             * IMPORTANT:
             * Do not add the ZIP file itself
             * into the ZIP.
             */
            $zip->addFile(
                $file_path,
                $zip_path
            );
        }
    }
}


// ============================================================
// CREATE ZIP
// ============================================================

$zip_path =
    $temp_dir .
    "/" .
    $zip_file_name;


$zip =
    new ZipArchive();


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
// ADD IMAGES
// ============================================================

add_folder_to_zip(
    $temp_dir,
    $zip
);


// ============================================================
// CLOSE ZIP
// ============================================================

$zip->close();


// ============================================================
// CLOSE DATABASE
// ============================================================

mysql_close();


// ============================================================
// CHECK ZIP
// ============================================================

if (!file_exists($zip_path)) {

    die(
        "ZIP file was not created."
    );
}


// ============================================================
// DOWNLOAD
// ============================================================

while (
    ob_get_level() > 0
) {

    ob_end_clean();
}


header(
    "Content-Type: application/zip"
);


header(
    'Content-Disposition: attachment; filename="' .
    $zip_file_name .
    '"'
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


readfile(
    $zip_path
);


// ============================================================
// DELETE TEMP DIRECTORY
// ============================================================

function delete_directory(
    $directory
) {

    if (!is_dir($directory)) {

        return;
    }


    $items =
        scandir($directory);


    foreach ($items as $item) {

        if (
            $item == "." ||
            $item == ".."
        ) {

            continue;
        }


        $path =
            $directory .
            "/" .
            $item;


        if (
            is_dir($path)
        ) {

            delete_directory(
                $path
            );

        } else {

            @unlink($path);
        }
    }


    @rmdir($directory);
}


delete_directory(
    $temp_dir
);


exit;

?>
<?php

include "web_check.php";
include "star_connection.php";

$lift_table = "dealer_registration_web_form";


// ============================================================
// SEARCH FILTERS
// ============================================================

$srch_date_from = isset($_GET["srch_date_from"])
    ? addslashes(trim($_GET["srch_date_from"]))
    : "";

$srch_date_to = isset($_GET["srch_date_to"])
    ? addslashes(trim($_GET["srch_date_to"]))
    : "";

$srch_name = isset($_GET["srch_name"])
    ? addslashes(trim($_GET["srch_name"]))
    : "";

$srch_phone = isset($_GET["srch_phone"])
    ? addslashes(trim($_GET["srch_phone"]))
    : "";

$whr_str = "";


// ============================================================
// DATE FROM FILTER
// ============================================================

if ($srch_date_from != "") {

    $aand = (trim($whr_str) != "") ? " AND " : "";

    $whr_str .=
        "$aand $lift_table.created_at >= '$srch_date_from 00:00:00' ";
}


// ============================================================
// DATE TO FILTER
// ============================================================

if ($srch_date_to != "") {

    $aand = (trim($whr_str) != "") ? " AND " : "";

    $next_date = date(
        'Y-m-d',
        strtotime($srch_date_to . ' +1 day')
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
// BUILD WHERE CLAUSE
// ============================================================

$new_whr_str = ($whr_str != "")
    ? "WHERE " . $whr_str
    : "";


// ============================================================
// FETCH DATA
// ============================================================

$sql1 = "
    SELECT $lift_table.*
    FROM $lift_table
    $new_whr_str
    ORDER BY $lift_table.created_at DESC
";

$res1 = mysql_query($sql1);

if (!$res1) {

    die(
        "Database Query Error: " .
        mysql_error() .
        "<br><br>" .
        $sql1
    );
}


// ============================================================
// CHECK ZIP
// ============================================================

if (!class_exists("ZipArchive")) {

    die(
        "ZipArchive extension is not enabled on this server."
    );
}


// ============================================================
// TIMESTAMP
// ============================================================

$timestamp = date("Ymd_His");


// ============================================================
// FILE NAMES
// ============================================================

$zip_file_name =
    "dealer_register_" .
    $timestamp .
    ".zip";

$csv_file_name =
    "dealer_register_" .
    $timestamp .
    ".csv";


// ============================================================
// TEMP DIRECTORY
// ============================================================

$temp_folder =
    sys_get_temp_dir() .
    "/dealer_export_" .
    $timestamp .
    "_" .
    mt_rand(1000, 9999);


if (!mkdir($temp_folder, 0777, true)) {

    die(
        "Unable to create temporary folder: " .
        $temp_folder
    );
}


// ============================================================
// IMAGE TEMP DIRECTORY
// ============================================================

$images_folder =
    $temp_folder .
    "/Images";


if (!mkdir($images_folder, 0777, true)) {

    die(
        "Unable to create Images folder."
    );
}


// ============================================================
// CSV PATH
// ============================================================

$csv_file_path =
    $temp_folder .
    "/" .
    $csv_file_name;


// ============================================================
// CREATE CSV
// ============================================================

$out = fopen(
    $csv_file_path,
    "w"
);


if (!$out) {

    die(
        "Unable to create CSV file."
    );
}


// ============================================================
// CSV HEADER
// ============================================================

fputcsv($out, array(

    "ID",
    "Name",
    "Relation",
    "Firm Name",
    "Area",
    "District",
    "Contact No",
    "Kurta Size",
    "Selfie",
    "ID Proof",
    "ID Proof Front Image",
    "ID Proof Back Image",
    "Submitted On"

));


// ============================================================
// IMAGE BASE URL
// ============================================================

$base_image_url =
    "https://" .
    $_SERVER["HTTP_HOST"] .
    "/SAP/dealer_registration/uploads/dealer_register/";


// ============================================================
// DOWNLOAD IMAGE FUNCTION
// ============================================================

function downloadImage(
    $url,
    $destination
) {

    /*
     * First try cURL.
     */

    if (function_exists("curl_init")) {

        $ch = curl_init($url);

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
            30
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

            file_put_contents(
                $destination,
                $data
            );

            return true;
        }
    }


    /*
     * Fallback to file_get_contents.
     */

    $context = stream_context_create(array(

        "http" => array(

            "timeout" => 30,

            "follow_location" => true

        ),

        "ssl" => array(

            "verify_peer" => false,

            "verify_peer_name" => false

        )

    ));


    $data = @file_get_contents(
        $url,
        false,
        $context
    );


    if ($data !== false) {

        file_put_contents(
            $destination,
            $data
        );

        return true;
    }


    return false;
}


// ============================================================
// PROCESS RECORDS
// ============================================================

$total_records = 0;

$total_images = 0;

$failed_images = 0;


if (mysql_num_rows($res1) > 0) {

    while (
        $row1 =
        mysql_fetch_assoc($res1)
    ) {

        $total_records++;


        // ====================================================
        // BASIC DATA
        // ====================================================

        $id =
            $row1["id"];

        $name =
            $row1["name"];

        $relation =
            $row1["relation"];

        $firm_name =
            $row1["firm_name"];

        $area =
            $row1["area"];

        $district =
            $row1["district"];

        $contact_no =
            $row1["contact_no"];

        $kurta_size =
            $row1["size"];

        $id_proof =
            $row1["id_proof"];


        // ====================================================
        // DATE
        // ====================================================

        $created_at = "";

        if (
            $row1["created_at"] != ""
        ) {

            $created_at =
                date(
                    "d/m/Y H:i:s",
                    strtotime(
                        $row1["created_at"]
                    )
                );
        }


        // ====================================================
        // FILENAMES
        // ====================================================

        $selfie_file =
            trim(
                $row1["selfie_photo"]
            );

        $id_front_file =
            trim(
                $row1["id_photo_front"]
            );

        $id_back_file =
            trim(
                $row1["id_photo_back"]
            );


        // ====================================================
        // URLS
        // ====================================================

        $selfie_url = "";

        if ($selfie_file != "") {

            $selfie_url =
                $base_image_url .
                rawurlencode(
                    $selfie_file
                );
        }


        $id_front_url = "";

        if ($id_front_file != "") {

            $id_front_url =
                $base_image_url .
                rawurlencode(
                    $id_front_file
                );
        }


        $id_back_url = "";

        if ($id_back_file != "") {

            $id_back_url =
                $base_image_url .
                rawurlencode(
                    $id_back_file
                );
        }


        // ====================================================
        // RECORD FOLDER
        // ====================================================

        $record_folder =
            $images_folder .
            "/" .
            $id;


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

        if ($selfie_file != "") {

            $filename =
                basename(
                    $selfie_file
                );

            $destination =
                $record_folder .
                "/selfie_" .
                $filename;


            if (
                downloadImage(
                    $selfie_url,
                    $destination
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

        if ($id_front_file != "") {

            $filename =
                basename(
                    $id_front_file
                );

            $destination =
                $record_folder .
                "/id_front_" .
                $filename;


            if (
                downloadImage(
                    $id_front_url,
                    $destination
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

        if ($id_back_file != "") {

            $filename =
                basename(
                    $id_back_file
                );

            $destination =
                $record_folder .
                "/id_back_" .
                $filename;


            if (
                downloadImage(
                    $id_back_url,
                    $destination
                )
            ) {

                $total_images++;

            } else {

                $failed_images++;
            }
        }


        // ====================================================
        // CSV ROW
        // ====================================================

        fputcsv($out, array(

            $id,

            $name,

            $relation,

            $firm_name,

            $area,

            $district,

            $contact_no,

            $kurta_size,

            $selfie_url,

            $id_proof,

            $id_front_url,

            $id_back_url,

            $created_at

        ));
    }
}


// ============================================================
// CLOSE CSV
// ============================================================

fclose($out);


// ============================================================
// CREATE ZIP
// ============================================================

$zip_path =
    $temp_folder .
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
        "Could not create ZIP. Error code: " .
        $result
    );
}


// ============================================================
// ADD CSV
// ============================================================

$zip->addFile(
    $csv_file_path,
    $csv_file_name
);


// ============================================================
// ADD DIRECTORY TO ZIP
// ============================================================

function addDirectoryToZip(
    $folder,
    &$zip,
    $zip_folder
) {

    if (!is_dir($folder)) {
        return;
    }


    $files =
        scandir($folder);


    foreach ($files as $file) {

        if (
            $file == "." ||
            $file == ".."
        ) {

            continue;
        }


        $full_path =
            $folder .
            "/" .
            $file;


        $zip_path =
            $zip_folder .
            "/" .
            $file;


        if (
            is_dir(
                $full_path
            )
        ) {

            $zip->addEmptyDir(
                $zip_path
            );


            addDirectoryToZip(
                $full_path,
                $zip,
                $zip_path
            );

        } else {

            $zip->addFile(
                $full_path,
                $zip_path
            );
        }
    }
}


// ============================================================
// ADD IMAGES
// ============================================================

addDirectoryToZip(
    $images_folder,
    $zip,
    "Images"
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
// DOWNLOAD ZIP
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
    "Content-Disposition: attachment; filename=\"" .
    $zip_file_name .
    "\""
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
// CLEAN TEMP DIRECTORY
// ============================================================

function deleteDirectoryRecursive(
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

            deleteDirectoryRecursive(
                $path
            );

        } else {

            @unlink($path);
        }
    }


    @rmdir($directory);
}


// Delete temporary files
deleteDirectoryRecursive(
    $temp_folder
);


exit;

?>
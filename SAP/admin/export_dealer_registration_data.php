<?php

include "web_check.php";
include "star_connection.php";

$lift_table = "dealer_registration_web_form";

/* ============================================================
   SEARCH FILTERS
============================================================ */

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


/* ============================================================
   WHERE CLAUSE
============================================================ */

$whr_str = "";


/* DATE FROM */

if ($srch_date_from != "") {

    $aand = (trim($whr_str) != "") ? " AND " : "";

    $whr_str .=
        "$aand $lift_table.created_at >= '$srch_date_from 00:00:00' ";
}


/* DATE TO */

if ($srch_date_to != "") {

    $aand = (trim($whr_str) != "") ? " AND " : "";

    $next_date = date(
        'Y-m-d',
        strtotime($srch_date_to . ' +1 day')
    );

    $whr_str .=
        "$aand $lift_table.created_at < '$next_date 00:00:00' ";
}


/* NAME */

if ($srch_name != "") {

    $aand = (trim($whr_str) != "") ? " AND " : "";

    $whr_str .=
        "$aand $lift_table.name LIKE '%$srch_name%' ";
}


/* PHONE */

if ($srch_phone != "") {

    $aand = (trim($whr_str) != "") ? " AND " : "";

    $whr_str .=
        "$aand $lift_table.contact_no LIKE '%$srch_phone%' ";
}


/* BUILD WHERE */

$new_whr_str = ($whr_str != "")
    ? "WHERE " . $whr_str
    : "";


/* ============================================================
   FETCH DATA
============================================================ */

$sql1 = "

    SELECT
        $lift_table.id,
        $lift_table.name,
        $lift_table.relation,
        $lift_table.firm_name,
        $lift_table.area,
        $lift_table.district,
        $lift_table.contact_no,
        $lift_table.size,
        $lift_table.id_proof,
        $lift_table.selfie_photo,
        $lift_table.id_photo_front,
        $lift_table.id_photo_back,
        $lift_table.created_at

    FROM $lift_table

    $new_whr_str

    ORDER BY $lift_table.created_at DESC
";


$res1 = mysql_query($sql1);


if (!$res1) {

    die(
        "Database Query Error: " .
        mysql_error()
    );
}


/* ============================================================
   FILE NAME
============================================================ */

$file_name =
    "dealer_registration_data_" .
    date("Ymd_His") .
    ".csv";


/* ============================================================
   IMAGE BASE URL
============================================================ */

$base_image_url =
    "https://" .
    $_SERVER["HTTP_HOST"] .
    "/SAP/dealer_registration/uploads/dealer_register/";


/* Full URL for an uploaded image, or "" when there is none. */

function image_link($base_image_url, $file_name)
{
    $file_name = trim($file_name);

    if ($file_name == "") {

        return "";
    }

    return $base_image_url . rawurlencode(basename($file_name));
}


/* ============================================================
   CSV HEADERS
============================================================ */

header("Content-Type: text/csv; charset=utf-8");

header(
    "Content-Disposition: attachment; filename=\"$file_name\""
);

header("Cache-Control: no-cache, no-store, must-revalidate");

header("Pragma: no-cache");

header("Expires: 0");


/* ============================================================
   OPEN OUTPUT
============================================================ */

$out = fopen("php://output", "w");


/* ============================================================
   CSV COLUMN HEADERS
============================================================ */

fputcsv($out, array(

    "ID",
    "Name",
    "Relation",
    "Firm Name",
    "Area",
    "District",
    "Contact No",
    "Kurta Size",
    "ID Proof",
    "Selfie Image Link",
    "ID Proof Front Image Link",
    "ID Proof Back Image Link",
    "Submitted On"

));


/* ============================================================
   EXPORT DATA
============================================================ */

if (mysql_num_rows($res1) > 0) {

    while ($row1 = mysql_fetch_assoc($res1)) {

        $created_at = "";

        if ($row1["created_at"] != "") {

            $created_at = date(
                "d/m/Y H:i:s",
                strtotime($row1["created_at"])
            );
        }


        fputcsv($out, array(

            $row1["id"],

            $row1["name"],

            $row1["relation"],

            $row1["firm_name"],

            $row1["area"],

            $row1["district"],

            $row1["contact_no"],

            $row1["size"],

            $row1["id_proof"],

            image_link($base_image_url, $row1["selfie_photo"]),

            image_link($base_image_url, $row1["id_photo_front"]),

            image_link($base_image_url, $row1["id_photo_back"]),

            $created_at

        ));

    }

}


fclose($out);

mysql_close();

exit;

?>
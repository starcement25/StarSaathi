<?php

include "web_check.php";
include "star_connection.php";

$lift_table = "lifting_register_qr_code";

$new_qry_string_filtered = "";
$export_filtered_str = "";


/* ============================================================
   SEARCH PARAMETERS
============================================================ */

$srch_name = isset($_GET["srch_name"])
    ? addslashes(trim($_GET["srch_name"]))
    : "";

$srch_phone = isset($_GET["srch_phone"])
    ? addslashes(trim($_GET["srch_phone"]))
    : "";

$srch_date_from = isset($_GET["srch_date_from"])
    ? addslashes(trim($_GET["srch_date_from"]))
    : "";

$srch_date_to = isset($_GET["srch_date_to"])
    ? addslashes(trim($_GET["srch_date_to"]))
    : "";


/* ============================================================
   WHERE CLAUSE
============================================================ */

// $whr_str = "";
$whr_str = "
    $lift_table.state IS NOT NULL AND TRIM($lift_table.state) != ''
    AND $lift_table.district IS NOT NULL AND TRIM($lift_table.district) != ''
    AND $lift_table.address IS NOT NULL AND TRIM($lift_table.address) != ''
    AND $lift_table.cons_stage IS NOT NULL AND TRIM($lift_table.cons_stage) != ''
    AND $lift_table.name IS NOT NULL AND TRIM($lift_table.name) != ''
    AND $lift_table.phone IS NOT NULL AND TRIM($lift_table.phone) != ''
    AND $lift_table.question IS NOT NULL AND TRIM($lift_table.question) != ''
    AND $lift_table.image IS NOT NULL AND TRIM($lift_table.image) != ''
";


/* ============================================================
   NAME SEARCH
============================================================ */

if ($srch_name != "") {

    $aand = (trim($whr_str) != "") ? " AND " : "";

    $whr_str .=
        "$aand $lift_table.name LIKE '%$srch_name%' ";

    $new_qry_string_filtered .=
        "&srch_name=" . urlencode($srch_name);

    $export_filtered_str .=
        "&srch_name=" . urlencode($srch_name);
}


/* ============================================================
   PHONE SEARCH
============================================================ */

if ($srch_phone != "") {

    $aand = (trim($whr_str) != "") ? " AND " : "";

    $whr_str .=
        "$aand $lift_table.phone LIKE '%$srch_phone%' ";

    $new_qry_string_filtered .=
        "&srch_phone=" . urlencode($srch_phone);

    $export_filtered_str .=
        "&srch_phone=" . urlencode($srch_phone);
}


/* ============================================================
   DATE FROM SEARCH
============================================================ */

if ($srch_date_from != "") {

    $aand = (trim($whr_str) != "") ? " AND " : "";

    $whr_str .=
        "$aand $lift_table.created_at >= '$srch_date_from 00:00:00' ";

    $new_qry_string_filtered .=
        "&srch_date_from=" . urlencode($srch_date_from);

    $export_filtered_str .=
        "&srch_date_from=" . urlencode($srch_date_from);
}


/* ============================================================
   DATE TO SEARCH
============================================================ */

if ($srch_date_to != "") {

    $aand = (trim($whr_str) != "") ? " AND " : "";

    $next_date = date(
        "Y-m-d",
        strtotime($srch_date_to . " +1 day")
    );

    $whr_str .=
        "$aand $lift_table.created_at < '$next_date 00:00:00' ";

    $new_qry_string_filtered .=
        "&srch_date_to=" . urlencode($srch_date_to);

    $export_filtered_str .=
        "&srch_date_to=" . urlencode($srch_date_to);
}


/* ============================================================
   BUILD WHERE
============================================================ */

$new_whr_str = ($whr_str != "")
    ? "WHERE " . $whr_str
    : "";


/* ============================================================
   FETCH DATA

   IMAGE COLUMN IS NOT NEEDED FOR THIS EXPORT.
============================================================ */

$sql1 = "

    SELECT

        $lift_table.id,

        $lift_table.name,

        $lift_table.phone,

        $lift_table.state,

        $lift_table.district,

        $lift_table.address,

        $lift_table.cons_stage,

        $lift_table.order_date,

        $lift_table.order_qty,

        $lift_table.question,

        $lift_table.created_at

    FROM $lift_table

    $new_whr_str

    ORDER BY $lift_table.id DESC

";


$res1 = mysql_query($sql1);


if (!$res1) {

    die(
        "Database Query Error: " .
        mysql_error()
    );
}


/* ============================================================
   CSV FILE NAME
============================================================ */

$file_name =
    "lifting_register_data_" .
    date("Ymd_His") .
    ".csv";


/* ============================================================
   CSV HEADERS
============================================================ */

header(
    "Content-Type: text/csv; charset=utf-8"
);

header(
    "Content-Disposition: attachment; filename=\"$file_name\""
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


/* ============================================================
   OPEN CSV OUTPUT
============================================================ */

$out = fopen(
    "php://output",
    "w"
);


/* ============================================================
   UTF-8 BOM

   Helps Excel display text correctly.
============================================================ */

fprintf(
    $out,
    chr(0xEF) . chr(0xBB) . chr(0xBF)
);


/* ============================================================
   CSV HEADER

   IMAGE LINK REMOVED.
============================================================ */

fputcsv($out, array(

    "ID",

    "Name",

    "Phone",

    "State",

    "District",

    "Address",

    "Construction Stage",

  

    "What's your Ghar Ka Vaada?",

    "Submitted On"

));


/* ============================================================
   EXPORT RECORDS
============================================================ */

if (mysql_num_rows($res1) > 0) {

    while ($row1 = mysql_fetch_assoc($res1)) {


        /* ====================================================
           NULL VALUES
           Convert NULL to blank.
        ==================================================== */

        $id = isset($row1["id"]) && $row1["id"] !== null
            ? $row1["id"]
            : "";

        $name = isset($row1["name"]) && $row1["name"] !== null
            ? $row1["name"]
            : "";

        $phone = isset($row1["phone"]) && $row1["phone"] !== null
            ? $row1["phone"]
            : "";

        $state = isset($row1["state"]) && $row1["state"] !== null
            ? $row1["state"]
            : "";

        $district = isset($row1["district"]) && $row1["district"] !== null
            ? $row1["district"]
            : "";

        $address = isset($row1["address"]) && $row1["address"] !== null
            ? $row1["address"]
            : "";

        $cons_stage = isset($row1["cons_stage"]) && $row1["cons_stage"] !== null
            ? $row1["cons_stage"]
            : "";

       

        $question = isset($row1["question"]) && $row1["question"] !== null
            ? $row1["question"]
            : "";


        /* ====================================================
           ORDER DATE

           Invalid dates such as:

           0000-00-00
           0000-00-00 00:00:00

           will become blank instead of:

           30/11/-0001
        ==================================================== */

        $order_date = "";

        $raw_order_date = isset($row1["order_date"])
            ? trim($row1["order_date"])
            : "";

        if (
            $raw_order_date != "" &&
            $raw_order_date != "0000-00-00" &&
            $raw_order_date != "0000-00-00 00:00:00" &&
            strpos($raw_order_date, "0000-00-00") !== 0
        ) {

            $order_timestamp = strtotime(
                $raw_order_date
            );

            if (
                $order_timestamp !== false &&
                $order_timestamp > 0
            ) {

                $order_date = date(
                    "d/m/Y",
                    $order_timestamp
                );
            }
        }


        /* ====================================================
           SUBMITTED DATE

           Invalid dates become blank.
        ==================================================== */

        $created_at = "";

        $raw_created_at = isset($row1["created_at"])
            ? trim($row1["created_at"])
            : "";

        if (
            $raw_created_at != "" &&
            $raw_created_at != "0000-00-00" &&
            $raw_created_at != "0000-00-00 00:00:00" &&
            strpos($raw_created_at, "0000-00-00") !== 0
        ) {

            $created_timestamp = strtotime(
                $raw_created_at
            );

            if (
                $created_timestamp !== false &&
                $created_timestamp > 0
            ) {

                $created_at = date(
                    "d/m/Y H:i:s",
                    $created_timestamp
                );
            }
        }


        /* ====================================================
           CSV ROW

           NO IMAGE COLUMN.
        ==================================================== */

        fputcsv($out, array(

            $id,

            $name,

            $phone,

            $state,

            $district,

            $address,

            $cons_stage,

           

            $question,

            $created_at

        ));
    }
}


/* ============================================================
   CLOSE CSV
============================================================ */

fclose($out);


/* ============================================================
   CLOSE DATABASE
============================================================ */

mysql_close();


/* ============================================================
   EXIT
============================================================ */

exit;

?>
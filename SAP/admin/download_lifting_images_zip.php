<?php
include "web_check.php";
include "star_connection.php";

$lift_table = "lifting_register_qr_code";
$img_folder = "/SAP/customer/uploads/lift_register/"; // absolute server path below, this is the URL-equivalent

// Resolve the actual filesystem path to that folder regardless of where this script sits
$doc_root = rtrim($_SERVER['DOCUMENT_ROOT'], "/");
$img_dir_fs = $doc_root . $img_folder; // physical folder on disk

$whr_str = "";

$srch_name = $_GET["srch_name"] ? addslashes(trim($_GET["srch_name"])) : "";
$srch_phone = $_GET["srch_phone"] ? addslashes(trim($_GET["srch_phone"])) : "";
$srch_date_from = $_GET["srch_date_from"] ? addslashes(trim($_GET["srch_date_from"])) : "";
$srch_date_to = $_GET["srch_date_to"] ? addslashes(trim($_GET["srch_date_to"])) : "";

if ($srch_name != "") {
    $aand = (trim($whr_str) != "") ? " and" : "";
    $whr_str .= "$aand ($lift_table.name like '%$srch_name%') ";
}
if ($srch_phone != "") {
    $aand = (trim($whr_str) != "") ? " and" : "";
    $whr_str .= "$aand ($lift_table.phone like '%$srch_phone%') ";
}
if ($srch_date_from != "") {
    $aand = (trim($whr_str) != "") ? " and" : "";
    $whr_str .= "$aand ($lift_table.order_date >= '$srch_date_from') ";
}
if ($srch_date_to != "") {
    $aand = (trim($whr_str) != "") ? " and" : "";
    $whr_str .= "$aand ($lift_table.order_date <= '$srch_date_to') ";
}

$new_whr_str = ($whr_str != "") ? "where " . $whr_str : "";

$sql1 = "select id, name, image FROM $lift_table $new_whr_str and image != '' ORDER BY id DESC";
// fix "and" leading into empty where clause
if ($new_whr_str == "") {
    $sql1 = "select id, name, image FROM $lift_table where image != '' ORDER BY id DESC";
}

$res1 = mysql_query($sql1);

if (!$res1 || mysql_num_rows($res1) == 0) {
    die("No images found to export.");
}

// Build the zip in a temp location
$zip_file_name = "lifting_register_images_" . date("Ymd_His") . ".zip";
$zip_tmp_path = sys_get_temp_dir() . "/" . $zip_file_name;

$zip = new ZipArchive();
if ($zip->open($zip_tmp_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die("Could not create zip file.");
}

$added_count = 0;
while ($row = mysql_fetch_assoc($res1)) {
    $img_file = $row["image"];
    $row_id = $row["id"];
    $row_name = preg_replace('/[^A-Za-z0-9_-]/', '_', $row["name"]); // sanitize for filename

    $full_path = $img_dir_fs . $img_file;

    if (file_exists($full_path)) {
        $ext = pathinfo($img_file, PATHINFO_EXTENSION);
        // name inside zip: id_name.ext, so files are identifiable once extracted
        $zip_entry_name = $row_id . "_" . $row_name . "." . $ext;
        $zip->addFile($full_path, $zip_entry_name);
        $added_count++;
    }
}

$zip->close();

if ($added_count == 0) {
    @unlink($zip_tmp_path);
    die("No matching image files were found on the server.");
}

// Stream the zip to the browser as a download
header('Content-Description: File Transfer');
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zip_file_name . '"');
header('Content-Length: ' . filesize($zip_tmp_path));
header('Cache-Control: must-revalidate');
header('Pragma: public');
flush();

readfile($zip_tmp_path);

// Clean up temp file after sending
@unlink($zip_tmp_path);
exit;
?>
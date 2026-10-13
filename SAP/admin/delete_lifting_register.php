<?php

include "web_check.php";
include "star_connection.php";

$lift_table = "lifting_register_qr_code";
$lift_log_table = "lifting_register_qr_code_log";
$upload_dir = __DIR__ . "/../customer/uploads/lift_register/";

$id = isset($_GET["id"])
    ? (int)$_GET["id"]
    : 0;

// keep the search filters / page the admin was on when returning to the list
$return_qs = isset($_GET["return_qs"]) ? trim($_GET["return_qs"]) : "";
$return_qs = ltrim($return_qs, "?");
$return_url = "lifting_register_list.php" . ($return_qs != "" ? "?" . $return_qs : "");
$return_url_js = json_encode($return_url);

if ($id <= 0) {
    echo "<script>
        alert('Invalid record ID.');
        window.location=$return_url_js;
    </script>";
    exit;
}


/* =========================================================
   FETCH IMAGE NAME (so the uploaded file can be removed too)
========================================================= */

$image = "";
$chk_res = mysql_query("SELECT image FROM $lift_table WHERE id = $id LIMIT 1");
if ($chk_res && mysql_num_rows($chk_res) > 0) {
    $chk_row = mysql_fetch_assoc($chk_res);
    $image = $chk_row["image"] ? trim($chk_row["image"]) : "";
} else {
    echo "<script>
        alert('Record not found.');
        window.location=$return_url_js;
    </script>";
    exit;
}


/* =========================================================
   INSERT INTO LOG TABLE BEFORE DELETION
   If the log can't be written, the record is not deleted.
========================================================= */

$delete_by_id = isset($_SESSION["start_report_admin"])
    ? addslashes($_SESSION["start_report_admin"])
    : "";

$delete_ip = isset($_SERVER["REMOTE_ADDR"])
    ? addslashes($_SERVER["REMOTE_ADDR"])
    : "";

$log_sql = "
    INSERT INTO $lift_log_table (
        id,
        name,
        phone,
        state,
        district,
        address,
        cons_stage,
        order_date,
        order_qty,
        question,
        image,
        created_at,
        deleted_at,
        delete_by_id,
        delete_ip
    )
    SELECT
        id,
        name,
        phone,
        state,
        district,
        address,
        cons_stage,
        order_date,
        order_qty,
        question,
        image,
        created_at,
        NOW() AS deleted_at,
        '$delete_by_id' AS delete_by_id,
        '$delete_ip' AS delete_ip
    FROM $lift_table
    WHERE id = $id
    LIMIT 1
";

$log_res = mysql_query($log_sql);

if (!$log_res) {
    echo "<script>
        alert('Unable to write delete log. Record was not deleted.');
        window.location=$return_url_js;
    </script>";
    exit;
}


/* =========================================================
   DELETE RECORD
========================================================= */

$sql = "
    DELETE FROM $lift_table
    WHERE id = $id
    LIMIT 1
";

$result = mysql_query($sql);


if ($result) {

    // image file is kept on the server (only the DB record is deleted) so the
    // image name stored in the log table still points to a real file.
    // basename() so a stored value can never point outside the upload folder
    // if ($image != "") {
    //     $img_path = $upload_dir . basename($image);
    //     if (is_file($img_path)) {
    //         @unlink($img_path);
    //     }
    // }

    echo "<script>
        alert('Record deleted successfully.');
        window.location=$return_url_js;
    </script>";

} else {

    echo "<script>
        alert('Unable to delete record.');
        window.location=$return_url_js;
    </script>";

}


mysql_close();

?>

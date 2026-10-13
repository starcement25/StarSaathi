<?php
session_start();
include "../star_connection.php";
mysql_query("SET time_zone = '+05:30'");
$res_msg = array();
$lift_table = "lifting_register_qr_code";

$id       = isset($_POST["id"]) ? (int)$_POST["id"] : 0;
$otp_in   = isset($_POST["otp"]) ? trim($_POST["otp"]) : "";

if ($id > 0 && $otp_in != "") {

    // must match the row that was authorized during OTP generation for this session
    if (!isset($_SESSION["lift_reg_id"]) || $_SESSION["lift_reg_id"] != $id) {
        $res_msg = array("process_sts" => "NO", "process_msg" => "Session expired. Please start again.");
        echo json_encode($res_msg);
        mysql_close();
        exit;
    }

    $sql = "select `otp`,`name`,`phone` from $lift_table where `id`='".$id."' limit 1";
    $res = mysql_query($sql);
    $tot = mysql_num_rows($res);

    if ($tot > 0) {
        $row = mysql_fetch_assoc($res);
        $sms_otp = trim($row["otp"]);

        if ($sms_otp == $otp_in) {
            // mark this row/session as verified so step 3 will accept the update
            $_SESSION["lift_reg_verified"] = $id;

            $res_msg = array(
                "process_sts" => "YES",
                "process_msg" => "OTP verified.",
                "id"          => $id,
                "name"        => $row["name"],
                "phone"       => $row["phone"]
            );
        } else {
            $res_msg = array("process_sts" => "NO", "process_msg" => "Wrong OTP");
        }
    } else {
        $res_msg = array("process_sts" => "NO", "process_msg" => "Invalid request.");
    }

} else {
    $res_msg = array("process_sts" => "NO", "process_msg" => "Please enter OTP.");
}

echo json_encode($res_msg);
mysql_close();
?>

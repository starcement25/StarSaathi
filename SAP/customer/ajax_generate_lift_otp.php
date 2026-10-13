<?php
session_start();
set_time_limit(0);
include "../dashboard/star_connection.php";
mysql_query("SET time_zone = '+05:30'");

$res_msg = array();
$lift_table = "lifting_register_qr_code";

$name    = isset($_POST["name"]) ? addslashes(trim($_POST["name"])) : "";
$mob_no  = isset($_POST["phone"]) ? addslashes(trim($_POST["phone"])) : "";
$sms_res = "";

if ($name != "" && $mob_no != "") {

    if (!preg_match('/^[0-9]{10}$/', $mob_no)) {
        $res_msg = array("process_sts" => "NO", "process_msg" => "Please enter a valid 10 digit mobile number.");
        echo json_encode($res_msg);
        mysql_close();
        exit;
    }

    $otp_for_login = rand(1,9).rand(0,9).rand(0,9).rand(1,9);

    // test numbers - same convention as existing login
    if ($mob_no == "9233974090" || $mob_no == "9638307128") {
        $otp_for_login = "1010";
    }

    // $otp_text = "OTP is ".$otp_for_login." for Star Saathi lifting registration STAR CEMENT";
     $otp_text = "OTP is ".$otp_for_login." for Star Saathi log in STAR CEMENT";

    // Every submission is a fresh row, so the same customer can register multiple times
    $sql_ins = "insert into $lift_table (`name`,`phone`,`otp`) values ('$name','$mob_no','$otp_for_login')";
    $res_ins = mysql_query($sql_ins);
// print_r($sql_ins);die;
    if ($res_ins) {
        // 
        $new_id = mysql_insert_id();

        if ($mob_no == "9233974090" || $mob_no == "9638307128") {
            // skip actual SMS for test numbers
        } else {
            //echo 456;die;
            $tmp_id = "1707160982733435860";
           
            $sms_res = send_sms_new1($tmp_id, $mob_no, $otp_text);
           
        }

        // authorize this specific row for the verify/save steps that follow
        $_SESSION["lift_reg_id"] = $new_id;
        $_SESSION["lift_reg_phone"] = $mob_no;

        $res_msg = array(
            "process_sts" => "YES",
            "process_msg" => "OTP has been sent to your mobile number.",
            "id"          => $new_id,
            "sms_res"     => $sms_res
        );
    } else {
        $res_msg = array("process_sts" => "NO", "process_msg" => "Something went wrong. Please try again.");
    }

} else {
    $res_msg = array("process_sts" => "NO", "process_msg" => "Please enter Name and Mobile Number.");
}

echo json_encode($res_msg);
mysql_close();
?>

<?php
include "star_connection.php";

$arc_consumer_reg = "arc_consumer_reg";
$login_user_id = isset($_POST["login_user_id"]) ? addslashes(trim($_POST["login_user_id"])) : "";
$user_type = isset($_POST["user_type"]) ? addslashes(trim($_POST["user_type"])) : "";
$mobile = isset($_POST["mobile"]) ? addslashes(trim($_POST["mobile"])) : "";
$name = isset($_POST["name"]) ? addslashes(trim($_POST["name"])) : "";
$bag = isset($_POST["bag"]) ? addslashes(trim($_POST["bag"])) : "";
// $S = isset($_POST["coupon_s"]) ? addslashes(trim($_POST["coupon_s"])) : "";
// $T = isset($_POST["coupon_t"]) ? addslashes(trim($_POST["coupon_t"])) : "";
// $A = isset($_POST["coupon_a"]) ? addslashes(trim($_POST["coupon_a"])) : "";
// $R = isset($_POST["coupon_r"]) ? addslashes(trim($_POST["coupon_r"])) : "";
// $lunch_box = isset($_POST["gift_lunch_box"]) ? addslashes(trim($_POST["gift_lunch_box"])) : "";
// $water_bottle = isset($_POST["gift_water_bottle"]) ? addslashes(trim($_POST["gift_water_bottle"])) : "";

$S = ($_POST["coupon_s"] != 'Select') ? $_POST["coupon_s"] : '';
$T = ($_POST["coupon_t"] != 'Select') ? $_POST["coupon_t"] : '';
$A = ($_POST["coupon_a"] != 'Select') ? $_POST["coupon_a"] : '';
$R = ($_POST["coupon_r"] != 'Select') ? $_POST["coupon_r"] : '';
$lunch_box = ($_POST["gift_lunch_box"] != 'Select') ? $_POST["gift_lunch_box"] : '';
$water_bottle = ($_POST["gift_water_bottle"] != 'Select') ? $_POST["gift_water_bottle"] : '';

// Enable error reporting
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

if ($user_type != "" && $login_user_id != "") {
    if ($user_type == "dealer") {
        $customer_data_arr = get_customer_data_check_by_id($login_user_id);
        $the_sts = isset($customer_data_arr["sts"]) ? $customer_data_arr["sts"] : "";
        $is_branch_arc = isset($customer_data_arr["is_branch_arc"]) ? $customer_data_arr["is_branch_arc"] : "";

        if ($the_sts == "YES") {
            if ($is_branch_arc == "YES") {
                $dns_customer_code = isset($customer_data_arr["dns_customer_code"]) ? $customer_data_arr["dns_customer_code"] : "";

                if ($mobile == "") {
                    $res_data = array("process_sts" => "NO", "process_msg" => "Please enter mobile.");
                } else if (strlen($mobile) < 10) {
                    $res_data = array("process_sts" => "NO", "process_msg" => "Please enter a 10 digit mobile number.");
                } else if ($name == "") {
                    $res_data = array("process_sts" => "NO", "process_msg" => "Please enter name.");
                } else if ($bag == "") {
                    $res_data = array("process_sts" => "NO", "process_msg" => "Please enter the number of bags.");
                } else if ($bag < 0) {
                    $res_data = array("process_sts" => "NO", "process_msg" => "Number of bags should be greater than or equal to 0.");
                } else {
                    $curr_datetime = date("Y-m-d H:i:s");
                    $sql_in = "INSERT INTO $arc_consumer_reg (`name`, `mobile`, `no_of_bags`, `customer_code`, `dns_customer_code`, `entry_datetime`, `last_updated_datetime`, `S`, `T`, `A`, `R`, `lunch_box`, `water_bottle`) 
                                VALUES ('$name', '$mobile', '$bag', '$login_user_id', '$dns_customer_code', '$curr_datetime', '$curr_datetime', '$S', '$T', '$A', '$R', '$lunch_box', '$water_bottle')";

                    // Debugging: print SQL query
                    //echo $sql_in;

                    $res_in = mysql_query($sql_in);
                    if ($res_in) {
                        $res_data = array("process_sts" => "YES", "process_msg" => "SALE REGISTRATION COMPLETED");
                    } else {
                        $res_data = array("process_sts" => "NO", "process_msg" => "Something went wrong.");
                    }
                }
            } else {
                $res_data = array("process_sts" => "NO", "process_msg" => "You can't perform this action.");
            }
        } else {
            $res_data = array("process_sts" => "NO", "process_msg" => "Your details are missing.");
        }
    } else {
        $res_data = array("process_sts" => "NO", "process_msg" => "You can't perform this action.");
    }
} else {
    $res_data = array("process_sts" => "NO", "process_msg" => "Something went wrong.");
}
echo json_encode($res_data);
mysql_close();
?>
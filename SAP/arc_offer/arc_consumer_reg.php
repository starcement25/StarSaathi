<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

ini_set('memory_limit', '99999M');
set_time_limit(0);
include "star_connection.php";

$is_show = "NO";
$show_message = "";

$user_type = $_GET["user_type"] ? strtolower($_GET["user_type"]) : "";
$login_user_id = $_GET["login_user_id"] ? urldecode($_GET["login_user_id"]) : "";

if ($user_type != "" && $login_user_id != "") {
    if ($user_type == "dealer") {
        $customer_data_arr = get_customer_data_check_by_id($login_user_id);
        $the_sts = $customer_data_arr["sts"];
        $is_branch_arc = $customer_data_arr["is_branch_arc"];
        if ($the_sts == "YES") {
            if ($is_branch_arc == "YES") {
                $is_show = "YES";
            } else {
                $show_message = "Feature not available.";
            }
        } else {
            $show_message = "Your details are missing.";
        }
    } else {
        $show_message = "Feature not available.";
    }
} else {
    $show_message = "Somethingg went wrong.";
}

?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <title>SALE REGISTRATION</title>

    <style>
        .mand_field {
            color: #F00;
            font-weight: bold;
            font-size: 12px;
            padding-left: 5px;
        }

        .alrt_msg {
            color: #F00;
            font-size: 12px;
            height: 22px;
            display: inline-block;
        }

        .resp_msg {
            font-weight: bold;
            height: 22px;
            display: block;
            margin: 3px 0px;
            text-align: center;
        }

        form.arc_consumer_reg_form div.form-group {
            margin-bottom: 0px;
        }

        .horizontal-group {
            display: flex;
            gap: 10px;
            /* Adjust the gap between the dropdowns if needed */
        }

        .form-control {
            width: auto;
            /* Adjust width as needed */
        }

        .mand_field {
            color: red;
        }

        .alrt_msg {
            color: red;
        }
    </style>


</head>

<body>
    <div class="container-fluid">
        <?php
        if ($is_show == "NO" && $show_message != "") { ?>
            <h4 style="text-align:center;margin-top:30px;"><?php echo $show_message; ?></h4>
            <?php
            exit;
        }
        ?>

        <h5 style="text-align:center">SALE REGISTRATION</h5>

        <form action="" method="POST" class="arc_consumer_reg_form" id="arc_consumer_reg_form">
            <div class="form-group">
                <label for="mobile">CUSTOMER MOBILE NUMBER:<span class="mand_field">*</span></label>
                <input type="tel" class="form-control" placeholder="Enter Mobile" id="mobile" maxlength="10"
                    autocomplete="OFF">
                <span class="alrt_msg" id="err_mobile"></span>
            </div>
            <div class="form-group">
                <label for="name">CUSTOMER NAME:<span class="mand_field">*</span></label>
                <input type="text" class="form-control" placeholder="Enter Name" id="name" autocomplete="OFF">
                <span class="alrt_msg" id="err_name"></span>
            </div>
            <div class="form-group">
                <label for="bag">NO. OF BAGS:<span class="mand_field">*</span></label>
                <input type="number" class="form-control" placeholder="Enter Number of Bags" id="bag"
                    autocomplete="OFF">
                <span class="alrt_msg" id="err_bag"></span>
            </div>

            <div class="form-group">
                <label for="coupon-distributed">COUPON DISTRIBUTED:<span class="mand_field">*</span></label>
                <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <label for="lunch-box">S</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;
                    <label for="lunch-box">T</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;
                    <label for="lunch-box">A</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;
                    <label for="lunch-box">R</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;
                </div>
                <div id="coupon-distributed" class="horizontal-group">
                    <select class="form-control" id="coupon_s" autocomplete="OFF">
                        <option value="Select">Select</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>
                    <select class="form-control" id="coupon_t" autocomplete="OFF">
                        <option value="Select">Select</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>
                    <select class="form-control" id="coupon_a" autocomplete="OFF">
                        <option value="Select">Select</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>
                    <select class="form-control" id="coupon_r" autocomplete="OFF">
                        <option value="Select">Select</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>
                </div>
            </div>

            <br>

            <div class="form-group">
                <label for="gift-distributed">GIFT DISTRIBUTED:<span class="mand_field">*</span></label>
                <div>
                    <label for="lunch-box">Lunch Box</label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <label for="water-botttle">Water Bottle</label>
                </div>
                <div id="gift-distributed" class="horizontal-group">
                    <select class="form-control" id="gift_lunch_box" autocomplete="ON">
                        <option value="Select">Select</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>
                    <select class="form-control" id="gift_water_bottle" autocomplete="ON">
                        <option value="Select">Select</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>
                </div>
            </div>

            <span class="alrt_msg" id="err_coupon"></span>


            <div class="form-group">
                <span class="resp_msg"></span>
            </div>
            <input class="btn btn-large btn-block btn-success" type="submit" value="REGISTER" />
        </form>



        <div class="row" style="margin:30px 0px 0px 0px;">

            <a href="index.php?login_user_id=<?php echo urlencode($login_user_id); ?>&user_type=<?php echo $user_type; ?>"
                class="btn btn-large btn-block btn-success">BACK</a>

        </div>


    </div>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            var xhrarc_consumer_reg;
            var img_ldr = '<img src="img/ajax-loader.gif" style="width:20px;">';

            var login_user_id = "<?php echo $login_user_id; ?>";
            var user_type = "<?php echo $user_type; ?>";

            jQuery("#mobile").on("keypress keyup blur", function (event) {
                jQuery(this).val(jQuery(this).val().replace(/[^\d].+/, ""));
                if ((event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });

            jQuery("#bag").on("keypress keyup blur", function (event) {
                jQuery(this).val(jQuery(this).val().replace(/[^\d].+/, ""));
                if ((event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });


            jQuery("form#arc_consumer_reg_form").submit(function () {
                var mobile = jQuery.trim(jQuery('#mobile').val());
                var name = jQuery.trim(jQuery('#name').val());
                var bag = jQuery.trim(jQuery('#bag').val());
                var coupon_s   = jQuery.trim(jQuery('#coupon_s').val());
                var coupon_t   = jQuery.trim(jQuery('#coupon_t').val());
                var coupon_a   = jQuery.trim(jQuery('#coupon_a').val());
                var coupon_r   = jQuery.trim(jQuery('#coupon_r').val());
                var gift_lunch_box   = jQuery.trim(jQuery('#gift_lunch_box').val());
                var gift_water_bottle   = jQuery.trim(jQuery('#gift_water_bottle').val());

                var resp_msg_elmnt = jQuery('.resp_msg');
                if (mobile == "") {
                    jQuery('#mobile').focus();
                    jQuery('#err_mobile').html("Please enter mobile");
                    setTimeout(function () {
                        jQuery('#err_mobile').html("");
                    }, 6000);
                } else if (mobile.length < 10) {
                    jQuery('#mobile').focus();
                    jQuery('#err_mobile').html("Please enter 10 digit mobile number");
                    setTimeout(function () {
                        jQuery('#err_mobile').html("");
                    }, 6000);
                } else if (name == "") {
                    jQuery('#name').focus();
                    jQuery('#err_name').html("Please enter name");
                    setTimeout(function () {
                        jQuery('#err_name').html("");
                    }, 6000);
                } else if (bag == "") {
                    jQuery('#bag').focus();
                    jQuery('#err_bag').html("Please enter the number of bag");
                    setTimeout(function () {
                        jQuery('#err_bag').html("");
                    }, 6000);
                } else if (bag <= 99) {
                    jQuery('#bag').focus();
                    jQuery('#err_bag').html("Minimum bags should be 100");
                    setTimeout(function () {
                        jQuery('#err_bag').html("");
                    }, 6000);
                } else {

                    resp_msg_elmnt.html(img_ldr);
                    if (xhrarc_consumer_reg && xhrarc_consumer_reg.readystate != 4) {
                        xhrarc_consumer_reg.abort();
                    }

                    xhrarc_consumer_reg = jQuery.ajax({
                        url: 'ajax_arc_consumer_reg.php',
                        type: 'post',
                        dataType: 'json',
                        data: "login_user_id=" + encodeURIComponent(login_user_id) + "&user_type=" + user_type + "&mobile=" + mobile + "&name=" + encodeURIComponent(name) + "&bag=" + encodeURIComponent(bag) + "&coupon_s=" + encodeURIComponent(coupon_s) + "&coupon_t=" + encodeURIComponent(coupon_t) + "&coupon_a=" + encodeURIComponent(coupon_a) + "&coupon_r=" + encodeURIComponent(coupon_r) + "&gift_lunch_box=" + encodeURIComponent(gift_lunch_box) + "&gift_water_bottle=" + encodeURIComponent(gift_water_bottle),
                        success: function (response) {
                            if (response.process_sts == "YES") {
                                resp_msg_elmnt.html(response.process_msg);
                                setTimeout(function () {
                                    resp_msg_elmnt.html("");
                                }, 6000);
                                jQuery('#mobile').val("");
                                jQuery('#name').val("");
                                jQuery('#bag').val("");
                                jQuery('#coupon_s').val("Select");
                                jQuery('#coupon_t').val("Select");
                                jQuery('#coupon_a').val("Select");
                                jQuery('#coupon_r').val("Select");
                                jQuery('#gift_lunch_box').val("Select");
                                jQuery('#gift_water_bottle').val("Select");

                            } else {
                                resp_msg_elmnt.html(response.process_msg);
                                setTimeout(function () {
                                    resp_msg_elmnt.html("");
                                }, 6000);
                            }
                        },
                        timeout: 0
                    });


                    return false;
                }

                return false;
            });


        });
    </script>
</body>

</html>
<?php
mysql_close();
?>
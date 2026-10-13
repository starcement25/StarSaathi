<?php

session_start();

include "../star_connection.php";
mysql_query("SET time_zone = '+05:30'");
$success_image = "";

$sql_image = "
    SELECT the_value
    FROM app_setting_master
    WHERE the_key_name = 'lifting_registration'
    LIMIT 1
";

$res_image = mysql_query($sql_image);

if ($res_image && mysql_num_rows($res_image) > 0) {

    $row_image = mysql_fetch_assoc($res_image);

    $success_image = trim($row_image["the_value"]);
}

/*
 * Build the correct browser URL
 */
if ($success_image != "") {

    $success_image =
        "https://starsaathi.com/SAP/admin/" .
        ltrim($success_image, "/");
}


?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Star Cement - Lifting Registration</title>
    <link rel="icon" href="../dashboard/images/star_logo.jpg" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet"
        type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <link href="../dashboard/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="../dashboard/plugins/node-waves/waves.css" rel="stylesheet" />
    <link href="../dashboard/plugins/animate-css/animate.css" rel="stylesheet" />
    <link href="../dashboard/css/style.css" rel="stylesheet">

    <script src="../dashboard/plugins/jquery/jquery.min.js"></script>
    <script src="../dashboard/plugins/bootstrap/js/bootstrap.js"></script>
    <script src="../dashboard/plugins/node-waves/waves.js"></script>
    <script src="../dashboard/plugins/jquery-validation/jquery.validate.js"></script>
    <script src="../dashboard/js/admin.js"></script>

    <style>
        #otp_part,
        #back_btn_part,
        #details_part {
            display: none;
        }

        .center_otp {
            text-align: center;
        }

        .btn[disabled] {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* ============================
           RESPONSIVE / MOBILE FIXES
           ============================ */

        * {
            box-sizing: border-box;
        }

        html, body {
            max-width: 100%;
            overflow-x: hidden;
        }

        body.login-page {
            padding: 15px;
        }

        h3 {
            font-size: 20px;
            padding: 0 10px;
            word-wrap: break-word;
        }

        /* Login box: fluid instead of fixed 420px */
        .login-box {
            width: 420px;
            max-width: 100%;
            margin-left: auto;
            margin-right: auto;
        }

        .login-box .card {
            width: 100%;
        }

        .login-box .body {
            padding-left: 20px !important;
            padding-right: 20px !important;
        }

        .msg img {
            max-width: 160px;
            width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        /* Prevent inputs from overflowing on very small screens */
        .form-control {
            max-width: 100%;
        }

        .input-group {
            width: 100%;
        }

        /* Modal fixes for small screens */
        .modal-dialog {
            width: auto;
            max-width: 100%;
            margin: 10px;
        }

        .modal-content {
            width: 100%;
        }

        .modal-body {
            padding: 15px;
        }

        .modal-body img#successUploadedImage {
            max-width: 100%;
            height: auto;
        }

        /* Checkbox + label row: keep readable on narrow screens */
        #terms_consent {
            width: 20px;
            height: 20px;
            margin-top: 3px;
        }

        label[for="terms_consent"] {
            font-size: 13px;
            line-height: 20px;
        }

        .waves-effect.unique{
                margin-top: 60px;
            }

        /* ============================
           BREAKPOINT: <= 480px (phones)
           ============================ */
        @media (max-width: 480px) {

            body.login-page {
                padding: 10px;
            }

            h3 {
                font-size: 18px;
                margin-bottom: 15px;
            }

            .login-box {
                width: 100%;
            }

            .login-box .body {
                padding: 15px !important;
            }

            .form-control {
                font-size: 14px;
            }

            .btn-lg {
                font-size: 15px;
                padding: 10px 14px;
            }

            .modal-title {
                font-size: 16px;
            }

            label[for="question_modal_input"] {
                font-size: 13px;
            }
        }

        /* ============================
           BREAKPOINT: <= 320px (smallest phones)
           ============================ */
        @media (max-width: 320px) {

            body.login-page {
                padding: 6px;
            }

            h3 {
                font-size: 15px;
                line-height: 20px;
                padding: 0 4px;
                margin-bottom: 10px;
            }

            .waves-effect.unique{
                margin-top: 30px;
            }

            .login-box .body {
                padding: 10px !important;
            }

            .msg img {
                max-width: 120px;
            }

            .input-group-addon {
                padding: 6px 8px;
            }

            .input-group-addon i.material-icons {
                font-size: 18px;
            }

            .form-control {
                font-size: 13px;
                padding-left: 4px;
                padding-right: 4px;
            }

            .form-control[type="file"] {
                font-size: 11px;
            }

            .btn-lg {
                font-size: 13px;
                padding: 8px 10px;
            }

            #terms_consent {
                width: 18px;
                height: 18px;
                margin-right: 6px;
            }

            label[for="terms_consent"] {
                font-size: 11px;
                line-height: 16px;
            }

            .modal-header {
                padding: 10px;
            }

            .modal-title {
                font-size: 14px;
            }

            .modal-body {
                padding: 10px;
            }

            .modal-body p strong {
                font-size: 13px;
            }

            .modal-footer {
                padding: 8px;
            }

            textarea.form-control {
                font-size: 13px;
            }
        }
    </style>
</head>

<body class="login-page">
    <h3 style="text-align:center; color:#ffffff;">Star Cement - Griha Pravesh Consumer Scheme</h3>
    <div class="login-box">

        <div class="card">
            <div class="body" style="padding-top:20px !important;">
                <form id="lift_reg_form" method="POST" action="" enctype="multipart/form-data">
                    <div class="msg" style="background-color:#ed2726; margin-top:10px;"><img style="text-align:center;"
                            src="../dashboard/images/star_logo.jpg" width="160px"></div>

                    <input type="hidden" name="reg_id" id="reg_id" value="">

                    <!-- STEP 1: name + phone -->
                    <div id="name_phone_part">

                        <div class="input-group">
                            <span class="input-group-addon"><i class="material-icons">person</i></span>
                            <div class="form-line">
                                <input type="text" class="form-control" name="name" id="cust_name"
                                    placeholder="Enter your name" autocomplete="off" required autofocus>
                            </div>
                        </div>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="material-icons">dialpad</i></span>
                            <div class="form-line">
                                <input type="tel" class="form-control" name="phone" id="cust_phone" maxlength="10"
                                    placeholder="Enter Mobile No." autocomplete="off" required>
                            </div>
                        </div>

                        <div class="input-group">
                            <input type="button" class="btn btn-block bg-red btn-lg waves-effect" value="CONTINUE"
                                id="continue_btn" />
                        </div>
                    </div>

                    <!-- STEP 2: OTP -->
                    <div class="input-group" id="otp_part">
                        <div class="form-line">
                            <input type="tel" class="form-control center_otp" name="otp" id="reg_otp"
                                placeholder="Enter OTP" autocomplete="off">
                        </div>
                    </div>

                    <div class="input-group" id="verify_btn_part" style="display:none;">
                        <input type="button" class="btn btn-block bg-red btn-lg waves-effect" value="VERIFY OTP"
                            id="verify_btn" />
                    </div>

                    <!-- STEP 3: rest of the details -->
                    <div id="details_part">



                        <div class="input-group">
                            <span class="input-group-addon"><i class="material-icons">home</i></span>
                            <div class="form-line">
                                <input type="text" class="form-control" name="address" id="address"
                                    placeholder="Address" autocomplete="off">
                            </div>
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="material-icons">place</i></span>
                            <div class="form-line">
                                <input type="text" class="form-control" name="district" id="district"
                                    placeholder="District" autocomplete="off">
                            </div>
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="material-icons">place</i></span>
                            <div class="form-line">
                                <input type="text" class="form-control" name="state" id="state" placeholder="State"
                                    autocomplete="off">
                            </div>
                        </div>

                        <div class="input-group">
                        <span class="input-group-addon"><i class="material-icons">build</i></span>
                        <div class="form-line">
                            <select class="form-control" name="cons_stage" id="cons_stage">
                                <option value="">Select Construction Stage</option>
                                <option value="Planning / Construction Yet to Start">Planning / Construction Yet to Start</option>
                                <option value="Foundation &amp; Plinth Work">Foundation &amp; Plinth Work</option>
                                <option value="RCC Structure - Columns, Beams &amp; Slabs">RCC Structure &ndash; Columns, Beams &amp; Slabs</option>
                                <option value="Brickwork / AAC Block Wall Construction">Brickwork / AAC Block Wall Construction</option>
                                <option value="Roof or Slab Casting">Roof or Slab Casting</option>
                                <option value="Plastering">Plastering</option>
                                <option value="Waterproofing">Waterproofing</option>
                                <option value="Flooring &amp; Tiling">Flooring &amp; Tiling</option>
                                <option value="Wall Putty &amp; Final Finishing">Wall Putty &amp; Final Finishing</option>
                                <option value="Repair / Renovation / Maintenance">Repair / Renovation / Maintenance</option>
                            </select>
                        </div>
                    </div>


                        <!-- <div class="input-group">
                            <span class="input-group-addon"><i class="material-icons">event</i></span>
                            <div class="form-line">
                                <label for="order_date"
                                    style="display:block;font-size:12px;color:#999;margin-bottom:2px;">Order
                                    Date</label>
                                <input type="date" class="form-control" name="order_date" id="order_date"
                                    autocomplete="off">
                            </div>
                        </div>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="material-icons">shopping_cart</i></span>
                            <div class="form-line">
                                <input type="number" class="form-control" name="order_qty" id="order_qty"
                                    placeholder="Order Quantity" autocomplete="off">
                            </div>
                        </div> -->

                        <div class="input-group">
                            <span class="input-group-addon"><i class="material-icons">image</i></span>
                            <div class="form-line">
                                <input type="file" class="form-control" name="image" id="image" accept="image/*">
                            </div>
                        </div>

                        <div class="input-group" style="
        margin-top:15px;
        margin-bottom:25px;
     ">

                            <div style="display:flex; align-items:flex-start;">

                                <input type="checkbox" id="terms_consent" name="terms_consent" value="1" style="
                   margin-top:2px;
                   margin-right:10px;
                   cursor:pointer;
                   flex-shrink:0;
               ">

                                <label for="terms_consent" style="
                                cursor:pointer;
                                margin:0;
                                ">

                                    I agree to the

                                    <a href="https://www.starcement.co.in/terms-and-conditions-of-contest"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    onclick="event.stopPropagation();"
                                    style="color:#ed2726; text-decoration:underline;">Terms and Conditions</a>

                                    and also give consent to receive promotional SMS
                                    and updates from Star Cement.

                            </label>

                            </div>

                        </div>

                        <div class="input-group">
                            <input type="button" class="btn btn-block bg-red btn-lg waves-effect unique" value="CONTINUE"
                                id="continue_details_btn" />
                        </div>
                    </div>

                    <!-- <div class="input-group" id="back_btn_part">
                        <input type="button" class="btn btn-block bg-red btn-lg waves-effect" value="START NEW ENTRY"
                            id="back_btn" />
                    </div> -->

                    <div class="input-group"
                        style="display:block;width:100%;height:35px;padding-bottom:10px;text-align:center;">
                        <span class="msg_resp" id="msg_resp"></span>
                    </div>

                    <!-- GHAR KA VAADA QUESTION MODAL (final step before actual submit) -->
                    <div class="modal fade" id="questionModal" tabindex="-1" role="dialog"
                        aria-labelledby="questionModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="questionModalLabel">One Last Step</h4>
                                </div>

                                <div class="modal-body">

                                    <label for="question_modal_input" style="
                       display:block;
                       color:#333;
                       line-height:20px;
                       margin-bottom:10px;
                   ">
                                        What&rsquo;s your Ghar Ka Vaada? Answer this simple question today for your
                                        chance to win an exciting gift!
                                    </label>

                                    <div class="form-line">
                                        <textarea class="form-control" name="question" id="question_modal_input"
                                            rows="3" placeholder="Type your answer here" autocomplete="off"></textarea>
                                    </div>

                                    <div style="text-align:center;margin-top:8px;min-height:18px;">
                                        <span id="question_modal_msg" style="color:#ed2726;font-size:13px;"></span>
                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <input type="button" class="btn btn-block bg-red btn-lg waves-effect" value="SUBMIT"
                                        id="modal_submit_btn" />
                                </div>

                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function () {
            var xhr1, xhr2, xhr3;
            var img1 = '<img src="../dashboard/images/ajax-loader.gif">';
            var msg_resp_elmnt = jQuery("#msg_resp");
            var is_submitting_details = false; // guards against double-click / double-submit

            function show_msg(text, clear_after) {

                msg_resp_elmnt
                    .html(text)
                    .show();

                if (clear_after) {

                    setTimeout(function () {
                        msg_resp_elmnt.html("").hide();
                    }, clear_after);
                }
            }

            function show_modal_msg(text, clear_after) {

                var modal_msg_elmnt = jQuery("#question_modal_msg");

                modal_msg_elmnt.html(text);

                if (clear_after) {

                    setTimeout(function () {
                        modal_msg_elmnt.html("");
                    }, clear_after);
                }
            }

            function reset_form() {

                // Reset form fields
                jQuery("#lift_reg_form")[0].reset();

                // Reset registration ID
                jQuery("#reg_id").val("");

                // Show first section
                jQuery("#name_phone_part").show();

                // Hide other sections 
                jQuery("#otp_part").hide();
                jQuery("#verify_btn_part").hide();
                jQuery("#details_part").hide();
                jQuery("#back_btn_part").hide();

                // VERY IMPORTANT: remove loader/message
                jQuery("#msg_resp").html("").hide();

                // Reset the question modal
                jQuery("#question_modal_input").val("");
                jQuery("#question_modal_msg").html("");
                jQuery("#questionModal").modal("hide");

                // Reset submit button
                is_submitting_details = false;

                jQuery("#modal_submit_btn")
                    .prop("disabled", false)
                    .val("SUBMIT");

                // Reset OTP
                jQuery("#reg_otp").val("");

                // Optional: focus first field
                jQuery("#cust_name").focus();
            }
            jQuery("#back_btn").click(function () {
                reset_form();
            });

            // STEP 1 -> generate OTP
            jQuery("#continue_btn").click(function () {
                var name = jQuery.trim(jQuery("#cust_name").val());
                var phone = jQuery.trim(jQuery("#cust_phone").val());

                if (name == "") {
                    show_msg("Please enter your name.", 5000);
                    jQuery("#cust_name").focus();
                    return;
                }
                if (phone == "" || phone.length < 10 || !phone.match('^[0-9]{10}$')) {
                    show_msg("Please enter a valid 10 digit mobile number.", 5000);
                    jQuery("#cust_phone").focus();
                    return;
                }

                show_msg(img1);
                if (xhr1 && xhr1.readystate != 4) { xhr1.abort(); }
                xhr1 = jQuery.ajax({
                    url: 'ajax_generate_lift_otp.php',
                    type: 'post',
                    dataType: 'json',
                    data: "name=" + encodeURIComponent(name) + "&phone=" + encodeURIComponent(phone),
                    success: function (response) {
                        if (response.process_sts == "YES") {
                            jQuery("#reg_id").val(response.id);
                            jQuery("#name_phone_part").hide();
                            jQuery("#otp_part").show();
                            jQuery("#verify_btn_part").show();
                            jQuery("#back_btn_part").show();
                            show_msg("");
                        } else {
                            show_msg(response.process_msg, 8000);
                        }
                    },
                    timeout: 0
                });
            });

            // STEP 2 -> verify OTP
            jQuery("#verify_btn").click(function () {
                var otp = jQuery.trim(jQuery("#reg_otp").val());
                var reg_id = jQuery("#reg_id").val();

                if (otp == "") {
                    show_msg("Please enter OTP.", 5000);
                    jQuery("#reg_otp").focus();
                    return;
                }

                show_msg(img1);
                if (xhr2 && xhr2.readystate != 4) { xhr2.abort(); }
                xhr2 = jQuery.ajax({
                    url: 'ajax_verify_lift_otp.php',
                    type: 'post',
                    dataType: 'json',
                    data: "id=" + encodeURIComponent(reg_id) + "&otp=" + encodeURIComponent(otp),
                    success: function (response) {
                        if (response.process_sts == "YES") {
                            jQuery("#otp_part").hide();
                            jQuery("#verify_btn_part").hide();
                            jQuery("#details_part").show();
                            show_msg("");
                        } else {
                            show_msg(response.process_msg, 8000);
                        }
                    },
                    timeout: 0
                });
            });

            // STEP 3a -> validate details, then open the Ghar Ka Vaada question modal
            jQuery("#continue_details_btn").click(function () {

                var state = jQuery.trim(jQuery("#state").val());
                var district = jQuery.trim(jQuery("#district").val());
                var address = jQuery.trim(jQuery("#address").val());
                var cons_stage = jQuery.trim(jQuery("#cons_stage").val());

                if (
                    state == "" ||
                    district == "" ||
                    address == "" ||
                    cons_stage == ""
                ) {
                    show_msg("Please fill all required fields.", 5000);
                    return;
                }

                // Check Terms & Conditions / SMS consent
                if (!jQuery("#terms_consent").is(":checked")) {

                    show_msg(
                        "Please agree to the Terms and Conditions and give consent to receive promotional SMS and updates from Star Cement.",
                        8000
                    );

                    jQuery("#terms_consent").focus();

                    return;
                }

                // clear any stale message/answer, then show the question modal
                jQuery("#question_modal_msg").html("");
                jQuery("#questionModal").modal("show");
            });

            // STEP 3b -> answer the question inside the modal, then actually submit (multipart because of image)
            jQuery("#modal_submit_btn").click(function () {

                // hard stop if a submission is already in flight
                if (is_submitting_details) {
                    return;
                }

                var $btn = jQuery(this);
                var reg_id = jQuery("#reg_id").val();
                var question = jQuery.trim(jQuery("#question_modal_input").val());

                if (question == "") {
                    show_modal_msg("Please answer the question to continue.", 5000);
                    jQuery("#question_modal_input").focus();
                    return;
                }

                var form_data = new FormData(jQuery("#lift_reg_form")[0]);
                form_data.set("id", reg_id);

                // lock the button and show a loader immediately
                is_submitting_details = true;
                $btn.prop("disabled", true).val("SUBMITTING...");

                if (xhr3 && xhr3.readystate != 4) { xhr3.abort(); }
                xhr3 = jQuery.ajax({
                    url: 'ajax_save_lift_details.php',
                    type: 'post',
                    dataType: 'json',
                    data: form_data,
                    contentType: false,
                    processData: false,
                    success: function (response) {

                        console.log("SERVER RESPONSE:", response);

                        if (response.process_sts == "YES") {

                            console.log("IMAGE DEBUG:", response.image_debug);

                            /*
                             * FIXED SUCCESS IMAGE
                             * This image is already stored on the server.
                             */
                            var successImageUrl =
                                <?php echo json_encode($success_image); ?>;

                            console.log("SUCCESS IMAGE URL:", successImageUrl);

                            /*
                             * Set fixed image in modal
                             */
                            jQuery("#successUploadedImage")
                                .attr("src", successImageUrl);

                            /*
                             * Close the question modal
                             */
                            jQuery("#questionModal").modal("hide");

                            /*
                             * Remove AJAX loader immediately
                             */
                            jQuery("#msg_resp")
                                .html("")
                                .hide();

                            /*
                             * Reset the form in the background
                             *
                             * This does NOT close the modal.
                             */
                            reset_form();

                            /*
                             * Show success modal
                             */
                            jQuery("#imageSuccessModal").modal("show");

                        } else {

                            is_submitting_details = false;

                            $btn
                                .prop("disabled", false)
                                .val("SUBMIT");

                            show_modal_msg(
                                response.process_msg,
                                8000
                            );

                            console.log(
                                "IMAGE DEBUG:",
                                response.image_debug
                            );
                        }
                    },
                    error: function () {
                        is_submitting_details = false;
                        $btn.prop("disabled", false).val("SUBMIT");
                        show_modal_msg("Network error. Please try again.", 8000);
                    },
                    timeout: 0
                });
            });

        });
    </script>
    <!-- SUCCESS IMAGE MODAL -->
    <div class="modal fade" id="imageSuccessModal" tabindex="-1" role="dialog" aria-labelledby="imageSuccessModalLabel">

        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                        <span aria-hidden="true">&times;</span>

                    </button>

                    <h4 class="modal-title" id="imageSuccessModalLabel">

                        <!-- Submission Successful -->

                    </h4>

                </div>

                <div class="modal-body text-center">

                    <p>
                        <strong>
                            Your details have been saved successfully.
                        </strong>
                    </p>

                    <img id="successUploadedImage" src="" alt="Uploaded Image" style="
                        display:block;
                        max-width:100%;
                        max-height:400px;
                        width:auto;
                        height:auto;
                        margin:15px auto;
                        border:1px solid #ddd;
                        padding:5px;
                    ">

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal">

                        Close

                    </button>

                </div>

            </div>

        </div>

    </div>
</body>

</html>
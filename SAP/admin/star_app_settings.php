<?php

include "web_check.php";
include "star_connection.php";

$app_setting_master = "app_setting_master";

$server_url1 = "http://" . $_SERVER['SERVER_NAME'] . "/";

$app_setting_key_arr = array(
    "carry_forward_process" => "ACTIVE",
    "carry_forward_process_message" => "",
    "is_show_reg_btn" => "NO",
    "gst_verification" => "false",
    "rssd_allocation_change" => "false",
    "rssd_allocation_days" => "0",
    "lifting_registration" => ""
);

$carry_forward_process_arr = array("ACTIVE", "INACTIVE");
$reg_btn_option_arr = array("YES", "NO");
$gst_verification_arr = array("true", "false");
$rssd_allocation_change_arr = array("true", "false");


// ============================================================
// FETCH APP SETTINGS
// ============================================================

$sql_ckredius = "
    SELECT the_key_name, the_value
    FROM $app_setting_master
";

$res_ckredius = mysql_query($sql_ckredius);

$totres_ckredius = mysql_num_rows($res_ckredius);

if ($totres_ckredius > 0) {

    while ($row_ckredius = mysql_fetch_assoc($res_ckredius)) {

        $the_key_name = $row_ckredius["the_key_name"];

        $the_key_value =
            isset($row_ckredius["the_value"])
                ? trim($row_ckredius["the_value"])
                : "";

        if ($the_key_name != "") {

            $app_setting_key_arr[$the_key_name] =
                $the_key_value;
        }
    }
}


$submsg = "";

$add_page_name = "star_app_settings.php";
$page_name = "star_app_settings.php";

include "web_header.php";

?>

<style>

.estarix_cls {
    color: #F00;
    margin-left: 5px;
}

.span_clear {
    clear: both;
    display: block;
}

.att_radius {
    text-align: center;
    width: 100%;
    height: 30px;
}

.att_late_count_with_respect_to_one_absent {
    text-align: center;
    width: 100%;
    height: 30px;
}

.att_send_leave_request_to_this_mail {
    text-align: left;
    width: 100%;
    height: 30px;
    padding-left: 5px;
    padding-top: 5px;
}

.setting_table tbody tr th,
.setting_table tbody tr td {
    width: 33%;
}

.setting_table tbody tr td span.small_msg {
    font-size: 11px;
    font-weight: bold;
}

.setting_table tbody tr td p {
    margin-bottom: 4px;
}

.the_float_btn {
    float: left;
}

.the_float_msg {
    float: left;
    margin-left: 10px;
    width: 30px;
    height: 30px;
}

</style>


<section class="content">

    <div class="container-fluid">

        <div class="block-header">
        </div>


        <!-- Basic Examples -->

        <div class="row clearfix">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                <div class="card">

                    <div class="header">

                        <h2>App Setting</h2>

                    </div>


                    <div class="body" style="padding:20px;">

                        <table
                            class="table table-bordered table-striped table-hover setting_table"
                        >

                            <thead>

                                <tr>

                                    <th>Settings Name</th>

                                    <th>Value</th>

                                    <th>Action</th>

                                </tr>

                            </thead>


                            <tbody>


                                <!-- ============================================================
                                     CARRY FORWARD PROCESS
                                ============================================================= -->

                                <tr>

                                    <td>

                                        <p>Carry Forward Process</p>

                                        <span class="small_msg">
                                        </span>

                                    </td>


                                    <td>

                                        <?php

                                        $fetched_carry_forward_process =
                                            $app_setting_key_arr[
                                                "carry_forward_process"
                                            ];

                                        ?>

                                        <select
                                            class="form-control str_carry_forward_process"
                                            id="str_carry_forward_process"
                                        >

                                            <?php

                                            foreach (
                                                $carry_forward_process_arr
                                                as $carry_forward_process_arr_val
                                            ) {

                                            ?>

                                                <option
                                                    value="<?php echo $carry_forward_process_arr_val; ?>"
                                                    <?php

                                                    if (
                                                        $carry_forward_process_arr_val ==
                                                        $fetched_carry_forward_process
                                                    ) {

                                                        echo 'selected="selected"';

                                                    }

                                                    ?>
                                                >

                                                    <?php
                                                    echo $carry_forward_process_arr_val;
                                                    ?>

                                                </option>

                                            <?php

                                            }

                                            ?>

                                        </select>

                                    </td>


                                    <td>

                                        <a
                                            href="javascript:void(0);"
                                            class="btn bg-red waves-effect upd_str_carry_forward_btn the_float_btn"
                                        >
                                            Update
                                        </a>

                                        <span
                                            class="str_carry_forward_msg_ldr the_float_msg"
                                        ></span>

                                        <span class="span_clear"></span>

                                    </td>

                                </tr>


                                <!-- ============================================================
                                     CARRY FORWARD PROCESS MESSAGE
                                ============================================================= -->

                                <tr>

                                    <td>

                                        <p>Carry Forward Process Message</p>

                                        <span class="small_msg">

                                            End user will see this message if
                                            "Carry Forward" status is "INACTIVE"

                                        </span>

                                    </td>


                                    <td>

                                        <?php

                                        $fetched_carry_forward_process_message =
                                            $app_setting_key_arr[
                                                "carry_forward_process_message"
                                            ];

                                        ?>

                                        <textarea
                                            class="form-control str_carry_forward_process_message"
                                            id="str_carry_forward_process_message"
                                            rows="3"
                                        ><?php
                                            echo $fetched_carry_forward_process_message;
                                        ?></textarea>

                                    </td>


                                    <td>

                                        <a
                                            href="javascript:void(0);"
                                            class="btn bg-red waves-effect upd_str_carry_forward_message_btn the_float_btn"
                                        >
                                            Update
                                        </a>

                                        <span
                                            class="str_carry_forward_message_msg_ldr the_float_msg"
                                        ></span>

                                        <span class="span_clear"></span>

                                    </td>

                                </tr>


                                <!-- ============================================================
                                     IOS REGISTRATION BUTTON
                                ============================================================= -->

                                <tr>

                                    <td>

                                        <p>
                                            Enable registration button for IOS app
                                        </p>

                                        <span class="small_msg">
                                        </span>

                                    </td>


                                    <td>

                                        <?php

                                        $is_show_reg_btn =
                                            $app_setting_key_arr[
                                                "is_show_reg_btn"
                                            ];

                                        ?>

                                        <select
                                            class="form-control is_show_reg_btn"
                                            id="is_show_reg_btn"
                                        >

                                            <?php

                                            foreach (
                                                $reg_btn_option_arr
                                                as $reg_btn_option_arr_val
                                            ) {

                                            ?>

                                                <option
                                                    value="<?php echo $reg_btn_option_arr_val; ?>"
                                                    <?php

                                                    if (
                                                        $reg_btn_option_arr_val ==
                                                        $is_show_reg_btn
                                                    ) {

                                                        echo 'selected="selected"';

                                                    }

                                                    ?>
                                                >

                                                    <?php
                                                    echo $reg_btn_option_arr_val;
                                                    ?>

                                                </option>

                                            <?php

                                            }

                                            ?>

                                        </select>

                                    </td>


                                    <td>

                                        <a
                                            href="javascript:void(0);"
                                            class="btn bg-red waves-effect upd_is_show_reg_btn_btn the_float_btn"
                                        >
                                            Update
                                        </a>

                                        <span
                                            class="is_show_reg_btn_msg_ldr the_float_msg"
                                        ></span>

                                        <span class="span_clear"></span>

                                    </td>

                                </tr>


                                <!-- ============================================================
                                     RSSD ALLOCATION CHANGE + DAYS
                                     SINGLE UPDATE BUTTON
                                ============================================================= -->

                                <?php

                                $fetched_rssd_allocation_change =
                                    isset(
                                        $app_setting_key_arr[
                                            "rssd_allocation_change"
                                        ]
                                    )
                                        ? strtolower(
                                            trim(
                                                $app_setting_key_arr[
                                                    "rssd_allocation_change"
                                                ]
                                            )
                                        )
                                        : "false";


                                if (
                                    $fetched_rssd_allocation_change != "true" &&
                                    $fetched_rssd_allocation_change != "false"
                                ) {

                                    $fetched_rssd_allocation_change =
                                        "false";

                                }


                                $fetched_rssd_allocation_days =
                                    isset(
                                        $app_setting_key_arr[
                                            "rssd_allocation_days"
                                        ]
                                    )
                                        ? (int)$app_setting_key_arr[
                                            "rssd_allocation_days"
                                        ]
                                        : 0;


                                $rssd_days_disabled =
                                    (
                                        $fetched_rssd_allocation_change !=
                                        "true"
                                    );

                                ?>


                                <!-- RSSD ALLOCATION CHANGE -->

                                <tr>

                                    <td>

                                        <p>
                                            RSSD Allocation change
                                        </p>

                                        <span class="small_msg">

                                            Enable or disable RSSD allocation
                                            change.

                                        </span>

                                    </td>


                                    <td>

                                        <select
                                            class="form-control"
                                            id="rssd_allocation_change"
                                            style="width:50%;"
                                        >

                                            <?php

                                            foreach (
                                                $rssd_allocation_change_arr
                                                as $rssd_allocation_change_arr_val
                                            ) {

                                            ?>

                                                <option
                                                    value="<?php echo $rssd_allocation_change_arr_val; ?>"
                                                    <?php

                                                    if (
                                                        $rssd_allocation_change_arr_val ==
                                                        $fetched_rssd_allocation_change
                                                    ) {

                                                        echo 'selected="selected"';

                                                    }

                                                    ?>
                                                >

                                                    <?php
                                                    echo $rssd_allocation_change_arr_val;
                                                    ?>

                                                </option>

                                            <?php

                                            }

                                            ?>

                                        </select>

                                    </td>


                                    <td rowspan="2">

                                        <a
                                            href="javascript:void(0);"
                                            class="btn bg-red waves-effect upd_rssd_allocation_btn the_float_btn"
                                        >
                                            Update
                                        </a>

                                        <span
                                            class="rssd_allocation_msg_ldr the_float_msg"
                                        ></span>

                                        <span class="span_clear"></span>

                                    </td>

                                </tr>


                                <!-- RSSD ALLOCATION DAYS -->

                                <tr>

                                    <td>

                                        <p>

                                            RSSD Allocation change (days)

                                            <span
                                                id="rssd_days_required_star"
                                                class="estarix_cls"
                                                <?php

                                                if (
                                                    $rssd_days_disabled
                                                ) {

                                                    echo 'style="display:none;"';

                                                }

                                                ?>
                                            >
                                                *
                                            </span>

                                        </p>


                                        <span class="small_msg">

                                            Number of days for RSSD allocation
                                            change.

                                            <br>

                                            Mandatory when RSSD Allocation
                                            change is TRUE.

                                        </span>

                                    </td>


                                    <td>

                                        <input
                                            type="number"
                                            class="form-control"
                                            id="rssd_allocation_days"
                                            min="<?php

                                                echo $rssd_days_disabled
                                                    ? "0"
                                                    : "1";

                                            ?>"
                                            step="1"
                                            value="<?php
                                                echo $fetched_rssd_allocation_days;
                                            ?>"
                                            style="width:50%;"

                                            <?php

                                            if ($rssd_days_disabled) {

                                                echo "disabled";

                                            }

                                            ?>
                                        >

                                    </td>

                                </tr>


                                <!-- ============================================================
                                     GST VERIFICATION
                                ============================================================= -->

                                <tr>

                                    <td>

                                        <p>GST Verification</p>

                                        <span class="small_msg">
                                        </span>

                                    </td>


                                    <td>

                                        <?php

                                        $fetched_gst_verification =
                                            isset(
                                                $app_setting_key_arr[
                                                    "gst_verification"
                                                ]
                                            )
                                                ? strtolower(
                                                    trim(
                                                        $app_setting_key_arr[
                                                            "gst_verification"
                                                        ]
                                                    )
                                                )
                                                : "false";

                                        ?>

                                        <select
                                            class="form-control gst_verification"
                                            id="gst_verification"
                                        >

                                            <?php

                                            foreach (
                                                $gst_verification_arr
                                                as $gst_verification_arr_val
                                            ) {

                                            ?>

                                                <option
                                                    value="<?php echo $gst_verification_arr_val; ?>"
                                                    <?php

                                                    if (
                                                        $gst_verification_arr_val ==
                                                        $fetched_gst_verification
                                                    ) {

                                                        echo 'selected="selected"';

                                                    }

                                                    ?>
                                                >

                                                    <?php
                                                    echo $gst_verification_arr_val;
                                                    ?>

                                                </option>

                                            <?php

                                            }

                                            ?>

                                        </select>

                                    </td>


                                    <td>

                                        <a
                                            href="javascript:void(0);"
                                            class="btn bg-red waves-effect upd_gst_verification_btn the_float_btn"
                                        >
                                            Update
                                        </a>

                                        <span
                                            class="gst_verification_msg_ldr the_float_msg"
                                        ></span>

                                        <span class="span_clear"></span>

                                    </td>

                                </tr>


                                <!-- ============================================================
                                     LIFTING REGISTRATION IMAGE
                                ============================================================= -->

                                <tr>

                                    <td>

                                        <p>Lifting Registration Image</p>

                                        <span class="small_msg">

                                            Upload an image. Uploading a new one
                                            will replace the existing image.

                                        </span>

                                    </td>


                                    <td>

                                        <?php

                                        $fetched_lifting_registration =
                                            isset(
                                                $app_setting_key_arr[
                                                    "lifting_registration"
                                                ]
                                            )
                                                ? $app_setting_key_arr[
                                                    "lifting_registration"
                                                ]
                                                : "";


                                        $admin_dir =
                                            rtrim(
                                                str_replace(
                                                    "\\",
                                                    "/",
                                                    dirname(
                                                        $_SERVER['SCRIPT_NAME']
                                                    )
                                                ),
                                                "/"
                                            );


                                        $lifting_img_src =
                                            $fetched_lifting_registration
                                                ? "https://" .
                                                    $_SERVER['SERVER_NAME'] .
                                                    $admin_dir .
                                                    "/" .
                                                    $fetched_lifting_registration
                                                : "images/no_image.png";

                                        ?>


                                        <img
                                            id="lifting_registration_preview"
                                            src="<?php
                                                echo $lifting_img_src;
                                            ?>"
                                            style="
                                                max-width:150px;
                                                max-height:150px;
                                                display:block;
                                                margin-bottom:8px;
                                                border:1px solid #ddd;
                                            "
                                        >


                                        <input
                                            type="file"
                                            id="lifting_registration_file"
                                            accept="image/*"
                                            style="width:100%;"
                                        >

                                    </td>


                                    <td>

                                        <a
                                            href="javascript:void(0);"
                                            class="btn bg-red waves-effect upd_lifting_registration_btn the_float_btn"
                                        >
                                            Upload / Replace
                                        </a>

                                        <span
                                            class="lifting_registration_msg_ldr the_float_msg"
                                        ></span>

                                        <span class="span_clear"></span>

                                    </td>

                                </tr>


                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<script type="text/javascript">

jQuery(function () {


    // ============================================================
    // COMMON LOADER IMAGES
    // ============================================================

    var img =
        '<img style="min-width:16px;width:16px;height:16px;margin:0 auto;" src="images/ajax-loader.gif">';


    var success_tik =
        '<img style="min-width:16px;width:16px;height:16px;margin:0 auto;" src="images/success_tick.png">';



    // ============================================================
    // RSSD ALLOCATION CHANGE
    //
    // TRUE:
    //      DAYS ENABLED
    //      DAYS MANDATORY
    //
    // FALSE:
    //      DAYS DISABLED
    //      DAYS = 0
    // ============================================================

    function handleRssdAllocationChange() {

        var rssd_allocation_change =
            jQuery.trim(
                jQuery("#rssd_allocation_change").val()
            );


        if (rssd_allocation_change == "true") {


            // Enable days field

            jQuery("#rssd_allocation_days")
                .prop("disabled", false)
                .attr("min", "1");


            // Show mandatory *

            jQuery("#rssd_days_required_star")
                .show();


        } else {


            // FALSE
            // Set days to zero

            jQuery("#rssd_allocation_days")
                .val("0")
                .prop("disabled", true)
                .attr("min", "0");


            // Hide mandatory *

            jQuery("#rssd_days_required_star")
                .hide();

        }

    }



    // ============================================================
    // RUN ON PAGE LOAD
    // ============================================================

    handleRssdAllocationChange();



    // ============================================================
    // DROPDOWN CHANGE
    // ============================================================

    jQuery("#rssd_allocation_change").change(function () {

        handleRssdAllocationChange();

    });



    // ============================================================
    // UPDATE RSSD ALLOCATION + DAYS
    //
    // SINGLE UPDATE BUTTON
    // ============================================================

    jQuery(".upd_rssd_allocation_btn").click(function () {


        var rssd_allocation_change =
            jQuery.trim(
                jQuery("#rssd_allocation_change").val()
            );


        var rssd_allocation_days =
            jQuery.trim(
                jQuery("#rssd_allocation_days").val()
            );


        // ========================================================
        // VALIDATE TRUE / FALSE
        // ========================================================

        if (
            rssd_allocation_change != "true" &&
            rssd_allocation_change != "false"
        ) {

            alert(
                "Invalid RSSD allocation change value."
            );

            return;
        }



        // ========================================================
        // FALSE
        //
        // DAYS ALWAYS 0
        // ========================================================

        if (
            rssd_allocation_change == "false"
        ) {

            rssd_allocation_days = "0";


            jQuery("#rssd_allocation_days")
                .val("0");

        }



        // ========================================================
        // TRUE
        //
        // DAYS IS MANDATORY
        // ========================================================

        if (
            rssd_allocation_change == "true"
        ) {


            // Empty

            if (
                rssd_allocation_days == ""
            ) {

                alert(
                    "RSSD Allocation change days is mandatory when RSSD Allocation change is TRUE."
                );


                jQuery("#rssd_allocation_days")
                    .focus();


                return;
            }



            // Numeric

            if (
                isNaN(rssd_allocation_days)
            ) {

                alert(
                    "Please enter a valid RSSD Allocation change days."
                );


                jQuery("#rssd_allocation_days")
                    .focus();


                return;
            }



            // Positive

            if (
                parseInt(rssd_allocation_days) <= 0
            ) {

                alert(
                    "RSSD Allocation change days must be greater than 0."
                );


                jQuery("#rssd_allocation_days")
                    .focus();


                return;
            }



            // Integer

            rssd_allocation_days =
                parseInt(rssd_allocation_days);

        }



        // ========================================================
        // LOADER
        // ========================================================

        jQuery(".rssd_allocation_msg_ldr")
            .html(img);



        // ========================================================
        // AJAX
        // ========================================================

        jQuery.ajax({

            url:
                "ajax_update_rssd_allocation.php",

            type:
                "post",

            dataType:
                "json",

            data: {

                rssd_allocation_change:
                    rssd_allocation_change,

                rssd_allocation_days:
                    rssd_allocation_days

            },


            success: function (response) {


                if (
                    response.process_sts == "YES"
                ) {


                    jQuery(".rssd_allocation_msg_ldr")
                        .html(success_tik);


                    // Refresh UI state

                    handleRssdAllocationChange();


                    setTimeout(function () {

                        jQuery(".rssd_allocation_msg_ldr")
                            .html("");

                    }, 5000);


                } else {


                    jQuery(".rssd_allocation_msg_ldr")
                        .html("");


                    alert(
                        response.msg
                            ? response.msg
                            : "Update failed."
                    );

                }

            },


            error: function () {

                jQuery(".rssd_allocation_msg_ldr")
                    .html("");

                alert(
                    "Update failed."
                );

            }

        });

    });



    // ============================================================
    // IOS REGISTRATION BUTTON
    // ============================================================

    jQuery(".upd_is_show_reg_btn_btn").click(function () {


        var is_show_reg_btn =
            jQuery.trim(
                jQuery("#is_show_reg_btn").val()
            );


        jQuery(".is_show_reg_btn_msg_ldr")
            .html(img);


        jQuery.ajax({

            url:
                "ajax_update_enable_reg_button.php",

            type:
                "post",

            dataType:
                "json",

            data:
                "is_show_reg_btn=" +
                encodeURIComponent(
                    is_show_reg_btn
                ),


            success: function (response) {


                if (
                    response.process_sts == "YES"
                ) {


                    jQuery(".is_show_reg_btn_msg_ldr")
                        .html(success_tik);


                    setTimeout(function () {

                        jQuery(".is_show_reg_btn_msg_ldr")
                            .html("");

                    }, 5000);

                } else {

                    jQuery(".is_show_reg_btn_msg_ldr")
                        .html("");

                }

            }

        });

    });



    // ============================================================
    // CARRY FORWARD PROCESS
    // ============================================================

    jQuery(".upd_str_carry_forward_btn").click(function () {


        var str_carry_forward_process =
            jQuery.trim(
                jQuery("#str_carry_forward_process").val()
            );


        jQuery(".str_carry_forward_msg_ldr")
            .html(img);


        jQuery.ajax({

            url:
                "ajax_update_carry_forward_process.php",

            type:
                "post",

            dataType:
                "json",

            data:
                "str_carry_forward_process=" +
                encodeURIComponent(
                    str_carry_forward_process
                ),


            success: function (response) {


                if (
                    response.process_sts == "YES"
                ) {


                    jQuery(".str_carry_forward_msg_ldr")
                        .html(success_tik);


                    setTimeout(function () {

                        jQuery(".str_carry_forward_msg_ldr")
                            .html("");

                    }, 5000);

                } else {

                    jQuery(".str_carry_forward_msg_ldr")
                        .html("");

                }

            }

        });

    });



    // ============================================================
    // CARRY FORWARD PROCESS MESSAGE
    // ============================================================

    jQuery(".upd_str_carry_forward_message_btn").click(function () {


        var str_carry_forward_process_message =
            jQuery.trim(
                jQuery("#str_carry_forward_process_message").val()
            );


        jQuery(".str_carry_forward_message_msg_ldr")
            .html(img);


        jQuery.ajax({

            url:
                "ajax_update_carry_forward_process_message.php",

            type:
                "post",

            dataType:
                "json",

            data:
                "str_carry_forward_process_message=" +
                encodeURIComponent(
                    str_carry_forward_process_message
                ),


            success: function (response) {


                if (
                    response.process_sts == "YES"
                ) {


                    jQuery(".str_carry_forward_message_msg_ldr")
                        .html(success_tik);


                    setTimeout(function () {

                        jQuery(".str_carry_forward_message_msg_ldr")
                            .html("");

                    }, 5000);

                } else {

                    jQuery(".str_carry_forward_message_msg_ldr")
                        .html("");

                }

            }

        });

    });



    // ============================================================
    // GST VERIFICATION
    // ============================================================

    jQuery(".upd_gst_verification_btn").click(function () {


        var gst_verification =
            jQuery.trim(
                jQuery("#gst_verification").val()
            );


        jQuery(".gst_verification_msg_ldr")
            .html(img);


        jQuery.ajax({

            url:
                "ajax_update_gst_verification.php",

            type:
                "post",

            dataType:
                "json",

            data:
                "gst_verification=" +
                encodeURIComponent(
                    gst_verification
                ),


            success: function (response) {


                if (
                    response.process_sts == "YES"
                ) {


                    jQuery(".gst_verification_msg_ldr")
                        .html(success_tik);


                    setTimeout(function () {

                        jQuery(".gst_verification_msg_ldr")
                            .html("");

                    }, 5000);

                } else {

                    jQuery(".gst_verification_msg_ldr")
                        .html("");

                }

            }

        });

    });



    // ============================================================
    // LIFTING REGISTRATION IMAGE
    // ============================================================

    jQuery(".upd_lifting_registration_btn").click(function () {


        var file_data =
            jQuery(
                "#lifting_registration_file"
            )[0].files[0];


        if (!file_data) {

            alert(
                "Please choose an image first."
            );

            return;
        }


        var form_data =
            new FormData();


        form_data.append(
            "lifting_registration_file",
            file_data
        );


        jQuery(".lifting_registration_msg_ldr")
            .html(img);


        jQuery.ajax({

            url:
                "ajax_update_lifting_registration.php",

            type:
                "post",

            dataType:
                "json",

            data:
                form_data,

            contentType:
                false,

            processData:
                false,


            success: function (response) {


                if (
                    response.process_sts == "YES"
                ) {


                    jQuery(
                        "#lifting_registration_preview"
                    ).attr(

                        "src",

                        response.img_url +
                        "?t=" +
                        new Date().getTime()

                    );


                    jQuery(
                        ".lifting_registration_msg_ldr"
                    ).html(success_tik);


                    setTimeout(function () {

                        jQuery(
                            ".lifting_registration_msg_ldr"
                        ).html("");

                    }, 5000);


                } else {


                    jQuery(
                        ".lifting_registration_msg_ldr"
                    ).html("");


                    alert(
                        response.msg
                            ? response.msg
                            : "Upload failed."
                    );

                }

            },


            error: function () {


                jQuery(
                    ".lifting_registration_msg_ldr"
                ).html("");


                alert(
                    "Upload failed."
                );

            }

        });

    });


});

</script>


<?php

include "web_footer.php";

mysql_close();

?>
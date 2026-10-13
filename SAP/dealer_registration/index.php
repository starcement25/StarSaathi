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

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"
        name="viewport">

    <title>Star Cement - Dealer Registration</title>

    <link rel="icon"
        href="../dashboard/images/star_logo.jpg"
        type="image/x-icon">

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&subset=latin,cyrillic-ext"
        rel="stylesheet"
        type="text/css">

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap"
        rel="stylesheet">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
        rel="stylesheet"
        type="text/css">

    <link href="../dashboard/plugins/bootstrap/css/bootstrap.css"
        rel="stylesheet">

    <link href="../dashboard/plugins/node-waves/waves.css"
        rel="stylesheet" />

    <link href="../dashboard/plugins/animate-css/animate.css"
        rel="stylesheet" />

    <link href="../dashboard/css/style.css"
        rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="../dashboard/plugins/jquery/jquery.min.js"></script>

    <script src="../dashboard/plugins/bootstrap/js/bootstrap.js"></script>

    <script src="../dashboard/plugins/node-waves/waves.js"></script>

    <script src="../dashboard/plugins/jquery-validation/jquery.validate.js"></script>

    <script src="../dashboard/js/admin.js"></script>


<style>

:root {
    --brand-red: #ed2726;
    --brand-red-dark: #b81c1b;
    --brand-maroon-1: #2b0a0a;
    --brand-maroon-2: #150404;
    --brand-gold: #d9ad55;
    --brand-gold-light: #f0d99b;
    --page-bg: var(--brand-red);
    --ink: #26201f;
    --muted: #928a89;
    --hairline: #ece5e2;
    --card-radius: 18px;
}

* {
    box-sizing: border-box;
}

html,
body {
    width: 100%;
    max-width: 100%;
    margin: 0;
    padding: 0;
    overflow-x: hidden !important;
    font-family: 'Roboto', sans-serif;
}

html {
    background: var(--page-bg);
}

body.login-page {
    width: 100%;
    min-height: 100vh;
    padding: 0 0 30px 0;
    overflow-x: hidden !important;
    background: var(--page-bg);
}

body.login-page > h3 {
    display: none;
}


/* =========================================
   HERO
========================================= */

.reg-hero {
    position: relative;
    width: 100%;
    line-height: 0;
    overflow: hidden;
}

.reg-hero .hero-img {
    display: block;
    width: 100%;
    height: auto;
}

.reg-hero .hero-fade {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 30%;
    background: linear-gradient(
        to bottom,
        rgba(13,3,3,0) 0%,
        rgba(13,3,3,0.45) 60%,
        var(--page-bg) 100%
    );
    pointer-events: none;
}

.reg-hero .hero-tag {
    position: absolute;
    left: 50%;
    bottom: 26px;
    transform: translateX(-50%);
    z-index: 1;
    padding: 10px 22px;
    border-radius: 999px;
    background: linear-gradient(
        180deg,
        var(--brand-red) 0%,
        var(--brand-red-dark) 100%
    );
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    box-shadow: 0 10px 22px rgba(0,0,0,0.30);
    white-space: nowrap;
}


/* =========================================
   CARD
========================================= */

.login-box {
    display: block;
    width: 100% !important;
    max-width: 460px !important;
    margin: -54px auto 0 auto;
    padding: 10px;
    position: relative;
    z-index: 2;
    background: #000000;
    border-radius: 18px;
}

.login-box .card {
    width: 100%;
    max-width: 100%;
    margin: 0;
    overflow: hidden;
    border-radius: var(--card-radius);
    box-shadow: 0 18px 40px rgba(30,10,10,0.18);
    border: 2px solid #000000;
}

.login-box .body {
    width: 100%;
    max-width: 100%;
    padding: 26px 20px 22px 20px !important;
    overflow: hidden;
    background: #ffffff;
}

.login-box .msg {
    display: none;
}


/* =========================================
   SECTION LABELS
========================================= */

.section-label {
    display: block;
    width: 100%;
    margin: 4px 0 10px 0;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: var(--brand-red);
}

.section-label:not(:first-child) {
    margin-top: 22px;
    padding-top: 16px;
    border-top: 1px dashed var(--hairline);
}


/* =========================================
   FORM INPUTS
========================================= */

.login-box .input-group {
    display: flex !important;
    width: 100% !important;
    max-width: 100% !important;
    margin-bottom: 14px;
    table-layout: auto !important;
    align-items: stretch;
    border: 1.5px solid var(--hairline);
    border-radius: 12px;
    background: #fbfaf8;
    transition:
        border-color .15s ease,
        background .15s ease;
}

.login-box .input-group:focus-within {
    border-color: var(--brand-red);
    background: #ffffff;
}

.login-box .input-group-addon {
    display: flex !important;
    flex: 0 0 44px;
    width: 44px !important;
    min-width: 44px;
    max-width: 44px;
    align-items: center;
    justify-content: center;
    padding: 0 !important;
    border: 0 !important;
    background: transparent !important;
}

.login-box .input-group-addon i {
    font-size: 19px;
    color: var(--brand-red);
    opacity: 0.85;
}

.login-box .input-group .form-line {
    flex: 1 1 auto;
    width: auto !important;
    min-width: 0;
    max-width: 100%;
    display: block;
    border: none !important;
    margin: 0 !important;
}

.login-box .input-group .form-control {
    display: block;
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0;
    box-sizing: border-box;
    height: 46px;
    padding: 0 14px 0 0 !important;
    border: none !important;
    background: transparent !important;
    font-size: 14px;
    color: var(--ink);
    box-shadow: none !important;
}

.login-box .input-group .form-control::placeholder {
    color: #a79f9c;
}


/* Remove blue focus line */

.login-box .input-group .form-line,
.login-box .input-group .form-line:before,
.login-box .input-group .form-line:after {
    border: none !important;
    box-shadow: none !important;
}

.login-box .input-group .form-line:after {
    display: none !important;
}

.login-box .input-group .form-control,
.login-box .input-group .form-control:focus,
.login-box .input-group .form-control:active {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
}


/* Remove extra vertical space */

.login-box .input-group .form-control {
    height: 46px !important;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
    line-height: 46px !important;
}

.login-box .input-group:focus-within {
    border-color: var(--brand-red) !important;
    box-shadow: none !important;
}


/* =========================================
   SELECT
========================================= */

select.form-control {
    appearance: none;
    -webkit-appearance: none;
    background-image:
        url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='8'><path d='M1 1l5 5 5-5' stroke='%23ed2726' stroke-width='1.6' fill='none' fill-rule='evenodd'/></svg>") !important;
    background-repeat: no-repeat !important;
    background-position: right 4px center !important;
}


/* =========================================
   ID PROOF RADIO GROUP
========================================= */

.id-proof-caption {
    display: block;
    margin: 0 0 10px 0;
    font-size: 12px;
    color: var(--muted);
}

.radio-group {
    display: flex;
    flex-wrap: nowrap;
    gap: 12px;
    margin-bottom: 14px;
}

.radio-pill {
    display: flex;
    flex: 0 0 auto;
    align-items: center;
    gap: 5px;
    font-size: 10.5px;
    line-height: 1.2;
    color: var(--ink);
    cursor: pointer;
    white-space: nowrap;
    user-select: none;
}

.radio-pill .txt {
    text-align: left;
    padding-top: 0;
    white-space: nowrap;
}

.radio-pill input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
    margin: 0;
}

.radio-pill .dot {
    flex: 0 0 13px;
    width: 13px;
    height: 13px;
    margin-top: 1px;
    border-radius: 50%;
    border: 1.5px solid #c9bcbb;
    background: #fff;
    position: relative;
    transition: border-color .15s ease;
}

.radio-pill .dot::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--brand-red);
    transform: translate(-50%, -50%) scale(0);
    transition: transform .15s ease;
}

.radio-pill input[type="radio"]:checked ~ .dot {
    border-color: var(--brand-red);
}

.radio-pill input[type="radio"]:checked ~ .dot::after {
    transform: translate(-50%, -50%) scale(1);
}

.radio-pill input[type="radio"]:focus-visible ~ .dot {
    outline: 2px solid var(--brand-red);
    outline-offset: 2px;
}


/* =========================================
   KURTA SIZE
========================================= */

.size-group {
    display: flex;
    flex-wrap: nowrap;
    gap: 5px;
    margin-bottom: 14px;
}

.size-option {
    position: relative;
    flex: 1 1 0;
    min-width: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 8px 2px;
    border: 1.5px solid #e3a9a8;
    border-radius: 10px;
    background: #fff7f7;
    cursor: pointer;
    text-align: center;
    overflow: hidden;
    transition:
        border-color .15s ease,
        background .15s ease;
    user-select: none;
}

.size-option.is-checked {
    border-color: var(--brand-red);
    background: #fff0f0;
}

.size-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
    margin: 0;
}

.size-option .size-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 1.5px solid #c9bcbb;
    background: #fff;
    position: relative;
    transition: border-color .15s ease;
}

.size-option .size-dot::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--brand-red);
    transform: translate(-50%, -50%) scale(0);
    transition: transform .15s ease;
}

.size-option input[type="radio"]:checked ~ .size-dot {
    border-color: var(--brand-red);
}

.size-option input[type="radio"]:checked ~ .size-dot::after {
    transform: translate(-50%, -50%) scale(1);
}

.size-option .size-label {
    font-size: 11px;
    font-weight: 700;
    color: var(--ink);
    letter-spacing: -0.2px;
}

.size-option .size-sub {
    font-size: 7.5px;
    color: var(--muted);
    white-space: nowrap;
    letter-spacing: -0.2px;
}

.size-option.is-checked .size-label,
.size-option.is-checked .size-sub {
    color: var(--brand-red-dark);
}


/* =========================================
   UPLOAD DROPZONES
========================================= */

.upload-tile {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
    padding: 22px 10px;
    border: 1.5px dashed #000000;
    border-radius: 14px;
    background: #fff7f7;
    text-align: center;
    cursor: pointer;
    transition:
        border-color .15s ease,
        background .15s ease,
        opacity .15s ease;
}

.upload-tile:hover,
.upload-tile.has-file {
    border-color: var(--brand-red);
    background: #fff0f0;
}

.upload-tile i {
    font-size: 26px;
    color: var(--brand-red);
}

.upload-tile .up-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--ink);
}

.upload-tile .up-sub {
    font-size: 11px;
    color: var(--muted);
}

.upload-tile .up-filename {
    max-width: 100%;
    font-size: 11px;
    color: var(--brand-red-dark);
    word-break: break-all;
    padding: 0 4px;
}

.upload-tile input[type="file"] {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}


/* =========================================
   DISABLED UPLOAD TILE
========================================= */

.upload-tile.disabled {
    opacity: 0.45;
    cursor: not-allowed;
    background: #f5f5f5;
    border-color: #cccccc;
}

.upload-tile.disabled i {
    color: #999999;
}

.upload-tile.disabled .up-title,
.upload-tile.disabled .up-sub {
    color: #999999;
}


/* =========================================
   UPLOAD ROW
========================================= */

.upload-row {
    display: flex;
    gap: 12px;
    margin-bottom: 4px;
}

.upload-row .upload-tile {
    flex: 1 1 0;
    padding: 18px 6px;
}


/* =========================================
   SUBMIT BUTTON
========================================= */

#submit_dealer_btn {
    display: block;
    width: 100% !important;
    max-width: 100%;
    margin: 24px 0 0 0;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(
        180deg,
        var(--brand-red) 0%,
        var(--brand-red-dark) 100%
    );
    border: none;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.6px;
    box-shadow: 0 10px 22px rgba(237,39,38,0.30);
}

.btn[disabled] {
    opacity: 0.6;
    cursor: not-allowed;
}


/* =========================================
   RESPONSE MESSAGE
========================================= */

.message-group {
    display: block !important;
    width: 100% !important;
    min-height: 30px;
    margin: 0 !important;
    padding: 10px 0 0 0 !important;
    text-align: center;
}

.msg_resp {
    display: inline-block;
    max-width: 100%;
    word-break: break-word;
    line-height: 18px;
    font-size: 13px;
    color: var(--brand-red-dark);
}


/* =========================================
   SUCCESS MODAL
========================================= */

.modal-dialog {
    width: auto;
    max-width: 420px;
    margin: 30px auto;
}

.modal-content {
    width: 100%;
    max-width: 100%;
    border-radius: 16px;
    overflow: hidden;
    border: none;
}

.modal-header {
    background: linear-gradient(
        180deg,
        var(--brand-red) 0%,
        var(--brand-red-dark) 100%
    );
    border-bottom: none;
}

.modal-header .close {
    color: #fff;
    opacity: 0.9;
    text-shadow: none;
}

#successUploadedImage {
    display: block;
    width: auto;
    max-width: 100%;
    max-height: 400px;
    height: auto;
    margin: 15px auto;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 5px;
}


/* =========================================
   TABLET
========================================= */

@media (max-width: 600px) {

    .login-box {
        width: 100% !important;
        max-width: 420px !important;
        margin-top: -46px;
    }

    .login-box .body {
        padding: 20px 16px !important;
    }

    .login-box .input-group {
        margin-bottom: 12px;
    }

    .login-box .input-group-addon {
        flex-basis: 40px;
        width: 40px !important;
        min-width: 40px;
        max-width: 40px;
    }

    .login-box .input-group-addon i {
        font-size: 18px;
    }

    .login-box .form-control {
        font-size: 13.5px;
    }

    .modal-dialog {
        margin: 15px;
    }
}


/* =========================================
   MOBILE
========================================= */

@media (max-width: 480px) {

    .reg-hero .hero-tag {
        font-size: 12px;
        padding: 8px 18px;
        bottom: 20px;
    }

    .login-box {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 12px;
    }

    .login-box .body {
        padding: 18px 14px !important;
    }

    .login-box .input-group-addon {
        flex-basis: 38px;
        width: 38px !important;
        min-width: 38px;
        max-width: 38px;
    }

    .upload-row {
        gap: 8px;
    }

    .radio-group {
        gap: 8px;
    }

    .radio-pill {
        flex: 0 0 auto;
        font-size: 9.5px;
        gap: 4px;
        white-space: nowrap;
    }

    .radio-pill .txt {
        white-space: nowrap;
    }

    .radio-pill .dot {
        flex-basis: 12px;
        width: 12px;
        height: 12px;
    }

    .size-group {
        gap: 3px;
    }

    .size-option {
        padding: 6px 1px;
    }

    .size-option .size-label {
        font-size: 10px;
    }

    .size-option .size-sub {
        font-size: 6.5px;
    }

    #submit_dealer_btn {
        font-size: 14px;
    }
}


/* =========================================
   VERY SMALL MOBILE
========================================= */

@media (max-width: 320px) {

    .login-box .body {
        padding: 14px 10px !important;
    }

    .radio-group {
        gap: 3px;
    }

    .radio-pill {
        font-size: 8.5px;
        gap: 3px;
    }

    .radio-pill .dot {
        flex-basis: 11px;
        width: 11px;
        height: 11px;
    }

    .size-group {
        gap: 2px;
    }

    .size-option {
        padding: 5px 1px;
    }

    .size-option .size-label {
        font-size: 9px;
    }

    .size-option .size-sub {
        font-size: 6px;
    }
}

</style>

</head>


<body class="login-page">


<div class="reg-hero">

    <img
        class="hero-img"
        src="uploads/star_vivah.jpg"
        alt="Star Vivaah - Star Cement">

    <div class="hero-fade"></div>

    <span class="hero-tag">
        Dealer Registration
    </span>

</div>


<div class="login-box">

    <div class="card">

        <div class="body">

            <form
                id="dealer_reg_form"
                method="POST"
                action=""
                enctype="multipart/form-data">


                <!-- =========================================
                     YOUR DETAILS
                ========================================== -->

                <span class="section-label">
                    Your Details
                </span>


                <div class="input-group">

                    <span class="input-group-addon">
                        <i class="material-icons">person</i>
                    </span>

                    <div class="form-line">

                        <input
                            type="text"
                            class="form-control"
                            name="name"
                            id="dealer_name"
                            placeholder="Enter your name"
                            autocomplete="off"
                            autofocus>

                    </div>

                </div>


<?php /* ===== REMOVED (1): RELATION FIELD =====
                <div class="input-group">

                    <span class="input-group-addon">
                        <i class="material-icons">group</i>
                    </span>

                    <div class="form-line">

                        <select
                            class="form-control"
                            name="relation"
                            id="relation">

                            <option value="">
                                Relation
                            </option>

                            <option value="Self">
                                Self
                            </option>

                            <option value="Family/Friends">
                                Family/Friends
                            </option>

                        </select>

                    </div>

                </div>
===== END REMOVED (1) ===== */ ?>


                <!-- CHANGED (2): "Firm Name" -> "Shop Name"
                     OLD: placeholder="Firm Name"
                     name/id kept as firm_name so backend still works -->
                <div class="input-group">

                    <span class="input-group-addon">
                        <i class="material-icons">store</i>
                    </span>

                    <div class="form-line">

                        <input
                            type="text"
                            class="form-control"
                            name="firm_name"
                            id="firm_name"
                            placeholder="Shop Name"
                            autocomplete="off">

                    </div>

                </div>


<?php /* ===== REMOVED (3): AREA FIELD =====
                <div class="input-group">

                    <span class="input-group-addon">
                        <i class="material-icons">place</i>
                    </span>

                    <div class="form-line">

                        <input
                            type="text"
                            class="form-control"
                            name="area"
                            id="area"
                            placeholder="Area"
                            autocomplete="off">

                    </div>

                </div>
===== END REMOVED (3) ===== */ ?>


<?php /* ===== REMOVED (4): DISTRICT FIELD =====
                <div class="input-group">

                    <span class="input-group-addon">
                        <i class="material-icons">place</i>
                    </span>

                    <div class="form-line">

                        <input
                            type="text"
                            class="form-control"
                            name="district"
                            id="district"
                            placeholder="District"
                            autocomplete="off">

                    </div>

                </div>
===== END REMOVED (4) ===== */ ?>


                <div class="input-group">

                    <span class="input-group-addon">
                        <i class="material-icons">dialpad</i>
                    </span>

                    <div class="form-line">

                        <input
                            type="tel"
                            class="form-control"
                            name="contact_no"
                            id="contact_no"
                            maxlength="10"
                            placeholder="Phone No."
                            autocomplete="off">

                    </div>

                </div>


<?php /* ===== REMOVED (5): KURTA SIZE SECTION =====

                <span class="section-label">
                  <i class="fas fa-shirt"></i>  Kurta Size
                </span>


                <div
                    class="size-group"
                    id="kurta_size_group">


                    <label class="size-option">
                        <input type="radio" name="kurta_size" value="S">
                        <span class="size-dot"></span>
                        <span class="size-label">S</span>
                        <span class="size-sub">(36 size)</span>
                    </label>


                    <label class="size-option">
                        <input type="radio" name="kurta_size" value="M">
                        <span class="size-dot"></span>
                        <span class="size-label">M</span>
                        <span class="size-sub">(38 size)</span>
                    </label>


                    <label class="size-option">
                        <input type="radio" name="kurta_size" value="L">
                        <span class="size-dot"></span>
                        <span class="size-label">L</span>
                        <span class="size-sub">(40 size)</span>
                    </label>


                    <label class="size-option">
                        <input type="radio" name="kurta_size" value="XL">
                        <span class="size-dot"></span>
                        <span class="size-label">XL</span>
                        <span class="size-sub">(42 size)</span>
                    </label>


                    <label class="size-option">
                        <input type="radio" name="kurta_size" value="XXL">
                        <span class="size-dot"></span>
                        <span class="size-label">XXL</span>
                        <span class="size-sub">(44 size)</span>
                    </label>


                    <label class="size-option">
                        <input type="radio" name="kurta_size" value="XXXL">
                        <span class="size-dot"></span>
                        <span class="size-label">XXXL</span>
                        <span class="size-sub">(46 size)</span>
                    </label>


                    <label class="size-option">
                        <input type="radio" name="kurta_size" value="XXXXL">
                        <span class="size-dot"></span>
                        <span class="size-label">XXXXL</span>
                        <span class="size-sub">(48 size)</span>
                    </label>


                </div>

===== END REMOVED (5) ===== */ ?>


                <!-- =========================================
                     SELFIE
                ========================================== -->

                <span class="section-label">
                    Selfie Photo
                </span>


                <label
                    class="upload-tile"
                    id="selfie_tile"
                    for="selfie_photo">

                    <i class="material-icons">
                        camera_alt
                    </i>

                    <span class="up-title">
                        Take Selfie / Upload Photo
                    </span>

                    <span class="up-sub">
                        Clear front-facing photo
                    </span>

                    <span
                        class="up-filename"
                        id="selfie_filename">
                    </span>

                    <input
                        type="file"
                        name="selfie_photo"
                        id="selfie_photo"
                        accept="image/*">

                </label>


<?php /* ===== REMOVED (6): ID PROOF SECTION (type radios + front/back photos) =====

                <span class="section-label">
                    ID Proof
                </span>


                <span class="id-proof-caption">
                    Select ID Proof Type
                </span>


                <div
                    class="radio-group"
                    id="id_proof_group">


                    <label class="radio-pill">
                        <input type="radio" name="id_proof" value="Aadhar Card">
                        <span class="dot"></span>
                        <span class="txt">Aadhar Card</span>
                    </label>


                    <label class="radio-pill">
                        <input type="radio" name="id_proof" value="Passport">
                        <span class="dot"></span>
                        <span class="txt">Passport</span>
                    </label>


                    <label class="radio-pill">
                        <input type="radio" name="id_proof" value="Driving License">
                        <span class="dot"></span>
                        <span class="txt">Driving License</span>
                    </label>


                </div>


                <div class="upload-row">


                    <label
                        class="upload-tile"
                        id="front_tile"
                        for="id_photo_front">

                        <i class="material-icons">
                            badge
                        </i>

                        <span class="up-title">
                            ID Front Photo
                        </span>

                        <span class="up-sub">
                            Upload front side
                        </span>

                        <span
                            class="up-filename"
                            id="front_filename">
                        </span>

                        <input
                            type="file"
                            name="id_photo_front"
                            id="id_photo_front"
                            accept="image/*">

                    </label>


                    <label
                        class="upload-tile disabled"
                        id="back_tile"
                        for="id_photo_back">

                        <i class="material-icons">
                            badge
                        </i>

                        <span class="up-title">
                            ID Back Photo
                        </span>

                        <span class="up-sub"
                              id="back_sub_text">
                            Select ID proof type
                        </span>

                        <span
                            class="up-filename"
                            id="back_filename">
                        </span>

                        <input
                            type="file"
                            name="id_photo_back"
                            id="id_photo_back"
                            accept="image/*"
                            disabled>

                    </label>


                </div>

===== END REMOVED (6) ===== */ ?>


                <!-- =========================================
                     SUBMIT
                ========================================== -->

                <div
                    class="input-group"
                    style="border:none;background:transparent;">

                    <input
                        type="button"
                        class="btn btn-block bg-red btn-lg waves-effect"
                        value="SUBMIT REGISTRATION"
                        id="submit_dealer_btn" />

                </div>


                <!-- =========================================
                     MESSAGE
                ========================================== -->

                <div
                    class="input-group message-group"
                    style="border:none;background:transparent;">

                    <span
                        class="msg_resp"
                        id="msg_resp">
                    </span>

                </div>


            </form>

        </div>

    </div>

</div>


<script type="text/javascript">

jQuery(document).ready(function () {


    var xhr1;

    var xhr_phone_check;

    var img1 =
        '<img src="../dashboard/images/ajax-loader.gif">';

    var msg_resp_elmnt =
        jQuery("#msg_resp");

    var is_submitting = false;

    var phone_is_unique = true;

    var last_checked_phone = "";


    /* =========================================
       FILE PREVIEW
    ========================================== */

    function bind_preview(
        input_id,
        tile_id,
        filename_id
    ) {

        jQuery("#" + input_id).on("change", function () {

            var $tile =
                jQuery("#" + tile_id);

            var $filename =
                jQuery("#" + filename_id);


            if (
                this.files &&
                this.files.length > 0
            ) {

                $tile.addClass("has-file");

                $filename.text(
                    this.files[0].name
                );

            } else {

                $tile.removeClass("has-file");

                $filename.text("");
            }

        });

    }


    bind_preview(
        "selfie_photo",
        "selfie_tile",
        "selfie_filename"
    );


    // REMOVED (6): ID photo previews (elements are commented out in HTML)
    // bind_preview("id_photo_front", "front_tile", "front_filename");
    // bind_preview("id_photo_back",  "back_tile",  "back_filename");


    /* =========================================
       KURTA SIZE
       REMOVED (5): section is commented out in HTML.
       This handler is harmless if left active, but kept
       commented here for clarity.
    ========================================== */

    // jQuery(
    //     "#kurta_size_group input[type='radio']"
    // ).on("change", function () {
    //
    //     jQuery(
    //         "#kurta_size_group .size-option"
    //     ).removeClass("is-checked");
    //
    //     jQuery(this)
    //         .closest(".size-option")
    //         .addClass("is-checked");
    //
    // });


    /* =========================================
       ID PROOF LOGIC
       REMOVED (6): the whole ID proof section is
       commented out in HTML, so this logic is
       commented out as well.
    ========================================== */

    // function updateIdPhotoFields() {
    //
    //     var id_proof =
    //         jQuery("input[name='id_proof']:checked").val();
    //
    //     var $frontInput = jQuery("#id_photo_front");
    //     var $backInput  = jQuery("#id_photo_back");
    //     var $frontTile  = jQuery("#front_tile");
    //     var $backTile   = jQuery("#back_tile");
    //     var $backSubText = jQuery("#back_sub_text");
    //
    //     // FRONT PHOTO: enabled when an ID proof is selected
    //     if (id_proof != "") {
    //         $frontInput.prop("disabled", false);
    //         $frontTile.removeClass("disabled");
    //     }
    //
    //     // DRIVING LICENSE: only front side required
    //     if (id_proof == "Driving License") {
    //         $backInput.val("");
    //         jQuery("#back_filename").text("");
    //         $backTile.removeClass("has-file").addClass("disabled");
    //         $backInput.prop("disabled", true);
    //         $backSubText.text("Not required for Driving License");
    //     }
    //     // AADHAR / PASSPORT: front and back required
    //     else if (id_proof == "Aadhar Card" || id_proof == "Passport") {
    //         $backInput.prop("disabled", false);
    //         $backTile.removeClass("disabled");
    //         $backSubText.text("Upload back side");
    //     }
    //     // Nothing selected
    //     else {
    //         $frontInput.prop("disabled", true);
    //         $backInput.prop("disabled", true);
    //         $frontTile.addClass("disabled");
    //         $backTile.addClass("disabled");
    //         $backSubText.text("Select ID proof type");
    //     }
    //
    // }
    //
    // jQuery("input[name='id_proof']").on("change", function () {
    //     updateIdPhotoFields();
    // });
    //
    // updateIdPhotoFields();


    /* =========================================
       MESSAGE
    ========================================== */

    function show_msg(
        text,
        clear_after
    ) {

        msg_resp_elmnt
            .html(text)
            .show();


        if (clear_after) {

            setTimeout(
                function () {

                    msg_resp_elmnt
                        .html("")
                        .hide();

                },
                clear_after
            );

        }

    }


    /* =========================================
       PHONE UNIQUENESS CHECK (on blur)
    ========================================== */

    jQuery("#contact_no").on("blur", function () {

        var contact_no =
            jQuery.trim(
                jQuery(this).val()
            );

        if (
            contact_no.length !== 10 ||
            !contact_no.match('^[0-9]{10}$')
        ) {
            /* let submit-time validation handle bad format */
            return;
        }

        if (contact_no === last_checked_phone) {
            /* already checked this exact number, no need to re-check */
            return;
        }

        if (
            xhr_phone_check &&
            xhr_phone_check.readyState != 4
        ) {

            xhr_phone_check.abort();

        }

        xhr_phone_check = jQuery.ajax({

            url: 'ajax_check_phone_unique.php',

            type: 'post',

            dataType: 'json',

            data: {
                contact_no: contact_no
            },

            success: function (response) {

                last_checked_phone = contact_no;

                if (response.exists == "YES") {

                    phone_is_unique = false;

                    show_msg(
                        "This phone number is already registered.",
                        5000
                    );

                    jQuery("#contact_no")
                        .addClass("has-error-border");

                } else {

                    phone_is_unique = true;

                    jQuery("#contact_no")
                        .removeClass("has-error-border");

                }

            }

        });

    });


    /* =========================================
       RESET FORM
    ========================================== */

    function reset_form() {

        jQuery("#dealer_reg_form")[0].reset();

        jQuery("#msg_resp")
            .html("")
            .hide();


        jQuery(".upload-tile")
            .removeClass("has-file");


        jQuery(".up-filename")
            .text("");


        // REMOVED (5): kurta size no longer on the form
        // jQuery(".size-option")
        //     .removeClass("is-checked");


        // REMOVED (6): ID proof no longer on the form
        // /* Restore initial ID state */
        // jQuery("#id_photo_front").prop("disabled", true);
        // jQuery("#id_photo_back").prop("disabled", true);
        // jQuery("#front_tile").addClass("disabled");
        // jQuery("#back_tile").addClass("disabled");
        // jQuery("#back_sub_text").text("Select ID proof type");


        /*
         * Reset phone uniqueness state
         */

        phone_is_unique = true;

        last_checked_phone = "";

        jQuery("#contact_no")
            .removeClass("has-error-border");


        is_submitting = false;


        jQuery("#submit_dealer_btn")
            .prop("disabled", false)
            .val("SUBMIT REGISTRATION");


        jQuery("#dealer_name")
            .focus();

    }


    /* =========================================
       SUBMIT
    ========================================== */

    jQuery("#submit_dealer_btn").click(
        function () {


            if (is_submitting) {
                return;
            }


            var $btn =
                jQuery(this);


            var name =
                jQuery.trim(
                    jQuery("#dealer_name").val()
                );


            // REMOVED (1): Relation
            // var relation =
            //     jQuery.trim(
            //         jQuery("#relation").val()
            //     );


            // firm_name field is now labelled "Shop Name" (2)
            var firm_name =
                jQuery.trim(
                    jQuery("#firm_name").val()
                );


            // REMOVED (3): Area
            // var area =
            //     jQuery.trim(
            //         jQuery("#area").val()
            //     );


            // REMOVED (4): District
            // var district =
            //     jQuery.trim(
            //         jQuery("#district").val()
            //     );


            var contact_no =
                jQuery.trim(
                    jQuery("#contact_no").val()
                );


            // REMOVED (5): Kurta size
            // var kurta_size =
            //     jQuery.trim(
            //         jQuery(
            //             "input[name='kurta_size']:checked"
            //         ).val() || ""
            //     );


            // REMOVED (6): ID proof type
            // var id_proof =
            //     jQuery.trim(
            //         jQuery(
            //             "input[name='id_proof']:checked"
            //         ).val() || ""
            //     );


            var selfie_photo =
                jQuery("#selfie_photo").val();


            // REMOVED (6): ID photos
            // var id_photo_front =
            //     jQuery("#id_photo_front").val();
            //
            // var id_photo_back =
            //     jQuery("#id_photo_back").val();


            /* =========================================
               BASIC FIELD VALIDATION
            ========================================== */

            // OLD (before changes):
            // if (
            //     name == "" ||
            //     relation == "" ||
            //     firm_name == "" ||
            //     area == "" ||
            //     district == "" ||
            //     contact_no == "" ||
            //     kurta_size == "" ||
            //     id_proof == ""
            // ) {
            //
            //     show_msg(
            //         "Please fill all required fields.",
            //         5000
            //     );
            //
            //     return;
            // }

            // NEW:
            if (
                name == "" ||
                firm_name == "" ||
                contact_no == ""
            ) {

                show_msg(
                    "Please fill all required fields.",
                    5000
                );

                return;
            }


            /* =========================================
               MOBILE VALIDATION
            ========================================== */

            if (
                contact_no.length < 10 ||
                !contact_no.match(
                    '^[0-9]{10}$'
                )
            ) {

                show_msg(
                    "Please enter a valid 10 digit mobile number.",
                    5000
                );

                jQuery("#contact_no")
                    .focus();

                return;
            }


            /* =========================================
               PHONE UNIQUENESS CHECK
            ========================================== */

            if (!phone_is_unique) {

                show_msg(
                    "This phone number is already registered.",
                    5000
                );

                jQuery("#contact_no")
                    .focus();

                return;
            }


            /* =========================================
               PHOTO VALIDATION
            ========================================== */

            // OLD CASE 1: both selfie and ID photos missing
            // if (
            //     selfie_photo == "" &&
            //     id_photo_front == ""
            // ) {
            //     show_msg(
            //         "Selfie photo and ID proof photo are required.",
            //         5000
            //     );
            //     return;
            // }


            // CASE 2 (kept): selfie missing
            if (
                selfie_photo == ""
            ) {

                show_msg(
                    "Selfie photo is required.",
                    5000
                );

                return;
            }


            // OLD CASE 3: ID front missing
            // if (
            //     id_photo_front == ""
            // ) {
            //     show_msg(
            //         "ID proof photo is required.",
            //         5000
            //     );
            //     return;
            // }


            // OLD CASE 4: Aadhar / Passport need back photo
            // if (
            //     id_proof == "Aadhar Card" ||
            //     id_proof == "Passport"
            // ) {
            //
            //     if (
            //         id_photo_back == ""
            //     ) {
            //
            //         show_msg(
            //             "ID proof back photo is required.",
            //             5000
            //         );
            //
            //         return;
            //     }
            //
            // }


            // OLD CASE 5: Driving License needs no back photo


            /* =========================================
               FORM DATA
            ========================================== */

            var form_data =
                new FormData(
                    jQuery(
                        "#dealer_reg_form"
                    )[0]
                );


            is_submitting = true;


            $btn
                .prop(
                    "disabled",
                    true
                )
                .val(
                    "SUBMITTING..."
                );


            show_msg(img1);


            if (
                xhr1 &&
                xhr1.readyState != 4
            ) {

                xhr1.abort();

            }


            /* =========================================
               AJAX
            ========================================== */

            xhr1 = jQuery.ajax({

                url:
                    'ajax_save_dealer_registration.php',

                type:
                    'post',

                dataType:
                    'json',

                data:
                    form_data,

                contentType:
                    false,

                processData:
                    false,


                success:
                    function (response) {


                        console.log(
                            "SERVER RESPONSE:",
                            response
                        );


                        if (
                            response.process_sts ==
                            "YES"
                        ) {


                            var successImageUrl =
                                <?php
                                echo json_encode(
                                    $success_image
                                );
                                ?>;


                            jQuery(
                                "#successUploadedImage"
                            )
                            .attr(
                                "src",
                                successImageUrl
                            );


                            jQuery(
                                "#msg_resp"
                            )
                            .html("")
                            .hide();


                            reset_form();


                            jQuery(
                                "#dealerSuccessModal"
                            ).modal("show");


                        }

                        else {


                            is_submitting =
                                false;


                            $btn
                                .prop(
                                    "disabled",
                                    false
                                )
                                .val(
                                    "SUBMIT REGISTRATION"
                                );


                            show_msg(
                                response.process_msg,
                                8000
                            );


                            console.log(
                                "UPLOAD DEBUG:",
                                response.upload_debug
                            );

                        }

                    },


                error:
                    function () {


                        is_submitting =
                            false;


                        $btn
                            .prop(
                                "disabled",
                                false
                            )
                            .val(
                                "SUBMIT REGISTRATION"
                            );


                        show_msg(
                            "Network error. Please try again.",
                            8000
                        );

                    },


                timeout:
                    0

            });

        }
    );

});

</script>


<!-- =========================================
     SUCCESS MODAL
========================================= -->

<div
    class="modal fade"
    id="dealerSuccessModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="dealerSuccessModalLabel">


    <div
        class="modal-dialog"
        role="document">


        <div class="modal-content">


            <div class="modal-header">


                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close">

                    <span aria-hidden="true">
                        &times;
                    </span>

                </button>


                <h4
                    class="modal-title"
                    id="dealerSuccessModalLabel"
                    style="color:#fff;">

                </h4>


            </div>


            <div
                class="modal-body text-center">


                <p>

                    <strong>
                        Your details have been saved successfully.
                    </strong>

                </p>


                <img
                    id="successUploadedImage"
                    src=""
                    alt="Uploaded Image"
                    style="
                        display:block;
                        max-width:100%;
                        max-height:400px;
                        width:auto;
                        height:auto;
                        margin:15px auto;
                        border:1px solid #ddd;
                        border-radius:10px;
                        padding:5px;
                    ">


            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-default"
                    data-dismiss="modal">

                    Close

                </button>

            </div>


        </div>

    </div>

</div>


</body>

</html>
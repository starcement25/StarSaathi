<?php

include "web_check.php";
include "star_connection.php";

$lift_table = "dealer_registration_web_form";

$new_qry_string_filtered = "";
$export_filtered_str = "";


/* =========================================================
   SEARCH PARAMETERS
========================================================= */

$srch_date_from = isset($_GET["srch_date_from"])
    ? addslashes(trim($_GET["srch_date_from"]))
    : "";

$srch_date_to = isset($_GET["srch_date_to"])
    ? addslashes(trim($_GET["srch_date_to"]))
    : "";

$srch_name = isset($_GET["srch_name"])
    ? addslashes(trim($_GET["srch_name"]))
    : "";

$srch_phone = isset($_GET["srch_phone"])
    ? addslashes(trim($_GET["srch_phone"]))
    : "";


$whr_str = "";


/* =========================================================
   NAME SEARCH
========================================================= */

if ($srch_name != "") {

    $aand = (trim($whr_str) != "") ? " and" : "";

    $whr_str .=
        "$aand $lift_table.name LIKE '%$srch_name%' ";

    $new_qry_string_filtered .=
        "&srch_name=" . urlencode($srch_name);

    $export_filtered_str .=
        "&srch_name=" . urlencode($srch_name);
}


/* =========================================================
   PHONE SEARCH
========================================================= */

if ($srch_phone != "") {

    $aand = (trim($whr_str) != "") ? " and" : "";

    $whr_str .=
        "$aand $lift_table.contact_no LIKE '%$srch_phone%' ";

    $new_qry_string_filtered .=
        "&srch_phone=" . urlencode($srch_phone);

    $export_filtered_str .=
        "&srch_phone=" . urlencode($srch_phone);
}


/* =========================================================
   DATE FROM SEARCH
========================================================= */

if ($srch_date_from != "") {

    $aand = (trim($whr_str) != "") ? " and" : "";

    $whr_str .=
        "$aand $lift_table.created_at >= '$srch_date_from 00:00:00' ";

    $new_qry_string_filtered .=
        "&srch_date_from=" . urlencode($srch_date_from);

    $export_filtered_str .=
        "&srch_date_from=" . urlencode($srch_date_from);
}


/* =========================================================
   DATE TO SEARCH
========================================================= */

if ($srch_date_to != "") {

    $aand = (trim($whr_str) != "") ? " and" : "";

    $whr_str .=
        "$aand $lift_table.created_at <= '$srch_date_to 23:59:59' ";

    $new_qry_string_filtered .=
        "&srch_date_to=" . urlencode($srch_date_to);

    $export_filtered_str .=
        "&srch_date_to=" . urlencode($srch_date_to);
}


/* =========================================================
   WHERE CLAUSE
========================================================= */

if ($whr_str != "") {

    $new_whr_str = "where " . $whr_str;

} else {

    $new_whr_str = "";
}


/* =========================================================
   PAGE SETTINGS
========================================================= */

$add_page_name = "dealer_registration_list.php";

$page_name = "dealer_registration_list.php";


/* =========================================================
   PAGINATION
========================================================= */

$adjacents = 4;

$targetpage = $page_name;

$limit = "100";

$page = isset($_GET['paged'])
    ? $_GET['paged']
    : 1;

$page = (int)$page;

if ($page < 1) {
    $page = 1;
}


$pgsql = "
    SELECT $lift_table.id
    FROM $lift_table
    $new_whr_str
    ORDER BY created_at DESC
";


$pgres = mysql_query($pgsql);

$total_pgres = mysql_num_rows($pgres);

$start_from = (($page - 1) * $limit);

$prev = $page - 1;

$next = $page + 1;

$lastpage = ceil($total_pgres / $limit);

$lpm1 = $lastpage - 1;


/* =========================================================
   HEADER
========================================================= */

include "web_header.php";

?>


<section class="content">

    <div class="container-fluid">

        <div class="block-header">
        </div>

        <div class="row clearfix">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                <div class="card">

                    <div class="header">

                        <h2>

                            Dealer Registration
                            (<?php echo $total_pgres; ?>)

                            &nbsp;&nbsp;

                            <a
                                href="export_dealer_registration.php?get_type=all<?php echo $export_filtered_str; ?>"
                                class="btn bg-red waves-effect">
                                Export All
                            </a>

                            &nbsp;
                            <a
    href="export_dealer_registration_data.php?get_type=all<?php echo $export_filtered_str; ?>"
    class="btn bg-red waves-effect">
    Export All Data
</a>
&nbsp;

                            <a
                                href="download_dealer_id_images_zip.php?get_type=all<?php echo $export_filtered_str; ?>"
                                class="btn bg-red waves-effect">
                                Download All Images (ZIP)
                            </a>

                        </h2>


                        <!-- =====================================================
                             SEARCH SECTION
                        ====================================================== -->

                        <div class="row clearfix">


                            <!-- NAME -->

                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">

                                <input
                                    type="text"
                                    class="form-control"
                                    id="srch_name"
                                    value="<?php echo htmlspecialchars($srch_name); ?>"
                                    placeholder="Name">

                            </div>


                            <!-- PHONE -->

                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">

                                <input
                                    type="text"
                                    class="form-control"
                                    id="srch_phone"
                                    value="<?php echo htmlspecialchars($srch_phone); ?>"
                                    placeholder="Phone No."
                                    maxlength="10">

                            </div>


                            <!-- DATE FROM -->

                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">

                                <input
                                    type="date"
                                    class="form-control"
                                    id="srch_date_from"
                                    value="<?php echo $srch_date_from; ?>"
                                    placeholder="Date From">

                            </div>


                            <!-- DATE TO -->

                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">

                                <input
                                    type="date"
                                    class="form-control"
                                    id="srch_date_to"
                                    value="<?php echo $srch_date_to; ?>"
                                    placeholder="Date To">

                            </div>


                            <!-- SEARCH BUTTON -->

                            <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">

                                <button
                                    type="button"
                                    class="btn bg-red waves-effect srch_btn">
                                    Search
                                </button>

                            </div>


                            <!-- RESET BUTTON -->

                            <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">

                                <button
                                    type="button"
                                    class="btn bg-red waves-effect srch_reset_btn">
                                    Reset
                                </button>

                            </div>

                        </div>


                        <span style="clear:both;display:block;"></span>

                    </div>


                    <div class="body">


                        <?php

                        echo olcPaging(
                            $adjacents,
                            $targetpage,
                            $limit,
                            $page,
                            $prev,
                            $next,
                            $lastpage,
                            $lpm1,
                            "paged",
                            $new_qry_string_filtered
                        );

                        ?>


                        <span style="display:block; clear:both;"></span>


                        <div class="table-responsive">

                            <table class="table table-bordered table-striped table-hover">


                                <thead>

                                    <tr>

                                        <th>ID</th>

                                        <th>Name</th>

                                        <th>Firm Name</th>

                                        <th>Relation</th>

                                        <th>Contact No</th>

                                        <th>District</th>

                                        <th>Area</th>

                                        <th>Kurta Size</th>

                                        <th>Selfie Image</th>

                                        <th>ID Proof</th>

                                        <th>ID Proof Front Image</th>

                                        <th>ID Proof Back Image</th>

                                        <th>Submitted On</th>
<th>Action</th>
                                    </tr>

                                </thead>


                                <tfoot>

                                    <tr>

                                        <th>ID</th>

                                        <th>Name</th>

                                        <th>Firm Name</th>

                                        <th>Relation</th>

                                        <th>Contact No</th>

                                        <th>District</th>

                                        <th>Area</th>

                                        <th>Kurta Size</th>

                                        <th>Selfie Image</th>

                                        <th>ID Proof</th>

                                        <th>ID Proof Front Image</th>

                                        <th>ID Proof Back Image</th>

                                        <th>Submitted On</th>
                                        <th>Action</th>

                                    </tr>

                                </tfoot>


                                <tbody>

                                <?php


                                $sql1 = "
                                    SELECT $lift_table.*
                                    FROM $lift_table
                                    $new_whr_str
                                    ORDER BY id DESC
                                    LIMIT $start_from,$limit
                                ";


                                $res1 = mysql_query($sql1);

                                $totres1 = mysql_num_rows($res1);


                                if ($totres1 > 0) {


                                    while ($row1 = mysql_fetch_assoc($res1)) {


                                        $id = $row1["id"];

                                        $name = $row1["name"];

                                        $firm_name = $row1["firm_name"];

                                        $relation = $row1["relation"];

                                        $area = $row1["area"];

                                        $district = $row1["district"];

                                        $size = $row1["size"];

                                        $contact_no = $row1["contact_no"];

                                        $selfie = $row1["selfie_photo"];

                                        $id_proof = $row1["id_proof"];

                                        $id_photo_front = $row1["id_photo_front"];

                                        $id_photo_back = $row1["id_photo_back"];


                                        $created_at =
                                            $row1["created_at"]
                                                ? date(
                                                    'd/m/Y H:i:s',
                                                    strtotime($row1["created_at"])
                                                )
                                                : "";


                                ?>


                                        <tr>


                                            <td>
                                                <?php echo $id; ?>
                                            </td>


                                            <td>
                                                <?php echo htmlspecialchars($name); ?>
                                            </td>


                                            <td>
                                                <?php echo htmlspecialchars($firm_name); ?>
                                            </td>


                                            <td>
                                                <?php echo htmlspecialchars($relation); ?>
                                            </td>


                                            <td>
                                                <?php echo htmlspecialchars($contact_no); ?>
                                            </td>


                                            <td>
                                                <?php echo htmlspecialchars($district); ?>
                                            </td>


                                            <td>
                                                <?php echo htmlspecialchars($area); ?>
                                            </td>


                                            <td>
                                                <?php echo htmlspecialchars($size); ?>
                                            </td>


                                            <!-- SELFIE -->

                                            <td>

                                                <?php

                                                if ($selfie != "") {

                                                    $selfie_url =
                                                        "https://" .
                                                        $_SERVER['HTTP_HOST'] .
                                                        "/SAP/dealer_registration/uploads/dealer_register/" .
                                                        $selfie;

                                                ?>

                                                    <small
                                                        style="display:block;word-break:break-all;color:#999;">
                                                        <?php echo $selfie_url; ?>
                                                    </small>

                                                    <a
                                                        href="<?php echo $selfie_url; ?>"
                                                        target="_blank">

                                                        <img
                                                            src="<?php echo $selfie_url; ?>"
                                                            width="50"
                                                            style="border-radius:4px;">

                                                    </a>

                                                <?php

                                                } else {

                                                    echo "-";

                                                }

                                                ?>

                                            </td>


                                            <!-- ID PROOF -->

                                            <td>
                                                <?php echo htmlspecialchars($id_proof); ?>
                                            </td>


                                            <!-- ID FRONT -->

                                            <td>

                                                <?php

                                                if ($id_photo_front != "") {

                                                    $id_photo_front_url =
                                                        "https://" .
                                                        $_SERVER['HTTP_HOST'] .
                                                        "/SAP/dealer_registration/uploads/dealer_register/" .
                                                        $id_photo_front;

                                                ?>

                                                    <small
                                                        style="display:block;word-break:break-all;color:#999;">
                                                        <?php echo $id_photo_front_url; ?>
                                                    </small>

                                                    <a
                                                        href="<?php echo $id_photo_front_url; ?>"
                                                        target="_blank">

                                                        <img
                                                            src="<?php echo $id_photo_front_url; ?>"
                                                            width="50"
                                                            style="border-radius:4px;">

                                                    </a>

                                                <?php

                                                } else {

                                                    echo "-";

                                                }

                                                ?>

                                            </td>


                                            <!-- ID BACK -->

                                            <td>

                                                <?php

                                                if ($id_photo_back != "") {

                                                    $id_photo_back_url =
                                                        "https://" .
                                                        $_SERVER['HTTP_HOST'] .
                                                        "/SAP/dealer_registration/uploads/dealer_register/" .
                                                        $id_photo_back;

                                                ?>

                                                    <small
                                                        style="display:block;word-break:break-all;color:#999;">
                                                        <?php echo $id_photo_back_url; ?>
                                                    </small>

                                                    <a
                                                        href="<?php echo $id_photo_back_url; ?>"
                                                        target="_blank">

                                                        <img
                                                            src="<?php echo $id_photo_back_url; ?>"
                                                            width="50"
                                                            style="border-radius:4px;">

                                                    </a>

                                                <?php

                                                } else {

                                                    echo "-";

                                                }

                                                ?>

                                            </td>


                                            <!-- CREATED -->

                                            <td>
                                                <?php echo $created_at; ?>
                                            </td>
<td style="text-align:center;">

    <button
        type="button"
        class="btn bg-red waves-effect"
        onclick="deleteDealer(
            <?php echo (int)$id; ?>,
            '<?php echo htmlspecialchars(addslashes($name), ENT_QUOTES); ?>',
            '<?php echo htmlspecialchars(addslashes($contact_no), ENT_QUOTES); ?>'
        )">
        <i class="material-icons">delete</i>
        Delete
    </button>

</td>

                                        </tr>


                                <?php

                                    }


                                } else {

                                ?>


                                    <tr>

                                        <td
                                            style="text-align:center"
                                            colspan="13">

                                            No data found.

                                        </td>

                                    </tr>


                                <?php

                                }

                                ?>


                                </tbody>

                            </table>

                        </div>


                        <?php

                        echo olcPaging(
                            $adjacents,
                            $targetpage,
                            $limit,
                            $page,
                            $prev,
                            $next,
                            $lastpage,
                            $lpm1,
                            "paged",
                            $new_qry_string_filtered
                        );

                        ?>


                        <span style="display:block; clear:both;"></span>


                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<script type="text/javascript">
function deleteDealer(id, name, phone) {

    var message =
        "Are you sure you want to delete this dealer?\n\n" +
        "Dealer Name: " + name + "\n" +
        "Phone Number: " + phone;

    if (confirm(message)) {

        window.location =
            "delete_dealer_registration.php?id=" +
            encodeURIComponent(id);
    }

}
jQuery(function () {
 

    /* =====================================================
       SEARCH BUTTON
    ====================================================== */

    jQuery(".srch_btn").click(function () {


        var srch_name =
            jQuery("#srch_name").val();

        var srch_phone =
            jQuery("#srch_phone").val();

        var srch_date_from =
            jQuery("#srch_date_from").val();

        var srch_date_to =
            jQuery("#srch_date_to").val();


        var qstring = "";
       


        function add_param(key, val) {

            if (val != "") {

                qstring +=
                    (qstring != "" ? "&" : "") +
                    key +
                    "=" +
                    encodeURIComponent(val);

            }

        }


        /* NAME */

        add_param(
            "srch_name",
            srch_name
        );


        /* PHONE */

        add_param(
            "srch_phone",
            srch_phone
        );


        /* DATE FROM */

        add_param(
            "srch_date_from",
            srch_date_from
        );


        /* DATE TO */

        add_param(
            "srch_date_to",
            srch_date_to
        );


        /* =================================================
           VALIDATE SEARCH
        ================================================= */

        if (qstring != "") {

            qstring =
                "?" + qstring;

            window.location =
                "dealer_registration_list.php" +
                qstring;

        } else {

            alert(
                "Please enter at least one field to search."
            );

        }

    });


    /* =====================================================
       RESET BUTTON
    ====================================================== */

    jQuery(".srch_reset_btn").click(function () {

        window.location =
            "dealer_registration_list.php";

    });


    /* =====================================================
       PHONE NUMBER - ONLY NUMBERS
    ====================================================== */

    jQuery("#srch_phone").on("input", function () {

        this.value =
            this.value.replace(
                /[^0-9]/g,
                ""
            );

    });


});

</script>


<?php

include "web_footer.php";

mysql_close();

?>
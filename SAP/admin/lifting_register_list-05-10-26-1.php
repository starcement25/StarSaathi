<?php

include "web_check.php";
include "star_connection.php";
$lift_table = "lifting_register_qr_code";

$new_qry_string_filtered = "";
$export_filtered_str = "";

$srch_name = $_GET["srch_name"] ? addslashes(trim($_GET["srch_name"])) : "";
$srch_phone = $_GET["srch_phone"] ? addslashes(trim($_GET["srch_phone"])) : "";
$srch_date_from = $_GET["srch_date_from"] ? addslashes(trim($_GET["srch_date_from"])) : "";
$srch_date_to = $_GET["srch_date_to"] ? addslashes(trim($_GET["srch_date_to"])) : "";

// $whr_str = "";
$whr_str = "
    $lift_table.state IS NOT NULL AND TRIM($lift_table.state) != ''
    AND $lift_table.district IS NOT NULL AND TRIM($lift_table.district) != ''
    AND $lift_table.address IS NOT NULL AND TRIM($lift_table.address) != ''
    AND $lift_table.cons_stage IS NOT NULL AND TRIM($lift_table.cons_stage) != ''
    AND $lift_table.name IS NOT NULL AND TRIM($lift_table.name) != ''
    AND $lift_table.phone IS NOT NULL AND TRIM($lift_table.phone) != ''
    AND $lift_table.question IS NOT NULL AND TRIM($lift_table.question) != ''
    AND $lift_table.image IS NOT NULL AND TRIM($lift_table.image) != ''
";

if ($srch_name != "") {
    $aand = (trim($whr_str) != "") ? " and" : "";
    $whr_str .= "$aand ($lift_table.name like '%$srch_name%') ";
    $new_qry_string_filtered .= "&srch_name=" . urlencode($srch_name);
    $export_filtered_str .= "&srch_name=" . urlencode($srch_name);
}

if ($srch_phone != "") {
    $aand = (trim($whr_str) != "") ? " and" : "";
    $whr_str .= "$aand ($lift_table.phone like '%$srch_phone%') ";
    $new_qry_string_filtered .= "&srch_phone=" . urlencode($srch_phone);
    $export_filtered_str .= "&srch_phone=" . urlencode($srch_phone);
}

if ($srch_date_from != "") {
    $aand = (trim($whr_str) != "") ? " and" : "";
    $whr_str .= "$aand ($lift_table.created_at >= '$srch_date_from') ";
    $new_qry_string_filtered .= "&srch_date_from=" . urlencode($srch_date_from);
    $export_filtered_str .= "&srch_date_from=" . urlencode($srch_date_from);
}

if ($srch_date_to != "") {
    $aand = (trim($whr_str) != "") ? " and" : "";
    $whr_str .= "$aand ($lift_table.created_at <= '$srch_date_to') ";
    $new_qry_string_filtered .= "&srch_date_to=" . urlencode($srch_date_to);
    $export_filtered_str .= "&srch_date_to=" . urlencode($srch_date_to);
}

if ($whr_str != "") {
    $new_whr_str = "where " . $whr_str;
} else {
    $new_whr_str = "";
}

$add_page_name = "lifting_register_list.php";
$page_name = "lifting_register_list.php";

/*---------PAGINATION RELATED CODE START----------*/
$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

$pgsql = "select $lift_table.id from $lift_table $new_whr_str ORDER BY created_at DESC";
$pgres = mysql_query($pgsql);
$total_pgres = mysql_num_rows($pgres);
$start_from = (($page - 1) * $limit);
$prev = $page - 1;
$next = $page + 1;
$lastpage = ceil($total_pgres / $limit);
$lpm1 = $lastpage - 1;
/*---------PAGINATION RELATED CODE END----------*/

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
                        <h2>Griha Pravesh Consumer Scheme (<?php echo $total_pgres; ?>)&nbsp;&nbsp;
                            <a href="export_lifting_register.php?get_type=all<?php echo $export_filtered_str; ?>"
                                class="btn bg-red waves-effe">Export All</a> &nbsp;
                              <a href="https://<?php echo $_SERVER['HTTP_HOST'];?>/SAP/customer/uploads/lifting_register_qr.png" download class="btn bg-red waves-effe">Download QR Code</a> &nbsp;
                                <a href="download_lifting_images_zip.php?get_type=all<?php echo $export_filtered_str; ?>"
        class="btn bg-red waves-effe">Download All Images (ZIP)</a> &nbsp;
                        </h2>
                        <div class="row clearfix">
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <input type="text" class="form-control" id="srch_name" value="<?php echo $srch_name; ?>"
                                    placeholder="Search Name">
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <input type="text" class="form-control" id="srch_phone"
                                    value="<?php echo $srch_phone; ?>" placeholder="Search Phone">
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <input type="date" class="form-control" id="srch_date_from"
                                    value="<?php echo $srch_date_from; ?>" placeholder="Order Date From">
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <input type="date" class="form-control" id="srch_date_to"
                                    value="<?php echo $srch_date_to; ?>" placeholder="Order Date To">
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <button type="button" class="btn bg-red waves-effect srch_btn">Search</button>
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <button type="button" class="btn bg-red waves-effect srch_reset_btn">Reset</button>
                            </div>
                        </div>
                        <span style="clear:both;display:block;"></span>
                    </div>
                    <div class="body">
                        <?php
                        echo olcPaging($adjacents, $targetpage, $limit, $page, $prev, $next, $lastpage, $lpm1, "paged", $new_qry_string_filtered);
                        ?>
                        <span style="display:block; clear:both;"></span>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>State</th>
                                        <th>District</th>
                                        <th>Address</th>
                                         <th>Construction Stage</th>
                                        <!-- <th>Order&nbsp;Date</th>
                                        <th>Order&nbsp;Qty</th> -->
                                        <th>Image</th>
                                        <th>What’s your Ghar Ka Vaada?</th>
                                        <th>Submitted&nbsp;On</th>
                                    </tr>
                                </thead>
                                 <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>State</th>
                                        <th>District</th>
                                        <th>Address</th>
                                         <th>Construction Stage</th>
                                        <!-- <th>Order&nbsp;Date</th>
                                        <th>Order&nbsp;Qty</th> -->
                                        <th>Image</th>
                                         <th>What’s your Ghar Ka Vaada?</th>
                                        <th>Submitted&nbsp;On</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php
                                    $sql1 = "select $lift_table.* FROM $lift_table $new_whr_str ORDER BY id DESC limit $start_from,$limit";
                                    $res1 = mysql_query($sql1);
                                    $totres1 = mysql_num_rows($res1);
                                    if ($totres1 > 0) {
                                        while ($row1 = mysql_fetch_assoc($res1)) {
                                            $id = $row1["id"];
                                            $name = $row1["name"];
                                            $phone = $row1["phone"];
                                            $state = $row1["state"];
                                            $district = $row1["district"];
                                            $cons_stage = $row1["cons_stage"];
                                            $question = $row1["question"];
                                            $address = $row1["address"];
                                            $order_date = $row1["order_date"] ? date('d/m/Y', strtotime($row1["order_date"])) : "";
                                            $order_qty = $row1["order_qty"];
                                            $image = $row1["image"];
                                            $created_at = $row1["created_at"] ? date('d/m/Y H:i:s', strtotime($row1["created_at"])) : "";
                                            ?>
                                            <tr>
                                                <td><?php echo $id; ?></td>
                                                <td><?php echo $name; ?></td>
                                                <td><?php echo $phone; ?></td>
                                                <td><?php echo $address; ?></td>
                                                <td><?php echo $state; ?></td>
                                                <td><?php echo $district; ?></td>
                                                <td><?php echo $cons_stage; ?></td>
                                                <!-- <td><?php echo $order_date; ?></td>
                                                <td><?php echo $order_qty; ?></td> -->
                                                <td>
                                                    <?php if ($image != "") {
                                                        // absolute URL - the uploads folder lives under /SAP/customer/, not
                                                        // wherever this admin page happens to be, so a relative path won't resolve here
                                                        $img_full_url = "https://" . $_SERVER['HTTP_HOST'] . "/SAP/customer/uploads/lift_register/" . $image;
                                                        ?>
                                                        <small
                                                            style="display:block;word-break:break-all;color:#999;"><?php echo $img_full_url; ?></small>
                                                        <a href="<?php echo $img_full_url; ?>" target="_blank">
                                                            <img src="<?php echo $img_full_url; ?>" width="50"
                                                                style="border-radius:4px;">
                                                        </a>
                                                    <?php } else {
                                                        echo "-";
                                                    } ?>
                                                </td>
                                                 <td><?php echo $question; ?></td>
                                                <td><?php echo $created_at; ?></td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td style="text-align:center" colspan="10">No data found.</td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                        echo olcPaging($adjacents, $targetpage, $limit, $page, $prev, $next, $lastpage, $lpm1, "paged", $new_qry_string_filtered);
                        ?>
                        <span style="display:block; clear:both;"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    jQuery(function () {
        jQuery(".srch_btn").click(function () {
            var srch_name = jQuery("#srch_name").val();
            var srch_phone = jQuery("#srch_phone").val();
            var srch_date_from = jQuery("#srch_date_from").val();
            var srch_date_to = jQuery("#srch_date_to").val();
            var qstring = "";

            function add_param(key, val) {
                if (val != "") {
                    qstring += (qstring != "" ? "&" : "") + key + "=" + encodeURIComponent(val);
                }
            }

            add_param("srch_name", srch_name);
            add_param("srch_phone", srch_phone);
            add_param("srch_date_from", srch_date_from);
            add_param("srch_date_to", srch_date_to);

            if (qstring != "") {
                qstring = "?" + qstring;
                window.location = "lifting_register_list.php" + qstring;
            } else {
                alert("Please enter atleast one field to search.");
            }
        });

        jQuery(".srch_reset_btn").click(function () {
            window.location = "lifting_register_list.php";
        });
    });
</script>
<?php
include "web_footer.php";
mysql_close();
?>
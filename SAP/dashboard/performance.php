<?php
include "web_check.php";
include "star_connection.php";

$t_apperpdo               = "T_APPERPDO";
$employee_master          = "employee_master";
$customer_master          = "customer_master";
$branch_master            = "branch_master";
$broker_master            = "broker_master";
$customer_broker_relation = "customer_broker_relation";
$ledger_balance           = "ledger_balance";
$branch_schemes_PDF       = "branch_schemes_PDF";
$notification_message     = "notification_message";
$month_wise_achievement   = "month_wise_achievement_new";   // ← NEW TABLE
$page_name                = "performance.php";

/* ------------------------------------------------------------------ */
/*  DATE / FINANCIAL YEAR SETUP                                        */
/* ------------------------------------------------------------------ */
$current_date  = date('Y-m-d');
$current_month = (int) date('n');   // 1–12, no leading zero
$current_year  = (int) date('Y');

// Financial year start (April = month 4)
$fy = ($current_month >= 4) ? $current_year : $current_year - 1;

$current_fin_year = $fy       . '-' . ($fy + 1);   // e.g. 2026-2027
$prev_fin_year    = ($fy - 1) . '-' . $fy;          // e.g. 2025-2026

/* ------------------------------------------------------------------ */
/*  SESSION VARIABLES                                                  */
/* ------------------------------------------------------------------ */
$sswa_user_type             = $_SESSION["sswa_user_type"];
$sswa_the_broker_id         = $_SESSION["sswa_user_id"];
$sswa_the_dns_broker_id     = $_SESSION["sswa_user_dns_id"];
$sswa_selected_dealer_code  = $_SESSION["sswa_selected_dealer_code"];
 $sswa_selected_customer_code= $_SESSION["sswa_selected_customer_code"];

/* ------------------------------------------------------------------ */
/*  sel_sdvl (sub-dealer filter from GET)                              */
/* ------------------------------------------------------------------ */
$sel_sdvl = (isset($_GET["sel_sdvl"]) && trim($_GET["sel_sdvl"]) != "")
            ? trim($_GET["sel_sdvl"]) : "";

/* ------------------------------------------------------------------ */
/*  CUSTOMER ID (for track-log)                                        */
/* ------------------------------------------------------------------ */
$sql3    = "SELECT `customer_id` FROM $customer_master WHERE `customer_code`='$sswa_selected_customer_code'";
$res3    = mysql_query($sql3);
$row3    = mysql_fetch_assoc($res3);
$the_customer_id = trim($row3["customer_id"]);

if (strtoupper($sswa_user_type) == 'DEALER') {
    $curr_date_time  = date("Y-m-d H:i:s");
    $webservice_name = "PERFORMANCE";
    $sqlin_tl = "INSERT INTO `webservice_track_log`
                 (`customer_code`,`webservice_name`,`details`,`datetime`)
                 VALUES ('$the_customer_id','$webservice_name','','$curr_date_time')";
    mysql_query($sqlin_tl);
}

/* ------------------------------------------------------------------ */
/*  SUB-DEALER DROPDOWN                                                */
/* ------------------------------------------------------------------ */
if ($sswa_user_type == "SP") {
    // $brsql = "SELECT $customer_master.`dns_customer_code`,$customer_master.`customer_code`, $customer_master.`customer_name`
    //           FROM $customer_broker_relation
    //           LEFT JOIN $customer_master
    //             ON $customer_broker_relation.`customer_code` = $customer_master.`customer_code`
    //           WHERE $customer_broker_relation.`broker_code` = '$sswa_the_broker_id'
    //             AND $customer_broker_relation.`acedns` = 'Y'
    //           ORDER BY $customer_master.`customer_name` ASC";

     $brsql = "SELECT $customer_master.`dns_customer_code`,$customer_master.`customer_code`, $customer_master.`customer_name`
              FROM $customer_broker_relation
              LEFT JOIN $customer_master
                ON $customer_broker_relation.`customer_code` = $customer_master.`customer_code`
              WHERE $customer_broker_relation.`broker_code` = '$sswa_the_broker_id'
                AND $customer_broker_relation.`acedns` = 'Y'
              ORDER BY $customer_master.`customer_name` ASC";

} else {
     $brsql = "SELECT `dns_customer_code`, `customer_code`,`customer_name`
              FROM $customer_master
              WHERE `rds_tag` = '$sswa_selected_customer_code'
                AND `acedns` = 'Y'
                AND cust_type IN ('Sub Dealer','RSSD')
              ORDER BY `customer_name` ASC";
}
$brres       = mysql_query($brsql);
$total_brres = mysql_num_rows($brres);

/* ------------------------------------------------------------------ */
/*  DETERMINE WHICH customer_code TO QUERY                            */
/* ------------------------------------------------------------------ */
$query_customer_code = ($sel_sdvl != "") ? $sel_sdvl : $sswa_selected_customer_code;
$query_customer_code = strtoupper($query_customer_code);

/* ------------------------------------------------------------------ */
/*  FETCH ALL ROWS FROM month_wise_achievement_new                     */
/* ------------------------------------------------------------------ */
  $sql_mwa = "SELECT `month`, `year`, `target_qty`, `achievement_qty`
            FROM $month_wise_achievement
            WHERE `customer_code` = '$query_customer_code'";
$res_mwa = mysql_query($sql_mwa);

$raw_data = array();
while ($row_mwa = mysql_fetch_assoc($res_mwa)) {
    $m = (int) $row_mwa['month'];
    $y = (int) $row_mwa['year'];
    $raw_data[$m][$y] = array(
        'target' => $row_mwa['target_qty'],
        'ach'    => $row_mwa['achievement_qty']
    );
}

/* ------------------------------------------------------------------ */
/*  BUILD ARRAYS FOR 12 MONTHS (Apr→Mar order)                        */
/*  Financial-year month mapping:                                      */
/*    Jan–Mar  → current FY year = fy+1,  prev FY year = fy           */
/*    Apr–Dec  → current FY year = fy,    prev FY year = fy-1         */
/* ------------------------------------------------------------------ */
// Month order for a financial year: Apr(4)…Dec(12), Jan(1)…Mar(3)
$fy_month_order = array(4,5,6,7,8,9,10,11,12,1,2,3);
$month_labels   = array(
    1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr', 5=>'May', 6=>'Jun',
    7=>'Jul', 8=>'Aug', 9=>'Sep', 10=>'Oct', 11=>'Nov', 12=>'Dec'
);
$month_labels_full = array(
    1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',
    7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'
);

// Arrays for chart / table (12 slots, Apr→Mar)
$curr_yer_target_arr      = array();
$curr_yer_achievement_arr = array();
$prev_yer_target_arr      = array();
$prev_yer_achievement_arr = array();
$ordered_month_labels     = array();
$ordered_month_labels_full= array();

foreach ($fy_month_order as $m) {
    // Determine calendar year for this month in current/previous FY
    if ($m >= 1 && $m <= 3) {
        $cur_year_m = $fy + 1;
        $pre_year_m = $fy;
    } else {
        $cur_year_m = $fy;
        $pre_year_m = $fy - 1;
    }

    $cur_target = isset($raw_data[$m][$cur_year_m]) ? (float)$raw_data[$m][$cur_year_m]['target'] : 0;
    $cur_ach    = isset($raw_data[$m][$cur_year_m]) ? (float)$raw_data[$m][$cur_year_m]['ach']    : 0;
    $pre_target = isset($raw_data[$m][$pre_year_m]) ? (float)$raw_data[$m][$pre_year_m]['target'] : 0;
    $pre_ach    = isset($raw_data[$m][$pre_year_m]) ? (float)$raw_data[$m][$pre_year_m]['ach']    : 0;

    $curr_yer_target_arr[]      = number_format($cur_target, 2, '.', '');
    $curr_yer_achievement_arr[] = number_format($cur_ach,    2, '.', '');
    $prev_yer_target_arr[]      = number_format($pre_target, 2, '.', '');
    $prev_yer_achievement_arr[] = number_format($pre_ach,    2, '.', '');
    $ordered_month_labels[]     = $month_labels[$m];
    $ordered_month_labels_full[]= $month_labels_full[$m];
}

/* ------------------------------------------------------------------ */
/*  TOTALS                                                             */
/* ------------------------------------------------------------------ */
$curr_total_target      = number_format(array_sum($curr_yer_target_arr),      2, '.', '');
$curr_total_achievement = number_format(array_sum($curr_yer_achievement_arr), 2, '.', '');
$prev_total_target      = number_format(array_sum($prev_yer_target_arr),      2, '.', '');
$prev_total_achievement = number_format(array_sum($prev_yer_achievement_arr), 2, '.', '');

include "web_header.php";
?>
<style>
#container  { height: 400px; }
#container2 { height: 400px; }

.highcharts-figure, .highcharts-data-table table {
    min-width: 310px;
    max-width: 800px;
    margin: 1em auto;
}

/* Hidden data tables used by Highcharts */
#datatable1, #datatable2 {
    font-family: Verdana, sans-serif;
    border-collapse: collapse;
    border: 1px solid #EBEBEB;
    margin: 10px auto;
    text-align: center;
    width: 100%;
    max-width: 500px;
    display: none;
}
#datatable1 caption, #datatable2 caption { padding: 1em 0; font-size: 1.2em; color: #555; }
#datatable1 th, #datatable2 th { font-weight: 600; padding: 0.5em; }
#datatable1 td, #datatable1 th, #datatable1 caption,
#datatable2 td, #datatable2 th, #datatable2 caption { padding: 0.5em; }
#datatable1 thead tr, #datatable1 tr:nth-child(even),
#datatable2 thead tr, #datatable2 tr:nth-child(even) { background: #f8f8f8; }
#datatable1 tr:hover, #datatable2 tr:hover { background: #f1f7ff; }

.mn_lft  { text-align: left;  font-weight: bold; }
.ta_right{ text-align: right; font-weight: bold; }
#ta_table_view { display: none; }
</style>

<section class="content">
    <div class="container-fluid" style="position:relative;">

        <!-- Dropdown row -->
        <div class="row clearfix">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <select class="form-control" id="astn_dlsbdl_code" name="astn_dlsbdl_code" style="padding-left:2px;">
                    <?php if ($sswa_user_type != "SP"): ?>
                        <option value="">Total Performance</option>
                    <?php endif; ?>

                    <?php
                    if ($total_brres > 0) {
                        while ($brrow = mysql_fetch_assoc($brres)) {
                            $the_br_dns  = $brrow["customer_code"];
                            $the_br_name = $brrow["customer_name"];
                            $selected    = ($the_br_dns == $sel_sdvl || $the_br_dns == $sswa_selected_dealer_code)
                                           ? 'selected="selected"' : '';
                            echo "<option value=\"{$the_br_dns}\" {$selected}>{$the_br_name}</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"></div>
        </div>

        <!-- Graph / Table toggle -->
        <div style="text-align:right; padding-top:10px;">
            <input name="the_gt_view" id="the_graph_view" value="Graph View" checked="checked" type="radio">
            <label for="the_graph_view">Graph View</label>
            <input name="the_gt_view" id="the_table_view" value="Table View" type="radio">
            <label for="the_table_view">Table View</label>
        </div>

        <!-- ===== GRAPH VIEW ===== -->
        <div class="row clearfix" id="ta_graph_view">
            <!-- Current FY chart -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="card">
                    <figure class="highcharts-figure">
                        <div id="container"></div>
                        <table id="datatable1">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Target(MT)</th>
                                    <th>Achievement(MT)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < 12; $i++): ?>
                                <tr>
                                    <th><?php echo $ordered_month_labels[$i]; ?></th>
                                    <td><?php echo $curr_yer_target_arr[$i]; ?></td>
                                    <td><?php echo $curr_yer_achievement_arr[$i]; ?></td>
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </figure>
                </div>
            </div>

            <!-- Previous FY chart -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="card">
                    <figure class="highcharts-figure">
                        <div id="container2"></div>
                        <table id="datatable2">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Target(MT)</th>
                                    <th>Achievement(MT)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < 12; $i++): ?>
                                <tr>
                                    <th><?php echo $ordered_month_labels[$i]; ?></th>
                                    <td><?php echo $prev_yer_target_arr[$i]; ?></td>
                                    <td><?php echo $prev_yer_achievement_arr[$i]; ?></td>
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </figure>
                </div>
            </div>
        </div><!-- /ta_graph_view -->

        <!-- ===== TABLE VIEW ===== -->
        <div class="row clearfix" id="ta_table_view">
            <!-- Current FY table -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="3" style="text-align:center;">
                                        For Year <?php echo $current_fin_year; ?>
                                    </th>
                                </tr>
                                <tr>
                                    <th>Month</th>
                                    <th style="text-align:right;">Target(MT)</th>
                                    <th style="text-align:right;">Achv.(MT)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < 12; $i++): ?>
                                <tr>
                                    <td class="mn_lft"><?php echo $ordered_month_labels_full[$i]; ?></td>
                                    <td class="ta_right"><?php echo $curr_yer_target_arr[$i]; ?></td>
                                    <td class="ta_right"><?php echo $curr_yer_achievement_arr[$i]; ?></td>
                                </tr>
                                <?php endfor; ?>
                                <tr>
                                    <td class="mn_lft">Total</td>
                                    <td class="ta_right"><?php echo $curr_total_target; ?></td>
                                    <td class="ta_right"><?php echo $curr_total_achievement; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Previous FY table -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="3" style="text-align:center;">
                                        For Year <?php echo $prev_fin_year; ?>
                                    </th>
                                </tr>
                                <tr>
                                    <th>Month</th>
                                    <th style="text-align:right;">Target(MT)</th>
                                    <th style="text-align:right;">Achv.(MT)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < 12; $i++): ?>
                                <tr>
                                    <td class="mn_lft"><?php echo $ordered_month_labels_full[$i]; ?></td>
                                    <td class="ta_right"><?php echo $prev_yer_target_arr[$i]; ?></td>
                                    <td class="ta_right"><?php echo $prev_yer_achievement_arr[$i]; ?></td>
                                </tr>
                                <?php endfor; ?>
                                <tr>
                                    <td class="mn_lft">Total</td>
                                    <td class="ta_right"><?php echo $prev_total_target; ?></td>
                                    <td class="ta_right"><?php echo $prev_total_achievement; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><!-- /ta_table_view -->

    </div>
</section>

<!-- ===== HIGHCHARTS ===== -->
<script type="text/javascript">

Highcharts.chart('container', {
    data: { table: 'datatable1' },
    chart: { type: 'column' },
    title: { text: 'For Year <?php echo $current_fin_year; ?>' },
    yAxis: {
        allowDecimals: true,
        title: { text: 'Values' }
    },
    tooltip: {
        formatter: function () {
            return '<b>' + this.series.name + '</b><br/>' +
                   this.point.y + ' ' + this.point.name.toLowerCase();
        }
    }
});

Highcharts.chart('container2', {
    data: { table: 'datatable2' },
    chart: { type: 'column' },
    title: { text: 'For Year <?php echo $prev_fin_year; ?>' },
    yAxis: {
        allowDecimals: true,
        title: { text: 'Values' }
    },
    tooltip: {
        formatter: function () {
            return '<b>' + this.series.name + '</b><br/>' +
                   this.point.y + ' ' + this.point.name.toLowerCase();
        }
    }
});

</script>

<!-- ===== JQUERY ===== -->
<script type="text/javascript">
jQuery(document).ready(function () {

    /* Chosen dropdown – navigate on change */
    jQuery('#astn_dlsbdl_code').chosen({
        width: "100%",
        no_results_text: 'Oops, no sub dealer found!',
        search_contains: true
    }).change(function () {
        var sel_sdvl = jQuery(this).val();
        if (sel_sdvl !== "") {
            window.location = "<?php echo $page_name; ?>?sel_sdvl=" + sel_sdvl;
        } else {
            window.location = "<?php echo $page_name; ?>";
        }
    });

    /* Graph / Table toggle */
    jQuery('input[name="the_gt_view"]').change(function () {
        var sl_gt_view = jQuery(this).val();
        if (sl_gt_view === "Graph View") {
            jQuery("#ta_graph_view").show();
            jQuery("#ta_table_view").hide();
        } else if (sl_gt_view === "Table View") {
            jQuery("#ta_table_view").show();
            jQuery("#ta_graph_view").hide();
        } else {
            jQuery("#ta_graph_view").show();
            jQuery("#ta_table_view").hide();
        }
    });

});
</script>

<?php
include "web_footer.php";
mysql_close();
?>

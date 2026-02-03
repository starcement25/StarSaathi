<?php
include "web_check.php";
include "star_connection.php";

$branch_master = "branch_master";
$customer_master = "customer_master";
$dealer_tour_status = "dealer_tour_status";
$dealer_reward_status = "dealer_reward_status";

$pg_sts_arr = array();
$pg_sts_arr[] = array("key_val"=>"ACTIVE","title_val"=>"ACTIVE");
$pg_sts_arr[] = array("key_val"=>"INACTIVE","title_val"=>"INACTIVE");

$new_qry_string_filtered = "";
$srch_dlr_dtls = isset($_GET["srch_dlr_dtls"]) ? addslashes(trim($_GET["srch_dlr_dtls"])) : "";
$sl_status = isset($_GET["sl_status"]) ? addslashes(trim($_GET["sl_status"])) : "";
$sl_cust_type = isset($_GET["sl_cust_type"]) ? addslashes(trim($_GET["sl_cust_type"])) : ""; // Ensure isset check
$whr_str = "";
$search_array = array("srch_dlr_dtls" => $srch_dlr_dtls, "sl_status" => $sl_status, "sl_cust_type" => $sl_cust_type);

foreach ($search_array as $search_array_key => $search_array_val) {
    if ($search_array_key == "srch_dlr_dtls" && $search_array_val != '') {
        $aand = $whr_str != "" ? " and" : "";
        $whr_str .= "$aand ($customer_master.`customer_id` LIKE '%$search_array_val%' OR $customer_master.`customer_name` LIKE '%$search_array_val%')";
        $new_qry_string_filtered .= "&srch_dlr_dtls=".$search_array_val;
    }

    if ($search_array_key == "sl_status" && $search_array_val != '') {
        $aand = $whr_str != "" ? " and" : "";
        $whr_str .= "$aand $dealer_reward_status.`status` = '$search_array_val'";
        $new_qry_string_filtered .= "&sl_status=".$search_array_val;
    }

    if ($search_array_key == "sl_cust_type" && $search_array_val != '') {
        if ($search_array_val == 'Dealer') {
            $aand = $whr_str != "" ? " and" : "";
            $whr_str .= "$aand $customer_master.`cust_type` = 'Dealer'";
        } else if ($search_array_val == 'Sub Dealer') {
            $aand = $whr_str != "" ? " and" : "";
            $whr_str .= "$aand $customer_master.`cust_type` = 'Sub Dealer'";
        }
        $new_qry_string_filtered .= "&sl_cust_type=".$search_array_val;
    }
}

if($search_array_key=="sl_status"){
    if($search_array_val!=''){
        //if(trim($whr_str)!=""){
            $aand = " and";
        /*}else{
            $aand = "";
        }*/
        if($search_array_val=='ACTIVE')
        {
            $whr_str .= " $aand $dealer_reward_status.`dealer_status`='$search_array_val'";
        }
        if($search_array_val=='INACTIVE')
        {
            $whr_str .= " $aand ($dealer_reward_status.`dealer_status`='$search_array_val' OR $customer_master.`customer_id` NOT IN(SELECT emp_code FROM $dealer_reward_status ))";
        }
        $new_qry_string_filtered .= "&sl_status=".$search_array_val;

    }
}

// if($whr_str!=""){
// 	$new_whr_str = $whr_str;
// }else{
// 	$new_whr_str ="";
// }
$new_whr_str = $whr_str != "" ? "AND $whr_str" : "";
$add_page_name = "star-saathi-rewards_test2.php";
$page_name = "star-saathi-rewards_test2.php";
$cnt = 0;
$countrow = 1;

$adjacents = 4;
$targetpage = $page_name;
$limit = 100;
$page = isset($_GET['paged']) ? $_GET['paged'] : 1;
$start_from = (($page - 1) * $limit);
$prev = $page - 1;
$next = $page + 1;

$cust_type = isset($_GET['sl_cust_type']) ? addslashes(trim($_GET['sl_cust_type'])) : 'Dealer'; // Default to 'Dealer'

if ($cust_type == 'Dealer') {
    $pgsql = "SELECT $customer_master.`customer_code` 
              FROM $customer_master 
              LEFT JOIN $dealer_reward_status 
              ON $customer_master.`customer_id` = $dealer_reward_status.`emp_code` 
              WHERE $customer_master.`cust_type`='Dealer' $new_whr_str";
} else {
    $pgsql = "SELECT $customer_master.`customer_code` 
              FROM $customer_master 
              LEFT JOIN $dealer_reward_status 
              ON $customer_master.`customer_id` = $dealer_reward_status.`emp_code` 
              WHERE $customer_master.`cust_type`='Sub Dealer' $new_whr_str";
}


$pgres = mysql_query($pgsql);
$total_pgres = mysql_num_rows($pgres);
$lastpage = ceil($total_pgres / $limit);
$lpm1 = $lastpage - 1;

include "web_header.php";
?>

<style>
.dlr_prfl_img {
    width: 150px;
}
.bwps_sel {
    width: 150px;
}
.os_ldr {
    position: absolute;
    right: 5px;
    top: 5px;
}

.activate-deactivate-buttons {
        display: none;
}
</style>

<section class="content">
    <div class="container-fluid">
        <div class="block-header"></div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>Star Saathi Rewards Status</h2>
                        <div class="row clearfix">
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <select class="form-control" id="sl_cust_type">
                                    <option value="">Choose List</option>
                                    <option value="Dealer" <?php if($sl_cust_type == "Dealer") { ?> selected="selected" <?php } ?>>Dealer</option>
                                    <option value="Sub Dealer" <?php if($sl_cust_type == "Sub Dealer") { ?> selected="selected" <?php } ?>>Sub Dealer</option>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <input type="text" class="form-control" id="srch_dlr_dtls" value="<?php echo $srch_dlr_dtls; ?>" placeholder="Search Dealer Details">
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <select class="form-control" id="sl_status_filter">
                                    <option value="">Status</option>
                                    <option value="ACTIVE" <?php if ($sl_status == "ACTIVE") echo 'selected="selected"'; ?>>ACTIVE</option>
                                    <option value="INACTIVE" <?php if ($sl_status == "INACTIVE") echo 'selected="selected"'; ?>>INACTIVE</option>
                                </select>
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <button type="button" class="btn bg-red waves-effect srch_btn">Search</button>
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <button type="button" class="btn bg-red waves-effect srch_reset_btn">Reset</button>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <button type="button" class="btn bg-red waves-effect srch_btn">Active All</button>
                            </div>

                            <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <button type="button" class="btn bg-red waves-effect srch_btn">Inactive All</button>

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
                                        <th>Dealer&nbsp;Code</th>
                                        <th>Dealer&nbsp;Name</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Dealer&nbsp;Code</th>
                                        <th>Dealer&nbsp;Name</th>
                                        <th>Status</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php
                                        $sql1 = "SELECT $customer_master.`customer_code`, $customer_master.`customer_id`, $customer_master.`customer_name`, $dealer_reward_status.`dealer_status` 
                                                FROM $customer_master 
                                                LEFT JOIN $dealer_reward_status 
                                                ON $customer_master.`customer_id` = $dealer_reward_status.`emp_code` 
                                                WHERE $customer_master.`cust_type`='$cust_type' $new_whr_str 
                                                ORDER BY $customer_master.`customer_name` ASC 
                                                LIMIT $start_from, $limit";

                                        $res1 = mysql_query($sql1);
                                        $totres1 = mysql_num_rows($res1);

                                        if ($totres1 > 0) {
                                            while ($row1 = mysql_fetch_assoc($res1)) {
                                                $customer_code = $row1["customer_code"];
                                                $customer_id = $row1["customer_id"];
                                                $customer_name = $row1["customer_name"];
                                                $dealer_status = $row1["dealer_status"] ?: 'ACTIVE';
                                    ?>
                                    <tr>
                                        <td><?php echo $customer_id; ?></td>
                                        <td><?php echo $customer_name; ?></td>
                                        <td style="position:relative;">
                                            <select class="form-control cwts_sel" id="cwts_sel_<?php echo $customer_id; ?>" the_customer_code="<?php echo $customer_id; ?>">
                                                <?php
                                                    foreach ($pg_sts_arr as $pg_sts_arr_val) {
                                                        $the_key_val = $pg_sts_arr_val["key_val"];
                                                        $the_title_val = $pg_sts_arr_val["title_val"];
                                                ?>
                                                <option value="<?php echo $the_key_val; ?>" <?php if ($the_key_val == $dealer_status) echo 'selected'; ?>><?php echo $the_title_val; ?></option>
                                                <?php } ?>
                                            </select>
                                            <span class="os_ldr" id="ca_ldr_<?php echo $customer_id; ?>"></span>
                                        </td>
                                    </tr>
                                    <?php
                                            }
                                        } else {
                                    ?>
                                    <tr>
                                        <td style="text-align:center" colspan="3">No data found.</td>
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
jQuery(function(){
    var imgs = '<img src="images/ajax-loader.gif"/>';
    var done_img = '<img src="images/success_tick.png"/>';

    jQuery(".cwts_sel").change(function(){
        var ancr_elmnt = jQuery(this);
        var the_status = ancr_elmnt.val();
        var the_customer_code = ancr_elmnt.attr("the_customer_code");
        if(the_customer_code!=""){
            var for_loader = jQuery("#ca_ldr_"+the_customer_code);
            for_loader.html(imgs);
            jQuery.ajax({
                url: '../dealer-active-inactive-menu.php',    
                type: 'post',
                dataType: "JSON",
                data: {the_customer_code: the_customer_code, the_status: the_status},
                success: function(response){
                    if(response.process_status == "YES"){
                        for_loader.html(done_img);
                        setTimeout(function (){
                            for_loader.html("");
                        }, 3000);
                    } else {
                        for_loader.html("");
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log("AJAX Error:", textStatus, errorThrown);
                }    
            });
        }
    });

    jQuery(".srch_btn").click(function(){
        var srch_dlr_dtls = jQuery("#srch_dlr_dtls").val();
        var sl_status = jQuery("#sl_status_filter").val();    
        var sl_cust_type = jQuery("#sl_cust_type").val();
        var qstring = "";

        if(srch_dlr_dtls != "" || sl_status != "" || sl_cust_type != ""){
            if(srch_dlr_dtls != ""){
                qstring += "srch_dlr_dtls=" + encodeURIComponent(srch_dlr_dtls);
            }
            if(sl_status != ""){
                qstring += (qstring != "" ? "&" : "") + "sl_status=" + encodeURIComponent(sl_status);
            }
            if(sl_cust_type != ""){
                qstring += (qstring != "" ? "&" : "") + "sl_cust_type=" + encodeURIComponent(sl_cust_type);
            }

            if(qstring != ""){
                qstring = "?" + qstring;
            }
            window.location = "<?php echo $page_name; ?>" + qstring;
        } else {
            alert("Please select at least one field to search.");
        }
    });

    jQuery(".srch_reset_btn").click(function(){
        window.location = "<?php echo $page_name; ?>";
    });

    // Function to activate/deactivate all dealers
    jQuery("#activate_all").click(function(){
        changeAllStatus("ACTIVE");
    });

    jQuery("#deactivate_all").click(function(){
        var cust_type = jQuery("#sl_cust_type").val();
        var alert_message = (cust_type == "Subdealer") ? 
            "Are you sure you want to Inactive all Sub dealers?" : 
            "Are you sure you want to Inactive all Dealer?";
        
        if(confirm(alert_message)){
            changeAllStatus("INACTIVE");
        }
    });

    function changeAllStatus(status){
        var cust_type = jQuery("#sl_cust_type").val();
        jQuery(".os_ldr").html(imgs); // Show loader for all
        jQuery.ajax({
            url: '../dealer-active-inactive-menu.php',
            type: 'post',
            dataType: "JSON",
            data: {cust_type: cust_type, all_status: status},
            success: function(response){
                if(response.process_status == "YES"){
                    jQuery(".cwts_sel").val(status);
                    jQuery(".os_ldr").html(done_img); // Show done image for all
                    setTimeout(function (){
                        jQuery(".os_ldr").html(""); // Remove loader after a delay
                    }, 3000);
                } else {
                    jQuery(".os_ldr").html("");
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("AJAX Error:", textStatus, errorThrown);
            }
        });
    }
});

</script>

<?php
include "web_footer.php";
mysql_close();
?>

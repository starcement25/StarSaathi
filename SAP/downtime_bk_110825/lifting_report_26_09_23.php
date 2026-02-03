<?php
include "web_check.php";
include "star_connection.php";
$lifting = "lifting";
$branch_master="branch_master";

$new_qry_string_filtered = "";
$export_filtered_str = "";
$srch_dlr_dtls = $_GET["srch_dlr_dtls"] ? addslashes(trim($_GET["srch_dlr_dtls"])) : "";
$whr_str = "";
$totres1 = 0;
$add_page_name = "lifting_report.php";
$page_name = "lifting_report.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$name_login = trim($_SESSION["start_report_admin_name"]);
// get the data 'order_show_branch' for this name_login from 'startreport_admin'
$sql = "select * from `startreport_admin` where `user_name`='$name_login' limit 1";
$res = mysql_query($sql);
// get the data 'order_show_branch' from 'startreport_admin'
if ($res) {
    $row = mysql_fetch_assoc($res);
    $order_show_branch = $row["order_show_branch"] ? trim($row["order_show_branch"]) : "";
}
// get the list of 'customer_code' where 'region' is 'order_show_branch' from 'customer_master'
$sql = "select `customer_code` from `customer_master` where `region`='$order_show_branch'";
$res = mysql_query($sql);
$totres = mysql_num_rows($res);
if ($totres > 0) {
    $customer_code_arr = array();
    while ($row = mysql_fetch_assoc($res)) {
        $customer_code_arr[] = $row["customer_code"];
    }
}
// get all from $lifting where 'linked_dealer_cust_code' is in $customer_code_arr
$customer_code_arr_str = implode("','", $customer_code_arr);
$whr_str = "where `linked_dealer_cust_code` in ('$customer_code_arr_str')";

// Get the numbers of filtered rows from $lifting
if ($order_show_branch == '') {
    $sql_pg = "select * from $lifting order by `lid` asc";
} else {
    $sql_pg = "select * from $lifting $whr_str order by `lid` asc";
}
$res_pg = mysql_query($sql_pg);
$totres_pg = mysql_num_rows($res_pg);

$total_pgres = $totres_pg;
$start_from = (($page - 1) * $limit);
$prev = $page - 1;                            //previous page is page - 1
$next = $page + 1;                            //next page is page + 1
$lastpage = ceil($total_pgres / $limit);   //lastpage is = total pages / items per page, rounded up.
$lpm1 = $lastpage - 1;

// get the data from $lifting
if ($order_show_branch == '') {
    $sql1 = "select * from $lifting order by `lid` desc limit $start_from,$limit";
} else {
    $sql1 = "select * from $lifting $whr_str order by `lid` desc limit $start_from,$limit";
}
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);

/*---------PAGINATION RELATED CODE START----------*/


include "web_header.php";
?>
<script type="text/javascript">
    jQuery(function() {

    });
</script>
<section class="content">
    <div class="container-fluid">
        <div class="block-header">

        </div>
        <!-- Basic Examples -->
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>Lifting Report (<?php echo $total_pgres; ?>)&nbsp;&nbsp;
                            <a href="export_lifting_report.php" class="btn bg-red waves-effe">Export&nbsp;Lifting&nbsp;Report</a> &nbsp;
                        </h2>
                                
                        <br><br>
                        <span style="clear:both;display:block;"></span>
                    </div>
                    <div class="body">
                    <div class="card">
                <div class="row">
                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                    <select class="form-control" id="sl_branch">
                    <option value="" selected>Select Branch</option>

                    <?php

                    $sql3 = "select `branch_code`,`branch_name` from $branch_master where `acedns`='Y' order by `branch_name`";
                    $res3 = mysql_query($sql3);
                    $totres3 = mysql_num_rows($res3);

                    if($totres3>0){
                        while($row3=mysql_fetch_assoc($res3)){
                            $the_branch_code = $row3["branch_code"];
                            $the_branch_name = $row3["branch_name"];
                            ?>
                    <option value="<?php echo $the_branch_code;?>"><?php echo $the_branch_name;?></option>
                            <?php
                        }
                    }
                    ?>
                    </select>
                        
                    </div>

                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                        <input type="text" class="form-control" id="srch_linked_dealer" style="width:100%;" value="<?php echo $srch_linked_dealer;?>" placeholder="Search Linked Dealer Name" title="Search Linked Dealer Name">
                    </div>

                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                        <input type="text" class="form-control" id="srch_sub_dealer" value="<?php echo $srch_sub_dealer;?>" placeholder="Search Sub Dealer / RSSD Name" title="Search Sub Dealer / RSSD Name">
                    </div>

                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                        <?php 
                            $months = array(
                                'January', 'February', 'March', 'April', 'May', 'June',
                                'July', 'August', 'September', 'October', 'November', 'December'
                            ); ?>

                            <select name="month[]" id="month" class="form-control">
                            <option value="">Select Month</option>
                            <?php 
                            // Loop through the array to generate options
                            foreach ($months as $month) { ?>
                                <option value="<?php echo $month; ?>"><?php echo $month; ?></option>
                            <?php } ?>

                            </select>
                        
                    </div>

                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                        <button type="button" class="btn bg-red waves-effect srch_btn" onclick="alpha_func()">Search</button>
                    </div>

                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                        <button type="button" class="btn bg-red waves-effect srch_reset_btn" onclick="reset_func()">Reset</button>
                    </div>
                </div>
            </div>


                            </div>
                        <?php
                        echo olcPaging($adjacents, $targetpage, $limit, $page, $prev, $next, $lastpage, $lpm1, "paged", $new_qry_string_filtered);
                        ?>
                        <span style="display:block; clear:both;"></span>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" id="data_table">
                                <thead>
                                    <tr>
                                        <th>Linked&nbsp;Dealer&nbsp;Code</th>
                                        <th>Linked&nbsp;Dealer&nbsp;SAP&nbsp;Code</th>
                                        <th>Linked&nbsp;Dealer&nbsp;Name</th>
                                        <th>Sub&nbsp;Dealer&nbsp;/&nbsp;RSSD&nbsp;Code</th>
                                        <th>Sub&nbsp;Dealer&nbsp;/&nbsp;RSSD&nbsp;SAP&nbsp;Code</th>
                                        <th>Sub&nbsp;Dealer&nbsp;/&nbsp;RSSD&nbsp;Name</th>
                                        <th>Branch</th>
                                        <th>Month</th>
                                        <th>Product&nbsp;Name</th>
                                        <th>Total(Bags)</th>
                                        <th>Date&nbsp;of&nbsp;Lifting</th>
                                        <th>Challan&nbsp;No.</th>
                                        <th>Submit&nbsp;Date&nbsp;Time</th>
                                        <th>Status&nbsp;(Approved&nbsp;/&nbsp;Pending&nbsp;/&nbsp;Rejected)</th>
                                        <th>Approve/Rejection&nbsp;Date&nbsp;Time</th>
                                        <th>Reason&nbsp;for&nbsp;Rejection</th>
                                        <th>Total&nbsp;Subdealer&nbsp;/&nbsp;RSSD&nbsp;Sale</th>
                                        <th>Total&nbsp;Dealer&nbsp;Sale</th>
                                        <th>Subdealer&nbsp;/&nbsp;RSSD&nbsp;Sale&nbsp;(%)</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Linked&nbsp;Dealer&nbsp;Code</th>
                                        <th>Linked&nbsp;Dealer&nbsp;SAP&nbsp;Code</th>
                                        <th>Linked&nbsp;Dealer&nbsp;Name</th>
                                        <th>Sub&nbsp;Dealer&nbsp;/&nbsp;RSSD&nbsp;Code</th>
                                        <th>Sub&nbsp;Dealer&nbsp;/&nbsp;RSSD&nbsp;SAP&nbsp;Code</th>
                                        <th>Sub&nbsp;Dealer&nbsp;/&nbsp;RSSD&nbsp;Name</th>
                                        <th>Branch</th>
                                        <th>Month</th>
                                        <th>Product&nbsp;Name</th>
                                        <th>Total(Bags)</th>
                                        <th>Date&nbsp;of&nbsp;Lifting</th>
                                        <th>Challan&nbsp;No.</th>
                                        <th>Submit&nbsp;Date&nbsp;Time</th>
                                        <th>Status&nbsp;(Approved&nbsp;/&nbsp;Pending&nbsp;/&nbsp;Rejected)</th>
                                        <th>Approve/Rejection&nbsp;Date&nbsp;Time</th>
                                        <th>Reason&nbsp;for&nbsp;Rejection</th>
                                        <th>Total&nbsp;Subdealer&nbsp;/&nbsp;RSSD&nbsp;Sale</th>
                                        <th>Total&nbsp;Dealer&nbsp;Sale</th>
                                        <th>Subdealer&nbsp;/&nbsp;RSSD&nbsp;Sale&nbsp;(%)</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php
                                    if ($totres1 > 0) {
                                        while ($row1 = mysql_fetch_assoc($res1)) {
                                            $linked_dealer_code = $row1["linked_dealer_code"] ? trim($row1["linked_dealer_code"]) : "";
                                            $linked_dealer_sap_code = $row1["linked_dealer_sap_code"] ? trim($row1["linked_dealer_sap_code"]) : "";
                                            $linked_dealer_name = $row1["linked_dealer_name"] ? trim($row1["linked_dealer_name"]) : "";
                                            $sub_dealer_rssd_code = $row1["sub_dealer_rssd_code"] ? trim($row1["sub_dealer_rssd_code"]) : "";
                                            $sub_dealer_rssd_sap_code = $row1["sub_dealer_rssd_sap_code"] ? trim($row1["sub_dealer_rssd_sap_code"]) : "";
                                            $sub_dealer_rssd_name = $row1["sub_dealer_rssd_name"] ? trim($row1["sub_dealer_rssd_name"]) : "";
                                            $branch = $row1["branch"] ? trim($row1["branch"]) : "";

                                            $prod_display_name = $row1["prod_display_name"] ? trim($row1["prod_display_name"]) : "";
                                            $total_bags = $row1["total_bags"] ? trim($row1["total_bags"]) : "";
                                            $date_of_lifting = $row1["date_of_lifting"] ? trim($row1["date_of_lifting"]) : "";
                                            $challan_no = $row1["challan_no"] ? trim($row1["challan_no"]) : "";
                                            $submit_date_time = $row1["submit_date_time"] ? trim($row1["submit_date_time"]) : "";
                                            $status = $row1["status"] ? trim($row1["status"]) : "";
                                            $status_date_and_time = $row1["status_date_and_time"] ? trim($row1["status_date_and_time"]) : "";
                                            $reason_for_rejection = $row1["reason_for_rejection"] ? trim($row1["reason_for_rejection"]) : "";
                                            $total_subdealer_rssd_sale = $row1["total_subdealer_rssd_sale"] ? trim($row1["total_subdealer_rssd_sale"]) : "";
                                            $total_dealer_sale = $row1["total_dealer_sale"] ? trim($row1["total_dealer_sale"]) : "";
                                            $subdealer_rssd_sale_percent = $row1["subdealer_rssd_sale_percent"] ? trim($row1["subdealer_rssd_sale_percent"]) : "";

                                            //$month = $row1["month"] ? trim($row1["month"]) : "";
                                            $month = "";
                                            if ($date_of_lifting != "") {
                                                $month = date("M-y", strtotime($date_of_lifting));
                                            }
                                    ?>
                                            <tr>
                                                <td><?php echo $linked_dealer_code; ?></td>
                                                <td><?php echo $linked_dealer_sap_code; ?></td>
                                                <td><?php echo $linked_dealer_name; ?></td>
                                                <td><?php echo $sub_dealer_rssd_code; ?></td>
                                                <td><?php echo $sub_dealer_rssd_sap_code; ?></td>
                                                <td><?php echo $sub_dealer_rssd_name; ?></td>
                                                <td><?php echo $branch; ?></td>
                                                <td><?php echo $month; ?></td>
                                                <td><?php echo $prod_display_name; ?></td>
                                                <td><?php echo $total_bags; ?></td>
                                                <td><?php echo $date_of_lifting; ?></td>
                                                <input type="hidden" id="date_of_lifting" value="<?php echo $date_of_lifting; ?>" />
                                                <td><?php echo $challan_no; ?></td>
                                                <td><?php echo $submit_date_time; ?></td>
                                                <td><?php echo $status; ?></td>
                                                <td><?php echo $status_date_and_time; ?></td>
                                                <td><?php echo $reason_for_rejection; ?></td>
                                                <td><?php echo $total_subdealer_rssd_sale; ?></td>
                                                <td><?php echo $total_dealer_sale; ?></td>
                                                <td><?php echo $subdealer_rssd_sale_percent; ?></td>
                                            </tr>

                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td style="text-align:center" colspan="19">No data found.</td>
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
        <!-- #END# Basic Examples -->
        <!-- Exportable Table -->

        <!-- #END# Exportable Table -->
    </div>
</section>
<script>
    function alpha_func(){

        var branchCode=document.getElementById('sl_branch').value;
        console.log(branchCode);
        var linkedDealerName=document.getElementById('srch_linked_dealer').value;
        console.log(linkedDealerName);
        var subDealerName=document.getElementById('srch_sub_dealer').value;
        console.log(subDealerName);
        var monthData=document.getElementById('month').value;
        console.log(monthData);
        // var date_of_lifting=document.getElementById('date_of_lifting').value;
        // var endDate=document.getElementById('end_date').value;

        $.ajax({
        url: 'branch_data_api.php',
        type: 'POST',
        data: { branchCode: branchCode,
            linkedDealerName: linkedDealerName,
            subDealerName: subDealerName,
            monthData: monthData },
        dataType: 'json',
        success: function(data) {
            var table = document.getElementById('data_table');
            var tableBody = table.getElementsByTagName('tbody')[0];
            tableBody.innerHTML = ''; // Clear the table body

            for (var i = 0; i < data.length; i++) {
                var row = tableBody.insertRow(i);
                
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                var cell4 = row.insertCell(3);
                var cell5 = row.insertCell(4);
                var cell6 = row.insertCell(5);
                var cell7 = row.insertCell(6);
                var cell8 = row.insertCell(7);
                var cell9 = row.insertCell(8);
                var cell10 = row.insertCell(9);
                var cell11 = row.insertCell(10);
                var cell12 = row.insertCell(11);
                var cell13 = row.insertCell(12);
                var cell14 = row.insertCell(13);
                var cell15 = row.insertCell(14);
                var cell16 = row.insertCell(15);
                var cell17 = row.insertCell(16);
                var cell18 = row.insertCell(17);
                var cell19 = row.insertCell(18);
                
                cell1.textContent = data[i].linked_dealer_code;
                cell2.textContent = data[i].linked_dealer_sap_code;
                cell3.textContent = data[i].linked_dealer_name;
                cell4.textContent = data[i].sub_dealer_rssd_code;
                cell5.textContent = data[i].sub_dealer_rssd_sap_code;
                cell6.textContent = data[i].sub_dealer_rssd_name;
                cell7.textContent = data[i].branch;
                // Assuming data[i].month contains a date string like "2023-04-12"
                var inputDate = new Date(data[i].date_of_lifting);
                var formattedDate = inputDate.toLocaleDateString('en-US', { month: 'short', year: '2-digit' });

                cell8.textContent = formattedDate; // This will be in the "M-YY" format, e.g., "Apr-23"
                cell9.textContent = data[i].prod_display_name;
                cell10.textContent = data[i].total_bags;
                cell11.textContent = data[i].date_of_lifting;
                cell12.textContent = data[i].challan_no;
                cell13.textContent = data[i].submit_date_time;
                cell14.textContent = data[i].status;
                cell15.textContent = data[i].status_date_and_time;
                cell16.textContent = data[i].reason_for_rejection;
                cell17.textContent = data[i].total_subdealer_rssd_sale;
                cell18.textContent = data[i].total_dealer_sale;
                cell19.textContent = data[i].subdealer_rssd_sale_percent;
                
            }
            }
        });

        // $.ajax({
        // url: 'linked_dealer_data_api.php',
        // type: 'POST',
        // data: { linkedDealerName: linkedDealerName },
        // dataType: 'json',
        // success: function(data1) {
        //     var table = document.getElementById('data_table');
        //     var tableBody = table.getElementsByTagName('tbody')[0];
        //     tableBody.innerHTML = ''; // Clear the table body

        //     for (var i = 0; i < data1.length; i++) {
        //         var row = tableBody.insertRow(i);
                
        //         var cell1 = row.insertCell(0);
        //         var cell2 = row.insertCell(1);
        //         var cell3 = row.insertCell(2);
        //         var cell4 = row.insertCell(3);
        //         var cell5 = row.insertCell(4);
        //         var cell6 = row.insertCell(5);
        //         var cell7 = row.insertCell(6);
        //         var cell8 = row.insertCell(7);
        //         var cell9 = row.insertCell(8);
        //         var cell10 = row.insertCell(9);
        //         var cell11 = row.insertCell(10);
        //         var cell12 = row.insertCell(11);
        //         var cell13 = row.insertCell(12);
        //         var cell14 = row.insertCell(13);
        //         var cell15 = row.insertCell(14);
        //         var cell16 = row.insertCell(15);
        //         var cell17 = row.insertCell(16);
        //         var cell18 = row.insertCell(17);
        //         var cell19 = row.insertCell(18);
                
        //         cell1.textContent = data1[i].linked_dealer_code;
        //         cell2.textContent = data1[i].linked_dealer_sap_code;
        //         cell3.textContent = data1[i].linked_dealer_name;
        //         cell4.textContent = data1[i].sub_dealer_rssd_code;
        //         cell5.textContent = data1[i].sub_dealer_rssd_sap_code;
        //         cell6.textContent = data1[i].sub_dealer_rssd_name;
        //         cell7.textContent = data1[i].branch;
        //         cell8.textContent = data1[i].month;
        //         cell9.textContent = data1[i].prod_display_name;
        //         cell10.textContent = data1[i].total_bags;
        //         cell11.textContent = data1[i].date_of_lifting;
        //         cell12.textContent = data1[i].challan_no;
        //         cell13.textContent = data1[i].submit_date_time;
        //         cell14.textContent = data1[i].status;
        //         cell15.textContent = data1[i].status_date_and_time;
        //         cell16.textContent = data1[i].reason_for_rejection;
        //         cell17.textContent = data1[i].total_subdealer_rssd_sale;
        //         cell18.textContent = data1[i].total_dealer_sale;
        //         cell19.textContent = data1[i].subdealer_rssd_sale_percent;
                
        //     }
        //     }
        // });

        // $.ajax({
        // url: 'sub_dealer_rssd_data_api.php',
        // type: 'POST',
        // data: { subDealerName: subDealerName },
        // dataType: 'json',
        // success: function(data2) {
        //     var table = document.getElementById('data_table');
        //     var tableBody = table.getElementsByTagName('tbody')[0];
        //     tableBody.innerHTML = ''; // Clear the table body

        //     for (var i = 0; i < data2.length; i++) {
        //         var row = tableBody.insertRow(i);
                
        //         var cell1 = row.insertCell(0);
        //         var cell2 = row.insertCell(1);
        //         var cell3 = row.insertCell(2);
        //         var cell4 = row.insertCell(3);
        //         var cell5 = row.insertCell(4);
        //         var cell6 = row.insertCell(5);
        //         var cell7 = row.insertCell(6);
        //         var cell8 = row.insertCell(7);
        //         var cell9 = row.insertCell(8);
        //         var cell10 = row.insertCell(9);
        //         var cell11 = row.insertCell(10);
        //         var cell12 = row.insertCell(11);
        //         var cell13 = row.insertCell(12);
        //         var cell14 = row.insertCell(13);
        //         var cell15 = row.insertCell(14);
        //         var cell16 = row.insertCell(15);
        //         var cell17 = row.insertCell(16);
        //         var cell18 = row.insertCell(17);
        //         var cell19 = row.insertCell(18);
                
        //         cell1.textContent = data2[i].linked_dealer_code;
        //         cell2.textContent = data2[i].linked_dealer_sap_code;
        //         cell3.textContent = data2[i].linked_dealer_name;
        //         cell4.textContent = data2[i].sub_dealer_rssd_code;
        //         cell5.textContent = data2[i].sub_dealer_rssd_sap_code;
        //         cell6.textContent = data2[i].sub_dealer_rssd_name;
        //         cell7.textContent = data2[i].branch;
        //         cell8.textContent = data2[i].month;
        //         cell9.textContent = data2[i].prod_display_name;
        //         cell10.textContent = data2[i].total_bags;
        //         cell11.textContent = data2[i].date_of_lifting;
        //         cell12.textContent = data2[i].challan_no;
        //         cell13.textContent = data2[i].submit_date_time;
        //         cell14.textContent = data2[i].status;
        //         cell15.textContent = data2[i].status_date_and_time;
        //         cell16.textContent = data2[i].reason_for_rejection;
        //         cell17.textContent = data2[i].total_subdealer_rssd_sale;
        //         cell18.textContent = data2[i].total_dealer_sale;
        //         cell19.textContent = data2[i].subdealer_rssd_sale_percent;
                
        //     }
        //     }
        // });

        // $.ajax({
        // url: 'month_data_api.php',
        // type: 'POST',
        // data: { monthData: monthData },
        // dataType: 'json',
        // success: function(data3) {
        //     var table = document.getElementById('data_table');
        //     var tableBody = table.getElementsByTagName('tbody')[0];
        //     tableBody.innerHTML = ''; // Clear the table body

        //     for (var i = 0; i < data3.length; i++) {
        //         var row = tableBody.insertRow(i);
                
        //         var cell1 = row.insertCell(0);
        //         var cell2 = row.insertCell(1);
        //         var cell3 = row.insertCell(2);
        //         var cell4 = row.insertCell(3);
        //         var cell5 = row.insertCell(4);
        //         var cell6 = row.insertCell(5);
        //         var cell7 = row.insertCell(6);
        //         var cell8 = row.insertCell(7);
        //         var cell9 = row.insertCell(8);
        //         var cell10 = row.insertCell(9);
        //         var cell11 = row.insertCell(10);
        //         var cell12 = row.insertCell(11);
        //         var cell13 = row.insertCell(12);
        //         var cell14 = row.insertCell(13);
        //         var cell15 = row.insertCell(14);
        //         var cell16 = row.insertCell(15);
        //         var cell17 = row.insertCell(16);
        //         var cell18 = row.insertCell(17);
        //         var cell19 = row.insertCell(18);
                
        //         cell1.textContent = data3[i].linked_dealer_code;
        //         cell2.textContent = data3[i].linked_dealer_sap_code;
        //         cell3.textContent = data3[i].linked_dealer_name;
        //         cell4.textContent = data3[i].sub_dealer_rssd_code;
        //         cell5.textContent = data3[i].sub_dealer_rssd_sap_code;
        //         cell6.textContent = data3[i].sub_dealer_rssd_name;
        //         cell7.textContent = data3[i].branch;
        //         cell8.textContent = data3[i].month;
        //         cell9.textContent = data3[i].prod_display_name;
        //         cell10.textContent = data3[i].total_bags;
        //         cell11.textContent = data3[i].date_of_lifting;
        //         cell12.textContent = data3[i].challan_no;
        //         cell13.textContent = data3[i].submit_date_time;
        //         cell14.textContent = data3[i].status;
        //         cell15.textContent = data3[i].status_date_and_time;
        //         cell16.textContent = data3[i].reason_for_rejection;
        //         cell17.textContent = data3[i].total_subdealer_rssd_sale;
        //         cell18.textContent = data3[i].total_dealer_sale;
        //         cell19.textContent = data3[i].subdealer_rssd_sale_percent;
                
        //     }
        //     }
        // });
    }
</script>

<script type="text/javascript">
    jQuery(function() {

        jQuery('#branch').chosen({no_results_text:'Oops, no branch found!',search_contains: true,placeholder_text_single: 'Select Branches'});

        var imgs = '<img src="images/ajax-loader.gif"/>';
        var done_img = '<img src="images/success_tick.png"/>';

    });
</script>

<script>
    function reset_func(){
        window.location.href = "https://starsaathi.com/SAP/admin/lifting_report.php";
    }
</script>

<?php
include "web_footer.php";
mysql_close();
?>
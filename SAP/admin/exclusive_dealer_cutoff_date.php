<?php
include "web_check.php";
include "star_connection.php";
$employee_master = "employee_master";
$customer_master = "customer_master";
$table_main = "dealer_exclusive_special_cutoff";
$changepassword = "changepassword";
$ai_status_arr = array("Y", "N");
if (@isset($_GET["submsg"]) && $_GET["submsg"] != "") {
    $submsg = $_GET["submsg"];
} else {
    $submsg = "";
}
$add_page_name = "exclusive_dealer_cutoff_date.php";
$page_name = "dealer_list.php";
$page = $_GET['paged'] ? $_GET['paged'] : 1;





// if (@$_POST["update"] == "Update") {
//     $upthe_theempid = $_POST["upthe_theempid"] ? addslashes(trim($_POST["upthe_theempid"])) : "";
//     $upthe_dns_emp_code = $_POST["upthe_dns_emp_code"] ? addslashes(trim($_POST["upthe_dns_emp_code"])) : "";
//     $dlr_status = $_POST["dlr_status"] ? addslashes(trim($_POST["dlr_status"])) : "N";
//     $emp_phone = $_POST["emp_phone"] ? addslashes(trim($_POST["emp_phone"])) : "";
//     $the_pno = $_POST["the_pno"] ? trim($_POST["the_pno"]) : "1";
//     if ($emp_phone == '') {
//         $submsg = 'Please enter phone number.';
//         $res_colour = 2;
//     } else {

//         $sql8 = "select `customer_code` from $customer_master where `phone_no`='$emp_phone' and `cust_type`='Dealer' and `customer_code`!='$upthe_theempid'";
//         $res8 = mysql_query($sql8);
//         $totres8 = mysql_num_rows($res8);
//         if ($totres8 > 0) {
//             $submsg = 'Phone number already exist. Please use another number.';
//             header("location:$add_page_name?theempid=$upthe_theempid&paged=$the_pno&submsg=" . $submsg);
//         } else {
//             $sql9 = "SELECT table_name FROM information_schema.tables WHERE table_schema = 'starsaat_START' AND table_name = 'employee_master';";
//             $res9 = mysql_query($sql9);
//             $totres9 = mysql_num_rows($res9);
//             if ($totres9 > 0) {
//                 if ($upthe_dns_emp_code != "") {
//                     $sql15 = "update $employee_master set `phone_no`='$emp_phone' where `dns_emp_code`='$upthe_dns_emp_code'";
//                     $res15 = mysql_query($sql15);
//                 }
//             }
//             $sql5 = "update $customer_master set `phone_no`='$emp_phone',`acedns`='$dlr_status' where `customer_code`='$upthe_theempid'";
//             $res5 = mysql_query($sql5);
//             $submsg = 'Phone number successfully updated.';
//             header("location:$add_page_name?theempid=$upthe_theempid&paged=$the_pno&submsg=" . $submsg);
//         }
//     }
// }
if (@isset($_GET["theempid"]) && $_GET["theempid"] != "") {
    $theempid = $_GET["theempid"] ? trim($_GET["theempid"]) : "";
    $sql8 = "select * from $customer_master where `customer_code`='$theempid'";
    $res8 = mysql_query($sql8);
    $totres8 = mysql_num_rows($res8);
    if ($totres8 > 0) {
        $row8 = mysql_fetch_assoc($res8);
        $dns_emp_code = $row8["dns_customer_code"];
        $dns_emp_id = $row8["customer_id"];
        $emp_name = $row8["customer_name"];
        $acedns = $row8["acedns"];
        $phone_no = $row8["phone_no"];
        $sms_otp = $row8["sms_otp"];
    } else {
        $theempid = "";
        $dns_emp_code = "";
        $dns_emp_id = "";
        $emp_name = "";
        $acedns = "";
        $phone_no = "";
        $sms_otp = "";
    }
} else {
    $theempid = "";
    $dns_emp_code = "";
    $dns_emp_id = "";
    $emp_name = "";
    $acedns = "";
    $phone_no = "";
    $sms_otp = "";
}
// echo $_GET["theempid"];
// die;
if (isset($_POST['create_cutoff_btn'])) {
    $for_year = addslashes(trim($_POST['for_year']));
    $for_month = addslashes(trim($_POST['for_month']));
    //$cut_off_month = addslashes(trim($_POST['cut_off_month']));
    $cutoff_date = addslashes(trim($_POST['cutoff_date']));

    // Check for duplicates
    $checkSql = "SELECT id FROM dealer_exclusive_special_cutoff WHERE for_year = '$for_year' AND for_month = '$for_month'";
    $checkRes = mysql_query($checkSql);

    if (mysql_num_rows($checkRes) > 0) {
        echo "<script>alert('Entry for this year and month already exists!');</script>";
    } else {
        $insertSql = "INSERT INTO dealer_exclusive_special_cutoff (customer_id,customer_code,for_year, for_month, cutoff_date) 
                      VALUES ('$dns_emp_id','$theempid','$for_year', '$for_month',  '$cutoff_date')";
        //   echo $insertSql;
        //   die;
        if (mysql_query($insertSql)) {
            echo "<script>window.location = 'exclusive_dealer_cutoff_date.php?theempid=" . urlencode($theempid) . "&paged=" . $page . "';</script>";
        } else {
            echo "<script>alert('Failed to insert record.');</script>";
        }
    }
}
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
                        <div class="row clearfix">
                            <h2 class="col-lg-2 "> Exclusive Dealer Special Cutoff Dates <?php echo $emp_name . '(' . $theempid . ')' ?>&nbsp;&nbsp;&nbsp; <?php if ($submsg != "") {
                                                                                                                                                        echo $submsg;
                                                                                                                                                    } ?></h2>


                            <div class="col-lg-2 ">
                                <button type="button" class="btn bg-green waves-effect" data-toggle="modal" data-target="#createModal">Create New CutOff Date</button>
                            </div>
                            <div class="col-lg-2 ">
                                <a href="<?php echo $page_name . "?paged=" . $page; ?>" class="btn bg-red waves-effect" style="margin-left: 20px;margin-bottom:10px;">Back To Dealer List</a>
                            </div>
                        </div>
                    </div>
                    <div class="body"  style="padding-bottom: 20px;">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Year</th>
                                        <th>Month</th>
                                        <!-- <th>Cut Off Month</th> -->
                                        <th>Cutoff Date</th>
                                         <th>Created On</th>
                                        <!-- <th>Action</th> -->
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Year</th>
                                        <th>Month</th>
                                        <!-- <th>Cut Off Month</th> -->
                                        <th>Cutoff Date</th>
                                        <!-- <th>Action</th> -->
                                          <th>Created On</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php
                                    $months = [
                                        "01" => "January",
                                        "02" => "February",
                                        "03" => "March",
                                        "04" => "April",
                                        "05" => "May",
                                        "06" => "June",
                                        "07" => "July",
                                        "08" => "August",
                                        "09" => "September",
                                        "10" => "October",
                                        "11" => "November",
                                        "12" => "December"
                                    ];
                                    $sql1 = "SELECT * FROM $table_main WHERE customer_code = '$theempid' ORDER BY created_at DESC";


                                    $res1 = mysql_query($sql1);
                                    if (mysql_num_rows($res1) > 0) {
                                        while ($roww = mysql_fetch_assoc($res1)) {
                                            $cutoff_date = $roww["cutoff_date"] ? date("jS M, Y", strtotime($roww["cutoff_date"])) : "";
                                             $created_at = $roww["created_at"] ? date("jS M, Y", strtotime($roww["created_at"])) : "";
                                            echo "<tr>";
                                            echo "<td>{$roww['for_year']}</td>";
                                            echo "<td>{$months[$roww['for_month']]}</td>";
                                            // echo "<td>{$row['cut_off_month']}</td>";
                                            echo "<td>{$cutoff_date}</td>";
                                            echo "<td>{$created_at}</td>";
                                            // echo "<td><a href='edit_special_cutoff_date.php?id={$roww['id']}' class='btn btn-sm btn-warning'>Edit</a></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo '<tr><td colspan="4" style="text-align:center">No data found.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- #END# Basic Examples -->
        <!-- Exportable Table -->

        <!-- #END# Exportable Table -->
    </div>


    <?php
    // Dropdown options
    $currentYear = date("Y");
    $years = range($currentYear, $currentYear + 5);
    $months = [
        "01" => "January",
        "02" => "February",
        "03" => "March",
        "04" => "April",
        "05" => "May",
        "06" => "June",
        "07" => "July",
        "08" => "August",
        "09" => "September",
        "10" => "October",
        "11" => "November",
        "12" => "December"
    ];
    ?>



    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel">
        <div class="modal-dialog" role="document">
            <form method="POST" id="create_cutoff_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Create New Cutoff</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>For Year</label>
                            <select name="for_year" class="form-control" required>
                                <option value="">Select Year</option>
                                <?php foreach ($years as $year): ?>
                                    <option value="<?= $year ?>"><?= $year ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>For Month</label>
                            <select name="for_month" class="form-control" required>
                                <option value="">Select Month</option>
                                <?php foreach ($months as $key => $month): ?>
                                    <option value="<?= $key ?>"><?= $month ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>



                        <div class="form-group">
                            <label>Cutoff Date</label>
                            <input type="date" name="cutoff_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" name="create_cutoff_btn" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</section>
<!-- <script type="text/javascript">
    jQuery(function() {
        var imgs = '<img src="images/ajax-loader.gif"/>';
        var done_img = '<img src="images/success_tick.png"/>';

        jQuery(".clemply").click(function() {
            var ancr_elmnt = jQuery(this);
            var clemplyid = ancr_elmnt.attr("clemplyid");
            if (clemplyid != "") {
                var theldrid = "ca_ldr_" + clemplyid;
                var for_loader = jQuery("#" + theldrid);
                for_loader.html(imgs);
                jQuery.ajax({
                    url: 'ajax_clear_allocation_by_emp_id.php',
                    type: 'post',
                    dataType: "JSON",
                    data: "clemplyid=" + clemplyid,
                    success: function(response) {
                        if (response.process_status == "YES") {
                            ancr_elmnt.html("--");
                            for_loader.html(done_img);
                            setTimeout(function() {
                                for_loader.html("");
                            }, 3000);
                        } else {
                            for_loader.html("");
                            alert(response.process_message);
                        }
                    }
                });
            }
        });



        jQuery(".srch_btn").click(function() {
            var srch_dlr_dtls = jQuery("#srch_dlr_dtls").val();
            var sl_dlr_actdat = jQuery("#sl_dlr_actdat").val();
            var sl_dlr_alocated_type = jQuery("#sl_dlr_alocated_type").val();
            var qstring = "";
            var amp = "";
            if (srch_dlr_dtls != "" || sl_dlr_actdat != "" || sl_dlr_alocated_type != "") {
                if (srch_dlr_dtls != "") {
                    if (qstring != "") {
                        qstring = qstring + "&srch_dlr_dtls=" + encodeURIComponent(srch_dlr_dtls);
                    } else {
                        qstring = qstring + "srch_dlr_dtls=" + encodeURIComponent(srch_dlr_dtls);
                    }
                }
                if (sl_dlr_actdat != "") {
                    if (qstring != "") {
                        qstring = qstring + "&sl_dlr_actdat=" + sl_dlr_actdat;
                    } else {
                        qstring = qstring + "sl_dlr_actdat=" + sl_dlr_actdat;
                    }
                }
                if (sl_dlr_alocated_type != "") {
                    if (qstring != "") {
                        qstring = qstring + "&sl_dlr_alocated_type=" + sl_dlr_alocated_type;
                    } else {
                        qstring = qstring + "sl_dlr_alocated_type=" + sl_dlr_alocated_type;
                    }
                }
                if (qstring != "") {
                    qstring = "?" + qstring;
                }
                window.location = "dealer_list.php" + qstring;
            } else {
                alert("Please select atleast one field to search.");
            }
        });

        jQuery(".srch_reset_btn").click(function() {
            window.location = "dealer_list.php";
        });
    });
</script> -->
<?php
include "web_footer.php";
mysql_close();
?>
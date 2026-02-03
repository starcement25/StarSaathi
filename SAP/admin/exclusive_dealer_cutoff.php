<?php
include "web_check.php";
include "star_connection.php";

if (isset($_POST['create_cutoff_btn'])) {
    $for_year = addslashes(trim($_POST['for_year']));
    $for_month = addslashes(trim($_POST['for_month']));
    $cutoff_date = addslashes(trim($_POST['cutoff_date']));

   
    $for_date = DateTime::createFromFormat('Y-m', "$for_year-$for_month");
    $cutoff_date_obj = DateTime::createFromFormat('Y-m-d', $cutoff_date);

    if (!$for_date || !$cutoff_date_obj) {
        echo "<script>alert('Invalid date format.');</script>";
        exit;
    }

  
    if ($for_date > $cutoff_date_obj) {
        echo "<script>alert('Cut off date can not be less than selected Month & year!');</script>";
        exit;
    }

    
    $checkSql = "SELECT id FROM dealer_exclusive_common_cutoff WHERE for_year = '$for_year' AND for_month = '$for_month'";
    $checkRes = mysql_query($checkSql);
// echo  $checkSql ;
// die;
    if (mysql_num_rows($checkRes) > 0) {
        echo "<script>alert('Entry for this year and month already exists!');</script>";
    } else {
        $insertSql = "INSERT INTO dealer_exclusive_common_cutoff (for_year, for_month, cutoff_date) 
                      VALUES ('$for_year', '$for_month',  '$cutoff_date')";
        if (mysql_query($insertSql)) {
            echo "<script>window.location = 'exclusive_dealer_cutoff.php?for_year=" . urlencode($for_year) . "';</script>";
        } else {
            echo "<script>alert('Failed to insert record.');</script>";
        }
    }
}


$survey_form = "dealer_exclusive_common_cutoff";
$profile_image_dir = "../profile_image/";
$new_qry_string_filtered = "";
$srch_year = isset($_GET["for_year"]) ? addslashes(trim($_GET["for_year"])) : "";
$whr_str = "";

if (!empty($srch_year)) {
    $whr_str = "$survey_form.`for_year` = '$srch_year'";
    $new_qry_string_filtered .= "&for_year=" . $srch_year;
}

$new_whr_str = $whr_str ? "WHERE $whr_str" : "";

$page_name = "exclusive_dealer_cutoff.php";
$limit = 100;
$page = isset($_GET['paged']) ? (int)$_GET['paged'] : 1;
$start_from = ($page - 1) * $limit;
$adjacents = 4;
$targetpage = $page_name;

$pgsql = "SELECT id FROM $survey_form $new_whr_str";
$pgres = mysql_query($pgsql);
$total_pgres = mysql_num_rows($pgres);
$prev = $page - 1;
$next = $page + 1;
$lastpage = ceil($total_pgres / $limit);
$lpm1 = $lastpage - 1;
// $sql1 = "SELECT * FROM $survey_form $new_whr_str ORDER BY cutoff_date DESC LIMIT $start_from, $limit";

//                                     $res1 = mysql_query($sql1);
//                                     if (mysql_num_rows($res1) > 0) {
//                                         while ($row = mysql_fetch_assoc($res1)) {
//                                            echo 1;
//                                         }
//                                     } else {
//                                         echo 0;
//                                     }
//                                     die;
include "web_header.php";
?>
<section class="content">
    <div class="container-fluid">
        <div class="block-header"></div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card">
                    <div class="header">
                        <h2>Dealer Exclusive Cutoff List (<?php echo $total_pgres; ?>)</h2>
                        <div class="row clearfix">
                            <div class="col-lg-6">
                                <!-- <input type="text" class="form-control" id="year_list" value="<?php echo $srch_year; ?>" placeholder="Search Year"> -->
                                <select class="form-control" id="year_list">
                                    <option value="">Select Year</option>
                                    <?php
                                    $yearSql = "SELECT DISTINCT for_year FROM dealer_exclusive_common_cutoff ORDER BY for_year DESC";
                                    $yearRes = mysql_query($yearSql);
                                    while ($yearRow = mysql_fetch_assoc($yearRes)) {
                                        $yearVal = $yearRow['for_year'];
                                        $selected = ($srch_year == $yearVal) ? "selected" : "";
                                        echo "<option value='$yearVal' $selected>$yearVal</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-lg-1">
                                <button type="button" class="btn bg-red waves-effect srch_btn">Search</button>
                            </div>
                            <div class="col-lg-1">
                                <button type="button" class="btn bg-red waves-effect srch_reset_btn">Reset</button>
                            </div>

                            <div class="col-lg-2 ">
                                <button type="button" class="btn bg-green waves-effect" data-toggle="modal" data-target="#createModal">Create New CutOff Date</button>
                            </div>
                            <div class="col-lg-2 ">
                                <a href="dealer_list.php" class="btn bg-red waves-effect" style="margin-left: 20px;margin-bottom:10px;">Back To Dealer List</a>
                            </div>
                        </div>
                        <span style="clear:both;display:block;"></span>
                    </div>
                    <div class="body" style="padding-bottom: 20px;">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Year</th>
                                        <th>Month</th>
                                        <!-- <th>Cut Off Month</th> -->
                                        <th>Cutoff Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Year</th>
                                        <th>Month</th>
                                        <!-- <th>Cut Off Month</th> -->
                                        <th>Cutoff Date</th>
                                        <th>Action</th>
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
                                    $sql1 = "SELECT * FROM $survey_form $new_whr_str ORDER BY cutoff_date DESC LIMIT $start_from, $limit";

                                    $res1 = mysql_query($sql1);
                                    if (mysql_num_rows($res1) > 0) {
                                        while ($row = mysql_fetch_assoc($res1)) {
                                            $cutoff_date = $row["cutoff_date"] ? date("jS M, Y", strtotime($row["cutoff_date"])) : "";
                                            echo "<tr>";
                                            echo "<td>{$row['for_year']}</td>";
                                            echo "<td>{$months[$row['for_month']]}</td>";
                                            // echo "<td>{$row['cut_off_month']}</td>";
                                            echo "<td>{$cutoff_date}</td>";
                                            echo "<td><a href='edit_cutoff_date.php?id={$row['id']}' class='btn btn-sm btn-warning'>Edit</a></td>";
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
                    <div class="block-header"></div>
                </div>
            </div>
        </div>
    </div>


    <?php
    // Dropdown options
    $currentYear =2025;
    $years = range($currentYear, $currentYear + 10);
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
<script type="text/javascript">
    jQuery(function() {
        jQuery(".srch_btn").click(function() {
            var year = jQuery("#year_list").val();
            if (year != "") {
                window.location = "<?php echo $page_name; ?>?for_year=" + encodeURIComponent(year);
            } else {
                alert("Please enter year to search.");
            }
        });

        jQuery(".srch_reset_btn").click(function() {
            window.location = "<?php echo $page_name; ?>";
        });
    });


    // var srch_year = jQuery("#year_list").val();
    // if (srch_year != "") {
    //     qstring = "for_year=" + encodeURIComponent(srch_year);
    //     window.location = "<?php echo $page_name; ?>?" + qstring;
    // } else {
    //     alert("Please select year to search.");
    // }
</script>
<?php
include "web_footer.php";
mysql_close();
?>
<?php
include "web_check.php";
include "star_connection.php";
$id = intval($_GET['id']);
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $for_year = $_POST['for_year'];
    $for_month = $_POST['for_month'];
    $cut_off_month = $_POST['cut_off_month'];
    $cutoff_date = $_POST['cutoff_date'];


    $check = mysql_query("SELECT * FROM dealer_exclusive_common_cutoff WHERE for_year='$for_year' AND for_month='$for_month' AND id != '$id'");
    if (mysql_num_rows($check) > 0) {
        $message = "Record with this Year and Month already exists!";
    } else {
        $update = mysql_query("UPDATE dealer_exclusive_common_cutoff SET cutoff_date='$cutoff_date' WHERE id='$id'");
        if ($update) {
            header("Location: exclusive_dealer_cutoff.php?msg=updated");
            exit;
        } else {
            $message = "Update failed.";
        }
    }
}


$res = mysql_query("SELECT * FROM dealer_exclusive_common_cutoff WHERE id='$id'");
$data = mysql_fetch_assoc($res);
// echo "<pre>";
// print_r($data);
// echo "</pre>";
// die;
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
include "web_header.php";
?>


<section class="content">
    <div class="container-fluid">
        <div class="block-header"></div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card">
                    <div class="header">
                        <h2>Edit Cutoff Date</h2>

                        <?php if ($message) echo "<p style='color:red;'>$message</p>"; ?>
                    </div>
                    <div class="body">
                        <form method="post"  style="padding-bottom: 20px;" >
                             <div class="row">
                                 <div class="col-sm-4">
                            <label>For Year</label>
                            <input type="text" class="form-control" value="<?php echo $data['for_year']; ?>" required readonly><br><br>
                            <!-- <select name="for_year" class="form-control" readonly>
                                <?php
                                $currentYear = date('Y');
                                for ($y = $currentYear - 5; $y <= $currentYear + 2; $y++) {
                                    $selected = $data['for_year'] == $y ? "selected" : "";
                                    echo "<option value='$y' $selected>$y</option>";
                                }
                                ?>
                            </select>-->
                            <br><br> 
                                 </div>
                                  <div class="col-sm-4">
                            <label>For Month</label>
                             <input type="text" class="form-control" value="<?php echo $months[$data['for_month']]; ?>" required readonly><br><br>
                            <!-- <select name="for_month" class="form-control" readonly>
                                <?php
                                foreach ($months as $num => $name) {
                                    $selected = $data['for_month'] == $num ? "selected" : "";
                                    echo "<option value='$num' $selected>$name</option>";
                                }
                                ?>
                            </select> -->
                            <br><br>
                                  </div>

 <div class="col-sm-4">
                            <label>Cut-off Date</label>
                            <input type="date" name="cutoff_date" class="form-control" value="<?php echo $data['cutoff_date']; ?>" required><br><br>
 </div>
                             </div>
                           <div class="form-group mt-4 mb-4">
    <input type="submit" value="Update" class="btn btn-primary">
    <a href="exclusive_dealer_cutoff.php" class="btn btn-secondary ml-2">Cancel</a>
</div>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include "web_footer.php";
mysql_close();
?>
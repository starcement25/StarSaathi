<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

include "web_check.php";
include "star_connection.php";
 $table_name = "lifting_final_file_upload_csv";

$img_dir = "../lifting_csv/";
$mime_type_array_pdf = array("csv");

if(isset($_GET["n_msg"]) and $_GET["n_msg"]!=""){
$n_msg = $_GET["n_msg"];
}else{
$n_msg = "";
}
$id=$zone=$year=$month=$CSV_file_name='';
$required="required";
$btn="Add File";

if ($_GET['id']) {
    $required='';
    $btn="Update File";
  $id=trim($_GET['id']);
    $sql1 = "SELECT * FROM `lifting_final_file_upload_csv` where id='".$id."'";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
    if($totres1>0){
        while($row1=mysql_fetch_assoc($res1)){
            $the_PDF_file_name_arr = array();
            $the_PDF_file_link_arr = array();
            $id = $row1["id"];
            $zone = $row1["zone"];
            $year = $row1["year"];
            $month = $row1["month"];
            $CSV_file_name = $row1["CSV_file_name"] ? trim($row1["CSV_file_name"]) : "";
        }
    }
}

if($_POST["snd_btn"]=="Add File"){
//echo"<pre>";print_r($_POST);die;


            //echo"<pre>";print_r($new_file_name);die;
            $zone = $_POST['zone'] ? trim($_POST['zone']) : "";
            $year = $_POST['year'] ? trim($_POST['year']) : "";
            $month = $_POST['month'] ? trim($_POST['month']) : "";
            $curr_date_time = date("Y-m-d H:i:s");
            // Check for duplicates
            $check_sql = "SELECT COUNT(*) as total FROM $table_name WHERE zone = '$zone' AND year = '$year' AND month = '$month'";
            $check_sql_res = mysql_query($check_sql);
            $row = mysql_fetch_assoc($check_sql_res);
            
            if ($row['total'] > 0) {
                //die("Duplicate entry found. Insert skipped.");
                $_SESSION['flash'] = [
                    'type' => 'warning', // or 'danger', 'warning', etc.
                    'message' => " Duplicate entry for zone = $zone, year = $year, month = $month. Insert skipped."
                ];
                header("Location: LiftingFinalFileUpload.php"); die;
            }
            //echo"<pre>";print_r($_SESSION);die;

			$sql_in = "insert into $table_name  (`zone`,`year`,`month`,`CSV_file_name`,`date_time`,`user_id`) values('$zone','$year','$month','','$curr_date_time','".$_SESSION['start_report_admin']."')";
			$res_in = mysql_query($sql_in);
            $last_id = mysql_insert_id();
             // Step 2: Handle file upload
            file_handel($last_id,$table_name);
    }
				
				

    if($_POST["snd_btn"]=="Update File"){
        //echo"<pre>";print_r($_POST);die;
        
        
                    //echo"<pre>";print_r($new_file_name);die;
                    $id = $_POST['id'] ? trim($_POST['id']) : "";
                    $zone = $_POST['zone'] ? trim($_POST['zone']) : "";
                    $year = $_POST['year'] ? trim($_POST['year']) : "";
                    $month = $_POST['month'] ? trim($_POST['month']) : "";
                    $curr_date_time = date("Y-m-d H:i:s");
                    $sql_in = "UPDATE $table_name SET `zone`='".$zone."',`year`='".$year."',`month`='".$month."' WHERE `id`='".$id."'";
                    $res_in = mysql_query($sql_in);
                    
                     // Step 2: Handle file upload
                     if(basename($_FILES["csvFile"]["name"])){
                        file_handel($id,$table_name);
                   }else {
                    $_SESSION['flash'] = [
                        'type' => 'success', // or 'danger', 'warning', etc.
                        'message' => 'Record Upload successfully!'
                    ];
                    header("Location: LiftingFinalFileUpload.php");die;
                        exit;
                }
            }
            function file_handel($last_id,$table_name){
                $uploadDir = "lifting_csv/";
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $fileName = basename($_FILES["csvFile"]["name"]);
                    $filePath = $uploadDir . $fileName;
                    $fileExt = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                    $fileSize = $_FILES["csvFile"]["size"];
                    $uniqueFileName = "csv_" . time() . "_" . uniqid() . ".csv";
                    $targetPath = $uploadDir . $uniqueFileName;

                    if ($fileExt != "csv") {
                        //echo "Only .csv files are allowed.";
                    // exit; die;
                    $_SESSION['flash'] = [
                        'type' => 'warning', // or 'danger', 'warning', etc.
                        'message' => " Only .csv files are allowed."
                    ];
                    $delete_sql="DELETE FROM `lifting_final_file_upload_csv` WHERE `id`='$last_id'";
                    mysql_query($delete_sql);
                    header("Location: LiftingFinalFileUpload.php");die;
                    
                    }

                    if ($fileSize > 10 * 1024 * 1024) { // 10MB
                        //echo "File exceeds 10MB limit.";
                        //exit; 
                        $_SESSION['flash'] = [
                            'type' => 'warning', // or 'danger', 'warning', etc.
                            'message' => " File exceeds 10MB limit."
                        ];
                        $delete_sql="DELETE FROM `lifting_final_file_upload_csv` WHERE `id`='$last_id'";
                         mysql_query($delete_sql);
                        header("Location: LiftingFinalFileUpload.php");die;
                        
                    }
                    if (file_exists($targetPath)) {
                        unlink($targetPath); // This deletes the uploaded CSV file
                    }
                    //echo"<pre>";print_r($filePath);die;
                    if (move_uploaded_file($_FILES["csvFile"]["tmp_name"], $targetPath)) {
                        // Step 3: Update the record with file path
                        //$safeFilePath = $conn->real_escape_string($filePath);
                        $sql_up = "UPDATE `$table_name` SET `CSV_file_name` = '$uniqueFileName' WHERE id = $last_id";

                        if (mysql_query($sql_up) === TRUE) {
                            if (($handle = fopen($targetPath, "r")) !== FALSE) {
                            //echo"<pre>";print_r('ss');die;

                                fgetcsv($handle); // Skip header
                                $delete_sql="DELETE FROM `lifting_final_allocation_data` WHERE `file_upload_id`='$last_id'";
                                mysql_query($delete_sql);

                                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                                    $allocationDateTime = date('Y-m-d H:i', strtotime(trim($data[0])));
                                    $appOrderNo = mysql_real_escape_string($data[1]);
                                    $invDate = date('Y-m-d', strtotime(trim($data[2])));
                                    $linkedDealerCode = $data[3];
                                    $linkedDealerSAPCode = $data[4];
                                    $linkedDealerName = $data[5];
                                    $subDealerCode = $data[6];
                                    $subDealerSAPCode = $data[7];
                                    $subDealerName = $data[8];
                                    $branch = $data[9];
                                    $month = $data[10];
                                    $productName = $data[11];
                                    $totalInvQty = (int)$data[12];
                                    $allocatedQty = (int)$data[13];
                                    $remainingQty = (int)$data[14];
                                    $invNo = $data[15];
                            
                                    $sql_insert = "INSERT INTO lifting_final_allocation_data (
                                        allocation_datetime, app_order_no, inv_date, linked_dealer_code,
                                        linked_dealer_sap_code, linked_dealer_name, sub_dealer_code,
                                        sub_dealer_sap_code, sub_dealer_name, branch, month, product_name,
                                        total_inv_qty, allocated_qty, remaining_allocation_qty, inv_no, file_upload_id
                                    ) VALUES (
                                        '$allocationDateTime', '$appOrderNo', '$invDate', '$linkedDealerCode',
                                        '$linkedDealerSAPCode', '$linkedDealerName', '$subDealerCode',
                                        '$subDealerSAPCode', '$subDealerName', '$branch', '$month', '$productName',
                                        $totalInvQty, $allocatedQty, $remainingQty, '$invNo', '$last_id'
                                    )";
                            //echo"<pre>";print_r($sql_insert);die;
                            
                                    mysql_query( $sql_insert);
                                }
                                fclose($handle);
                                //echo "CSV data imported successfully!";
                                if (file_exists($targetPath)) {
                                    unlink($targetPath); // This deletes the uploaded CSV file
                                }
                            } else {
                                //echo "Failed to open uploaded CSV.";
                                $_SESSION['flash'] = [
                                    'type' => 'warning', // or 'danger', 'warning', etc.
                                    'message' => 'Failed to open uploaded CSV!'
                                ];
                                $delete_sql="DELETE FROM `lifting_final_file_upload_csv` WHERE `id`='$last_id'";
                                mysql_query($delete_sql);
                                header("Location: LiftingFinalFileUpload.php");die;
                            }
                            
                            //echo "File uploaded and record updated successfully.";
                            $_SESSION['flash'] = [
                                'type' => 'success', // or 'danger', 'warning', etc.
                                'message' => 'Record Upload successfully!'
                            ];
                            header("Location: LiftingFinalFileUpload.php");die;
                                exit;
                          
                        } else {
                            //echo "Error updating record: " . $conn->error; die;
                            //echo 'Writable? ' . (is_writable('lifting_csv') ? 'Yes' : 'No');
                            // echo "Failed to upload file."; print_r(error_get_last()); die;
                            $_SESSION['flash'] = [
                                'type' => 'warning', // or 'danger', 'warning', etc.
                                'message' => 'Error updating record!'
                            ];
                           
                        }
                    } else {
                        //echo 'Writable? ' . (is_writable('lifting_csv') ? 'Yes' : 'No');
                        //echo "Failed to upload file."; print_r(error_get_last()); die;
                        $_SESSION['flash'] = [
                            'type' => 'warning', // or 'danger', 'warning', etc.
                            'message' => 'Failed to insert the record!'
                        ];
                      
                    }
            }



include "web_header.php";
?>
<style>
.estarix_cls{
	color:#F00;
	margin-left:5px;
}
.branch_err_cls,.pdf_err_cls{
	color:#F00;
	margin-left:5px;
}
.noty_curr_count,.noty_count_loader,.show_noty_sending_count{
	float:left;
	margin-left:5px;
}
.noty_curr_count{
	width:35px;
}
.noty_count_loader{
	width:24px;
}
.show_noty_sending_count{
	width:20px;
}
.show_noty_sending_count img{
	width:100%;;
}
.clear_class{
	clear:both;
	display:block;
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
                          <h2>Add Approved RSSD Lifting &nbsp;&nbsp;
                          <a href="LiftingFinalFileUpload.php" class="btn bg-red waves-effe">Show Lifting Final File</a></h2>
                        </div>
                        <div class="body" style="padding:20px;">
                            <form action="" method="post" enctype="multipart/form-data" id="multiple_upload_schemes_pdf_form" class="multiple_upload_schemes_pdf_form">
                                <input type="hidden" name="id" value="<?=$id?>"/>

                            <div class="row clearfix">
                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <div class="form-group"> 
                                    <label>Zone <span class="estarix_cls">*</span></label>
                                        <div class="form-line">
                                            <select id="zone" class="form-control" name="zone" require>
                                            <option <?php echo ($zone=='NE')?'selected': ''; ?> >NE</option>
                                            <option <?php echo ($zone=='ROE')?'selected': ''; ?> >ROE</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                                    <div class="form-group"> 
                                    <label>Year <span class="estarix_cls">*</span></label>
                                        <div class="form-line">
                                            <select id="year" class="form-control" name="year" require>
                                            <?php
                                                $currentYear = date("Y");
                                                for ($i = 0; $i <= 10; $i++) {
                                                    $year_data = $currentYear - $i;
                                                    $select_year= ($year==$year_data)?'selected': ''; 
                                                    echo "<option $select_year value=\"$year_data\">$year_data</option>";
                                                }
                                            ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 ">
                                    <div class="form-group"> 
                                    <label>Month <span class="estarix_cls">*</span></label>
                                        <div class="form-line">
                                            <select id="month" class="form-control" name="month" require >
                                                <option <?php echo ($month=='January')?'selected': ''; ?>  value="January">January</option>
                                                <option <?php echo ($month=='February')?'selected': ''; ?> value="February">February</option>
                                                <option <?php echo ($month=='March')?'selected': ''; ?> value="March">March</option>
                                                <option <?php echo ($month=='April')?'selected': ''; ?> value="April">April</option>
                                                <option <?php echo ($month=='May')?'selected': ''; ?> value="May">May</option>
                                                <option <?php echo ($month=='June')?'selected': ''; ?> value="June">June</option>
                                                <option <?php echo ($month=='July')?'selected': ''; ?> value="July">July</option>
                                                <option <?php echo ($month=='August')?'selected': ''; ?> value="August">August</option>
                                                <option <?php echo ($month=='September')?'selected': ''; ?> value="September">September</option>
                                                <option <?php echo ($month=='October')?'selected': ''; ?> value="October">October</option>
                                                <option <?php echo ($month=='November')?'selected': ''; ?> value="November">November</option>
                                                <option <?php echo ($month=='December')?'selected': ''; ?> value="December">December</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 ">
                                <div class="form-group"> 
                                        <div class="form-group n_pdf_upload_section">
                                            <label class="Select PDF">Select CSV Files<?php if($required!=''){ ?><span class="estarix_cls">*</span><?php } ?>&nbsp;(file size: less than 10MB ,Max file upload limit:10)&nbsp;<span class="pdf_err_cls"></span></label>
                                            <input type="file" class="form-control" id="csvFile" name="csvFile" multiple placeholder="Select CSV file" accept=".csv" <?= $required ?>>
                                        </div>
                                    <div class="form-group n_pdf_upload_section">
                                    <input type="submit" class="btn btn-primary waves-effect snd_btn" name="snd_btn" id="snd_btn"  value="<?=$btn?>" />
                                    <span><?php echo $CSV_file_name; ?></span>
                                    
                                    </div>
                                        <div class="form-group n_pdf_upload_section"> 
                                            <div class="loaddr_msg" id="loaddr_msg">
                                            <span class="uploading" style="float:left;margin-left:10px;display:none;">
                                            <label>&nbsp;</label>
                                            <img src="images/uploading.gif"/>
                                            </span>
                                            <span class="percent" style="float:left;margin-left:10px;"></span>
                                            <span style="clear:both;display:block;"></span>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Basic Examples -->
        </div>
    </section>
   

<?php
include "web_footer.php";
mysql_close();
?>
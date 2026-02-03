<?php
include "web_check.php";
include "config_setup_acedns.php";
include "star_connection.php";
include "pclzip.lib.php";
$db_name1 = "starsaat_acednsproduct";
$db_name2 = "starsaat_START";
$table_name = "employee_master";
$changepassword = "changepassword";
function similar_file_exists($filename) {
  if (file_exists($filename)) {
	return $filename;
  }
  $dir = dirname($filename);
  $files = glob($dir . '/*');
  $lcaseFilename = strtolower($filename);
  foreach($files as $file) {
	if (strtolower($file) == $lcaseFilename) {
	  return $file;
	}
  }
  return false;
}
$submsg = "";
if(@$_POST["upload"]=="Upload"){
$zip_file_name = $_FILES["zip_file"]["name"];
$zip_file_type = $_FILES["zip_file"]["type"];
$zip_file_size = $_FILES["zip_file"]["size"];
$zip_file_tmp = $_FILES["zip_file"]["tmp_name"];
$nick_name = "START";

if($zip_file_name!=""){
//For Unzip a zip file
	
	$upload_dir="../pop_prod_img/";
	$upload_file = $upload_dir.$zip_file_name;
	
	$thezipupload = move_uploaded_file($zip_file_tmp,$upload_file);
	if($thezipupload){
		$zipfile = new PclZip($upload_file);
		$zipresfun = $zipfile->extract($upload_dir);
		if($zipresfun== 0){
			$submsg = "Failed to unzip the file. Please try later.".$zipresfun;
		}
		else{
			$submsg = "Zip file uploaded successfully";
		}		
	}else{
		$submsg = "Failed to upload the file. Please try later.";
	}	
}else{
	$submsg = "Please browse the ZIP file first...";
}
}
$add_page_name = "upload_pop_image.php";
$page_name = "upload_pop_image.php";

include "web_header.php";
?>
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                
            </div>
            <!-- Basic Examples -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                          <h2>Upload Data</h2>
                        </div>
                        <div class="body">
<div class="table-responsive">
<form action="" method="POST" name="upld_data_form" id="upld_data_form" enctype="multipart/form-data">
<div class="row clearfix" style="margin:0px;">
<div class="col-sm-12">
    <div class="form-group" style="margin-top:10px;">
        <label for="zip_file">Upload Image ZIP file <strong><font color="#FF0000">[File extension will be .zip and Maximam size 10 MB]</font></strong></label>
        <div class="form-line">
<input type="file" class="form-control" id="zip_file" name="zip_file" placeholder="Select Zip file">
        </div>
    </div>
<div class="form-group">
<input type="submit" class="btn bg-red waves-effect upld_btn" name="upload" value="Upload" />
</div>
<div class="form-group">
<?php if($submsg!=""){ echo $submsg;}?>
</div>  
</div>
</div>
</form>
</div>
</div>
                    </div>
                </div>
            </div>
            <!-- #END# Basic Examples -->
            <!-- Exportable Table -->
            
            <!-- #END# Exportable Table -->
        </div>
    </section>
<script type="text/javascript">
jQuery(function(){

jQuery("form#upld_data_form").submit(function(){
	var zip_file = jQuery("#zip_file").val();
	if(zip_file==""){
		alert("Please browse the ZIP file first...");
		jQuery("#zip_file").focus();
		return false;
	}else{
		var fname = zip_file.toUpperCase();
		var pos1 = fname.indexOf(".ZIP");
		if(pos1==-1){
		alert("Invalid File Type\nPlease use ZIP only...");
		jQuery("#zip_file").focus();
		return false;
		}else{
		return true;
		}
	}
	
});

});
</script>
<?php
include "web_footer.php";
mysql_close();
?>
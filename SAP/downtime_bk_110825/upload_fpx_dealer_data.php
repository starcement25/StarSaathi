<?php
include "web_check.php";
include "config_setup_acedns.php";
include "star_connection.php";
include "pclzip.lib.php";
$db_name1 = "starsaat_acednsproduct";
$db_name2 = "starsaathi_STARS";
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
	$nick_name = strtoupper($nick_name);
	$folderName = strtoupper($nick_name);
	$error_array=array();
	if (!file_exists("../csv")){
		mkdir("../csv");
		chmod("../csv", 0777);
	}
	if ( !file_exists("../csv/$folderName")){
		mkdir("../csv/$folderName");
		chmod("../csv/$folderName", 0777);
	}
	if ( !file_exists("../csv/$folderName/filebkup/")){
		mkdir("../csv/$folderName/filebkup/");
		chmod("../csv/$folderName/filebkup/", 0777);
	}
	// Get array of all source files
		$files = scandir("../csv/$folderName");
		// Identify directories
		$source = "../csv/$folderName/";
		$destination = "../csv/$folderName/filebkup/";
		// Cycle through all source files
		foreach ($files as $file) {
		  if (in_array($file, array(".",".."))) continue;
		  // If we copied this successfully, mark it for deletion
		  if (@copy($source.$file, $destination.$file)) {
			$delete[] = $source.$file;
		  }
		}
		// Delete all successfully-copied files
		foreach ($delete as $file) {
		  unlink($file);
		}
		
	$upload_dir="../csv/$folderName/";
	$upload_file = $upload_dir.$zip_file_name;
	
	$thezipupload = move_uploaded_file($zip_file_tmp,$upload_file);
	if($thezipupload){
	$zipfile = new PclZip($upload_file);
	$zipresfun = $zipfile->extract($upload_dir);
	if($zipresfun!= 0){
		/*---------------------------------CODE START--------------------------------------------*/


	//FPX Dealer CSV

	if(similar_file_exists("../csv/$folderName/fpx_dealers.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/fpx_dealers.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		$lines = file($filename);
		$sqldelete="truncate fpx_dealer_list";
		$rsdelete=mysql_query($sqldelete);
		foreach($lines as $line)
		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)

			{ 

				while($char!="")

				{

					if($double_coute_found && $char=="\"")

					{

						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

			

					if(!$double_coute_found && $char=="\"")

					{  

					

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

				

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

			  

				$dealer_sap_code=trim($data[0]);
				$dealer_firm_name=trim($data[1]);
				$dealer_name=trim($data[2]);
				$branch_name=trim($data[3]);
				$region=trim($data[4]);
				$status=trim($data[5]);

				$csv_row_count=$rec_count+1;

					$sqlfpxdealer  = "insert into  fpx_dealer_list SET ";
					$sqlfpxdealer .= "  dealer_sap_code='".mysql_real_escape_string($dealer_sap_code)."'";
					$sqlfpxdealer .= " , dealer_firm_name='".mysql_real_escape_string($dealer_firm_name)."'";
					$sqlfpxdealer .= " ,  dealer_name ='".mysql_real_escape_string($dealer_name)."'";
					$sqlfpxdealer .= " ,  branch_name ='".mysql_real_escape_string($branch_name)."'";
					$sqlfpxdealer .= " , region='".mysql_real_escape_string($region)."'";
					$sqlfpxdealer .= " , status='".mysql_real_escape_string($status)."'";
					$sqlfpxdealer .= " , upload_time=CURRENT_TIMESTAMP()";

				
				mysql_query($sqlfpxdealer) or array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count in fpx_dealers.csv.Please check.");

			}

			 $rec_count++;

		}		

		$successval=1;

	}
	/*else
	{

		echo $successval="Naming convention  is wrong.";

		exit();

	}*/
	//FPX Dealer Truck CSV

	if(similar_file_exists("../csv/$folderName/fpx_dealer_truck.csv")!=false)
	{
		$filename=similar_file_exists("../csv/$folderName/fpx_dealer_truck.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";

		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		$lines = file($filename);
		$sqldelete="truncate fpx_dealer_truck_mapping";
		$rsdelete=mysql_query($sqldelete);
		foreach($lines as $line)
		{

			$i = 0;

			$char = substr($line, $i, 1);

			$value ="";

			$data="";

			$double_coute_found = false;

			if($rec_count>=1)
			{ 

				while($char!="")
				{

					if($double_coute_found && $char=="\"")
					{
						$double_coute_found = false;

						$i++;

						$char = substr($line, $i, 1);

						continue;
					}

					if(!$double_coute_found && $char=="\"")
					{  

						$double_coute_found = true;

						$i++;

						$char = substr($line, $i, 1);

						continue;

					}

					if($char=="," && !$double_coute_found)

					{

						$data[]=$value;

						$value = "";

					}

					else 

					{

					$value .= $char;

					}

					$i++;

					$char = substr($line, $i, 1);

				} //end of while

			   $data[]=$value;

			  //print_r($data);

				$dealer_sap_code=trim($data[0]);
				$dealer_name=trim($data[1]);
				$branch_name=trim($data[2]);
				$region=trim($data[3]);
				$truck_no=trim($data[4]);

				$csv_row_count=$rec_count+1;

					$sqlfpxdealer  = "insert into  fpx_dealer_truck_mapping SET ";
					$sqlfpxdealer .= "  dealer_sap_code='".mysql_real_escape_string($dealer_sap_code)."'";
					$sqlfpxdealer .= " ,  dealer_name ='".mysql_real_escape_string($dealer_name)."'";
					$sqlfpxdealer .= " ,  branch_name ='".mysql_real_escape_string($branch_name)."'";
					$sqlfpxdealer .= " , region='".mysql_real_escape_string($region)."'";
					$sqlfpxdealer .= " , truck_no ='".mysql_real_escape_string($truck_no)."'";
					$sqlfpxdealer .= " , upload_time=CURRENT_TIMESTAMP()";

				
				mysql_query($sqlfpxdealer) or array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count in fpx_dealer_truck.csv.Please check.");

			}

			 $rec_count++;

		}		

		$successval=1;

	}
	/*else
	{
		echo $successval="Naming convention  is wrong.";

		exit();

	}*/
	
	if($successval==1)
	{
		if(count($error_array)>0)
		{
		$error_string=implode('#',$error_array);
		$submsg .= 'Zip file extracted and data has been uploaded successfully with the following error(s).';
		}
		else{
		$submsg .= 'Zip file extracted and data has been uploaded successfully';
		}
		$submsg .= $error_string;
	}else 
			{
				$submsg .= "Something went wrong.";
				
			}
		/*---------------------------------CODE END--------------------------------------------*/
		
	}else{
		$submsg = "Failed to unzip the file. Please try later.".$zipresfun;
	}
	}else{
		$submsg = "Failed to upload the file. Please try later.";
	}	
}else{
	$submsg = "Please browse the ZIP file first...";
}
}
$add_page_name = "upload_fpx_dealer_data.php";
$page_name = "upload_fpx_dealer_data.php";

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
        <label for="zip_file">Upload ZIP file <strong><font color="#FF0000">[File extension will be .zip]</font></strong></label>
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



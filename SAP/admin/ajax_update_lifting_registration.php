<?php
include "web_check.php";
include "star_connection.php";

$app_setting_master = "app_setting_master";
$response = array("process_sts"=>"NO","msg"=>"","img_url"=>"");

if(isset($_FILES["lifting_registration_file"]) && $_FILES["lifting_registration_file"]["error"]==0){

	$allowed_ext = array("jpg","jpeg","png","gif");
	$file_name = $_FILES["lifting_registration_file"]["name"];
	$file_tmp  = $_FILES["lifting_registration_file"]["tmp_name"];
	$file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

	if(!in_array($file_ext, $allowed_ext)){
		$response["msg"] = "Invalid file type.";
		echo json_encode($response);
		exit;
	}

	// Folder relative to THIS script's location on disk
	$upload_dir = rtrim("uploads/app_settings", "/") . "/";
	if(!is_dir($upload_dir)){
		mkdir($upload_dir, 0755, true);
	}

	$new_file_name = "lifting_registration_".time().".".$file_ext;
	$upload_path = $upload_dir.$new_file_name; // e.g. uploads/app_settings/lifting_registration_123.png

	if(move_uploaded_file($file_tmp, $upload_path)){

		$sql_old = "select `the_value` from $app_setting_master where `the_key_name`='lifting_registration'";
		$res_old = mysql_query($sql_old);
		if($res_old && mysql_num_rows($res_old)>0){
			$row_old = mysql_fetch_assoc($res_old);
			$old_val = $row_old["the_value"];
			if($old_val && file_exists($old_val)){
				@unlink($old_val);
			}
			$safe_upload_path = mysql_real_escape_string($upload_path);
			$sql_upd = "update $app_setting_master set `the_value`='$safe_upload_path' where `the_key_name`='lifting_registration'";
			mysql_query($sql_upd);
		}else{
			$safe_upload_path = mysql_real_escape_string($upload_path);
			$sql_ins = "insert into $app_setting_master (`the_key_name`,`the_value`) values ('lifting_registration','$safe_upload_path')";
			mysql_query($sql_ins);
		}

		// Build URL from the script's own folder, not the domain root
		$script_dir = rtrim(str_replace("\\", "/", dirname($_SERVER['SCRIPT_NAME'])), "/"); // e.g. /SAP/admin
		$response["process_sts"] = "YES";
		$response["img_url"] = "https://".$_SERVER['SERVER_NAME'].$script_dir."/".$upload_path;

	}else{
		$response["msg"] = "Failed to move uploaded file.";
	}

}else{
	$response["msg"] = "No file uploaded or upload error.";
}

echo json_encode($response);
mysql_close();
?>
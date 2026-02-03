<?php
include "star_connection.php";
$server_url1 = "http://" . $_SERVER['SERVER_NAME']."/";
$customer_master = "customer_master";
$broker_master = "broker_master";
$notification_data = array();
$curr_date_time  = date("Y-m-d H:i:s");
$img_dir = "profile_image/";
$img_url = $server_url1."profile_image/";
$mime_type_array = array("image/jpeg", "image/png","image/jpg");
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
$user_type = $_REQUEST["user_type"] ? addslashes(trim($_REQUEST["user_type"])) : "";
$new_profile_image_name = $_FILES["profile_image"]["name"];
if($the_id!="" && $user_type!="" && $new_profile_image_name!=""){

$new_profile_image_type = $_FILES["profile_image"]["type"];
$new_profile_image_size = $_FILES["profile_image"]["size"];
$new_profile_image_tmp = $_FILES["profile_image"]["tmp_name"];


if(!in_array($new_profile_image_type,$mime_type_array)){
$res_data = array("process_status"=>"NO","process_message"=>"Please select an image file(PNG,JPG).");
}else{
if($user_type="dealer"){
	$sql_usr = "select `profile_image`,`customer_code`,`dns_customer_code` from $customer_master where `customer_code`='$the_id'";
$res_usr = mysql_query($sql_usr);
$totres_usr = mysql_num_rows($res_usr);
if($totres_usr>0){
	$row_usr=mysql_fetch_assoc($res_usr);
	$old_profile_image = $row_usr["profile_image"] ? trim($row_usr["profile_image"]) : "";
	
	$new_profile_image_name = str_replace(" ","_",$new_profile_image_name);
	$new_profile_image_name = str_replace("-","_",$new_profile_image_name);		
	$new_profile_image_name = "dealer_".time()."_".$new_profile_image_name;
				
	$file_up = move_uploaded_file($new_profile_image_tmp, $img_dir.$new_profile_image_name);
	if($file_up){
		$sql_usr_upd = "update $customer_master set `profile_image`='$new_profile_image_name' where `customer_code`='$the_id'";
		$res_usr_upd = mysql_query($sql_usr_upd);
		if($old_profile_image!=""){
			if(file_exists($img_dir.$old_profile_image)){
				unlink($img_dir.$old_profile_image);
			}
		}
		$the_profile_image_url = $img_url.$new_profile_image_name;
		$res_data = array("process_status"=>"YES","process_message"=>"Profile image successfully updated.","the_profile_image_url"=>$the_profile_image_url);
	}else{
		$res_data = array("process_status"=>"NO","process_message"=>"Failed to update profile image.");	
	}	
}else{
$res_data = array("process_status"=>"NO","process_message"=>"The dealer details doesn't exist.");	
}
}else if($user_type="broker"){
	$sql_usr = "select `profile_image`,`broker_id`,`dns_broker_id` from $broker_master where `broker_id`='$the_id'";
$res_usr = mysql_query($sql_usr);
$totres_usr = mysql_num_rows($res_usr);
if($totres_usr>0){
	$row_usr=mysql_fetch_assoc($res_usr);
	$old_profile_image = $row_usr["profile_image"] ? trim($row_usr["profile_image"]) : "";
	
	$new_profile_image_name = str_replace(" ","_",$new_profile_image_name);
	$new_profile_image_name = str_replace("-","_",$new_profile_image_name);		
	$new_profile_image_name = "broker_".time()."_".$new_profile_image_name;
				
	$file_up = move_uploaded_file($new_profile_image_tmp, $img_dir.$new_profile_image_name);
	if($file_up){
		$sql_usr_upd = "update $broker_master set `profile_image`='$new_profile_image' where `broker_id`='$the_id'";
		$res_usr_upd = mysql_query($sql_usr_upd);
		if($old_profile_image!=""){
			if(file_exists($img_dir.$old_profile_image)){
				unlink($img_dir.$old_profile_image);
			}
		}
		$the_profile_image_url = $img_url.$new_profile_image_name;
		$res_data = array("process_status"=>"YES","process_message"=>"Profile image successfully updated.","the_profile_image_url"=>$the_profile_image_url);
	}else{
		$res_data = array("process_status"=>"NO","process_message"=>"Failed to update profile image.");	
	}	
}else{
$res_data = array("process_status"=>"NO","process_message"=>"The dealer details doesn't exist.");	
}
}else{
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.");
}

}

}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"All fields are mandatory.");
}	
echo json_encode($res_data);
mysql_close();
?>
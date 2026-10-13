<?php
include "web_check.php";
include "star_connection.php";
$customer_broker_relation = "customer_broker_relation";
$broker_master = "broker_master";
$employee_master = "employee_master";
$customer_master = "customer_master";
$changepassword = "changepassword";
$sp_broker_permision = "sp_broker_permision";
$selected_sp_broker_permision = "selected_sp_broker_permision";
$the_menu_master_array = array();
$the_menu_selected_array = array();
$sqlmm = "SELECT `id`, `permision_type`, `permision_name`, `status` FROM $sp_broker_permision WHERE `status` ='Y' order by `permision_name` asc";
$resmm = mysql_query($sqlmm);
$totresmm = mysql_num_rows($resmm);
if($totresmm>0){
	while($rowmm=mysql_fetch_assoc($resmm)){
		$permision_id = $rowmm["id"];
		$permision_type = $rowmm["permision_type"];
		$permision_name = $rowmm["permision_name"];
		$the_menu_master_array[] = array("permision_id"=>$permision_id,"permision_type"=>$permision_type,"permision_name"=>$permision_name);
	}
}
if(@isset($_GET["submsg"]) && $_GET["submsg"]!=""){
	$submsg =$_GET["submsg"];
}else{
	$submsg ="";
}
$add_page_name = "permision_sp_broker_list.php";
$page_name = "sp_broker_list.php";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

if(@$_POST["update"]=="Update"){
$upthe_brokerid = $_POST["upthe_brokerid"] ? addslashes(trim($_POST["upthe_brokerid"])) : "";
$the_pno = $_POST["the_pno"] ? trim($_POST["the_pno"]) : "1";
$asupd_user_admin_menu_menu = array();
$asupd_user_admin_menu_menu = @$_POST["user_admin_menu"] ? $_POST["user_admin_menu"] : array();
		$asupd_user_admin_menu_menu_new = array_unique($asupd_user_admin_menu_menu);
		if(count($asupd_user_admin_menu_menu_new)>0){
		$asupd_user_admin_menu_menu_str = implode("','",$asupd_user_admin_menu_menu_new);
		$sqldtlfm = "delete from $selected_sp_broker_permision where `user_id`='$upthe_brokerid' and `permision_id` not in('".$asupd_user_admin_menu_menu_str."')";
		$resdtlfm = mysql_query($sqldtlfm);
		}else{
		$sqldtlfm = "delete from $selected_sp_broker_permision where `user_id`='$upthe_brokerid'";
		$resdtlfm = mysql_query($sqldtlfm);
		}
			//echo"<pre>";print_r($asupd_user_admin_menu_menu_new);die;

		foreach($asupd_user_admin_menu_menu_new as $asupd_user_admin_menu_menu_new_val){
			$the_fmi = addslashes(trim($asupd_user_admin_menu_menu_new_val));
			$sqlckfm = "select `permision_id`,`user_id` from $selected_sp_broker_permision where `user_id`='$upthe_brokerid' and `permision_id`='$the_fmi'";
			//echo"<pre>";print_r($sqlckfm);die;

			$resckfm = mysql_query($sqlckfm);
			$totresckfm = mysql_num_rows($resckfm);
				if($totresckfm==0){
					$sql_infm = "insert into $selected_sp_broker_permision (`user_id`,`permision_id`,`update_at`) values('$upthe_brokerid','".$the_fmi."',now())";
					//echo"<pre>";print_r($sql_infm);die;

					$res_infm = mysql_query($sql_infm);
				}else{
					$sql_upfm = "update $selected_sp_broker_permision set `update_at`=now() where `user_id`='$upthe_brokerid' and `permision_id`='$the_fmi'";
					$res_upfm = mysql_query($sql_upfm);
				}
			}
			//echo"<pre>";print_r($sql_upfm);die;

		$submsg = 'Permission successfully updated.';
		header("location:$add_page_name?thebrokerid=$upthe_brokerid&paged=$the_pno&submsg=".$submsg);
}

if(@isset($_GET["thebrokerid"]) && $_GET["thebrokerid"]!=""){
	$thebrokerid = $_GET["thebrokerid"] ? trim($_GET["thebrokerid"]) : "";
	$sql8 = "select * from $broker_master where `broker_id`='$thebrokerid'";
	$res8 = mysql_query($sql8);
	$totres8 = mysql_num_rows($res8);
	if($totres8>0){
		$row1 = mysql_fetch_assoc($res8);
		$dns_broker_id = $row1["dns_broker_id"];
		$broker_name = $row1["broker_name"];
		$contact_person = $row1["contact_person"];
		$phone_no = $row1["phone_no"];
		$mail_id = $row1["mail_id"] ? trim($row1["mail_id"]) : "";
		$phone_no = $row1["phone_no"] ? trim($row1["phone_no"]) : "";
		$app_version = $row1["app_version"];
		
		$sqlfm = "select `permision_id`,`user_id` from $selected_sp_broker_permision where `user_id`='$thebrokerid'";
	$resfm = mysql_query($sqlfm);
	$totresfm = mysql_num_rows($resfm);
	if($totresfm>0){
	while($rowfm=mysql_fetch_array($resfm)){
	$permision_idfm = $rowfm["permision_id"];
	$the_menu_selected_array[] = $permision_idfm;
	}
	}
		
	}else{
header("location:".$page_name);
	}	
}else{
header("location:".$page_name);	
}
 //print_r($the_menu_selected_array);

include "web_header.php";
?>
<script type="text/javascript">
jQuery(function () {

	
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
                          <h2>Permission Sales Promoter &nbsp;&nbsp;&nbsp; <?php if($submsg!=""){ echo $submsg;}?></h2>
                        </div>
                        <div class="body">

 <div class="table-responsive">
 <form action="" method="POST" enctype="multipart/form-data">
<div class="row clearfix" style="margin:0px;">
<div class="col-sm-12">
    <div class="form-group">
    <label for="dealer_id">SP&nbsp;ID : <?php echo $dns_broker_id;?></label>
    </div>
    <div class="form-group">
    <label for="dealer_name">Name : <?php echo $broker_name;?></label>
    </div>
     <div class="form-group">
    <label for="active_status">Contact Person : <?php echo $contact_person;?></label>
    </div>

<div class="form-group">
<label class="Select Permission">Select Permission  &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="btn bg-red waves-effe select_all_cls">Select All</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="btn bg-red waves-effe deselect_all_cls">Deselect All</a></label>
<div class="form-line">
<select name="user_admin_menu[]" id="user_admin_menu" class="chosen-select user_admin_menu" multiple data-placeholder="Choose permission">
<?php
if(count($the_menu_master_array)>0){
	foreach($the_menu_master_array as $the_menu_master_array_val){
		$get_ftr_mn_id = $the_menu_master_array_val["permision_id"];
		$get_ftr_mn_nm = $the_menu_master_array_val["permision_name"]; ?>
	<option <?php if(in_array($get_ftr_mn_id,$the_menu_selected_array)){ ?> selected="selected" <?php } ?> value="<?php echo $get_ftr_mn_id;?>"><?php echo $get_ftr_mn_nm;?></option>
	<?php }
}
?>
</select>
</div>
</div>
<div class="form-group" style="text-align:center;">
       <?php if($thebrokerid!=""){ ?>
    <input type="hidden" name="upthe_brokerid" value="<?php echo $thebrokerid;?>" />
    <input type="hidden" name="the_pno" value="<?php echo $page;?>" />
	<input type="submit" class="btn bg-red waves-effect" name="update" style="margin-bottom:10px;" value="Update" />
	<?php } ?>
    <a href="<?php echo $page_name."?paged=".$page;?>" class="btn bg-red waves-effect" style="margin-left: 20px;margin-bottom:10px;">Back To Sales Promoter List</a>
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
	jQuery('#user_admin_menu').chosen({width:"100%",no_results_text:'Oops, no permission found!'});
	
	jQuery(".select_all_cls").click(function(){
jQuery('#user_admin_menu option').prop('selected', true);  
jQuery('#user_admin_menu').trigger('chosen:updated');
});
jQuery(".deselect_all_cls").click(function(){
jQuery('#user_admin_menu option:selected').removeAttr('selected'); 
jQuery('#user_admin_menu').trigger('chosen:updated');
});
});
</script>
<?php
include "web_footer.php";
mysql_close();
?>

<?php
include "web_check.php";
include "star_connection.php";
$customer_broker_relation = "customer_broker_relation";
$broker_master = "broker_master";
$employee_master = "employee_master";
$customer_master = "customer_master";
$changepassword = "changepassword";
$menu_master = "menu_master";
$selected_menu_for_user = "selected_menu_for_user";
$the_menu_master_array = array();
$the_menu_selected_array = array();
$sqlmm = "select `menu_id`,`menu_name` from $menu_master order by `menu_name` asc";
$resmm = mysql_query($sqlmm);
$totresmm = mysql_num_rows($resmm);
if($totresmm>0){
	while($rowmm=mysql_fetch_assoc($resmm)){
		$menu_idmm = $rowmm["menu_id"];
		$menu_namemm = $rowmm["menu_name"];
		$the_menu_master_array[] = array("menu_id"=>$menu_idmm,"menu_name"=>$menu_namemm);
	}
}
if(@isset($_GET["submsg"]) && $_GET["submsg"]!=""){
	$submsg =$_GET["submsg"];
}else{
	$submsg ="";
}
$add_page_name = "edit_sp_broker_list.php";
$page_name = "sp_broker_list.php";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

if(@$_POST["update"]=="Update"){
$upthe_brokerid = $_POST["upthe_brokerid"] ? addslashes(trim($_POST["upthe_brokerid"])) : "";
$emp_phone = $_POST["emp_phone"] ? addslashes(trim($_POST["emp_phone"])) : "";
$the_pno = $_POST["the_pno"] ? trim($_POST["the_pno"]) : "1";
$asupd_user_admin_menu_menu = array();
$asupd_user_admin_menu_menu = $_POST["user_admin_menu"];
	if($emp_phone==''){
		$submsg = 'Please enter phone number.';
		$res_colour = 2;
	}else{
		
$sql8 = "select `broker_id` from $broker_master where `phone_no`='$emp_phone' and `broker_id`!='$upthe_brokerid'";
	$res8 = mysql_query($sql8);
	$totres8 = mysql_num_rows($res8);
	if($totres8>0){
	$submsg = 'Phone number already exist. Please use another number.';
	header("location:$add_page_name?thebrokerid=$upthe_brokerid&paged=$the_pno&submsg=".$submsg);
	}else{
$sql5 = "update $broker_master set `phone_no`='$emp_phone' where `broker_id`='$upthe_brokerid'";
$res5 = mysql_query($sql5);
$submsg = 'Phone number successfully updated.';

		$asupd_user_admin_menu_menu_new = array();
		$asupd_user_admin_menu_menu_new = $asupd_user_admin_menu_menu;
		$asupd_user_admin_menu_menu_str = implode("','",$asupd_user_admin_menu_menu);
		$sqldtlfm = "delete from $selected_menu_for_user where `user_id`='$upthe_brokerid' and `menu_id` not in('".$asupd_user_admin_menu_menu_str."')";
		$resdtlfm = mysql_query($sqldtlfm);
		foreach($asupd_user_admin_menu_menu_new as $asupd_user_admin_menu_menu_new_val){
			$the_fmi = $asupd_user_admin_menu_menu_new_val;
			$sqlckfm = "select `menu_id`,`user_id` from $selected_menu_for_user where `user_id`='$upthe_brokerid' and `menu_id`='$the_fmi'";
			$resckfm = mysql_query($sqlckfm);
			$totresckfm = mysql_num_rows($resckfm);
				if($totresckfm==0){
					$sql_infm = "insert into $selected_menu_for_user (`user_id`,`menu_id`) values('$upthe_brokerid','".$the_fmi."')";
					$res_infm = mysql_query($sql_infm);
				}
			}
		header("location:$add_page_name?thebrokerid=$upthe_brokerid&paged=$the_pno&submsg=".$submsg);

		}		
	
	}
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
		
		$sqlfm = "select `menu_id`,`user_id` from $selected_menu_for_user where `user_id`='$thebrokerid'";
	$resfm = mysql_query($sqlfm);
	$totresfm = mysql_num_rows($resfm);
	if($totresfm>0){
	while($rowfm=mysql_fetch_array($resfm)){
	$menu_idfm = $rowfm["menu_id"];
	$the_menu_selected_array[] = $menu_idfm;
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
                          <h2>Edit Sales Promoter &nbsp;&nbsp;&nbsp; <?php if($submsg!=""){ echo $submsg;}?></h2>
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
        <label for="phone">Mobile</label>
        <div class="form-line">
        <input type="text" name="emp_phone" class="form-control emp_phone" id="emp_phone" value="<?php echo $phone_no;?>"  />
        </div>
    </div>
<div class="form-group">
<label for="active_status">Email : <?php echo $mail_id;?></label>
</div>
<div class="form-group">
<label for="active_status">App Version : <?php echo $app_version;?></label>
</div>
	
<div class="form-group">
<label class="Select Menus">Select Menus &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="btn bg-red waves-effe select_all_cls">Select All</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="btn bg-red waves-effe deselect_all_cls">Deselect All</a></label>
<div class="form-line">
<select name="user_admin_menu[]" id="user_admin_menu" class="chosen-select user_admin_menu" multiple data-placeholder="Choose menus">
<?php
if(count($the_menu_master_array)>0){
	foreach($the_menu_master_array as $the_menu_master_array_val){
		$get_ftr_mn_id = $the_menu_master_array_val["menu_id"];
		$get_ftr_mn_nm = $the_menu_master_array_val["menu_name"]; ?>
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
	<input type="submit" class="btn bg-red waves-effect srch_btn" name="update" style="margin-bottom:10px;" value="Update" />
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
	var imgs = '<img src="images/ajax-loader.gif"/>';
	var done_img = '<img src="images/success_tick.png"/>';
	jQuery('#user_admin_menu').chosen({width:"100%",no_results_text:'Oops, no menu found!'});
	
	jQuery(".select_all_cls").click(function(){
jQuery('#user_admin_menu option').prop('selected', true);  
jQuery('#user_admin_menu').trigger('chosen:updated');
});
jQuery(".deselect_all_cls").click(function(){
jQuery('#user_admin_menu option:selected').removeAttr('selected'); 
jQuery('#user_admin_menu').trigger('chosen:updated');
});
	
	jQuery(".clemply").click(function(){
		var ancr_elmnt = jQuery(this);
		var clemplyid = ancr_elmnt.attr("clemplyid");
		if(clemplyid!=""){
			var theldrid = "ca_ldr_"+clemplyid;
			var for_loader = jQuery("#"+theldrid);
			for_loader.html(imgs);
			jQuery.ajax({
			url: 'ajax_clear_allocation_by_emp_id.php',
			type: 'post',
			dataType: "JSON",
			data: "clemplyid="+clemplyid,
			success: function(response){
			if(response.process_status=="YES"){
				ancr_elmnt.html("--");
			for_loader.html(done_img);
			setTimeout(function (){
			for_loader.html("");
			},3000);
			}else{
			for_loader.html("");
			alert(response.process_message);
			}
			}
			});
		}
	});
	
	
	
	jQuery(".srch_btn").click(function(){
		var srch_dlr_dtls = jQuery("#srch_dlr_dtls").val();
		var sl_dlr_actdat = jQuery("#sl_dlr_actdat").val();
		var sl_dlr_alocated_type = jQuery("#sl_dlr_alocated_type").val();		
		var qstring ="";
		var amp = "";
		if(srch_dlr_dtls!="" || sl_dlr_actdat!="" || sl_dlr_alocated_type!=""){
		if(srch_dlr_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}else{
				qstring = qstring+"srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}
		}
		if(sl_dlr_actdat!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_dlr_actdat="+sl_dlr_actdat;
			}else{
				qstring = qstring+"sl_dlr_actdat="+sl_dlr_actdat;
			}
		}
		if(sl_dlr_alocated_type!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_dlr_alocated_type="+sl_dlr_alocated_type;
			}else{
				qstring = qstring+"sl_dlr_alocated_type="+sl_dlr_alocated_type;
			}
		}
		  if(qstring!=""){
			 qstring = "?"+qstring; 
		  }
		window.location = "dealer_list.php"+qstring;
		}else{
			alert("Please select atleast one field to search.");
		}
	});
	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "dealer_list.php";
	});
});
</script>
<?php
include "web_footer.php";
mysql_close();
?>
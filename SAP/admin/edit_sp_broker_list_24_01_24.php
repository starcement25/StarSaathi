<?php
include "web_check.php";
include "star_connection.php";
$customer_broker_relation = "customer_broker_relation";
$broker_master = "broker_master";
$employee_master = "employee_master";
$customer_master = "customer_master";
$changepassword = "changepassword";
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
		
	}else{
header("location:".$page_name);
	}	
}else{
header("location:".$page_name);	
}


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
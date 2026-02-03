<?php
include "web_check.php";
include "star_connection.php";
$table_name = "employee_master";
$changepassword = "changepassword";
$branch_master = "branch_master";
$notification_message = "notification_message";
$img_dir = "noty_images/";
$server_url1 = "http://" . $_SERVER['SERVER_NAME']."/";
$image_link_url = $server_url1."admin/noty_images/";
$mime_type_array = array("image/jpeg", "image/png","image/jpg");
$mime_type_array_pdf = array("application/pdf");

if(isset($_GET["n_msg"]) and $_GET["n_msg"]!=""){
$n_msg = $_GET["n_msg"];
}else{
$n_msg = "";
}
if($_POST["snd_btn"]=="Send Notification"){
$the_noty_title = $_POST["noty_title"] ? addslashes(trim($_POST["noty_title"])) : "";
	$the_noty_msg = $_POST["noty_msg"] ? addslashes(trim($_POST["noty_msg"])) : "";
	$the_astn_branch_code = $_POST["astn_branch_code"] ? $_POST["astn_branch_code"] : "";
	$the_radio_file_type = $_POST["radio_file_type"] ? addslashes(trim($_POST["radio_file_type"])) : "NONE";
	if($the_radio_file_type=="IMAGE"){
$nimage_file_name = $_FILES["nimage_file"]["name"];
$nimage_file_type = $_FILES["nimage_file"]["type"];
$nimage_file_size = $_FILES["nimage_file"]["size"];
$nimage_file_tmp = $_FILES["nimage_file"]["tmp_name"];
	}else if($the_radio_file_type=="PDF"){
$nimage_file_name = $_FILES["npdf_file"]["name"];
$nimage_file_type = $_FILES["npdf_file"]["type"];
$nimage_file_size = $_FILES["npdf_file"]["size"];
$nimage_file_tmp = $_FILES["npdf_file"]["tmp_name"];		
	}else{
$nimage_file_name = "";
$nimage_file_type = "";
$nimage_file_size = "";
$nimage_file_tmp = "";	
	}
	if($the_noty_title==""){
		$n_msg = "Please enter title";
	}else if($the_noty_msg==""){
		$n_msg = "Please enter message";
	}else{
		
		if(count($the_astn_branch_code)>0){
			$the_astn_branch_code_str = implode(",",$the_astn_branch_code);
		}else{
			$the_astn_branch_code_str = "ALL";
		}
		$curr_date_time = date("Y-m-d H:i:s");
		if($the_radio_file_type=="IMAGE"){
				if($nimage_file_name!=""){
				if(!in_array($nimage_file_type,$mime_type_array)){
				$n_msg = 'Please select an image file(PNG,JPG).';
				}else{
				$nimage_file_name = str_replace(" ","_",$nimage_file_name);
				$nimage_file_name = str_replace("-","_",$nimage_file_name);		
				$new_file_name = "icon_".time()."_".$nimage_file_name;
				$file_up = move_uploaded_file($nimage_file_tmp, $img_dir.$new_file_name);
				$sql_in = "insert into $notification_message (`title`,`message`,`image_name`,`file_type`,`branch_code`,`date_time`) values('$the_noty_title','$the_noty_msg','$new_file_name','$the_radio_file_type','$the_astn_branch_code_str','$curr_date_time')";
				$res_in = mysql_query($sql_in);
				if($res_in){
				$new_gen_noty_id = mysql_insert_id();
				$n_msg = 'Notification sending process has been staretd successfully.';				
				}else{
				$n_msg = 'Something went wrong. Please try later.';
				}
				}
				}else{
				$n_msg = 'Please select image file.';
				}
		}else if($the_radio_file_type=="PDF"){
			if($nimage_file_name!=""){
			if(!in_array($nimage_file_type,$mime_type_array_pdf)){
			$n_msg = 'Please select an PDF file.';
			}else{
			$nimage_file_name = str_replace(" ","_",$nimage_file_name);
			$nimage_file_name = str_replace("-","_",$nimage_file_name);		
			$new_file_name = "pdf_".time()."_".$nimage_file_name;
			$file_up = move_uploaded_file($nimage_file_tmp, $img_dir.$new_file_name);
			$sql_in = "insert into $notification_message (`title`,`message`,`image_name`,`file_type`,`branch_code`,`date_time`) values('$the_noty_title','$the_noty_msg','$new_file_name','$the_radio_file_type','$the_astn_branch_code_str','$curr_date_time')";
			$res_in = mysql_query($sql_in);
			if($res_in){
			$new_gen_noty_id = mysql_insert_id();
			$n_msg = 'Notification sending process has been staretd successfully.';				
			}else{
			$n_msg = 'Something went wrong. Please try later.';
			}
			}
			}else{
			$n_msg = 'Please select PDF file.';
			}
		}else{
			$sql_in = "insert into $notification_message (`title`,`message`,`branch_code`,`date_time`) values('$the_noty_title','$the_noty_msg','$the_astn_branch_code_str','$curr_date_time')";
			$res_in = mysql_query($sql_in);
			if($res_in){
			$new_gen_noty_id = mysql_insert_id();
			$n_msg = 'Notification sending process has been staretd successfully.';
			}else{
			$n_msg = 'Something went wrong. Please try later.';
			}
		}
				
	}
}
function show_branch_name_by_id($brid){
	$brnm = "";
	$branch_master = "branch_master";
	$brid = $brid ? addslashes(trim($brid)) : "";
	if($brid!=""){
		$brsql2 = "select `branch_name` from $branch_master where `branch_code`='$brid'";
		$brres2 = mysql_query($brsql2);
		$total_brres2 = mysql_num_rows($brres2);
		if($total_brres2>0){
			$brrow2 = mysql_fetch_assoc($brres2);
			$brnm = $brrow2["branch_name"];
		}
	}
	return $brnm;
}

function show_multiple_branch_name_by_ids($brids){
	$brnms = "";
	$brids_arr = array();
	$brmns_arr = array();
	$branch_master = "branch_master";
	$brids = $brids ? addslashes(trim($brids)) : "";
	if($brids!=""){
		$brids_arr = explode(",",$brids);
		$brids_str = implode("','",$brids_arr);
		$brsql2 = "select `branch_name` from $branch_master where `branch_code` in('".$brids_str."')";
		$brres2 = mysql_query($brsql2);
		$total_brres2 = mysql_num_rows($brres2);
		if($total_brres2>0){
			while($brrow2 = mysql_fetch_assoc($brres2)){
			$brmns_arr[] = $brrow2["branch_name"];
			}
			$brnms = implode(",",$brmns_arr);
		}
	}
	return $brnms;
}

$brsql = "select `branch_code`,`branch_name` from $branch_master where `acedns`='Y' order by `branch_name` asc";
$brres = mysql_query($brsql);
$total_brres = mysql_num_rows($brres);

$submsg = "";
$add_page_name = "send_notification.php";
$page_name = "send_notification.php";

$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/


/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select `id` from $notification_message";
$pgres = mysql_query($pgsql);
$total_pgres = mysql_num_rows($pgres);
$start_from = (($page-1)*$limit);
$prev = $page - 1;							//previous page is page - 1
$next = $page + 1;							//next page is page + 1
$lastpage = ceil($total_pgres/$limit);   //lastpage is = total pages / items per page, rounded up.
$lpm1 = $lastpage - 1;

/*---------PAGINATION RELATED CODE START----------*/

include "web_header.php";
if($new_gen_noty_id!=""){
/*
?>
<script type="text/javascript">
jQuery(function(){
var xhr2;
function call_bg_noty(the_noty_id){
xhr2 = jQuery.ajax({
url: 'ajax_call_bg_noty.php',
type: 'post',
dataType: 'json',
data: "the_noty_id="+the_noty_id,
success: function(response){			
},
timeout : 0
});
setTimeout(function(){
if(xhr2 && xhr2.readystate != 4){
xhr2.abort();
}
},5000);
}
	var the_noty_id = "<?php echo $new_gen_noty_id;?>";
	call_bg_noty(the_noty_id);
});
</script>
<?php 
*/
} ?>
<style>
.estarix_cls{
	color:#F00;
	margin-left:5px;
}
.title_err_cls,.msg_err_cls,.img_err_cls,.pdf_err_cls{
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
.n_image_upload_section,.n_pdf_upload_section{
	display:none;
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
                          <h2>NOTIFICATION</h2>
                        </div>
                        <div class="body" style="padding:20px;">
                        <form action="" method="post" enctype="multipart/form-data" id="send_notification_form" class="send_notification_form">

<div class="form-group">
<label class="Enter Title">Enter Title <span class="estarix_cls">*</span><span class="title_err_cls"></span></label>
<div class="form-line">
<input type="text" name="noty_title"  id="noty_title" class="form-control"  />
</div>
</div>
<div class="form-group">
<label class="form-label">Enter Message<span class="estarix_cls">*</span><span class="msg_err_cls"></span></label>
<div class="form-line">
<textarea name="noty_msg"  id="noty_msg"  cols="30" rows="2" class="form-control no-resize"></textarea>
</div>
</div>

<div class="form-group">
<label class="Send to Branch">Send To Multiple Branches</label>
<select class="form-control" id="astn_branch_code" name="astn_branch_code[]" style="padding-left:2px;" multiple  data-placeholder="Choose Multiple Branches...">
<?php
if($total_brres>0){
	while($brrow=mysql_fetch_assoc($brres)){
		$the_br_code = $brrow["branch_code"];
		$the_br_name = $brrow["branch_name"];?>
        <option value="<?php echo $the_br_code;?>"><?php echo $the_br_name." (".$the_br_code.")";?></option>
		<?php
	}
	
}
?>

</select>

</div>
<div class="form-group">
<label class="Select File">Select File</label>
<div class="demo-radio-button">
<input name="radio_file_type" id="radio_file_type_none" value="NONE" checked="checked" type="radio">
<label for="radio_file_type_none">NONE</label>
<input name="radio_file_type" id="radio_file_type_image" value="IMAGE" type="radio">
<label for="radio_file_type_image">IMAGE</label>
<input name="radio_file_type"  id="radio_file_type_pdf" value="PDF" type="radio">
<label for="radio_file_type_pdf">PDF</label>
</div>
</div>
<div class="form-group n_image_upload_section">
<label class="Select Image">Select Image<span class="estarix_cls">*</span><span class="img_err_cls"></span></label>
<input type="file" class="form-control" id="nimage_file" name="nimage_file" placeholder="Select Image file">
</div>
<div class="form-group n_pdf_upload_section">
<label class="Select PDF">Select PDF<span class="estarix_cls">*</span><span class="pdf_err_cls"></span></label>
<input type="file" class="form-control" id="npdf_file" name="npdf_file" placeholder="Select PDF file">
</div>

<input type="submit" class="btn btn-primary waves-effect snd_btn" name="snd_btn" id="snd_btn1"  value="Send Notification" />   
<span class="loaddr_msg" id="loaddr_msg"><?php if($n_msg!=""){ echo $n_msg;}?></span>
</form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Basic Examples -->
            <!-- Exportable Table -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                        <h2>Notification List (<?php echo $total_pgres;?>)&nbsp;&nbsp;&nbsp;</h2>
                        </div>
                        <div class="body" style="padding:20px;">
                        <?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                        <th>Title</th>
                                        <th>Message </th>
                                        <th>Image/PDF</th>
                                        <th>Sent to Branch</th>
                                        <th style="text-align:center;">Sending Count</th>
                                        <th>Sending Status</th>
                                        <th>Date Time</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                        <th>Title</th>
                                        <th>Message </th>
                                        <th>Image/PDF</th>
                                        <th>Sent to Branch</th>
                                        <th style="text-align:center;">Sending Count</th>
                                        <th>Sending Status</th>
                                        <th>Date Time</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select * from $notification_message order by `id` desc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$n_id = $row1["id"];
		$title = $row1["title"];
		$message = $row1["message"];
		$file_type = $row1["file_type"];
		$image_name = $row1["image_name"] ? trim($row1["image_name"]) : "";
		if($image_name!=""){
			if(file_exists($img_dir.$image_name)){
			$m_image_link = $image_link_url.$image_name;
			}else{
			$m_image_link ="";
			}
		}else{
			$m_image_link ="";
		}
		$branch_code = $row1["branch_code"] ? trim($row1["branch_code"]) : "";
		if($branch_code=="ALL"){
			$branch_nm = "ALL";
		}else{
			$branch_nm = show_multiple_branch_name_by_ids($branch_code);
		}
		
        $sending_count = $row1["sending_count"];
		$sending_status = $row1["status"];
		$date_time = $row1["date_time"];
?>
<tr>
<td><?php echo $title;?></td>
<td><?php echo $message;?></td>
<td><?php
if($file_type=="IMAGE"){
if($m_image_link!=""){ ?>
	<a class='show_image_pdf_scheme_inline' href="<?php echo $m_image_link;?>"><img src="<?php echo $m_image_link; ?>" style="width:50px;" /></a>
<?php }else{
	echo "NONE";
}
}else if($file_type=="PDF"){ 
if($m_image_link!=""){ ?>
	<a class='btn bg-red waves-effect show_image_pdf_scheme' href="<?php echo $m_image_link;?>">View PDF</a>
<?php }else{
	echo "NONE";
}}else{
echo "NONE";	
}
?></td>
<td><?php echo $branch_nm;?></td>
<td style="text-align:center;"><?php 
if($sending_status=="END"){
	echo $sending_count;
}else{
	?>
<span id="noty_curr_count_<?php echo $n_id;?>" class="noty_curr_count"><?php echo $sending_count;?></span>
<a href="javascript:void(0);" class="show_noty_sending_count" id="show_noty_sending_count_btn_<?php echo $n_id;?>" the_noty_id="<?php echo $n_id;?>"><img src="images/refresh_btn.png" /></a>
<span id="noty_count_loader_<?php echo $n_id;?>" class="noty_count_loader"></span>
<span class="clear_class"></span>
<?php 
}
?></td>
<td><?php 
if($sending_status=="END"){
echo $sending_status;
}else{ ?>
<span id="noty_curr_sts_<?php echo $n_id;?>"><?php echo $sending_status;?></span>
<?php }
?></td>
<td><?php echo $date_time;?></td>
</tr>
<?php
}

}else{
?>
<tr>
<td style="text-align:center" colspan="7">No notification found.</td>
</tr>
<?php
}
?>

</tbody>
</table>
                            </div>
<?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Exportable Table -->
        </div>
    </section>
<script type="text/javascript">
jQuery(function(){
jQuery('#astn_branch_code').chosen({width:"100%",no_results_text:'Oops, no branch found!',search_contains: true});
jQuery(".show_image_pdf_scheme").colorbox({iframe:true, width:"90%", height:"95%"});
jQuery(".show_image_pdf_scheme_inline").colorbox();

jQuery('input[name="radio_file_type"]').change(function(){
        var curr_f_type_val = jQuery(this).val();
		if(curr_f_type_val=="IMAGE"){
			jQuery(".n_image_upload_section").show();
			jQuery(".n_pdf_upload_section").hide();
		}else if(curr_f_type_val=="PDF"){
			jQuery(".n_pdf_upload_section").show();
			jQuery(".n_image_upload_section").hide();
		}else{
			jQuery(".n_image_upload_section").hide();
			jQuery(".n_pdf_upload_section").hide();
		}
    });

jQuery(".show_noty_sending_count").click(function(){
	var the_n_id = jQuery.trim(jQuery(this).attr("the_noty_id"));
	if(the_n_id!=""){
		var n_count_elmnt = jQuery("#noty_curr_count_"+the_n_id);
		var n_loader_elmnt = jQuery("#noty_count_loader_"+the_n_id);
		var show_noty_sending_count_btn = jQuery("#show_noty_sending_count_btn_"+the_n_id);
		var noty_curr_sts = jQuery("#noty_curr_sts_"+the_n_id);
		
		var img = '<img src="images/ajax-loader.gif">';
		jQuery(n_loader_elmnt).html(img);
	jQuery.ajax({
		url: 'ajax_show_noty_sending_count_and_status_by_id.php',
		type: 'post',
		dataType: 'json',
		data: "the_n_id="+the_n_id,
		success: function(response){
			if(response.process_sts=="YES"){
				jQuery(n_loader_elmnt).html("");
				var n_cr_sts = response.n_cr_sts;
				var n_cr_cnt = response.n_cr_cnt;
				jQuery(n_count_elmnt).html(n_cr_cnt);
				jQuery(noty_curr_sts).html(n_cr_sts);
				if(n_cr_sts=="END"){
					show_noty_sending_count_btn.hide();
					n_count_elmnt.css("float","none");
				}
			}else{
				jQuery(n_loader_elmnt).html("");
				alert(response.process_msg);
			}
			
		},
		timeout : 0
		});
	}
});



jQuery("form#send_notification_form").submit(function(){
	var noty_title = jQuery.trim(jQuery("#noty_title").val());
	var noty_msg = jQuery.trim(jQuery("#noty_msg").val());
	var radio_file_type = jQuery("input[name='radio_file_type']:checked").val();
	var nimage_file = jQuery.trim(jQuery("#nimage_file").val());
	var npdf_file = jQuery.trim(jQuery("#npdf_file").val());
	
	if(noty_title==""){
		jQuery(".title_err_cls").html("Please enter title.");
		jQuery("#noty_title").focus();
		setTimeout(function(){
			jQuery(".title_err_cls").html("");
		},5000);
		return false;
	}else if(noty_msg==""){
		jQuery(".msg_err_cls").html("Please enter message.");
		jQuery("#noty_msg").focus();
		setTimeout(function(){
			jQuery(".msg_err_cls").html("");
		},5000);
		return false;
	}else if(radio_file_type=="IMAGE" && nimage_file==""){
		jQuery(".img_err_cls").html("Please select image file.");
		jQuery("#nimage_file").focus();
		setTimeout(function(){
			jQuery(".img_err_cls").html("");
		},5000);
		return false;
	}else if(radio_file_type=="PDF" && npdf_file==""){
		jQuery(".pdf_err_cls").html("Please select PDF file.");
		jQuery("#npdf_file").focus();
		setTimeout(function(){
			jQuery(".pdf_err_cls").html("");
		},5000);
		return false;
	}else{
		return true;
	}
});

});
</script>
<?php
include "web_footer.php";
mysql_close();
?>
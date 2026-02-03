<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include "web_header.php";
include "web_check.php";
include "star_connection.php";
$lifting = "lifting";

$add_page_name = "lifting_report.php";
$page_name = "lifting_report.php";

/*---------PAGINATION RELATED CODE START----------*/
if ($_REQUEST[mode] == 'sublift') {
	$validation_date = $_POST['validn_date'];
	$start_date = $_POST['start_date'];
	$end_date = $_POST['end_date'];
	$brancharray = array();
	$res_msg = array();
	$brancharray = $_POST['branch'];
	//rancharray=array('B0050','B0060');
	foreach ($brancharray as $branchval) {
		$sql = "SELECT branch FROM  lifting_date_validation  WHERE branch='" . $branchval . "'";

		$rsquery = mysql_query($sql);
		$countquery = mysql_num_rows($rsquery);
		if ($countquery > 0) {
			$sqlup = "UPDATE lifting_date_validation SET validation_from='" . $start_date . "',validation_to='" . $end_date . "',
				validation_last_date='" . $validation_date . "',validation_create_date=CURRENT_TIMESTAMP() WHERE branch='" . $branchval . "'";
			mysql_query($sqlup);
		} else {
			echo $sqlinsert = "INSERT INTO lifting_date_validation SET validation_from='" . $start_date . "',			validation_to='" . $end_date . "',validation_last_date='" . $validation_date . "',
			validation_create_date=CURRENT_TIMESTAMP(),branch='" . $branchval . "',
					ip_address='" . $_SERVER['REMOTE_ADDR'] . "'";
			mysql_query($sqlinsert);
		}
	}
	echo "Lifting Successful";
}
?>
<script type="text/javascript">
	jQuery(function() {

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
						<h2 style="text-align: center;">Lifting Panel</h2>
						<span style="clear:both;display:block;"></span>
					</div>
					<div class="body">
						<center>
							<form action="lifting_validate_date_submission.php" method="post" id="multiple_validation_form" class="multiple_validation_form">
								<input type='hidden' name='mode' value="sublift">
								<div style="color:white;background-color:#F44336;"><span style="font-weight:bold; font-size:14px;">Lifting Panel Validation</span></div>
								<table width="100%" style="margin-top:20px;">
									<tbody>
										<tr>
											<td width="40%" align="right">
												<label for="branch" style="margin-right: 10px;">Branch:</label>
											</td>
											<td width="60%">
												<?php
												$sql_branch = "SELECT DISTINCT `branch_code`, `branch_name` FROM `branch_master` ORDER BY `branch_name` ASC";
												// echo $sql_branch;
												$res_branch = mysql_query($sql_branch);

												echo '<select name="branch[]" id="branch" value="Select Branches" class="chosen-select" style="width: 20%;" multiple>';
												echo '<option value="">Select</option>';

												while ($row_branch = mysql_fetch_array($res_branch)) {
													$branch_code = $row_branch['branch_code'];
													$branch_name = $row_branch['branch_name'];
													echo "<option value=\"$branch_code\">$branch_name</option>";
												}

												echo '</select>';
												?>
											</td>
										</tr>
										<tr>
											<td align="right">
												<label for="start_date" style="margin-right: 10px;">From Date:</label>
											</td>
											<td>
												<input type="date" name="start_date" id="start_date" style="width:20%;margin-top:10px;margin-bottom:10px;">
												&nbsp;&nbsp;&nbsp;&nbsp;
												<label for="end_date" style="margin-right: 10px;">To Date:</label>

												<input type="date" name="end_date" id="end_date" style="width: 20%;">
											</td>
											</td>

										</tr>
										<tr>
											<td align="right">
												<label for="validn_date" style="margin-right: 10px;">Validation Date:</label>
											</td>
											<td>
												<input type="date" name="validn_date" id="validn_date" style="width:20%;margin-bottom:10px;">
											</td>
										</tr>
										<tr>
										<tr>
											<td align="right">
												<label for="apprvl_date" style="margin-right: 10px;">Approval Date:</label>
											</td>
											<td>
												<input type="date" name="apprvl_date" id="apprvl_date" style="width:20%;margin-bottom:10px;">
											</td>
										</tr>
										<tr>
											<td colspan="4" align="center">
												<input type="submit" name="submit1" value="Submit" style="margin-bottom:10px;">
											</td>
										</tr>
									</tbody>
								</table>
								<div class="form-group n_pdf_upload_section">
									<div class="loaddr_msg" id="loaddr_msg">
										<span class="uploading" style="float:left;margin-left:10px;display:none;">
											<label>&nbsp;</label>
											<img src="images/uploading.gif" />
										</span>
										<span class="percent" style="float:left;margin-left:10px;"></span>
										<span style="clear:both;display:block;"></span>
									</div>
								</div>
							</form>
						</center>
						<div id="display"></div>
						<div id="display_details"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script type="text/javascript" src="ajax1.js"></script>
<script type="text/javascript">
	/*function display_result(){
	
		if(document.getElementById("branch").value.search(/\S/) == -1){
			alert('Please Select Branch');
			return false;
		}
		if(document.getElementById("start_date").value.search(/\S/) == -1){
		alert('Please choose From date');
		return false;
		}
		if(document.getElementById("end_date").value.search(/\S/) == -1){
		alert('Please choose To date');
		return false;
		}
		if(document.getElementById("validn_date").value.search(/\S/) == -1){
		alert('Please choose validation date');
		return false;
		}
		
		var branch = document.getElementById("branch").value;
		var validation_date = document.getElementById("validn_date").value;
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
	    alert(document.getElementsByName(branch[]).value);
		/*var multipledropdown = document.getElementsByName('branch[]');
		var valsemployee='';
		alert(multipledropdown);
		for(var i=0, n=multipledropdown.length;i<n;i++) {
			
		  if (multipledropdown[i].selected==true) 
		  {
			alert(multipledropdown[i].selected.value);
			valsemployee += ","+multipledropdown[i].selected.value;
		  }
		}*/


	/*var dateval = encodeURIComponent(document.getElementById("validation_date").value);
	var datearray = dateval.split("-");
	var newdate = datearray[0] + '-' + datearray[1] + '-' + datearray[2];
	if(newdate < month_select_validate)
	{
		alert('Please choose proper validation date');
		return false;
	}*/
	/*document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader"><br><br><center><div align="center" style="color:green; font-weight:bold;">Please Wait For Few Minutes...</div></center>';
		GenericAjaxFunction('lifting_validate_date_submission.php?validation_date='+validation_date+'&start_date='+start_date+'&end_date='+end_date+'&branch='+branch,'display',0);
	}*/
	jQuery(function() {
		jQuery('#branch').chosen({
			width: "100%",
			no_results_text: 'Oops, no branch found!',
			search_contains: true,
			placeholder_text_multiple: 'Select Branches'
		});

		var imgs = '<img src="images/ajax-loader.gif"/>';
		var done_img = '<img src="images/success_tick.png"/>';

		var percent = jQuery('.percent');
		jQuery('#multiple_validation_form').on('submit', function(e) {
			e.preventDefault();
			var branch = jQuery.trim(jQuery("#branch").val());
			var start_dt = jQuery.trim(jQuery("#start_date").val());
			var end_dt = jQuery.trim(jQuery("#end_date").val());
			var validation_date = jQuery.trim(jQuery("#validn_date").val());
			var apprvl_date = jQuery.trim(jQuery("#apprvl_date").val());


			/*if(document.getElementById("branch").value.search(/\S/) == -1){
					alert('Please Select Branch');
					return false;
				}
				if(document.getElementById("start_date").value.search(/\S/) == -1){
				alert('Please choose From date');
				return false;
				}
				if(document.getElementById("end_date").value.search(/\S/) == -1){
				alert('Please choose To date');
				return false;
				}
				if(document.getElementById("validn_date").value.search(/\S/) == -1){
				alert('Please choose validation date');
				return false;
				}
				
				var branch = document.getElementById("branch").value;
				var validation_date = document.getElementById("validn_date").value;
				var start_date = document.getElementById("start_date").value;
				var end_date = document.getElementById("end_date").value;*/
			if (branch == "") {
				//jQuery(".branch_err_cls").html("Please select branch.");
				alert('Please Select Branch');
				jQuery("#branch").focus();
				setTimeout(function() {
					//jQuery(".branch_err_cls").html("");
				}, 5000);
				return false;
			} else if (start_dt == "") {
				//jQuery(".date_err_cls").html("Please select start date.");
				alert('Please select From date.');
				jQuery("#start_dt").focus();
				setTimeout(function() {
					//jQuery(".date_err_cls").html("");
				}, 5000);
				return false;
			} else if (end_dt == "") {
				//jQuery(".date_err_cls").html("Please select end date.");
				alert('Please select end date.');
				jQuery("#end_dt").focus();
				setTimeout(function() {
					//jQuery(".date_err_cls").html("");
				}, 5000);
				return false;
			} 
			/*
			else if (validation_date == "") {
				//jQuery(".date_err_cls").html("Please select validation date.");
				alert('Please select validation date.');
				jQuery("#validation_date").focus();
				setTimeout(function() {
					//jQuery(".date_err_cls").html("");
				}, 5000);
				return false;
			} else if (apprvl_date == "") {
				//jQuery(".date_err_cls").html("Please select validation date.");
				alert('Please select approval date.');
				jQuery("#apprvl_date").focus();
				setTimeout(function() {
					//jQuery(".date_err_cls").html("");
				}, 5000);
				return false;
			} */
			else {
				jQuery(this).ajaxSubmit({
					uploadProgress: function(event, position, total, percentComplete) {
						var percentVal = percentComplete + '%';
						percent.html(percentVal);
					},
					beforeSubmit: function(e) {
						count = 0;
						exc_msg = "";
						mx_img_siz = 10;
						mx_sz_cnt = 0;
						ext_cnt = 0;

						jQuery(".percent").html(exc_msg);
						setTimeout(function() {
							jQuery('.percent').html("");
						}, 8000);
						//return false;
						//} else {
						jQuery('.uploading').show();
						//}				
						return true;
					},
					success: function(e) {
						jQuery('.uploading').hide();
					},
					error: function(e) {},
					resetForm: true,
					complete: function(xhr) {
						result = xhr.responseText;
						result = $.parseJSON(result);
						if (result.process_sts == "YES") {
							jQuery('#branch option:selected').removeAttr('selected');
							jQuery('#branch').trigger('chosen:updated');
							jQuery("#start_dt").val("");
							jQuery("#end_dt").val("");
							jQuery('.percent').html(result.process_msg);
							setTimeout(function() {
								jQuery('.percent').html("");
							}, 8000);
						} else {
							jQuery('.percent').html(result.process_msg);
							setTimeout(function() {
								jQuery('.percent').html("");
							}, 8000);
						}
					}
				});
			}


		});


	});
</script>
<?php
include "web_footer.php";
mysql_close();
?>
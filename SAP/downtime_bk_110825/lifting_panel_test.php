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
                          <h2>Lifting Panel- Test </h2>
                            
<span style="clear:both;display:block;"></span>
</div>
<div class="body">
<center><span style="font-weight:bold; font-size:14px;">Yellow Card Validation Customer Wise</span>
<table width="100%"><tbody><tr><td width="90%" align="center">
    <script type="text/javascript" src="ajax1.js"></script>

    <div id="display_data"><table id="criteria_tab" class="border" width="95%" style="border-collapse:collapse;border:1px solid #A92A61; margin-bottom:26px;padding-bottom:30px;">

<tbody>
	<tr class="TDHEAD">
		<td colspan="3" style="padding:10px;background-color:#F44336;color:white;" align="center">Select Criteria<br/></td>
</tr><tr></tr>
<tr>
	<td align="center" style="padding-top:10px;" width="30%"><td width="30%" style="text-align:center;">Zone: </td>
	<td width="30%"><select name="zone" id="zone" onchange="zone_state(this.value);"><option value="">Select</option><option value="all">All</option><option value="'BIHAR'">BIHAR</option><option value="'JHARKHAND'">JHARKHAND</option><option value="'NB1'">NB1</option><option value="'NB2'">NB2</option><option value="'NE1'">NE1</option><option value="'NE2'">NE2</option><option value="'NT'">NT</option><option value="'SOUTH BENGAL'">SOUTH BENGAL</option><option value="'UTTAR PRADESH'">UTTAR PRADESH</option></select></td>
</tr><br>
<tr>
	<td align="center" style="padding-top:10px;" width="30%">
	<td width="30%" style="text-align:center;">State: </td>
	<td id="state_select_div" width="30%" style="text-align:left"><select style="visibility:hidden;" name="state" id="state" onchange="state_emp(this.value);">
</select></td>
</td>
</tr><br>
<tr>
	<td align="center" style="padding-top:20px;" width="30%">
	<td width="30%" style="text-align:center;">Branch: </td>
	<td id="branch_select_div" width="30%" style="text-align:left;"><select name="branch" id="branch" style="visibility:hidden;" onchange="branch_saleaccess(this.value);"></select></td>
</td></tr>
<tr><td style="margin-left:300px;margin-right:200px;">
	<td style="padding:10px;text-align:center;">
From: <input type="date" name="start_date" id="start_date" style="width:70%;">
</td>
<td style="padding:10px;text-align:center;">
To: <input type="date" name="end_date" id="end_date" style="width:70%;">
</td>
</td>
</tr><br>
<tr><td style="margin-left:300px;">
	<td	align="center" style="padding-top:10px;padding-bottom:10px;">Validation Date: <input type="date" name="validn_date" id="validn_date" style="width:30%;"></td></tr>
</div></td></td></tr>

<tr><td colspan="2" align="right"><input type="submit" name="submit" value="Submit" onclick="display_result();"></td></tr></tbody></table></div>
</center>
<div id="display"></div>
<div id="display_details"></div>
    <script>

	function display_result(){
		if(document.getElementById("zone").value.search(/\S/) == -1){
			alert('Please Select Zone');
			return false;
		}
		if(document.getElementById("state").value.search(/\S/) == -1){
			alert('Please Select State');
			return false;
		}
		if(document.getElementById("branch").value.search(/\S/) == -1){
			alert('Please Select Branch');
			return false;
		}
		
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		var validn_date = document.getElementById("validn_date").value;

		var zone = document.getElementById("zone").value;
		var state = document.getElementById("state").value;
		var branch = document.getElementById("branch").value;
				
		/*var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		
		if(document.getElementById("start_date").value.search(/\S/) == -1 && document.getElementById("end_date").value.search(/\S/) == -1){
			alert("Please provide start date/end date");
			return false;
		}
		
		if(start_date>end_date){
			alert("Start date cannot be greater than end date");
			return false;
		}*/
		
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		// GenericAjaxFunction('yellowcard_qty_update_data.php?start_data='+start_data+'&zone='+zone+'&state='+state+'&branch='+branch+'&department='+department,'display',0);
		GenericAjaxFunction('lifting_panel_yellowcard.php?start_date=' + start_date + '&zone=' + zone + '&state=' + state + '&branch=' + branch + '&end_date=' + end_date + '&validn_date=' + validn_date, 'display', 0);

		
		if(document.getElementById("display").innerHTML != 'No Records Found')
			document.getElementById("print_export").hidden = false;
	}

	function zone_state(zone){

		if(document.getElementById("zone").value.search(/\S/) == -1)

			return false;

		var zone = encodeURIComponent(zone);

		document.getElementById("state_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_zone_related_data.php?zone='+zone+'&type=state','state_select_div',0);
		
	}

	

	function zone_branch(zone){

		if(document.getElementById("zone").value.search(/\S/) == -1)

			return false;

		var zone = encodeURIComponent(zone);

		document.getElementById("branch_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_zone_related_data.php?zone='+zone+'&type=branch','branch_select_div',0);

	}

	

	function zone_saleaccess(zone){

		if(document.getElementById("zone").value.search(/\S/) == -1)

			return false;

		var zone = encodeURIComponent(zone);

		document.getElementById("saleaccess_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_zone_related_data.php?zone='+zone+'&type=sale_access','saleaccess_select_div',0);

	}

	

	function zone_hq(zone){

		if(document.getElementById("zone").value.search(/\S/) == -1)

			return false;

		var zone = encodeURIComponent(zone);

		document.getElementById("hq_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_zone_related_data.php?zone='+zone+'&type=hq','hq_select_div',0);

	}

	

	function zone_designation(zone){

		if(document.getElementById("zone").value.search(/\S/) == -1)

			return false;

		var zone = encodeURIComponent(zone);

		document.getElementById("designation_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_zone_related_data.php?zone='+zone+'&type=designation','designation_select_div',0);

	}

	

	function zone_emp(zone){

		if(document.getElementById("zone").value.search(/\S/) == -1)

			return false;

		var zone = encodeURIComponent(zone);

		document.getElementById("emp_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_zone_related_data.php?zone='+zone+'&type=emp','emp_select_div',0);

	}

	

	

	

	function state_branch(state){

		if(document.getElementById("state").value.search(/\S/) == -1)

			return false;

			

		var state = encodeURIComponent(state);

		var zone = encodeURIComponent(document.getElementById("zone").value);

		document.getElementById("branch_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_state_related_data.php?state='+state+'&zone='+zone+'&type=branch','branch_select_div',0);

		

		
	}

	function branch_route(branch){

		if(document.getElementById("branch").value.search(/\S/) == -1)

			return false;

			

		var branch = encodeURIComponent(branch);

		var zone = encodeURIComponent(document.getElementById("zone").value);

		var state = encodeURIComponent(document.getElementById("state").value);

		document.getElementById("route_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_branch_related_data.php?state='+state+'&zone='+zone+'&branch='+branch+'&type=route','route_select_div',0);

		

		
	}

	function branch_cluster(branch){

		if(document.getElementById("branch").value.search(/\S/) == -1)

			return false;

			

		var branch = encodeURIComponent(branch);

		var zone = encodeURIComponent(document.getElementById("zone").value);

		var state = encodeURIComponent(document.getElementById("state").value);

		document.getElementById("cluster_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_branch_related_data.php?state='+state+'&zone='+zone+'&branch='+branch+'&type=cluster','cluster_select_div',0);

		

		
	}

	function cluster_route(cluster){

		if(document.getElementById("cluster").value.search(/\S/) == -1)

			return false;

			

		var cluster = encodeURIComponent(cluster);

		var branch = encodeURIComponent(document.getElementById("branch").value);

		var zone = encodeURIComponent(document.getElementById("zone").value);

		var state = encodeURIComponent(document.getElementById("state").value);

		document.getElementById("route_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_cluster_related_data.php?state='+state+'&zone='+zone+'&branch='+branch+'&cluster='+cluster+'&type=route','route_select_div',0);

		

		
	}

	function sale_access(state){

		if(document.getElementById("state").value.search(/\S/) == -1)

			return false;

		var state = encodeURIComponent(state);

		document.getElementById("saleaccess_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_state_related_data.php?state='+state+'&type=sale_access','saleaccess_select_div',0);

	}

	

	function state_hq(state){

		if(document.getElementById("state").value.search(/\S/) == -1)

			return false;

		var state = encodeURIComponent(state);

		document.getElementById("hq_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_state_related_data.php?state='+state+'&type=hq','hq_select_div',0);

	}

	

	function state_designation(state){

		if(document.getElementById("state").value.search(/\S/) == -1)

			return false;

		var state = encodeURIComponent(state);

		document.getElementById("designation_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_state_related_data.php?state='+state+'&type=designation','designation_select_div',0);

	}

	

	function state_emp(state){

		if(document.getElementById("state").value.search(/\S/) == -1)

			return false;

		var state = encodeURIComponent(state);

		document.getElementById("state_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_state_related_data.php?state='+state+'&type=emp','state_select_div',0);

	}

	

	function branch_saleaccess(branch){

		if(document.getElementById("branch").value.search(/\S/) == -1)

			return false;

			

		var branch = encodeURIComponent(branch);

		var zone = encodeURIComponent(document.getElementById("zone").value);

		var state = encodeURIComponent(document.getElementById("state").value);

		

		//alert(branch+zone+state);

		

		document.getElementById("saleaccess_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_branch_related_data.php?branch='+branch+'&zone='+zone+'&state='+state+'&type=sale_access','saleaccess_select_div',0);

		

		
	}

	

	function branch_hq(branch){

		if(document.getElementById("branch").value.search(/\S/) == -1)

			return false;

		var branch = encodeURIComponent(branch);

		document.getElementById("hq_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_branch_related_data.php?branch='+branch+'&type=hq','hq_select_div',0);

	}

	

	function branch_designation(branch){

		if(document.getElementById("branch").value.search(/\S/) == -1)

			return false;

		var branch = encodeURIComponent(branch);

		document.getElementById("designation_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_branch_related_data.php?branch='+branch+'&type=designation','designation_select_div',0);

	}

	

	function branch_emp(branch){

		if(document.getElementById("branch").value.search(/\S/) == -1)

			return false;

		var branch = encodeURIComponent(branch);

		document.getElementById("emp_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_branch_related_data.php?branch='+branch+'&type=emp','emp_select_div',0);

	}

	

	

	

	

	function saleaccess_hq(sale_access){

		if(document.getElementById("sale_access").value.search(/\S/) == -1)

			return false;

		var sale_access = encodeURIComponent(sale_access);

		document.getElementById("hq_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_saleaccess_related_data.php?sale_access='+sale_access+'&type=hq','hq_select_div',0);

	}

	

	function saleaccess_designation(sale_access){

		if(document.getElementById("sale_access").value.search(/\S/) == -1)

			return false;

		var sale_access = encodeURIComponent(sale_access);

		document.getElementById("designation_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_saleaccess_related_data.php?sale_access='+sale_access+'&type=designation','designation_select_div',0);

	}

	function saleaccess_level(sale_access){

		if(document.getElementById("sale_access").value.search(/\S/) == -1)

			return false;

		var sale_access = encodeURIComponent(sale_access);

		document.getElementById("level_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_saleaccess_related_data.php?sale_access='+sale_access+'&type=level','level_select_div',0);

	}

	

	function saleaccess_emp(sale_access){

		if(document.getElementById("sale_access").value.search(/\S/) == -1)

			return false;

			

		var sale_access = encodeURIComponent(sale_access);

		var branch = encodeURIComponent(document.getElementById("branch").value);

		var state = encodeURIComponent(document.getElementById("state").value);

		var zone = encodeURIComponent(document.getElementById("zone").value);

		

		document.getElementById("emp_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_saleaccess_related_data.php?sale_access='+sale_access+'&branch='+branch+'&state='+state+'&zone='+zone+'&type=emp','emp_select_div',0);

		

		
	}

	

	function level_emp(level){

		/*if(document.getElementById("level").value.search(/\S/) == -1)

			return false;*/

			

		var level = encodeURIComponent(level);

		var branch = encodeURIComponent(document.getElementById("branch").value);

		var state = encodeURIComponent(document.getElementById("state").value);

		var zone = encodeURIComponent(document.getElementById("zone").value);

		var sale_access = encodeURIComponent(document.getElementById("sale_access").value);

		

		//alert(level);

		//alert(sale_access);

		

		document.getElementById("emp_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_saleaccess_related_data.php?level='+level+'&sale_access='+sale_access+'&branch='+branch+'&state='+state+'&zone='+zone+'&type=levelemp','emp_select_div',0);

		

		
	}

	

	function hq_designation(hq){

		if(document.getElementById("hq").value.search(/\S/) == -1)

			return false;

			

		var hq_one = encodeURIComponent(hq);

		var zone = encodeURIComponent(document.getElementById("zone").value);

		var branch = encodeURIComponent(document.getElementById("branch").value);

		var state = encodeURIComponent(document.getElementById("state").value);

		

		document.getElementById("designation_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_hq_related_data.php?hq='+hq_one+'&zone='+zone+'&branch='+branch+'&state='+state+'&type=designation','designation_select_div',0);

	}

	

	function hq_emp(hq){

		if(document.getElementById("hq").value.search(/\S/) == -1)

			return false;

		var hq = encodeURIComponent(hq);

		document.getElementById("emp_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_hq_related_data.php?hq='+hq+'&type=emp','emp_select_div',0);

	}

	function designation_emp(designation){

		//alert(designation);

		if(document.getElementById("designation").value.search(/\S/) == -1)

			return false;

		//var designation = encodeURIComponent(designation);		

		var zone = encodeURIComponent(document.getElementById("zone").value);

		var state = encodeURIComponent(document.getElementById("state").value);

		var hq = encodeURIComponent(document.getElementById("hq").value);

		var designation = encodeURIComponent(document.getElementById("designation").value);

		var branch = encodeURIComponent(document.getElementById("branch").value);

		document.getElementById("emp_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';

		GenericAjaxFunction('get_designation_related_data.php?zone='+zone+'&state='+state+'&hq='+hq+'&designation='+designation+'&branch='+branch+'&type=emp','emp_select_div',0);

	}

	

	</script>

    </td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></tbody></table>

</div>
<?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>
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

});
</script>
<?php
include "web_footer.php";
mysql_close();
?>
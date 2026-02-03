<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
                          <h2>Lifting Panel </h2>
                            
<span style="clear:both;display:block;"></span>
</div>
<div class="body">
<center><span style="font-weight:bold; font-size:14px;">Yellow Card Validation Customer Wise</span><br><br>
<table width="100%"><tbody><tr><td width="90%" align="center">
    <script type="text/javascript" src="ajax1.js"></script>

    <div id="display_data"><table id="criteria_tab" class="border" width="45%" style="border-collapse:collapse;border:1px solid #A92A61; padding:6px;">

<tbody><tr class="TDHEAD"><td colspan="2" align="center">Select Criteria</td></tr><tr><td align="right">Zone:</td><td><select name="zone" id="zone" onchange="zone_state(this.value);"><option value="">Select</option><option value="all">All</option><option value="'BIHAR'">BIHAR</option><option value="'JHARKHAND'">JHARKHAND</option><option value="'NB1'">NB1</option><option value="'NB2'">NB2</option><option value="'NE1'">NE1</option><option value="'NE2'">NE2</option><option value="'NT'">NT</option><option value="'SOUTH BENGAL'">SOUTH BENGAL</option><option value="'UTTAR PRADESH'">UTTAR PRADESH</option></select></td></tr>
<tr><td align="right">State:</td>
<td><div id="state_select_div"><select name="state" id="state" onchange="state_emp(this.value);">
<option value="">Select</option><option value="'AP','ARUNACHAL PRADESH','ASSAM','ASSAM-SILCHAR','BIHAR','JHARKHAND','MANIPUR','MEGHALAYA','MEGHALAYA-TURA','MIZORAM','NAGALAND','NON TRADE (BH)','NON TRADE (NE)','NON TRADE (WB)','NORTH BENGAL','SOUTH BENGAL','TRIPURA','UTTAR PRADESH'">All</option><option value="'AP'">AP</option><option value="'ARUNACHAL PRADESH'">ARUNACHAL PRADESH</option><option value="'ASSAM'">ASSAM</option><option value="'ASSAM-SILCHAR'">ASSAM-SILCHAR</option><option value="'BIHAR'">BIHAR</option><option value="'JHARKHAND'">JHARKHAND</option><option value="'MANIPUR'">MANIPUR</option><option value="'MEGHALAYA'">MEGHALAYA</option><option value="'MEGHALAYA-TURA'">MEGHALAYA-TURA</option><option value="'MIZORAM'">MIZORAM</option><option value="'NAGALAND'">NAGALAND</option><option value="'NON TRADE (BH)'">NON TRADE (BH)</option><option value="'NON TRADE (NE)'">NON TRADE (NE)</option><option value="'NON TRADE (WB)'">NON TRADE (WB)</option><option value="'NORTH BENGAL'">NORTH BENGAL</option><option value="'SOUTH BENGAL'">SOUTH BENGAL</option><option value="'TRIPURA'">TRIPURA</option><option value="'UTTAR PRADESH'">UTTAR PRADESH</option></select></div></td></tr>
<tr><td align="right">Branch:</td><td><div id="branch_select_div"><select name="branch" id="branch" onchange="branch_saleaccess(this.value);"><option value="">Select</option><option value="all">All</option><option value="B0038">DHANBAD</option><option value="B0039">TATA</option><option value="B0040">RANCHI</option><option value="B0041">DEOGHAR</option></select></div></td></tr><tr><td align="right">Department:</td><td><div id="saleaccess_select_div"></div></td></tr><tr><td align="right">Employee:</td><td><div id="emp_select_div"></div></td></tr><tr>
    <td align="right">Sub-Dealer:</td>
    <td><div id="subdealer_div"></div></td>
</tr><tr><td align="right">Month:</td><td align="left"><select name="month_select" id="month_select" onchange="clear_display_div();"><option value="">Select</option><option value="1900-02">Feb-1900</option><option value="2016-06">Jun-2016</option><option value="2016-09">Sep-2016</option><option value="2016-10">Oct-2016</option><option value="2016-11">Nov-2016</option><option value="2016-12">Dec-2016</option><option value="2017-01">Jan-2017</option><option value="2017-02">Feb-2017</option><option value="2017-03">Mar-2017</option><option value="2017-04">Apr-2017</option><option value="2017-05">May-2017</option><option value="2017-06">Jun-2017</option><option value="2017-07">Jul-2017</option><option value="2017-08">Aug-2017</option><option value="2017-09">Sep-2017</option><option value="2017-10">Oct-2017</option><option value="2017-11">Nov-2017</option><option value="2017-12">Dec-2017</option><option value="2018-01">Jan-2018</option><option value="2018-02">Feb-2018</option><option value="2018-03">Mar-2018</option><option value="2018-04">Apr-2018</option><option value="2018-05">May-2018</option><option value="2018-06">Jun-2018</option><option value="2018-07">Jul-2018</option><option value="2018-08">Aug-2018</option><option value="2018-09">Sep-2018</option><option value="2018-10">Oct-2018</option><option value="2018-11">Nov-2018</option><option value="2018-12">Dec-2018</option><option value="2019-01">Jan-2019</option><option value="2019-02">Feb-2019</option><option value="2019-03">Mar-2019</option><option value="2019-04">Apr-2019</option><option value="2019-05">May-2019</option><option value="2019-06">Jun-2019</option><option value="2019-07">Jul-2019</option><option value="2019-08">Aug-2019</option><option value="2019-09">Sep-2019</option><option value="2019-10">Oct-2019</option><option value="2019-11">Nov-2019</option><option value="2019-12">Dec-2019</option><option value="2020-01">Jan-2020</option><option value="2020-02">Feb-2020</option><option value="2020-03">Mar-2020</option><option value="2020-04">Apr-2020</option><option value="2020-05">May-2020</option><option value="2020-06">Jun-2020</option><option value="2020-07">Jul-2020</option><option value="2020-08">Aug-2020</option><option value="2020-09">Sep-2020</option><option value="2020-10">Oct-2020</option><option value="2020-11">Nov-2020</option><option value="2020-12">Dec-2020</option><option value="2021-01">Jan-2021</option><option value="2021-02">Feb-2021</option><option value="2021-03">Mar-2021</option><option value="2021-04">Apr-2021</option><option value="2021-05">May-2021</option><option value="2021-06">Jun-2021</option><option value="2021-07">Jul-2021</option><option value="2021-08">Aug-2021</option><option value="2021-09">Sep-2021</option><option value="2021-10">Oct-2021</option><option value="2021-11">Nov-2021</option><option value="2021-12">Dec-2021</option><option value="2022-01">Jan-2022</option><option value="2022-02">Feb-2022</option><option value="2022-03">Mar-2022</option><option value="2022-04">Apr-2022</option><option value="2022-05">May-2022</option><option value="2022-06">Jun-2022</option><option value="2022-07">Jul-2022</option><option value="2022-08">Aug-2022</option><option value="2022-09">Sep-2022</option><option value="2022-10">Oct-2022</option><option value="2022-11">Nov-2022</option><option value="2022-12">Dec-2022</option><option value="2023-01">Jan-2023</option><option value="2023-02">Feb-2023</option><option value="2023-03">Mar-2023</option><option value="2023-04">Apr-2023</option><option value="2023-05">May-2023</option><option value="2023-06">Jun-2023</option></select></td></tr><tr><td colspan="2" align="center"><div id="date_div" hidden="">

From:<input type="date" name="start_date" id="start_date" style="height:15px;">

To:<input type="date" name="end_date" id="end_date" style="height:15px;">

</div></td></tr><tr><td colspan="2" align="right"><input type="submit" name="submit" value="Submit" onclick="display_result();"></td></tr></tbody></table></div>
</center>
    <script>

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
<?php
include "web_check.php";
include "star_connection.php";
$customer_broker_relation = "customer_broker_relation";
$broker_master = "broker_master";
$employee_master = "employee_master";
$customer_master = "customer_master";
$changepassword = "changepassword";

$selected_menu_for_user = "selected_menu_for_user";
function get_selected_user_menus_by_uid($uid){
$qpnmvl = "Not set";
$submsg = '';
$nmsvalarr = array();
$menu_master = "menu_master";
$selected_menu_for_user = "selected_menu_for_user";
$uid = $uid ? trim($uid) : "" ;
	if($uid!=""){
	$sql1 = "select `menu_name` from $menu_master where `menu_id` in(select `menu_id` from $selected_menu_for_user where `user_id`='$uid') ";
	$res1 = mysql_query($sql1);
	$totres1 = mysql_num_rows($res1);
	if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
	$the_menu_name_sho = trim($row1["menu_name"]);
	$nmsvalarr[] = $the_menu_name_sho;
	}
	$qpnmvl = implode(",",$nmsvalarr);
	}	 
	}
return $qpnmvl;
}

$new_qry_string_filtered = "";
$export_filtered_str = "";
$srch_dlr_dtls = $_GET["srch_dlr_dtls"] ? addslashes(trim($_GET["srch_dlr_dtls"])) : "";
$whr_str = "";
$search_array = array("srch_dlr_dtls"=>$srch_dlr_dtls);
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_dlr_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand (`contact_person` like '%$search_array_val%' or `dns_broker_id` like '%$search_array_val%' or `broker_id` like '%$search_array_val%' or `broker_name` like '%$search_array_val%' or `phone_no` like '%$search_array_val%' ) ";
			$new_qry_string_filtered .= "&srch_dlr_dtls=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&srch_dlr_dtls=".$search_array_val;
			}else{
				$export_filtered_str .= "&srch_dlr_dtls=".$search_array_val;
			}
		}
	}
}

if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}
$add_page_name = "edit_sp_broker_list.php";
$page_name = "sp_broker_list.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select `broker_id` from $broker_master $new_whr_str";
$pgres = mysql_query($pgsql);
$total_pgres = mysql_num_rows($pgres);
$start_from = (($page-1)*$limit);
$prev = $page - 1;							//previous page is page - 1
$next = $page + 1;							//next page is page + 1
$lastpage = ceil($total_pgres/$limit);   //lastpage is = total pages / items per page, rounded up.
$lpm1 = $lastpage - 1;

/*---------PAGINATION RELATED CODE START----------*/


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
                          <h2>Sales Promoter List (<?php echo $total_pgres;?>)&nbsp;&nbsp;
<a href="export_sp_broker_data.php?get_type=all<?php echo $export_filtered_str;?>" class="btn bg-red waves-effe">Export&nbsp;Sales&nbsp;Promoter&nbsp;List</a> &nbsp;
 <?php /*?><a href="export_dealer.php?get_type=notloggedin" class="btn bg-red waves-effe">Export&nbsp;not&nbsp;loggedin&nbsp;dealer</a><?php */?>
                          </h2>
                            <div class="row clearfix">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_dlr_dtls" value="<?php echo $srch_dlr_dtls;?>" placeholder="Search Sales Promoter Details">
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_btn" >Search</button>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_reset_btn" >Reset</button>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">

    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">

    </div>
    
        
    </div>
<span style="clear:both;display:block;"></span>
                        </div>
                        <div class="body">
<?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>
 <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>SP&nbsp;ID</th>
                                            <th>Name</th>
                                            <th>Contact&nbsp;Person</th>
                                            <th>Mobile</th>
                                            <th>Email</th>
                                            <th>App&nbsp;Version</th>
											<th>Selected&nbsp;Menus</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>SP&nbsp;ID</th>
                                            <th>Name</th>
                                            <th>Contact&nbsp;Person</th>
                                            <th>Mobile</th>
                                            <th>Email</th>
                                            <th>App&nbsp;Version</th>
											<th>Selected&nbsp;Menus</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select * from $broker_master $new_whr_str order by `broker_name` asc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$broker_id = $row1["broker_id"];
		$dns_broker_id = $row1["dns_broker_id"];
		$broker_name = $row1["broker_name"];
		$contact_person = $row1["contact_person"];
		$mail_id = $row1["mail_id"] ? trim($row1["mail_id"]) : "";
		$phone_no = $row1["phone_no"] ? trim($row1["phone_no"]) : "";
		$app_version = $row1["app_version"];
		$menuname_for_the_user = get_selected_user_menus_by_uid($broker_id);
?>
<tr>
<td><?php echo $dns_broker_id;?></td>
<td><?php echo $broker_name;?></td>
<td><?php echo $contact_person;?></td>
<td><?php echo $phone_no;?></td>
<td><?php echo $mail_id;?></td>
<td><?php echo $app_version;?></td>
<td><?php echo $menuname_for_the_user;?></td>	
<td><a href="<?php echo $add_page_name."?thebrokerid=".$broker_id."&paged=".$page?>" class="btn bg-red waves-effect">Edit</a></td>
</tr>

<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="7">No data found.</td>
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
		var the_dns_emp_code = ancr_elmnt.attr("the_dns_emp_code");
		if(clemplyid!=""){
			var theldrid = "ca_ldr_"+the_dns_emp_code;
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
		var qstring ="";
		var amp = "";
		if(srch_dlr_dtls!=""){
		if(srch_dlr_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}else{
				qstring = qstring+"srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}
		}
		
		
		  if(qstring!=""){
			 qstring = "?"+qstring; 
		  }
		window.location = "<?php echo $page_name;?>"+qstring;
		}else{
			alert("Please select atleast one field to search.");
		}
	});
	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "<?php echo $page_name;?>";
	});
});
</script>
<?php
include "web_footer.php";
mysql_close();
?>
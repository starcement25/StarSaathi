<?php
include "web_check.php";
include "star_connection.php";
$table_name = "employee_master";
$customer_master = "customer_master";
$changepassword = "changepassword";
$survey_for_new_year_eve_carnival_concert = "survey_for_new_year_eve_carnival_concert";
$profile_image_dir = "../profile_image/";
$new_qry_string_filtered = "";
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
$whr_str .= "$aand ($survey_for_new_year_eve_carnival_concert.`sf_dealer_id` like '%$search_array_val%' or $survey_for_new_year_eve_carnival_concert.`sf_dealer_name` like '%$search_array_val%' or $survey_for_new_year_eve_carnival_concert.`sf_dealer_mobile` like '%$search_array_val%' ) ";
			$new_qry_string_filtered .= "&srch_dlr_dtls=".$search_array_val;
		}
	}
}
if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}
$add_page_name = "submitted_nye_list.php";
$page_name = "submitted_nye_list.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/
$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;
/*---------PAGINATION RELATED CODE END----------*/
/*---------PAGINATION RELATED CODE START----------*/
$pgsql = "select `sf_id` from $survey_for_new_year_eve_carnival_concert left join $changepassword on $survey_for_new_year_eve_carnival_concert.`sf_cust_code`=$changepassword.`customer_code` $new_whr_str";
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
<style>
.dlr_prfl_img{
	width:150px;
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
                          <h2>NYE List (<?php echo $total_pgres;?>)&nbsp;&nbsp;
                          <a href="export_submitted_nye_list.php" class="btn bg-red waves-effe">Export&nbsp;NYE&nbsp;List</a> &nbsp;
                          </h2>
                            <div class="row clearfix">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_dlr_dtls" value="<?php echo $srch_dlr_dtls;?>" placeholder="Search Dealer Details">
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
<th>Dealer&nbsp;ID</th>
<th>Dealer&nbsp;SAP&nbsp;Code</th>
<th>Dealer&nbsp;Name</th>
<th>Phone</th>
<th>Device&nbsp;Type</th>
<th>App&nbsp;Version</th>
<th>Branch&nbsp;Name</th>
<th>Branch&nbsp;Code</th>
<th>Branch&nbsp;DNS&nbsp;Code</th>
<th>1.&nbsp;Attending</th>
<th>a.&nbsp;Alan&nbsp;Walker</th>
<th>b.&nbsp;Badhshah</th>
<th>c.&nbsp;David&nbsp;Guetta</th>
<th>d.&nbsp;Sunidhi&nbsp;Chauhan</th>
<th>e.&nbsp;Lucky&nbsp;Ali</th>
<th>f.&nbsp;Zubeen&nbsp;Garg</th>
<th>g.&nbsp;Pitbull</th>
<th>h.&nbsp;Mika&nbsp;Singh</th>
<th>i.&nbsp;Arijit&nbsp;Singh</th>
<th>j.&nbsp;Papon</th>
<th>k.&nbsp;DJ&nbsp;Nucleya</th>
<th>l.&nbsp;Ankit&nbsp;Tiwari</th>
<th>m.&nbsp;Others</th>
<th>3a.&nbsp;2000</th>
<th>3b.&nbsp;3000</th>
<th>3c.&nbsp;4000</th>
<th>3d.&nbsp;5000</th>
<th>3e.&nbsp;7000</th>
<th>4a.&nbsp;Spouse&nbsp;only</th>
<th>4b.&nbsp;Spouse&nbsp;&&nbsp;Kid(s)</th>
<th>4c.&nbsp;Friends&nbsp;&&nbsp;Colleagues</th>
<th>4d.&nbsp;Parents&nbsp;&&nbsp;Siblings</th>
<th>4e.&nbsp;Solo</th>
<th>5.&nbsp;Liquour</th>
<th>6.&nbsp;30th&nbsp;December</th>
<th>7.&nbsp;31st&nbsp;December</th>
<th>Submitted&nbsp;On</th>
</tr>
</thead>
<tfoot>
<tr>
<th>Dealer&nbsp;ID</th>
<th>Dealer&nbsp;SAP&nbsp;Code</th>
<th>Dealer&nbsp;Name</th>
<th>Phone</th>
<th>Device&nbsp;Type</th>
<th>App&nbsp;Version</th>
<th>Branch&nbsp;Name</th>
<th>Branch&nbsp;Code</th>
<th>Branch&nbsp;DNS&nbsp;Code</th>
<th>1.&nbsp;Attending</th>
<th>a.&nbsp;Alan&nbsp;Walker</th>
<th>b.&nbsp;Badhshah</th>
<th>c.&nbsp;David&nbsp;Guetta</th>
<th>d.&nbsp;Sunidhi&nbsp;Chauhan</th>
<th>e.&nbsp;Lucky&nbsp;Ali</th>
<th>f.&nbsp;Zubeen&nbsp;Garg</th>
<th>g.&nbsp;Pitbull</th>
<th>h.&nbsp;Mika&nbsp;Singh</th>
<th>i.&nbsp;Arijit&nbsp;Singh</th>
<th>j.&nbsp;Papon</th>
<th>k.&nbsp;DJ&nbsp;Nucleya</th>
<th>l.&nbsp;Ankit&nbsp;Tiwari</th>
<th>m.&nbsp;Others</th>
<th>3a.&nbsp;2000</th>
<th>3b.&nbsp;3000</th>
<th>3c.&nbsp;4000</th>
<th>3d.&nbsp;5000</th>
<th>3e.&nbsp;7000</th>
<th>4a.&nbsp;Spouse&nbsp;only</th>
<th>4b.&nbsp;Spouse&nbsp;&&nbsp;Kid(s)</th>
<th>4c.&nbsp;Friends&nbsp;&&nbsp;Colleagues</th>
<th>4d.&nbsp;Parents&nbsp;&&nbsp;Siblings</th>
<th>4e.&nbsp;Solo</th>
<th>5.&nbsp;Liquour</th>
<th>6.&nbsp;30th&nbsp;December</th>
<th>7.&nbsp;31st&nbsp;December</th>
<th>Submitted&nbsp;On</th>
</tr>
</tfoot>
<tbody>
<?php
$sql1 = "select $survey_for_new_year_eve_carnival_concert.*,$changepassword.`device_type`,$changepassword.`app_version` from $survey_for_new_year_eve_carnival_concert left join $changepassword on $survey_for_new_year_eve_carnival_concert.`sf_cust_code`=$changepassword.`customer_code` $new_whr_str order by $survey_for_new_year_eve_carnival_concert.`sf_submitted_datetime` desc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$sf_id = $row1["sf_id"];
		$sf_cust_code = $row1["sf_cust_code"];
		$sf_dealer_id = $row1["sf_dealer_id"];
		$sf_dealer_sap_code = $row1["sf_dealer_sap_code"];
		$sf_dealer_name = $row1["sf_dealer_name"];
		$sf_dealer_mobile = $row1["sf_dealer_mobile"];
		$sf_branch_name = $row1["sf_branch_name"];
		$sf_branch_code = $row1["sf_branch_code"];
		$sf_dns_branch_code = $row1["sf_dns_branch_code"];
		
$sf_1_attending = $row1["sf_1_attending"] ? trim($row1["sf_1_attending"]) : "";
$sf_a_alan_walker = $row1["sf_a_alan_walker"] ? trim($row1["sf_a_alan_walker"]) : "";
$sf_b_badhshah = $row1["sf_b_badhshah"] ? trim($row1["sf_b_badhshah"]) : "";
$sf_c_david_guetta = $row1["sf_c_david_guetta"] ? trim($row1["sf_c_david_guetta"]) : "";
$sf_d_sunidhi_chauhan = $row1["sf_d_sunidhi_chauhan"] ? trim($row1["sf_d_sunidhi_chauhan"]) : "";
$sf_e_lucky_ali = $row1["sf_e_lucky_ali"] ? trim($row1["sf_e_lucky_ali"]) : "";
$sf_f_zubeen_garg = $row1["sf_f_zubeen_garg"] ? trim($row1["sf_f_zubeen_garg"]) : "";
$sf_g_pitbull = $row1["sf_g_pitbull"] ? trim($row1["sf_g_pitbull"]) : "";
$sf_h_mika_singh = $row1["sf_h_mika_singh"] ? trim($row1["sf_h_mika_singh"]) : "";
$sf_i_arijit_singh = $row1["sf_i_arijit_singh"] ? trim($row1["sf_i_arijit_singh"]) : "";
$sf_j_papon = $row1["sf_j_papon"] ? trim($row1["sf_j_papon"]) : "";
$sf_k_dj_nucleya = $row1["sf_k_dj_nucleya"] ? trim($row1["sf_k_dj_nucleya"]) : "";
$sf_l_ankit_tiwari = $row1["sf_l_ankit_tiwari"] ? trim($row1["sf_l_ankit_tiwari"]) : "";
$sf_m_others = $row1["sf_m_others"] ? trim($row1["sf_m_others"]) : "";
$sf_3a_2000 = $row1["sf_3a_2000"] ? trim($row1["sf_3a_2000"]) : "";
$sf_3b_3000 = $row1["sf_3b_3000"] ? trim($row1["sf_3b_3000"]) : "";
$sf_3c_4000 = $row1["sf_3c_4000"] ? trim($row1["sf_3c_4000"]) : "";
$sf_3d_5000 = $row1["sf_3d_5000"] ? trim($row1["sf_3d_5000"]) : "";
$sf_3e_7000 = $row1["sf_3e_7000"] ? trim($row1["sf_3e_7000"]) : "";
$sf_4a_spouse_only = $row1["sf_4a_spouse_only"] ? trim($row1["sf_4a_spouse_only"]) : "";
$sf_4b_spouse_and_kid = $row1["sf_4b_spouse_and_kid"] ? trim($row1["sf_4b_spouse_and_kid"]) : "";
$sf_4c_friends_and_colleagues = $row1["sf_4c_friends_and_colleagues"] ? trim($row1["sf_4c_friends_and_colleagues"]) : "";
$sf_4d_parents_and_siblings = $row1["sf_4d_parents_and_siblings"] ? trim($row1["sf_4d_parents_and_siblings"]) : "";
$sf_4e_solo = $row1["sf_4e_solo"] ? trim($row1["sf_4e_solo"]) : "";
$sf_5_liquour = $row1["sf_5_liquour"] ? trim($row1["sf_5_liquour"]) : "";
$sf_6_30th_december = $row1["sf_6_30th_december"] ? trim($row1["sf_6_30th_december"]) : "";	
$sf_7_31st_december = $row1["sf_7_31st_december"] ? trim($row1["sf_7_31st_december"]) : "";	
		
		
		$sf_submitted_datetime = $row1["sf_submitted_datetime"] ? trim($row1["sf_submitted_datetime"]) : "";
		if($sf_submitted_datetime!=""){
			$sf_submitted_datetime = date("jS M,Y h:i A",strtotime($sf_submitted_datetime));
		}
		
		$sf_device_type = $row1["device_type"];
		$sf_app_version = $row1["app_version"];

?>
<tr>
<td><?php echo $sf_dealer_id;?></td>
<td><?php echo $sf_dealer_sap_code;?></td>
<td><?php echo $sf_dealer_name;?></td>
<td><?php echo $sf_dealer_mobile;?></td>
<td><?php echo $sf_device_type;?></td>
<td><?php echo $sf_app_version;?></td>
<td><?php echo $sf_branch_name;?></td>
<td><?php echo $sf_branch_code;?></td>
<td><?php echo $sf_dns_branch_code;?></td>
<td><?php echo $sf_1_attending;?></td>
<td><?php echo $sf_a_alan_walker;?></td>
<td><?php echo $sf_b_badhshah;?></td>
<td><?php echo $sf_c_david_guetta;?></td>
<td><?php echo $sf_d_sunidhi_chauhan;?></td>
<td><?php echo $sf_e_lucky_ali;?></td>
<td><?php echo $sf_f_zubeen_garg;?></td>
<td><?php echo $sf_g_pitbull;?></td>
<td><?php echo $sf_h_mika_singh;?></td>
<td><?php echo $sf_i_arijit_singh;?></td>
<td><?php echo $sf_j_papon;?></td>
<td><?php echo $sf_k_dj_nucleya;?></td>
<td><?php echo $sf_l_ankit_tiwari;?></td>
<td><?php echo $sf_m_others;?></td>
<td><?php echo $sf_3a_2000;?></td>
<td><?php echo $sf_3b_3000;?></td>
<td><?php echo $sf_3c_4000;?></td>
<td><?php echo $sf_3d_5000;?></td>
<td><?php echo $sf_3e_7000;?></td>
<td><?php echo $sf_4a_spouse_only;?></td>
<td><?php echo $sf_4b_spouse_and_kid;?></td>
<td><?php echo $sf_4c_friends_and_colleagues;?></td>
<td><?php echo $sf_4d_parents_and_siblings;?></td>
<td><?php echo $sf_4e_solo;?></td>
<td><?php echo $sf_5_liquour;?></td>
<td><?php echo $sf_6_30th_december;?></td>
<td><?php echo $sf_7_31st_december;?></td>
<td><?php echo $sf_submitted_datetime;?></td>
</tr>
<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="37">No data found.</td>
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
			alert("Please enter dealer details to search.");
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
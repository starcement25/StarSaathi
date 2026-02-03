<?php
include "web_check.php";
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$employee_master = "employee_master";
$customer_master = "customer_master";
$branch_master = "branch_master";
$broker_master = "broker_master";
$customer_broker_relation = "customer_broker_relation";
$ledger_balance = "ledger_balance";
$branch_schemes_PDF = "branch_schemes_PDF";
$notification_message = "notification_message";
$self_appraisal_product_wise = "self_appraisal_product_wise";
$page_name = "performance.php";
$curr_yer_target_arr = array();
$curr_yer_achievement_arr = array();
$prev_yer_target_arr = array();
$prev_yer_achievement_arr = array();

if(isset($_GET["sel_sdvl"]) && @$_GET["sel_sdvl"]!=""){
$sel_sdvl = trim($_GET["sel_sdvl"]);
}else{
$sel_sdvl = "";	
}

$sswa_user_type = $_SESSION["sswa_user_type"];
$sswa_the_broker_id = $_SESSION["sswa_user_id"];
$sswa_the_dns_broker_id = $_SESSION["sswa_user_dns_id"];
$sswa_selected_dealer_code = $_SESSION["sswa_selected_dealer_code"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];

if($sswa_user_type=="SP"){
$brsql = "select $customer_master.`dns_customer_code`,$customer_master.`customer_name` from $customer_broker_relation left join $customer_master on $customer_broker_relation.`customer_code`=$customer_master.`customer_code` where $customer_broker_relation.`broker_code`='$sswa_the_broker_id' and $customer_broker_relation.`acedns`='Y' order by $customer_master.`customer_name` asc";	
}else{
$brsql = "select `dns_customer_code`,`customer_name` from $customer_master where `rds_tag` = '$sswa_selected_customer_code' and `acedns`='Y' order by `customer_name` asc";
}
$brres = mysql_query($brsql);
$total_brres = mysql_num_rows($brres);


if($sel_sdvl!=""){
	$sql_perfrm = "select `jan_31_target`,`jan_31_achievement`,`feb_28_target`,`feb_28_achievement`,`mar_31_target`,`mar_31_achievement`,`apr_30_target`,`apr_30_achievement`,`may_31_target`,`may_31_achievement`,`jun_30_target`,`jun_30_achievement`,`jul_31_target`,`jul_31_achievement`,`aug_31_target`,`aug_31_achievement`,`sep_30_target`,`sep_30_achievement`,`oct_31_target`,`oct_31_achievement`,`nov_30_target`,`nov_30_achievement`,`dec_31_target`,`dec_31_achievement`,`jan_prev_y_target`,`jan_prev_y_achievement`,`feb_prev_y_target`,`feb_prev_y_achievement`,`mar_prev_y_target`,`mar_prev_y_achievement`,`apr_prev_y_target`,`apr_prev_y_achievement`,`may_prev_y_target`,`may_prev_y_achievement`,`jun_prev_y_target`,`jun_prev_y_achievement`,`jul_prev_y_target`,`jul_prev_y_achievement`,`aug_prev_y_target`,`aug_prev_y_achievement`,`sep_prev_y_target`,`sep_prev_y_achievement`,`oct_prev_y_target`,`oct_prev_y_achievement`,`nov_prev_y_target`,`nov_prev_y_achievement`,`dec_prev_y_target`,`dec_prev_y_achievement` from $self_appraisal_product_wise where `customer_code`='$sel_sdvl'";
}else{
	$sql_perfrm = "select `jan_31_target`,`jan_31_achievement`,`feb_28_target`,`feb_28_achievement`,`mar_31_target`,`mar_31_achievement`,`apr_30_target`,`apr_30_achievement`,`may_31_target`,`may_31_achievement`,`jun_30_target`,`jun_30_achievement`,`jul_31_target`,`jul_31_achievement`,`aug_31_target`,`aug_31_achievement`,`sep_30_target`,`sep_30_achievement`,`oct_31_target`,`oct_31_achievement`,`nov_30_target`,`nov_30_achievement`,`dec_31_target`,`dec_31_achievement`,`jan_prev_y_target`,`jan_prev_y_achievement`,`feb_prev_y_target`,`feb_prev_y_achievement`,`mar_prev_y_target`,`mar_prev_y_achievement`,`apr_prev_y_target`,`apr_prev_y_achievement`,`may_prev_y_target`,`may_prev_y_achievement`,`jun_prev_y_target`,`jun_prev_y_achievement`,`jul_prev_y_target`,`jul_prev_y_achievement`,`aug_prev_y_target`,`aug_prev_y_achievement`,`sep_prev_y_target`,`sep_prev_y_achievement`,`oct_prev_y_target`,`oct_prev_y_achievement`,`nov_prev_y_target`,`nov_prev_y_achievement`,`dec_prev_y_target`,`dec_prev_y_achievement` from $self_appraisal_product_wise where `customer_code`='$sswa_selected_dealer_code'";
}

$res_perfrm = mysql_query($sql_perfrm);
$totres_perfrm = mysql_num_rows($res_perfrm);
if($totres_perfrm>0){
	$row_perfrm = mysql_fetch_assoc($res_perfrm);


$apr_30_target = $row_perfrm["apr_30_target"] ? floatval($row_perfrm["apr_30_target"]) : 0;
$apr_30_achievement = $row_perfrm["apr_30_achievement"] ? floatval($row_perfrm["apr_30_achievement"]) : 0;
$curr_yer_target_arr[] = $apr_30_target;
$curr_yer_achievement_arr[] = $apr_30_achievement;

$may_31_target = $row_perfrm["may_31_target"] ? floatval($row_perfrm["may_31_target"]) : 0;
$may_31_achievement = $row_perfrm["may_31_achievement"] ? floatval($row_perfrm["may_31_achievement"]) : 0;
$curr_yer_target_arr[] = $may_31_target;
$curr_yer_achievement_arr[] = $may_31_achievement;

$jun_30_target = $row_perfrm["jun_30_target"] ? floatval($row_perfrm["jun_30_target"]) : 0;
$jun_30_achievement = $row_perfrm["jun_30_achievement"] ? floatval($row_perfrm["jun_30_achievement"]) : 0;
$curr_yer_target_arr[] = $jun_30_target;
$curr_yer_achievement_arr[] = $jun_30_achievement;

$jul_31_target = $row_perfrm["jul_31_target"] ? floatval($row_perfrm["jul_31_target"]) : 0;
$jul_31_achievement = $row_perfrm["jul_31_achievement"] ? floatval($row_perfrm["jul_31_achievement"]) : 0;
$curr_yer_target_arr[] = $jul_31_target;
$curr_yer_achievement_arr[] = $jul_31_achievement;

$aug_31_target = $row_perfrm["aug_31_target"] ? floatval($row_perfrm["aug_31_target"]) : 0;
$aug_31_achievement = $row_perfrm["aug_31_achievement"] ? floatval($row_perfrm["aug_31_achievement"]) : 0;
$curr_yer_target_arr[] = $aug_31_target;
$curr_yer_achievement_arr[] = $aug_31_achievement;

$sep_30_target = $row_perfrm["sep_30_target"] ? floatval($row_perfrm["sep_30_target"]) : 0;
$sep_30_achievement = $row_perfrm["sep_30_achievement"] ? floatval($row_perfrm["sep_30_achievement"]) : 0;
$curr_yer_target_arr[] = $sep_30_target;
$curr_yer_achievement_arr[] = $sep_30_achievement;

$oct_31_target = $row_perfrm["oct_31_target"] ? floatval($row_perfrm["oct_31_target"]) : 0;
$oct_31_achievement = $row_perfrm["jan_31_target"] ? floatval($row_perfrm["jan_31_target"]) : 0;
$curr_yer_target_arr[] = $oct_31_target;
$curr_yer_achievement_arr[] = $oct_31_achievement;

$nov_30_target = $row_perfrm["nov_30_target"] ? floatval($row_perfrm["nov_30_target"]) : 0;
$nov_30_achievement = $row_perfrm["nov_30_achievement"] ? floatval($row_perfrm["nov_30_achievement"]) : 0;
$curr_yer_target_arr[] = $nov_30_target;
$curr_yer_achievement_arr[] = $nov_30_achievement;

$dec_31_target = $row_perfrm["dec_31_target"] ? floatval($row_perfrm["dec_31_target"]) : 0;
$dec_31_achievement = $row_perfrm["dec_31_achievement"] ? floatval($row_perfrm["dec_31_achievement"]) : 0;
$curr_yer_target_arr[] = $dec_31_target;
$curr_yer_achievement_arr[] = $dec_31_achievement;

$jan_31_target = $row_perfrm["jan_31_target"] ? floatval($row_perfrm["jan_31_target"]) : 0;
$jan_31_achievement = $row_perfrm["jan_31_achievement"] ? floatval($row_perfrm["jan_31_achievement"]) : 0;
$curr_yer_target_arr[] = $jan_31_target;
$curr_yer_achievement_arr[] = $jan_31_achievement;

$feb_28_target = $row_perfrm["feb_28_target"] ? floatval($row_perfrm["feb_28_target"]) : 0;
$feb_28_achievement = $row_perfrm["feb_28_achievement"] ? floatval($row_perfrm["feb_28_achievement"]) : 0;
$curr_yer_target_arr[] = $feb_28_target;
$curr_yer_achievement_arr[] = $feb_28_achievement;

$mar_31_target = $row_perfrm["mar_31_target"] ? floatval($row_perfrm["mar_31_target"]) : 0;
$mar_31_achievement = $row_perfrm["mar_31_achievement"] ? floatval($row_perfrm["mar_31_achievement"]) : 0;
$curr_yer_target_arr[] = $mar_31_target;
$curr_yer_achievement_arr[] = $mar_31_achievement;

$apr_prev_y_target = $row_perfrm["apr_prev_y_target"] ? floatval($row_perfrm["apr_prev_y_target"]) : 0;
$apr_prev_y_achievement = $row_perfrm["apr_prev_y_achievement"] ? floatval($row_perfrm["apr_prev_y_achievement"]) : 0;
$prev_yer_target_arr[] = $apr_prev_y_target;
$prev_yer_achievement_arr[] = $apr_prev_y_achievement;

$may_prev_y_target = $row_perfrm["may_prev_y_target"] ? floatval($row_perfrm["may_prev_y_target"]) : 0;
$may_prev_y_achievement = $row_perfrm["may_prev_y_achievement"] ? floatval($row_perfrm["may_prev_y_achievement"]) : 0;
$prev_yer_target_arr[] = $may_prev_y_target;
$prev_yer_achievement_arr[] = $may_prev_y_achievement;

$jun_prev_y_target = $row_perfrm["jun_prev_y_target"] ? floatval($row_perfrm["jun_prev_y_target"]) : 0;
$jun_prev_y_achievement = $row_perfrm["jun_prev_y_achievement"] ? floatval($row_perfrm["jun_prev_y_achievement"]) : 0;
$prev_yer_target_arr[] = $jun_prev_y_target;
$prev_yer_achievement_arr[] = $jun_prev_y_achievement;

$jul_prev_y_target = $row_perfrm["jul_prev_y_target"] ? floatval($row_perfrm["jul_prev_y_target"]) : 0;
$jul_prev_y_achievement = $row_perfrm["jul_prev_y_achievement"] ? floatval($row_perfrm["jul_prev_y_achievement"]) : 0;
$prev_yer_target_arr[] = $jul_prev_y_target;
$prev_yer_achievement_arr[] = $jul_prev_y_achievement;

$aug_prev_y_target = $row_perfrm["aug_prev_y_target"] ? floatval($row_perfrm["aug_prev_y_target"]) : 0;
$aug_prev_y_achievement = $row_perfrm["aug_prev_y_achievement"] ? floatval($row_perfrm["aug_prev_y_achievement"]) : 0;
$prev_yer_target_arr[] = $aug_prev_y_target;
$prev_yer_achievement_arr[] = $aug_prev_y_achievement;

$sep_prev_y_target = $row_perfrm["sep_prev_y_target"] ? floatval($row_perfrm["sep_prev_y_target"]) : 0;
$sep_prev_y_achievement = $row_perfrm["sep_prev_y_achievement"] ? floatval($row_perfrm["sep_prev_y_achievement"]) : 0;
$prev_yer_target_arr[] = $sep_prev_y_target;
$prev_yer_achievement_arr[] = $sep_prev_y_achievement;

$oct_prev_y_target = $row_perfrm["oct_prev_y_target"] ? floatval($row_perfrm["oct_prev_y_target"]) : 0;
$oct_prev_y_achievement = $row_perfrm["oct_prev_y_achievement"] ? floatval($row_perfrm["oct_prev_y_achievement"]) : 0;
$prev_yer_target_arr[] = $oct_prev_y_target;
$prev_yer_achievement_arr[] = $oct_prev_y_achievement;

$nov_prev_y_target = $row_perfrm["nov_prev_y_target"] ? floatval($row_perfrm["nov_prev_y_target"]) : 0;
$nov_prev_y_achievement = $row_perfrm["nov_prev_y_achievement"] ? floatval($row_perfrm["nov_prev_y_achievement"]) : 0;
$prev_yer_target_arr[] = $nov_prev_y_target;
$prev_yer_achievement_arr[] = $nov_prev_y_achievement;

$dec_prev_y_target = $row_perfrm["dec_prev_y_target"] ? floatval($row_perfrm["dec_prev_y_target"]) : 0;
$dec_prev_y_achievement = $row_perfrm["dec_prev_y_achievement"] ? floatval($row_perfrm["dec_prev_y_achievement"]) : 0;
$prev_yer_target_arr[] = $dec_prev_y_target;
$prev_yer_achievement_arr[] = $dec_prev_y_achievement;

$jan_prev_y_target = $row_perfrm["jan_prev_y_target"] ? floatval($row_perfrm["jan_prev_y_target"]) : 0;
$jan_prev_y_achievement = $row_perfrm["jan_prev_y_achievement"] ? floatval($row_perfrm["jan_prev_y_achievement"]) : 0;
$prev_yer_target_arr[] = $jan_prev_y_target;
$prev_yer_achievement_arr[] = $jan_prev_y_achievement;

$feb_prev_y_target = $row_perfrm["feb_prev_y_target"] ? floatval($row_perfrm["feb_prev_y_target"]) : 0;
$feb_prev_y_achievement = $row_perfrm["feb_prev_y_achievement"] ? floatval($row_perfrm["feb_prev_y_achievement"]) : 0;
$prev_yer_target_arr[] = $feb_prev_y_target;
$prev_yer_achievement_arr[] = $feb_prev_y_achievement;

$mar_prev_y_target = $row_perfrm["mar_prev_y_target"] ? floatval($row_perfrm["mar_prev_y_target"]) : 0;
$mar_prev_y_achievement = $row_perfrm["mar_prev_y_achievement"] ? floatval($row_perfrm["mar_prev_y_achievement"]) : 0;
$prev_yer_target_arr[] = $mar_prev_y_target;
$prev_yer_achievement_arr[] = $mar_prev_y_achievement;

}else{
$apr_30_target = 0;
$apr_30_achievement = 0;
$may_31_target = 0;
$may_31_achievement = 0;
$jun_30_target = 0;
$jun_30_achievement = 0;
$jul_31_target = 0;
$jul_31_achievement = 0;
$aug_31_target = 0;
$aug_31_achievement = 0;
$sep_30_target = 0;
$sep_30_achievement = 0;
$oct_31_target = 0;
$oct_31_achievement = 0;
$nov_30_target = 0;
$nov_30_achievement = 0;
$dec_31_target = 0;
$dec_31_achievement = 0;
$jan_31_target = 0;
$jan_31_achievement = 0;
$feb_28_target = 0;
$feb_28_achievement = 0;
$mar_31_target = 0;
$mar_31_achievement = 0;

$apr_prev_y_target = 0;
$apr_prev_y_achievement = 0;
$may_prev_y_target = 0;
$may_prev_y_achievement = 0;
$jun_prev_y_target = 0;
$jun_prev_y_achievement = 0;
$jul_prev_y_target = 0;
$jul_prev_y_achievement = 0;
$aug_prev_y_target = 0;
$aug_prev_y_achievement = 0;
$sep_prev_y_target = 0;
$sep_prev_y_achievement = 0;
$oct_prev_y_target = 0;
$oct_prev_y_achievement = 0;
$nov_prev_y_target = 0;
$nov_prev_y_achievement = 0;
$dec_prev_y_target = 0;
$dec_prev_y_achievement = 0;
$jan_prev_y_target = 0;
$jan_prev_y_achievement = 0;
$feb_prev_y_target = 0;
$feb_prev_y_achievement = 0;
$mar_prev_y_target = 0;
$mar_prev_y_achievement = 0;
}

include "web_header.php";

?>
<style>
#container {
  height: 400px;
}
#container2 {
    height: 400px;
}

.highcharts-figure, .highcharts-data-table table {
  min-width: 310px;
  max-width: 800px;
  margin: 1em auto;
}

#datatable1 {
  font-family: Verdana, sans-serif;
  border-collapse: collapse;
  border: 1px solid #EBEBEB;
  margin: 10px auto;
  text-align: center;
  width: 100%;
  max-width: 500px;
  display:none;
}
#datatable1 caption {
  padding: 1em 0;
  font-size: 1.2em;
  color: #555;
}
#datatable1 th {
	font-weight: 600;
  padding: 0.5em;
}
#datatable1 td, #datatable1 th, #datatable1 caption {
  padding: 0.5em;
}
#datatable1 thead tr, #datatable1 tr:nth-child(even) {
  background: #f8f8f8;
}
#datatable1 tr:hover {
  background: #f1f7ff;
}

#datatable2 {
  font-family: Verdana, sans-serif;
  border-collapse: collapse;
  border: 1px solid #EBEBEB;
  margin: 10px auto;
  text-align: center;
  width: 100%;
  max-width: 500px;
  display:none;
}
#datatable2 caption {
  padding: 1em 0;
  font-size: 1.2em;
  color: #555;
}
#datatable2 th {
	font-weight: 600;
  padding: 0.5em;
}
#datatable2 td, #datatable1 th, #datatable2 caption {
  padding: 0.5em;
}
#datatable2 thead tr, #datatable2 tr:nth-child(even) {
  background: #f8f8f8;
}
#datatable2 tr:hover {
  background: #f1f7ff;
}


.mn_lft{
	text-align:left;
	font-weight:bold;
}
.ta_right{
	text-align:right;
	font-weight:bold;
}
#ta_table_view{
	 display:none;
}


</style>

<section class="content">
    <div class="container-fluid" style="position:relative;">

<div class="row clearfix">
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
<select class="form-control" id="astn_dlsbdl_code" name="astn_dlsbdl_code" style="padding-left:2px;">
<?php
if($sswa_user_type=="SP"){
	
}else{
?>
<option value="">Total Performance</option>
<?php
}
if($total_brres>0){
	while($brrow=mysql_fetch_assoc($brres)){
		$the_br_dns_customer_code = $brrow["dns_customer_code"];
		$the_br_customer_name = $brrow["customer_name"];?>
        <option value="<?php echo $the_br_dns_customer_code;?>" <?php if(($the_br_dns_customer_code==$sel_sdvl) || ($the_br_dns_customer_code==$sswa_selected_dealer_code)){ ?> selected="selected" <?php } ?>><?php echo $the_br_customer_name;?></option>
		<?php
	}
	
}
?>

</select>
</div>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

</div>
</div>

<div style="text-align:right;padding-top:10px;">
<input name="the_gt_view" id="the_graph_view" value="Graph View" checked="checked" type="radio">
<label for="the_graph_view">Graph View</label>
<input name="the_gt_view" id="the_table_view" value="Table View" type="radio">
<label for="the_table_view">Table View</label>
</div>

        <div class="row clearfix" id="ta_graph_view">
         <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">    
                <div class="card">
                 <figure class="highcharts-figure">
  <div id="container"></div>
  <table id="datatable1">
    <thead>
      <tr>
        <th></th>
        <th>Target</th>
        <th>Achivements</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th>Apr</th>
        <td><?php echo $apr_30_target; ?></td>
        <td><?php echo $apr_30_achievement ; ?></td>
      </tr>
      <tr>
        <th>May</th>
        <td><?php echo $may_31_target ; ?></td>
        <td><?php echo $may_31_achievement ; ?></td>
      </tr>
      <tr>
        <th>Jun</th>
        <td><?php echo $jun_30_target ; ?></td>
        <td><?php echo $jun_30_achievement ; ?></td>
      </tr>
      <tr>
        <th>Jul</th>
        <td><?php echo $jul_31_target ; ?></td>
        <td><?php echo $jul_31_achievement ; ?></td>
      </tr>
      <tr>
        <th>Aug</th>
        <td><?php echo $aug_31_target ; ?></td>
        <td><?php echo $aug_31_achievement ; ?></td>
      </tr>
      <tr>
        <th>Sep</th>
        <td><?php echo $sep_30_target ; ?></td>
        <td><?php echo $sep_30_achievement ; ?></td>
      </tr>
      <tr>
        <th>Oct</th>
        <td><?php echo $oct_31_target ; ?></td>
        <td><?php echo $oct_31_achievement ; ?></td>
      </tr>
      <tr>
        <th>Nov</th>
        <td><?php echo $nov_30_target ; ?></td>
        <td><?php echo $nov_30_achievement ; ?></td>
      </tr>
      <tr>
        <th>Dec</th>
        <td><?php echo $dec_31_target ; ?></td>
        <td><?php echo $dec_31_achievement ; ?></td>
      </tr>
      <tr>
        <th>Jan</th>
        <td><?php echo $jan_31_target ; ?></td>
        <td><?php echo $jan_31_achievement ; ?></td>
      </tr>
      <tr>
        <th>Feb</th>
        <td><?php echo $feb_28_target ; ?></td>
        <td><?php echo $feb_28_achievement ; ?></td>
      </tr>
      <tr>
        <th>Mar</th>
        <td><?php echo $mar_31_target ; ?></td>
        <td><?php echo $mar_31_achievement ; ?></td>
      </tr>
    </tbody>
  </table>
</figure>   
                    
                </div>
            </div>
         <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="card">
                 <figure class="highcharts-figure">
  <div id="container2"></div>
  <table id="datatable2">
    <thead>
      <tr>
        <th></th>
        <th>Target</th>
        <th>Achivements</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th>Apr</th>
        <td><?php echo $apr_prev_y_target; ?></td>
        <td><?php echo $apr_prev_y_achievement ; ?></td>
      </tr>
      <tr>
        <th>May</th>
        <td><?php echo $may_prev_y_target ; ?></td>
        <td><?php echo $may_prev_y_achievement ; ?></td>
      </tr>
      <tr>
        <th>Jun</th>
        <td><?php echo $jun_prev_y_target ; ?></td>
        <td><?php echo $jun_prev_y_achievement ; ?></td>
      </tr>
      <tr>
        <th>Jul</th>
        <td><?php echo $jul_prev_y_target ; ?></td>
        <td><?php echo $jul_prev_y_achievement ; ?></td>
      </tr>
      <tr>
        <th>Aug</th>
        <td><?php echo $aug_prev_y_target ; ?></td>
        <td><?php echo $aug_prev_y_achievement ; ?></td>
      </tr>
      <tr>
        <th>Sep</th>
        <td><?php echo $sep_prev_y_target ; ?></td>
        <td><?php echo $sep_prev_y_achievement ; ?></td>
      </tr>
      <tr>
        <th>Oct</th>
        <td><?php echo $oct_prev_y_target ; ?></td>
        <td><?php echo $oct_prev_y_achievement ; ?></td>
      </tr>
      <tr>
        <th>Nov</th>
        <td><?php echo $nov_prev_y_target ; ?></td>
        <td><?php echo $nov_prev_y_achievement ; ?></td>
      </tr>
      <tr>
        <th>Dec</th>
        <td><?php echo $dec_prev_y_target ; ?></td>
        <td><?php echo $dec_prev_y_achievement ; ?></td>
      </tr>
      <tr>
        <th>Jan</th>
        <td><?php echo $jan_prev_y_target ; ?></td>
        <td><?php echo $jan_prev_y_achievement ; ?></td>
      </tr>
      <tr>
        <th>Feb</th>
        <td><?php echo $feb_prev_y_target ; ?></td>
        <td><?php echo $feb_prev_y_achievement ; ?></td>
      </tr>
      <tr>
        <th>Mar</th>
        <td><?php echo $mar_prev_y_target ; ?></td>
        <td><?php echo $mar_prev_y_achievement ; ?></td>
      </tr>
    </tbody>
  </table>
</figure>  
                    
                </div>
            </div>
        </div>
        
        
        <div class="row clearfix" id="ta_table_view">
         <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">    
                <div class="card">
<div class="table-responsive">
<table class="table table-bordered">
<thead>
<tr>
<th colspan="3" style="text-align:center;">For Year 2020-2021</th>
</tr>
<tr>
<th>Month</th>
<th style="text-align:right;">Target</th>
<th style="text-align:right;">Achv.</th>
</tr>
</thead>
<tbody>
                 
<tr>
<td class="mn_lft">April</td>
<td class="ta_right"><?php echo $apr_30_target;?></td>
<td class="ta_right"><?php echo $apr_30_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">May</td>
<td class="ta_right"><?php echo $may_31_target;?></td>
<td class="ta_right"><?php echo $may_31_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">June</td>
<td class="ta_right"><?php echo $jun_30_target;?></td>
<td class="ta_right"><?php echo $jun_30_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">July</td>
<td class="ta_right"><?php echo $jul_31_target;?></td>
<td class="ta_right"><?php echo $jul_31_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">August</td>
<td class="ta_right"><?php echo $aug_31_target;?></td>
<td class="ta_right"><?php echo $aug_31_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">September</td>
<td class="ta_right"><?php echo $sep_30_target;?></td>
<td class="ta_right"><?php echo $sep_30_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">October</td>
<td class="ta_right"><?php echo $oct_31_target;?></td>
<td class="ta_right"><?php echo $oct_31_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">November</td>
<td class="ta_right"><?php echo $nov_30_target;?></td>
<td class="ta_right"><?php echo $nov_30_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">December</td>
<td class="ta_right"><?php echo $dec_31_target;?></td>
<td class="ta_right"><?php echo $dec_31_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">January</td>
<td class="ta_right"><?php echo $jan_31_target;?></td>
<td class="ta_right"><?php echo $jan_31_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">February</td>
<td class="ta_right"><?php echo $feb_28_target;?></td>
<td class="ta_right"><?php echo $feb_28_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">March</td>
<td class="ta_right"><?php echo $mar_31_target;?></td>
<td class="ta_right"><?php echo $mar_31_achievement;?></td>
</tr>

</tbody>
</table>
</div>
                </div>
            </div>
         <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="card">
<div class="table-responsive">
<table class="table table-bordered">
<thead>
<tr>
<th colspan="3" style="text-align:center;">For Year 2019-2020</th>
</tr>
<tr>
<th>Month</th>
<th style="text-align:right;">Target</th>
<th style="text-align:right;">Achv.</th>
</tr>
</thead>
<tbody>
                 
<tr>
<td class="mn_lft">April</td>
<td class="ta_right"><?php echo $apr_prev_y_target;?></td>
<td class="ta_right"><?php echo $apr_prev_y_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">May</td>
<td class="ta_right"><?php echo $may_prev_y_target;?></td>
<td class="ta_right"><?php echo $may_prev_y_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">June</td>
<td class="ta_right"><?php echo $jun_prev_y_target;?></td>
<td class="ta_right"><?php echo $jun_prev_y_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">July</td>
<td class="ta_right"><?php echo $jul_prev_y_target;?></td>
<td class="ta_right"><?php echo $jul_prev_y_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">August</td>
<td class="ta_right"><?php echo $aug_prev_y_target;?></td>
<td class="ta_right"><?php echo $aug_prev_y_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">September</td>
<td class="ta_right"><?php echo $sep_prev_y_target;?></td>
<td class="ta_right"><?php echo $sep_prev_y_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">October</td>
<td class="ta_right"><?php echo $oct_prev_y_target;?></td>
<td class="ta_right"><?php echo $oct_prev_y_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">November</td>
<td class="ta_right"><?php echo $nov_prev_y_target;?></td>
<td class="ta_right"><?php echo $nov_prev_y_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">December</td>
<td class="ta_right"><?php echo $dec_prev_y_target;?></td>
<td class="ta_right"><?php echo $dec_prev_y_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">January</td>
<td class="ta_right"><?php echo $jan_prev_y_target;?></td>
<td class="ta_right"><?php echo $jan_prev_y_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">February</td>
<td class="ta_right"><?php echo $feb_prev_y_target;?></td>
<td class="ta_right"><?php echo $feb_prev_y_achievement;?></td>
</tr>

<tr>
<td class="mn_lft">March</td>
<td class="ta_right"><?php echo $mar_prev_y_target;?></td>
<td class="ta_right"><?php echo $mar_prev_y_achievement;?></td>
</tr>

</tbody>
</table>
</div>                   
                    
                </div>
            </div>
        </div>
        
        
    </div>
</section>
   

<script type="text/javascript">

Highcharts.chart('container', {
  data: {
    table: 'datatable1'
  },
  chart: {
    type: 'column'
  },
  title: {
    text: 'For Year 2020-2021'
  },
  yAxis: {
    allowDecimals: true,
    title: {
      text: 'Values'
    }
  },
  tooltip: {
    formatter: function () {
      return '<b>' + this.series.name + '</b><br/>' +
        this.point.y + ' ' + this.point.name.toLowerCase();
    }
  }
});

Highcharts.chart('container2', {
  data: {
    table: 'datatable2'
  },
  chart: {
    type: 'column'
  },
  title: {
    text: 'For Year 2019-2020'
  },
  yAxis: {
    allowDecimals: true,
    title: {
      text: 'Values'
    }
  },
  tooltip: {
    formatter: function () {
      return '<b>' + this.series.name + '</b><br/>' +
        this.point.y + ' ' + this.point.name.toLowerCase();
    }
  }
});

</script>

<script type="text/javascript">
jQuery(document).ready(function(){

jQuery('#astn_dlsbdl_code').chosen({width:"100%",no_results_text:'Oops, no sub dealer found!',search_contains: true}).change(function() {
    var sel_sdvl = jQuery(this).val();
	if(sel_sdvl!=""){
		window.location = "<?php echo $page_name;?>?sel_sdvl="+sel_sdvl;
	}else{
		window.location = "<?php echo $page_name;?>";
	}

});
	
	jQuery('input[name="the_gt_view"]').change(function(){
	var sl_gt_view = jQuery(this).val();
	if(sl_gt_view=="Graph View"){
	jQuery("#ta_graph_view").show();
	jQuery("#ta_table_view").hide();
	}else if(sl_gt_view=="Table View"){
	jQuery("#ta_table_view").show();
	jQuery("#ta_graph_view").hide();
	}else{
	jQuery("#ta_graph_view").show();
	jQuery("#ta_table_view").hide();
	}
	});
	
});

</script> 

<?php
include "web_footer.php";
mysql_close();
?>
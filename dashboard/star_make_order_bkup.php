<?php
include "web_check.php";
include "star_connection.php";
$useragent = $_SERVER['HTTP_USER_AGENT'];
function is_json($string,$return_data = false) {
      $data = json_decode($string);
     return (json_last_error() == JSON_ERROR_NONE) ? ($return_data ? $data : TRUE) : FALSE;
}
$t_apperpdo = "T_APPERPDO";
$employee_master = "employee_master";
$customer_master = "customer_master";
$branch_master = "branch_master";
$broker_master = "broker_master";
$customer_broker_relation = "customer_broker_relation";
$ledger_balance = "ledger_balance";
$branch_schemes_PDF = "branch_schemes_PDF";
$notification_message = "notification_message";
$destination_master = "destination_master";
$branch_destination_freight = "branch_destination_freight";
$branch_dump = "branch_dump";
$product_master = "product_master";
function show_destination_data_code($the_destination_code){
$destination_data = array("dns_destination_code"=>"","destination_name"=>"");
$destination_master = "destination_master";
if($the_destination_code!=""){
$sqld = "select `dns_destination_code`,`destination_name` from $destination_master where `destination_code`='$the_destination_code'";	
$resd = mysql_query($sqld);
$totresd = mysql_num_rows($resd);
	if($totresd>0){
		$rowd = mysql_fetch_assoc($resd);
		$dns_dest_code = $rowd["dns_destination_code"] ? trim($rowd["dns_destination_code"]) : "";
		$dns_dest_nm = $rowd["destination_name"] ? trim($rowd["destination_name"]) : "";
		$destination_data = array("dns_destination_code"=>$dns_dest_code,"destination_name"=>$dns_dest_nm);
	}
}
return $destination_data;
}
function get_branch_code_from_cuat_id($cust_id){
$customer_master = "customer_master";
$the_branch_code = "";
	$cust_id = $cust_id ? addslashes(trim($cust_id)) : "";
	if($cust_id!=''){
		$sqls = "select `branch_code` from $customer_master where `customer_code`='$cust_id'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if($totress>0){
			$rows = mysql_fetch_assoc($ress);
			$the_branch_code = $rows["branch_code"] ? trim($rows["branch_code"]) : "";
		}
	}
	return $the_branch_code;
}
function show_dump_name_from_dump_code($the_dump_code,$branch_code){
$the_dump_name = "";
$branch_dump = "branch_dump";
if($the_dump_code!="" && $branch_code!=""){
$sql1 = "select `dump_name` from $branch_dump where `branch_code`='$branch_code' and `dump_code`='$the_dump_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_dump_name = $row1["dump_name"] ? addslashes(trim($row1["dump_name"])) : "";
	}
}
return $the_dump_name;
}

$sswa_user_id = $_SESSION["sswa_user_id"];
$sswa_user_dns_id = $_SESSION["sswa_user_dns_id"];

$sswa_user_type = $_SESSION["sswa_user_type"];
$sswa_selected_dealer_code = $_SESSION["sswa_selected_dealer_code"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];
$add_page_name = "star_make_order.php";
$page_name = "star_make_order.php";

if(isset($_GET["ordr_sts_msg"]) && @$_GET["ordr_sts_msg"]!=""){
$ordr_sts_msg = $_GET["ordr_sts_msg"] ? trim($_GET["ordr_sts_msg"]) : "";
}else{
$ordr_sts_msg = "";	
}
if(isset($_GET["ordr_sts_type"]) && @$_GET["ordr_sts_type"]!=""){
$ordr_sts_type = $_GET["ordr_sts_type"] ? trim($_GET["ordr_sts_type"]) : "";
}else{
$ordr_sts_type = "";	
}



if(@$_POST["continu_btn"]=="CONTINUE"){
$gen_order_data_arr = array();
if($sswa_user_type=="DEALER"){
$user_type = "DEALER";
$login_user_id = "";
}else{
$user_type = "BROKER";
$login_user_id = $sswa_user_dns_id;
}


$prod_qty_arr = $_POST["prod_qty"] ? $_POST["prod_qty"] : array();
$isBlankVal = "YES";
$isNumericVal = "NO";
foreach($prod_qty_arr as $prod_qty_arr_key=>$prod_qty_arr_val){
	$the_prod_qty_arr_key = trim($prod_qty_arr_key);
	$the_prod_qty_arr_val = trim($prod_qty_arr_val);
	if($the_prod_qty_arr_val!=""){
		$isBlankVal = "NO";
		if(is_numeric($the_prod_qty_arr_val)){
		$isNumericVal = "YES";
		}
	}
}
if($isBlankVal=="YES"){
$ordr_sts_msg = "Please enter atleast one product quantity.";
$ordr_sts_type = "2";
header("location:".$page_name."?ordr_sts_type=".$ordr_sts_type."&ordr_sts_msg=".$ordr_sts_msg);
}else if($isNumericVal=="NO"){
$ordr_sts_msg = "Please enter numeric product quantity.";
$ordr_sts_type = "2";
header("location:".$page_name."?ordr_sts_type=".$ordr_sts_type."&ordr_sts_msg=".$ordr_sts_msg);
}else{

$order_for_type = $_POST["order_for_type"] ? $_POST["order_for_type"] : "";
$sl_sd_id = $_POST["sl_sd_id"] ? $_POST["sl_sd_id"] : "";
$consignee_name = $_POST["consignee_name"] ? trim($_POST["consignee_name"]) : "";
$consignee_address = $_POST["consignee_address"] ? trim($_POST["consignee_address"]) : "";
$the_freight = $_POST["the_freight"] ? $_POST["the_freight"] : "";

$sl_des_ex_id = $_POST["sl_des_ex_id"] ? $_POST["sl_des_ex_id"] : "";
$sl_des_for_id = $_POST["sl_des_for_id"] ? $_POST["sl_des_for_id"] : "";
$phone_no = $_POST["phone_no"] ? trim($_POST["phone_no"]) : "";
$dump_status = $_POST["dump_status"] ? $_POST["dump_status"] : "";
$sl_dump_id = $_POST["sl_dump_id"] ? $_POST["sl_dump_id"] : "";

if($order_for_type==""){
$ordr_sts_msg = "Please select ship to.";
$ordr_sts_type = "2";
header("location:".$page_name."?ordr_sts_type=".$ordr_sts_type."&ordr_sts_msg=".$ordr_sts_msg);
}else if($order_for_type=="Sub Dealer" && $sl_sd_id==""){
$ordr_sts_msg = "Please select sub-dealer.";
$ordr_sts_type = "2";
header("location:".$page_name."?ordr_sts_type=".$ordr_sts_type."&ordr_sts_msg=".$ordr_sts_msg);
}else if($consignee_name==""){
$ordr_sts_msg = "Please enter consignee name.";
$ordr_sts_type = "2";
header("location:".$page_name."?ordr_sts_type=".$ordr_sts_type."&ordr_sts_msg=".$ordr_sts_msg);
}else if($consignee_address==""){
$ordr_sts_msg = "Please enter consignee address.";
$ordr_sts_type = "2";
header("location:".$page_name."?ordr_sts_type=".$ordr_sts_type."&ordr_sts_msg=".$ordr_sts_msg);
}else if($the_freight=="EX" && $sl_des_ex_id==""){
$ordr_sts_msg = "Please select destination.";
$ordr_sts_type = "2";
header("location:".$page_name."?ordr_sts_type=".$ordr_sts_type."&ordr_sts_msg=".$ordr_sts_msg);
}else if($the_freight=="FOR" && $sl_des_for_id==""){
$ordr_sts_msg = "Please select destination.";
$ordr_sts_type = "2";
header("location:".$page_name."?ordr_sts_type=".$ordr_sts_type."&ordr_sts_msg=".$ordr_sts_msg);
}else if($phone_no==""){
$ordr_sts_msg = "Please enter phone number.";
$ordr_sts_type = "2";
header("location:".$page_name."?ordr_sts_type=".$ordr_sts_type."&ordr_sts_msg=".$ordr_sts_msg);
}else if($dump_status=="YES" && $sl_dump_id==""){
$ordr_sts_msg = "Please select dump name.";
$ordr_sts_type = "2";
header("location:".$page_name."?ordr_sts_type=".$ordr_sts_type."&ordr_sts_msg=".$ordr_sts_msg);
}else{
$order_for = $consignee_name.",".$consignee_address;
if($the_freight=="EX"){
	$the_dest_code = $sl_des_ex_id;
}else if($the_freight=="FOR"){
	$the_dest_code = $sl_des_for_id;
}else{
	$the_dest_code = "";
}


$destination_data_arr = show_destination_data_code($the_dest_code);
$destination_name = $destination_data_arr["destination_name"];
if($dump_status=="YES"){
$cust_branch_code = get_branch_code_from_cuat_id($sswa_selected_customer_code);
$dump_name = show_dump_name_from_dump_code($sl_dump_id,$cust_branch_code);
}else{
$dump_name = "";	
}

foreach($prod_qty_arr as $prod_qty_arr_key2=>$prod_qty_arr_val2){
	$the_prod_qty_arr_key = trim($prod_qty_arr_key2);
	$the_prod_qty_arr_val = trim($prod_qty_arr_val2);
	if($the_prod_qty_arr_val!=""){
		if(is_numeric($the_prod_qty_arr_val)){
			$gen_order_data_arr[] = array("apporderno"=>"","erporderno"=>"","erporderdt"=>"","order_for"=>$order_for,"order_for_type"=>$order_for_type,"customer_code"=>$sswa_selected_customer_code,"sub_dealer_code"=>$sl_sd_id,"prod_code"=>$the_prod_qty_arr_key,"qty"=>$the_prod_qty_arr_val,"freight"=>$the_freight,"destination_code"=>$the_dest_code,"destination_name"=>$destination_name,"destination_address"=>$destination_name,"phone_no"=>$phone_no,"dump_status"=>$dump_status,"dump_code"=>$sl_dump_id,"dump_name"=>$dump_name);
		}
	}
}

$url_ck = "http://starsaathi.com/acedns_star_save_online_offline_app_order_new_v3.php";
$the_postfields = array("user_type" => $user_type,
						"login_user_id" => $login_user_id,
						"order_data" => $gen_order_data_arr);
						
$the_postfields_string = http_build_query($the_postfields);
/*echo "<pre>";
print_r($the_postfields);
echo "</pre>";
exit;*/
$ch_sheader = curl_init();
curl_setopt($ch_sheader,CURLOPT_URL,$url_ck);
curl_setopt($ch_sheader, CURLOPT_POST, 1);
curl_setopt($ch_sheader, CURLOPT_POSTFIELDS,$the_postfields_string);
curl_setopt($ch_sheader,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch_sheader, CURLOPT_USERAGENT, $useragent);
$body_for_mcode = curl_exec($ch_sheader);
curl_close($ch_sheader);
/*echo $body_for_mcode;
exit;*/
$response_type_check2 = is_json($body_for_mcode);
if($response_type_check2){
$mcode_data_array = json_decode($body_for_mcode, true);
if(array_key_exists("process_status",$mcode_data_array)){
$ordr_sts_msg = $mcode_data_array["process_status"];	
if($process_status_text=="YES"){
$ordr_sts_msg = $mcode_data_array["process_message"];
$ordr_sts_type = "1";
header("location:".$page_name."?ordr_sts_type=".$ordr_sts_type."&ordr_sts_msg=".$ordr_sts_msg);
}else{
$ordr_sts_msg = $mcode_data_array["process_message"];
$ordr_sts_type = "2";
header("location:".$page_name."?ordr_sts_type=".$ordr_sts_type."&ordr_sts_msg=".$ordr_sts_msg);
}
}else{
$ordr_sts_msg = "Something went wrong.";
$ordr_sts_type = "2";
header("location:".$page_name."?ordr_sts_type=".$ordr_sts_type."&ordr_sts_msg=".$ordr_sts_msg);
}
}else{
$ordr_sts_msg = "Something went wrong.";
$ordr_sts_type = "2";
header("location:".$page_name."?ordr_sts_type=".$ordr_sts_type."&ordr_sts_msg=".$ordr_sts_msg);
}

}

	
}

	
}




$sqlsd = "select `customer_code`,`customer_name` from $customer_master where `rds_tag` = '$sswa_selected_customer_code' and `acedns`='Y' order by `customer_name` asc";
$ressd = mysql_query($sqlsd);
$totressd = mysql_num_rows($ressd);

$the_branch_raw_arr = array();
$pgsql_brnc = "select `branch_code` from $customer_master where `customer_code`='$sswa_selected_customer_code'";
$pgres_brnc = mysql_query($pgsql_brnc);
$total_pgres_brnc = mysql_num_rows($pgres_brnc);
if($total_pgres_brnc>0){
	$row_brnc=mysql_fetch_assoc($pgres_brnc);
	$the_branch_raw = $row_brnc["branch_code"] ? trim($row_brnc["branch_code"]) : "";
	if($the_branch_raw!=""){	
	$the_branch_raw_arr = explode(",",$the_branch_raw);
	foreach($the_branch_raw_arr as $the_branch_raw_arr_val){
	$tagged_cust_branch_arr[] = $the_branch_raw_arr_val;
	}
	}
}

$tagged_cust_branch_arr_str = implode("','",$tagged_cust_branch_arr);

$sql_prod="SELECT `prod_code`,`prod_desc` FROM $product_master
 WHERE `acedns`='Y' and `acedns`!='' and `black_list`='N' and `branch_code` IN ('".$tagged_cust_branch_arr_str."') ORDER BY `prod_desc` ASC";
$res_prod = mysql_query($sql_prod);
$totres_prod=mysql_num_rows($res_prod);


$sql_dst_ex="SELECT DM.destination_code,DM.destination_name,DM.ex_for_type FROM $destination_master DM,$branch_destination_freight BDF
WHERE DM.destination_code=BDF.destination_code and DM.ex_for_type ='EX' and BDF.acedns='Y' and BDF.acedns!='' AND BDF.branch_code IN ('".$tagged_cust_branch_arr_str."') ORDER BY DM.destination_name ASC";
$res_dst_ex = mysql_query($sql_dst_ex);
$totres_dst_ex = mysql_num_rows($res_dst_ex);

$sql_dst_for="SELECT DM.destination_code,DM.destination_name,DM.ex_for_type FROM $destination_master DM,$branch_destination_freight BDF
WHERE DM.destination_code=BDF.destination_code and DM.ex_for_type ='FOR' and BDF.acedns='Y' and BDF.acedns!='' AND BDF.branch_code IN ('".$tagged_cust_branch_arr_str."') ORDER BY DM.destination_name ASC";
$res_dst_for = mysql_query($sql_dst_for);
$totres_dst_for = mysql_num_rows($res_dst_for);

$sql_dmp="SELECT `dump_code`,`dump_name` FROM $branch_dump
 WHERE `acedns`='Y' and `acedns`!='' and `is_plant`!='Y' and `is_plant`!='' and `branch_code` IN ('".$tagged_cust_branch_arr_str."') ORDER BY `dump_name` ASC";
$res_dmp = mysql_query($sql_dmp);
$totres_dmp=mysql_num_rows($res_dmp);






include "web_header.php";
?>

<style>
.teEachField1_off_ord{
	display:block;
	width:100%;
	text-align:left;
}
.teEachField2_off_ord{
	display:block;
	width:100%;
	text-align:center;
}
.teEachField3_off_ord{
	display:block;
	width:100%;
	text-align:center;
}
.row .card .table-responsive table.table tbody tr td{
	padding:5px;
}
@media only screen and (max-width: 700px) {
.teEachField1_off_ord{
display:block;
width:auto;
word-wrap: break-word;
font-size: 12px;
white-space: normal;
padding-bottom: 2px;
}
.teEachField2_off_ord{
display:block;
width:auto;
word-wrap: break-word;
font-size: 12px;
white-space: normal;
padding-bottom: 2px;
width:70px;
}
.teEachField3_off_ord{
display:block;
width:auto;
word-wrap: break-word;
font-size: 11px;
white-space: normal;
padding-bottom: 2px;
}

}
.teEachField2_off_ord .prod_qty{
	text-align:center;
	padding: 2px !important;
}
.sd_div,.destination_for_div,.dump_div{
	display:none;
}
.mand_msg{
	color:#FF0000;
	margin-left:5px;
}
.err_msg{
	color:#FF0000;
	margin-right:10px;
}
.ship_to_loader,.sd_loader{
	color:#FF0000;
}
.consignee_name{
	border:1px solid #cccccc !important;
}
.consignee_address{
	border:1px solid #cccccc !important;
}
.phone_no{
	border:1px solid #cccccc !important;
}
.continu_btn{
margin-bottom: 10px;
background-color: #666;
color: #fff;
padding: 9px;
}
.continu_btn:hover, .continu_btn:focus, .continu_btn.focus{
background-color: #666;
color: #fff;
}
.prod_continu_btn{
background-color: #666;
color: #fff;
padding: 9px;
width: 95%;
display: inline-block;
}
.prod_continu_btn:hover, .prod_continu_btn:focus, .prod_continu_btn.focus{
background-color: #666;
color: #fff;
}
.the_back_btn{
margin-bottom: 10px;
background-color: #666;
color: #fff;
padding: 9px;	
}
.the_back_btn:hover, .the_back_btn:focus, .the_back_btn.focus{
background-color: #666;
color: #fff;
}
.form-group .form-control{
padding: 6px 12px;
}
.body{
padding-top: 7px;
padding-left: 10px;
padding-bottom: 10px;
padding-right: 10px;
}
#user_details_div{
	display:none;
}
#user_details_div{
padding:10px !important;	
}
.order_submt_msg{
width:100%;
display:block;
text-align: center;
font-size: 17px;
margin: 10px auto;
font-weight: bold;
}
.msg_succcess_colour{
color: green;	
}
.msg_fail_colour{
color: red;	
}
</style>

<section class="content">
<div class="container-fluid">
<div class="row clearfix">

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding:0px;">
<span class="order_submt_msg <?php if($ordr_sts_type="1"){ echo "msg_succcess_colour";}else if($ordr_sts_type="2"){ echo "msg_fail_colour"; }?>"><?php if($ordr_sts_msg!=""){ echo $ordr_sts_msg;}?></span>
<div class="card">


<form action="" method="POST" enctype="multipart/form-data" name="make_order_form" id="make_order_form" class="make_order_form">
<div class="body" id="product_details_div" style="padding:0px 0px 5px 0px;">
<div class="table-responsive">
<table class="table table-bordered">
<tbody>
<?php
if($totres_prod>0){
	while($row_prod=mysql_fetch_assoc($res_prod)){
		$the_prod_code = $row_prod["prod_code"];
		$the_prod_desc = $row_prod["prod_desc"];
		?>

<tr>
<td>
<div>
<span class="teEachField1_off_ord">
<?php echo $the_prod_desc;?>
</span>
</div>
</td>
<td>
<div>
<span class="teEachField2_off_ord">
<input type="tel" class="form-control prod_qty" name="prod_qty[<?php echo $the_prod_code;?>]" autocomplete="off" id="prod_qty_<?php echo $the_prod_code;?>">
</span>
</div>
</td>
<td>
<div>
<span class="teEachField3_off_ord">
QTY (MT)
</span>
</div>
</td>
</tr>


<?php } ?>

<?php }else{ ?>
<tr>
<td colspan="3" align="center">No Product Assigned For Making Order</td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
<?php
if($totres_prod>0){ ?>
<div class="form-group v_element" style="text-align:center;margin-bottom: 0px;">
<a href="javascript:void(0);" class="btn btn-secondary btn-block prod_continu_btn" name="prod_continu_btn" id="prod_continu_btn">CONTINUE</a>
</div>
<div class="form-group v_element" style="text-align:center;margin:10px auto !important">
<span class="prod_msg_span" id="prod_msg_span"></span>
</div>
<?php } ?>
</div>
<div class="body" id="user_details_div">

<div class="form-group v_element">
<label for="SHIP TO">SHIP TO <span class="mand_msg">*</span><span class="ship_to_loader"></span></label>
<div style=" text-align:left;padding-top:10px;">
<input name="order_for_type" id="order_for_type_self" value="Self" type="radio">
<label for="order_for_type_self">Self</label>
<input name="order_for_type" id="order_for_type_sub_dealer" value="Sub Dealer" type="radio">
<label for="order_for_type_sub_dealer">Sub Dealer</label>
<input name="order_for_type" id="order_for_type_others" value="Others" type="radio">
<label for="order_for_type_others">Others</label>
</div>
</div>

<div class="form-group v_element sd_div">
<label for="Select Sub-Dealer">SELECT SUB-DEALER <span class="mand_msg">*</span> <span class="sd_loader"></span> </label>
<div class="form-line">
<select name="sl_sd_id" id="sl_sd_id" class="form-control sl_sd_id" data-placeholder="Choose Sub-Dealer...">
<option value="">Choose Sub-Dealer...</option>  
<?php
if($totressd>0){
	while($rowsd=mysql_fetch_assoc($ressd)){
		$the_dns_sd_code = $rowsd["customer_code"];
		$the_sd_name = $rowsd["customer_name"];
		?>
<option value="<?php echo $the_dns_sd_code; ?>"><?php echo $the_sd_name; ?></option>        
        
        <?php
	}
}
?>
</select>
</div>
<span class="err_msg" id="sl_sd_id_error"></span>
</div>

<div class="form-group v_element">
<label for="CONSIGNEE NAME">CONSIGNEE NAME <span class="mand_msg">*</span></label>
<input type="text" class="form-control consignee_name" id="consignee_name" autocomplete="off" name="consignee_name">

<span class="err_msg" id="consignee_name_error"></span>
</div>

<div class="form-group v_element">
<label for="CONSIGNEE ADDRESS">CONSIGNEE ADDRESS <span class="mand_msg">*</span></label>

<textarea class="form-control consignee_address" id="consignee_address" rows="3" name="consignee_address"></textarea>

<span class="err_msg" id="consignee_address_error"></span>
</div>

<div class="form-group v_element">
<label for="FREIGHT">FREIGHT <span class="mand_msg">*</span></label>
<div style=" text-align:left;padding-top:10px;">
<input name="the_freight" id="the_freight_ex" value="EX" type="radio" checked="checked">
<label for="the_freight_ex">Ex</label>
<input name="the_freight" id="the_freight_for" value="FOR" type="radio">
<label for="the_freight_for">For</label>
</div>
</div>


<div class="form-group v_element destination_ex_div">
<label for="SELECT DESTINATION">SELECT DESTINATION <span class="mand_msg">*</span></label>
<div class="form-line">
<select name="sl_des_ex_id" id="sl_des_ex_id" class="form-control sl_des_ex_id" data-placeholder="Choose Destination...">
<option value="">Choose Destination...</option>  
<?php
if($totres_dst_ex>0){
	while($row_dst_ex=mysql_fetch_assoc($res_dst_ex)){
		$the_destination_code_ex = $row_dst_ex["destination_code"];
		$the_destination_name_ex = $row_dst_ex["destination_name"];
		?>
<option value="<?php echo $the_destination_code_ex; ?>"><?php echo $the_destination_name_ex; ?></option>        
        
        <?php
	}
}
?>
</select>
</div>
<span class="err_msg" id="sl_des_ex_id_error"></span>
</div>

<div class="form-group v_element destination_for_div">
<label for="SELECT DESTINATION">SELECT DESTINATION <span class="mand_msg">*</span></label>
<div class="form-line">
<select name="sl_des_for_id" id="sl_des_for_id" class="form-control sl_des_for_id" data-placeholder="Choose Destination...">
<option value="">Choose Destination...</option>  
<?php
if($totres_dst_for>0){
	while($row_dst_for=mysql_fetch_assoc($res_dst_for)){
		$the_destination_code_for = $row_dst_for["destination_code"];
		$the_destination_name_for = $row_dst_for["destination_name"];
		?>
<option value="<?php echo $the_destination_code_for; ?>"><?php echo $the_destination_name_for; ?></option>        
        
        <?php
	}
}
?>
</select>
</div>
<span class="err_msg" id="sl_des_for_id_error"></span>
</div>


<div class="form-group v_element">
<label for="PHONE NO">PHONE NO <span class="mand_msg">*</span></label>
<input type="tel" class="form-control phone_no" id="phone_no" name="phone_no" maxlength="10" autocomplete="off">

<span class="err_msg" id="phone_no_error"></span>
</div>


<div class="form-group v_element">
<label for="DUMP DETAILS">DUMP DETAILS</label>
<div style=" text-align:left;padding-top:10px;">
<input name="dump_status" id="dump_status_no" value="NO" type="radio" checked="checked">
<label for="dump_status_no">No</label>
<input name="dump_status" id="dump_status_yes" value="YES" type="radio">
<label for="dump_status_yes">Yes</label>
</div>
</div>

<div class="form-group v_element dump_div">
<label for="SELECT DUMP NAME">SELECT DUMP NAME <span class="mand_msg">*</span></label>
<div class="form-line">
<select name="sl_dump_id" id="sl_dump_id" class="form-control sl_dump_id" data-placeholder="Choose Dump Name...">
<option value="">Choose Dump Name...</option>  
<?php
if($totres_dmp>0){
	while($row_dmp=mysql_fetch_assoc($res_dmp)){
		$the_dump_code = $row_dmp["dump_code"];
		$the_dump_name = $row_dmp["dump_name"];
		?>
<option value="<?php echo $the_dump_code; ?>"><?php echo $the_dump_name; ?></option>        
        
        <?php
	}
}
?>
</select>
</div>
<span class="err_msg" id="sl_dump_id_error"></span>
</div>

<div class="form-group v_element" style="text-align:center;margin-bottom: 15px;">
<input type="submit" class="btn btn-secondary btn-block continu_btn" name="continu_btn" style="margin-bottom:10px;" value="CONTINUE">
</div>

<div class="form-group v_element" style="text-align:center;margin-bottom: 0px;">
<a href="javascript:void(0);" class="btn btn-secondary btn-block the_back_btn" name="the_back_btn" id="the_back_btn">BACK</a>
</div>


</div>
</form>



</div>
</div>



</div>
</div>
</section>
<script type="text/javascript">
jQuery(function(){
var the_dlr_id = "<?php echo $sswa_selected_dealer_code;?>";
var the_cust_id = "<?php echo $sswa_selected_customer_code;?>";
var xhrself,xhrsubdealer;
var img1 = '<img src="images/ajax-loader.gif">';


setTimeout(function(){
jQuery(".order_submt_msg").html("");
},12000);

jQuery('#sl_sd_id').chosen({width:"100%",no_results_text:'Oops, no Sub-Dealer found!'});

jQuery('#sl_des_ex_id').chosen({width:"100%",no_results_text:'Oops, no Destination found!'});
jQuery('#sl_des_for_id').chosen({width:"100%",no_results_text:'Oops, no Destination found!'});
jQuery('#sl_dump_id').chosen({width:"100%",no_results_text:'Oops, no Dump Name found!'});


jQuery("#prod_continu_btn").click(function(){
var prod_msg_span_elmnt = jQuery("#prod_msg_span");
var numchk = /^[0-9]+\.?[0-9]*$/;
var isBlankVal = "YES";
var isNumericVal = "NO";
jQuery.each(jQuery("#product_details_div .table-responsive .table-bordered tbody tr td div span.teEachField2_off_ord .prod_qty"), function(){
var pval = jQuery.trim(jQuery(this).val());
if(pval!=""){
	isBlankVal = "NO";
	if( numchk.test(pval) ){
	isNumericVal = "YES";
	}
}
});
if(isBlankVal=="YES"){
prod_msg_span_elmnt.html("Please enter atleast one product quantity.");
setTimeout(function(){
prod_msg_span_elmnt.html("");
},5000);
}else if(isNumericVal=="NO"){
prod_msg_span_elmnt.html("Please enter numeric product quantity.");
setTimeout(function(){
prod_msg_span_elmnt.html("");
},5000);
}else{
prod_msg_span_elmnt.html("");
jQuery("#product_details_div").hide();
jQuery("#user_details_div").show();
}
});

jQuery("#the_back_btn").click(function(){
jQuery("#user_details_div").hide();
jQuery("#product_details_div").show();
});

jQuery(".prod_qty").on("keypress keyup blur",function (event) {
jQuery(this).val(jQuery(this).val().replace(/[^0-9\.]/g,''));
if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
event.preventDefault();
}
});

jQuery("#phone_no").on("keypress keyup blur",function (event) {    
jQuery(this).val(jQuery(this).val().replace(/[^\d].+/, ""));
if ((event.which < 48 || event.which > 57)) {
event.preventDefault();
}
});


jQuery("form#make_order_form").submit(function(){

var numchk = /^[0-9]+\.?[0-9]*$/;
var isBlankVal = "YES";
var isNumericVal = "NO";
jQuery.each(jQuery("#product_details_div .table-responsive .table-bordered tbody tr td div span.teEachField2_off_ord .prod_qty"), function(){
var pval = jQuery.trim(jQuery(this).val());
if(pval!=""){
	isBlankVal = "NO";
	if( numchk.test(pval) ){
	isNumericVal = "YES";
	}
}
});
if(isBlankVal=="YES"){
jQuery("#user_details_div").hide();
jQuery("#product_details_div").show();
return false;
}else if(isNumericVal=="NO"){
jQuery("#user_details_div").hide();
jQuery("#product_details_div").show();
return false;
}else{


var order_for_type = jQuery("input[name='order_for_type']:checked").val();
var sl_sd_id = jQuery.trim(jQuery("#sl_sd_id").val());
var consignee_name = jQuery.trim(jQuery("#consignee_name").val());
var consignee_address = jQuery.trim(jQuery("#consignee_address").val());

var the_freight = jQuery("input[name='the_freight']:checked").val();
var sl_des_ex_id = jQuery.trim(jQuery("#sl_des_ex_id").val());
var sl_des_for_id = jQuery.trim(jQuery("#sl_des_for_id").val());

var phone_no = jQuery.trim(jQuery("#phone_no").val());

var dump_status = jQuery("input[name='dump_status']:checked").val();
var sl_dump_id = jQuery.trim(jQuery("#sl_dump_id").val());

if(order_for_type==undefined){
jQuery(".ship_to_loader").html("Please select ship to.");
jQuery("#order_for_type_self").focus();
setTimeout(function(){
jQuery(".ship_to_loader").html("");
},5000);
return false;
}else if(order_for_type=="Sub Dealer" && sl_sd_id==""){
jQuery(".sd_loader").html("Please select sub-dealer.");
jQuery("#sl_sd_id").focus();
setTimeout(function(){
jQuery(".sd_loader").html("");
},5000);
return false;
}else if(consignee_name==""){
jQuery("#consignee_name_error").html("Please enter consignee name.");
jQuery("#consignee_name").focus();
setTimeout(function(){
jQuery("#consignee_name_error").html("");
},5000);
return false;
}else if(consignee_address==""){
jQuery("#consignee_address_error").html("Please enter consignee address.");
jQuery("#consignee_address").focus();
setTimeout(function(){
jQuery("#consignee_address_error").html("");
},5000);
return false;
}else if(the_freight=="EX" && sl_des_ex_id==""){
jQuery("#sl_des_ex_id_error").html("Please select destination.");
jQuery("#sl_des_ex_id").focus();
setTimeout(function(){
jQuery("#sl_des_ex_id_error").html("");
},5000);
return false;
}else if(the_freight=="FOR" && sl_des_for_id==""){
jQuery("#sl_des_for_id_error").html("Please select destination.");
jQuery("#sl_des_for_id").focus();
setTimeout(function(){
jQuery("#sl_des_for_id_error").html("");
},5000);
return false;
}else if(phone_no==""){
jQuery("#phone_no_error").html("Please enter phone number.");
jQuery("#phone_no").focus();
setTimeout(function(){
jQuery("#phone_no_error").html("");
},5000);
return false;
}else if(dump_status=="YES" && sl_dump_id==""){
jQuery("#sl_dump_id_error").html("Please select dump name.");
jQuery("#sl_des_ex_id").focus();
setTimeout(function(){
jQuery("#sl_dump_id_error").html("");
},5000);
return false;
}else{
	jQuery(".page-loader-wrapper").show();
return true;
}


}
	
});	

var ship_to_loader_elmnt = jQuery(".ship_to_loader");
var sd_loader_elmnt = jQuery(".sd_loader");

jQuery('input[name="order_for_type"]').change(function(){
var order_for_type = jQuery(this).val();
jQuery("#sl_sd_id").val("");
jQuery("#sl_sd_id").trigger("chosen:updated")
if(order_for_type=="Self"){
	jQuery(".sd_div").hide();
ship_to_loader_elmnt.html(img1);
if(xhrself && xhrself.readystate != 4){
xhrself.abort();
}
xhrself = jQuery.ajax({
url: 'ajax_get_user_details.php',
type: 'post',
dataType: 'json',
data: "dealer_id="+the_cust_id+"&user_type=dealer",
success: function(response){
if(response.process_sts=="YES"){
var user_name = response.user_name;
var user_address = response.user_address;
var user_phone = response.user_phone;
jQuery("#consignee_name").val(user_name);
jQuery("#consignee_address").val(user_address);
jQuery("#phone_no").val(user_phone);
ship_to_loader_elmnt.html("");
sd_loader_elmnt.html("");
}else{
alert(response.process_msg);
jQuery("#consignee_name").val("");
jQuery("#consignee_address").val("");
jQuery("#phone_no").val("");
ship_to_loader_elmnt.html("");
sd_loader_elmnt.html("");
}					
},
timeout : 0
});
	
}else if(order_for_type=="Sub Dealer"){
	jQuery(".sd_div").show();
	jQuery("#consignee_name").val("");
	jQuery("#consignee_address").val("");
	jQuery("#phone_no").val("");
	ship_to_loader_elmnt.html("");
sd_loader_elmnt.html("");
}else if(order_for_type=="Others"){
	jQuery(".sd_div").hide();
	jQuery("#consignee_name").val("");
	jQuery("#consignee_address").val("");
	jQuery("#phone_no").val("");
	ship_to_loader_elmnt.html("");
sd_loader_elmnt.html("");
}
});



jQuery("#sl_sd_id").change(function(){
	var sl_sd_id = jQuery.trim(jQuery(this).val());
	if(sl_sd_id!=""){
sd_loader_elmnt.html(img1);
if(xhrsubdealer && xhrsubdealer.readystate != 4){
xhrsubdealer.abort();
}
xhrsubdealer = jQuery.ajax({
url: 'ajax_get_user_details.php',
type: 'post',
dataType: 'json',
data: "dealer_id="+sl_sd_id+"&user_type=subdealer",
success: function(response){
if(response.process_sts=="YES"){
var user_name = response.user_name;
var user_address = response.user_address;
var user_phone = response.user_phone;
jQuery("#consignee_name").val(user_name);
jQuery("#consignee_address").val(user_address);
jQuery("#phone_no").val(user_phone);
ship_to_loader_elmnt.html("");
sd_loader_elmnt.html("");
}else{
alert(response.process_msg);
jQuery("#consignee_name").val("");
jQuery("#consignee_address").val("");
jQuery("#phone_no").val("");
ship_to_loader_elmnt.html("");
sd_loader_elmnt.html("");
}					
},
timeout : 0
});
	}else{
		jQuery("#consignee_name").val("");
		jQuery("#consignee_address").val("");
		jQuery("#phone_no").val("");
		ship_to_loader_elmnt.html("");
		sd_loader_elmnt.html("");
	}
});


jQuery('input[name="the_freight"]').change(function(){
var the_freight = jQuery(this).val();
if(the_freight=="EX"){
	jQuery(".destination_ex_div").show();
	jQuery(".destination_for_div").hide();
}else if(the_freight=="FOR"){
	jQuery(".destination_for_div").show();
	jQuery(".destination_ex_div").hide();
}else{
	jQuery(".destination_ex_div").show();
	jQuery(".destination_for_div").hide();
}
});

jQuery('input[name="dump_status"]').change(function(){
var dump_status = jQuery(this).val();
if(dump_status=="NO"){
	jQuery(".dump_div").hide();
}else if(dump_status=="YES"){
	jQuery(".dump_div").show();
}else{
	jQuery(".dump_div").hide();
}
});







});
</script>
<?php
include "web_footer.php";
mysql_close();
?>
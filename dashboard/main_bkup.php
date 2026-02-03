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
$page_name = "main.php";
$order_show_branch="";
$dnsbcarr = array();
$dnsbcstr ="";
$theactbcarr = array();
$theactbcstr ="";

$sswa_user_type = $_SESSION["sswa_user_type"];
$sswa_the_broker_id = $_SESSION["sswa_user_id"];
$sswa_the_dns_broker_id = $_SESSION["sswa_user_dns_id"];
$sswa_selected_dealer_code = $_SESSION["sswa_selected_dealer_code"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];

$sql3 = "select `dns_customer_code`,`customer_id` from $customer_master where `customer_code`='$sswa_selected_customer_code'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
$row3 = mysql_fetch_assoc($res3);
$the_dealer_id = trim($row3["dns_customer_code"]);
$the_customer_id = trim($row3["customer_id"]);
}else{
$the_customer_id = "";
}

if(strtoupper($sswa_user_type)=='DEALER'){
/*----Tracklog Code Start-----*/
$curr_date_time = date("Y-m-d H:i:s");
$webservice_name = "HOME";
$sqlin_tl = "insert into `webservice_track_log` (`customer_code`,`webservice_name`,`details`,`datetime`) values ('$the_customer_id','$webservice_name','','$curr_date_time')";
$resin_tl = mysql_query($sqlin_tl);
/*----Tracklog Code End-----*/
}


if($sswa_user_type=="SP"){
	
$sql_dlr = "select $customer_master.`dns_customer_code`,$customer_master.`customer_name` from $customer_broker_relation left join $customer_master on $customer_broker_relation.`customer_code`=$customer_master.`customer_code` where $customer_broker_relation.`broker_code`='$sswa_the_broker_id' and $customer_broker_relation.`acedns`='Y' order by $customer_master.`customer_name` asc";
$res_dlr = mysql_query($sql_dlr);
$totres_dlr = mysql_num_rows($res_dlr);

}else{
$totres_dlr = 0;	
}


$total_ledger_balance = 0;
$total_scheme = 0;
$total_notification = 0;

/*$pgsql_lgr = "select `balance` from $ledger_balance where `dns_customer_code`='$sswa_selected_dealer_code'";
$pgres_lgr = mysql_query($pgsql_lgr);
$total_pgres_lgr = mysql_num_rows($pgres_lgr);
if($total_pgres_lgr>0){
	$row_lgr=mysql_fetch_assoc($pgres_lgr);
	$total_ledger_balance = $row_lgr["balance"] ? trim($row_lgr["balance"]) : 0;
	if($total_ledger_balance==""){
	$total_ledger_balance = 0;	
	}
}*/
if($the_customer_id!="" ){

$add_page_name = "ledger_balance.php";
$page_name = "ledger_balance.php";
$cnt = 0;
$countrow = 1;
$url_ck1 = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/ZFI_CREDIT_LIMIT_CDS/ZFI_Credit_limit(kunnr=\''.$the_customer_id.'\')?$format=json&sap-client=900';
$body_for_mcode1 = get_data_from_cserver($url_ck1);
if(isJsonCk($body_for_mcode1)){
	$json_decoded = json_decode($body_for_mcode1,true);
	if(count($json_decoded)>0){
		if(array_key_exists("d",$json_decoded)){
		$kunnr = $json_decoded["d"]["kunnr"];
		$name1 = $json_decoded["d"]["name1"];
		$name2 = $json_decoded["d"]["name2"];
		$name3 = $json_decoded["d"]["name3"];
		$credit_limit = $json_decoded["d"]["credit_limit"];
		$credit_expose = $json_decoded["d"]["credit_expose"];
		$total_ledger_balance=$credit_expose;
		}
		
	}
	else $total_ledger_balance=0;
}
}
else $total_ledger_balance=0;

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
if(count($tagged_cust_branch_arr)>0){
	$tagged_cust_branch_str = implode("','",$tagged_cust_branch_arr);
$sql_scm="SELECT count(`sl_no`) as `all_scm_count` FROM $branch_schemes_PDF where `acedns`='Y' AND branch_code IN ('".$tagged_cust_branch_str."') and CURDATE() 
between `start_date` and `end_date`";
$res_scm = mysql_query($sql_scm);
$total_pgres_scm = mysql_num_rows($res_scm);
if($total_pgres_scm>0){
	$row_scm=mysql_fetch_assoc($res_scm);
	$total_scheme = $row_scm["all_scm_count"];
}


$tagged_cust_branch_for_noti_arr = $tagged_cust_branch_arr;
$tagged_cust_branch_for_noti_arr[] = "ALL";
$tagged_cust_branch_for_noti_arr_str = implode("|",$tagged_cust_branch_for_noti_arr);
$sql_noti = "select count(`id`) as `all_noti_count` from $notification_message where `branch_code` REGEXP '".$tagged_cust_branch_for_noti_arr_str."'";
$res_noti = mysql_query($sql_noti);
$tot_noti = mysql_num_rows($res_noti);
if($tot_noti>0){
$row_noti=mysql_fetch_assoc($res_noti);
$total_notification = $row_noti["all_noti_count"];	
}
}

include "web_header.php";
?>


    <section class="content">
        <div class="container-fluid">
            <!-- Widgets -->
            <div class="row clearfix">
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="info-box bg-pink hover-expand-effect">
                    <a href="ledger_balance.php">
                        <div class="icon">
                            <i class="material-icons">playlist_add_check</i>
                        </div>
                        </a>
                        <div class="content">
                            <div class="text">LEDGER</div>
                            <p><strong><?php echo $total_ledger_balance;?></strong></p>
                        </div>
                        
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="info-box bg-cyan hover-expand-effect">
                    <a href="show_scheme.php">
                        <div class="icon">
                            <i class="material-icons">help</i>
                        </div>
                        </a>
                        <div class="content">
                            <div class="text">SCHEME</div>
                            <p><strong><?php echo $total_scheme;?></strong></p>
                            
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="info-box bg-light-green hover-expand-effect">
                    <a href="show_nitifications.php">
                        <div class="icon">
                            <i class="material-icons">forum</i>
                        </div>
                         </a>
                        <div class="content">
                            <div class="text">NOTIFICATION</div>
                            <p><strong><?php echo $total_notification;?></strong></p>
                        </div>
                    </div>
                </div>
                
            </div>

<?php if($sswa_user_type=="SP"){ ?>           
<div class="row clearfix">
<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
<label>Choose a Dealer</label>
</div>
<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
 <select class="form-control" id="astn_dlsbdl_code" name="astn_dlsbdl_code" style="padding-left:2px;">
<?php
if($totres_dlr>0){
	while($row_dlr=mysql_fetch_assoc($res_dlr)){
		$the_br_dns_customer_code = $row_dlr["dns_customer_code"];
		$the_br_customer_name = $row_dlr["customer_name"];?>
        <option value="<?php echo $the_br_dns_customer_code;?>" <?php if($the_br_dns_customer_code==$sswa_selected_dealer_code){ ?> selected="selected" <?php } ?>><?php echo $the_br_customer_name;?></option>
		<?php
	}
	
}
?>

</select>   
</div>
<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
    
</div>

</div>
<?php } ?>             
        </div>
    </section>

<script type="text/javascript">
jQuery(document).ready(function(){

<?php if($sswa_user_type=="SP"){ ?>
var xhrconflgin;
jQuery('#astn_dlsbdl_code').chosen({width:"100%",no_results_text:'Oops, no dealer found!',search_contains: true}).change(function() {
    var sel_sdvl = jQuery(this).val();
	if(sel_sdvl!=""){
		if(xhrconflgin && xhrconflgin.readystate != 4){
			xhrconflgin.abort();
			}
			xhrconflgin = jQuery.ajax({
			url: 'ajax_select_dealer_for_delails.php',
			type: 'post',
			dataType: 'json',
			data: "dealer_id="+sel_sdvl,
			success: function(response){
			if(response.process_sts=="YES"){
			window.location = "<?php echo $page_name;?>";
			}else{
			window.location = "<?php echo $page_name;?>";
			}					
			},
			timeout : 0
			});
		
	}else{
		window.location = "<?php echo $page_name;?>";
	}

});

<?php } ?>

});
</script>
<?php
include "web_footer.php";
mysql_close();
?>
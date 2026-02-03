<?php
include "web_check.php";
include "star_connection.php";
//  echo $_SESSION["sswa_user_type"];die;
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
$sswa_selected_customer_type = $_SESSION["sswa_selected_customer_type"];

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
	if ($sswa_selected_customer_type == "Sub Dealer" || $sswa_selected_customer_type == "RSSD") {
        $sql_dlr = "select $customer_master.`dns_customer_code`,$customer_master.`cust_type`,$customer_master.`customer_name` from $customer_broker_relation left join $customer_master on $customer_broker_relation.`customer_code`=$customer_master.`customer_code` where $customer_master.`cust_type`='$sswa_selected_customer_type' and $customer_broker_relation.`acedns`='Y' order by $customer_master.`customer_name` asc";
    }else{
        $sql_dlr = "select $customer_master.`dns_customer_code`,$customer_master.`cust_type`,$customer_master.`customer_name` from $customer_broker_relation left join $customer_master on $customer_broker_relation.`customer_code`=$customer_master.`customer_code` where $customer_broker_relation.`broker_code`='$sswa_the_broker_id' and $customer_master.`cust_type`='dealer' and $customer_broker_relation.`acedns`='Y' order by $customer_master.`customer_name` asc";
    }
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
$url_ck1 = 'https://starfiori.starcement.co.in:44300/sap/opu/odata/sap/ZFI_CREDIT_LIMIT_CDS/ZFI_Credit_limit(kunnr=\''.$the_customer_id.'\')?$format=json&sap-client=900';
$body_for_mcode1 = get_data_from_cserver($url_ck1);
if(isJsonCk($body_for_mcode1)){
	$json_decoded = json_decode($body_for_mcode1,true);
	if(count($json_decoded)>0){
		if(array_key_exists("d",$json_decoded)){
		$kunnr = $json_decoded["d"]["kunnr"];
		$name1 = $json_decoded["d"]["name1"];
		$name2 = $json_decoded["d"]["name2"];
		$name3 = $json_decoded["d"]["name3"];
		$credit_expose = $json_decoded["d"]["credit_expose"];
		$credit_limit = $json_decoded["d"]["credit_limit"];
		
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

$curr_date = date("Y-m-d");
$curr_month=date("m");
$curr_year=date("Y");

/*
if($curr_month >= '04'){
$from_date=$curr_year.'-'.'04-01';
}
else
{
	$from_date=($curr_year-1).'-'.'04-01';
}
*/

$from_date="2022-07-31";


$to_date=$curr_date;
$url_ck1= BASE_URL ."dealerwise-credit-limit-s-deposit.php?customer_code=$the_customer_id&from_date=$from_date&to_date=$to_date";

$body_for_mcode1 = get_data_from_cserver($url_ck1);

if(isJsonCk($body_for_mcode1)){
$json_decoded = json_decode($body_for_mcode1,true);
if(count($json_decoded)>0){
	$credit_expose = $json_decoded["credit_expose"] ? trim($json_decoded["credit_expose"]) : "";
	$credit_limit = $json_decoded["credit_limit"] ? trim($json_decoded["credit_limit"]) : "";
	
	$security_deposit = $json_decoded["security_deposit"] ? trim($json_decoded["security_deposit"]) : "";
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
				<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="info-box bg-pink hover-expand-effect">
                    <a href="show_nitifications.php">
                        <div class="icon">
                            <i class="material-icons">forum</i>
                        </div>
                         </a>
                        <div class="content">
                            <div class="text">OUTSTANDING BALANCE</div>
                            <p><strong><?php echo $credit_expose;?></strong></p>
                        </div>
                    </div>
                </div>
				
				
				<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="info-box bg-cyan hover-expand-effect">
                    <a href="show_nitifications.php">
                        <div class="icon">
                            <i class="material-icons">forum</i>
                        </div>
                         </a>
                        <div class="content">
                            <div class="text">CREDIT LIMIT</div>
                            <p><strong><?php echo $credit_limit;?></strong></p>
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
                            <div class="text">SECURITY DEPOSIT</div>
                            <p><strong><?php echo $security_deposit;?></strong></p>
                        </div>
                    </div>
                </div>
                
            </div>

<?php if($sswa_user_type=="SP"){ ?>           
<div class="row clearfix">
<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
<label>Choose</label>
<label style="margin-right: 15px;">
<input type="radio" name="customer_type" class="customer_type" value="dealer" checked
        style="position: static !important; left: 0 !important; opacity: 1 !important; display: inline-block !important;">
DEALER
</label>

<label style="margin-right: 15px;">
<input type="radio" name="customer_type" class="customer_type" value="subdealer"
        style="position: static !important; left: 0 !important; opacity: 1 !important; display: inline-block !important;">
SUB DEALER
</label>

<label>
<input type="radio" name="customer_type" class="customer_type" value="rssd"
        style="position: static !important; left: 0 !important; opacity: 1 !important; display: inline-block !important;">
RSSD
</label>
</div>
<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
    <div class="dealer-select-wrap">
 <select class="form-control dealer" id="astn_dlsbdl_code" name="astn_dlsbdl_code" style="padding-left:2px;">
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

<div class="subdealer-select-wrap" style="display:none;">
 <select class="form-control subdealer" id="astn_dlsbdl_code" name="astn_dlsbdl_code" style="padding-left:2px;">
<?php
$sql_subdlr = "select $customer_master.`dns_customer_code`,$customer_master.`cust_type`,$customer_master.`customer_name` from $customer_broker_relation left join $customer_master on $customer_broker_relation.`customer_code`=$customer_master.`customer_code` where $customer_master.`cust_type`='sub dealer' and $customer_broker_relation.`acedns`='Y' order by $customer_master.`customer_name` asc";
$res_subdlr = mysql_query($sql_subdlr);
if($res_subdlr){
	while($row_subdlr=mysql_fetch_assoc($res_subdlr)){
		$the_br_dns_customer_code = $row_subdlr["dns_customer_code"];
		$the_br_customer_name = $row_subdlr["customer_name"];?>
        <option value="<?php echo $the_br_dns_customer_code;?>" <?php if($the_br_dns_customer_code==$sswa_selected_dealer_code){ ?> selected="selected" <?php } ?>><?php echo $the_br_customer_name;?></option>
		<?php
	}
	
}
?>
</select>
</div>

<div class="rssd-select-wrap" style="display:none;">
 <select class="form-control rssd" id="astn_dlsbdl_code" name="astn_dlsbdl_code" style="padding-left:2px; display:none;">
<?php
$sql_rssd = "select $customer_master.`dns_customer_code`,$customer_master.`cust_type`,$customer_master.`customer_name` from $customer_broker_relation left join $customer_master on $customer_broker_relation.`customer_code`=$customer_master.`customer_code` where $customer_master.`cust_type`='rssd' and $customer_broker_relation.`acedns`='Y' order by $customer_master.`customer_name` asc";
$res_rssd = mysql_query($sql_rssd);
if($res_rssd){
	while($row_rssd=mysql_fetch_assoc($res_rssd)){
		$the_br_dns_customer_code = $row_rssd["dns_customer_code"];
		$the_br_customer_name = $row_rssd["customer_name"];?>
        <option value="<?php echo $the_br_dns_customer_code;?>" <?php if($the_br_dns_customer_code==$sswa_selected_dealer_code){ ?> selected="selected" <?php } ?>><?php echo $the_br_customer_name;?></option>
		<?php
	}
	
}
?>
</select>
</div>
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
// jQuery('#astn_dlsbdl_code').chosen({width:"100%",no_results_text:'Oops, no dealer found!',search_contains: true}).change(function() {
//     var sel_sdvl = jQuery(this).val();
// 	if(sel_sdvl!=""){
// 		if(xhrconflgin && xhrconflgin.readystate != 4){
// 			xhrconflgin.abort();
// 			}
// 			xhrconflgin = jQuery.ajax({
// 			url: 'ajax_select_dealer_for_delails.php',
// 			type: 'post',
// 			dataType: 'json',
// 			data: "dealer_id="+sel_sdvl,
// 			success: function(response){
// 			if(response.process_sts=="YES"){
// 			window.location = "<?php echo $page_name;?>";
// 			}else{
// 			window.location = "<?php echo $page_name;?>";
// 			}					
// 			},
// 			timeout : 0
// 			});
		
// 	}else{
// 		window.location = "<?php echo $page_name;?>";
// 	}

// });


    jQuery('.dealer, .subdealer, .rssd').chosen({
        width: "100%",
        no_results_text: 'Oops, no result found!',
        search_contains: true
    }).change(function() {
        var selectedType = $('input[name="customer_type"]:checked').val();
        var sel_sdvl = jQuery(this).val();
        if (sel_sdvl != "") {
            if (xhrconflgin && xhrconflgin.readyState != 4) {
                xhrconflgin.abort();
            }
            xhrconflgin = jQuery.ajax({
                url: 'ajax_select_dealer_for_delails.php',
                type: 'post',
                dataType: 'json',
                // data: "dealer_id=" + sel_sdvl,
                data: {
                    dealer_id: sel_sdvl,
                    customer_type: selectedType
                },
                success: function(response) {
                    console.log(response);
                    window.location = "<?php echo $page_name;?>";
                },
                timeout: 0
            });
        } else {
            window.location = "<?php echo $page_name;?>";
        }
    });

    // Handle radio button change
    jQuery('.customer_type').on('change', function() {
        var selectedType = jQuery(this).val();

        // Hide all select wrappers
        jQuery('.dealer-select-wrap, .subdealer-select-wrap, .rssd-select-wrap').hide();

        // Show the one corresponding to selected type
        if (selectedType === 'dealer') {
            jQuery('.dealer-select-wrap').show();
        } else if (selectedType === 'subdealer') {
            jQuery('.subdealer-select-wrap').show();
        } else if (selectedType === 'rssd') {
            jQuery('.rssd-select-wrap').show();
        }

        // Update Chosen visibility
        jQuery('.dealer, .subdealer, .rssd').trigger('chosen:updated');
    });

    // Trigger change on load
    jQuery('.customer_type:checked').trigger('change');

<?php } ?>

});
</script>
<?php
include "web_footer.php";
mysql_close();
?>
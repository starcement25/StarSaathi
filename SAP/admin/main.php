<?php
include "web_check.php";
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$employee_master = "employee_master";
$customer_master = "customer_master";
$branch_master = "branch_master";
$order_show_branch="";
$dnsbcarr = array();
$dnsbcstr ="";
$theactbcarr = array();
$theactbcstr ="";

if($_SESSION["start_user_type"]=="MANAGER"){
	$order_show_branch = $_SESSION["order_show_branch"] ? trim($_SESSION["order_show_branch"]) : "";
	
	if($order_show_branch=="NE"){
	$dns_branchcode_master = "north_east_branch";
	}else if($order_show_branch=="NOTNE"){
	$dns_branchcode_master = "not_north_east_branch";
	}
	if($order_show_branch=="NE" || $order_show_branch=="NOTNE"){	
	$sqlbm = "select `branch_code` from $dns_branchcode_master";
	$resbm = mysql_query($sqlbm);
	$totresbm = mysql_num_rows($resbm);
	if($totresbm>0){
		$dnsbcarr = array();
		while($rowbm=mysql_fetch_assoc($resbm)){
			$the_dns_bc = $rowbm["branch_code"] ? trim($rowbm["branch_code"]) : "";
			if($the_dns_bc!=""){
				$dnsbcarr[] = $the_dns_bc;
			}
		}
		if(count($dnsbcarr)>0){
			$dnsbcstr = implode("','",$dnsbcarr);
	$sqlabc = "select `branch_code` from $branch_master where `dns_branch_code` in('".$dnsbcstr."')";
	$resabc = mysql_query($sqlabc);
	$totresabc = mysql_num_rows($resabc);
	if($totresabc>0){
		while($rowabc=mysql_fetch_assoc($resabc)){
			$the_bc = $rowabc["branch_code"] ? trim($rowabc["branch_code"]) : "";
			if($the_bc!=""){
				$theactbcarr[] = $the_bc;
			}
		}
		if(count($theactbcarr)>0){
			$theactbcstr = implode("','",$theactbcarr);
			
		}
		
	}
	
		}
	}
	}else{
		if($order_show_branch!=""){
			if($order_show_branch=="MISNE"){
				$whr_qry = " where `branch_state`='NE' ";
			}else if($order_show_branch=="MISROE"){
				$whr_qry = " where `branch_state` in('BIHAR','WB') ";
			}else if($order_show_branch=="MISALL"){
				$whr_qry = " where `branch_state` in('BIHAR','WB','NE') ";				
			}else{
				$whr_qry = " where `branch_state`='$order_show_branch' ";
			}
			$sqlabc = "select `branch_code` from $branch_master $whr_qry ";
			$resabc = mysql_query($sqlabc);
			$totresabc = mysql_num_rows($resabc);
			if($totresabc>0){
			while($rowabc=mysql_fetch_assoc($resabc)){
			$the_bc = $rowabc["branch_code"] ? trim($rowabc["branch_code"]) : "";
			if($the_bc!=""){
			$theactbcarr[] = $the_bc;
			}
			}
			if(count($theactbcarr)>0){
			$theactbcstr = implode("','",$theactbcarr);
			
			}
			
			}
			
			
		}
		
	}
	
	
	
	
}

$all_order_count = 0;
$total_pending_order_count = 0;
$total_dealers = 0;
$total_sub_dealers = 0;
$pgsql4 = "select count(`customer_code`) as `all_sub_dealer_count` from $customer_master where `cust_type`='Sub Dealer' and `acedns`='Y'";
$pgres4 = mysql_query($pgsql4);
$total_pgres4 = mysql_num_rows($pgres4);
if($total_pgres4>0){
	$row14=mysql_fetch_assoc($pgres4);
	$total_sub_dealers = $row14["all_sub_dealer_count"];
}

$pgsql3 = "select count(`customer_code`) as `all_dealer_count` from $customer_master where `cust_type`='Dealer' and `acedns`='Y'";
$pgres3 = mysql_query($pgsql3);
$total_pgres3 = mysql_num_rows($pgres3);
if($total_pgres3>0){
	$row13=mysql_fetch_assoc($pgres3);
	$total_dealers = $row13["all_dealer_count"];
}
if($_SESSION["start_user_type"]=="MANAGER" && $order_show_branch!=""){
//$pgsql2 = "select count(`APPORDERNO`) as `all_ord_count` from $t_apperpdo left join $customer_master on $t_apperpdo.`dns_customer_code`=$customer_master.`dns_customer_code` where $t_apperpdo.`APPORDERNO`!='' and $customer_master.`branch_code` in('".$theactbcstr."')";
$pgsql2 = "select count(`APPORDERNO`) as `all_ord_count` from $t_apperpdo left join $customer_master on $t_apperpdo.`customer_code`=$customer_master.`customer_code` where $t_apperpdo.`APPORDERNO`!=''  and $customer_master.`branch_code` in('".$theactbcstr."') $new_whr_str ";

}else{
$pgsql2 = "select count(`APPORDERNO`) as `all_ord_count` from $t_apperpdo where `APPORDERNO`!=''";
}
$pgres2 = mysql_query($pgsql2);
$total_pgres2 = mysql_num_rows($pgres2);
if($total_pgres2>0){
	$row12=mysql_fetch_assoc($pgres2);
	$all_order_count = $row12["all_ord_count"];
}
if($_SESSION["start_user_type"]=="MANAGER" && $order_show_branch!=""){
$pgsql = "select count(`APPORDERNO`) as `ord_count` from $t_apperpdo left join $customer_master on $t_apperpdo.`dns_customer_code`=$customer_master.`dns_customer_code` where $t_apperpdo.`STATUS`='Order received' and $t_apperpdo.`APPORDERNO`!='' and $customer_master.`branch_code` in('".$theactbcstr."')";
}else{
$pgsql = "select count(`APPORDERNO`) as `ord_count` from $t_apperpdo where `STATUS`='Order received' and `APPORDERNO`!=''";
}
$pgres = mysql_query($pgsql);
$total_pgres = mysql_num_rows($pgres);
if($total_pgres>0){
	$row1=mysql_fetch_assoc($pgres);
	$total_pending_order_count = $row1["ord_count"];
}

include "web_header.php";
?>


    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>DASHBOARD</h2>
            </div>

            <!-- Widgets -->
            <div class="row clearfix">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-pink hover-expand-effect">
                    <a href="order_list.php?sl_ord_sts=Order received">
                        <div class="icon">
                            <i class="material-icons">playlist_add_check</i>
                        </div>
                        </a>
                        <div class="content">
                            <div class="text">PENDING ORDER</div>
                            <div class="number count-to" data-from="0" data-to="<?php echo $total_pending_order_count;?>" data-speed="1000" data-fresh-interval="20"></div>
                        </div>
                        
                    </div>
                </div>
<?php
if($_SESSION["start_user_type"]!="MANAGER"){
?>
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
<div class="info-box bg-cyan hover-expand-effect">
<a href="dealer_list.php">
<div class="icon">
<i class="material-icons">help</i>
</div>
</a>
<div class="content">
<div class="text">DEALER</div>
<div class="number count-to" data-from="0" data-to="<?php echo $total_dealers;?>" data-speed="1000" data-fresh-interval="20"></div>
</div>
</div>
</div>
<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
<div class="info-box bg-light-green hover-expand-effect">
<div class="icon">
<i class="material-icons">forum</i>
</div>
<div class="content">
<div class="text">SUB-DEALER</div>
<div class="number count-to" data-from="0" data-to="<?php echo $total_sub_dealers;?>" data-speed="1000" data-fresh-interval="20"></div>
</div>
</div>
</div>
<?php } ?>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-orange hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">person_add</i>
                        </div>
                        <div class="content">
                            <div class="text">ALL ORDERS</div>
                            <div class="number count-to" data-from="0" data-to="<?php echo $all_order_count;?>" data-speed="1000" data-fresh-interval="20"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

<?php
include "web_footer.php";
mysql_close();
?>
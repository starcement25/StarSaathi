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


$sswa_user_type = $_SESSION["sswa_user_type"];
$sswa_selected_dealer_code = $_SESSION["sswa_selected_dealer_code"];
$sswa_selected_customer_code = $_SESSION["sswa_selected_customer_code"];

$sql3 = "select `customer_id` from $customer_master where `customer_code`='$sswa_selected_customer_code'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
$row3 = mysql_fetch_assoc($res3);
$the_customer_id = trim($row3["customer_id"]);

if(strtoupper($sswa_user_type)=='DEALER'){
/*----Tracklog Code Start-----*/
$curr_date_time = date("Y-m-d H:i:s");
$webservice_name = "NOTIFICATION";
$sqlin_tl = "insert into `webservice_track_log` (`customer_code`,`webservice_name`,`details`,`datetime`) values ('$the_customer_id','$webservice_name','','$curr_date_time')";
$resin_tl = mysql_query($sqlin_tl);
/*----Tracklog Code End-----*/
}


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

$tagged_cust_branch_arr[] = "ALL";
$tagged_cust_branch_arr_str = implode("|",$tagged_cust_branch_arr);


$add_page_name = "show_nitifications.php";
$page_name = "show_nitifications.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "40";
$page = $_GET['paged'] ? $_GET['paged'] : 1;


$pgsql = "select `id` from $notification_message where `branch_code` REGEXP '".$tagged_cust_branch_arr_str."' ";
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
.teEachField1_off_ord{
	display:block;
	width:100%;
}
.teEachField2_off_ord{
	display:block;
	width:100%;
}

.teEachField1_on_ord{
	display:block;
	width:100%;
}
.teEachField2_on_ord{
	display:block;
	width:100%;
}

@media only screen and (max-width: 700px) {

.teEachField1_off_ord{
display:block;
width:300px;
word-wrap: break-word;
font-size: 12px;
white-space: normal;
padding-bottom: 2px;
}
.teEachField2_off_ord{
display:block;
font-size: 12px;
white-space: normal;
padding-bottom: 2px;
}

.teEachField1_on_ord{
display:block;
width:200px;
word-wrap: break-word;
font-size: 12px;
white-space: normal;
padding-bottom: 2px;
}
.teEachField2_on_ord{
display:block;
font-size: 12px;
white-space: normal;
padding-bottom: 2px;
}

}
</style>

<section class="content">
        <div class="container-fluid">
            <div class="row clearfix">
            

<?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>


<?php
$sql1 = "select * from $notification_message where `branch_code` REGEXP '".$tagged_cust_branch_arr_str."' order by `id` desc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$noti_id = $row1["id"];
		$message = $row1["message"] ? trim($row1["message"]) : "";
		$title = $row1["title"] ? trim($row1["title"]) : "";
		$date_time = $row1["date_time"] ? trim($row1["date_time"]) : "";
		$date_show = date("d/m/Y",strtotime($date_time));
		
?>
<div class="card" style="margin-bottom:15px;">
<div class="table-responsive">
<table class="table table-bordered">
<tbody>
<tr>
<td>
<div>
<span class="teEachField1_off_ord">
<b><?php echo $title;?></b>
<br />
<?php echo $message;?>
</span>
</div>
</td>
</tr>
<tr>
<td style="text-align:right;"><?php echo $date_show;?></td>
</tr>
</tbody>
</table>
</div>
</div>
<?php } 
}else{
?>
<div class="card">
<div class="table-responsive">
<table class="table table-bordered">
<tbody>
<tr>
<td align="center">No data found</td>
</tr>
</tbody>
</table>
</div>
</div>
<?php } ?>
              
             

<?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>


</div>
</div>
</section>



<?php
include "web_footer.php";
mysql_close();
?>
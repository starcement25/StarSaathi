<?php
ini_set('memory_limit', '99999M');
set_time_limit(0);
include "star_connection.php";
$arc_gift_redeem_table = "arc_gift_redeem_table";
$add_page_name = "arc_gift_redemption_history.php";
$page_name = "arc_gift_redemption_history.php";
$is_show = "NO";
$show_message = "";
$new_qry_string_filtered = "";
$user_type = $_GET["user_type"] ? strtolower($_GET["user_type"]) : "";
$login_user_id = $_GET["login_user_id"] ? urldecode($_GET["login_user_id"]) : "";

if($user_type!="" && $login_user_id!=""){
if($user_type=="dealer"){
	$customer_data_arr = get_customer_data_check_by_id($login_user_id);
	$the_sts = $customer_data_arr["sts"];
	$is_branch_arc = $customer_data_arr["is_branch_arc"];
	if($the_sts=="YES"){
		if($is_branch_arc=="YES"){
			$is_show = "YES";
			$new_qry_string_filtered = "&user_type=dealer&login_user_id=".urlencode($login_user_id);
/*---------PAGINATION RELATED CODE START----------*/
$adjacents = 4;
$targetpage = $page_name;
$limit = "40";
$page = $_GET['paged'] ? $_GET['paged'] : 1;
/*---------PAGINATION RELATED CODE END----------*/
/*---------PAGINATION RELATED CODE START----------*/
$pgsql = "select $arc_gift_redeem_table.`ac_id` from $arc_gift_redeem_table where `customer_code`='$login_user_id'";
$pgres = mysql_query($pgsql);
$total_pgres = mysql_num_rows($pgres);
$start_from = (($page-1)*$limit);
$prev = $page - 1;							//previous page is page - 1
$next = $page + 1;							//next page is page + 1
$lastpage = ceil($total_pgres/$limit);   //lastpage is = total pages / items per page, rounded up.
$lpm1 = $lastpage - 1;
/*---------PAGINATION RELATED CODE START----------*/
			
			
		}else{
			$show_message = "Feature not available.";
		}		
	}else{
		$show_message = "Your details are missing.";
	}
}else{
$show_message = "Feature not available.";
}
}else{
$show_message = "Something went wrong.";	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="js/jquery.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<title>GIFT REDEMPTION HISTORY</title>
<style>

.right_text { float:right;}
.status_del { color:#090;}
.status_pen { color:#F00;}

.mand_field{
color:#F00;
font-weight:bold;
font-size:12px;
padding-left:5px;
}
.alrt_msg{
color:#F00;
font-size: 12px;
height:22px;
display:inline-block;
}
.resp_msg{
font-weight:bold;
height:22px;
display:block;
margin:3px 0px;
text-align:center;
}
form.arc_consumer_reg_form div.form-group{
	margin-bottom:0px;
}
.card{
margin-top:5px;
background-color:#f3f3f3;
width:100%;
}

/* Pagination & Pager ========================== */
.pagination{
	background: none repeat scroll 0 0 #FFFFFF;float: right;height: 35px;margin-bottom: 10px;
	margin-top: 10px;position: relative;
}
.pagination > a {
    border: 1px solid #DFDFDF;display:inline-block;text-shadow:0 1px 0 rgba(255, 255, 255, 0.8);margin: 0 6px;
    padding: 8px;text-decoration:none;background:linear-gradient(to bottom, #FFFFFF, #B4B4B4) repeat scroll 0 0 #909090;color:#333;
	line-height: 16px;
}
.pagination > a:hover {
    border: 1px solid #DFDFDF;text-shadow:0 1px 0 rgba(255, 255, 255, 0.8);display:inline-block;margin: 0 6px;
    padding: 8px;background:linear-gradient(to top, grey, #F7F7F7) repeat scroll 0 0 #CFCFCF;color:#333;
	line-height: 16px;
}
.current_pg {
    border: 1px solid #DFDFDF;text-shadow:0 1px 0 rgba(255, 255, 255, 0.8);display:inline-block;margin: 0 6px;
    padding: 8px;background:linear-gradient(to top, grey, #F7F7F7) repeat scroll 0 0 #CFCFCF;color:#333;
	line-height: 16px;
}
.disabled_pg{border: 1px solid #DCDCDC;text-shadow:0 1px 0 rgba(255, 255, 255, 0.8);display:inline-block;
    margin: 0 6px;padding: 8px;background:linear-gradient(to top, #BCBCBC, #FFFFFF) repeat scroll 0 0 #FEFEFE;
	color:#8F8F8F;
	line-height: 16px;
}

</style>


</head>

<body>
<div class="container-fluid">
<?php
if($is_show=="NO" && $show_message!=""){?>
<h4 style="text-align:center;margin-top:30px;"><?php echo $show_message;?></h4>
<?php
exit;	
}
?>

<h5 style="text-align:center; margin-bottom:30px; margin-top:10px">GIFT REDEMPTION HISTORY</h5>

<?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>


<div class="row" style="margin:0px;padding:0px;">

<?php
$sql1 = "select * from $arc_gift_redeem_table where `customer_code`='$login_user_id' order by `entry_datetime` asc limit $start_from,$limit";
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
	while($row1=mysql_fetch_assoc($res1)){
		$ac_id = $row1["ac_id"];
		$consumer_name = $row1["name"];
		$consumer_mobile = $row1["mobile"];
		$redeemed_bags = $row1["redeemed_bags"];
		$each_status = $row1["status"];
		$each_gift_name = $row1["gift_name"];
		$entry_datetime = $row1["entry_datetime"];
		if($entry_datetime!=""){
		$entry_datetime	 = date("jS M'y",strtotime($entry_datetime));
		}
		$sts_cls = "status_pen";
		if($each_status=="PENDING"){
			$sts_cls = "status_pen";
		}else if($each_status=="DELIVERED"){
			$sts_cls = "status_del";
		}
		
?>

<div class="card">
<div class="card-body">
<h5 class="card-title"><?php echo $each_gift_name;?></h5>
<p class="card-text"><?php echo $consumer_name;?><br/>
<span><?php echo $consumer_mobile;?></span><span class="right_text"><?php echo $entry_datetime;?></span><br/>
<span><strong>Redeemed Bags:</strong> <?php echo $redeemed_bags;?></span><span class="right_text <?php echo $sts_cls;?>"><strong><?php echo $each_status;?></strong></span></p>

</div>
</div>
<?php
	}
}
?>

</div>

<?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>


<div class="row" style="margin:30px 0px 0px 0px;">

<a href="index.php?login_user_id=<?php echo urlencode($login_user_id);?>&user_type=<?php echo $user_type;?>" class="btn btn-large btn-block btn-success">BACK</a>

</div>


</div>
<script type="text/javascript">
jQuery(document).ready(function(){
var xhrarc_consumer_reg;
var img_ldr = '<img src="img/ajax-loader.gif" style="width:20px;">';


});
</script>
</body>
</html>
<?php
mysql_close();
?>
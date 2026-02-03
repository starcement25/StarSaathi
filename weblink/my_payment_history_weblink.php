<?php
include "star_connection.php";
$ledger_transaction_table = "ledger_transaction_table";
$the_page_name = "my_payment_history_weblink.php";
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
$current_date_time = date("Y-m-d H:i:s");

$new_qry_string_filtered .= "&the_id=".$the_id;

/*---------PAGINATION RELATED CODE START----------*/
$adjacents = 4;
$targetpage = $the_page_name;
$limit = "30";
$page = $_GET['paged'] ? $_GET['paged'] : 1;
/*---------PAGINATION RELATED CODE END----------*/
/*---------PAGINATION RELATED CODE START----------*/
$pgsql = "SELECT `lt_order_id` from $ledger_transaction_table where `customer_code`='$the_id' and `order_status`='Success'";
$pgres = mysql_query($pgsql);
$total_pgres = mysql_num_rows($pgres);
$start_from = (($page-1)*$limit);
$prev = $page - 1;							//previous page is page - 1
$next = $page + 1;							//next page is page + 1
$lastpage = ceil($total_pgres/$limit);   //lastpage is = total pages / items per page, rounded up.
$lpm1 = $lastpage - 1;
/*---------PAGINATION RELATED CODE START----------*/




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<meta name="viewport" content="width=device-width, initial-scale=1">
<title>My Payment History</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<style>

.pagination{
	background: none repeat scroll 0 0 #FFFFFF;float: right;height: 35px;margin-bottom: 10px;
	margin-top: 10px;position: relative;
}
.pagination > a {
    border: 1px solid #DFDFDF;display:inline-block;text-shadow:0 1px 0 rgba(255, 255, 255, 0.8);margin: 0 6px;
    padding: 5px;text-decoration:none;background:linear-gradient(to bottom, #FFFFFF, #B4B4B4) repeat scroll 0 0 #909090;color:#333;
}
.pagination > a:hover {
    border: 1px solid #DFDFDF;text-shadow:0 1px 0 rgba(255, 255, 255, 0.8);display:inline-block;margin: 0 6px;
    padding: 5px;background:linear-gradient(to top, grey, #F7F7F7) repeat scroll 0 0 #CFCFCF;color:#333;
}
.current_pg {
    border: 1px solid #DFDFDF;text-shadow:0 1px 0 rgba(255, 255, 255, 0.8);display:inline-block;margin: 0 6px;
    padding: 5px;background:linear-gradient(to top, grey, #F7F7F7) repeat scroll 0 0 #CFCFCF;color:#333;
}
.disabled_pg{border: 1px solid #DCDCDC;text-shadow:0 1px 0 rgba(255, 255, 255, 0.8);display:inline-block;
    margin: 0 6px;padding: 5px;background:linear-gradient(to top, #BCBCBC, #FFFFFF) repeat scroll 0 0 #FEFEFE;
	color:#8F8F8F;
}
.srch_div_cont{
	width:auto;
	display:inline-block;
	float:right;
}
.add_top_bottom_padding{
	padding:5px 8px;
	text-align:center;
}
.os_ldr{
	display: block;
    height: 20px;
    margin-top: 2px;
    width: 100%;
	text-align:center;
}

.bkbtn{
	padding:5px;
	display:block !important;
}
.span_el{
color:#199ebd;	
}
.performers_table tbody tr td{
	font-size:14px;
}
.heighlight_player{
background-color:green;
color:#fff;
font-weight: bold;	
}
/*.pagination > li > a, .pagination > li > span { padding: 10px 20px !important;}*/
.till_date_class{
	text-align:center;
	font-size:14px;
	display:block;
	font-weight:bold;
	margin-top:5px;
	margin-bottom:8px;
}
</style>
</head>
<body>
<div class="container-fluid" style="padding:0px;">
<div class="container" style="position:relative;text-align:center;">

</div>
<?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>
<div class="table-responsive">
<table class="table table-bordered performers_table">
<thead>
<th style="border-right:0px;font-size:10px">Date & Time</th>
<th align="right" style="text-align:right;font-size:10px">Amount</th>
<th align="center" style="text-align:center;font-size:10px;">Payment By</th>
</thead>
<tbody>
<?php
$sql2 = "SELECT * from $ledger_transaction_table where `customer_code`='$the_id' and `order_status`='Success' order by `order_datetime` desc limit $start_from,$limit";
$res2 = mysql_query($sql2);
$totres2 = mysql_num_rows($res2);
$cnt = 1;
if($totres2>0){
while($row2=mysql_fetch_assoc($res2)){
$order_datetime = $row2["order_datetime"];
if($order_datetime!=""){
	$order_datetime = date("jS M,y h:i A",strtotime($order_datetime));
}
$the_amount = $row2["amount"] ? trim($row2["amount"]) : "0";
$the_payment_by = $row2["payment_by"] ? trim($row2["payment_by"]) : "0";
$the_tracking_id = $row2["tracking_id"];
?>
<tr>
<td align="left" valign="middle" style="border-right:0px;" ><?php echo $order_datetime;?></td>
<td align="right" valign="middle"  style="text-align:right;"><?php echo number_format($the_amount,2, '.', '');?></td>
<td align="center" valign="middle" style="text-align:center;"><?php echo $the_payment_by;?></td>
</tr> 
<?php
$cnt++;
 } }else{ ?>
<tr>
<td align="center" valign="middle" colspan="3" >No record found</td>
</tr>
<?php  } ?>
</tbody>
</table>

</div>
<?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>
</div>
</body>
</html>
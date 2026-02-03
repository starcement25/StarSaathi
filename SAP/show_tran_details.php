<?php
include "star_connection.php";
$test_payment = "test_payment";
$sql2 = "SELECT * FROM $test_payment order by `order_id` desc";
$res2 = mysql_query($sql2);
$totres2 = mysql_num_rows($res2);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Star Transaction Details</title>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<style>
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
</style>
</head>
<body>
<div class="container-fluid" style="padding:10px;">

<div class="table-responsive">
<table class="table table-bordered performers_table">
<thead>
<tr>
<th>Order&nbsp;ID</th>
<th>Cust&nbsp;Code</th>
<th>Amount</th>
<th>Order&nbsp;Status</th>
<th>Order&nbsp;Date</th>
</tr>
</thead>
<tbody>
<?php
$cnt = 1;
if($totres2>0){
while($row2=mysql_fetch_assoc($res2)){
$order_id = $row2["order_id"];
$emp_code = $row2["emp_code"];
$amount = $row2["amount"];
$order_status = $row2["order_status"];
$order_datetime = $row2["order_datetime"];
?>
<tr>
<td><?php echo $order_id;?></td>
<td><?php echo $emp_code;?></td>
<td><?php echo $amount;?></td>
<td><?php echo $order_status;?></td>
<td><?php echo $order_datetime;?></td>
</tr> 
<?php
$cnt++;
 } }else{ ?>
<tr>
<td align="center" valign="middle" colspan="5" >No record found</td>
</tr>
<?php  } ?>
</tbody>
</table>

</div>
</div>
</body>
</html>
<?php
mysql_close();
?>
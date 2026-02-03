<?php
set_time_limit(0);
include "star_connection.php";
$res_msg= array();
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$apporder_no = $_REQUEST["apporder_no"] ? trim($_REQUEST["apporder_no"]) : "";
$erporder_no = $_REQUEST["erporder_no"] ? trim($_REQUEST["erporder_no"]) : "";
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Challan Details</title>
<link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap Core Js -->
<script src="plugins/bootstrap/js/bootstrap.js"></script>
<style>
.teEachField1_off_ord{
display:block;
width:110px;
word-wrap: break-word;
font-size: 12px;
white-space: normal;
padding-bottom: 2px;
}
.teEachField1_off_ord2{
display:block;
width:150px;
word-wrap: break-word;
font-size: 12px;
white-space: normal;
padding-bottom: 2px;
}
.table-responsive table.table tbody tr td{
	padding:2px 0px 0px 5px;
}
.card{
margin-bottom:10px;
padding: 8px;
background-color: #fff;
border: 1px solid #ccc;
border-radius: 6px;
}

</style>
</head>

<body style="background-color:#e2e2e2;">

<div class="container-fluid" style="z-index:9999;">
<div class="row clearfix" style="padding:10px 3px;">

<?php
if($apporder_no!="" || $erporder_no!=""){
if($apporder_no!="" && $erporder_no!=""){
$sql1 = "select * from $t_dochallan where `APPORDERNO`='".addslashes($apporder_no)."' and `ERPORDERNO`='".addslashes($erporder_no)."'";
}else if($apporder_no!="" && $erporder_no==""){
$sql1 = "select * from $t_dochallan where `APPORDERNO`='".addslashes($apporder_no)."'";
}else if($apporder_no=="" && $erporder_no!=""){
$sql1 = "select * from $t_dochallan where `ERPORDERNO`='".addslashes($erporder_no)."'";
}else{
$sql1 = "";	
}
if($sql1!=""){
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
if($totres1>0){
while($row1=mysql_fetch_assoc($res1)){
		$apporder_no_chtl = $row1["APPORDERNO"];
		$erporder_no_chtl = $row1["ERPORDERNO"];
		$erporder_date_chtl = $row1["ERPORDERDT"];
		$SoTime = $row1["SoTime"];
		$challanno_chtl = $row1["CHALLANNO"];
		$challandt_chtl = $row1["CHALLANDT"];
		$InvTime = $row1["InvTime"];
		$truckno_chtl= $row1["TRUCKNO"];
		$driverno_chtl= $row1["DRIVERNO"];
		$prod_qty_chtl = $row1["QTY"];
		$challanqty_chtl = $row1["CHALLANQTY"];
		if($SoTime!='')
		{
			$erporder_date_chtl = date("Y-m-d",strtotime($erporder_date_chtl));
			$SoTime_str = str_replace("PT","",$SoTime);
			$SoTime_str = str_replace("H","",$SoTime_str);
			$SoTime_str = str_replace("M","",$SoTime_str);
			$SoTime_str = str_replace("S","",$SoTime_str);
		}
		$erporder_date_chtl=$erporder_date_chtl.' '.$SoTime_str;
		$erporder_date_chtl = date("Y-m-d H:i:s",strtotime($erporder_date_chtl));
		
		if($InvTime!='')
		{
			$challandt_chtl = date("Y-m-d",strtotime($challandt_chtl));
			$InvTime_str = str_replace("PT","",$InvTime);
			$InvTime_str = str_replace("H","",$InvTime_str);
			$InvTime_str = str_replace("M","",$InvTime_str);
			$InvTime_str = str_replace("S","",$InvTime_str);
		}
		$challandt_chtl=$challandt_chtl.' '.$InvTime_str;
		$challandt_chtl = date("Y-m-d H:i:s",strtotime($challandt_chtl));
?>

<div class="card">
<div class="table-responsive" style="margin:0px;">
<table class="table table-bordered">
<tbody>
<tr>
<td><span class="teEachField1_off_ord">App&nbsp;Order&nbsp;No</span></td>
<td><span class="teEachField1_off_ord2"><?php echo $apporder_no_chtl;?></span></td>
</tr>
<tr>
<td><span class="teEachField1_off_ord">Sale Order No</span></td>
<td><span class="teEachField1_off_ord2"><?php echo $erporder_no_chtl;?></span></td>
</tr>
<tr>
<td><span class="teEachField1_off_ord">Sale Order Date</span></td>
<td><span class="teEachField1_off_ord2"><?php echo $erporder_date_chtl;?></span></td>
</tr>
<tr>
<td><span class="teEachField1_off_ord">Challan&nbsp;No</span></td>
<td><span class="teEachField1_off_ord2"><?php echo $challanno_chtl;?></span></td>
</tr>
<tr>
<td><span class="teEachField1_off_ord">Challan&nbsp;Date</span></td>
<td><span class="teEachField1_off_ord2"><?php echo $challandt_chtl;?></span></td>
</tr>
<tr>
<td><span class="teEachField1_off_ord">QTY&nbsp;(MT)</span></td>
<td><span class="teEachField1_off_ord2"><?php echo number_format($prod_qty_chtl,2);?></span></td>
</tr>
<tr>
<td><span class="teEachField1_off_ord">Challan&nbsp;QTY&nbsp;(MT)</span></td>
<td><span class="teEachField1_off_ord2"><?php echo number_format($challanqty_chtl,2);?></span></td>
</tr>
<tr>
<td><span class="teEachField1_off_ord">Truck&nbsp;No</span></td>
<td><span class="teEachField1_off_ord2"><?php echo $truckno_chtl;?></span></td>
</tr>
<tr>
<td><span class="teEachField1_off_ord">Driver&nbsp;Contact</span></td>
<td><span class="teEachField1_off_ord2"><?php echo $driverno_chtl;?></span></td>
</tr>
</tbody>
</table>
</div>
</div>

<?php 
}
}}} ?>


</div>
</div>


</body>
</html>
<?php
mysql_close();
?>
<?php
set_time_limit(0);
include "star_connection.php";
$res_msg= array();
$t_apperpdo = "T_APPERPDO";
$t_doinvoice = "T_DOINVOICE";
$T_APPERPDO_OFFLINE = "T_APPERPDO_OFFLINE";
$apporder_no = $_REQUEST["apporder_no"] ? urldecode(trim($_REQUEST["apporder_no"])) : "";
$erporder_no = $_REQUEST["erporder_no"] ? urldecode(trim($_REQUEST["erporder_no"])) : "";
$dns_customer_code = "";
if($apporder_no!=""){
$sql34 = "select `APPORDERNO`,`dns_customer_code` from $t_apperpdo where `APPORDERNO`='$apporder_no'";
$res34 = mysql_query($sql34);
$totres34 = mysql_num_rows($res34);
if($totres34>0){
$row34=mysql_fetch_assoc($res34);
$dns_customer_code = $row34["dns_customer_code"];
}
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Challan Details</title>
<link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.js"></script>
<style>
.table-responsive .table thead tr th,.table-responsive .table tfoot tr th{
font-size: 12px;
padding: 2px;
line-height: 12px;
}
.table-responsive .table tbody tr td{
font-size: 13px;
padding: 4px 2px 4px 2px;
line-height: 12px;
}
.table-responsive{
margin-top:30px;
margin-bottom:20px;	
}
</style>
</head>

<body>
<div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>App&nbsp;Order&nbsp;No</th>
                                            <th>Sale Order&nbsp;No</th>
                                            <th>Delivery&nbsp;No</th>
											<th>Invoice&nbsp;No</th>
											<th>Invoice&nbsp;Date</th>
                                            <th>Product&nbsp;Name</th>
                                            <th>Invoice qty&nbsp;(MT)</th>
                                            <th>Destination</th>
                                            <th>Truck No</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                      <tr>
                                            <th>App&nbsp;Order&nbsp;No</th>
                                            <th>Sale Order&nbsp;No</th>
                                            <th>Delivery&nbsp;No</th>
											<th>Invoice&nbsp;No</th>
											<th>Invoice&nbsp;Date</th>
                                            <th>Product&nbsp;Name</th>
                                            <th>Invoice qty&nbsp;(MT)</th>
                                            <th>Destination</th>
                                            <th>Truck No</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>

<?php
if($apporder_no!="" || $erporder_no!=""){
	if($apporder_no!="" && $erporder_no!=""){
		$sql1 = "select * from $t_doinvoice where (`APPORDERNO`='".addslashes($apporder_no)."' or `ERPORDERNO`='".addslashes($erporder_no)."')";
	}else if($apporder_no!="" && $erporder_no==""){
		$sql1 = "select * from $t_doinvoice where `dns_customer_code`='$dns_customer_code' and  `APPORDERNO`='".addslashes($apporder_no)."'";
	}else if($apporder_no=="" && $erporder_no!=""){
		/*$sql1 = "select * from $t_dochallan where `dns_customer_code`='$dns_customer_code' and  `ERPORDERNO`='".addslashes($erporder_no)."'";*/
		$sql1 = "select * from $t_doinvoice where  `ERPORDERNO`='".addslashes($erporder_no)."'";
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
		$challanno_chtl = $row1["CHALLANNO"];
		$INVNO= $row1["INVNO"];
		$INVDT= $row1["INVDT"];
		$prod_display_name_chtl= $row1["prod_display_name"];
		$INVQTY= $row1["INVQTY"];
		$TRUCKNO = $row1["TRUCKNO"];
		$destination = $row1["destination"];
		
	
?>
<tr>
<td><?php echo $apporder_no_chtl;?></td>
<td><?php echo $erporder_no_chtl;?></td>
<td><?php echo $challanno_chtl;?></td>
<td><?php echo $INVNO;?></td>
<td><?php echo $INVDT;?></td>	
<td><?php echo $prod_display_name_chtl;?></td>
<td><?php echo $INVQTY;?></td>
<td><?php echo $destination;?></td>
<td><?php echo $TRUCKNO;?></td>
</tr>

<?php
$the_sl_no++;
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="14">No data found.</td>
</tr>
<?php
}
}else{
?>
<tr>
<td style="text-align:center" colspan="14">something went wrong.</td>
</tr>
<?php	
}
}else{
	?>
<tr>
<td style="text-align:center" colspan="14">something went wrong.</td>
</tr>
<?php
}
?>
</tbody>
</table>
</div>
</body>
</html>
<?php
mysql_close();
?>
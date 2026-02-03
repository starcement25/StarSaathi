<?php
set_time_limit(0);
include "star_connection.php";
$res_msg= array();
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
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
                                            <th>ERP&nbsp;No</th>
                                            <th>ERP&nbsp;Date</th>
                                            <th>Challan&nbsp;No</th>
                                            <th>Challan&nbsp;Date</th>
                                            <th>Product&nbsp;Name</th>
                                            <th>qty&nbsp;(MT)</th>
                                            <th>Challan&nbsp;qty&nbsp;(MT)</th>
                                            <th>Transporter&nbsp;Name</th>
                                            <th>Quantity&nbsp;Checking</th>
                                            <th>Quality&nbsp;Checking</th>
                                            <th>Remarks</th>
                                            <th>Status</th>
                                            <th>Submitted&nbsp;On</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                      <tr>
                                            <th>App&nbsp;Order&nbsp;No</th>
                                            <th>ERP&nbsp;No</th>
                                            <th>ERP&nbsp;Date</th>
                                            <th>Challan&nbsp;No</th>
                                            <th>Challan&nbsp;Date</th>
                                            <th>Product&nbsp;Name</th>
                                            <th>qty&nbsp;(MT)</th>
                                            <th>Challan&nbsp;qty&nbsp;(MT)</th>
                                            <th>Transporter&nbsp;Name</th>
                                            <th>Quantity&nbsp;Checking</th>
                                            <th>Quality&nbsp;Checking</th>
                                            <th>Remarks</th>
                                            <th>Status</th>
                                            <th>Submitted&nbsp;On</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>

<?php
if($apporder_no!="" || $erporder_no!=""){
	if($apporder_no!="" && $erporder_no!=""){
		$sql1 = "select * from $t_dochallan where `dns_customer_code`='$dns_customer_code' and (`APPORDERNO`='".addslashes($apporder_no)."' or `ERPORDERNO`='".addslashes($erporder_no)."')";
	}else if($apporder_no!="" && $erporder_no==""){
		$sql1 = "select * from $t_dochallan where `dns_customer_code`='$dns_customer_code' and  `APPORDERNO`='".addslashes($apporder_no)."'";
	}else if($apporder_no=="" && $erporder_no!=""){
		/*$sql1 = "select * from $t_dochallan where `dns_customer_code`='$dns_customer_code' and  `ERPORDERNO`='".addslashes($erporder_no)."'";*/
		$sql1 = "select * from $t_dochallan where  `ERPORDERNO`='".addslashes($erporder_no)."'";
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
		$challanno_chtl = $row1["CHALLANNO"];
		$challandt_chtl = $row1["CHALLANDT"];
		$prod_code_chtl= $row1["prod_code"];
		$dns_prod_code_chtl= $row1["dns_prod_code"];
		$prod_display_name_chtl= $row1["prod_display_name"];
		$prod_qty_chtl = $row1["QTY"];
		$challanqty_chtl = $row1["CHALLANQTY"];
		
		if($prod_display_name_chtl=='')
		{	
			$sqlproductdetails = "select prod_display_name from $T_APPERPDO_OFFLINE where  `ERPORDERNO`='".addslashes($erporder_no)."'";
			$resproductdetails= mysql_query($sqlproductdetails);
			$rowproductdetails=mysql_fetch_array($resproductdetails);
			$prod_display_name_chtl=$rowproductdetails['prod_display_name'];
			
		}
		
$is_confirmed_challan_material_received = $row1["is_confirmed_challan_material_received"] ? trim($row1["is_confirmed_challan_material_received"]) : "";
$challan_quantity_checking = $row1["challan_quantity_checking"] ? trim($row1["challan_quantity_checking"]) : "";
$ch_quantity_no_of_bags = $row1["ch_quantity_no_of_bags"] ? trim($row1["ch_quantity_no_of_bags"]) : "";


$challan_quality_checking = $row1["challan_quality_checking"] ? trim($row1["challan_quality_checking"]) : "";
$ch_quality_no_of_damaged_bags = $row1["ch_quality_no_of_damaged_bags"] ? trim($row1["ch_quality_no_of_damaged_bags"]) : "";

$challan_remarks = $row1["challan_remarks"] ? trim($row1["challan_remarks"]) : "";
$ch_status = $row1["ch_status"] ? trim($row1["ch_status"]) : "Pending";

$transporter_name = $row1["transporter_name"] ? trim($row1["transporter_name"]) : "";
$confirmed_challan_material_received_datetime = $row1["confirmed_challan_material_received_datetime"] ? trim($row1["confirmed_challan_material_received_datetime"]) : "";
if($confirmed_challan_material_received_datetime!=""){
//$confirmed_challan_material_received_datetime = date("jS M,Y h:i A",strtotime($confirmed_challan_material_received_datetime));	
$confirmed_challan_material_received_datetime = date("d-m-Y H:i:s",strtotime($confirmed_challan_material_received_datetime));	
}	
?>
<tr>
<td><?php echo $apporder_no_chtl;?></td>
<td><?php echo $erporder_no_chtl;?></td>
<td><?php echo $erporder_date_chtl;?></td>
<td><?php echo $challanno_chtl;?></td>
<td><?php echo $challandt_chtl;?></td>
<td><?php echo $prod_display_name_chtl;?></td>
<td><?php echo $prod_qty_chtl;?></td>
<td><?php echo $challanqty_chtl;?></td>
<td><?php echo $transporter_name;?></td>
<td><?php echo $challan_quantity_checking;
if($ch_quantity_no_of_bags!=""){
	echo "<br>No. of bags short: ".$ch_quantity_no_of_bags;
}
?></td>
<td><?php echo $challan_quality_checking;
if($ch_quality_no_of_damaged_bags!=""){
	echo "<br>No. of damaged bags: ".$ch_quality_no_of_damaged_bags;
}
?></td>
<td><?php echo $challan_remarks;?></td>
<td><?php echo $ch_status;?></td>
<td><?php echo $confirmed_challan_material_received_datetime;?></td>
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
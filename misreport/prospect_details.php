<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php
$db = "acedns_".strtoupper($_SESSION['nick_name']);

define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234");
define("DB","$db");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);
?>

<body>
<center>

<?php

$sql_prospect = "SELECT * FROM prospective_customer_header WHERE trans_id = '$_GET[pcid]'";
$res_prospect = mysql_query($sql_prospect);
$row_prospect = mysql_fetch_array($res_prospect);

$sql_tagged_customer_name = "SELECT customer_name FROM customer_master WHERE customer_code = '$row_prospect[tagged_customer_code]'";
$res_tagged_customer_name = mysql_query($sql_tagged_customer_name);
$row_tagged_customer_name = mysql_fetch_array($res_tagged_customer_name);
?>

<table border="1" width="100%" style="border-collapse:collapse;" class="border">
  <tr>
  	<td colspan="2" align="center" class="TDHEAD">Prospect Details</td>
  </tr>
  <tr>
  	<td><b>Prospect Name:</b></td>
    <td><?php echo $row_prospect['customer_name']; ?></td>
  </tr>
  <tr>
  	<td><b>Address:</b></td>
    <td><?php echo $row_prospect['address']; ?></td>
  </tr>
  <tr>
  	<td><b>Pin:</b></td>
    <td><?php echo $row_prospect['pin']; ?></td>
  </tr>
  <tr>
  	<td><b>Area:</b></td>
    <td><?php  echo $_GET['route_name']; ?></td>
  </tr>
  <tr>
  	<td><b>Phone Number:</b></td>
    <td><?php echo $row_prospect['phone_no']; ?></td>
  </tr>
  <tr>
  	<td><b>Tagged Customer:</b></td>
    <td><?php echo $row_tagged_customer_name['customer_name']; ?></td>
  </tr>
  <tr>
  	<td><b>Customer Type:</b></td>
    <td><?php echo $row_prospect['cust_type']; ?></td>
  </tr>
	<tr>
  	<td><b>Created On:</b></td>
    <td><?php echo date('d-m-Y',strtotime(substr($_GET['pcid'],7,8))); ?></td>
  </tr>

</table>

</center>
</body>
<?php
mysql_close($link);
?>

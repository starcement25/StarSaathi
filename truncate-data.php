<?php
define("SERVER","216.237.114.58");
define("USER","coralweb");
define("PASSWORD","coral5071");
define("DB","CORAL");
$link=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysql_select_db(DB,$link) or die("could not connect the database.");


$sqldeleteprodgroup="truncate product_group_master";
$rsdeleteprodgroup=mysql_query($sqldeleteprodgroup);

$sqldeleteprodsubgroup="truncate product_sub_group_master";
$rsdeleteprodsubgroup=mysql_query($sqldeleteprodsubgroup);

$sqlskutempdelete="truncate product_master_temp";
$rsskutempdelete=mysql_query($sqlskutempdelete);
$sqlskudelete="truncate product_master";
$rsskudelete=mysql_query($sqlskudelete);


$sqldeleteemp="truncate employee_master";
$rsdeleteemp=mysql_query($sqldeleteemp);
$sqldeletepassword="truncate changepassword";
$rsdeletepassword=mysql_query($sqldeletepassword);

$sql  = "insert into employee_master ";
$sql .= " SET emp_code='C0007'";
$sql .= " , emp_name='Common'";
mysql_query($sql);
						
$sqlcp  = "insert into changepassword ";
$sqlcp .= " SET emp_code='C0007'";
$sqlcp .= " , newpassword='1234'";
$sqlcp .= " , oldpassword='1234'"; 
$sqlcp .= " , status='true'";
$sqlcp .= " , is_licensed='1'"; 
mysql_query($sqlcp);

$sqldeleteroute="truncate route_master";
$rsdeletetroute=mysql_query($sqldeleteroute);

$sqlcusdelete="truncate customer_master";
$rscusdelete=mysql_query($sqlcusdelete);
$sqlcusdeletetemp="truncate customer_master_temp";
$rscusdeletetemp=mysql_query($sqlcusdeletetemp);


$sqldeleteout="truncate outstanding";
$rsdeleteout=mysql_query($sqldeleteout);

$sqldeletemrp="truncate mrp";
$rsdeletemrp=mysql_query($sqldeletemrp);

echo 'Truncate Successful.';
	
?>
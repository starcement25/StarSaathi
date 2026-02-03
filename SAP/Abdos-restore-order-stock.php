<?php
    define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_ABDOS");
$link=mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB,$link);
function similar_file_exists($filename) {
  if (file_exists($filename)) {
	return $filename;
  }
  $dir = dirname($filename);
  $files = glob($dir . '/*');
  $lcaseFilename = strtolower($filename);
  foreach($files as $file) {
	if (strtolower($file) == $lcaseFilename) {
	  return $file;
	}
  }
  return false;
}
	
	//For order
	$sqlselorder="SELECT customer_code,order_no FROM order_header_old";
	$rsselorder=mysql_query($sqlselorder);
	while($rowselorder=mysql_fetch_array($rsselorder))
	{
		$customer_code_old=$rowselorder['customer_code'];
		$order_no_old=$rowselorder['order_no'];
		$sqlorder  = "UPDATE order_header SET customer_code='".$customer_code_old."' WHERE order_no='".$order_no_old."'";
		mysql_query($sqlorder);
	}
	
	//For stock
	$sqlselstock="SELECT customer_code,transaction_id FROM stock_audit_old";
	$rsselstock=mysql_query($sqlselstock);
	while($rowselstock=mysql_fetch_array($rsselstock))
	{
		$customer_code_old_stk=$rowselstock['customer_code'];
		$transaction_id_old=$rowselstock['transaction_id'];
		$sqlaudit  = "UPDATE stock_audit SET customer_code='".$customer_code_old_stk."' WHERE transaction_id='".$transaction_id_old."'";
		mysql_query($sqlaudit);
	}
	echo 'success';
?>
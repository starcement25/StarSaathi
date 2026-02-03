<?php
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");
	require("include/config-email-setup.php");

	$emp_code=$_REQUEST['emp_code'];
	/*$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempname=mysql_query($sqlempname);
	$rowempname=mysql_fetch_array($rsempname);
	$emp_name=$rowempname['emp_name'];*/
	$sqlrds="SELECT rds_code FROM rds_master WHERE emp_code='".$emp_code."'";
	$rsrds=mysql_query($sqlrds);
	$rowrds=mysql_fetch_array($rsrds);
	$rds_code=$rowrds['rds_code'];

	$sqlactivitylog="SELECT transaction_id FROM activity_log WHERE 	updated_flag_app='0' AND receiver_code='".$emp_code."'";
	$rsactivitylog=mysql_query($sqlactivitylog);
	$cntactivitylog=mysql_num_rows($rsactivitylog);
	if($cntactivitylog >0)
	{
		$sqlUpdate="UPDATE activity_log SET updated_flag_app='1' WHERE receiver_code='".$emp_code."'";
	}
	else
	{
		$sqlUpdate="UPDATE activity_log SET
					updated_flag_app='1'
					WHERE receiver_code IS NULL AND (rds_code='".$rds_code."' OR SUBSTRING(transaction_id,2,5)='".$emp_code."' OR SUBSTRING(transaction_id,3,5)='".$emp_code."')";
	}
	if(mysql_query($sqlUpdate))
	{
		echo "1";
	}
	else
	{
		echo "0";
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/transactionDeletionConfirmation.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/transactionDeletionConfirmation.php?nick_name=$nick_name&emp_code=$emp_code"."\r\n";
	$insertPos=0;  // variable for saving 
	while (!feof($file)) {
		$line=fgets($file);
		if (strpos($line, 'http://')!==false) {
			$insertPos=ftell($file);
			$newline =  $newuser;
		}
		else
		{
			$newline.=$line;   // append existing data with new data of user
		}
	}
	fseek($file,$insertPos);   // move pointer to the file position where we saved above 
	fwrite($file, $newline);
	fclose($file);*/	
	
?>

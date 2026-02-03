<?php    
    require("include/config.php");
	require("include/dbcon.php");

	$emp_code=$_REQUEST['emp_code'];
	$device_id=$_REQUEST['device_id'];
	
	$sqlselect="SELECT is_update FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code='".$emp_code."'";
	$rsselect=mysql_query($sqlselect);
	$count=mysql_num_rows($rsselect);
		
	if($count>0)
	{
		$rowselect=mysql_fetch_array($rsselect);
		$is_update=$rowselect['is_update'];
		if($is_update==1){
				$sqlselectversion="SELECT version_code  FROM db_version ";
				$rsselectversion=mysql_query($sqlselectversion);
				$rowselectversion=mysql_fetch_array($rsselectversion);
				$versionCode=$rowselectversion['version_code'];
				
				$sqlUpdatetablestructure="UPDATE table_structure_updation SET
									db_version_code='".$versionCode."',
									is_update='0'
									WHERE device_id='".$device_id."' AND emp_code='".$emp_code."'";
				mysql_query($sqlUpdatetablestructure);
			}	
		}	
	
	$sqlselect="SELECT emp_code FROM emp_data_update_log  WHERE emp_code='".$emp_code."'";
	$rsselect=mysql_query($sqlselect);
	$count=mysql_num_rows($rsselect);
	$rowselect=mysql_fetch_array($rsselect);
	
	if($count>0)
	{
		$sqlUpdate="UPDATE emp_data_update_log SET
					update_time=CURRENT_TIMESTAMP() WHERE emp_code='".$emp_code."'";
		if(mysql_query($sqlUpdate))
		{
			echo "1";
		}
		else
		{
			echo "0";
		}
	}
	else
	{
		$sqlInsert="INSERT INTO emp_data_update_log SET
					emp_code='".$emp_code."',
					update_time=CURRENT_TIMESTAMP()";
		if(mysql_query($sqlInsert))
		{
			echo "1";
		}
		else
		{
			echo "0";
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/emp-data-update-time.php?nick_name=$nick_name&emp_code=$emp_code&device_id=$device_id";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/emp-data-update-time.php?nick_name=$nick_name&emp_code=$emp_code&device_id=$device_id"."\r\n";
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
<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];
$device_id=$_REQUEST['device_id'];

$sqlselectversion="SELECT version_code  FROM db_version ";
$rsselectversion=mysql_query($sqlselectversion);
$rowselectversion=mysql_fetch_array($rsselectversion);
$versionCode=$rowselectversion['version_code'];

if($mode=='INSTALL')
{
	$sqlquery="SELECT * FROM table_structure_master ORDER BY t_structure_id";
	$result = mysql_query($sqlquery) or die(mysql_error());
	$counttable=mysql_num_rows($result);
	
	$sqlselect="SELECT * FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code='".$emp_code."'";
	$rsselect=mysql_query($sqlselect);
	$count=mysql_num_rows($rsselect);
	
	if($count<1){
		$sqlInsert="INSERT INTO table_structure_updation SET
						emp_code='".$emp_code."',
						db_version_code='".$versionCode."',
						device_id='".$device_id."',
						is_update='0'";
		mysql_query($sqlInsert);
	}

}
else{
	$sqlselect="SELECT * FROM table_structure_updation  WHERE device_id='".$device_id."'";
	$rsselect=mysql_query($sqlselect);
	$count=mysql_num_rows($rsselect);
		
	if($count>0)
	{
		$rowselect=mysql_fetch_array($rsselect);
		$is_update=$rowselect['is_update'];
		if($is_update==1){
			$sqlquery="SELECT * FROM table_structure_master WHERE need_update='Y' ORDER BY t_structure_id";
			$result = mysql_query($sqlquery);
			$counttable=mysql_num_rows($result);
			
			$sqlUpdate="UPDATE table_structure_updation SET
						db_version_code='".$versionCode."',
						is_update='0'
						WHERE device_id='".$device_id."' AND emp_code='".$emp_code."'";
			mysql_query($sqlUpdate);
			
			$sqlcntupdation="SELECT COUNT(device_id) AS no_of_updated_users FROM table_structure_updation WHERE is_update='0'";
			$rscntupdation=mysql_query($sqlcntupdation);
			$rowcntupdation=mysql_fetch_array($rscntupdation);
			$no_of_updated_users=$rowcntupdation['no_of_updated_users'];
			
			if($no_of_updated_users==no_of_licensed_users){
				$sqlupdatetable="UPDATE table_structure_master SET need_update='N',is_transaction='N',is_master='N'";
				mysql_query($sqlupdatetable);
			}
		}
		else
		{
			$counttable=0;		
		}
	}
}
if($counttable>0){
	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	while($rowstructuredetails = mysql_fetch_array($result))
	{
			$contents.="<data>";
			$contents .='<table_name><![CDATA['.mb_convert_encoding($rowstructuredetails['table_name'], 'UTF-8', 'UTF-8').']]></table_name>
						<table_structure><![CDATA['.mb_convert_encoding($rowstructuredetails['table_structure'], 'UTF-8', 'UTF-8').']]></table_structure>
						<transaction><![CDATA['.mb_convert_encoding($rowstructuredetails['is_transaction'], 'UTF-8', 'UTF-8').']]></transaction>
						<master><![CDATA['.mb_convert_encoding($rowstructuredetails['is_master'], 'UTF-8', 'UTF-8').']]></master>
						';
			$contents.="</data>";
			//echo $cnt++;
	}
	$contents .= "</recordset>";
	echo $contents;		
}
else
{
	echo '0';
}
?>
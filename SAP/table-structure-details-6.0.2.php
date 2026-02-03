<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
/*require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");*/

define("SERVER","starsaathi-rds-server.clcy6zb4izp8.ap-south-1.rds.amazonaws.com");
define("USER","admin");
define("PASSWORD","zwPB6L65ZC}p8L89");
define("APICALLLOGURL","https://starsaathi.com/SAP/");

// $servername = "starsaathi-rds-server.clcy6zb4izp8.ap-south-1.rds.amazonaws.com";
// $username = "admin";
// $password = "zwPB6L65ZC}p8L89";
// $db_name = "starsaathi_STARS";

$nick_name=$_REQUEST['nick_name'];

define("SERVERREMOTE","52.66.101.239");
define("USERREMOTE","root");
define("PASSWORDREMOTE","cmcl@123");
define("DBREMOTE","$nick_name");

$emp_code=$_REQUEST['emp_code'];
$device_id=$_REQUEST['device_id'];
$mode=$_REQUEST['mode'];

function connecttodb($servername,$dbname,$dbuser,$dbpassword)
{
	$dbnamefinal="starsaat_".$dbname;
	$link=mysql_connect($servername,$dbuser,$dbpassword,TRUE) or die("Database Connection Error.");
	mysql_select_db($dbnamefinal,$link) or die("could not connect the database for invalid nick name");
	return $link;
}
$linksetup=connecttodb(SERVER,"acednsproduct",USER,PASSWORD);

$sqldbaccessdetails="SELECT remote_db_access FROM user_details WHERE nick_name='".$nick_name."'";
$rsdbaccessdetails=mysql_query($sqldbaccessdetails,$linksetup);
$rowdbaccessdetails=mysql_fetch_array($rsdbaccessdetails);
$remote_db_access=$rowdbaccessdetails['remote_db_access'];

if($remote_db_access=='yes')
{
	$link=connecttodb(SERVERREMOTE,DBREMOTE,USERREMOTE,PASSWORDREMOTE);
}
else
{
    $link=connecttodb(SERVER,"$nick_name",USER,PASSWORD);
}

$sqlselectversion="SELECT version_code  FROM db_version ";
$rsselectversion=mysql_query($sqlselectversion,$link);
$rowselectversion=mysql_fetch_array($rsselectversion);
$versionCode=$rowselectversion['version_code'];

function insertapilog($datetime,$emp_code,$url,$nick_name,$link)
{
	mysql_select_db("starsaat_".$nick_name);
	$sqlinsertapilog="INSERT INTO apicalllog SET date_time=CURRENT_TIMESTAMP,
					  emp_code='".$emp_code."',
					  url='".$url."'";
	mysql_query($sqlinsertapilog,$link);
}

$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/table-structure-details-6.0.2.php?nick_name=$nick_name&emp_code=$emp_code&device_id=$device_id&mode=$mode";
	insertapilog($datetime,$emp_code,$url,$nick_name,$link);

if($mode=='INSTALL')
{
	$sqlquery="SELECT * FROM table_structure_master ORDER BY t_structure_id";
	$result = mysql_query($sqlquery,$link) or die(mysql_error());
	$counttable=mysql_num_rows($result);
	if($emp_code!='')
	{
		$sqlselect="SELECT * FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code='".$emp_code."'";
	}
	else
	{
		$sqlselect="SELECT * FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code=''";
	}
	$rsselect=mysql_query($sqlselect,$link);
	$count=mysql_num_rows($rsselect);

	if($count<1){
		$sqlInsert="INSERT INTO table_structure_updation SET
				    emp_code='".$emp_code."',
					db_version_code='".$versionCode."',
					device_id='".$device_id."',
					is_update='0'";
		mysql_query($sqlInsert,$link);
	}
}
else{
	if($emp_code!='')
	{
		$sqlselect="SELECT * FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code='".$emp_code."'";
	}
	else
	{
		$sqlselect="SELECT * FROM table_structure_updation  WHERE device_id='".$device_id."' and emp_code=''";
	}
	$rsselect=mysql_query($sqlselect,$link);
	$count=mysql_num_rows($rsselect);

	if($count>0)
	{
		$rowselect=mysql_fetch_array($rsselect);
		$is_update=$rowselect['is_update'];
		$user_db_version_code=$rowselect['db_version_code'];
		$version_chk= ($versionCode-$user_db_version_code)*10;
		$chkval=1;
		//echo intval($chkval);
		if((intval($version_chk) > intval($chkval)) && $is_update==1)
		{
			$sqlquery="SELECT * FROM app_db_update_execution WHERE db_version > ".$user_db_version_code."
						AND db_version <= ".$versionCode."  ORDER BY table_name ASC ";
			$result = mysql_query($sqlquery,$linksetup) or die(mysql_error());
			$counttable=mysql_num_rows($result);
		}
		else if($is_update==1){
			$sqlquery="SELECT * FROM table_structure_master WHERE need_update='Y' ORDER BY t_structure_id DESC";
			$result = mysql_query($sqlquery,$link);
			$counttable=mysql_num_rows($result);

			$sqlcntupdation="SELECT COUNT(device_id) AS no_of_updated_users FROM table_structure_updation WHERE is_update='0'";
			$rscntupdation=mysql_query($sqlcntupdation,$link);
			$rowcntupdation=mysql_fetch_array($rscntupdation);
			$no_of_updated_users=$rowcntupdation['no_of_updated_users'];

			if($no_of_updated_users==no_of_licensed_users){
				$sqlupdatetable="UPDATE table_structure_master SET need_update='N',is_transaction='N',is_master='N'";
				mysql_query($sqlupdatetable,$link);
			}
		}
		else
		{
			$counttable=0;
		}
	}
}
if($counttable>0){
	$sqlquerybaseurl="SELECT previous_baseurl_app,current_baseurl_app FROM user_details WHERE nick_name='".$nick_name."'";
	$resultbaseurl = mysql_query($sqlquerybaseurl,$linksetup) or die(mysql_error());
	$rowbaseurl=mysql_fetch_array($resultbaseurl);
	$previous_baseurl_app=$rowbaseurl['previous_baseurl_app'];
	$current_baseurl_app=$rowbaseurl['current_baseurl_app'];
	if($previous_baseurl_app!=$current_baseurl_app || $mode=='INSTALL')
	{
		$baseurlchanged='Y';
	}
	else
	{
		$baseurlchanged='N';
	}

	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	while($rowstructuredetails = mysql_fetch_array($result))
	{
		$contents.="<data>";
		$contents .='<table_name><![CDATA['.mb_convert_encoding($rowstructuredetails['table_name'], 'UTF-8', 'UTF-8').']]></table_name>
					<table_structure><![CDATA['.mb_convert_encoding($rowstructuredetails['table_structure'], 'UTF-8', 'UTF-8').']]></table_structure>
					<transaction><![CDATA['.mb_convert_encoding($rowstructuredetails['is_transaction'], 'UTF-8', 'UTF-8').']]></transaction>
					<master><![CDATA['.mb_convert_encoding($rowstructuredetails['is_master'], 'UTF-8', 'UTF-8').']]></master>
					<db_version><![CDATA['.mb_convert_encoding($versionCode, 'UTF-8', 'UTF-8').']]></db_version>
					<base_url_changed><![CDATA['.mb_convert_encoding($baseurlchanged, 'UTF-8', 'UTF-8').']]></base_url_changed>
					<current_baseurl_app><![CDATA['.mb_convert_encoding($current_baseurl_app, 'UTF-8', 'UTF-8').']]></current_baseurl_app>
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
mysql_close($link);
?>

<?php
set_time_limit(0);
include "s_connection.php";
date_default_timezone_set("Asia/Kolkata");
$db_backup_files = "db_backup_files";
$mysqlDatabaseName ='starsaat_START';
$mysqlUserName ='starsaat_dnsprod';
$mysqlPassword ='dnsprod1234#';
$mysqlHostName ='localhost';

//$file = 'aceshop_dbnew2.sql.gz';
$file = "starsaat_START_".date("Y_m_d_h_i_s").".sql.gz";
$remote_file = 'admin/dbup/'.$file;

$mysqlExportPath ='public_html/'.$remote_file;

//DO NOT EDIT BELOW THIS LINE   // $mysqlPassword ='PharmacyFR12#';
//Export the database and output the status to the page
$command='mysqldump --opt -h' .$mysqlHostName .' -u' .$mysqlUserName .' -p' .$mysqlPassword .' ' .$mysqlDatabaseName .' | gzip > ~/' .$mysqlExportPath;

$output=array();
exec($command,$output,$worked);
switch($worked){
case 0:
$sql = "insert into $db_backup_files (`backup_file_name`) values ('$file')";
$res = mysql_query($sql);

echo 'Database <b>' .$mysqlDatabaseName .'</b> successfully exported to <b>~/' .$mysqlExportPath .'</b>';
break;
case 1:
echo 'There was a warning during the export of <b>' .$mysqlDatabaseName .'</b> to <b>~/' .$mysqlExportPath .'</b>';
break;
case 2:
echo 'There was an error during export. Please check your values:<br/><br/><table><tr><td>MySQL Database Name:</td><td><b>' .$mysqlDatabaseName .'</b></td></tr><tr><td>MySQL User Name:</td><td><b>' .$mysqlUserName .'</b></td></tr><tr><td>MySQL Password:</td><td><b>NOTSHOWN</b></td></tr><tr><td>MySQL Host Name:</td><td><b>' .$mysqlHostName .'</b></td></tr></table>';
break;
}
mysql_close();
?>
<?php
function duplicateTables($sourceDB=NULL, $targetDB=NULL) {
	
    $link = mysql_connect('localhost', 'coralweb', 'coral5071') or die(mysql_error()); // connect to database
	$create_database=mysql_query("CREATE DATABASE ".$targetDB."");
    $result = mysql_query('SHOW TABLES FROM ' . $sourceDB) or die(mysql_error());
	$insert_table_array=array('admin_master','app_version','bank_master','db_version','table_structure_master','transport_mode_category','transport_mode_sub_category');
    while($row = mysql_fetch_row($result)) {
        mysql_query('DROP TABLE IF EXISTS `' . $targetDB . '`.`' . $row[0] . '`') or die(mysql_error());
        mysql_query('CREATE TABLE `' . $targetDB . '`.`' . $row[0] . '` LIKE `' . $sourceDB . '`.`' . $row[0] . '`') or die(mysql_error());
        if(in_array($row[0],$insert_table_array)){
			mysql_query('INSERT INTO `' . $targetDB . '`.`' . $row[0] . '` SELECT * FROM `' . $sourceDB . '`.`' . $row[0] . '`') or die(mysql_error());
		}
        mysql_query('OPTIMIZE TABLE `' . $targetDB . '`.`' . $row[0] . '`') or die(mysql_error());
    }
    mysql_free_result($result);
    mysql_close($link);
} // end duplicateTables()
duplicateTables('KUNJ', 'RUPA');
?>
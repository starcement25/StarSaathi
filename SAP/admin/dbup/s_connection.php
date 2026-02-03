<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
date_default_timezone_set("Asia/Kolkata");
$servername = "localhost";
// $username = "starsaat_dnsprod";
// $password = "dnsprod1234#";
$username = "root";
$password = "Passw0rd123#$";
$db_name = "starsaat_START";

$conn = mysql_connect($servername, $username, $password);
if(!$conn){
   die('Could not connect: ' . mysql_error());
}

$db_selected = mysql_select_db($db_name, $conn);
if (!$db_selected) {
    die ('Can\'t connect to database : ' . mysql_error());
}

?>
<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
if(!isset($_SESSION["sswa_user_id"])){
	header("location:index.php");
}


?>
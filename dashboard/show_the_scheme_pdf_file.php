<?php
$the_file_name = $_GET["the_file_name"] ? trim($_GET["the_file_name"]) : "";
if($the_file_name!=""){
$file = '../schemes/'.$the_file_name;
// Header content type 
header('Content-type: application/pdf'); 

header('Content-Disposition: inline; filename="' . $the_file_name . '"'); 

header('Content-Transfer-Encoding: binary'); 

header('Accept-Ranges: bytes'); 

// Read the file 
@readfile($file); 
}
?>
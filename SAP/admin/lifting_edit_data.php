<?php
    include "web_check.php";
    include "star_connection.php";
    $lifting = "lifting";

    $inputValueText=$_REQUEST['inputValueText'];
    $id=$_REQUEST['id'];

    $sql_query = mysql_query("UPDATE $lifting SET `total_bags` = '$inputValueText' WHERE `lid`='$id'");

    // echo "UPDATE $lifting SET `total_bags` = '$inputValue' WHERE `total_bags`='$id'";

    // die();

    $sql_query2=mysql_query("SELECT `total_bags` FROM $lifting WHERE `lid`='$id'");

    if(mysql_num_rows($sql_query2)>0){
        $response=mysql_fetch_array($sql_query2);
        echo $response['total_bags'];
    }else{
        return false;
    }

?>
<?php

include "web_check.php";
include "star_connection.php";

$lift_table = "dealer_registration_web_form";

$id = isset($_GET["id"])
    ? (int)$_GET["id"]
    : 0;

if ($id <= 0) {
    echo "<script>
        alert('Invalid dealer ID.');
        window.location='dealer_registration_list.php';
    </script>";
    exit;
}


/* =========================================================
   DELETE DEALER
========================================================= */

$sql = "
    DELETE FROM $lift_table
    WHERE id = $id
    LIMIT 1
";

$result = mysql_query($sql);


if ($result) {

    echo "<script>
        alert('Dealer deleted successfully.');
        window.location='dealer_registration_list.php';
    </script>";

} else {

    echo "<script>
        alert('Unable to delete dealer.');
        window.location='dealer_registration_list.php';
    </script>";

}


mysql_close();

?>
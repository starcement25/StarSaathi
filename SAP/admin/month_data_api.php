<?php 
include "star_connection.php";

$pop_order = "pop_order";
$pop_product_master = "pop_product_master";
$customer_master = "customer_master";
$branch_master = "branch_master";
$t_order_pop = "T_ORDER_POP";
$product_master = "product_master";
$broker_master = "broker_master";
$lifting = "lifting";

$monthData=$_POST['monthData'];

$monthNumber = date('n', strtotime($monthData));

// echo "Month Number: $monthNumber"."<br/>";

$sql4 = "SELECT * FROM lifting WHERE MONTH(`date_of_lifting`)='$monthNumber'";

// echo $sql4;

$query4 = mysql_query($sql4);

$result4 = array();

while ($data4 = mysql_fetch_array($query4)) {
    $result4[] = $data4;
}

echo json_encode($result4);

?>

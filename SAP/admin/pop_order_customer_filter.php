<?php 
    include "star_connection.php";

    $pop_order="pop_order";
    $pop_product_master="pop_product_master";
    $customer_master="customer_master";
    $branch_master="branch_master";
    $t_order_pop="T_ORDER_POP";
    $product_master="product_master";
    $broker_master="broker_master";
    $lifting="lifting";

    $custId=$_POST['custId'];

    $sql1="select * from $pop_order where `customer_code`='$custId'";

    $query1=mysql_query($sql1);

    $result1=array();

    while($data1=mysql_fetch_array($query1)){
        $result1[]=$data1;
    }

    echo json_encode($result1);

?>

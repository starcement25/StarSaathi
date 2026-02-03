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

    $linkedDealerName=$_POST['linkedDealerName'];

    $sql2="select * from $lifting where `linked_dealer_name`='$linkedDealerName'";

    $query2=mysql_query($sql2);

    $result2=array();

    while($data2=mysql_fetch_array($query2)){
        $result2[]=$data2;
    }

    echo json_encode($result2);

?>

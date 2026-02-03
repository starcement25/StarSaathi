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

    $subDealerName=$_POST['subDealerName'];

    $sql2="select * from $lifting where `sub_dealer_rssd_name`='$subDealerName'";

    $query2=mysql_query($sql2);

    $result2=array();

    while($data2=mysql_fetch_array($query2)){
        $result2[]=$data2;
    }

    echo json_encode($result2);

?>

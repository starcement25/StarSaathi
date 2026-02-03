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

    $branchCode=$_POST['branchCode'];

    $sql0="select * from $branch_master where `branch_code`='$branchCode'";
    
    $res0=mysql_query($sql0);
    $branch=mysql_fetch_assoc($res0);
    $branch_name=$branch['branch_name'];

    $sql="select * from $pop_order where `branch`='$branch_name'";

    $query=mysql_query($sql);

    $result=array();

    while($data=mysql_fetch_array($query)){
    
    $result[]=$data;
}

    echo json_encode($result);

?>

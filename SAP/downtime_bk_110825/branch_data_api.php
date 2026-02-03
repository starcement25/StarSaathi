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
    $linkedDealerName=$_POST['linkedDealerName'];
    $subDealerName=$_POST['subDealerName'];
    $monthData=$_POST['monthData'];
    $monthNumber = date('n', strtotime($monthData));

    $sql4 = "";

    $sql0="select * from $branch_master where `branch_code`='$branchCode'";
    
    $res0=mysql_query($sql0);
    $branch=mysql_fetch_assoc($res0);
    $branch_name=$branch['branch_name'];

    if($branch_name!='' && $linkedDealerName=='' && $subDealerName=='' && $monthData==''){
        $sql="select * from $lifting where `branch`='$branch_name'";
    }else if($branch_name=='' && $linkedDealerName!='' && $subDealerName=='' && $monthData==''){
        $sql="select * from $lifting where `linked_dealer_name`='$linkedDealerName'";
    }else if($branch_name=='' && $linkedDealerName=='' && $subDealerName!='' && $monthData==''){
        $sql="select * from $lifting where `sub_dealer_rssd_name`='$subDealerName'";
    }else if($branch_name=='' && $linkedDealerName=='' && $subDealerName=='' && $monthData!=''){
        $sql="SELECT * FROM $lifting WHERE MONTH(`date_of_lifting`)='$monthNumber'";
    }else if($branch_name!='' && $linkedDealerName!='' && $subDealerName=='' && $monthData!=''){
        $sql="select * from $lifting where `branch`='$branch_name' and `linked_dealer_name`='$linkedDealerName' and MONTH(`date_of_lifting`)='$monthNumber'";
    }else if($branch_name!='' && $linkedDealerName!='' && $subDealerName!='' && $monthData!=''){
        $sql="select * from $lifting where `branch`='$branch_name' and `linked_dealer_name`='$linkedDealerName' and `sub_dealer_rssd_name`='$subDealerName' and MONTH(`date_of_lifting`)='$monthNumber'";
    }else if($branch_name!='' && $linkedDealerName=='' && $subDealerName!='' && $monthData!=''){
        $sql="select * from $lifting where `branch`='$branch_name' and `sub_dealer_rssd_name`='$subDealerName' and MONTH(`date_of_lifting`)='$monthNumber'";
    }else if($branch_name!='' && $linkedDealerName=='' && $subDealerName=='' && $monthData!=''){
        $sql="select * from $lifting where `branch`='$branch_name' and MONTH(`date_of_lifting`)='$monthNumber'";
    }else if($branch_name=='' && $linkedDealerName!='' && $subDealerName=='' && $monthData!=''){
        $sql="select * from $lifting where `linked_dealer_name`='$linkedDealerName' and MONTH(`date_of_lifting`)='$monthNumber'";
    }else if($branch_name=='' && $linkedDealerName=='' && $subDealerName!='' && $monthData!=''){
        $sql="select * from $lifting where `sub_dealer_rssd_name`='$subDealerName' and MONTH(`date_of_lifting`)='$monthNumber'";
    }else if($branch_name!='' && $linkedDealerName!='' && $subDealerName!='' && $monthData==''){
        $sql="select * from $lifting where `branch`='$branch_name' and `linked_dealer_name`='$linkedDealerName' and `sub_dealer_rssd_name`='$subDealerName'";
    }else if($branch_name!='' && $linkedDealerName!='' && $subDealerName=='' && $monthData==''){
        $sql="select * from $lifting where `branch`='$branch_name' and `linked_dealer_name`='$linkedDealerName'";
    }else if($branch_name!='' && $linkedDealerName=='' && $subDealerName!='' && $monthData==''){
        $sql="select * from $lifting where `branch`='$branch_name' and `sub_dealer_rssd_name`='$subDealerName'";
    }
        
    
    

    $query=mysql_query($sql);

    $result=array();

    while($data=mysql_fetch_array($query)){
    
    $result[]=$data;
}

    echo json_encode($result);

?>

<?php
session_start();
set_time_limit(0);
include "star_connection.php";
$res_msg			= array();
$customer_master = "customer_master";
$broker_master = "broker_master";
$customer_broker_relation = "customer_broker_relation";
$sswa_the_broker_id = $_SESSION["sswa_user_id"];
$sswa_the_dns_broker_id = $_SESSION["sswa_user_dns_id"];
$dealer_id	= $_POST["dealer_id"] ? addslashes(trim($_POST["dealer_id"])) : "";
$customer_type	= $_POST["customer_type"] ? addslashes(trim($_POST["customer_type"])) : "";

if($dealer_id!=""){

    if ($customer_type == "subdealer" || $customer_type == "rssd") {
        $sql24 = "select $customer_broker_relation.`broker_code`,$customer_master.`customer_name`,$customer_master.`customer_code`,$customer_master.`cust_type`,$customer_master.`dns_customer_code` from $customer_broker_relation left join $customer_master on $customer_broker_relation.`customer_code`=$customer_master.`customer_code` where $customer_master.`dns_customer_code`='$dealer_id'";
    }else{
        $sql24 = "select $customer_broker_relation.`broker_code`,$customer_master.`customer_name`,$customer_master.`customer_code`,$customer_master.`cust_type`,$customer_master.`dns_customer_code` from $customer_broker_relation left join $customer_master on $customer_broker_relation.`customer_code`=$customer_master.`customer_code` where $customer_broker_relation.`broker_code`='$sswa_the_broker_id' and $customer_master.`dns_customer_code`='$dealer_id'";
    }
    
$res24 = mysql_query($sql24);
$totres24 = mysql_num_rows($res24);
if($totres24>0){
$row24 = mysql_fetch_assoc($res24);
$selected_customer_name = trim($row24["customer_name"]);
$selected_customer_code = trim($row24["customer_code"]);
$selected_cust_type = trim($row24["cust_type"]);
$selected_dns_customer_code = trim($row24["dns_customer_code"]);

$_SESSION["sswa_selected_dealer_name"]= $selected_customer_name;
$_SESSION["sswa_selected_dealer_code"]= $selected_dns_customer_code;
$_SESSION["sswa_selected_cust_type"]= $selected_cust_type;
$_SESSION["sswa_selected_customer_code"]= $selected_customer_code;

$res_msg = array("process_sts"=>"YES");

}else{
$res_msg = array("process_sts"=>"NO");
}

}else{
$res_msg = array("process_sts"=>"NO");
}
echo json_encode($res_msg);
mysql_close();
?>
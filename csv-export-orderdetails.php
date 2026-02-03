<?php	
set_time_limit(1000);
error_reporting(E_ALL ^ E_NOTICE);
require("include/config.php");
require("include/dbcon.php");
require("include/functions.php");

$fileName = $_REQUEST['branch_name']."-orderdetails-".time().".csv";
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$fileName);
header("Pragma: no-cache");
header("Expires: 0");

$body='Order Number, Customer Code, Customer Name, Emp Code, Emp Name,Order Date,Prod Group,SKU Code,SKU Name,Qty,Sale Rate'."\n";	

$empcodelist=mysql_query("SELECT EM.emp_code as empcode FROM `employee_master` EM,branch_master BM WHERE EM.branch_code=BM.branch_code AND BM.branch_name='".$_REQUEST['branch_name']."'");
while ($ids = mysql_fetch_array($empcodelist)){
    $idss[] = $ids['empcode'];
}
$empin = "'".implode("','", $idss)."'";


$orderno=mysql_query("SELECT order_no,customer_code,SUBSTRING(order_no,2,5) as emp_code, DATE_FORMAT(SUBSTRING(order_no,-14,8),'%d-%m-%Y') as order_date, customer_code FROM `order_header` where csv_transfer_flag='0' and SUBSTRING(order_no,2,5) IN($empin)");

while($row_order_header=mysql_fetch_array($orderno))
{
        
    $order_no = $row_order_header['order_no'];
    $emp_code = $row_order_header['emp_code'];
    $order_date = $row_order_header['order_date'];
    $customer_code = $row_order_header['customer_code'];
    $transaction_type = $row_order_header['transaction_type'];

    $update_header=mysql_query("UPDATE order_header SET csv_transfer_flag='1' WHERE order_no='".$order_no."' limit 1" );
    
    $sql_customer_details = "SELECT dns_customer_code, customer_name FROM customer_master WHERE customer_code = '".$customer_code."'";
    $res_customer_details = mysql_query($sql_customer_details);
    $row_customer_details = mysql_fetch_array($res_customer_details);
    $dns_customer_code = $row_customer_details['dns_customer_code'];
    $customer_name = $row_customer_details['customer_name'];
    
    $sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
    $res_emp_details = mysql_query($sql_emp_details);
    $row_emp_details = mysql_fetch_array($res_emp_details);
    $dns_emp_code = $row_emp_details['dns_emp_code'];
    $emp_name = $row_emp_details['emp_name'];

    $sql_order_details = "SELECT sku_code, qty, sale_rate, amount FROM order_details WHERE order_no = '".$order_no."'";
    $res_order_details = mysql_query($sql_order_details);
    while($row_order_details = mysql_fetch_array($res_order_details)){
        $sku_code = $row_order_details['sku_code'];
        $qty = $row_order_details['qty'];
        $sale_rate = $row_order_details['sale_rate'];
        $amount = $row_order_details['amount'];
        
       
        
        $sql_sku_name = "SELECT dns_prod_code, product_group_code, prod_desc FROM product_master WHERE prod_code = '".$sku_code."'";
        $res_sku_name = mysql_query($sql_sku_name);
        $row_sku_name = mysql_fetch_array($res_sku_name);
        $sku_name = $row_sku_name['prod_desc'];
        $dns_prod_code = $row_sku_name['dns_prod_code'];
        $product_group_code = $row_sku_name['product_group_code'];
        
        $sql_prod_group_name = "SELECT product_group_name FROM product_group_master WHERE product_group_code = '".$product_group_code."'";
        $res_prod_group_name = mysql_query($sql_prod_group_name);
        $row_prod_group_name = mysql_fetch_array($res_prod_group_name);
        $prod_group_name = $row_prod_group_name['product_group_name'];  

        $body.= "\"".$order_no."\",";
        $body.= "\"".$dns_customer_code."\",";
        $body.= "\"".$customer_name."\",";
        $body.= "\"".$dns_emp_code."\",";
        $body.= "\"".$emp_name."\",";
        $body.= "\"".$order_date."\",";
        $body.= "\"".$prod_group_name."\",";
        $body.= "\"".$dns_prod_code."\",";
        $body.= "\"".$sku_name."\",";
        $body.= "\"".$qty."\",";
        $body.= "\"".$sale_rate."\"\n";

    }
}
echo $body;
exit;
?>
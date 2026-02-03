<?php		
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

    
	$sqlempcodefetch="SELECT emp_code FROM employee_master WHERE acedns='Y'";
    $rsempcodefetch=mysql_query($sqlempcodefetch);
	$emp_code_no_appaccess='';
    while($rowempcodefetch=mysql_fetch_array($rsempcodefetch))
    {
        $emp_code_fetched=$rowempcodefetch['emp_code'];
        $sqlchkchild="SELECT COUNT(emp_code) AS total_child FROM employee_master WHERE reporting_to='".$emp_code_fetched."'";
        $rschkchild=mysql_query($sqlchkchild);
        $rowchkchild=mysql_fetch_array($rschkchild);
        $total_child=$rowchkchild['total_child'];
        
        $sqlchkcustomer="SELECT COUNT(customer_code) AS total_customer FROM customer_master WHERE emp_code='".$emp_code_fetched."'";
        $rschkcustomer=mysql_query($sqlchkcustomer);
        $rowchkcustomer=mysql_fetch_array($rschkcustomer);
        $total_customer=$rowchkcustomer['total_customer'];
        
        if($total_child==0  && $total_customer==0)
        {
          $emp_code_no_appaccess=$emp_code_no_appaccess."'".$emp_code_fetched."'".',';
        }
	}
	echo $emp_code_no_appaccess=substr($emp_code_no_appaccess,0,-1);
 ?>
<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

/*$sqlbranch="SELECT branch_code FROM branch_master ORDER BY branch_code ASC";
$rsbranch=mysql_query($sqlbranch);
while($rowbranch=mysql_fetch_array($rsbranch))
{
	$branch_code_cl_stk=$rowbranch['branch_code'];*/
	$sqlprod="SELECT prod_code,vertical_value FROM product_master WHERE prod_code NOT IN(SELECT product_code FROM mrp)";
	$rsprod=mysql_query($sqlprod);
	while($rowprod=mysql_fetch_array($rsprod))
	{
		$prod_code=$rowprod['prod_code'];
		$vertical_value=$rowprod['vertical_value'];
		$sqlmaxmrpcode="SELECT MAX( CAST( SUBSTRING( mrp_code, -(length( mrp_code ) -1), length( mrp_code ) -1 ) AS UNSIGNED ) ) AS max_mrp_code from mrp";
		$rsmaxmrpcode=mysql_query($sqlmaxmrpcode);
		$rowmaxmrpcode=mysql_fetch_array($rsmaxmrpcode);
		$max_mrp_code=$rowmaxmrpcode['max_mrp_code'];
		
		if($max_mrp_code=='')
		{
			$max_mrp_code='001';
		}
		else
		{
			$max_mrp_code++;
		}
		$max_mrp_code='z'.$max_mrp_code;

		$sql  = "insert into mrp ";
		$sql .= " SET product_code='".$prod_code."'";
		$sql .= " , branch_code=''";
		$sql .= " , mrp_code='".$max_mrp_code."'";
		$sql .= " , dns_mrp_code=''";
		$sql .= " , mrp='0'";
		$sql .= " , sale_rate='0'";
		$sql .= " , vertical_value='".mysql_real_escape_string($vertical_value)."'";
		$sql .= " , UOM=''";
		$sql .= " , download_time=CURRENT_TIMESTAMP()";
		mysql_query($sql);
	}
//}
//echo "Successfull";
?>

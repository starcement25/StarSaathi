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
	$sqlprod="SELECT * FROM `customer_master` WHERE `customer_code` NOT IN(SELECT customer_code FROM customer_branch_relation)";
	$rsprod=mysql_query($sqlprod);
	while($rowprod=mysql_fetch_array($rsprod))
	{
		$customer_code=$rowprod['customer_code'];
		$branch_code_name='BC35,BC27,BC18';
		$sqlbranchcode="SELECT branch_code FROM branch_master WHERE FIND_IN_SET(dns_branch_code,'".$branch_code_name."')";
		$rsbranchcode=mysql_query($sqlbranchcode);
		while($rowbranchcode=mysql_fetch_array($rsbranchcode))
		{
			$branch_code=$rowbranchcode['branch_code'];
			$sqlcustomerbranch="SELECT customer_code FROM customer_branch_relation WHERE customer_code='".$customer_code."' AND branch_code='".$branch_code."'";
				$rscustomerbranch=mysql_query($sqlcustomerbranch);
				$countcustomerbranch=mysql_num_rows($rscustomerbranch);
				if($countcustomerbranch <1)
				{
					$sqlinsertcustomerbranch="INSERT INTO customer_branch_relation ";
					$sqlinsertcustomerbranch .= " SET customer_code='".$customer_code."'";
					$sqlinsertcustomerbranch .= " , branch_code='".$branch_code."'";
					mysql_query($sqlinsertcustomerbranch);
				}
			}

	}
//}
//echo "Successfull";
?>

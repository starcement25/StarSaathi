<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_EMAMI");

mysql_connect(SERVER,USER,PASSWORD);
mysql_select_db(DB);
$sqlquery="SELECT branch_code,route_code FROM branch_route_freight_test";
$result = mysql_query($sqlquery);
		while($rowcustomer = mysql_fetch_array($result))
		{
			$branch_code_new=$rowcustomer['branch_code'];
			$route_code_new=$rowcustomer['route_code'];
			$sqlbranch="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$branch_code_new."'";
			$rsbranch=mysql_query($sqlbranch);
			$rowbranch=mysql_fetch_array($rsbranch);
			$branch_code=$rowbranch['branch_code'];
			
			$sqlroute="SELECT route_code FROM route_master WHERE route_name='".$route_code_new."'";
			$rsroute=mysql_query($sqlroute);
			$rowroute=mysql_fetch_array($rsroute);
			$route_code=$rowroute['route_code'];
			
			$sqlchk="SELECT freight FROM branch_route_freight WHERE branch_code='".$branch_code."' AND route_code='".$route_code."' AND acedns='Y'"; 
			$rschk=mysql_query($sqlchk);
			$countchk=mysql_num_rows($rschk);
			if($countchk==0)
			{
				$sqlbranchdestinationfreight  = "insert into branch_route_freight ";
				$sqlbranchdestinationfreight .= " SET branch_code='".$branch_code."'";
				$sqlbranchdestinationfreight .= " ,route_code='".$route_code."'";
				$sqlbranchdestinationfreight .= " ,acedns='Y'";
				$sqlbranchdestinationfreight .= " ,freight='0'";
				$sqlbranchdestinationfreight .= " , `date`='2017-08-07'";
				echo $sqlbranchdestinationfreight .= " , download_time=CURRENT_TIMESTAMP()";
				mysql_query($sqlbranchdestinationfreight);
			}
		}
			//exit();
	echo 'SUCCESS';
?>

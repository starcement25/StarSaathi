<?php
	/*define("SERVERREMOTE","103.87.174.95");
	define("USERREMOTE","starsaat_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234#");
	define("DBREMOTE","starsaathi_STARS");
		
	$link=mysql_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die(mysql_error()."Database Connection Error.");
	mysql_select_db(DBREMOTE,$link) or die(mysql_error()."could not connect the database");
	*/
	include "star_connection.php";
	include "../cron_page_start.php";
	$dns_branch_code_array=array();
	$sqlbranchSAP="SELECT VKGRP,BEZEI,STATE,STATUS FROM pbranchmaster ORDER BY AEDAT ASC";
	$rsbranchSAP=mysql_query($sqlbranchSAP);
	$countbranchSAP=mysql_num_rows($rsbranchSAP);
	if($countbranchSAP >0)
	{
	while($rowbranchSAP=mysql_fetch_array($rsbranchSAP))
	{
		$dns_branch_code=$rowbranchSAP['VKGRP'];
		if(!in_array($dns_branch_code,$dns_branch_code_array))
			{
				array_push($dns_branch_code_array,$dns_branch_code);
			}
			${branch_name.$dns_branch_code}=$rowbranchSAP['BEZEI'];
			${branch_state.$dns_branch_code}=$rowbranchSAP['STATE'];
			${acedns.$dns_branch_code} .=" ".$rowbranchSAP['STATUS'];
	}
	foreach($dns_branch_code_array as $dns_branch_code_val){
			
			$dns_branch_code=$dns_branch_code_val;
			$branch_name=${branch_name.$dns_branch_code_val};
			$branch_state=${branch_state.$dns_branch_code_val};
			$acedns=${acedns.$dns_branch_code_val};
			$acedns='Y';
			
			echo $sqlbranchchk="SELECT * FROM branch_master WHERE dns_branch_code='".addslashes($dns_branch_code)."'";
			$rsbranchchk=mysql_query($sqlbranchchk);
			$countbranchchk=mysql_num_rows($rsbranchchk);
			$csv_row_count=$rec_count+1;
			if($countbranchchk<1)
			{
				$sqlmaxbranchcode="SELECT MAX(branch_code) AS max_branch_code FROM  branch_master WHERE 1";
				$rsmaxbranchcode=mysql_query($sqlmaxbranchcode);
				$rowmaxbranchcode=mysql_fetch_array($rsmaxbranchcode);
				$max_branch_code=$rowmaxbranchcode['max_branch_code'];
				
				if($max_branch_code=='')
				{
					$max_branch_code='B0001';
				}
				else
				{
					$max_branch_code++;
				}
			
				$sqlbranch  = "insert into branch_master SET ";
				$sqlbranch .= "  	branch_code='".mysql_real_escape_string($max_branch_code)."'";
				$sqlbranch .= " , dns_branch_code='".mysql_real_escape_string($dns_branch_code)."'";
				$sqlbranch .= " , branch_name='".mysql_real_escape_string($branch_name)."'";
				$sqlbranch .= " , branch_state='".mysql_real_escape_string($branch_state)."'";
				$sqlbranch .= " , branch_location=''";
				$sqlbranch .= " , acedns='".mysql_real_escape_string($acedns)."'";
				$sqlbranch .= " , comp_code=''";
				$sqlbranch .= " , branch_email_id=''";
				$sqlbranch .= " , alternative_email_id=''";
				echo $sqlbranch .= " , download_time=CURRENT_TIMESTAMP()";
			}
			else
			{
				$rowbranchnamechk=mysql_fetch_array($rsbranchnamechk);
				$branch_code=$rowbranchnamechk['branch_code'];

				$sqlbranch  = "UPDATE branch_master SET ";
				$sqlbranch .= "  	dns_branch_code='".mysql_real_escape_string($dns_branch_code)."'";
				$sqlbranch .= " , branch_location=''";
				$sqlbranch .= " , branch_name='".mysql_real_escape_string($branch_name)."'";
				$sqlbranch .= " , branch_state='".mysql_real_escape_string($branch_state)."'";
				$sqlbranch .= " , comp_code=''";
				$sqlbranch .= " , branch_email_id=''";
				$sqlbranch .= " , acedns='".mysql_real_escape_string($acedns)."'";
				$sqlbranch .= " , download_time=CURRENT_TIMESTAMP()";
				$sqlbranch .= " , alternative_email_id='' 
								WHERE branch_code='".$branch_code."'";
			}
			mysql_query($sqlbranch);
	}

		echo 'SUCCESS';
	}
	else echo 'SOMETHING WENT WRONG';
	include "../cron_page_end.php";
	mysql_close();
?>

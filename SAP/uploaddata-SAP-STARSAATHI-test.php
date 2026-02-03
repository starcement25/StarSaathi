<?php
	set_time_limit(0);
	ini_set('memory_limit', '-1');

	define("SERVERREMOTE","103.87.174.95");
	define("USERREMOTE","starsaat_dnsprod");
	define("PASSWORDREMOTE","dnsprod1234#");
	define("DBREMOTE","starsaathi_STARS");
		
	$link=mysql_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die(mysql_error()."Database Connection Error.");
	mysql_select_db(DBREMOTE,$link) or die(mysql_error()."could not connect the database");
	
	$dns_customer_code_array=array();
	/*$sqlcustomerSAP="SELECT KUNNR,ALTKN,NAME1,NAME2,NAME3,STRAS,STREET1,STREET2,STREET3,ORT01,ORT02,PSTLZ,TELF2,
					CREDIT_LIMIT,SMTP_ADDR,VKGRP,KDGRP,PAN_NUMBER,MIN_STOCK_QTY,ACCOUNT_NO,CITY2,COUNTER_POT,ZZONE,LZONE,VTEXT,`SEARCH_TERM2`,AUFSD,`REGION` 
					FROM ptblcustomermaster WHERE SEARCH_TERM2='1500000178' ORDER BY KUNNR,AEDAT ASC,ADDITIONAL_DATA1 ASC";*/
	$sqlcustomerSAP="SELECT KUNNR,ALTKN,NAME1,NAME2,NAME3,STRAS,STREET1,STREET2,STREET3,ORT01,ORT02,PSTLZ,TELF2,
					CREDIT_LIMIT,SMTP_ADDR,VKGRP,KDGRP,PAN_NUMBER,MIN_STOCK_QTY,ACCOUNT_NO,CITY2,COUNTER_POT,ZZONE,LZONE,VTEXT,`SEARCH_TERM2`,AUFSD,`REGION` 
					FROM ptblcustomermaster WHERE KDGRP NOT IN('15') AND KUNNR='1400046923' ORDER BY KUNNR,AEDAT ASC,ADDITIONAL_DATA1 ASC";
	$rscustomerSAP=mysql_query($sqlcustomerSAP);
	$countcustomerSAP=mysql_num_rows($rscustomerSAP);
	if($countcustomerSAP >0)
	{
	while($rowcustomerSAP=mysql_fetch_array($rscustomerSAP))
	{
		$dns_customer_code=$rowcustomerSAP['KUNNR'];
		if(!in_array($dns_customer_code,$dns_customer_code_array))
			{
				array_push($dns_customer_code_array,$dns_customer_code);
			}
			if($rowcustomerSAP['NAME1']!='')
			{
			${customer_name.$dns_customer_code}=$rowcustomerSAP['NAME1'];
			}
			if($rowcustomerSAP['NAME2']!='')
			{
			${customer_name.$dns_customer_code} .=" ".$rowcustomerSAP['NAME2'];
			}
			if($rowcustomerSAP['NAME3']!='')
			{
			${customer_name.$dns_customer_code} .=" ".$rowcustomerSAP['NAME3'];
			}		
			${customer_code_dns.$dns_customer_code}=$rowcustomerSAP['ALTKN'];
			${branch_code.$dns_customer_code}=$rowcustomerSAP['VKGRP'];
			${dns_route_code.$dns_customer_code}='';
			${route_name.$dns_customer_code}='';
			${phone_no.$dns_customer_code}=$rowcustomerSAP['TELF2'];
			${acedns.$dns_customer_code}=$rowcustomerSAP['AUFSD'];
			${cust_type.$dns_customer_code}=$rowcustomerSAP['KDGRP'];
			if($rowcustomerSAP['STRAS']!='')
			{
			${address.$dns_customer_code}=$rowcustomerSAP['STRAS'];
			}
			if($rowcustomerSAP['STREET1']!='')
			{
				${address.$dns_customer_code} .=", ".$rowcustomerSAP['STREET1'];
			}
			if($rowcustomerSAP['STREET2']!='')
			{
				${address.$dns_customer_code} .=", ".$rowcustomerSAP['STREET2'];
			}
			if($rowcustomerSAP['STREET3']!='')
			{
				${address.$dns_customer_code} .=", ".$rowcustomerSAP['STREET3'];
			}
			/*if($rowcustomerSAP['ORT01']!='')
			{
				${address.$dns_customer_code} .=", ".$rowcustomerSAP['ORT01'];
			}
			if($rowcustomerSAP['ORT02']!='')
			{
				${address.$dns_customer_code} .=", ".$rowcustomerSAP['ORT02'];
			}*/
			if($rowcustomerSAP['PSTLZ']!='')
			{
				${address.$dns_customer_code} .=", ".$rowcustomerSAP['PSTLZ'];
			}
			${pin.$dns_customer_code}=$rowcustomerSAP['PSTLZ'];
			${rds_tag.$dns_customer_code}='';
			${credit_limit.$dns_customer_code}=$rowcustomerSAP['CREDIT_LIMIT'];
			${pan.$dns_customer_code}=$rowcustomerSAP['PAN_NUMBER'];
			${minimum_stock.$dns_customer_code}=$rowcustomerSAP['MIN_STOCK_QTY'];
			${email.$dns_customer_code}=$rowcustomerSAP['SMTP_ADDR'];
			${district.$dns_customer_code}=$rowcustomerSAP['CITY2'];
			${bank_account_number.$dns_customer_code}=$rowcustomerSAP['ACCOUNT_NO'];
			${monthly_potential.$dns_customer_code}=$rowcustomerSAP['COUNTER_POT'];
			${zone.$dns_customer_code}=$rowcustomerSAP['ZZONE'];
			${destination.$dns_customer_code}=$rowcustomerSAP['LZONE'];
			${destination_name.$dns_customer_code}=$rowcustomerSAP['VTEXT'];
			${sub_dealer_old_code.$dns_customer_code}=$rowcustomerSAP['SEARCH_TERM2'];
			${region.$dns_customer_code}=$rowcustomerSAP['REGION'];
		}
	foreach($dns_customer_code_array as $dns_customer_code_val){
			
			/*echo $dns_customer_code_val;
			echo '<br />';
			echo ${customer_name.$dns_customer_code_val};
			echo '<br />';
			echo  ${branch_code.$dns_customer_code_val};
			echo '<br />';
			echo ${dns_route_code.$dns_customer_code_val};
			echo '<br />';
			echo ${route_name.$dns_customer_code_val};
			echo '<br />';
			echo ${phone_no.$dns_customer_code_val};
			echo '<br />';
			echo ${acedns.$dns_customer_code_val};
			echo '<br />';
			echo ${whatsapp_no.$dns_customer_code_val};
			echo '<br />';
			echo ${cust_type.$dns_customer_code_val};
			echo '<br />';
			echo ${address.$dns_customer_code_val};
			echo '<br />';*/
			$customer_id=$dns_customer_code_val;
			//echo 'dfdfdfdfdfdf'.$dns_customer_code;
			$customer_name=${customer_name.$dns_customer_code_val};
			//$customer_id=${customer_id.$dns_customer_code_val};
			$branch_code=${branch_code.$dns_customer_code_val};
			$dns_route_code=${dns_route_code.$dns_customer_code_val};
			$route_name=${route_name.$dns_customer_code_val};
			$phone_no=${phone_no.$dns_customer_code_val};
			$acedns=${acedns.$dns_customer_code_val};
			$cust_type=${cust_type.$dns_customer_code_val};
			$address=${address.$dns_customer_code_val};
			$rds_tag=${rds_tag.$dns_customer_code_val};
			$pin=${pin.$dns_customer_code_val};
			$credit_limit=${credit_limit.$dns_customer_code_val};
			$email=${email.$dns_customer_code_val};
			$pan=${pan.$dns_customer_code_val};
			$minimum_stock=${minimum_stock.$dns_customer_code_val};
			$district=${district.$dns_customer_code_val};
			$bank_account_number=${bank_account_number.$dns_customer_code_val};
			$monthly_potential=${monthly_potential.$dns_customer_code_val};
			$zone=${zone.$dns_customer_code_val};
			$destination=${destination.$dns_customer_code_val};
			$destination_name=${destination_name.$dns_customer_code_val};
			$sub_dealer_old_code=${sub_dealer_old_code.$dns_customer_code_val};
			$region=${region.$dns_customer_code_val};
			
			if($cust_type=='21' || $cust_type=='22')
			{
				$mapped_dealer_ship_to_party_dealer=${sub_dealer_old_code.$dns_customer_code_val};
			}
			else $mapped_dealer_ship_to_party_dealer='';
			
			if($cust_type!='07' && $cust_type!='08'  && $cust_type!='09' && $cust_type!='21' && $cust_type!='22')
			{
				$dns_customer_code=${sub_dealer_old_code.$dns_customer_code_val};
			}
			else if($cust_type=='21' || $cust_type=='22')
			{
				$dns_customer_code=$customer_id;
			}
			else
			{
				$dns_customer_code=${customer_code_dns.$dns_customer_code_val};
			}

			if($acedns=='' || $acedns=='NULL')
			{
				$acedns='Y';
			}
			else $acedns='N';
			
			//For Ship to Party
			/*if(substr($customer_id,0,2)=='14')
			{
				$cust_type='13';
			}*/
			//End

			$sqlcusttype="SELECT description FROM pcustomergroup WHERE customer_grp='".$cust_type."'";
			$rscusttype=mysql_query($sqlcusttype);
			$rowcusttype=mysql_fetch_array($rscusttype);
			$cust_type_fetched=$rowcusttype['description'];
			if($cust_type_fetched=='Sub-Dealer') $cust_type_fetched='Sub Dealer';
			if($cust_type_fetched=='Royal Star Dealer') $cust_type_fetched='Dealer';
			if($cust_type_fetched=='Super Stockist') $cust_type_fetched='Dealer';
			
			if($cust_type!='07' && $cust_type!='08' && $cust_type!='09' && $cust_type!='21' && $cust_type!='22')
			{
				$sqlPARTY="SELECT PARTY FROM pZSDCUST WHERE SOLD_PARTY='".$customer_id."' AND PARTY!='".$customer_id."'";
				$rsPARTY=mysql_query($sqlPARTY);
				$rowPARTY=mysql_fetch_array($rsPARTY);
				$rds_tag=$rowPARTY['PARTY'];
			}
			else if($cust_type=='21' || $cust_type=='22')
			{
				$rds_tag=$mapped_dealer_ship_to_party_dealer;
			}
			else $rds_tag='';
			
			//For Ship to Party Branch
			if(substr($customer_id,0,2)=='14')
			{
				$sqlshiptobranch="SELECT VKGRP FROM ptblcustomermaster WHERE  KUNNR='".$rds_tag."' ORDER BY AEDAT ASC,ADDITIONAL_DATA1 ASC";
				$rsshiptobranch=mysql_query($sqlshiptobranch);
				$rowshiptobranch=mysql_fetch_array($rsshiptobranch);
				$branch_code=$rowshiptobranch['VKGRP'];
			}
			//End ship to branch
			$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$branch_code."'";
			$rsbranchcode=mysql_query($sqlbranchcode);
			$rowbranchcode=mysql_fetch_array($rsbranchcode);
			$branch_code_update=$rowbranchcode['branch_code'];
			
			if($rds_tag !='')
			{
			$sqlrdscode="SELECT customer_code FROM customer_master WHERE customer_id='".addslashes($rds_tag)."'";
			$rsrdscode=mysql_query($sqlrdscode);
			$rowrdscode=mysql_fetch_array($rsrdscode);
			$rds_code=$rowrdscode['customer_code'];
			}
			else $rds_code='';
		
			if($dns_route_code!='' && $route_name!='')
			{
				$sqlroutechk="SELECT * FROM route_master WHERE dns_route_code='".addslashes($dns_route_code)."'";
				$rsroutechk=mysql_query($sqlroutechk);
				$countroutechk=mysql_num_rows($rsroutechk);
				if($countroutechk<1 && $route_name!='')
				{
					$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM 
									route_master WHERE route_code NOT LIKE '%N%'";
					$rsmaxroutecode=mysql_query($sqlmaxroutecode);
					$rowmaxroutecode=mysql_fetch_array($rsmaxroutecode);
					$new_route_code=$rowmaxroutecode['new_route_code'];
					
					if($new_route_code=='')
					{
						$max_route_code='RT/1';
					}
					else
					{
						$max_route_code='RT/'.($new_route_code+1);
					}
					$sqlroute  = "insert into route_master ";
					$sqlroute .= " SET route_code='".$max_route_code."'";
					$sqlroute .= " ,dns_route_code='".$dns_route_code."'";
					$sqlroute .= " ,route_name='".$route_name."'";
					$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
					mysql_query($sqlroute) or  array_push($error_array,"mysql_error().
									Internal DATA execution problem on route table.PLease contact aceDNS admin.");				
					//modifyempdatadownloadlog($emp_code,strtoupper($folderName));
					$route_code=$max_route_code;
				}
				else
				{
					$rowroutechk=mysql_fetch_array($rsroutechk);
					$route_code=$rowroutechk['route_code'];
					$route_name_db=$rowroutechk['route_name'];
					if($route_name_db !=$route_name)
					{
						$sqlupdateroue="UPDATE route_master SET route_name='".$route_name."',download_time=CURRENT_TIMESTAMP() 
										WHERE route_code='".$route_code."'";
						mysql_query($sqlupdateroue) or  array_push($error_array,"mysql_error().
									Internal DATA execution problem on route table.PLease contact aceDNS admin.");
					}
				}
			}
			else $route_code='';
			$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_id='".addslashes($customer_id)."'";
			$rscustomernamechk=mysql_query($sqlcustomernamechk);
			$countcustomernamechk=mysql_num_rows($rscustomernamechk);
			$csv_row_count=$rec_count+1;
			if($countcustomernamechk<1)
			{
				$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE 'N%'";
				$rsmaxcustomercode=mysql_query($sqlmaxcustomercode);
				$rowmaxcustomercode=mysql_fetch_array($rsmaxcustomercode);
				$max_customer_code=$rowmaxcustomercode['max_customer_code'];
				
				if($max_customer_code=='')
				{
					$max_customer_code='C/0000001';
				}
				else
				{
					$max_customer_code++;
				}
				$sql  = "insert into customer_master ";
				$sql .= " SET customer_code='".$max_customer_code."'";
				$sql .= " , dns_customer_code='".$dns_customer_code."'";
				$sql .= " , customer_id='".$customer_id."'";
				$sql .= " , customer_name='".addslashes($customer_name)."'";
				$sql .= " , branch_code='".addslashes($branch_code_update)."'";
				$sql .= " , phone_no='".$phone_no."'";
				//$sql .= " , route_code='".$route_code."'";
				$sql .= " , acedns='".$acedns."'";
				$sql .= " , black_list='N'";
				$sql .= " , cust_type='".addslashes($cust_type_fetched)."'";
				$sql .= " , pin='".$pin."'";
				$sql .= " , email='".addslashes($email)."'";
				$sql .= " , credit_limit='".addslashes($credit_limit)."'";
				$sql .= " , PAN='".addslashes($pan)."'";
				$sql .= " , minimum_stock='".$minimum_stock."'";
				$sql .= " , district='".$district."'";
				$sql .= " , bank_account_number='".$bank_account_number."'";
				$sql .= " , monthly_potential='".$monthly_potential."'";
				$sql .= " , zone='".$zone."'";
				$sql .= " , rds_tag='".$rds_code."'";
				$sql .= " , address='".addslashes($address)."'";
				$sql .= " , region='".addslashes($region)."'";
				echo $sql .= " , download_time=CURRENT_TIMESTAMP()";
				mysql_query($sql) or  "mysql_error().Duplicate key @row $csv_row_count on Customer name and Employee columns in customer master.csv.Please check.";
			   //modifyempdatadownloadlog($emp_code,strtoupper($folderName));
			   $customer_code=$max_customer_code;
			   //$starsathi_operation='INSERT';
			 }
			else
			{
				$rowcustomernamechk=mysql_fetch_array($rscustomernamechk);
				$customer_code_db=$rowcustomernamechk['customer_code'];
				$route_code_db=$rowcustomernamechk['route_code'];
				$phone_no_db=$rowcustomernamechk['phone_no'];
				$acedns_db=$rowcustomernamechk['acedns'];
				$black_list_db=$rowcustomernamechk['black_list'];
				$customer_type_db=$rowcustomernamechk['cust_type'];
				$branch_code_db=$rowcustomernamechk['branch_code'];
				$customer_name_db=$rowcustomernamechk['customer_name'];
				$customer_id_db=$rowcustomernamechk['customer_id'];
				$dns_customer_code_db=$rowcustomernamechk['dns_customer_code'];
				$address_db=$rowcustomernamechk['address'];
				$rds_tag_db=$rowcustomernamechk['rds_tag'];
				$whatsapp_no_db=$rowcustomernamechk['whatsapp_no'];
				$customer_type_db=$rowcustomernamechk['cust_type'];
				$pin_db=$rowcustomernamechk['pin'];
				$email_db=$rowcustomernamechk['email'];
				$credit_limit_db=$rowcustomernamechk['credit_limit'];
				$pan_db=$rowcustomernamechk['PAN'];
				$minimum_stock_db=$rowcustomernamechk['minimum_stock'];
				$district_db=$rowcustomernamechk['district'];
				$bank_account_number_db=$rowcustomernamechk['bank_account_number'];
				$monthly_potential_db=$rowcustomernamechk['monthly_potential'];
				$zone_db=$rowcustomernamechk['zone'];
				$region_db=$rowcustomernamechk['region'];
				$update_condition=" customer_id='".addslashes($customer_id)."'";
				if($route_code_db!=$route_code || $customer_type_db!=$cust_type 
				|| $branch_code_db!=$branch_code_update || $customer_name_db!=$customer_name || $dns_customer_code_db!=$dns_customer_code || $phone_no_db!=$phone_no 
				|| $address_db!=$address || $whatsapp_no_db!=$whatsapp_no || $rds_tag_db!=$rds_code || $acedns_db!=$acedns || $pin_db!=$pin || $email_db!=$email || $credit_limit_db!=$credit_limit 
				|| $customer_id_db!=$customer_id || $customer_type_db!=$cust_type_fetched || $pan_db!=$pan || $minimum_stock_db!=$minimum_stock || $district_db!=$district
				|| $bank_account_number_db!=$bank_account_number || $monthly_potential_db!=$monthly_potential || $zone_db!=$zone || $region_db!=$region) 
				{
					$sqlupdated  = "update customer_master SET";
					//$sqlupdated .= " SET route_code='".$route_code."'";
					$sqlupdated .= " customer_name='".addslashes($customer_name)."'";
					$sqlupdated .= " , dns_customer_code='".addslashes($dns_customer_code)."'";
					$sqlupdated .= " , branch_code='".addslashes($branch_code_update)."'";
					$sqlupdated .= " , cust_type='".addslashes($cust_type_fetched)."'";
					$sqlupdated .= " , phone_no='".$phone_no."'";
					$sqlupdated .= " , district='".$district."'";
					$sqlupdated .= " , bank_account_number='".$bank_account_number."'";
					$sqlupdated .= " , monthly_potential='".$monthly_potential."'";
					$sqlupdated .= " , zone='".$zone."'";
					$sqlupdated .= " , rds_tag='".$rds_code."'";
					$sqlupdated .= " , region='".addslashes($region)."'";
					$sqlupdated .= "  , address='".addslashes($address)."'
									  , pin='".addslashes($pin)."'
									  , credit_limit='".addslashes($credit_limit)."'
									  , email='".addslashes($email)."'
									  , PAN='".addslashes($pan)."'
									  , minimum_stock='".addslashes($minimum_stock)."'
									  , acedns='".$acedns."'
									  , download_time=CURRENT_TIMESTAMP() 
									 WHERE  ".$update_condition."";
					mysql_query($sqlupdated) or ".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.";
					//modifyempdatadownloadlog($emp_code,strtoupper($folderName));
				}
				$customer_code=$customer_code_db;
			}
			$sqlfetchdestination="SELECT destination_code FROM destination_master WHERE dns_destination_code='".$destination."'";
			$rsfetchdestination=mysql_query($sqlfetchdestination);
			$cntfetchdestination=mysql_num_rows($rsfetchdestination);
			if($cntfetchdestination==0)
			{
				$sqlmaxdestinationcode="SELECT MAX(destination_code) AS max_destination_code FROM  destination_master WHERE 1";
				$rsmaxdestinationcode=mysql_query($sqlmaxdestinationcode);
				$rowmaxdestinationcode=mysql_fetch_array($rsmaxdestinationcode);
				$max_destination_code=$rowmaxdestinationcode['max_destination_code'];

					if($max_destination_code=='')
					{
						$max_destination_code='D0001';
					}
					else
					{
						$max_destination_code++;
					}
				$sqldestination  = "insert into destination_master ";
				$sqldestination .= " SET destination_code='".$max_destination_code."'";
				$sqldestination .= " , dns_destination_code='".$destination."'";
				$sqldestination .= " , destination_name='".mysql_real_escape_string($destination_name)."'";
				$sqldestination .= " , ex_for_type='FOR'";
				$sqldestination .= " , download_time=CURRENT_TIMESTAMP()";
				mysql_query($sqldestination) or ".Internel error occurrs in destination update.Please check.";
				$destination_code=$max_destination_code;
			}
			else
			{
				$rowfetchdestination=mysql_fetch_array($rsfetchdestination);
				$destination_code=$rowfetchdestination['destination_code'];
			}
			if($destination_code!='')
			{
				/*$sqlchkdestination="SELECT destination_code FROM customer_destination WHERE customer_code='".$customer_code."' AND 
									destination_code='".$destination_code."'";*/
				$sqlchkdestination="SELECT destination_code FROM customer_destination WHERE customer_code='".$customer_code."'";					
				$rschkdestination=mysql_query($sqlchkdestination);
				$cntchkdestination=mysql_num_rows($rschkdestination);
				if($cntchkdestination==0)
				{
					$sqlcustdestination  = "insert into customer_destination ";
					$sqlcustdestination .= " SET customer_code='".$customer_code."'";
					$sqlcustdestination .= " , destination_code='".$destination_code."'";
					$sqlcustdestination .= " , acedns='Y'";
					$sqlcustdestination .= " , download_time=CURRENT_TIMESTAMP()";
					mysql_query($sqlcustdestination) or ".Internel error occurrs in destination update.Please check.";
				}
				else
				{
					$sqlcustdestination  = "UPDATE customer_destination ";
					$sqlcustdestination .= " SET destination_code='".$destination_code."'";
					$sqlcustdestination .= " , acedns='Y'";
					$sqlcustdestination .= " , download_time=CURRENT_TIMESTAMP() WHERE  customer_code='".$customer_code."'";
					mysql_query($sqlcustdestination) or ".Internel error occurrs in destination update.Please check.";
				}
			}
		}
		$res_msg = array("process_sts"=>"YES","process_msg"=>"Successful.");
	}
	
	else {
		$res_msg = array("process_sts"=>"NO","process_msg"=>"SOMETHING WENT WRONG.");
	}
	 echo json_encode($res_msg);

	mysql_close();
?>

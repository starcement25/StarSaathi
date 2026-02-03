<?php
set_time_limit(1000);
error_reporting(E_ALL ^ E_NOTICE);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

function similar_file_exists($filename) {
  if (file_exists($filename)) {
	return $filename;
  }
  $dir = dirname($filename);
  $files = glob($dir . '/*');
  $lcaseFilename = strtolower($filename);
  foreach($files as $file) {
	if (strtolower($file) == $lcaseFilename) {
	  return $file;
	}
  }
  return false;
}
function return_auto_code($code_prefix,$code_type,$code){
	
	if($code_type=='employee')
	{
		if(strlen($code)=='1')
		{
			$build_code=$code_prefix.'000'.$code;
		}
		if(strlen($code)=='2')
		{
			$build_code=$code_prefix.'00'.$code;
		}
		if(strlen($code)=='3')
		{
			$build_code=$code_prefix.'0'.$code;
		}
	}
	return $build_code;
}

	$folderName="$nick_name";
	$error_array=array();
	//For Branch Master CSV
	if(similar_file_exists("csv/$folderName/Branch master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/Branch master.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
		$lines = file($filename);
		/*$sqldelete="truncate branch_master";
		$rsdelete=mysql_query($sqldelete);*/
		foreach($lines as $line)
		{
			$i = 0;
			$char = substr($line, $i, 1);
			$value ="";
			$data="";
			$double_coute_found = false;
			if($rec_count>=1)
			{ 
				while($char!="")
				{
					if($double_coute_found && $char=="\"")
					{
						$double_coute_found = false;
						$i++;
						$char = substr($line, $i, 1);
						continue;
					}
			
					if(!$double_coute_found && $char=="\"")
					{  
					
						$double_coute_found = true;
						$i++;
						$char = substr($line, $i, 1);
						continue;
					}
				
					if($char=="," && !$double_coute_found)
					{
						$data[]=$value;
						$value = "";
					}
					else 
					{
					$value .= $char;
					}
					$i++;
					$char = substr($line, $i, 1);
				} //end of while
			   $data[]=$value;
			  //print_r($data);
			  
				$dns_branch_code=trim($data[0]);
				$branch_name=trim($data[1]);
				$branch_location=trim($data[2]);
				$comp_code=trim($data[3]);
				$branch_email_id=trim($data[5]);
				$branch_accounts_email_id=trim($data[6]);
				$alternative_email_id=trim($data[7]);
				$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_name)."' 
									AND branch_location='".$branch_location."'";
				$rsbranchnamechk=mysql_query($sqlbranchnamechk);
				$countbranchnamechk=mysql_num_rows($rsbranchnamechk);
				
				$csv_row_count=$rec_count+1;
				if($countbranchnamechk<1)
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
					$sqlbranch .= " , branch_location='".mysql_real_escape_string($branch_location)."'";
					$sqlbranch .= " , comp_code='".mysql_real_escape_string($comp_code)."'";
					$sqlbranch .= " , branch_email_id='".mysql_real_escape_string($branch_email_id)."'";
					$sqlbranch .= " , alternative_email_id='".mysql_real_escape_string($alternative_email_id)."'";
				}
				else
				{
					$rowbranchnamechk=mysql_fetch_array($rsbranchnamechk);

					$sqlbranch  = "UPDATE branch_master SET ";
					$sqlbranch .= "  	dns_branch_code='".mysql_real_escape_string($dns_branch_code)."'";
					$sqlbranch .= " , branch_location='".mysql_real_escape_string($branch_location)."'";
					$sqlbranch .= " , comp_code='".mysql_real_escape_string($comp_code)."'";
					$sqlbranch .= " , branch_email_id='".mysql_real_escape_string($branch_email_id)."'";
					$sqlbranch .= " , alternative_email_id='".mysql_real_escape_string($alternative_email_id)."' WHERE branch_name='".addslashes($branch_name)."'";
				}
				mysql_query($sqlbranch) or array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count in Branch master.csv.Please check.");
			}
			 $rec_count++;
		}		
		$successval=1;
	}
	/*else
	{
		echo $successval="Naming convention for Branch master.csv is wrong.";
		exit();
	}*/

		if(similar_file_exists("csv/$folderName/ProductMaster.csv")!=false)
		{
			$filename=similar_file_exists("csv/$folderName/ProductMaster.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			$lines = file($filename);
			$duplicate_product=array();
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{ 
					while($char!="")
					{
						
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
				
						if(!$double_coute_found && $char=="\"")
						{  
						
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
					
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
					 $data[]=$value;
					//print_r($data);
				  
					$prod_desc=trim($data[0]);
					$product_group_code_name=trim($data[1]);
					$product_sub_group_code_name=trim($data[2]);
					//$product_brand_code_name=trim($data[5]);
					//For SELVEL
					/*if($product_brand_code_name!=''){
					$prod_desc=$prod_desc.'-'.$product_brand_code_name;
					}
					$product_brand_code_name='';*/
					//End For SELVEL
					$cl_stk=trim($data[4]);
					if(strpos($cl_stk,',')!=false){
						$stkpos=strpos($cl_stk,',');
					$cl_stk = substr($cl_stk,0,$stkpos).substr(strstr($cl_stk, ","),1);
					}
					$acedns='Y';
					$black_list='N';
					$csv_row_count=$rec_count+1;
						if(no_of_filter > 1){
						//Product group code checking start
						$sqlprodgroupnamechk="SELECT product_group_code FROM product_group_master WHERE product_group_name='".addslashes($product_group_code_name)."'";
						$rsprodgroupnamechk=mysql_query($sqlprodgroupnamechk);
						$countprodgroupnamechk=mysql_num_rows($rsprodgroupnamechk);
						if($countprodgroupnamechk<1){
							$sqlmaxproductgroupcode="SELECT MAX( CAST( SUBSTRING( product_group_code, -(length( product_group_code ) -2), length( product_group_code ) -2 ) AS UNSIGNED ) ) AS max_product_group_code from product_group_master";
							$rsmaxproductgroupcode=mysql_query($sqlmaxproductgroupcode);
							$rowmaxproductgroupcode=mysql_fetch_array($rsmaxproductgroupcode);
							$max_product_group_code=$rowmaxproductgroupcode['max_product_group_code'];
							
							if($max_product_group_code=='')
							{
								$max_product_group_code='1';
							}
							else
							{
								$max_product_group_code++;
							}
							$max_product_group_code='BR'.$max_product_group_code;
							$sqlbrand  = "INSERT INTO product_group_master SET ";
							$sqlbrand .= "  product_group_code='".$max_product_group_code."'";
							$sqlbrand .= " , product_group_name='".addslashes($product_group_code_name)."'";
							$sqlbrand .= " , vertical_value=''";
							$sqlbrand .= " , download_time=CURRENT_TIMESTAMP()";
							mysql_query($sqlbrand) or array_push($error_array,"mysql_error().Internal error occurrs in product_group_name column @row $csv_row_count in sku master.csv.Please check.");
							$product_group_code=$max_product_group_code;
						}
						else
						{
							$rowprodgroupnamechk=mysql_fetch_array($rsprodgroupnamechk);
							$product_group_code=$rowprodgroupnamechk['product_group_code'];
							$vertical_value_db=$rowprodgroupnamechk['vertical_value'];
							if($vertical_value_db!=$vertical_value)
							{
								$sqlupdatebrand  = "UPDATE product_group_master SET ";
								$sqlupdatebrand .= " vertical_value=''";
								$sqlupdatebrand .= " , download_time=CURRENT_TIMESTAMP() WHERE product_group_name='".addslashes($product_group_code_name)."'";
								mysql_query($sqlupdatebrand) or array_push($error_array,"mysql_error().Internal error occurrs in product_group_name column @row $csv_row_count in sku master.csv.Please check.");
							}
						}
						//Product group code checking end
					 }
					if(no_of_filter > 2){
						//Product sub group code checking start
						$sqlprodsubgroupnamechk="SELECT product_sub_group_code FROM product_sub_group_master WHERE product_sub_group_name='".addslashes($product_sub_group_code_name)."' 
												AND product_group_code='".$product_group_code."'";
						$rsprodsubgroupnamechk=mysql_query($sqlprodsubgroupnamechk);
						$countprodsubgroupnamechk=mysql_num_rows($rsprodsubgroupnamechk);
						if($countprodsubgroupnamechk<1){
							$sqlmaxproductsubgroupcode="SELECT MAX( CAST( SUBSTRING( product_sub_group_code, -(length( product_sub_group_code ) -2), length( product_sub_group_code ) -2 ) AS UNSIGNED ) ) AS max_product_sub_group_code from product_sub_group_master";
							$rsmaxproductsubgroupcode=mysql_query($sqlmaxproductsubgroupcode);
							$rowmaxproductsubgroupcode=mysql_fetch_array($rsmaxproductsubgroupcode);
							$max_product_sub_group_code=$rowmaxproductsubgroupcode['max_product_sub_group_code'];
							
							if($max_product_sub_group_code=='')
							{
								$max_product_sub_group_code='1';
							}
							else
							{
								$max_product_sub_group_code++;
							}
							$max_product_sub_group_code='BF'.$max_product_sub_group_code;
							$sqlbrandform  = "INSERT INTO product_sub_group_master SET ";
							$sqlbrandform .= "  product_sub_group_code='".mysql_real_escape_string($max_product_sub_group_code)."'";
							$sqlbrandform .= " , product_sub_group_name='".addslashes($product_sub_group_code_name)."'";
							$sqlbrandform .= " , product_group_code='".mysql_real_escape_string($product_group_code)."'";
							$sqlbrandform .= " , vertical_value=''";
							$sqlbrandform .= " , download_time=CURRENT_TIMESTAMP()";
							mysql_query($sqlbrandform);
							$product_sub_group_code=$max_product_sub_group_code;
						}
						else
						{
							$rowprodsubgroupnamechk=mysql_fetch_array($rsprodsubgroupnamechk);
							$product_sub_group_code=$rowprodsubgroupnamechk['product_sub_group_code'];
							$vertical_value_sub_group=$rowprodsubgroupnamechk['vertical_value'];
							if($vertical_value_sub_group!=$vertical_value)
							{
								$sqlupdatebrandform  = "UPDATE product_sub_group_master SET ";
								$sqlupdatebrandform .= " vertical_value=''";
								$sqlupdatebrandform .= " , download_time=CURRENT_TIMESTAMP() WHERE 
														product_sub_group_name='".addslashes($product_sub_group_code_name)." AND product_group_code='".$product_group_code."'";
								mysql_query($sqlupdatebrandform);
							}
						}
						//Product sub group code checking end
					}
					/*if(no_of_filter > 3){
						//Product brand code checking start
						$sqlprodbrandnamechk="SELECT product_brand_code FROM product_brand_master WHERE product_brand_name='".addslashes($product_brand_code_name)."'
												AND product_sub_group_code='".$product_sub_group_code."' AND product_group_code='".$product_group_code."'";
						$rsprodbrandnamechk=mysql_query($sqlprodbrandnamechk);
						$countprodbrandnamechk=mysql_num_rows($rsprodbrandnamechk);
						if($countprodbrandnamechk<1){
							$sqlmaxproductbrandcode="SELECT MAX( CAST( SUBSTRING( product_brand_code, -(length( product_brand_code ) -2), length( product_brand_code ) -2 ) AS UNSIGNED ) ) AS max_product_brand_code from product_brand_master";
							$rsmaxproductbrandcode=mysql_query($sqlmaxproductbrandcode);
							$rowmaxproductbrandcode=mysql_fetch_array($rsmaxproductbrandcode);
							$max_product_brand_code=$rowmaxproductbrandcode['max_product_brand_code'];
							
							if($max_product_brand_code=='')
							{
								$max_product_brand_code='1';
							}
							else
							{
								$max_product_brand_code++;
							}
							$max_product_brand_code='BS'.$max_product_brand_code;
							$sqlbrandsubform  = "INSERT INTO product_brand_master SET ";
							$sqlbrandsubform .= "  product_brand_code='".mysql_real_escape_string($max_product_brand_code)."'";
							$sqlbrandsubform .= " , product_sub_group_code='".mysql_real_escape_string($product_sub_group_code)."'";
							$sqlbrandsubform .= " , product_group_code='".mysql_real_escape_string($product_group_code)."'";
							$sqlbrandsubform .= " , product_brand_name='".addslashes($product_brand_code_name)."'";
							$sqlbrandsubform .= " , vertical_value='".addslashes($vertical_value)."'";
							$sqlbrandsubform .= " , download_time=CURRENT_TIMESTAMP()";
							mysql_query($sqlbrandsubform);
							$product_brand_code=$max_product_brand_code;
						}
						else
						{
							$rowprodbrandnamechk=mysql_fetch_array($rsprodbrandnamechk);
							$product_brand_code=$rowprodbrandnamechk['product_brand_code'];
							$vertical_value_brand=$rowprodbrandnamechk['vertical_value'];
							if($vertical_value_brand!=$vertical_value)
							{
								$sqlupdatebrandsubform  = "UPDATE product_brand_master SET ";
								$sqlupdatebrandsubform .= " vertical_value='".addslashes($vertical_value)."'";
								$sqlupdatebrandsubform .= " , download_time=CURRENT_TIMESTAMP() 
															WHERE product_brand_name='".addslashes($product_brand_code_name)." 
															AND product_sub_group_code='".$product_sub_group_code."' AND product_group_code='".$product_group_code."'";
								mysql_query($sqlupdatebrandsubform);
							}
						}
						//Product brand code checking end
					}*/
						$sqlskunamechk="SELECT * FROM product_master WHERE prod_desc='".addslashes($prod_desc)."' AND product_group_code='".$product_group_code."' AND product_sub_group_code='".$product_sub_group_code."' 
										AND product_brand_code=''";
						$rsskunamechk=mysql_query($sqlskunamechk);
						$countskunamechk=@mysql_num_rows($rsskunamechk);
						$rowskunamechk=@mysql_fetch_array($rsskunamechk);
						
						if($countskunamechk<1)
						{
							$sqlmaxskucode="SELECT MAX(prod_code) AS max_prod_code FROM  product_master WHERE 1";
							$rsmaxskucode=mysql_query($sqlmaxskucode);
							$rowmaxskucode=mysql_fetch_array($rsmaxskucode);
							$max_prod_code=$rowmaxskucode['max_prod_code'];
							
							if($max_prod_code=='')
							{
								$max_prod_code='12001';
							}
							else
							{
								$max_prod_code++;
							}
							$sql  = "insert into product_master ";
							$sql .= " SET prod_code='".$max_prod_code."'";
							$sql .= " , prod_desc='".addslashes($prod_desc)."'";
							$sql .= " , product_group_code='".mysql_real_escape_string($product_group_code)."'";
							$sql .= " , product_sub_group_code='".mysql_real_escape_string($product_sub_group_code)."'";
							$sql .= " , product_brand_code=''";
							$sql .= " , cl_stk='".mysql_real_escape_string($cl_stk)."'";
							$sql .= " , acedns='".$acedns."'";
							$sql .= " , black_list='".$black_list."'";
							$sql .= " , vertical_value=''";
							$sql .= " , download_time=CURRENT_TIMESTAMP()";
							$sql .= " ,	download_time_cl_stk=CURRENT_TIMESTAMP()";
							mysql_query($sql) or array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count on Sku code column in Sku master.csv.Please check.");
						}
						else
						{
							$cl_stk_db=$rowskunamechk['cl_stk'];
							$acedns_db=$rowskunamechk['acedns'];
							$black_list_db=$rowskunamechk['black_list'];
							$prod_code_db=$rowskunamechk['prod_code'];
							$product_group_code_db=$rowskunamechk['product_group_code'];
							$product_sub_group_code_db=$rowskunamechk['product_sub_group_code'];
							
							if(($cl_stk_db==$cl_stk) && ($acedns_db!=$acedns || $black_list_db!=$black_list 
								|| $product_group_code_db!=$product_group_code || $product_sub_group_code_db!=$product_sub_group_code ))
							{
								$sql  = "UPDATE product_master ";
								$sql .= " SET prod_desc='".addslashes($prod_desc)."'";
								$sql .= " , product_group_code='".mysql_real_escape_string($product_group_code)."'";
								$sql .= " , product_sub_group_code='".mysql_real_escape_string($product_sub_group_code)."'";
								$sql .= " , product_brand_code=''";
								$sql .= " , acedns='".$acedns."'";
								$sql .= " , black_list='".$black_list."'";
								$sql .= " , download_time=CURRENT_TIMESTAMP()";
								$sql .= " , vertical_value='' WHERE prod_code='".$prod_code_db."'";
								mysql_query($sql) or array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count on Sku code column in sku master.csv.Please check.");
							}
							else if($cl_stk_db!=$cl_stk)
							{
								$sql  = "UPDATE product_master ";
								$sql .= " SET cl_stk='".$cl_stk."',download_time_cl_stk=CURRENT_TIMESTAMP() WHERE prod_code='".$prod_code_db."'";
								mysql_query($sql) or array_push($error_array,"mysql_error().Internal error @row $csv_row_count on Sku code column in sku master.csv.Please check.");
	
							}
						}
					//For TT
					//array_push($duplicate_product,$dns_prod_code." \t".$prod_desc." \t".$product_group_code." \t".$product_sub_group_code);

				}
		$rec_count++;
		}
		//Product group code checking start
			if(no_of_filter==2 || no_of_filter==3){
				$sqlgroupcodeproduct="SELECT product_group_code FROM product_master WHERE product_group_code NOT IN
									(SELECT product_group_code FROM product_group_master) GROUP BY product_group_code";
				$rsgroupcodeproduct=mysql_query($sqlgroupcodeproduct);
				$cntgroupcodeproduct=mysql_num_rows($rsgroupcodeproduct);
				if($cntgroupcodeproduct>0)
				{
					$groupcodeproduct='';
					while($rowgroupcodeproduct=mysql_fetch_array($rsgroupcodeproduct))
					{
						$groupcodeproduct=$groupcodeproduct.$rowgroupcodeproduct['product_group_code'].',';
					}
					$groupcodeproduct=substr($groupcodeproduct,0,-1);
					$errorgroupcodeproduct=$groupcodeproduct.' exists in Sku master but not exists in Brand Master.';
					array_push($error_array,$errorgroupcodeproduct);
				}
			}
		//Product group code checking end
		//Product sub group code checking start
			if(no_of_filter==3){
			$sqlsubgroupcodeproduct="SELECT product_sub_group_code FROM product_master WHERE product_sub_group_code NOT IN
			(SELECT product_sub_group_code FROM product_sub_group_master) GROUP BY product_sub_group_code";
			$rssubgroupcodeproduct=mysql_query($sqlsubgroupcodeproduct);
			$cntsubgroupcodeproduct=mysql_num_rows($rssubgroupcodeproduct);
			if($cntsubgroupcodeproduct>0)
			{
				$subgroupcodeproduct='';
				while($rowsubgroupcodeproduct=mysql_fetch_array($rssubgroupcodeproduct))
				{
					$subgroupcodeproduct=$subgroupcodeproduct.$rowsubgroupcodeproduct['product_sub_group_code'].',';
				}
				$subgroupcodeproduct=substr($subgroupcodeproduct,0,-1);
				$errorsubgroupcodeproduct=$subgroupcodeproduct.' exists in Sku master but not exists in Brand Form Master.';
				array_push($error_array,$errorsubgroupcodeproduct);
			}
		}
		//Product sub group code checking end
		/*foreach($duplicate_product as $duplicate_product_val)
		{
			$dupliacateproductval=$dupliacateproductval.$duplicate_product_val."\n";
		}
		//print_r($customeroutstandingmissmatchArr);
				$data = str_replace("\r","",$dupliacateproductval);
				
				header("Content-type: application/x-msdownload"); 
				header("Content-Disposition: attachment; filename=duplicateproduct.xls"); 
				header("Pragma: no-cache"); 
				header("Expires: 0"); 
				print "$data";*/
			$successval=1;
	}
	/*else
	{
		echo $successval="Naming convention for SKU Master.csv is wrong.";
		exit();
	}*/
	//exit();	
	
	//For Employee CSV
	if(similar_file_exists("csv/$folderName/employee_master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/employee_master.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
			$lines = file($filename);
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				
				if($rec_count>=1)
				{ 
					$reporting_to_val='';
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
				
						if(!$double_coute_found && $char=="\"")
						{  
						
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
					
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				  $data[]=$value;
				  
					
					$employee_code=trim($data[0]);
					$dns_employee_code=trim($data[1]);
					$employee_name=trim($data[2]);
					$branch_code_name=trim($data[4]);
					$reporting_to=trim($data[5]);
					$vertical_value=trim($data[6]);
					$email=trim($data[7]);
					$phone_no=trim($data[8]);
					$HQ=trim($data[9]);
					$sale_access=trim($data[10]);
					$designation=trim($data[11]);
					
					if(providing_code=='yes'){
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$branch_code_name."'";
						$sqlreportingto="SELECT emp_code FROM employee_master WHERE FIND_IN_SET(dns_emp_code,'".$reporting_to."')";
					}
					else
					{
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_code_name)."'";
						$sqlreportingto="SELECT emp_code FROM employee_master WHERE FIND_IN_SET(emp_name,'".$reporting_to."')";
					}
					$rsbranchcode=mysql_query($sqlbranchcode);
					$rowbranchcode=mysql_fetch_array($rsbranchcode);
					$branch_code=$rowbranchcode['branch_code'];

					$sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($employee_name)."'";
					$rsempnamechk=mysql_query($sqlempnamechk);
					$countempnamechk=mysql_num_rows($rsempnamechk);
					
					$rsreportingto=mysql_query($sqlreportingto);
					
					while($rowreportingto=mysql_fetch_array($rsreportingto))
					{
						$reporting_to_val=$reporting_to_val.$rowreportingto['emp_code'].',';
					}
					$reporting_to_val=substr($reporting_to_val,0,-1);
					//exit();
					
					$csv_row_count=$rec_count+1;
					if($countempnamechk<1)
					{
						$sqlmaxempcode="SELECT MAX(emp_code) AS max_emp_code FROM  employee_master ";
						$rsmaxempcode=mysql_query($sqlmaxempcode);
						$rowmaxempcode=mysql_fetch_array($rsmaxempcode);
						$max_emp_code=$rowmaxempcode['max_emp_code'];
						if($max_emp_code=='')
						{
							$max_emp_code='E0001';
						}
						else
						{
							$max_emp_code++;
						}
						
						$sql  = "insert into employee_master ";
						$sql .= " SET emp_code='".$max_emp_code."'";
						$sql .= " , dns_emp_code='".$dns_employee_code."'";
						$sql .= " , emp_name='".$employee_name."'";
						$sql .= " , branch_code='".$branch_code."'";
						$sql .= " , vertical_value='".$vertical_value."'";
						$sql .= " , reporting_to='".$reporting_to_val."'";
						$sql .= " , email='".$email."'";
						$sql .= " , phone_no='".$phone_no."'";
						$sql .= " , sale_access='".$sale_access."'";
						$sql .= " , HQ='".$HQ."'";
						$sql .= " , designation='".$designation."'";
						mysql_query($sql) or  array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count on Employee code column in Employee Master.csv.Please check.");
						
						$sqlcp  = "insert into changepassword ";
						$sqlcp .= " SET emp_code='".$max_emp_code."'";
						$sqlcp .= " , newpassword='1234'";
						$sqlcp .= " , oldpassword='1234'"; 
						$sqlcp .= " , status='true'";
						$sqlcp .= " , is_licensed='1'"; 
						mysql_query($sqlcp) or  array_push($error_array,"mysql_error().Internal DATA execution problem on password table.PLease contact aceDNS admin.");
					}
					else
					{
						$rowempnamechk=mysql_fetch_array($rsempnamechk);
						$emp_code_db=$rowempnamechk['emp_code'];

						$sqlupdate  = "UPDATE employee_master ";
						$sqlupdate .= " SET branch_code='".$branch_code."'";
						$sqlupdate .= " , vertical_value='".$vertical_value."'";
						$sqlupdate .= " , reporting_to='".$reporting_to_val."'";
						$sqlupdate .= " , email='".$email."'";
						$sqlupdate .= " , sale_access='".$sale_access."'";
						$sqlupdate .= " , HQ='".$HQ."'";
						$sqlupdate .= " , designation='".$designation."'";
						$sqlupdate .= " , phone_no='".$phone_no."' WHERE emp_code='".addslashes($emp_code_db)."'";
						mysql_query($sqlupdate) or  array_push($error_array,"mysql_error().Internel error  @row $csv_row_count on in Employee Master.csv.Please check.");
					}
				}
				 $rec_count++;
			}
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Employee Master.csv is wrong.";
			exit();
		}*/
		
		//For RDS CSV
	if(similar_file_exists("csv/$folderName/RDS MASTER.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/RDS MASTER.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
		$lines = file($filename);
		/*$sqlroutedelete="truncate route_master";
		$rsroutedelete=mysql_query($sqlroutedelete);*/
		
		foreach($lines as $line)
		{
			$i = 0;
			$char = substr($line, $i, 1);
			$value ="";
			$data="";
			$double_coute_found = false;
			if($rec_count>=1)
			{ 
				while($char!="")
				{
					if($double_coute_found && $char=="\"")
					{
						$double_coute_found = false;
						$i++;
						$char = substr($line, $i, 1);
						continue;
					}
			
					if(!$double_coute_found && $char=="\"")
					{  
					
						$double_coute_found = true;
						$i++;
						$char = substr($line, $i, 1);
						continue;
					}
				
					if($char=="," && !$double_coute_found)
					{
						$data[]=$value;
						$value = "";
					}
					else 
					{
					$value .= $char;
					}
					$i++;
					$char = substr($line, $i, 1);
				} //end of while
			   $data[]=$value;
			  //print_r($data);
				$rdscode=trim($data[0]);
				$rdsname	  =trim($data[1]); 
				$emp_code_name =trim($data[2]);
				$rds_type =trim($data[3]);
				
				if(providing_code=='yes'){
					$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name)."'";
					$rsempcode=mysql_query($sqlempcode);
					$rowempcode=mysql_fetch_array($rsempcode);
					$emp_code=$rowempcode['emp_code'];
				}
				else
				{
					$sqlempcode="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name)."'";
					$rsempcode=mysql_query($sqlempcode);
					$rowempcode=mysql_fetch_array($rsempcode);
					$emp_code=$rowempcode['emp_code'];
				}
				$sqlrdsnamechk="SELECT * FROM rds_master WHERE rds_name='".addslashes($rdsname)."' AND emp_code='".$emp_code."'";
				$rsrdsnamechk=mysql_query($sqlrdsnamechk);
				$countrdsnamechk=mysql_num_rows($rsrdsnamechk);
					
				$csv_row_count=$rec_count+1;
				if($countrdsnamechk<1)
				{
					$sqlmaxrdscode="SELECT MAX(rds_code) AS max_rds_code FROM  rds_master WHERE 1";
					$rsmaxrdscode=mysql_query($sqlmaxrdscode);
					$rowmaxrdscode=mysql_fetch_array($rsmaxrdscode);
					$max_rds_code=$rowmaxrdscode['max_rds_code'];
					
					if($max_rds_code=='')
					{
						$max_rds_code='C/0005718';
					}
					else
					{
						$max_rds_code++;
					}

					$sqlrds  = "insert into rds_master ";
					$sqlrds .= " SET rds_code='".$max_rds_code."'";
					$sqlrds .= " ,rds_name='".$rdsname."'";
					$sqlrds .= " , emp_code='".$emp_code."'";
					$sqlrds .= " , rds_type='".$rds_type."'";
					$sqlrds .= " , download_time=CURRENT_TIMESTAMP()";
				
					mysql_query($sqlrds) or array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count on Customer name and Employee name columns in rds master.csv.Please check.");
				}
				else
				{
					$sqlupdated  = "update rds_master ";
					$sqlupdated .= " SET rds_type='".$rds_type."'";
					$sqlupdated .= " WHERE rds_name='".addslashes($rdsname)."' AND emp_code='".$emp_code."'";
					mysql_query($sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on rds master.csv.Please check.");
				}
			}
			 $rec_count++;
		}
		$successval=1;
	}
	//For Customer CSV
	if(similar_file_exists("csv/$folderName/CustomerMaster.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/CustomerMaster.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
			$lines = file($filename);
			$countroute=0;
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{ 
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
				
						if(!$double_coute_found && $char=="\"")
						{  
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				   $data[]=$value;
				  //print_r($data);
					
					$customer_name	=trim($data[0]);
					$route_name	  =trim($data[1]);  
					$emp_code_name		=trim($data[2]);
					//For VIPL employee only
					/*$sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".trim($emp_code)."'";
					$rsempnamechk=mysql_query($sqlempnamechk);
					$rowempnamechk=mysql_fetch_array($rsempnamechk);
					$emp_code=$rowempnamechk['emp_code'];*/
					$acedns		  ='Y';
					$current_balance =trim($data[3]);
					$credit_days	 =trim($data[4]);
					$black_list	  ='N'; 
				
					if(providing_code=='yes'){
						$sqlempcode="SELECT emp_code,branch_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name)."'";
						$rsempcode=mysql_query($sqlempcode);
						$rowempcode=mysql_fetch_array($rsempcode);
						$emp_code=$rowempcode['emp_code'];
						$branch_code=$rowempcode['branch_code'];
					}
					else
					{
						$sqlempcode="SELECT emp_code,branch_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name)."'";
						$rsempcode=mysql_query($sqlempcode);
						$rowempcode=mysql_fetch_array($rsempcode);
						$emp_code=$rowempcode['emp_code'];
						//exit();
						$branch_code=$rowempcode['branch_code'];
					}
					$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."' AND emp_code='".$emp_code."'";
					$rsroutechk=mysql_query($sqlroutechk);
					$countroutechk=mysql_num_rows($rsroutechk);
					if($countroutechk<1 && $route_name!='')
					{
						$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM route_master WHERE route_code NOT LIKE '%N%'";
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
							//$max_route_code++;
						}

						$sqlroute  = "insert into route_master ";
						$sqlroute .= " SET route_code='".$max_route_code."'";
						$sqlroute .= " ,route_name='".$route_name."'";
						$sqlroute .= " , emp_code='".$emp_code."'";
						$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
						mysql_query($sqlroute) or  array_push($error_array,"mysql_error().Internal DATA execution problem on route table.PLease contact aceDNS admin.");
						$route_code=$max_route_code;
					}
					else
					{
						$rowroutechk=mysql_fetch_array($rsroutechk);
						$route_code=$rowroutechk['route_code'];
					}
					//For VIPL ROUTE
					
					/*$sqlroutechk="SELECT * FROM route_master WHERE route_name='".$route_name."' AND emp_code='".$emp_code."'";
					$rsroutechk=mysql_query($sqlroutechk);
					$countroutechk=mysql_num_rows($rsroutechk);
					if($countroutechk<1)
					{
						$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code FROM route_master";
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
							//$max_route_code++;
						}
						
						$sqlroute  = "insert into route_master ";
						$sqlroute .= " SET route_code='".$max_route_code."'";
						$sqlroute .= " ,route_name='".$route_name."'";
						$sqlroute .= " , emp_code='".$emp_code."'";
						$sqlroute .= " , vertical_value='".$vertical_value."'";
					mysql_query($sqlroute) or  array_push($error_array,"mysql_error().Internal DATA execution problem on route table.PLease contact aceDNS admin.");					
					}
					
					$sqlroutecode="SELECT route_code FROM route_master WHERE route_name='".trim($route_name)."' AND emp_code='".$emp_code."'";
					$rsroutecode=mysql_query($sqlroutecode);
					$rowroutecode=mysql_fetch_array($rsroutecode);
					$route_code=$rowroutecode['route_code'];*/

					$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."' AND emp_code='".$emp_code."' AND route_code='".$route_code."'";
					/*$sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."'";*/
					$rscustomernamechk=mysql_query($sqlcustomernamechk);
					$countcustomernamechk=mysql_num_rows($rscustomernamechk);
					
					$csv_row_count=$rec_count+1;
					if($countcustomernamechk<1)
					{
						$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE '%N%'";
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
						$sql .= " , customer_name='".addslashes($customer_name)."'";
						$sql .= " , branch_code='".addslashes($branch_code)."'";
						$sql .= " , route_code='".$route_code."'";
						$sql .= " , emp_code='".$emp_code."'";
						$sql .= " , current_balance	='".$current_balance."'";
						$sql .= " , acedns='".$acedns."'";
						$sql .= " , black_list='".$black_list."'";
						$sql .= " , cust_type='R'";
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
						//exit();
						mysql_query($sql) or array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count on Customer name and Employee columns in customer master.csv.Please check.");
					}
					else
					{
						$rowcustomernamechk=mysql_fetch_array($rscustomernamechk);
						$customer_code_db=$rowcustomernamechk['customer_code'];
						$route_code_db=$rowcustomernamechk['route_code'];
						$emp_code_db=$rowcustomernamechk['emp_code'];
						$current_balance_db=$rowcustomernamechk['current_balance'];
						$credit_limit_db=$rowcustomernamechk['credit_limit'];
						$acedns_db=$rowcustomernamechk['acedns'];
						$black_list_db=$rowcustomernamechk['black_list'];
						$customer_type_db=$rowcustomernamechk['cust_type'];
						
						if($route_code_db!=$route_code || $emp_code_db!=$emp_code || $current_balance_db!=$current_balance )
						{
							$sqlupdated  = "update customer_master ";
							$sqlupdated .= " SET route_code='".$route_code."'";
							$sqlupdated .= " , current_balance	='".$current_balance."'";
							$sqlupdated .= " ,download_time=CURRENT_TIMESTAMP() 
											 WHERE customer_name='".addslashes($customer_name)."' AND emp_code='".$emp_code."' AND route_code='".$route_code."' ";
							mysql_query($sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");
						}
					}
				}
				 $rec_count++;
			}
			//exit();
			//Emp code checking start
				$sqlempcoderetail="SELECT emp_code FROM customer_master WHERE emp_code NOT IN(SELECT emp_code FROM employee_master)";
				$rsempcoderetail=mysql_query($sqlempcoderetail);
				$cntempcoderetail=mysql_num_rows($rsempcoderetail);
				if($cntempcoderetail>0)
				{
					$empcoderetail='';
					while($rowempcoderetail=mysql_fetch_array($rsempcoderetail))
					{
						$empcoderetail=$empcoderetail.$rowempcoderetail['emp_code'].',';
					}
					$empcoderetail=substr($empcoderetail,0,-1);
					$errorempcoderetail=$empcoderetail.' exists in customer_master but not exists in employee_master.';
					array_push($error_array,$errorempcoderetail);
				}
			//Emp code checking end
			//Route code checking start
				$sqlroutecoderetail="SELECT route_code FROM customer_master WHERE route_code NOT IN(SELECT route_code FROM route_master)";
				$rsroutecoderetail=mysql_query($sqlroutecoderetail);
				$cntroutecoderetail=mysql_num_rows($rsroutecoderetail);
				if($cntroutecoderetail>0)
				{
					$routecoderetail='';
					while($rowroutecoderetail=mysql_fetch_array($rsroutecoderetail))
					{
						$routecoderetail=$routecoderetail.$rowroutecoderetail['route_code'].',';
					}
					$routecoderetail=substr($routecoderetail,0,-1);
					$errorroutecoderetail=$routecoderetail.' exists in customer_master but not exists in route_master.';
					array_push($error_array,$errorroutecoderetail);
				}
			//Route code checking end
		
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Customer Master.csv is wrong.";
			exit();
		}*/
		

	//For Outstanding CSV
	if(similar_file_exists("csv/$folderName/OutstandingMaster.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/OutstandingMaster.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
		//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
		
			$lines = file($filename);
			$sqldelete="truncate outstanding";
			$rsdelete=mysql_query($sqldelete);
			$customeroutstandingmissmatchArr=array();
			$line='';
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{ 
					while($char!="")
					{
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if(!$double_coute_found && $char=="\"")
						{  
						
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				  $data[]=$value;
				  //print_r($data);
				  
					$csv_row_count=$rec_count+1;
					$customer_code_name=trim($data[0]);
					if(providing_code=='yes')
					{
						$emp_code_name=trim($data[1]);
						$invoice_id=trim($data[2]);
						$date=trim($data[3]);
						$dateArr=explode('/',$date);
						if(strlen($dateArr[2])==2)
						{
							$year='20'.$dateArr[2];
						}
						else
						{
							$year=$dateArr[2];
						}
						$finaldate=$year.'-'.$dateArr[1].'-'.$dateArr[0];
						$invoice_amount=trim($data[4]);
						if(strpos($invoice_amount,',')!=false){
							//$invoicepos=strpos($invoice_amount,',');
						//$invoice_amount = substr($invoice_amount,0,$invoicepos).substr(strstr($invoice_amount, ","),1);
							$invoice_amount =str_replace(',','',$invoice_amount);
						}
						if(strpos($invoice_amount,' Cr')!=false){
							$invoice_amount='-'.$invoice_amount;
						}
						$due_amount=trim($data[5]);
						if(strpos($due_amount,',')!=false){
						//$due_amount = substr($due_amount,0,strpos($due_amount,',')).substr(strstr($due_amount, ","),1);
						$due_amount =str_replace(',','',$due_amount);
						}
						if(strpos($due_amount,' Cr')!=false){
							$due_amount ='-'.$due_amount;
						}
						$vertical_value	  =trim($data[6]); 

					}
					else
					{
						//$route_code_name=trim($data[1]);
						$invoice_id=trim($data[1]);
						$date=substr($data[2],0,10);
						$dateArr=explode('/',$date);
						if(strlen($dateArr[2])==2)
						{
							$year='20'.$dateArr[2];
						}
						else
						{
							$year=$dateArr[2];
						}
						$finaldate=$year.'-'.$dateArr[1].'-'.$dateArr[0];
						$invoice_amount=trim($data[3]);
						if(strpos($invoice_amount,',')!=false){
							//$invoicepos=strpos($invoice_amount,',');
						//$invoice_amount = substr($invoice_amount,0,$invoicepos).substr(strstr($invoice_amount, ","),1);
							$invoice_amount =str_replace(',','',$invoice_amount);
						}
						if(strpos($invoice_amount,' Cr')!=false){
							$invoice_amount='-'.$invoice_amount;
						}
						$due_amount=trim($data[4]);
						if(strpos($due_amount,',')!=false){
						//$due_amount = substr($due_amount,0,strpos($due_amount,',')).substr(strstr($due_amount, ","),1);
						$due_amount =str_replace(',','',$due_amount);
						}
						if(strpos($due_amount,' Cr')!=false){
							$due_amount ='-'.$due_amount;
						}
						$vertical_value	  =trim($data[5]); 
					}
	
					if(providing_code=='yes'){
						/*$sqlroutecode="SELECT route_code FROM route_master WHERE dns_route_code='".$route_code_name."'";
						$rsroutecode=mysql_query($sqlroutecode);
						$rowroutecode=mysql_fetch_array($rsroutecode);
						$route_code=$rowroutecode['route_code'];
						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".$customer_code_name."' AND route_code='".$route_code."'";*/
						$sqlempcode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name)."'";
						$rsempcode=mysql_query($sqlempcode);
						$rowempcode=mysql_fetch_array($rsempcode);
						$emp_code=$rowempcode['emp_code'];
						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".$customer_code_name."'";
					}
					else
					{
						/*$sqlroutecode="SELECT route_code FROM route_master WHERE route_name='".$route_code_name."'";
						$rsroutecode=mysql_query($sqlroutecode);
						$rowroutecode=mysql_fetch_array($rsroutecode);
						$route_code=$rowroutecode['route_code'];
						//$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_code_name)."' AND route_code='".$route_code."'";
						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_code_name)."' AND route_code='".$route_code."'";*/
						$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_code_name)."'";
					}
					$rscustomercode=mysql_query($sqlcustomercode);
					$countcustomercode=mysql_num_rows($rscustomercode);
					$rowcustomercode=mysql_fetch_array($rscustomercode);
					$customer_code=$rowcustomercode['customer_code'];
					
					if($countcustomercode <1 && !in_array($customer_code_name,$customeroutstandingmissmatchArr))
					{
						$customeroutstandingmissmatch='';
						$customeroutstandingmissmatch.=$customer_code_name.',';
						array_push($customeroutstandingmissmatchArr,$customer_code_name);
						//$lineexcel .= $customeroutstandingmissmatch."\n";
					}

					$sql  = "insert into outstanding ";
					$sql .= " SET customer_code='".mysql_real_escape_string($customer_code)."'";
					$sql .= " ,route_code='".mysql_real_escape_string($route_code)."'";
					$sql .= " , invoice_id='".mysql_real_escape_string($invoice_id)."'";
					$sql .= " , date='".mysql_real_escape_string($finaldate)."'";
					$sql .= " , invoice_amount='".mysql_real_escape_string($invoice_amount)."'";
					$sql .= " , due_amount='".mysql_real_escape_string($due_amount)."'";
					
					mysql_query($sql) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Outstanding.csv.Please check.");;
				}
				 $rec_count++;
			}
			//exit();
			//Customer code checking start
				//print_r($customeroutstandingmissmatchArr);
				$customeroutstandingmissmatch=substr($customeroutstandingmissmatch,0,-1);
				$errorcustomeroutstanding=$customeroutstandingmissmatch.' exists in outstanding but not exists in customer_master.';
				array_push($error_array,$errorcustomeroutstanding);
				/*$data = str_replace("\r","",$lineexcel);
				
				header("Content-type: application/x-msdownload"); 
				header("Content-Disposition: attachment; filename=customermissmatch.xls"); 
				header("Pragma: no-cache"); 
				header("Expires: 0"); 
				print "$data";*/
			//Customer code checking end		
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Outstanding.csv is wrong.";
			exit();
		}*/
		
	
	//For MRP CSV
	if(similar_file_exists("csv/$folderName/MRPMaster.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/MRPMaster.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
			$lines = file($filename);
			/*$sqldelete="truncate mrp";
			$rsdelete=mysql_query($sqldelete);*/
			
			foreach($lines as $line)
			{
				$i = 0;
				$char = substr($line, $i, 1);
				$value ="";
				$data="";
				$double_coute_found = false;
				if($rec_count>=1)
				{ 
					while($char!="")
					{
						
						if($double_coute_found && $char=="\"")
						{
							$double_coute_found = false;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
				
						if(!$double_coute_found && $char=="\"")
						{  
							$double_coute_found = true;
							$i++;
							$char = substr($line, $i, 1);
							continue;
						}
					
						if($char=="," && !$double_coute_found)
						{
							$data[]=$value;
							$value = "";
						}
						else 
						{
						$value .= $char;
						}
						$i++;
						$char = substr($line, $i, 1);
					} //end of while
				  $data[]=$value;
				//print_r($data);
				  	$csv_row_count=$rec_count+1;
					$prod_code_name=trim($data[0]);
					//$brand_code_name=trim($data[2]);
					//$brand_form_code_name=trim($data[3]);
					$mrp=trim($data[1]);
					if(strpos($mrp,',')!=false){
						$mrppos=strpos($mrp,',');
					$mrp = substr($mrp,0,$mrppos).substr(strstr($mrp, ","),1);
					}
					$sale_rate=trim($data[2]);
					if(strpos($sale_rate,',')!=false){
						$sale_ratepos=strpos($sale_rate,',');
						$sale_rate = substr($sale_rate,0,$sale_ratepos).substr(strstr($sale_rate, ","),1);
					}
					$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_name)."'";
					$rsprodcode=mysql_query($sqlprodcode);
					$rowprodcode=mysql_fetch_array($rsprodcode);
					$prod_code=$rowprodcode['prod_code'];
					
					$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."'";
					$rsmrpchk=mysql_query($sqlmrpchk);
					$countmrpchk=mysql_num_rows($rsmrpchk);
					$csv_row_count=$rec_count+1;
					if($countmrpchk<1){
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
						$sql .= " , mrp_code='".$max_mrp_code."'";
						$sql .= " , mrp='".mysql_real_escape_string($mrp)."'";
						$sql .= " , sale_rate='".mysql_real_escape_string($sale_rate)."'";
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
						mysql_query($sql)  or  array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count on Mrp code  columns in Mrp.csv.Please check.");
					}
					else
					{
						$rowmrpchk=mysql_fetch_array($rsmrpchk);
						$mrp_db=$rowmrpchk['mrp'];
						$sale_rate_db=$rowmrpchk['sale_rate'];	
						if($mrp_db!=$mrp || $sale_rate_db!=$sale_rate){
							$sqlupdate  = "UPDATE mrp ";
							$sqlupdate .= " SET mrp='".mysql_real_escape_string($mrp)."'";
							$sqlupdate .= " , sale_rate='".mysql_real_escape_string($sale_rate)."'";
							$sqlupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE product_code='".$prod_code."'";
							mysql_query($sqlupdate) or  array_push($error_array,".Internal error occurs @row $csv_row_count on Mrp.csv.Please check.");
						}
					}
				}
				 $rec_count++;
			}
			//Product code checking start
				$sqlprodcodeprice="SELECT product_code FROM mrp WHERE product_code NOT IN
									(SELECT prod_code FROM product_master) GROUP BY product_code";
				$rsprodcodeprice=mysql_query($sqlprodcodeprice);
				$cntprodcodeprice=mysql_num_rows($rsprodcodeprice);
				if($cntprodcodeprice>0)
				{
					$prodcodeprice='';
					while($rowprodcodeprice=mysql_fetch_array($rsprodcodeprice))
					{
						$prodcodeprice=$prodcodeprice.$rowprodcodeprice['product_code'].',';
					}
					$prodcodeprice=substr($prodcodeprice,0,-1);
					$errorprodcodeprice=$prodcodeprice.' exists in MRP but not exists in Sku Master.';
					array_push($error_array,$errorprodcodeprice);
				}
			//Product code checking end		
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for MRP.csv is wrong.";
			exit();
		}*/
		if($successval==1)
		{
			$sqlInsert="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP()";
			if(mysql_query($sqlInsert))
			{
				echo $err = 'Zip file extracted and data has been uploaded successfully';
				$curdateserver=gmdate('Y-m-d H:i:s',strtotime('+330 minute'));
				$url="http://www.acedns.in/acednsproduct/mailDatabaseDetailsTally.php?nick_name=$nick_name";
				$url = str_replace(" ", '%20', $url);
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_TIMEOUT, 100);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
				$response = json_decode(curl_exec($ch));
			}
		}
?>
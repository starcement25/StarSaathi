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
		if ( !file_exists("csv/$folderName")){
		mkdir("csv/$folderName");
		chmod("csv/$folderName", 0777);
	}
	// Get array of all source files
	/*$files = scandir("csv/$folderName");
	// Identify directories
	$source = "csv/$folderName/";
	$destination = "csv/$folderName/filebkup/";
	// Cycle through all source files
	foreach ($files as $file) {
	  if (in_array($file, array(".",".."))) continue;
	  // If we copied this successfully, mark it for deletion
	  if (@copy($source.$file, $destination.$file)) {
		$delete[] = $source.$file;
	  }
	}
	// Delete all successfully-copied files
	foreach ($delete as $file) {
	  unlink($file);
	}*/

	$filename=similar_file_exists("csv/$nick_name/aceDNS_csv.zip");
	$zip = new ZipArchive;
	if ($zip->open($filename)) {
		$zip->extractTo("csv/$nick_name/");
		$zip->close();
	}

	$successval=0;
	
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
				$branch_state=trim($data[4]);
				$branch_email_id=trim($data[5]);
				$branch_accounts_email_id=trim($data[6]);
				$alternative_email_id=trim($data[7]);
				$plant_name=trim($data[8]);
				/*$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_name)."' 
									AND branch_location='".$branch_location."'";*/
									
				if(providing_code=='yes'){
					$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$dns_branch_code."'";
				}
				else
				{
					$sqlbranchnamechk="SELECT branch_code FROM branch_master WHERE branch_name='".addslashes($branch_name)."'";
				}					
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
					$sqlbranch .= " , branch_state='".mysql_real_escape_string($branch_state)."'";
					$sqlbranch .= " , branch_location='".mysql_real_escape_string($branch_location)."'";
					$sqlbranch .= " , comp_code='".mysql_real_escape_string($comp_code)."'";
					$sqlbranch .= " , branch_email_id='".mysql_real_escape_string($branch_email_id)."'";
					$sqlbranch .= " , alternative_email_id='".mysql_real_escape_string($alternative_email_id)."'";
					$sqlbranch .= " , plant_name='".mysql_real_escape_string($plant_name)."'";
					$sqlbranch .= " , download_time=CURRENT_TIMESTAMP()";
				}
				else
				{
					$rowbranchnamechk=mysql_fetch_array($rsbranchnamechk);
					$branch_code_db=$rowbranchnamechk['branch_code'];

					$sqlbranch  = "UPDATE branch_master SET ";
					$sqlbranch .= "  dns_branch_code='".mysql_real_escape_string($dns_branch_code)."'";
					$sqlbranch .= " , branch_location='".mysql_real_escape_string($branch_location)."'";
					$sqlbranch .= " , comp_code='".mysql_real_escape_string($comp_code)."'";
					$sqlbranch .= " , branch_state='".mysql_real_escape_string($branch_state)."'";
					$sqlbranch .= " , branch_name='".mysql_real_escape_string($branch_name)."'";
					$sqlbranch .= " , branch_email_id='".mysql_real_escape_string($branch_email_id)."'";
					$sqlbranch .= " , alternative_email_id='".mysql_real_escape_string($alternative_email_id)."'";
					$sqlbranch .= " , download_time=CURRENT_TIMESTAMP()";
					$sqlbranch .= " , plant_name='".mysql_real_escape_string($plant_name)."' WHERE branch_code='".addslashes($branch_code_db)."'";
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
	//For SKU master csv
	if(similar_file_exists("csv/$folderName/Sku master.csv")!=false)
	{
			$filename=similar_file_exists("csv/$folderName/Sku master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			$lines = file($filename);
			$duplicate_product=array();
			$branch_code_array=array();
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
				  
					$branch_code_name=trim($data[0]);
					$dns_prod_code=trim($data[1]);
					$prod_desc=trim($data[2]);
					//$prod_desc=str_replace('~','"',$prod_desc);
					$product_group_code_name=trim($data[3]);
					$product_sub_group_code_name=trim($data[4]);
					$product_brand_code_name=trim($data[5]);
					//For SELVEL
					/*if($product_brand_code_name!=''){
					$prod_desc=$prod_desc.'-'.$product_brand_code_name;
					}
					$product_brand_code_name='';*/
					//End For SELVEL
					$cl_stk=trim($data[6]);
					if(strpos($cl_stk,',')!=false){
						$stkpos=strpos($cl_stk,',');
					$cl_stk = substr($cl_stk,0,$stkpos).substr(strstr($cl_stk, ","),1);
					}
					$acedns=trim($data[7]);
					$black_list=trim($data[8]);
					$vertical_value=trim($data[9]);
					$UOM1=trim($data[10]);
					$UOM2=trim($data[11]);
					$conversion=trim($data[12]);
					$pack_size=trim($data[13]);
					$UOM3=trim($data[14]);
					$conversion_factor_two=trim($data[15]);
					$conversion_factor_two=str_replace(',','',$conversion_factor_two);
					$TD=trim($data[16]);
		
					//echo no_of_filter;
					$csv_row_count=$rec_count+1;
					if(providing_code=='yes'){
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$branch_code_name."'";
					}
					else
					{
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".$branch_code_name."'";
					}
					$rsbranchcode=mysql_query($sqlbranchcode);
					$rowbranchcode=mysql_fetch_array($rsbranchcode);
					$branch_code=$rowbranchcode['branch_code'];

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
							$sqlbrand .= " , vertical_value='".addslashes($vertical_value)."'";
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
								$sqlupdatebrand .= " vertical_value='".addslashes($vertical_value)."'";
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
							$sqlbrandform .= " , vertical_value='".addslashes($vertical_value)."'";
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
								$sqlupdatebrandform .= " vertical_value='".addslashes($vertical_value)."'";
								$sqlupdatebrandform .= " , download_time=CURRENT_TIMESTAMP() WHERE 
														product_sub_group_name='".addslashes($product_sub_group_code_name)." AND product_group_code='".$product_group_code."'";
								mysql_query($sqlupdatebrandform);
							}
						}
						//Product sub group code checking end
					}
					if(no_of_filter > 3){
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
					}
						if(branch_wise_product=='yes')
						{
							if(providing_code=='yes'){
								$sqlskunamechk="SELECT * FROM product_master WHERE  branch_code='".$branch_code."' AND  
											dns_prod_code='".$dns_prod_code."' AND product_group_code='".$product_group_code."' 
											AND product_sub_group_code='".$product_sub_group_code."' AND product_brand_code='".$product_brand_code."'";
							}
							else
							{
								$sqlskunamechk="SELECT * FROM product_master WHERE  branch_code='".$branch_code."' AND  
											prod_desc='".$prod_desc."' AND product_group_code='".$product_group_code."' 
											AND product_sub_group_code='".$product_sub_group_code."' AND product_brand_code='".$product_brand_code."'";
							}
						}
						else
						{
							if(providing_code=='yes'){
							   $sqlskunamechk="SELECT * FROM product_master WHERE  dns_prod_code='".$dns_prod_code."' 
											AND product_group_code='".$product_group_code."' AND product_sub_group_code='".$product_sub_group_code."' 
											AND product_brand_code='".$product_brand_code."'";
							}
							else
							{
								$sqlskunamechk="SELECT * FROM product_master WHERE  prod_desc='".$prod_desc."' AND 
												product_group_code='".$product_group_code."' 
											AND product_sub_group_code='".$product_sub_group_code."' AND product_brand_code='".$product_brand_code."'";
							}
						}
						$rsskunamechk=mysql_query($sqlskunamechk);
						$countskunamechk=@mysql_num_rows($rsskunamechk);
						$rowskunamechk=@mysql_fetch_array($rsskunamechk);
						$updateflag=0;
						$insertflag=0;
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
							$sql .= " , dns_prod_code='".$dns_prod_code."'";
							$sql .= " , branch_code='".$branch_code."'";
							$sql .= " , prod_desc='".addslashes($prod_desc)."'";
							$sql .= " , product_group_code='".mysql_real_escape_string($product_group_code)."'";
							$sql .= " , product_sub_group_code='".mysql_real_escape_string($product_sub_group_code)."'";
							$sql .= " , product_brand_code='".mysql_real_escape_string($product_brand_code)."'";
							$sql .= " , cl_stk='".mysql_real_escape_string($cl_stk)."'";
							$sql .= " , acedns='".$acedns."'";
							$sql .= " , black_list='".$black_list."'";
							$sql .= " , vertical_value='".addslashes($vertical_value)."'";
							$sql .= " , UOM1='".$UOM1."'";
							$sql .= " , UOM2='".$UOM2."'";
							$sql .= " , pack_size='".$pack_size."'";
							$sql .= " , conversion_factor='".$conversion."'";
							$sql .= " , UOM3='".$UOM3."'";
							$sql .= " , conversion_factor_two='".$conversion_factor_two."'";
							$sql .= " , TD='".$TD."'";
							$sql .= " , download_time=CURRENT_TIMESTAMP()";
							$sql .= " ,	download_time_cl_stk=CURRENT_TIMESTAMP()";
							mysql_query($sql) or array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count on Sku code column in Sku master.csv.Please check.");
							
							if(branch_wise_mrp=='yes')
							{
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
		
								$sqlinsertmrp  = "insert into mrp ";
								$sqlinsertmrp .= " SET product_code='".mysql_real_escape_string($max_prod_code)."'";
								$sqlinsertmrp .= " , branch_code='".mysql_real_escape_string($branch_code)."'";
								$sqlinsertmrp .= " , mrp_code='".$max_mrp_code."'";
								$sqlinsertmrp .= " , dns_mrp_code=''";
								$sqlinsertmrp .= " , mrp='0'";
								$sqlinsertmrp .= " , sale_rate='0'";
								$sqlinsertmrp .= " , vertical_value='".addslashes($vertical_value)."'";
								$sqlinsertmrp .= " , UOM=''";
								$sqlinsertmrp .= " , download_time=CURRENT_TIMESTAMP()";
								mysql_query($sqlinsertmrp)  or  array_push($error_array,"mysql_error().Internal error occurs in addition of mrp.Please check.");
							}
							$insertflag=1;
						}
						else
						{
							$cl_stk_db=$rowskunamechk['cl_stk'];
							$branch_code_db=$rowskunamechk['branch_code'];
							$acedns_db=$rowskunamechk['acedns'];
							$black_list_db=$rowskunamechk['black_list'];
							$prod_code_db=$rowskunamechk['prod_code'];
							$product_group_code_db=$rowskunamechk['product_group_code'];
							$product_sub_group_code_db=$rowskunamechk['product_sub_group_code'];
							$product_brand_code_db=$rowskunamechk['product_brand_code'];
							$UOM1_db=$rowskunamechk['UOM1'];
							$UOM2_db=$rowskunamechk['UOM2'];
							$conversion_db=$rowskunamechk['conversion_factor'];
							$vertical_value_db=$rowskunamechk['vertical_value'];
							$conversion_factor_two_db=$rowskunamechk['conversion_factor_two'];
							$pack_size_db=$rowskunamechk['pack_size'];
							$UOM3_db=$rowskunamechk['UOM3'];
							$prod_desc_db=$rowskunamechk['prod_desc'];
							$TD_db=$rowskunamechk['TD'];

							if($acedns_db!=$acedns || $black_list_db!=$black_list  || $prod_desc_db!=$prod_desc 
								|| $product_group_code_db!=$product_group_code || $product_sub_group_code_db!=$product_sub_group_code 
								|| $product_brand_code_db!=$product_brand_code || $branch_code_db!=$branch_code || $conversion_db!=$conversion 
								|| $vertical_value_db!=$vertical_value || $conversion_factor_two_db!=$conversion_factor_two || $UOM3_db!=$UOM3 || $pack_size_db!=$pack_size || $TD_db!=$TD)
							{
								$sql  = "UPDATE product_master ";
								$sql .= " SET branch_code='".$branch_code."'";
								$sql .= " , prod_desc='".addslashes($prod_desc)."'";
								$sql .= " , product_group_code='".mysql_real_escape_string($product_group_code)."'";
								$sql .= " , product_sub_group_code='".mysql_real_escape_string($product_sub_group_code)."'";
								$sql .= " , product_brand_code='".mysql_real_escape_string($product_brand_code)."'";
								$sql .= " , acedns='".$acedns."'";
								$sql .= " , black_list='".$black_list."'";
								$sql .= " , UOM1	 ='".$UOM1."'";
								$sql .= " , UOM2  ='".$UOM2."'";
								$sql .= "  ,conversion_factor='".$conversion."'";
								$sql .= " , pack_size='".$pack_size."'";
								$sql .= " , UOM3='".$UOM3."'";
								$sql .= " , conversion_factor_two='".$conversion_factor_two."'";
								$sql .= " , TD='".$TD."'";							
								$sql .= " , download_time=CURRENT_TIMESTAMP()";
								$sql .= " , vertical_value='".addslashes($vertical_value)."' WHERE prod_code='".$prod_code_db."' AND branch_code='".$branch_code_db."'";
								mysql_query($sql) or array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count on Sku code column in sku master.csv.Please check.");
								$updateflag=1;
							}
							else if($cl_stk_db!=$cl_stk)
							{
								$sql  = "UPDATE product_master ";
								$sql .= " SET cl_stk='".$cl_stk."',download_time_cl_stk=CURRENT_TIMESTAMP() WHERE prod_code='".$prod_code_db."'  AND branch_code='".$branch_code_db."'";
								mysql_query($sql) or array_push($error_array,"mysql_error().Internal error @row $csv_row_count on Sku code column in sku master.csv.Please check.");
								$updateflag=1;
							}
						}
						if(branch_wise_product=='yes' && ($updateflag==1 || $insertflag==1))//Start For emp data download log
						{
							if(!in_array($branch_code,$branch_code_array))
							{
								array_push($branch_code_array,$branch_code);
								$sqlbranchwiseemp="SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$branch_code."', branch_code)";
								$rsbranchwiseemp=mysql_query($sqlbranchwiseemp);
								while($rowbranchwiseemp=mysql_fetch_array($rsbranchwiseemp))
								{
									$emp_code_branchwise=$rowbranchwiseemp['emp_code'];
									modifyempdatadownloadlog($emp_code_branchwise,strtoupper($folderName));
								}
							}
						}//End For emp data download log

					//For TT
					//array_push($duplicate_product,$dns_prod_code." \t".$prod_desc." \t".$product_group_code." \t".$product_sub_group_code);
				}
		$rec_count++;
		}
		if(branch_wise_product=='no')//Start For emp data download log with no branch tagging
		{
			$emp_code='';
			modifyempdatadownloadlog($emp_code,strtoupper($folderName));
		}//End For emp data download log with no branch tagging

		//check product group status
		$sqlproductgroupstatus="SELECT product_group_code FROM product_group_master WHERE product_group_code NOT 
		IN(SELECT DISTINCT product_group_code FROM product_master WHERE acedns='Y' and black_list='N' )";
		$rsproductgroupstatus=mysql_query($sqlproductgroupstatus);
		$cntproductgroupstatus=mysql_num_rows($rsproductgroupstatus);
		if($cntproductgroupstatus>0)
		 {
			 $groupcodestatus='';
			  while($rowproductgroupstatus=mysql_fetch_array($rsproductgroupstatus))
			  {
				$groupcodestatus=$groupcodestatus."'".$rowproductgroupstatus['product_group_code']."'".',';
			  }
			  $groupcodestatus=substr($groupcodestatus,0,-1);
			 $sqlupdateproductgroupstatus="UPDATE product_group_master set acedns='N' WHERE product_group_code IN(".$groupcodestatus.")";
			 mysql_query($sqlupdateproductgroupstatus);
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
	if(similar_file_exists("csv/$folderName/Employee master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/Employee master.csv");
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
					$branch_code='';
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
				  
					
					$dns_employee_code=trim($data[0]);
					$employee_name=trim($data[1]);
					$branch_code_name=trim($data[2]);
					$vertical_value=trim($data[3]);
					$reporting_to=trim($data[4]);
					if(strpos($reporting_to,';')!=false)
					 {
						$reporting_to=str_replace(';',',',$reporting_to);
					 }
					if($reporting_to!='')
					{
						$reporting_val_array=explode(',',$reporting_to);
						foreach($reporting_val_array as $reporting_val)
						{
							if(providing_code == 'yes')
							{
								$sql_check_emp_code = "SELECT emp_code FROM employee_master WHERE dns_emp_code = '".$reporting_val."'";
							}
							else
							{
							   $sql_check_emp_code = "SELECT emp_code FROM employee_master WHERE emp_name = '".$reporting_val."'";
							}
							$res_check_emp_code = mysql_query($sql_check_emp_code);
							$total_rows_emp_code = mysql_num_rows($res_check_emp_code);
							$reporting_not_exists='';
							if($total_rows_emp_code == 0)
							{
								if(providing_code == 'yes')
								{
									$reporting_not_exists=$dns_employee_code.'#'.$reporting_val;
									array_push($reporting_to_array,$reporting_not_exists);
								}
								else
								{
									$reporting_not_exists=$employee_name.'#'.$reporting_val;
									array_push($reporting_to_array,$reporting_not_exists);
								}
							}
						}
				    }

					$email=trim($data[5]);
					$phone_no=trim($data[6]);
					$sale_access=trim($data[7]);
					$designation=trim($data[8]);
					$HQ=trim($data[9]);
					$state=trim($data[10]);
					$zone=trim($data[11]);
					$acedns=trim($data[12]);
					
					if($acedns =='N')
					{
						$app_access='N';
					}
					else
					{
						$app_access='Y';
					}
										
					if(providing_code=='yes'){
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE FIND_IN_SET(dns_branch_code,'".$branch_code_name."')";
						$sqlreportingto="SELECT emp_code FROM employee_master WHERE FIND_IN_SET(dns_emp_code,'".$reporting_to."')";
					}
					else
					{
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE FIND_IN_SET(branch_name,'".$branch_code_name."')";
						$sqlreportingto="SELECT emp_code FROM employee_master WHERE FIND_IN_SET(emp_name,'".$reporting_to."')";
					}
					$rsbranchcode=mysql_query($sqlbranchcode);
					while($rowbranchcode=mysql_fetch_array($rsbranchcode))
					{
						$branch_code=$branch_code.$rowbranchcode['branch_code'].',';
					}
					$branch_code=substr($branch_code,0,-1);
					if(vertical_fields=='yes'){
						$vertical_condition=" AND vertical_value='".$vertical_value."'";
					}
					else
					{
						$vertical_condition="";
					}
					if(providing_code=='yes'){
						$sqlempnamechk="SELECT emp_code FROM employee_master WHERE dns_emp_code='".$dns_employee_code."' ".$vertical_condition."";
					}
					else{
						$sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($employee_name)."' ".$vertical_condition."";
					}
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
						$sql .= " , acedns='".$acedns."'";
						$sql .= " , app_access='".$app_access."'";
						$sql .= " , state='".$state."'";
						$sql .= " , zone='".$zone."'";
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
						mysql_query($sql) or  array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count on Employee code column in Employee Master.csv.Please check.");
						
						$sqlcp  = "insert into changepassword ";
						$sqlcp .= " SET emp_code='".$max_emp_code."'";
						$sqlcp .= " , newpassword='1234'";
						$sqlcp .= " , oldpassword='1234'"; 
						$sqlcp .= " , status='true'";
						$sqlcp .= " , is_licensed='1'"; 
						mysql_query($sqlcp) or  array_push($error_array,"mysql_error().Internal DATA execution problem on password table.PLease contact aceDNS admin.");
						modifyempdatadownloadlog($max_emp_code,strtoupper($folderName));
					}
					else
					{
						$rowempnamechk=mysql_fetch_array($rsempnamechk);
						$emp_code_db=$rowempnamechk['emp_code'];
						$sqlupdate  = "UPDATE employee_master ";
						$sqlupdate .= " SET branch_code='".$branch_code."'";
						$sqlupdate .= " , emp_name='".$employee_name."'";
						$sqlupdate .= " , vertical_value='".$vertical_value."'";
						$sqlupdate .= " , reporting_to='".$reporting_to_val."'";
						$sqlupdate .= " , email='".$email."'";
						$sqlupdate .= " , sale_access='".$sale_access."'";
						$sqlupdate .= " , HQ='".$HQ."'";
						$sqlupdate .= " , designation='".$designation."'";
						$sqlupdate .= " , acedns='".$acedns."'";
						$sqlupdate .= " , app_access='".$app_access."'";
						$sqlupdate .= " , state='".$state."'";
						$sqlupdate .= " , zone='".$zone."'";
						$sqlupdate .= " , download_time=CURRENT_TIMESTAMP()";
						$sqlupdate .= " , phone_no='".$phone_no."' WHERE emp_code='".$emp_code_db."'";
						mysql_query($sqlupdate) or  array_push($error_array,"mysql_error().Internel error  @row $csv_row_count on in Employee Master.csv.Please check.");
						modifyempdatadownloadlog($emp_code_db,strtoupper($folderName));
					}
				}
				 $rec_count++;
			}
			foreach($reporting_to_array as $reporting_to_concat_values)
			{
				$reporting_to_splitval=explode('#',$reporting_to_concat_values);
				if(providing_code=='yes'){
					$sqlempowncode="SELECT emp_code,reporting_to FROM employee_master WHERE dns_emp_code='".$reporting_to_splitval[0]."'";
					$sqlempbosscode="SELECT emp_code FROM employee_master WHERE dns_emp_code='".$reporting_to_splitval[1]."'";
				}
				else{
					$sqlempowncode="SELECT emp_code,reporting_to FROM employee_master WHERE emp_name='".$reporting_to_splitval[0]."'";
					$sqlempbosscode="SELECT emp_code FROM employee_master WHERE emp_name='".$reporting_to_splitval[1]."'";
				}
				$rsempowncode=mysql_query($sqlempowncode);
				$rsempbosscode=mysql_query($sqlempbosscode);
				$countempowncode=mysql_num_rows($rsempowncode);
				$countempbosscode=mysql_num_rows($rsempbosscode);
				
				/*if($countempowncode == 0 || $countempbosscode==0)
				{
					echo "Please provide the details for the reporting to '".$reporting_to_splitval[1]."'";
					die;
				}*/
				if($countempbosscode >0)
				{
					$rowempowncode=mysql_fetch_array($rsempowncode);
					$emp_own_reportingto=$rowempowncode['reporting_to'];
					$rowempbosscode=mysql_fetch_array($rsempbosscode);
					if($emp_own_reportingto!='')
					{
					  $sqlupdatereportingto="UPDATE employee_master SET reporting_to=CONCAT(reporting_to,',".$rowempbosscode['emp_code']."') 
						                   WHERE emp_code='".$rowempowncode['emp_code']."'";
					}
					else
					{
						$sqlupdatereportingto="UPDATE employee_master SET reporting_to='".$rowempbosscode['emp_code']."' 
												WHERE emp_code='".$rowempowncode['emp_code']."'";
					}
					mysql_query($sqlupdatereportingto);					
				}
			}
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Employee Master.csv is wrong.";
			exit();
		}*/
		
	//For Customer CSV
	if(similar_file_exists("csv/$folderName/Customer Master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/Customer Master.csv");
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
					
					$dns_customer_code =trim($data[0]);
					$customer_name	=trim($data[1]);
					$phone_no		=trim($data[2]);
					$dns_route_code	  =trim($data[3]);
					$route_name	  =trim($data[4]);  
					$emp_code_name		=trim($data[5]);
					if($folderName=='MAITHAN')
					{
						$sqlemparray="SELECT emp_name FROM employee_master WHERE HQ='".$route_name."'";
						$rsemparray=mysql_query($sqlemparray);
						$emp_code_name_array=array();
						while($rowemparray=mysql_fetch_array($rsemparray))
						{
							array_push($emp_code_name_array,$rowemparray['emp_name']);
						}
					}
					else
					{
						if(strpos($emp_code_name,';')!=false)
						 {
							$emp_code_name=str_replace(';',',',$emp_code_name);
						 }
						$emp_code_name_array=explode(',',$emp_code_name);
					}

					//For VIPL employee only
					/*$sqlempnamechk="SELECT emp_code FROM employee_master WHERE emp_name='".trim($emp_code)."'";
					$rsempnamechk=mysql_query($sqlempnamechk);
					$rowempnamechk=mysql_fetch_array($rsempnamechk);
					$emp_code=$rowempnamechk['emp_code'];*/
					$acedns		  =trim($data[6]);
					$credit_limit	=trim($data[7]);
					$credit_days	 =trim($data[8]);
					$current_balance =trim($data[9]);
					$black_list	  =trim($data[10]); 
					$TD	  		  =trim($data[11]);
					$branch_code_name =str_replace(' ','',trim($data[12]));
					$customer_type   =trim($data[13]);
					$rds_tag   =trim($data[14]);
					$sauda_validity_period  =trim($data[15]);
					
					
				 foreach($emp_code_name_array as $emp_code_name_value_next)
				 {
				  if($emp_code_name_value_next!='')
				  {
					if(providing_code=='yes'){
						$sqlempcode="SELECT emp_code,branch_code FROM employee_master WHERE dns_emp_code='".addslashes($emp_code_name_value_next)."'";
						$rsempcode=mysql_query($sqlempcode);
						$rowempcode=mysql_fetch_array($rsempcode);
						$emp_code=$rowempcode['emp_code'];
						$branch_code=$rowempcode['branch_code'];
					}
					else
					{
						$sqlempcode="SELECT emp_code,branch_code FROM employee_master WHERE emp_name='".addslashes($emp_code_name_value_next)."'";
						$rsempcode=mysql_query($sqlempcode);
						$rowempcode=mysql_fetch_array($rsempcode);
						$emp_code=$rowempcode['emp_code'];
						//exit();
						$branch_code=$rowempcode['branch_code'];
					}
					//$emp_code=$emp_code_name;
					/*$sqlrdscode="SELECT rds_code FROM rds_master WHERE rds_name='".addslashes($rds_tag)."' AND emp_code='".$emp_code."'";
					$rsrdscode=mysql_query($sqlrdscode);
					$rowrdscode=mysql_fetch_array($rsrdscode);
					$rds_code=$rowrdscode['rds_code'];*/
					if($rds_tag!='')
					{
						$sqlimmediateboss="SELECT reporting_to FROM employee_master WHERE emp_code='".$emp_code."'";
						$rsimmediateboss=mysql_query($sqlimmediateboss);
						$rowimmediateboss=mysql_fetch_array($rsimmediateboss);
						$imediateboss=$rowimmediateboss['reporting_to'];
	
						if(providing_code=='yes'){
						  $sqlrdscode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".addslashes($rds_tag)."' 
										AND emp_code='".$imediateboss."'";
						}
						else
						{
						  $sqlrdscode="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($rds_tag)."' AND emp_code='".$imediateboss."'";
						}
						$rsrdscode=mysql_query($sqlrdscode);
						$rowrdscode=mysql_fetch_array($rsrdscode);
						$rds_code=$rowrdscode['customer_code'];
					}
					else
					{
						$rds_code='';
					}
					
					//$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."'";
					if(providing_code=='yes'){
						$sqlroutechk="SELECT * FROM route_master WHERE dns_route_code='".addslashes($dns_route_code	)."' AND emp_code='".$emp_code."'";
					}
					else
					{
						$sqlroutechk="SELECT * FROM route_master WHERE route_name='".addslashes($route_name)."' AND emp_code='".$emp_code."'";
					}

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
						$sqlroute .= " ,dns_route_code='".$dns_route_code."'";
						$sqlroute .= " ,route_name='".$route_name."'";
						$sqlroute .= " , emp_code='".$emp_code."'";
						$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
						mysql_query($sqlroute) or  array_push($error_array,"mysql_error().Internal DATA execution problem on route table.PLease contact aceDNS admin.");
						modifyempdatadownloadlog($emp_code,strtoupper($folderName));
						$route_code=$max_route_code;
					}
					else
					{
						$rowroutechk=mysql_fetch_array($rsroutechk);
						$route_code=$rowroutechk['route_code'];
					}
					if(providing_code=='yes'){
					   $sqlcustomernamechk="SELECT * FROM customer_master WHERE dns_customer_code='".addslashes($dns_customer_code)."' AND 
										emp_code='".$emp_code."' AND route_code='".$route_code."'";
					}
					else
					{
					  $sqlcustomernamechk="SELECT * FROM customer_master WHERE customer_name='".addslashes($customer_name)."' AND 
										emp_code='".$emp_code."' AND route_code='".$route_code."'";
					}
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
						$sql .= " , dns_customer_code='".$dns_customer_code."'";
						$sql .= " , customer_name='".addslashes($customer_name)."'";
						$sql .= " , branch_code='".addslashes($branch_code)."'";
						$sql .= " , phone_no='".$phone_no."'";
						$sql .= " , route_code='".$route_code."'";
						$sql .= " , emp_code='".$emp_code."'";
						$sql .= " , current_balance	='".$current_balance."'";
						$sql .= " , credit_limit='".$credit_limit."'";
						$sql .= " , credit_days='".$credit_days."'";
						$sql .= " , acedns='".$acedns."'";
						$sql .= " , black_list='".$black_list."'";
						$sql .= " , TD='".$TD."'";
						$sql .= " , rds_tag='".$rds_code."'";
						$sql .= " , cust_type='".$customer_type."'";
						$sql .= " , sauda_validity_period='".$sauda_validity_period."'";
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
						//exit();
						mysql_query($sql) or array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count on Customer name and Employee columns in customer master.csv.Please check.");
					    modifyempdatadownloadlog($emp_code,strtoupper($folderName));
						$customer_code=$max_customer_code;
					}
					else
					{
						$rowcustomernamechk=mysql_fetch_array($rscustomernamechk);
						$customer_code_db=$rowcustomernamechk['customer_code'];
						$route_code_db=$rowcustomernamechk['route_code'];
						$emp_code_db=$rowcustomernamechk['emp_code'];
						$current_balance_db=$rowcustomernamechk['current_balance'];
						$credit_limit_db=$rowcustomernamechk['credit_limit'];
						$credit_days_db=$rowcustomernamechk['credit_days'];
						$acedns_db=$rowcustomernamechk['acedns'];
						$black_list_db=$rowcustomernamechk['black_list'];
						$TD_db=$rowcustomernamechk['TD'];
						$customer_type_db=$rowcustomernamechk['cust_type'];
						$rds_tag_db=$rowcustomernamechk['rds_tag'];
						$branch_code_db=$rowcustomernamechk['branch_code'];
						$sauda_validity_period_db=$rowcustomernamechk['sauda_validity_period'];
						$customer_name_db=$rowcustomernamechk['customer_name'];
						$dns_customer_code_db=$rowcustomernamechk['dns_customer_code'];
						
						if(providing_code=='yes'){
							$update_condition=" dns_customer_code='".addslashes($dns_customer_code)."'";
						}
						else
						{
							$update_condition=" customer_name='".addslashes($customer_name)."'";
						}
						if($route_code_db!=$route_code || $emp_code_db!=$emp_code || $current_balance_db!=$current_balance || $acedns_db!=$acedns || $black_list_db!=$black_list || $TD_db!=$TD || $customer_type_db!=$customer_type || $rds_tag_db!=$rds_code 
						|| $branch_code_db!=$branch_code || $sauda_validity_period_db!= $sauda_validity_period || $credit_days_db!= $credit_days 
						|| $customer_name_db!=$customer_name || $dns_customer_code_db!=$dns_customer_code)
						{
							$sqlupdated  = "update customer_master ";
							$sqlupdated .= " SET route_code='".$route_code."'";
							$sqlupdated .= " , dns_customer_code='".$dns_customer_code."'";
							$sqlupdated .= " , customer_name='".addslashes($customer_name)."'";
							$sqlupdated .= " , current_balance	='".$current_balance."'";
							$sqlupdated .= " , acedns='".$acedns."'";
							$sqlupdated .= " , branch_code='".$branch_code."'";
							$sqlupdated .= " , TD='".$TD."'";
							$sqlupdated .= " , cust_type='".$customer_type."'";
						    $sqlupdated .= " , credit_days='".$credit_days."'";
							$sqlupdated .= " , sauda_validity_period='".$sauda_validity_period."'";
							$sqlupdated .= " , rds_tag='".$rds_code."',download_time=CURRENT_TIMESTAMP() 
											 WHERE  ".$update_condition." AND emp_code='".$emp_code."' AND route_code='".$route_code."' ";
							mysql_query($sqlupdated) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");
						  modifyempdatadownloadlog($emp_code,strtoupper($folderName));
						}
						if(($credit_limit_db!=$credit_limit))
						{
							$sqlupdatedcredit  = "update customer_master ";
							$sqlupdatedcredit .= " SET credit_limit='".$credit_limit."'";
							$sqlupdatedcredit .= " ,download_time_credit_limit=CURRENT_TIMESTAMP() 
											 WHERE ".$update_condition." AND emp_code='".$emp_code."' AND route_code='".$route_code."'";
							mysql_query($sqlupdatedcredit) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Customer Master.csv.Please check.");
							modifyempdatadownloadlog($emp_code,strtoupper($folderName));
						}
						$customer_code=$customer_code_db;
					 }
					}//End of IF emp blank value checking
				  }//End of emp code name foreach
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
	if(similar_file_exists("csv/$folderName/Outstanding.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/Outstanding.csv");
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
						if(strpos($date,'-')!=false){
							$date=str_replace('-','/',$date);
						}
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
						$date=trim($data[2]);
						if(strpos($date,'-')!=false){
							$date=str_replace('-','/',$date);
						}
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
						$sqlcustomercode="SELECT customer_code,emp_code FROM customer_master WHERE dns_customer_code='".$customer_code_name."'";

					}
					else
					{
						$sqlcustomercode="SELECT customer_code,emp_code FROM customer_master WHERE customer_name='".addslashes($customer_code_name)."'";
					}
					$rscustomercode=mysql_query($sqlcustomercode);
					$countcustomercode=mysql_num_rows($rscustomercode);
					$rowcustomercode=mysql_fetch_array($rscustomercode);
					$customer_code=$rowcustomercode['customer_code'];
					$emp_code=$rowcustomercode['emp_code'];
					/*if($countcustomercode <1 && !in_array($customer_code_name,$customeroutstandingmissmatchArr))
					{
						$customeroutstandingmissmatch='';
						$customeroutstandingmissmatch.=$customer_code_name.',';
						array_push($customeroutstandingmissmatchArr,$customer_code_name);
						//$lineexcel .= $customeroutstandingmissmatch."\n";
					}*/

					$sql  = "insert into outstanding ";
					$sql .= " SET customer_code='".mysql_real_escape_string($customer_code)."'";
					$sql .= " ,route_code='".mysql_real_escape_string($route_code)."'";
					$sql .= " , invoice_id='".mysql_real_escape_string($invoice_id)."'";
					$sql .= " , date='".mysql_real_escape_string($finaldate)."'";
					$sql .= " , invoice_amount='".mysql_real_escape_string($invoice_amount)."'";
					$sql .= " , due_amount='".mysql_real_escape_string($due_amount)."'";
					
					mysql_query($sql) or array_push($error_array,".Internel error occurrs @row $csv_row_count on Outstanding.csv.Please check.");
					if($emp_code!='')
					 {
					 	modifyempdatadownloadlog($emp_code,strtoupper($folderName));
					 }
				}
				 $rec_count++;
			}
			//exit();
			//Customer code checking start
				//print_r($customeroutstandingmissmatchArr);
				/*$customeroutstandingmissmatch=substr($customeroutstandingmissmatch,0,-1);
				$errorcustomeroutstanding=$customeroutstandingmissmatch.' exists in outstanding but not exists in customer_master.';
				array_push($error_array,$errorcustomeroutstanding);*/
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
		
		//For Destination Master CSV
		if(similar_file_exists("csv/$folderName/Destination master.csv")!=false)
		{
			$filename=similar_file_exists("csv/$folderName/Destination master.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			
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
				  
					$dns_destination_code=trim($data[0]);
					$destination_name=trim($data[1]);
					$sqldestinationnamechk="SELECT destination_code FROM destination_master WHERE destination_name='".addslashes($destination_name)."'";
					$rsdestinationnamechk=mysql_query($sqldestinationnamechk);
					$countdestinationnamechk=mysql_num_rows($rsdestinationnamechk);
					
					$csv_row_count=$rec_count+1;
					if($countdestinationnamechk<1)
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
					
						$sqldestination  = "insert into destination_master SET ";
						$sqldestination .= "   destination_code='".mysql_real_escape_string($max_destination_code)."'";
						$sqldestination .= " , dns_destination_code='".mysql_real_escape_string($dns_destination_code)."'";
						$sqldestination .= " , destination_name='".mysql_real_escape_string($destination_name)."'";
						$sqldestination .= " , download_time=CURRENT_TIMESTAMP()";
						mysql_query($sqldestination) or array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count in Destination master.csv.Please check.");
						$emp_code='';
						modifyempdatadownloadlog($emp_code,strtoupper($folderName));		

					}
					else
					{
						$rowdestinationnamechk=mysql_fetch_array($rsdestinationnamechk);
	
						$sqldestination  = "UPDATE destination_master SET ";
						$sqldestination .= "  dns_destination_code='".mysql_real_escape_string($dns_destination_code)."',download_time=CURRENT_TIMESTAMP()
											 WHERE destination_name='".addslashes($destination_name)."'";
						mysql_query($sqldestination) or array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count in Destination master.csv.Please check.");
						$emp_code='';
						modifyempdatadownloadlog($emp_code,strtoupper($folderName));		

					}
				}
				 $rec_count++;
			}		
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Destination master.csv is wrong.";
			exit();
		}*/

	//For MRP CSV
	if(similar_file_exists("csv/$folderName/MRP.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/MRP.csv");
		$rec_count = 0;
		$ins_count = 0;
		$err = "";
		
			$lines = file($filename);
			/*$sqldelete="truncate mrp";
			$rsdelete=mysql_query($sqldelete);*/
			$branch_code_array=array();
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
				    $branch_code_name=trim($data[0]);
					$prod_code_name=trim($data[1]);
					//$brand_code_name=trim($data[2]);
					//$brand_form_code_name=trim($data[3]);
					$dns_mrp_code=trim($data[2]);
					$mrp=trim($data[3]);
					if(strpos($mrp,',')!=false){
						$mrppos=strpos($mrp,',');
					$mrp = substr($mrp,0,$mrppos).substr(strstr($mrp, ","),1);
					}
					$sale_rate=trim($data[4]);
					if(strpos($sale_rate,',')!=false){
						$sale_ratepos=strpos($sale_rate,',');
						$sale_rate = substr($sale_rate,0,$sale_ratepos).substr(strstr($sale_rate, ","),1);
					}

					$vertical_value=trim($data[5]);
					//$UOM=trim($data[6]);
					$destination_code_name=trim($data[6]);
					$order_type=trim($data[7]);
					
					if(providing_code=='yes'){
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE dns_branch_code='".$branch_code_name."'";
					}
					else
					{
						$sqlbranchcode="SELECT branch_code FROM branch_master WHERE branch_name='".$branch_code_name."'";
						//$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_name)."' 
								//AND product_group_code='".$brand_code_name."' AND product_sub_group_code='".$brand_form_code_name."'";
					}
					$rsbranchcode=mysql_query($sqlbranchcode);
					$rowbranchcode=mysql_fetch_array($rsbranchcode);
					$branch_code=$rowbranchcode['branch_code'];
					if(providing_code=='yes'){
						if(branch_wise_product=='yes')
						{
							$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."' AND branch_code='".$branch_code."'";
						}
						else
						{
							$sqlprodcode="SELECT prod_code FROM product_master WHERE dns_prod_code='".$prod_code_name."' AND branch_code='".$branch_code."'";
						}
					}
					else
					{
						$sqlprodcode="SELECT prod_code FROM product_master WHERE prod_desc='".addslashes($prod_code_name)."'";
					}
					$rsprodcode=mysql_query($sqlprodcode);
					$rowprodcode=mysql_fetch_array($rsprodcode);
					$prod_code=$rowprodcode['prod_code'];
					
					if($destination_code_name!='')
					{
						if(providing_code=='yes'){
								$sqldestinationcode="SELECT destination_code FROM destination_master WHERE dns_destination_code='".$destination_code_name."'";
						}
						else
						{
							$sqldestinationcode="SELECT destination_code FROM destination_master WHERE destination_name='".addslashes($destination_code_name)."'";
						}
					}
					$rsdestinationcode=mysql_query($sqldestinationcode);
					$rowdestinationcode=mysql_fetch_array($rsdestinationcode);
					$destination_code=$rowdestinationcode['destination_code'];

					
					if(uom_wise_mrp=='yes' && branch_wise_mrp=='yes')
					{
						$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND 	UOM='".$UOM."' AND branch_code='".$branch_code."'";
					}
					else if(destination_ordertype_price_list=='yes')
					{
						$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND destination_code='".$destination_code."' 
									AND order_type='".$order_type."'";
					}
					else if(destination_price_list=='yes' && destination_ordertype_price_list=='no')
					{
						$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND destination_code='".$destination_code."'";
					}
					else
					{
						if(providing_code=='yes')
						{
							$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND branch_code='".$branch_code."' 
										AND dns_mrp_code='".$dns_mrp_code."'";
						}
						else
						{
							$sqlmrpchk="SELECT * FROM mrp WHERE product_code='".$prod_code."' AND branch_code='".$branch_code."'";
						}
					}
					$rsmrpchk=mysql_query($sqlmrpchk);
					$countmrpchk=mysql_num_rows($rsmrpchk);
					$csv_row_count=$rec_count+1;
					$insertflag=0;
					$updateflag=0;
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
						$sql .= " , branch_code='".$branch_code."'";
						$sql .= " , mrp_code='".$max_mrp_code."'";
						$sql .= " , dns_mrp_code='".$dns_mrp_code."'";
						$sql .= " , mrp='".mysql_real_escape_string($mrp)."'";
						$sql .= " , sale_rate='".mysql_real_escape_string($sale_rate)."'";
						$sql .= " , vertical_value='".mysql_real_escape_string($vertical_value)."'";
						$sql .= " , UOM='".mysql_real_escape_string($UOM)."'";
						$sql .= " , destination_code='".mysql_real_escape_string($destination_code)."'";
						$sql .= " , order_type='".mysql_real_escape_string($order_type)."'";
						$sql .= " , download_time=CURRENT_TIMESTAMP()";
						mysql_query($sql)  or  array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count on Mrp code  columns in Mrp.csv.Please check.");
						$insertflag=1;
					}
					else
					{
						$rowmrpchk=mysql_fetch_array($rsmrpchk);
						$mrp_db=$rowmrpchk['mrp'];
						$sale_rate_db=$rowmrpchk['sale_rate'];	
						$destination_code_db=$rowmrpchk['destination_code'];
						$order_type_db=	$rowmrpchk['order_type'];
						$mrp_code_db=$rowmrpchk['mrp_code'];
						if($mrp =='') $mrp=0;
						if($mrp_db!=$mrp || $sale_rate_db!=$sale_rate || $destination_code_db!=$destination_code || $order_type_db!=$order_type){
							$sqlupdate  = "UPDATE mrp ";
							$sqlupdate .= " SET mrp='".mysql_real_escape_string($mrp)."'";
							$sqlupdate .= " , sale_rate='".mysql_real_escape_string($sale_rate)."'";
							$sqlupdate .= " , destination_code	='".mysql_real_escape_string($destination_code)."'";
							$sqlupdate .= " , order_type='".mysql_real_escape_string($order_type)."'";
							$sqlupdate .= " , download_time=CURRENT_TIMESTAMP() WHERE mrp_code='".$mrp_code_db."'";
							mysql_query($sqlupdate) or  array_push($error_array,".Internal error occurs @row $csv_row_count on Mrp.csv.Please check.");
							$updateflag=1;
						}
					}
					if(branch_wise_mrp=='yes' && ($updateflag==1 || $insertflag==1))//Start For emp data download log
					{
						if(!in_array($branch_code,$branch_code_array))
						{
							array_push($branch_code_array,$branch_code);
							$sqlbranchwiseemp="SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$branch_code."', branch_code)";
							$rsbranchwiseemp=mysql_query($sqlbranchwiseemp);
							while($rowbranchwiseemp=mysql_fetch_array($rsbranchwiseemp))
							{
								$emp_code_branchwise=$rowbranchwiseemp['emp_code'];
								modifyempdatadownloadlog($emp_code_branchwise,strtoupper($folderName));
							}
						}
					}//End For emp data download log
				}
				 $rec_count++;
			}
		    if(branch_wise_mrp=='no')//Start For emp data download log with no branch tagging
			{
				$emp_code='';
				modifyempdatadownloadlog($emp_code,strtoupper($folderName));
			}//End For emp data download log with no branch tagging

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
		//For Distributor route csv
		if(similar_file_exists("csv/$folderName/Distributor route.csv")!=false)
		{  
		    $filename=similar_file_exists("csv/$folderName/Distributor route.csv");
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
						$route_code_name=trim($data[1]);
						$acedns=trim($data[2]);
						
						if(providing_code=='yes'){
							$sqlcustomercode="SELECT customer_code FROM customer_master WHERE dns_customer_code='".$customer_code_name."'";
						}
						else
						{
							$sqlcustomercode="SELECT customer_code FROM customer_master WHERE customer_name='".$customer_code_name."'";
						}
						$rscustomercode=mysql_query($sqlcustomercode);
						$rowcustomercode=mysql_fetch_array($rscustomercode);
						$customer_code=$rowcustomercode['customer_code'];
						
						$sqlroutecode="SELECT route_code FROM route_master WHERE route_name='".$route_code_name."'";
						$rsroutecode=mysql_query($sqlroutecode);
						$rowroutecode=mysql_fetch_array($rsroutecode);
						$route_code=$rowroutecode['route_code'];

						if($customer_code!='' && $route_code!='')
						{
							$sqldistributorroute="SELECT distributor_code,acends,route_code FROM distributor_route_relation WHERE distributor_code='".$customer_code."' 
												AND route_code='".$route_code."'";
							$rsdistributorroute=mysql_query($sqldistributorroute);
							$countdistributorroute=mysql_num_rows($rsdistributorroute);
							if($countdistributorroute <1)
							{
								$sqlinsertdistributorroute="INSERT INTO distributor_route_relation ";
								$sqlinsertdistributorroute .= " SET distributor_code='".$customer_code."'";
								$sqlinsertdistributorroute .= " ,route_code='".$route_code."'";
								$sqlinsertdistributorroute .= " ,acedns='".$acedns."'";
								$sqlinsertdistributorroute .= " ,download_time=CURRENT_TIMESTAMP()";
								mysql_query($sqlinsertdistributorroute) or array_push($error_array,".Internal error occurrs @row $csv_row_count in Distributor route.csv.Please check.");
							}
							else
							{
								$rowdistributorroute=mysql_fetch_array($rsdistributorroute);
								$acednsdb=$rowdistributorroute['acends'];
								$distributor_code_db=$rowdistributorroute['distributor_code'];
								$route_code_db=$rowdistributorroute['route_code'];
								
								if($acednsdb!=$acedns)
								{
									$sqlupdatedistributorroute="UPDATE distributor_route_relation SET acedns='".$acedns."',
															  download_time=CURRENT_TIMESTAMP() WHERE distributor_code='".$distributor_code_db."' AND route_code='".$route_code_db."'";
									$rsupdatedistributorroute=mysql_query($sqlupdatedistributorroute) or array_push($error_array,".Internal error occurrs @row $csv_row_count in Distributor route.csv.Please check.");
								}
							}
						}
					}
					 $rec_count++;
				}
				$successval=1;
			}
			/*else
			{
				echo $successval="Naming convention for Distributor route.csv is wrong.";
				exit();
			}*/
		
	    //For Employee target achievement CSV
		if(similar_file_exists("csv/$folderName/Emp_Target_Achievement.csv")!=false)
		{
			$filename=similar_file_exists("csv/$folderName/Emp_Target_Achievement.csv");
			$rec_count = 0;
			$ins_count = 0;
			$err = "";
			
			//$type=strtoupper(substr($_FILES['excel_file']['name'],(strrpos($_FILES['excel_file']['name'],".")+1)));
			$sqldelete="truncate emp_target_achievement";
			$rsdelete=mysql_query($sqldelete);

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
				  
					$emp_name=trim($data[0]);
					$month=trim($data[1]);
					$month=str_replace('/','-',$month);
					//$month=substr($month,3,7);
					$district=trim($data[2]);
					$customer_name=trim($data[3]);
					$volume_target=trim($data[4]);
					$volume_target=str_replace(',','',$volume_target);
					$volume_achievement=trim($data[5]);
					$volume_achievement=str_replace(',','',$volume_achievement);
					$collection_target=trim($data[6]);
					$collection_target=str_replace(',','',$collection_target);
					$collection_achievement=trim($data[7]);
					$collection_achievement=str_replace(',','',$collection_achievement);
					
					$sqlcustomerchk="SELECT customer_code FROM customer_master WHERE customer_name='".addslashes($customer_name)."'";
					$rscustomerchk=mysql_query($sqlcustomerchk);
					$rowcustomerchk=mysql_fetch_array($rscustomerchk);
					$customer_code=$rowcustomerchk['customer_code'];
					
					$sqlempchk="SELECT emp_code FROM employee_master WHERE emp_name='".addslashes($emp_name)."'";
					$rsempchk=mysql_query($sqlempchk);
					$rowempchk=mysql_fetch_array($rsempchk);
					$emp_code=$rowempchk['emp_code'];

					
					$csv_row_count=$rec_count+1;
					
				    $sqlemptargetachievement  = "insert into emp_target_achievement SET ";
				    $sqlemptargetachievement .= "   emp_code='".mysql_real_escape_string($emp_code)."'";
				    $sqlemptargetachievement .= " , emp_name='".mysql_real_escape_string(addslashes($emp_name))."'";
					$sqlemptargetachievement .= " , month='".mysql_real_escape_string($month)."'";
					$sqlemptargetachievement .= " , district='".mysql_real_escape_string(addslashes($district))."'";
					$sqlemptargetachievement .= " , customer_code='".mysql_real_escape_string($customer_code)."'";
					$sqlemptargetachievement .= " , customer_name='".mysql_real_escape_string(addslashes($customer_name))."'";
					$sqlemptargetachievement .= " , volume_target='".mysql_real_escape_string($volume_target)."'";
					$sqlemptargetachievement .= " , volume_achievement='".mysql_real_escape_string($volume_achievement)."'";
					$sqlemptargetachievement .= " , collection_target='".mysql_real_escape_string($collection_target)."'";
					$sqlemptargetachievement .= " , collection_achievement='".mysql_real_escape_string($collection_achievement)."'";
					$sqlemptargetachievement .= " , download_time=CURRENT_TIMESTAMP()";
					mysql_query($sqlemptargetachievement) or array_push($error_array,"mysql_error().Duplicate key @row $csv_row_count in Emp_Target_Achievement.csv.Please check.");
						modifyempdatadownloadlog($emp_code,strtoupper($folderName));		
				}
				 $rec_count++;
			}		
			$successval=1;
		}
		/*else
		{
			echo $successval="Naming convention for Emp_Target_Achievement.csv is wrong.";
			exit();
		}*/

		if($successval==1)
		{
			$sqlInsert="INSERT INTO data_refresh_log SET refresh_date_time=CURRENT_TIMESTAMP()";
			if(mysql_query($sqlInsert))
			{
				if(count($error_array)>0)
				{
					$mailbodyerror='Data has been successfully uploaded to '.$nick_name.' database with the following error(s).<br /><br />';
					
					for($i=0;$i<count($error_array);$i++){
						$mailbodyerror.= "<b>$error_array[$i]</b><br /><br />";
					}	
				}
				else{
					$mailbodyerror='Data has been successfully uploaded to '.$nick_name.' database.';	
				}

				echo $err = 'Zip file extracted and data has been uploaded successfully';
				$curdateserver=gmdate('Y-m-d H:i:s',strtotime('+330 minute'));
				$url="http://www.acedns.in/acednsproduct/mailDatabaseDetails.php?nick_name=$nick_name&mailbodyerror=$mailbodyerror&mode=standard";
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
		else
		{
			echo $err = 'Zip file extraction failure';
		}
?>
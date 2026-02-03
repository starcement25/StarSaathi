<?php
	set_time_limit(1000);	
	/*$nick_name='AMPL';
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234");
	require("include/config-setup.php");
	define("DB","acedns_$nick_name");
	//require("include/dbcon.php");
	$link=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysql_select_db(DB,$link) or die("could not connect the database for invalid nick name");*/
	//require("include/functions.php");
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");
	require("include/config-email-setup.php");
	
	$folderName=$nick_name;
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
	
	$error_array=array();
	
	//For Company Master CSV
	if(similar_file_exists("csv/$folderName/company master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/company master.csv");
		$linescompany = file($filename);
		$csvcompanycount=(count($linescompany)-1);
	}
	//For Branch Master CSV
	if(similar_file_exists("csv/$folderName/branch master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/branch master.csv");
		$linesbranch = file($filename);
		$csvbranchcount=(count($linesbranch)-1);
	}
	//For Employee CSV
	if(similar_file_exists("csv/$folderName/employee master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/employee master.csv");
		$linesemployee = file($filename);
		$csvemployeecount=(count($linesemployee)-1);
	}
	//For Vendor CSV
	if(similar_file_exists("csv/$folderName/vendor master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/vendor master.csv");
		$linesvendor = file($filename);
		$csvvendorcount=(count($linesvendor)-1);
	}
	//For Route CSV
	/*if(similar_file_exists("csv/$folderName/CUSTOMER MASTER.CSV")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/CUSTOMER MASTER.CSV");
		$linesroute = file($filename);
		$csvroutecount=(count($linesroute)-1);
	}*/
	//For Customer CSV
	if(similar_file_exists("csv/$folderName/CUSTOMER MASTER.CSV")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/CUSTOMER MASTER.CSV");
		$linescustomer = file($filename);
		$csvcustomercount=(count($linescustomer)-1);
	}
	//For Sku CSV
	if(similar_file_exists("csv/$folderName/sku master.csv")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/sku master.csv");
		$linessku = file($filename);
		$csvskucount=(count($linessku)-1);
	}
	//For Outstanding CSV
	if(similar_file_exists("csv/$folderName/OUTSTANDING.CSV")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/OUTSTANDING.CSV");
		$linesoutstanding = file($filename);
		$csvoutstandingcount=(count($linesoutstanding)-1);
	}
	//For MRP CSV
	if(similar_file_exists("csv/$folderName/MRP.CSV")!=false)
	{
		$filename=similar_file_exists("csv/$folderName/MRP.CSV");
		$linesmrp = file($filename);
		$csvmrpcount=(count($linesmrp)-1);
	}
	//Company
   $sqlcompanycount="select * from company_master";
   $rscompanycount=mysql_query($sqlcompanycount) or die(mysql_error());
   $companycount=mysql_num_rows($rscompanycount);

   //Branch
   $sqlbranchcount="select * from branch_master";
   $rsbranchcount=mysql_query($sqlbranchcount) or die(mysql_error());
   $branchcount=mysql_num_rows($rsbranchcount);

   //Category
  /* $sqlcatcount="select * from category_master";
   $rscatcount=mysql_query($sqlcatcount) or die(mysql_error());
   $catcount=mysql_num_rows($rscatcount);

   //Sub Category
   $sqlsubcatcount="select * from sub_category_master";
   $rssubcatcount=mysql_query($sqlsubcatcount) or die(mysql_error());
   $subcatcount=mysql_num_rows($rssubcatcount);*/
   //Employee
   $sqlemployeecount="select * from employee_master";
   $rsemployeecount=mysql_query($sqlemployeecount) or die(mysql_error());
   $employeecount=mysql_num_rows($rsemployeecount);
   if(sale=='yes')
   {
		//Vendor
	   $sqlvendorcount="select * from vendor_master";
	   $rsvendorcount=mysql_query($sqlvendorcount) or die(mysql_error());
	   $vendorcount=mysql_num_rows($rsvendorcount);
	   
	   $vendorTR='<tr    style=\"scrollbar-face-color:#80a537;scrollbar-highlight-color:#EDF4DD;scrollbar-3dlight-color:#D8E7B6;scrollbar-darkshadow-color:#D8E7B6;scrollbar-shadow-color:#EDF4DD;scrollbar-arrow-color:#FFFFFF;scrollbar-track-color:#D8E7B6;margin: 0;background: #FFFFFF;\"> 
					<td align=\"center\">vendor_master</td>
					<td align=\"right\">'.$vendorcount.'</td>
					<td align=\"right\">'.$csvvendorcount.'</td>
				</tr>';
   }
   else
   {
	   $vendorTR='';
   }
   //Route
  /* $sqlroutecount="select * from route_master";
   $rsroutecount=mysql_query($sqlroutecount) or die(mysql_error());
   $routecount=mysql_num_rows($rsroutecount);*/
   
	//customer
	$sqlcustomercount="select * from customer_master";
	$rscustomercount=mysql_query($sqlcustomercount) or die(mysql_error());
	$customercount=mysql_num_rows($rscustomercount);
	
   //SKU
   $sqlskucount="select * from product_master";
   $rsskucount=mysql_query($sqlskucount) or die(mysql_error());
   $skucount=mysql_num_rows($rsskucount);
   
   //Outstanding
   $sqloutstandingcount="select * from outstanding";
   $rsoutstandingcount=mysql_query($sqloutstandingcount) or die(mysql_error());
   $outstandingcount=mysql_num_rows($rsoutstandingcount);

   //Mrp
   $sqlmrpcount="select * from mrp";
   $rsmrpcount=mysql_query($sqlmrpcount) or die(mysql_error());
   $mrpcount=mysql_num_rows($rsmrpcount);
  	

   	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	$val=$date.'-'.$month.'-'.$year.' @'.$hour.':'.$minute.':'.$second;
	
   	//$email   ='rajuyk@automotiveml.com'; 
	$email=$corresponding_emails;
	if($nick_name=='RKBK' || $nick_name=='CGEW')
	{
		$email=$corresponding_emails.',ak@coral.in';
	}
	if($nick_name=='NAPTC' || $nick_name=='PBCL')
	{
		$email='arupratanacharjee@gmail.com';
	}
	$subject =$nick_name.' batch running details on '.$val;
	$mailbodyerror=$_REQUEST['mailbodyerror'];
	$mode=$_REQUEST['mode'];
	if($mailbodyerror !='' && $mode=='standard')
	{
		$messageerror=$mailbodyerror;
	}
	else
	{
		$messageerror='';
	}
	$message='<html><body>
			<table width=\"99%\" align=\"center\" border=\"0\" cellpadding=\"5\" cellspacing=\"5\"  style=\"BORDER: #80A537 1px solid;\">
				<tr style=\"FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;COLOR: #FFFFFF;BACKGROUND-COLOR:#80a537;\" > 
					<td colspan=\"8\">'.$messageerror.'</td>
				</tr>
			</table>	
			<table width=\"99%\" align=\"center\" border=\"0\" cellpadding=\"5\" cellspacing=\"5\"  style=\"BORDER: #80A537 1px solid;\">
				<tr style=\"FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;COLOR: #FFFFFF;BACKGROUND-COLOR:#80a537;\" > 
					<td colspan=\"8\">Comparison between the MYSQL database and CSV row count are mentioned below</td>
				</tr>
				<tr style=\"FONT-FAMILY: Verdana;FONT-SIZE : 11px;FONT-WEIGHT: bold;BACKGROUND-COLOR:#c0c8b0;\"> 
					<td width=\"\" align=\"center\"><strong>Table Name</strong></td>
					<td width=\"30%\" align=\"left\"><strong>MYSQL Row Count</strong></td>
					<td width=\"30%\" align=\"left\"><strong>CSV Row Count</strong></td>
				</tr>
				<tr    style=\"scrollbar-face-color:#80a537;scrollbar-highlight-color:#EDF4DD;scrollbar-3dlight-color:#D8E7B6;scrollbar-darkshadow-color:#D8E7B6;scrollbar-shadow-color:#EDF4DD;scrollbar-arrow-color:#FFFFFF;scrollbar-track-color:#D8E7B6;margin: 0;background: #FFFFFF;\"> 
					<td align=\"center\">company_master</td>
					<td align=\"right\">'.$companycount.'</td>
					<td align=\"right\">'.$csvcompanycount.'</td>
				</tr>
				<tr    style=\"scrollbar-face-color:#80a537;scrollbar-highlight-color:#EDF4DD;scrollbar-3dlight-color:#D8E7B6;scrollbar-darkshadow-color:#D8E7B6;scrollbar-shadow-color:#EDF4DD;scrollbar-arrow-color:#FFFFFF;scrollbar-track-color:#D8E7B6;margin: 0;background: #FFFFFF;\"> 
					<td align=\"center\">branch_master</td>
					<td align=\"right\">'.$branchcount.'</td>
					<td align=\"right\">'.$csvbranchcount.'</td>
				</tr>
				
				<tr    style=\"scrollbar-face-color:#80a537;scrollbar-highlight-color:#EDF4DD;scrollbar-3dlight-color:#D8E7B6;scrollbar-darkshadow-color:#D8E7B6;scrollbar-shadow-color:#EDF4DD;scrollbar-arrow-color:#FFFFFF;scrollbar-track-color:#D8E7B6;margin: 0;background: #FFFFFF;\"> 
					<td align=\"center\">employee_master</td>
					<td align=\"right\">'.$employeecount.'</td>
					<td align=\"right\">'.$csvemployeecount.'</td>
				</tr>'.$vendorTR.'	
				<tr    style=\"scrollbar-face-color:#80a537;scrollbar-highlight-color:#EDF4DD;scrollbar-3dlight-color:#D8E7B6;scrollbar-darkshadow-color:#D8E7B6;scrollbar-shadow-color:#EDF4DD;scrollbar-arrow-color:#FFFFFF;scrollbar-track-color:#D8E7B6;margin: 0;background: #FFFFFF;\"> 
					<td align=\"center\">customer_master</td>
					<td align=\"right\">'.$customercount.'</td>
					<td align=\"right\">'.$csvcustomercount.'</td>
				</tr>	
				<tr    style=\"scrollbar-face-color:#80a537;scrollbar-highlight-color:#EDF4DD;scrollbar-3dlight-color:#D8E7B6;scrollbar-darkshadow-color:#D8E7B6;scrollbar-shadow-color:#EDF4DD;scrollbar-arrow-color:#FFFFFF;scrollbar-track-color:#D8E7B6;margin: 0;background: #FFFFFF;\"> 
					<td align=\"center\">sku_master</td>
					<td align=\"right\">'.$skucount.'</td>
					<td align=\"right\">'.$csvskucount.'</td>
				</tr>	
				<tr    style=\"scrollbar-face-color:#80a537;scrollbar-highlight-color:#EDF4DD;scrollbar-3dlight-color:#D8E7B6;scrollbar-darkshadow-color:#D8E7B6;scrollbar-shadow-color:#EDF4DD;scrollbar-arrow-color:#FFFFFF;scrollbar-track-color:#D8E7B6;margin: 0;background: #FFFFFF;\"> 
					<td align=\"center\">outstanding</td>
					<td align=\"right\">'.$outstandingcount.'</td>
					<td align=\"right\">'.$csvoutstandingcount.'</td>
				</tr>
				<tr    style=\"scrollbar-face-color:#80a537;scrollbar-highlight-color:#EDF4DD;scrollbar-3dlight-color:#D8E7B6;scrollbar-darkshadow-color:#D8E7B6;scrollbar-shadow-color:#EDF4DD;scrollbar-arrow-color:#FFFFFF;scrollbar-track-color:#D8E7B6;margin: 0;background: #FFFFFF;\"> 
					<td align=\"center\">mrp</td>
					<td align=\"right\">'.$mrpcount.'</td>
					<td align=\"right\">'.$csvmrpcount.'</td>
				</tr>	
				</table></body></html>';   

			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=UTF-8\n";
			$headers .= "From: ".FROMTAG."<".FROMEMAIL."> \r\n" .
						"Reply-To:".FROMEMAIL." \r\n" .
						"Bcc: ".BCCEMAIL." \r\n".
						'X-Mailer: PHP/' . phpversion();
	//echo $message;

	$flgSend=mail($email, $subject, $message, $headers,'-facedns@coral.in');
	/*if($flgSend)
		{
			echo "Csv Generated & Email Sending.";
		}
		else
		{
			echo "Email Can Not Send.";
		} */
?>
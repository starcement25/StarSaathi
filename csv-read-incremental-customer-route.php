<?php		
 set_time_limit(1000);
 ini_set('memory_limit', '-1');  
error_reporting(E_ALL ^ E_NOTICE);
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
require("include/config-setup.php");
define("DB","acedns_STAR");

//require("include/dbcon.php");
$link=mysql_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysql_select_db(DB,$link) or die("could not connect the database for invalid nick name");
require("include/config-email-setup.php");
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
			$customer_code_del_prev_array=array();
			$route_code_del_prev_array=array();
			$emp_code_del_prev_array=array();
	
			//------------------------------------------For customer route creation------------------------------------------------------------------
			$sqlcustomersel="SELECT * FROM customer_master WHERE 1  order by customer_name ASC";
			$rscustomersel=mysql_query($sqlcustomersel);
			while($rowcustomersel=mysql_fetch_array($rscustomersel))
			{
				$route_code_prev_string='';
				$customer_code_prev_array=array();
				$customer_name_prev_array=array();
				$dns_customer_code_prev_array=array();
				$emp_code_prev_array=array();
				$route_code_existing=$rowcustomersel['route_code'];
				$emp_code_existing=$rowcustomersel['emp_code'];
				array_push($emp_code_prev_array,$emp_code_existing);
				$customer_code_existing=$rowcustomersel['customer_code'];
				$sqlroutechk="SELECT * FROM route_master WHERE route_name=(SELECT route_name FROM route_master WHERE 
							route_code='".$route_code_existing."' AND flag='0')";
				$rsroutechk=mysql_query($sqlroutechk);
				$countroutechk=mysql_num_rows($rsroutechk);
				/*if($countroutechk<1 && $route_name!='')
				{
						$sqlmaxroutecode="SELECT MAX( CAST( SUBSTRING( route_code, 4, length( route_code ) -3 ) AS UNSIGNED ) ) AS new_route_code 
												FROM route_master WHERE route_code NOT LIKE '%N%'";
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
						//$sqlroute .= " , vertical_value='".$vertical_value."'";
						$sqlroute .= " , download_time=CURRENT_TIMESTAMP()";
						mysql_query($sqlroute) or  array_push($error_array,"mysql_error().Internal DATA execution problem on route table.PLease contact aceDNS admin.");				
						//modifyempdatadownloadlog($emp_code,strtoupper($folderName));
						$route_code=$max_route_code;
					}*/
					if($countroutechk >0)
					{	
						$countroute=1;
						while($rowroutechk=mysql_fetch_array($rsroutechk))
						{
							if($countroute==1)
							{
								$route_code_present=$rowroutechk['route_code'];
								$sqlupdateroute="UPDATE route_master SET emp_code='',flag='1',download_time=CURRENT_TIMESTAMP() 
													WHERE route_code='".$route_code_present."'";
								mysql_query($sqlupdateroute);
							}
							else
							{
								$route_code_prev_string.="'".$rowroutechk['route_code']."'".',';
							}
								$countroute++;
						}
							$route_code_prev_string=substr($route_code_prev_string,0,-1);
							if($route_code_prev_string!='')
							{
								$route_code_prev_string_final=$route_code_prev_string.','."'".$route_code_present."'";
							}
							else
							{
								$route_code_prev_string_final="'".$route_code_present."'";
							}
							$sqlselcustomerprevroute="SELECT customer_code,customer_name,emp_code,dns_customer_code FROM customer_master WHERE 
															route_code IN(".$route_code_prev_string_final.")";
							//exit();
							$rsselcustomerprevroute=mysql_query($sqlselcustomerprevroute);
							while($rowselcustomerprevroute=mysql_fetch_array($rsselcustomerprevroute))
							{
								$customer_code_previous=$rowselcustomerprevroute['customer_code'];
								$emp_code_previous=$rowselcustomerprevroute['emp_code'];
								$customer_name_previous=$rowselcustomerprevroute['customer_name'];
								$dns_customer_code_previous=$rowselcustomerprevroute['dns_customer_code'];
								//if(in_array($dns_customer_code_previous,$customer_name_previous) )
								if(in_array($dns_customer_code_previous,$dns_customer_code_prev_array) )
								{
									//echo 'duplicate exists';
									array_push($customer_code_del_prev_array,$customer_code_previous);
									array_push($emp_code_del_prev_array,$emp_code_previous);
									array_push($route_code_del_prev_array,$route_code_present);
									//array_push($dns_customer_code_del_prev_array,$dns_customer_code_previous);
								}
								else
								{
									$sqlinsertcustomerroute="INSERT INTO customer_route_emp_relation SET customer_code='".$customer_code_previous."',
																route_code='".$route_code_present."',
																emp_code='".$emp_code_previous."',
																acedns='Y',
																download_time=CURRENT_TIMESTAMP()";
									mysql_query($sqlinsertcustomerroute);
									array_push($dns_customer_code_prev_array,$dns_customer_code_previous);								//}
									//$customer_code_prev_string.="'".$rowselcustomerprevroute['customer_code']."'".',';
								}
								$sqlupdatepevcustomer="UPDATE customer_master SET route_code='".$route_code_present."',emp_code='' 
													WHERE customer_code='".$customer_code_previous."'";
								mysql_query($sqlupdatepevcustomer);
							}
							$sqldelprevroute="DELETE FROM route_master WHERE route_code IN(".$route_code_prev_string.")";
							mysql_query($sqldelprevroute);
							$replaced_route_code_prev_string=str_replace("'","",$route_code_prev_string);
							$sqlrouterefference="INSERT INTO route_refference SET route_replaced='".$replaced_route_code_prev_string."',
												route_replaced_by='".$route_code_present."'";
							mysql_query($sqlrouterefference);					
						}
						//exit();
					}//End of while
					//-----------------------------------------------------End for customer route creation--------------------------------------------------
					//print_r($customer_code_del_prev_array);
					//print_r($route_code_del_prev_array);
					for($i=0;$i<count($customer_code_del_prev_array);$i++)
					 {
						$customer_code_prev_string='';
						$sqlcustomerchk="SELECT customer_code FROM customer_master WHERE route_code='".$route_code_del_prev_array[$i]."' AND 
									dns_customer_code=(SELECT dns_customer_code FROM customer_master WHERE 
									customer_code='".$customer_code_del_prev_array[$i]."')";
						$rscustomerchk=mysql_query($sqlcustomerchk);
						while($rowcustomerchk=mysql_fetch_array($rscustomerchk))
						{
							$customer_code_prev_string.="'".$rowcustomerchk['customer_code']."'".',';
						}
						$customer_code_prev_string=substr($customer_code_prev_string,0,-1);
						$sqlcustomerroutechk="SELECT customer_code FROM customer_route_emp_relation WHERE customer_code IN(".$customer_code_prev_string.")";
						$rscustomerroutechk=mysql_query($sqlcustomerroutechk);
						$rowcustomerroutechk=mysql_fetch_array($rscustomerroutechk);
						$customer_code_present=$rowcustomerroutechk['customer_code'];
						
						$sqlupdateorder="UPDATE order_header SET customer_code='".$customer_code_present."' 
											WHERE customer_code='".$customer_code_del_prev_array[$i]."'";
						mysql_query($sqlupdateorder);
						$sqlupdateprevordercounting="UPDATE prev_order_counting_master SET customer_code='".$customer_code_present."' 
											WHERE customer_code='".$customer_code_del_prev_array[$i]."'";
						mysql_query($sqlupdateprevordercounting);					
						$sqlupdatepaymentheader="UPDATE payment_header SET customer_code='".$customer_code_present."' 
											WHERE customer_code='".$customer_code_del_prev_array[$i]."'";
						mysql_query($sqlupdatepaymentheader);
						
						$sqlupdatestockaudit="UPDATE stock_audit SET customer_code='".$customer_code_present."' 
											WHERE customer_code='".$customer_code_del_prev_array[$i]."'";
						mysql_query($sqlupdatestockaudit);
						
						$sqlupdateroutecustomerplan="UPDATE route_customer_plan SET customer_code='".$customer_code_present."' 
											WHERE customer_code='".$customer_code_del_prev_array[$i]."'";
						mysql_query($sqlupdateroutecustomerplan);
						
						$sqlupdatemarketfeedback="UPDATE mf_stk_audit_header SET customer_code='".$customer_code_present."' 
											WHERE customer_code='".$customer_code_del_prev_array[$i]."'";
						mysql_query($sqlupdatemarketfeedback);
						
						$sqlupdatemarketfeedbackone="UPDATE market_feedback SET customer_code='".$customer_code_present."' 
											WHERE customer_code='".$customer_code_del_prev_array[$i]."'";
						mysql_query($sqlupdatemarketfeedbackone);
						
						$sqlupdateyellowcard="UPDATE yellow_card_details SET customer_code='".$customer_code_present."' 
											WHERE customer_code='".$customer_code_del_prev_array[$i]."'";
						mysql_query($sqlupdateyellowcard);
						
						$sqlupdatecompetitorpricing="UPDATE competitor_pricing SET customer_code='".$customer_code_present."' 
											WHERE customer_code='".$customer_code_del_prev_array[$i]."'";
						mysql_query($sqlupdatecompetitorpricing);
						
						$sqlupdatecompetitorstock="UPDATE competitor_stock SET customer_code='".$customer_code_present."' 
											WHERE customer_code='".$customer_code_del_prev_array[$i]."'";
						mysql_query($sqlupdatecompetitorstock);
						
						$selcustomerroute="SELECT customer_code FROM customer_route_emp_relation WHERE 
											customer_code='".$customer_code_present."' AND route_code='".$route_code_del_prev_array[$i]."' 
											AND emp_code='".$emp_code_del_prev_array[$i]."'";
						$rscustomerroute=mysql_query($selcustomerroute);
						$cntcustomerroute=mysql_num_rows($rscustomerroute);
						if($cntcustomerroute ==0)
						{	
							$sqlinsertcustomerroutedel="INSERT INTO customer_route_emp_relation SET 
													customer_code='".$customer_code_present."',
													route_code='".$route_code_del_prev_array[$i]."',
													emp_code='".$emp_code_del_prev_array[$i]."',
													acedns='Y',
													download_time=CURRENT_TIMESTAMP()";
							mysql_query($sqlinsertcustomerroutedel);									
						}
						$sqldelpevcustomer="DELETE FROM customer_master WHERE customer_code='".$customer_code_del_prev_array[$i]."'";
						mysql_query($sqldelpevcustomer);
						$sqlcustomerrefference="INSERT INTO customer_refference SET customer_replaced='".$customer_code_del_prev_array[$i]."',
													customer_replaced_by='".$customer_code_present."'";
						mysql_query($sqlcustomerrefference);					
					}
			echo $successval=1;
			exit();
?>
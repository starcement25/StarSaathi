<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];
$newpassword=$_REQUEST['newpassword'];
$deviceid=$_REQUEST['deviceid'];

$sqlquery="select employee_master.emp_code,employee_master.emp_name,employee_master.sale_access,changepassword.newpassword,
		   changepassword.deviceid,employee_master.acedns from employee_master,changepassword where employee_master.emp_code=changepassword.emp_code 
			and changepassword.newpassword='".$newpassword."' and changepassword.emp_code='".$emp_code."'";
$result = mysql_query($sqlquery);
$count=mysql_num_rows($result);

	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
		while($rowsemp = mysql_fetch_array($result))
		{
			$sqlselect="SELECT is_licensed FROM changepassword WHERE emp_code='".$emp_code."'";
			$rsselect=mysql_query($sqlselect);
			$rowselect=mysql_fetch_array($rsselect);
			$is_licensed=$rowselect['is_licensed'];
			$device_id_database=$rowsemp['deviceid'];
			$acedns=$rowsemp['acedns'];
			
			//echo 'notdb'.$deviceid;
			//echo 'db'.$device_id_database;
			/*if($nick_name=='STAR')
			{
				echo 'NOT LICENSED USER';
			}
			else
			{*/
			if(strtoupper($acedns)=='Y'){
				if($deviceid=='')
				{
					echo '6';
				}
				else
				{
					if($deviceid!=$device_id_database) //Checking the posted deviceid and the database existed deviceid  is same or not
	  				{
						if($device_id_database=='')     // Checking that the database existed deviceid is blank or not
		 				{
							$sqlchkdeviceid="SELECT EM.emp_name from employee_master EM,changepassword CH where 
							EM.emp_code=CH.emp_code AND CH.deviceid='".$deviceid."'";
							$rschkdeviceid=mysql_query($sqlchkdeviceid);
							$cntchkdeviceid=mysql_num_rows($rschkdeviceid);
							if($cntchkdeviceid>0) // Checking that the POST data deviceid is already existed on the database for different employee code or not
							{
							   $rowchkdeviceid=mysql_fetch_array($rschkdeviceid);
							   $emp_name=$rowchkdeviceid['emp_name'];
							   echo '4'.'/'.$emp_name;
							 }
							 else
							 {
								$sqlUpdate="UPDATE changepassword SET deviceid='".$deviceid."' WHERE emp_code='".$emp_code."'";
								 if(mysql_query($sqlUpdate)){
									$sqlupdatetablestructure="UPDATE table_structure_updation SET emp_code='".$emp_code."' WHERE 
																device_id='".$deviceid."' AND emp_code=''";
									mysql_query($sqlupdatetablestructure);							
									$i = 0; 
									$contents.="<data>";
									while ($i < mysql_num_fields($result)) { 
										$meta = mysql_fetch_field($result, $i);
										$contents .='<'.$meta->name.'><![CDATA['.mb_convert_encoding($rowsemp[$meta->name], 'UTF-8', 'UTF-8').']]></'.$meta->name.'>';
										$i = $i + 1; 
										}
									$contents.="</data>";
									$contents .= "</recordset>";			
									echo $contents;
								}
								else
								{
									echo '0';
								}
							 }
						}//End of IF for device id database blank
						else
						{
							echo '0';
						}
					}
					else
					{
						$i = 0; 
						$sqlupdatetablestructure="UPDATE table_structure_updation SET emp_code='".$emp_code."' WHERE 
												device_id='".$deviceid."' AND emp_code=''";
						mysql_query($sqlupdatetablestructure);
						$contents.="<data>";
						while ($i < mysql_num_fields($result)) { 
							$meta = mysql_fetch_field($result, $i);
							$contents .='<'.$meta->name.'><![CDATA['.mb_convert_encoding($rowsemp[$meta->name], 'UTF-8', 'UTF-8').']]></'.$meta->name.'>';
							$i = $i + 1; 
							}
						$contents.="</data>";
						$contents .= "</recordset>";			
						echo $contents;
					}
				}
			} //End of Licensed User Checking IF
			else
			{
				echo 'NOT LICENSED USER';
			}
		  //}
		}// End of While
	}//End of Valid User Checking IF
	else
	{
	  echo 'NOT VALID USER';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/emplogin-check-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&deviceid=$deviceid&newpassword=$newpassword";
	insertapilog($datetime,$emp_code,$url,$nick_name);
mysql_close($link);
?>

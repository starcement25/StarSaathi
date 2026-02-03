<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
	$nick_name=strtoupper($_REQUEST['nick_name']);
	$mode=$_REQUEST['mode'];
	define("SERVER","localhost");
	define("USER","starsaat_dnsprod");
	define("PASSWORD","dnsprod1234#");
	define("APICALLLOGURL","http://starsaathi.com/");
	
	$linkdbaccess=mysql_connect(SERVER,USER,PASSWORD) or die("Setup Database Connection Error.");
	mysql_select_db("starsaat_acednsproduct",$linkdbaccess) or die("could not connect the setup database");
	/*if($nick_name=='ABDOS')
	{
		echo '0'.'¥'.'0';
		exit();
	}*/
	$sqldbaccessdetails="SELECT remote_db_access FROM user_details WHERE nick_name='".$nick_name."'";
	$rsdbaccessdetails=mysql_query($sqldbaccessdetails,$linkdbaccess);
	$rowdbaccessdetails=mysql_fetch_array($rsdbaccessdetails);
	$remote_db_access=$rowdbaccessdetails['remote_db_access'];
	mysql_close($linkdbaccess);
	if($remote_db_access=='yes' && $mode!='SETUP')
	{
		define("SERVERREMOTE","52.66.101.239");
		define("USERREMOTE","root");
		define("PASSWORDREMOTE","cmcl@123");
		define("DBREMOTE","acedns_$nick_name");
	}
	else
	{
		if($mode=='SETUP')
		{
			define("DB","starsaat_acednsproduct");
		}
		else
		{
			define("DB","starsaat_$nick_name");
		}
		define("URL","http://acedns.in/acednsproduct");

		//define("CSS","http://localhost/shopnshop/css/style.css");	
	}
	
	function insertapilog($datetime,$emp_code,$url,$nick_name)
	{
		mysql_select_db("starsaat_".$nick_name);
		$sqlinsertapilog="INSERT INTO apicalllog SET date_time=CURRENT_TIMESTAMP,
						  emp_code='".$emp_code."',
						  url='".$url."'";
		mysql_query($sqlinsertapilog);				  
	}
	function modifyempdatadownloadlog($emp_code,$nick_name)
	{
		mysql_select_db("starsaat_".$nick_name);
		if($emp_code=='')
		{
			$sqlallemp="SELECT emp_code FROM employee_master WHERE acedns <> 'N'";
			$rsallemp=mysql_query($sqlallemp);
			while($rowallemp=mysql_fetch_array($rsallemp))
			{
				$emp_code_all=$rowallemp['emp_code'];
				$sqlemplogchk="SELECT emp_code,is_download FROM emp_data_download_log WHERE emp_code='".$emp_code_all."'";
				$rsemplogchk=mysql_query($sqlemplogchk);
				$countemplogchk=mysql_num_rows($rsemplogchk);
				if($countemplogchk<1)
				{
					$sqlinsertemplog  = "INSERT INTO emp_data_download_log SET ";
					$sqlinsertemplog .= "  	emp_code='".mysql_real_escape_string($emp_code_all)."'";
					$sqlinsertemplog .= " , is_download='yes'";
					$sqlinsertemplog .= " , is_download_time=CURRENT_TIMESTAMP()";
					mysql_query($sqlinsertemplog);
				}
				else
				{
					$rowemplogchk=mysql_fetch_array($rsemplogchk);
					$is_download=$rowemplogchk['is_download'];
					if($is_download=='no')
					{
						$sqlupdateemplog="UPDATE emp_data_download_log SET is_download='yes',
										is_download_time=CURRENT_TIMESTAMP() WHERE emp_code='".$emp_code_all."'";
						mysql_query($sqlupdateemplog);	
					}
				}
			}
		}
		else
		{
			$sqlemplogchk="SELECT emp_code,is_download FROM emp_data_download_log WHERE emp_code='".$emp_code."'";
			$rsemplogchk=mysql_query($sqlemplogchk);
			$countemplogchk=mysql_num_rows($rsemplogchk);
			if($countemplogchk<1)
			{
				$sqlinsertemplog  = "INSERT INTO emp_data_download_log SET ";
				$sqlinsertemplog .= "  	emp_code='".mysql_real_escape_string($emp_code)."'";
				$sqlinsertemplog .= " , is_download='yes'";
				$sqlinsertemplog .= " , is_download_time=CURRENT_TIMESTAMP()";
				mysql_query($sqlinsertemplog);
			}
			else
			{
				$rowemplogchk=mysql_fetch_array($rsemplogchk);
				$is_download=$rowemplogchk['is_download'];
				if($is_download=='no')
				{
					$sqlupdateemplog="UPDATE emp_data_download_log SET is_download='yes',
									is_download_time=CURRENT_TIMESTAMP() WHERE emp_code='".$emp_code."'";
					mysql_query($sqlupdateemplog);	
				}
			}
		}
	}
	function returndatarefresh($emp_code)
	{
		if($emp_code!='')
		{
			$sqlempdownloadchk="SELECT is_download FROM emp_data_download_log WHERE emp_code='".$emp_code."'";
			$rsempdownloadchk=mysql_query($sqlempdownloadchk);
			$rowempdownloadchk=mysql_fetch_array($rsempdownloadchk);
			$is_download=$rowempdownloadchk['is_download'];
			$countempdownloadchk=mysql_num_rows($rsempdownloadchk);
			if($is_download=='yes')
			{
				$datarefresh=2;
			}
			else
			{
				$datarefresh=0;
			}
		}
		else
		{
			$datarefresh=0;
		}
		return $datarefresh;
   }
?>

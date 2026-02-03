<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$sqlselbusinessname="SELECT mall_id,business_name,DCE_status FROM foot_soldier";
$rsselbusinessname=mysql_query($sqlselbusinessname);
while($rowselbusinessname=mysql_fetch_array($rsselbusinessname))
{
	$foot_sol_mall_id=$rowselbusinessname['mall_id'];
	$foot_sol_business_name=$rowselbusinessname['business_name'];
	$foot_sol_DCE_status=$rowselbusinessname['DCE_status'];
	$sqlselsurveyheader="SELECT survey_id FROM survey_header WHERE mall_id='".$foot_sol_mall_id."' AND business_name='".addslashes($foot_sol_business_name)."'";
	$rsselsurveyheader=mysql_query($sqlselsurveyheader);
	$cntselsurveyheader=mysql_num_rows($rsselsurveyheader);
	if($cntselsurveyheader==0)
	{
		$sqlupdate="UPDATE foot_soldier SET DCE_status='NOT DONE',download_time=CURRENT_TIMESTAMP() WHERE 
					business_name='".addslashes($foot_sol_business_name)."' and mall_id='".$foot_sol_mall_id."'";
		$rsupdate=mysql_query($sqlupdate);
	}
	/*else
	{
		if($foot_sol_DCE_status =='NOT DONE')
		{
			$sqlupdate="UPDATE foot_soldier SET DCE_status='DONE',download_time=CURRENT_TIMESTAMP() WHERE 
					business_name='".$foot_sol_business_name."' and mall_id='".$foot_sol_mall_id."'";
		    $rsupdate=mysql_query($sqlupdate);
		}
	}*/
}
?>

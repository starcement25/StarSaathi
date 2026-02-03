<?php
ob_start();
	session_start();
	if(strtoupper($_SESSION['nick_name']) == 'STAR' && (strtoupper($_SESSION['admin_login']) == 'ACCOUNTS' || strtoupper($_SESSION['admin_login']) == 'E0674'))
	{
		require("adminUtils_accounts.php");
	}
	else
	{
		require("adminUtils.php");
	}
	if($_SESSION['admin_login'] == '')   		header('location: index.php');

	disphtml("main();");

ob_end_flush();
function main()
{
	if(strtoupper($_SESSION['nick_name']) == 'STAR')
		$mis_url = "admin_mis_report_star.php";
	else if(strtoupper($_SESSION['nick_name']) == 'CASHLESS')
		$mis_url = "adminMisReportSurvey.php";
	else if(strtoupper($_SESSION['nick_name']) == 'HALDIRAM')
		$mis_url = "adminMisReportEmphierarchy.php";
	else
		$mis_url = "adminMisReport.php";
?>
    <table width="40%" align="center" border="0" cellpadding="0" cellspacing="0">
        <tr>
        	
            <td  valign="middle"   class="butterfly" id="fly2"><a href="<?php echo $mis_url; ?>" style="cursor:pointer;"><img src="images/mis.png" alt="" /></a></td>
            
            <td width="">
            	<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0">
                	<tr>
                    	<td  valign="top"  class="butterfly" id="fly1"><a href="adminAttendanceTracker.php" style="cursor:pointer;"><img src="images/attendance-tracker.png" alt="" /></a></td>
                    </tr>
                    <tr>
                    	
                        <td  valign="top"  class="butterfly" id="fly2"><a href="adminActivity.php" style="cursor:pointer;"><img src="images/activity.png" alt="" /></a></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
<? 
}
?>


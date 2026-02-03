<?php
require("include/config.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
//$emp_code='E0924';
$mode=$_REQUEST['mode'];
$page=$_REQUEST['page'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
if($mode=='MTD')
{
	$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE()) AND MONTH(LO.date) = MONTH(CURDATE())  AND DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') <=DATE_FORMAT(NOW(),'%Y%-%m-%d')";
}
else if($mode=='YTD')
{
	$date_condition=" AND YEAR(LO.date) = YEAR(CURDATE())";
}
else if($mode == 'yourchoice'){
	$date_condition = " AND (DATE_FORMAT(SUBSTRING(LO.trans_id,-14,8),'%Y%-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."') ";
}
else
{
	$date_condition="";
}
	
$sqlemp="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
$rsemp=mysql_query($sqlemp) or die(mysql_error()." Error in select employee name and code : ".$sqlemp);
$rowemp=mysql_fetch_array($rsemp);
$emp_name=$rowemp['emp_name'];
?>
<style type="text/css">
.TDHEAD{
	FONT-FAMILY: Verdana;
	FONT-SIZE : 11px;
	FONT-WEIGHT: bold;
	COLOR: #FFFFFF;
	BACKGROUND-COLOR: #A92A61;/*#92C006;*/
}

.TDHEAD_SUB{
	FONT-FAMILY: Verdana;
	FONT-SIZE : 11px;
	FONT-WEIGHT: bold;
	BACKGROUND-COLOR:#c0c8b0;
}

.border{
	BORDER: #A92A61/*#80A537*/ 1px solid;
}
TD{
	FONT-FAMILY: Verdana;
	FONT-SIZE : 11px;
}

</style>
<?php 
$tableval='
<table width="57%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" style="height: 250px;overflow-y: scroll;display:block;">
    <tr class="TDHEAD" > 
        <td colspan="5" align="center"><strong>Emp Name: '.$emp_name.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Emp Code:'. $emp_code.'</strong></td>
    </tr>
    <tr class="TDHEAD_SUB"> 
        <td width="5%" align="center">Sl</td>
        <td width="30%" align="left" style="padding-left:20px;">Date</td>
        <td width="30%" align="left" style="padding-left:20px;">Check-In Time</td>
		 <td width="30%" align="left" style="padding-left:20px;">Check-Out Time</td>
        <td width="" align="left" style="padding-left:20px;">Locate</td>
    </tr> ';
        
    $sqlinformation="SELECT DATE_FORMAT(date,'%T') AS time,DATE_FORMAT(date,'%b,%e %Y') AS date,trans_id,DATE_FORMAT(date,'%Y-%m-%d') AS dateformat FROM 
                    location LO WHERE LO.trans_id LIKE 'A%' AND LO.emp_code='".$emp_code."' ".$date_condition." ORDER BY DATE_FORMAT(date,'%Y-%m-%d') DESC ";
    $resinformation=mysql_query($sqlinformation) or die(mysql_error()." Error in select transaction information: ".$sqlinformation);
    $count=mysql_num_rows($resinformation);
        $cnt=$GLOBALS[start]+1;
        while($rowinformation=mysql_fetch_array($resinformation))
        {
            $trans_id=$rowinformation['trans_id'];
            $time=$rowinformation['time'];
            $date=$rowinformation['date'];
			$dateformat=$rowinformation['dateformat'];
			$sql_checkout = "SELECT SUBSTRING(LO.date,12) AS checkout_time FROM location LO WHERE 
							LO.emp_code = '".$emp_code."' AND LO.trans_id LIKE 'CH%' AND SUBSTRING(LO.date,1,10)='".$dateformat."'";
			$res_checkout = mysql_query($sql_checkout);
			$row_checkout = mysql_fetch_array($res_checkout);
			$check_out_time = $row_checkout['checkout_time'];
			if($check_out_time == '')
			$check_out_time = '--';

			$rowval.='<tr> 
                    <td valign="top" align="center">'.$cnt++.'</td>
                    <td align="left" valign="top" style="padding-left:20px;">'.$date.'</td>
                    <td align="left" valign="top" style="padding-left:20px;">'.$time.'</td>
					<td align="left" valign="top" style="padding-left:20px;">'.$check_out_time.'</td>
                    <td align="left" valign="top" style="padding-left:20px;"><a href="adminAttendanceLocate.php?trans_id='.$trans_id.'&emp_code='.$emp_code.'&radio_search=employee&mode='.$mode.'&page='.$page.'&start_date='.$start_date.'&end_date='.$end_date.'" style="color:#930;font-weight:bold;">Locate</a></td>
              </tr>';
        }
$tablevalend='</table>';				
$finalval=$tableval.$rowval.$tablevalend;

echo $finalval;

mysql_close($link);
?>
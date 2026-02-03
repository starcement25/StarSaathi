<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");

function getReverseGeo($latitude,$longitude)
{
	// format this string with the appropriate latitude longitude
	$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true";
	// make the HTTP request
	$data = @file_get_contents($url);
	// parse the json response
	$jsondata = json_decode($data,true);
	
	//print_r($jsondata);
	// if we get a formatted_address array and the status was OK, get the addres
	if(is_array($jsondata )&& $jsondata['status']=='OK')
	{
		  $addr = $jsondata['results']['0']['formatted_address'];
	}		
	return  $addr;	
}

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

if($nick_name=='AMPL' || $nick_name=='TT')
{
  $spam_filter='-facedns@coral.in';
}
else
{
  $spam_filter='-facedns@acedns.in';
}
$body=file_get_contents('php://input');

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><NOTES_INFO><location><emp_code><![CDATA[E0096]]></emp_code><trans_id><![CDATA[NIE009620160418160521]]></trans_id><latt><![CDATA[22.5643652]]></latt><longi><![CDATA[88.3568814]]></longi><date><![CDATA[2016-04-18 16:05:21]]></date></location>
<NOTES_INFO_DATA><NOTES_INFO_ID><![CDATA[NIE009620160418160521]]></NOTES_INFO_ID><FEEDBACK><![CDATA[vygvgvgv]]></FEEDBACK></NOTES_INFO_DATA></NOTES_INFO><NOTES_INFO><location><emp_code><![CDATA[E0096]]></emp_code><trans_id><![CDATA[NIE009620160418160804]]></trans_id><latt><![CDATA[22.5643697]]></latt><longi><![CDATA[88.3568751]]></longi><date><![CDATA[2016-04-18 16:08:04]]></date></location><NOTES_INFO_DATA><NOTES_INFO_ID><![CDATA[NIE009620160418160804]]></NOTES_INFO_ID><FEEDBACK><![CDATA[HV yg gbgb]]></FEEDBACK></NOTES_INFO_DATA></NOTES_INFO><NOTES_INFO><location><emp_code><![CDATA[E0096]]></emp_code><trans_id><![CDATA[NIE009620160418161036]]></trans_id><latt><![CDATA[22.5643596]]></latt><longi><![CDATA[88.3568777]]></longi><date><![CDATA[2016-04-18 16:10:36]]></date></location><NOTES_INFO_DATA><NOTES_INFO_ID><![CDATA[NIE009620160418161036]]></NOTES_INFO_ID><FEEDBACK><![CDATA[fctftvtg]]></FEEDBACK></NOTES_INFO_DATA></NOTES_INFO></root>";*/

/*$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysql_query($sqlinsert_xml_data);*/
	
$location_emp_code="*ROOT*REQUISITION_DETAILS*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*REQUISITION_DETAILS*LOCATION*TRANS_ID";
$location_latt = "*ROOT*REQUISITION_DETAILS*LOCATION*LATT";
$location_longi = "*ROOT*REQUISITION_DETAILS*LOCATION*LONGI";
$location_date="*ROOT*REQUISITION_DETAILS*LOCATION*DATE";

$allocation_id = "*ROOT*REQUISITION_DETAILS*REQUISITION_DATA*ALLOCATION_ID";
$prod_code="*ROOT*REQUISITION_DETAILS*REQUISITION_DATA*PROD_CODE";
$allot_qty="*ROOT*REQUISITION_DETAILS*REQUISITION_DATA*ALLOT_QTY";
$requisition_id="*ROOT*REQUISITION_DETAILS*REQUISITION_DATA*REQUISITION_ID";
$requisition_qty="*ROOT*REQUISITION_DETAILS*REQUISITION_DATA*REQUISITION_QTY";

$requisition_array=array();
$requisition_details_array=array();

$counter = 0;
$counterrequisition=0;
class xml_requisition{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date;	
}
class xml_requisition_details{
	var $allocation_id,$prod_code,$allot_qty,$requisition_id,$requisition_qty;
}
function startTag($parser, $data){
    global $current_tag;
    $current_tag .= "*$data";
}

function endTag($parser, $data){
    global $current_tag;
    $tag_key = strrpos($current_tag, '*');
    $current_tag = substr($current_tag, 0, $tag_key);
}

function contents($parser, $data){
    global $current_tag,$counter,$counterrequisition,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$allocation_id,$prod_code,$allot_qty,$requisition_id,$requisition_qty,$requisition_array,$requisition_details_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,25)=='*ROOT*REQUISITION_DETAILS')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$requisition_array[$counter] = new xml_requisition();
				$requisition_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$requisition_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$requisition_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$requisition_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$requisition_array[$counter]->location_date = $data;
				$counter++;
				break;
		}
	}
	if(substr($current_tag,0,42)=='*ROOT*REQUISITION_DETAILS*REQUISITION_DATA')
	 {
			//echo $current_tag.'<br />';
			//echo $data.'<br />';
			switch($current_tag){
				case $allocation_id:
					$requisition_details_array[$counterrequisition] = new xml_requisition_details();
					$requisition_details_array[$counterrequisition]->allocation_id = $data;
					break;
				case $prod_code:
					$requisition_details_array[$counterrequisition]->prod_code = $data;
					break;
				case $allot_qty:
					$requisition_details_array[$counterrequisition]->allot_qty = $data;
					break;
				case $requisition_id:
					$requisition_details_array[$counterrequisition]->requisition_id = $data;
					break;
				case $requisition_qty:
					$requisition_details_array[$counterrequisition]->requisition_qty = $data;
					$counterrequisition++;
					break;
		 }
	}
}
$xml_parser = xml_parser_create();
xml_set_element_handler($xml_parser, "startTag", "endTag");
xml_set_character_data_handler($xml_parser, "contents");
$data = $body;

if(!(xml_parse($xml_parser, $data, LIBXML_PARSEHUGE))){
    die("Error on line " . xml_get_current_line_number($xml_parser));
}
xml_parser_free($xml_parser);
mysql_query("SET AUTOCOMMIT=0");
mysql_query("START TRANSACTION");

$flag=1;
/* --------------------START QUERY FOR REQUISITION ------------------------------------------------------------------------------------------*/
//print_r($notes_info_array);
$requtisition_array_trans_id=array();
if(count($requisition_array)>0)
{
		for($x=0;$x<count($requisition_array);$x++){
			$location_emp_code=$requisition_array[$x]->location_emp_code;
			$location_trans_id=$requisition_array[$x]->location_trans_id;
			$location_latt=$requisition_array[$x]->location_latt;
			$location_longi=$requisition_array[$x]->location_longi;
			$location_date=$requisition_array[$x]->location_date;
			
			//For updating the lattitude  and longitude for those records whose lattitude and longitude are zero for the particular employee
			if($location_latt>0 && $location_longi>0)
			{
				$sqlupdatelatlongzero="UPDATE location SET latt='".$location_latt."',longi='".$location_longi."' WHERE 
										emp_code='".$location_emp_code."' AND latt='0' AND longi='0'";
				$resupdatelatlongzero= mysql_query($sqlupdatelatlongzero) or die(mysql_error()." Error in update location with lattslongi zero: ".$sqlupdatelatlongzero); 
			}
	
			//For checking that trans id exist or not for notes info
			$sqlchkorlocation="SELECT * FROM location WHERE trans_id='".$location_trans_id."'";
			$reschkorlocation = mysql_query($sqlchkorlocation) or die(mysql_error()." Error in check notes info: ".$sqlchkorlocation); 
			$rowchkorlocation = mysql_fetch_array($reschkorlocation);
			$countchkorlocation=mysql_num_rows($reschkorlocation);
			
			//For update the location table for existing trans id for notes info
			if($countchkorlocation>0)
			{
				if(!in_array($location_trans_id,$requtisition_array_trans_id))
				{
					array_push($requtisition_array_trans_id,$location_trans_id);
				}
				$sqlupdateorlocation="UPDATE location SET emp_code='".$location_emp_code."',
										latt='".$location_latt."',
										longi='".$location_longi."'
										WHERE trans_id='".$location_trans_id."'";
				$rsupdateorlocation=mysql_query($sqlupdateorlocation) or die(mysql_error()." Error in update notes info location: ".$sqlupdateorlocation);
				if($rsupdateorlocation)
				{
					$flag=6;
				}
				else
				{
					echo $flag=0;
				}
			}
			else
			{
			//Creation of code random no parameter
			$sqlempname="SELECT emp_name,branch_code,vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
			$rsempname=mysql_query($sqlempname);
			$rowempname=mysql_fetch_array($rsempname);
			$emp_name=$rowempname['emp_name'];
			$branch_code=$rowempname['branch_code'];
			$vertical_value=$rowempname['vertical_value'];
			
			$random_no_length=7-strlen($nick_name);//7 is the maximum length of the company nick name
			$foldernamerand=$nick_name.rand(pow(10, $random_no_length-1), pow(10, $random_no_length)-1);
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));
	
			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			$location_date_updatetime=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
	
			//For Insert into the location table for new trans id regarding market feedback stock audit
			$sqlinsertorlocation="INSERT INTO location SET emp_code='".$location_emp_code."',
								trans_id='".$location_trans_id."',
								latt='".$location_latt."',
								longi='".$location_longi."',
								date='".$location_date."',
								updatetime='".$location_date_updatetime."'"; 
		  if(mysql_query($sqlinsertorlocation))
			{
				$flag=5;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo $flag=0;
				return;
			}
		}
	 }//End of for loop
	 
	 if(count($requisition_details_array)>0)
		{
			for($i=0;$i<count($requisition_details_array);$i++){
				$allocation_id=$requisition_details_array[$i]->allocation_id;
				$prod_code=$requisition_details_array[$i]->prod_code;
				$allot_qty=$requisition_details_array[$i]->allot_qty;
				$requisition_id=$requisition_details_array[$i]->requisition_id;
				$requisition_qty=$requisition_details_array[$i]->requisition_qty;
					
				if(!in_array($requisition_id,$requtisition_array_trans_id))
				{
					$sqlinsertrequisition="INSERT INTO requisition_details SET allocation_id ='".$allocation_id."',
										  prod_code 		    ='".$prod_code."',
										  allot_qty 			='".$allot_qty."',
										  requisition_id 		='".$requisition_id."',
										  requisition_qty 		='".$requisition_qty."'";											  
					if(mysql_query($sqlinsertrequisition))
					{
						$flag=5;
					}
					else
					{
						mysql_query("ROLLBACK");
						echo $flag=0;
						return;
					}
				}
			}
		}
}
 /* --------------------END QUERY FOR RQUISITION--------------------------------------------------------------------------------------------------------*/
if($flag==5)
{
	 $sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	 $rsupdatelastoperationtime=mysql_query($sqlupdatelastoperationtime);	 
	 mysql_query("COMMIT");
	 echo $flag=1;
}
if($flag==6)
{
     $sqlupdatelastoperationtime="UPDATE changepassword SET last_operation_datetime='".$last_operation_datetime."' WHERE emp_code='".$emp_code."'";
	 $rsupdatelastoperationtime=mysql_query($sqlupdatelastoperationtime);	 
	 mysql_query("COMMIT");
	 echo $flag=1;
}
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url =APICALLLOGURL."/operationdb-requisition-details.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysql_close($link);
?>

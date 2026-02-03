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

/*$body="<?xml version='1.0' encoding='UTF-8'?><root><REQUISITION_DETAILS><location><emp_code><![CDATA[E0096]]></emp_code><trans_id><![CDATA[RDE009620160418160521]]></trans_id><latt><![CDATA[22.5643652]]></latt><longi><![CDATA[88.3568814]]></longi><date><![CDATA[2016-04-18 16:05:21]]></date></location><REQUISITION_DATA><ALLOCATION_ID><![CDATA[NIE009620160418160521]]></ALLOCATION_ID><PROD_CODE><![CDATA[vygvgvgv]]></PROD_CODE>
<ALLOT_QTY><![CDATA[vygvgvgv]]></ALLOT_QTY><REQUISITION_ID><![CDATA[vygvgvgv]]></REQUISITION_ID><REQUISITION_QTY><![CDATA[vygvgvgv]]></REQUISITION_QTY></REQUISITION_DATA></REQUISITION_DETAILS></root>";*/

/*$body_xml=str_replace("'",'"',$body);
	$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
							xml='".$body_xml."',
							insertdate=CURRENT_TIMESTAMP()";
	mysql_query($sqlinsert_xml_data);*/
	
$location_emp_code="*ROOT*STOCK_OUT_DETAILS*LOCATION*EMP_CODE";
$location_trans_id = "*ROOT*STOCK_OUT_DETAILS*LOCATION*TRANS_ID";
$location_latt = "*ROOT*STOCK_OUT_DETAILS*LOCATION*LATT";
$location_longi = "*ROOT*STOCK_OUT_DETAILS*LOCATION*LONGI";
$location_date="*ROOT*STOCK_OUT_DETAILS*LOCATION*DATE";

$stock_out_id = "*ROOT*STOCK_OUT_DETAILS*STOCK_OUT_DATA*STOCK_OUT_ID";
$prod_code="*ROOT*STOCK_OUT_DETAILS*STOCK_OUT_DATA*PROD_CODE";
$IMEI="*ROOT*STOCK_OUT_DETAILS*STOCK_OUT_DATA*IMEI";
$stock_out_date="*ROOT*STOCK_OUT_DETAILS*STOCK_OUT_DATA*STOCK_OUT_DATE";

$stock_out_array=array();
$stock_out_details_array=array();

$counter = 0;
$counterstockout=0;
class xml_stock_out{
	var $location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date;	
}
class xml_stock_out_details{
	var $stock_out_id,$prod_code,$IMEI,$stock_out_date;
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
    global $current_tag,$counter,$counterstockout,$location_emp_code,$location_trans_id,$location_latt,$location_longi,$location_date,$stock_out_id,$prod_code,$IMEI,$stock_out_date,$stock_out_array,$stock_out_details_array;
	//echo $current_tag.'<br />';
	//echo $data.'<br />';
	if(substr($current_tag,0,23)=='*ROOT*STOCK_OUT_DETAILS')
	{
		//echo $current_tag.'<br />';
		//echo $data.'<br />';
		switch($current_tag){
			case $location_emp_code:
				$stock_out_array[$counter] = new xml_stock_out();
				$stock_out_array[$counter]->location_emp_code = $data;
				break;
			case $location_trans_id:
				$stock_out_array[$counter]->location_trans_id = $data;
				break;
			case $location_latt:
				$stock_out_array[$counter]->location_latt = $data;
				break;
			case $location_longi:
				$stock_out_array[$counter]->location_longi = $data;
				break;
			case $location_date:
				$stock_out_array[$counter]->location_date = $data;
				$counter++;
				break;
		}
	}
	if(substr($current_tag,0,38)=='*ROOT*STOCK_OUT_DETAILS*STOCK_OUT_DATA')
	 {
			//echo $current_tag.'<br />';
			//echo $data.'<br />';
			switch($current_tag){
				case $stock_out_id:
					$stock_out_details_array[$counterstockout] = new xml_stock_out_details();
					$stock_out_details_array[$counterstockout]->stock_out_id = $data;
					break;
				case $prod_code:
					$stock_out_details_array[$counterstockout]->prod_code = $data;
					break;
				case $IMEI:
					$stock_out_details_array[$counterstockout]->IMEI = $data;
					break;
				case $stock_out_date:
					$stock_out_details_array[$counterstockout]->stock_out_date = $data;
					$counterstockout++;
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
/* --------------------START QUERY FOR STOCK OUT ------------------------------------------------------------------------------------------*/
//print_r($notes_info_array);
$stock_out_array_trans_id=array();
if(count($stock_out_array)>0)
{
		for($x=0;$x<count($stock_out_array);$x++){
			$location_emp_code=$stock_out_array[$x]->location_emp_code;
			$location_trans_id=$stock_out_array[$x]->location_trans_id;
			$location_latt=$stock_out_array[$x]->location_latt;
			$location_longi=$stock_out_array[$x]->location_longi;
			$location_date=$stock_out_array[$x]->location_date;
			
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
				if(!in_array($location_trans_id,$stock_out_array_trans_id))
				{
					array_push($stock_out_array_trans_id,$location_trans_id);
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
	 
	 if(count($stock_out_details_array)>0)
		{
			for($i=0;$i<count($stock_out_details_array);$i++){
				
				$stock_out_id=$stock_out_details_array[$i]->stock_out_id;
				$prod_code=$stock_out_details_array[$i]->prod_code;
				$IMEI=$stock_out_details_array[$i]->IMEI;
				$stock_out_date=$stock_out_details_array[$i]->stock_out_date;
					
				if(!in_array($stock_out_id,$stock_out_array_trans_id))
				{
					$sqlinsertstockout="INSERT INTO stock_out_details SET stock_out_id ='".$stock_out_id."',
										  prod_code     ='".$prod_code."',
										  IMEI 			='".$IMEI."',
										  stock_out_date ='".$stock_out_date."'";											  
					if(mysql_query($sqlinsertstockout))
					{
						$flag=5;
						$sqlupdatestockout="UPDATE customer_product_billing SET stock_out_date='".$stock_out_date."' WHERE 
											prod_code   ='".$prod_code."' AND IMEI='".$IMEI."'";
						if(mysql_query($sqlupdatestockout))
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
 /* --------------------END QUERY FOR STOCK OUT--------------------------------------------------------------------------------------------------------*/
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
$url =APICALLLOGURL."/operationdb-stock_out-details.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
insertapilog($datetime,$emp_code,$url,$nick_name);
mysql_close($link);
?>

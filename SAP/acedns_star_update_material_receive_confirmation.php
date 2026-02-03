<?php
ini_set('memory_limit', '99999M');
set_time_limit(0);
include "star_connection.php";
include "function-sfa.php";
$t_apperpdo_temp = "T_APPERPDO_TEMP";
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$customer_master = "customer_master";
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";
$apporderno = $_REQUEST["apporderno"] ? addslashes(trim($_REQUEST["apporderno"])) : "";
$quantity_checking = $_REQUEST["quantity_checking"] ? addslashes(trim($_REQUEST["quantity_checking"])) : "";
$quality_checking = $_REQUEST["quality_checking"] ? addslashes(trim($_REQUEST["quality_checking"])) : "";
$remarks = $_REQUEST["remarks"] ? addslashes(trim($_REQUEST["remarks"])) : "";
if($the_id!="" && $apporderno!=""){
$sql3 = "select `dns_customer_code`,`customer_name`,`phone_no` from $customer_master where `customer_code`='$the_id'";
$res3 = mysql_query($sql3);
$totres3 = mysql_num_rows($res3);
if($totres3>0){
$row3 = mysql_fetch_assoc($res3);
$the_dealer_id = addslashes(trim($row3["dns_customer_code"]));
$the_dealer_name = trim($row3["customer_name"]);
$the_dealer_phone = trim($row3["phone_no"]);


//$sql34 = "select `APPORDERNO` from $t_apperpdo where `customer_code`='$the_id' and `APPORDERNO`='$apporderno'";
$sql34 = "select `APPORDERNO`,ERPORDERNO,order_date,dns_branch_code,dns_customer_code,consignee_name,consignee_address,freight,destination_name,prod_display_name,QTY from $t_apperpdo where `customer_code`='$the_id' and `APPORDERNO`='$apporderno'";
$res34 = mysql_query($sql34);
$totres34 = mysql_num_rows($res34);
if($totres34>0){
	$carr_datetime = date("Y-m-d H:i:s");
	
	//For Email Body
	$row34=mysql_fetch_array($res34);
	$ERPORDERNO = $row34['ERPORDERNO'];
	$order_date_formated = date('jS M, y',strtotime($row34['order_date']));
	$dns_branch_code = $row34['dns_branch_code'];
	$dns_customer_code = $row34['dns_customer_code'];
	$consignee_name = $row34['consignee_name'];
	$consignee_address = $row34['consignee_address'];
	$freight = $row34['freight'];
	$destination_name = $row34['destination_name'];
	$prod_display_name = $row34['prod_display_name'];
	$QTY = $row34['QTY'];
	$sqlcustname="SELECT customer_name FROM customer_master WHERE dns_customer_code='".$dns_customer_code."'";
	$rscustname=mysql_query($sqlcustname);
	$rowcustname=mysql_fetch_array($rscustname);
	$customer_name=$rowcustname['customer_name'];
	
	$sqlbranch = "select branch_name from branch_master where dns_branch_code='".$dns_branch_code."'";	
	$resbranch = mysql_query($sqlbranch);
	$rowbranch=mysql_fetch_array($resbranch);
	$branch_name=$rowbranch['branch_name'];
	
	$sqlchallanno = "select CHALLANNO from T_DOCHALLAN where APPORDERNO='".$apporderno."' AND ERPORDERNO='".$ERPORDERNO."' AND dns_customer_code='".$dns_customer_code."'";	
	$reschallanno = mysql_query($sqlchallanno);
	$rowchallanno=mysql_fetch_array($reschallanno);
	$CHALLANNO=$rowchallanno['CHALLANNO'];
	
$sql345 = "update $t_apperpdo set `is_confirmed_material_received`='YES',`quantity_checking`='$quantity_checking',`quality_checking`='$quality_checking',`remarks`='$remarks',`confirmed_material_received_datetime`='$carr_datetime' where `customer_code`='$the_id' and `APPORDERNO`='$apporderno'";
$res345 = mysql_query($sql345);
$sql3456 = "update $t_apperpdo_temp set `is_confirmed_material_received`='YES',`quantity_checking`='$quantity_checking',`quality_checking`='$quality_checking',`remarks`='$remarks',`confirmed_material_received_datetime`='$carr_datetime' where `customer_code`='$the_id' and `APPORDERNO`='$apporderno'";
$res3456 = mysql_query($sql3456);
	
$res_data = array("process_status"=>"YES","process_message"=>"Successfully updated.");

if($quantity_checking=="NOT OK" || $quality_checking=="NOT OK"){
define("SERVERREMOTE","103.242.119.68");
define("USERREMOTE","acedns_dnsprod");
define("PASSWORDREMOTE","dnsprod1234#");
define("DBREMOTE","acedns_STAR");
$link=mysql_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die("Database Connection Error.");
mysql_select_db(DBREMOTE,$link) or die("could not connect the database");

$all_email_arr = array();
$email_hierarchy='';
$sqldealeremp="SELECT emp_code FROM customer_route_emp_relation WHERE acedns='Y' AND customer_code='".$the_id."'";
$rsdealeremp=mysql_query($sqldealeremp,$link);
while($rowdealeremp=mysql_fetch_array($rsdealeremp))
{
$emp_code_db=$rowdealeremp['emp_code'];
$employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code_db);
//$emp_hierarchy_condition='c1.emp_code IN('.$employee_hierarchy.')';
$sqlemailhierarchy="SELECT email,designation FROM employee_master WHERE emp_code IN (".$employee_upper_hierarchy.") AND UPPER(sale_access)='PRIMARY' AND acedns='Y'";
$rsemailhierarchy=mysql_query($sqlemailhierarchy);
while($rowemailhierarchy=mysql_fetch_array($rsemailhierarchy))
{
$the_email_upp = $rowemailhierarchy['email'] ? trim($rowemailhierarchy['email']) : "";
if($the_email_upp!=""){
$all_email_arr[] = $the_email_upp;
}
}
}


$sqlbroker="SELECT broker_code FROM customer_broker_relation WHERE acedns='Y' AND customer_code='".$the_id."'";
$rsbroker=mysql_query($sqlbroker,$link);
while($rowbroker=mysql_fetch_array($rsbroker))
{
$sqlemailbroker="SELECT mail_id FROM broker_master WHERE broker_id='".$rowbroker['broker_code']."' AND acedns='Y'";
$rsemailbroker=mysql_query($sqlemailbroker,$link);
while($rowemailbroker=mysql_fetch_array($rsemailbroker))
{
$the_brok_email_upp = $rowemailbroker['mail_id'] ? trim($rowemailbroker['mail_id']) : "";
if($the_brok_email_upp!=""){
$all_email_arr[] = $the_brok_email_upp;
}
}
}

/*$all_email_arr[] = "dipankarc@coral.in";
$all_email_arr[] = "mridu@forcepower.in";
$all_email_arr[] = "suranjitd@coral.in";*/

if(count($all_email_arr)>0){
$final_email = implode(",",$all_email_arr);	

/*$final_email=substr($email_hierarchy,0,-1).','.substr($email_broker,0,-1).','.'dipankarc@coral.in'.','.'emovesfa@starcement.co.in'.','.'manishranjan@starcement.co.in';*/
$final_email=$final_email.','.'mriduj@coral.in'.','.'rohitd@forcepower.in'.','.'manishranjan@starcement.co.in';

$apporderno = $_REQUEST["apporderno"] ? addslashes(trim($_REQUEST["apporderno"])) : "";
$quantity_checking = $_REQUEST["quantity_checking"] ? addslashes(trim($_REQUEST["quantity_checking"])) : "";
$quality_checking = $_REQUEST["quality_checking"] ? addslashes(trim($_REQUEST["quality_checking"])) : "";
$remarks = $_REQUEST["remarks"] ? addslashes(trim($_REQUEST["remarks"])) : "";

		$subject = "Material Received Feedback from ".$the_dealer_name;
		/*$message= '<br><b>App Order No: </b> '.$apporderno.'<br>
					<b>Dealer Id: </b> '.$the_dealer_id.'<br>
					<b>Dealer Phone: </b> '.$the_dealer_phone.'<br>
					<b>Quantity Checking: </b> '.$quantity_checking.'<br>
					<b>Quality Checking: </b> '.$quality_checking.'<br>
					<b>Remarks: </b> '.$remarks.'<br>';*/
		$message= '<br><b>App Order No: </b> '.$apporderno.'<br>
			<b>ERP No: </b> '.$ERPORDERNO.'<br>
			<b>Challan no: </b> '.$CHALLANNO.'<br>
			<b>DATE: </b> '.$order_date_formated.'<br>
			<b>Branch Name: </b> '.$branch_name.'<br>
			<b>Customer Name: </b> '.$customer_name.'<br>
			<b>Consignee Name: </b> '.$consignee_name.'<br>
			<b>Consignee Address: </b> '.$consignee_address.'<br>
			<b>Phone No.: </b> '.$the_dealer_phone.'<br>
			<b>Freight: </b> '.$freight.'<br>
			<b>Destination: </b> '.$destination_name.'<br>
			<b>Product Name: </b> '.$prod_display_name.'<br>
			<b>Qty (MT): </b> '.$QTY.'<br>
			<b>Quantity issue: </b> '.$quantity_checking.'<br>
			<b>Quality issue: </b> '.$quality_checking.'<br>
			<b>Remarks: </b> '.$remarks.'<br>';			
		$send_mail = send_the_mail($final_email,$subject,$message);
	
}


}

}else{
$res_data = array("process_status"=>"NO","process_message"=>"You have no access to update this order details.");
}




}else{
$res_data = array("process_status"=>"NO","process_message"=>"Dealer record not found.");
}
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"The id is mandatory.");
}
echo json_encode($res_data);

?>
<?php
include "star_connection.php";
include "function-sfa.php";
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$customer_master = "customer_master";
$product_master = "product_master";
$broker_master = "broker_master";
function get_broker_name_from_id($cust_id){
	$broker_master = "broker_master";
	$custname = "";
	$cust_id = $cust_id ? addslashes(trim($cust_id)) : "";
	if($cust_id!=''){
		$sqls = "select `broker_name` from $broker_master where `dns_broker_id`='$cust_id'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if($totress>0){
			$rows = mysql_fetch_assoc($ress);
			$custname = $rows["broker_name"] ? trim($rows["broker_name"]) : "";
		}
	}
	return $custname;
}

function get_customer_name_from_id($cust_id){
	$customer_master = "customer_master";
	$custname = "";
	$cust_id = $cust_id ? addslashes(trim($cust_id)) : "";
	if($cust_id!=''){
		$sqls = "select `customer_name` from $customer_master where `customer_code`='$cust_id'";
		$ress = mysql_query($sqls);
		$totress = mysql_num_rows($ress);
		if($totress>0){
			$rows = mysql_fetch_assoc($ress);
			$custname = $rows["customer_name"] ? trim($rows["customer_name"]) : "";
		}
	}
	return $custname;
}

function fetch_emails_by_destination_code($the_des_code){
$email_str = "";
$email_array = array();
$email_array["branch_name"] = "";
$email_array["email"][] = "starsaathi@gmail.com";
$branch_destination_freight = "branch_destination_freight";
$branch_master = "branch_master";
$the_des_code = $the_des_code ? addslashes(trim($the_des_code)) : "";
if($the_des_code!=""){
$sql1 = "select `branch_code` from $branch_destination_freight where `destination_code`='$the_des_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_branch_code = $row1["branch_code"] ? addslashes(trim($row1["branch_code"])) : "";
		$sql2 = "select `branch_name`,`branch_email_id` from $branch_master where `branch_code`='$the_branch_code'";	
		$res2 = mysql_query($sql2);
		$totres2 = mysql_num_rows($res2);
		if($totres2>0){
			$row2 = mysql_fetch_assoc($res2);
			$the_branch_name = trim($row2["branch_name"]);
			if($the_branch_name!=""){
				$email_array["branch_name"] = $the_branch_name;
			}
			$the_branch_email_id = trim($row2["branch_email_id"]);
			if($the_branch_email_id!=""){
				$email_array["email"][] = $the_branch_email_id;
			}
		}
	}
}
return $email_array;
}

function show_customer_code_from_dns_customer_code($the_dns_customer_code){
$the_cust_code = "";
$customer_master = "customer_master";
if($the_dns_customer_code!=""){
$sql1 = "select `customer_code` from $customer_master where `dns_customer_code`='$the_dns_customer_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_cust_code = $row1["customer_code"] ? addslashes(trim($row1["customer_code"])) : "";
	}
}

return $the_cust_code;
}

function fetch_emails_by_customer_code($the_customer_code){
$email_str = "";
$email_array = array();
$email_array["branch_name"] = "";
$email_array["email"][] = "starsaathi@gmail.com";
$email_array["email"][] = "kaushikshrivastava@starcement.co.in";
$email_array["email"][] = "mridu@forcepower.in";
$branch_destination_freight = "branch_destination_freight";
$branch_master = "branch_master";
$customer_master = "customer_master";
$the_customer_code = $the_customer_code ? addslashes(trim($the_customer_code)) : "";
if($the_customer_code!=""){
$sql1 = "select `branch_code` from $customer_master where `customer_code`='$the_customer_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_branch_code = $row1["branch_code"] ? addslashes(trim($row1["branch_code"])) : "";
		$sql2 = "select `branch_name`,`branch_email_id` from $branch_master where `branch_code`='$the_branch_code'";	
		$res2 = mysql_query($sql2);
		$totres2 = mysql_num_rows($res2);
		if($totres2>0){
			$row2 = mysql_fetch_assoc($res2);
			$the_branch_name = trim($row2["branch_name"]);
			if($the_branch_name!=""){
				$email_array["branch_name"] = $the_branch_name;
			}
			$the_branch_email_id = trim($row2["branch_email_id"]);
			if($the_branch_email_id!=""){
				$email_array["email"][] = $the_branch_email_id;
			}
		}
	}
}
return $email_array;
}

function show_dns_customer_from_customer_code_code($the_customer_code){
$the_cust_code = "";
$customer_master = "customer_master";
if($the_customer_code!=""){
$sql1 = "select `dns_customer_code` from $customer_master where `customer_code`='$the_customer_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$the_cust_code = $row1["dns_customer_code"] ? addslashes(trim($row1["dns_customer_code"])) : "";
	}
}

return $the_cust_code;
}
function show_product_data_from_prod_code($the_prod_code){
$product_dtls = array("dns_prod_code"=>"","prod_desc"=>"");
$product_master = "product_master";
if($the_prod_code!=""){
$sql1 = "select `dns_prod_code`,`prod_desc` from $product_master where `prod_code`='$the_prod_code'";	
$res1 = mysql_query($sql1);
$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_assoc($res1);
		$dns_prod_code = $row1["dns_prod_code"] ? addslashes(trim($row1["dns_prod_code"])) : "";
		$prod_desc = $row1["prod_desc"] ? addslashes(trim($row1["prod_desc"])) : "";
		$product_dtls = array("dns_prod_code"=>$dns_prod_code,"prod_desc"=>$prod_desc);
	}
}

return $product_dtls;
}
function show_dns_destination_code($the_destination_code){
	$dns_dest_code='';
if($the_destination_code!=""){
$sqld = "select `dns_destination_code` from destination_master where destination_code='$the_destination_code'";	
$resd = mysql_query($sqld);
$totresd = mysql_num_rows($resd);
	if($totresd>0){
		$rowd = mysql_fetch_assoc($resd);
		$dns_dest_code = $rowd["dns_destination_code"] ? addslashes(trim($rowd["dns_destination_code"])) : "";
	}
}

return $dns_dest_code;
}

$user_type = $_REQUEST["user_type"] ? strtoupper($_REQUEST["user_type"]) : "";
$login_user_id = $_REQUEST["login_user_id"] ? $_REQUEST["login_user_id"] : "";
if($user_type==""){
$user_type = "DEALER";
}
$order_data = $_REQUEST["order_data"] ? $_REQUEST["order_data"] : array();

/*$data =  json_encode($_REQUEST);
$fp = fopen('lidn.txt', 'w');
fwrite($fp, $data);
fclose($fp);*/
//$sqlinsert="INSERT INTO orderdata set 	data='".$order_data."',insertdatetime=CURRENT_TIMESTAMP()";
//mysql_query($sqlinsert);
if(count($order_data)>0){
$cntslno=1;
foreach($order_data as $ki=>$order_data_val){

//$apporderno = $order_data_val["apporderno"] ? addslashes(trim($order_data_val["apporderno"])) : "";
$erporderno = "";
$erporderdt = $order_data_val["erporderdt"] ? addslashes(trim($order_data_val["erporderdt"])) : "";
$order_for = $order_data_val["order_for"] ? addslashes(trim($order_data_val["order_for"])) : "";
$customer_code = $order_data_val["customer_code"] ? addslashes(trim($order_data_val["customer_code"])) : "";
$prod_code = $order_data_val["prod_code"] ? addslashes(trim($order_data_val["prod_code"])) : "";
$dns_prod_code = "";
$qty = $order_data_val["qty"] ? addslashes(trim($order_data_val["qty"])) : "";
$freight = $order_data_val["freight"] ? addslashes(trim($order_data_val["freight"])) : "";
$destination_code = $order_data_val["destination_code"] ? addslashes(trim($order_data_val["destination_code"])) : "";
$destination_name = $order_data_val["destination_name"] ? addslashes(trim($order_data_val["destination_name"])) : "";
$destination_address = $order_data_val["destination_address"] ? addslashes(trim($order_data_val["destination_address"])) : "";
$dns_customer_code = show_dns_customer_from_customer_code_code($customer_code);
${'dns_customer_code'.$cntslno}=$dns_customer_code;
$prod_dtld = show_product_data_from_prod_code($prod_code);
$dns_prod_code = $prod_dtld["dns_prod_code"];
${'dns_prod_code'.$cntslno}=$dns_prod_code;
$prod_dtld_desc = $prod_dtld["prod_desc"];
$dns_destination_code=show_dns_destination_code($destination_code);
${'dns_destination_code'.$cntslno}=$dns_destination_code;

$phone_no = $order_data_val["phone_no"] ? addslashes(trim($order_data_val["phone_no"])) : "";
$dump_status = $order_data_val["dump_status"] ? addslashes(trim($order_data_val["dump_status"])) : "NO";
if($dump_status==""){
$dump_status = "NO";	
}
$dump_name = $order_data_val["dump_name"] ? addslashes(trim($order_data_val["dump_name"])) : "";

$dealer_truck = $order_data_val["dealer_truck"] ? $order_data_val["dealer_truck"] : "NO";
if($dealer_truck==""){
$dealer_truck = "NO";
}


$order_date = date("Y-m-d H:i:s");
$sqlin = "insert into $t_apperpdo (`ERPORDERNO`,`ERPORDERDT`,`order_date`,`order_for`,`customer_code`,`dns_customer_code`,`prod_code`,`dns_prod_code`,`prod_display_name`,`QTY`,`freight`,`destination_code`,`destination_name`,`destination_address`,`phone_no`,`dump_status`,`dump_name`,`order_from`,`order_by`,`dealer_truck`) values('$erporderno','$erporderdt','$order_date','$order_for','$customer_code','$dns_customer_code','$prod_code','$dns_prod_code','$prod_dtld_desc','$qty','$freight','$destination_code','$destination_name','$destination_address','$phone_no','$dump_status','$dump_name','$user_type','$login_user_id','$dealer_truck')";
$resin = mysql_query($sqlin);
if($resin){
$created_last_id = mysql_insert_id();
$new_order_id_val = str_pad($created_last_id, 7, "0", STR_PAD_LEFT);
$apporderno = "SS".$new_order_id_val;
${'apporderno'.$cntslno}=$apporderno;
$sqlupd1 = "update $t_apperpdo set `APPORDERNO`='$apporderno' where `id`='$created_last_id'";
$resupd1 = mysql_query($sqlupd1);
$the_fetched_array = array();	
$the_fetched_array = fetch_emails_by_customer_code($customer_code);
if(count($the_fetched_array)>0){
	if(array_key_exists("email",$the_fetched_array)){
		$the_fetched_email_arrays = $the_fetched_array["email"];
		$the_fetched_emails = implode(",",$the_fetched_email_arrays);
		if(array_key_exists("branch_name",$the_fetched_array)){
			$the_fetched_branch_name = $the_fetched_array["branch_name"] ? ucwords(strtolower($the_fetched_array["branch_name"])) : "";
			$subject = "Order No.$apporderno for branch ".$the_fetched_branch_name;
		}else{
			$the_fetched_branch_name = "";
			$subject = "Order No.$apporderno for Starsaathi";
		}
		$curr_date_str = date("jS M, y",strtotime($order_date));
		$consignee_name = "";
		$consignee_address_arr = array();
		$consignee_address = "";
		if($order_for!=""){
			if( strpos($order_for,",") !== false ) {
				$ofrarr = array();
				$ofrarr = explode(",",$order_for);
				if(count($ofrarr)>0){
					for($i=0;$i<count($ofrarr);$i++){
					
					if($i==0){
						$consignee_name = $ofrarr[$i];
					}else{
						$consignee_address_arr[] = $ofrarr[$i];
					}
					
					}
				}
			}
		}
		if(count($consignee_address_arr)>0){
			$consignee_address = implode(",",$consignee_address_arr);
		}
		$fetched_customer_name = get_customer_name_from_id($customer_code);
		
		if($the_fetched_emails!=""){
		$to      = $the_fetched_emails;
		$message = '<br><b>App Order No: </b> '.$apporderno.'<br>
					<b>DATE: </b> '.$curr_date_str.'<br>
					<b>Branch Name: </b> '.$the_fetched_branch_name.'<br>
					<b>Customer Name: </b> '.$fetched_customer_name.'<br>
					<b>Consignee Name: </b> '.$consignee_name.'<br>
					<b>Consignee Address: </b> '.$consignee_address.'<br>
					<b>Freight: </b> '.$freight.'<br>
					<b>Destination: </b> '.$destination_name.'<br>
					<b>Product Name: </b> '.$prod_dtld_desc.'<br>
					<b>qty (MT): </b> '.$qty.'<br>
					<b>Phone No.: </b> '.$phone_no.'<br>
					<b>Dump Status: </b> '.$dump_status.'<br>
					<b>Dump Name: </b> '.$dump_name.'<br>
					<b>Dealer Truck: </b> '.$dealer_truck.'<br>';
					
					if($user_type=="BROKER"){
					$broker_name = get_broker_name_from_id($login_user_id);
					$message .= '<b>Order by broker: </b> '.$broker_name.'<br>';
					}
			
		//$res_mail = send_the_mail($to,$subject,$message);
		}
	}
}

}
$cntslno++;
}

$res_data = array("process_status"=>"YES","process_message"=>"The order successfully received.");
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Please select product then make order.");
}
echo json_encode($res_data);
//mysql_close();
//For SFA INSERT

define("SERVERREMOTE","103.242.119.68");
define("USERREMOTE","acedns_dnsprod");
define("PASSWORDREMOTE","dnsprod1234#");
define("DBREMOTE","acedns_STAR");
	
$link=mysql_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die("Database Connection Error.");
mysql_select_db(DBREMOTE,$link) or die("could not connect the database");
$sqlinsert="INSERT INTO orderdata set 	data='".$order_data."',insertdatetime=CURRENT_TIMESTAMP()";
mysql_query($sqlinsert,$link);
$cntordata=1;
if(count($order_data)>0){
	foreach($order_data as $ki=>$order_data_val){
		//$apporderno = $order_data_val["apporderno"] ? addslashes(trim($order_data_val["apporderno"])) : "";
		$erporderno = "";
		$erporderdt = $order_data_val["erporderdt"] ? addslashes(trim($order_data_val["erporderdt"])) : "";
		$order_for = $order_data_val["order_for"] ? addslashes(trim($order_data_val["order_for"])) : "";
		//$customer_code = $order_data_val["customer_code"] ? addslashes(trim($order_data_val["customer_code"])) : "";
		//$prod_code = $order_data_val["prod_code"] ? addslashes(trim($order_data_val["prod_code"])) : "";
		$dns_prod_code = "";
		$qty = $order_data_val["qty"] ? addslashes(trim($order_data_val["qty"])) : "";
		$freight = $order_data_val["freight"] ? addslashes(trim($order_data_val["freight"])) : "";
		//$destination_code = $order_data_val["destination_code"] ? addslashes(trim($order_data_val["destination_code"])) : "";
		$destination_name = $order_data_val["destination_name"] ? addslashes(trim($order_data_val["destination_name"])) : "";
		$destination_address = $order_data_val["destination_address"] ? addslashes(trim($order_data_val["destination_address"])) : "";
		//$dns_customer_code = show_dns_customer_from_customer_code_code($customer_code);
		//$prod_dtld = show_product_data_from_prod_code($prod_code);
		//$dns_prod_code = $prod_dtld["dns_prod_code"];
		//$prod_dtld_desc = $prod_dtld["prod_desc"];
		$dns_prod_code =${'dns_prod_code'.$cntordata};
		$dns_customer_code =${'dns_customer_code'.$cntordata};
		$dns_destination_code =${'dns_destination_code'.$cntordata};
		$apporderno =${'apporderno'.$cntordata};
		
		$phone_no = $order_data_val["phone_no"] ? addslashes(trim($order_data_val["phone_no"])) : "";
		$dump_status = $order_data_val["dump_status"] ? addslashes(trim($order_data_val["dump_status"])) : "NO";
		if($dump_status==""){
		$dump_status = "NO";	
		}
		$dump_name = $order_data_val["dump_name"] ? addslashes(trim($order_data_val["dump_name"])) : "";
		
		$dealer_truck = $order_data_val["dealer_truck"] ? $order_data_val["dealer_truck"] : "NO";
		if($dealer_truck==""){
		$dealer_truck = "NO";
		}
		$order_date = date("Y-m-d H:i:s");
		$sqlcustcode="SELECT customer_code,branch_code,customer_name FROM customer_master WHERE dns_customer_code='".$dns_customer_code."'";
		$rscustcode=mysql_query($sqlcustcode,$link);
		$rowcustcode=mysql_fetch_array($rscustcode);
		$customer_code = $rowcustcode['customer_code'];
		$branch_code = $rowcustcode['branch_code'];
		$customer_name=$rowcustcode['customer_name'];
		
		$sqlbranch = "select branch_name from branch_master where branch_code='".$branch_code."'";	
		$resbranch = mysql_query($sqlbranch);
		$rowbranch=mysql_fetch_array($resbranch);
		$branch_name=$rowbranch['branch_name'];
		
		$sqlprod = "select prod_code,prod_desc from product_master where `dns_prod_code`='".$dns_prod_code."' AND branch_code='".$branch_code."'";	
		$resprod = mysql_query($sqlprod,$link);
		$rowprod = mysql_fetch_array($resprod);
		$prod_code = $rowprod["prod_code"];
		$prod_desc = $rowprod["prod_desc"];
		
		$sqldestination = "select destination_code,destination_name from destination_master where dns_destination_code='".$dns_destination_code."'";	
		$resdestination= mysql_query($sqldestination,$link);
		$rowdestination = mysql_fetch_array($resdestination);
		$destination_code = $rowdestination["destination_code"];
		$destination_name = $rowdestination["destination_name"];
		$destination_address = $rowdestination["destination_name"];
		
		$sqlin = "insert into $t_apperpdo (`APPORDERNO`,`ERPORDERNO`,`ERPORDERDT`,`order_date`,`order_for`,`customer_code`,`dns_customer_code`,`prod_code`,`dns_prod_code`,`prod_display_name`,`QTY`,`freight`,`destination_code`,`destination_name`,`destination_address`,`phone_no`,`dump_status`,`dump_name`,`order_from`,`order_by`,`dealer_truck`) values('$apporderno','$erporderno','$erporderdt','$order_date','$order_for','$customer_code','$dns_customer_code','$prod_code','$dns_prod_code','$prod_desc',
	'$qty','$freight','$destination_code','$destination_name','$destination_address','$phone_no','$dump_status','$dump_name','$user_type','$login_user_id','$dealer_truck')";
		$resin = mysql_query($sqlin,$link);
		$sqlinapproval = "insert into T_APPERPDO_APPROVAL(`APPORDERNO`,`ERPORDERNO`,`ERPORDERDT`,`order_date`,`order_for`,`customer_code`,`dns_customer_code`,`prod_code`,`dns_prod_code`,`prod_display_name`,`QTY`,`freight`,`destination_code`,`destination_name`,`destination_address`,`phone_no`,`dump_status`,`dump_name`,`order_from`,`order_by`,`dealer_truck`) values('$apporderno','$erporderno','$erporderdt','$order_date','$order_for','$customer_code','$dns_customer_code','$prod_code','$dns_prod_code','$prod_desc',
	'$qty','$freight','$destination_code','$destination_name','$destination_address','$phone_no','$dump_status','$dump_name','$user_type','$login_user_id','$dealer_truck')";
		$resinapproval = mysql_query($sqlinapproval,$link);
		
		$ownempmailstring='';
		$reportingmailstring='';
		$email_hierarchy='';
		$sqldealeremp="SELECT emp_code FROM customer_route_emp_relation WHERE acedns='Y' AND customer_code='".$customer_code."'";
		$rsdealeremp=mysql_query($sqldealeremp,$link);
		while($rowdealeremp=mysql_fetch_array($rsdealeremp))
		{
			$emp_code_db=$rowdealeremp['emp_code'];
			$employee_upper_hierarchy=return_employee_upper_hierarchy($emp_code_db);
		//$emp_hierarchy_condition='c1.emp_code IN('.$employee_hierarchy.')';
		/*$sqlemailhierarchy="SELECT email FROM employee_master WHERE emp_code IN (".$employee_upper_hierarchy.")";
		$rsemailhierarchy=mysql_query($sqlemailhierarchy);
		$email_hierarchy='';
		while($rowemailhierarchy=mysql_fetch_array($rsemailhierarchy))
		{
			$email_hierarchy=$email_hierarchy.$rowemailhierarchy['email'].',';
		}
		$email_hierarchy=substr($email_hierarchy,0,-1);
			$sqlownempmail="SELECT email FROM employee_master WHERE emp_code='".$emp_code_db."'";
			$rsownempmail=mysql_query($sqlownempmail);
			$rowownempmail=mysql_fetch_array($rsownempmail);
			$ownempmail=$rowownempmail['email'];
			if($ownempmail!='')
			{
				$ownempmailstring=$ownempmailstring.$ownempmail.',';
			}
			
			$sqlreporting="SELECT reporting_to FROM employee_master WHERE emp_code='".$emp_code_db."'";
			$rsreporting=mysql_query($sqlreporting);
			$rowreporting=mysql_fetch_array($rsreporting);
			$reporting_to=$rowreporting['reporting_to'];
			$reporting_to_array=explode(',',$reporting_to);
			foreach($reporting_to_array as $reporingval)
			{
				$sqlreportingempmail="SELECT email,designation FROM employee_master WHERE emp_code='".$reporingval."'";
				$rsreportingempmail=mysql_query($sqlreportingempmail);
				$rowreportingempmail=mysql_fetch_array($rsreportingempmail);
				$reportingempmail=$rowreportingempmail['email'];
				$designation=$rowreportingempmail['designation'];
				if(strtoupper($designation)=='ASM')
				{
					if($reportingempmail!='')
					{
						$reportingmailstring=$reportingmailstring.$reportingempmail.',';
					}
				}
			}*/
			$sqlemailhierarchy="SELECT email FROM employee_master WHERE emp_code IN (".$employee_upper_hierarchy.") AND UPPER(sale_access)='PRIMARY'";
			$rsemailhierarchy=mysql_query($sqlemailhierarchy);
			while($rowemailhierarchy=mysql_fetch_array($rsemailhierarchy))
			{
				$email_hierarchy=$email_hierarchy.$rowemailhierarchy['email'].',';
			}
		}
		
		$consignee_name = "";
		$consignee_address_arr = array();
		$consignee_address = "";
		if($order_for!=""){
			if( strpos($order_for,",") !== false ) {
				$ofrarr = array();
				$ofrarr = explode(",",$order_for);
				if(count($ofrarr)>0){
					for($i=0;$i<count($ofrarr);$i++){
					
					if($i==0){
						$consignee_name = $ofrarr[$i];
					}else{
						$consignee_address_arr[] = $ofrarr[$i];
					}
					
					}
				}
			}
		}
		if(count($consignee_address_arr)>0){
			$consignee_address = implode(",",$consignee_address_arr);
		}
		$curr_date_format = date("jS M, y",strtotime($order_date));
		//$final_email=substr($ownempmailstring,0,-1).','.substr($reportingmailstring,0,-1).','.'dipankarc@coral.in';
		$final_email=substr($email_hierarchy,0,-1).','.'dipankarc@coral.in'.','.'kaushikshrivastava@starcement.co.in';

		$subject = "Order for branch ".strtoupper($branch_name)." ";
		$message = '<br><b>App Order No: </b> '.$apporderno.'<br>
					<b>DATE: </b> '.$curr_date_format.'<br>
					<b>Branch Name: </b> '.strtoupper($branch_name).'<br>
					<b>Customer Name: </b> '.$customer_name.'<br>
					<b>Consignee Name: </b> '.$consignee_name.'<br>
					<b>Consignee Address: </b> '.$consignee_address.'<br>
					<b>Freight: </b> '.$freight.'<br>
					<b>Destination: </b> '.$destination_name.'<br>
					<b>Product Name: </b> '.$prod_desc.'<br>
					<b>qty (MT): </b> '.$qty.'<br>
					<b>Phone No.: </b> '.$phone_no.'<br>
					<b>Dump Status: </b> '.$dump_status.'<br>
					<b>Dump Name: </b> '.$dump_name.'<br>
					<b>Dealer Truck: </b> '.$dealer_truck.'<br>';
		$send_mail = send_the_mail($final_email,$subject,$message);
		
		$cntordata++;
	}
}
mysql_close();
?>
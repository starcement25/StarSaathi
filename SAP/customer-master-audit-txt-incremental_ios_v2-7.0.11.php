<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require("include/config.php");

require("include/config-setup.php");

require("include/dbcon.php");
//echo"<pre>";print_r('ss0');die;
//comment line sk 19_05_25
//require("include/functions.php");

//echo"<pre>";print_r('ss1');die;
// function utf8ize($mixed) {
//     if (is_array($mixed)) {
//         foreach ($mixed as $key => $value) {
//             $mixed[$key] = utf8ize($value);
//         }
//     } elseif (is_string($mixed)) {
//         return mb_convert_encoding($mixed, "UTF-8", "UTF-8, ISO-8859-1, ISO-8859-15");
//     }
//     return $mixed;
// }
function utf8ize($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } elseif (is_string($mixed)) {
        // Step 1: Replace common NBSP bytes with regular space (fixes �)
        $mixed = str_replace("\xC2\xA0", '', $mixed);
        $mixed = str_replace("\xA0", '', $mixed);
        
        // Step 2: Safe UTF-8 conversion (detect source encoding first)
        $encoding = mb_detect_encoding($mixed, ['UTF-8', 'ISO-8859-1', 'ISO-8859-15'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $mixed = mb_convert_encoding($mixed, 'UTF-8', $encoding);
        }
    }
    return $mixed;
}

$emp_code=$_REQUEST['emp_code'];
$user_type = $_REQUEST['user_type'] ? strtolower(trim($_REQUEST['user_type'])) : "";
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€','',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€','',$data_download_time);
$broker_master = "broker_master";
$customer_broker_relation = "customer_broker_relation";
$tagged_cust_code_arr = array();
$tagged_cust_code_str = "";
if($user_type=="broker"){
	$sql1 = "select `broker_id` from $broker_master where `dns_broker_id`='$emp_code' ";
	$res1 = mysql_query($sql1);
	$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_array($res1);
		$broker_id = $row1["broker_id"];
		$sql2 = "select `customer_code` from $customer_broker_relation where `broker_code`='$broker_id' and acedns='Y'";
		$res2 = mysql_query($sql2);
		$totres2 = mysql_num_rows($res2);
		if($totres2>0){
			while($row2 = mysql_fetch_array($res2)){
				$customer_code_ftc = $row2["customer_code"] ? trim($row2["customer_code"]) : "";
				if($customer_code_ftc!=""){
					$tagged_cust_code_arr[] = $customer_code_ftc;
				}
			}
			if(count($tagged_cust_code_arr)>0){
				$tagged_cust_code_str = implode("','",$tagged_cust_code_arr);
			}
		}
	}
}

if($incremental_download=='no')
{
	$login_condition=" AND acedns='Y'";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
$rsbranches=mysql_query($sqlbranches);
$countbranches=mysql_num_rows($rsbranches);

		if($user_type=="broker"){
			if($tagged_cust_code_str!=''){
		$sqlquerycustomerroute="SELECT customer_code,customer_name,emp_code,current_balance,credit_limit,black_list,acedns,TD,cust_type,rds_tag,sauda_validity_period,address,phone_no,pin,landline_no,owner_name,owner_phone,cust_class,weekly_closing_day,coverage_type,TIN,PAN,minimum_stock,branch_code,visit_day,email,sauda_limit,pending_qty,route_code,customer_id
		FROM customer_master WHERE
		1 ".$login_condition." AND customer_code IN('".$tagged_cust_code_str."') or customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag in('".$tagged_cust_code_str."')  AND acedns='Y') ORDER BY route_code DESC,acedns DESC";
			}
			else
			{
				$sqlquerycustomerroute='';
			}

		}else{

		/*$sqlquerycustomerroute="SELECT customer_code,customer_name,emp_code,current_balance,credit_limit,black_list,acedns,
							TD,cust_type,rds_tag,sauda_validity_period,address,phone_no,pin,landline_no,owner_name,owner_phone,
							 cust_class,weekly_closing_day,coverage_type,TIN,PAN,minimum_stock,branch_code,visit_day,email,sauda_limit,pending_qty,route_code
							 FROM customer_master WHERE
							 1 ".$login_condition." AND (
							 customer_code='".$emp_code."'
							 OR customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$emp_code."') AND acedns='Y'
							 )
							ORDER BY route_code DESC,acedns DESC";*/
			$sqlquerycustomerroute="SELECT customer_code,customer_name,emp_code,current_balance,credit_limit,black_list,acedns,
							TD,cust_type,rds_tag,sauda_validity_period,address,phone_no,pin,landline_no,owner_name,owner_phone,
							 cust_class,weekly_closing_day,coverage_type,TIN,PAN,minimum_stock,branch_code,visit_day,email,sauda_limit,pending_qty,route_code,customer_id
							 FROM customer_master WHERE
							 1 ".$login_condition." AND (
							 customer_code='".$emp_code."'
							 OR customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$emp_code."') AND acedns='Y'
							 ) AND cust_type!='Ship to Party-dealer' AND cust_type!='ShiptoParty-Subdeale'
							ORDER BY route_code DESC,acedns DESC";
		}
		//echo"<pre>";print_r($sqlquerycustomerroute);die;
		$resultcustomer = mysql_query($sqlquerycustomerroute);
		$countcustomer=mysql_num_rows($resultcustomer);
		if($countcustomer>0){
			$date=gmdate('d',strtotime('+330 minute'));
			$month=gmdate('m',strtotime('+330 minute'));
			$year=gmdate('Y',strtotime('+330 minute'));

			$hour=gmdate('H',strtotime('+330 minute'));
			$minute=gmdate('i',strtotime('+330 minute'));
			$second=gmdate('s',strtotime('+330 minute'));
			//$location_date=$year.'-'.$month.'-'.$date.''.$hour.':'.$minute.':'.$second;
			$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second;

			$customer_count=0;
			$customebr_code_array=array();
			$customers = array();

				while($rowcustomer = mysql_fetch_array($resultcustomer)) {
					$customer_code = $rowcustomer['customer_code'];
					
					if(!in_array($customer_code, $customebr_code_array)) {  // Note: typo fixed from $customebr_code_array
						if($rowcustomer['customer_name'] != '') {
							$customers[] = array(
								'customer_code' => ($customer_code != '') ? $customer_code : '',
								'customer_name' => ($rowcustomer['customer_name'] != '') ? trim(preg_replace('/[\r\n]+/', '', $rowcustomer['customer_name'])) : '',
								'route_code' => ($rowcustomer['route_code'] != '') ? $rowcustomer['route_code'] : '',
								'emp_code' => ($rowcustomer['emp_code'] != '') ? $rowcustomer['emp_code'] : '',
								'current_balance' => ($rowcustomer['current_balance'] != '') ? $rowcustomer['current_balance'] : '',
								'credit_limit' => ($rowcustomer['credit_limit'] != '') ? $rowcustomer['credit_limit'] : '',
								'acedns' => ($rowcustomer['acedns'] != '') ? $rowcustomer['acedns'] : '',
								'black_list' => ($rowcustomer['black_list'] != '') ? $rowcustomer['black_list'] : '',
								'TD' => ($rowcustomer['TD'] != '') ? $rowcustomer['TD'] : '0',
								'cust_type' => ($rowcustomer['cust_type'] != '') ? $rowcustomer['cust_type'] : '',
								'rds_tag' => ($rowcustomer['rds_tag'] != '') ? $rowcustomer['rds_tag'] : '',
								'sauda_validity_period' => ($rowcustomer['sauda_validity_period'] != '') ? $rowcustomer['sauda_validity_period'] : '',
								'address' => ($rowcustomer['address'] != '') ? trim(preg_replace('/[\r\n]+/', '', $rowcustomer['address'])) : '',
								'pin' => ($rowcustomer['pin'] != '') ? $rowcustomer['pin'] : '',
								'phone_no' => ($rowcustomer['phone_no'] != '') ? $rowcustomer['phone_no'] : '',
								'landline_no' => ($rowcustomer['landline_no'] != '') ? $rowcustomer['landline_no'] : '',
								'owner_name' => ($rowcustomer['owner_name'] != '') ? $rowcustomer['owner_name'] : '',
								'owner_phone' => ($rowcustomer['owner_phone'] != '') ? $rowcustomer['owner_phone'] : '',
								'cust_class' => ($rowcustomer['cust_class'] != '') ? $rowcustomer['cust_class'] : '',
								'weekly_closing_day' => ($rowcustomer['weekly_closing_day'] != '') ? $rowcustomer['weekly_closing_day'] : '',
								'coverage_type' => ($rowcustomer['coverage_type'] != '') ? $rowcustomer['coverage_type'] : '',
								'TIN' => ($rowcustomer['TIN'] != '') ? $rowcustomer['TIN'] : '',
								'PAN' => ($rowcustomer['PAN'] != '') ? $rowcustomer['PAN'] : '',
								'minimum_stock' => ($rowcustomer['minimum_stock'] != '') ? $rowcustomer['minimum_stock'] : '',
								'branch_code' => ($rowcustomer['branch_code'] != '') ? $rowcustomer['branch_code'] : '',
								'visit_day' => ($rowcustomer['visit_day'] != '') ? $rowcustomer['visit_day'] : '',
								'email' => ($rowcustomer['email'] != '') ? $rowcustomer['email'] : '',
								'sauda_limit' => ($rowcustomer['sauda_limit'] != '') ? $rowcustomer['sauda_limit'] : '',
								'pending_qty' => ($rowcustomer['pending_qty'] != '') ? $rowcustomer['pending_qty'] : '',
								'customer_id' => ($rowcustomer['customer_id'] != '') ? $rowcustomer['customer_id'] : ''
							);
							
							$customer_count++;
						}
					}
				}

			
			
		}

		
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/customer-master-audit-txt-incremental_v2-7.0.11.phpp?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	
		//echo"<pre>";print_r($customers);die;
	$customers = utf8ize($customers);

			// Check if no data found
			if (empty($customers)) {
				echo json_encode([
					'success' => false,
					'message' => 'No data found',
					'data' => [],
					'count' => 0
				]);
			} else {
				echo json_encode([
					'success' => true,
					'message' => 'Data retrieved successfully',
					'data' => $customers,
					'count' => $customer_count
				], JSON_PRETTY_PRINT| JSON_UNESCAPED_SLASHES);
			}
?>

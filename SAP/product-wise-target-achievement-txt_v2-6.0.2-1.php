<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
/*error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);*/
$app_service_track_log='app_service_track_log';
$customer_master='customer_master';

$emp_code=$_REQUEST['emp_code'] ? strtolower(trim($_REQUEST['emp_code'])) : "";
$user_type = $_REQUEST['user_type'] ? strtolower(trim($_REQUEST['user_type'])) : "";

//sk add line new rssd implementation

	$year=date('Y');
 $sqlcustomercode="SELECT customer_id,cust_type FROM customer_master where customer_code='".$emp_code."'";
		$rscustomercode=mysql_query($sqlcustomercode);
		$rowcustomercode=mysql_fetch_array($rscustomercode);
		 $customer_id=$rowcustomercode['customer_id']; 
		  $cust_type=$rowcustomercode['cust_type']; 
		if($cust_type=="RSSD"){


		// Get current year
		$year = gmdate('Y', strtotime('+330 minute'));

		// Months array
		$months = [
			1=>'January', 2=>'February', 3=>'March', 4=>'April', 5=>'May', 6=>'June',
			7=>'July', 8=>'August', 9=>'September', 10=>'October', 11=>'November', 12=>'December'
		];

		$data = '';
		$emp_code = strtoupper($emp_code);
		$data='12¥'.'8'."\n";
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		$data=$data.$contentsdatetime;
// Loop through months 1 to 12
for ($m=1; $m<=12; $m++) {
    $month_name = $months[$m];
    $month_number = sprintf("%02d", $m);

    // Current cycle: Apr-Dec this year, Jan-Mar next year
    if ($m >=4) {
        $curr_year = $year; 
    } else {
        $curr_year = $year+1;
    }

    // Previous cycle: Apr-Dec last year, Jan-Mar this year
    if ($m >=4) {
        $prev_year = $year-1;
    } else {
        $prev_year = $year;
    }

    // Fetch total_allocated_qty for current cycle
    $sql_curr = "SELECT COALESCE(SUM(l.allocated_qty),0) as qty 
                 FROM lifting_final_file_upload_csv f
                 LEFT JOIN lifting_final_allocation_data l 
                    ON l.file_upload_id = f.id 
                   AND l.sub_dealer_sap_code = '$customer_id'
                 WHERE f.month='$month_name' AND f.year='$curr_year'";
    $res_curr = mysql_query($sql_curr);
    $row_curr = mysql_fetch_assoc($res_curr);
    $curr_qty = (float)$row_curr['qty'];

    // Fetch total_allocated_qty for previous cycle
    $sql_prev = "SELECT COALESCE(SUM(l.allocated_qty),0) as qty 
                 FROM lifting_final_file_upload_csv f
                 LEFT JOIN lifting_final_allocation_data l 
                    ON l.file_upload_id = f.id 
                   AND l.sub_dealer_sap_code = '$customer_id'
                 WHERE f.month='$month_name' AND f.year='$prev_year'";
    $res_prev = mysql_query($sql_prev);
    $row_prev = mysql_fetch_assoc($res_prev);
    $prev_qty = (float)$row_prev['qty'];

    // Append to data
    $data .= "12001^STAR ANTIRUST CEMENT  TRADE^".$emp_code."^".$month_number."^0^".$curr_qty."^0^".$prev_qty."\n";
}

// Output file
header("Content-type: application/text"); 
header("Content-Disposition: attachment; filename=product_wise_target_ach.txt");
print $data;
die;
}

//sk add end 
//echo"<pre>";print_r($user_type);die;

$date= gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$broker_master = "broker_master";
$customer_broker_relation = "customer_broker_relation";
$tagged_cust_code_arr = array();
$tagged_cust_code_str = "";
date_default_timezone_set('Asia/Kolkata');

if($employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' SAPW.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" SAPW.emp_code='".$emp_code."'";
}

$the_dlr_code_arr = array();

if($user_type=="broker"){
	$sql1 = "select `broker_id` from $broker_master where `dns_broker_id`='$emp_code' ";
	$res1 = mysql_query($sql1);
	$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		$row1 = mysql_fetch_array($res1);
		$broker_id = $row1["broker_id"];
		$sql2 = "select `customer_code` from $customer_broker_relation where `broker_code`='$broker_id' ";
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
				$sql12 = "select `dns_customer_code` from `customer_master` where `customer_code` in('".$tagged_cust_code_str."') and `acedns`='Y' ";
				$res12 = mysql_query($sql12);
				$totres12 = mysql_num_rows($res12);
				if($totres12>0){
				while($row12 = mysql_fetch_assoc($res12)){
				$the_dns_dlr_id = $row12["dns_customer_code"] ? trim($row12["dns_customer_code"]) : "";
				if($the_dns_dlr_id!=""){
				$the_dlr_code_arr[] = $the_dns_dlr_id;
				}
				}
				}
			}
		}
	}

}

else{

 $sqlcustomercode="SELECT dns_customer_code FROM customer_master where customer_code='".$emp_code."'";
$rscustomercode=mysql_query($sqlcustomercode);
$rowcustomercode=mysql_fetch_array($rscustomercode);
$dns_customer_code=$rowcustomercode['dns_customer_code'];
if($dns_customer_code!=""){
	$the_dlr_code_arr[] = $dns_customer_code;
	$sql1 = "select `dns_customer_code` from `customer_master` where `rds_tag`='".$emp_code."' and `acedns`='Y' ";
	$res1 = mysql_query($sql1);
	$totres1 = mysql_num_rows($res1);
	if($totres1>0){
		while($row1 = mysql_fetch_assoc($res1)){
			$the_sub_dlr_id = $row1["dns_customer_code"] ? trim($row1["dns_customer_code"]) : "";
			if($the_sub_dlr_id!=""){
				$the_dlr_code_arr[] = $the_sub_dlr_id;
			}
		}
	}
}
}
//add sk 19-08-28
//$the_dlr_code_arr[]=$emp_code;
//echo"<pre>";print_r($sql1);die;
if(count($the_dlr_code_arr)>0){
	$the_dlr_code_arr_str = implode("','",$the_dlr_code_arr);
}else{
	$the_dlr_code_arr_str = "";
}
/*
$sqlquery="SELECT SAPW.prod_code,PM.prod_desc,CM.customer_code as emp_code, SAPW.jan_31_target,SAPW.jan_31_achievement,SAPW.feb_28_target,SAPW.feb_28_achievement,SAPW.mar_31_target,
SAPW.mar_31_achievement,SAPW.apr_30_target,SAPW.apr_30_achievement,SAPW.may_31_target,SAPW.may_31_achievement,SAPW.jun_30_target,
SAPW.jun_30_achievement,SAPW.jul_31_target,SAPW.jul_31_achievement,SAPW.aug_31_target,SAPW.aug_31_achievement,SAPW.sep_30_target,
SAPW.sep_30_achievement,SAPW.oct_31_target,SAPW.oct_31_achievement,SAPW.nov_30_target,SAPW.nov_30_achievement,SAPW.dec_31_target,
SAPW.dec_31_achievement,SAPW.jan_prev_y_target,SAPW.jan_prev_y_achievement,SAPW.feb_prev_y_target,SAPW.feb_prev_y_achievement,SAPW.mar_prev_y_target,SAPW.mar_prev_y_achievement,SAPW.apr_prev_y_target,SAPW.apr_prev_y_achievement,SAPW.may_prev_y_target,SAPW.may_prev_y_achievement,SAPW.jun_prev_y_target,
SAPW.jun_prev_y_achievement,SAPW.jul_prev_y_target,SAPW.jul_prev_y_achievement,SAPW.aug_prev_y_target,SAPW.aug_prev_y_achievement,SAPW.sep_prev_y_target,SAPW.sep_prev_y_achievement,SAPW.oct_prev_y_target,SAPW.oct_prev_y_achievement,SAPW.nov_prev_y_target,SAPW.nov_prev_y_achievement,SAPW.dec_prev_y_target,
SAPW.dec_prev_y_achievement FROM self_appraisal_product_wise SAPW left join product_master PM on SAPW.prod_code = PM.prod_code left join customer_master CM on SAPW.customer_code = CM.dns_customer_code  WHERE SAPW.customer_code in('".$the_dlr_code_arr_str."')";
*/
/*
$sqlquery="SELECT SAPW.prod_code,PM.prod_desc,CM.customer_code as emp_code, SAPW.jan_31_target,SAPW.jan_31_achievement,SAPW.feb_28_target,SAPW.feb_28_achievement,SAPW.mar_31_target,
SAPW.mar_31_achievement,SAPW.apr_30_target,SAPW.apr_30_achievement,SAPW.may_31_target,SAPW.may_31_achievement,SAPW.jun_30_target,
SAPW.jun_30_achievement,SAPW.jul_31_target,SAPW.jul_31_achievement,SAPW.aug_31_target,SAPW.aug_31_achievement,SAPW.sep_30_target,
SAPW.sep_30_achievement,SAPW.oct_31_target,SAPW.oct_31_achievement,SAPW.nov_30_target,SAPW.nov_30_achievement,SAPW.dec_31_target,
SAPW.dec_31_achievement,SAPW.jan_prev_y_target,SAPW.jan_prev_y_achievement,SAPW.feb_prev_y_target,SAPW.feb_prev_y_achievement,SAPW.mar_prev_y_target,SAPW.mar_prev_y_achievement,SAPW.apr_prev_y_target,SAPW.apr_prev_y_achievement,SAPW.may_prev_y_target,SAPW.may_prev_y_achievement,SAPW.jun_prev_y_target,
SAPW.jun_prev_y_achievement,SAPW.jul_prev_y_target,SAPW.jul_prev_y_achievement,SAPW.aug_prev_y_target,SAPW.aug_prev_y_achievement,SAPW.sep_prev_y_target,SAPW.sep_prev_y_achievement,SAPW.oct_prev_y_target,SAPW.oct_prev_y_achievement,SAPW.nov_prev_y_target,SAPW.nov_prev_y_achievement,SAPW.dec_prev_y_target,
SAPW.dec_prev_y_achievement FROM self_appraisal_product_wise SAPW left join product_master PM on SAPW.prod_code = PM.prod_code left join customer_master CM on SAPW.customer_code = CM.customer_id  WHERE CM.dns_customer_code in('".$the_dlr_code_arr_str."')";
//echo"<pre>";print_r($sqlquery);
$result = mysql_query($sqlquery);
$count=mysql_num_rows($result);

	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		$countappraisal=0;
		while($rowsappraisal = mysql_fetch_array($result))
		{
			for($i=1;$i<=12;$i++)
			{
				if(strlen($i)==1)
				{
					$i='0'.$i;
				}
				$month = date("$i"); // Current month
				$first_date='01'.'-'.$month.'-'.$year;
				$month_abrev=date('M',strtotime($first_date));
				$days = cal_days_in_month(CAL_GREGORIAN,$month,$year);
				$column_target=strtolower($month_abrev).'_'.$days.'_target';
				$coumn_achievement=strtolower($month_abrev).'_'.$days.'_achievement';
				if(previous_year=='yes')
				{
					$column_prev_target=strtolower($month_abrev).'_prev_y_target';
					$column_prev_target=$rowsappraisal[$column_prev_target];
					$coumn_prev_achievement=strtolower($month_abrev).'_prev_y_achievement';
					$coumn_prev_achievement=$rowsappraisal[$coumn_prev_achievement];
				}
				if($i==1)
				{
					$column_target=$rowsappraisal['jan_31_target'];
					$column_achievement=$rowsappraisal['jan_31_achievement'];
				}
				if($i==2)
				{
					$column_target=$rowsappraisal['feb_28_target'];
					$column_achievement=$rowsappraisal['feb_28_achievement'];
				}
				if($i==3)
				{
					$column_target=$rowsappraisal['mar_31_target'];
					$column_achievement=$rowsappraisal['mar_31_achievement'];
				}
				if($i==4)
				{
					$column_target=$rowsappraisal['apr_30_target'];
					$column_achievement=$rowsappraisal['apr_30_achievement'];
				}
				if($i==5)
				{
					$column_target=$rowsappraisal['may_31_target'];
					$column_achievement=$rowsappraisal['may_31_achievement'];
				}
				if($i==6)
				{
					$column_target=$rowsappraisal['jun_30_target'];
					$column_achievement=$rowsappraisal['jun_30_achievement'];
				}
				if($i==7)
				{
					$column_target=$rowsappraisal['jul_31_target'];
					$column_achievement=$rowsappraisal['jul_31_achievement'];
				}
				if($i==8)
				{
					$column_target=$rowsappraisal['aug_31_target'];
					$column_achievement=$rowsappraisal['aug_31_achievement'];
				}
				if($i==9)
				{
					$column_target=$rowsappraisal['sep_30_target'];
					$column_achievement=$rowsappraisal['sep_30_achievement'];
				}
				if($i==10)
				{
					$column_target=$rowsappraisal['oct_31_target'];
					$column_achievement=$rowsappraisal['oct_31_achievement'];
				}
				if($i==11)
				{
					$column_target=$rowsappraisal['nov_30_target'];
					$column_achievement=$rowsappraisal['nov_30_achievement'];
				}
				if($i==12)
				{
					$column_target=$rowsappraisal['dec_31_target'];
					$column_achievement=$rowsappraisal['dec_31_achievement'];
				}
				
				$contents  = (($rowsappraisal['prod_code']!='')?$rowsappraisal['prod_code']: ' ')."^";
				$contents  .= (($rowsappraisal['prod_desc']!='')?trim(preg_replace('/[\r\n]+/', '',$rowsappraisal['prod_desc'])): ' ')."^";
				$contents  .= (($rowsappraisal['emp_code']!='')?$rowsappraisal['emp_code']: ' ')."^";
				$contents  .= $i."^";
				$contents  .= (($column_target!='')?$column_target: ' ')."^";
				$contents  .= (($column_achievement!='')?$column_achievement: ' ')."^";
				$contents  .= (($column_prev_target!='')?$column_prev_target: ' ')."^";
				$contents  .= (($coumn_prev_achievement!='')?$coumn_prev_achievement: ' ');
				$linecontents  .= $contents."\n";
				$countappraisal++;	
			}
		}
		$contentsrowcolumn=$countappraisal.'¥'.'8';
		*/
	
	

	if ($emp_code == '') {
    echo "Invalid Customer Code";
    exit;
}
/* ===== HEADER OUTPUT ===== */
$current_date = date('Y-m-d');
$current_time = date('H:i:s');
echo "12¥8\n";
echo $current_date . "€" . $current_time . "\n";

	$static_code = "12001";
	$product_name = "STAR ANTIRUST CEMENT  TRADE";
$current_year  = date('Y');
$current_month = date('n');

// Financial Year (Apr start)
$fy = ($current_month >= 4) ? $current_year : $current_year - 1;

/* ===== FETCH DATA ===== */
$data = array();

$sql = "
SELECT month, year, target_qty, achievement_qty
FROM month_wise_achievement_new
WHERE customer_code = '$emp_code'
";

$res = mysql_query($sql);

while ($row = mysql_fetch_assoc($res)) {
    $m = $row['month'];
    $y = $row['year'];

    $data[$m][$y] = array(
        'target' => $row['target_qty'],
        'ach'    => $row['achievement_qty']
    );
}

/* ===== LOOP 12 MONTHS ===== */
for ($m = 1; $m <= 12; $m++) {

    $month = str_pad($m, 2, '0', STR_PAD_LEFT);

    // ✅ FINANCIAL YEAR MAPPING
    if ($m >= 1 && $m <= 3) {
        // Jan–Mar
        $cur_year = $fy + 1;
        $pre_year = $fy;
    } else {
        // Apr–Dec
        $cur_year = $fy;
        $pre_year = $fy - 1;
    }
$emp_code=strtoupper($emp_code);
    // Current Year Data
    $cur_target = isset($data[$m][$cur_year]) ? $data[$m][$cur_year]['target'] : 0;
    $cur_ach    = isset($data[$m][$cur_year]) ? $data[$m][$cur_year]['ach'] : 0;

    // Previous Year Data
    $pre_target = isset($data[$m][$pre_year]) ? $data[$m][$pre_year]['target'] : 0;
    $pre_ach    = isset($data[$m][$pre_year]) ? $data[$m][$pre_year]['ach'] : 0;
 	$cur_target =number_format ($cur_target, 2, '.', '');
	$cur_ach    =number_format ($cur_ach, 2, '.', '');	
	$pre_target =number_format ($pre_target, 2, '.', '');
	$pre_ach    =number_format ($pre_ach, 2, '.', '');
    echo $static_code . "^" .
		 $product_name . "^" .
		 $emp_code . "^" .
		 $month . "^" .
         $cur_target . "^" .
         $cur_ach . "^" .
         $pre_target . "^" .
         $pre_ach . "\n";
}

		//$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/product-wise-target-achievement-txt-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=product_wise_target_ach.txt");
	print "$datacontents"; 

	//echo json_encode($res_data);
	
	mysql_close($link);		
?>

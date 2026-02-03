<?php
$credit_days = 65;
$my_os_amount = 0;
$vl_less_equ_10days = 10;
$vl_11_17days = 10;
$vl_18_25days = 10;
$vl_26_30days = 10;
$vl_31_45days = 10;
$vl_46_60days = 10;
$vl_61_90days = 10;
$vl_91_120days = 10;
$vl_121_180days = 10;
$vl_greater_180days = 10;

$is_allow_order = "YES";
$is_greater_than_30 = "NO";
$ledg_os_balance = 0;
$ledg_credit_limit = 200000.00;
$ostotal = 185343.03;
$ledg_pending_orders = 0.00;
$ord_tot_amount = 6343.76;


$the_arr_less_30 = array();
$the_arr_greater_30 = array();
$the_arr_less_30[] = array("range_from"=>0,"range_to"=>10,"os_amount"=>$vl_less_equ_10days);
$the_arr_less_30[] = array("range_from"=>11,"range_to"=>17,"os_amount"=>$vl_11_17days);
$the_arr_less_30[] = array("range_from"=>18,"range_to"=>25,"os_amount"=>$vl_18_25days);
$the_arr_less_30[] = array("range_from"=>26,"range_to"=>30,"os_amount"=>$vl_26_30days);
$the_arr_greater_30[] = array("range_from"=>31,"range_to"=>45,"os_amount"=>$vl_31_45days);
$the_arr_greater_30[] = array("range_from"=>46,"range_to"=>60,"os_amount"=>$vl_46_60days);
$the_arr_greater_30[] = array("range_from"=>61,"range_to"=>90,"os_amount"=>$vl_61_90days);
$the_arr_greater_30[] = array("range_from"=>91,"range_to"=>120,"os_amount"=>$vl_91_120days);
$the_arr_greater_30[] = array("range_from"=>121,"range_to"=>180,"os_amount"=>$vl_121_180days);
$the_arr_greater_30[] = array("range_from"=>0,"range_to"=>180,"os_amount"=>$vl_greater_180days);



	foreach($the_arr_greater_30 as $the_arr_greater_30_val){
		$the_arr_greater_30_os_amount = $the_arr_greater_30_val["os_amount"] ? $the_arr_greater_30_val["os_amount"] : 0;
		if($the_arr_greater_30_os_amount>0){
			$is_greater_than_30 = "YES";
			break;
		}
	}

if($is_greater_than_30=="NO"){
	/*foreach($the_arr_greater_30 as $the_arr_greater_30_val){
		$the_arr_greater_30_range_to = $the_arr_greater_30_val["range_to"];
		$the_arr_greater_30_os_amount = $the_arr_greater_30_val["os_amount"];
		if($the_arr_greater_30_range_to<=30){
			$ledg_os_balance = ($ledg_os_balance + $the_arr_greater_30_os_amount);
		}
	}*/
	
	$dlr_available_limit = ($ledg_credit_limit - ($ostotal) - $ledg_pending_orders);
}else{
$is_allow_order = "NO";	
}

/*if($is_greater_than_30=="YES"){
$is_allow_order = "NO";
}else{

if($ord_tot_amount < $dlr_available_limit){
$is_allow_order = "YES";
}else{
$is_allow_order = "NO";	
}
	
}*/
if($is_greater_than_30=="NO" && $ord_tot_amount<$dlr_available_limit){
$is_allow_order = "YES";
}else{
$is_allow_order = "NO";		
}

echo "<br>is_greater_than_30: ".$is_greater_than_30;
echo "<br>is_allow_order: ".$is_allow_order;
echo "<br>ord_tot_amount: ".$ord_tot_amount;
echo "<br>dlr_available_limit: ".$dlr_available_limit;

exit;
$the_arr = array();
$the_arr[] = array("range_from"=>0,"range_to"=>10,"os_amount"=>$vl_less_equ_10days);
$the_arr[] = array("range_from"=>11,"range_to"=>17,"os_amount"=>$vl_11_17days);
$the_arr[] = array("range_from"=>18,"range_to"=>25,"os_amount"=>$vl_18_25days);
$the_arr[] = array("range_from"=>26,"range_to"=>30,"os_amount"=>$vl_26_30days);
$the_arr[] = array("range_from"=>31,"range_to"=>45,"os_amount"=>$vl_31_45days);
$the_arr[] = array("range_from"=>46,"range_to"=>60,"os_amount"=>$vl_46_60days);
$the_arr[] = array("range_from"=>61,"range_to"=>90,"os_amount"=>$vl_61_90days);
$the_arr[] = array("range_from"=>91,"range_to"=>120,"os_amount"=>$vl_91_120days);
$the_arr[] = array("range_from"=>121,"range_to"=>180,"os_amount"=>$vl_121_180days);
$the_arr[] = array("range_from"=>0,"range_to"=>180,"os_amount"=>$vl_greater_180days);

if(count($the_arr)>0){

foreach($the_arr as $the_arr_val){
	$range_to = $the_arr_val["range_to"];
	$os_amount = $the_arr_val["os_amount"];
if($range_to>$credit_days){
$my_os_amount = ($my_os_amount + $os_amount);
echo "<br> range_from:".$range_from.", range_to:".$range_to.", os_amount:".$os_amount.", sts: yes<br> ";
}
}
	
}

echo $my_os_amount;

?>
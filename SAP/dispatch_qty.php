<?php
include "star_connection.php";
$t_apperpdo = "T_APPERPDO";
$customer_master = "customer_master";
$T_DOCHALLAN = "T_DOCHALLAN";
$allocation_details  = "allocation_details";
$dispatched_order_data = array();
$dispatched_challan_data= array();
$curr_date_time  = date("Y-m-d H:i:s");
$order_item_arr = array();
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$curr_date = date("Y-m-d");
$before_30_day_date = date('Y-m-d',strtotime("-30 days"));
$the_customer_code = $_REQUEST["customer_code"] ? addslashes(trim($_REQUEST["customer_code"])) : "";
/*$the_status = $_REQUEST["the_status"] ? addslashes(trim($_REQUEST["the_status"])) : "";
$start_date = $_REQUEST["start_date"] ? addslashes(trim($_REQUEST["start_date"])) : $before_30_day_date;
$end_date = $_REQUEST["end_date"] ? addslashes(trim($_REQUEST["end_date"])) : $curr_date;
$page_no = $_REQUEST["page_no"] ? $_REQUEST["page_no"] : 1;
$limit = 10;
$start_from = (($page_no-1)*$limit);*/
if($the_customer_code==""){
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.","dispatched_order_data"=>$dispatched_order_data);
}else{
$sql_cust = "SELECT `dns_customer_code` FROM $customer_master where `customer_id`='".$the_customer_code."'";
$res_cust = mysql_query($sql_cust);
$tot_res_cust = mysql_num_rows($res_cust);
if($tot_res_cust>0){
	
	$row_cust=mysql_fetch_array($res_cust);
	$dns_customer_code=$row_cust['dns_customer_code'];
	
	/*if($start_date!="" && $end_date!=""){
		$date_qry = " and `order_date` between '".$start_date." ".$frm_hrs."' and '".$end_date." ".$to_hrs."'";
	}else{
		$date_qry = "";
	}*/
	/*$sqlall2 = "select $t_apperpdo.*,$customer_master.`customer_name` from $t_apperpdo inner join $customer_master on $t_apperpdo.`dns_customer_code`=$customer_master.`dns_customer_code`  
	where $t_apperpdo.dns_customer_code='".$the_customer_code."' and (`status`='Success' or `status`='Shipped' )  $date_qry order by $t_apperpdo_pop.`order_date` desc limit $start_from,$limit";*/
	$sqlall2 = "select $t_apperpdo.APPORDERNO,$t_apperpdo.order_date ,$t_apperpdo.QTY,$t_apperpdo.dns_prod_code,$t_apperpdo.prod_display_name,$t_apperpdo.STATUS ,$t_apperpdo.freight,$t_apperpdo.destination_name,$customer_master.`customer_name`,$customer_master.`customer_id` from $t_apperpdo inner join $customer_master on $t_apperpdo.`dns_customer_code`=$customer_master.`dns_customer_code`  
	where $t_apperpdo.dns_customer_code='".$dns_customer_code."' and `status`='Dispatched'  order by $t_apperpdo.`order_date` desc";
$resall2 = mysql_query($sqlall2);
$totall2 = mysql_num_rows($resall2);
if($totall2>0){
while($row112=mysql_fetch_assoc($resall2)){
$order_id = $row112["APPORDERNO"] ? trim($row112["APPORDERNO"]) : "";
$order_date = $row112["order_date"] ? trim($row112["order_date"]) : "";
if($order_date!=""){
			$order_full_date_time = date("jS M Y h:i A",strtotime($order_date));
		}else{
			$order_full_date_time = "";
		}
		
$customer_name = $row112["customer_name"] ? trim($row112["customer_name"]) : "";
$customer_id = $row112["customer_id"] ? trim($row112["customer_id"]) : "";	
$destination_name = $row112["destination_name"] ? trim($row112["destination_name"]) : "";
$dns_prod_code = $row112["dns_prod_code"] ? trim($row112["dns_prod_code"]) : "";
$prod_display_name = $row112["prod_display_name"] ? trim($row112["prod_display_name"]) : "";
$qty = $row112["QTY"] ? trim($row112["QTY"]) : "";
$freight = $row112["freight"] ? trim($row112["freight"]) : "";
$STATUS = $row112["STATUS"] ? trim($row112["STATUS"]) : "";	
	$allocation_qty="0";
	$sqlallocation = "select SUM(allocation_qty) AS allocation_qty from $allocation_details where `customer_id`='$customer_id' and `APPORDERNO`='$order_id' AND dns_prod_code='$dns_prod_code'";
	$resallocation = mysql_query($sqlallocation);
	$totallocation = mysql_num_rows($resallocation);			
	if($totallocation>0){
		while($rowallocation=mysql_fetch_assoc($resallocation)){
		$allocation_qty = $rowallocation["allocation_qty"] ? trim($rowallocation["allocation_qty"]) : "0";
		}
	}
	
    
	
	$sqlall3 = "select * from $T_DOCHALLAN where `dns_customer_code`='$dns_customer_code' and `APPORDERNO`='$order_id' 
	AND dns_prod_code='$dns_prod_code'";
			$resall3 = mysql_query($sqlall3);
			$totall3 = mysql_num_rows($resall3);			
			if($totall3>0){
				$dispatched_challan_data=array();
				while($row113=mysql_fetch_assoc($resall3)){
				$ch_challanno = $row113["CHALLANNO"] ? trim($row113["CHALLANNO"]) : "";
				$ch_challandt = $row113["CHALLANDT"] ? trim($row113["CHALLANDT"]) : "";
				$ch_challanqty = $row113["CHALLANQTY"] ? trim($row113["CHALLANQTY"]) : "";
				
					if($ch_challandt!=""){
						$challan_full_date_time = date("jS M Y h:i A",strtotime($ch_challandt));
					}else{
						$challan_full_date_time = "";
					}
	
				$dispatched_challan_data[] =array("challanno"=>$ch_challanno,"dispatch_date"=>$ch_challandt,"dispatch_qty"=>$ch_challanqty);
				
				}
			}
	
/*$sqlpopproduct="SELECT prod_image,price_per_piece,GST_rate FROM pop_product_master WHERE status='Y' AND dns_prod_code='".$dns_prod_code."'";
$rspopproduct=mysql_query($sqlpopproduct);
while($rowpopproduct=mysql_fetch_array($rspopproduct))
{
$prod_image=$rowpopproduct['prod_image'];
$price_per_piece=$rowpopproduct['price_per_piece'];
$GST_rate=$rowpopproduct['GST_rate'];
}
$prod_img_URL="https://starsaathi.com/SAP/pop_prod_img/".$prod_image;
$total_amount=($qty*$price_per_piece)+$GST_rate;
*/	
$dispatched_order_data[] = array("order_id"=>$order_id,"order_date"=>$order_full_date_time,"customer_name"=>$customer_name,"destination_name"=>$destination_name,"dns_prod_code"=>$dns_prod_code,"prod_display_name"=>$prod_display_name,"qty"=>$qty,"freight"=>$freight,"STATUS"=>$STATUS,"allocation_qty"=>$allocation_qty,"dispatched_challan_data"=>$dispatched_challan_data);
}

$grouped_data= array();
    foreach ($dispatched_order_data as $data) {
        $date = substr($data["order_date"], 0, 10);
        $prod_display_name = $data["prod_display_name"];
        if (!isset($grouped_data[$date])) {
            $grouped_data[$date] = array(
                "order_date" => $data["order_date"],
                "allocation_qty" => 0,
                "prod_display_name" => $prod_display_name,
            );
        }
        $grouped_data[$date][$prod_display_name]["allocation_qty"] += $data["allocation_qty"];
    }
    $dispatched_order_data = array_values($grouped_data);

$res_data = array("process_status"=>"YES","process_message"=>"Success.","dispatched_order_data"=>$dispatched_order_data);
}else{
$res_data = array("process_status"=>"NO","process_message"=>"No Dispatched order found.");
}
	}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Dealer record not found.","dispatched_order_data"=>$dispatched_order_data);
}
}
echo json_encode($res_data);
if($conn!=""){
mysql_close($conn);
}
?>
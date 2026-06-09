<?php
include "star_connection.php";
$allocation_details = "allocation_details_invoicewise";

$response = array();

$customer_id = $_REQUEST["customer_id"] ? addslashes(trim($_REQUEST["customer_id"])) : "";
$user_type=$_REQUEST["user_type"] ? addslashes(trim($_REQUEST["user_type"])) : "";
$year_month=$_REQUEST["year_month"] ? addslashes(trim($_REQUEST["year_month"])) : "";
if ($customer_id == "") {
    $response["process_status"] = "NO";
    $response["process_message"] = "Customer ID is missing.";
} else {

    if(strtoupper($user_type)=='DEALER' || $user_type=='')
	{
		$user_type_clause="customer_id = '$customer_id'";
	}
	if(strtoupper($user_type)=='RSSD')
	{
		$user_type_clause="sub_dealer_id = '$customer_id'";
	}
	if($year_month!='')
	{
		//$year_month_condition=" AND SUBSTRING(date_and_time,1,7)='".$year_month."'";
		$year_month_condition=" AND SUBSTRING(inv_date,1,7)='".$year_month."'";
	}
	else $year_month_condition="";
//sk 28/05/25
/*$sqlin = "SELECT allocation_id, dns_prod_code, prod_desc, allocation_qty, date_and_time, order_id,sub_dealer_id,inv_no,inv_qty,inv_date   FROM $allocation_details WHERE $user_type_clause $year_month_condition AND inv_cancl='no' order by date_and_time desc";*/
$sqlin = "SELECT allocation_id, dns_prod_code, prod_desc, allocation_qty, date_and_time, order_id,sub_dealer_id,inv_no,inv_qty,inv_date,delete_at   FROM $allocation_details WHERE $user_type_clause $year_month_condition AND inv_cancl='no' AND delete_at='0' order by date_and_time desc";

//echo
//     SELECT allocation_id, dns_prod_code, prod_desc, allocation_qty, date_and_time, order_id
// FROM allocation_details
// WHERE customer_id = '1000000341'
// ORDER BY date_and_time DESC;

    $resin = mysql_query($sqlin);

    if (!$resin) {

        $response["process_status"] = "ERROR";
        $response["process_message"] = "Error: " . mysql_error();
    } else {

        if (mysql_num_rows($resin) > 0) {

            $response["process_status"] = "YES";
            $response["process_message"] = "Allocation details fetched successfully.";


            $allocation_data = array();

            while ($row = mysql_fetch_assoc($resin)) {

				$allocation_id=$row['allocation_id'];
				$dns_prod_code=$row['dns_prod_code'];
				$prod_desc=$row['prod_desc'];
				$allocation_qty=$row['allocation_qty'];
				$date_and_time=$row['date_and_time'];
				$order_id=$row['order_id'];
				$inv_no=$row['inv_no'];
				$sub_dealer_id=$row['sub_dealer_id'];
				$inv_date=$row['inv_date'];
				$inv_qty=$row['inv_qty'];
				$delete_at=(int)$row['delete_at'];

				$sqlcustomer="SELECT customer_name from customer_master where customer_id='".$sub_dealer_id."'";
				$rscustomer=mysql_query($sqlcustomer);
				$rowcustomer=mysql_fetch_array($rscustomer);
				$customer_name=$rowcustomer['customer_name'];
				//echo"<pre>";print_r($sqlcustomer);

                $allocation_data[] = array("allocation_id" => $allocation_id, "prod_desc" => $prod_desc, "allocation_qty" => $allocation_qty, "date_and_time" => $date_and_time, "order_id" => $order_id, "inv_no" => $inv_no, "inv_date" => $inv_date, "counter_name" => $customer_name,"is_deleted"=>$delete_at);
            }

			//echo"<pre>";print_r($allocation_data);die;

            $response["allocation_data"] = $allocation_data;
        } else {

            $response["process_status"] = "NO";
            $response["process_message"] = "No allocation data found for the customer.";
        }
    }
}


echo json_encode($response);
?>

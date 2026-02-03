<?php
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$branch_master = "branch_master";
$arc_consumer_reg = "arc_consumer_reg";
$customer_master = "customer_master";

function get_branch_name_from_id($bid){
    $branch_master = "branch_master";
    $branchname = "";
    $bid = $bid ? addslashes(trim($bid)) : "";
    if($bid != ''){
        $sqls = "select `branch_name` from $branch_master where `branch_code`='$bid'";
        $ress = mysql_query($sqls);
        $totress = mysql_num_rows($ress);
        if($totress > 0){
            $rows = mysql_fetch_assoc($ress);
            $branchname = $rows["branch_name"] ? trim($rows["branch_name"]) : "";
        }
    }
    return $branchname;
}

$srch_dtls = $_GET["srch_dtls"] ? addslashes(trim($_GET["srch_dtls"])) : "";
$get_status = $_GET["status"] ? trim($_GET["status"]) : "";

error_log("Search Details: " . $srch_dtls);
error_log("Status: " . $get_status);

startCreatArcConsumerRegCsvfile($get_status, $srch_dtls);

function startCreatArcConsumerRegCsvfile($get_status, $srch_dtls){
    $branch_master = "branch_master";
    $arc_consumer_reg = "arc_consumer_reg";
    $customer_master = "customer_master";
    $get_status = $get_status ? trim($get_status) : "";
    $srch_dtls = $srch_dtls ? trim($srch_dtls) : "";
    $curr_date = date("jS_M_Y_h_m_s_A");

    $where_qry = "";
    if($get_status != ""){
        $where_qry .= " where $arc_consumer_reg.`status`='$get_status' ";
    }

    if($srch_dtls != ""){
        if($where_qry != ""){
            $where_qry .= " and ";
        } else {
            $where_qry .= " where ";
        }
        // $where_qry .= "($arc_consumer_reg.`name` like '%$srch_dtls%' or $arc_consumer_reg.`mobile` like '%$srch_dtls%' or $arc_consumer_reg.`dns_customer_code` like '%$srch_dtls%' or $customer_master.`customer_name` like '%$srch_dtls%')";

		$where_qry .= "($arc_consumer_reg.`name` like '%$srch_dtls%' or $arc_consumer_reg.`mobile` like '%$srch_dtls%' or $arc_consumer_reg.`dns_customer_code` like '%$srch_dtls%' or $customer_master.`customer_name` like '%$srch_dtls%' or $arc_consumer_reg.`S` like '%$srch_dtls%' or $arc_consumer_reg.`T` like '%$srch_dtls%' or $arc_consumer_reg.`A` like '%$srch_dtls%' or $arc_consumer_reg.`R` like '%$srch_dtls%' or $arc_consumer_reg.`lunch_box` like '%$srch_dtls%' or $arc_consumer_reg.`water bottle` like '%$srch_dtls%')";
    }

    $the_file_name = "arc_consumer_reg_".$get_status."_".$curr_date.".csv";

    $output = '';
    $output .= '"Consumer_Name","Consumer_Mobile","No_of_bags(ARC)","Status","Dealer_Code","Dealer_Name","Branch","Entry_Date","Entry_Time",Coupon S, Coupon T, Coupon A,Coupon R, Lunch Box,Water Bottle';
    $output .= "\n";

    $sql = "select $arc_consumer_reg.*,$customer_master.`customer_name`,$customer_master.`branch_code` from $arc_consumer_reg left join $customer_master on $arc_consumer_reg.`customer_code`=$customer_master.`customer_code` $where_qry order by $arc_consumer_reg.`entry_datetime` desc";
    $res = mysql_query($sql);
    $totres = mysql_num_rows($res);
    if($totres > 0){
        while ($row1 = mysql_fetch_assoc($res)) {
            $ac_id = $row1["ac_id"];
            $consumer_name = $row1["name"];
            $consumer_mobile = $row1["mobile"];
            $no_of_bags = $row1["no_of_bags"];
            $each_status = $row1["status"];
            $branch_code = $row1["branch_code"];
            $branch_name = get_branch_name_from_id($branch_code);
            $ar_customer_code = $row1["customer_code"];
            $ar_dns_customer_code = $row1["dns_customer_code"];
            $ar_customer_name = $row1["customer_name"];
            $entry_datetime = $row1["entry_datetime"];
            $entry_date = "";
            $entry_time = "";
            if($entry_datetime != ""){
                $entry_date = date("d-m-Y", strtotime($entry_datetime));
                $entry_time = date("h:i a", strtotime($entry_datetime));
            }
            $col_S = $row1["S"];
            $col_T = $row1["T"];
            $col_A = $row1["A"];
            $col_R = $row1["R"];
            $lunch_box = $row1["lunch_box"];
            $water_bottle = $row1["water_bottle"];

            $output .= '"'.$consumer_name.'","'.$consumer_mobile.'","'.$no_of_bags.'","'.$each_status.'","'.$ar_dns_customer_code.'","'.$ar_customer_name.'","'.$branch_name.'","'.$entry_date.'","'.$entry_time.'", '.$col_S.',"'.$col_T.'","'.$col_A.'","'.$col_R.'","'.$lunch_box.'","'.$water_bottle.'"';
            $output .= "\n";
        }
    }

    // Download the file
    $filename = $the_file_name;
    header('Content-type: application/csv');
    header('Content-Disposition: attachment; filename='.$filename);
    header('Pragma: no-cache');
    header('Expires: 0');
    echo $output;
    exit;
}

mysql_close();
?>

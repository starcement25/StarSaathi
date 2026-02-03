<?php
error_reporting(E_ALL);
ini_set('memory_limit', '512M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");

include "star_connection.php";

$t_apperpdo       = "T_APPERPDO";
$cron_update_table = "cron_update_table";
$process_name      = "make_order_status_cancel";

// -------------------------------------------------
// Helper: Track process start/end
// -------------------------------------------------
function process_update_tracking($process_name, $flag, $remark) {
    global $cron_update_table;
    $curr_datetime = date("Y-m-d H:i:s");
    $remark = addslashes(trim($remark));
    if ($flag === "START") {
        $sql = "UPDATE $cron_update_table 
                   SET last_start_datetime='$curr_datetime', remark='$remark' 
                 WHERE process_name='$process_name'";
    } elseif ($flag === "END") {
        $sql = "UPDATE $cron_update_table 
                   SET last_end_datetime='$curr_datetime', remark='$remark' 
                 WHERE process_name='$process_name'";
    }
    mysql_query($sql);
}

// -------------------------------------------------
// START Process
// -------------------------------------------------
process_update_tracking($process_name, "START", "Process Start");

$starfiori_port_no = $GLOBALS['starfiori_port_no'];
$curr_date = date("Y-m-d");
$app_order_id_arr = [];

for ($i = 0; $i <= 2; $i++) 
	{
    $the_date_time = date('Y-m-d\T00:00:00', strtotime("-$i days", strtotime($curr_date)));
    //$the_date_time = date('2025-09-09\T00:00:00', strtotime("-$i days", strtotime($curr_date)));

    // Build API URL
    $filter   = '&$'."filter=(erdat eq datetime'".$the_date_time."' and status eq 'CANCEL')";
     $url      = "https://starfiori.starcement.co.in:".$starfiori_port_no."/sap/opu/odata/sap/ZSD_PARKLOT_SO_SERV_CDS/ZSD_PARKLOT_SO_SERV?\$format=json".str_replace(" ","%20",$filter);
	//echo"<pre>";print_r($url);

    // Call API
    $response = get_data_from_cserver($url);
//echo"<pre>";print_r($response);die;
    if (isJsonCk($response)) {
        $json_decoded = json_decode($response, true);

        if (!empty($json_decoded["d"]["results"])) {
            foreach ($json_decoded["d"]["results"] as $row) {
                if (!empty($row["app_ref_no"])) {
                    $app_order_id_arr[] = trim($row["app_ref_no"]);
                }
            }
        }
    }
}

// -------------------------------------------------
// Update DB
// -------------------------------------------------
//echo"<pre>";print_r($app_order_id_arr);die;

if (!empty($app_order_id_arr)) { 
    $app_order_ids_str = implode("','", array_unique($app_order_id_arr));
   echo $sql_upd = "UPDATE $t_apperpdo 
                   SET STATUS='Order canceled' 
                 WHERE APPORDERNO IN('$app_order_ids_str')";
    mysql_query($sql_upd);
}

// -------------------------------------------------
// END Process
// -------------------------------------------------
process_update_tracking($process_name, "END", "Process End");

$res_data = ["process_status" => "YES", "process_message" => "DONE"];
echo json_encode($res_data);

mysql_close();
?>

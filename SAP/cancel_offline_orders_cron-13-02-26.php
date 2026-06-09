<?php
error_reporting(E_ALL);
ini_set('memory_limit', '512M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");

include "star_connection.php";

$t_apperpdo_offline = "T_APPERPDO_OFFLINE";
$allocation_table   = "allocation_details_invoicewise";

$starfiori_port_no = $GLOBALS['starfiori_port_no'];

$update_count = 0;

// 🔁 Loop for last 3 days
for($i=0; $i<10; $i++)
{
    $date_val = date('Y-m-d', strtotime("-$i days"));
    $the_date = $date_val . "T00:00:00";
    $the_filter = "&\$filter=(erdat eq datetime'".$the_date."')";

    $url = "https://starfiori.starcement.co.in:".$starfiori_port_no."/sap/opu/odata/sap/ZSD_VBAK_SO_SERV_CDS/ZSD_VBAK_SO_SERV?\$format=json".str_replace(" ","%20",$the_filter);

    $response = get_data_from_cserver($url);

    if(isJsonCk($response))
    {
        $json = json_decode($response, true);

        if(isset($json["d"]["results"]))
        {
            foreach($json["d"]["results"] as $row)
            {
                $vbeln = trim($row["vbeln"]);
                $abgru = trim($row["abgru"]);

                // ✅ If cancellation reason exists
                if($vbeln != "" && $abgru != "")
                {
                    // Update T_APPERPDO_OFFLINE
                    $sql1 = "UPDATE $t_apperpdo_offline 
                             SET cancel='yes' 
                             WHERE ERPORDERNO='$vbeln'";
                    mysql_query($sql1);

                    // Update allocation table (join condition)
                    $sql2 = "UPDATE $allocation_table a
                             JOIN $t_apperpdo_offline t 
                             ON t.ERPORDERNO = a.APPORDERNO
                             SET a.inv_cancl='yes'
                             WHERE t.ERPORDERNO='$vbeln'";
                    mysql_query($sql2);

                    //
                    $log_table = "offline_order_cancel_log";
                    $curr_datetime = date("Y-m-d H:i:s");

                    // Get apperpdo ID
                    $sql_get_app = "SELECT id FROM $t_apperpdo_offline 
                                    WHERE ERPORDERNO='$vbeln' LIMIT 1";
                    $res_app = mysql_query($sql_get_app);
                    $row_app = mysql_fetch_assoc($res_app);
                    $apperpdo_id = $row_app['id'];

                    // Get allocation_id
                    $sql_get_alloc = "SELECT allocation_id 
                                    FROM $allocation_table 
                                    WHERE APPORDERNO='$vbeln' LIMIT 1";
                    $res_alloc = mysql_query($sql_get_alloc);
                    $row_alloc = mysql_fetch_assoc($res_alloc);
                    $allocation_id = $row_alloc['allocation_id'];

                    // ✅ Direct insert (duplicate safe because UNIQUE key)
                    $sql_insert_log = "INSERT IGNORE INTO $log_table
                        (ERPORDERNO, apperpdo_id, allocation_id, log_datetime,api_date)
                        VALUES
                        ('$vbeln', '$apperpdo_id', '$allocation_id', '$curr_datetime','$date_val')";

                    mysql_query($sql_insert_log);

                    $update_count++;
                }
            }
        }
    }
}

echo json_encode([
    "status" => "SUCCESS",
    "cancel_updated_records" => $update_count
]);

mysql_close();
?>

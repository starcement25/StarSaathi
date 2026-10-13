
<?php
error_reporting(E_STRICT);
ini_set('memory_limit', '99999M');
set_time_limit(0);

date_default_timezone_set("Asia/Kolkata");

include "star_connection.php";
include "../cron_page_start.php";

$t_apperpdo          = "T_APPERPDO";
$t_doinvoice         = "T_DOINVOICE";
$customer_master     = "customer_master";

$curr_date_time = date("Y-m-d H:i:s");
$curr_date      = date("Ymd");

$in_cnt  = 0;
$upd_cnt = 0;

$res_data = array();
$process_message = "";


/*
|--------------------------------------------------------------------------
| SAVE LOG
|--------------------------------------------------------------------------
*/

function save_invoice_log(
    $customer_id,
    $invoice_no,
    $app_order_no,
    $api_url,
    $api_response,
    $log_type,
    $log_message
){

    $curr_date_time1 = date("Y-m-d H:i:s");

    $customer_id   = addslashes($customer_id);
    $invoice_no    = addslashes($invoice_no);
    $app_order_no  = addslashes($app_order_no);
    $api_url       = addslashes($api_url);
    $api_response  = addslashes($api_response);
    $log_type      = addslashes($log_type);
    $log_message   = addslashes($log_message);

    $sql_log = "INSERT INTO invoice_api_log_final SET

        customer_id   = '$customer_id',
        invoice_no    = '$invoice_no',
        app_order_no  = '$app_order_no',
        api_url       = '$api_url',
        api_response  = '$api_response',
        log_type      = '$log_type',
        log_message   = '$log_message',
        created_at    = '$curr_date_time1'";

    mysql_query($sql_log);
}



/*
|--------------------------------------------------------------------------
| DATE RANGE
|--------------------------------------------------------------------------
*/

$the_start_date_invoice = date('Ymd', strtotime("-7 days"));
$the_end_date_invoice   = $curr_date;


/*
|--------------------------------------------------------------------------
| CUSTOMER LOOP
|--------------------------------------------------------------------------
*/

$sqlcust = "SELECT customer_id
FROM $customer_master
WHERE acedns='Y'
AND cust_type='dealer'";

$rescust = mysql_query($sqlcust);

if(mysql_num_rows($rescust) > 0){

    while($rowcust = mysql_fetch_assoc($rescust)){


        $the_customer_id = trim($rowcust['customer_id']);

// 

        /*
        |--------------------------------------------------------------------------
        | API URL
        |--------------------------------------------------------------------------
        */

       /* $the_filter =
        '&$filter=(CustCo eq \''.$the_customer_id.'\'
        and (
            InvoiceDt ge \''.$the_start_date_invoice.'\'
            and InvoiceDt le \''.$the_end_date_invoice.'\'
        ))&sap-client=900';


        $url_ck1 =
        'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.
        '/sap/opu/odata/sap/ZSD_CUSTOMER_BULK_INVOICE_SRV/'.
        'ZSD_CUSTOMER_INVOICESet?$format=json'.
        str_replace(" ","%20",$the_filter);
*/
try{
$the_filter = '&$filter=(CustCo eq \''.$the_customer_id.'\' and ( InvoiceDt ge \''.$the_start_date_invoice.'\' and InvoiceDt le \''.$the_end_date_invoice.'\') )&sap-client=900';
 $url_ck1 = 'https://starfiori.starcement.co.in:'.SRARFIORI_PORT_NO.'/sap/opu/odata/sap/ZSD_CUSTOMER_BULK_INVOICE_SRV/ZSD_CUSTOMER_INVOICESet?$format=json'.str_replace(" ","%20",$the_filter);

        /*
        |--------------------------------------------------------------------------
        | API RESPONSE
        |--------------------------------------------------------------------------
        */

        $body_for_mcode10 = get_data_from_cserver($url_ck1);


        save_invoice_log(
            $the_customer_id,
            '',
            '',
            $url_ck1,
            $body_for_mcode10,
            'API_RESPONSE',
            'API response received successfully'
        );


        /*
        |--------------------------------------------------------------------------
        | JSON CHECK
        |--------------------------------------------------------------------------
        */

        if(!isJsonCk($body_for_mcode10)){

            save_invoice_log(
                $the_customer_id,
                '',
                '',
                $url_ck1,
                $body_for_mcode10,
                'INVALID_JSON',
                'API returned invalid JSON response'
            );

            continue;
        }


        $json_decoded21 = json_decode($body_for_mcode10, true);

        if(
            !isset($json_decoded21["d"]["results"])
            || count($json_decoded21["d"]["results"]) == 0
        ){
            continue;
        }


        $app_results_arr = $json_decoded21["d"]["results"];


        /*
        |--------------------------------------------------------------------------
        | UNIQUE ARRAY BY INVNO
        |--------------------------------------------------------------------------
        */

        $unique_invoice_arr = array();

        foreach($app_results_arr as $app_results_aval){

            $InvoiceNo = trim($app_results_aval["InvoiceNo"]);

            if($InvoiceNo == ""){
                continue;
            }

            /*
            SKIP DUPLICATE INVNO INSIDE SAME API RESPONSE
            */

            if(isset($unique_invoice_arr[$InvoiceNo])){
                continue;
            }


            $unique_invoice_arr[$InvoiceNo] = array(

                "InvoiceNo"      => trim($app_results_aval["InvoiceNo"]),
                "InvoiceDt"      => trim($app_results_aval["InvoiceDt"]),
                "ChallanNo"      => trim($app_results_aval["ChallanNo"]),
                "Description"    => trim($app_results_aval["Description"]),
                "Qty"            => trim($app_results_aval["Qty"]),
                "ConDest"        => trim($app_results_aval["ConDest"]),
                "VehicleNo"      => trim($app_results_aval["VehicleNo"]),
                "sale_order_no"  => trim($app_results_aval["DoNo"]),
                "APPORDERNO"     => trim($app_results_aval["CustPo"])

            );
        }



        /*
        |--------------------------------------------------------------------------
        | PROCESS UNIQUE DATA
        |--------------------------------------------------------------------------
        */

        foreach($unique_invoice_arr as $invoice_data){

            $InvoiceNo     = addslashes($invoice_data["InvoiceNo"]);
            $InvoiceDt     = addslashes($invoice_data["InvoiceDt"]);
            $ChallanNo     = addslashes($invoice_data["ChallanNo"]);
            $Description   = addslashes($invoice_data["Description"]);
            $Qty           = addslashes($invoice_data["Qty"]);
            $ConDest       = addslashes($invoice_data["ConDest"]);
            $VehicleNo     = addslashes($invoice_data["VehicleNo"]);
            $sale_order_no = addslashes($invoice_data["sale_order_no"]);
            $APPORDERNO    = addslashes($invoice_data["APPORDERNO"]);



            /*
            |--------------------------------------------------------------------------
            | LOCK TABLE
            |--------------------------------------------------------------------------
            */

            mysql_query("LOCK TABLES $t_doinvoice WRITE");


            /*
            |--------------------------------------------------------------------------
            | CHECK EXISTING
            |--------------------------------------------------------------------------
            */

            $sql1 = "SELECT id
            FROM $t_doinvoice
            WHERE INVNO='$InvoiceNo'
            LIMIT 1";

            $res1 = mysql_query($sql1);

            $totres1 = mysql_num_rows($res1);



            /*
            |--------------------------------------------------------------------------
            | UPDATE
            |--------------------------------------------------------------------------
            */

            if($totres1 > 0){

                $row1  = mysql_fetch_assoc($res1);
                $the_id = $row1["id"];


                $sql_clupd = "UPDATE $t_doinvoice SET

                    `APPORDERNO`      = '$APPORDERNO',
                    `ERPORDERNO`      = '$sale_order_no',
                    `ERPORDERDT`      = '',
                    `CHALLANDT`       = '',
                    `CHALLANNO`       = '$ChallanNo',
                    `INVQTY`          = '$Qty',
                    `customer_code`   = '$the_customer_id',
                    `TRUCKNO`         = '$VehicleNo',
                    `LMDT`            = '$curr_date_time',
                    `prod_display_name` = '$Description',
                    `INVNO`           = '$InvoiceNo',
                    `INVDT`           = '$InvoiceDt',
                    `destination`     = '$ConDest'

                WHERE id='$the_id'";

                $res_clupd = mysql_query($sql_clupd);


                if($res_clupd){

                    $upd_cnt++;

                    save_invoice_log(
                        $the_customer_id,
                        $InvoiceNo,
                        $APPORDERNO,
                        $url_ck1,
                        '',
                        'UPDATE',
                        'Invoice updated successfully'
                    );

                }else{

                    save_invoice_log(
                        $the_customer_id,
                        $InvoiceNo,
                        $APPORDERNO,
                        $url_ck1,
                        mysql_error(),
                        'UPDATE_ERROR',
                        'Failed to update invoice'
                    );
                }


            }else{


                /*
                |--------------------------------------------------------------------------
                | INSERT
                |--------------------------------------------------------------------------
                */

                echo $sql_clin = "INSERT INTO $t_doinvoice SET

                    `APPORDERNO`      = '$APPORDERNO',
                    `ERPORDERNO`      = '$sale_order_no',
                    `ERPORDERDT`      = '',
                    `CHALLANDT`       = '',
                    `CHALLANNO`       = '$ChallanNo',
                    `INVQTY`          = '$Qty',
                    `customer_code`   = '$the_customer_id',
                    `TRUCKNO`         = '$VehicleNo',
                    `LMDT`            = '$curr_date_time',
                    `prod_display_name` = '$Description',
                    `INVNO`           = '$InvoiceNo',
                    `INVDT`           = '$InvoiceDt',
                    `destination`     = '$ConDest'";

			echo "</br>";
                $res_clin = mysql_query($sql_clin);


                if($res_clin){

                    $in_cnt++;

                    save_invoice_log(
                        $the_customer_id,
                        $InvoiceNo,
                        $APPORDERNO,
                        $url_ck1,
                        '',
                        'INSERT',
                        'Invoice inserted successfully'
                    );

                }else{

                    save_invoice_log(
                        $the_customer_id,
                        $InvoiceNo,
                        $APPORDERNO,
                        $url_ck1,
                        mysql_error(),
                        'INSERT_ERROR',
                        'Failed to insert invoice'
                    );
                }
            }



            /*
            |--------------------------------------------------------------------------
            | UNLOCK TABLE
            |--------------------------------------------------------------------------
            */

            mysql_query("UNLOCK TABLES");



            /*
            |--------------------------------------------------------------------------
            | UPDATE ORDER STATUS
            |--------------------------------------------------------------------------
            */

            $sqlupdateorderstat = "UPDATE $t_apperpdo
            SET status='Dispatched'
            WHERE APPORDERNO='$APPORDERNO'";

            mysql_query($sqlupdateorderstat);

        }
}catch(Exception $e){
    echo $e->getMessage();
   echo $the_customer_id;
   break;
}
    }
}



/*
|--------------------------------------------------------------------------
| FINAL RESPONSE
|--------------------------------------------------------------------------
*/

$process_message =
$in_cnt." record inserted, ".$upd_cnt." record updated";

$res_data = array(

    "process_status"  => "YES",
    "process_message" => $process_message

);

echo json_encode($res_data);
include "invoice_sync_cron.php";
include "../cron_page_end.php";

mysql_close();

?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);
ini_set('memory_limit', '1024M');

date_default_timezone_set("Asia/Kolkata");

include "star_connection.php";

$t_apperpdo = "T_APPERPDO";

$response = array();
$response['updated'] = 0;
$response['skipped'] = 0;
$response['errors'] = 0;
$response['data'] = array();

/*
|--------------------------------------------------------------------------
| Find records having 'A' in ERPORDERNO
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT
        id,
        APPORDERNO,
        ERPORDERNO
    FROM $t_apperpdo
    WHERE ERPORDERNO LIKE '%A%' 
    
    ORDER BY ERPORDERNO ASC
";

$res = mysql_query($sql);

if(!$res)
{
    die(json_encode(array(
        "status" => "ERROR",
        "message" => mysql_error()
    )));
}

while($row = mysql_fetch_assoc($res))
{
    $id          = trim($row['id']);
    $appOrderNo  = trim($row['APPORDERNO']);
    $oldERPNo    = trim($row['ERPORDERNO']);

    if(empty($appOrderNo))
    {
        $response['skipped']++;

        $response['data'][] = array(
            "id" => $id,
            "apporderno" => $appOrderNo,
            "old_erporderno" => $oldERPNo,
            "status" => "SKIPPED",
            "message" => "APPORDERNO Blank"
        );

        continue;
    }

    try
    {
        $starfiori_port_no = $GLOBALS['starfiori_port_no'];

        $url =
        "https://starfiori.starcement.co.in:" .
        $starfiori_port_no .
        "/sap/opu/odata/sap/ZSD_PARKLOT_SO_SERV_CDS/" .
        "ZSD_PARKLOT_SO_SERV('" .
        $appOrderNo .
        "')?\$format=json&sap-client=900";

        echo "<br>Processing : ".$appOrderNo;

        $apiResponse = get_data_from_cserver($url);

        if(!isJsonCk($apiResponse))
        {
            $response['errors']++;

            $response['data'][] = array(
                "id" => $id,
                "apporderno" => $appOrderNo,
                "status" => "ERROR",
                "message" => "Invalid JSON Response"
            );

            continue;
        }

        $json = json_decode($apiResponse, true);

        if(
            !isset($json['d']) ||
            !isset($json['d']['so_num'])
        )
        {
            $response['errors']++;

            $response['data'][] = array(
                "id" => $id,
                "apporderno" => $appOrderNo,
                "status" => "ERROR",
                "message" => "so_num Not Found"
            );

            continue;
        }

        $so_num = trim($json['d']['so_num']);

        if($so_num == "")
        {
            $response['skipped']++;

            $response['data'][] = array(
                "id" => $id,
                "apporderno" => $appOrderNo,
                "status" => "SKIPPED",
                "message" => "Blank so_num"
            );

            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | Update ERPORDERNO
        |--------------------------------------------------------------------------
        */
        $updateSql = "
            UPDATE $t_apperpdo
            SET ERPORDERNO = '".addslashes($so_num)."'
            WHERE id = '$id'
        ";

        if(mysql_query($updateSql))
        {
            $response['updated']++;

            $response['data'][] = array(
                "id" => $id,
                "apporderno" => $appOrderNo,
                "old_erporderno" => $oldERPNo,
                "new_erporderno" => $so_num,
                "status" => "UPDATED"
            );

            echo "<br><b>UPDATED</b> : ".$oldERPNo." => ".$so_num;
        }
        else
        {
            $response['errors']++;

            $response['data'][] = array(
                "id" => $id,
                "apporderno" => $appOrderNo,
                "status" => "ERROR",
                "message" => mysql_error()
            );
        }
    }
    catch(Exception $e)
    {
        $response['errors']++;

        $response['data'][] = array(
            "id" => $id,
            "apporderno" => $appOrderNo,
            "status" => "ERROR",
            "message" => $e->getMessage()
        );
    }
}

$response['status'] = "SUCCESS";
$response['total_records'] =
    $response['updated'] +
    $response['skipped'] +
    $response['errors'];

echo "<pre>";
print_r($response);
echo "</pre>";

mysql_close();
?>
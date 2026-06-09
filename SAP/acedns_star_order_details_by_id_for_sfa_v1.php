<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-type: text/html; charset=utf-8');
include "star_connection.php";
mysql_set_charset("UTF8");
$T_APPERPDO         = "T_APPERPDO";
$T_DOINVOICE        = "T_DOINVOICE";
$res_data           = array();
$order_data         = array();
$order_challan_data = array();
$the_id = $_REQUEST["the_id"] ? addslashes(trim($_REQUEST["the_id"])) : "";

if ($the_id != "") {

    $sqlall = "
        SELECT 
            APPORDERNO,
            ERPORDERNO,
            ERPORDERDT,
            order_date,
            order_for,
            customer_code,
            dns_customer_code,
            STATUS,
            prod_code,
            dns_prod_code,
            prod_display_name,
            QTY,
            sale_order_qty,
            freight,
            dump_name,
            destination_address,
            is_confirmed_material_received,
            quantity_checking,
            quality_checking,
            remarks,
            phone_no
        FROM $T_APPERPDO
        WHERE dns_customer_code = '$the_id'
        ORDER BY order_date DESC
    ";
    $resall = mysql_query($sqlall);
    $totall = mysql_num_rows($resall);

    if ($totall > 0) {

        // ── Step 1: Collect all rows and APPORDERNOs ──
        $all_rows        = array();
        $apporderno_list = array();
        while ($row_tmp = mysql_fetch_assoc($resall)) {
            $all_rows[] = $row_tmp;
            if (!empty($row_tmp['APPORDERNO'])) {
                $apporderno_list[] = "'" . addslashes(trim($row_tmp['APPORDERNO'])) . "'";
            }
        }

        // ── Step 2: Fetch plant map ONCE for all orders ──
        $plant_map = array();
        if (!empty($apporderno_list)) {
            $in_clause = implode(",", $apporderno_list);
            $sql_plant_map = "
                SELECT z.APPORDERNO, p.plant_name
                FROM zorderdetailsp z
                LEFT JOIN plant_list_cement p ON p.plant_code = z.PLANT
                WHERE z.APPORDERNO IN ($in_clause)
            ";
            $res_plant_map = mysql_query($sql_plant_map);
            if ($res_plant_map) {
                while ($pm = mysql_fetch_assoc($res_plant_map)) {
                    $plant_map[trim($pm['APPORDERNO'])] = trim($pm['plant_name']);
                }
            }
        }

        // ── Step 3: Loop stored rows ──
        foreach ($all_rows as $row11) {

            $apporderno = $row11["APPORDERNO"] ? trim($row11["APPORDERNO"]) : "";
            $erporderno = $row11["ERPORDERNO"] ? trim($row11["ERPORDERNO"]) : "";
            $erporderdt = $row11["ERPORDERDT"];
            $isdoimp    = "";

            // ── Plant name logic ──
            $freight   = $row11["freight"]   ? trim($row11["freight"])   : "";
            $dump_name = $row11["dump_name"] ? trim($row11["dump_name"]) : "";

            if ($freight == 'FOR') {
                $plant_name = isset($plant_map[$apporderno]) ? $plant_map[$apporderno] : "";
            } elseif ($freight == 'EXW') {
                $plant_name = $dump_name;
            } else {
                $plant_name = "";
            }

            $order_for = $row11["order_for"] ? trim($row11["order_for"]) : "";
            if ($order_for != "") {
                if (strpos($order_for, ",") !== false) {
                    $ofrarr = array();
                    $ofrarr = explode(",", $order_for);
                    if (count($ofrarr) > 0) {
                        $order_for = $ofrarr[0];
                    }
                }
            }

            $customer_code     = $row11["customer_code"];
            $dns_customer_code = $row11["dns_customer_code"];
            $status            = $row11["STATUS"];

            $order_date = $row11["order_date"] ? trim($row11["order_date"]) : "";
            if ($order_date != "") {
                $order_full_date_time = date("jS M Y h:i A", strtotime($order_date));
            } else {
                $order_full_date_time = "";
            }

            $prod_code         = $row11["prod_code"];
            $dns_prod_code     = $row11["dns_prod_code"];
            $prod_display_name = $row11["prod_display_name"];
            $qty               = $row11["QTY"];

            $destination_address            = $row11["destination_address"]            ? trim($row11["destination_address"])            : "";
            $is_confirmed_material_received = $row11["is_confirmed_material_received"] ? trim($row11["is_confirmed_material_received"]) : "";
            $quantity_checking              = $row11["quantity_checking"]              ? trim($row11["quantity_checking"])              : "";
            $quality_checking               = $row11["quality_checking"]               ? trim($row11["quality_checking"])               : "";
            $remarks                        = $row11["remarks"]                        ? trim($row11["remarks"])                        : "";
            $phone_no                       = $row11["phone_no"];

            $the_actual_ord_id  = $apporderno ? addslashes($apporderno) : addslashes($erporderno);
            $order_challan_data = array();

            if ($the_actual_ord_id != "") {
                $sqlall2 = "SELECT * FROM $T_DOINVOICE
                            WHERE (`APPORDERNO`='$the_actual_ord_id' OR `ERPORDERNO`='$the_actual_ord_id')";
                $resall2 = mysql_query($sqlall2);
                $totall2 = mysql_num_rows($resall2);

                if ($totall2 > 0) {
                    while ($row112 = mysql_fetch_assoc($resall2)) {
                        $inv_apporderno    = $row112["APPORDERNO"]        ? trim($row112["APPORDERNO"])        : "";
                        $inv_erporderno    = $row112["ERPORDERNO"]        ? trim($row112["ERPORDERNO"])        : "";
                        $challan_no        = $row112["CHALLANNO"]         ? trim($row112["CHALLANNO"])         : "";
                        $inv_no            = $row112["INVNO"]             ? trim($row112["INVNO"])             : "";
                        $inv_date          = $row112["INVDT"]             ? trim($row112["INVDT"])             : "";
                        $inv_date          = date('Y-m-d', strtotime($inv_date));
                        $prod_display_name = $row112["prod_display_name"] ? trim($row112["prod_display_name"]) : "";
                        $inv_qty           = $row112["INVQTY"]            ? trim($row112["INVQTY"])            : "";
                        $customer_code     = $row112["customer_code"];
                        $truckno           = $row112["TRUCKNO"]           ? trim($row112["TRUCKNO"])           : "";

                        $order_challan_data[] = array(
                            "apporderno"        => $inv_apporderno,
                            "erporderno"        => $inv_erporderno,
                            "challanno"         => $challan_no,
                            "invno"             => $inv_no,
                            "invdt"             => $inv_date,
                            "prod_display_name" => $prod_display_name,
                            "invqty"            => $inv_qty,
                            "customer_code"     => $customer_code,
                            "truckno"           => $truckno,
                            "ischlnimp"         => ""
                        );
                    }
                }
            }

            // ── Response array - original order preserved ──
            $order_data[] = array(
                "apporderno"                     => $apporderno,
                "erporderno"                     => $erporderno,
                "erporderdt"                     => $erporderdt,
                "isdoimp"                        => $isdoimp,
                "order_for"                      => $order_for,
                "customer_code"                  => $customer_code,
                "dns_customer_code"              => $dns_customer_code,
                "status"                         => $status,
                "prod_code"                      => $prod_code,
                "dns_prod_code"                  => $dns_prod_code,
                "prod_display_name"              => $prod_display_name,
                "qty"                            => $qty,
                "order_full_date_time"           => $order_full_date_time,
                "order_challan_data"             => $order_challan_data,
                "freight"                        => $freight,
                "plant_name"                     => $plant_name,    // ✅ added after freight
                "destination_address"            => $destination_address,
                "is_confirmed_material_received" => $is_confirmed_material_received,
                "quantity_checking"              => $quantity_checking,
                "quality_checking"               => $quality_checking,
                "remarks"                        => $remarks
            );
        }

        $res_data = array(
            "process_status"  => "YES",
            "process_message" => "Success.",
            "order_data"      => $order_data
        );

    } else {
        $res_data = array(
            "process_status"  => "NO",
            "process_message" => "No order data found."
        );
    }

} else {
    $res_data = array(
        "process_status"  => "NO",
        "process_message" => "The id is mandatory."
    );
}

echo json_encode($res_data);
mysql_close();
?>
<?php
error_reporting(E_STRICT);
ini_set('memory_limit', '99999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include "star_connection.php";

$customer_master = "customer_master";
$product_master  = "product_master";

$customer_code = $_REQUEST['customer_code'];
$year  = $_REQUEST['year'];
$month = $_REQUEST['month'];

// ================= CUSTOMER TYPE =================
$cust_type = "SELECT cust_type FROM $customer_master WHERE customer_id='$customer_code'";
$result = mysql_query($cust_type);
$row = mysql_fetch_assoc($result);

// =================================================
// ================= DEALER =========================
// =================================================
if ($row['cust_type'] == 'Dealer') {

    $url = 'https://starfiori.starcement.co.in:' . SRARFIORI_PORT_NO .
        '/sap/opu/odata/sap/ZOVW_CUSTTGTACH_CDS/ZOVW_CUSTTGTACH(p_kunnr=\'' . $customer_code .
        '\',p_yr=\'' . $year . '\',p_mnth=\'' . $month . '\')/Set?$format=json&sap-client=900';

    $response = get_data_from_cserver($url);

    if (isJsonCk($response)) {

        $json = json_decode($response, true);

        if (isset($json['d']['results']) && count($json['d']['results']) > 0) {

            $results = $json['d']['results'];

            // 🔥 Load product master once
            $product_map = [];
            $res_prod = mysql_query("SELECT dns_prod_code, prod_desc FROM $product_master");
            while ($row_prod = mysql_fetch_assoc($res_prod)) {
                $product_map[$row_prod['dns_prod_code']] = $row_prod['prod_desc'];
            }

            // 🔥 GROUP DATA BY BASE ITEMCODE
            $grouped_data = [];

            foreach ($results as $r) {

                $bukrs = trim($r["bukrs"]);
                $itemcode = trim($r["itemcode"]); // base itemcode

                $TGTQTY = isset($r["TGTQTY"]) ? (float)$r["TGTQTY"] : 0;
                $ACHQTY = isset($r["ACHQTY"]) ? (float)$r["ACHQTY"] : 0;

                // item type
                $item_type = "";
                if ($bukrs == "1010") $item_type = "SCL";
                if ($bukrs == "1017") $item_type = "SCNEL";

                // product name
                $prod_desc = isset($product_map[$itemcode]) ? $product_map[$itemcode] : '';

                // INIT
                if (!isset($grouped_data[$itemcode])) {
                    $grouped_data[$itemcode] = [
                        "bukrs" => [],
                        "customercode" => $customer_code,
                        "itemcode" => $itemcode,
                        "yr" => $year,
                        "MNTH" => $month,
                        "TGTQTY" => 0,
                        "ACHQTY" => 0,
                        "itemname" => $prod_desc,
                        "item_type" => []
                    ];
                }

                // SUM
                $grouped_data[$itemcode]["TGTQTY"] += $TGTQTY;
                $grouped_data[$itemcode]["ACHQTY"] += $ACHQTY;

                // COLLECT bukrs
                if (!in_array($bukrs, $grouped_data[$itemcode]["bukrs"])) {
                    $grouped_data[$itemcode]["bukrs"][] = $bukrs;
                }

                // COLLECT item_type
                if (!in_array($item_type, $grouped_data[$itemcode]["item_type"])) {
                    $grouped_data[$itemcode]["item_type"][] = $item_type;
                }
            }

            // FINAL FORMAT
            $target_ach_data = [];

            foreach ($grouped_data as $g) {
                $target_ach_data[] = [
                    "bukrs" => implode("+", $g["bukrs"]),
                    "customercode" => $g["customercode"],
                    "itemcode" => $g["itemcode"],
                    "yr" => $g["yr"],
                    "MNTH" => $g["MNTH"],
                    "TGTQTY" => number_format($g["TGTQTY"], 2, '.', ''),
                    "ACHQTY" => number_format($g["ACHQTY"], 2, '.', ''),
                    "itemname" => $g["itemname"],
                    "item_type" => implode("+", $g["item_type"])
                ];
            }

            // SORT
            usort($target_ach_data, function ($a, $b) {
                return strcmp($a['itemcode'], $b['itemcode']);
            });

            echo json_encode([
                "process_status" => "YES",
                "process_message" => "Success",
                "target_ach_data" => $target_ach_data
            ]);

        } else {
            echo json_encode(["process_status" => "NO", "process_message" => "No Records Found"]);
        }

    } else {
        echo json_encode(["process_status" => "NO", "process_message" => "Invalid API Response"]);
    }

// =================================================
// ================= SUB DEALER =====================
// =================================================
} else {

    $sub_dealer_sap_code = $customer_code;

    $monthNum = intval($month);
    $monthName = date("F", mktime(0, 0, 0, $monthNum, 1));

    $sql = "
    SELECT 
        p.product_name,
        '$sub_dealer_sap_code' AS sub_dealer_sap_code,
        COALESCE(SUM(l.allocated_qty), 0) AS total_allocated_qty
    FROM (
        SELECT DISTINCT product_name
        FROM lifting_final_allocation_data 
        WHERE product_name IS NOT NULL 
          AND product_name <> ''
    ) p
    LEFT JOIN lifting_final_allocation_data l
        ON l.product_name = p.product_name
       AND l.sub_dealer_sap_code = '" . mysql_real_escape_string($sub_dealer_sap_code) . "'
       AND file_upload_id IN (
            SELECT id 
            FROM lifting_final_file_upload_csv 
            WHERE year = '$year' 
              AND month = '$monthName'
        )
    GROUP BY p.product_name
    HAVING total_allocated_qty > 0
    ORDER BY p.product_name
    ";

    $result = mysql_query($sql);

    $data = [];

    if ($result && mysql_num_rows($result) > 0) {
        while ($row = mysql_fetch_assoc($result)) {

            $data[] = [
                "bukrs" => "",
                "itemcode" => "",
                "item_type" => "",
                "customercode" => $row['sub_dealer_sap_code'],
                "yr" => $year,
                "MNTH" => $monthName,
                "TGTQTY" => "0.00",
                "ACHQTY" => $row['total_allocated_qty'],
                "itemname" => $row['product_name']
            ];
        }

        echo json_encode([
            "process_status" => "YES",
            "process_message" => "Success",
            "month" => $monthName,
            "year" => $year,
            "target_ach_data" => $data
        ]);

    } else {
        echo json_encode([
            "process_status" => "NO",
            "process_message" => "No records found"
        ]);
    }
}
?>
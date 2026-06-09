<?php
header("Content-Type: text/plain");
date_default_timezone_set('Asia/Kolkata');

include_once("star_connection.php");

/* ===== GET PARAM ===== */
$customer_code = isset($_GET['customer_code']) ? trim($_GET['customer_code']) : '';

if ($customer_code == '') {
    echo "Invalid Customer Code";
    exit;
}

/* ===== HEADER ===== */
echo "12¥8\n";
echo date('Y-m-d') . "€" . date('H:i:s') . "\n";

/* ===== FINANCIAL YEAR BASE ===== */
$current_year  = date('Y');
$current_month = date('n');

// Financial Year (Apr start)
$fy = ($current_month >= 4) ? $current_year : $current_year - 1;

/* ===== FETCH DATA ===== */
$data = array();

$sql = "
SELECT month, year, target_qty, achievement_qty
FROM month_wise_achievement_new
WHERE customer_code = '$customer_code'
";

$res = mysql_query($sql);

while ($row = mysql_fetch_assoc($res)) {
    $m = $row['month'];
    $y = $row['year'];

    $data[$m][$y] = array(
        'target' => $row['target_qty'],
        'ach'    => $row['achievement_qty']
    );
}

/* ===== LOOP 12 MONTHS ===== */
for ($m = 1; $m <= 12; $m++) {

    $month = str_pad($m, 2, '0', STR_PAD_LEFT);

    // ✅ FINANCIAL YEAR MAPPING
    if ($m >= 1 && $m <= 3) {
        // Jan–Mar
        $cur_year = $fy + 1;
        $pre_year = $fy;
    } else {
        // Apr–Dec
        $cur_year = $fy;
        $pre_year = $fy - 1;
    }

    // Current Year Data
    $cur_target = isset($data[$m][$cur_year]) ? $data[$m][$cur_year]['target'] : 0;
    $cur_ach    = isset($data[$m][$cur_year]) ? $data[$m][$cur_year]['ach'] : 0;

    // Previous Year Data
    $pre_target = isset($data[$m][$pre_year]) ? $data[$m][$pre_year]['target'] : 0;
    $pre_ach    = isset($data[$m][$pre_year]) ? $data[$m][$pre_year]['ach'] : 0;

    echo $month . "^" .
         $cur_target . "^" .
         $cur_ach . "^" .
         $pre_target . "^" .
         $pre_ach . "\n";
}
?>
<?php 
include "web_check.php";
include "star_connection.php";
include "web_header.php";
?>

<style>
    body { font-family: Arial; margin:0; background:#f4f4f4; }
    .topbar { background:#fff; color:#000; padding:14px 16px; font-size:18px; }
    .container { max-width:740px; margin:auto; padding:10px; }

    .month-box { background:#fff; padding:10px; border-radius:6px; margin-bottom:12px; }
    select { width:100%; padding:10px; border-radius:6px; border:1px solid #ccc; }

    .dealer-row { background:#fbe3e3; padding:14px; border-radius:6px; margin-top:10px; cursor:pointer; display:flex; justify-content:space-between; }
    .dealer-name { font-weight:bold; }
    .chev { font-size:22px; }

    .detail-card { background:#fff; padding:14px; border-radius:6px; margin-top:6px; display:none; border:1px solid #eee; }

    .line { padding:4px 0; font-size:14px; }
</style>
<?php
session_start();
$customer_master = "customer_master";

// ----------------------------------------------
//  SETTINGS
// ----------------------------------------------
$API_URL = BASE_URL."ajax_allocation_lifting_invoicewise_v10.php"; // CHANGE THIS
$sswa_selected_customer_code = isset($_SESSION["sswa_selected_customer_code"]) ? $_SESSION["sswa_selected_customer_code"] : "";
// fetch customer_id
$sql3 = "select `customer_id` from $customer_master where `customer_code`='$sswa_selected_customer_code'";
$res3 = mysql_query($sql3);
$row3 = mysql_fetch_assoc($res3);
$customer_id = trim($row3["customer_id"]);
//echo"<pre>";print_r($the_customer_id);die;

$user_type   = "DEALER";

// selected month (YYYY-MM)
$year_month = isset($_GET["year_month"]) ? $_GET["year_month"] : date("Y-m");

// ----------------------------------------------
//  FETCH DATA USING PHP cURL
// ----------------------------------------------
function call_api($url, $postData) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 40);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

$postData = [
    "customer_id" => $customer_id,
    "user_type"   => $user_type,
    "year_month"  => $year_month
];
//echo"<pre>";print_r($postData);die;

$api_response_raw = call_api($API_URL, $postData);
$api = json_decode($api_response_raw, true);

// If API fails
$allocation_data = [];
if ($api && $api["process_status"] == "YES") {
    $allocation_data = $api["allocation_data"];
}
?>
<section class="content">
<div class="container-fluid" style="padding:0px 2px;">
<div class="card">
	<div class="header">
        <div class="body">
        <div class="topbar">ALLOCATED (<?= htmlspecialchars(date("M, Y", strtotime($year_month))) ?>)</div>

<div class="container">

    <!-- Month Selector -->
    <div class="month-box">
        <form method="GET">
            <label>Select Month</label>
            <select name="year_month" onchange="this.form.submit()">
                <?php
                // show last 18 months
                for ($i=0; $i<18; $i++) {
                    $m = date("Y-m", strtotime("-$i month"));
                    $label = date("M, Y", strtotime("-$i month"));
                    echo "<option value='$m' ".($year_month==$m?'selected':'').">$label</option>";
                }
                ?>
            </select>
        </form>
    </div>
    <?php
// --- Group allocation by counter_name ---
$grouped = [];

foreach ($allocation_data as $row) {
    $grouped[$row["counter_name"]][] = $row;
}
?>

<div id="list">

<?php if (empty($grouped)) : ?>

    <div style="padding:20px;text-align:center;color:#777;">
        No allocation found for <?= htmlspecialchars($year_month) ?>.
    </div>

<?php else: ?>

    <?php foreach ($grouped as $counter_name => $items): ?>

        <?php $row_id = md5($counter_name); ?>

        <!-- HEADER ROW -->
        <div class="dealer-row" data-id="<?= $row_id ?>">
            <div class="dealer-name"><?= htmlspecialchars($counter_name) ?></div>
            <div class="chev">▾</div>
        </div>

        <!-- MULTIPLE ALLOCATIONS INSIDE THE GROUP -->
        <div class="detail-card" id="detail_<?= $row_id ?>" style="display:none;">

            <?php foreach ($items as $r): ?>

                <div style="background:#fff;border:1px solid #eee;padding:12px;border-radius:8px;margin-bottom:10px;">
                    <div class="line"><b>Product:</b> <?= htmlspecialchars($r["prod_desc"]) ?></div>
                    <div class="line"><b>Allocated Qty:</b> <?= htmlspecialchars($r["allocation_qty"]) ?></div>
                    <div class="line"><b>Trans Date:</b> <?= htmlspecialchars($r["date_and_time"]) ?></div>
                    <div class="line"><b>Invoice No:</b> <?= htmlspecialchars($r["inv_no"]) ?></div>
                    <div class="line"><b>Invoice Date:</b> <?= htmlspecialchars($r["inv_date"]) ?></div>
                    <!-- <div class="line"><b>Order ID:</b> <?= htmlspecialchars($r["order_id"]) ?></div> -->
                </div>

            <?php endforeach; ?>

        </div>

    <?php endforeach; ?>

<?php endif; ?>

</div>

</div>
</div>

<script>
$(document).on("click", ".dealer-row", function() {
    var id = $(this).data("id");
    $("#detail_" + id).slideToggle(200);

    var chev = $(this).find(".chev");
    chev.text(chev.text() == "▾" ? "▴" : "▾");
});
</script>
</div>
</div>
</div>

<?php
include "web_footer.php";
mysql_close();
?>

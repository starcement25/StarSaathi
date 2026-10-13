<?php
//echo "This cron stop some time";die;
/**
 * ---------------------------------------------------------------------------
 * AUTO ALLOCATION CRON
 * ---------------------------------------------------------------------------
 * Picks up RSSD (sub-dealer) invoices from T_DOINVOICE and creates the
 * matching allocation rows in `allocation_details_invoicewise` automatically, so dealers
 * do not have to allocate every invoice by hand.
 *
 * Rows written here are stamped with `auto_allocation` = 'Yes' so they can be
 * told apart from allocations made through the dashboard.
 *
 * Duplicate protection
 *   A row is skipped when ANY allocation already exists for the key
 *      APPORDERNO + inv_no + prod_desc + customer_id + sub_dealer_id
 *   regardless of whether that existing row was created manually or by an
 *   earlier run of this cron. This makes the cron safe to re-run and safe to
 *   overlap with manual allocation.
 *
 * Date range
 *   Default : last AUTO_ALLOC_LOOKBACK_DAYS days up to today (INVDT based).
 *   Manual  : CLI  ->  php auto_allocation_cron.php 20260825
 *                      php auto_allocation_cron.php 20260820 20260825
 *             HTTP ->  auto_allocation_cron.php?inv_date=20260825
 *                      auto_allocation_cron.php?from_date=20260820&to_date=20260825
 *             (both YYYYMMDD and YYYY-MM-DD are accepted)
 *
 * Schedule (3 times a day, IST) - add to crontab:
 *   0 9,14,20 * * * /usr/bin/php /var/www/html/SAP/auto_allocation_cron.php >/dev/null 2>&1
 *
 * Logging (three layers)
 *   1. File   : SAP/cron_logs/auto_allocation/auto_allocation_YYYY-MM-DD.log
 *               every row, every skip reason, plus a run summary.
 *   2. Table  : `auto_allocation_log` - one queryable row per record acted on,
 *               carrying run_id, status (INSERTED/SKIPPED/FAILED), reason and
 *               the resulting allocation_id.
 *   3. Heartbeat : `cron_update_table` via cron_log_helper.php.
 * ---------------------------------------------------------------------------
 */

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 1);
ini_set('memory_limit', '1024M');
set_time_limit(0);
date_default_timezone_set('Asia/Kolkata');

include "star_connection.php";
include "cron_log_helper.php";
/* ------------------------------------------------------------------ config */

// Optional manual override: when this array is non-empty the run is restricted
// to these APPORDERNOs only (the INVDT range still applies). Leave it empty
// (array()) for a normal scheduled run.
$array_APPORDERNO = array();


$allocation_details   = "allocation_details_invoicewise";
$T_DOINVOICE          = "T_DOINVOICE";
$T_APPERPDO           = "T_APPERPDO";
$T_APPERPDO_OFFLINE   = "T_APPERPDO_OFFLINE";
$customer_master      = "customer_master";

define('AUTO_ALLOC_PROCESS_NAME', 'auto_allocation_cron');

// How many days back from today to scan when no manual date is supplied.
define('AUTO_ALLOC_LOOKBACK_DAYS', 2);

// Hard floor on T_DOINVOICE.INVDT (YYYYMMDD). Invoices dated before this are
// never auto allocated, even when a manual date range is supplied.
define('AUTO_ALLOC_MIN_INVDT', '20260915');

// Only these sub-dealer customer types are auto allocated.
// Kept as a plain array (not a define) because define() with an array value
// needs PHP 7+, and this script still runs on the mysql_* extension. 'Ship to Party-dealer',
$AUTO_ALLOC_CUST_TYPES = array(
    'RSSD',
    
    'ShiptoParty-Subdeale',
    'Sub Dealer',
);

// Value written into the allocation_details_invoicewise.auto_allocation flag column.
define('AUTO_ALLOC_FLAG_COLUMN', 'auto_allocation');
define('AUTO_ALLOC_FLAG_YES', 'Yes');
define('AUTO_ALLOC_FLAG_NO', 'No');

// Format used when writing INVDT into allocation_details_invoicewise.inv_date.
// INVDT arrives as YYYYMMDD; the column is a DATE and the manual screen
// (dashboard/save_allocation.php) writes 'Y-m-d' - so we match that.
define('AUTO_ALLOC_INV_DATE_FORMAT', 'Y-m-d');

// Value written into allocation_details_invoicewise.is_offline, mirroring
// save_allocation_details_invoicewise.php.
define('AUTO_ALLOC_OFFLINE_YES', 'Offline');
define('AUTO_ALLOC_OFFLINE_NO', 'Online');

// Honour the same business rules the manual screen enforces.
define('AUTO_ALLOC_CHECK_ACEDNS', true);           // skip inactive customers
define('AUTO_ALLOC_CHECK_APPOINTMENT_DATE', true); // appointment date <= invoice date

define('AUTO_ALLOC_LOG_DIR', __DIR__ . '/cron_logs/auto_allocation');
define('AUTO_ALLOC_LOCK_FILE', __DIR__ . '/cron_logs/auto_allocation/.auto_allocation.lock');

// Per-record audit table. Every row the cron acts on gets an entry here with
// its outcome, so allocations can be traced back without grepping log files.
define('AUTO_ALLOC_LOG_TABLE', 'auto_allocation_log');

// Whether to also log rows skipped because they are ALREADY allocated.
// The 3-day window is rescanned 3 times a day, so an invoice line reports
// "already allocated" ~9 times before it falls out of the window. Logging
// those would grow this table roughly 10x faster than it grows useful rows,
// so they are off by default - the file log still records every one of them.
define('AUTO_ALLOC_LOG_DUPLICATE_SKIPS', false);

$AUTO_ALLOC_IS_CLI = (php_sapi_name() === 'cli');

/* ----------------------------------------------------------------- logging */

$GLOBALS['aa_log_handle'] = null;
$GLOBALS['aa_run_id']     = date('YmdHis') . '-' . substr(md5(uniqid('', true)), 0, 6);

function aa_log_open()
{
    if (!is_dir(AUTO_ALLOC_LOG_DIR)) {
        @mkdir(AUTO_ALLOC_LOG_DIR, 0775, true);
    }
    // The log directory sits under the web root and the logs carry customer
    // codes, so keep it off the web.
    $htaccess = AUTO_ALLOC_LOG_DIR . '/.htaccess';
    if (!file_exists($htaccess)) {
        @file_put_contents($htaccess, "Require all denied\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n");
    }
    $file = AUTO_ALLOC_LOG_DIR . '/auto_allocation_' . date('Y-m-d') . '.log';
    $GLOBALS['aa_log_handle'] = @fopen($file, 'a');
    return $file;
}

function aa_log($level, $message)
{
    $line = '[' . date('Y-m-d H:i:s') . '] [' . $GLOBALS['aa_run_id'] . '] ['
          . str_pad($level, 7) . '] ' . $message;

    if ($GLOBALS['aa_log_handle']) {
        fwrite($GLOBALS['aa_log_handle'], $line . PHP_EOL);
    }
    if ($GLOBALS['AUTO_ALLOC_IS_CLI']) {
        echo $line . PHP_EOL;
    }
}

function aa_log_close()
{
    if ($GLOBALS['aa_log_handle']) {
        fclose($GLOBALS['aa_log_handle']);
        $GLOBALS['aa_log_handle'] = null;
    }
}

/* ----------------------------------------------------------------- helpers */

function aa_esc($value)
{
    return mysql_real_escape_string(trim((string)$value));
}

/**
 * Accepts YYYYMMDD or YYYY-MM-DD (or anything strtotime understands) and
 * returns YYYYMMDD, or "" when the value is not a usable date.
 */
function aa_normalise_date($value)
{
    $value = trim((string)$value);
    if ($value === '') {
        return "";
    }
    if (preg_match('/^\d{8}$/', $value)) {
        $y = (int)substr($value, 0, 4);
        $m = (int)substr($value, 4, 2);
        $d = (int)substr($value, 6, 2);
        return checkdate($m, $d, $y) ? $value : "";
    }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
        $ts = strtotime($value);
        return $ts ? date('Ymd', $ts) : "";
    }
    $ts = strtotime($value);
    return $ts ? date('Ymd', $ts) : "";
}

/**
 * Make sure allocation_details_invoicewise carries the auto allocation flag column.
 * Adds it on first run so no manual DDL step is needed.
 */
function aa_ensure_flag_column($table, $column)
{
    $res = mysql_query("SHOW COLUMNS FROM `$table` LIKE '" . aa_esc($column) . "'");
    if ($res && mysql_num_rows($res) > 0) {
        return true;
    }

    $sql = "ALTER TABLE `$table`
            ADD COLUMN `$column` VARCHAR(10) NOT NULL DEFAULT '" . AUTO_ALLOC_FLAG_NO . "'";
    if (mysql_query($sql)) {
        aa_log('INFO', "Added missing column `$table`.`$column` (default '" . AUTO_ALLOC_FLAG_NO . "').");
        return true;
    }

    aa_log('ERROR', "Could not add column `$table`.`$column`: " . mysql_error());
    return false;
}

/**
 * Check that the per-record audit table is there. The cron no longer creates
 * it - run auto_allocation_cron.sql once at deployment. When it is missing the
 * run still goes ahead, file logged only.
 */
function aa_ensure_log_table()
{
    $table = AUTO_ALLOC_LOG_TABLE;

    $res = mysql_query("SHOW TABLES LIKE '" . aa_esc($table) . "'");
    if ($res && mysql_num_rows($res) > 0) {
        return true;
    }

    aa_log('ERROR', "Audit table `$table` does not exist - create it with "
                  . "auto_allocation_cron.sql. Continuing with file logging only.");
    return false;
}

/**
 * Write one outcome row to the audit table.
 *
 * $status : INSERTED | SKIPPED | FAILED
 * $ctx    : the already-escaped source row fields
 */
function aa_db_log($status, $reason, $ctx, $allocation_id = null, $order_id = '')
{
    if (empty($GLOBALS['aa_db_log_ready'])) {
        return;
    }

    $table  = AUTO_ALLOC_LOG_TABLE;
    $alloc  = ($allocation_id === null || $allocation_id === '')
            ? "NULL" : "'" . aa_esc($allocation_id) . "'";

    // `reason` is VARCHAR(255) and mysql_error() can be longer than that;
    // under strict mode an over-long value would fail the audit insert.
    $reason = substr((string)$reason, 0, 255);

    $sql = "INSERT INTO `$table`
        (`run_id`,`log_datetime`,`from_date`,`to_date`,`status`,`reason`,
         `allocation_id`,`order_id`,`APPORDERNO`,`inv_no`,`customer_id`,
         `sub_dealer_id`,`dns_prod_code`,`prod_desc`,`inv_dt`,`inv_qty`)
        VALUES
        ('" . aa_esc($GLOBALS['aa_run_id']) . "',
         '" . date('Y-m-d H:i:s') . "',
         '" . aa_esc($GLOBALS['from_date']) . "',
         '" . aa_esc($GLOBALS['to_date']) . "',
         '" . aa_esc($status) . "',
         '" . aa_esc($reason) . "',
         $alloc,
         '" . aa_esc($order_id) . "',
         '" . aa_esc($ctx['APPORDERNO']) . "',
         '" . aa_esc($ctx['inv_no']) . "',
         '" . aa_esc($ctx['customer_id']) . "',
         '" . aa_esc($ctx['sub_dealer_id']) . "',
         '" . aa_esc($ctx['dns_prod_code']) . "',
         '" . aa_esc($ctx['prod_desc']) . "',
         '" . aa_esc($ctx['inv_dt']) . "',
         '" . aa_esc($ctx['inv_qty']) . "')";

    if (!mysql_query($sql)) {
        // Never let an audit failure stop the allocation run.
        $GLOBALS['aa_db_log_errors']++;
        if ($GLOBALS['aa_db_log_errors'] <= 5) {
            aa_log('ERROR', "Audit log insert failed: " . mysql_error());
        }
    }
}

function aa_build_order_id($sub_dealer_id, $apporderno)
{
    $micro_time = microtime(true);
    $micro      = sprintf("%06d", ($micro_time - floor($micro_time)) * 1000000);
    return "AUTO_" . $sub_dealer_id . "_" . date("YmdHis") . $micro . "_" . $apporderno;
}

/* -------------------------------------------------------------------- boot */

$log_file = aa_log_open();

// Single instance guard - three runs a day should never overlap, but a long
// run must not be trampled by the next trigger.
$lock_handle = @fopen(AUTO_ALLOC_LOCK_FILE, 'c');
if ($lock_handle && !flock($lock_handle, LOCK_EX | LOCK_NB)) {
    aa_log('WARN', 'Another auto allocation run is still in progress. Exiting.');
    aa_log_close();
    if (!$AUTO_ALLOC_IS_CLI) {
        echo json_encode(array(
            "process_status"  => "NO",
            "process_message" => "Another auto allocation run is already in progress."
        ));
    }
    exit;
}

/* -------------------------------------------------------------- date range */

$arg_from = "";
$arg_to   = "";

if ($AUTO_ALLOC_IS_CLI) {
    $arg_from = isset($argv[1]) ? $argv[1] : "";
    $arg_to   = isset($argv[2]) ? $argv[2] : "";
} else {
    if (isset($_REQUEST['inv_date']) && trim($_REQUEST['inv_date']) !== '') {
        $arg_from = $_REQUEST['inv_date'];
        $arg_to   = $_REQUEST['inv_date'];
    } else {
        $arg_from = isset($_REQUEST['from_date']) ? $_REQUEST['from_date'] : "";
        $arg_to   = isset($_REQUEST['to_date']) ? $_REQUEST['to_date'] : "";
    }
}

$from_date = aa_normalise_date($arg_from);
$to_date   = aa_normalise_date($arg_to);
$date_mode = "manual";

if ($from_date !== "" && $to_date === "") {
    $to_date = $from_date;              // single day supplied
}
if ($from_date === "" && $to_date !== "") {
    $from_date = $to_date;
}
if ($from_date === "" && $to_date === "") {
    $date_mode = "auto (last " . AUTO_ALLOC_LOOKBACK_DAYS . " days)";
    $from_date = date('Ymd', strtotime('-' . AUTO_ALLOC_LOOKBACK_DAYS . ' days'));
    //$to_date   = date('Ymd');
    $to_date   = '20260915';
}
if ($from_date > $to_date) {
    $swap      = $from_date;
    $from_date = $to_date;
    $to_date   = $swap;
}

aa_log('INFO', str_repeat('=', 78));
aa_log('INFO', 'Auto allocation cron started. Mode: ' . ($AUTO_ALLOC_IS_CLI ? 'CLI' : 'HTTP')
              . ' | Date mode: ' . $date_mode
              . ' | INVDT range: ' . $from_date . ' .. ' . $to_date
              . ' | INVDT floor: ' . AUTO_ALLOC_MIN_INVDT);
aa_log('INFO', 'Log file: ' . $log_file);

log_cron_start(AUTO_ALLOC_PROCESS_NAME, $conn);

/* ------------------------------------------------- flag column + audit table */

$flag_column_ready = aa_ensure_flag_column($allocation_details, AUTO_ALLOC_FLAG_COLUMN);

$GLOBALS['aa_db_log_errors'] = 0;
$GLOBALS['aa_db_log_ready']  = aa_ensure_log_table();

/* ------------------------------------------------------------ source data */

$from_date_esc = aa_esc($from_date);
$to_date_esc   = aa_esc($to_date);
$min_invdt_esc = aa_esc(AUTO_ALLOC_MIN_INVDT);
$cust_type_list = array();
foreach ($AUTO_ALLOC_CUST_TYPES as $one_cust_type) {
    $one_cust_type = trim((string)$one_cust_type);
    if ($one_cust_type !== '') {
        $cust_type_list[] = "'" . aa_esc($one_cust_type) . "'";
    }
}
if (empty($cust_type_list)) {
    aa_log('ERROR', 'No customer types configured in $AUTO_ALLOC_CUST_TYPES - nothing to allocate.');
    log_cron_end(AUTO_ALLOC_PROCESS_NAME, $conn);
    aa_log_close();
    if (!$AUTO_ALLOC_IS_CLI) {
        echo json_encode(array(
            "process_status"  => "NO",
            "process_message" => "No customer types configured for auto allocation."
        ));
    }
    mysql_close();
    exit;
}
$cust_type_in = implode(",", $cust_type_list);

// Restrict to specific APPORDERNOs when the override list is filled in.
$apporderno_filter = "";
if (!empty($array_APPORDERNO) && is_array($array_APPORDERNO)) {
    $apporderno_list = array();
    foreach ($array_APPORDERNO as $one_apporderno) {
        $one_apporderno = trim((string)$one_apporderno);
        if ($one_apporderno !== '') {
            $apporderno_list[] = "'" . aa_esc($one_apporderno) . "'";
        }
    }
    if (!empty($apporderno_list)) {
        $apporderno_filter = "AND tdoi.APPORDERNO IN (" . implode(",", $apporderno_list) . ")";
        aa_log('INFO', 'APPORDERNO filter active (' . count($apporderno_list) . '): '
                     . implode(", ", $array_APPORDERNO));
    }
}

 $sql_src = "
    SELECT
        cm2.customer_id          AS customer_id,
        cm.customer_id           AS sub_dealer_id,
        cm.acedns                AS sub_dealer_acedns,
        cm.appointment_date      AS sub_dealer_appointment_date,
        -- Product comes from the invoice line itself, with the order line as a
        -- fallback. prod_desc is part of the duplicate key, so it has to be the
        -- same column the manual invoice-wise screen posts (T_DOINVOICE).
        IF(tdoi.dns_prod_code IS NULL OR tdoi.dns_prod_code = '',
           tdoa.dns_prod_code, tdoi.dns_prod_code) AS dns_prod_code,
        IF(tdoi.prod_display_name IS NULL OR tdoi.prod_display_name = '',
           tdoa.prod_display_name, tdoi.prod_display_name) AS prod_desc,
        tdoi.APPORDERNO          AS APPORDERNO,
        tdoi.INVNO               AS inv_no,
        tdoi.INVDT               AS inv_dt,
        tdoi.INVQTY              AS allocation_qty,
        tdoi.INVQTY              AS inv_qty,
        (SELECT COUNT(*) FROM $T_APPERPDO_OFFLINE off
          WHERE off.ERPORDERNO = tdoi.APPORDERNO) AS offline_cnt
    FROM $T_DOINVOICE tdoi
    JOIN $T_APPERPDO tdoa
         ON tdoa.APPORDERNO = tdoi.APPORDERNO
    JOIN $customer_master cm
         ON cm.dns_customer_code = tdoa.dns_sub_dealer_code
    LEFT JOIN $customer_master cm2
         ON cm2.dns_customer_code = tdoa.dns_customer_code
    WHERE cm.cust_type IN ($cust_type_in)
      AND tdoi.INVDT >= '$min_invdt_esc'
      AND tdoi.INVDT BETWEEN '$from_date_esc' AND '$to_date_esc'
      $apporderno_filter
    ORDER BY tdoi.INVDT ASC, tdoi.APPORDERNO ASC, tdoi.INVNO ASC
";
//echo"<pre>";print_r($sql_src);die;

$res_src = mysql_query($sql_src);

if (!$res_src) {
    $err = mysql_error();
    aa_log('ERROR', 'Source query failed: ' . $err);
    aa_log('ERROR', 'SQL: ' . preg_replace('/\s+/', ' ', $sql_src));
    log_cron_end(AUTO_ALLOC_PROCESS_NAME, $conn);
    aa_log_close();
    if (!$AUTO_ALLOC_IS_CLI) {
        echo json_encode(array(
            "process_status"  => "NO",
            "process_message" => "Auto allocation source query failed.",
            "error"           => $err
        ));
    }
    mysql_close();
    exit;
}

$total_rows = mysql_num_rows($res_src);
aa_log('INFO', "Source rows fetched: $total_rows");

/* ------------------------------------------------------------- processing */

$stat = array(
    'fetched'            => $total_rows,
    'inserted'           => 0,
    'skipped_duplicate'  => 0,
    'skipped_batch_dupe' => 0,
    'skipped_no_dealer'  => 0,
    'skipped_no_invno'   => 0,
    'skipped_inactive'   => 0,
    'skipped_appt_date'  => 0,
    'skipped_bad_qty'    => 0,
    'failed'             => 0,
);

$seen_in_batch       = array();
$allocation_date_time = date("Y-m-d H:i:s");

while ($row = mysql_fetch_assoc($res_src)) {

    $customer_id     = aa_esc($row['customer_id']);
    $sub_dealer_id   = aa_esc($row['sub_dealer_id']);
    $dns_prod_code   = aa_esc($row['dns_prod_code']);
    $prod_desc       = aa_esc($row['prod_desc']);
    $APPORDERNO      = aa_esc($row['APPORDERNO']);
    $inv_no          = aa_esc($row['inv_no']);
    $inv_dt_raw      = trim((string)$row['inv_dt']);
    $allocation_qty  = trim((string)$row['allocation_qty']);
    $inv_qty         = trim((string)$row['inv_qty']);
    $acedns          = trim((string)$row['sub_dealer_acedns']);
    $appointment_date = trim((string)$row['sub_dealer_appointment_date']);
    $is_offline      = ((int)$row['offline_cnt'] > 0)
                     ? AUTO_ALLOC_OFFLINE_YES : AUTO_ALLOC_OFFLINE_NO;

    $tag = "APPORDERNO=$APPORDERNO INVNO=$inv_no PROD=$dns_prod_code "
         . "DEALER=$customer_id SUBDEALER=$sub_dealer_id INVDT=$inv_dt_raw";

    // Context carried into every audit-table row for this source record.
    $ctx = array(
        'APPORDERNO'    => $APPORDERNO,
        'inv_no'        => $inv_no,
        'customer_id'   => $customer_id,
        'sub_dealer_id' => $sub_dealer_id,
        'dns_prod_code' => $dns_prod_code,
        'prod_desc'     => $prod_desc,
        'inv_dt'        => $inv_dt_raw,
        'inv_qty'       => $allocation_qty,
    );

    // --- the dealer side is a LEFT JOIN, so it can come back empty ---------
    if ($customer_id === '' || $sub_dealer_id === '') {
        $stat['skipped_no_dealer']++;
        $reason = 'No dealer/sub-dealer mapping in customer_master';
        aa_log('SKIP', "$reason. $tag");
        aa_db_log('SKIPPED', $reason, $ctx);
        continue;
    }

    // --- quantity must be meaningful --------------------------------------
    if (!is_numeric($allocation_qty) || (float)$allocation_qty <= 0) {
        $stat['skipped_bad_qty']++;
        $reason = "Invoice qty is zero/non-numeric ('$allocation_qty')";
        aa_log('SKIP', "$reason. $tag");
        aa_db_log('SKIPPED', $reason, $ctx);
        continue;
    }

    // --- same rule the manual screen applies: inactive customer -----------
    if (AUTO_ALLOC_CHECK_ACEDNS && strtoupper($acedns) === 'N') {
        $stat['skipped_inactive']++;
        $reason = 'Sub-dealer is inactive (acedns=N)';
        aa_log('SKIP', "$reason. $tag");
        aa_db_log('SKIPPED', $reason, $ctx);
        continue;
    }

    // --- same rule the manual screen applies: appointment vs invoice date --
    if (AUTO_ALLOC_CHECK_APPOINTMENT_DATE && $appointment_date !== '' && $inv_dt_raw !== '') {
        $appt_obj = DateTime::createFromFormat('d.m.Y', $appointment_date);
        if ($appt_obj !== false) {
            if ($appt_obj->format('Ymd') > $inv_dt_raw) {
                $stat['skipped_appt_date']++;
                $reason = "RSAR appointment date ($appointment_date) is after invoice date";
                aa_log('SKIP', "$reason. $tag");
                aa_db_log('SKIPPED', $reason, $ctx);
                continue;
            }
        }
    }

    // --- the invoice number is the key this table is built on --------------
    if ($inv_no === '') {
        $stat['skipped_no_invno']++;
        $reason = 'Invoice has no INVNO in T_DOINVOICE';
        aa_log('SKIP', "$reason. $tag");
        aa_db_log('SKIPPED', $reason, $ctx);
        continue;
    }

    // --- duplicate key ----------------------------------------------------
    $dupe_key = $APPORDERNO . '|' . $inv_no . '|' . $prod_desc . '|'
              . $customer_id . '|' . $sub_dealer_id;

    if (isset($seen_in_batch[$dupe_key])) {
        $stat['skipped_batch_dupe']++;
        $reason = "Duplicate allocation key within this run (qty $allocation_qty not allocated)";
        aa_log('WARN', "$reason. $tag");
        aa_db_log('SKIPPED', $reason, $ctx);
        continue;
    }

    // dns_prod_code is NOT part of this key: the manual screen
    // (save_allocation_details_invoicewise.php) does not populate that column,
    // so keying on it would miss manual rows and allocate the invoice twice.
    // Soft-deleted rows (delete_at=1) still count - if an admin removed an
    // allocation the cron must not silently put it back.
    $sql_chk = "
        SELECT 1
        FROM $allocation_details
        WHERE APPORDERNO    = '$APPORDERNO'
          AND inv_no        = '$inv_no'
          AND prod_desc     = '$prod_desc'
          AND customer_id   = '$customer_id'
          AND sub_dealer_id = '$sub_dealer_id'
        LIMIT 1
    ";
    //echo"<pre>";print_r(mysql_num_rows($res_chk));die;

    $res_chk = mysql_query($sql_chk);
    //echo"<pre>";print_r(mysql_num_rows($res_chk));die;

    if (!$res_chk) {
        $stat['failed']++;
        $reason = 'Duplicate check query failed: ' . mysql_error();
        aa_log('ERROR', "$reason $tag");
        aa_db_log('FAILED', $reason, $ctx);
        continue;
    }
    if (mysql_num_rows($res_chk) > 0) {
        $stat['skipped_duplicate']++;
        $reason = 'Already allocated (manual or auto)';
        aa_log('SKIP', "$reason. $tag");
        if (AUTO_ALLOC_LOG_DUPLICATE_SKIPS) {
            aa_db_log('SKIPPED', $reason, $ctx);
        }
        $seen_in_batch[$dupe_key] = true;
        continue;
    }

    // --- insert -----------------------------------------------------------
    $inv_date = "";
    if (preg_match('/^\d{8}$/', $inv_dt_raw)) {
        $inv_date = date(
            AUTO_ALLOC_INV_DATE_FORMAT,
            mktime(0, 0, 0, (int)substr($inv_dt_raw, 4, 2),
                            (int)substr($inv_dt_raw, 6, 2),
                            (int)substr($inv_dt_raw, 0, 4))
        );
    }
    $inv_date      = aa_esc($inv_date);
    $order_id      = aa_esc(aa_build_order_id($sub_dealer_id, $APPORDERNO));
    $qty_esc       = aa_esc($allocation_qty);
    $inv_qty_esc   = aa_esc($inv_qty);
    $offline_esc   = aa_esc($is_offline);

    $flag_col_sql = $flag_column_ready ? ",`" . AUTO_ALLOC_FLAG_COLUMN . "`" : "";
    $flag_val_sql = $flag_column_ready ? ",'" . AUTO_ALLOC_FLAG_YES . "'" : "";

    // Same column set the manual screen writes, plus dns_prod_code (the
    // reports sort on it) and the auto flag. inv_cancl keeps its 'no' default;
    // update_cancel_invoices_in_allocation.php owns that column.
   echo $sql_in = "INSERT INTO $allocation_details
        (`date_and_time`,`order_id`,`APPORDERNO`,`inv_no`,`customer_id`,`sub_dealer_id`,
         `dns_prod_code`,`prod_desc`,`inv_date`,`inv_qty`,`allocation_qty`,
         `is_offline`,`delete_at`$flag_col_sql)
        VALUES
        ('$allocation_date_time','$order_id','$APPORDERNO','$inv_no','$customer_id','$sub_dealer_id',
         '$dns_prod_code','$prod_desc','$inv_date','$inv_qty_esc','$qty_esc',
         '$offline_esc','0'$flag_val_sql)";

    if (mysql_query($sql_in)) {
        $stat['inserted']++;
        $seen_in_batch[$dupe_key] = true;
        $new_allocation_id = mysql_insert_id();
        aa_log('INSERT', "allocation_id=$new_allocation_id order_id=$order_id "
                       . "qty=$allocation_qty $tag");
        aa_db_log('INSERTED', 'Auto allocated', $ctx, $new_allocation_id, $order_id);
    } else {
        $stat['failed']++;
        $reason = 'Insert failed: ' . mysql_error();
        aa_log('ERROR', "$reason $tag");
        aa_log('ERROR', 'SQL: ' . preg_replace('/\s+/', ' ', $sql_in));
        aa_db_log('FAILED', $reason, $ctx, null, $order_id);
    }
}

/* ----------------------------------------------------------------- summary */

aa_log('INFO', 'Run summary -> '
    . 'fetched=' . $stat['fetched'] . ', '
    . 'inserted=' . $stat['inserted'] . ', '
    . 'skipped_already_allocated=' . $stat['skipped_duplicate'] . ', '
    . 'skipped_same_key_in_run=' . $stat['skipped_batch_dupe'] . ', '
    . 'skipped_no_dealer_mapping=' . $stat['skipped_no_dealer'] . ', '
    . 'skipped_no_invoice_no=' . $stat['skipped_no_invno'] . ', '
    . 'skipped_inactive_customer=' . $stat['skipped_inactive'] . ', '
    . 'skipped_appointment_date=' . $stat['skipped_appt_date'] . ', '
    . 'skipped_invalid_qty=' . $stat['skipped_bad_qty'] . ', '
    . 'failed=' . $stat['failed']);

if (!$GLOBALS['aa_db_log_ready']) {
    aa_log('WARN', 'Audit table ' . AUTO_ALLOC_LOG_TABLE . ' unavailable - this run was file-logged only.');
} elseif ($GLOBALS['aa_db_log_errors'] > 0) {
    aa_log('WARN', 'Audit log insert failures this run: ' . $GLOBALS['aa_db_log_errors']);
}

aa_log('INFO', 'Auto allocation cron finished.');
aa_log('INFO', str_repeat('=', 78));

log_cron_end(AUTO_ALLOC_PROCESS_NAME, $conn);
aa_log_close();

if ($lock_handle) {
    flock($lock_handle, LOCK_UN);
    fclose($lock_handle);
}

if (!$AUTO_ALLOC_IS_CLI) {
    header('Content-Type: application/json');
    echo json_encode(array(
        "process_status"  => "YES",
        "process_message" => "Auto allocation completed.",
        "run_id"          => $GLOBALS['aa_run_id'],
        "from_date"       => $from_date,
        "to_date"         => $to_date,
        "log_file"        => $log_file,
        "log_table"       => AUTO_ALLOC_LOG_TABLE,
        "summary"         => $stat
    ));
}

mysql_close();
?>
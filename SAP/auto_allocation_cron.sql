-- ---------------------------------------------------------------------------
-- Auto Allocation Cron - schema prerequisites
-- ---------------------------------------------------------------------------
-- auto_allocation_cron.php will add the flag column by itself on first run,
-- but on a large `allocation_details_invoicewise` table an online ALTER is
-- better run deliberately during a maintenance window. Run this first and the
-- cron will simply detect the column and skip its own ALTER.
-- ---------------------------------------------------------------------------

-- 1. Flag that marks rows created by the cron (manual rows stay 'No').
ALTER TABLE `allocation_details_invoicewise`
    ADD COLUMN `auto_allocation` VARCHAR(10) NOT NULL DEFAULT 'No';

-- 2. Index backing the duplicate check the cron runs once per source row:
--    APPORDERNO + inv_no + prod_desc + customer_id + sub_dealer_id.
--    Without this the cron does a full table scan per invoice line.
--    (dns_prod_code is deliberately not in the key - the manual screen does
--     not populate it, so an auto row must not be told apart by it.)
ALTER TABLE `allocation_details_invoicewise`
    ADD INDEX `idx_alloc_dupe_key`
    (`APPORDERNO`, `inv_no`, `prod_desc`, `customer_id`, `sub_dealer_id`);

-- 3. Helpful for reporting on auto vs manual allocations.
ALTER TABLE `allocation_details_invoicewise`
    ADD INDEX `idx_alloc_auto_flag` (`auto_allocation`);


-- 4. Per-record audit table. One row per source record the cron acted on,
--    with the outcome and the reason. The cron does NOT create this table -
--    run this statement once at deployment or the run is file logged only.
--
--    Note: rows skipped as "already allocated" are NOT logged here by default
--    (see AUTO_ALLOC_LOG_DUPLICATE_SKIPS in auto_allocation_cron.php). The
--    3-day window is rescanned 3x a day, so those repeat ~9 times per invoice
--    line and would swamp the table. The file log still records them.
CREATE TABLE IF NOT EXISTS `auto_allocation_log` (
    `log_id`        BIGINT(20)    NOT NULL AUTO_INCREMENT,
    `run_id`        VARCHAR(32)   NOT NULL DEFAULT '',   -- groups one cron run
    `log_datetime`  DATETIME      NULL DEFAULT NULL,
    `from_date`     VARCHAR(8)    NOT NULL DEFAULT '',   -- INVDT window scanned
    `to_date`       VARCHAR(8)    NOT NULL DEFAULT '',
    `status`        VARCHAR(10)   NOT NULL DEFAULT '',   -- INSERTED|SKIPPED|FAILED
    `reason`        VARCHAR(255)  NOT NULL DEFAULT '',
    `allocation_id` BIGINT(20)    NULL DEFAULT NULL,     -- allocation_details_invoicewise.allocation_id
    `order_id`      VARCHAR(100)  NOT NULL DEFAULT '',
    `APPORDERNO`    VARCHAR(50)   NOT NULL DEFAULT '',
    `inv_no`        VARCHAR(255)  NOT NULL DEFAULT '',   -- T_DOINVOICE.INVNO
    `customer_id`   VARCHAR(50)   NOT NULL DEFAULT '',   -- dealer
    `sub_dealer_id` VARCHAR(50)   NOT NULL DEFAULT '',   -- RSSD sub-dealer
    `dns_prod_code` VARCHAR(50)   NOT NULL DEFAULT '',
    `prod_desc`     VARCHAR(255)  NOT NULL DEFAULT '',
    `inv_dt`        VARCHAR(8)    NOT NULL DEFAULT '',
    `inv_qty`       VARCHAR(30)   NOT NULL DEFAULT '',
    PRIMARY KEY (`log_id`),
    KEY `idx_aal_run` (`run_id`),
    KEY `idx_aal_status` (`status`),
    KEY `idx_aal_datetime` (`log_datetime`),
    KEY `idx_aal_apporder` (`APPORDERNO`),
    KEY `idx_aal_allocation` (`allocation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ---------------------------------------------------------------------------
-- Useful queries once the cron is live
-- ---------------------------------------------------------------------------

-- Outcome breakdown per run, most recent first.
-- SELECT `run_id`, `from_date`, `to_date`, `status`, COUNT(*) AS records
--   FROM `auto_allocation_log`
--  GROUP BY `run_id`, `from_date`, `to_date`, `status`
--  ORDER BY MAX(`log_datetime`) DESC;

-- Everything the cron refused to allocate today, and why.
-- SELECT `log_datetime`, `status`, `reason`, `APPORDERNO`, `inv_no`,
--        `customer_id`, `sub_dealer_id`, `dns_prod_code`, `inv_qty`
--   FROM `auto_allocation_log`
--  WHERE `status` <> 'INSERTED'
--    AND DATE(`log_datetime`) = CURDATE()
--  ORDER BY `log_datetime` DESC;

-- Trace an allocation row back to the run that created it.
-- SELECT ad.*, l.`run_id`, l.`reason`, l.`log_datetime`
--   FROM `allocation_details_invoicewise` ad
--   JOIN `auto_allocation_log` l ON l.`allocation_id` = ad.`allocation_id`
--  WHERE ad.`APPORDERNO` = 'YOUR_ORDER_NO';

-- Anything that hard-failed and needs attention.
-- SELECT * FROM `auto_allocation_log`
--  WHERE `status` = 'FAILED' ORDER BY `log_datetime` DESC LIMIT 100;

-- Housekeeping: keep 6 months of audit rows.
-- DELETE FROM `auto_allocation_log`
--  WHERE `log_datetime` < DATE_SUB(NOW(), INTERVAL 6 MONTH);

-- What did the cron create today?
-- SELECT * FROM `allocation_details_invoicewise`
--  WHERE `auto_allocation` = 'Yes'
--    AND DATE(`date_and_time`) = CURDATE()
--  ORDER BY `date_and_time` DESC;

-- Auto vs manual split for the current month.
-- SELECT `auto_allocation`, COUNT(*) AS rows_created, SUM(`allocation_qty`) AS qty
--   FROM `allocation_details_invoicewise`
--  WHERE DATE_FORMAT(`date_and_time`, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
--  GROUP BY `auto_allocation`;

-- Cron heartbeat (written by cron_log_helper.php).
-- SELECT * FROM `cron_update_table` WHERE `process_name` = 'auto_allocation_cron';

-- Roll back one bad run (find the run window in the log file first).
-- DELETE FROM `allocation_details_invoicewise`
--  WHERE `auto_allocation` = 'Yes'
--    AND `date_and_time` BETWEEN '2026-08-27 09:00:00' AND '2026-08-27 09:30:00';
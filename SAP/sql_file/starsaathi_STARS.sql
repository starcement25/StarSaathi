-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 10, 2025 at 04:31 PM
-- Server version: 5.7.44-log
-- PHP Version: 8.1.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `starsaathi_STARS`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_master`
--

CREATE TABLE `admin_master` (
  `admin_id` int(5) NOT NULL,
  `admin_login` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `admin_pwd` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `admin_email` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `admin_phone_no` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ageing`
--

CREATE TABLE `ageing` (
  `id` bigint(20) NOT NULL,
  `zone` varchar(80) DEFAULT NULL,
  `classdescr` text,
  `alias` varchar(100) DEFAULT NULL,
  `party` varchar(80) DEFAULT NULL,
  `securitydeposit` varchar(40) DEFAULT NULL,
  `crlim` varchar(80) DEFAULT NULL,
  `colldt` varchar(40) DEFAULT NULL,
  `chllndt` varchar(40) DEFAULT NULL,
  `mobal` varchar(80) DEFAULT NULL,
  `mtdsales` varchar(50) DEFAULT NULL,
  `mtdcoll` varchar(80) DEFAULT NULL,
  `ostotal` varchar(50) DEFAULT NULL,
  `less_equ_10days` varchar(50) DEFAULT NULL,
  `11_17days` varchar(50) DEFAULT NULL,
  `18_25days` varchar(50) DEFAULT NULL,
  `26_30days` varchar(50) DEFAULT NULL,
  `31_45days` varchar(50) DEFAULT NULL,
  `46_60days` varchar(50) DEFAULT NULL,
  `61_90days` varchar(50) DEFAULT NULL,
  `91_120days` varchar(50) DEFAULT NULL,
  `121_180days` varchar(50) DEFAULT NULL,
  `greater_180days` varchar(50) DEFAULT NULL,
  `onacc` varchar(80) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `allocation_details`
--

CREATE TABLE `allocation_details` (
  `allocation_id` bigint(20) NOT NULL,
  `date_and_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `dispatch_date` date DEFAULT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `APPORDERNO` varchar(50) DEFAULT NULL,
  `challan_no` varchar(255) DEFAULT NULL,
  `customer_id` varchar(255) DEFAULT NULL,
  `sub_dealer_id` varchar(50) DEFAULT NULL,
  `dns_prod_code` varchar(20) DEFAULT NULL,
  `prod_desc` varchar(255) DEFAULT NULL,
  `dispatch_qty` varchar(10) DEFAULT NULL,
  `allocation_qty` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `allocation_details_invoicewise`
--

CREATE TABLE `allocation_details_invoicewise` (
  `allocation_id` bigint(20) NOT NULL,
  `date_and_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `inv_date` date DEFAULT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `APPORDERNO` varchar(50) DEFAULT NULL,
  `inv_no` varchar(255) DEFAULT NULL,
  `customer_id` varchar(255) DEFAULT NULL,
  `sub_dealer_id` varchar(50) DEFAULT NULL,
  `dns_prod_code` varchar(20) DEFAULT NULL,
  `prod_desc` varchar(255) DEFAULT NULL,
  `inv_qty` varchar(10) DEFAULT NULL,
  `allocation_qty` varchar(255) DEFAULT NULL,
  `inv_cancl` enum('yes','no') NOT NULL DEFAULT 'no'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `allocation_invoice_del`
--

CREATE TABLE `allocation_invoice_del` (
  `sl_no` int(4) NOT NULL,
  `Allocation Date Time` varchar(255) DEFAULT NULL,
  `APPORDERNO` varchar(255) DEFAULT NULL,
  `Inv Date` varchar(255) DEFAULT NULL,
  `Linked Dealer Code` varchar(255) DEFAULT NULL,
  `Linked Dealer SAP Code` varchar(255) DEFAULT NULL,
  `Linked Dealer Name` varchar(255) DEFAULT NULL,
  `Sub Dealer/RSSD Code` varchar(255) DEFAULT NULL,
  `Sub Dealer/RSSD SAP Code` varchar(255) DEFAULT NULL,
  `Sub Dealer/RSSD Name` varchar(255) DEFAULT NULL,
  `Branch` varchar(255) DEFAULT NULL,
  `Month` varchar(255) DEFAULT NULL,
  `Product Name` varchar(255) DEFAULT NULL,
  `Total Inv qty` varchar(255) DEFAULT NULL,
  `Allocated qty` varchar(255) DEFAULT NULL,
  `Remaining Allocation qty` varchar(255) DEFAULT NULL,
  `Inv no` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `apicalllog`
--

CREATE TABLE `apicalllog` (
  `id` int(11) NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `emp_code` varchar(30) NOT NULL,
  `url` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `api_verification`
--

CREATE TABLE `api_verification` (
  `api_id` int(11) NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `deviceid` varchar(100) NOT NULL,
  `verificationcode` text NOT NULL,
  `apicreatedate` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app_service_track_log`
--

CREATE TABLE `app_service_track_log` (
  `id` bigint(20) NOT NULL,
  `appservice_name` varchar(80) DEFAULT NULL,
  `customer_code` varchar(40) DEFAULT NULL,
  `details` text,
  `datetime` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app_setting_master`
--

CREATE TABLE `app_setting_master` (
  `asm_id` bigint(20) NOT NULL,
  `the_key_name` varchar(80) DEFAULT NULL,
  `the_value` text,
  `last_updated_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app_updation`
--

CREATE TABLE `app_updation` (
  `device_id` varchar(255) NOT NULL,
  `version_code` varchar(255) NOT NULL,
  `is_update` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `arc_consumer_reg`
--

CREATE TABLE `arc_consumer_reg` (
  `ac_id` bigint(20) NOT NULL,
  `name` varchar(70) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `no_of_bags` varchar(20) DEFAULT NULL,
  `redeemed_bags` varchar(20) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'APPROVED',
  `customer_code` varchar(50) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `reference_gift_redeem_id` varchar(20) DEFAULT NULL,
  `entry_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `S` varchar(10) NOT NULL,
  `T` varchar(10) NOT NULL,
  `A` varchar(10) NOT NULL,
  `R` varchar(10) NOT NULL,
  `lunch_box` varchar(10) NOT NULL,
  `water_bottle` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `arc_consumer_reg_old`
--

CREATE TABLE `arc_consumer_reg_old` (
  `ac_id` bigint(20) NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `no_of_bags` varchar(20) DEFAULT NULL,
  `redeemed_bags` varchar(20) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'PENDING',
  `customer_code` varchar(50) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `reference_gift_redeem_id` varchar(20) DEFAULT NULL,
  `entry_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `arc_dealer_cust_point_table`
--

CREATE TABLE `arc_dealer_cust_point_table` (
  `adcpt_id` bigint(20) NOT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `persone_name` varchar(70) DEFAULT NULL,
  `persone_mobile` varchar(30) DEFAULT NULL,
  `points` varchar(20) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `arc_gift_catalogue`
--

CREATE TABLE `arc_gift_catalogue` (
  `id` int(11) NOT NULL,
  `bag_limit_from` varchar(20) DEFAULT NULL,
  `bag_limit_to` varchar(20) DEFAULT NULL,
  `gift_name` varchar(40) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `arc_gift_redeem_table`
--

CREATE TABLE `arc_gift_redeem_table` (
  `ac_id` bigint(20) NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `redeemed_bags` varchar(20) DEFAULT NULL,
  `gift_id` varchar(20) DEFAULT NULL,
  `gift_name` varchar(50) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'PENDING',
  `customer_code` varchar(50) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `entry_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `auto_notification_log`
--

CREATE TABLE `auto_notification_log` (
  `anl_id` bigint(20) NOT NULL,
  `cust_dns_code` varchar(50) DEFAULT NULL,
  `cust_name` varchar(100) DEFAULT NULL,
  `message_type` varchar(30) DEFAULT NULL,
  `message_sent_status` varchar(30) DEFAULT NULL,
  `error_message` varchar(250) DEFAULT NULL,
  `device_type` varchar(30) DEFAULT NULL,
  `message_text` text,
  `sent_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bank_master`
--

CREATE TABLE `bank_master` (
  `bank_id` int(4) NOT NULL,
  `bank_name` text NOT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `branch_consumer_scheme_status`
--

CREATE TABLE `branch_consumer_scheme_status` (
  `sl_no` int(10) NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `consumer_scheme_status` enum('INACTIVE','ACTIVE') NOT NULL DEFAULT 'INACTIVE',
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `branch_credit_limit_status`
--

CREATE TABLE `branch_credit_limit_status` (
  `sl_no` int(10) NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `credit_limit_status` enum('Y','N') NOT NULL DEFAULT 'N',
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `arc_status` enum('N','Y') NOT NULL DEFAULT 'N'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `branch_destination_freight`
--

CREATE TABLE `branch_destination_freight` (
  `sl_no` int(10) NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `destination_code` varchar(100) NOT NULL,
  `freight` double DEFAULT NULL,
  `acedns` varchar(10) NOT NULL,
  `date` date DEFAULT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `branch_dump`
--

CREATE TABLE `branch_dump` (
  `sl_no` int(10) NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `dump_code` varchar(100) NOT NULL,
  `dump_name` varchar(255) NOT NULL,
  `acedns` enum('Y','N') NOT NULL DEFAULT 'Y',
  `is_plant` enum('Y','N') NOT NULL DEFAULT 'Y',
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `branch_game_status`
--

CREATE TABLE `branch_game_status` (
  `sl_no` int(10) NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `game_status` enum('INACTIVE','ACTIVE') NOT NULL DEFAULT 'INACTIVE',
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `branch_master`
--

CREATE TABLE `branch_master` (
  `comp_code` varchar(20) NOT NULL,
  `branch_code` varchar(20) NOT NULL,
  `dns_branch_code` varchar(20) DEFAULT '',
  `branch_name` varchar(255) DEFAULT NULL,
  `branch_location` varchar(100) DEFAULT NULL,
  `branch_state` varchar(20) DEFAULT NULL,
  `branch_email_id` varchar(100) DEFAULT NULL,
  `HQ` varchar(255) DEFAULT NULL,
  `branch_accounts_email_id` varchar(100) DEFAULT NULL,
  `alternative_email_id` varchar(100) DEFAULT '',
  `plant_name` varchar(255) DEFAULT NULL,
  `is_plant` enum('yes','no') NOT NULL DEFAULT 'no',
  `costcenter` varchar(255) DEFAULT NULL,
  `acedns` enum('Y','N') NOT NULL DEFAULT 'Y',
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `branch_PGstatus`
--

CREATE TABLE `branch_PGstatus` (
  `sl_no` int(10) NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `pg_status` enum('INACTIVE','ACTIVE') NOT NULL DEFAULT 'INACTIVE',
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `branch_pop_product`
--

CREATE TABLE `branch_pop_product` (
  `sl_no` int(4) NOT NULL,
  `branch_code` varchar(50) DEFAULT NULL,
  `branch_name` varchar(255) DEFAULT NULL,
  `dns_prod_code` varchar(50) DEFAULT NULL,
  `prod_desc` varchar(255) DEFAULT NULL,
  `status` enum('Y','N') NOT NULL DEFAULT 'Y',
  `upload_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `branch_rssd_allocation_days`
--

CREATE TABLE `branch_rssd_allocation_days` (
  `sl_no` int(10) NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `allocation_days` int(3) NOT NULL DEFAULT '0',
  `upload_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `branch_schemes_PDF`
--

CREATE TABLE `branch_schemes_PDF` (
  `sl_no` int(10) NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `PDF_file_name` text NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `acedns` varchar(10) NOT NULL DEFAULT 'Y',
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `broker_master`
--

CREATE TABLE `broker_master` (
  `broker_id` varchar(30) NOT NULL,
  `dns_broker_id` varchar(30) DEFAULT NULL,
  `broker_name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `mail_id` varchar(255) DEFAULT NULL,
  `phone_no` varchar(50) DEFAULT NULL,
  `brokerage_cost` double DEFAULT NULL,
  `acedns` enum('Y','N') NOT NULL DEFAULT 'Y',
  `state_code` varchar(20) DEFAULT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sms_otp` varchar(15) DEFAULT NULL,
  `deviceid` varchar(100) DEFAULT NULL,
  `registrationid` text,
  `device_type` varchar(20) DEFAULT NULL,
  `app_version` varchar(20) DEFAULT NULL,
  `profile_image` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `call_duration`
--

CREATE TABLE `call_duration` (
  `transaction_id` varchar(50) NOT NULL,
  `customer_code` varchar(50) NOT NULL,
  `call_duration` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `changepassword`
--

CREATE TABLE `changepassword` (
  `emp_code` varchar(20) DEFAULT NULL,
  `customer_code` varchar(100) NOT NULL,
  `dns_customer_code` varchar(80) DEFAULT NULL,
  `newpassword` varchar(60) DEFAULT NULL,
  `oldpassword` varchar(60) DEFAULT NULL,
  `status` enum('true','false') DEFAULT NULL,
  `deviceid` varchar(100) DEFAULT NULL,
  `registrationid` text,
  `device_type` varchar(20) DEFAULT NULL,
  `app_version` varchar(30) DEFAULT NULL,
  `is_licensed` enum('0','1') NOT NULL DEFAULT '0',
  `loggedin_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_operation_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ch_data`
--

CREATE TABLE `ch_data` (
  `id` int(11) NOT NULL,
  `dns_emp_code` varchar(30) NOT NULL,
  `emp_code` varchar(30) NOT NULL,
  `emp_name` varchar(80) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `company_master`
--

CREATE TABLE `company_master` (
  `comp_code` varchar(20) NOT NULL,
  `dns_comp_code` varchar(20) DEFAULT NULL,
  `comp_name` varchar(255) NOT NULL,
  `admin_email_id` varchar(255) NOT NULL,
  `account_email_id` varchar(255) NOT NULL,
  `no_of_branches` int(5) NOT NULL,
  `db_backup` enum('yes','no') NOT NULL DEFAULT 'no'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `consumer_scheme`
--

CREATE TABLE `consumer_scheme` (
  `id` bigint(20) NOT NULL,
  `date_and_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `trans_id` varchar(255) DEFAULT NULL,
  `customer_id` varchar(50) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_phone_no` varchar(255) DEFAULT NULL,
  `dhalai_master_qty` varchar(20) DEFAULT NULL,
  `weather_shield_qty` varchar(20) DEFAULT NULL,
  `date_of_purchase` date DEFAULT NULL,
  `lottery_no` varchar(50) DEFAULT NULL,
  `is_duplicate` enum('yes','no') NOT NULL DEFAULT 'no'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cron_update_table`
--

CREATE TABLE `cron_update_table` (
  `id` int(11) NOT NULL,
  `process_name` varchar(80) DEFAULT NULL,
  `last_start_datetime` varchar(50) DEFAULT NULL,
  `last_end_datetime` varchar(50) DEFAULT NULL,
  `remark` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer_broker_relation`
--

CREATE TABLE `customer_broker_relation` (
  `customer_code` varchar(30) NOT NULL,
  `broker_code` varchar(30) NOT NULL,
  `mapped_broker` varchar(255) DEFAULT NULL,
  `acedns` enum('Y','N') NOT NULL DEFAULT 'Y',
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer_broker_relation_test`
--

CREATE TABLE `customer_broker_relation_test` (
  `customer_code` varchar(30) NOT NULL,
  `broker_code` varchar(30) DEFAULT NULL,
  `mapped_broker` varchar(10) DEFAULT NULL,
  `acedns` enum('Y','N') NOT NULL DEFAULT 'Y',
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer_destination`
--

CREATE TABLE `customer_destination` (
  `sl_no` int(10) NOT NULL,
  `customer_code` varchar(50) NOT NULL,
  `destination_code` varchar(100) NOT NULL,
  `acedns` varchar(10) NOT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer_invoice_table`
--

CREATE TABLE `customer_invoice_table` (
  `id` int(55) NOT NULL,
  `customer_code` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer_master`
--

CREATE TABLE `customer_master` (
  `customer_code` varchar(255) NOT NULL,
  `dns_customer_code` varchar(255) NOT NULL,
  `customer_id` varchar(50) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `pin` varchar(50) DEFAULT NULL,
  `phone_no` varchar(80) DEFAULT NULL,
  `landline_no` text,
  `route_code` varchar(255) DEFAULT NULL,
  `emp_code` varchar(20) DEFAULT NULL,
  `current_balance` varchar(50) DEFAULT NULL,
  `credit_limit` varchar(50) DEFAULT NULL,
  `credit_days` varchar(255) DEFAULT NULL,
  `acedns` varchar(2) DEFAULT NULL,
  `black_list` varchar(2) DEFAULT NULL,
  `TD` varchar(20) DEFAULT '0',
  `cust_type` varchar(100) DEFAULT NULL,
  `rds_tag` varchar(20) DEFAULT NULL,
  `sauda_validity_period` varchar(50) DEFAULT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `owner_phone` varchar(100) DEFAULT NULL,
  `cust_class` varchar(255) DEFAULT NULL,
  `weekly_closing_day` varchar(50) DEFAULT NULL,
  `coverage_type` varchar(50) DEFAULT NULL,
  `TIN` varchar(50) DEFAULT NULL,
  `PAN` varchar(200) DEFAULT NULL,
  `district` varchar(255) DEFAULT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `download_time_credit_limit` varchar(50) DEFAULT '0000-00-00 00:00:00',
  `vertical_value` varchar(20) DEFAULT NULL,
  `branch_code` varchar(255) DEFAULT NULL,
  `minimum_stock` varchar(50) NOT NULL DEFAULT '0',
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_account_number` varchar(50) DEFAULT NULL,
  `email` text,
  `visit_day` varchar(20) DEFAULT NULL,
  `state_code` varchar(20) DEFAULT NULL,
  `monthly_potential` varchar(50) NOT NULL DEFAULT '0',
  `sauda_limit` varchar(50) NOT NULL DEFAULT '0',
  `incoterms` varchar(20) DEFAULT NULL,
  `pending_qty` varchar(50) NOT NULL DEFAULT '0',
  `loadability_ton` int(3) DEFAULT NULL,
  `transport_mode` varchar(20) DEFAULT NULL,
  `sauda_type` varchar(10) DEFAULT NULL,
  `visit_sequence` int(3) DEFAULT NULL,
  `transferred` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `zone` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `whatsapp_no` varchar(30) DEFAULT NULL,
  `sms_otp` varchar(15) DEFAULT NULL,
  `order_restriction` enum('yes','no') NOT NULL DEFAULT 'no',
  `profile_image` varchar(200) DEFAULT NULL,
  `plant` varchar(50) DEFAULT NULL,
  `appointment_date` varchar(60) DEFAULT NULL,
  `lattitude` varchar(30) DEFAULT NULL,
  `longitude` varchar(30) DEFAULT NULL,
  `DOB` varchar(60) DEFAULT NULL,
  `ANV_DATE` varchar(60) DEFAULT NULL,
  `IFSC` varchar(50) DEFAULT NULL,
  `sub_zone` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer_master_STAR`
--

CREATE TABLE `customer_master_STAR` (
  `id` bigint(20) NOT NULL,
  `dns_customer_code` varchar(50) NOT NULL,
  `customer_name` varchar(80) NOT NULL,
  `address` text NOT NULL,
  `latlong` varchar(50) NOT NULL,
  `latitude` varchar(30) NOT NULL,
  `longitude` varchar(30) NOT NULL,
  `pin` varchar(20) NOT NULL,
  `phone_no` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer_master_temp`
--

CREATE TABLE `customer_master_temp` (
  `customer_code` varchar(20) NOT NULL,
  `customer_name` varchar(60) DEFAULT NULL,
  `phone_no` varchar(20) DEFAULT NULL,
  `route_code` varchar(20) DEFAULT NULL,
  `emp_code` varchar(20) DEFAULT NULL,
  `current_balance` double DEFAULT NULL,
  `credit_limit` double DEFAULT NULL,
  `acedns` varchar(2) DEFAULT '',
  `black_list` varchar(2) DEFAULT '',
  `TD` varchar(20) DEFAULT NULL,
  `vertical_value` longblob
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer_refference`
--

CREATE TABLE `customer_refference` (
  `sl_no` int(10) NOT NULL,
  `customer_replaced` text NOT NULL,
  `customer_replaced_by` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer_route_emp_relation`
--

CREATE TABLE `customer_route_emp_relation` (
  `sl_no` int(10) NOT NULL,
  `customer_code` varchar(255) NOT NULL,
  `emp_code` varchar(30) NOT NULL,
  `acedns` varchar(10) DEFAULT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer_route_emp_relation_test`
--

CREATE TABLE `customer_route_emp_relation_test` (
  `customer_code` varchar(255) NOT NULL,
  `route_code` varchar(255) NOT NULL,
  `emp_code` varchar(30) NOT NULL,
  `rds_tag` varchar(255) DEFAULT NULL,
  `acedns` varchar(5) DEFAULT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer_visit_details`
--

CREATE TABLE `customer_visit_details` (
  `row_id` bigint(20) NOT NULL,
  `emp_code` varchar(20) NOT NULL,
  `trans_id` varchar(30) NOT NULL,
  `customer_code` varchar(255) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `cust_type` varchar(30) NOT NULL,
  `route_code` varchar(255) NOT NULL,
  `route_name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cust_data`
--

CREATE TABLE `cust_data` (
  `id` int(11) NOT NULL,
  `dns_code` varchar(30) NOT NULL,
  `acsdns` varchar(10) NOT NULL,
  `cust_type` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `data_refresh_log`
--

CREATE TABLE `data_refresh_log` (
  `sl_no` int(10) NOT NULL,
  `refresh_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uploading_ip` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dbbackupcheck`
--

CREATE TABLE `dbbackupcheck` (
  `emp_code` varchar(20) NOT NULL,
  `device_id` varchar(20) NOT NULL,
  `is_checked` enum('0','1') NOT NULL DEFAULT '0',
  `is_delete` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `db_backup_files`
--

CREATE TABLE `db_backup_files` (
  `id` int(11) NOT NULL,
  `backup_file_name` varchar(80) NOT NULL,
  `date_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `db_version`
--

CREATE TABLE `db_version` (
  `version_code` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dealer_app_version`
--

CREATE TABLE `dealer_app_version` (
  `id` int(11) NOT NULL,
  `device_type` varchar(30) NOT NULL,
  `app_version` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dealer_reward_status`
--

CREATE TABLE `dealer_reward_status` (
  `sl_no` int(10) NOT NULL,
  `emp_code` varchar(50) NOT NULL,
  `dealer_status` varchar(255) DEFAULT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dealer_rssd_test`
--

CREATE TABLE `dealer_rssd_test` (
  `id` int(11) NOT NULL,
  `zone` varchar(150) NOT NULL,
  `branch_code` varchar(150) NOT NULL,
  `customer_name` varchar(200) NOT NULL,
  `dns_customer_code` varchar(100) NOT NULL,
  `customer_code` varchar(100) NOT NULL,
  `credit_limit` varchar(100) NOT NULL,
  `customer_id` varchar(100) NOT NULL,
  `security_deposit_amount` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dealer_sales_team_visit_survey`
--

CREATE TABLE `dealer_sales_team_visit_survey` (
  `sl_no` int(10) NOT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `sap_customer_code` varchar(50) DEFAULT NULL,
  `emp_code` varchar(50) DEFAULT NULL,
  `emp_name` varchar(255) DEFAULT NULL,
  `visit_datetime` timestamp NULL DEFAULT NULL,
  `survey_rating` varchar(50) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `rating_update_datetime` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dealer_security_ledger_status`
--

CREATE TABLE `dealer_security_ledger_status` (
  `sl_no` int(10) NOT NULL,
  `customer_code` varchar(50) NOT NULL,
  `security_ledger_status` enum('INACTIVE','ACTIVE','Not Set') NOT NULL DEFAULT 'ACTIVE',
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dealer_security_ledger_status_old`
--

CREATE TABLE `dealer_security_ledger_status_old` (
  `sl_no` int(10) NOT NULL,
  `customer_code` varchar(50) NOT NULL,
  `security_ledger_status` enum('INACTIVE','ACTIVE','Not Set') NOT NULL DEFAULT 'INACTIVE',
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dealer_tour_status`
--

CREATE TABLE `dealer_tour_status` (
  `sl_no` int(10) NOT NULL,
  `customer_code` varchar(50) NOT NULL,
  `tour_status` enum('INACTIVE','ACTIVE','Not Set') NOT NULL DEFAULT 'INACTIVE',
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_status_log`
--

CREATE TABLE `delivery_status_log` (
  `sl_no` bigint(10) NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `request_params` text NOT NULL,
  `response_value` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `destination_master`
--

CREATE TABLE `destination_master` (
  `destination_code` varchar(100) NOT NULL,
  `dns_destination_code` varchar(50) DEFAULT NULL,
  `destination_name` varchar(255) NOT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ex_for_type` enum('EX','FOR') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `destination_wise_price`
--

CREATE TABLE `destination_wise_price` (
  `id` int(11) NOT NULL,
  `dns_prod_code` varchar(40) DEFAULT NULL,
  `product_name` varchar(50) DEFAULT NULL,
  `dns_branch_code` varchar(40) DEFAULT NULL,
  `branch_name` varchar(50) DEFAULT NULL,
  `dns_destination_code` varchar(40) DEFAULT NULL,
  `destination_name` varchar(50) DEFAULT NULL,
  `rate` varchar(10) DEFAULT '0',
  `effective_date` varchar(40) DEFAULT NULL,
  `effective_rate` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `do_order_cancel_by_log`
--

CREATE TABLE `do_order_cancel_by_log` (
  `id` bigint(20) NOT NULL,
  `order_id` varchar(50) DEFAULT NULL,
  `cancel_by` varchar(30) DEFAULT NULL,
  `c_remark` varchar(250) DEFAULT NULL,
  `cancel_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dummy_registration_for_app_approval`
--

CREATE TABLE `dummy_registration_for_app_approval` (
  `id` int(11) NOT NULL,
  `name` varchar(40) DEFAULT NULL,
  `mobile` varchar(40) DEFAULT NULL,
  `password` varchar(40) DEFAULT NULL,
  `reg_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_kyc_master`
--

CREATE TABLE `employee_kyc_master` (
  `id` bigint(20) NOT NULL,
  `emp_code` varchar(30) DEFAULT NULL,
  `customer_code` varchar(80) DEFAULT NULL,
  `dns_customer_code` varchar(80) DEFAULT NULL,
  `whatsapp_no` varchar(20) DEFAULT NULL,
  `dob` varchar(30) DEFAULT NULL,
  `dom` varchar(30) DEFAULT NULL,
  `email_id` varchar(50) DEFAULT NULL,
  `last_updated_datetime` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_kyc_master_backup`
--

CREATE TABLE `employee_kyc_master_backup` (
  `id` bigint(20) NOT NULL,
  `emp_code` varchar(30) DEFAULT NULL,
  `whatsapp_no` varchar(20) DEFAULT NULL,
  `dob` varchar(30) DEFAULT NULL,
  `dom` varchar(30) DEFAULT NULL,
  `email_id` varchar(50) DEFAULT NULL,
  `last_updated_datetime` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_master`
--

CREATE TABLE `employee_master` (
  `emp_code` varchar(20) NOT NULL,
  `dns_emp_code` varchar(20) DEFAULT '',
  `emp_name` varchar(60) NOT NULL,
  `acedns` varchar(2) NOT NULL,
  `branch_code` text,
  `DOJ` varchar(100) DEFAULT NULL,
  `lower_leaves` text,
  `reporting_to` varchar(255) DEFAULT NULL,
  `vertical_value` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT '',
  `phone_no` varchar(50) DEFAULT '',
  `HQ` varchar(255) DEFAULT NULL,
  `sale_access` varchar(50) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `District` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zone` varchar(100) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `app_access` enum('Y','N') NOT NULL DEFAULT 'Y',
  `functionality` varchar(50) DEFAULT NULL,
  `functionality_rel_val` varchar(50) DEFAULT NULL,
  `level` varchar(10) DEFAULT NULL,
  `acedns_changed_date` varchar(50) DEFAULT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_master_bkup`
--

CREATE TABLE `employee_master_bkup` (
  `emp_code` varchar(20) NOT NULL,
  `dns_emp_code` varchar(20) DEFAULT '',
  `emp_name` varchar(60) NOT NULL,
  `acedns` varchar(2) NOT NULL,
  `branch_code` text,
  `DOJ` date NOT NULL,
  `lower_leaves` text,
  `reporting_to` varchar(255) DEFAULT NULL,
  `vertical_value` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT '',
  `phone_no` varchar(50) DEFAULT '',
  `HQ` varchar(255) DEFAULT NULL,
  `sale_access` varchar(50) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `District` varchar(255) NOT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zone` varchar(100) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `app_access` enum('Y','N') NOT NULL DEFAULT 'Y',
  `functionality` varchar(50) DEFAULT NULL,
  `functionality_rel_val` varchar(50) DEFAULT NULL,
  `level` varchar(10) NOT NULL,
  `acedns_changed_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_master_one`
--

CREATE TABLE `employee_master_one` (
  `emp_code` varchar(20) NOT NULL,
  `dns_emp_code` varchar(20) DEFAULT '',
  `emp_name` varchar(60) NOT NULL,
  `acedns` varchar(2) NOT NULL,
  `branch_code` text,
  `lower_leaves` text,
  `reporting_to` text,
  `vertical_value` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT '',
  `phone_no` varchar(50) DEFAULT '',
  `HQ` varchar(255) DEFAULT NULL,
  `sale_access` enum('primary','secondary','tertiary') NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zone` varchar(100) DEFAULT NULL,
  `app_access` enum('Y','N') NOT NULL DEFAULT 'Y'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_master_two`
--

CREATE TABLE `employee_master_two` (
  `emp_code` varchar(20) NOT NULL,
  `dns_emp_code` varchar(20) DEFAULT '',
  `emp_name` varchar(60) NOT NULL,
  `acedns` varchar(2) NOT NULL,
  `branch_code` text,
  `DOJ` date NOT NULL,
  `lower_leaves` text,
  `reporting_to` varchar(255) DEFAULT NULL,
  `vertical_value` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT '',
  `phone_no` varchar(50) DEFAULT '',
  `HQ` varchar(255) DEFAULT NULL,
  `sale_access` varchar(50) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `District` varchar(255) NOT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zone` varchar(100) DEFAULT NULL,
  `app_access` enum('Y','N') NOT NULL DEFAULT 'Y',
  `functionality` varchar(50) DEFAULT NULL,
  `functionality_rel_val` varchar(50) DEFAULT NULL,
  `acedns_changed_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `otp_code` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_route_swapping_log`
--

CREATE TABLE `employee_route_swapping_log` (
  `customer_code` varchar(255) NOT NULL,
  `route_code` varchar(255) NOT NULL,
  `emp_code` varchar(30) NOT NULL,
  `acedns` varchar(5) DEFAULT NULL,
  `activate_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deactivate_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_ip` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_swapping_log`
--

CREATE TABLE `employee_swapping_log` (
  `customer_code` varchar(255) NOT NULL,
  `route_code` varchar(255) NOT NULL,
  `emp_code` varchar(30) NOT NULL,
  `acedns` varchar(5) DEFAULT NULL,
  `activate_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deactivate_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_ip` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `emp_data_download_log`
--

CREATE TABLE `emp_data_download_log` (
  `sl_no` int(5) NOT NULL,
  `emp_code` varchar(50) NOT NULL,
  `is_download` enum('yes','no') NOT NULL DEFAULT 'no',
  `is_download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `emp_data_update_log`
--

CREATE TABLE `emp_data_update_log` (
  `emp_code` varchar(20) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `emp_menu_access`
--

CREATE TABLE `emp_menu_access` (
  `sl_no` int(5) NOT NULL,
  `emp_code` varchar(30) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `menu_type` enum('app','mis') NOT NULL,
  `menu` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `epod_details`
--

CREATE TABLE `epod_details` (
  `epod_id` bigint(20) NOT NULL,
  `customer_id` varchar(50) DEFAULT NULL,
  `date_and_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `challan_no` varchar(255) DEFAULT NULL,
  `is_delivered` enum('yes','no') DEFAULT NULL,
  `challan_date` datetime DEFAULT NULL,
  `total_order` int(3) DEFAULT NULL,
  `dispatched_order` int(3) DEFAULT NULL,
  `received_order` int(3) DEFAULT NULL,
  `pending_order` int(3) DEFAULT NULL,
  `delivery_qty` int(3) DEFAULT NULL,
  `remarks` text,
  `media` text,
  `upload_pic` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `foot_soldier`
--

CREATE TABLE `foot_soldier` (
  `foot_soldier_id` varchar(50) NOT NULL,
  `mall_name` varchar(255) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `pin_code` varchar(20) DEFAULT NULL,
  `type` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fpx_dealer_list`
--

CREATE TABLE `fpx_dealer_list` (
  `sl_no` int(10) NOT NULL,
  `dealer_sap_code` varchar(50) NOT NULL,
  `dealer_firm_name` varchar(255) DEFAULT NULL,
  `dealer_name` varchar(255) DEFAULT NULL,
  `branch_name` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `upload_time` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fpx_dealer_truck_mapping`
--

CREATE TABLE `fpx_dealer_truck_mapping` (
  `sl_no` int(10) NOT NULL,
  `dealer_sap_code` varchar(50) NOT NULL,
  `dealer_name` varchar(255) DEFAULT NULL,
  `branch_name` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `truck_no` varchar(50) DEFAULT NULL,
  `upload_time` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `generic_oil_master`
--

CREATE TABLE `generic_oil_master` (
  `oil_name` varchar(255) NOT NULL,
  `competitor_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ldg_id_to_del`
--

CREATE TABLE `ldg_id_to_del` (
  `ldg_id` bigint(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger`
--

CREATE TABLE `ledger` (
  `ldg_id` bigint(20) NOT NULL,
  `customer_code` varchar(80) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `voucher_date` varchar(50) DEFAULT NULL,
  `voucher_no` varchar(80) DEFAULT NULL,
  `quantity` varchar(30) DEFAULT NULL,
  `amount_dr` varchar(50) DEFAULT NULL,
  `amount_cr` varchar(50) DEFAULT NULL,
  `balance` varchar(500) DEFAULT NULL,
  `entry_date` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_balance`
--

CREATE TABLE `ledger_balance` (
  `lb_id` bigint(20) NOT NULL,
  `customer_code` varchar(80) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `balance` varchar(50) DEFAULT NULL,
  `date` varchar(30) DEFAULT NULL,
  `link` varchar(80) NOT NULL DEFAULT 'http://cp.starcement.co.in:2022/',
  `entry_date` varchar(50) DEFAULT NULL,
  `credit_limit` varchar(20) DEFAULT NULL,
  `credit_days` varchar(20) DEFAULT NULL,
  `current_balance` varchar(20) DEFAULT NULL,
  `pending_orders` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_transaction_table`
--

CREATE TABLE `ledger_transaction_table` (
  `lt_order_id` bigint(20) NOT NULL,
  `razorpay_order_id` varchar(40) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `customer_sap_code` varchar(50) DEFAULT NULL,
  `customer_name` varchar(60) DEFAULT NULL,
  `mobile` varchar(40) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `address` text,
  `amount` varchar(30) DEFAULT NULL,
  `payment_by` varchar(40) DEFAULT NULL,
  `order_status` varchar(80) DEFAULT 'Pending',
  `tracking_id` varchar(50) DEFAULT NULL,
  `razorpay_payment_id` varchar(50) DEFAULT NULL,
  `razorpay_signature` varchar(250) DEFAULT NULL,
  `payment_mode` varchar(30) DEFAULT NULL,
  `card_name` varchar(50) DEFAULT NULL,
  `bank_ref_no` varchar(50) DEFAULT NULL,
  `failure_message` varchar(100) DEFAULT NULL,
  `status_message` varchar(100) DEFAULT NULL,
  `order_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tran_datetime` varchar(30) DEFAULT NULL,
  `bin_country` varchar(50) DEFAULT NULL,
  `response_code` varchar(30) DEFAULT NULL,
  `status_code` varchar(30) DEFAULT NULL,
  `currency` varchar(30) NOT NULL DEFAULT 'INR',
  `vault` varchar(20) DEFAULT NULL,
  `offer_type` varchar(50) DEFAULT NULL,
  `offer_code` varchar(30) DEFAULT NULL,
  `discount_value` varchar(30) DEFAULT NULL,
  `mer_amount` varchar(30) DEFAULT NULL,
  `eci_value` varchar(30) DEFAULT NULL,
  `retry` varchar(30) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lifting`
--

CREATE TABLE `lifting` (
  `lid` bigint(20) NOT NULL,
  `linked_dealer_cust_code` varchar(40) DEFAULT NULL,
  `linked_dealer_code` varchar(40) DEFAULT NULL,
  `linked_dealer_sap_code` varchar(40) DEFAULT NULL,
  `linked_dealer_name` varchar(50) DEFAULT NULL,
  `sub_dealer_cust_code` varchar(40) DEFAULT NULL,
  `sub_dealer_rssd_code` varchar(40) DEFAULT NULL,
  `sub_dealer_rssd_sap_code` varchar(40) DEFAULT NULL,
  `sub_dealer_rssd_name` varchar(50) DEFAULT NULL,
  `branch_code` varchar(40) DEFAULT NULL,
  `dns_branch_code` varchar(40) DEFAULT NULL,
  `branch` varchar(50) DEFAULT NULL,
  `month` varchar(20) DEFAULT NULL,
  `prod_code` varchar(50) DEFAULT NULL,
  `dns_prod_code` varchar(50) DEFAULT NULL,
  `prod_display_name` varchar(50) DEFAULT NULL,
  `ppc` varchar(30) DEFAULT NULL,
  `arc` varchar(30) DEFAULT NULL,
  `opc` varchar(30) DEFAULT NULL,
  `total_bags` varchar(30) DEFAULT NULL,
  `date_of_lifting` varchar(30) DEFAULT NULL,
  `challan_no` varchar(30) DEFAULT NULL,
  `submit_date_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(30) DEFAULT 'PENDING',
  `status_date_and_time` varchar(30) DEFAULT NULL,
  `reason_for_rejection` varchar(250) DEFAULT NULL,
  `total_subdealer_rssd_sale` varchar(30) DEFAULT NULL,
  `total_dealer_sale` varchar(30) DEFAULT NULL,
  `subdealer_rssd_sale_percent` varchar(30) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lifting_date_validation`
--

CREATE TABLE `lifting_date_validation` (
  `sl_no` int(10) NOT NULL,
  `branch` varchar(30) NOT NULL,
  `customer_code` varchar(30) DEFAULT NULL,
  `validation_from` varchar(50) NOT NULL,
  `validation_to` varchar(50) NOT NULL,
  `validation_last_date` varchar(50) DEFAULT NULL,
  `approval_last_date` varchar(50) DEFAULT NULL,
  `validation_create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `emp_code` varchar(20) NOT NULL,
  `trans_id` varchar(30) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `latt` double NOT NULL,
  `longi` double NOT NULL,
  `transferred` enum('YES','NO') NOT NULL DEFAULT 'NO'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lottery_master`
--

CREATE TABLE `lottery_master` (
  `lottery_no` int(7) NOT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  `trans_id` varchar(50) NOT NULL,
  `datetime` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mail_access`
--

CREATE TABLE `mail_access` (
  `id` int(4) NOT NULL,
  `mail_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `branch_code` text NOT NULL,
  `attributes` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `market_feedback`
--

CREATE TABLE `market_feedback` (
  `market_feedback_id` varchar(50) NOT NULL,
  `route_code` varchar(50) NOT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `product_group` varchar(100) NOT NULL,
  `competitor_name` varchar(255) NOT NULL,
  `PTD` double NOT NULL,
  `PTR` double NOT NULL,
  `PTC` double NOT NULL,
  `PV` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `menu_access`
--

CREATE TABLE `menu_access` (
  `sl_no` int(10) NOT NULL,
  `emp_code` varchar(30) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `not_accessible_menu` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `menu_master`
--

CREATE TABLE `menu_master` (
  `menu_id` int(5) NOT NULL,
  `menu_name` varchar(255) NOT NULL,
  `menu_admin_page` varchar(255) DEFAULT NULL,
  `is_active` enum('yes','no') NOT NULL DEFAULT 'yes'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mf_stk_audit_details`
--

CREATE TABLE `mf_stk_audit_details` (
  `mf_stk_audit_id` varchar(30) NOT NULL,
  `competitor_name` varchar(100) NOT NULL,
  `qty_mt` double NOT NULL,
  `scheme_discount` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mf_stk_audit_header`
--

CREATE TABLE `mf_stk_audit_header` (
  `mf_stk_audit_id` varchar(30) NOT NULL,
  `customer_code` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `remarks` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `missing_erp_challan`
--

CREATE TABLE `missing_erp_challan` (
  `id` bigint(20) NOT NULL,
  `APPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERDT` varchar(50) DEFAULT NULL,
  `CHALLANNO` varchar(50) DEFAULT NULL,
  `CHALLANDT` varchar(50) DEFAULT NULL,
  `prod_code` varchar(50) DEFAULT NULL,
  `dns_prod_code` varchar(50) DEFAULT NULL,
  `prod_display_name` varchar(50) DEFAULT NULL,
  `QTY` varchar(30) DEFAULT NULL,
  `CHALLANQTY` varchar(50) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `TRUCKNO` varchar(50) DEFAULT NULL,
  `DRIVERNO` varchar(50) DEFAULT NULL,
  `LMDT` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `missing_erp_orders`
--

CREATE TABLE `missing_erp_orders` (
  `id` bigint(20) NOT NULL,
  `APPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERDT` varchar(50) DEFAULT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `order_for` varchar(200) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `prod_code` varchar(50) DEFAULT NULL,
  `dns_prod_code` varchar(50) DEFAULT NULL,
  `prod_display_name` varchar(50) DEFAULT NULL,
  `QTY` varchar(30) DEFAULT NULL,
  `STATUS` enum('Order received','DO approved','Dispatched') DEFAULT 'Order received',
  `destination_code` varchar(50) DEFAULT NULL,
  `destination_name` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mis_data_details`
--

CREATE TABLE `mis_data_details` (
  `emp_code` varchar(20) NOT NULL,
  `sale_access` varchar(30) NOT NULL,
  `reporting_to` text NOT NULL,
  `customer_code` text,
  `present_tdy` double NOT NULL,
  `present_mtd` double NOT NULL,
  `present_ytd` double NOT NULL,
  `customer_visited_tdy` double NOT NULL,
  `customer_visited_mtd` double NOT NULL,
  `customer_visited_ytd` double NOT NULL,
  `order_received_tdy` double NOT NULL,
  `order_received_mtd` double NOT NULL,
  `order_received_ytd` double NOT NULL,
  `no_transaction_tdy` double NOT NULL,
  `no_transaction_mtd` double NOT NULL,
  `no_transaction_ytd` int(11) NOT NULL,
  `stock_audit_tdy` double NOT NULL,
  `stock_audit_mtd` double NOT NULL,
  `stock_audit_ytd` double NOT NULL,
  `kyc_tdy` double NOT NULL,
  `kyc_mtd` double NOT NULL,
  `kyc_ytd` double NOT NULL,
  `brand_activity_tdy` double NOT NULL,
  `brand_activity_mtd` double NOT NULL,
  `brand_activity_ytd` double NOT NULL,
  `technical_meet_tdy` double NOT NULL,
  `technical_meet_mtd` double NOT NULL,
  `technical_meet_ytd` double NOT NULL,
  `site_visit_tdy` double NOT NULL,
  `site_visit_mtd` double NOT NULL,
  `site_visit_ytd` double NOT NULL,
  `market_feedback_tdy` double NOT NULL,
  `market_feedback_mtd` double NOT NULL,
  `market_feedback_ytd` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mis_details_emp_datewise`
--

CREATE TABLE `mis_details_emp_datewise` (
  `sl_no` int(20) NOT NULL,
  `operation_date` date NOT NULL,
  `emp_code` varchar(20) NOT NULL,
  `present` double NOT NULL,
  `customer_visited` text,
  `order_received` double NOT NULL,
  `no_transaction` double NOT NULL,
  `stock_audit` double NOT NULL,
  `kyc` double NOT NULL,
  `brand_activity` double NOT NULL,
  `technical_meet` double NOT NULL,
  `site_visit` double NOT NULL,
  `market_feedback` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mrp`
--

CREATE TABLE `mrp` (
  `branch_code` varchar(20) DEFAULT '',
  `product_code` varchar(20) NOT NULL,
  `mrp_code` varchar(20) NOT NULL,
  `dns_mrp_code` varchar(20) DEFAULT '',
  `destination_code` varchar(100) DEFAULT NULL,
  `order_type` varchar(50) DEFAULT NULL,
  `mrp` double DEFAULT NULL,
  `sale_rate` double DEFAULT NULL,
  `UOM` varchar(50) DEFAULT NULL,
  `vertical_value` varchar(20) DEFAULT NULL,
  `acedns` varchar(2) NOT NULL DEFAULT 'Y',
  `state_code` varchar(50) DEFAULT NULL,
  `ws_rate` double DEFAULT NULL,
  `distributor_rate` double DEFAULT NULL,
  `ss_rate` double DEFAULT NULL,
  `depot_rate` double DEFAULT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `north_east_branch`
--

CREATE TABLE `north_east_branch` (
  `sl_no` int(3) NOT NULL,
  `branch_code` varchar(100) DEFAULT NULL,
  `branch_name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `notes_info_details`
--

CREATE TABLE `notes_info_details` (
  `notes_info_id` varchar(255) NOT NULL,
  `feedback` text,
  `hint_remarks` text NOT NULL,
  `image` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notice_2023`
--

CREATE TABLE `notice_2023` (
  `sf_id` bigint(20) NOT NULL,
  `sf_cust_code` varchar(80) DEFAULT NULL,
  `sf_dealer_id` varchar(80) DEFAULT NULL,
  `sf_dealer_sap_code` varchar(40) DEFAULT NULL,
  `sf_dealer_name` varchar(100) DEFAULT NULL,
  `sf_dealer_mobile` varchar(60) DEFAULT NULL,
  `sf_branch_name` varchar(80) DEFAULT NULL,
  `sf_branch_code` varchar(50) DEFAULT NULL,
  `sf_dns_branch_code` varchar(50) DEFAULT NULL,
  `sf_state` varchar(80) DEFAULT NULL,
  `sf_is_checked_declaration` varchar(40) DEFAULT NULL,
  `sf_submitted_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notice_branch`
--

CREATE TABLE `notice_branch` (
  `nb_id` bigint(20) NOT NULL,
  `nb_branch_code` varchar(20) DEFAULT NULL,
  `nb_dns_branch_code` varchar(20) DEFAULT NULL,
  `nb_branch_name` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notification_ack_relation`
--

CREATE TABLE `notification_ack_relation` (
  `notification_id` varchar(30) NOT NULL,
  `receiver_id` varchar(30) NOT NULL,
  `ack_id` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notification_master`
--

CREATE TABLE `notification_master` (
  `notification_id` varchar(30) NOT NULL,
  `type_of_notification` varchar(50) NOT NULL,
  `sender_id` varchar(30) NOT NULL,
  `message` text NOT NULL,
  `transferred` enum('YES','NO') NOT NULL DEFAULT 'NO'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notification_message`
--

CREATE TABLE `notification_message` (
  `id` bigint(20) NOT NULL,
  `branch_code` text NOT NULL,
  `title` varchar(180) DEFAULT NULL,
  `message` text,
  `image_name` varchar(80) DEFAULT NULL,
  `file_type` enum('NONE','IMAGE','PDF') NOT NULL DEFAULT 'NONE',
  `sending_count` varchar(30) NOT NULL DEFAULT '0',
  `status` enum('STARTED','END') NOT NULL DEFAULT 'STARTED',
  `date_time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notification_read_table`
--

CREATE TABLE `notification_read_table` (
  `nrt_id` bigint(20) NOT NULL,
  `noti_id` varchar(30) DEFAULT NULL,
  `member_id` varchar(50) DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `not_north_east_branch`
--

CREATE TABLE `not_north_east_branch` (
  `sl_no` int(3) NOT NULL,
  `branch_code` varchar(50) DEFAULT NULL,
  `branch_name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `nye_branch`
--

CREATE TABLE `nye_branch` (
  `nb_id` bigint(20) NOT NULL,
  `nb_branch_code` varchar(20) DEFAULT NULL,
  `nb_dns_branch_code` varchar(20) DEFAULT NULL,
  `nb_branch_name` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `orderdata`
--

CREATE TABLE `orderdata` (
  `id` int(5) NOT NULL,
  `data` text NOT NULL,
  `insertdatetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_no` varchar(30) NOT NULL,
  `sku_code` varchar(20) NOT NULL,
  `qty` double NOT NULL,
  `mrp_code` varchar(20) DEFAULT NULL,
  `TD` double DEFAULT NULL,
  `premium` double DEFAULT NULL,
  `VAT` double DEFAULT NULL,
  `sale_rate` double DEFAULT NULL,
  `freight_charge` double DEFAULT NULL,
  `UOM` varchar(50) DEFAULT NULL,
  `amount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `order_header`
--

CREATE TABLE `order_header` (
  `order_no` varchar(30) NOT NULL,
  `customer_code` varchar(255) NOT NULL,
  `branch_code` varchar(30) DEFAULT NULL,
  `destination_code` varchar(100) DEFAULT NULL,
  `vertical_value` varchar(50) DEFAULT NULL,
  `d_instruction` longtext NOT NULL,
  `hint_remarks` varchar(255) DEFAULT NULL,
  `sale_type` varchar(8) NOT NULL,
  `order_type` varchar(50) DEFAULT NULL,
  `order_value` double NOT NULL DEFAULT '0',
  `TD` double NOT NULL,
  `tag_distributor_code` varchar(30) DEFAULT NULL,
  `transaction_type` varchar(10) NOT NULL,
  `GST_type` varchar(50) NOT NULL,
  `VAT` double DEFAULT NULL,
  `freight_component` varchar(50) DEFAULT NULL,
  `transferred` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `price_validation_type` enum('DOPS','SPA') NOT NULL DEFAULT 'DOPS',
  `status` enum('Pending','Confirm') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `order_query`
--

CREATE TABLE `order_query` (
  `id` bigint(20) NOT NULL,
  `date_and_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `order_id` varchar(255) DEFAULT NULL,
  `customer_code` varchar(255) DEFAULT NULL,
  `linked_dealer_code` varchar(255) DEFAULT NULL,
  `dns_prod_code` varchar(255) DEFAULT NULL,
  `prod_name` varchar(255) DEFAULT NULL,
  `qty_bags` varchar(255) DEFAULT NULL,
  `query_date` varchar(255) DEFAULT NULL,
  `date_of_lifting` varchar(100) NOT NULL,
  `remarks` varchar(1000) DEFAULT NULL,
  `status_from_app` varchar(150) DEFAULT NULL,
  `status_remarks` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `order_restriction`
--

CREATE TABLE `order_restriction` (
  `SAP_code` varchar(50) NOT NULL,
  `order_restriction` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `outstanding`
--

CREATE TABLE `outstanding` (
  `customer_code` varchar(20) NOT NULL,
  `route_code` varchar(20) DEFAULT '',
  `recid` varchar(30) NOT NULL DEFAULT '0',
  `invoice_id` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `invoice_amount` double NOT NULL,
  `due_amount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payment_details`
--

CREATE TABLE `payment_details` (
  `receipt_id` varchar(30) NOT NULL,
  `invoice_id` varchar(30) NOT NULL,
  `recid` varchar(30) NOT NULL DEFAULT '0',
  `amount` double NOT NULL,
  `discount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payment_header`
--

CREATE TABLE `payment_header` (
  `receipt_id` varchar(30) NOT NULL,
  `customer_code` varchar(255) NOT NULL,
  `amount` double NOT NULL,
  `cash_cheque` varchar(50) DEFAULT NULL,
  `cheque_no` varchar(22) NOT NULL,
  `date` date NOT NULL,
  `bank` varchar(255) DEFAULT NULL,
  `rdate` date DEFAULT NULL,
  `sale_type` varchar(8) NOT NULL,
  `p_remark` longtext NOT NULL,
  `transferred` enum('YES','NO') NOT NULL DEFAULT 'NO'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pbranchmaster`
--

CREATE TABLE `pbranchmaster` (
  `MANDT` varchar(3) DEFAULT NULL,
  `KUNNR` varchar(10) DEFAULT NULL,
  `VKGRP` varchar(3) DEFAULT NULL,
  `BEZEI` varchar(20) DEFAULT NULL,
  `BUKRS` varchar(4) DEFAULT NULL,
  `STATE` varchar(30) DEFAULT NULL,
  `ERDAT` varchar(8) DEFAULT NULL,
  `AEDAT` varchar(8) DEFAULT NULL,
  `STATUS` varchar(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pcustomergroup`
--

CREATE TABLE `pcustomergroup` (
  `customer_grp` varchar(50) NOT NULL,
  `description` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `performance`
--

CREATE TABLE `performance` (
  `id` bigint(20) NOT NULL,
  `customer_code` varchar(50) NOT NULL,
  `dns_customer_code` varchar(50) NOT NULL,
  `customer_name` varchar(150) NOT NULL,
  `product_desc` text NOT NULL,
  `prv_april` varchar(20) NOT NULL,
  `prv_april_achv` varchar(20) NOT NULL,
  `april` varchar(20) NOT NULL,
  `april_achv` varchar(20) NOT NULL,
  `prev_may` varchar(20) NOT NULL,
  `prev_may_achv` varchar(20) NOT NULL,
  `may` varchar(20) NOT NULL,
  `may_achv` varchar(20) NOT NULL,
  `prev_june` varchar(20) NOT NULL,
  `prev_june_achv` varchar(20) NOT NULL,
  `june` varchar(20) NOT NULL,
  `june_achv` varchar(20) NOT NULL,
  `prev_july` varchar(20) NOT NULL,
  `prev_july_achv` varchar(20) NOT NULL,
  `july` varchar(20) NOT NULL,
  `july_achv` varchar(20) NOT NULL,
  `prev_aug` varchar(20) NOT NULL,
  `prev_aug_achv` varchar(20) NOT NULL,
  `august` varchar(20) NOT NULL,
  `august_achv` varchar(20) NOT NULL,
  `prev_sep` varchar(20) NOT NULL,
  `prev_sep_achv` varchar(20) NOT NULL,
  `september` varchar(20) NOT NULL,
  `september_achv` varchar(20) NOT NULL,
  `prev_oct` varchar(20) NOT NULL,
  `prev_oct_achv` varchar(20) NOT NULL,
  `october` varchar(20) NOT NULL,
  `october_achv` varchar(20) NOT NULL,
  `prev_nov` varchar(20) NOT NULL,
  `prev_nov_achv` varchar(20) NOT NULL,
  `november` varchar(20) NOT NULL,
  `november_achv` varchar(20) NOT NULL,
  `prev_dec` varchar(20) NOT NULL,
  `prev_dec_achv` varchar(20) NOT NULL,
  `december` varchar(20) NOT NULL,
  `december_achv` varchar(20) NOT NULL,
  `prev_jan` varchar(20) NOT NULL,
  `prev_jan_achv` varchar(20) NOT NULL,
  `january` varchar(20) NOT NULL,
  `january_achv` varchar(20) NOT NULL,
  `prev_feb` varchar(20) NOT NULL,
  `prev_feb_achv` varchar(20) NOT NULL,
  `february` varchar(20) NOT NULL,
  `february_achv` varchar(20) NOT NULL,
  `prev_march` varchar(20) NOT NULL,
  `prev_march_achv` varchar(20) NOT NULL,
  `march` varchar(20) NOT NULL,
  `march_achv` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `place_order_lifting_days`
--

CREATE TABLE `place_order_lifting_days` (
  `sl_no` int(10) NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `days` int(3) NOT NULL DEFAULT '0',
  `upload_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pop_order`
--

CREATE TABLE `pop_order` (
  `id` bigint(20) NOT NULL,
  `date_and_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `formatted_date` date DEFAULT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `customer_code` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `linked_dealer_code` varchar(255) DEFAULT NULL,
  `linked_dealer_name` varchar(255) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `qty` varchar(255) DEFAULT NULL,
  `price_per_pcs` varchar(255) DEFAULT NULL,
  `gst_percent` varchar(55) DEFAULT NULL,
  `net_amount` varchar(255) DEFAULT NULL,
  `gst_amount` varchar(255) DEFAULT NULL,
  `total_amount` varchar(255) DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pin_code` varchar(255) DEFAULT NULL,
  `remarks` text,
  `printed_address_pin` varchar(255) DEFAULT NULL,
  `contact_num_printed` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pop_order_03_09_2024`
--

CREATE TABLE `pop_order_03_09_2024` (
  `id` bigint(20) NOT NULL,
  `date_and_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `formatted_date` date DEFAULT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `customer_code` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `linked_dealer_code` varchar(255) DEFAULT NULL,
  `linked_dealer_name` varchar(255) DEFAULT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `qty` varchar(255) DEFAULT NULL,
  `price_per_pcs` varchar(255) DEFAULT NULL,
  `gst_percent` varchar(55) DEFAULT NULL,
  `net_amount` varchar(255) DEFAULT NULL,
  `gst_amount` varchar(255) DEFAULT NULL,
  `total_amount` varchar(255) DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pin_code` varchar(255) DEFAULT NULL,
  `remarks` text,
  `printed_address_pin` varchar(255) DEFAULT NULL,
  `contact_num_printed` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pop_product_master`
--

CREATE TABLE `pop_product_master` (
  `sl_no` int(10) NOT NULL,
  `dns_prod_code` varchar(50) DEFAULT NULL,
  `prod_desc` varchar(255) DEFAULT NULL,
  `prod_image` varchar(255) DEFAULT NULL,
  `min_order_qty` varchar(50) DEFAULT NULL,
  `price_per_piece` varchar(50) DEFAULT NULL,
  `GST_rate` varchar(20) DEFAULT NULL,
  `status` enum('Y','N') DEFAULT NULL,
  `upload_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_gateway` enum('Y','N') NOT NULL DEFAULT 'Y',
  `dealer_login` enum('Y','N') NOT NULL DEFAULT 'Y',
  `sp_login` enum('Y','N') NOT NULL DEFAULT 'Y'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `prev_stock_counting_master`
--

CREATE TABLE `prev_stock_counting_master` (
  `customer_code` varchar(20) NOT NULL,
  `product_code` varchar(20) NOT NULL,
  `visit_1` double NOT NULL,
  `visit1_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `visit_2` double NOT NULL,
  `visit2_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `visit_3` double NOT NULL,
  `visit3_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `download_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `product_brand_master`
--

CREATE TABLE `product_brand_master` (
  `product_brand_code` varchar(255) NOT NULL,
  `dns_product_brand_code` varchar(255) DEFAULT '',
  `product_sub_group_code` varchar(255) NOT NULL,
  `product_brand_name` varchar(255) NOT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vertical_value` varchar(20) DEFAULT NULL,
  `is_download` varchar(5) DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `product_group_master`
--

CREATE TABLE `product_group_master` (
  `product_group_code` varchar(255) NOT NULL,
  `dns_product_group_code` varchar(255) DEFAULT NULL,
  `product_group_name` varchar(255) NOT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vertical_value` varchar(20) DEFAULT NULL,
  `acedns` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `product_master`
--

CREATE TABLE `product_master` (
  `branch_code` varchar(20) NOT NULL DEFAULT '',
  `prod_code` varchar(20) NOT NULL,
  `dns_prod_code` varchar(20) DEFAULT '',
  `product_group_code` varchar(255) DEFAULT NULL,
  `product_sub_group_code` varchar(255) DEFAULT NULL,
  `product_brand_code` varchar(255) DEFAULT NULL,
  `prod_desc` text,
  `cl_stk` double DEFAULT NULL,
  `UOM1` varchar(10) DEFAULT NULL,
  `UOM2` varchar(10) DEFAULT NULL,
  `UOM3` varchar(10) DEFAULT NULL,
  `acedns` varchar(2) NOT NULL DEFAULT '',
  `black_list` varchar(2) DEFAULT NULL,
  `vertical_value` varchar(20) DEFAULT NULL,
  `freight_cost` double DEFAULT NULL,
  `focus` enum('Y','N') NOT NULL DEFAULT 'N',
  `weightage` varchar(50) DEFAULT NULL,
  `vat` double DEFAULT NULL,
  `addl_vat` double DEFAULT NULL,
  `conversion_factor` varchar(255) DEFAULT NULL,
  `conversion_factor_two` varchar(50) DEFAULT NULL,
  `secondary_unit` varchar(10) DEFAULT NULL,
  `pack_size` varchar(50) DEFAULT NULL,
  `pack_unit` varchar(10) DEFAULT NULL,
  `TD` double DEFAULT NULL,
  `dealer_login` enum('Y','N') NOT NULL DEFAULT 'Y',
  `sp_login` enum('Y','N') NOT NULL DEFAULT 'Y',
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `download_time_cl_stk` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `product_master_sync`
--

CREATE TABLE `product_master_sync` (
  `branch_code` varchar(20) NOT NULL DEFAULT '',
  `state_code` varchar(50) DEFAULT NULL,
  `prod_code` varchar(20) NOT NULL,
  `dns_prod_code` varchar(20) DEFAULT '',
  `product_group_code` varchar(255) DEFAULT NULL,
  `product_sub_group_code` varchar(255) DEFAULT NULL,
  `product_brand_code` varchar(255) DEFAULT NULL,
  `prod_desc` text,
  `cl_stk` double DEFAULT NULL,
  `UOM1` varchar(10) DEFAULT NULL,
  `UOM2` varchar(10) DEFAULT NULL,
  `UOM3` varchar(10) DEFAULT NULL,
  `UOM4` varchar(10) DEFAULT NULL,
  `UOM5` varchar(10) DEFAULT NULL,
  `acedns` varchar(2) NOT NULL DEFAULT '',
  `black_list` varchar(2) NOT NULL,
  `vertical_value` varchar(20) DEFAULT NULL,
  `freight_cost` double DEFAULT NULL,
  `focus` enum('Y','N') NOT NULL DEFAULT 'N',
  `weightage` varchar(50) DEFAULT NULL,
  `vat` double DEFAULT NULL,
  `addl_vat` double DEFAULT NULL,
  `lead_time` varchar(50) DEFAULT NULL,
  `buffer_level` varchar(70) DEFAULT NULL,
  `max_level_marketing` varchar(100) DEFAULT NULL,
  `gross_weight` varchar(50) DEFAULT NULL,
  `packing_realization` double DEFAULT NULL,
  `conversion_factor` varchar(255) DEFAULT NULL,
  `conversion_factor_two` varchar(50) DEFAULT NULL,
  `secondary_unit` varchar(10) DEFAULT NULL,
  `pack_size` varchar(50) DEFAULT NULL,
  `pack_unit` varchar(10) DEFAULT NULL,
  `TD` double DEFAULT NULL,
  `prod_size` varchar(255) DEFAULT NULL,
  `fg_rm` varchar(50) DEFAULT NULL,
  `oil_category` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `hsn_sac` varchar(255) DEFAULT NULL,
  `CGST` double DEFAULT NULL,
  `IGST` double DEFAULT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `download_time_cl_stk` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `product_master_temp`
--

CREATE TABLE `product_master_temp` (
  `branch_code` varchar(20) NOT NULL DEFAULT '',
  `prod_code` varchar(20) NOT NULL,
  `product_group_code` varchar(255) NOT NULL,
  `product_sub_group_code` varchar(255) NOT NULL,
  `product_brand_code` varchar(255) NOT NULL,
  `prod_desc` text,
  `cl_stk` double NOT NULL,
  `acedns` varchar(2) NOT NULL DEFAULT '',
  `black_list` varchar(2) NOT NULL,
  `vertical_value` varchar(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `product_master_test`
--

CREATE TABLE `product_master_test` (
  `branch_code` varchar(20) NOT NULL DEFAULT '',
  `prod_code` varchar(20) NOT NULL,
  `dns_prod_code` varchar(20) DEFAULT '',
  `product_group_code` varchar(255) DEFAULT NULL,
  `product_sub_group_code` varchar(255) DEFAULT NULL,
  `product_brand_code` varchar(255) DEFAULT NULL,
  `prod_desc` text,
  `cl_stk` double DEFAULT NULL,
  `UOM1` varchar(10) DEFAULT NULL,
  `UOM2` varchar(10) DEFAULT NULL,
  `UOM3` varchar(10) DEFAULT NULL,
  `acedns` varchar(2) NOT NULL DEFAULT '',
  `black_list` varchar(2) DEFAULT NULL,
  `vertical_value` varchar(20) DEFAULT NULL,
  `freight_cost` double DEFAULT NULL,
  `focus` enum('Y','N') NOT NULL DEFAULT 'N',
  `weightage` varchar(50) DEFAULT NULL,
  `vat` double DEFAULT NULL,
  `addl_vat` double DEFAULT NULL,
  `conversion_factor` varchar(255) DEFAULT NULL,
  `conversion_factor_two` varchar(50) DEFAULT NULL,
  `secondary_unit` varchar(10) DEFAULT NULL,
  `pack_size` varchar(50) DEFAULT NULL,
  `pack_unit` varchar(10) DEFAULT NULL,
  `TD` double DEFAULT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `download_time_cl_stk` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `product_sub_group_master`
--

CREATE TABLE `product_sub_group_master` (
  `product_sub_group_code` varchar(255) NOT NULL,
  `dns_product_sub_group_code` varchar(255) DEFAULT '',
  `product_group_code` varchar(255) NOT NULL,
  `product_sub_group_name` varchar(255) NOT NULL,
  `vertical_value` varchar(20) DEFAULT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `prospective_customer_details`
--

CREATE TABLE `prospective_customer_details` (
  `trans_id` varchar(30) NOT NULL,
  `product_code` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `prospective_customer_header`
--

CREATE TABLE `prospective_customer_header` (
  `trans_id` varchar(30) NOT NULL,
  `customer_code` varchar(50) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `pin` varchar(20) NOT NULL,
  `area` varchar(255) NOT NULL,
  `phone_no` varchar(20) NOT NULL,
  `remarks` text NOT NULL,
  `cust_type` varchar(10) DEFAULT NULL,
  `tagged_customer_code` varchar(20) DEFAULT 'NULL'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `prospective_customer_master`
--

CREATE TABLE `prospective_customer_master` (
  `emp_code` varchar(20) NOT NULL,
  `customer_code` varchar(30) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `pin` varchar(20) NOT NULL,
  `area` varchar(255) NOT NULL,
  `phone_no` varchar(20) NOT NULL,
  `cust_type` varchar(20) NOT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ptblcustomermaster`
--

CREATE TABLE `ptblcustomermaster` (
  `MANDT` varchar(3) DEFAULT NULL COMMENT 'Client',
  `KUNNR` varchar(10) DEFAULT NULL COMMENT 'Customer Number',
  `VKORG` varchar(4) DEFAULT NULL COMMENT 'Sales Organization',
  `VWERK` varchar(4) DEFAULT NULL COMMENT 'Delivering Plant (Own or External)',
  `NAME1` varchar(35) DEFAULT NULL COMMENT 'Name 1',
  `NAME2` varchar(35) DEFAULT NULL COMMENT 'Name 2',
  `NAME3` varchar(35) DEFAULT NULL COMMENT 'Name 3',
  `NAME4` varchar(40) DEFAULT NULL COMMENT 'Name 4 of organization',
  `STRAS` varchar(35) DEFAULT NULL COMMENT 'Street and House Number',
  `STREET1` varchar(40) DEFAULT NULL COMMENT 'Street 2',
  `STREET2` varchar(40) DEFAULT NULL COMMENT 'Street 3',
  `STREET3` varchar(40) DEFAULT NULL COMMENT 'Street 4',
  `PSTLZ` varchar(10) DEFAULT NULL COMMENT 'Postal Code',
  `ORT01` varchar(35) DEFAULT NULL COMMENT 'City',
  `ORT02` varchar(35) DEFAULT NULL COMMENT 'District',
  `CITY2` varchar(40) DEFAULT NULL COMMENT 'District',
  `ADD_VAL_FR` varchar(10) DEFAULT NULL COMMENT 'Valid From Date of a BP Address',
  `ADD_VAL_TO` varchar(10) DEFAULT NULL COMMENT 'Validity End of a BP Address',
  `LATITUDE` varchar(25) DEFAULT NULL COMMENT 'Train station',
  `LONGITUDE` varchar(10) DEFAULT NULL COMMENT 'City Coordinates',
  `TELF2` varchar(16) DEFAULT NULL COMMENT 'Second telephone number',
  `SMTP_ADDR` varchar(241) DEFAULT NULL COMMENT 'E-Mail Address',
  `DOB` varchar(10) DEFAULT NULL COMMENT 'Date Organization Founded',
  `ANV_DATE` varchar(10) DEFAULT NULL COMMENT 'Business partner: Liquidation date of organization',
  `KDGRP` varchar(2) DEFAULT NULL COMMENT 'Customer Group',
  `KTEXT` varchar(20) DEFAULT NULL COMMENT 'Name',
  `LZONE` varchar(10) DEFAULT NULL COMMENT 'Transportation zone to or from which the goods are delivered',
  `BUKRS` varchar(4) DEFAULT NULL COMMENT 'Company Code',
  `BANK_KEY` varchar(15) DEFAULT NULL COMMENT 'Bank Key',
  `ALTKN` varchar(10) DEFAULT NULL COMMENT 'Previous Master Record Number',
  `ERDAT` varchar(8) DEFAULT NULL COMMENT 'Date on which the Record Was Created',
  `AEDAT` varchar(8) DEFAULT NULL COMMENT 'Changed On',
  `KONDA` varchar(2) DEFAULT NULL COMMENT 'Customer Price Group',
  `REGION` varchar(3) DEFAULT NULL COMMENT 'Region',
  `ZZONE` varchar(1) DEFAULT NULL COMMENT 'Zone',
  `SUB_ZONE` varchar(2) DEFAULT NULL COMMENT 'SubZone',
  `TRANSZONE` varchar(10) DEFAULT NULL COMMENT 'Transportation zone to or from which the goods are delivered',
  `VKGRP` varchar(3) DEFAULT NULL COMMENT 'Sales group',
  `ZTERM` varchar(4) DEFAULT NULL COMMENT 'Terms of payment key',
  `ACCOUNT_NO` varchar(18) DEFAULT NULL COMMENT 'Bank Account Number',
  `CREDIT_LIMIT` varchar(15) DEFAULT NULL COMMENT 'Credit Limit',
  `PAN_NUMBER` varchar(40) DEFAULT NULL COMMENT 'Permanent Account Number',
  `MIN_STOCK_QTY` varchar(7) DEFAULT NULL COMMENT 'Business partner: International location number part 1 (DI)',
  `COUNTER_POT` varchar(5) DEFAULT NULL COMMENT 'Status',
  `STATUS` varchar(1) DEFAULT NULL,
  `SEARCH_TERM1` varchar(20) DEFAULT NULL COMMENT 'Search Term 1 Business Partner',
  `SEARCH_TERM2` varchar(20) DEFAULT NULL COMMENT 'Search Term 2 Business Partner',
  `VTEXT` varchar(20) DEFAULT NULL COMMENT 'Description',
  `AUFSD` varchar(2) DEFAULT NULL COMMENT 'Central Order block for customer',
  `ADDITIONAL_DATA1` varchar(10) DEFAULT NULL COMMENT 'timestamp',
  `ADDITIONAL_DATA2` varchar(10) DEFAULT NULL COMMENT 'Additional data',
  `ADDITIONAL_DATA3` varchar(10) DEFAULT NULL COMMENT 'Additional data',
  `ADDITIONAL_DATA4` varchar(10) DEFAULT NULL COMMENT 'Additional data',
  `ADDITIONAL_DATA5` varchar(10) DEFAULT NULL COMMENT 'Additional data'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ptblcustomermaster_bkup`
--

CREATE TABLE `ptblcustomermaster_bkup` (
  `MANDT` varchar(3) DEFAULT NULL COMMENT 'Client',
  `KUNNR` varchar(10) DEFAULT NULL COMMENT 'Customer Number',
  `VKORG` varchar(4) DEFAULT NULL COMMENT 'Sales Organization',
  `VWERK` varchar(4) DEFAULT NULL COMMENT 'Delivering Plant (Own or External)',
  `NAME1` varchar(35) DEFAULT NULL COMMENT 'Name 1',
  `NAME2` varchar(35) DEFAULT NULL COMMENT 'Name 2',
  `NAME3` varchar(35) DEFAULT NULL COMMENT 'Name 3',
  `STRAS` varchar(35) DEFAULT NULL COMMENT 'Street and House Number',
  `STREET1` varchar(40) DEFAULT NULL COMMENT 'Street 2',
  `STREET2` varchar(40) DEFAULT NULL COMMENT 'Street 3',
  `STREET3` varchar(40) DEFAULT NULL COMMENT 'Street 4',
  `PSTLZ` varchar(10) DEFAULT NULL COMMENT 'Postal Code',
  `ORT01` varchar(35) DEFAULT NULL COMMENT 'City',
  `ORT02` varchar(35) DEFAULT NULL COMMENT 'District',
  `TELF2` varchar(16) DEFAULT NULL COMMENT 'Second telephone number',
  `SMTP_ADDR` varchar(241) DEFAULT NULL COMMENT 'E-Mail Address',
  `KDGRP` varchar(2) DEFAULT NULL COMMENT 'Customer Group',
  `LZONE` varchar(10) DEFAULT NULL COMMENT 'Transportation zone to or from which the goods are delivered',
  `BUKRS` varchar(4) DEFAULT NULL COMMENT 'Company Code',
  `ALTKN` varchar(10) DEFAULT NULL COMMENT 'Previous Master Record Number',
  `ERDAT` date DEFAULT NULL COMMENT 'Date on which the Record Was Created',
  `AEDAT` date DEFAULT NULL COMMENT 'Date on which the Record Was Created',
  `KONDA` varchar(2) DEFAULT NULL COMMENT 'Changed On',
  `Transzone` varchar(10) DEFAULT NULL COMMENT 'Transportation zone to or from which the goods are delivered',
  `VKGRP` varchar(3) DEFAULT NULL COMMENT 'Sales group',
  `ZTERM` varchar(4) DEFAULT NULL COMMENT 'Customer Price Group',
  `CREDIT_LIMIT` varchar(15) DEFAULT NULL COMMENT 'Terms of payment key',
  `STATUS` varchar(2) DEFAULT NULL COMMENT 'Credit Limit status'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ptblmaterialmaster`
--

CREATE TABLE `ptblmaterialmaster` (
  `MANDT` varchar(3) DEFAULT NULL COMMENT 'Client',
  `MATNR` varchar(40) DEFAULT NULL COMMENT 'Material Number',
  `WERKS` varchar(4) DEFAULT NULL COMMENT 'Plant',
  `VKORG` varchar(4) DEFAULT NULL COMMENT 'Sales Organization',
  `VTWEG` varchar(255) DEFAULT NULL COMMENT 'Distribution Channel',
  `MAKTX` varchar(50) DEFAULT NULL COMMENT 'Material Description',
  `MEINS` varchar(50) DEFAULT NULL COMMENT 'Base Unit of Measure',
  `MTART` varchar(40) DEFAULT NULL COMMENT 'Material type',
  `MATKL` varchar(50) DEFAULT NULL COMMENT 'Material Group',
  `SPART` varchar(50) DEFAULT NULL COMMENT 'Division',
  `MSTDE` date DEFAULT NULL COMMENT 'Date from which the cross-plant material status is valid',
  `BRGEW` double DEFAULT NULL COMMENT 'Gross weight',
  `NTGEW` double DEFAULT NULL COMMENT 'Net weight',
  `VOLUM` double DEFAULT NULL COMMENT 'Volume',
  `GROES` varchar(100) DEFAULT NULL COMMENT 'Size/dimensions',
  `MVGR1` varchar(50) DEFAULT NULL COMMENT 'Material Group 1',
  `MVGR2` varchar(50) DEFAULT NULL COMMENT 'Material Group 2',
  `MVGR3` varchar(50) DEFAULT NULL COMMENT 'Material Group 3',
  `MVGR4` varchar(50) DEFAULT NULL COMMENT 'Material Group 4',
  `MVGR5` varchar(50) DEFAULT NULL COMMENT 'Material Group 5',
  `LVORM` varchar(20) DEFAULT NULL COMMENT 'Flag Material for Deletion at Plant Level',
  `ERDAT` date DEFAULT NULL COMMENT 'Date on which the Record Was Created',
  `AEDAT` date DEFAULT NULL COMMENT 'Last Change Date (DRF)',
  `STATUS` varchar(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pZSDCUST`
--

CREATE TABLE `pZSDCUST` (
  `MANDT` int(11) NOT NULL,
  `PARTY` varchar(20) NOT NULL COMMENT 'Customer Number',
  `SOLD_PARTY` varchar(20) NOT NULL COMMENT 'SUB dealer Number',
  `PARVW` varchar(5) NOT NULL COMMENT 'Partner Function',
  `PARZA` varchar(3) NOT NULL COMMENT 'Partnet Counter',
  `ERDAT` date DEFAULT NULL,
  `PARTY_TYP` varchar(40) DEFAULT NULL COMMENT 'Name',
  `INCO1` varchar(5) DEFAULT NULL COMMENT 'Incoterms (Part 1)',
  `LZONE` varchar(10) DEFAULT NULL,
  `Status` varchar(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rds_master`
--

CREATE TABLE `rds_master` (
  `rds_code` varchar(20) NOT NULL,
  `dns_rds_code` varchar(20) DEFAULT NULL,
  `rds_name` varchar(255) NOT NULL,
  `emp_code` varchar(20) NOT NULL,
  `rds_type` varchar(10) NOT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `redeeme_details`
--

CREATE TABLE `redeeme_details` (
  `sn` int(11) NOT NULL,
  `scheme_expiry_date` date NOT NULL,
  `points` double NOT NULL,
  `award` varchar(255) NOT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `remote_user`
--

CREATE TABLE `remote_user` (
  `ID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `age` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CONNECTION='mysql://acedns_dnsprod:dnsprod1234#@salesmpower.acedns.in:3306/acedns_PALSONS/local_user';

-- --------------------------------------------------------

--
-- Table structure for table `ROE_branch_list`
--

CREATE TABLE `ROE_branch_list` (
  `nb_id` varchar(50) NOT NULL,
  `nb_branch_code` varchar(20) DEFAULT NULL,
  `nb_dns_branch_code` varchar(20) DEFAULT NULL,
  `nb_branch_name` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `route_customer_plan`
--

CREATE TABLE `route_customer_plan` (
  `route_plan_trans_id` varchar(30) NOT NULL,
  `route_code` varchar(255) NOT NULL,
  `visit_date` date NOT NULL,
  `customer_code` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `route_refference`
--

CREATE TABLE `route_refference` (
  `sl_no` int(10) NOT NULL,
  `route_replaced` text NOT NULL,
  `route_replaced_by` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sale_rate`
--

CREATE TABLE `sale_rate` (
  `branch_code` varchar(20) DEFAULT '',
  `product_code` varchar(20) NOT NULL,
  `sale_rate_code` varchar(20) NOT NULL,
  `sale_rate` double NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `SAP_erp_dump_mapping`
--

CREATE TABLE `SAP_erp_dump_mapping` (
  `sl_no` int(3) NOT NULL,
  `ERP_code` varchar(50) NOT NULL,
  `SAP_code` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `SAP_erp_product_mapping`
--

CREATE TABLE `SAP_erp_product_mapping` (
  `sl_no` int(2) NOT NULL,
  `ERP_code` varchar(50) NOT NULL,
  `SAP_code` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `selected_menu_for_user`
--

CREATE TABLE `selected_menu_for_user` (
  `smid` int(11) NOT NULL,
  `user_id` varchar(30) DEFAULT NULL,
  `menu_id` varchar(30) DEFAULT NULL,
  `edit_access` enum('ACTIVE','INACTIVE') DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `self_appraisal`
--

CREATE TABLE `self_appraisal` (
  `sl_no` bigint(15) NOT NULL,
  `emp_code` varchar(50) NOT NULL,
  `customer_code` varchar(50) NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `HQ` varchar(255) DEFAULT NULL,
  `route_code` varchar(50) DEFAULT NULL,
  `product_group_code` varchar(50) DEFAULT NULL,
  `product_sub_group_code` varchar(50) DEFAULT NULL,
  `product_brand_code` varchar(50) DEFAULT NULL,
  `product_code` varchar(50) DEFAULT NULL,
  `jan_1_target` double DEFAULT NULL,
  `jan_1_achievement` double DEFAULT NULL,
  `jan_2_target` double DEFAULT NULL,
  `jan_2_achievement` double DEFAULT NULL,
  `jan_3_target` double DEFAULT NULL,
  `jan_3_achievement` double DEFAULT NULL,
  `jan_4_target` double DEFAULT NULL,
  `jan_4_achievement` double DEFAULT NULL,
  `jan_5_target` double DEFAULT NULL,
  `jan_5_achievement` double DEFAULT NULL,
  `jan_6_target` double DEFAULT NULL,
  `jan_6_achievement` double DEFAULT NULL,
  `jan_7_target` double DEFAULT NULL,
  `jan_7_achievement` double DEFAULT NULL,
  `jan_8_target` double DEFAULT NULL,
  `jan_8_achievement` double DEFAULT NULL,
  `jan_9_target` double DEFAULT NULL,
  `jan_9_achievement` double DEFAULT NULL,
  `jan_10_target` double DEFAULT NULL,
  `jan_10_achievement` double DEFAULT NULL,
  `jan_11_target` double DEFAULT NULL,
  `jan_11_achievement` double DEFAULT NULL,
  `jan_12_target` double DEFAULT NULL,
  `jan_12_achievement` double DEFAULT NULL,
  `jan_13_target` double DEFAULT NULL,
  `jan_13_achievement` double DEFAULT NULL,
  `jan_14_target` double DEFAULT NULL,
  `jan_14_achievement` double DEFAULT NULL,
  `jan_15_target` double DEFAULT NULL,
  `jan_15_achievement` double DEFAULT NULL,
  `jan_16_target` double DEFAULT NULL,
  `jan_16_achievement` double DEFAULT NULL,
  `jan_17_target` double DEFAULT NULL,
  `jan_17_achievement` double DEFAULT NULL,
  `jan_18_target` double DEFAULT NULL,
  `jan_18_achievement` double DEFAULT NULL,
  `jan_19_target` double DEFAULT NULL,
  `jan_19_achievement` double DEFAULT NULL,
  `jan_20_target` double DEFAULT NULL,
  `jan_20_achievement` double DEFAULT NULL,
  `jan_21_target` double DEFAULT NULL,
  `jan_21_achievement` double DEFAULT NULL,
  `jan_22_target` double DEFAULT NULL,
  `jan_22_achievement` double DEFAULT NULL,
  `jan_23_target` double DEFAULT NULL,
  `jan_23_achievement` double DEFAULT NULL,
  `jan_24_target` double DEFAULT NULL,
  `jan_24_achievement` double DEFAULT NULL,
  `jan_25_target` double DEFAULT NULL,
  `jan_25_achievement` double DEFAULT NULL,
  `jan_26_target` double DEFAULT NULL,
  `jan_26_achievement` double DEFAULT NULL,
  `jan_27_target` double DEFAULT NULL,
  `jan_27_achievement` double DEFAULT NULL,
  `jan_28_target` double DEFAULT NULL,
  `jan_28_achievement` double DEFAULT NULL,
  `jan_29_target` double DEFAULT NULL,
  `jan_29_achievement` double DEFAULT NULL,
  `jan_30_target` double DEFAULT NULL,
  `jan_30_achievement` double DEFAULT NULL,
  `jan_31_target` double DEFAULT NULL,
  `jan_31_achievement` double DEFAULT NULL,
  `feb_1_target` double DEFAULT NULL,
  `feb_1_achievement` double DEFAULT NULL,
  `feb_2_target` double DEFAULT NULL,
  `feb_2_achievement` double DEFAULT NULL,
  `feb_3_target` double DEFAULT NULL,
  `feb_3_achievement` double DEFAULT NULL,
  `feb_4_target` double DEFAULT NULL,
  `feb_4_achievement` double DEFAULT NULL,
  `feb_5_target` double DEFAULT NULL,
  `feb_5_achievement` double DEFAULT NULL,
  `feb_6_target` double DEFAULT NULL,
  `feb_6_achievement` double DEFAULT NULL,
  `feb_7_target` double DEFAULT NULL,
  `feb_7_achievement` double DEFAULT NULL,
  `feb_8_target` double DEFAULT NULL,
  `feb_8_achievement` double DEFAULT NULL,
  `feb_9_target` double DEFAULT NULL,
  `feb_9_achievement` double DEFAULT NULL,
  `feb_10_target` double DEFAULT NULL,
  `feb_10_achievement` double DEFAULT NULL,
  `feb_11_target` double DEFAULT NULL,
  `feb_11_achievement` double DEFAULT NULL,
  `feb_12_target` double DEFAULT NULL,
  `feb_12_achievement` double DEFAULT NULL,
  `feb_13_target` double DEFAULT NULL,
  `feb_13_achievement` double DEFAULT NULL,
  `feb_14_target` double DEFAULT NULL,
  `feb_14_achievement` double DEFAULT NULL,
  `feb_15_target` double DEFAULT NULL,
  `feb_15_achievement` double DEFAULT NULL,
  `feb_16_target` double DEFAULT NULL,
  `feb_16_achievement` double DEFAULT NULL,
  `feb_17_target` double DEFAULT NULL,
  `feb_17_achievement` double DEFAULT NULL,
  `feb_18_target` double DEFAULT NULL,
  `feb_18_achievement` double DEFAULT NULL,
  `feb_19_target` double DEFAULT NULL,
  `feb_19_achievement` double DEFAULT NULL,
  `feb_20_target` double DEFAULT NULL,
  `feb_20_achievement` double DEFAULT NULL,
  `feb_21_target` double DEFAULT NULL,
  `feb_21_achievement` double DEFAULT NULL,
  `feb_22_target` double DEFAULT NULL,
  `feb_22_achievement` double DEFAULT NULL,
  `feb_23_target` double DEFAULT NULL,
  `feb_23_achievement` double DEFAULT NULL,
  `feb_24_target` double DEFAULT NULL,
  `feb_24_achievement` double DEFAULT NULL,
  `feb_25_target` double DEFAULT NULL,
  `feb_25_achievement` double DEFAULT NULL,
  `feb_26_target` double DEFAULT NULL,
  `feb_26_achievement` double DEFAULT NULL,
  `feb_27_target` double DEFAULT NULL,
  `feb_27_achievement` double DEFAULT NULL,
  `feb_28_target` double DEFAULT NULL,
  `feb_28_achievement` double DEFAULT NULL,
  `feb_29_target` double DEFAULT NULL,
  `feb_29_achievement` double DEFAULT NULL,
  `mar_1_target` double DEFAULT NULL,
  `mar_1_achievement` double DEFAULT NULL,
  `mar_2_target` double DEFAULT NULL,
  `mar_2_achievement` double DEFAULT NULL,
  `mar_3_target` double DEFAULT NULL,
  `mar_3_achievement` double DEFAULT NULL,
  `mar_4_target` double DEFAULT NULL,
  `mar_4_achievement` double DEFAULT NULL,
  `mar_5_target` double DEFAULT NULL,
  `mar_5_achievement` double DEFAULT NULL,
  `mar_6_target` double DEFAULT NULL,
  `mar_6_achievement` double DEFAULT NULL,
  `mar_7_target` double DEFAULT NULL,
  `mar_7_achievement` double DEFAULT NULL,
  `mar_8_target` double DEFAULT NULL,
  `mar_8_achievement` double DEFAULT NULL,
  `mar_9_target` double DEFAULT NULL,
  `mar_9_achievement` double DEFAULT NULL,
  `mar_10_target` double DEFAULT NULL,
  `mar_10_achievement` double DEFAULT NULL,
  `mar_11_target` double DEFAULT NULL,
  `mar_11_achievement` double DEFAULT NULL,
  `mar_12_target` double DEFAULT NULL,
  `mar_12_achievement` double DEFAULT NULL,
  `mar_13_target` double DEFAULT NULL,
  `mar_13_achievement` double DEFAULT NULL,
  `mar_14_target` double DEFAULT NULL,
  `mar_14_achievement` double DEFAULT NULL,
  `mar_15_target` double DEFAULT NULL,
  `mar_15_achievement` double DEFAULT NULL,
  `mar_16_target` double DEFAULT NULL,
  `mar_16_achievement` double DEFAULT NULL,
  `mar_17_target` double DEFAULT NULL,
  `mar_17_achievement` double DEFAULT NULL,
  `mar_18_target` double DEFAULT NULL,
  `mar_18_achievement` double DEFAULT NULL,
  `mar_19_target` double DEFAULT NULL,
  `mar_19_achievement` double DEFAULT NULL,
  `mar_20_target` double DEFAULT NULL,
  `mar_20_achievement` double DEFAULT NULL,
  `mar_21_target` double DEFAULT NULL,
  `mar_21_achievement` double DEFAULT NULL,
  `mar_22_target` double DEFAULT NULL,
  `mar_22_achievement` double DEFAULT NULL,
  `mar_23_target` double DEFAULT NULL,
  `mar_23_achievement` double DEFAULT NULL,
  `mar_24_target` double DEFAULT NULL,
  `mar_24_achievement` double DEFAULT NULL,
  `mar_25_target` double DEFAULT NULL,
  `mar_25_achievement` double DEFAULT NULL,
  `mar_26_target` double DEFAULT NULL,
  `mar_26_achievement` double DEFAULT NULL,
  `mar_27_target` double DEFAULT NULL,
  `mar_27_achievement` double DEFAULT NULL,
  `mar_28_target` double DEFAULT NULL,
  `mar_28_achievement` double DEFAULT NULL,
  `mar_29_target` double DEFAULT NULL,
  `mar_29_achievement` double DEFAULT NULL,
  `mar_30_target` double DEFAULT NULL,
  `mar_30_achievement` double DEFAULT NULL,
  `mar_31_target` double DEFAULT NULL,
  `mar_31_achievement` double DEFAULT NULL,
  `apr_1_target` double DEFAULT NULL,
  `apr_1_achievement` double DEFAULT NULL,
  `apr_2_target` double DEFAULT NULL,
  `apr_2_achievement` double DEFAULT NULL,
  `apr_3_target` double DEFAULT NULL,
  `apr_3_achievement` double DEFAULT NULL,
  `apr_4_target` double DEFAULT NULL,
  `apr_4_achievement` double DEFAULT NULL,
  `apr_5_target` double DEFAULT NULL,
  `apr_5_achievement` double DEFAULT NULL,
  `apr_6_target` double DEFAULT NULL,
  `apr_6_achievement` double DEFAULT NULL,
  `apr_7_target` double DEFAULT NULL,
  `apr_7_achievement` double DEFAULT NULL,
  `apr_8_target` double DEFAULT NULL,
  `apr_8_achievement` double DEFAULT NULL,
  `apr_9_target` double DEFAULT NULL,
  `apr_9_achievement` double DEFAULT NULL,
  `apr_10_target` double DEFAULT NULL,
  `apr_10_achievement` double DEFAULT NULL,
  `apr_11_target` double DEFAULT NULL,
  `apr_11_achievement` double DEFAULT NULL,
  `apr_12_target` double DEFAULT NULL,
  `apr_12_achievement` double DEFAULT NULL,
  `apr_13_target` double DEFAULT NULL,
  `apr_13_achievement` double DEFAULT NULL,
  `apr_14_target` double DEFAULT NULL,
  `apr_14_achievement` double DEFAULT NULL,
  `apr_15_target` double DEFAULT NULL,
  `apr_15_achievement` double DEFAULT NULL,
  `apr_16_target` double DEFAULT NULL,
  `apr_16_achievement` double DEFAULT NULL,
  `apr_17_target` double DEFAULT NULL,
  `apr_17_achievement` double DEFAULT NULL,
  `apr_18_target` double DEFAULT NULL,
  `apr_18_achievement` double DEFAULT NULL,
  `apr_19_target` double DEFAULT NULL,
  `apr_19_achievement` double DEFAULT NULL,
  `apr_20_target` double DEFAULT NULL,
  `apr_20_achievement` double DEFAULT NULL,
  `apr_21_target` double DEFAULT NULL,
  `apr_21_achievement` double DEFAULT NULL,
  `apr_22_target` double DEFAULT NULL,
  `apr_22_achievement` double DEFAULT NULL,
  `apr_23_target` double DEFAULT NULL,
  `apr_23_achievement` double DEFAULT NULL,
  `apr_24_target` double DEFAULT NULL,
  `apr_24_achievement` double DEFAULT NULL,
  `apr_25_target` double DEFAULT NULL,
  `apr_25_achievement` double DEFAULT NULL,
  `apr_26_target` double DEFAULT NULL,
  `apr_26_achievement` double DEFAULT NULL,
  `apr_27_target` double DEFAULT NULL,
  `apr_27_achievement` double DEFAULT NULL,
  `apr_28_target` double DEFAULT NULL,
  `apr_28_achievement` double DEFAULT NULL,
  `apr_29_target` double DEFAULT NULL,
  `apr_29_achievement` double DEFAULT NULL,
  `apr_30_target` double DEFAULT NULL,
  `apr_30_achievement` double DEFAULT NULL,
  `may_1_target` double DEFAULT NULL,
  `may_1_achievement` double DEFAULT NULL,
  `may_2_target` double DEFAULT NULL,
  `may_2_achievement` double DEFAULT NULL,
  `may_3_target` double DEFAULT NULL,
  `may_3_achievement` double DEFAULT NULL,
  `may_4_target` double DEFAULT NULL,
  `may_4_achievement` double DEFAULT NULL,
  `may_5_target` double DEFAULT NULL,
  `may_5_achievement` double DEFAULT NULL,
  `may_6_target` double DEFAULT NULL,
  `may_6_achievement` double DEFAULT NULL,
  `may_7_target` double DEFAULT NULL,
  `may_7_achievement` double DEFAULT NULL,
  `may_8_target` double DEFAULT NULL,
  `may_8_achievement` double DEFAULT NULL,
  `may_9_target` double DEFAULT NULL,
  `may_9_achievement` double DEFAULT NULL,
  `may_10_target` double DEFAULT NULL,
  `may_10_achievement` double DEFAULT NULL,
  `may_11_target` double DEFAULT NULL,
  `may_11_achievement` double DEFAULT NULL,
  `may_12_target` double DEFAULT NULL,
  `may_12_achievement` double DEFAULT NULL,
  `may_13_target` double DEFAULT NULL,
  `may_13_achievement` double DEFAULT NULL,
  `may_14_target` double DEFAULT NULL,
  `may_14_achievement` double DEFAULT NULL,
  `may_15_target` double DEFAULT NULL,
  `may_15_achievement` double DEFAULT NULL,
  `may_16_target` double DEFAULT NULL,
  `may_16_achievement` double DEFAULT NULL,
  `may_17_target` double DEFAULT NULL,
  `may_17_achievement` double DEFAULT NULL,
  `may_18_target` double DEFAULT NULL,
  `may_18_achievement` double DEFAULT NULL,
  `may_19_target` double DEFAULT NULL,
  `may_19_achievement` double DEFAULT NULL,
  `may_20_target` double DEFAULT NULL,
  `may_20_achievement` double DEFAULT NULL,
  `may_21_target` double DEFAULT NULL,
  `may_21_achievement` double DEFAULT NULL,
  `may_22_target` double DEFAULT NULL,
  `may_22_achievement` double DEFAULT NULL,
  `may_23_target` double DEFAULT NULL,
  `may_23_achievement` double DEFAULT NULL,
  `may_24_target` double DEFAULT NULL,
  `may_24_achievement` double DEFAULT NULL,
  `may_25_target` double DEFAULT NULL,
  `may_25_achievement` double DEFAULT NULL,
  `may_26_target` double DEFAULT NULL,
  `may_26_achievement` double DEFAULT NULL,
  `may_27_target` double DEFAULT NULL,
  `may_27_achievement` double DEFAULT NULL,
  `may_28_target` double DEFAULT NULL,
  `may_28_achievement` double DEFAULT NULL,
  `may_29_target` double DEFAULT NULL,
  `may_29_achievement` double DEFAULT NULL,
  `may_30_target` double DEFAULT NULL,
  `may_30_achievement` double DEFAULT NULL,
  `may_31_target` double DEFAULT NULL,
  `may_31_achievement` double DEFAULT NULL,
  `jun_1_target` double DEFAULT NULL,
  `jun_1_achievement` double DEFAULT NULL,
  `jun_2_target` double DEFAULT NULL,
  `jun_2_achievement` double DEFAULT NULL,
  `jun_3_target` double DEFAULT NULL,
  `jun_3_achievement` double DEFAULT NULL,
  `jun_4_target` double DEFAULT NULL,
  `jun_4_achievement` double DEFAULT NULL,
  `jun_5_target` double DEFAULT NULL,
  `jun_5_achievement` double DEFAULT NULL,
  `jun_6_target` double DEFAULT NULL,
  `jun_6_achievement` double DEFAULT NULL,
  `jun_7_target` double DEFAULT NULL,
  `jun_7_achievement` double DEFAULT NULL,
  `jun_8_target` double DEFAULT NULL,
  `jun_8_achievement` double DEFAULT NULL,
  `jun_9_target` double DEFAULT NULL,
  `jun_9_achievement` double DEFAULT NULL,
  `jun_10_target` double DEFAULT NULL,
  `jun_10_achievement` double DEFAULT NULL,
  `jun_11_target` double DEFAULT NULL,
  `jun_11_achievement` double DEFAULT NULL,
  `jun_12_target` double DEFAULT NULL,
  `jun_12_achievement` double DEFAULT NULL,
  `jun_13_target` double DEFAULT NULL,
  `jun_13_achievement` double DEFAULT NULL,
  `jun_14_target` double DEFAULT NULL,
  `jun_14_achievement` double DEFAULT NULL,
  `jun_15_target` double DEFAULT NULL,
  `jun_15_achievement` double DEFAULT NULL,
  `jun_16_target` double DEFAULT NULL,
  `jun_16_achievement` double DEFAULT NULL,
  `jun_17_target` double DEFAULT NULL,
  `jun_17_achievement` double DEFAULT NULL,
  `jun_18_target` double DEFAULT NULL,
  `jun_18_achievement` double DEFAULT NULL,
  `jun_19_target` double DEFAULT NULL,
  `jun_19_achievement` double DEFAULT NULL,
  `jun_20_target` double DEFAULT NULL,
  `jun_20_achievement` double DEFAULT NULL,
  `jun_21_target` double DEFAULT NULL,
  `jun_21_achievement` double DEFAULT NULL,
  `jun_22_target` double DEFAULT NULL,
  `jun_22_achievement` double DEFAULT NULL,
  `jun_23_target` double DEFAULT NULL,
  `jun_23_achievement` double DEFAULT NULL,
  `jun_24_target` double DEFAULT NULL,
  `jun_24_achievement` double DEFAULT NULL,
  `jun_25_target` double DEFAULT NULL,
  `jun_25_achievement` double DEFAULT NULL,
  `jun_26_target` double DEFAULT NULL,
  `jun_26_achievement` double DEFAULT NULL,
  `jun_27_target` double DEFAULT NULL,
  `jun_27_achievement` double DEFAULT NULL,
  `jun_28_target` double DEFAULT NULL,
  `jun_28_achievement` double DEFAULT NULL,
  `jun_29_target` double DEFAULT NULL,
  `jun_29_achievement` double DEFAULT NULL,
  `jun_30_target` double DEFAULT NULL,
  `jun_30_achievement` double DEFAULT NULL,
  `jul_1_target` double DEFAULT NULL,
  `jul_1_achievement` double DEFAULT NULL,
  `jul_2_target` double DEFAULT NULL,
  `jul_2_achievement` double DEFAULT NULL,
  `jul_3_target` double DEFAULT NULL,
  `jul_3_achievement` double DEFAULT NULL,
  `jul_4_target` double DEFAULT NULL,
  `jul_4_achievement` double DEFAULT NULL,
  `jul_5_target` double DEFAULT NULL,
  `jul_5_achievement` double DEFAULT NULL,
  `jul_6_target` double DEFAULT NULL,
  `jul_6_achievement` double DEFAULT NULL,
  `jul_7_target` double DEFAULT NULL,
  `jul_7_achievement` double DEFAULT NULL,
  `jul_8_target` double DEFAULT NULL,
  `jul_8_achievement` double DEFAULT NULL,
  `jul_9_target` double DEFAULT NULL,
  `jul_9_achievement` double DEFAULT NULL,
  `jul_10_target` double DEFAULT NULL,
  `jul_10_achievement` double DEFAULT NULL,
  `jul_11_target` double DEFAULT NULL,
  `jul_11_achievement` double DEFAULT NULL,
  `jul_12_target` double DEFAULT NULL,
  `jul_12_achievement` double DEFAULT NULL,
  `jul_13_target` double DEFAULT NULL,
  `jul_13_achievement` double DEFAULT NULL,
  `jul_14_target` double DEFAULT NULL,
  `jul_14_achievement` double DEFAULT NULL,
  `jul_15_target` double DEFAULT NULL,
  `jul_15_achievement` double DEFAULT NULL,
  `jul_16_target` double DEFAULT NULL,
  `jul_16_achievement` double DEFAULT NULL,
  `jul_17_target` double DEFAULT NULL,
  `jul_17_achievement` double DEFAULT NULL,
  `jul_18_target` double DEFAULT NULL,
  `jul_18_achievement` double DEFAULT NULL,
  `jul_19_target` double DEFAULT NULL,
  `jul_19_achievement` double DEFAULT NULL,
  `jul_20_target` double DEFAULT NULL,
  `jul_20_achievement` double DEFAULT NULL,
  `jul_21_target` double DEFAULT NULL,
  `jul_21_achievement` double DEFAULT NULL,
  `jul_22_target` double DEFAULT NULL,
  `jul_22_achievement` double DEFAULT NULL,
  `jul_23_target` double DEFAULT NULL,
  `jul_23_achievement` double DEFAULT NULL,
  `jul_24_target` double DEFAULT NULL,
  `jul_24_achievement` double DEFAULT NULL,
  `jul_25_target` double DEFAULT NULL,
  `jul_25_achievement` double DEFAULT NULL,
  `jul_26_target` double DEFAULT NULL,
  `jul_26_achievement` double DEFAULT NULL,
  `jul_27_target` double DEFAULT NULL,
  `jul_27_achievement` double DEFAULT NULL,
  `jul_28_target` double DEFAULT NULL,
  `jul_28_achievement` double DEFAULT NULL,
  `jul_29_target` double DEFAULT NULL,
  `jul_29_achievement` double DEFAULT NULL,
  `jul_30_target` double DEFAULT NULL,
  `jul_30_achievement` double DEFAULT NULL,
  `jul_31_target` double DEFAULT NULL,
  `jul_31_achievement` double DEFAULT NULL,
  `aug_1_target` double DEFAULT NULL,
  `aug_1_achievement` double DEFAULT NULL,
  `aug_2_target` double DEFAULT NULL,
  `aug_2_achievement` double DEFAULT NULL,
  `aug_3_target` double DEFAULT NULL,
  `aug_3_achievement` double DEFAULT NULL,
  `aug_4_target` double DEFAULT NULL,
  `aug_4_achievement` double DEFAULT NULL,
  `aug_5_target` double DEFAULT NULL,
  `aug_5_achievement` double DEFAULT NULL,
  `aug_6_target` double DEFAULT NULL,
  `aug_6_achievement` double DEFAULT NULL,
  `aug_7_target` double DEFAULT NULL,
  `aug_7_achievement` double DEFAULT NULL,
  `aug_8_target` double DEFAULT NULL,
  `aug_8_achievement` double DEFAULT NULL,
  `aug_9_target` double DEFAULT NULL,
  `aug_9_achievement` double DEFAULT NULL,
  `aug_10_target` double DEFAULT NULL,
  `aug_10_achievement` double DEFAULT NULL,
  `aug_11_target` double DEFAULT NULL,
  `aug_11_achievement` double DEFAULT NULL,
  `aug_12_target` double DEFAULT NULL,
  `aug_12_achievement` double DEFAULT NULL,
  `aug_13_target` double DEFAULT NULL,
  `aug_13_achievement` double DEFAULT NULL,
  `aug_14_target` double DEFAULT NULL,
  `aug_14_achievement` double DEFAULT NULL,
  `aug_15_target` double DEFAULT NULL,
  `aug_15_achievement` double DEFAULT NULL,
  `aug_16_target` double DEFAULT NULL,
  `aug_16_achievement` double DEFAULT NULL,
  `aug_17_target` double DEFAULT NULL,
  `aug_17_achievement` double DEFAULT NULL,
  `aug_18_target` double DEFAULT NULL,
  `aug_18_achievement` double DEFAULT NULL,
  `aug_19_target` double DEFAULT NULL,
  `aug_19_achievement` double DEFAULT NULL,
  `aug_20_target` double DEFAULT NULL,
  `aug_20_achievement` double DEFAULT NULL,
  `aug_21_target` double DEFAULT NULL,
  `aug_21_achievement` double DEFAULT NULL,
  `aug_22_target` double DEFAULT NULL,
  `aug_22_achievement` double DEFAULT NULL,
  `aug_23_target` double DEFAULT NULL,
  `aug_23_achievement` double DEFAULT NULL,
  `aug_24_target` double DEFAULT NULL,
  `aug_24_achievement` double DEFAULT NULL,
  `aug_25_target` double DEFAULT NULL,
  `aug_25_achievement` double DEFAULT NULL,
  `aug_26_target` double DEFAULT NULL,
  `aug_26_achievement` double DEFAULT NULL,
  `aug_27_target` double DEFAULT NULL,
  `aug_27_achievement` double DEFAULT NULL,
  `aug_28_target` double DEFAULT NULL,
  `aug_28_achievement` double DEFAULT NULL,
  `aug_29_target` double DEFAULT NULL,
  `aug_29_achievement` double DEFAULT NULL,
  `aug_30_target` double DEFAULT NULL,
  `aug_30_achievement` double DEFAULT NULL,
  `aug_31_target` double DEFAULT NULL,
  `aug_31_achievement` double DEFAULT NULL,
  `sep_1_target` double DEFAULT NULL,
  `sep_1_achievement` double DEFAULT NULL,
  `sep_2_target` double DEFAULT NULL,
  `sep_2_achievement` double DEFAULT NULL,
  `sep_3_target` double DEFAULT NULL,
  `sep_3_achievement` double DEFAULT NULL,
  `sep_4_target` double DEFAULT NULL,
  `sep_4_achievement` double DEFAULT NULL,
  `sep_5_target` double DEFAULT NULL,
  `sep_5_achievement` double DEFAULT NULL,
  `sep_6_target` double DEFAULT NULL,
  `sep_6_achievement` double DEFAULT NULL,
  `sep_7_target` double DEFAULT NULL,
  `sep_7_achievement` double DEFAULT NULL,
  `sep_8_target` double DEFAULT NULL,
  `sep_8_achievement` double DEFAULT NULL,
  `sep_9_target` double DEFAULT NULL,
  `sep_9_achievement` double DEFAULT NULL,
  `sep_10_target` double DEFAULT NULL,
  `sep_10_achievement` double DEFAULT NULL,
  `sep_11_target` double DEFAULT NULL,
  `sep_11_achievement` double DEFAULT NULL,
  `sep_12_target` double DEFAULT NULL,
  `sep_12_achievement` double DEFAULT NULL,
  `sep_13_target` double DEFAULT NULL,
  `sep_13_achievement` double DEFAULT NULL,
  `sep_14_target` double DEFAULT NULL,
  `sep_14_achievement` double DEFAULT NULL,
  `sep_15_target` double DEFAULT NULL,
  `sep_15_achievement` double DEFAULT NULL,
  `sep_16_target` double DEFAULT NULL,
  `sep_16_achievement` double DEFAULT NULL,
  `sep_17_target` double DEFAULT NULL,
  `sep_17_achievement` double DEFAULT NULL,
  `sep_18_target` double DEFAULT NULL,
  `sep_18_achievement` double DEFAULT NULL,
  `sep_19_target` double DEFAULT NULL,
  `sep_19_achievement` double DEFAULT NULL,
  `sep_20_target` double DEFAULT NULL,
  `sep_20_achievement` double DEFAULT NULL,
  `sep_21_target` double DEFAULT NULL,
  `sep_21_achievement` double DEFAULT NULL,
  `sep_22_target` double DEFAULT NULL,
  `sep_22_achievement` double DEFAULT NULL,
  `sep_23_target` double DEFAULT NULL,
  `sep_23_achievement` double DEFAULT NULL,
  `sep_24_target` double DEFAULT NULL,
  `sep_24_achievement` double DEFAULT NULL,
  `sep_25_target` double DEFAULT NULL,
  `sep_25_achievement` double DEFAULT NULL,
  `sep_26_target` double DEFAULT NULL,
  `sep_26_achievement` double DEFAULT NULL,
  `sep_27_target` double DEFAULT NULL,
  `sep_27_achievement` double DEFAULT NULL,
  `sep_28_target` double DEFAULT NULL,
  `sep_28_achievement` double DEFAULT NULL,
  `sep_29_target` double DEFAULT NULL,
  `sep_29_achievement` double DEFAULT NULL,
  `sep_30_target` double DEFAULT NULL,
  `sep_30_achievement` double DEFAULT NULL,
  `oct_1_target` double DEFAULT NULL,
  `oct_1_achievement` double DEFAULT NULL,
  `oct_2_target` double DEFAULT NULL,
  `oct_2_achievement` double DEFAULT NULL,
  `oct_3_target` double DEFAULT NULL,
  `oct_3_achievement` double DEFAULT NULL,
  `oct_4_target` double DEFAULT NULL,
  `oct_4_achievement` double DEFAULT NULL,
  `oct_5_target` double DEFAULT NULL,
  `oct_5_achievement` double DEFAULT NULL,
  `oct_6_target` double DEFAULT NULL,
  `oct_6_achievement` double DEFAULT NULL,
  `oct_7_target` double DEFAULT NULL,
  `oct_7_achievement` double DEFAULT NULL,
  `oct_8_target` double DEFAULT NULL,
  `oct_8_achievement` double DEFAULT NULL,
  `oct_9_target` double DEFAULT NULL,
  `oct_9_achievement` double DEFAULT NULL,
  `oct_10_target` double DEFAULT NULL,
  `oct_10_achievement` double DEFAULT NULL,
  `oct_11_target` double DEFAULT NULL,
  `oct_11_achievement` double DEFAULT NULL,
  `oct_12_target` double DEFAULT NULL,
  `oct_12_achievement` double DEFAULT NULL,
  `oct_13_target` double DEFAULT NULL,
  `oct_13_achievement` double DEFAULT NULL,
  `oct_14_target` double DEFAULT NULL,
  `oct_14_achievement` double DEFAULT NULL,
  `oct_15_target` double DEFAULT NULL,
  `oct_15_achievement` double DEFAULT NULL,
  `oct_16_target` double DEFAULT NULL,
  `oct_16_achievement` double DEFAULT NULL,
  `oct_17_target` double DEFAULT NULL,
  `oct_17_achievement` double DEFAULT NULL,
  `oct_18_target` double DEFAULT NULL,
  `oct_18_achievement` double DEFAULT NULL,
  `oct_19_target` double DEFAULT NULL,
  `oct_19_achievement` double DEFAULT NULL,
  `oct_20_target` double DEFAULT NULL,
  `oct_20_achievement` double DEFAULT NULL,
  `oct_21_target` double DEFAULT NULL,
  `oct_21_achievement` double DEFAULT NULL,
  `oct_22_target` double DEFAULT NULL,
  `oct_22_achievement` double DEFAULT NULL,
  `oct_23_target` double DEFAULT NULL,
  `oct_23_achievement` double DEFAULT NULL,
  `oct_24_target` double DEFAULT NULL,
  `oct_24_achievement` double DEFAULT NULL,
  `oct_25_target` double DEFAULT NULL,
  `oct_25_achievement` double DEFAULT NULL,
  `oct_26_target` double DEFAULT NULL,
  `oct_26_achievement` double DEFAULT NULL,
  `oct_27_target` double DEFAULT NULL,
  `oct_27_achievement` double DEFAULT NULL,
  `oct_28_target` double DEFAULT NULL,
  `oct_28_achievement` double DEFAULT NULL,
  `oct_29_target` double DEFAULT NULL,
  `oct_29_achievement` double DEFAULT NULL,
  `oct_30_target` double DEFAULT NULL,
  `oct_30_achievement` double DEFAULT NULL,
  `oct_31_target` double DEFAULT NULL,
  `oct_31_achievement` double DEFAULT NULL,
  `nov_1_target` double DEFAULT NULL,
  `nov_1_achievement` double DEFAULT NULL,
  `nov_2_target` double DEFAULT NULL,
  `nov_2_achievement` double DEFAULT NULL,
  `nov_3_target` double DEFAULT NULL,
  `nov_3_achievement` double DEFAULT NULL,
  `nov_4_target` double DEFAULT NULL,
  `nov_4_achievement` double DEFAULT NULL,
  `nov_5_target` double DEFAULT NULL,
  `nov_5_achievement` double DEFAULT NULL,
  `nov_6_target` double DEFAULT NULL,
  `nov_6_achievement` double DEFAULT NULL,
  `nov_7_target` double DEFAULT NULL,
  `nov_7_achievement` double DEFAULT NULL,
  `nov_8_target` double DEFAULT NULL,
  `nov_8_achievement` double DEFAULT NULL,
  `nov_9_target` double DEFAULT NULL,
  `nov_9_achievement` double DEFAULT NULL,
  `nov_10_target` double DEFAULT NULL,
  `nov_10_achievement` double DEFAULT NULL,
  `nov_11_target` double DEFAULT NULL,
  `nov_11_achievement` double DEFAULT NULL,
  `nov_12_target` double DEFAULT NULL,
  `nov_12_achievement` double DEFAULT NULL,
  `nov_13_target` double DEFAULT NULL,
  `nov_13_achievement` double DEFAULT NULL,
  `nov_14_target` double DEFAULT NULL,
  `nov_14_achievement` double DEFAULT NULL,
  `nov_15_target` double DEFAULT NULL,
  `nov_15_achievement` double DEFAULT NULL,
  `nov_16_target` double DEFAULT NULL,
  `nov_16_achievement` double DEFAULT NULL,
  `nov_17_target` double DEFAULT NULL,
  `nov_17_achievement` double DEFAULT NULL,
  `nov_18_target` double DEFAULT NULL,
  `nov_18_achievement` double DEFAULT NULL,
  `nov_19_target` double DEFAULT NULL,
  `nov_19_achievement` double DEFAULT NULL,
  `nov_20_target` double DEFAULT NULL,
  `nov_20_achievement` double DEFAULT NULL,
  `nov_21_target` double DEFAULT NULL,
  `nov_21_achievement` double DEFAULT NULL,
  `nov_22_target` double DEFAULT NULL,
  `nov_22_achievement` double DEFAULT NULL,
  `nov_23_target` double DEFAULT NULL,
  `nov_23_achievement` double DEFAULT NULL,
  `nov_24_target` double DEFAULT NULL,
  `nov_24_achievement` double DEFAULT NULL,
  `nov_25_target` double DEFAULT NULL,
  `nov_25_achievement` double DEFAULT NULL,
  `nov_26_target` double DEFAULT NULL,
  `nov_26_achievement` double DEFAULT NULL,
  `nov_27_target` double DEFAULT NULL,
  `nov_27_achievement` double DEFAULT NULL,
  `nov_28_target` double DEFAULT NULL,
  `nov_28_achievement` double DEFAULT NULL,
  `nov_29_target` double DEFAULT NULL,
  `nov_29_achievement` double DEFAULT NULL,
  `nov_30_target` double DEFAULT NULL,
  `nov_30_achievement` double DEFAULT NULL,
  `dec_1_target` double DEFAULT NULL,
  `dec_1_achievement` double DEFAULT NULL,
  `dec_2_target` double DEFAULT NULL,
  `dec_2_achievement` double DEFAULT NULL,
  `dec_3_target` double DEFAULT NULL,
  `dec_3_achievement` double DEFAULT NULL,
  `dec_4_target` double DEFAULT NULL,
  `dec_4_achievement` double DEFAULT NULL,
  `dec_5_target` double DEFAULT NULL,
  `dec_5_achievement` double DEFAULT NULL,
  `dec_6_target` double DEFAULT NULL,
  `dec_6_achievement` double DEFAULT NULL,
  `dec_7_target` double DEFAULT NULL,
  `dec_7_achievement` double DEFAULT NULL,
  `dec_8_target` double DEFAULT NULL,
  `dec_8_achievement` double DEFAULT NULL,
  `dec_9_target` double DEFAULT NULL,
  `dec_9_achievement` double DEFAULT NULL,
  `dec_10_target` double DEFAULT NULL,
  `dec_10_achievement` double DEFAULT NULL,
  `dec_11_target` double DEFAULT NULL,
  `dec_11_achievement` double DEFAULT NULL,
  `dec_12_target` double DEFAULT NULL,
  `dec_12_achievement` double DEFAULT NULL,
  `dec_13_target` double DEFAULT NULL,
  `dec_13_achievement` double DEFAULT NULL,
  `dec_14_target` double DEFAULT NULL,
  `dec_14_achievement` double DEFAULT NULL,
  `dec_15_target` double DEFAULT NULL,
  `dec_15_achievement` double DEFAULT NULL,
  `dec_16_target` double DEFAULT NULL,
  `dec_16_achievement` double DEFAULT NULL,
  `dec_17_target` double DEFAULT NULL,
  `dec_17_achievement` double DEFAULT NULL,
  `dec_18_target` double DEFAULT NULL,
  `dec_18_achievement` double DEFAULT NULL,
  `dec_19_target` double DEFAULT NULL,
  `dec_19_achievement` double DEFAULT NULL,
  `dec_20_target` double DEFAULT NULL,
  `dec_20_achievement` double DEFAULT NULL,
  `dec_21_target` double DEFAULT NULL,
  `dec_21_achievement` double DEFAULT NULL,
  `dec_22_target` double DEFAULT NULL,
  `dec_22_achievement` double DEFAULT NULL,
  `dec_23_target` double DEFAULT NULL,
  `dec_23_achievement` double DEFAULT NULL,
  `dec_24_target` double DEFAULT NULL,
  `dec_24_achievement` double DEFAULT NULL,
  `dec_25_target` double DEFAULT NULL,
  `dec_25_achievement` double DEFAULT NULL,
  `dec_26_target` double DEFAULT NULL,
  `dec_26_achievement` double DEFAULT NULL,
  `dec_27_target` double DEFAULT NULL,
  `dec_27_achievement` double DEFAULT NULL,
  `dec_28_target` double DEFAULT NULL,
  `dec_28_achievement` double DEFAULT NULL,
  `dec_29_target` double DEFAULT NULL,
  `dec_29_achievement` double DEFAULT NULL,
  `dec_30_target` double DEFAULT NULL,
  `dec_30_achievement` double DEFAULT NULL,
  `dec_31_target` double DEFAULT NULL,
  `dec_31_achievement` double DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `self_appraisal_branch_wise`
--

CREATE TABLE `self_appraisal_branch_wise` (
  `sl_no` bigint(15) NOT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `emp_code` varchar(50) NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `jan_1_target` double DEFAULT NULL,
  `jan_1_achievement` double DEFAULT NULL,
  `jan_2_target` double DEFAULT NULL,
  `jan_2_achievement` double DEFAULT NULL,
  `jan_3_target` double DEFAULT NULL,
  `jan_3_achievement` double DEFAULT NULL,
  `jan_4_target` double DEFAULT NULL,
  `jan_4_achievement` double DEFAULT NULL,
  `jan_5_target` double DEFAULT NULL,
  `jan_5_achievement` double DEFAULT NULL,
  `jan_6_target` double DEFAULT NULL,
  `jan_6_achievement` double DEFAULT NULL,
  `jan_7_target` double DEFAULT NULL,
  `jan_7_achievement` double DEFAULT NULL,
  `jan_8_target` double DEFAULT NULL,
  `jan_8_achievement` double DEFAULT NULL,
  `jan_9_target` double DEFAULT NULL,
  `jan_9_achievement` double DEFAULT NULL,
  `jan_10_target` double DEFAULT NULL,
  `jan_10_achievement` double DEFAULT NULL,
  `jan_11_target` double DEFAULT NULL,
  `jan_11_achievement` double DEFAULT NULL,
  `jan_12_target` double DEFAULT NULL,
  `jan_12_achievement` double DEFAULT NULL,
  `jan_13_target` double DEFAULT NULL,
  `jan_13_achievement` double DEFAULT NULL,
  `jan_14_target` double DEFAULT NULL,
  `jan_14_achievement` double DEFAULT NULL,
  `jan_15_target` double DEFAULT NULL,
  `jan_15_achievement` double DEFAULT NULL,
  `jan_16_target` double DEFAULT NULL,
  `jan_16_achievement` double DEFAULT NULL,
  `jan_17_target` double DEFAULT NULL,
  `jan_17_achievement` double DEFAULT NULL,
  `jan_18_target` double DEFAULT NULL,
  `jan_18_achievement` double DEFAULT NULL,
  `jan_19_target` double DEFAULT NULL,
  `jan_19_achievement` double DEFAULT NULL,
  `jan_20_target` double DEFAULT NULL,
  `jan_20_achievement` double DEFAULT NULL,
  `jan_21_target` double DEFAULT NULL,
  `jan_21_achievement` double DEFAULT NULL,
  `jan_22_target` double DEFAULT NULL,
  `jan_22_achievement` double DEFAULT NULL,
  `jan_23_target` double DEFAULT NULL,
  `jan_23_achievement` double DEFAULT NULL,
  `jan_24_target` double DEFAULT NULL,
  `jan_24_achievement` double DEFAULT NULL,
  `jan_25_target` double DEFAULT NULL,
  `jan_25_achievement` double DEFAULT NULL,
  `jan_26_target` double DEFAULT NULL,
  `jan_26_achievement` double DEFAULT NULL,
  `jan_27_target` double DEFAULT NULL,
  `jan_27_achievement` double DEFAULT NULL,
  `jan_28_target` double DEFAULT NULL,
  `jan_28_achievement` double DEFAULT NULL,
  `jan_29_target` double DEFAULT NULL,
  `jan_29_achievement` double DEFAULT NULL,
  `jan_30_target` double DEFAULT NULL,
  `jan_30_achievement` double DEFAULT NULL,
  `jan_31_target` double DEFAULT NULL,
  `jan_31_achievement` double DEFAULT NULL,
  `feb_1_target` double DEFAULT NULL,
  `feb_1_achievement` double DEFAULT NULL,
  `feb_2_target` double DEFAULT NULL,
  `feb_2_achievement` double DEFAULT NULL,
  `feb_3_target` double DEFAULT NULL,
  `feb_3_achievement` double DEFAULT NULL,
  `feb_4_target` double DEFAULT NULL,
  `feb_4_achievement` double DEFAULT NULL,
  `feb_5_target` double DEFAULT NULL,
  `feb_5_achievement` double DEFAULT NULL,
  `feb_6_target` double DEFAULT NULL,
  `feb_6_achievement` double DEFAULT NULL,
  `feb_7_target` double DEFAULT NULL,
  `feb_7_achievement` double DEFAULT NULL,
  `feb_8_target` double DEFAULT NULL,
  `feb_8_achievement` double DEFAULT NULL,
  `feb_9_target` double DEFAULT NULL,
  `feb_9_achievement` double DEFAULT NULL,
  `feb_10_target` double DEFAULT NULL,
  `feb_10_achievement` double DEFAULT NULL,
  `feb_11_target` double DEFAULT NULL,
  `feb_11_achievement` double DEFAULT NULL,
  `feb_12_target` double DEFAULT NULL,
  `feb_12_achievement` double DEFAULT NULL,
  `feb_13_target` double DEFAULT NULL,
  `feb_13_achievement` double DEFAULT NULL,
  `feb_14_target` double DEFAULT NULL,
  `feb_14_achievement` double DEFAULT NULL,
  `feb_15_target` double DEFAULT NULL,
  `feb_15_achievement` double DEFAULT NULL,
  `feb_16_target` double DEFAULT NULL,
  `feb_16_achievement` double DEFAULT NULL,
  `feb_17_target` double DEFAULT NULL,
  `feb_17_achievement` double DEFAULT NULL,
  `feb_18_target` double DEFAULT NULL,
  `feb_18_achievement` double DEFAULT NULL,
  `feb_19_target` double DEFAULT NULL,
  `feb_19_achievement` double DEFAULT NULL,
  `feb_20_target` double DEFAULT NULL,
  `feb_20_achievement` double DEFAULT NULL,
  `feb_21_target` double DEFAULT NULL,
  `feb_21_achievement` double DEFAULT NULL,
  `feb_22_target` double DEFAULT NULL,
  `feb_22_achievement` double DEFAULT NULL,
  `feb_23_target` double DEFAULT NULL,
  `feb_23_achievement` double DEFAULT NULL,
  `feb_24_target` double DEFAULT NULL,
  `feb_24_achievement` double DEFAULT NULL,
  `feb_25_target` double DEFAULT NULL,
  `feb_25_achievement` double DEFAULT NULL,
  `feb_26_target` double DEFAULT NULL,
  `feb_26_achievement` double DEFAULT NULL,
  `feb_27_target` double DEFAULT NULL,
  `feb_27_achievement` double DEFAULT NULL,
  `feb_28_target` double DEFAULT NULL,
  `feb_28_achievement` double DEFAULT NULL,
  `feb_29_target` double DEFAULT NULL,
  `feb_29_achievement` double DEFAULT NULL,
  `mar_1_target` double DEFAULT NULL,
  `mar_1_achievement` double DEFAULT NULL,
  `mar_2_target` double DEFAULT NULL,
  `mar_2_achievement` double DEFAULT NULL,
  `mar_3_target` double DEFAULT NULL,
  `mar_3_achievement` double DEFAULT NULL,
  `mar_4_target` double DEFAULT NULL,
  `mar_4_achievement` double DEFAULT NULL,
  `mar_5_target` double DEFAULT NULL,
  `mar_5_achievement` double DEFAULT NULL,
  `mar_6_target` double DEFAULT NULL,
  `mar_6_achievement` double DEFAULT NULL,
  `mar_7_target` double DEFAULT NULL,
  `mar_7_achievement` double DEFAULT NULL,
  `mar_8_target` double DEFAULT NULL,
  `mar_8_achievement` double DEFAULT NULL,
  `mar_9_target` double DEFAULT NULL,
  `mar_9_achievement` double DEFAULT NULL,
  `mar_10_target` double DEFAULT NULL,
  `mar_10_achievement` double DEFAULT NULL,
  `mar_11_target` double DEFAULT NULL,
  `mar_11_achievement` double DEFAULT NULL,
  `mar_12_target` double DEFAULT NULL,
  `mar_12_achievement` double DEFAULT NULL,
  `mar_13_target` double DEFAULT NULL,
  `mar_13_achievement` double DEFAULT NULL,
  `mar_14_target` double DEFAULT NULL,
  `mar_14_achievement` double DEFAULT NULL,
  `mar_15_target` double DEFAULT NULL,
  `mar_15_achievement` double DEFAULT NULL,
  `mar_16_target` double DEFAULT NULL,
  `mar_16_achievement` double DEFAULT NULL,
  `mar_17_target` double DEFAULT NULL,
  `mar_17_achievement` double DEFAULT NULL,
  `mar_18_target` double DEFAULT NULL,
  `mar_18_achievement` double DEFAULT NULL,
  `mar_19_target` double DEFAULT NULL,
  `mar_19_achievement` double DEFAULT NULL,
  `mar_20_target` double DEFAULT NULL,
  `mar_20_achievement` double DEFAULT NULL,
  `mar_21_target` double DEFAULT NULL,
  `mar_21_achievement` double DEFAULT NULL,
  `mar_22_target` double DEFAULT NULL,
  `mar_22_achievement` double DEFAULT NULL,
  `mar_23_target` double DEFAULT NULL,
  `mar_23_achievement` double DEFAULT NULL,
  `mar_24_target` double DEFAULT NULL,
  `mar_24_achievement` double DEFAULT NULL,
  `mar_25_target` double DEFAULT NULL,
  `mar_25_achievement` double DEFAULT NULL,
  `mar_26_target` double DEFAULT NULL,
  `mar_26_achievement` double DEFAULT NULL,
  `mar_27_target` double DEFAULT NULL,
  `mar_27_achievement` double DEFAULT NULL,
  `mar_28_target` double DEFAULT NULL,
  `mar_28_achievement` double DEFAULT NULL,
  `mar_29_target` double DEFAULT NULL,
  `mar_29_achievement` double DEFAULT NULL,
  `mar_30_target` double DEFAULT NULL,
  `mar_30_achievement` double DEFAULT NULL,
  `mar_31_target` double DEFAULT NULL,
  `mar_31_achievement` double DEFAULT NULL,
  `apr_1_target` double DEFAULT NULL,
  `apr_1_achievement` double DEFAULT NULL,
  `apr_2_target` double DEFAULT NULL,
  `apr_2_achievement` double DEFAULT NULL,
  `apr_3_target` double DEFAULT NULL,
  `apr_3_achievement` double DEFAULT NULL,
  `apr_4_target` double DEFAULT NULL,
  `apr_4_achievement` double DEFAULT NULL,
  `apr_5_target` double DEFAULT NULL,
  `apr_5_achievement` double DEFAULT NULL,
  `apr_6_target` double DEFAULT NULL,
  `apr_6_achievement` double DEFAULT NULL,
  `apr_7_target` double DEFAULT NULL,
  `apr_7_achievement` double DEFAULT NULL,
  `apr_8_target` double DEFAULT NULL,
  `apr_8_achievement` double DEFAULT NULL,
  `apr_9_target` double DEFAULT NULL,
  `apr_9_achievement` double DEFAULT NULL,
  `apr_10_target` double DEFAULT NULL,
  `apr_10_achievement` double DEFAULT NULL,
  `apr_11_target` double DEFAULT NULL,
  `apr_11_achievement` double DEFAULT NULL,
  `apr_12_target` double DEFAULT NULL,
  `apr_12_achievement` double DEFAULT NULL,
  `apr_13_target` double DEFAULT NULL,
  `apr_13_achievement` double DEFAULT NULL,
  `apr_14_target` double DEFAULT NULL,
  `apr_14_achievement` double DEFAULT NULL,
  `apr_15_target` double DEFAULT NULL,
  `apr_15_achievement` double DEFAULT NULL,
  `apr_16_target` double DEFAULT NULL,
  `apr_16_achievement` double DEFAULT NULL,
  `apr_17_target` double DEFAULT NULL,
  `apr_17_achievement` double DEFAULT NULL,
  `apr_18_target` double DEFAULT NULL,
  `apr_18_achievement` double DEFAULT NULL,
  `apr_19_target` double DEFAULT NULL,
  `apr_19_achievement` double DEFAULT NULL,
  `apr_20_target` double DEFAULT NULL,
  `apr_20_achievement` double DEFAULT NULL,
  `apr_21_target` double DEFAULT NULL,
  `apr_21_achievement` double DEFAULT NULL,
  `apr_22_target` double DEFAULT NULL,
  `apr_22_achievement` double DEFAULT NULL,
  `apr_23_target` double DEFAULT NULL,
  `apr_23_achievement` double DEFAULT NULL,
  `apr_24_target` double DEFAULT NULL,
  `apr_24_achievement` double DEFAULT NULL,
  `apr_25_target` double DEFAULT NULL,
  `apr_25_achievement` double DEFAULT NULL,
  `apr_26_target` double DEFAULT NULL,
  `apr_26_achievement` double DEFAULT NULL,
  `apr_27_target` double DEFAULT NULL,
  `apr_27_achievement` double DEFAULT NULL,
  `apr_28_target` double DEFAULT NULL,
  `apr_28_achievement` double DEFAULT NULL,
  `apr_29_target` double DEFAULT NULL,
  `apr_29_achievement` double DEFAULT NULL,
  `apr_30_target` double DEFAULT NULL,
  `apr_30_achievement` double DEFAULT NULL,
  `may_1_target` double DEFAULT NULL,
  `may_1_achievement` double DEFAULT NULL,
  `may_2_target` double DEFAULT NULL,
  `may_2_achievement` double DEFAULT NULL,
  `may_3_target` double DEFAULT NULL,
  `may_3_achievement` double DEFAULT NULL,
  `may_4_target` double DEFAULT NULL,
  `may_4_achievement` double DEFAULT NULL,
  `may_5_target` double DEFAULT NULL,
  `may_5_achievement` double DEFAULT NULL,
  `may_6_target` double DEFAULT NULL,
  `may_6_achievement` double DEFAULT NULL,
  `may_7_target` double DEFAULT NULL,
  `may_7_achievement` double DEFAULT NULL,
  `may_8_target` double DEFAULT NULL,
  `may_8_achievement` double DEFAULT NULL,
  `may_9_target` double DEFAULT NULL,
  `may_9_achievement` double DEFAULT NULL,
  `may_10_target` double DEFAULT NULL,
  `may_10_achievement` double DEFAULT NULL,
  `may_11_target` double DEFAULT NULL,
  `may_11_achievement` double DEFAULT NULL,
  `may_12_target` double DEFAULT NULL,
  `may_12_achievement` double DEFAULT NULL,
  `may_13_target` double DEFAULT NULL,
  `may_13_achievement` double DEFAULT NULL,
  `may_14_target` double DEFAULT NULL,
  `may_14_achievement` double DEFAULT NULL,
  `may_15_target` double DEFAULT NULL,
  `may_15_achievement` double DEFAULT NULL,
  `may_16_target` double DEFAULT NULL,
  `may_16_achievement` double DEFAULT NULL,
  `may_17_target` double DEFAULT NULL,
  `may_17_achievement` double DEFAULT NULL,
  `may_18_target` double DEFAULT NULL,
  `may_18_achievement` double DEFAULT NULL,
  `may_19_target` double DEFAULT NULL,
  `may_19_achievement` double DEFAULT NULL,
  `may_20_target` double DEFAULT NULL,
  `may_20_achievement` double DEFAULT NULL,
  `may_21_target` double DEFAULT NULL,
  `may_21_achievement` double DEFAULT NULL,
  `may_22_target` double DEFAULT NULL,
  `may_22_achievement` double DEFAULT NULL,
  `may_23_target` double DEFAULT NULL,
  `may_23_achievement` double DEFAULT NULL,
  `may_24_target` double DEFAULT NULL,
  `may_24_achievement` double DEFAULT NULL,
  `may_25_target` double DEFAULT NULL,
  `may_25_achievement` double DEFAULT NULL,
  `may_26_target` double DEFAULT NULL,
  `may_26_achievement` double DEFAULT NULL,
  `may_27_target` double DEFAULT NULL,
  `may_27_achievement` double DEFAULT NULL,
  `may_28_target` double DEFAULT NULL,
  `may_28_achievement` double DEFAULT NULL,
  `may_29_target` double DEFAULT NULL,
  `may_29_achievement` double DEFAULT NULL,
  `may_30_target` double DEFAULT NULL,
  `may_30_achievement` double DEFAULT NULL,
  `may_31_target` double DEFAULT NULL,
  `may_31_achievement` double DEFAULT NULL,
  `jun_1_target` double DEFAULT NULL,
  `jun_1_achievement` double DEFAULT NULL,
  `jun_2_target` double DEFAULT NULL,
  `jun_2_achievement` double DEFAULT NULL,
  `jun_3_target` double DEFAULT NULL,
  `jun_3_achievement` double DEFAULT NULL,
  `jun_4_target` double DEFAULT NULL,
  `jun_4_achievement` double DEFAULT NULL,
  `jun_5_target` double DEFAULT NULL,
  `jun_5_achievement` double DEFAULT NULL,
  `jun_6_target` double DEFAULT NULL,
  `jun_6_achievement` double DEFAULT NULL,
  `jun_7_target` double DEFAULT NULL,
  `jun_7_achievement` double DEFAULT NULL,
  `jun_8_target` double DEFAULT NULL,
  `jun_8_achievement` double DEFAULT NULL,
  `jun_9_target` double DEFAULT NULL,
  `jun_9_achievement` double DEFAULT NULL,
  `jun_10_target` double DEFAULT NULL,
  `jun_10_achievement` double DEFAULT NULL,
  `jun_11_target` double DEFAULT NULL,
  `jun_11_achievement` double DEFAULT NULL,
  `jun_12_target` double DEFAULT NULL,
  `jun_12_achievement` double DEFAULT NULL,
  `jun_13_target` double DEFAULT NULL,
  `jun_13_achievement` double DEFAULT NULL,
  `jun_14_target` double DEFAULT NULL,
  `jun_14_achievement` double DEFAULT NULL,
  `jun_15_target` double DEFAULT NULL,
  `jun_15_achievement` double DEFAULT NULL,
  `jun_16_target` double DEFAULT NULL,
  `jun_16_achievement` double DEFAULT NULL,
  `jun_17_target` double DEFAULT NULL,
  `jun_17_achievement` double DEFAULT NULL,
  `jun_18_target` double DEFAULT NULL,
  `jun_18_achievement` double DEFAULT NULL,
  `jun_19_target` double DEFAULT NULL,
  `jun_19_achievement` double DEFAULT NULL,
  `jun_20_target` double DEFAULT NULL,
  `jun_20_achievement` double DEFAULT NULL,
  `jun_21_target` double DEFAULT NULL,
  `jun_21_achievement` double DEFAULT NULL,
  `jun_22_target` double DEFAULT NULL,
  `jun_22_achievement` double DEFAULT NULL,
  `jun_23_target` double DEFAULT NULL,
  `jun_23_achievement` double DEFAULT NULL,
  `jun_24_target` double DEFAULT NULL,
  `jun_24_achievement` double DEFAULT NULL,
  `jun_25_target` double DEFAULT NULL,
  `jun_25_achievement` double DEFAULT NULL,
  `jun_26_target` double DEFAULT NULL,
  `jun_26_achievement` double DEFAULT NULL,
  `jun_27_target` double DEFAULT NULL,
  `jun_27_achievement` double DEFAULT NULL,
  `jun_28_target` double DEFAULT NULL,
  `jun_28_achievement` double DEFAULT NULL,
  `jun_29_target` double DEFAULT NULL,
  `jun_29_achievement` double DEFAULT NULL,
  `jun_30_target` double DEFAULT NULL,
  `jun_30_achievement` double DEFAULT NULL,
  `jul_1_target` double DEFAULT NULL,
  `jul_1_achievement` double DEFAULT NULL,
  `jul_2_target` double DEFAULT NULL,
  `jul_2_achievement` double DEFAULT NULL,
  `jul_3_target` double DEFAULT NULL,
  `jul_3_achievement` double DEFAULT NULL,
  `jul_4_target` double DEFAULT NULL,
  `jul_4_achievement` double DEFAULT NULL,
  `jul_5_target` double DEFAULT NULL,
  `jul_5_achievement` double DEFAULT NULL,
  `jul_6_target` double DEFAULT NULL,
  `jul_6_achievement` double DEFAULT NULL,
  `jul_7_target` double DEFAULT NULL,
  `jul_7_achievement` double DEFAULT NULL,
  `jul_8_target` double DEFAULT NULL,
  `jul_8_achievement` double DEFAULT NULL,
  `jul_9_target` double DEFAULT NULL,
  `jul_9_achievement` double DEFAULT NULL,
  `jul_10_target` double DEFAULT NULL,
  `jul_10_achievement` double DEFAULT NULL,
  `jul_11_target` double DEFAULT NULL,
  `jul_11_achievement` double DEFAULT NULL,
  `jul_12_target` double DEFAULT NULL,
  `jul_12_achievement` double DEFAULT NULL,
  `jul_13_target` double DEFAULT NULL,
  `jul_13_achievement` double DEFAULT NULL,
  `jul_14_target` double DEFAULT NULL,
  `jul_14_achievement` double DEFAULT NULL,
  `jul_15_target` double DEFAULT NULL,
  `jul_15_achievement` double DEFAULT NULL,
  `jul_16_target` double DEFAULT NULL,
  `jul_16_achievement` double DEFAULT NULL,
  `jul_17_target` double DEFAULT NULL,
  `jul_17_achievement` double DEFAULT NULL,
  `jul_18_target` double DEFAULT NULL,
  `jul_18_achievement` double DEFAULT NULL,
  `jul_19_target` double DEFAULT NULL,
  `jul_19_achievement` double DEFAULT NULL,
  `jul_20_target` double DEFAULT NULL,
  `jul_20_achievement` double DEFAULT NULL,
  `jul_21_target` double DEFAULT NULL,
  `jul_21_achievement` double DEFAULT NULL,
  `jul_22_target` double DEFAULT NULL,
  `jul_22_achievement` double DEFAULT NULL,
  `jul_23_target` double DEFAULT NULL,
  `jul_23_achievement` double DEFAULT NULL,
  `jul_24_target` double DEFAULT NULL,
  `jul_24_achievement` double DEFAULT NULL,
  `jul_25_target` double DEFAULT NULL,
  `jul_25_achievement` double DEFAULT NULL,
  `jul_26_target` double DEFAULT NULL,
  `jul_26_achievement` double DEFAULT NULL,
  `jul_27_target` double DEFAULT NULL,
  `jul_27_achievement` double DEFAULT NULL,
  `jul_28_target` double DEFAULT NULL,
  `jul_28_achievement` double DEFAULT NULL,
  `jul_29_target` double DEFAULT NULL,
  `jul_29_achievement` double DEFAULT NULL,
  `jul_30_target` double DEFAULT NULL,
  `jul_30_achievement` double DEFAULT NULL,
  `jul_31_target` double DEFAULT NULL,
  `jul_31_achievement` double DEFAULT NULL,
  `aug_1_target` double DEFAULT NULL,
  `aug_1_achievement` double DEFAULT NULL,
  `aug_2_target` double DEFAULT NULL,
  `aug_2_achievement` double DEFAULT NULL,
  `aug_3_target` double DEFAULT NULL,
  `aug_3_achievement` double DEFAULT NULL,
  `aug_4_target` double DEFAULT NULL,
  `aug_4_achievement` double DEFAULT NULL,
  `aug_5_target` double DEFAULT NULL,
  `aug_5_achievement` double DEFAULT NULL,
  `aug_6_target` double DEFAULT NULL,
  `aug_6_achievement` double DEFAULT NULL,
  `aug_7_target` double DEFAULT NULL,
  `aug_7_achievement` double DEFAULT NULL,
  `aug_8_target` double DEFAULT NULL,
  `aug_8_achievement` double DEFAULT NULL,
  `aug_9_target` double DEFAULT NULL,
  `aug_9_achievement` double DEFAULT NULL,
  `aug_10_target` double DEFAULT NULL,
  `aug_10_achievement` double DEFAULT NULL,
  `aug_11_target` double DEFAULT NULL,
  `aug_11_achievement` double DEFAULT NULL,
  `aug_12_target` double DEFAULT NULL,
  `aug_12_achievement` double DEFAULT NULL,
  `aug_13_target` double DEFAULT NULL,
  `aug_13_achievement` double DEFAULT NULL,
  `aug_14_target` double DEFAULT NULL,
  `aug_14_achievement` double DEFAULT NULL,
  `aug_15_target` double DEFAULT NULL,
  `aug_15_achievement` double DEFAULT NULL,
  `aug_16_target` double DEFAULT NULL,
  `aug_16_achievement` double DEFAULT NULL,
  `aug_17_target` double DEFAULT NULL,
  `aug_17_achievement` double DEFAULT NULL,
  `aug_18_target` double DEFAULT NULL,
  `aug_18_achievement` double DEFAULT NULL,
  `aug_19_target` double DEFAULT NULL,
  `aug_19_achievement` double DEFAULT NULL,
  `aug_20_target` double DEFAULT NULL,
  `aug_20_achievement` double DEFAULT NULL,
  `aug_21_target` double DEFAULT NULL,
  `aug_21_achievement` double DEFAULT NULL,
  `aug_22_target` double DEFAULT NULL,
  `aug_22_achievement` double DEFAULT NULL,
  `aug_23_target` double DEFAULT NULL,
  `aug_23_achievement` double DEFAULT NULL,
  `aug_24_target` double DEFAULT NULL,
  `aug_24_achievement` double DEFAULT NULL,
  `aug_25_target` double DEFAULT NULL,
  `aug_25_achievement` double DEFAULT NULL,
  `aug_26_target` double DEFAULT NULL,
  `aug_26_achievement` double DEFAULT NULL,
  `aug_27_target` double DEFAULT NULL,
  `aug_27_achievement` double DEFAULT NULL,
  `aug_28_target` double DEFAULT NULL,
  `aug_28_achievement` double DEFAULT NULL,
  `aug_29_target` double DEFAULT NULL,
  `aug_29_achievement` double DEFAULT NULL,
  `aug_30_target` double DEFAULT NULL,
  `aug_30_achievement` double DEFAULT NULL,
  `aug_31_target` double DEFAULT NULL,
  `aug_31_achievement` double DEFAULT NULL,
  `sep_1_target` double DEFAULT NULL,
  `sep_1_achievement` double DEFAULT NULL,
  `sep_2_target` double DEFAULT NULL,
  `sep_2_achievement` double DEFAULT NULL,
  `sep_3_target` double DEFAULT NULL,
  `sep_3_achievement` double DEFAULT NULL,
  `sep_4_target` double DEFAULT NULL,
  `sep_4_achievement` double DEFAULT NULL,
  `sep_5_target` double DEFAULT NULL,
  `sep_5_achievement` double DEFAULT NULL,
  `sep_6_target` double DEFAULT NULL,
  `sep_6_achievement` double DEFAULT NULL,
  `sep_7_target` double DEFAULT NULL,
  `sep_7_achievement` double DEFAULT NULL,
  `sep_8_target` double DEFAULT NULL,
  `sep_8_achievement` double DEFAULT NULL,
  `sep_9_target` double DEFAULT NULL,
  `sep_9_achievement` double DEFAULT NULL,
  `sep_10_target` double DEFAULT NULL,
  `sep_10_achievement` double DEFAULT NULL,
  `sep_11_target` double DEFAULT NULL,
  `sep_11_achievement` double DEFAULT NULL,
  `sep_12_target` double DEFAULT NULL,
  `sep_12_achievement` double DEFAULT NULL,
  `sep_13_target` double DEFAULT NULL,
  `sep_13_achievement` double DEFAULT NULL,
  `sep_14_target` double DEFAULT NULL,
  `sep_14_achievement` double DEFAULT NULL,
  `sep_15_target` double DEFAULT NULL,
  `sep_15_achievement` double DEFAULT NULL,
  `sep_16_target` double DEFAULT NULL,
  `sep_16_achievement` double DEFAULT NULL,
  `sep_17_target` double DEFAULT NULL,
  `sep_17_achievement` double DEFAULT NULL,
  `sep_18_target` double DEFAULT NULL,
  `sep_18_achievement` double DEFAULT NULL,
  `sep_19_target` double DEFAULT NULL,
  `sep_19_achievement` double DEFAULT NULL,
  `sep_20_target` double DEFAULT NULL,
  `sep_20_achievement` double DEFAULT NULL,
  `sep_21_target` double DEFAULT NULL,
  `sep_21_achievement` double DEFAULT NULL,
  `sep_22_target` double DEFAULT NULL,
  `sep_22_achievement` double DEFAULT NULL,
  `sep_23_target` double DEFAULT NULL,
  `sep_23_achievement` double DEFAULT NULL,
  `sep_24_target` double DEFAULT NULL,
  `sep_24_achievement` double DEFAULT NULL,
  `sep_25_target` double DEFAULT NULL,
  `sep_25_achievement` double DEFAULT NULL,
  `sep_26_target` double DEFAULT NULL,
  `sep_26_achievement` double DEFAULT NULL,
  `sep_27_target` double DEFAULT NULL,
  `sep_27_achievement` double DEFAULT NULL,
  `sep_28_target` double DEFAULT NULL,
  `sep_28_achievement` double DEFAULT NULL,
  `sep_29_target` double DEFAULT NULL,
  `sep_29_achievement` double DEFAULT NULL,
  `sep_30_target` double DEFAULT NULL,
  `sep_30_achievement` double DEFAULT NULL,
  `oct_1_target` double DEFAULT NULL,
  `oct_1_achievement` double DEFAULT NULL,
  `oct_2_target` double DEFAULT NULL,
  `oct_2_achievement` double DEFAULT NULL,
  `oct_3_target` double DEFAULT NULL,
  `oct_3_achievement` double DEFAULT NULL,
  `oct_4_target` double DEFAULT NULL,
  `oct_4_achievement` double DEFAULT NULL,
  `oct_5_target` double DEFAULT NULL,
  `oct_5_achievement` double DEFAULT NULL,
  `oct_6_target` double DEFAULT NULL,
  `oct_6_achievement` double DEFAULT NULL,
  `oct_7_target` double DEFAULT NULL,
  `oct_7_achievement` double DEFAULT NULL,
  `oct_8_target` double DEFAULT NULL,
  `oct_8_achievement` double DEFAULT NULL,
  `oct_9_target` double DEFAULT NULL,
  `oct_9_achievement` double DEFAULT NULL,
  `oct_10_target` double DEFAULT NULL,
  `oct_10_achievement` double DEFAULT NULL,
  `oct_11_target` double DEFAULT NULL,
  `oct_11_achievement` double DEFAULT NULL,
  `oct_12_target` double DEFAULT NULL,
  `oct_12_achievement` double DEFAULT NULL,
  `oct_13_target` double DEFAULT NULL,
  `oct_13_achievement` double DEFAULT NULL,
  `oct_14_target` double DEFAULT NULL,
  `oct_14_achievement` double DEFAULT NULL,
  `oct_15_target` double DEFAULT NULL,
  `oct_15_achievement` double DEFAULT NULL,
  `oct_16_target` double DEFAULT NULL,
  `oct_16_achievement` double DEFAULT NULL,
  `oct_17_target` double DEFAULT NULL,
  `oct_17_achievement` double DEFAULT NULL,
  `oct_18_target` double DEFAULT NULL,
  `oct_18_achievement` double DEFAULT NULL,
  `oct_19_target` double DEFAULT NULL,
  `oct_19_achievement` double DEFAULT NULL,
  `oct_20_target` double DEFAULT NULL,
  `oct_20_achievement` double DEFAULT NULL,
  `oct_21_target` double DEFAULT NULL,
  `oct_21_achievement` double DEFAULT NULL,
  `oct_22_target` double DEFAULT NULL,
  `oct_22_achievement` double DEFAULT NULL,
  `oct_23_target` double DEFAULT NULL,
  `oct_23_achievement` double DEFAULT NULL,
  `oct_24_target` double DEFAULT NULL,
  `oct_24_achievement` double DEFAULT NULL,
  `oct_25_target` double DEFAULT NULL,
  `oct_25_achievement` double DEFAULT NULL,
  `oct_26_target` double DEFAULT NULL,
  `oct_26_achievement` double DEFAULT NULL,
  `oct_27_target` double DEFAULT NULL,
  `oct_27_achievement` double DEFAULT NULL,
  `oct_28_target` double DEFAULT NULL,
  `oct_28_achievement` double DEFAULT NULL,
  `oct_29_target` double DEFAULT NULL,
  `oct_29_achievement` double DEFAULT NULL,
  `oct_30_target` double DEFAULT NULL,
  `oct_30_achievement` double DEFAULT NULL,
  `oct_31_target` double DEFAULT NULL,
  `oct_31_achievement` double DEFAULT NULL,
  `nov_1_target` double DEFAULT NULL,
  `nov_1_achievement` double DEFAULT NULL,
  `nov_2_target` double DEFAULT NULL,
  `nov_2_achievement` double DEFAULT NULL,
  `nov_3_target` double DEFAULT NULL,
  `nov_3_achievement` double DEFAULT NULL,
  `nov_4_target` double DEFAULT NULL,
  `nov_4_achievement` double DEFAULT NULL,
  `nov_5_target` double DEFAULT NULL,
  `nov_5_achievement` double DEFAULT NULL,
  `nov_6_target` double DEFAULT NULL,
  `nov_6_achievement` double DEFAULT NULL,
  `nov_7_target` double DEFAULT NULL,
  `nov_7_achievement` double DEFAULT NULL,
  `nov_8_target` double DEFAULT NULL,
  `nov_8_achievement` double DEFAULT NULL,
  `nov_9_target` double DEFAULT NULL,
  `nov_9_achievement` double DEFAULT NULL,
  `nov_10_target` double DEFAULT NULL,
  `nov_10_achievement` double DEFAULT NULL,
  `nov_11_target` double DEFAULT NULL,
  `nov_11_achievement` double DEFAULT NULL,
  `nov_12_target` double DEFAULT NULL,
  `nov_12_achievement` double DEFAULT NULL,
  `nov_13_target` double DEFAULT NULL,
  `nov_13_achievement` double DEFAULT NULL,
  `nov_14_target` double DEFAULT NULL,
  `nov_14_achievement` double DEFAULT NULL,
  `nov_15_target` double DEFAULT NULL,
  `nov_15_achievement` double DEFAULT NULL,
  `nov_16_target` double DEFAULT NULL,
  `nov_16_achievement` double DEFAULT NULL,
  `nov_17_target` double DEFAULT NULL,
  `nov_17_achievement` double DEFAULT NULL,
  `nov_18_target` double DEFAULT NULL,
  `nov_18_achievement` double DEFAULT NULL,
  `nov_19_target` double DEFAULT NULL,
  `nov_19_achievement` double DEFAULT NULL,
  `nov_20_target` double DEFAULT NULL,
  `nov_20_achievement` double DEFAULT NULL,
  `nov_21_target` double DEFAULT NULL,
  `nov_21_achievement` double DEFAULT NULL,
  `nov_22_target` double DEFAULT NULL,
  `nov_22_achievement` double DEFAULT NULL,
  `nov_23_target` double DEFAULT NULL,
  `nov_23_achievement` double DEFAULT NULL,
  `nov_24_target` double DEFAULT NULL,
  `nov_24_achievement` double DEFAULT NULL,
  `nov_25_target` double DEFAULT NULL,
  `nov_25_achievement` double DEFAULT NULL,
  `nov_26_target` double DEFAULT NULL,
  `nov_26_achievement` double DEFAULT NULL,
  `nov_27_target` double DEFAULT NULL,
  `nov_27_achievement` double DEFAULT NULL,
  `nov_28_target` double DEFAULT NULL,
  `nov_28_achievement` double DEFAULT NULL,
  `nov_29_target` double DEFAULT NULL,
  `nov_29_achievement` double DEFAULT NULL,
  `nov_30_target` double DEFAULT NULL,
  `nov_30_achievement` double DEFAULT NULL,
  `dec_1_target` double DEFAULT NULL,
  `dec_1_achievement` double DEFAULT NULL,
  `dec_2_target` double DEFAULT NULL,
  `dec_2_achievement` double DEFAULT NULL,
  `dec_3_target` double DEFAULT NULL,
  `dec_3_achievement` double DEFAULT NULL,
  `dec_4_target` double DEFAULT NULL,
  `dec_4_achievement` double DEFAULT NULL,
  `dec_5_target` double DEFAULT NULL,
  `dec_5_achievement` double DEFAULT NULL,
  `dec_6_target` double DEFAULT NULL,
  `dec_6_achievement` double DEFAULT NULL,
  `dec_7_target` double DEFAULT NULL,
  `dec_7_achievement` double DEFAULT NULL,
  `dec_8_target` double DEFAULT NULL,
  `dec_8_achievement` double DEFAULT NULL,
  `dec_9_target` double DEFAULT NULL,
  `dec_9_achievement` double DEFAULT NULL,
  `dec_10_target` double DEFAULT NULL,
  `dec_10_achievement` double DEFAULT NULL,
  `dec_11_target` double DEFAULT NULL,
  `dec_11_achievement` double DEFAULT NULL,
  `dec_12_target` double DEFAULT NULL,
  `dec_12_achievement` double DEFAULT NULL,
  `dec_13_target` double DEFAULT NULL,
  `dec_13_achievement` double DEFAULT NULL,
  `dec_14_target` double DEFAULT NULL,
  `dec_14_achievement` double DEFAULT NULL,
  `dec_15_target` double DEFAULT NULL,
  `dec_15_achievement` double DEFAULT NULL,
  `dec_16_target` double DEFAULT NULL,
  `dec_16_achievement` double DEFAULT NULL,
  `dec_17_target` double DEFAULT NULL,
  `dec_17_achievement` double DEFAULT NULL,
  `dec_18_target` double DEFAULT NULL,
  `dec_18_achievement` double DEFAULT NULL,
  `dec_19_target` double DEFAULT NULL,
  `dec_19_achievement` double DEFAULT NULL,
  `dec_20_target` double DEFAULT NULL,
  `dec_20_achievement` double DEFAULT NULL,
  `dec_21_target` double DEFAULT NULL,
  `dec_21_achievement` double DEFAULT NULL,
  `dec_22_target` double DEFAULT NULL,
  `dec_22_achievement` double DEFAULT NULL,
  `dec_23_target` double DEFAULT NULL,
  `dec_23_achievement` double DEFAULT NULL,
  `dec_24_target` double DEFAULT NULL,
  `dec_24_achievement` double DEFAULT NULL,
  `dec_25_target` double DEFAULT NULL,
  `dec_25_achievement` double DEFAULT NULL,
  `dec_26_target` double DEFAULT NULL,
  `dec_26_achievement` double DEFAULT NULL,
  `dec_27_target` double DEFAULT NULL,
  `dec_27_achievement` double DEFAULT NULL,
  `dec_28_target` double DEFAULT NULL,
  `dec_28_achievement` double DEFAULT NULL,
  `dec_29_target` double DEFAULT NULL,
  `dec_29_achievement` double DEFAULT NULL,
  `dec_30_target` double DEFAULT NULL,
  `dec_30_achievement` double DEFAULT NULL,
  `dec_31_target` double DEFAULT NULL,
  `dec_31_achievement` double DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `self_appraisal_customer_wise`
--

CREATE TABLE `self_appraisal_customer_wise` (
  `sl_no` bigint(15) NOT NULL,
  `emp_code` varchar(50) NOT NULL,
  `customer_code` varchar(50) NOT NULL,
  `jan_1_target` double DEFAULT NULL,
  `jan_1_achievement` double DEFAULT NULL,
  `jan_2_target` double DEFAULT NULL,
  `jan_2_achievement` double DEFAULT NULL,
  `jan_3_target` double DEFAULT NULL,
  `jan_3_achievement` double DEFAULT NULL,
  `jan_4_target` double DEFAULT NULL,
  `jan_4_achievement` double DEFAULT NULL,
  `jan_5_target` double DEFAULT NULL,
  `jan_5_achievement` double DEFAULT NULL,
  `jan_6_target` double DEFAULT NULL,
  `jan_6_achievement` double DEFAULT NULL,
  `jan_7_target` double DEFAULT NULL,
  `jan_7_achievement` double DEFAULT NULL,
  `jan_8_target` double DEFAULT NULL,
  `jan_8_achievement` double DEFAULT NULL,
  `jan_9_target` double DEFAULT NULL,
  `jan_9_achievement` double DEFAULT NULL,
  `jan_10_target` double DEFAULT NULL,
  `jan_10_achievement` double DEFAULT NULL,
  `jan_11_target` double DEFAULT NULL,
  `jan_11_achievement` double DEFAULT NULL,
  `jan_12_target` double DEFAULT NULL,
  `jan_12_achievement` double DEFAULT NULL,
  `jan_13_target` double DEFAULT NULL,
  `jan_13_achievement` double DEFAULT NULL,
  `jan_14_target` double DEFAULT NULL,
  `jan_14_achievement` double DEFAULT NULL,
  `jan_15_target` double DEFAULT NULL,
  `jan_15_achievement` double DEFAULT NULL,
  `jan_16_target` double DEFAULT NULL,
  `jan_16_achievement` double DEFAULT NULL,
  `jan_17_target` double DEFAULT NULL,
  `jan_17_achievement` double DEFAULT NULL,
  `jan_18_target` double DEFAULT NULL,
  `jan_18_achievement` double DEFAULT NULL,
  `jan_19_target` double DEFAULT NULL,
  `jan_19_achievement` double DEFAULT NULL,
  `jan_20_target` double DEFAULT NULL,
  `jan_20_achievement` double DEFAULT NULL,
  `jan_21_target` double DEFAULT NULL,
  `jan_21_achievement` double DEFAULT NULL,
  `jan_22_target` double DEFAULT NULL,
  `jan_22_achievement` double DEFAULT NULL,
  `jan_23_target` double DEFAULT NULL,
  `jan_23_achievement` double DEFAULT NULL,
  `jan_24_target` double DEFAULT NULL,
  `jan_24_achievement` double DEFAULT NULL,
  `jan_25_target` double DEFAULT NULL,
  `jan_25_achievement` double DEFAULT NULL,
  `jan_26_target` double DEFAULT NULL,
  `jan_26_achievement` double DEFAULT NULL,
  `jan_27_target` double DEFAULT NULL,
  `jan_27_achievement` double DEFAULT NULL,
  `jan_28_target` double DEFAULT NULL,
  `jan_28_achievement` double DEFAULT NULL,
  `jan_29_target` double DEFAULT NULL,
  `jan_29_achievement` double DEFAULT NULL,
  `jan_30_target` double DEFAULT NULL,
  `jan_30_achievement` double DEFAULT NULL,
  `jan_31_target` double DEFAULT NULL,
  `jan_31_achievement` double DEFAULT NULL,
  `feb_1_target` double DEFAULT NULL,
  `feb_1_achievement` double DEFAULT NULL,
  `feb_2_target` double DEFAULT NULL,
  `feb_2_achievement` double DEFAULT NULL,
  `feb_3_target` double DEFAULT NULL,
  `feb_3_achievement` double DEFAULT NULL,
  `feb_4_target` double DEFAULT NULL,
  `feb_4_achievement` double DEFAULT NULL,
  `feb_5_target` double DEFAULT NULL,
  `feb_5_achievement` double DEFAULT NULL,
  `feb_6_target` double DEFAULT NULL,
  `feb_6_achievement` double DEFAULT NULL,
  `feb_7_target` double DEFAULT NULL,
  `feb_7_achievement` double DEFAULT NULL,
  `feb_8_target` double DEFAULT NULL,
  `feb_8_achievement` double DEFAULT NULL,
  `feb_9_target` double DEFAULT NULL,
  `feb_9_achievement` double DEFAULT NULL,
  `feb_10_target` double DEFAULT NULL,
  `feb_10_achievement` double DEFAULT NULL,
  `feb_11_target` double DEFAULT NULL,
  `feb_11_achievement` double DEFAULT NULL,
  `feb_12_target` double DEFAULT NULL,
  `feb_12_achievement` double DEFAULT NULL,
  `feb_13_target` double DEFAULT NULL,
  `feb_13_achievement` double DEFAULT NULL,
  `feb_14_target` double DEFAULT NULL,
  `feb_14_achievement` double DEFAULT NULL,
  `feb_15_target` double DEFAULT NULL,
  `feb_15_achievement` double DEFAULT NULL,
  `feb_16_target` double DEFAULT NULL,
  `feb_16_achievement` double DEFAULT NULL,
  `feb_17_target` double DEFAULT NULL,
  `feb_17_achievement` double DEFAULT NULL,
  `feb_18_target` double DEFAULT NULL,
  `feb_18_achievement` double DEFAULT NULL,
  `feb_19_target` double DEFAULT NULL,
  `feb_19_achievement` double DEFAULT NULL,
  `feb_20_target` double DEFAULT NULL,
  `feb_20_achievement` double DEFAULT NULL,
  `feb_21_target` double DEFAULT NULL,
  `feb_21_achievement` double DEFAULT NULL,
  `feb_22_target` double DEFAULT NULL,
  `feb_22_achievement` double DEFAULT NULL,
  `feb_23_target` double DEFAULT NULL,
  `feb_23_achievement` double DEFAULT NULL,
  `feb_24_target` double DEFAULT NULL,
  `feb_24_achievement` double DEFAULT NULL,
  `feb_25_target` double DEFAULT NULL,
  `feb_25_achievement` double DEFAULT NULL,
  `feb_26_target` double DEFAULT NULL,
  `feb_26_achievement` double DEFAULT NULL,
  `feb_27_target` double DEFAULT NULL,
  `feb_27_achievement` double DEFAULT NULL,
  `feb_28_target` double DEFAULT NULL,
  `feb_28_achievement` double DEFAULT NULL,
  `feb_29_target` double DEFAULT NULL,
  `feb_29_achievement` double DEFAULT NULL,
  `mar_1_target` double DEFAULT NULL,
  `mar_1_achievement` double DEFAULT NULL,
  `mar_2_target` double DEFAULT NULL,
  `mar_2_achievement` double DEFAULT NULL,
  `mar_3_target` double DEFAULT NULL,
  `mar_3_achievement` double DEFAULT NULL,
  `mar_4_target` double DEFAULT NULL,
  `mar_4_achievement` double DEFAULT NULL,
  `mar_5_target` double DEFAULT NULL,
  `mar_5_achievement` double DEFAULT NULL,
  `mar_6_target` double DEFAULT NULL,
  `mar_6_achievement` double DEFAULT NULL,
  `mar_7_target` double DEFAULT NULL,
  `mar_7_achievement` double DEFAULT NULL,
  `mar_8_target` double DEFAULT NULL,
  `mar_8_achievement` double DEFAULT NULL,
  `mar_9_target` double DEFAULT NULL,
  `mar_9_achievement` double DEFAULT NULL,
  `mar_10_target` double DEFAULT NULL,
  `mar_10_achievement` double DEFAULT NULL,
  `mar_11_target` double DEFAULT NULL,
  `mar_11_achievement` double DEFAULT NULL,
  `mar_12_target` double DEFAULT NULL,
  `mar_12_achievement` double DEFAULT NULL,
  `mar_13_target` double DEFAULT NULL,
  `mar_13_achievement` double DEFAULT NULL,
  `mar_14_target` double DEFAULT NULL,
  `mar_14_achievement` double DEFAULT NULL,
  `mar_15_target` double DEFAULT NULL,
  `mar_15_achievement` double DEFAULT NULL,
  `mar_16_target` double DEFAULT NULL,
  `mar_16_achievement` double DEFAULT NULL,
  `mar_17_target` double DEFAULT NULL,
  `mar_17_achievement` double DEFAULT NULL,
  `mar_18_target` double DEFAULT NULL,
  `mar_18_achievement` double DEFAULT NULL,
  `mar_19_target` double DEFAULT NULL,
  `mar_19_achievement` double DEFAULT NULL,
  `mar_20_target` double DEFAULT NULL,
  `mar_20_achievement` double DEFAULT NULL,
  `mar_21_target` double DEFAULT NULL,
  `mar_21_achievement` double DEFAULT NULL,
  `mar_22_target` double DEFAULT NULL,
  `mar_22_achievement` double DEFAULT NULL,
  `mar_23_target` double DEFAULT NULL,
  `mar_23_achievement` double DEFAULT NULL,
  `mar_24_target` double DEFAULT NULL,
  `mar_24_achievement` double DEFAULT NULL,
  `mar_25_target` double DEFAULT NULL,
  `mar_25_achievement` double DEFAULT NULL,
  `mar_26_target` double DEFAULT NULL,
  `mar_26_achievement` double DEFAULT NULL,
  `mar_27_target` double DEFAULT NULL,
  `mar_27_achievement` double DEFAULT NULL,
  `mar_28_target` double DEFAULT NULL,
  `mar_28_achievement` double DEFAULT NULL,
  `mar_29_target` double DEFAULT NULL,
  `mar_29_achievement` double DEFAULT NULL,
  `mar_30_target` double DEFAULT NULL,
  `mar_30_achievement` double DEFAULT NULL,
  `mar_31_target` double DEFAULT NULL,
  `mar_31_achievement` double DEFAULT NULL,
  `apr_1_target` double DEFAULT NULL,
  `apr_1_achievement` double DEFAULT NULL,
  `apr_2_target` double DEFAULT NULL,
  `apr_2_achievement` double DEFAULT NULL,
  `apr_3_target` double DEFAULT NULL,
  `apr_3_achievement` double DEFAULT NULL,
  `apr_4_target` double DEFAULT NULL,
  `apr_4_achievement` double DEFAULT NULL,
  `apr_5_target` double DEFAULT NULL,
  `apr_5_achievement` double DEFAULT NULL,
  `apr_6_target` double DEFAULT NULL,
  `apr_6_achievement` double DEFAULT NULL,
  `apr_7_target` double DEFAULT NULL,
  `apr_7_achievement` double DEFAULT NULL,
  `apr_8_target` double DEFAULT NULL,
  `apr_8_achievement` double DEFAULT NULL,
  `apr_9_target` double DEFAULT NULL,
  `apr_9_achievement` double DEFAULT NULL,
  `apr_10_target` double DEFAULT NULL,
  `apr_10_achievement` double DEFAULT NULL,
  `apr_11_target` double DEFAULT NULL,
  `apr_11_achievement` double DEFAULT NULL,
  `apr_12_target` double DEFAULT NULL,
  `apr_12_achievement` double DEFAULT NULL,
  `apr_13_target` double DEFAULT NULL,
  `apr_13_achievement` double DEFAULT NULL,
  `apr_14_target` double DEFAULT NULL,
  `apr_14_achievement` double DEFAULT NULL,
  `apr_15_target` double DEFAULT NULL,
  `apr_15_achievement` double DEFAULT NULL,
  `apr_16_target` double DEFAULT NULL,
  `apr_16_achievement` double DEFAULT NULL,
  `apr_17_target` double DEFAULT NULL,
  `apr_17_achievement` double DEFAULT NULL,
  `apr_18_target` double DEFAULT NULL,
  `apr_18_achievement` double DEFAULT NULL,
  `apr_19_target` double DEFAULT NULL,
  `apr_19_achievement` double DEFAULT NULL,
  `apr_20_target` double DEFAULT NULL,
  `apr_20_achievement` double DEFAULT NULL,
  `apr_21_target` double DEFAULT NULL,
  `apr_21_achievement` double DEFAULT NULL,
  `apr_22_target` double DEFAULT NULL,
  `apr_22_achievement` double DEFAULT NULL,
  `apr_23_target` double DEFAULT NULL,
  `apr_23_achievement` double DEFAULT NULL,
  `apr_24_target` double DEFAULT NULL,
  `apr_24_achievement` double DEFAULT NULL,
  `apr_25_target` double DEFAULT NULL,
  `apr_25_achievement` double DEFAULT NULL,
  `apr_26_target` double DEFAULT NULL,
  `apr_26_achievement` double DEFAULT NULL,
  `apr_27_target` double DEFAULT NULL,
  `apr_27_achievement` double DEFAULT NULL,
  `apr_28_target` double DEFAULT NULL,
  `apr_28_achievement` double DEFAULT NULL,
  `apr_29_target` double DEFAULT NULL,
  `apr_29_achievement` double DEFAULT NULL,
  `apr_30_target` double DEFAULT NULL,
  `apr_30_achievement` double DEFAULT NULL,
  `may_1_target` double DEFAULT NULL,
  `may_1_achievement` double DEFAULT NULL,
  `may_2_target` double DEFAULT NULL,
  `may_2_achievement` double DEFAULT NULL,
  `may_3_target` double DEFAULT NULL,
  `may_3_achievement` double DEFAULT NULL,
  `may_4_target` double DEFAULT NULL,
  `may_4_achievement` double DEFAULT NULL,
  `may_5_target` double DEFAULT NULL,
  `may_5_achievement` double DEFAULT NULL,
  `may_6_target` double DEFAULT NULL,
  `may_6_achievement` double DEFAULT NULL,
  `may_7_target` double DEFAULT NULL,
  `may_7_achievement` double DEFAULT NULL,
  `may_8_target` double DEFAULT NULL,
  `may_8_achievement` double DEFAULT NULL,
  `may_9_target` double DEFAULT NULL,
  `may_9_achievement` double DEFAULT NULL,
  `may_10_target` double DEFAULT NULL,
  `may_10_achievement` double DEFAULT NULL,
  `may_11_target` double DEFAULT NULL,
  `may_11_achievement` double DEFAULT NULL,
  `may_12_target` double DEFAULT NULL,
  `may_12_achievement` double DEFAULT NULL,
  `may_13_target` double DEFAULT NULL,
  `may_13_achievement` double DEFAULT NULL,
  `may_14_target` double DEFAULT NULL,
  `may_14_achievement` double DEFAULT NULL,
  `may_15_target` double DEFAULT NULL,
  `may_15_achievement` double DEFAULT NULL,
  `may_16_target` double DEFAULT NULL,
  `may_16_achievement` double DEFAULT NULL,
  `may_17_target` double DEFAULT NULL,
  `may_17_achievement` double DEFAULT NULL,
  `may_18_target` double DEFAULT NULL,
  `may_18_achievement` double DEFAULT NULL,
  `may_19_target` double DEFAULT NULL,
  `may_19_achievement` double DEFAULT NULL,
  `may_20_target` double DEFAULT NULL,
  `may_20_achievement` double DEFAULT NULL,
  `may_21_target` double DEFAULT NULL,
  `may_21_achievement` double DEFAULT NULL,
  `may_22_target` double DEFAULT NULL,
  `may_22_achievement` double DEFAULT NULL,
  `may_23_target` double DEFAULT NULL,
  `may_23_achievement` double DEFAULT NULL,
  `may_24_target` double DEFAULT NULL,
  `may_24_achievement` double DEFAULT NULL,
  `may_25_target` double DEFAULT NULL,
  `may_25_achievement` double DEFAULT NULL,
  `may_26_target` double DEFAULT NULL,
  `may_26_achievement` double DEFAULT NULL,
  `may_27_target` double DEFAULT NULL,
  `may_27_achievement` double DEFAULT NULL,
  `may_28_target` double DEFAULT NULL,
  `may_28_achievement` double DEFAULT NULL,
  `may_29_target` double DEFAULT NULL,
  `may_29_achievement` double DEFAULT NULL,
  `may_30_target` double DEFAULT NULL,
  `may_30_achievement` double DEFAULT NULL,
  `may_31_target` double DEFAULT NULL,
  `may_31_achievement` double DEFAULT NULL,
  `jun_1_target` double DEFAULT NULL,
  `jun_1_achievement` double DEFAULT NULL,
  `jun_2_target` double DEFAULT NULL,
  `jun_2_achievement` double DEFAULT NULL,
  `jun_3_target` double DEFAULT NULL,
  `jun_3_achievement` double DEFAULT NULL,
  `jun_4_target` double DEFAULT NULL,
  `jun_4_achievement` double DEFAULT NULL,
  `jun_5_target` double DEFAULT NULL,
  `jun_5_achievement` double DEFAULT NULL,
  `jun_6_target` double DEFAULT NULL,
  `jun_6_achievement` double DEFAULT NULL,
  `jun_7_target` double DEFAULT NULL,
  `jun_7_achievement` double DEFAULT NULL,
  `jun_8_target` double DEFAULT NULL,
  `jun_8_achievement` double DEFAULT NULL,
  `jun_9_target` double DEFAULT NULL,
  `jun_9_achievement` double DEFAULT NULL,
  `jun_10_target` double DEFAULT NULL,
  `jun_10_achievement` double DEFAULT NULL,
  `jun_11_target` double DEFAULT NULL,
  `jun_11_achievement` double DEFAULT NULL,
  `jun_12_target` double DEFAULT NULL,
  `jun_12_achievement` double DEFAULT NULL,
  `jun_13_target` double DEFAULT NULL,
  `jun_13_achievement` double DEFAULT NULL,
  `jun_14_target` double DEFAULT NULL,
  `jun_14_achievement` double DEFAULT NULL,
  `jun_15_target` double DEFAULT NULL,
  `jun_15_achievement` double DEFAULT NULL,
  `jun_16_target` double DEFAULT NULL,
  `jun_16_achievement` double DEFAULT NULL,
  `jun_17_target` double DEFAULT NULL,
  `jun_17_achievement` double DEFAULT NULL,
  `jun_18_target` double DEFAULT NULL,
  `jun_18_achievement` double DEFAULT NULL,
  `jun_19_target` double DEFAULT NULL,
  `jun_19_achievement` double DEFAULT NULL,
  `jun_20_target` double DEFAULT NULL,
  `jun_20_achievement` double DEFAULT NULL,
  `jun_21_target` double DEFAULT NULL,
  `jun_21_achievement` double DEFAULT NULL,
  `jun_22_target` double DEFAULT NULL,
  `jun_22_achievement` double DEFAULT NULL,
  `jun_23_target` double DEFAULT NULL,
  `jun_23_achievement` double DEFAULT NULL,
  `jun_24_target` double DEFAULT NULL,
  `jun_24_achievement` double DEFAULT NULL,
  `jun_25_target` double DEFAULT NULL,
  `jun_25_achievement` double DEFAULT NULL,
  `jun_26_target` double DEFAULT NULL,
  `jun_26_achievement` double DEFAULT NULL,
  `jun_27_target` double DEFAULT NULL,
  `jun_27_achievement` double DEFAULT NULL,
  `jun_28_target` double DEFAULT NULL,
  `jun_28_achievement` double DEFAULT NULL,
  `jun_29_target` double DEFAULT NULL,
  `jun_29_achievement` double DEFAULT NULL,
  `jun_30_target` double DEFAULT NULL,
  `jun_30_achievement` double DEFAULT NULL,
  `jul_1_target` double DEFAULT NULL,
  `jul_1_achievement` double DEFAULT NULL,
  `jul_2_target` double DEFAULT NULL,
  `jul_2_achievement` double DEFAULT NULL,
  `jul_3_target` double DEFAULT NULL,
  `jul_3_achievement` double DEFAULT NULL,
  `jul_4_target` double DEFAULT NULL,
  `jul_4_achievement` double DEFAULT NULL,
  `jul_5_target` double DEFAULT NULL,
  `jul_5_achievement` double DEFAULT NULL,
  `jul_6_target` double DEFAULT NULL,
  `jul_6_achievement` double DEFAULT NULL,
  `jul_7_target` double DEFAULT NULL,
  `jul_7_achievement` double DEFAULT NULL,
  `jul_8_target` double DEFAULT NULL,
  `jul_8_achievement` double DEFAULT NULL,
  `jul_9_target` double DEFAULT NULL,
  `jul_9_achievement` double DEFAULT NULL,
  `jul_10_target` double DEFAULT NULL,
  `jul_10_achievement` double DEFAULT NULL,
  `jul_11_target` double DEFAULT NULL,
  `jul_11_achievement` double DEFAULT NULL,
  `jul_12_target` double DEFAULT NULL,
  `jul_12_achievement` double DEFAULT NULL,
  `jul_13_target` double DEFAULT NULL,
  `jul_13_achievement` double DEFAULT NULL,
  `jul_14_target` double DEFAULT NULL,
  `jul_14_achievement` double DEFAULT NULL,
  `jul_15_target` double DEFAULT NULL,
  `jul_15_achievement` double DEFAULT NULL,
  `jul_16_target` double DEFAULT NULL,
  `jul_16_achievement` double DEFAULT NULL,
  `jul_17_target` double DEFAULT NULL,
  `jul_17_achievement` double DEFAULT NULL,
  `jul_18_target` double DEFAULT NULL,
  `jul_18_achievement` double DEFAULT NULL,
  `jul_19_target` double DEFAULT NULL,
  `jul_19_achievement` double DEFAULT NULL,
  `jul_20_target` double DEFAULT NULL,
  `jul_20_achievement` double DEFAULT NULL,
  `jul_21_target` double DEFAULT NULL,
  `jul_21_achievement` double DEFAULT NULL,
  `jul_22_target` double DEFAULT NULL,
  `jul_22_achievement` double DEFAULT NULL,
  `jul_23_target` double DEFAULT NULL,
  `jul_23_achievement` double DEFAULT NULL,
  `jul_24_target` double DEFAULT NULL,
  `jul_24_achievement` double DEFAULT NULL,
  `jul_25_target` double DEFAULT NULL,
  `jul_25_achievement` double DEFAULT NULL,
  `jul_26_target` double DEFAULT NULL,
  `jul_26_achievement` double DEFAULT NULL,
  `jul_27_target` double DEFAULT NULL,
  `jul_27_achievement` double DEFAULT NULL,
  `jul_28_target` double DEFAULT NULL,
  `jul_28_achievement` double DEFAULT NULL,
  `jul_29_target` double DEFAULT NULL,
  `jul_29_achievement` double DEFAULT NULL,
  `jul_30_target` double DEFAULT NULL,
  `jul_30_achievement` double DEFAULT NULL,
  `jul_31_target` double DEFAULT NULL,
  `jul_31_achievement` double DEFAULT NULL,
  `aug_1_target` double DEFAULT NULL,
  `aug_1_achievement` double DEFAULT NULL,
  `aug_2_target` double DEFAULT NULL,
  `aug_2_achievement` double DEFAULT NULL,
  `aug_3_target` double DEFAULT NULL,
  `aug_3_achievement` double DEFAULT NULL,
  `aug_4_target` double DEFAULT NULL,
  `aug_4_achievement` double DEFAULT NULL,
  `aug_5_target` double DEFAULT NULL,
  `aug_5_achievement` double DEFAULT NULL,
  `aug_6_target` double DEFAULT NULL,
  `aug_6_achievement` double DEFAULT NULL,
  `aug_7_target` double DEFAULT NULL,
  `aug_7_achievement` double DEFAULT NULL,
  `aug_8_target` double DEFAULT NULL,
  `aug_8_achievement` double DEFAULT NULL,
  `aug_9_target` double DEFAULT NULL,
  `aug_9_achievement` double DEFAULT NULL,
  `aug_10_target` double DEFAULT NULL,
  `aug_10_achievement` double DEFAULT NULL,
  `aug_11_target` double DEFAULT NULL,
  `aug_11_achievement` double DEFAULT NULL,
  `aug_12_target` double DEFAULT NULL,
  `aug_12_achievement` double DEFAULT NULL,
  `aug_13_target` double DEFAULT NULL,
  `aug_13_achievement` double DEFAULT NULL,
  `aug_14_target` double DEFAULT NULL,
  `aug_14_achievement` double DEFAULT NULL,
  `aug_15_target` double DEFAULT NULL,
  `aug_15_achievement` double DEFAULT NULL,
  `aug_16_target` double DEFAULT NULL,
  `aug_16_achievement` double DEFAULT NULL,
  `aug_17_target` double DEFAULT NULL,
  `aug_17_achievement` double DEFAULT NULL,
  `aug_18_target` double DEFAULT NULL,
  `aug_18_achievement` double DEFAULT NULL,
  `aug_19_target` double DEFAULT NULL,
  `aug_19_achievement` double DEFAULT NULL,
  `aug_20_target` double DEFAULT NULL,
  `aug_20_achievement` double DEFAULT NULL,
  `aug_21_target` double DEFAULT NULL,
  `aug_21_achievement` double DEFAULT NULL,
  `aug_22_target` double DEFAULT NULL,
  `aug_22_achievement` double DEFAULT NULL,
  `aug_23_target` double DEFAULT NULL,
  `aug_23_achievement` double DEFAULT NULL,
  `aug_24_target` double DEFAULT NULL,
  `aug_24_achievement` double DEFAULT NULL,
  `aug_25_target` double DEFAULT NULL,
  `aug_25_achievement` double DEFAULT NULL,
  `aug_26_target` double DEFAULT NULL,
  `aug_26_achievement` double DEFAULT NULL,
  `aug_27_target` double DEFAULT NULL,
  `aug_27_achievement` double DEFAULT NULL,
  `aug_28_target` double DEFAULT NULL,
  `aug_28_achievement` double DEFAULT NULL,
  `aug_29_target` double DEFAULT NULL,
  `aug_29_achievement` double DEFAULT NULL,
  `aug_30_target` double DEFAULT NULL,
  `aug_30_achievement` double DEFAULT NULL,
  `aug_31_target` double DEFAULT NULL,
  `aug_31_achievement` double DEFAULT NULL,
  `sep_1_target` double DEFAULT NULL,
  `sep_1_achievement` double DEFAULT NULL,
  `sep_2_target` double DEFAULT NULL,
  `sep_2_achievement` double DEFAULT NULL,
  `sep_3_target` double DEFAULT NULL,
  `sep_3_achievement` double DEFAULT NULL,
  `sep_4_target` double DEFAULT NULL,
  `sep_4_achievement` double DEFAULT NULL,
  `sep_5_target` double DEFAULT NULL,
  `sep_5_achievement` double DEFAULT NULL,
  `sep_6_target` double DEFAULT NULL,
  `sep_6_achievement` double DEFAULT NULL,
  `sep_7_target` double DEFAULT NULL,
  `sep_7_achievement` double DEFAULT NULL,
  `sep_8_target` double DEFAULT NULL,
  `sep_8_achievement` double DEFAULT NULL,
  `sep_9_target` double DEFAULT NULL,
  `sep_9_achievement` double DEFAULT NULL,
  `sep_10_target` double DEFAULT NULL,
  `sep_10_achievement` double DEFAULT NULL,
  `sep_11_target` double DEFAULT NULL,
  `sep_11_achievement` double DEFAULT NULL,
  `sep_12_target` double DEFAULT NULL,
  `sep_12_achievement` double DEFAULT NULL,
  `sep_13_target` double DEFAULT NULL,
  `sep_13_achievement` double DEFAULT NULL,
  `sep_14_target` double DEFAULT NULL,
  `sep_14_achievement` double DEFAULT NULL,
  `sep_15_target` double DEFAULT NULL,
  `sep_15_achievement` double DEFAULT NULL,
  `sep_16_target` double DEFAULT NULL,
  `sep_16_achievement` double DEFAULT NULL,
  `sep_17_target` double DEFAULT NULL,
  `sep_17_achievement` double DEFAULT NULL,
  `sep_18_target` double DEFAULT NULL,
  `sep_18_achievement` double DEFAULT NULL,
  `sep_19_target` double DEFAULT NULL,
  `sep_19_achievement` double DEFAULT NULL,
  `sep_20_target` double DEFAULT NULL,
  `sep_20_achievement` double DEFAULT NULL,
  `sep_21_target` double DEFAULT NULL,
  `sep_21_achievement` double DEFAULT NULL,
  `sep_22_target` double DEFAULT NULL,
  `sep_22_achievement` double DEFAULT NULL,
  `sep_23_target` double DEFAULT NULL,
  `sep_23_achievement` double DEFAULT NULL,
  `sep_24_target` double DEFAULT NULL,
  `sep_24_achievement` double DEFAULT NULL,
  `sep_25_target` double DEFAULT NULL,
  `sep_25_achievement` double DEFAULT NULL,
  `sep_26_target` double DEFAULT NULL,
  `sep_26_achievement` double DEFAULT NULL,
  `sep_27_target` double DEFAULT NULL,
  `sep_27_achievement` double DEFAULT NULL,
  `sep_28_target` double DEFAULT NULL,
  `sep_28_achievement` double DEFAULT NULL,
  `sep_29_target` double DEFAULT NULL,
  `sep_29_achievement` double DEFAULT NULL,
  `sep_30_target` double DEFAULT NULL,
  `sep_30_achievement` double DEFAULT NULL,
  `oct_1_target` double DEFAULT NULL,
  `oct_1_achievement` double DEFAULT NULL,
  `oct_2_target` double DEFAULT NULL,
  `oct_2_achievement` double DEFAULT NULL,
  `oct_3_target` double DEFAULT NULL,
  `oct_3_achievement` double DEFAULT NULL,
  `oct_4_target` double DEFAULT NULL,
  `oct_4_achievement` double DEFAULT NULL,
  `oct_5_target` double DEFAULT NULL,
  `oct_5_achievement` double DEFAULT NULL,
  `oct_6_target` double DEFAULT NULL,
  `oct_6_achievement` double DEFAULT NULL,
  `oct_7_target` double DEFAULT NULL,
  `oct_7_achievement` double DEFAULT NULL,
  `oct_8_target` double DEFAULT NULL,
  `oct_8_achievement` double DEFAULT NULL,
  `oct_9_target` double DEFAULT NULL,
  `oct_9_achievement` double DEFAULT NULL,
  `oct_10_target` double DEFAULT NULL,
  `oct_10_achievement` double DEFAULT NULL,
  `oct_11_target` double DEFAULT NULL,
  `oct_11_achievement` double DEFAULT NULL,
  `oct_12_target` double DEFAULT NULL,
  `oct_12_achievement` double DEFAULT NULL,
  `oct_13_target` double DEFAULT NULL,
  `oct_13_achievement` double DEFAULT NULL,
  `oct_14_target` double DEFAULT NULL,
  `oct_14_achievement` double DEFAULT NULL,
  `oct_15_target` double DEFAULT NULL,
  `oct_15_achievement` double DEFAULT NULL,
  `oct_16_target` double DEFAULT NULL,
  `oct_16_achievement` double DEFAULT NULL,
  `oct_17_target` double DEFAULT NULL,
  `oct_17_achievement` double DEFAULT NULL,
  `oct_18_target` double DEFAULT NULL,
  `oct_18_achievement` double DEFAULT NULL,
  `oct_19_target` double DEFAULT NULL,
  `oct_19_achievement` double DEFAULT NULL,
  `oct_20_target` double DEFAULT NULL,
  `oct_20_achievement` double DEFAULT NULL,
  `oct_21_target` double DEFAULT NULL,
  `oct_21_achievement` double DEFAULT NULL,
  `oct_22_target` double DEFAULT NULL,
  `oct_22_achievement` double DEFAULT NULL,
  `oct_23_target` double DEFAULT NULL,
  `oct_23_achievement` double DEFAULT NULL,
  `oct_24_target` double DEFAULT NULL,
  `oct_24_achievement` double DEFAULT NULL,
  `oct_25_target` double DEFAULT NULL,
  `oct_25_achievement` double DEFAULT NULL,
  `oct_26_target` double DEFAULT NULL,
  `oct_26_achievement` double DEFAULT NULL,
  `oct_27_target` double DEFAULT NULL,
  `oct_27_achievement` double DEFAULT NULL,
  `oct_28_target` double DEFAULT NULL,
  `oct_28_achievement` double DEFAULT NULL,
  `oct_29_target` double DEFAULT NULL,
  `oct_29_achievement` double DEFAULT NULL,
  `oct_30_target` double DEFAULT NULL,
  `oct_30_achievement` double DEFAULT NULL,
  `oct_31_target` double DEFAULT NULL,
  `oct_31_achievement` double DEFAULT NULL,
  `nov_1_target` double DEFAULT NULL,
  `nov_1_achievement` double DEFAULT NULL,
  `nov_2_target` double DEFAULT NULL,
  `nov_2_achievement` double DEFAULT NULL,
  `nov_3_target` double DEFAULT NULL,
  `nov_3_achievement` double DEFAULT NULL,
  `nov_4_target` double DEFAULT NULL,
  `nov_4_achievement` double DEFAULT NULL,
  `nov_5_target` double DEFAULT NULL,
  `nov_5_achievement` double DEFAULT NULL,
  `nov_6_target` double DEFAULT NULL,
  `nov_6_achievement` double DEFAULT NULL,
  `nov_7_target` double DEFAULT NULL,
  `nov_7_achievement` double DEFAULT NULL,
  `nov_8_target` double DEFAULT NULL,
  `nov_8_achievement` double DEFAULT NULL,
  `nov_9_target` double DEFAULT NULL,
  `nov_9_achievement` double DEFAULT NULL,
  `nov_10_target` double DEFAULT NULL,
  `nov_10_achievement` double DEFAULT NULL,
  `nov_11_target` double DEFAULT NULL,
  `nov_11_achievement` double DEFAULT NULL,
  `nov_12_target` double DEFAULT NULL,
  `nov_12_achievement` double DEFAULT NULL,
  `nov_13_target` double DEFAULT NULL,
  `nov_13_achievement` double DEFAULT NULL,
  `nov_14_target` double DEFAULT NULL,
  `nov_14_achievement` double DEFAULT NULL,
  `nov_15_target` double DEFAULT NULL,
  `nov_15_achievement` double DEFAULT NULL,
  `nov_16_target` double DEFAULT NULL,
  `nov_16_achievement` double DEFAULT NULL,
  `nov_17_target` double DEFAULT NULL,
  `nov_17_achievement` double DEFAULT NULL,
  `nov_18_target` double DEFAULT NULL,
  `nov_18_achievement` double DEFAULT NULL,
  `nov_19_target` double DEFAULT NULL,
  `nov_19_achievement` double DEFAULT NULL,
  `nov_20_target` double DEFAULT NULL,
  `nov_20_achievement` double DEFAULT NULL,
  `nov_21_target` double DEFAULT NULL,
  `nov_21_achievement` double DEFAULT NULL,
  `nov_22_target` double DEFAULT NULL,
  `nov_22_achievement` double DEFAULT NULL,
  `nov_23_target` double DEFAULT NULL,
  `nov_23_achievement` double DEFAULT NULL,
  `nov_24_target` double DEFAULT NULL,
  `nov_24_achievement` double DEFAULT NULL,
  `nov_25_target` double DEFAULT NULL,
  `nov_25_achievement` double DEFAULT NULL,
  `nov_26_target` double DEFAULT NULL,
  `nov_26_achievement` double DEFAULT NULL,
  `nov_27_target` double DEFAULT NULL,
  `nov_27_achievement` double DEFAULT NULL,
  `nov_28_target` double DEFAULT NULL,
  `nov_28_achievement` double DEFAULT NULL,
  `nov_29_target` double DEFAULT NULL,
  `nov_29_achievement` double DEFAULT NULL,
  `nov_30_target` double DEFAULT NULL,
  `nov_30_achievement` double DEFAULT NULL,
  `dec_1_target` double DEFAULT NULL,
  `dec_1_achievement` double DEFAULT NULL,
  `dec_2_target` double DEFAULT NULL,
  `dec_2_achievement` double DEFAULT NULL,
  `dec_3_target` double DEFAULT NULL,
  `dec_3_achievement` double DEFAULT NULL,
  `dec_4_target` double DEFAULT NULL,
  `dec_4_achievement` double DEFAULT NULL,
  `dec_5_target` double DEFAULT NULL,
  `dec_5_achievement` double DEFAULT NULL,
  `dec_6_target` double DEFAULT NULL,
  `dec_6_achievement` double DEFAULT NULL,
  `dec_7_target` double DEFAULT NULL,
  `dec_7_achievement` double DEFAULT NULL,
  `dec_8_target` double DEFAULT NULL,
  `dec_8_achievement` double DEFAULT NULL,
  `dec_9_target` double DEFAULT NULL,
  `dec_9_achievement` double DEFAULT NULL,
  `dec_10_target` double DEFAULT NULL,
  `dec_10_achievement` double DEFAULT NULL,
  `dec_11_target` double DEFAULT NULL,
  `dec_11_achievement` double DEFAULT NULL,
  `dec_12_target` double DEFAULT NULL,
  `dec_12_achievement` double DEFAULT NULL,
  `dec_13_target` double DEFAULT NULL,
  `dec_13_achievement` double DEFAULT NULL,
  `dec_14_target` double DEFAULT NULL,
  `dec_14_achievement` double DEFAULT NULL,
  `dec_15_target` double DEFAULT NULL,
  `dec_15_achievement` double DEFAULT NULL,
  `dec_16_target` double DEFAULT NULL,
  `dec_16_achievement` double DEFAULT NULL,
  `dec_17_target` double DEFAULT NULL,
  `dec_17_achievement` double DEFAULT NULL,
  `dec_18_target` double DEFAULT NULL,
  `dec_18_achievement` double DEFAULT NULL,
  `dec_19_target` double DEFAULT NULL,
  `dec_19_achievement` double DEFAULT NULL,
  `dec_20_target` double DEFAULT NULL,
  `dec_20_achievement` double DEFAULT NULL,
  `dec_21_target` double DEFAULT NULL,
  `dec_21_achievement` double DEFAULT NULL,
  `dec_22_target` double DEFAULT NULL,
  `dec_22_achievement` double DEFAULT NULL,
  `dec_23_target` double DEFAULT NULL,
  `dec_23_achievement` double DEFAULT NULL,
  `dec_24_target` double DEFAULT NULL,
  `dec_24_achievement` double DEFAULT NULL,
  `dec_25_target` double DEFAULT NULL,
  `dec_25_achievement` double DEFAULT NULL,
  `dec_26_target` double DEFAULT NULL,
  `dec_26_achievement` double DEFAULT NULL,
  `dec_27_target` double DEFAULT NULL,
  `dec_27_achievement` double DEFAULT NULL,
  `dec_28_target` double DEFAULT NULL,
  `dec_28_achievement` double DEFAULT NULL,
  `dec_29_target` double DEFAULT NULL,
  `dec_29_achievement` double DEFAULT NULL,
  `dec_30_target` double DEFAULT NULL,
  `dec_30_achievement` double DEFAULT NULL,
  `dec_31_target` double DEFAULT NULL,
  `dec_31_achievement` double DEFAULT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `jan_prev_y_target` double DEFAULT NULL,
  `jan_prev_y_achievement` double DEFAULT NULL,
  `feb_prev_y_target` double DEFAULT NULL,
  `feb_prev_y_achievement` double DEFAULT NULL,
  `mar_prev_y_target` double DEFAULT NULL,
  `mar_prev_y_achievement` double DEFAULT NULL,
  `apr_prev_y_target` double DEFAULT NULL,
  `apr_prev_y_achievement` double DEFAULT NULL,
  `may_prev_y_target` double DEFAULT NULL,
  `may_prev_y_achievement` double DEFAULT NULL,
  `jun_prev_y_target` double DEFAULT NULL,
  `jun_prev_y_achievement` double DEFAULT NULL,
  `jul_prev_y_target` double DEFAULT NULL,
  `jul_prev_y_achievement` double DEFAULT NULL,
  `aug_prev_y_target` double DEFAULT NULL,
  `aug_prev_y_achievement` double DEFAULT NULL,
  `sep_prev_y_target` double DEFAULT NULL,
  `sep_prev_y_achievement` double DEFAULT NULL,
  `oct_prev_y_target` double DEFAULT NULL,
  `oct_prev_y_achievement` double DEFAULT NULL,
  `nov_prev_y_target` double DEFAULT NULL,
  `nov_prev_y_achievement` double DEFAULT NULL,
  `dec_prev_y_target` double DEFAULT NULL,
  `dec_prev_y_achievement` double DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `self_appraisal_product_wise`
--

CREATE TABLE `self_appraisal_product_wise` (
  `sl_no` bigint(15) NOT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `emp_code` varchar(50) NOT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `prod_code` varchar(50) NOT NULL,
  `jan_1_target` double DEFAULT NULL,
  `jan_1_achievement` double DEFAULT NULL,
  `jan_2_target` double DEFAULT NULL,
  `jan_2_achievement` double DEFAULT NULL,
  `jan_3_target` double DEFAULT NULL,
  `jan_3_achievement` double DEFAULT NULL,
  `jan_4_target` double DEFAULT NULL,
  `jan_4_achievement` double DEFAULT NULL,
  `jan_5_target` double DEFAULT NULL,
  `jan_5_achievement` double DEFAULT NULL,
  `jan_6_target` double DEFAULT NULL,
  `jan_6_achievement` double DEFAULT NULL,
  `jan_7_target` double DEFAULT NULL,
  `jan_7_achievement` double DEFAULT NULL,
  `jan_8_target` double DEFAULT NULL,
  `jan_8_achievement` double DEFAULT NULL,
  `jan_9_target` double DEFAULT NULL,
  `jan_9_achievement` double DEFAULT NULL,
  `jan_10_target` double DEFAULT NULL,
  `jan_10_achievement` double DEFAULT NULL,
  `jan_11_target` double DEFAULT NULL,
  `jan_11_achievement` double DEFAULT NULL,
  `jan_12_target` double DEFAULT NULL,
  `jan_12_achievement` double DEFAULT NULL,
  `jan_13_target` double DEFAULT NULL,
  `jan_13_achievement` double DEFAULT NULL,
  `jan_14_target` double DEFAULT NULL,
  `jan_14_achievement` double DEFAULT NULL,
  `jan_15_target` double DEFAULT NULL,
  `jan_15_achievement` double DEFAULT NULL,
  `jan_16_target` double DEFAULT NULL,
  `jan_16_achievement` double DEFAULT NULL,
  `jan_17_target` double DEFAULT NULL,
  `jan_17_achievement` double DEFAULT NULL,
  `jan_18_target` double DEFAULT NULL,
  `jan_18_achievement` double DEFAULT NULL,
  `jan_19_target` double DEFAULT NULL,
  `jan_19_achievement` double DEFAULT NULL,
  `jan_20_target` double DEFAULT NULL,
  `jan_20_achievement` double DEFAULT NULL,
  `jan_21_target` double DEFAULT NULL,
  `jan_21_achievement` double DEFAULT NULL,
  `jan_22_target` double DEFAULT NULL,
  `jan_22_achievement` double DEFAULT NULL,
  `jan_23_target` double DEFAULT NULL,
  `jan_23_achievement` double DEFAULT NULL,
  `jan_24_target` double DEFAULT NULL,
  `jan_24_achievement` double DEFAULT NULL,
  `jan_25_target` double DEFAULT NULL,
  `jan_25_achievement` double DEFAULT NULL,
  `jan_26_target` double DEFAULT NULL,
  `jan_26_achievement` double DEFAULT NULL,
  `jan_27_target` double DEFAULT NULL,
  `jan_27_achievement` double DEFAULT NULL,
  `jan_28_target` double DEFAULT NULL,
  `jan_28_achievement` double DEFAULT NULL,
  `jan_29_target` double DEFAULT NULL,
  `jan_29_achievement` double DEFAULT NULL,
  `jan_30_target` double DEFAULT NULL,
  `jan_30_achievement` double DEFAULT NULL,
  `jan_31_target` double DEFAULT NULL,
  `jan_31_achievement` double DEFAULT NULL,
  `feb_1_target` double DEFAULT NULL,
  `feb_1_achievement` double DEFAULT NULL,
  `feb_2_target` double DEFAULT NULL,
  `feb_2_achievement` double DEFAULT NULL,
  `feb_3_target` double DEFAULT NULL,
  `feb_3_achievement` double DEFAULT NULL,
  `feb_4_target` double DEFAULT NULL,
  `feb_4_achievement` double DEFAULT NULL,
  `feb_5_target` double DEFAULT NULL,
  `feb_5_achievement` double DEFAULT NULL,
  `feb_6_target` double DEFAULT NULL,
  `feb_6_achievement` double DEFAULT NULL,
  `feb_7_target` double DEFAULT NULL,
  `feb_7_achievement` double DEFAULT NULL,
  `feb_8_target` double DEFAULT NULL,
  `feb_8_achievement` double DEFAULT NULL,
  `feb_9_target` double DEFAULT NULL,
  `feb_9_achievement` double DEFAULT NULL,
  `feb_10_target` double DEFAULT NULL,
  `feb_10_achievement` double DEFAULT NULL,
  `feb_11_target` double DEFAULT NULL,
  `feb_11_achievement` double DEFAULT NULL,
  `feb_12_target` double DEFAULT NULL,
  `feb_12_achievement` double DEFAULT NULL,
  `feb_13_target` double DEFAULT NULL,
  `feb_13_achievement` double DEFAULT NULL,
  `feb_14_target` double DEFAULT NULL,
  `feb_14_achievement` double DEFAULT NULL,
  `feb_15_target` double DEFAULT NULL,
  `feb_15_achievement` double DEFAULT NULL,
  `feb_16_target` double DEFAULT NULL,
  `feb_16_achievement` double DEFAULT NULL,
  `feb_17_target` double DEFAULT NULL,
  `feb_17_achievement` double DEFAULT NULL,
  `feb_18_target` double DEFAULT NULL,
  `feb_18_achievement` double DEFAULT NULL,
  `feb_19_target` double DEFAULT NULL,
  `feb_19_achievement` double DEFAULT NULL,
  `feb_20_target` double DEFAULT NULL,
  `feb_20_achievement` double DEFAULT NULL,
  `feb_21_target` double DEFAULT NULL,
  `feb_21_achievement` double DEFAULT NULL,
  `feb_22_target` double DEFAULT NULL,
  `feb_22_achievement` double DEFAULT NULL,
  `feb_23_target` double DEFAULT NULL,
  `feb_23_achievement` double DEFAULT NULL,
  `feb_24_target` double DEFAULT NULL,
  `feb_24_achievement` double DEFAULT NULL,
  `feb_25_target` double DEFAULT NULL,
  `feb_25_achievement` double DEFAULT NULL,
  `feb_26_target` double DEFAULT NULL,
  `feb_26_achievement` double DEFAULT NULL,
  `feb_27_target` double DEFAULT NULL,
  `feb_27_achievement` double DEFAULT NULL,
  `feb_28_target` double DEFAULT NULL,
  `feb_28_achievement` double DEFAULT NULL,
  `feb_29_target` double DEFAULT NULL,
  `feb_29_achievement` double DEFAULT NULL,
  `mar_1_target` double DEFAULT NULL,
  `mar_1_achievement` double DEFAULT NULL,
  `mar_2_target` double DEFAULT NULL,
  `mar_2_achievement` double DEFAULT NULL,
  `mar_3_target` double DEFAULT NULL,
  `mar_3_achievement` double DEFAULT NULL,
  `mar_4_target` double DEFAULT NULL,
  `mar_4_achievement` double DEFAULT NULL,
  `mar_5_target` double DEFAULT NULL,
  `mar_5_achievement` double DEFAULT NULL,
  `mar_6_target` double DEFAULT NULL,
  `mar_6_achievement` double DEFAULT NULL,
  `mar_7_target` double DEFAULT NULL,
  `mar_7_achievement` double DEFAULT NULL,
  `mar_8_target` double DEFAULT NULL,
  `mar_8_achievement` double DEFAULT NULL,
  `mar_9_target` double DEFAULT NULL,
  `mar_9_achievement` double DEFAULT NULL,
  `mar_10_target` double DEFAULT NULL,
  `mar_10_achievement` double DEFAULT NULL,
  `mar_11_target` double DEFAULT NULL,
  `mar_11_achievement` double DEFAULT NULL,
  `mar_12_target` double DEFAULT NULL,
  `mar_12_achievement` double DEFAULT NULL,
  `mar_13_target` double DEFAULT NULL,
  `mar_13_achievement` double DEFAULT NULL,
  `mar_14_target` double DEFAULT NULL,
  `mar_14_achievement` double DEFAULT NULL,
  `mar_15_target` double DEFAULT NULL,
  `mar_15_achievement` double DEFAULT NULL,
  `mar_16_target` double DEFAULT NULL,
  `mar_16_achievement` double DEFAULT NULL,
  `mar_17_target` double DEFAULT NULL,
  `mar_17_achievement` double DEFAULT NULL,
  `mar_18_target` double DEFAULT NULL,
  `mar_18_achievement` double DEFAULT NULL,
  `mar_19_target` double DEFAULT NULL,
  `mar_19_achievement` double DEFAULT NULL,
  `mar_20_target` double DEFAULT NULL,
  `mar_20_achievement` double DEFAULT NULL,
  `mar_21_target` double DEFAULT NULL,
  `mar_21_achievement` double DEFAULT NULL,
  `mar_22_target` double DEFAULT NULL,
  `mar_22_achievement` double DEFAULT NULL,
  `mar_23_target` double DEFAULT NULL,
  `mar_23_achievement` double DEFAULT NULL,
  `mar_24_target` double DEFAULT NULL,
  `mar_24_achievement` double DEFAULT NULL,
  `mar_25_target` double DEFAULT NULL,
  `mar_25_achievement` double DEFAULT NULL,
  `mar_26_target` double DEFAULT NULL,
  `mar_26_achievement` double DEFAULT NULL,
  `mar_27_target` double DEFAULT NULL,
  `mar_27_achievement` double DEFAULT NULL,
  `mar_28_target` double DEFAULT NULL,
  `mar_28_achievement` double DEFAULT NULL,
  `mar_29_target` double DEFAULT NULL,
  `mar_29_achievement` double DEFAULT NULL,
  `mar_30_target` double DEFAULT NULL,
  `mar_30_achievement` double DEFAULT NULL,
  `mar_31_target` double DEFAULT NULL,
  `mar_31_achievement` double DEFAULT NULL,
  `apr_1_target` double DEFAULT NULL,
  `apr_1_achievement` double DEFAULT NULL,
  `apr_2_target` double DEFAULT NULL,
  `apr_2_achievement` double DEFAULT NULL,
  `apr_3_target` double DEFAULT NULL,
  `apr_3_achievement` double DEFAULT NULL,
  `apr_4_target` double DEFAULT NULL,
  `apr_4_achievement` double DEFAULT NULL,
  `apr_5_target` double DEFAULT NULL,
  `apr_5_achievement` double DEFAULT NULL,
  `apr_6_target` double DEFAULT NULL,
  `apr_6_achievement` double DEFAULT NULL,
  `apr_7_target` double DEFAULT NULL,
  `apr_7_achievement` double DEFAULT NULL,
  `apr_8_target` double DEFAULT NULL,
  `apr_8_achievement` double DEFAULT NULL,
  `apr_9_target` double DEFAULT NULL,
  `apr_9_achievement` double DEFAULT NULL,
  `apr_10_target` double DEFAULT NULL,
  `apr_10_achievement` double DEFAULT NULL,
  `apr_11_target` double DEFAULT NULL,
  `apr_11_achievement` double DEFAULT NULL,
  `apr_12_target` double DEFAULT NULL,
  `apr_12_achievement` double DEFAULT NULL,
  `apr_13_target` double DEFAULT NULL,
  `apr_13_achievement` double DEFAULT NULL,
  `apr_14_target` double DEFAULT NULL,
  `apr_14_achievement` double DEFAULT NULL,
  `apr_15_target` double DEFAULT NULL,
  `apr_15_achievement` double DEFAULT NULL,
  `apr_16_target` double DEFAULT NULL,
  `apr_16_achievement` double DEFAULT NULL,
  `apr_17_target` double DEFAULT NULL,
  `apr_17_achievement` double DEFAULT NULL,
  `apr_18_target` double DEFAULT NULL,
  `apr_18_achievement` double DEFAULT NULL,
  `apr_19_target` double DEFAULT NULL,
  `apr_19_achievement` double DEFAULT NULL,
  `apr_20_target` double DEFAULT NULL,
  `apr_20_achievement` double DEFAULT NULL,
  `apr_21_target` double DEFAULT NULL,
  `apr_21_achievement` double DEFAULT NULL,
  `apr_22_target` double DEFAULT NULL,
  `apr_22_achievement` double DEFAULT NULL,
  `apr_23_target` double DEFAULT NULL,
  `apr_23_achievement` double DEFAULT NULL,
  `apr_24_target` double DEFAULT NULL,
  `apr_24_achievement` double DEFAULT NULL,
  `apr_25_target` double DEFAULT NULL,
  `apr_25_achievement` double DEFAULT NULL,
  `apr_26_target` double DEFAULT NULL,
  `apr_26_achievement` double DEFAULT NULL,
  `apr_27_target` double DEFAULT NULL,
  `apr_27_achievement` double DEFAULT NULL,
  `apr_28_target` double DEFAULT NULL,
  `apr_28_achievement` double DEFAULT NULL,
  `apr_29_target` double DEFAULT NULL,
  `apr_29_achievement` double DEFAULT NULL,
  `apr_30_target` double DEFAULT NULL,
  `apr_30_achievement` double DEFAULT NULL,
  `may_1_target` double DEFAULT NULL,
  `may_1_achievement` double DEFAULT NULL,
  `may_2_target` double DEFAULT NULL,
  `may_2_achievement` double DEFAULT NULL,
  `may_3_target` double DEFAULT NULL,
  `may_3_achievement` double DEFAULT NULL,
  `may_4_target` double DEFAULT NULL,
  `may_4_achievement` double DEFAULT NULL,
  `may_5_target` double DEFAULT NULL,
  `may_5_achievement` double DEFAULT NULL,
  `may_6_target` double DEFAULT NULL,
  `may_6_achievement` double DEFAULT NULL,
  `may_7_target` double DEFAULT NULL,
  `may_7_achievement` double DEFAULT NULL,
  `may_8_target` double DEFAULT NULL,
  `may_8_achievement` double DEFAULT NULL,
  `may_9_target` double DEFAULT NULL,
  `may_9_achievement` double DEFAULT NULL,
  `may_10_target` double DEFAULT NULL,
  `may_10_achievement` double DEFAULT NULL,
  `may_11_target` double DEFAULT NULL,
  `may_11_achievement` double DEFAULT NULL,
  `may_12_target` double DEFAULT NULL,
  `may_12_achievement` double DEFAULT NULL,
  `may_13_target` double DEFAULT NULL,
  `may_13_achievement` double DEFAULT NULL,
  `may_14_target` double DEFAULT NULL,
  `may_14_achievement` double DEFAULT NULL,
  `may_15_target` double DEFAULT NULL,
  `may_15_achievement` double DEFAULT NULL,
  `may_16_target` double DEFAULT NULL,
  `may_16_achievement` double DEFAULT NULL,
  `may_17_target` double DEFAULT NULL,
  `may_17_achievement` double DEFAULT NULL,
  `may_18_target` double DEFAULT NULL,
  `may_18_achievement` double DEFAULT NULL,
  `may_19_target` double DEFAULT NULL,
  `may_19_achievement` double DEFAULT NULL,
  `may_20_target` double DEFAULT NULL,
  `may_20_achievement` double DEFAULT NULL,
  `may_21_target` double DEFAULT NULL,
  `may_21_achievement` double DEFAULT NULL,
  `may_22_target` double DEFAULT NULL,
  `may_22_achievement` double DEFAULT NULL,
  `may_23_target` double DEFAULT NULL,
  `may_23_achievement` double DEFAULT NULL,
  `may_24_target` double DEFAULT NULL,
  `may_24_achievement` double DEFAULT NULL,
  `may_25_target` double DEFAULT NULL,
  `may_25_achievement` double DEFAULT NULL,
  `may_26_target` double DEFAULT NULL,
  `may_26_achievement` double DEFAULT NULL,
  `may_27_target` double DEFAULT NULL,
  `may_27_achievement` double DEFAULT NULL,
  `may_28_target` double DEFAULT NULL,
  `may_28_achievement` double DEFAULT NULL,
  `may_29_target` double DEFAULT NULL,
  `may_29_achievement` double DEFAULT NULL,
  `may_30_target` double DEFAULT NULL,
  `may_30_achievement` double DEFAULT NULL,
  `may_31_target` double DEFAULT NULL,
  `may_31_achievement` double DEFAULT NULL,
  `jun_1_target` double DEFAULT NULL,
  `jun_1_achievement` double DEFAULT NULL,
  `jun_2_target` double DEFAULT NULL,
  `jun_2_achievement` double DEFAULT NULL,
  `jun_3_target` double DEFAULT NULL,
  `jun_3_achievement` double DEFAULT NULL,
  `jun_4_target` double DEFAULT NULL,
  `jun_4_achievement` double DEFAULT NULL,
  `jun_5_target` double DEFAULT NULL,
  `jun_5_achievement` double DEFAULT NULL,
  `jun_6_target` double DEFAULT NULL,
  `jun_6_achievement` double DEFAULT NULL,
  `jun_7_target` double DEFAULT NULL,
  `jun_7_achievement` double DEFAULT NULL,
  `jun_8_target` double DEFAULT NULL,
  `jun_8_achievement` double DEFAULT NULL,
  `jun_9_target` double DEFAULT NULL,
  `jun_9_achievement` double DEFAULT NULL,
  `jun_10_target` double DEFAULT NULL,
  `jun_10_achievement` double DEFAULT NULL,
  `jun_11_target` double DEFAULT NULL,
  `jun_11_achievement` double DEFAULT NULL,
  `jun_12_target` double DEFAULT NULL,
  `jun_12_achievement` double DEFAULT NULL,
  `jun_13_target` double DEFAULT NULL,
  `jun_13_achievement` double DEFAULT NULL,
  `jun_14_target` double DEFAULT NULL,
  `jun_14_achievement` double DEFAULT NULL,
  `jun_15_target` double DEFAULT NULL,
  `jun_15_achievement` double DEFAULT NULL,
  `jun_16_target` double DEFAULT NULL,
  `jun_16_achievement` double DEFAULT NULL,
  `jun_17_target` double DEFAULT NULL,
  `jun_17_achievement` double DEFAULT NULL,
  `jun_18_target` double DEFAULT NULL,
  `jun_18_achievement` double DEFAULT NULL,
  `jun_19_target` double DEFAULT NULL,
  `jun_19_achievement` double DEFAULT NULL,
  `jun_20_target` double DEFAULT NULL,
  `jun_20_achievement` double DEFAULT NULL,
  `jun_21_target` double DEFAULT NULL,
  `jun_21_achievement` double DEFAULT NULL,
  `jun_22_target` double DEFAULT NULL,
  `jun_22_achievement` double DEFAULT NULL,
  `jun_23_target` double DEFAULT NULL,
  `jun_23_achievement` double DEFAULT NULL,
  `jun_24_target` double DEFAULT NULL,
  `jun_24_achievement` double DEFAULT NULL,
  `jun_25_target` double DEFAULT NULL,
  `jun_25_achievement` double DEFAULT NULL,
  `jun_26_target` double DEFAULT NULL,
  `jun_26_achievement` double DEFAULT NULL,
  `jun_27_target` double DEFAULT NULL,
  `jun_27_achievement` double DEFAULT NULL,
  `jun_28_target` double DEFAULT NULL,
  `jun_28_achievement` double DEFAULT NULL,
  `jun_29_target` double DEFAULT NULL,
  `jun_29_achievement` double DEFAULT NULL,
  `jun_30_target` double DEFAULT NULL,
  `jun_30_achievement` double DEFAULT NULL,
  `jul_1_target` double DEFAULT NULL,
  `jul_1_achievement` double DEFAULT NULL,
  `jul_2_target` double DEFAULT NULL,
  `jul_2_achievement` double DEFAULT NULL,
  `jul_3_target` double DEFAULT NULL,
  `jul_3_achievement` double DEFAULT NULL,
  `jul_4_target` double DEFAULT NULL,
  `jul_4_achievement` double DEFAULT NULL,
  `jul_5_target` double DEFAULT NULL,
  `jul_5_achievement` double DEFAULT NULL,
  `jul_6_target` double DEFAULT NULL,
  `jul_6_achievement` double DEFAULT NULL,
  `jul_7_target` double DEFAULT NULL,
  `jul_7_achievement` double DEFAULT NULL,
  `jul_8_target` double DEFAULT NULL,
  `jul_8_achievement` double DEFAULT NULL,
  `jul_9_target` double DEFAULT NULL,
  `jul_9_achievement` double DEFAULT NULL,
  `jul_10_target` double DEFAULT NULL,
  `jul_10_achievement` double DEFAULT NULL,
  `jul_11_target` double DEFAULT NULL,
  `jul_11_achievement` double DEFAULT NULL,
  `jul_12_target` double DEFAULT NULL,
  `jul_12_achievement` double DEFAULT NULL,
  `jul_13_target` double DEFAULT NULL,
  `jul_13_achievement` double DEFAULT NULL,
  `jul_14_target` double DEFAULT NULL,
  `jul_14_achievement` double DEFAULT NULL,
  `jul_15_target` double DEFAULT NULL,
  `jul_15_achievement` double DEFAULT NULL,
  `jul_16_target` double DEFAULT NULL,
  `jul_16_achievement` double DEFAULT NULL,
  `jul_17_target` double DEFAULT NULL,
  `jul_17_achievement` double DEFAULT NULL,
  `jul_18_target` double DEFAULT NULL,
  `jul_18_achievement` double DEFAULT NULL,
  `jul_19_target` double DEFAULT NULL,
  `jul_19_achievement` double DEFAULT NULL,
  `jul_20_target` double DEFAULT NULL,
  `jul_20_achievement` double DEFAULT NULL,
  `jul_21_target` double DEFAULT NULL,
  `jul_21_achievement` double DEFAULT NULL,
  `jul_22_target` double DEFAULT NULL,
  `jul_22_achievement` double DEFAULT NULL,
  `jul_23_target` double DEFAULT NULL,
  `jul_23_achievement` double DEFAULT NULL,
  `jul_24_target` double DEFAULT NULL,
  `jul_24_achievement` double DEFAULT NULL,
  `jul_25_target` double DEFAULT NULL,
  `jul_25_achievement` double DEFAULT NULL,
  `jul_26_target` double DEFAULT NULL,
  `jul_26_achievement` double DEFAULT NULL,
  `jul_27_target` double DEFAULT NULL,
  `jul_27_achievement` double DEFAULT NULL,
  `jul_28_target` double DEFAULT NULL,
  `jul_28_achievement` double DEFAULT NULL,
  `jul_29_target` double DEFAULT NULL,
  `jul_29_achievement` double DEFAULT NULL,
  `jul_30_target` double DEFAULT NULL,
  `jul_30_achievement` double DEFAULT NULL,
  `jul_31_target` double DEFAULT NULL,
  `jul_31_achievement` double DEFAULT NULL,
  `aug_1_target` double DEFAULT NULL,
  `aug_1_achievement` double DEFAULT NULL,
  `aug_2_target` double DEFAULT NULL,
  `aug_2_achievement` double DEFAULT NULL,
  `aug_3_target` double DEFAULT NULL,
  `aug_3_achievement` double DEFAULT NULL,
  `aug_4_target` double DEFAULT NULL,
  `aug_4_achievement` double DEFAULT NULL,
  `aug_5_target` double DEFAULT NULL,
  `aug_5_achievement` double DEFAULT NULL,
  `aug_6_target` double DEFAULT NULL,
  `aug_6_achievement` double DEFAULT NULL,
  `aug_7_target` double DEFAULT NULL,
  `aug_7_achievement` double DEFAULT NULL,
  `aug_8_target` double DEFAULT NULL,
  `aug_8_achievement` double DEFAULT NULL,
  `aug_9_target` double DEFAULT NULL,
  `aug_9_achievement` double DEFAULT NULL,
  `aug_10_target` double DEFAULT NULL,
  `aug_10_achievement` double DEFAULT NULL,
  `aug_11_target` double DEFAULT NULL,
  `aug_11_achievement` double DEFAULT NULL,
  `aug_12_target` double DEFAULT NULL,
  `aug_12_achievement` double DEFAULT NULL,
  `aug_13_target` double DEFAULT NULL,
  `aug_13_achievement` double DEFAULT NULL,
  `aug_14_target` double DEFAULT NULL,
  `aug_14_achievement` double DEFAULT NULL,
  `aug_15_target` double DEFAULT NULL,
  `aug_15_achievement` double DEFAULT NULL,
  `aug_16_target` double DEFAULT NULL,
  `aug_16_achievement` double DEFAULT NULL,
  `aug_17_target` double DEFAULT NULL,
  `aug_17_achievement` double DEFAULT NULL,
  `aug_18_target` double DEFAULT NULL,
  `aug_18_achievement` double DEFAULT NULL,
  `aug_19_target` double DEFAULT NULL,
  `aug_19_achievement` double DEFAULT NULL,
  `aug_20_target` double DEFAULT NULL,
  `aug_20_achievement` double DEFAULT NULL,
  `aug_21_target` double DEFAULT NULL,
  `aug_21_achievement` double DEFAULT NULL,
  `aug_22_target` double DEFAULT NULL,
  `aug_22_achievement` double DEFAULT NULL,
  `aug_23_target` double DEFAULT NULL,
  `aug_23_achievement` double DEFAULT NULL,
  `aug_24_target` double DEFAULT NULL,
  `aug_24_achievement` double DEFAULT NULL,
  `aug_25_target` double DEFAULT NULL,
  `aug_25_achievement` double DEFAULT NULL,
  `aug_26_target` double DEFAULT NULL,
  `aug_26_achievement` double DEFAULT NULL,
  `aug_27_target` double DEFAULT NULL,
  `aug_27_achievement` double DEFAULT NULL,
  `aug_28_target` double DEFAULT NULL,
  `aug_28_achievement` double DEFAULT NULL,
  `aug_29_target` double DEFAULT NULL,
  `aug_29_achievement` double DEFAULT NULL,
  `aug_30_target` double DEFAULT NULL,
  `aug_30_achievement` double DEFAULT NULL,
  `aug_31_target` double DEFAULT NULL,
  `aug_31_achievement` double DEFAULT NULL,
  `sep_1_target` double DEFAULT NULL,
  `sep_1_achievement` double DEFAULT NULL,
  `sep_2_target` double DEFAULT NULL,
  `sep_2_achievement` double DEFAULT NULL,
  `sep_3_target` double DEFAULT NULL,
  `sep_3_achievement` double DEFAULT NULL,
  `sep_4_target` double DEFAULT NULL,
  `sep_4_achievement` double DEFAULT NULL,
  `sep_5_target` double DEFAULT NULL,
  `sep_5_achievement` double DEFAULT NULL,
  `sep_6_target` double DEFAULT NULL,
  `sep_6_achievement` double DEFAULT NULL,
  `sep_7_target` double DEFAULT NULL,
  `sep_7_achievement` double DEFAULT NULL,
  `sep_8_target` double DEFAULT NULL,
  `sep_8_achievement` double DEFAULT NULL,
  `sep_9_target` double DEFAULT NULL,
  `sep_9_achievement` double DEFAULT NULL,
  `sep_10_target` double DEFAULT NULL,
  `sep_10_achievement` double DEFAULT NULL,
  `sep_11_target` double DEFAULT NULL,
  `sep_11_achievement` double DEFAULT NULL,
  `sep_12_target` double DEFAULT NULL,
  `sep_12_achievement` double DEFAULT NULL,
  `sep_13_target` double DEFAULT NULL,
  `sep_13_achievement` double DEFAULT NULL,
  `sep_14_target` double DEFAULT NULL,
  `sep_14_achievement` double DEFAULT NULL,
  `sep_15_target` double DEFAULT NULL,
  `sep_15_achievement` double DEFAULT NULL,
  `sep_16_target` double DEFAULT NULL,
  `sep_16_achievement` double DEFAULT NULL,
  `sep_17_target` double DEFAULT NULL,
  `sep_17_achievement` double DEFAULT NULL,
  `sep_18_target` double DEFAULT NULL,
  `sep_18_achievement` double DEFAULT NULL,
  `sep_19_target` double DEFAULT NULL,
  `sep_19_achievement` double DEFAULT NULL,
  `sep_20_target` double DEFAULT NULL,
  `sep_20_achievement` double DEFAULT NULL,
  `sep_21_target` double DEFAULT NULL,
  `sep_21_achievement` double DEFAULT NULL,
  `sep_22_target` double DEFAULT NULL,
  `sep_22_achievement` double DEFAULT NULL,
  `sep_23_target` double DEFAULT NULL,
  `sep_23_achievement` double DEFAULT NULL,
  `sep_24_target` double DEFAULT NULL,
  `sep_24_achievement` double DEFAULT NULL,
  `sep_25_target` double DEFAULT NULL,
  `sep_25_achievement` double DEFAULT NULL,
  `sep_26_target` double DEFAULT NULL,
  `sep_26_achievement` double DEFAULT NULL,
  `sep_27_target` double DEFAULT NULL,
  `sep_27_achievement` double DEFAULT NULL,
  `sep_28_target` double DEFAULT NULL,
  `sep_28_achievement` double DEFAULT NULL,
  `sep_29_target` double DEFAULT NULL,
  `sep_29_achievement` double DEFAULT NULL,
  `sep_30_target` double DEFAULT NULL,
  `sep_30_achievement` double DEFAULT NULL,
  `oct_1_target` double DEFAULT NULL,
  `oct_1_achievement` double DEFAULT NULL,
  `oct_2_target` double DEFAULT NULL,
  `oct_2_achievement` double DEFAULT NULL,
  `oct_3_target` double DEFAULT NULL,
  `oct_3_achievement` double DEFAULT NULL,
  `oct_4_target` double DEFAULT NULL,
  `oct_4_achievement` double DEFAULT NULL,
  `oct_5_target` double DEFAULT NULL,
  `oct_5_achievement` double DEFAULT NULL,
  `oct_6_target` double DEFAULT NULL,
  `oct_6_achievement` double DEFAULT NULL,
  `oct_7_target` double DEFAULT NULL,
  `oct_7_achievement` double DEFAULT NULL,
  `oct_8_target` double DEFAULT NULL,
  `oct_8_achievement` double DEFAULT NULL,
  `oct_9_target` double DEFAULT NULL,
  `oct_9_achievement` double DEFAULT NULL,
  `oct_10_target` double DEFAULT NULL,
  `oct_10_achievement` double DEFAULT NULL,
  `oct_11_target` double DEFAULT NULL,
  `oct_11_achievement` double DEFAULT NULL,
  `oct_12_target` double DEFAULT NULL,
  `oct_12_achievement` double DEFAULT NULL,
  `oct_13_target` double DEFAULT NULL,
  `oct_13_achievement` double DEFAULT NULL,
  `oct_14_target` double DEFAULT NULL,
  `oct_14_achievement` double DEFAULT NULL,
  `oct_15_target` double DEFAULT NULL,
  `oct_15_achievement` double DEFAULT NULL,
  `oct_16_target` double DEFAULT NULL,
  `oct_16_achievement` double DEFAULT NULL,
  `oct_17_target` double DEFAULT NULL,
  `oct_17_achievement` double DEFAULT NULL,
  `oct_18_target` double DEFAULT NULL,
  `oct_18_achievement` double DEFAULT NULL,
  `oct_19_target` double DEFAULT NULL,
  `oct_19_achievement` double DEFAULT NULL,
  `oct_20_target` double DEFAULT NULL,
  `oct_20_achievement` double DEFAULT NULL,
  `oct_21_target` double DEFAULT NULL,
  `oct_21_achievement` double DEFAULT NULL,
  `oct_22_target` double DEFAULT NULL,
  `oct_22_achievement` double DEFAULT NULL,
  `oct_23_target` double DEFAULT NULL,
  `oct_23_achievement` double DEFAULT NULL,
  `oct_24_target` double DEFAULT NULL,
  `oct_24_achievement` double DEFAULT NULL,
  `oct_25_target` double DEFAULT NULL,
  `oct_25_achievement` double DEFAULT NULL,
  `oct_26_target` double DEFAULT NULL,
  `oct_26_achievement` double DEFAULT NULL,
  `oct_27_target` double DEFAULT NULL,
  `oct_27_achievement` double DEFAULT NULL,
  `oct_28_target` double DEFAULT NULL,
  `oct_28_achievement` double DEFAULT NULL,
  `oct_29_target` double DEFAULT NULL,
  `oct_29_achievement` double DEFAULT NULL,
  `oct_30_target` double DEFAULT NULL,
  `oct_30_achievement` double DEFAULT NULL,
  `oct_31_target` double DEFAULT NULL,
  `oct_31_achievement` double DEFAULT NULL,
  `nov_1_target` double DEFAULT NULL,
  `nov_1_achievement` double DEFAULT NULL,
  `nov_2_target` double DEFAULT NULL,
  `nov_2_achievement` double DEFAULT NULL,
  `nov_3_target` double DEFAULT NULL,
  `nov_3_achievement` double DEFAULT NULL,
  `nov_4_target` double DEFAULT NULL,
  `nov_4_achievement` double DEFAULT NULL,
  `nov_5_target` double DEFAULT NULL,
  `nov_5_achievement` double DEFAULT NULL,
  `nov_6_target` double DEFAULT NULL,
  `nov_6_achievement` double DEFAULT NULL,
  `nov_7_target` double DEFAULT NULL,
  `nov_7_achievement` double DEFAULT NULL,
  `nov_8_target` double DEFAULT NULL,
  `nov_8_achievement` double DEFAULT NULL,
  `nov_9_target` double DEFAULT NULL,
  `nov_9_achievement` double DEFAULT NULL,
  `nov_10_target` double DEFAULT NULL,
  `nov_10_achievement` double DEFAULT NULL,
  `nov_11_target` double DEFAULT NULL,
  `nov_11_achievement` double DEFAULT NULL,
  `nov_12_target` double DEFAULT NULL,
  `nov_12_achievement` double DEFAULT NULL,
  `nov_13_target` double DEFAULT NULL,
  `nov_13_achievement` double DEFAULT NULL,
  `nov_14_target` double DEFAULT NULL,
  `nov_14_achievement` double DEFAULT NULL,
  `nov_15_target` double DEFAULT NULL,
  `nov_15_achievement` double DEFAULT NULL,
  `nov_16_target` double DEFAULT NULL,
  `nov_16_achievement` double DEFAULT NULL,
  `nov_17_target` double DEFAULT NULL,
  `nov_17_achievement` double DEFAULT NULL,
  `nov_18_target` double DEFAULT NULL,
  `nov_18_achievement` double DEFAULT NULL,
  `nov_19_target` double DEFAULT NULL,
  `nov_19_achievement` double DEFAULT NULL,
  `nov_20_target` double DEFAULT NULL,
  `nov_20_achievement` double DEFAULT NULL,
  `nov_21_target` double DEFAULT NULL,
  `nov_21_achievement` double DEFAULT NULL,
  `nov_22_target` double DEFAULT NULL,
  `nov_22_achievement` double DEFAULT NULL,
  `nov_23_target` double DEFAULT NULL,
  `nov_23_achievement` double DEFAULT NULL,
  `nov_24_target` double DEFAULT NULL,
  `nov_24_achievement` double DEFAULT NULL,
  `nov_25_target` double DEFAULT NULL,
  `nov_25_achievement` double DEFAULT NULL,
  `nov_26_target` double DEFAULT NULL,
  `nov_26_achievement` double DEFAULT NULL,
  `nov_27_target` double DEFAULT NULL,
  `nov_27_achievement` double DEFAULT NULL,
  `nov_28_target` double DEFAULT NULL,
  `nov_28_achievement` double DEFAULT NULL,
  `nov_29_target` double DEFAULT NULL,
  `nov_29_achievement` double DEFAULT NULL,
  `nov_30_target` double DEFAULT NULL,
  `nov_30_achievement` double DEFAULT NULL,
  `dec_1_target` double DEFAULT NULL,
  `dec_1_achievement` double DEFAULT NULL,
  `dec_2_target` double DEFAULT NULL,
  `dec_2_achievement` double DEFAULT NULL,
  `dec_3_target` double DEFAULT NULL,
  `dec_3_achievement` double DEFAULT NULL,
  `dec_4_target` double DEFAULT NULL,
  `dec_4_achievement` double DEFAULT NULL,
  `dec_5_target` double DEFAULT NULL,
  `dec_5_achievement` double DEFAULT NULL,
  `dec_6_target` double DEFAULT NULL,
  `dec_6_achievement` double DEFAULT NULL,
  `dec_7_target` double DEFAULT NULL,
  `dec_7_achievement` double DEFAULT NULL,
  `dec_8_target` double DEFAULT NULL,
  `dec_8_achievement` double DEFAULT NULL,
  `dec_9_target` double DEFAULT NULL,
  `dec_9_achievement` double DEFAULT NULL,
  `dec_10_target` double DEFAULT NULL,
  `dec_10_achievement` double DEFAULT NULL,
  `dec_11_target` double DEFAULT NULL,
  `dec_11_achievement` double DEFAULT NULL,
  `dec_12_target` double DEFAULT NULL,
  `dec_12_achievement` double DEFAULT NULL,
  `dec_13_target` double DEFAULT NULL,
  `dec_13_achievement` double DEFAULT NULL,
  `dec_14_target` double DEFAULT NULL,
  `dec_14_achievement` double DEFAULT NULL,
  `dec_15_target` double DEFAULT NULL,
  `dec_15_achievement` double DEFAULT NULL,
  `dec_16_target` double DEFAULT NULL,
  `dec_16_achievement` double DEFAULT NULL,
  `dec_17_target` double DEFAULT NULL,
  `dec_17_achievement` double DEFAULT NULL,
  `dec_18_target` double DEFAULT NULL,
  `dec_18_achievement` double DEFAULT NULL,
  `dec_19_target` double DEFAULT NULL,
  `dec_19_achievement` double DEFAULT NULL,
  `dec_20_target` double DEFAULT NULL,
  `dec_20_achievement` double DEFAULT NULL,
  `dec_21_target` double DEFAULT NULL,
  `dec_21_achievement` double DEFAULT NULL,
  `dec_22_target` double DEFAULT NULL,
  `dec_22_achievement` double DEFAULT NULL,
  `dec_23_target` double DEFAULT NULL,
  `dec_23_achievement` double DEFAULT NULL,
  `dec_24_target` double DEFAULT NULL,
  `dec_24_achievement` double DEFAULT NULL,
  `dec_25_target` double DEFAULT NULL,
  `dec_25_achievement` double DEFAULT NULL,
  `dec_26_target` double DEFAULT NULL,
  `dec_26_achievement` double DEFAULT NULL,
  `dec_27_target` double DEFAULT NULL,
  `dec_27_achievement` double DEFAULT NULL,
  `dec_28_target` double DEFAULT NULL,
  `dec_28_achievement` double DEFAULT NULL,
  `dec_29_target` double DEFAULT NULL,
  `dec_29_achievement` double DEFAULT NULL,
  `dec_30_target` double DEFAULT NULL,
  `dec_30_achievement` double DEFAULT NULL,
  `dec_31_target` double DEFAULT NULL,
  `dec_31_achievement` double DEFAULT NULL,
  `jan_prev_y_target` double DEFAULT NULL,
  `jan_prev_y_achievement` double DEFAULT NULL,
  `feb_prev_y_target` double DEFAULT NULL,
  `feb_prev_y_achievement` double DEFAULT NULL,
  `mar_prev_y_target` double DEFAULT NULL,
  `mar_prev_y_achievement` double DEFAULT NULL,
  `apr_prev_y_target` double DEFAULT NULL,
  `apr_prev_y_achievement` double DEFAULT NULL,
  `may_prev_y_target` double DEFAULT NULL,
  `may_prev_y_achievement` double DEFAULT NULL,
  `jun_prev_y_target` double DEFAULT NULL,
  `jun_prev_y_achievement` double DEFAULT NULL,
  `jul_prev_y_target` double DEFAULT NULL,
  `jul_prev_y_achievement` double DEFAULT NULL,
  `aug_prev_y_target` double DEFAULT NULL,
  `aug_prev_y_achievement` double DEFAULT NULL,
  `sep_prev_y_target` double DEFAULT NULL,
  `sep_prev_y_achievement` double DEFAULT NULL,
  `oct_prev_y_target` double DEFAULT NULL,
  `oct_prev_y_achievement` double DEFAULT NULL,
  `nov_prev_y_target` double DEFAULT NULL,
  `nov_prev_y_achievement` double DEFAULT NULL,
  `dec_prev_y_target` double DEFAULT NULL,
  `dec_prev_y_achievement` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sku_master`
--

CREATE TABLE `sku_master` (
  `branch_code` varchar(20) NOT NULL DEFAULT '',
  `prod_code` varchar(50) NOT NULL,
  `prod_desc` text NOT NULL,
  `product_group_code` varchar(255) NOT NULL,
  `product_sub_group_code` varchar(255) NOT NULL,
  `product_brand_code` varchar(255) NOT NULL,
  `TD` varchar(20) NOT NULL,
  `size` varchar(50) NOT NULL,
  `acedns` varchar(2) NOT NULL DEFAULT '',
  `black_list` varchar(2) NOT NULL,
  `vertical_value` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `sp_destination`
--

CREATE TABLE `sp_destination` (
  `sl_no` int(10) NOT NULL,
  `broker_id` varchar(50) DEFAULT NULL,
  `destination_code` varchar(100) DEFAULT NULL,
  `acedns` varchar(10) DEFAULT 'N',
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sqllog`
--

CREATE TABLE `sqllog` (
  `sl_no` int(2) NOT NULL,
  `sqltxt` text NOT NULL,
  `update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `startreport_admin`
--

CREATE TABLE `startreport_admin` (
  `id` int(11) NOT NULL,
  `user_name` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `status` enum('ACTIVE','DEACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `user_type` enum('ADMIN','MANAGER','READONLY') NOT NULL DEFAULT 'ADMIN',
  `order_show_branch` varchar(40) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `start_slider`
--

CREATE TABLE `start_slider` (
  `id` int(11) NOT NULL,
  `image_name` varchar(80) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subdealer_branch_schemes_PDF`
--

CREATE TABLE `subdealer_branch_schemes_PDF` (
  `sl_no` int(10) NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `PDF_file_name` text NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `acedns` varchar(10) NOT NULL DEFAULT 'Y',
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subdlr_data`
--

CREATE TABLE `subdlr_data` (
  `id` int(11) NOT NULL,
  `dns_code` varchar(30) NOT NULL,
  `acsdns` varchar(10) NOT NULL,
  `cust_type` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `survey_form`
--

CREATE TABLE `survey_form` (
  `sf_id` bigint(20) NOT NULL,
  `sf_cust_code` varchar(80) DEFAULT NULL,
  `sf_dealer_id` varchar(80) DEFAULT NULL,
  `sf_dealer_sap_code` varchar(40) DEFAULT NULL,
  `sf_dealer_name` varchar(100) DEFAULT NULL,
  `sf_dealer_mobile` varchar(60) DEFAULT NULL,
  `sf_branch_name` varchar(80) DEFAULT NULL,
  `sf_branch_code` varchar(50) DEFAULT NULL,
  `sf_dns_branch_code` varchar(50) DEFAULT NULL,
  `sf_state` varchar(80) DEFAULT NULL,
  `sf_is_return_fy21_22` varchar(40) DEFAULT NULL,
  `sf_is_clmd_morthn_50k_tdstcs_fy22_23` varchar(40) DEFAULT NULL,
  `sf_is_turnover_exceeds_10_crores_fy22_23` varchar(40) DEFAULT NULL,
  `sf_pan` varchar(50) DEFAULT NULL,
  `sf_tan` varchar(50) DEFAULT NULL,
  `sf_is_checked_declaration` varchar(40) DEFAULT NULL,
  `sf_1_attending` varchar(20) NOT NULL DEFAULT 'No',
  `sf_a_alan_walker` varchar(20) NOT NULL DEFAULT 'N',
  `sf_b_badhshah` varchar(20) NOT NULL DEFAULT 'N',
  `sf_c_david_guetta` varchar(20) NOT NULL DEFAULT 'N',
  `sf_d_sunidhi_chauhan` varchar(20) NOT NULL DEFAULT 'N',
  `sf_e_lucky_ali` varchar(20) NOT NULL DEFAULT 'N',
  `sf_f_zubeen_garg` varchar(20) NOT NULL DEFAULT 'N',
  `sf_g_pitbull` varchar(20) NOT NULL DEFAULT 'N',
  `sf_h_mika_singh` varchar(20) NOT NULL DEFAULT 'N',
  `sf_i_arijit_singh` varchar(20) NOT NULL DEFAULT 'N',
  `sf_j_papon` varchar(20) DEFAULT 'N',
  `sf_k_dj_nucleya` varchar(20) NOT NULL DEFAULT 'N',
  `sf_l_ankit_tiwari` varchar(20) NOT NULL DEFAULT 'N',
  `sf_m_others` text,
  `sf_3a_2000` varchar(20) NOT NULL DEFAULT 'N',
  `sf_3b_3000` varchar(20) NOT NULL DEFAULT 'N',
  `sf_3c_4000` varchar(20) NOT NULL DEFAULT 'N',
  `sf_3d_5000` varchar(20) NOT NULL DEFAULT 'N',
  `sf_3e_7000` varchar(20) NOT NULL DEFAULT 'N',
  `sf_4a_spouse_only` varchar(20) NOT NULL DEFAULT 'N',
  `sf_4b_spouse_and_kid` varchar(20) NOT NULL DEFAULT 'N',
  `sf_4c_friends_and_colleagues` varchar(20) NOT NULL DEFAULT 'N',
  `sf_4d_parents_and_siblings` varchar(20) NOT NULL DEFAULT 'N',
  `sf_4e_solo` varchar(20) NOT NULL DEFAULT 'N',
  `sf_5_liquour` varchar(20) NOT NULL DEFAULT 'No',
  `sf_6_30th_december` varchar(20) NOT NULL DEFAULT 'No',
  `sf_submitted_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `survey_form_18_20`
--

CREATE TABLE `survey_form_18_20` (
  `sf_id` bigint(20) NOT NULL,
  `sf_cust_code` varchar(80) DEFAULT NULL,
  `sf_dealer_id` varchar(80) DEFAULT NULL,
  `sf_dealer_name` varchar(100) DEFAULT NULL,
  `sf_dealer_mobile` varchar(60) DEFAULT NULL,
  `sf_branch_name` varchar(80) DEFAULT NULL,
  `sf_branch_code` varchar(50) DEFAULT NULL,
  `sf_dns_branch_code` varchar(50) DEFAULT NULL,
  `sf_state` varchar(80) DEFAULT NULL,
  `sf_is_return_fy18_19` varchar(40) DEFAULT NULL,
  `sf_is_return_fy19_20` varchar(40) DEFAULT NULL,
  `sf_is_clmd_morthn_50k_tdstcs_fy18_19orfy19_20` varchar(40) DEFAULT NULL,
  `sf_is_turnover_exceeds_10_crores_fy20_21` varchar(40) DEFAULT NULL,
  `sf_pan` varchar(50) DEFAULT NULL,
  `sf_is_checked_declaration` varchar(40) DEFAULT NULL,
  `sf_submitted_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `survey_form_fy_21_22_23`
--

CREATE TABLE `survey_form_fy_21_22_23` (
  `sf_id` bigint(20) NOT NULL,
  `sf_cust_code` varchar(80) DEFAULT NULL,
  `sf_dealer_id` varchar(80) DEFAULT NULL,
  `sf_dealer_sap_code` varchar(40) DEFAULT NULL,
  `sf_dealer_name` varchar(100) DEFAULT NULL,
  `sf_dealer_mobile` varchar(60) DEFAULT NULL,
  `sf_branch_name` varchar(80) DEFAULT NULL,
  `sf_branch_code` varchar(50) DEFAULT NULL,
  `sf_dns_branch_code` varchar(50) DEFAULT NULL,
  `sf_state` varchar(80) DEFAULT NULL,
  `sf_is_return_fy21_22` varchar(40) DEFAULT NULL,
  `sf_is_clmd_morthn_50k_tdstcs_fy22_23` varchar(40) DEFAULT NULL,
  `sf_is_turnover_exceeds_10_crores_fy22_23` varchar(40) DEFAULT NULL,
  `sf_pan` varchar(50) DEFAULT NULL,
  `sf_tan` varchar(50) DEFAULT NULL,
  `sf_is_checked_declaration` varchar(40) DEFAULT NULL,
  `sf_submitted_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `survey_form_prev_data`
--

CREATE TABLE `survey_form_prev_data` (
  `sf_id` bigint(20) NOT NULL,
  `sf_cust_code` varchar(80) DEFAULT NULL,
  `sf_dealer_id` varchar(80) DEFAULT NULL,
  `sf_dealer_sap_code` varchar(40) DEFAULT NULL,
  `sf_dealer_name` varchar(100) DEFAULT NULL,
  `sf_dealer_mobile` varchar(60) DEFAULT NULL,
  `sf_branch_name` varchar(80) DEFAULT NULL,
  `sf_branch_code` varchar(50) DEFAULT NULL,
  `sf_dns_branch_code` varchar(50) DEFAULT NULL,
  `sf_state` varchar(80) DEFAULT NULL,
  `sf_is_return_fy21_22` varchar(40) DEFAULT NULL,
  `sf_is_clmd_morthn_50k_tdstcs_fy22_23` varchar(40) DEFAULT NULL,
  `sf_is_turnover_exceeds_10_crores_fy22_23` varchar(40) DEFAULT NULL,
  `sf_pan` varchar(50) DEFAULT NULL,
  `sf_tan` varchar(50) DEFAULT NULL,
  `sf_is_checked_declaration` varchar(40) DEFAULT NULL,
  `sf_1_attending` varchar(20) NOT NULL DEFAULT 'No',
  `sf_a_alan_walker` varchar(20) NOT NULL DEFAULT 'N',
  `sf_b_badhshah` varchar(20) NOT NULL DEFAULT 'N',
  `sf_c_david_guetta` varchar(20) NOT NULL DEFAULT 'N',
  `sf_d_sunidhi_chauhan` varchar(20) NOT NULL DEFAULT 'N',
  `sf_e_lucky_ali` varchar(20) NOT NULL DEFAULT 'N',
  `sf_f_zubeen_garg` varchar(20) NOT NULL DEFAULT 'N',
  `sf_g_pitbull` varchar(20) NOT NULL DEFAULT 'N',
  `sf_h_mika_singh` varchar(20) NOT NULL DEFAULT 'N',
  `sf_i_arijit_singh` varchar(20) NOT NULL DEFAULT 'N',
  `sf_j_papon` varchar(20) DEFAULT 'N',
  `sf_k_dj_nucleya` varchar(20) NOT NULL DEFAULT 'N',
  `sf_l_ankit_tiwari` varchar(20) NOT NULL DEFAULT 'N',
  `sf_m_others` text,
  `sf_3a_2000` varchar(20) NOT NULL DEFAULT 'N',
  `sf_3b_3000` varchar(20) NOT NULL DEFAULT 'N',
  `sf_3c_4000` varchar(20) NOT NULL DEFAULT 'N',
  `sf_3d_5000` varchar(20) NOT NULL DEFAULT 'N',
  `sf_3e_7000` varchar(20) NOT NULL DEFAULT 'N',
  `sf_4a_spouse_only` varchar(20) NOT NULL DEFAULT 'N',
  `sf_4b_spouse_and_kid` varchar(20) NOT NULL DEFAULT 'N',
  `sf_4c_friends_and_colleagues` varchar(20) NOT NULL DEFAULT 'N',
  `sf_4d_parents_and_siblings` varchar(20) NOT NULL DEFAULT 'N',
  `sf_4e_solo` varchar(20) NOT NULL DEFAULT 'N',
  `sf_5_liquour` varchar(20) NOT NULL DEFAULT 'No',
  `sf_6_30th_december` varchar(20) NOT NULL DEFAULT 'No',
  `sf_submitted_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `survey_for_new_year_eve_carnival_concert`
--

CREATE TABLE `survey_for_new_year_eve_carnival_concert` (
  `sf_id` bigint(20) NOT NULL,
  `sf_cust_code` varchar(80) DEFAULT NULL,
  `sf_dealer_id` varchar(80) DEFAULT NULL,
  `sf_dealer_sap_code` varchar(40) DEFAULT NULL,
  `sf_dealer_name` varchar(100) DEFAULT NULL,
  `sf_dealer_mobile` varchar(60) DEFAULT NULL,
  `sf_branch_name` varchar(80) DEFAULT NULL,
  `sf_branch_code` varchar(50) DEFAULT NULL,
  `sf_dns_branch_code` varchar(50) DEFAULT NULL,
  `sf_state` varchar(80) DEFAULT NULL,
  `sf_1_attending` varchar(20) NOT NULL DEFAULT 'No',
  `sf_a_alan_walker` varchar(20) NOT NULL DEFAULT 'N',
  `sf_b_badhshah` varchar(20) NOT NULL DEFAULT 'N',
  `sf_c_david_guetta` varchar(20) NOT NULL DEFAULT 'N',
  `sf_d_sunidhi_chauhan` varchar(20) NOT NULL DEFAULT 'N',
  `sf_e_lucky_ali` varchar(20) NOT NULL DEFAULT 'N',
  `sf_f_zubeen_garg` varchar(20) NOT NULL DEFAULT 'N',
  `sf_g_pitbull` varchar(20) NOT NULL DEFAULT 'N',
  `sf_h_mika_singh` varchar(20) NOT NULL DEFAULT 'N',
  `sf_i_arijit_singh` varchar(20) NOT NULL DEFAULT 'N',
  `sf_j_papon` varchar(20) DEFAULT 'N',
  `sf_k_dj_nucleya` varchar(20) NOT NULL DEFAULT 'N',
  `sf_l_ankit_tiwari` varchar(20) NOT NULL DEFAULT 'N',
  `sf_m_others` text,
  `sf_3a_2000` varchar(20) NOT NULL DEFAULT 'N',
  `sf_3b_3000` varchar(20) NOT NULL DEFAULT 'N',
  `sf_3c_4000` varchar(20) NOT NULL DEFAULT 'N',
  `sf_3d_5000` varchar(20) NOT NULL DEFAULT 'N',
  `sf_3e_7000` varchar(20) NOT NULL DEFAULT 'N',
  `sf_4a_spouse_only` varchar(20) NOT NULL DEFAULT 'N',
  `sf_4b_spouse_and_kid` varchar(20) NOT NULL DEFAULT 'N',
  `sf_4c_friends_and_colleagues` varchar(20) NOT NULL DEFAULT 'N',
  `sf_4d_parents_and_siblings` varchar(20) NOT NULL DEFAULT 'N',
  `sf_4e_solo` varchar(20) NOT NULL DEFAULT 'N',
  `sf_5_liquour` varchar(20) NOT NULL DEFAULT 'No',
  `sf_6_30th_december` varchar(20) NOT NULL DEFAULT 'No',
  `sf_7_31st_december` varchar(20) NOT NULL DEFAULT 'No',
  `sf_submitted_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `table_structure_master`
--

CREATE TABLE `table_structure_master` (
  `t_structure_id` tinyint(3) NOT NULL,
  `table_name` varchar(255) NOT NULL,
  `table_structure` text NOT NULL,
  `need_update` enum('Y','N') NOT NULL DEFAULT 'Y',
  `is_transaction` enum('Y','N') NOT NULL DEFAULT 'N',
  `is_master` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `table_structure_updation`
--

CREATE TABLE `table_structure_updation` (
  `emp_code` varchar(20) NOT NULL,
  `device_id` varchar(255) NOT NULL,
  `db_version_code` varchar(20) NOT NULL,
  `is_update` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customer_master`
--

CREATE TABLE `tbl_customer_master` (
  `MANDT` varchar(3) DEFAULT NULL COMMENT 'Client',
  `KUNNR` varchar(10) NOT NULL COMMENT 'Customer Number',
  `VKORG` varchar(4) NOT NULL COMMENT 'Sales Organization',
  `DWERK_EXT` varchar(4) NOT NULL COMMENT 'Delivering Plant (Own or External)',
  `NAME1_GP` varchar(35) DEFAULT NULL COMMENT 'Name 1',
  `NAME2_GP` varchar(35) DEFAULT NULL COMMENT 'Name 2',
  `NAME3_GP` varchar(35) DEFAULT NULL COMMENT 'Name 3',
  `STRAS_GP` varchar(35) DEFAULT NULL COMMENT 'Street and House Number',
  `AD_STRSPP1` varchar(40) DEFAULT NULL COMMENT 'Street 2',
  `AD_STRSPP2` varchar(40) DEFAULT NULL COMMENT 'Street 3',
  `AD_STRSPP3` varchar(40) DEFAULT NULL COMMENT 'Street 4',
  `PSTLZ` varchar(10) DEFAULT NULL COMMENT 'Postal Code',
  `ORT01_GP` varchar(35) DEFAULT NULL COMMENT 'City',
  `ORT02_GP` varchar(35) DEFAULT NULL COMMENT 'District',
  `TELF2` varchar(16) DEFAULT NULL COMMENT 'Second telephone number',
  `AD_SMTPADR` varchar(241) DEFAULT NULL COMMENT 'E-Mail Address',
  `KDGRP` varchar(2) DEFAULT NULL COMMENT 'Customer Group',
  `LZONE` varchar(10) DEFAULT NULL COMMENT ' 	  Transportation zone to or from which the goods are delivered',
  `BUKRS` varchar(4) DEFAULT NULL COMMENT 'Company Code',
  `ALTKN` varchar(10) DEFAULT NULL COMMENT 'Previous Master Record Number',
  `ERDAT_RF` date DEFAULT NULL COMMENT 'Date on which the Record Was Created',
  `ZAEDAT` date DEFAULT NULL COMMENT 'Changed On',
  `KONDA` varchar(2) DEFAULT NULL COMMENT 'Customer Price Group',
  `DZTERM` varchar(4) DEFAULT NULL COMMENT 'Terms of payment key',
  `UKM_CREDIT_LIMIT` varchar(15) DEFAULT NULL COMMENT 'Credit Limit',
  `ZSTAT_KEY` varchar(1) DEFAULT NULL COMMENT 'Status'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_material_master`
--

CREATE TABLE `tbl_material_master` (
  `MANDT` varchar(3) DEFAULT NULL COMMENT 'Client',
  `MATNR` varchar(40) DEFAULT NULL COMMENT 'Material Number',
  `WERKS_D` varchar(4) DEFAULT NULL COMMENT 'Plant',
  `VKORG` varchar(4) DEFAULT NULL COMMENT 'Sales Organization',
  `VTWEG` varchar(255) DEFAULT NULL COMMENT 'Distribution Channel',
  `MAKTX` varchar(50) DEFAULT NULL COMMENT 'Material Description',
  `MEINS` varchar(50) DEFAULT NULL COMMENT 'Base Unit of Measure',
  `MTART` varchar(40) DEFAULT NULL COMMENT 'Material type',
  `MATKL` varchar(50) DEFAULT NULL COMMENT 'Material Group',
  `SPART` varchar(50) DEFAULT NULL COMMENT 'Division',
  `MSTDE` date DEFAULT NULL COMMENT 'Date from which the cross-plant material status is valid',
  `BRGEW` double DEFAULT NULL COMMENT 'Gross weight',
  `NTGEW` double DEFAULT NULL COMMENT 'Net weight',
  `VOLUM` double DEFAULT NULL COMMENT 'Volume',
  `GROES` varchar(100) DEFAULT NULL COMMENT 'Size/dimensions',
  `MVGR1` varchar(50) DEFAULT NULL COMMENT 'Material Group 1',
  `MVGR2` varchar(50) DEFAULT NULL COMMENT 'Material Group 2',
  `MVGR3` varchar(50) DEFAULT NULL COMMENT 'Material Group 3',
  `MVGR4` varchar(50) DEFAULT NULL COMMENT 'Material Group 4',
  `MVGR5` varchar(50) DEFAULT NULL COMMENT 'Material Group 5',
  `LVOWK` varchar(20) DEFAULT NULL COMMENT 'Flag Material for Deletion at Plant Level',
  `ERDAT_RF` date DEFAULT NULL COMMENT 'Date on which the Record Was Created',
  `AEDAT_DRF` date DEFAULT NULL COMMENT 'Last Change Date (DRF)'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `temporary_table_branch_email`
--

CREATE TABLE `temporary_table_branch_email` (
  `id` int(11) NOT NULL,
  `branch_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `TMP1_T_APPERPDO`
--

CREATE TABLE `TMP1_T_APPERPDO` (
  `APPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERDT` varchar(50) DEFAULT NULL,
  `CUSTOMER_CODE` varchar(50) DEFAULT NULL,
  `DNS_PROD_CODE` varchar(50) DEFAULT NULL,
  `PROD_DISPLAY_NAME` varchar(50) DEFAULT NULL,
  `QTY` varchar(50) DEFAULT NULL,
  `LMDT` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `TMP1_T_DOCHALLAN`
--

CREATE TABLE `TMP1_T_DOCHALLAN` (
  `APPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERDT` varchar(50) DEFAULT NULL,
  `CHALLANNO` varchar(50) DEFAULT NULL,
  `CHALLANDT` varchar(50) DEFAULT NULL,
  `QTY` varchar(50) DEFAULT NULL,
  `CHALLANQTY` varchar(50) DEFAULT NULL,
  `TRUCKNO` varchar(50) DEFAULT NULL,
  `DRIVERNO` varchar(50) DEFAULT NULL,
  `CUSTOMER_CODE` varchar(50) DEFAULT NULL,
  `DNS_PROD_CODE` varchar(50) DEFAULT NULL,
  `PROD_DISPLAY_NAME` varchar(50) DEFAULT NULL,
  `LMDT` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `TMP_T_APPERPDO`
--

CREATE TABLE `TMP_T_APPERPDO` (
  `APPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERDT` varchar(50) DEFAULT NULL,
  `CUSTOMER_CODE` varchar(50) DEFAULT NULL,
  `DNS_PROD_CODE` varchar(50) DEFAULT NULL,
  `PROD_DISPLAY_NAME` varchar(50) DEFAULT NULL,
  `QTY` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `TMP_T_DOCHALLAN`
--

CREATE TABLE `TMP_T_DOCHALLAN` (
  `APPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERDT` varchar(50) DEFAULT NULL,
  `CHALLANNO` varchar(50) DEFAULT NULL,
  `CHALLANDT` varchar(50) DEFAULT NULL,
  `QTY` varchar(50) DEFAULT NULL,
  `CHALLANQTY` varchar(50) DEFAULT NULL,
  `TRUCKNO` varchar(50) DEFAULT NULL,
  `DRIVERNO` varchar(50) DEFAULT NULL,
  `CUSTOMER_CODE` varchar(50) DEFAULT NULL,
  `DNS_PROD_CODE` varchar(50) DEFAULT NULL,
  `PROD_DISPLAY_NAME` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `T_APPERPDO`
--

CREATE TABLE `T_APPERPDO` (
  `id` bigint(20) NOT NULL,
  `ref_order_id_for_duplicate_ck` varchar(50) DEFAULT NULL,
  `APPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERDT` varchar(50) DEFAULT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `order_for` varchar(200) DEFAULT NULL,
  `order_for_type` varchar(40) DEFAULT NULL,
  `consignee_name` text,
  `consignee_address` text,
  `sub_dealer_code` varchar(50) DEFAULT NULL,
  `dns_sub_dealer_code` varchar(50) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `prod_code` varchar(50) DEFAULT NULL,
  `dns_prod_code` varchar(50) DEFAULT NULL,
  `prod_display_name` varchar(50) DEFAULT NULL,
  `QTY` varchar(30) DEFAULT NULL,
  `STATUS` enum('Order received','DO approved','Dispatched','Order authorized','Order canceled','Delivered','CREDIT CHECK FAILED') DEFAULT 'Order received',
  `freight` varchar(20) DEFAULT NULL,
  `branch_code` varchar(40) DEFAULT NULL,
  `dns_branch_code` varchar(40) DEFAULT NULL,
  `destination_code` varchar(50) DEFAULT NULL,
  `destination_name` text,
  `destination_address` text,
  `phone_no` varchar(30) DEFAULT NULL,
  `dump_status` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `dump_code` varchar(50) DEFAULT NULL,
  `dump_name` varchar(150) DEFAULT NULL,
  `order_from` varchar(30) NOT NULL DEFAULT 'DEFAULT',
  `order_by` varchar(50) DEFAULT NULL,
  `dealer_truck` varchar(20) NOT NULL DEFAULT 'NO',
  `ref_sub_dealer_order_id` varchar(50) DEFAULT NULL,
  `authorized_by` varchar(80) DEFAULT NULL,
  `authorization_date` datetime DEFAULT NULL,
  `mail_sent` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `mail_sent_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_confirmed_material_received` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `quantity_checking` varchar(50) DEFAULT NULL,
  `quality_checking` varchar(50) DEFAULT NULL,
  `remarks` text,
  `confirmed_material_received_datetime` varchar(40) DEFAULT NULL,
  `sale_order_prod_code` varchar(50) DEFAULT NULL,
  `sale_order_qty` varchar(50) DEFAULT NULL,
  `sale_order_dump` varchar(100) DEFAULT NULL,
  `Delivery_point` varchar(25) DEFAULT NULL,
  `Remarks_text` varchar(250) DEFAULT NULL,
  `Additional_data_1` varchar(25) DEFAULT NULL,
  `Additional_data_2` varchar(25) DEFAULT NULL,
  `Additional_data_3` varchar(25) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `T_APPERPDO_03_10_24`
--

CREATE TABLE `T_APPERPDO_03_10_24` (
  `id` bigint(20) NOT NULL,
  `ref_order_id_for_duplicate_ck` varchar(50) DEFAULT NULL,
  `APPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERDT` varchar(50) DEFAULT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `order_for` varchar(200) DEFAULT NULL,
  `order_for_type` varchar(40) DEFAULT NULL,
  `consignee_name` text,
  `consignee_address` text,
  `sub_dealer_code` varchar(50) DEFAULT NULL,
  `dns_sub_dealer_code` varchar(50) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `prod_code` varchar(50) DEFAULT NULL,
  `dns_prod_code` varchar(50) DEFAULT NULL,
  `prod_display_name` varchar(50) DEFAULT NULL,
  `QTY` varchar(30) DEFAULT NULL,
  `STATUS` enum('Order received','DO approved','Dispatched','Order authorized','Order canceled','Delivered','CREDIT CHECK FAILED') DEFAULT 'Order received',
  `freight` varchar(20) DEFAULT NULL,
  `branch_code` varchar(40) DEFAULT NULL,
  `dns_branch_code` varchar(40) DEFAULT NULL,
  `destination_code` varchar(50) DEFAULT NULL,
  `destination_name` text,
  `destination_address` text,
  `phone_no` varchar(30) DEFAULT NULL,
  `dump_status` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `dump_code` varchar(50) DEFAULT NULL,
  `dump_name` varchar(150) DEFAULT NULL,
  `order_from` varchar(30) NOT NULL DEFAULT 'DEFAULT',
  `order_by` varchar(50) DEFAULT NULL,
  `dealer_truck` varchar(20) NOT NULL DEFAULT 'NO',
  `ref_sub_dealer_order_id` varchar(50) DEFAULT NULL,
  `authorized_by` varchar(80) DEFAULT NULL,
  `authorization_date` datetime DEFAULT NULL,
  `mail_sent` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `mail_sent_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_confirmed_material_received` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `quantity_checking` varchar(50) DEFAULT NULL,
  `quality_checking` varchar(50) DEFAULT NULL,
  `remarks` text,
  `confirmed_material_received_datetime` varchar(40) DEFAULT NULL,
  `sale_order_prod_code` varchar(50) DEFAULT NULL,
  `sale_order_qty` varchar(50) DEFAULT NULL,
  `sale_order_dump` varchar(100) DEFAULT NULL,
  `Delivery_point` varchar(25) DEFAULT NULL,
  `Remarks_text` varchar(250) DEFAULT NULL,
  `Additional_data_1` varchar(25) DEFAULT NULL,
  `Additional_data_2` varchar(25) DEFAULT NULL,
  `Additional_data_3` varchar(25) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `T_APPERPDO_ARCHIVE_24`
--

CREATE TABLE `T_APPERPDO_ARCHIVE_24` (
  `id` bigint(20) NOT NULL,
  `ref_order_id_for_duplicate_ck` varchar(50) DEFAULT NULL,
  `APPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERDT` varchar(50) DEFAULT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `order_for` varchar(200) DEFAULT NULL,
  `order_for_type` varchar(40) DEFAULT NULL,
  `consignee_name` text,
  `consignee_address` text,
  `sub_dealer_code` varchar(50) DEFAULT NULL,
  `dns_sub_dealer_code` varchar(50) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `prod_code` varchar(50) DEFAULT NULL,
  `dns_prod_code` varchar(50) DEFAULT NULL,
  `prod_display_name` varchar(50) DEFAULT NULL,
  `QTY` varchar(30) DEFAULT NULL,
  `STATUS` enum('Order received','DO approved','Dispatched','Order authorized','Order canceled','Delivered','CREDIT CHECK FAILED') DEFAULT 'Order received',
  `freight` varchar(20) DEFAULT NULL,
  `branch_code` varchar(40) DEFAULT NULL,
  `dns_branch_code` varchar(40) DEFAULT NULL,
  `destination_code` varchar(50) DEFAULT NULL,
  `destination_name` text,
  `destination_address` text,
  `phone_no` varchar(30) DEFAULT NULL,
  `dump_status` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `dump_code` varchar(50) DEFAULT NULL,
  `dump_name` varchar(150) DEFAULT NULL,
  `order_from` varchar(30) NOT NULL DEFAULT 'DEFAULT',
  `order_by` varchar(50) DEFAULT NULL,
  `dealer_truck` varchar(20) NOT NULL DEFAULT 'NO',
  `ref_sub_dealer_order_id` varchar(50) DEFAULT NULL,
  `authorized_by` varchar(80) DEFAULT NULL,
  `authorization_date` datetime DEFAULT NULL,
  `mail_sent` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `mail_sent_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_confirmed_material_received` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `quantity_checking` varchar(50) DEFAULT NULL,
  `quality_checking` varchar(50) DEFAULT NULL,
  `remarks` text,
  `confirmed_material_received_datetime` varchar(40) DEFAULT NULL,
  `sale_order_prod_code` varchar(50) DEFAULT NULL,
  `sale_order_qty` varchar(50) DEFAULT NULL,
  `sale_order_dump` varchar(100) DEFAULT NULL,
  `Delivery_point` varchar(25) DEFAULT NULL,
  `Remarks_text` varchar(250) DEFAULT NULL,
  `Additional_data_1` varchar(25) DEFAULT NULL,
  `Additional_data_2` varchar(25) DEFAULT NULL,
  `Additional_data_3` varchar(25) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `T_APPERPDO_INVOICE`
--

CREATE TABLE `T_APPERPDO_INVOICE` (
  `id` bigint(20) NOT NULL,
  `ref_order_id_for_duplicate_ck` varchar(50) DEFAULT NULL,
  `APPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERDT` varchar(50) DEFAULT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `order_for` varchar(200) DEFAULT NULL,
  `order_for_type` varchar(40) DEFAULT NULL,
  `consignee_name` text,
  `consignee_address` text,
  `sub_dealer_code` varchar(50) DEFAULT NULL,
  `dns_sub_dealer_code` varchar(50) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `prod_code` varchar(50) DEFAULT NULL,
  `dns_prod_code` varchar(50) DEFAULT NULL,
  `prod_display_name` varchar(50) DEFAULT NULL,
  `QTY` varchar(30) DEFAULT NULL,
  `STATUS` enum('Order received','DO approved','Dispatched','Order authorized','Order canceled','Delivered','CREDIT CHECK FAILED') DEFAULT 'Order received',
  `freight` varchar(20) DEFAULT NULL,
  `branch_code` varchar(40) DEFAULT NULL,
  `dns_branch_code` varchar(40) DEFAULT NULL,
  `destination_code` varchar(50) DEFAULT NULL,
  `destination_name` text,
  `destination_address` text,
  `phone_no` varchar(30) DEFAULT NULL,
  `dump_status` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `dump_code` varchar(50) DEFAULT NULL,
  `dump_name` varchar(150) DEFAULT NULL,
  `order_from` varchar(30) NOT NULL DEFAULT 'DEFAULT',
  `order_by` varchar(50) DEFAULT NULL,
  `dealer_truck` varchar(20) NOT NULL DEFAULT 'NO',
  `ref_sub_dealer_order_id` varchar(50) DEFAULT NULL,
  `authorized_by` varchar(80) DEFAULT NULL,
  `authorization_date` datetime DEFAULT NULL,
  `mail_sent` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `mail_sent_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_confirmed_material_received` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `quantity_checking` varchar(50) DEFAULT NULL,
  `quality_checking` varchar(50) DEFAULT NULL,
  `remarks` text,
  `confirmed_material_received_datetime` varchar(40) DEFAULT NULL,
  `sale_order_prod_code` varchar(50) DEFAULT NULL,
  `sale_order_qty` varchar(50) DEFAULT NULL,
  `sale_order_dump` varchar(100) DEFAULT NULL,
  `Delivery_point` varchar(25) DEFAULT NULL,
  `Remarks_text` varchar(250) DEFAULT NULL,
  `Additional_data_1` varchar(25) DEFAULT NULL,
  `Additional_data_2` varchar(25) DEFAULT NULL,
  `Additional_data_3` varchar(25) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `T_APPERPDO_OFFLINE`
--

CREATE TABLE `T_APPERPDO_OFFLINE` (
  `id` bigint(20) NOT NULL,
  `ERPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERDT` varchar(50) DEFAULT NULL,
  `consignee_SAP_code` varchar(40) DEFAULT NULL,
  `consignee_name` text,
  `consignee_address` text,
  `consignee_code` varchar(50) DEFAULT NULL,
  `dns_consignee_code` varchar(50) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `customer_SAP_code` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `prod_code` varchar(50) DEFAULT NULL,
  `dns_prod_code` varchar(50) DEFAULT NULL,
  `prod_display_name` varchar(50) DEFAULT NULL,
  `QTY` varchar(30) DEFAULT NULL,
  `STATUS` enum('Order received','DO approved','Dispatched','Order authorized','Order canceled','Delivered','CREDIT CHECK FAILED') DEFAULT 'Order received',
  `freight` varchar(20) DEFAULT NULL,
  `branch_code` varchar(40) DEFAULT NULL,
  `dns_branch_code` varchar(40) DEFAULT NULL,
  `destination_code` varchar(50) DEFAULT NULL,
  `destination_name` text,
  `destination_address` text,
  `phone_no` varchar(30) DEFAULT NULL,
  `dump_status` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `dump_code` varchar(50) DEFAULT NULL,
  `dump_name` varchar(150) DEFAULT NULL,
  `mail_sent` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `mail_sent_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_confirmed_material_received` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `quantity_checking` varchar(50) DEFAULT NULL,
  `quality_checking` varchar(50) DEFAULT NULL,
  `remarks` text,
  `last_update_datetime` varchar(50) DEFAULT NULL,
  `unit_of_measurement` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `T_APPERPDO_OLD`
--

CREATE TABLE `T_APPERPDO_OLD` (
  `id` bigint(20) NOT NULL,
  `ref_order_id_for_duplicate_ck` varchar(50) DEFAULT NULL,
  `APPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERDT` varchar(50) DEFAULT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `order_for` varchar(200) DEFAULT NULL,
  `order_for_type` varchar(40) DEFAULT NULL,
  `consignee_name` text,
  `consignee_address` text,
  `sub_dealer_code` varchar(50) DEFAULT NULL,
  `dns_sub_dealer_code` varchar(50) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `prod_code` varchar(50) DEFAULT NULL,
  `dns_prod_code` varchar(50) DEFAULT NULL,
  `prod_display_name` varchar(50) DEFAULT NULL,
  `QTY` varchar(30) DEFAULT NULL,
  `STATUS` enum('Order received','DO approved','Dispatched','Order authorized','Order canceled','Delivered','CREDIT CHECK FAILED') DEFAULT 'Order received',
  `freight` varchar(20) DEFAULT NULL,
  `branch_code` varchar(40) DEFAULT NULL,
  `dns_branch_code` varchar(40) DEFAULT NULL,
  `destination_code` varchar(50) DEFAULT NULL,
  `destination_name` text,
  `destination_address` text,
  `phone_no` varchar(30) DEFAULT NULL,
  `dump_status` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `dump_code` varchar(50) DEFAULT NULL,
  `dump_name` varchar(150) DEFAULT NULL,
  `order_from` varchar(30) NOT NULL DEFAULT 'DEFAULT',
  `order_by` varchar(50) DEFAULT NULL,
  `dealer_truck` varchar(20) NOT NULL DEFAULT 'NO',
  `ref_sub_dealer_order_id` varchar(50) DEFAULT NULL,
  `authorized_by` varchar(80) DEFAULT NULL,
  `authorization_date` datetime DEFAULT NULL,
  `mail_sent` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `mail_sent_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_confirmed_material_received` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `quantity_checking` varchar(50) DEFAULT NULL,
  `quality_checking` varchar(50) DEFAULT NULL,
  `remarks` text,
  `confirmed_material_received_datetime` varchar(40) DEFAULT NULL,
  `sale_order_prod_code` varchar(50) DEFAULT NULL,
  `sale_order_qty` varchar(50) DEFAULT NULL,
  `sale_order_dump` varchar(100) DEFAULT NULL,
  `Delivery_point` varchar(25) DEFAULT NULL,
  `Remarks_text` varchar(250) DEFAULT NULL,
  `Additional_data_1` varchar(25) DEFAULT NULL,
  `Additional_data_2` varchar(25) DEFAULT NULL,
  `Additional_data_3` varchar(25) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `T_APPERPDO_TEMP`
--

CREATE TABLE `T_APPERPDO_TEMP` (
  `id` bigint(20) NOT NULL,
  `ref_order_id_for_duplicate_ck` varchar(50) DEFAULT NULL,
  `APPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERDT` varchar(50) DEFAULT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `order_for` varchar(200) DEFAULT NULL,
  `order_for_type` varchar(40) DEFAULT NULL,
  `consignee_name` varchar(80) DEFAULT NULL,
  `consignee_address` text,
  `sub_dealer_code` varchar(50) DEFAULT NULL,
  `dns_sub_dealer_code` varchar(50) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `prod_code` varchar(50) DEFAULT NULL,
  `dns_prod_code` varchar(50) DEFAULT NULL,
  `prod_display_name` varchar(50) DEFAULT NULL,
  `QTY` varchar(30) DEFAULT NULL,
  `STATUS` enum('Order received','DO approved','Dispatched','Order authorized','Order canceled') DEFAULT 'Order received',
  `freight` varchar(20) DEFAULT NULL,
  `branch_code` varchar(40) DEFAULT NULL,
  `dns_branch_code` varchar(40) DEFAULT NULL,
  `destination_code` varchar(50) DEFAULT NULL,
  `destination_name` text,
  `destination_address` text,
  `phone_no` varchar(30) DEFAULT NULL,
  `dump_status` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `dump_code` varchar(50) DEFAULT NULL,
  `dump_name` varchar(150) DEFAULT NULL,
  `order_from` varchar(30) NOT NULL DEFAULT 'DEFAULT',
  `order_by` varchar(50) DEFAULT NULL,
  `dealer_truck` varchar(20) NOT NULL DEFAULT 'NO',
  `ref_sub_dealer_order_id` varchar(50) DEFAULT NULL,
  `authorized_by` varchar(80) DEFAULT NULL,
  `authorization_date` datetime DEFAULT NULL,
  `is_confirmed_material_received` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `quantity_checking` varchar(50) DEFAULT NULL,
  `quality_checking` varchar(50) DEFAULT NULL,
  `remarks` text,
  `confirmed_material_received_datetime` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `T_DOCHALLAN`
--

CREATE TABLE `T_DOCHALLAN` (
  `id` bigint(20) NOT NULL,
  `APPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERDT` varchar(50) DEFAULT NULL,
  `SoTime` varchar(50) DEFAULT NULL,
  `InvItem` varchar(30) DEFAULT NULL,
  `CHALLANNO` varchar(50) DEFAULT NULL,
  `CHALLANDT` varchar(50) DEFAULT NULL,
  `InvTime` varchar(50) DEFAULT NULL,
  `prod_code` varchar(50) DEFAULT NULL,
  `dns_prod_code` varchar(50) DEFAULT NULL,
  `prod_display_name` varchar(50) DEFAULT NULL,
  `QTY` varchar(30) DEFAULT NULL,
  `CHALLANQTY` varchar(50) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `TRUCKNO` varchar(50) DEFAULT NULL,
  `DRIVERNO` varchar(50) DEFAULT NULL,
  `LMDT` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_dispatch_pn_sent` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `dispatch_pn_sent_remark` varchar(80) DEFAULT NULL,
  `is_confirmed_challan_material_received` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `challan_quantity_checking` varchar(50) DEFAULT NULL,
  `ch_quantity_no_of_bags` varchar(20) DEFAULT NULL,
  `challan_quality_checking` varchar(50) DEFAULT NULL,
  `ch_quality_no_of_damaged_bags` varchar(20) DEFAULT NULL,
  `challan_remarks` text,
  `confirmed_challan_material_received_datetime` varchar(40) DEFAULT NULL,
  `transporter_name` varchar(50) DEFAULT NULL,
  `plant` varchar(40) DEFAULT NULL,
  `ch_status` varchar(30) NOT NULL DEFAULT 'Pending'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `T_DOCHALLAN_ARCHIVE_24`
--

CREATE TABLE `T_DOCHALLAN_ARCHIVE_24` (
  `id` bigint(20) NOT NULL,
  `APPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERDT` varchar(50) DEFAULT NULL,
  `SoTime` varchar(50) DEFAULT NULL,
  `InvItem` varchar(30) DEFAULT NULL,
  `CHALLANNO` varchar(50) DEFAULT NULL,
  `CHALLANDT` varchar(50) DEFAULT NULL,
  `InvTime` varchar(50) DEFAULT NULL,
  `prod_code` varchar(50) DEFAULT NULL,
  `dns_prod_code` varchar(50) DEFAULT NULL,
  `prod_display_name` varchar(50) DEFAULT NULL,
  `QTY` varchar(30) DEFAULT NULL,
  `CHALLANQTY` varchar(50) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `TRUCKNO` varchar(50) DEFAULT NULL,
  `DRIVERNO` varchar(50) DEFAULT NULL,
  `LMDT` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_dispatch_pn_sent` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `dispatch_pn_sent_remark` varchar(80) DEFAULT NULL,
  `is_confirmed_challan_material_received` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `challan_quantity_checking` varchar(50) DEFAULT NULL,
  `ch_quantity_no_of_bags` varchar(20) DEFAULT NULL,
  `challan_quality_checking` varchar(50) DEFAULT NULL,
  `ch_quality_no_of_damaged_bags` varchar(20) DEFAULT NULL,
  `challan_remarks` text,
  `confirmed_challan_material_received_datetime` varchar(40) DEFAULT NULL,
  `transporter_name` varchar(50) DEFAULT NULL,
  `plant` varchar(40) DEFAULT NULL,
  `ch_status` varchar(30) NOT NULL DEFAULT 'Pending'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `T_DOINVOICE`
--

CREATE TABLE `T_DOINVOICE` (
  `id` bigint(20) NOT NULL,
  `APPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERNO` varchar(50) DEFAULT NULL,
  `ERPORDERDT` varchar(50) DEFAULT NULL,
  `CHALLANNO` varchar(50) DEFAULT NULL,
  `CHALLANDT` varchar(50) DEFAULT NULL,
  `INVNO` varchar(50) DEFAULT NULL,
  `INVDT` varchar(50) DEFAULT NULL,
  `prod_code` varchar(50) DEFAULT NULL,
  `dns_prod_code` varchar(50) DEFAULT NULL,
  `prod_display_name` varchar(50) DEFAULT NULL,
  `QTY` varchar(30) DEFAULT NULL,
  `INVQTY` varchar(50) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `TRUCKNO` varchar(50) DEFAULT NULL,
  `DRIVERNO` varchar(50) DEFAULT NULL,
  `LMDT` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_dispatch_pn_sent` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `dispatch_pn_sent_remark` varchar(80) DEFAULT NULL,
  `transporter_name` varchar(50) DEFAULT NULL,
  `inv_status` varchar(30) NOT NULL DEFAULT 'Pending',
  `destination` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `T_MAIN_ORDER_POP`
--

CREATE TABLE `T_MAIN_ORDER_POP` (
  `order_id` bigint(20) NOT NULL,
  `razorpay_order_id` varchar(40) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `customer_sap_code` varchar(50) DEFAULT NULL,
  `customer_name` varchar(60) DEFAULT NULL,
  `customer_region` varchar(20) DEFAULT NULL,
  `mobile` varchar(40) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `address` text,
  `pin` varchar(30) DEFAULT NULL,
  `remarks` text,
  `printed_address_pin` text,
  `contact_num_printed` varchar(30) DEFAULT NULL,
  `amount` varchar(30) DEFAULT NULL,
  `payment_by` varchar(40) DEFAULT NULL,
  `order_status` varchar(80) DEFAULT 'Pending',
  `tracking_id` varchar(50) DEFAULT NULL,
  `razorpay_payment_id` varchar(50) DEFAULT NULL,
  `razorpay_signature` varchar(250) DEFAULT NULL,
  `payment_mode` varchar(30) DEFAULT NULL,
  `card_name` varchar(50) DEFAULT NULL,
  `bank_ref_no` varchar(250) DEFAULT NULL,
  `failure_message` varchar(100) DEFAULT NULL,
  `status_message` varchar(100) DEFAULT NULL,
  `order_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tran_datetime` varchar(30) DEFAULT NULL,
  `bin_country` varchar(50) DEFAULT NULL,
  `response_code` varchar(30) DEFAULT NULL,
  `status_code` varchar(30) DEFAULT NULL,
  `currency` varchar(30) NOT NULL DEFAULT 'INR',
  `vault` varchar(20) DEFAULT NULL,
  `offer_type` varchar(50) DEFAULT NULL,
  `offer_code` varchar(30) DEFAULT NULL,
  `discount_value` varchar(30) DEFAULT NULL,
  `mer_amount` varchar(30) DEFAULT NULL,
  `eci_value` varchar(30) DEFAULT NULL,
  `retry` varchar(30) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `T_ORDER_POP`
--

CREATE TABLE `T_ORDER_POP` (
  `id` bigint(20) NOT NULL,
  `APPORDERNO` varchar(50) DEFAULT NULL,
  `the_order_id` varchar(30) DEFAULT NULL,
  `the_tracking_id` varchar(150) DEFAULT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `customer_code` varchar(50) DEFAULT NULL,
  `dns_customer_code` varchar(50) DEFAULT NULL,
  `prod_code` varchar(50) DEFAULT NULL,
  `dns_prod_code` varchar(50) DEFAULT NULL,
  `prod_display_name` varchar(50) DEFAULT NULL,
  `QTY` varchar(30) DEFAULT NULL,
  `prod_rate` varchar(20) DEFAULT NULL,
  `prod_amount` varchar(20) DEFAULT NULL,
  `gst_rate` varchar(20) DEFAULT NULL,
  `gst_amount` varchar(20) DEFAULT NULL,
  `prod_total_amount` varchar(20) DEFAULT NULL,
  `branch_code` varchar(40) DEFAULT NULL,
  `dns_branch_code` varchar(40) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `pin` varchar(30) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `printed_address_pin` varchar(255) DEFAULT NULL,
  `contact_num_printed` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `order_status` varchar(255) DEFAULT NULL,
  `admin_remark` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `T_SUBDEALER_ORDER`
--

CREATE TABLE `T_SUBDEALER_ORDER` (
  `id` bigint(20) NOT NULL,
  `ref_order_id_for_duplicate_ck` varchar(50) DEFAULT NULL,
  `sub_dealer_order_id` varchar(50) DEFAULT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `order_for` varchar(200) DEFAULT NULL,
  `order_for_type` varchar(40) DEFAULT NULL,
  `consignee_name` varchar(80) DEFAULT NULL,
  `consignee_address` text,
  `sub_dealer_code` varchar(50) DEFAULT NULL,
  `dns_sub_dealer_code` varchar(50) DEFAULT NULL,
  `sub_dealer_phone_no` varchar(30) DEFAULT NULL,
  `belong_dealer_code` varchar(50) DEFAULT NULL,
  `belong_dealer_dns_code` varchar(50) DEFAULT NULL,
  `belong_dealer_phone_no` varchar(30) DEFAULT NULL,
  `prod_code` varchar(50) DEFAULT NULL,
  `dns_prod_code` varchar(50) DEFAULT NULL,
  `prod_display_name` varchar(50) DEFAULT NULL,
  `QTY` varchar(30) DEFAULT NULL,
  `STATUS` enum('PENDING','APPROVED','REJECTED') NOT NULL DEFAULT 'PENDING',
  `freight` varchar(20) DEFAULT NULL,
  `branch_code` varchar(40) DEFAULT NULL,
  `dns_branch_code` varchar(40) DEFAULT NULL,
  `destination_code` varchar(50) DEFAULT NULL,
  `destination_name` text,
  `destination_address` text,
  `dump_status` enum('NO','YES') NOT NULL DEFAULT 'NO',
  `dump_code` varchar(50) DEFAULT NULL,
  `dump_name` varchar(150) DEFAULT NULL,
  `order_from` varchar(30) NOT NULL DEFAULT 'DEFAULT',
  `order_by` varchar(50) DEFAULT NULL,
  `dealer_truck` varchar(20) NOT NULL DEFAULT 'NO',
  `authorized_by` varchar(80) DEFAULT NULL,
  `authorization_date` datetime DEFAULT NULL,
  `mail_sent` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `mail_sent_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `verify_ledger_details`
--

CREATE TABLE `verify_ledger_details` (
  `vld_id` bigint(20) NOT NULL,
  `customer_code` varchar(80) NOT NULL,
  `dns_customer_code` varchar(80) NOT NULL,
  `ledger_year_month_day` varchar(50) NOT NULL,
  `total_amount_dr` varchar(50) NOT NULL,
  `total_amount_cr` varchar(50) NOT NULL,
  `status` enum('REJECTED','APPROVED') NOT NULL DEFAULT 'REJECTED',
  `comment` text NOT NULL,
  `saved_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `verify_ledger_details_backup`
--

CREATE TABLE `verify_ledger_details_backup` (
  `vld_id` bigint(20) NOT NULL,
  `customer_code` varchar(80) NOT NULL,
  `dns_customer_code` varchar(80) NOT NULL,
  `ledger_year_month_day` varchar(50) NOT NULL,
  `total_amount_dr` varchar(50) NOT NULL,
  `total_amount_cr` varchar(50) NOT NULL,
  `status` enum('REJECTED','APPROVED') NOT NULL DEFAULT 'REJECTED',
  `comment` text NOT NULL,
  `saved_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vertical_branch_employeewise_details`
--

CREATE TABLE `vertical_branch_employeewise_details` (
  `vertical_value` varchar(50) NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `emp_code` varchar(50) NOT NULL,
  `qty` double NOT NULL,
  `secondary_qty` double NOT NULL,
  `calls_made` double NOT NULL,
  `productive` double NOT NULL,
  `primary` double NOT NULL,
  `secondary` double NOT NULL,
  `operation_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vertical_mail_access`
--

CREATE TABLE `vertical_mail_access` (
  `vertical_code` text NOT NULL,
  `vertical_name` text NOT NULL,
  `headed_by` varchar(255) NOT NULL,
  `headed_by_email` varchar(255) DEFAULT NULL,
  `reporting_to` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `webservice_track_log`
--

CREATE TABLE `webservice_track_log` (
  `id` bigint(20) NOT NULL,
  `webservice_name` varchar(80) DEFAULT NULL,
  `customer_code` varchar(40) DEFAULT NULL,
  `details` text,
  `datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `zorderdetailsp`
--

CREATE TABLE `zorderdetailsp` (
  `id` bigint(20) NOT NULL,
  `APPORDERNO` varchar(35) DEFAULT NULL,
  `DATE` varchar(12) DEFAULT NULL,
  `time` varchar(12) DEFAULT NULL,
  `Cust_Code` varchar(10) DEFAULT NULL,
  `Consignee_Code` varchar(10) DEFAULT NULL,
  `Freight` varchar(3) DEFAULT NULL,
  `DestinationCode` varchar(10) DEFAULT NULL,
  `ProductCode` varchar(40) DEFAULT NULL,
  `Qty` varchar(17) DEFAULT NULL,
  `Unit` varchar(20) DEFAULT NULL,
  `PLANT` varchar(4) DEFAULT NULL,
  `STATUS` varchar(6) DEFAULT NULL,
  `OrderNo` varchar(10) DEFAULT NULL,
  `Remarks` varchar(50) DEFAULT NULL,
  `Delivery_point` varchar(25) DEFAULT NULL,
  `Remarks_text` varchar(250) DEFAULT NULL,
  `Additional_data_1` varchar(25) DEFAULT NULL,
  `Additional_data_2` varchar(25) DEFAULT NULL,
  `Additional_data_3` varchar(25) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `zorderdetailsp_03_10_24`
--

CREATE TABLE `zorderdetailsp_03_10_24` (
  `id` bigint(20) NOT NULL,
  `APPORDERNO` varchar(35) DEFAULT NULL,
  `DATE` varchar(12) DEFAULT NULL,
  `time` varchar(12) DEFAULT NULL,
  `Cust_Code` varchar(10) DEFAULT NULL,
  `Consignee_Code` varchar(10) DEFAULT NULL,
  `Freight` varchar(3) DEFAULT NULL,
  `DestinationCode` varchar(10) DEFAULT NULL,
  `ProductCode` varchar(40) DEFAULT NULL,
  `Qty` varchar(17) DEFAULT NULL,
  `Unit` varchar(20) DEFAULT NULL,
  `PLANT` varchar(4) DEFAULT NULL,
  `STATUS` varchar(6) DEFAULT NULL,
  `OrderNo` varchar(10) DEFAULT NULL,
  `Remarks` varchar(50) DEFAULT NULL,
  `Delivery_point` varchar(25) DEFAULT NULL,
  `Remarks_text` varchar(250) DEFAULT NULL,
  `Additional_data_1` varchar(25) DEFAULT NULL,
  `Additional_data_2` varchar(25) DEFAULT NULL,
  `Additional_data_3` varchar(25) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ZSD_DELIVERY`
--

CREATE TABLE `ZSD_DELIVERY` (
  `MANDT` varchar(3) DEFAULT NULL COMMENT 'Client',
  `INV_NO` varchar(10) DEFAULT NULL COMMENT 'Billing Document',
  `INV_ITEM` varchar(6) DEFAULT NULL COMMENT 'Billing Item',
  `INV_DATE` varchar(8) DEFAULT NULL COMMENT 'Billing Date',
  `INV_TIME` varchar(6) DEFAULT NULL COMMENT 'Field of type TIMS',
  `INV_QTY` varchar(17) DEFAULT NULL COMMENT 'Actual billed quantity',
  `SO_NO` varchar(10) DEFAULT NULL COMMENT 'Sales and Distribution Document Number',
  `SO_ITEM` varchar(6) DEFAULT NULL COMMENT 'Item number of the SD document',
  `SO_DATE` varchar(8) DEFAULT NULL COMMENT 'Date on which the record was created',
  `SO_TIME` varchar(6) DEFAULT NULL COMMENT 'Field of type TIMS',
  `SO_QTY` varchar(17) DEFAULT NULL COMMENT 'Target Quantity in Sales Units',
  `TRUCK_NO` varchar(40) DEFAULT NULL COMMENT 'Vehicle Number',
  `DRIVER_NO` varchar(11) DEFAULT NULL COMMENT 'Driver No',
  `TRANSPORTER_NAME` varchar(30) DEFAULT NULL COMMENT 'Driver Name',
  `PLANT` varchar(4) DEFAULT NULL COMMENT 'Plant',
  `DEL_STATUS` varchar(1) DEFAULT NULL COMMENT 'Delivery Status'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ZSS_Quot_Ord`
--

CREATE TABLE `ZSS_Quot_Ord` (
  `Sr_No` varchar(10) NOT NULL,
  `Unique_Store_ID` varchar(25) DEFAULT NULL,
  `Emp_Name` varchar(50) DEFAULT NULL,
  `Survey_Date` date DEFAULT NULL,
  `Survey_Month` varchar(15) DEFAULT NULL,
  `Lattitude` varchar(10) DEFAULT NULL,
  `Longitude` varchar(10) DEFAULT NULL,
  `Lead_Type` varchar(50) DEFAULT NULL,
  `Self_Others` varchar(10) DEFAULT NULL,
  `Site_Location` varchar(50) DEFAULT NULL,
  `State` varchar(50) DEFAULT NULL,
  `District` varchar(50) DEFAULT NULL,
  `Total_Qty_Required_MT` varchar(20) DEFAULT NULL,
  `Monthly_Qty_Required_MT` varchar(20) DEFAULT NULL,
  `Current_Brand_used` varchar(10) DEFAULT NULL,
  `Expected_Rate_Per_Bag` varchar(30) DEFAULT NULL,
  `Current_price` varchar(30) DEFAULT NULL,
  `Contact_person_name` varchar(50) DEFAULT NULL,
  `Designation` varchar(50) DEFAULT NULL,
  `Contact_number` varchar(10) DEFAULT NULL,
  `Mail_ID` varchar(25) DEFAULT NULL,
  `Mode_of_Payment` varchar(10) DEFAULT NULL,
  `Credit_Terms` varchar(20) DEFAULT NULL,
  `AAC_Block_is_required_or_not` varchar(5) DEFAULT NULL,
  `Category_type_of_construction` varchar(50) DEFAULT NULL,
  `Lead_status` varchar(5) DEFAULT NULL,
  `Type_of_lead` varchar(10) DEFAULT NULL,
  `Next_Visit_Date` date DEFAULT NULL,
  `Lead_Action` varchar(5) DEFAULT NULL,
  `Lead_Remarks` varchar(50) DEFAULT NULL,
  `Assigned_to` varchar(50) DEFAULT NULL,
  `Quotation_Required` varchar(5) DEFAULT NULL,
  `PO` varchar(10) DEFAULT NULL,
  `Remarks` varchar(50) DEFAULT NULL,
  `Requirement_timings` varchar(50) DEFAULT NULL,
  `Action_Taken` varchar(10) DEFAULT NULL,
  `Approved_Price` varchar(30) DEFAULT NULL,
  `Sales_Org` varchar(4) DEFAULT NULL,
  `Division` varchar(2) DEFAULT NULL,
  `Distribution_Channel` varchar(2) DEFAULT NULL,
  `Document_Type` varchar(4) DEFAULT NULL,
  `Customer_Reference_No` varchar(35) DEFAULT NULL,
  `Customer_Reference_Date` date DEFAULT NULL,
  `Valid_to_Date` varchar(40) DEFAULT NULL,
  `Material_Number` varchar(10) DEFAULT NULL,
  `Sold_to_Party` varchar(10) DEFAULT NULL,
  `Ship_to_Party` varchar(10) DEFAULT NULL,
  `Version` varchar(12) DEFAULT NULL,
  `Plant` varchar(4) DEFAULT NULL,
  `Share_Lead_Site_Details` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_master`
--
ALTER TABLE `admin_master`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `ageing`
--
ALTER TABLE `ageing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `allocation_details`
--
ALTER TABLE `allocation_details`
  ADD PRIMARY KEY (`allocation_id`),
  ADD KEY `customer_code` (`customer_id`),
  ADD KEY `dns_prod_code` (`dns_prod_code`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `challan_no` (`challan_no`);

--
-- Indexes for table `allocation_details_invoicewise`
--
ALTER TABLE `allocation_details_invoicewise`
  ADD PRIMARY KEY (`allocation_id`),
  ADD KEY `inv_date` (`inv_date`),
  ADD KEY `date_and_time` (`date_and_time`),
  ADD KEY `APPORDERNO` (`APPORDERNO`),
  ADD KEY `inv_no` (`inv_no`),
  ADD KEY `prod_desc` (`prod_desc`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `sub_dealer_id` (`sub_dealer_id`);

--
-- Indexes for table `allocation_invoice_del`
--
ALTER TABLE `allocation_invoice_del`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `apicalllog`
--
ALTER TABLE `apicalllog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `api_verification`
--
ALTER TABLE `api_verification`
  ADD PRIMARY KEY (`api_id`);

--
-- Indexes for table `app_service_track_log`
--
ALTER TABLE `app_service_track_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appservice_name` (`appservice_name`,`customer_code`,`datetime`);

--
-- Indexes for table `app_setting_master`
--
ALTER TABLE `app_setting_master`
  ADD PRIMARY KEY (`asm_id`),
  ADD KEY `company_id` (`the_key_name`);

--
-- Indexes for table `app_updation`
--
ALTER TABLE `app_updation`
  ADD PRIMARY KEY (`device_id`);

--
-- Indexes for table `arc_consumer_reg`
--
ALTER TABLE `arc_consumer_reg`
  ADD PRIMARY KEY (`ac_id`);

--
-- Indexes for table `arc_consumer_reg_old`
--
ALTER TABLE `arc_consumer_reg_old`
  ADD PRIMARY KEY (`ac_id`);

--
-- Indexes for table `arc_dealer_cust_point_table`
--
ALTER TABLE `arc_dealer_cust_point_table`
  ADD PRIMARY KEY (`adcpt_id`);

--
-- Indexes for table `arc_gift_catalogue`
--
ALTER TABLE `arc_gift_catalogue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `arc_gift_redeem_table`
--
ALTER TABLE `arc_gift_redeem_table`
  ADD PRIMARY KEY (`ac_id`);

--
-- Indexes for table `auto_notification_log`
--
ALTER TABLE `auto_notification_log`
  ADD PRIMARY KEY (`anl_id`);
ALTER TABLE `auto_notification_log` ADD FULLTEXT KEY `message_text` (`message_text`);

--
-- Indexes for table `bank_master`
--
ALTER TABLE `bank_master`
  ADD PRIMARY KEY (`bank_id`);

--
-- Indexes for table `branch_consumer_scheme_status`
--
ALTER TABLE `branch_consumer_scheme_status`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `branch_credit_limit_status`
--
ALTER TABLE `branch_credit_limit_status`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `branch_destination_freight`
--
ALTER TABLE `branch_destination_freight`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `branch_dump`
--
ALTER TABLE `branch_dump`
  ADD PRIMARY KEY (`sl_no`),
  ADD KEY `branch_code` (`branch_code`),
  ADD KEY `dump_code` (`dump_code`),
  ADD KEY `is_plant` (`is_plant`),
  ADD KEY `acedns` (`acedns`);

--
-- Indexes for table `branch_game_status`
--
ALTER TABLE `branch_game_status`
  ADD PRIMARY KEY (`sl_no`),
  ADD KEY `branch_code` (`branch_code`);

--
-- Indexes for table `branch_master`
--
ALTER TABLE `branch_master`
  ADD PRIMARY KEY (`branch_code`),
  ADD KEY `branch_code` (`branch_code`,`dns_branch_code`,`branch_name`,`plant_name`);

--
-- Indexes for table `branch_PGstatus`
--
ALTER TABLE `branch_PGstatus`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `branch_pop_product`
--
ALTER TABLE `branch_pop_product`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `branch_rssd_allocation_days`
--
ALTER TABLE `branch_rssd_allocation_days`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `branch_schemes_PDF`
--
ALTER TABLE `branch_schemes_PDF`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `broker_master`
--
ALTER TABLE `broker_master`
  ADD PRIMARY KEY (`broker_id`),
  ADD KEY `dns_broker_id` (`dns_broker_id`),
  ADD KEY `broker_name` (`broker_name`),
  ADD KEY `acedns` (`acedns`),
  ADD KEY `download_time` (`download_time`);

--
-- Indexes for table `changepassword`
--
ALTER TABLE `changepassword`
  ADD PRIMARY KEY (`customer_code`),
  ADD KEY `emp_code` (`emp_code`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `dns_customer_code` (`dns_customer_code`),
  ADD KEY `app_version` (`app_version`),
  ADD KEY `newpassword` (`newpassword`),
  ADD KEY `oldpassword` (`oldpassword`),
  ADD KEY `deviceid` (`deviceid`),
  ADD KEY `device_type` (`device_type`),
  ADD KEY `is_licensed` (`is_licensed`);

--
-- Indexes for table `ch_data`
--
ALTER TABLE `ch_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_master`
--
ALTER TABLE `company_master`
  ADD PRIMARY KEY (`comp_code`);

--
-- Indexes for table `consumer_scheme`
--
ALTER TABLE `consumer_scheme`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cron_update_table`
--
ALTER TABLE `cron_update_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_broker_relation`
--
ALTER TABLE `customer_broker_relation`
  ADD PRIMARY KEY (`customer_code`,`broker_code`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `broker_code` (`broker_code`),
  ADD KEY `acedns` (`acedns`),
  ADD KEY `download_time` (`download_time`);

--
-- Indexes for table `customer_broker_relation_test`
--
ALTER TABLE `customer_broker_relation_test`
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `broker_code` (`broker_code`),
  ADD KEY `acedns` (`acedns`),
  ADD KEY `download_time` (`download_time`);

--
-- Indexes for table `customer_destination`
--
ALTER TABLE `customer_destination`
  ADD PRIMARY KEY (`sl_no`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `destination_code` (`destination_code`),
  ADD KEY `acedns` (`acedns`);

--
-- Indexes for table `customer_invoice_table`
--
ALTER TABLE `customer_invoice_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_master`
--
ALTER TABLE `customer_master`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `dns_customer_code` (`dns_customer_code`),
  ADD KEY `customer_name` (`customer_name`),
  ADD KEY `route_code` (`route_code`),
  ADD KEY `emp_code` (`emp_code`),
  ADD KEY `acedns` (`acedns`),
  ADD KEY `black_list` (`black_list`),
  ADD KEY `cust_type` (`cust_type`),
  ADD KEY `rds_tag` (`rds_tag`),
  ADD KEY `download_time` (`download_time`),
  ADD KEY `download_time_credit_limit` (`download_time_credit_limit`),
  ADD KEY `branch_code` (`branch_code`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `phone_no` (`phone_no`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `customer_master_STAR`
--
ALTER TABLE `customer_master_STAR`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_master_temp`
--
ALTER TABLE `customer_master_temp`
  ADD PRIMARY KEY (`customer_code`);

--
-- Indexes for table `customer_refference`
--
ALTER TABLE `customer_refference`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `customer_route_emp_relation`
--
ALTER TABLE `customer_route_emp_relation`
  ADD PRIMARY KEY (`sl_no`),
  ADD KEY `emp_code` (`emp_code`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `acedns` (`acedns`);

--
-- Indexes for table `customer_route_emp_relation_test`
--
ALTER TABLE `customer_route_emp_relation_test`
  ADD PRIMARY KEY (`customer_code`,`route_code`,`emp_code`);

--
-- Indexes for table `customer_visit_details`
--
ALTER TABLE `customer_visit_details`
  ADD PRIMARY KEY (`row_id`),
  ADD UNIQUE KEY `emp_code` (`emp_code`,`trans_id`,`customer_code`,`route_code`);

--
-- Indexes for table `cust_data`
--
ALTER TABLE `cust_data`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_refresh_log`
--
ALTER TABLE `data_refresh_log`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `dbbackupcheck`
--
ALTER TABLE `dbbackupcheck`
  ADD PRIMARY KEY (`emp_code`);

--
-- Indexes for table `db_backup_files`
--
ALTER TABLE `db_backup_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `db_version`
--
ALTER TABLE `db_version`
  ADD PRIMARY KEY (`version_code`);

--
-- Indexes for table `dealer_app_version`
--
ALTER TABLE `dealer_app_version`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dealer_reward_status`
--
ALTER TABLE `dealer_reward_status`
  ADD PRIMARY KEY (`sl_no`),
  ADD UNIQUE KEY `emp_code` (`emp_code`);

--
-- Indexes for table `dealer_rssd_test`
--
ALTER TABLE `dealer_rssd_test`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dealer_sales_team_visit_survey`
--
ALTER TABLE `dealer_sales_team_visit_survey`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `dealer_security_ledger_status`
--
ALTER TABLE `dealer_security_ledger_status`
  ADD PRIMARY KEY (`sl_no`),
  ADD KEY `customer_code` (`customer_code`);

--
-- Indexes for table `dealer_security_ledger_status_old`
--
ALTER TABLE `dealer_security_ledger_status_old`
  ADD PRIMARY KEY (`sl_no`),
  ADD KEY `customer_code` (`customer_code`);

--
-- Indexes for table `dealer_tour_status`
--
ALTER TABLE `dealer_tour_status`
  ADD PRIMARY KEY (`sl_no`),
  ADD KEY `customer_code` (`customer_code`);

--
-- Indexes for table `delivery_status_log`
--
ALTER TABLE `delivery_status_log`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `destination_master`
--
ALTER TABLE `destination_master`
  ADD PRIMARY KEY (`destination_code`),
  ADD KEY `dns_destination_code` (`dns_destination_code`),
  ADD KEY `destination_name` (`destination_name`),
  ADD KEY `destination_code` (`destination_code`);

--
-- Indexes for table `destination_wise_price`
--
ALTER TABLE `destination_wise_price`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `do_order_cancel_by_log`
--
ALTER TABLE `do_order_cancel_by_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dummy_registration_for_app_approval`
--
ALTER TABLE `dummy_registration_for_app_approval`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_kyc_master`
--
ALTER TABLE `employee_kyc_master`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emp_code` (`emp_code`),
  ADD KEY `whatsapp_no` (`whatsapp_no`,`email_id`,`last_updated_datetime`),
  ADD KEY `customer_code` (`customer_code`,`dns_customer_code`);

--
-- Indexes for table `employee_kyc_master_backup`
--
ALTER TABLE `employee_kyc_master_backup`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emp_code` (`emp_code`),
  ADD KEY `whatsapp_no` (`whatsapp_no`,`email_id`,`last_updated_datetime`);

--
-- Indexes for table `employee_master`
--
ALTER TABLE `employee_master`
  ADD PRIMARY KEY (`emp_code`);

--
-- Indexes for table `employee_master_bkup`
--
ALTER TABLE `employee_master_bkup`
  ADD PRIMARY KEY (`emp_code`),
  ADD KEY `emp_name` (`emp_name`),
  ADD KEY `emp_code` (`emp_code`),
  ADD KEY `reporting_to` (`reporting_to`),
  ADD KEY `dns_emp_code` (`dns_emp_code`),
  ADD KEY `acedns` (`acedns`),
  ADD KEY `vertical_value` (`vertical_value`),
  ADD KEY `sale_access` (`sale_access`),
  ADD KEY `District` (`District`),
  ADD KEY `state` (`state`),
  ADD KEY `zone` (`zone`),
  ADD KEY `app_access` (`app_access`),
  ADD KEY `download_time` (`download_time`);

--
-- Indexes for table `employee_master_one`
--
ALTER TABLE `employee_master_one`
  ADD PRIMARY KEY (`emp_code`);

--
-- Indexes for table `employee_master_two`
--
ALTER TABLE `employee_master_two`
  ADD PRIMARY KEY (`emp_code`),
  ADD KEY `emp_name` (`emp_name`),
  ADD KEY `emp_code` (`emp_code`),
  ADD KEY `acedns` (`acedns`),
  ADD KEY `download_time` (`download_time`),
  ADD KEY `dns_emp_code` (`dns_emp_code`);

--
-- Indexes for table `emp_data_download_log`
--
ALTER TABLE `emp_data_download_log`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `emp_data_update_log`
--
ALTER TABLE `emp_data_update_log`
  ADD PRIMARY KEY (`emp_code`);

--
-- Indexes for table `emp_menu_access`
--
ALTER TABLE `emp_menu_access`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `epod_details`
--
ALTER TABLE `epod_details`
  ADD PRIMARY KEY (`epod_id`),
  ADD KEY `date_and_time` (`date_and_time`),
  ADD KEY `challan_no` (`challan_no`);

--
-- Indexes for table `foot_soldier`
--
ALTER TABLE `foot_soldier`
  ADD PRIMARY KEY (`foot_soldier_id`);

--
-- Indexes for table `fpx_dealer_list`
--
ALTER TABLE `fpx_dealer_list`
  ADD PRIMARY KEY (`sl_no`),
  ADD KEY `dealer_sap_code` (`dealer_sap_code`),
  ADD KEY `status` (`status`),
  ADD KEY `dealer_name` (`dealer_name`);

--
-- Indexes for table `fpx_dealer_truck_mapping`
--
ALTER TABLE `fpx_dealer_truck_mapping`
  ADD PRIMARY KEY (`sl_no`),
  ADD KEY `truck_no` (`truck_no`),
  ADD KEY `dealer_name` (`dealer_name`),
  ADD KEY `dealer_sap_code` (`dealer_sap_code`);

--
-- Indexes for table `generic_oil_master`
--
ALTER TABLE `generic_oil_master`
  ADD PRIMARY KEY (`oil_name`);

--
-- Indexes for table `ledger`
--
ALTER TABLE `ledger`
  ADD PRIMARY KEY (`ldg_id`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `dns_customer_code` (`dns_customer_code`);

--
-- Indexes for table `ledger_balance`
--
ALTER TABLE `ledger_balance`
  ADD PRIMARY KEY (`lb_id`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `dns_customer_code` (`dns_customer_code`);

--
-- Indexes for table `ledger_transaction_table`
--
ALTER TABLE `ledger_transaction_table`
  ADD PRIMARY KEY (`lt_order_id`);

--
-- Indexes for table `lifting`
--
ALTER TABLE `lifting`
  ADD PRIMARY KEY (`lid`);

--
-- Indexes for table `lifting_date_validation`
--
ALTER TABLE `lifting_date_validation`
  ADD PRIMARY KEY (`sl_no`),
  ADD KEY `branch` (`branch`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`trans_id`),
  ADD KEY `emp_code` (`emp_code`,`trans_id`,`date`,`transferred`);

--
-- Indexes for table `lottery_master`
--
ALTER TABLE `lottery_master`
  ADD PRIMARY KEY (`lottery_no`);

--
-- Indexes for table `mail_access`
--
ALTER TABLE `mail_access`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `market_feedback`
--
ALTER TABLE `market_feedback`
  ADD KEY `market_feedback_id` (`market_feedback_id`,`route_code`,`customer_code`,`product_group`,`competitor_name`);

--
-- Indexes for table `menu_access`
--
ALTER TABLE `menu_access`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `menu_master`
--
ALTER TABLE `menu_master`
  ADD PRIMARY KEY (`menu_id`),
  ADD KEY `menu_name` (`menu_name`),
  ADD KEY `menu_admin_page` (`menu_admin_page`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `mf_stk_audit_details`
--
ALTER TABLE `mf_stk_audit_details`
  ADD KEY `mf_stk_audit_id` (`mf_stk_audit_id`,`competitor_name`);

--
-- Indexes for table `mf_stk_audit_header`
--
ALTER TABLE `mf_stk_audit_header`
  ADD KEY `mf_stk_audit_id` (`mf_stk_audit_id`,`customer_code`);

--
-- Indexes for table `missing_erp_challan`
--
ALTER TABLE `missing_erp_challan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `APPORDERNO` (`APPORDERNO`),
  ADD KEY `ERPORDERNO` (`ERPORDERNO`),
  ADD KEY `CHALLANNO` (`CHALLANNO`),
  ADD KEY `prod_code` (`prod_code`),
  ADD KEY `dns_prod_code` (`dns_prod_code`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `dns_customer_code` (`dns_customer_code`);

--
-- Indexes for table `missing_erp_orders`
--
ALTER TABLE `missing_erp_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `APPORDERNO` (`APPORDERNO`),
  ADD KEY `ERPORDERNO` (`ERPORDERNO`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `dns_customer_code` (`dns_customer_code`),
  ADD KEY `prod_code` (`prod_code`),
  ADD KEY `dns_prod_code` (`dns_prod_code`),
  ADD KEY `STATUS` (`STATUS`),
  ADD KEY `prod_display_name` (`prod_display_name`);

--
-- Indexes for table `mis_data_details`
--
ALTER TABLE `mis_data_details`
  ADD PRIMARY KEY (`emp_code`),
  ADD KEY `emp_code` (`emp_code`,`sale_access`);

--
-- Indexes for table `mis_details_emp_datewise`
--
ALTER TABLE `mis_details_emp_datewise`
  ADD PRIMARY KEY (`sl_no`),
  ADD KEY `emp_code` (`emp_code`,`operation_date`);

--
-- Indexes for table `mrp`
--
ALTER TABLE `mrp`
  ADD PRIMARY KEY (`mrp_code`),
  ADD KEY `branch_code` (`branch_code`),
  ADD KEY `product_code` (`product_code`),
  ADD KEY `vertical_value` (`vertical_value`),
  ADD KEY `download_time` (`download_time`);

--
-- Indexes for table `north_east_branch`
--
ALTER TABLE `north_east_branch`
  ADD PRIMARY KEY (`sl_no`),
  ADD KEY `branch_code` (`branch_name`);

--
-- Indexes for table `notes_info_details`
--
ALTER TABLE `notes_info_details`
  ADD PRIMARY KEY (`notes_info_id`);

--
-- Indexes for table `notice_2023`
--
ALTER TABLE `notice_2023`
  ADD PRIMARY KEY (`sf_id`);

--
-- Indexes for table `notice_branch`
--
ALTER TABLE `notice_branch`
  ADD PRIMARY KEY (`nb_id`);

--
-- Indexes for table `notification_message`
--
ALTER TABLE `notification_message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_read_table`
--
ALTER TABLE `notification_read_table`
  ADD PRIMARY KEY (`nrt_id`);

--
-- Indexes for table `not_north_east_branch`
--
ALTER TABLE `not_north_east_branch`
  ADD PRIMARY KEY (`sl_no`),
  ADD KEY `branch_code` (`branch_name`);

--
-- Indexes for table `nye_branch`
--
ALTER TABLE `nye_branch`
  ADD PRIMARY KEY (`nb_id`);

--
-- Indexes for table `orderdata`
--
ALTER TABLE `orderdata`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD KEY `order_no` (`order_no`,`sku_code`,`mrp_code`);

--
-- Indexes for table `order_header`
--
ALTER TABLE `order_header`
  ADD PRIMARY KEY (`order_no`),
  ADD KEY `order_no` (`order_no`,`customer_code`,`branch_code`,`destination_code`,`vertical_value`,`sale_type`,`order_type`,`transaction_type`,`transferred`);

--
-- Indexes for table `order_query`
--
ALTER TABLE `order_query`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_restriction`
--
ALTER TABLE `order_restriction`
  ADD PRIMARY KEY (`SAP_code`);

--
-- Indexes for table `outstanding`
--
ALTER TABLE `outstanding`
  ADD KEY `customer_code` (`customer_code`);

--
-- Indexes for table `payment_details`
--
ALTER TABLE `payment_details`
  ADD KEY `receipt_id` (`receipt_id`,`invoice_id`);

--
-- Indexes for table `payment_header`
--
ALTER TABLE `payment_header`
  ADD PRIMARY KEY (`receipt_id`),
  ADD KEY `receipt_id` (`receipt_id`,`customer_code`,`transferred`);

--
-- Indexes for table `pcustomergroup`
--
ALTER TABLE `pcustomergroup`
  ADD PRIMARY KEY (`customer_grp`);

--
-- Indexes for table `performance`
--
ALTER TABLE `performance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `place_order_lifting_days`
--
ALTER TABLE `place_order_lifting_days`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `pop_order`
--
ALTER TABLE `pop_order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pop_order_03_09_2024`
--
ALTER TABLE `pop_order_03_09_2024`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pop_product_master`
--
ALTER TABLE `pop_product_master`
  ADD PRIMARY KEY (`sl_no`),
  ADD KEY `payment_gateway` (`payment_gateway`),
  ADD KEY `dns_prod_code` (`dns_prod_code`),
  ADD KEY `dealer_login` (`dealer_login`);

--
-- Indexes for table `prev_stock_counting_master`
--
ALTER TABLE `prev_stock_counting_master`
  ADD PRIMARY KEY (`customer_code`,`product_code`);

--
-- Indexes for table `product_brand_master`
--
ALTER TABLE `product_brand_master`
  ADD PRIMARY KEY (`product_sub_group_code`,`product_brand_name`);

--
-- Indexes for table `product_group_master`
--
ALTER TABLE `product_group_master`
  ADD PRIMARY KEY (`product_group_code`),
  ADD KEY `product_group_code` (`product_group_code`,`dns_product_group_code`,`product_group_name`,`download_time`,`vertical_value`,`acedns`);

--
-- Indexes for table `product_master`
--
ALTER TABLE `product_master`
  ADD PRIMARY KEY (`branch_code`,`prod_code`),
  ADD KEY `dns_prod_code` (`dns_prod_code`),
  ADD KEY `acedns` (`acedns`),
  ADD KEY `branch_code` (`branch_code`),
  ADD KEY `download_time` (`download_time`),
  ADD KEY `vertical_value` (`vertical_value`);
ALTER TABLE `product_master` ADD FULLTEXT KEY `prod_desc` (`prod_desc`);

--
-- Indexes for table `product_master_temp`
--
ALTER TABLE `product_master_temp`
  ADD PRIMARY KEY (`branch_code`,`prod_code`);

--
-- Indexes for table `product_master_test`
--
ALTER TABLE `product_master_test`
  ADD PRIMARY KEY (`branch_code`,`prod_code`),
  ADD KEY `branch_code` (`branch_code`,`prod_code`,`dns_prod_code`,`product_group_code`,`product_sub_group_code`,`product_brand_code`,`cl_stk`,`acedns`,`black_list`,`vertical_value`,`download_time`,`download_time_cl_stk`);

--
-- Indexes for table `product_sub_group_master`
--
ALTER TABLE `product_sub_group_master`
  ADD PRIMARY KEY (`product_sub_group_code`,`product_group_code`);

--
-- Indexes for table `prospective_customer_header`
--
ALTER TABLE `prospective_customer_header`
  ADD PRIMARY KEY (`trans_id`);

--
-- Indexes for table `prospective_customer_master`
--
ALTER TABLE `prospective_customer_master`
  ADD PRIMARY KEY (`customer_code`);

--
-- Indexes for table `ptblcustomermaster`
--
ALTER TABLE `ptblcustomermaster`
  ADD KEY `KUNNR` (`KUNNR`),
  ADD KEY `LZONE` (`LZONE`),
  ADD KEY `VWERK` (`VWERK`),
  ADD KEY `KDGRP` (`KDGRP`),
  ADD KEY `ADDITIONAL_DATA1` (`ADDITIONAL_DATA1`),
  ADD KEY `AEDAT` (`AEDAT`),
  ADD KEY `SEARCH_TERM2` (`SEARCH_TERM2`),
  ADD KEY `TRANSZONE` (`TRANSZONE`);

--
-- Indexes for table `pZSDCUST`
--
ALTER TABLE `pZSDCUST`
  ADD PRIMARY KEY (`SOLD_PARTY`,`PARTY`,`PARVW`),
  ADD KEY `PARTY` (`PARTY`),
  ADD KEY `SOLD_PARTY` (`SOLD_PARTY`);

--
-- Indexes for table `rds_master`
--
ALTER TABLE `rds_master`
  ADD PRIMARY KEY (`rds_code`,`emp_code`);

--
-- Indexes for table `redeeme_details`
--
ALTER TABLE `redeeme_details`
  ADD PRIMARY KEY (`sn`);

--
-- Indexes for table `remote_user`
--
ALTER TABLE `remote_user`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `ROE_branch_list`
--
ALTER TABLE `ROE_branch_list`
  ADD PRIMARY KEY (`nb_id`);

--
-- Indexes for table `route_customer_plan`
--
ALTER TABLE `route_customer_plan`
  ADD PRIMARY KEY (`route_plan_trans_id`,`route_code`,`visit_date`,`customer_code`);

--
-- Indexes for table `route_refference`
--
ALTER TABLE `route_refference`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `sale_rate`
--
ALTER TABLE `sale_rate`
  ADD PRIMARY KEY (`sale_rate_code`);

--
-- Indexes for table `SAP_erp_dump_mapping`
--
ALTER TABLE `SAP_erp_dump_mapping`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `SAP_erp_product_mapping`
--
ALTER TABLE `SAP_erp_product_mapping`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `selected_menu_for_user`
--
ALTER TABLE `selected_menu_for_user`
  ADD PRIMARY KEY (`smid`);

--
-- Indexes for table `self_appraisal`
--
ALTER TABLE `self_appraisal`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `self_appraisal_branch_wise`
--
ALTER TABLE `self_appraisal_branch_wise`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `self_appraisal_customer_wise`
--
ALTER TABLE `self_appraisal_customer_wise`
  ADD PRIMARY KEY (`sl_no`),
  ADD KEY `customer_code` (`customer_code`);

--
-- Indexes for table `self_appraisal_product_wise`
--
ALTER TABLE `self_appraisal_product_wise`
  ADD PRIMARY KEY (`sl_no`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `prod_code` (`prod_code`),
  ADD KEY `emp_code` (`emp_code`);

--
-- Indexes for table `sp_destination`
--
ALTER TABLE `sp_destination`
  ADD PRIMARY KEY (`sl_no`),
  ADD KEY `customer_code` (`broker_id`),
  ADD KEY `destination_code` (`destination_code`),
  ADD KEY `acedns` (`acedns`);

--
-- Indexes for table `sqllog`
--
ALTER TABLE `sqllog`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `startreport_admin`
--
ALTER TABLE `startreport_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `start_slider`
--
ALTER TABLE `start_slider`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subdealer_branch_schemes_PDF`
--
ALTER TABLE `subdealer_branch_schemes_PDF`
  ADD PRIMARY KEY (`sl_no`);

--
-- Indexes for table `subdlr_data`
--
ALTER TABLE `subdlr_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dns_code` (`dns_code`);

--
-- Indexes for table `survey_form`
--
ALTER TABLE `survey_form`
  ADD PRIMARY KEY (`sf_id`);

--
-- Indexes for table `survey_form_18_20`
--
ALTER TABLE `survey_form_18_20`
  ADD PRIMARY KEY (`sf_id`);

--
-- Indexes for table `survey_form_fy_21_22_23`
--
ALTER TABLE `survey_form_fy_21_22_23`
  ADD PRIMARY KEY (`sf_id`);

--
-- Indexes for table `survey_form_prev_data`
--
ALTER TABLE `survey_form_prev_data`
  ADD PRIMARY KEY (`sf_id`);

--
-- Indexes for table `survey_for_new_year_eve_carnival_concert`
--
ALTER TABLE `survey_for_new_year_eve_carnival_concert`
  ADD PRIMARY KEY (`sf_id`);

--
-- Indexes for table `table_structure_master`
--
ALTER TABLE `table_structure_master`
  ADD PRIMARY KEY (`t_structure_id`);

--
-- Indexes for table `table_structure_updation`
--
ALTER TABLE `table_structure_updation`
  ADD PRIMARY KEY (`emp_code`,`device_id`);

--
-- Indexes for table `tbl_customer_master`
--
ALTER TABLE `tbl_customer_master`
  ADD PRIMARY KEY (`KUNNR`,`VKORG`,`DWERK_EXT`);

--
-- Indexes for table `temporary_table_branch_email`
--
ALTER TABLE `temporary_table_branch_email`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `T_APPERPDO`
--
ALTER TABLE `T_APPERPDO`
  ADD PRIMARY KEY (`id`),
  ADD KEY `APPORDERNO` (`APPORDERNO`),
  ADD KEY `ERPORDERNO` (`ERPORDERNO`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `dns_customer_code` (`dns_customer_code`),
  ADD KEY `prod_code` (`prod_code`),
  ADD KEY `dns_prod_code` (`dns_prod_code`),
  ADD KEY `STATUS` (`STATUS`);

--
-- Indexes for table `T_APPERPDO_03_10_24`
--
ALTER TABLE `T_APPERPDO_03_10_24`
  ADD PRIMARY KEY (`id`),
  ADD KEY `APPORDERNO` (`APPORDERNO`),
  ADD KEY `ERPORDERNO` (`ERPORDERNO`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `dns_customer_code` (`dns_customer_code`),
  ADD KEY `prod_code` (`prod_code`),
  ADD KEY `dns_prod_code` (`dns_prod_code`),
  ADD KEY `STATUS` (`STATUS`);

--
-- Indexes for table `T_APPERPDO_ARCHIVE_24`
--
ALTER TABLE `T_APPERPDO_ARCHIVE_24`
  ADD PRIMARY KEY (`id`),
  ADD KEY `APPORDERNO` (`APPORDERNO`),
  ADD KEY `ERPORDERNO` (`ERPORDERNO`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `dns_customer_code` (`dns_customer_code`),
  ADD KEY `prod_code` (`prod_code`),
  ADD KEY `dns_prod_code` (`dns_prod_code`),
  ADD KEY `STATUS` (`STATUS`);

--
-- Indexes for table `T_APPERPDO_INVOICE`
--
ALTER TABLE `T_APPERPDO_INVOICE`
  ADD PRIMARY KEY (`id`),
  ADD KEY `APPORDERNO` (`APPORDERNO`),
  ADD KEY `ERPORDERNO` (`ERPORDERNO`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `dns_customer_code` (`dns_customer_code`),
  ADD KEY `prod_code` (`prod_code`),
  ADD KEY `dns_prod_code` (`dns_prod_code`),
  ADD KEY `STATUS` (`STATUS`);

--
-- Indexes for table `T_APPERPDO_OFFLINE`
--
ALTER TABLE `T_APPERPDO_OFFLINE`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ERPORDERNO` (`ERPORDERNO`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `dns_customer_code` (`dns_customer_code`),
  ADD KEY `prod_code` (`prod_code`),
  ADD KEY `dns_prod_code` (`dns_prod_code`),
  ADD KEY `STATUS` (`STATUS`);

--
-- Indexes for table `T_APPERPDO_OLD`
--
ALTER TABLE `T_APPERPDO_OLD`
  ADD PRIMARY KEY (`id`),
  ADD KEY `APPORDERNO` (`APPORDERNO`),
  ADD KEY `ERPORDERNO` (`ERPORDERNO`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `dns_customer_code` (`dns_customer_code`),
  ADD KEY `prod_code` (`prod_code`),
  ADD KEY `dns_prod_code` (`dns_prod_code`),
  ADD KEY `STATUS` (`STATUS`);

--
-- Indexes for table `T_APPERPDO_TEMP`
--
ALTER TABLE `T_APPERPDO_TEMP`
  ADD PRIMARY KEY (`id`),
  ADD KEY `APPORDERNO` (`APPORDERNO`),
  ADD KEY `ERPORDERNO` (`ERPORDERNO`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `dns_customer_code` (`dns_customer_code`),
  ADD KEY `prod_code` (`prod_code`),
  ADD KEY `dns_prod_code` (`dns_prod_code`),
  ADD KEY `STATUS` (`STATUS`);

--
-- Indexes for table `T_DOCHALLAN`
--
ALTER TABLE `T_DOCHALLAN`
  ADD PRIMARY KEY (`id`),
  ADD KEY `APPORDERNO` (`APPORDERNO`),
  ADD KEY `ERPORDERNO` (`ERPORDERNO`),
  ADD KEY `CHALLANNO` (`CHALLANNO`),
  ADD KEY `prod_code` (`prod_code`),
  ADD KEY `dns_prod_code` (`dns_prod_code`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `dns_customer_code` (`dns_customer_code`),
  ADD KEY `ERPORDERDT` (`ERPORDERDT`),
  ADD KEY `LMDT` (`LMDT`),
  ADD KEY `CHALLANDT` (`CHALLANDT`);

--
-- Indexes for table `T_DOCHALLAN_ARCHIVE_24`
--
ALTER TABLE `T_DOCHALLAN_ARCHIVE_24`
  ADD PRIMARY KEY (`id`),
  ADD KEY `APPORDERNO` (`APPORDERNO`),
  ADD KEY `ERPORDERNO` (`ERPORDERNO`),
  ADD KEY `CHALLANNO` (`CHALLANNO`),
  ADD KEY `prod_code` (`prod_code`),
  ADD KEY `dns_prod_code` (`dns_prod_code`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `dns_customer_code` (`dns_customer_code`),
  ADD KEY `ERPORDERDT` (`ERPORDERDT`),
  ADD KEY `LMDT` (`LMDT`),
  ADD KEY `CHALLANDT` (`CHALLANDT`);

--
-- Indexes for table `T_DOINVOICE`
--
ALTER TABLE `T_DOINVOICE`
  ADD PRIMARY KEY (`id`),
  ADD KEY `APPORDERNO` (`APPORDERNO`),
  ADD KEY `ERPORDERNO` (`ERPORDERNO`),
  ADD KEY `CHALLANNO` (`CHALLANNO`),
  ADD KEY `prod_code` (`prod_code`),
  ADD KEY `dns_prod_code` (`dns_prod_code`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `dns_customer_code` (`dns_customer_code`),
  ADD KEY `ERPORDERDT` (`ERPORDERDT`),
  ADD KEY `LMDT` (`LMDT`),
  ADD KEY `CHALLANDT` (`CHALLANDT`),
  ADD KEY `prod_display_name` (`prod_display_name`);

--
-- Indexes for table `T_MAIN_ORDER_POP`
--
ALTER TABLE `T_MAIN_ORDER_POP`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `T_ORDER_POP`
--
ALTER TABLE `T_ORDER_POP`
  ADD PRIMARY KEY (`id`),
  ADD KEY `APPORDERNO` (`APPORDERNO`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `dns_customer_code` (`dns_customer_code`),
  ADD KEY `prod_code` (`prod_code`),
  ADD KEY `dns_prod_code` (`dns_prod_code`),
  ADD KEY `STATUS` (`status`);

--
-- Indexes for table `T_SUBDEALER_ORDER`
--
ALTER TABLE `T_SUBDEALER_ORDER`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_code` (`belong_dealer_code`),
  ADD KEY `dns_customer_code` (`belong_dealer_dns_code`),
  ADD KEY `prod_code` (`prod_code`),
  ADD KEY `dns_prod_code` (`dns_prod_code`),
  ADD KEY `STATUS` (`STATUS`);

--
-- Indexes for table `verify_ledger_details`
--
ALTER TABLE `verify_ledger_details`
  ADD PRIMARY KEY (`vld_id`);

--
-- Indexes for table `verify_ledger_details_backup`
--
ALTER TABLE `verify_ledger_details_backup`
  ADD PRIMARY KEY (`vld_id`);

--
-- Indexes for table `vertical_branch_employeewise_details`
--
ALTER TABLE `vertical_branch_employeewise_details`
  ADD PRIMARY KEY (`vertical_value`,`branch_code`,`emp_code`,`operation_date`);

--
-- Indexes for table `webservice_track_log`
--
ALTER TABLE `webservice_track_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_code` (`customer_code`),
  ADD KEY `webservice_name` (`webservice_name`),
  ADD KEY `datetime` (`datetime`);

--
-- Indexes for table `zorderdetailsp`
--
ALTER TABLE `zorderdetailsp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zorderdetailsp_03_10_24`
--
ALTER TABLE `zorderdetailsp_03_10_24`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ZSS_Quot_Ord`
--
ALTER TABLE `ZSS_Quot_Ord`
  ADD PRIMARY KEY (`Sr_No`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_master`
--
ALTER TABLE `admin_master`
  MODIFY `admin_id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ageing`
--
ALTER TABLE `ageing`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `allocation_details`
--
ALTER TABLE `allocation_details`
  MODIFY `allocation_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `allocation_details_invoicewise`
--
ALTER TABLE `allocation_details_invoicewise`
  MODIFY `allocation_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `allocation_invoice_del`
--
ALTER TABLE `allocation_invoice_del`
  MODIFY `sl_no` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `apicalllog`
--
ALTER TABLE `apicalllog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `api_verification`
--
ALTER TABLE `api_verification`
  MODIFY `api_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_service_track_log`
--
ALTER TABLE `app_service_track_log`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_setting_master`
--
ALTER TABLE `app_setting_master`
  MODIFY `asm_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `arc_consumer_reg`
--
ALTER TABLE `arc_consumer_reg`
  MODIFY `ac_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `arc_consumer_reg_old`
--
ALTER TABLE `arc_consumer_reg_old`
  MODIFY `ac_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `arc_dealer_cust_point_table`
--
ALTER TABLE `arc_dealer_cust_point_table`
  MODIFY `adcpt_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `arc_gift_catalogue`
--
ALTER TABLE `arc_gift_catalogue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `arc_gift_redeem_table`
--
ALTER TABLE `arc_gift_redeem_table`
  MODIFY `ac_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auto_notification_log`
--
ALTER TABLE `auto_notification_log`
  MODIFY `anl_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_master`
--
ALTER TABLE `bank_master`
  MODIFY `bank_id` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch_consumer_scheme_status`
--
ALTER TABLE `branch_consumer_scheme_status`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch_credit_limit_status`
--
ALTER TABLE `branch_credit_limit_status`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch_destination_freight`
--
ALTER TABLE `branch_destination_freight`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch_dump`
--
ALTER TABLE `branch_dump`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch_game_status`
--
ALTER TABLE `branch_game_status`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch_PGstatus`
--
ALTER TABLE `branch_PGstatus`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch_pop_product`
--
ALTER TABLE `branch_pop_product`
  MODIFY `sl_no` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch_rssd_allocation_days`
--
ALTER TABLE `branch_rssd_allocation_days`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch_schemes_PDF`
--
ALTER TABLE `branch_schemes_PDF`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ch_data`
--
ALTER TABLE `ch_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consumer_scheme`
--
ALTER TABLE `consumer_scheme`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cron_update_table`
--
ALTER TABLE `cron_update_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_destination`
--
ALTER TABLE `customer_destination`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_invoice_table`
--
ALTER TABLE `customer_invoice_table`
  MODIFY `id` int(55) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_master_STAR`
--
ALTER TABLE `customer_master_STAR`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_refference`
--
ALTER TABLE `customer_refference`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_route_emp_relation`
--
ALTER TABLE `customer_route_emp_relation`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_visit_details`
--
ALTER TABLE `customer_visit_details`
  MODIFY `row_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cust_data`
--
ALTER TABLE `cust_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `data_refresh_log`
--
ALTER TABLE `data_refresh_log`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_backup_files`
--
ALTER TABLE `db_backup_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dealer_app_version`
--
ALTER TABLE `dealer_app_version`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dealer_reward_status`
--
ALTER TABLE `dealer_reward_status`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dealer_rssd_test`
--
ALTER TABLE `dealer_rssd_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dealer_sales_team_visit_survey`
--
ALTER TABLE `dealer_sales_team_visit_survey`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dealer_security_ledger_status`
--
ALTER TABLE `dealer_security_ledger_status`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dealer_security_ledger_status_old`
--
ALTER TABLE `dealer_security_ledger_status_old`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dealer_tour_status`
--
ALTER TABLE `dealer_tour_status`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_status_log`
--
ALTER TABLE `delivery_status_log`
  MODIFY `sl_no` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `destination_wise_price`
--
ALTER TABLE `destination_wise_price`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `do_order_cancel_by_log`
--
ALTER TABLE `do_order_cancel_by_log`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dummy_registration_for_app_approval`
--
ALTER TABLE `dummy_registration_for_app_approval`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_kyc_master`
--
ALTER TABLE `employee_kyc_master`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_kyc_master_backup`
--
ALTER TABLE `employee_kyc_master_backup`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emp_data_download_log`
--
ALTER TABLE `emp_data_download_log`
  MODIFY `sl_no` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emp_menu_access`
--
ALTER TABLE `emp_menu_access`
  MODIFY `sl_no` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `epod_details`
--
ALTER TABLE `epod_details`
  MODIFY `epod_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fpx_dealer_list`
--
ALTER TABLE `fpx_dealer_list`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fpx_dealer_truck_mapping`
--
ALTER TABLE `fpx_dealer_truck_mapping`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger`
--
ALTER TABLE `ledger`
  MODIFY `ldg_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_balance`
--
ALTER TABLE `ledger_balance`
  MODIFY `lb_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_transaction_table`
--
ALTER TABLE `ledger_transaction_table`
  MODIFY `lt_order_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lifting`
--
ALTER TABLE `lifting`
  MODIFY `lid` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lifting_date_validation`
--
ALTER TABLE `lifting_date_validation`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mail_access`
--
ALTER TABLE `mail_access`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_access`
--
ALTER TABLE `menu_access`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_master`
--
ALTER TABLE `menu_master`
  MODIFY `menu_id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `missing_erp_challan`
--
ALTER TABLE `missing_erp_challan`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `missing_erp_orders`
--
ALTER TABLE `missing_erp_orders`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mis_details_emp_datewise`
--
ALTER TABLE `mis_details_emp_datewise`
  MODIFY `sl_no` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `north_east_branch`
--
ALTER TABLE `north_east_branch`
  MODIFY `sl_no` int(3) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notice_2023`
--
ALTER TABLE `notice_2023`
  MODIFY `sf_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notice_branch`
--
ALTER TABLE `notice_branch`
  MODIFY `nb_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_message`
--
ALTER TABLE `notification_message`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_read_table`
--
ALTER TABLE `notification_read_table`
  MODIFY `nrt_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `not_north_east_branch`
--
ALTER TABLE `not_north_east_branch`
  MODIFY `sl_no` int(3) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nye_branch`
--
ALTER TABLE `nye_branch`
  MODIFY `nb_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orderdata`
--
ALTER TABLE `orderdata`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_query`
--
ALTER TABLE `order_query`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `performance`
--
ALTER TABLE `performance`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `place_order_lifting_days`
--
ALTER TABLE `place_order_lifting_days`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pop_order`
--
ALTER TABLE `pop_order`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pop_order_03_09_2024`
--
ALTER TABLE `pop_order_03_09_2024`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pop_product_master`
--
ALTER TABLE `pop_product_master`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `redeeme_details`
--
ALTER TABLE `redeeme_details`
  MODIFY `sn` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `remote_user`
--
ALTER TABLE `remote_user`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `route_refference`
--
ALTER TABLE `route_refference`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `SAP_erp_dump_mapping`
--
ALTER TABLE `SAP_erp_dump_mapping`
  MODIFY `sl_no` int(3) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `SAP_erp_product_mapping`
--
ALTER TABLE `SAP_erp_product_mapping`
  MODIFY `sl_no` int(2) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `selected_menu_for_user`
--
ALTER TABLE `selected_menu_for_user`
  MODIFY `smid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `self_appraisal`
--
ALTER TABLE `self_appraisal`
  MODIFY `sl_no` bigint(15) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `self_appraisal_branch_wise`
--
ALTER TABLE `self_appraisal_branch_wise`
  MODIFY `sl_no` bigint(15) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `self_appraisal_customer_wise`
--
ALTER TABLE `self_appraisal_customer_wise`
  MODIFY `sl_no` bigint(15) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `self_appraisal_product_wise`
--
ALTER TABLE `self_appraisal_product_wise`
  MODIFY `sl_no` bigint(15) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sp_destination`
--
ALTER TABLE `sp_destination`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sqllog`
--
ALTER TABLE `sqllog`
  MODIFY `sl_no` int(2) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `startreport_admin`
--
ALTER TABLE `startreport_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `start_slider`
--
ALTER TABLE `start_slider`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subdealer_branch_schemes_PDF`
--
ALTER TABLE `subdealer_branch_schemes_PDF`
  MODIFY `sl_no` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subdlr_data`
--
ALTER TABLE `subdlr_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `survey_form`
--
ALTER TABLE `survey_form`
  MODIFY `sf_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `survey_form_18_20`
--
ALTER TABLE `survey_form_18_20`
  MODIFY `sf_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `survey_form_fy_21_22_23`
--
ALTER TABLE `survey_form_fy_21_22_23`
  MODIFY `sf_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `survey_form_prev_data`
--
ALTER TABLE `survey_form_prev_data`
  MODIFY `sf_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `survey_for_new_year_eve_carnival_concert`
--
ALTER TABLE `survey_for_new_year_eve_carnival_concert`
  MODIFY `sf_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `table_structure_master`
--
ALTER TABLE `table_structure_master`
  MODIFY `t_structure_id` tinyint(3) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `temporary_table_branch_email`
--
ALTER TABLE `temporary_table_branch_email`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `T_APPERPDO`
--
ALTER TABLE `T_APPERPDO`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `T_APPERPDO_03_10_24`
--
ALTER TABLE `T_APPERPDO_03_10_24`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `T_APPERPDO_ARCHIVE_24`
--
ALTER TABLE `T_APPERPDO_ARCHIVE_24`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `T_APPERPDO_INVOICE`
--
ALTER TABLE `T_APPERPDO_INVOICE`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `T_APPERPDO_OFFLINE`
--
ALTER TABLE `T_APPERPDO_OFFLINE`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `T_APPERPDO_OLD`
--
ALTER TABLE `T_APPERPDO_OLD`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `T_APPERPDO_TEMP`
--
ALTER TABLE `T_APPERPDO_TEMP`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `T_DOCHALLAN`
--
ALTER TABLE `T_DOCHALLAN`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `T_DOCHALLAN_ARCHIVE_24`
--
ALTER TABLE `T_DOCHALLAN_ARCHIVE_24`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `T_DOINVOICE`
--
ALTER TABLE `T_DOINVOICE`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `T_MAIN_ORDER_POP`
--
ALTER TABLE `T_MAIN_ORDER_POP`
  MODIFY `order_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `T_ORDER_POP`
--
ALTER TABLE `T_ORDER_POP`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `T_SUBDEALER_ORDER`
--
ALTER TABLE `T_SUBDEALER_ORDER`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `verify_ledger_details`
--
ALTER TABLE `verify_ledger_details`
  MODIFY `vld_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `verify_ledger_details_backup`
--
ALTER TABLE `verify_ledger_details_backup`
  MODIFY `vld_id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `webservice_track_log`
--
ALTER TABLE `webservice_track_log`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zorderdetailsp`
--
ALTER TABLE `zorderdetailsp`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zorderdetailsp_03_10_24`
--
ALTER TABLE `zorderdetailsp_03_10_24`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

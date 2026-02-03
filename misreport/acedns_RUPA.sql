-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 19, 2015 at 01:34 PM
-- Server version: 5.5.37-cll
-- PHP Version: 5.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `acedns_RUPA`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_log`
--

CREATE TABLE IF NOT EXISTS `access_log` (
  `access_id` varchar(30) NOT NULL,
  `access_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `access_type` varchar(255) NOT NULL,
  `added_data` text,
  `modified_data` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `admin_master`
--

CREATE TABLE IF NOT EXISTS `admin_master` (
  `admin_id` int(5) NOT NULL AUTO_INCREMENT,
  `admin_login` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `admin_pwd` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `admin_email` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=51 ;

-- --------------------------------------------------------

--
-- Table structure for table `apicalllog`
--

CREATE TABLE IF NOT EXISTS `apicalllog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `emp_code` varchar(30) NOT NULL,
  `url` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28760 ;

-- --------------------------------------------------------

--
-- Table structure for table `app_updation`
--

CREATE TABLE IF NOT EXISTS `app_updation` (
  `device_id` varchar(255) NOT NULL,
  `version_code` varchar(255) NOT NULL,
  `is_update` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `app_version`
--

CREATE TABLE IF NOT EXISTS `app_version` (
  `version_code` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`version_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `attendence`
--

CREATE TABLE IF NOT EXISTS `attendence` (
  `emp_code` varchar(20) NOT NULL,
  `trans_id` varchar(32) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `transferred` enum('YES','NO') NOT NULL DEFAULT 'NO'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bank_master`
--

CREATE TABLE IF NOT EXISTS `bank_master` (
  `bank_id` int(4) NOT NULL AUTO_INCREMENT,
  `bank_name` text NOT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`bank_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=256 ;

-- --------------------------------------------------------

--
-- Table structure for table `branch_master`
--

CREATE TABLE IF NOT EXISTS `branch_master` (
  `comp_code` varchar(20) NOT NULL,
  `branch_code` varchar(20) NOT NULL,
  `dns_branch_code` varchar(20) DEFAULT '',
  `branch_name` varchar(255) NOT NULL,
  `branch_location` varchar(100) NOT NULL,
  `branch_state` varchar(20) NOT NULL,
  `branch_email_id` varchar(100) DEFAULT NULL,
  `branch_accounts_email_id` varchar(100) DEFAULT NULL,
  `alternative_email_id` varchar(100) DEFAULT '',
  PRIMARY KEY (`branch_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `changepassword`
--

CREATE TABLE IF NOT EXISTS `changepassword` (
  `emp_code` varchar(20) NOT NULL,
  `newpassword` varchar(60) NOT NULL,
  `oldpassword` varchar(60) NOT NULL,
  `status` enum('true','false') NOT NULL,
  `deviceid` varchar(100) NOT NULL,
  `registrationid` text,
  `is_licensed` enum('0','1') NOT NULL DEFAULT '0',
  `loggedin_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_operation_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`emp_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `color_master`
--

CREATE TABLE IF NOT EXISTS `color_master` (
  `color_code` varchar(255) NOT NULL,
  `color_name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `company_master`
--

CREATE TABLE IF NOT EXISTS `company_master` (
  `comp_code` varchar(20) NOT NULL,
  `dns_comp_code` varchar(20) DEFAULT NULL,
  `comp_name` varchar(255) NOT NULL,
  `admin_email_id` varchar(255) NOT NULL,
  `account_email_id` varchar(255) NOT NULL,
  `no_of_branches` int(5) NOT NULL,
  PRIMARY KEY (`comp_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer_master`
--

CREATE TABLE IF NOT EXISTS `customer_master` (
  `customer_code` varchar(255) NOT NULL,
  `dns_customer_code` varchar(255) DEFAULT NULL,
  `customer_name` varchar(60) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `pin` varchar(50) DEFAULT NULL,
  `phone_no` varchar(20) DEFAULT NULL,
  `route_code` varchar(255) NOT NULL,
  `emp_code` varchar(20) NOT NULL,
  `current_balance` double NOT NULL,
  `credit_limit` double NOT NULL,
  `credit_days` varchar(255) DEFAULT NULL,
  `acedns` varchar(2) NOT NULL DEFAULT '',
  `black_list` varchar(2) NOT NULL DEFAULT '',
  `TD` varchar(20) DEFAULT '0',
  `cust_type` varchar(10) DEFAULT NULL,
  `rds_tag` varchar(20) DEFAULT NULL,
  `sauda_validity_period` varchar(50) DEFAULT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `download_time_credit_limit` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `vertical_value` varchar(20) DEFAULT NULL,
  `branch_code` varchar(20) DEFAULT '',
  PRIMARY KEY (`customer_code`,`route_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer_master_temp`
--

CREATE TABLE IF NOT EXISTS `customer_master_temp` (
  `customer_code` varchar(20) NOT NULL,
  `customer_name` varchar(60) NOT NULL,
  `phone_no` varchar(20) DEFAULT NULL,
  `route_code` varchar(20) NOT NULL,
  `emp_code` varchar(20) NOT NULL,
  `current_balance` double NOT NULL,
  `credit_limit` double NOT NULL,
  `acedns` varchar(2) NOT NULL DEFAULT '',
  `black_list` varchar(2) NOT NULL DEFAULT '',
  `TD` varchar(20) DEFAULT NULL,
  `vertical_value` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`customer_code`,`route_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `data_refresh_log`
--

CREATE TABLE IF NOT EXISTS `data_refresh_log` (
  `sl_no` int(10) NOT NULL AUTO_INCREMENT,
  `refresh_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sl_no`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=49 ;

-- --------------------------------------------------------

--
-- Table structure for table `dbbackupcheck`
--

CREATE TABLE IF NOT EXISTS `dbbackupcheck` (
  `emp_code` varchar(20) NOT NULL,
  `device_id` varchar(20) NOT NULL,
  `is_checked` enum('0','1') NOT NULL DEFAULT '0',
  `is_delete` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`emp_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `db_version`
--

CREATE TABLE IF NOT EXISTS `db_version` (
  `version_code` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`version_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_master`
--

CREATE TABLE IF NOT EXISTS `employee_master` (
  `emp_code` varchar(20) NOT NULL,
  `dns_emp_code` varchar(20) DEFAULT '',
  `emp_name` varchar(60) NOT NULL,
  `acedns` varchar(2) NOT NULL,
  `branch_code` varchar(20) DEFAULT '',
  `reporting_to` text,
  `vertical_value` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT '',
  `phone_no` varchar(50) DEFAULT '',
  `HQ` varchar(255) DEFAULT NULL,
  `sale_access` enum('primary','secondary','tertiary') NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zone` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`emp_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `emp_data_update_log`
--

CREATE TABLE IF NOT EXISTS `emp_data_update_log` (
  `emp_code` varchar(20) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`emp_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE IF NOT EXISTS `location` (
  `emp_code` varchar(20) NOT NULL,
  `trans_id` varchar(30) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `latt` double NOT NULL,
  `longi` double NOT NULL,
  `transferred` enum('YES','NO') NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`trans_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mail_access`
--

CREATE TABLE IF NOT EXISTS `mail_access` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `mail_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `branch_code` text NOT NULL,
  `attributes` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `menu_access`
--

CREATE TABLE IF NOT EXISTS `menu_access` (
  `emp_code` varchar(30) NOT NULL,
  `not_accessible_menu` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mrp`
--

CREATE TABLE IF NOT EXISTS `mrp` (
  `branch_code` varchar(20) DEFAULT '',
  `product_code` varchar(20) NOT NULL,
  `mrp_code` varchar(20) NOT NULL,
  `dns_mrp_code` varchar(20) DEFAULT '',
  `mrp` double DEFAULT NULL,
  `sale_rate` double DEFAULT NULL,
  `UOM` varchar(50) DEFAULT NULL,
  `vertical_value` varchar(20) DEFAULT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mrp_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notification_ack_relation`
--

CREATE TABLE IF NOT EXISTS `notification_ack_relation` (
  `notification_id` varchar(30) NOT NULL,
  `receiver_id` varchar(30) NOT NULL,
  `ack_id` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notification_master`
--

CREATE TABLE IF NOT EXISTS `notification_master` (
  `notification_id` varchar(30) NOT NULL,
  `type_of_notification` varchar(50) NOT NULL,
  `sender_id` varchar(30) NOT NULL,
  `message` text NOT NULL,
  `transferred` enum('YES','NO') NOT NULL DEFAULT 'NO'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE IF NOT EXISTS `order_details` (
  `order_no` varchar(30) NOT NULL,
  `sku_code` varchar(20) NOT NULL,
  `qty` double NOT NULL,
  `mrp_code` varchar(20) DEFAULT NULL,
  `TD` double DEFAULT NULL,
  `VAT` double DEFAULT NULL,
  `sale_rate` double DEFAULT NULL,
  `freight_charge` double DEFAULT NULL,
  `amount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `order_header`
--

CREATE TABLE IF NOT EXISTS `order_header` (
  `order_no` varchar(30) NOT NULL,
  `customer_code` varchar(255) NOT NULL,
  `branch_code` varchar(30) DEFAULT NULL,
  `vertical_value` varchar(50) DEFAULT NULL,
  `d_instruction` longtext NOT NULL,
  `sale_type` varchar(8) NOT NULL,
  `order_value` double NOT NULL,
  `TD` double NOT NULL,
  `tag_distributor_code` varchar(30) DEFAULT NULL,
  `transaction_type` varchar(10) NOT NULL,
  `VAT` double DEFAULT NULL,
  `transferred` enum('YES','NO') NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`order_no`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `outstanding`
--

CREATE TABLE IF NOT EXISTS `outstanding` (
  `customer_code` varchar(20) NOT NULL,
  `route_code` varchar(20) DEFAULT '',
  `branch_code` varchar(20) DEFAULT '',
  `vertical_value` varchar(20) DEFAULT '',
  `recid` varchar(30) NOT NULL DEFAULT '0',
  `invoice_id` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `invoice_amount` double NOT NULL,
  `due_amount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payment_details`
--

CREATE TABLE IF NOT EXISTS `payment_details` (
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

CREATE TABLE IF NOT EXISTS `payment_header` (
  `receipt_id` varchar(30) NOT NULL,
  `customer_code` varchar(255) NOT NULL,
  `amount` double NOT NULL,
  `cash_cheque` varchar(50) DEFAULT NULL,
  `cheque_no` varchar(6) NOT NULL,
  `date` date NOT NULL,
  `bank` varchar(255) DEFAULT NULL,
  `rdate` date DEFAULT NULL,
  `sale_type` varchar(8) NOT NULL,
  `p_remark` longtext NOT NULL,
  `transferred` enum('YES','NO') NOT NULL DEFAULT 'NO',
  PRIMARY KEY (`receipt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `prev_stock_counting_master`
--

CREATE TABLE IF NOT EXISTS `prev_stock_counting_master` (
  `customer_code` varchar(20) NOT NULL,
  `product_code` varchar(20) NOT NULL,
  `visit_1` double NOT NULL,
  `visit1_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `visit_2` double NOT NULL,
  `visit2_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `visit_3` double NOT NULL,
  `visit3_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `download_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`customer_code`,`product_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `product_brand_master`
--

CREATE TABLE IF NOT EXISTS `product_brand_master` (
  `product_brand_code` varchar(255) NOT NULL,
  `dns_product_brand_code` varchar(255) DEFAULT '',
  `product_sub_group_code` varchar(255) NOT NULL,
  `product_brand_name` varchar(255) NOT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vertical_value` varchar(20) DEFAULT NULL,
  `is_download` varchar(5) DEFAULT '',
  PRIMARY KEY (`product_sub_group_code`,`product_brand_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `product_group_master`
--

CREATE TABLE IF NOT EXISTS `product_group_master` (
  `product_group_code` varchar(255) NOT NULL,
  `dns_product_group_code` varchar(255) DEFAULT NULL,
  `product_group_name` varchar(255) NOT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `vertical_value` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`product_group_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `product_master`
--

CREATE TABLE IF NOT EXISTS `product_master` (
  `branch_code` varchar(20) NOT NULL DEFAULT '',
  `prod_code` varchar(20) NOT NULL,
  `dns_prod_code` varchar(20) DEFAULT '',
  `product_group_code` varchar(255) NOT NULL,
  `product_sub_group_code` varchar(255) NOT NULL,
  `product_brand_code` varchar(255) NOT NULL,
  `prod_desc` text,
  `cl_stk` double NOT NULL,
  `UOM1` varchar(10) DEFAULT NULL,
  `UOM2` varchar(10) DEFAULT NULL,
  `UOM3` varchar(10) DEFAULT NULL,
  `acedns` varchar(2) NOT NULL DEFAULT '',
  `black_list` varchar(2) NOT NULL,
  `vertical_value` varchar(20) DEFAULT NULL,
  `conversion_factor` varchar(255) DEFAULT NULL,
  `conversion_factor_two` varchar(50) DEFAULT NULL,
  `pack_size` varchar(50) DEFAULT NULL,
  `TD` double DEFAULT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `download_time_cl_stk` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`branch_code`,`prod_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `product_master_temp`
--

CREATE TABLE IF NOT EXISTS `product_master_temp` (
  `branch_code` varchar(20) NOT NULL DEFAULT '',
  `prod_code` varchar(20) NOT NULL,
  `product_group_code` varchar(255) NOT NULL,
  `product_sub_group_code` varchar(255) NOT NULL,
  `product_brand_code` varchar(255) NOT NULL,
  `prod_desc` text,
  `cl_stk` double NOT NULL,
  `acedns` varchar(2) NOT NULL DEFAULT '',
  `black_list` varchar(2) NOT NULL,
  `vertical_value` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`branch_code`,`prod_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `product_sub_group_master`
--

CREATE TABLE IF NOT EXISTS `product_sub_group_master` (
  `product_sub_group_code` varchar(255) NOT NULL,
  `dns_product_sub_group_code` varchar(255) DEFAULT '',
  `product_group_code` varchar(255) NOT NULL,
  `product_sub_group_name` varchar(255) NOT NULL,
  `vertical_value` varchar(20) DEFAULT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`product_sub_group_code`,`product_group_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `prospective_customer_details`
--

CREATE TABLE IF NOT EXISTS `prospective_customer_details` (
  `trans_id` varchar(30) NOT NULL,
  `product_code` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `prospective_customer_header`
--

CREATE TABLE IF NOT EXISTS `prospective_customer_header` (
  `trans_id` varchar(30) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `pin` varchar(20) NOT NULL,
  `area` varchar(255) NOT NULL,
  `phone_no` varchar(20) NOT NULL,
  `remarks` text NOT NULL,
  `cust_type` varchar(10) DEFAULT NULL,
  `tagged_customer_code` varchar(20) DEFAULT 'NULL',
  PRIMARY KEY (`trans_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `prospective_customer_master`
--

CREATE TABLE IF NOT EXISTS `prospective_customer_master` (
  `emp_code` varchar(20) NOT NULL,
  `customer_code` varchar(30) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `pin` varchar(20) NOT NULL,
  `area` varchar(255) NOT NULL,
  `phone_no` varchar(20) NOT NULL,
  `remarks` text NOT NULL,
  PRIMARY KEY (`customer_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rds_master`
--

CREATE TABLE IF NOT EXISTS `rds_master` (
  `rds_code` varchar(20) NOT NULL,
  `dns_rds_code` varchar(20) DEFAULT NULL,
  `rds_name` varchar(255) NOT NULL,
  `emp_code` varchar(20) NOT NULL,
  `rds_type` varchar(10) NOT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`rds_code`,`emp_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `redeeme_details`
--

CREATE TABLE IF NOT EXISTS `redeeme_details` (
  `sn` int(11) NOT NULL AUTO_INCREMENT,
  `scheme_expiry_date` date NOT NULL,
  `points` double NOT NULL,
  `award` varchar(255) NOT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sn`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `route_master`
--

CREATE TABLE IF NOT EXISTS `route_master` (
  `route_code` varchar(255) NOT NULL,
  `dns_route_code` varchar(20) DEFAULT '',
  `route_name` varchar(255) NOT NULL,
  `emp_code` varchar(20) NOT NULL,
  `vertical_value` varchar(255) DEFAULT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`route_code`,`emp_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `route_plan`
--

CREATE TABLE IF NOT EXISTS `route_plan` (
  `route_plan_trans_id` varchar(30) NOT NULL,
  `emp_code` varchar(30) NOT NULL,
  `route_code` varchar(255) NOT NULL,
  `visit_date` date NOT NULL,
  `remarks` text,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`emp_code`,`route_code`,`visit_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `route_plan_access_period`
--

CREATE TABLE IF NOT EXISTS `route_plan_access_period` (
  `emp_code` varchar(20) NOT NULL,
  `access_start_date` date NOT NULL,
  `access_end_date` date NOT NULL,
  `period` varchar(255) DEFAULT NULL,
  `modified_by` varchar(20) NOT NULL,
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `route_plan_log`
--

CREATE TABLE IF NOT EXISTS `route_plan_log` (
  `route_plan_trans_id` varchar(30) NOT NULL,
  `emp_code` varchar(30) NOT NULL,
  `prev_route_code` varchar(255) NOT NULL,
  `current_route_code` varchar(255) NOT NULL,
  `visit_date` date NOT NULL,
  `remarks` text,
  `created_by` varchar(10) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `sale_rate`
--

CREATE TABLE IF NOT EXISTS `sale_rate` (
  `branch_code` varchar(20) DEFAULT '',
  `product_code` varchar(20) NOT NULL,
  `sale_rate_code` varchar(20) NOT NULL,
  `sale_rate` double NOT NULL,
  PRIMARY KEY (`sale_rate_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sku_master`
--

CREATE TABLE IF NOT EXISTS `sku_master` (
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
-- Table structure for table `sku_master_sub_group`
--

CREATE TABLE IF NOT EXISTS `sku_master_sub_group` (
  `branch_code` varchar(20) NOT NULL DEFAULT '',
  `prod_code` varchar(50) NOT NULL,
  `prod_desc` text NOT NULL,
  `product_group_code` varchar(255) NOT NULL,
  `product_sub_group_code` varchar(255) NOT NULL,
  `product_brand_code` varchar(255) NOT NULL,
  `size` varchar(50) NOT NULL,
  `color` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `state_product_group_master`
--

CREATE TABLE IF NOT EXISTS `state_product_group_master` (
  `branch_code` varchar(20) NOT NULL,
  `product_group_code` varchar(20) NOT NULL,
  `product_sub_group_code` varchar(20) NOT NULL,
  `product_brand_code` varchar(20) NOT NULL,
  `prod_code` varchar(20) NOT NULL,
  `price_list_id` varchar(20) NOT NULL,
  PRIMARY KEY (`branch_code`,`product_group_code`,`product_sub_group_code`,`product_brand_code`,`prod_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stock_audit`
--

CREATE TABLE IF NOT EXISTS `stock_audit` (
  `transaction_id` varchar(30) NOT NULL,
  `customer_code` varchar(20) NOT NULL,
  `product_code` varchar(20) NOT NULL,
  `quantity` double NOT NULL,
  `product_mrp` double NOT NULL,
  `product_details` varchar(255) DEFAULT NULL,
  `remarks` text NOT NULL,
  PRIMARY KEY (`transaction_id`,`product_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `table_structure_master`
--

CREATE TABLE IF NOT EXISTS `table_structure_master` (
  `t_structure_id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(255) NOT NULL,
  `table_structure` text NOT NULL,
  `need_update` enum('Y','N') NOT NULL DEFAULT 'Y',
  `is_transaction` enum('Y','N') NOT NULL DEFAULT 'N',
  `is_master` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`t_structure_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=68 ;

-- --------------------------------------------------------

--
-- Table structure for table `table_structure_updation`
--

CREATE TABLE IF NOT EXISTS `table_structure_updation` (
  `emp_code` varchar(20) NOT NULL,
  `device_id` varchar(255) NOT NULL,
  `db_version_code` varchar(20) NOT NULL,
  `is_update` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`emp_code`,`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tour_expenses`
--

CREATE TABLE IF NOT EXISTS `tour_expenses` (
  `tour_exp_trans_id` varchar(30) NOT NULL,
  `emp_code` varchar(15) NOT NULL,
  `start_destination` varchar(255) NOT NULL,
  `end_destination` varchar(255) NOT NULL,
  `fare` double NOT NULL,
  `tour_date` date NOT NULL,
  `transport_mode_sub_cat_id` int(4) NOT NULL,
  `supporting_attached` varchar(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`tour_exp_trans_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transport_mode_category`
--

CREATE TABLE IF NOT EXISTS `transport_mode_category` (
  `transport_mode_cat_id` int(4) NOT NULL AUTO_INCREMENT,
  `transport_mode_cat_name` varchar(50) NOT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`transport_mode_cat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `transport_mode_sub_category`
--

CREATE TABLE IF NOT EXISTS `transport_mode_sub_category` (
  `transport_mode_sub_cat_id` int(4) NOT NULL AUTO_INCREMENT,
  `transport_mode_sub_cat_name` varchar(50) NOT NULL,
  `transport_mode_cat_id` int(4) NOT NULL,
  `download_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`transport_mode_sub_cat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_access`
--

CREATE TABLE IF NOT EXISTS `user_access` (
  `emp_code` varchar(30) NOT NULL,
  `accessibility_menu` varchar(255) NOT NULL,
  PRIMARY KEY (`emp_code`,`accessibility_menu`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vertical_branch_employeewise_details`
--

CREATE TABLE IF NOT EXISTS `vertical_branch_employeewise_details` (
  `vertical_value` varchar(50) NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `emp_code` varchar(50) NOT NULL,
  `qty` double NOT NULL,
  `secondary_qty` double NOT NULL,
  `calls_made` double NOT NULL,
  `productive` double NOT NULL,
  `primary` double NOT NULL,
  `secondary` double NOT NULL,
  `operation_date` date NOT NULL,
  PRIMARY KEY (`vertical_value`,`branch_code`,`emp_code`,`operation_date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vertical_mail_access`
--

CREATE TABLE IF NOT EXISTS `vertical_mail_access` (
  `vertical_code` text NOT NULL,
  `vertical_name` text NOT NULL,
  `headed_by` varchar(255) NOT NULL,
  `headed_by_email` varchar(255) DEFAULT NULL,
  `reporting_to` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `xml_data`
--

CREATE TABLE IF NOT EXISTS `xml_data` (
  `xml_data_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `emp_code` varchar(20) NOT NULL,
  `xml` longtext NOT NULL,
  `insertdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`xml_data_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=114 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

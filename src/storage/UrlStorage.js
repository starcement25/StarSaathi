let UrlStorage = {
  BaseUrlList: {
    Saathi: {
      base_url_saathi: "https://starsaathi.com/SAP",
      mason_link: "https://starlinkinfluencers.in/web/public/dealer/authenticate?authkey=$2y$10$wAa61qlCON2lRFMsRxobGeynsxG5M/CPHB.Vxt21DLY4dnbmaN9a6&sapcode=",
    },
    SBS: {
      base_url_sbs: "https://sbs.starsaathi.com/api/",
      performance: "api/authentication/product-wise-target?",
      sbs_performance: "api/analytics/performance"
    },
  },

  // Auth URL
  AuthURL: {
    login_url: "/reportmvc/api/v2/checkloginnew_v2",
    otp_verification_url: "/reportmvc/api/v2/verifyotpnew_v2",
    logout_url: "/acedns_star_clear_allocation_by_id", //the_id
    check_auth: '/check_token_valid.php',
  },

  // Non-Auth URL
  NonAuthURL: {
    Saathi: {
      // Dashboard URL
      DashboardURL: {
        dealer_wise_tour_data_download_url: "/dealer-wise-tour-data-download.php",
        engagement_list_url: "/game_authorization.php",
        rewards_list_url: "/dealer-wise-rewards_test.php",
        app_version_check_url: "/show_latest_app_version_v2.php",
        product_data_list_url: "/branchwise-product-data-download-v2.php",
        update_firebase_token_url: "/updateRegistrationId_v2-6.0.0.php",
        database_details_url: "/table-structure-details-6.0.2.php",
        update_profile_image_url: "/acedns_update_profile_image.php",
        dealer_wise_credit_limit_url: "/dealerwise-credit-limit-s-deposit-v2.php",
        update_image: "/acedns_update_profile_image.php",
        customer_credit_api: "/customer_credit_api.php"
      },

      // Download Database API
      DownloadDatabaseAPI: {
        branchwise_schemes_TXT_download_API: "/branchwise-scheme-download-txt_v2-6.0.1.php", //nick_name, emp_code, user_type, last_update_time
        branch_master_TXT_download_API: "/branch-master-txt_v2-6.0.3.php", //nick_name, emp_code, user_type, incremental_download, last_update_time, data_download_time
        product_wise_target_achievement_TXT_download_API: "/product-wise-target-achievement-txt_v2-6.0.2-1.php", //nick_name, user_type, emp_code
        //https://starsaathi.com/SAP/product-wise-target-achievement-txt_v2-6.0.2-1.php?emp_code=C/0001261
        customer_master_audit_TXT_download_API: "/customer-master-audit-txt-incremental_v2-7.0.11.php", //nick_name, emp_code, user_type, incremental_download, last_update_time, data_download_time
        destination_master_TXT_download_API: "/destination-master-txt_v2-6.0.4.php", //nick_name, emp_code, broker_id, incremental_download, last_update_time, data_download_time
        branch_dump_TXT_download_API: "/branch-dump-master-txt_v2-6.0.3.php", //nick_name, emp_code, incremental_download, last_update_time, data_download_time
        data_refresh_TXT_download_API: "/datadownloaddictionary_v2-7.0.4.php", //nick_name, emp_code, incremental_download, user_type, last_update_time, device_id
        item_wise_target_achievement_SAP_data: "item_wise_target_achievement_SAP_data.php",
      },

      // Order URL
      OrderURL: {
        ledger_list_data_url: "/acedns_star_ledger_by_id.php", //the_id
        new_order_create_url: "/acedns_star_save_online_offline_app_order_new_v4.php",
      },

      // Track Order URL
      TrackOrderURL: {
        other_offline_order_list_url: "/acedns_show_offline_order_list.php", //the_id, start_date, end_date, user_type
        other_order_list_url: "/acedns_star_order_details_by_id_v1.php", //the_id, user_type
        dealer_offline_order_list_url: "/acedns_show_offline_order_list.php", //the_id, start_date, end_date, user_type
        dealer_order_list_url: "/acedns_show_order_list_for_subdealer_V1.php", //the_id, start_date, end_date, user_type
      },

      // Ledger URL
      LedgerURL: {
        other_ledger_list_url: "/acedns_star_ledger_by_id.php", //the_id
        dealer_ledger_list_url: "/acedns_star_ledger_subdealer_rssd.php", //the_id, user_type
        other_statement_details_PDF_download_API: "/dashboard/detailed_statement_pdf_download.php", //the_start_date, the_end_date, the_customer_code, user_type
        dealer_statement_details_PDF_download_API: "/dashboard/detailed_statement_pdf_download.php", //the_start_date, the_end_date, the_customer_code, user_type
        customer_ledger_month_wise_list_url: "/acedns_star_show_month_wize_ledger_by_id.php", //the_id
      },

      // Product Wise Performance URL
      ProductWisePerformanceURL: {
        product_wise_target_achievement_url: "/item_wise_target_achievement_SAP_data.php", //customer_code, year, month
      },

      // Invoice URL
      InvoiceURL: {
        invoice_list_url: "/acedns_show_invoice_list.php", //customer_code, from_date, to_date
      },

      // Ageing URL
      AgeingURL: {
        dealer_wise_ageing_list_url: "/dealerwise-ageing-data.php", //customer_code
      },

      // Order Query URL
      OrderQueryURL: {
        dealer_order_query_list_url: "/order_query_data_download.php", //customer_code={{customer_code}}, start_date=2024-12-01, end_date=2024-12-31
        dealer_order_query_update_url: "/order_query_data_status_update.php",
        rssd_order_query_create_url: "/save_order_query_data.php",
      },

      // Sales Visit Feedback URL
      SalesVisitFeedbackURL: {
        sales_visit_list_url: "/dealer-site-visit-list.php", //customer_code
        sales_visit_feedback_create_url: "/save_dealer_sales_team_visit_details.php",
      },

      // Allocation History URL
      AllocationHistoryURL: {
        allocation_history_list_url: "/ajax_allocation_lifting_invoicewise_v10.php", //customer_id, user_type, page_no
      },

      // Payment URL
      PaymentURL: {
        payment_list_url: "/acedns_star_ledger_by_id.php", //the_id
        payment_details_url: "/acedns_pay_ledger_amount_v2.php", //customer_code, the_amount, payment_by
      },

      // Consumer URL
      ConsumerURL: {
        consumer_data_save_url: "/save_consumer_scheme.php",
      },

      // KYC URL
      KYCURL: {
        KYC_details_url: "/acedns_show_employee_kyc.php", //emp_code
        update_KYC_url: "/acedns_save_employee_kyc.php",
      },

      // Credit Limit URL
      CreditLimitURL: {
        balance_details_url: "/acedns_star_ledger_balance_details_by_id.php", //the_id
      },

      // Order URL
      OrderURL1: {
        dealer_data_list_url: "/ship-to-party-master-txt-V3.php", //emp_code, user_type, login_type
        truck_data_list_url: "/fpx-delaer-truck-list.php", //customer_code
        epod_details_url: "/save_epod_details.php", //customer_id, epod_data[0][challan_no], epod_data[0][date_and_time], epod_data[0][challan_date], epod_data[0][is_delivered]
        update_matirial_rcv_url: "/acedns_star_update_material_receive_confirmation_v2.php", //the_id, ch_quantity_no_of_bags, ch_quality_no_of_damaged_bags, ch_uid, challanno, quantity_checking, quality_checking
      },

      // Lifting URL
      LiftingURL: {
        reject_lifting_history_list_for_sub_dealer_url: "/acedns_star_show_reject_lifting_history_for_sub_dealer.php", //sub_dealer_cust_code, year_month
        accept_lifting_history_list_for_sub_dealer_url: "/acedns_star_show_approve_lifting_history_for_sub_dealer.php", //sub_dealer_cust_code, year_month
        pending_lifting_history_list_for_sub_dealer_url: "/acedns_star_show_pending_lifting_history_for_sub_dealer.php", //sub_dealer_cust_code, year_month
        dispatched_order_data_list_url: "/dispatched-order-list-invoicewise-v10.php", //month_year, customer_code
        dispatched_order_data_list_RSSD_url: "/dispatched-order-list-invoicewise-v10_rssd.php", //month_year, customer_code
        allocation_data_invoice_url: "/ajax_allocation_lifting_invoicewise_v10.php", //customer_id, year_month, user_type
        add_lifting_url: "acedns_star_add_lifting.php",
        lifting_validation_url: "/lifting_date_validation_data.php", //customer_code, user_type
        lifting_approve_url: "/save_allocation_details_invoicewise.php",
        download_invoice_url: "/dispatched-order-list-download-invoicewise-v2.php", //invoice_no, customer_code'
      },
      SchemeUrl: {
        getScheme: "/dealer_schemes.php"
      },

      // Notification URL
      NotificationURL: {
        notification_list_url: "/acedns_show_notifications.php", //the_branch_code, the_id'
        notification_read_unread_url: "/make_notification_read_v3.php", //the_id, noti_id'
      },

      // Product URL
      ProductURL: {
        pop_product_list_url: "/pop-product-data-download-v2.php", //customer_code, user_type'
        create_pop_order_url: "/save_pop_order_date_v1.php", //customer_code, user_type, dns_customer_code, address, pin, remarks, printed_address_pin, contact_num_printed, payment_by, order_data[0][dns_prod_code], order_data[0][prod_desc], order_data[0][qty], order_data[0][prod_image], order_data[0][customer_code], order_data[0][dns_customer_code], order_data[0][printed_address_pin], order_data[0][contact_num_printed], order_data[0][address], order_data[0][pin], order_data[0][remarks]'
        show_pop_order_list: "/show-pop-order-list.php",
        schema: "api/authentication/schema/"
      },

      // Save App Data URL
      SaveAppDataURL: {
        save_app_usage_details_url: "/save_app_usage_details.php",
      },

      // Other URL
      OtherURL: {
        KismatKiBoriPermissionUrl: "/admin/branch_wise_kismat_ki_bori_permission.php",
        api_exclusive_get_month_list: "/api_exclusive_get_month_list.php",
        api_exclusive_data_save: "/api_exclusive_data_save.php",
        add_sikkim_consumer_scheme: "/admin/add_sikkim_consumer_scheme.php",
        api_get_exclusive_dealername: "/api_get_exclusive_dealername.php"
      },
    },
    SBS: {
      other: {
        t_c_url: "api/tnc/",
        p_p_url: "api/privacy_policies/",
        r_p_url: "api/refund_policy/",
        about_us_url: "api/about_us/",
      },
      dashboard: {
        banner_url: "api/banners/",
        rewards_list_url: "api/dealer_wise_rewards_test",
        dealer_wise_tour_data_download_url: "api/dealer_wise_tour_data_download",
        engagement_list_url: "api/game_authorization",
        update_user_sbs: '/api/authentication/save-device-info/'
      },
      order: {
        dealer_list: "api/orders/user-list-all",
        user_product_list: "api/orders/user-product-list",
        plant_details: "api/orders/plant-details",
        ship_to: "api/orders/ship-to",
        order_dump_list: "api/orders/branch-dump",
        create_order: "api/orders/star-make-order",
        dealer_details: "api/authentication/dealers-by-id/",
        check_truck_list: "api/orders/check-dot",
        order_history: "api/orders/order-details",
        order_history_offline: "api/orders/offline-order-details",
      },
      performance: {
        item_wise_performance: "api/analytics/item_wise_target_achievement_SAP_data"
      },
      Ledger: {
        ledger_data: "api/dealer/display-balance",
        ledger_list: "api/dealer/detailed_txn_ledger"
      },
      Scheme: {
        scheme_list: "api/customer/scheme-list"
      }
    },
  },

  // API Parameter
  ParameterList: {
    BasicData: {
      nick_name: "",
      emp_code: "",
      customer_code: "",
      user_type: "",
      incremental_download: "",
      broker_id: "",
      broker_user_type: "",
      broker_emp_code: "",
      emp_id: "",
      emp_mobile_number: "",
      selectedCustomerCode: "",
      selectedCustomerType: "",
      ledger_balance_data: "",
      credit_details: "",
      belong_dealer_name: "",
      belong_dealer_dns_code: "",
      belong_dealer_code: "",
      customerDetails: "",
      profile_image_url: "",
      dns_emp_code: ''
    },
  },
};
export default UrlStorage
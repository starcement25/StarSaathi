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
  AuthURL: {
    login_url: "/reportmvc/api/v2/checkloginnew_v2",
    otp_verification_url: "/reportmvc/api/v2/verifyotpnew_v2",
    logout_url: "/acedns_star_clear_allocation_by_id",
    check_auth: '/check_token_valid.php',
  },
  NonAuthURL: {
    Saathi: {
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
      DownloadDatabaseAPI: {
        branchwise_schemes_TXT_download_API: "/branchwise-scheme-download-txt_v2-6.0.1.php",
        branch_master_TXT_download_API: "/branch-master-txt_v2-6.0.3.php",
        product_wise_target_achievement_TXT_download_API: "/product-wise-target-achievement-txt_v2-6.0.2-1.php",
        customer_master_audit_TXT_download_API: "/customer-master-audit-txt-incremental_v2-7.0.11.php",
        destination_master_TXT_download_API: "/destination-master-txt_v2-6.0.4.php",
        branch_dump_TXT_download_API: "/branch-dump-master-txt_v2-6.0.3.php",
        data_refresh_TXT_download_API: "/datadownloaddictionary_v2-7.0.4.php",
        item_wise_target_achievement_SAP_data: "item_wise_target_achievement_SAP_data.php",
      },
      OrderURL: {
        ledger_list_data_url: "/acedns_star_ledger_by_id.php",
        new_order_create_url: "/acedns_star_save_online_offline_app_order_new_v4.php",
      },
      TrackOrderURL: {
        other_offline_order_list_url: "/acedns_show_offline_order_list.php",
        other_order_list_url: "/acedns_star_order_details_by_id_v1.php",
        dealer_offline_order_list_url: "/acedns_show_offline_order_list.php",
        dealer_order_list_url: "/acedns_show_order_list_for_subdealer_V1.php",
      },
      LedgerURL: {
        other_ledger_list_url: "/acedns_star_ledger_by_id.php",
        dealer_ledger_list_url: "/acedns_star_ledger_subdealer_rssd.php",
        other_statement_details_PDF_download_API: "/dashboard/detailed_statement_pdf_download.php",
        dealer_statement_details_PDF_download_API: "/dashboard/detailed_statement_pdf_download.php",
        customer_ledger_month_wise_list_url: "/acedns_star_show_month_wize_ledger_by_id.php",
      },
      ProductWisePerformanceURL: {
        product_wise_target_achievement_url: "/item_wise_target_achievement_SAP_data.php",
      },
      InvoiceURL: {
        invoice_list_url: "/acedns_show_invoice_list.php",
      },
      AgeingURL: {
        dealer_wise_ageing_list_url: "/dealerwise-ageing-data.php",
      },
      OrderQueryURL: {
        dealer_order_query_list_url: "/order_query_data_download.php",
        dealer_order_query_update_url: "/order_query_data_status_update.php",
        rssd_order_query_create_url: "/save_order_query_data.php",
      },
      SalesVisitFeedbackURL: {
        sales_visit_list_url: "/dealer-site-visit-list.php",
        sales_visit_feedback_create_url: "/save_dealer_sales_team_visit_details.php",
      },
      AllocationHistoryURL: {
        allocation_history_list_url: "/ajax_allocation_lifting_invoicewise_v11.php",
      },
      PaymentURL: {
        payment_list_url: "/acedns_star_ledger_by_id.php",
        payment_details_url: "/acedns_pay_ledger_amount_v2.php",
      },
      ConsumerURL: {
        consumer_data_save_url: "/save_consumer_scheme.php",
      },
      KYCURL: {
        KYC_details_url: "/acedns_show_employee_kyc.php",
        update_KYC_url: "/acedns_save_employee_kyc.php",
      },
      CreditLimitURL: {
        balance_details_url: "/acedns_star_ledger_balance_details_by_id.php",
      },
      OrderURL1: {
        dealer_data_list_url: "/ship-to-party-master-txt-V3.php",
        truck_data_list_url: "/fpx-delaer-truck-list.php",
        epod_details_url: "/save_epod_details.php",
        update_matirial_rcv_url: "/acedns_star_update_material_receive_confirmation_v2.php",
      },
      LiftingURL: {
        reject_lifting_history_list_for_sub_dealer_url: "/acedns_star_show_reject_lifting_history_for_sub_dealer.php",
        accept_lifting_history_list_for_sub_dealer_url: "/acedns_star_show_approve_lifting_history_for_sub_dealer.php",
        pending_lifting_history_list_for_sub_dealer_url: "/acedns_star_show_pending_lifting_history_for_sub_dealer.php",
        dispatched_order_data_list_url: "/dispatched-order-list-invoicewise-v11.php",
        dispatched_order_data_list_RSSD_url: "/dispatched-order-list-invoicewise-v10_rssd.php",
        allocation_data_invoice_url: "/ajax_allocation_lifting_invoicewise_v10.php",
        add_lifting_url: "acedns_star_add_lifting.php",
        lifting_validation_url: "/lifting_date_validation_data.php",
        lifting_approve_url: "/save_allocation_details_invoicewise.php",
        lifting_re_approve_url: "/admin/ajax_update_rssd_allocation_new.php",
        download_invoice_url: "/dispatched-order-list-download-invoicewise-v2.php",
      },
      SchemeUrl: {
        getScheme: "/dealer_schemes.php"
      },
      NotificationURL: {
        notification_list_url: "/acedns_show_notifications.php",
        notification_read_unread_url: "/make_notification_read_v3.php",
      },
      ProductURL: {
        pop_product_list_url: "/pop-product-data-download-v2.php",
        create_pop_order_url: "/save_pop_order_date_v1.php",
        show_pop_order_list: "/show-pop-order-list.php",
        schema: "api/authentication/schema/"
      },
      SaveAppDataURL: {
        save_app_usage_details_url: "/save_app_usage_details.php",
      },
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
}
export default UrlStorage
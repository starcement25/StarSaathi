import UrlStorage from '../UrlStorage';
import { DataBaseSetup } from './DataBase'
import moment from 'moment';
const db = DataBaseSetup();

//android_metadata CHECK

//employee_master_login CHECK
export const checkDataFor_last_login = () => {
    var status = false;
    var todaysDate = moment(new Date()).format('YYYY-MM-DD')
    db.transaction(txn => {
        txn.executeSql(
            `SELECT flag FROM employee_master_login where emp_code=${UrlStorage.ParameterList.BasicData.emp_code} AND date=${todaysDate}`, [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                if (len > 0) {
                    const flag = results.rows.item(0).flag;
                    status = flag == 1
                }
            },
            error => { }
        );
    });

    return status
}

//menu_details CHECK

//order_form_details CHECK

//product_details CHECK

//user_details CHECK

//route_master CHECK

//customer_master CHECK
export const checkDataFor_rssd_broker = () => {
    var status = []
    db.transaction(txn => {
        txn.executeSql(
            "SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type == 'RSSD' AND customer_code !=  '" + UrlStorage.ParameterList.BasicData.customer_code + "' ORDER BY customer_name ASC",
            [],
            (sqlTnx, res) => {
                status = res.rows;
            },
            error => { }
        );
    });
    return status
}
export const checkDataFor_other_broker = () => {
    var status = []
    db.transaction(txn => {
        txn.executeSql(
            "SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type == 'RSSD' AND customer_code !=  '" + UrlStorage.ParameterList.BasicData.customer_code + "' ORDER BY customer_name ASC",
            [],
            (sqlTnx, res) => {
                status = res.rows;
            },
            error => { }
        );
    });
    return status
}
export const checkDataFor_all_broker = () => {
    var status = []
    db.transaction(txn => {
        txn.executeSql(
            "SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type IN( 'Sub Dealer','RSSD') AND rds_tag =  '" + UrlStorage.ParameterList.BasicData.customer_code + "' ORDER BY customer_name ASC",
            [],
            (sqlTnx, res) => {
                status = res.rows;
            },
            error => { }
        );
    });
    return status
}
export const checkDataFor_not_dealer = () => {
    var status = []
    db.transaction(txn => {
        txn.executeSql(
            "SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type != 'Dealer' ORDER BY customer_name ASC",
            [],
            (sqlTnx, res) => {
                status = res.rows;
            },
            error => { }
        );
    });
    return status
}
export const checkDataFor_all_dealer = () => {
    var status = []
    db.transaction(txn => {
        txn.executeSql(
            "SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type = 'Dealer' ORDER BY customer_name ASC",
            [],
            (sqlTnx, res) => {
                status = res.rows;
            },
            error => { }
        );
    });
    return status
}

//outstanding_master CHECK

//product_group_master CHECK

//product_sub_group_master CHECK

//product_brand_master CHECK

//product_master CHECK

//mrp CHECK

//route_plan_transaction CHECK

//order_header CHECK

//payment_details CHECK

//payment_header CHECK

//location CHECK

//order_details CHECK

//prospective_customer_master CHECK

//attendence CHECK

//transport_mode_category CHECK

//transport_mode_sub_category CHECK

//tour_expenses CHECK

//bank_master CHECK

//prospective_customer_header CHECK

//prospective_customer_details CHECK

//data_download_log CHECK
export const checkDataFor_last_download_time = (table_name) => {
    var last_download_time = ''
    db.transaction(txn => {
        txn.executeSql(
            `SELECT last_download_time FROM data_download_log where table_name=${table_name}`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                if (len > 0) {
                    last_download_time = results.rows.item(0).last_download_time;
                }
            },
            error => { }
        );
    });

    return last_download_time
}

//prev_stock_counting_master CHECK

//stock_audit CHECK

//rds_master CHECK

//route_plan_access_period CHECK

//emp_master CHECK
export const checkDataFor_sale_access = () => {
    var sale_access = ''
    db.transaction(txn => {
        txn.executeSql(
            `SELECT sale_access FROM emp_master where emp_code=${UrlStorage.ParameterList.BasicData.emp_code}`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                if (len > 0) {
                    sale_access = results.rows.item(0).sale_access;
                }
            },
            error => { }
        );
    });

    return sale_access
}

//branch_master CHECK

//vendor_master CHECK

//closing_stock CHECK

//freight_expenses CHECK

//loyalty_card_holder_master CHECK

//card_transaction CHECK

//goods_in_transit CHECK

//transaction_log CHECK

//scheme_details CHECK

//loyalty_purchase_details CHECK

//app_info CHECK
export const checkDataFor_app_version = () => {
    var app_version = ''
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM app_info`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                if (len > 0) {
                    app_version = results.rows.item(0).app_version;
                }
            },
            error => { }
        );
    });

    return app_version
}

//mis_transaction_log CHECK

//notes_info CHECK

//user_access CHECK

//route_plan_details CHECK

//emp_image CHECK

//supporting_attachment_details CHECK

//lodging_expenses CHECK

//fooding_expenses CHECK

//gcm CHECK

//notification_details CHECK

//redeeme_details CHECK

//sauda_allocation CHECK

//customer_branch_relation CHECK

//sauda_header CHECK

//sauda_details CHECK

//broker_master CHECK

//sauda_form_details CHECK

//product_promotion CHECK

//pin_code_master CHECK

//generic_oil_master CHECK

//market_feedback CHECK

//menu_access CHECK

//street_master CHECK

//sauda_allocation_log CHECK

//sauda_allocation_access CHECK

//pending_contract CHECK

//sauda_transaction_log CHECK

//prev_order_counting_master CHECK

//pending_contract_ageing CHECK

//outstanding_ageing CHECK

//order_status CHECK

//sauda_mrp CHECK

//sale_performance_details CHECK

//OTP_details CHECK

//destination_master CHECK

//route_customer_plan_transaction CHECK

//check_in_out_details CHECK

//survey_output CHECK

//survey_output_temp CHECK

//survey_input CHECK

//survey_form_details CHECK

//survey_category_master CHECK

//table_view CHECK

//market_feedback_details CHECK

//competitor_group_master CHECK

//mf_stk_audit_header CHECK

//mf_stk_audit_details CHECK

//emp_menu_access CHECK

//emp_target_achievement CHECK

//non_trade_customer_master CHECK

//survey_header CHECK

//self_appraisal_details CHECK

//self_appraisal_customer_wise CHECK

//self_appraisal_branch_wise CHECK

//hint_remarks_details CHECK

//yellow_card_details CHECK

//call_duration CHECK

//yellow_card_date_validation CHECK

//self_appraisal_product_wise CHECK
export const checkDataFor_all_month_wish_target = () => {
    var monthWishTargetList = []
    var mData = "04, 05, 06, 07, 08, 09, 10, 11, 12, 01, 02, 03".split(',');
    for (var i = 0; i < mData.length; i++) {
        var obj = {
            'month': mData[i],
            'target': 0,
            'achievement': 0
        }
        db.transaction(txn => {
            txn.executeSql(
                "SELECT SUM(target), SUM(achievement) FROM self_appraisal_product_wise WHERE month=" + "'" + mData[i].trim() + "' AND emp_code = '" + UrlStorage.ParameterList.BasicData.customer_code + "'",
                [],
                (sqlTnx, res) => {
                    const len = res.rows.length;
                    if (len > 0) {
                        obj.target = results.rows.item(0).target
                        obj.achievement = results.rows.item(0).achievement
                    }
                },
                error => { }
            );
        });
        monthWishTargetList.push(obj)
    }
    return monthWishTargetList
}
export const checkDataFor_all_month_wish_target_1 = () => {
    var monthWishTargetList = []
    var mData = "04, 05, 06, 07, 08, 09, 10, 11, 12, 01, 02, 03".split(',');
    for (var i = 0; i < mData.length; i++) {
        var obj = {
            'month': mData[i],
            'target': 0,
            'achievement': 0
        }
        db.transaction(txn => {
            txn.executeSql(
                "SELECT SUM(prev_y_target), SUM(prev_y_achievement) FROM self_appraisal_product_wise WHERE month=" + "'" + mData[i].trim() + "' AND emp_code = '" + UrlStorage.ParameterList.BasicData.customer_code + "'",
                [],
                (sqlTnx, res) => {
                    const len = res.rows.length;
                    if (len > 0) {
                        obj.target = results.rows.item(0).target
                        obj.achievement = results.rows.item(0).achievement
                    }
                },
                error => { }
            );
        });
        monthWishTargetList.push(obj)
    }
    return monthWishTargetList
}
export const checkDataFor_sum_of_target = () => {
    var data = ''
    db.transaction(txn => {
        txn.executeSql(
            "SELECT SUM(target) FROM self_appraisal_product_wise WHERE emp_code = '" + UrlStorage.ParameterList.BasicData.customer_code + "'",
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                if (len > 0) {
                    data = results.rows.item(0);
                }
            },
            error => { }
        );
    });

    return data
}
export const checkDataFor_sum_of_achievement = () => {
    var data = ''
    db.transaction(txn => {
        txn.executeSql(
            "SELECT SUM(achievement) FROM self_appraisal_product_wise WHERE emp_code = '" + UrlStorage.ParameterList.BasicData.customer_code + "'",
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                if (len > 0) {
                    data = results.rows.item(0);
                }
            },
            error => { }
        );
    });

    return data
}
export const checkDataFor_sum_of_prev_y_target = () => {
    var data = ''
    db.transaction(txn => {
        txn.executeSql(
            "SELECT SUM(prev_y_target) FROM self_appraisal_product_wise WHERE emp_code = '" + UrlStorage.ParameterList.BasicData.customer_code + "'",
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                if (len > 0) {
                    data = results.rows.item(0);
                }
            },
            error => { }
        );
    });

    return data
}
export const checkDataFor_sum_of_prev_y_achievement = () => {
    var data = ''
    db.transaction(txn => {
        txn.executeSql(
            "SELECT SUM(prev_y_achievement) FROM self_appraisal_product_wise WHERE emp_code = '" + UrlStorage.ParameterList.BasicData.customer_code + "'",
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                if (len > 0) {
                    data = results.rows.item(0);
                }
            },
            error => { }
        );
    });

    return data
}

//branch_schemes_PDF CHECK

//branch_dump CHECK
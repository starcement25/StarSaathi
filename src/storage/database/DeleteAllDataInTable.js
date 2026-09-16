import { DataBaseSetup } from './DataBase'
const db = DataBaseSetup()

export const deleteAllDataIn_employee_master_login = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM employee_master_login`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_menu_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM menu_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_order_form_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM order_form_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_product_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM product_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_user_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM user_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_route_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM route_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_customer_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM customer_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_outstanding_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM outstanding_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_product_group_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM product_group_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_product_sub_group_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM product_sub_group_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_product_brand_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM product_brand_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_product_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM product_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_mrp = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM mrp`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_route_plan_transaction = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM route_plan_transaction`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_order_header = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM order_header`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_payment_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM payment_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_payment_header = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM payment_header`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_location = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM location`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_order_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM order_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_prospective_customer_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM prospective_customer_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_attendence = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM attendence`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_transport_mode_category = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM transport_mode_category`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_transport_mode_sub_category = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM transport_mode_sub_category`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_tour_expenses = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM tour_expenses`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_bank_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM bank_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_prospective_customer_header = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM prospective_customer_header`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_prospective_customer_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM prospective_customer_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_data_download_log = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM data_download_log`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_prev_stock_counting_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM prev_stock_counting_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_stock_audit = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM stock_audit`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_rds_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM rds_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_route_plan_access_period = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM route_plan_access_period`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_emp_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM emp_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_branch_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM branch_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_vendor_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM vendor_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_closing_stock = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM closing_stock`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_freight_expenses = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM freight_expenses`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_loyalty_card_holder_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM loyalty_card_holder_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_card_transaction = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM card_transaction`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_goods_in_transit = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM goods_in_transit`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_transaction_log = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM transaction_log`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_scheme_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM scheme_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_loyalty_purchase_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM loyalty_purchase_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_app_info = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM app_info`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_mis_transaction_log = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM mis_transaction_log`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_notes_info = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM notes_info`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_user_access = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM user_access`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_route_plan_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM route_plan_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_emp_image = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM emp_image`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_supporting_attachment_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM supporting_attachment_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_lodging_expenses = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM lodging_expenses`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_fooding_expenses = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM fooding_expenses`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_gcm = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM gcm`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_notification_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM notification_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_redeeme_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM redeeme_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_sauda_allocation = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM sauda_allocation`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_customer_branch_relation = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM customer_branch_relation`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_sauda_header = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM sauda_header`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_sauda_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM sauda_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_broker_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM broker_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_sauda_form_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM sauda_form_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_product_promotion = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM product_promotion`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_pin_code_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM pin_code_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_generic_oil_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM generic_oil_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_market_feedback = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM market_feedback`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_menu_access = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM menu_access`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_street_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM street_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_sauda_allocation_log = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM sauda_allocation_log`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_sauda_allocation_access = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM sauda_allocation_access`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_pending_contract = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM pending_contract`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_sauda_transaction_log = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM sauda_transaction_log`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_prev_order_counting_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM prev_order_counting_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_pending_contract_ageing = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM pending_contract_ageing`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_outstanding_ageing = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM outstanding_ageing`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_order_status = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM order_status`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_sauda_mrp = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM sauda_mrp`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_sale_performance_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM sale_performance_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_OTP_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM OTP_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_destination_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM destination_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_route_customer_plan_transaction = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM route_customer_plan_transaction`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_check_in_out_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM check_in_out_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_survey_output = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM survey_output`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_survey_output_temp = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM survey_output_temp`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_survey_input = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM survey_input`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_survey_form_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM survey_form_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_survey_category_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM TABLEsurvey_category_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_table_view = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM table_view`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_market_feedback_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM market_feedback_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_competitor_group_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM competitor_group_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_mf_stk_audit_header = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM mf_stk_audit_header`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_mf_stk_audit_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM mf_stk_audit_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_emp_menu_access = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM emp_menu_access`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_emp_target_achievement = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM emp_target_achievement`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_non_trade_customer_master = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM non_trade_customer_master`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_survey_header = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM survey_header`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_self_appraisal_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM EXISTSself_appraisal_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_self_appraisal_customer_wise = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM EXISTSself_appraisal_customer_wise`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_self_appraisal_branch_wise = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM EXISTSself_appraisal_branch_wise`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_hint_remarks_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM hint_remarks_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_yellow_card_details = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM yellow_card_details`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_call_duration = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM call_duration`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_yellow_card_date_validation = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM yellow_card_date_validation`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_self_appraisal_product_wise = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM self_appraisal_product_wise`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_branch_schemes_PDF = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM branch_schemes_PDF`, [], (sqlTxn, res) => { }, error => { },) })
}

export const deleteAllDataIn_branch_dump = () => {
    db.transaction(txn => { txn.executeSql(`DELETE FROM branch_dump`, [], (sqlTxn, res) => { }, error => { },) })
}

export const clearDestinationMaster = () => {
    return new Promise((resolve, reject) => {
        db.transaction(txn => { txn.executeSql(`DELETE FROM destination_master`, [], () => resolve(), error => reject(error)) })
    })
}
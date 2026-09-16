import { DataBaseSetup } from './DataBase'
const db = DataBaseSetup()

export const insertDataIn_employee_master_login = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO employee_master_login(emp_code, date, emp_name, device_id, password , sale_access, flag, app_version, updation_flag) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
                    [data[i].emp_code, data[i].date, data[i].emp_name, data[i].device_id, data[i].password, data[i].sale_access, data[i].flag, data[i].app_version, data[i].updation_flag],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_menu_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO menu_details(menu_id, user_id, attendance , route_plan , take_order, collection, stk_audit, business_prospect, tour_exp, capture_image, notes , activity_report, loyalty, mis_report, delete_transaction , loading_freight , sauda_allocation , survey , product_promotion, replacement , market_feedback , sauda_allocation_app , pending_contract , sauda_mis , order_status , checkout , sauda_outstanding , sale_performance , check_in_out , outstanding , outstanding_ageing , target_achievement , wholesaler_info , self_appraisal , yellow_card , catalogue , catalogue_url , tele_tran , TD_allocation_app , catalogue_dependency , TD_allocation_vertical , run_time_TD_approval_vertical , quotation , CRM_app , ISP , monthly_report_mail , retailer_app ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
                    [data[i].menu_id, data[i].user_id, data[i].attendance, data[i].route_plan, data[i].take_order, data[i].collection, data[i].stk_audit, data[i].business_prospect, data[i].tour_exp, data[i].capture_image, data[i].notes, data[i].activity_report, data[i].loyalty, data[i].mis_report, data[i].delete_transaction, data[i].loading_freight, data[i].sauda_allocation, data[i].survey, data[i].product_promotion, data[i].replacement, data[i].market_feedback, data[i].sauda_allocation_app, data[i].pending_contract, data[i].sauda_mis, data[i].order_status, data[i].checkout, data[i].sauda_outstanding, data[i].sale_performance, data[i].check_in_out, data[i].outstanding, data[i].outstanding_ageing, data[i].target_achievement, data[i].wholesaler_info, data[i].self_appraisal, data[i].yellow_card, data[i].catalogue, data[i].catalogue_url, data[i].tele_tran, data[i].TD_allocation_app, data[i].catalogue_dependency, data[i].TD_allocation_vertical, data[i].run_time_TD_approval_vertical, data[i].quotation, data[i].CRM_app, data[i].ISP, data[i].monthly_report_mail, data[i].retailer_app],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_order_form_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO order_form_details(order_form_id, user_id, credit_limit, cl_stk, mrp_input_dropdown, mrp, TD, TD_type, add_customer, tagged_customer_for_business_prospect, sale_rate , sale_rate_input_dropdown , attached_printer, printer_mandatory, payment_type, tag_distributor, sale, instruction, VAT, VAT_details, branch_rds_transfer, amount, VAT_type, TD_calc, TD_trans_type, VAT_calc_on, TD_validation, TD_calc_basedon, premium, previous_order, add_customer_OTP, customer_information_check, add_customer_route_creation, order_type, freight_component, tax_type, destination, input_screen_normal, input_screen_special, add_customer_details, add_customer_trade_nontrade, printer_type, printer_menu, hint_remarks, hint_remarks_val, add_customer_image_creation, distributor_route_emp_relation, multiple_UOM, input_screen_planwise, TD_type_input_dropdown, input_screen_planwise_filter1wise, input_screen_price_validation, sauda_sale_rate_input_dropdown) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
                    [data[i].order_form_id, data[i].user_id, data[i].credit_limit, data[i].cl_stk, data[i].mrp_input_dropdown, data[i].mrp, data[i].TD, data[i].TD_type, data[i].add_customer, data[i].tagged_customer_for_business_prospect, data[i].sale_rate, data[i].sale_rate_input_dropdown, data[i].attached_printer, data[i].printer_mandatory, data[i].payment_type, data[i].tag_distributor, data[i].sale, data[i].instruction, data[i].VAT, data[i].VAT_details, data[i].branch_rds_transfer, data[i].amount, data[i].VAT_type, data[i].TD_calc, data[i].TD_trans_type, data[i].VAT_calc_on, data[i].TD_validation, data[i].TD_calc_basedon, data[i].premium, data[i].previous_order, data[i].add_customer_OTP, data[i].customer_information_check, data[i].add_customer_route_creation, data[i].order_type, data[i].freight_component, data[i].tax_type, data[i].destination, data[i].input_screen_normal, data[i].input_screen_special, data[i].add_customer_details, data[i].add_customer_trade_nontrade, data[i].printer_type, data[i].printer_menu, data[i].hint_remarks, data[i].hint_remarks_val, data[i].add_customer_image_creation, data[i].distributor_route_emp_relation, data[i].multiple_UOM, data[i].input_screen_planwise, data[i].TD_type_input_dropdown, data[i].input_screen_planwise_filter1wise, data[i].input_screen_price_validation, data[i].sauda_sale_rate_input_dropdown],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_product_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO product_details(product_id, user_id, no_of_filter, col1, col2, col3, col4, uom_wise_mrp, sauda_allocation_basedon_filter, product_in_business_prospect, branch_wise_product, secondary_unit, destination_price_list, destination_ordertype_price_list, state_wise_mrp, multiple_rate, product_qty_wise_TD, focus_product) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
                    [data[i].product_id, data[i].user_id, data[i].no_of_filter, data[i].col1, data[i].col2, data[i].col3, data[i].col4, data[i].uom_wise_mrp, data[i].sauda_allocation_basedon_filter, data[i].product_in_business_prospect, data[i].branch_wise_product, data[i].secondary_unit, data[i].destination_price_list, data[i].destination_ordertype_price_list, data[i].state_wise_mrp, data[i].multiple_rate, data[i].product_qty_wise_TD, data[i].focus_product],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_user_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO user_details(user_id, name, address, phone_no, email, license_key, no_users, nick_name, no_of_branches, image, email_hierarchywise, vertical_fields, vertical_fields_value, previous_stock, multiple_prospect, multiple_prospect_value, stock_audit_scan, stock_audit_rate, location_drag_drop, tour_plan_daywise, check_in_out_typeval, FCM, minimum_stock, stk_audit_unit, stk_audit_irrespective_routeplan, stk_audit_cust_type, notes_info_hint_remarks, notes_info_upload_photo, country, time_zonea) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
                    [data[i].user_id, data[i].name, data[i].address, data[i].phone_no, data[i].email, data[i].license_key, data[i].no_users, data[i].nick_name, data[i].no_of_branches, data[i].image, data[i].email_hierarchywise, data[i].vertical_fields, data[i].vertical_fields_value, data[i].previous_stock, data[i].multiple_prospect, data[i].multiple_prospect_value, data[i].stock_audit_scan, data[i].stock_audit_rate, data[i].location_drag_drop, data[i].tour_plan_daywise, data[i].check_in_out_typeval, data[i].FCM, data[i].minimum_stock, data[i].stk_audit_unit, data[i].stk_audit_irrespective_routeplan, data[i].stk_audit_cust_type, data[i].notes_info_hint_remarks, data[i].notes_info_upload_photo, data[i].country, data[i].time_zonea],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_route_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO route_master(route_code, route_name, emp_code, flag) VALUES ( ?, ?, ?, ?)`,
                    [data[i].route_code, data[i].route_name, data[i].emp_code, data[i].flag],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_outstanding_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO outstanding_master(customer_code, recid, invoice_id, customer_name, date, invoice_amount, due_amount) VALUES (?, ?, ?, ?, ?, ?, ?)`,
                    [data[i].customer_code, data[i].recid, data[i].invoice_id, data[i].customer_name, data[i].date, data[i].invoice_amount, data[i].due_amount],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_product_group_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO product_group_master(product_group_code, product_group_name, vertical_value) VALUES (?, ?, ?)`,
                    [data[i].product_group_code, data[i].product_group_name, data[i].vertical_value],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_product_sub_group_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO product_sub_group_master(product_sub_group_code, product_group_code, product_sub_group_name) VALUES (?, ?, ?)`,
                    [data[i].product_sub_group_code, data[i].product_group_code, data[i].product_sub_group_name],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_product_brand_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO product_brand_master(product_brand_code, product_sub_group_code, product_brand_name) VALUES (?, ?, ?)`,
                    [data[i].product_brand_code, data[i].product_sub_group_code, data[i].product_brand_name],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_product_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO product_master(prod_code, product_group_code, product_group_name, product_sub_group_code, product_sub_group_name, product_brand_code, product_brand_name, prod_desc, black_list, acedns, cl_stk, uom1, uom2, conversion_factor, pack_size, uom3, conversion_factor_two, TD, branch_code, vertical_value, secondary_unit, dns_prod_code, focus, weightage, vat, addl_vat, freight_cost) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
                    [data[i].prod_code, data[i].product_group_code, data[i].product_group_name, data[i].product_sub_group_code, data[i].product_sub_group_name, data[i].product_brand_code, data[i].product_brand_name, data[i].prod_desc, data[i].black_list, data[i].acedns, data[i].cl_stk, data[i].uom1, data[i].uom2, data[i].conversion_factor, data[i].pack_size, data[i].uom3, data[i].conversion_factor_two, data[i].TD, data[i].branch_code, data[i].vertical_value, data[i].secondary_unit, data[i].dns_prod_code, data[i].focus, data[i].weightage, data[i].vat, data[i].addl_vat, data[i].freight_cost],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_mrp = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO mrp(sku_code, mrp_code, mrp_value, sale_rate, UOM, branch_code, destination_code, order_type, acedns, ws_rate, distributor_rate, ss_rate, depot_rate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
                    [data[i].sku_code, data[i].mrp_code, data[i].mrp_value, data[i].sale_rate, data[i].UOM, data[i].branch_code, data[i].destination_code, data[i].order_type, data[i].acedns, data[i].ws_rate, data[i].distributor_rate, data[i].ss_rate, data[i].depot_rate],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_route_plan_transaction = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO route_plan_transaction(route_plan_trans_id, emp_code, route_code, visit_date, create_date, route_name, flag , previous_route_code, previous_route_name, remarks, distributor_code, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
                    [data[i].route_plan_trans_id, data[i].emp_code, data[i].route_code, data[i].visit_date, data[i].create_date, data[i].route_name, data[i].flag, data[i].previous_route_code, data[i].previous_route_name, data[i].remarks, data[i].distributor_code, data[i].status],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_order_header = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO order_header(order_no, customer_code, transferred, sale_type, flag, d_instruction, TD, order_value, tag_distributor_code, transaction_type, VAT, vertical_value, destination_code, order_type, freight_component, GST_type, price_validation_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
                    [data[i].order_no, data[i].customer_code, data[i].transferred, data[i].sale_type, data[i].flag, data[i].d_instruction, data[i].TD, data[i].order_value, data[i].tag_distributor_code, data[i].transaction_type, data[i].VAT, data[i].vertical_value, data[i].destination_code, data[i].order_type, data[i].freight_component, data[i].GST_type, data[i].price_validation_type],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_payment_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO payment_details(receipt_id, invoice_id, amount, discount, flag, rec_id) VALUES (?, ?, ?, ?, ?, ?)`,
                    [data[i].receipt_id, data[i].invoice_id, data[i].amount, data[i].discount, data[i].flag, data[i].rec_id],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_payment_header = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO payment_header(receipt_id, customer_code, amount, cash_cheque, cheque_no, date, bank, rdate, transferred, flag, p_remark, sale_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
                    [data[i].receipt_id, data[i].customer_code, data[i].amount, data[i].cash_cheque, data[i].cheque_no, data[i].date, data[i].bank, data[i].rdate, data[i].transferred, data[i].flag, data[i].p_remark, data[i].sale_type],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_location = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO location(emp_code, trans_id, date, latt, longi, flag, network_response) VALUES (?, ?, ?, ?, ?, ?, ?)`,
                    [data[i].emp_code, data[i].trans_id, data[i].date, data[i].latt, data[i].longi, data[i].flag, data[i].network_response],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_order_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO order_details(order_no, sku_code, qty, flag, mrp_code, TD, sale_rate, VAT, amount, freight_charge, premium, UOM) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].order_no, data[i].sku_code, data[i].qty, data[i].flag, data[i].mrp_code, data[i].TD, data[i].sale_rate, data[i].VAT, data[i].amount, data[i].freight_charge, data[i].premium, data[i].UOM],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_prospective_customer_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO prospective_customer_master(emp_code, customer_code, customer_name, address, pin, area, phone_no, remarks, area_name) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].emp_code, data[i].customer_code, data[i].customer_name, data[i].address, data[i].pin, data[i].area, data[i].phone_no, data[i].remarks, data[i].area_name],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_attendence = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO attendence(emp_code, trans_id, date, flag) VALUES ( ?, ?, ?, ? )`,
                    [data[i].emp_code, data[i].trans_id, data[i].date, data[i].flag],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_transport_mode_category = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO transport_mode_category(transport_mode_cat_id, transport_mode_cat_name) VALUES ( ?, ? )`,
                    [data[i].transport_mode_cat_id, data[i].transport_mode_cat_name],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_transport_mode_sub_category = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO transport_mode_sub_category(transport_mode_sub_cat_id, transport_mode_sub_cat_name, transport_mode_cat_id) VALUES ( ?, ?, ? )`,
                    [data[i].transport_mode_sub_cat_id, data[i].transport_mode_sub_cat_name, data[i].transport_mode_cat_id],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_tour_expenses = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO tour_expenses(tour_exp_trans_id, emp_code, start_destination, end_destination, fare, tour_date, transport_mode_sub_cat_id, transport_mode_cat_id, supporting_attached, distance, flag, attachment_id) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].tour_exp_trans_id, data[i].emp_code, data[i].start_destination, data[i].end_destination, data[i].fare, data[i].tour_date, data[i].transport_mode_sub_cat_id, data[i].transport_mode_cat_id, data[i].supporting_attached, data[i].distance, data[i].flag, data[i].attachment_id],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_bank_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO bank_master(bank_id, bank_name) VALUES ( ?, ? )`,
                    [data[i].bank_id, data[i].bank_name],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_prospective_customer_header = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO prospective_customer_header(trans_id, customer_name, address, pin, area, phone_no, remarks, tagged_customer_code, flag, area_name, cust_type) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].trans_id, data[i].customer_name, data[i].address, data[i].pin, data[i].area, data[i].phone_no, data[i].remarks, data[i].tagged_customer_code, data[i].flag, data[i].area_name, data[i].cust_type],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_prospective_customer_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO prospective_customer_details(trans_id, product_code, flag) VALUES ( ?, ?, ? )`,
                    [data[i].trans_id, data[i].product_code, data[i].flag],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_data_download_log = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO data_download_log(table_name, last_download_time, is_download, is_refresh) VALUES ( ?, ?, ?, ? )`,
                    [data[i].table_name, data[i].last_download_time, data[i].is_download, data[i].is_refresh],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_prev_stock_counting_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO prev_stock_counting_master(customer_code, product_code, visit_details) VALUES ( ?, ?, ? )`,
                    [data[i].customer_code, data[i].product_code, data[i].visit_details],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_stock_audit = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO stock_audit(transaction_id, customer_code, product_code, quantity, product_mrp, product_details, remarks, flag) VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].transaction_id, data[i].customer_code, data[i].product_code, data[i].quantity, data[i].product_mrp, data[i].product_details, data[i].remarks, data[i].flag],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_rds_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO rds_master(rds_code, rds_name, rds_type) VALUES ( ?, ?, ? )`,
                    [data[i].rds_code, data[i].rds_name, data[i].rds_type],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_route_plan_access_period = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO route_plan_access_period(access_start_date, access_end_date, period) VALUES ( ?, ?, ? )`,
                    [data[i].access_start_date, data[i].access_end_date, data[i].period],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_emp_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO emp_master(emp_code, emp_name, sale_access, reporting_to, level, designation, vertical_value, branch_code, state, zone, acedns, lower_leaves) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].emp_code, data[i].emp_name, data[i].sale_access, data[i].reporting_to, data[i].level, data[i].designation, data[i].vertical_value, data[i].branch_code, data[i].state, data[i].zone, data[i].acedns, data[i].lower_leaves],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_branch_master = (data) => {
    if (!data || data.length === 0) {
        return Promise.resolve(false)
    }
    return new Promise((resolve, reject) => {
        db.transaction(
            txn => {
                data.forEach((row, i) => {
                    txn.executeSql(`INSERT INTO branch_master(company_code, branch_code, branch_name, Hq, plant_name) VALUES (?, ?, ?, ?, ?)`,
                        [row.company_code, row.branch_code, row.branch_name, row.Hq, row.plant_name],
                        (sqlTxn, res) => { },
                        error => { }
                    )
                })
            },
            error => { reject(error) },
            () => { resolve(true) }
        )
    })
}

export const insertDataIn_vendor_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO vendor_master(vendor_code, vendor_name) VALUES ( ?, ? )`,
                    [data[i].vendor_code, data[i].vendor_name],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_closing_stock = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO closing_stock(prod_code, cl_stk) VALUES ( ?, ? )`,
                    [data[i].prod_code, data[i].cl_stk],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_freight_expenses = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO freight_expenses(freight_exp_trans_id, emp_code, trans_type, date, amount, remarks, flag) VALUES ( ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].freight_exp_trans_id, data[i].emp_code, data[i].trans_type, data[i].date, data[i].amount, data[i].remarks, data[i].flag],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_loyalty_card_holder_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO loyalty_card_holder_master(loyalty_card_holder_code, loyalty_card_holder_name, loyalty_card_no, card_type, total_purchase_value, total_reward_point, last_update_on, redeemed, phone_no, address, vehicle_no) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].loyalty_card_holder_code, data[i].loyalty_card_holder_name, data[i].loyalty_card_no, data[i].card_type, data[i].total_purchase_value, data[i].total_reward_point, data[i].last_update_on, data[i].redeemed, data[i].phone_no, data[i].address, data[i].vehicle_no],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_card_transaction = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO card_transaction(transaction_id, loyalty_card_no, rds_code, purchase_value, trans_type, vehicle_no, vehicle_type, points_earned, points_redeemed, flag) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].transaction_id, data[i].loyalty_card_no, data[i].rds_code, data[i].purchase_value, data[i].trans_type, data[i].vehicle_no, data[i].vehicle_type, data[i].points_earned, data[i].points_redeemed, data[i].flag],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_goods_in_transit = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO goods_in_transit(grn_no, despatcher_code, prod_code, despatch_qty, bal_rec_qty, status, order_no, trans_type, sale_rate) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].grn_no, data[i].despatcher_code, data[i].prod_code, data[i].despatch_qty, data[i].bal_rec_qty, data[i].status, data[i].order_no, data[i].trans_type, data[i].sale_rate],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_transaction_log = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO transaction_log(branch_name, rds_name, emp_name, trans_date, trans_id, customer_name, sku_name, qty, sale_rate, amount, VAT, TD, trans_type, d_instruction) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].branch_name, data[i].rds_name, data[i].emp_name, data[i].trans_date, data[i].trans_id, data[i].customer_name, data[i].sku_name, data[i].qty, data[i].sale_rate, data[i].amount, data[i].VAT, data[i].TD, data[i].trans_type, data[i].d_instruction],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_scheme_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO scheme_details(vertical_name, scheme_value) VALUES ( ?, ? )`,
                    [data[i].vertical_name, data[i].scheme_value],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_loyalty_purchase_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO loyalty_purchase_details(loyalty_card_no, vertical_name, purchase_value, accumulated_points, redeemed_points) VALUES ( ?, ?, ?, ?, ? )`,
                    [data[i].loyalty_card_no, data[i].vertical_name, data[i].purchase_value, data[i].accumulated_points, data[i].redeemed_points],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_app_info = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO app_info(nick_name, app_version, db_version, logo, base_url) VALUES ( ?, ?, ?, ?, ? )`,
                    [data[i].nick_name, data[i].app_version, data[i].db_version, data[i].logo, data[i].base_url],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_mis_transaction_log = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO mis_transaction_log(branch_code, rds_code, emp_code, trans_date, trans_id, customer_code, customer_name, sku_code, qty, sale_rate, amount, VAT, TD, trans_type, d_instruction, group_code) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].branch_code, data[i].rds_code, data[i].emp_code, data[i].trans_date, data[i].trans_id, data[i].customer_code, data[i].customer_name, data[i].sku_code, data[i].qty, data[i].sale_rate, data[i].amount, data[i].VAT, data[i].TD, data[i].trans_type, data[i].d_instruction, data[i].group_code],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_notes_info = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO notes_info(trans_id, feedback) VALUES ( ?, ? )`,
                    [data[i].trans_id, data[i].feedback],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_user_access = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO user_access(emp_code, accessibility_menu) VALUES ( ?, ? )`,
                    [data[i].emp_code, data[i].accessibility_menu],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_route_plan_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO route_plan_details(route_plan_id, user_id, route_plan_access_period, route_plan_deviation, route_plan_approval, route_plan_flow, route_customer_planning, distributor_route_planning, distributor_route_planning_multiple) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].route_plan_id, data[i].user_id, data[i].route_plan_access_period, data[i].route_plan_deviation, data[i].route_plan_approval, data[i].route_plan_flow, data[i].route_customer_planning, data[i].distributor_route_planning, data[i].distributor_route_planning_multiple],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_emp_image = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO emp_image(profile_image) VALUES ( ? )`,
                    [data[i].profile_image],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_supporting_attachment_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO supporting_attachment_details(attachment_id, emp_code, source_name, flag) VALUES ( ?, ?, ?, ? )`,
                    [data[i].attachment_id, data[i].emp_code, data[i].source_name, data[i].flag],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_lodging_expenses = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO lodging_expenses(lodg_exp_trans_id, emp_code, base_station, hotel_name, rent, chkin_date, chkout_date, payment_amount, payment_mode, flag, attachment_id) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].lodg_exp_trans_id, data[i].emp_code, data[i].base_station, data[i].hotel_name, data[i].rent, data[i].chkin_date, data[i].chkout_date, data[i].payment_amount, data[i].payment_mode, data[i].flag, data[i].attachment_id],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_fooding_expenses = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO fooding_expenses(food_exp_trans_id, emp_code, base_station, expense_type, date, accompany, payment_amount, payment_mode, flag, attachment_id) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].food_exp_trans_id, data[i].emp_code, data[i].base_station, data[i].expense_type, data[i].date, data[i].accompany, data[i].payment_amount, data[i].payment_mode, data[i].flag, data[i].attachment_id],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_gcm = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO gcm(registrationid) VALUES ( ? )`,
                    [data[i].registrationid],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_notification_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO notification_details(notification_id, notification_type, sender_id, message, flag, ack_id) VALUES ( ?, ?, ?, ?, ?, ? )`,
                    [data[i].notification_id, data[i].notification_type, data[i].sender_id, data[i].message, data[i].flag, data[i].ack_id],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_redeeme_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO redeeme_details(scheme_expiry_date, points, award) VALUES ( ?, ?, ? )`,
                    [data[i].scheme_expiry_date, data[i].points, data[i].award],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_sauda_allocation = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO sauda_allocation(emp_code, product_filter_code, qty, allot_qty, BAL) VALUES ( ?, ?, ?, ?, ? )`,
                    [data[i].emp_code, data[i].product_filter_code, data[i].qty, data[i].allot_qty, data[i].BAL],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_customer_branch_relation = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO customer_branch_relation(customer_code, branch_code, acedns) VALUES ( ?, ?, ? )`,
                    [data[i].customer_code, data[i].branch_code, data[i].acedns],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_sauda_header = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO sauda_header(sauda_no, customer_code, transferred, flag, d_instruction, TD, sauda_value, broker_id, transaction_type, VAT, branch_code, sauda_valid_from) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].sauda_no, data[i].customer_code, data[i].transferred, data[i].flag, data[i].d_instruction, data[i].TD, data[i].sauda_value, data[i].broker_id, data[i].transaction_type, data[i].VAT, data[i].branch_code, data[i].sauda_valid_from],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_sauda_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO sauda_details(sauda_no, sku_code, qty, flag, mrp_code, TD, sale_rate, VAT, amount, freight_charge, premium) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].sauda_no, data[i].sku_code, data[i].qty, data[i].flag, data[i].mrp_code, data[i].TD, data[i].sale_rate, data[i].VAT, data[i].amount, data[i].freight_charge, data[i].premium],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_broker_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO broker_master(broker_id, broker_name) VALUES ( ?, ? )`,
                    [data[i].broker_id, data[i].broker_name],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_sauda_form_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO sauda_form_details(sauda_form_id, user_id, sauda_allocation_carry_forward, sauda_depot_wise, sauda_rate_variable, sauda_rate_variable_value, sauda_booked_through, sauda_valid_from) VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].sauda_form_id, data[i].user_id, data[i].sauda_allocation_carry_forward, data[i].sauda_depot_wise, data[i].sauda_rate_variable, data[i].sauda_rate_variable_value, data[i].sauda_booked_through, data[i].sauda_valid_from],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_product_promotion = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO product_promotion(prospect_code, prospect_name, pin, street_name, street_no, building_no, apartment_no, phone_no, oil_used, email, competitor_name) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].prospect_code, data[i].prospect_name, data[i].pin, data[i].street_name, data[i].street_no, data[i].building_no, data[i].apartment_no, data[i].phone_no, data[i].oil_used, data[i].email, data[i].competitor_name],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_pin_code_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO pin_code_master(pin_code, area) VALUES ( ?, ? )`,
                    [data[i].pin_code, data[i].area],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_generic_oil_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO generic_oil_master(oil_name, competitor_name) VALUES ( ?, ? )`,
                    [data[i].oil_name, data[i].competitor_name],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_market_feedback = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO market_feedback(market_feedback_id, route_code, product_group, competitor_name, PTD, PTR, PTC, PV, customer_code) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].market_feedback_id, data[i].route_code, data[i].product_group, data[i].competitor_name, data[i].PTD, data[i].PTR, data[i].PTC, data[i].PV, data[i].customer_code],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_menu_access = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO menu_access(not_accessibility_menu) VALUES ( ? )`,
                    [data[i].not_accessibility_menu],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_street_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO street_master(street_name, pin_code) VALUES ( ?, ? )`,
                    [data[i].street_name, data[i].pin_code],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_sauda_allocation_log = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO sauda_allocation_log(allocation_id, date, emp_code, product_filter_code, qty_ton, qty) VALUES ( ?, ?, ?, ?, ?, ? )`,
                    [data[i].allocation_id, data[i].date, data[i].emp_code, data[i].product_filter_code, data[i].qty_ton, qty],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_sauda_allocation_access = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO sauda_allocation_access(emp_code, designation, do_allocation, get_allocation) VALUES ( ?, ?, ?, ? )`,
                    [data[i].emp_code, data[i].designation, data[i].do_allocation, data[i].get_allocation],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_pending_contract = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO pending_contract(branch_code, prod_code, customer_code, broker_id, contract_qty, despatch_qty, pending_qty, sauda_date) VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].branch_code, data[i].prod_code, data[i].customer_code, data[i].broker_id, data[i].contract_qty, data[i].despatch_qty, data[i].pending_qty, data[i].sauda_date],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_sauda_transaction_log = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO sauda_transaction_log(branch_code, broker_id, emp_code, sauda_date, sauda_no, customer_code, prod_code, qty, convert_qty_one, convert_qty_two, sale_rate, TD, premium, freight_charge, amount, plant, state, zone) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].branch_code, data[i].broker_id, data[i].emp_code, data[i].sauda_date, data[i].sauda_no, data[i].customer_code, data[i].prod_code, data[i].qty, data[i].convert_qty_one, data[i].convert_qty_two, data[i].sale_rate, data[i].TD, data[i].premium, data[i].freight_charge, data[i].amount, data[i].plant, data[i].state, data[i].zone],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_prev_order_counting_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO prev_order_counting_master(customer_code, product_code, visit_details) VALUES ( ?, ?, ? )`,
                    [data[i].customer_code, data[i].product_code, data[i].visit_details],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_pending_contract_ageing = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO pending_contract_ageing(branch_code, product_group_code, prod_code, customer_code, broker_id, qty_0_15, qty_16_30, qty_31_45, qty_46_60, qty_greater_60, greater_60_days, contract_qty, despatch_qty) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].branch_code, data[i].product_group_code, data[i].prod_code, data[i].customer_code, data[i].broker_id, data[i].qty_0_15, data[i].qty_16_30, data[i].qty_31_45, data[i].qty_46_60, data[i].qty_greater_60, data[i].greater_60_days, data[i].contract_qty, data[i].despatch_qty],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_outstanding_ageing = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO outstanding_ageing(customer_code, customer_name, outstanding_amount, amount_0_15_days, amount_16_30_days, amount_31_45_days, amount_46_90_days, amount_greater_90_days) VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].customer_code, data[i].customer_name, data[i].outstanding_amount, data[i].amount_0_15_days, data[i].amount_16_30_days, data[i].amount_31_45_days, data[i].amount_46_90_days, data[i].amount_greater_90_days],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_order_status = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO order_status(order_no, customer_code, product_code, order_qty, delivery_qty, status, remarks, flag) VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].order_no, data[i].customer_code, data[i].product_code, data[i].order_qty, data[i].delivery_qty, data[i].status, data[i].remarks, data[i].flag],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_sauda_mrp = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO sauda_mrp(sku_code, mrp_code, mrp_value, sale_rate, UOM, branch_code) VALUES ( ?, ?, ?, ?, ?, ? )`,
                    [data[i].sku_code, data[i].mrp_code, data[i].mrp_value, data[i].sale_rate, data[i].UOM, data[i].branch_code],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_sale_performance_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO sale_performance_details(customer_code, customer_name, emp_code, emp_name, product_group_code, YTD_sale, MTD_sale) VALUES ( ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].customer_code, data[i].customer_name, data[i].emp_code, data[i].emp_name, data[i].product_group_code, data[i].YTD_sale, data[i].MTD_sale],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_OTP_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO OTP_details(mobile_no, OTP, flag) VALUES ( ?, ?, ? )`, [data[i].mobile_no, data[i].OTP, data[i].flag],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_destination_master = (data = []) => {
    return new Promise((resolve, reject) => {
        if (!data.length) {
            resolve()
            return
        }
        db.transaction(txn => {
            data.forEach(item => {
                txn.executeSql(`INSERT INTO destination_master (destination_code, destination_name, ex_for_type) VALUES (?, ?, ?)`,
                    [item.destination_code, item.destination_name, item.ex_for_type ?? '']
                )
            })
        },
            error => reject(error),
            () => resolve())
    })
}

export const insertDataIn_route_customer_plan_transaction = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO route_customer_plan_transaction(route_plan_trans_id, route_code, visit_date, customer_code, flag, status) VALUES ( ?, ?, ?, ?, ?, ? )`,
                    [data[i].route_plan_trans_id, data[i].route_code, data[i].visit_date, data[i].customer_code, data[i].flag, data[i].status],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_check_in_out_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO check_in_out_details(trans_id, check_in_time, customer_code, check_out_time, remarks) VALUES ( ?, ?, ?, ?, ? )`,
                    [data[i].trans_id, data[i].check_in_time, data[i].customer_code, data[i].check_out_time, data[i].remarks],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_survey_output = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO survey_output(survey_id, row_id, action_id, value, type, flag) VALUES ( ?, ?, ?, ?, ?, ? )`,
                    [data[i].survey_id, data[i].row_id, data[i].action_id, data[i].value, data[i].type, data[i].flag],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_survey_output_temp = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO survey_output_temp(row_id, value, layout_name) VALUES ( ?, ?, ? )`,
                    [data[i].row_id, data[i].value, data[i].layout_name],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_survey_input = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO survey_input(row_id, action_id, menu_id, layout_name, display_name, type, display_table_name, mandatory, action, validation, display_order, survey_type, survey_sub_menu, acedns) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].row_id, data[i].action_id, data[i].menu_id, data[i].layout_name, data[i].display_name, data[i].type, data[i].display_table_name, data[i].mandatory, data[i].action, data[i].validation, data[i].display_order, data[i].survey_type, data[i].survey_sub_menu, data[i].acedns],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_survey_form_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO survey_form_details(survey_form_id, user_id, survey_menu, survey_type, survey_type_details, mall_survey_relation, survey_sub_type_details, OTP, survey_layer, survey_submenu, survey_submenu_details, outlet_menu, survey_route_plan, other_text, survey_report_row_id, customer_email_update) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].survey_form_id, data[i].user_id, data[i].survey_menu, data[i].survey_type, data[i].survey_type_details, data[i].mall_survey_relation, data[i].survey_sub_type_details, data[i].OTP, data[i].survey_layer, data[i].survey_submenu, data[i].survey_submenu_details, data[i].outlet_menu, data[i].survey_route_plan, data[i].other_text, data[i].survey_report_row_id, data[i].customer_email_update],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_survey_category_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO survey_category_master(id_row, cat_id, sub_cat_id, cat_name, sub_cat_name) VALUES ( ?, ?, ?, ?, ? )`,
                    [data[i].id_row, data[i].cat_id, data[i].sub_cat_id, data[i].cat_name, data[i].sub_cat_name],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_table_view = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO table_view(row_id, type, value, dependent_on, dependent_value, action) VALUES ( ?, ?, ?, ?, ?, ? )`,
                    [data[i].row_id, data[i].type, data[i].value, data[i].dependent_on, data[i].dependent_value, data[i].action],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_market_feedback_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO market_feedback_details(market_feedback_id, user_id, mf_group_enable, mf_col1, mf_col2, mf_col3, mf_col4, mf_sub_menu_details, mf_sub_menu_image) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].market_feedback_id, data[i].user_id, data[i].mf_group_enable, data[i].mf_col1, data[i].mf_col2, data[i].mf_col3, data[i].mf_col4, data[i].mf_sub_menu_details, data[i].mf_sub_menu_image],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_competitor_group_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO competitor_group_master(group_name, competitor_name, UOM) VALUES ( ?, ?, ? )`,
                    [data[i].group_name, data[i].competitor_name, data[i].UOM],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_mf_stk_audit_header = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO mf_stk_audit_header(mf_stk_audit_id, customer_code, remarks, image) VALUES ( ?, ?, ?, ? )`,
                    [data[i].mf_stk_audit_id, data[i].customer_code, data[i].remarks, data[i].image],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_mf_stk_audit_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO mf_stk_audit_details(mf_stk_audit_id, competitor_name, qty_mt, scheme_discount) VALUES ( ?, ?, ?, ? )`,
                    [data[i].mf_stk_audit_id, data[i].competitor_name, data[i].qty_mt, data[i].scheme_discount],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_emp_menu_access = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO emp_menu_access(menu) VALUES ( ? )`,
                    [data[i].menu],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_emp_target_achievement = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO emp_target_achievement(emp_code, emp_name, month, district, customer_code, customer_name, volume_target, volume_achievement, collection_target, collection_achievement) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].emp_code, data[i].emp_name, data[i].month, data[i].district, data[i].customer_code, data[i].customer_name, data[i].volume_target, data[i].volume_achievement, data[i].collection_target, data[i].collection_achievement],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_non_trade_customer_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO non_trade_customer_master(customer_code, customer_name, address, phone_no, route_code, emp_code, rds_tag) VALUES ( ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].customer_code, data[i].customer_name, data[i].address, data[i].phone_no, data[i].route_code, data[i].emp_code, data[i].rds_tag],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_survey_header = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO survey_header(survey_id, survey_type, menu_name, mall_id, mall_name, business_name, contact_name, phone_no, questions_answered, route_code, flag) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].survey_id, data[i].survey_type, data[i].menu_name, data[i].mall_id, data[i].mall_name, data[i].business_name, data[i].contact_name, data[i].phone_no, data[i].questions_answered, data[i].route_code, data[i].flag],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_self_appraisal_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO EXISTSself_appraisal_details(self_appraisal_id, user_id, multiple_target_achievement, multiple_target_achievement_val, volume_wise, value_wise, product_group_wise, product_sub_group_wise, product_brand_wise, product_wise, employee_wise, customer_wise, branch_wise, HQ_wise, route_wise, on_total, on_individual, month_wise, week_wise, day_wise, UOM_val) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].self_appraisal_id, data[i].user_id, data[i].multiple_target_achievement, data[i].multiple_target_achievement_val, data[i].volume_wise, data[i].value_wise, data[i].product_group_wise, data[i].product_sub_group_wise, data[i].product_brand_wise, data[i].product_wise, data[i].employee_wise, data[i].customer_wise, data[i].branch_wise, data[i].HQ_wise, data[i].route_wise, data[i].on_total, data[i].on_individual, data[i].month_wise, data[i].week_wise, data[i].day_wise, data[i].UOM_val],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_self_appraisal_customer_wise = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO EXISTSself_appraisal_customer_wise(customer_code, customer_name, emp_code, month, target, achievement) VALUES ( ?, ?, ?, ?, ?, ? )`,
                    [data[i].customer_code, data[i].customer_name, data[i].emp_code, data[i].month, data[i].target, data[i].achievement],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_self_appraisal_branch_wise = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO EXISTSself_appraisal_branch_wise(branch_code, branch_name, emp_code, month, target, achievement) VALUES ( ?, ?, ?, ?, ?, ? )`,
                    [data[i].branch_code, data[i].branch_name, data[i].emp_code, data[i].month, data[i].target, data[i].achievement],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_hint_remarks_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO hint_remarks_details(trans_id, hint_remarks, flag) VALUES ( ?, ?, ? )`,
                    [data[i].trans_id, data[i].hint_remarks, data[i].flag],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_yellow_card_details = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO yellow_card_details(yellow_card_no, customer_code, challan_no, challan_date, qty, qty_UOM, flag) VALUES ( ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].yellow_card_no, data[i].customer_code, data[i].challan_no, data[i].challan_date, data[i].qty, data[i].qty_UOM, data[i].flag],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_call_duration = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO call_duration(transaction_id, customer_code, call_duration, flag) VALUES ( ?, ?, ?, ? )`,
                    [data[i].transaction_id, data[i].customer_code, data[i].call_duration, data[i].flag],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_yellow_card_date_validation = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO yellow_card_date_validation(validation_month, validation_date) VALUES ( ?, ? )`,
                    [data[i].validation_month, data[i].validation_date],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_self_appraisal_product_wise = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO self_appraisal_product_wise(prod_code, prod_desc, emp_code, month, target, achievement, prev_y_target, prev_y_achievement) VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )`,
                    [data[i].prod_code, data[i].prod_desc, data[i].emp_code, data[i].month, data[i].target, data[i].achievement, data[i].prev_y_target, data[i].prev_y_achievement],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_branch_schemes_PDF = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO branch_schemes_PDF(branch_code, PDF_file_name, acedns) VALUES ( ?, ?, ? )`,
                    [data[i].branch_code, data[i].PDF_file_name, data[i].acedns],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_customer_master = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO customer_master(customer_code, customer_name, route_code, emp_code, black_list, acedns, credit_limit, current_balance, TD, cust_type, route_name, pin, phone_no, rds_tag, flag, sauda_validity_period, check_flag, address, landline_no, owner_name, owner_phone, cust_class, weekly_closing_day, coverage_type, TIN, PAN, minimum_stock, branch_code, visit_day, email, sauda_limit, pending_qty, SAP_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
                    [data[i].customer_code, data[i].customer_name, data[i].route_code, data[i].emp_code, data[i].black_list, data[i].acedns, data[i].credit_limit, data[i].current_balance, data[i].TD, data[i].cust_type, data[i].route_name, data[i].pin, data[i].phone_no, data[i].rds_tag, data[i].flag, data[i].sauda_validity_period, data[i].check_flag, data[i].address, data[i].landline_no, data[i].owner_name, data[i].owner_phone, data[i].cust_class, data[i].weekly_closing_day, data[i].coverage_type, data[i].TIN, data[i].PAN, data[i].minimum_stock, data[i].branch_code, data[i].visit_day, data[i].email, data[i].sauda_limit, data[i].pending_qty, data[i].SAP_code],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}

export const insertDataIn_branch_dump = (data) => {
    if (!data) {
        return false
    } else {
        db.transaction(txn => {
            for (let i = 0; i < data.length; ++i)
                txn.executeSql(`INSERT INTO branch_dump(branch_code, dump_code, dump_name, acedns, is_plant, download_time) VALUES ( ?, ?, ?, ?, ?, ? )`,
                    [data[i].branch_code, data[i].dump_code, data[i].dump_name, data[i].acedns, data[i].is_plant, data[i].download_time],
                    (sqlTxn, res) => { },
                    error => { },
                )
        })
    }
}
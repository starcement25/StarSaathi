import UrlStorage from '../UrlStorage';
import { DataBaseSetup } from './DataBase'
const db = DataBaseSetup();

//android_metadata GET

//employee_master_login GET
export const getAllDataFrom_employee_master_login = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM employee_master_login`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//menu_details GET
export const getAllDataFrom_menu_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM menu_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//order_form_details GET
export const getAllDataFrom_order_form_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM order_form_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//product_details GET
export const getAllDataFrom_product_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM product_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//user_details GET
export const getAllDataFrom_user_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM user_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//route_master GET
export const getAllDataFrom_route_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM route_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//outstanding_master GET
export const getAllDataFrom_outstanding_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM outstanding_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//product_group_master GET
export const getAllDataFrom_product_group_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM product_group_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//product_sub_group_master GET
export const getAllDataFrom_product_sub_group_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM product_sub_group_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//product_brand_master GET
export const getAllDataFrom_product_brand_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM product_brand_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//product_master GET
export const getAllDataFrom_product_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM product_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//mrp GET
export const getAllDataFrom_mrp = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM mrp`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//route_plan_transaction GET
export const getAllDataFrom_route_plan_transaction = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM route_plan_transaction`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//order_header GET
export const getAllDataFrom_order_header = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM order_header`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//payment_details GET
export const getAllDataFrom_payment_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM payment_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//payment_header GET
export const getAllDataFrom_payment_header = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM payment_header`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//location GET
export const getAllDataFrom_location = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM location`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//order_details GET
export const getAllDataFrom_order_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM order_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//prospective_customer_master GET
export const getAllDataFrom_prospective_customer_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM prospective_customer_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//attendence GET
export const getAllDataFrom_attendence = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM attendence`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//transport_mode_category GET
export const getAllDataFrom_transport_mode_category = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM transport_mode_category`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//transport_mode_sub_category GET
export const getAllDataFrom_transport_mode_sub_category = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM transport_mode_sub_category`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//tour_expenses GET
export const getAllDataFrom_tour_expenses = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM tour_expenses`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//bank_master GET
export const getAllDataFrom_bank_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM bank_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//prospective_customer_header GET
export const getAllDataFrom_prospective_customer_header = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM prospective_customer_header`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//prospective_customer_details GET
export const getAllDataFrom_prospective_customer_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM prospective_customer_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//data_download_log GET
export const getAllDataFrom_data_download_log = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM data_download_log`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//prev_stock_counting_master GET
export const getAllDataFrom_prev_stock_counting_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM prev_stock_counting_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//stock_audit GET
export const getAllDataFrom_stock_audit = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM stock_audit`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//rds_master GET
export const getAllDataFrom_rds_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM rds_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//route_plan_access_period GET
export const getAllDataFrom_route_plan_access_period = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM route_plan_access_period`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//emp_master GET
export const getAllDataFrom_emp_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM emp_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//branch_master GET
export const getAllDataFrom_branch_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM branch_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//vendor_master GET
export const getAllDataFrom_vendor_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM vendor_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//closing_stock GET
export const getAllDataFrom_closing_stock = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM closing_stock`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//freight_expenses GET
export const getAllDataFrom_freight_expenses = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM freight_expenses`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//loyalty_card_holder_master GET
export const getAllDataFrom_loyalty_card_holder_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM loyalty_card_holder_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//card_transaction GET
export const getAllDataFrom_card_transaction = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM card_transaction`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//goods_in_transit GET
export const getAllDataFrom_goods_in_transit = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM goods_in_transit`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//transaction_log GET
export const getAllDataFrom_transaction_log = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM transaction_log`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//scheme_details GET
export const getAllDataFrom_scheme_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM scheme_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//loyalty_purchase_details GET
export const getAllDataFrom_loyalty_purchase_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM loyalty_purchase_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//app_info GET
export const getAllDataFrom_app_info = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM app_info`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//mis_transaction_log GET
export const getAllDataFrom_mis_transaction_log = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM mis_transaction_log`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//notes_info GET
export const getAllDataFrom_notes_info = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM notes_info`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//user_access GET
export const getAllDataFrom_user_access = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM user_access`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//route_plan_details GET
export const getAllDataFrom_route_plan_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM route_plan_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//emp_image GET
export const getAllDataFrom_emp_image = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM emp_image`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//supporting_attachment_details GET
export const getAllDataFrom_supporting_attachment_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM supporting_attachment_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//lodging_expenses GET
export const getAllDataFrom_lodging_expenses = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM lodging_expenses`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//fooding_expenses GET
export const getAllDataFrom_fooding_expenses = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM fooding_expenses`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//gcm GET
export const getAllDataFrom_gcm = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM gcm`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//notification_details GET
export const getAllDataFrom_notification_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM notification_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//redeeme_details GET
export const getAllDataFrom_redeeme_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM redeeme_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//sauda_allocation GET
export const getAllDataFrom_sauda_allocation = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM sauda_allocation`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//customer_branch_relation GET
export const getAllDataFrom_customer_branch_relation = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM customer_branch_relation`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//sauda_header GET
export const getAllDataFrom_sauda_header = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM sauda_header`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//sauda_details GET
export const getAllDataFrom_sauda_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM sauda_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//broker_master GET
export const getAllDataFrom_broker_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM broker_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//sauda_form_details GET
export const getAllDataFrom_sauda_form_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM sauda_form_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//product_promotion GET
export const getAllDataFrom_product_promotion = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM product_promotion`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//pin_code_master GET
export const getAllDataFrom_pin_code_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM pin_code_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//generic_oil_master GET
export const getAllDataFrom_generic_oil_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM generic_oil_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//market_feedback GET
export const getAllDataFrom_market_feedback = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM market_feedback`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//menu_access GET
export const getAllDataFrom_menu_access = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM menu_access`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//street_master GET
export const getAllDataFrom_street_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM street_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//sauda_allocation_log GET
export const getAllDataFrom_sauda_allocation_log = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM sauda_allocation_log`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//sauda_allocation_access GET
export const getAllDataFrom_sauda_allocation_access = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM sauda_allocation_access`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//pending_contract GET
export const getAllDataFrom_pending_contract = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM pending_contract`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//sauda_transaction_log GET
export const getAllDataFrom_sauda_transaction_log = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM sauda_transaction_log`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//prev_order_counting_master GET
export const getAllDataFrom_prev_order_counting_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM prev_order_counting_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//pending_contract_ageing GET
export const getAllDataFrom_pending_contract_ageing = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM pending_contract_ageing`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//outstanding_ageing GET
export const getAllDataFrom_outstanding_ageing = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM outstanding_ageing`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//order_status GET
export const getAllDataFrom_order_status = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM order_status`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//sauda_mrp GET
export const getAllDataFrom_sauda_mrp = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM sauda_mrp`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//sale_performance_details GET
export const getAllDataFrom_sale_performance_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM sale_performance_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//OTP_details GET
export const getAllDataFrom_OTP_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM OTP_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//destination_master GET
export const getAllDataFrom_destination_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM destination_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//get destination list
export const getDestiList = () => {
    return new Promise((resolve, reject) => {
        db.transaction(txn => {
            txn.executeSql(
                `SELECT * FROM destination_master ORDER BY destination_name ASC`,
                [],
                (sqlTnx, res) => {
                    let rows = []
                    const len = res.rows.length

                    for (let i = 0; i < len; i++) {
                        rows.push(res.rows.item(i))
                    }

                    resolve(rows)
                },
                error => {
                    reject(error)
                }
            )
        })
    })
}

// Get subdealers from local database
// export const getMySubDealerList = (userType, selectedCustomerCode) => {
//     return new Promise((resolve, reject) => {
//         let query = '';

//         if (userType.toLowerCase() === 'broker') {
//             query = `
//         SELECT address, customer_name, customer_code, phone_no, SAP_code
//         FROM customer_master
//         WHERE cust_type IN ('Sub Dealer', 'RSSD')
//           AND rds_tag = ?
//         ORDER BY customer_name COLLATE NOCASE ASC
//       `;
//         } else {
//             query = `
//         SELECT address, customer_name, customer_code, phone_no, SAP_code
//         FROM customer_master
//         WHERE cust_type != 'Dealer'
//         ORDER BY customer_name COLLATE NOCASE ASC
//       `;
//         }
//         db.transaction(txn => {
//             txn.executeSql(
//                 query,
//                 userType.toLowerCase() === 'broker' ? [selectedCustomerCode] : [],
//                 (sqlTnx, res) => {
//                     const rows = [];
//                     const len = res.rows.length;

//                     for (let i = 0; i < len; i++) {
//                         const row = res.rows.item(i);

//                         rows.push({
//                             customer_code: row.customer_code,
//                             customer_name: row.customer_name,
//                             address: row.address,
//                             phone_no: row.phone_no,
//                             SAP_code: row.SAP_code,
//                         });
//                     }

//                     // 🔒 Final safety sort (case-insensitive, trimmed)
//                     rows.sort((a, b) =>
//                         a.customer_name
//                             .trim()
//                             .toLowerCase()
//                             .localeCompare(b.customer_name.trim().toLowerCase())
//                     );

//                     resolve(rows);
//                 },
//                 error => {
//                     reject(error);
//                 }
//             );
//         });
//     });
// };


export const getMySubDealerList = (userType, selectedCustomerCode, isRssd = false) => {
    return new Promise((resolve, reject) => {

        // Run this ONCE to inspect your actual customer_master schema
        db.transaction(txn => {
            txn.executeSql(
                `PRAGMA table_info(customer_master)`,
                [],
                (sqlTnx, res) => {
                    //console.log('=== customer_master COLUMNS ===');
                    for (let i = 0; i < res.rows.length; i++) {
                        const row = res.rows.item(i);
                        console.log(`col[${i}]: "${row.name}" | type: ${row.type}`);
                    }
                },
                error => console.log('PRAGMA error:', error)
            );
        });




        let query = '';

        // console.log('=== getMySubDealerList DEBUG ===');
        // console.log('userType:', userType);
        // console.log('selectedCustomerCode:', selectedCustomerCode);
        // console.log('isRssd:', isRssd);

        // Step 1: First let's check what cust_type values actually exist in the table
        db.transaction(txn => {
            txn.executeSql(
                `SELECT DISTINCT cust_type, substr(customer_id, 1, 2) as id_prefix, COUNT(*) as cnt 
                 FROM customer_master 
                 GROUP BY cust_type, substr(customer_id, 1, 2)`,
                [],
                (sqlTnx, res) => {
                    console.log('--- All cust_type & customer_id prefixes in DB ---');
                    for (let i = 0; i < res.rows.length; i++) {
                        const row = res.rows.item(i);
                        console.log(`cust_type: "${row.cust_type}" | id_prefix: "${row.id_prefix}" | count: ${row.cnt}`);
                    }
                },
                error => console.log('Debug query error:', error)
            );
        });

        // Step 2: Check rds_tag values if broker
        if (userType.toLowerCase() === 'broker') {
            db.transaction(txn => {
                txn.executeSql(
                    `SELECT DISTINCT rds_tag, COUNT(*) as cnt FROM customer_master GROUP BY rds_tag`,
                    [],
                    (sqlTnx, res) => {
                        console.log('--- All rds_tag values in DB ---');
                        for (let i = 0; i < res.rows.length; i++) {
                            const row = res.rows.item(i);
                            console.log(`rds_tag: "${row.rds_tag}" | count: ${row.cnt}`);
                        }
                        //console.log('selectedCustomerCode being matched against rds_tag:', selectedCustomerCode);
                    },
                    error => console.log('rds_tag debug error:', error)
                );
            });
        }

        const rssdCondition = `(cust_type = 'RSSD' AND SAP_code LIKE '15%')`;

        const fullCondition = `
            (cust_type = 'Dealer' AND SAP_code LIKE '10%')
            OR (cust_type = 'RSSD' AND SAP_code LIKE '15%')
            OR (cust_type = 'Ship to Party-dealer' AND SAP_code LIKE '14%')
            OR (cust_type = 'ShiptoParty-Subdeale' AND SAP_code LIKE '14%')
        `;

        const whereCondition = isRssd ? rssdCondition : fullCondition;

        if (userType.toLowerCase() === 'broker') {
            query = `
                SELECT address, customer_name, customer_code, phone_no, SAP_code
                FROM customer_master
                WHERE (${whereCondition})
                  AND rds_tag = ?
                ORDER BY customer_name COLLATE NOCASE ASC
            `;
        } else {
            query = `
                SELECT address, customer_name, customer_code, phone_no, SAP_code
                FROM customer_master
                WHERE (${whereCondition})
                ORDER BY customer_name COLLATE NOCASE ASC
            `;
        }

        // console.log('Final query:', query);
        // console.log('Query params:', userType.toLowerCase() === 'broker' ? [selectedCustomerCode] : []);

        db.transaction(txn => {
            txn.executeSql(
                query,
                userType.toLowerCase() === 'broker' ? [selectedCustomerCode] : [],
                (sqlTnx, res) => {
                    console.log('=== RESULT ROW COUNT:', res.rows.length, '===');
                    const rows = [];
                    const len = res.rows.length;
                    for (let i = 0; i < len; i++) {
                        const row = res.rows.item(i);
                        rows.push({
                            customer_code: row.customer_code,
                            customer_name: row.customer_name,
                            address: row.address,
                            phone_no: row.phone_no,
                            SAP_code: row.SAP_code,
                        });
                    }
                    rows.sort((a, b) =>
                        a.customer_name
                            .trim()
                            .toLowerCase()
                            .localeCompare(b.customer_name.trim().toLowerCase())
                    );
                    resolve(rows);
                },
                error => {
                    console.log('=== MAIN QUERY ERROR ===', error);
                    reject(error);
                }
            );
        });
    });
};


// export const getDestiList = () => {
//   return new Promise((resolve, reject) => {
//     let destinationList = [];

//     db.transaction(txn => {
//       txn.executeSql(
//         `SELECT destination_code, destination_name, ex_for_type FROM destination_master`,
//         [],
//         (sqlTnx, res) => {
//           const len = res.rows.length;
//           for (let i = 0; i < len; i++) {
//             const row = res.rows.item(i);
//             destinationList.push({
//               destinationCode: row.destination_code,
//               destinationName: row.destination_name,
//               exForType: row.ex_for_type,
//             });
//           }
//           resolve(destinationList);
//         },
//         error => {
//           reject(error);
//         }
//       );
//     });
//   });
// };

//route_customer_plan_transaction GET
export const getAllDataFrom_route_customer_plan_transaction = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM route_customer_plan_transaction`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//check_in_out_details GET
export const getAllDataFrom_check_in_out_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM check_in_out_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//survey_output GET
export const getAllDataFrom_survey_output = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM survey_output`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//survey_output_temp GET
export const getAllDataFrom_survey_output_temp = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM survey_output_temp`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//survey_input GET
export const getAllDataFrom_survey_input = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM survey_input`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//survey_form_details GET
export const getAllDataFrom_survey_form_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM survey_form_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//survey_category_master GET
export const getAllDataFrom_survey_category_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM survey_category_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//table_view GET
export const getAllDataFrom_table_view = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM table_view`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//market_feedback_details GET
export const getAllDataFrom_market_feedback_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM market_feedback_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//competitor_group_master GET
export const getAllDataFrom_competitor_group_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM competitor_group_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//mf_stk_audit_header GET
export const getAllDataFrom_mf_stk_audit_header = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM mf_stk_audit_header`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//mf_stk_audit_details GET
export const getAllDataFrom_mf_stk_audit_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM mf_stk_audit_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//emp_menu_access GET
export const getAllDataFrom_emp_menu_access = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM emp_menu_access`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//emp_target_achievement GET
export const getAllDataFrom_emp_target_achievement = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM emp_target_achievement`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//non_trade_customer_master GET
export const getAllDataFrom_non_trade_customer_master = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM non_trade_customer_master`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//survey_header GET
export const getAllDataFrom_survey_header = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM survey_header`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//self_appraisal_details GET
export const getAllDataFrom_self_appraisal_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM self_appraisal_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//self_appraisal_customer_wise GET
export const getAllDataFrom_self_appraisal_customer_wise = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM self_appraisal_customer_wise`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//self_appraisal_branch_wise GET
export const getAllDataFrom_self_appraisal_branch_wise = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM self_appraisal_branch_wise`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//hint_remarks_details GET
export const getAllDataFrom_hint_remarks_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM hint_remarks_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//yellow_card_details GET
export const getAllDataFrom_yellow_card_details = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM yellow_card_details`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//call_duration GET
export const getAllDataFrom_call_duration = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM call_duration`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//yellow_card_date_validation GET
export const getAllDataFrom_yellow_card_date_validation = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM yellow_card_date_validation`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//self_appraisal_product_wise GET
export const getAllDataFrom_self_appraisal_product_wise = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM self_appraisal_product_wise`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}

//branch_schemes_PDF GET
export const getAllDataFrom_branch_schemes_PDF = () => {
    return new Promise((resolve, reject) => {
        db.transaction(txn => {
            txn.executeSql(
                `SELECT * FROM branch_schemes_PDF`,
                [],
                (sqlTnx, res) => {
                    let rows = [];
                    const len = res.rows.length;

                    for (let i = 0; i < len; i++) {
                        rows.push(res.rows.item(i));
                    }
                    resolve(rows); // ✅ return rows AFTER query completes
                },
                error => {
                    reject(error);
                }
            );
        });
    });
}
export const getAllDataFrom_branch_schemes_PDF_Sub_dealer = (branch_code) => {
    return new Promise((resolve, reject) => {
        db.transaction(txn => {
            txn.executeSql(
                "SELECT * FROM branch_schemes_PDF WHERE branch_code='" + branch_code + "'",
                [],
                (sqlTnx, res) => {
                    let rows = [];
                    const len = res.rows.length;

                    for (let i = 0; i < len; i++) {
                        rows.push(res.rows.item(i));
                    }
                    resolve(rows); // ✅ return rows AFTER query completes
                },
                error => {
                    reject(error);
                }
            );
        });
    });
}

//branch_dump GET
export const getAllDataFrom_branch_dump = () => {
    let rows = [];
    db.transaction(txn => {
        txn.executeSql(
            `SELECT * FROM branch_dump`,
            [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                for (let i = 0; i < len; i++) {
                    rows.push(res.rows.item(i));
                }
            },
            error => { }
        );
    });
    return rows
}
export const getDestinationsByCustomerCode = (customerCode) => {
    return new Promise((resolve, reject) => {
        if (!customerCode) {
            resolve([]);
            return;
        }

        db.transaction(txn => {
            // 🔹 Step 1: Get destination_code(s) from customer_destination
            txn.executeSql(
                `SELECT destination_code 
                 FROM customer_destination 
                 WHERE customer_code = ?`,
                [customerCode],
                (tx1, res1) => {
                    const len1 = res1.rows.length;

                    if (len1 === 0) {
                        resolve([]); // No destinations mapped
                        return;
                    }

                    // Collect destination codes
                    let destinationCodes = [];
                    for (let i = 0; i < len1; i++) {
                        destinationCodes.push(res1.rows.item(i).destination_code);
                    }

                    // 🔹 Prepare placeholders (?, ?, ?)
                    const placeholders = destinationCodes.map(() => '?').join(',');

                    // 🔹 Step 2: Fetch destination_master using those codes
                    txn.executeSql(
                        `SELECT * 
                         FROM destination_master 
                         WHERE destination_code IN (${placeholders})`,
                        destinationCodes,
                        (tx2, res2) => {
                            let rows = [];
                            const len2 = res2.rows.length;

                            for (let i = 0; i < len2; i++) {
                                rows.push(res2.rows.item(i));
                            }

                            resolve(rows); // ✅ Final array
                        },
                        error2 => {
                            reject(error2);
                        }
                    );
                },
                error1 => {
                    reject(error1);
                }
            );
        });
    });
};


//customer_master GET
export const getAllDataFrom_customer_master = () => {
    return new Promise((resolve, reject) => {
        db.transaction(txn => {
            txn.executeSql(
                `SELECT * FROM customer_master WHERE  cust_type = 'Dealer' ORDER BY customer_name ASC`,
                [],
                (sqlTnx, res) => {
                    let rows = [];
                    const len = res.rows.length;

                    for (let i = 0; i < len; i++) {
                        rows.push(res.rows.item(i));
                    }
                    resolve(rows); // ✅ return rows AFTER query completes
                },
                error => {
                    reject(error);
                }
            );
        });
    });
}
export const getAllDataFrom_customer_master1 = (type, typeBranch) => {
    console.log('hit');
    var sql = ''
    if (typeBranch == '')
        sql = `SELECT c.*, b.branch_name FROM customer_master c LEFT JOIN branch_master b ON TRIM(c.branch_code) = TRIM(b.branch_code) WHERE  c.cust_type = '${type}' ORDER BY c.customer_name ASC`
    else
        sql = `SELECT c.*, b.branch_name FROM customer_master c LEFT JOIN branch_master b ON TRIM(c.branch_code) = TRIM(b.branch_code) WHERE  c.cust_type = '${type}' AND b.branch_code = '${typeBranch}' ORDER BY c.customer_name ASC`
    return new Promise((resolve, reject) => {
        db.transaction(txn => {
            txn.executeSql(
                sql,
                [],
                (sqlTnx, res) => {
                    let rows = [];
                    const len = res.rows.length;

                    for (let i = 0; i < len; i++) {
                        rows.push(res.rows.item(i));
                    }
                    resolve(rows); // ✅ return rows AFTER query completes
                },
                error => {
                    reject(error);
                }
            );
        });
    });
}

export const getAllBranchCodeAndName = () => {
    console.log('hi');

    return new Promise((resolve, reject) => {
        db.transaction(txn => {
            txn.executeSql(
                `SELECT branch_code, branch_name FROM branch_master`,
                [],
                (sqlTxn, res) => {
                    let rows = [];
                    const len = res.rows.length;
                    console.log(len);


                    for (let i = 0; i < len; i++) {
                        rows.push(res.rows.item(i));
                    }

                    rows.forEach(r => console.log(`branch_code: "${r.branch_code}" | branch_name: "${r.branch_name}"`));

                    resolve(rows);
                },
                error => {
                    console.log('getAllBranchCodeAndName error:', error);
                    reject(error);
                }
            );
        });
    });
}


export const getAllDataFrom_customer_masterSubDealer = () => {
    return new Promise((resolve, reject) => {
        db.transaction(txn => {
            var emp_code = UrlStorage.ParameterList.BasicData.user_type != 'broker' ? UrlStorage.ParameterList.BasicData.selectedCustomerCode : UrlStorage.ParameterList.BasicData.customerDetails.customer_code
            txn.executeSql(
                "SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type IN( 'Sub Dealer','RSSD') AND rds_tag =  '" + emp_code + "' ORDER BY customer_name ASC",
                [],
                (sqlTnx, res) => {
                    let rows = [];
                    const len = res.rows.length;

                    for (let i = 0; i < len; i++) {
                        rows.push(res.rows.item(i));
                    }
                    resolve(rows); // ✅ return rows AFTER query completes
                },
                error => {
                    reject(error);
                }
            );
        });
    });
}
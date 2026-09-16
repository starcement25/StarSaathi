import UrlStorage from '../UrlStorage'
import { DataBaseSetup } from './DataBase'
import moment from 'moment'
const db = DataBaseSetup()

export const checkDataFor_last_login = () => {
    var status = false
    var todaysDate = moment(new Date()).format('YYYY-MM-DD')
    db.transaction(txn => {
        txn.executeSql(`SELECT flag FROM employee_master_login where emp_code=${UrlStorage.ParameterList.BasicData.emp_code} AND date=${todaysDate}`, [],
            (sqlTnx, res) => {
                const len = res.rows.length
                if (len > 0) {
                    const flag = results.rows.item(0).flag
                    status = flag == 1
                }
            },
            error => { }
        )
    })

    return status
}

export const checkDataFor_rssd_broker = () => {
    var status = []
    db.transaction(txn => {
        txn.executeSql("SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type == 'RSSD' AND customer_code !=  '" + UrlStorage.ParameterList.BasicData.customer_code + "' ORDER BY customer_name ASC", [],
            (sqlTnx, res) => {
                status = res.rows
            },
            error => { }
        )
    })
    return status
}

export const checkDataFor_other_broker = () => {
    var status = []
    db.transaction(txn => {
        txn.executeSql("SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type == 'RSSD' AND customer_code !=  '" + UrlStorage.ParameterList.BasicData.customer_code + "' ORDER BY customer_name ASC", [],
            (sqlTnx, res) => {
                status = res.rows
            },
            error => { }
        )
    })
    return status
}

export const checkDataFor_all_broker = () => {
    var status = []
    db.transaction(txn => {
        txn.executeSql("SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type IN( 'Sub Dealer','RSSD') AND rds_tag =  '" + UrlStorage.ParameterList.BasicData.customer_code + "' ORDER BY customer_name ASC", [],
            (sqlTnx, res) => {
                status = res.rows
            },
            error => { }
        )
    })
    return status
}

export const checkDataFor_not_dealer = () => {
    var status = []
    db.transaction(txn => {
        txn.executeSql("SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type != 'Dealer' ORDER BY customer_name ASC", [],
            (sqlTnx, res) => {
                status = res.rows
            },
            error => { }
        )
    })
    return status
}

export const checkDataFor_all_dealer = () => {
    var status = []
    db.transaction(txn => {
        txn.executeSql("SELECT address, customer_name, customer_code, phone_no, SAP_code FROM customer_master WHERE  cust_type = 'Dealer' ORDER BY customer_name ASC", [],
            (sqlTnx, res) => {
                status = res.rows
            },
            error => { }
        )
    })
    return status
}

export const checkDataFor_last_download_time = (table_name) => {
    var last_download_time = ''
    db.transaction(txn => {
        txn.executeSql(`SELECT last_download_time FROM data_download_log where table_name=${table_name}`, [],
            (sqlTnx, res) => {
                const len = res.rows.length
                if (len > 0) {
                    last_download_time = results.rows.item(0).last_download_time
                }
            },
            error => { }
        )
    })

    return last_download_time
}

export const checkDataFor_sale_access = () => {
    var sale_access = ''
    db.transaction(txn => {
        txn.executeSql(`SELECT sale_access FROM emp_master where emp_code=${UrlStorage.ParameterList.BasicData.emp_code}`, [],
            (sqlTnx, res) => {
                const len = res.rows.length
                if (len > 0) {
                    sale_access = results.rows.item(0).sale_access
                }
            },
            error => { }
        )
    })

    return sale_access
}

export const checkDataFor_app_version = () => {
    var app_version = ''
    db.transaction(txn => {
        txn.executeSql(`SELECT * FROM app_info`, [],
            (sqlTnx, res) => {
                const len = res.rows.length
                if (len > 0) {
                    app_version = results.rows.item(0).app_version
                }
            },
            error => { }
        )
    })

    return app_version
}

export const checkDataFor_all_month_wish_target = () => {
    var monthWishTargetList = []
    var mData = "04, 05, 06, 07, 08, 09, 10, 11, 12, 01, 02, 03".split(',')
    for (var i = 0; i < mData.length; i++) {
        var obj = { 'month': mData[i], 'target': 0, 'achievement': 0 }
        db.transaction(txn => {
            txn.executeSql("SELECT SUM(target), SUM(achievement) FROM self_appraisal_product_wise WHERE month=" + "'" + mData[i].trim() + "' AND emp_code = '" + UrlStorage.ParameterList.BasicData.customer_code + "'", [],
                (sqlTnx, res) => {
                    const len = res.rows.length
                    if (len > 0) {
                        obj.target = results.rows.item(0).target
                        obj.achievement = results.rows.item(0).achievement
                    }
                },
                error => { }
            )
        })
        monthWishTargetList.push(obj)
    }
    return monthWishTargetList
}

export const checkDataFor_all_month_wish_target_1 = () => {
    var monthWishTargetList = []
    var mData = "04, 05, 06, 07, 08, 09, 10, 11, 12, 01, 02, 03".split(',')
    for (var i = 0; i < mData.length; i++) {
        var obj = { 'month': mData[i], 'target': 0, 'achievement': 0 }
        db.transaction(txn => {
            txn.executeSql("SELECT SUM(prev_y_target), SUM(prev_y_achievement) FROM self_appraisal_product_wise WHERE month=" + "'" + mData[i].trim() + "' AND emp_code = '" + UrlStorage.ParameterList.BasicData.customer_code + "'", [],
                (sqlTnx, res) => {
                    const len = res.rows.length
                    if (len > 0) {
                        obj.target = results.rows.item(0).target
                        obj.achievement = results.rows.item(0).achievement
                    }
                },
                error => { }
            )
        })
        monthWishTargetList.push(obj)
    }
    return monthWishTargetList
}

export const checkDataFor_sum_of_target = () => {
    var data = ''
    db.transaction(txn => {
        txn.executeSql("SELECT SUM(target) FROM self_appraisal_product_wise WHERE emp_code = '" + UrlStorage.ParameterList.BasicData.customer_code + "'", [],
            (sqlTnx, res) => {
                const len = res.rows.length
                if (len > 0) {
                    data = results.rows.item(0)
                }
            },
            error => { }
        )
    })

    return data
}

export const checkDataFor_sum_of_achievement = () => {
    var data = ''
    db.transaction(txn => {
        txn.executeSql("SELECT SUM(achievement) FROM self_appraisal_product_wise WHERE emp_code = '" + UrlStorage.ParameterList.BasicData.customer_code + "'", [],
            (sqlTnx, res) => {
                const len = res.rows.length
                if (len > 0) {
                    data = results.rows.item(0)
                }
            },
            error => { }
        )
    })

    return data
}

export const checkDataFor_sum_of_prev_y_target = () => {
    var data = ''
    db.transaction(txn => {
        txn.executeSql("SELECT SUM(prev_y_target) FROM self_appraisal_product_wise WHERE emp_code = '" + UrlStorage.ParameterList.BasicData.customer_code + "'", [],
            (sqlTnx, res) => {
                const len = res.rows.length
                if (len > 0) {
                    data = results.rows.item(0)
                }
            },
            error => { }
        )
    })

    return data
}

export const checkDataFor_sum_of_prev_y_achievement = () => {
    var data = ''
    db.transaction(txn => {
        txn.executeSql("SELECT SUM(prev_y_achievement) FROM self_appraisal_product_wise WHERE emp_code = '" + UrlStorage.ParameterList.BasicData.customer_code + "'", [],
            (sqlTnx, res) => {
                const len = res.rows.length
                if (len > 0) {
                    data = results.rows.item(0)
                }
            },
            error => { }
        )
    })

    return data
}
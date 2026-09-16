import DataStorage from '../DataStorage'
import UrlStorage from '../UrlStorage'
import { checkDataFor_app_version, checkDataFor_sale_access } from './CheckDataInTable'
import { DataBaseSetup } from './DataBase'
import moment from 'moment'
const db = DataBaseSetup()

export const updateDataFor_login_user = () => {
    var date = moment(new Date()).format('YYYY-MM-DD')
    var sale_access = checkDataFor_sale_access()
    var app_version = checkDataFor_app_version()
    db.transaction(txn => {
        for (let i = 0; i < data.length; ++i)
            txn.executeSql(
                `INSERT INTO employee_master_login(emp_code, date, emp_name, device_id, password , sale_access, flag, app_version, updation_flag) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
                [UrlStorage.ParameterList.BasicData.emp_code, date, DataStorage.emp_name, DataStorage.device_id, DataStorage.password, sale_access, 1, app_version, date],
                (sqlTxn, res) => { },
                error => { },
            )
    })
}

export const updateDataFor_logout_user = () => {
    var date = moment(new Date()).format('YYYY-MM-DD')
    db.transaction(txn => {
        for (let i = 0; i < data.length; ++i)
            txn.executeSql(
                `UPDATE employee_master_login SET flag = ? WHERE emp_code = ? AND date = ?`,
                [0, UrlStorage.ParameterList.BasicData.emp_code, date],
                (sqlTxn, res) => { },
                error => { },
            )
    })
}

export const updateDataFor_data_download_log = (table_name) => {
    var date = moment(new Date()).format('YYYY-MM-DD hh:mm:ss')
    db.transaction(txn => {
        for (let i = 0; i < data.length; ++i)
            txn.executeSql(
                `UPDATE data_download_log SET last_download_time = ? WHERE table_name = ?`,
                [date, table_name],
                (sqlTxn, res) => { },
                error => { },
            )
    })
}
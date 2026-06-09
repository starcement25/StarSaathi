const SQLite = require('react-native-sqlite-storage')

const errorCB = (err) => { }
const openCB = () => { }

export const DataBaseSetup = () => {
    var db = SQLite.openDatabase("SBS.db", "1.0", "Database", 200000, openCB, errorCB);
    return db
}

export const createTable = (query) => {
    return new Promise((resolve, reject) => {
        const db = DataBaseSetup();
        db.transaction(tx => {
            tx.executeSql(
                query.replace(';', ''), [],
                () => { resolve(); },
                error => { resolve(); }
            );
        });
    });
}
export const getAllTableName = () => {
    const db = DataBaseSetup();
    db.transaction(txn => {
        txn.executeSql(
            "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%';", [],
            (sqlTnx, res) => {
                const len = res.rows.length;
                if (len > 0) {
                    let tables = [];
                    for (let i = 0; i < len; i++) {
                        tables.push(res.rows.item(i).name);
                    }
                }
            },
            error => { }
        );
    });
}
export const getDataFromTable = (dataSet, tableName, query) => {
    let rows = [];
    const db = DataBaseSetup();
    db.transaction(txn => {
        txn.executeSql(
            `SELECT ${dataSet} FROM ${tableName} WHERE ${query}`, [],
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
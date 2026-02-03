package org.forcepower.starcement;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.os.Environment;

import static org.forcepower.starcement.util.PreferenceData.get_direcory_path;
import static org.forcepower.starcement.util.Utils.print_log_d;

import org.forcepower.starcement.bean.SelfAppraisalDetailsCustomerWise;

import java.io.BufferedReader;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;

public final class StarCementDB extends SQLiteOpenHelper
{
    private static final int DB_VERSION = 1;
    private static final String DB_NAME = "m3StarCement.db";
    private static final String TABLE_LEDER = "ledger";
    private static final String TABLE_LEDER_BALANCE = "ledger_balance";
    private static final String T_APPERPDO = "T_APPERPDO";
    private static final String T_DOCHALLAN = "T_DOCHALLAN";
    private static final String T_LIFTING_ALLOCATING = "allocation";

    public StarCementDB(final Context context)
    {
        super(context,  get_direcory_path(context) + DB_NAME, null, DB_VERSION);
    }

    @Override
    public void onCreate(SQLiteDatabase db) {
        try
        {
            //need to add dns_customer_code
            final String CREATE_LEDGER = "CREATE TABLE IF NOT EXISTS ledger ( customer_code TEXT, voucher_date TEXT, voucher_no TEXT, quantity TEXT, amount_dr TEXT, amount_cr TEXT, narration TEXT, entry_date TEXT)";
            db.execSQL(CREATE_LEDGER);

            final String CREATE_LEDGER_BAL = "CREATE TABLE IF NOT EXISTS ledger_balance ( customer_code TEXT, balance TEXT, date TEXT, link TEXT)";
            db.execSQL(CREATE_LEDGER_BAL);


            final String T_APPERPDO = "CREATE TABLE IF NOT EXISTS T_APPERPDO ( apporderno text, erporderno text, erporderdt text, order_for text, customer_code text, dns_customer_code text, status text, prod_code text, dns_prod_code text, prod_display_name text, qty text, order_full_date_time text, freight text, destination_address text, is_confirmed_material_received text )";
            db.execSQL(T_APPERPDO);


            final String T_DOCHALLAN = "CREATE TABLE IF NOT EXISTS T_DOCHALLAN ( apporderno text, erporderno text, erporderdt text, challanno text, challandt text, prod_code text, qty text, challanqty text, truckno text, driverno text, customer_code text, dns_customer_code text, prod_display_name text, challan_material_received text, ch_uid text, transporter_name text, ch_quantity_no_of_bags text, ch_status text, colour_code text)";
            db.execSQL(T_DOCHALLAN);

            db.execSQL("CREATE TABLE IF NOT EXISTS " + T_LIFTING_ALLOCATING + " (order_id TEXT, dns_prod_code TEXT, qty TEXT, challanno TEXT PRIMARY KEY, dispatch_date TEXT, dispatch_qty TEXT, allocation_qty TEXT, dispatch_date_milli TEXT, prod_display_name TEXT, available_allocation_qty TEXT)");
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    @Override
    public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion)
    {
        try
        {
            db.execSQL("DROP TABLE IF EXISTS ledger");
            db.execSQL("DROP TABLE IF EXISTS ledger_balance");
            db.execSQL("DROP TABLE IF EXISTS T_APPERPDO");
            db.execSQL("DROP TABLE IF EXISTS T_DOCHALLAN");
            db.execSQL("DROP TABLE IF EXISTS " +T_LIFTING_ALLOCATING);
            onCreate(db);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public void insertLifting(final String dns_prod_code, final String qty, final String challanno,
                              final String dispatch_date, final String dispatch_qty,
                              final String allocation_qty, final String dispatch_date_milli,
                              final String order_id, final String prod_display_name,
                              final String available_allocation_qty)
    {
        try
        {
            SQLiteDatabase db = this.getWritableDatabase();
            ContentValues values = new ContentValues();

            values.put("order_id", order_id);
            values.put("dns_prod_code", dns_prod_code);
            values.put("qty", qty);
            values.put("challanno", challanno);
            values.put("dispatch_date", dispatch_date);
            values.put("dispatch_qty", dispatch_qty);
            values.put("allocation_qty", allocation_qty);
            values.put("dispatch_date_milli", dispatch_date_milli);
            values.put("prod_display_name", prod_display_name);
            values.put("available_allocation_qty", available_allocation_qty);

            long l = db.insertWithOnConflict(T_LIFTING_ALLOCATING, "challanno", values, SQLiteDatabase.CONFLICT_REPLACE);
            print_log_d("insert_95 ", l + " ");
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    public void update_available_allocation_qty(final String dns_prod_code, final float input_qty)
    {
        try
        {
            float remaining_qty = get_available_allocation_qty(dns_prod_code) - input_qty;
            SQLiteDatabase db = this.getWritableDatabase();
            ContentValues cv = new ContentValues();
            cv.put("available_allocation_qty", remaining_qty);
            int returnVal=db.update(T_LIFTING_ALLOCATING,cv,"dns_prod_code=?", new String[] { dns_prod_code });
            print_log_d("returnVal",returnVal+"");
            db.close();
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    public float getDispatchQty(final String dns_prod_code)
    {
        float value = 0;
        try
        {
            // Select All Query
            String selectQuery = "SELECT SUM(dispatch_qty) FROM " + T_LIFTING_ALLOCATING + " WHERE dns_prod_code = '" +dns_prod_code+"'";

            SQLiteDatabase db = this.getWritableDatabase();
            Cursor cursor = db.rawQuery(selectQuery, null);

            // looping through all rows and adding to list
            if (cursor.moveToFirst())
            {
                value = cursor.getFloat(0); //return value
            }
            cursor.close();
            db.close(); // Closing database connection
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }

//        if(value == null)
//            value = "0";
        return value;
    }

    public String getDispatchDate(final String dns_prod_code)
    {
        String value = "";
        try
        {
            // Select All Query
            String selectQuery = "SELECT dispatch_date FROM " + T_LIFTING_ALLOCATING + " WHERE dns_prod_code = '" +dns_prod_code+"'";

            SQLiteDatabase db = this.getWritableDatabase();
            Cursor cursor = db.rawQuery(selectQuery, null);

            // looping through all rows and adding to list
            if (cursor.moveToFirst())
            {
                value = cursor.getString(0); //return value
            }
            cursor.close();
            db.close(); // Closing database connection
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }

//        if(value == null)
//            value = "0";
        return value;
    }

    public float get_available_allocation_qty(final String dns_prod_code)
    {
        float parent_value = 0;
        try
        {
            // Select All Query
            String selectQuery = "SELECT SUM(available_allocation_qty) FROM " + T_LIFTING_ALLOCATING + " WHERE dns_prod_code = '" +dns_prod_code+"'";

            SQLiteDatabase db = this.getWritableDatabase();
            Cursor cursor = db.rawQuery(selectQuery, null);

            // looping through all rows and adding to list
            if (cursor.moveToFirst())
            {
                parent_value = cursor.getFloat(0); //return value
            }
            cursor.close();
            db.close(); // Closing database connection
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }


//        if(value == null)
//            value = "0";
        return parent_value;
    }

    public float getQty(final String dns_prod_code)
    {
        float value = 0;
        try
        {
            // Select All Query
            String selectQuery = "SELECT SUM(qty) FROM " + T_LIFTING_ALLOCATING + " WHERE dns_prod_code = '" +dns_prod_code+"'";

            SQLiteDatabase db = this.getWritableDatabase();
            Cursor cursor = db.rawQuery(selectQuery, null);

            // looping through all rows and adding to list
            if (cursor.moveToFirst())
            {
                value = cursor.getFloat(0); //return value
            }
            cursor.close();
            db.close(); // Closing database connection
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }

//        if(value == null)
//            value = "0";
        return value;
    }
    public String get_prod_display_name(final String dns_prod_code)
    {
        String value = "";
        try
        {
            // Select All Query
            String selectQuery = "SELECT prod_display_name FROM " + T_LIFTING_ALLOCATING + " WHERE dns_prod_code = '" +dns_prod_code+"'";

            SQLiteDatabase db = this.getWritableDatabase();
            Cursor cursor = db.rawQuery(selectQuery, null);

            // looping through all rows and adding to list
            if (cursor.moveToFirst())
            {
                value = cursor.getString(0); //return value
            }
            cursor.close();
            db.close(); // Closing database connection
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }

        if(value == null)
            value = dns_prod_code;
        return value;
    }
    public String get_root_order_id(final String dns_prod_code)
    {
        String value = "";
        try
        {
            // Select All Query
            String selectQuery = "SELECT order_id FROM " + T_LIFTING_ALLOCATING + " WHERE dns_prod_code = '" +dns_prod_code+"'";

            SQLiteDatabase db = this.getWritableDatabase();
            Cursor cursor = db.rawQuery(selectQuery, null);

            // looping through all rows and adding to list
            if (cursor.moveToFirst())
            {
                value = cursor.getString(0); //return value
            }
            cursor.close();
            db.close(); // Closing database connection
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }

        if(value == null)
            value = "";
        return value;
    }
    public int get_is_allocation_data()
    {
        int value = 0;
        try
        {
            // Select All Query
            String selectQuery = "SELECT allocation_qty FROM " + T_LIFTING_ALLOCATING;

            SQLiteDatabase db = this.getWritableDatabase();
            Cursor cursor = db.rawQuery(selectQuery, null);

            // looping through all rows and adding to list
            if (cursor.moveToFirst())
            {
                value = cursor.getInt(0); //return value
            }
            cursor.close();
            db.close(); // Closing database connection
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }

        // return dataList
        return value;
    }
    public void insertLeder(String customer_code, String voucher_date,
                            String voucher_no, String quantity,
                            String amount_dr, String amount_cr,
                            String narration, String entry_date)
    {
        try
        {
            SQLiteDatabase db = this.getWritableDatabase();
            ContentValues values = new ContentValues();

            values.put("customer_code", customer_code+"");
            values.put("voucher_date", voucher_date+"");
            values.put("voucher_no", voucher_no+"");
            values.put("quantity", quantity+"");
            values.put("amount_dr", amount_dr+"");
            values.put("amount_cr", amount_cr+"");
            values.put("narration", narration+"");
            values.put("entry_date", entry_date+"");

            db.insert(TABLE_LEDER, null, values);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    public void insertLederBal(String customer_code, String balance, String date, String link)
    {
        try
        {
            SQLiteDatabase db = this.getWritableDatabase();
            ContentValues values = new ContentValues();

            values.put("customer_code", customer_code+"");
            values.put("balance", balance+"");
            values.put("date", date+"");
            values.put("link", link+"");

            db.insert(TABLE_LEDER_BALANCE, null, values);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    // Getting key wise data
    public String getLedgerBalance(String key)
    {

        String value = "";
        try
        {
            // Select All Query
            String selectQuery = "SELECT  "+key+" FROM " + TABLE_LEDER_BALANCE;

            SQLiteDatabase db = this.getWritableDatabase();
            Cursor cursor = db.rawQuery(selectQuery, null);

            // looping through all rows and adding to list
            if (cursor.moveToFirst())
            {
                value = cursor.getString(0); //return value
            }
            cursor.close();
            db.close(); // Closing database connection
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }

        // return dataList
        return value;
    }

    public void insertOrderData(String apporderno, String erporderno, String customer_code,
                                String dns_customer_code, String erporderdt,
                                String order_for, String status, String prod_code, String dns_prod_code,
                                String prod_display_name, String qty, String order_full_date_time,
                                String freight, String destination_address, String is_confirmed_material_received
    )
    {
        try
        {
            SQLiteDatabase db = this.getWritableDatabase();
            ContentValues values = new ContentValues();

            values.put("apporderno", apporderno+"");
            values.put("erporderno", erporderno+"");
            values.put("customer_code", customer_code+"");
            values.put("dns_customer_code", dns_customer_code+"");
            values.put("erporderdt", erporderdt+"");

//            values.put("order_challan_data", order_challan_data+"");
            values.put("order_for", order_for +"");
            values.put("status", status +"");
            values.put("prod_code", prod_code +"");
            values.put("dns_prod_code", dns_prod_code +"");
            values.put("prod_display_name", prod_display_name +"");
            values.put("qty", qty +"");
            values.put("order_full_date_time", order_full_date_time +"");
            values.put("freight", freight +"");
            values.put("destination_address", destination_address +"");
            values.put("is_confirmed_material_received", is_confirmed_material_received +"");

            db.insert(T_APPERPDO, null, values);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    // Getting All getAllLedgerDetails
    public List<commonDatabaseHelper> getAllLedgerDetails()
    {
        //  used as a common class
        List<commonDatabaseHelper> dataList = new ArrayList<>();
        // Select All Query
        String selectQuery = "SELECT  * FROM " + TABLE_LEDER +" ORDER BY datetime(voucher_date) DESC LIMIT 50";

        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);

        // looping through all rows and adding to list
        if (cursor.moveToFirst())
        {
            do
            {
                commonDatabaseHelper data = new commonDatabaseHelper();

                data.setItem0(cursor.getString(0)); // Dealer id
                data.setItem1(cursor.getString(1)); // Voucher Date
                data.setItem2(cursor.getString(2)); // Voucher No
                data.setItem3(cursor.getString(3)); //Quantity
                data.setItem4(cursor.getString(4)); // Amount dr
                data.setItem5(cursor.getString(5)); // amount cr
                data.setItem6(cursor.getString(6)); // Balance

                // Adding contact to list
                dataList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        db.close(); // Closing database connection

        // return dataList
        return dataList;
    }

    public void insertChallan(String apporderno, String challandt, String challanno,
                              String challanqty, String driverno, String erporderdt, String erporderno,
                              String prod_code, String qty, String truckno, String prod_display_name,
                              final String challan_material_received, final String ch_uid,
                              final String transporter_name, final String ch_quantity_no_of_bags,
                              final String ch_status, final String colour_code)
    {
        try
        {
            SQLiteDatabase db = this.getWritableDatabase();
            ContentValues values = new ContentValues();

            values.put("apporderno", apporderno+"");
            values.put("challandt", challandt+"");
            values.put("challanno", challanno+"");
            values.put("challanqty", challanqty+"");
            values.put("driverno", driverno+"");
            values.put("erporderdt", erporderdt+"");
            values.put("erporderno", erporderno+"");
            values.put("prod_code", prod_code+"");
            values.put("qty", qty+"");
            values.put("truckno", truckno+"");
            values.put("prod_display_name", prod_display_name+"");
            values.put("challan_material_received", challan_material_received+"");
            values.put("ch_uid", ch_uid+"");
            values.put("transporter_name", transporter_name+"");
            values.put("ch_quantity_no_of_bags", ch_quantity_no_of_bags+"");
            values.put("ch_status", ch_status+"");
            values.put("colour_code", colour_code+"");


            db.insert(T_DOCHALLAN, null, values);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    // Getting All getAllLedgerDetails
    public List<commonDatabaseHelper> getAllAppOrderDetails()
    {
        //  used as a common class
        List<commonDatabaseHelper> dataList = new ArrayList<>();
        // Select All Query
        String selectQuery = "SELECT apporderno, status, qty, prod_display_name, order_full_date_time, freight, destination_address, is_confirmed_material_received, erporderno FROM " + T_APPERPDO +" WHERE apporderno != '' ORDER BY datetime(erporderdt) DESC";

        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);

        // looping through all rows and adding to list
        if (cursor.moveToFirst())
        {
            do
            {
//                if(!cursor.getString(0).trim().matches("") && !cursor.getString(0).trim().isEmpty())
//                {
                    commonDatabaseHelper data = new commonDatabaseHelper();

                    data.setItem0(cursor.getString(0)); // apporderno
                    data.setItem1(cursor.getString(1)); // status


                    data.setItem2(cursor.getString(2)); // qty
                    data.setItem3(cursor.getString(3)); //prod_display_name
                    data.setItem4(cursor.getString(4)); //order_full_date_time

                    data.setItem5(cursor.getString(5)); // f
                    data.setItem6(cursor.getString(6)); // destination_address
                    data.setItem7(cursor.getString(7)); // is_confirmed_material_received
                    data.setItem8(cursor.getString(8)); // erporderno

                    // Adding contact to list
                    dataList.add(data);
//                }

            } while (cursor.moveToNext());
        }
        cursor.close();

        db.close(); // Closing database connection

        // return dataList
        return dataList;
    }

    public List<commonDatabaseHelper> getSubCategoryAppOrder(String item0)
    {
        List<commonDatabaseHelper> dataList = new ArrayList<>();

        try
        {
            //  used as a common class
            String selectQuery = "SELECT challanno, challandt, challanqty, truckno, driverno, challan_material_received, ch_uid, apporderno, erporderno, transporter_name, ch_quantity_no_of_bags, ch_status, colour_code, qty FROM " + T_DOCHALLAN  + " WHERE apporderno = '" +item0+"'";

            SQLiteDatabase db = this.getWritableDatabase();
            Cursor cursor2 = db.rawQuery(selectQuery, null);
            // looping through all rows and adding to list
            if (cursor2.moveToFirst())
            {
                do
                {
                    if(!cursor2.getString(0).matches("") &&
                            !cursor2.getString(0).equalsIgnoreCase("null"))
                    {
                        commonDatabaseHelper e = new commonDatabaseHelper();
                        e.setItem0(cursor2.getString(0)); //challanno
                        e.setItem1(cursor2.getString(1)); //erporderdt
                        e.setItem2(cursor2.getString(2)); //challanqty

                        e.setItem3(cursor2.getString(3)); //truckno
                        e.setItem4(cursor2.getString(4)); //driverno
                        e.setItem5(cursor2.getString(5)); //challan_material_received
                        e.setItem6(cursor2.getString(6)); //ch_uid
                        e.setItem7(cursor2.getString(7)); //apporderno
                        e.setItem8(cursor2.getString(8)); //erporderno
                        e.setItem9(cursor2.getString(9)); //transporter_name
                        e.setItem10(cursor2.getString(10)); //ch_quantity_no_of_bags
                        e.setItem11(cursor2.getString(11)); //ch_status
                        e.setItem12(cursor2.getString(12)); //colour_code
                        e.setItem13(cursor2.getString(13)); //Qty
                        dataList.add(e);
                    }


                }while (cursor2.moveToNext());

            }


            // Adding contact to list
            cursor2.close();
            db.close(); // Closing database connection

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return dataList;
    }

    // Getting key wise data
    public String deleteTable(String TABLE_NAME)
    {

        String returnValue = "";
        try
        {
            // Select All Query
            SQLiteDatabase db = this.getWritableDatabase();
            db.execSQL("delete from "+ TABLE_NAME);
            db.close(); // Closing database connection
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }

        // return dataList
        return returnValue;
    }
    public String getAllChallanNumber(final String dns_prod_code)
    {
        String dataList = "";
        try
        {
            // Select All Query
            String selectQuery = "SELECT challanno FROM " + T_LIFTING_ALLOCATING + " WHERE dns_prod_code = '" +dns_prod_code+"' AND available_allocation_qty > 0";

            SQLiteDatabase db = this.getWritableDatabase();
            Cursor cursor = db.rawQuery(selectQuery, null);

            int i=0;
            // looping through all rows and adding to list
            if (cursor.moveToFirst())
            {
                do
                {
                    if(i ==0 )
                    {
                        dataList = cursor.getString(0);
                        i++;
                    }
                    else
                    {
                        dataList = dataList + ","+cursor.getString(0);
                    }
                } while (cursor.moveToNext());
            }
            cursor.close();
            db.close(); // Closing database connection

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        // return dataList
        return dataList;
    }
}

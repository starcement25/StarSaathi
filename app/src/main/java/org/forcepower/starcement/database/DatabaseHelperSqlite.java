package org.forcepower.starcement.database;

import static org.forcepower.starcement.util.PreferenceData.get_direcory_path;
import static org.forcepower.starcement.util.Utils.print_log_d;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.MatrixCursor;
import android.database.SQLException;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;

import org.forcepower.starcement.commonDatabaseHelper;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by Amit on 29/04/2017.
 */
public final class DatabaseHelperSqlite extends SQLiteOpenHelper
{
    // All Static variables
    // Database Version
    private static final int DATABASE_VERSION = 2;

    // Database Name
    private static final String DATABASE_NAME = "Notifications.db";

    // Contacts table name
    private static final String TABLE_FIREBASE_TOKEN = "firebase_token";
    private static final String TABLE_NOTIFICATIONS = "firebase_notifications";

    // Contacts Table Columns names
    private static final String KEY_ID = "nid";
    private static final String KEY_UPDATE_AVAILABLE = "is_update_available";
    private static final String KEY_NOTI_TITLE = "m_title";
    private static final String KEY_NOTI_MESSAGE = "m_message";
    private static final String KEY_NOTI_IMAGE_LINK = "m_image_link";
    private static final String KEY_NOTI_DATE_TIME = "n_date_time";
    private static final String KEY_NOTI_FILE_TYPE = "m_file_type";

    private static final String KEY_NOTI_TITLE_STATUS = "m_status";

    public DatabaseHelperSqlite(Context context) {
        super(context, get_direcory_path(context) + DATABASE_NAME, null, DATABASE_VERSION);
    }

    // Creating Tables
    @Override
    public void onCreate(SQLiteDatabase db) {
        String CREATE_CONTACTS_TABLE = "CREATE TABLE IF NOT EXISTS " + TABLE_FIREBASE_TOKEN + "("
                + KEY_ID + " TEXT," + KEY_UPDATE_AVAILABLE + " TEXT)";

        db.execSQL(CREATE_CONTACTS_TABLE);

        String CREATE_TABLE_NOTIFICATIONS = "CREATE TABLE IF NOT EXISTS " + TABLE_NOTIFICATIONS + "("
                + KEY_ID + " TEXT PRIMARY KEY,"
                + KEY_NOTI_TITLE + " TEXT, "
                + KEY_NOTI_MESSAGE + " TEXT, "
                + KEY_NOTI_IMAGE_LINK + " TEXT, "
                + KEY_NOTI_DATE_TIME + " TEXT, "
                +  KEY_NOTI_TITLE_STATUS + " TEXT default 'unread', "
                +  KEY_NOTI_FILE_TYPE + " TEXT )";

        db.execSQL(CREATE_TABLE_NOTIFICATIONS);
    }

    public ArrayList<Cursor> getData(String Query){
        //get writable database
        SQLiteDatabase sqlDB = this.getWritableDatabase();
        String[] columns = new String[] { "message" };
        //an array list of cursor to save two cursors one has results from the query
        //other cursor stores error message if any errors are triggered
        ArrayList<Cursor> alc = new ArrayList<Cursor>(2);
        MatrixCursor Cursor2= new MatrixCursor(columns);
        alc.add(null);
        alc.add(null);

        try{
            String maxQuery = Query ;
            //execute the query results will be save in Cursor c
            Cursor c = sqlDB.rawQuery(maxQuery, null);

            //add value to cursor2
            Cursor2.addRow(new Object[] { "Success" });

            alc.set(1,Cursor2);
            if (null != c && c.getCount() > 0) {

                alc.set(0,c);
                c.moveToFirst();

                return alc ;
            }
            return alc;
        } catch(SQLException sqlEx){
            print_log_d("printing exception", sqlEx.getMessage());
            //if any exceptions are triggered save the error message to cursor an return the arraylist
            Cursor2.addRow(new Object[] { ""+sqlEx.getMessage() });
            alc.set(1,Cursor2);
            return alc;
        } catch(Exception ex){
            print_log_d("printing exception", ex.getMessage());

            //if any exceptions are triggered save the error message to cursor an return the arraylist
            Cursor2.addRow(new Object[] { ""+ex.getMessage() });
            alc.set(1,Cursor2);
            return alc;
        }
    }

    // Upgrading database
    @Override
    public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
        // Drop older table if existed
        db.execSQL("DROP TABLE IF EXISTS " + TABLE_FIREBASE_TOKEN);
        db.execSQL("DROP TABLE IF EXISTS " + TABLE_NOTIFICATIONS);
        // Create tables again
        onCreate(db);
    }

    // Adding new contact
    public void addRegistrationIdAndStatus(String id) {
        try
        {
            SQLiteDatabase db = this.getWritableDatabase();
            ContentValues values = new ContentValues();
            values.put(KEY_ID, id);
            values.put(KEY_UPDATE_AVAILABLE, "yes");
            db.delete(TABLE_FIREBASE_TOKEN, null, new String[] {});
            // Inserting Row
            long status=db.insert(TABLE_FIREBASE_TOKEN, null, values);
            print_log_d("status",status+"");
            db.close(); // Closing database connection
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    // Adding new contact
    public void addNotifications(String id, String title, String msg, String image_link,
                                 String date_time, String m_file_type, String the_noti_sts) {
        SQLiteDatabase db = this.getWritableDatabase();
        ContentValues values = new ContentValues();
        values.put(KEY_ID, id);
        values.put(KEY_NOTI_TITLE, title);
        values.put(KEY_NOTI_MESSAGE, msg);
        values.put(KEY_NOTI_IMAGE_LINK, image_link);
        values.put(KEY_NOTI_DATE_TIME, date_time);
        values.put(KEY_NOTI_FILE_TYPE, m_file_type);
        values.put(KEY_NOTI_TITLE_STATUS, the_noti_sts);

        // Inserting Row
        long status=db.insert(TABLE_NOTIFICATIONS, null, values);
        //			db.insertWithOnConflict("directory", "memberid", cv, SQLiteDatabase.CONFLICT_REPLACE);
        print_log_d("status",status+"");
        db.close(); // Closing database connection
    }

    public void updateRegistrationIdAndStatus(String id,String isUpdateAvailable) {
        try
        {
            SQLiteDatabase db = this.getWritableDatabase();

//            String updateQuery = "UPDATE "+ TABLE_FIREBASE_TOKEN +" SET " + KEY_ID+"='"+id+"' AND "+KEY_UPDATE_AVAILABLE+"='"+isUpdateAvailable+"'";
            ContentValues cv = new ContentValues();
            cv.put(KEY_ID,id); //These Fields should be your String values of actual column names
            cv.put(KEY_UPDATE_AVAILABLE,isUpdateAvailable);
            int returnVal=db.update(TABLE_FIREBASE_TOKEN,cv,null,null);
            print_log_d("returnVal",returnVal+"");
//            db.execSQL(updateQuery);
//            Cursor cursor = db.rawQuery(updateQuery, null);
//            cursor.moveToFirst();
//            cursor.close();
            db.close();
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }


    }

    public void updateNotiStatus(String id,String status_) {
        try
        {
            SQLiteDatabase db = this.getWritableDatabase();
            ContentValues cv = new ContentValues();
            cv.put(KEY_NOTI_TITLE_STATUS, status_);
            int returnVal=db.update(TABLE_NOTIFICATIONS,cv,KEY_ID + "=?", new String[] { id });
            print_log_d("returnVal",returnVal+"");
            db.close();
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }


    }

    public Boolean isUpdateAvailable(String registrationid) {
        Boolean isUpdateAvailable=false;
        try
        {
            // Select All Query
            String selectQuery = "SELECT "+KEY_ID+" FROM " + TABLE_FIREBASE_TOKEN +" LIMIT 1";

            SQLiteDatabase db = this.getWritableDatabase();
            Cursor cursor = db.rawQuery(selectQuery, null);

            // looping through all rows and adding to list
            if (cursor.moveToFirst())
            {
                if(cursor.getString(0).matches(registrationid))
                {
                    isUpdateAvailable=true;
                }
                cursor.close();
                db.close();
            }
        }
        catch (Exception e)
        {
            isUpdateAvailable=false;
        }

        // return contact list
        return isUpdateAvailable;
    }

    public String getRegistrationId() {
        String RegistrationId="";
        try
        {
            // Select All Query
            String selectQuery = "SELECT "+KEY_ID+" FROM " + TABLE_FIREBASE_TOKEN +" LIMIT 1";

            SQLiteDatabase db = this.getWritableDatabase();
            Cursor cursor = db.rawQuery(selectQuery, null);

            // looping through all rows and adding to list
            if (cursor.moveToFirst())
            {
                RegistrationId=cursor.getString(0);

            }
            cursor.close();
            db.close();

        }
        catch (Exception e)
        {
            RegistrationId="";
        }

        // return contact list
        return RegistrationId;
    }

    // Getting All getAllLedgerDetails
    public List<commonDatabaseHelper> getAllNotiList(String status) {
        //  used as a common class
        List<commonDatabaseHelper> dataList = new ArrayList<>();
        // Select All Query
        String selectQuery = "SELECT  * FROM " + TABLE_NOTIFICATIONS + " ORDER BY cast(nid as int) DESC";

        if(!status.matches(""))
        {
            selectQuery = "SELECT  * FROM " + TABLE_NOTIFICATIONS + " WHERE " + KEY_NOTI_TITLE_STATUS + " = '" + status + "' COLLATE NOCASE ORDER BY cast(nid as int) DESC";
        }
        SQLiteDatabase db = this.getWritableDatabase();
        Cursor cursor = db.rawQuery(selectQuery, null);

        // looping through all rows and adding to list
        if (cursor.moveToFirst())
        {
            do
            {
                commonDatabaseHelper data = new commonDatabaseHelper();

                data.setItem0(cursor.getString(0)); // nid
                data.setItem1(cursor.getString(1)); // m_title
                data.setItem2(cursor.getString(2)); // m_message
                data.setItem3(cursor.getString(3)); // m_image_link
                data.setItem4(cursor.getString(4)); // n_date_time
                data.setItem5(cursor.getString(5)); // status
                data.setItem6(cursor.getString(6)); // m_file_type

                // Adding contact to list
                dataList.add(data);
            } while (cursor.moveToNext());
        }
        cursor.close();
        db.close(); // Closing database connection

        // return dataList
        return dataList;
    }

    // Getting key wise data
    public String deleteTable(String TABLE_NAME) {

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
}

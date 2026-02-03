package org.forcepower.starcement;

import android.app.ProgressDialog;
import android.content.Context;
import android.util.Log;
import android.view.Gravity;
import android.widget.Toast;

import org.forcepower.starcement.util.Utils;

import java.io.File;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Locale;
import java.util.Objects;
import java.util.regex.Pattern;

/**
 * Created by Amit on 23/10/2017.
 */

public final class Utils_
{
    public static ProgressDialog loaderDialog;
    static boolean canShowToast = true;

    public static void closeApp(Context context,String msg) {
        if(!msg.matches(""))
        {
            showToast(context,msg);
        }
        Utils.cancelProgressDialog();
    }


    public static void showProgressDialog(Context mContext, String msg) {
        if (loaderDialog == null) {
            loaderDialog = new ProgressDialog(mContext);
            loaderDialog.setCancelable(false);
            loaderDialog.setMessage(msg);
            loaderDialog.show();
        }
    }

    public static void cancelProgressDialog() {
        if (loaderDialog != null && loaderDialog.isShowing()) {
            loaderDialog.dismiss();
            loaderDialog = null;
        }
    }

    public static void changeProgressDialogMsg(Context mContext, String msg) {
        if (loaderDialog != null) {
            loaderDialog.setMessage(msg);
        } else {
            showProgressDialog(mContext, msg);
        }
    }

    public static void showToast(Context mContext, String msg) {
        if (canShowToast) {
            Toast toast = Toast.makeText(mContext, msg, Toast.LENGTH_LONG);
            toast.setGravity(Gravity.CENTER, 0, 0);
            toast.show();
        }
    }

    public static final Pattern EMAIL_ADDRESS_PATTERN = Pattern.compile(
            "^(([\\w-]+\\.)+[\\w-]+|([a-zA-Z]{1}|[\\w-]{2,}))@"
                    +"((([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\\.([0-1]?"
                    +"[0-9]{1,2}|25[0-5]|2[0-4][0-9])\\."
                    +"([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\\.([0-1]?"
                    +"[0-9]{1,2}|25[0-5]|2[0-4][0-9])){1}|"
                    +"([a-zA-Z]+[\\w-]+\\.)+[a-zA-Z]{2,4})$"
    );

    public static boolean validEmailFormat(String UserEmailNo) {
        return EMAIL_ADDRESS_PATTERN.matcher(UserEmailNo).matches();
    }

    public static void print_Log_d(String key, String value) {
        try
        {
            if(BuildConfig.DEBUG)
                Log.d(key ,  value);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public static void print_Log_d(String key_value) {
        try
        {
            if(BuildConfig.DEBUG)
                Log.d(" ", key_value+" ");
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public static String ApiRes(final String str) {
        try
        {

            print_Log_d("dkerur_ ", str);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return str;
    }

    public static void deleteRecursive(File fileOrDirectory) {
        try
        {
            if (fileOrDirectory.isDirectory())
                for (File child : Objects.requireNonNull(fileOrDirectory.listFiles()))
                    deleteRecursive(child);

            fileOrDirectory.delete();
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public static String changeDateFormat_(final String inputDateString,
                                           final String inputDateFormat,
                                           final String outputDateFormat) {
        final SimpleDateFormat inputFormat = new SimpleDateFormat(inputDateFormat);
        final SimpleDateFormat outputFormat = new SimpleDateFormat(outputDateFormat);

        Date date = null;
        String outputDateString = null;

        try
        {
            date = inputFormat.parse(inputDateString);
            outputDateString = outputFormat.format(date);
        }
        catch (ParseException e)
        {
            e.printStackTrace();
        }
        return outputDateString;
    }

    public static long dateToMilliSecond(final String mDate) {
        long millis = 0;
        try
        {
            final SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault());
            final Date date = sdf.parse(mDate);
            millis = date.getTime();
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return millis;
    }
}

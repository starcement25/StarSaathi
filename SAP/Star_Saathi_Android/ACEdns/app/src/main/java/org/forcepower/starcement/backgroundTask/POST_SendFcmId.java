package org.forcepower.starcement.backgroundTask;

import static org.forcepower.starcement.SharedPrefData.get_firebase_token;
import static org.forcepower.starcement.SharedPrefData.set_branch_wise_pg_rollout;
import static org.forcepower.starcement.Utils_.print_Log_d;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import android.widget.Toast;

import cz.msebera.android.httpclient.NameValuePair;
import org.forcepower.starcement.BuildConfig;
import org.forcepower.starcement.R;
import org.forcepower.starcement.constants.AceDnsWebServiceURL;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.database.DatabaseHelperSqlite;
import org.forcepower.starcement.util.HTTPUtils;
import org.json.JSONObject;

import java.io.File;
import java.util.ArrayList;

/**
 * Created by Amit on 29/04/2017.
 */

public final class POST_SendFcmId extends AsyncTaskCoroutine<String, String>
{
    Activity mContext;
    JSONObject jo1 = new JSONObject();
    Boolean redirectionFlag;
    String mEmployeeCode;
    ArrayList<NameValuePair> nameValuePairsFCM;
    public POST_SendFcmId(Activity context, ArrayList<NameValuePair> nameValuePairsFCM, Boolean redirectionFlag, String mEmployeeCode)
    {
        mContext = context;
        this.nameValuePairsFCM=nameValuePairsFCM;
        this.redirectionFlag=redirectionFlag;
        this.mEmployeeCode=mEmployeeCode;

    }
    @Override
    public void onPreExecute()
    {
        super.onPreExecute();
//        Utils.showProgressDialog(mContext, "Registering for broadcast...");
    }

    @Override
    public String doInBackground(final String... params)
    {
        try
        {
            String response= HTTPUtils.getDataByHTTP_POST(mContext,Constants.baseURL+AceDnsWebServiceURL.updateFireBaseRegistrationIdUrl, nameValuePairsFCM);
            print_Log_d("fmc74_a ", Constants.baseURL+AceDnsWebServiceURL.updateFireBaseRegistrationIdUrl);
            print_Log_d("fmc74_p ", nameValuePairsFCM.toString());
            print_Log_d("fmc74_r ", response);
            jo1 = new JSONObject(response);
            set_branch_wise_pg_rollout(mContext, jo1.optString("branch_wise_pg_rollout"));
            if(jo1.optString("process_status").equalsIgnoreCase("YES"))
            {
                DatabaseHelperSqlite helper=new DatabaseHelperSqlite(mContext);
                helper.addRegistrationIdAndStatus(get_firebase_token(mContext));
            }

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return null;
    }

    @Override
    public void onPostExecute(String result)
    {
        super.onPostExecute(result);
//        Utils.cancelProgressDialog();

        try
        {
            if(jo1.optString("force_logout").equalsIgnoreCase("YES"))
            {
                if (HTTPUtils.isConnectionPossible(mContext))
                {
//                    if(!BuildConfig.DEBUG)
//                    {
                        new TRANS_GetLogOut_Asynctask(mContext).execute("");
//                    }
                }
            }

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    private void logOutDialog(String msg)
    {
        final AlertDialog.Builder AlertDG = new AlertDialog.Builder(mContext, R.style.MyDialog);
        AlertDG.setTitle("Log out");
        AlertDG.setMessage(msg + "");
        AlertDG.setPositiveButton("OK", new DialogInterface.OnClickListener() {

            public void onClick(DialogInterface dialog, int which) {
                AlertDG.create().dismiss();
                if (HTTPUtils.isConnectionPossible(mContext))
                {
                    new TRANS_GetLogOut_Asynctask(mContext).execute("");
                }
            }
        });
        AlertDG.setCancelable(false);
        AlertDG.create().show();
    }
}

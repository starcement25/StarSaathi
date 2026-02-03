package org.forcepower.starcement.backgroundTask;

import static org.forcepower.starcement.SharedPrefData.get_login_mobile_number;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.game_authorization;
import static org.forcepower.starcement.util.Utils.getAlphaNumericString;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.showCommonAlertDialog;

import android.app.Activity;
import android.content.Intent;

import org.forcepower.starcement.aaa.CommonWebViewActivity;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.json.JSONObject;

import java.util.ArrayList;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;

public final class TRANS_GetEngagement_Asynctask extends AsyncTaskCoroutine<String, String>
{
    private Activity mContext;
    private String launchURL = "";
    public TRANS_GetEngagement_Asynctask(final Activity mContext) {
        this.mContext = mContext;
    }

    @Override
    public void onPreExecute()
    {
        super.onPreExecute();
        Utils.showProgressDialog(mContext, "Please wait..");
    }
    @Override
    public String doInBackground(final String... params)
    {
        String POST_result = "";
        try
        {
            if (HTTPUtils.isConnectionPossible(mContext))
            {
                try
                {
                    String url = game_authorization;
                    print_log_d("PRINT_game_authorization_", url);
                    final ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(2);

                    nameValuePairs.add(new BasicNameValuePair("mobileNumber", get_login_mobile_number(mContext)));
                    ;
                    final String generatedString = getAlphaNumericString(16);

                    print_Log_d("game_authorization_generatedString_ ", "" + generatedString);
                    print_Log_d("game_authorization_length ", "" + generatedString.length());

                    nameValuePairs.add(new BasicNameValuePair("sessionToken", generatedString));

                    POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);


                    final JSONObject jo = new JSONObject(POST_result);
                    print_log_d("nff5_url ", game_authorization);
                    print_log_d("nff5_res ", POST_result);
                    print_log_d("nff5_par ", nameValuePairs.toString());

                    final String data = jo.optString("data");
                    final JSONObject jsonObject = new JSONObject(data);
                    launchURL = jsonObject.optString("launchURL");
                    print_log_d("PRINT_game_authorization_launchURL ", launchURL);


                }
                catch (Exception e)
                {
                    POST_result = "Network Failure";
                }
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        return POST_result;
    }
    @Override
    public void onPostExecute(final String result)
    {
        super.onPostExecute(result);

        try
        {
            if(!launchURL.isEmpty())
            {
                Intent intent = new Intent(mContext, CommonWebViewActivity.class);
                intent.putExtra("webview_caption", "ENGAGEMENTS");
                intent.putExtra("webview_url", launchURL);
                mContext.startActivity(intent);
            }
            else
            {
                showCommonAlertDialog(mContext, "ENGAGEMENTS", "Coming soon!");
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        finally
        {
            Utils.cancelProgressDialog();
        }
    }
}
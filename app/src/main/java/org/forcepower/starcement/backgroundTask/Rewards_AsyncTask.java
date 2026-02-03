package org.forcepower.starcement.backgroundTask;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.dealer_wise_rewards;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.showCommonAlertDialog;

import android.app.Activity;
import android.content.Intent;

import org.forcepower.starcement.aaa.CommonWebViewActivity;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;

public final class Rewards_AsyncTask extends AsyncTaskCoroutine<String, String>
{
    private Activity mContext;
    private JSONObject jo = new JSONObject();
    private String reward_link = "";

    public Rewards_AsyncTask(final Activity mContext) {
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
                    final String url = dealer_wise_rewards;
                    print_log_d("PRINT_dealer_wise_rewards_", url);
                    final ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(1);
                    nameValuePairs.add(new BasicNameValuePair("user_type", get_user_type(mContext)));

                    if(get_user_type(mContext).equalsIgnoreCase("broker"))
                    {
                        nameValuePairs.add(new BasicNameValuePair("emp_code", get_selected_dealer_sap_code(mContext)));
                    }
                    else
                    {
                        nameValuePairs.add(new BasicNameValuePair("emp_code", get_dealer_id(mContext)));
                    }


                    POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);

                    print_log_d("df5e_url ", dealer_wise_rewards);
                    print_log_d("df5e_par ", nameValuePairs.toString());
                    print_log_d("df5e_res ", POST_result);
                    jo = new JSONObject(POST_result);

                    final String reward_data = jo.optString("reward_data");
                    final JSONArray jsonArray = new JSONArray(reward_data);
                    reward_link = jsonArray.getJSONObject(0).optString("reward_link");
//                    reward_link = "https://dealerrewards.myvtd.site/sap_code=1000000341/";
                    print_Log_d("reward_link_68 ", reward_link);
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
            if(!reward_link.isEmpty())
            {
                Intent intent = new Intent(mContext, CommonWebViewActivity.class);
                intent.putExtra("webview_caption", "Rewards");
                intent.putExtra("webview_url", reward_link);
                mContext.startActivity(intent);
            }
            else
            {
                showCommonAlertDialog(mContext, "Rewards", "You have zero Reward points");
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

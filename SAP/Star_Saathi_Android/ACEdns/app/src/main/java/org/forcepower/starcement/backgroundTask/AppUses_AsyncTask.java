package org.forcepower.starcement.backgroundTask;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.save_app_usage_details;
import static org.forcepower.starcement.util.Utils.print_log_d;

import android.app.Activity;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;

import java.util.ArrayList;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;

public final class AppUses_AsyncTask extends AsyncTaskCoroutine<String, String>
{
    private Activity mContext;
    private String webservice_name;
    public AppUses_AsyncTask(final Activity mContext, final String webservice_name)
    {
        this.mContext = mContext;
        this.webservice_name = webservice_name.replaceAll("\n", " ");
    }

    @Override
    public void onPreExecute()
    {
        super.onPreExecute();
//        Utils.showProgressDialog(mContext, "Please wait..");
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
                    String url = save_app_usage_details;
                    print_log_d("app_used_oer6r8_ ", url);
                    final ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(1);
//                    nameValuePairs.add(new BasicNameValuePair("user_type", get_user_type(mContext)));
                    nameValuePairs.add(new BasicNameValuePair("webservice_name", webservice_name.toUpperCase()));
                    nameValuePairs.add(new BasicNameValuePair("customer_id", get_dealer_id(mContext)));

                    POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);

                    print_log_d("app_used_oer6r8__url ", save_app_usage_details);
                    print_log_d("app_used_oer6r8__par ", nameValuePairs.toString());
                    print_log_d("app_used_oer6r8__res ", POST_result);
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

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        finally
        {
//            Utils.cancelProgressDialog();
        }
    }
}

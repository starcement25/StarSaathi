package org.forcepower.starcement.backgroundTask;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import android.app.Activity;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.json.JSONObject;

import java.util.ArrayList;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;


public final class Sample_AsyncTask extends AsyncTaskCoroutine<String, String>
{

	private Activity mContext;
	private JSONObject jo = new JSONObject();
	public Sample_AsyncTask(final Activity context)
	{
		this.mContext = context;
	}

	@Override
	public void onPreExecute()
	{
		super.onPreExecute();
		Utils.changeProgressDialogMsg(mContext, "Please wait..");
	}

	@Override
	public String doInBackground(String... par)
	{
		String POST_result = "";
		try
		{
			final String url = "";
			print_log_d("PRINT_", url);
			final ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(1);
			if(get_user_type(mContext).equalsIgnoreCase("broker"))
			{
				nameValuePairs.add(new BasicNameValuePair("customer_id", get_selected_dealer_sap_code(mContext)));
			}
			else
			{
				nameValuePairs.add(new BasicNameValuePair("customer_id", get_dealer_id(mContext)));
			}

			nameValuePairs.add(new BasicNameValuePair("epod_data", ""));


			POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);
			jo = new JSONObject(POST_result);
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
		try
		{
			if(jo.optString("process_status").equalsIgnoreCase("No"))
			{
				show_msg_alert(mContext, jo.optString("process_message"), false);
			}
			else
			{
				show_msg_alert(mContext, jo.optString("process_message"), true);
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

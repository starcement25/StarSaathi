package org.forcepower.starcement.aaa;

import android.annotation.SuppressLint;
import android.app.ProgressDialog;
import android.content.Context;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import android.os.Bundle;
import android.os.Handler;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;
import org.forcepower.starcement.R;
import org.forcepower.starcement.util.HTTPUtils;
import org.json.JSONObject;

import java.util.ArrayList;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.acedns_star_ledger_balance_details_by_id;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.employeeDetailObject;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.print_log_d;

public final class CreditLimitActivity extends AceDnsParentActivity implements SwipeRefreshLayout.OnRefreshListener
{
	private Context mContext;
	private TextView tv_credit_bal, tv_pending_orders,tv_credit_limit;
	private ArrayList<NameValuePair> mHttpParamPairs = new ArrayList<>();
	private SwipeRefreshLayout chartListSwipeRefreshLayout;

	@Override
	protected void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_credit_limit);
		try
		{
			mContext = this;
			if(get_user_type(mContext).equalsIgnoreCase("broker"))
			{
				selected_customr_code = get_selected_customer_code(mContext);
			}
			else
			{
				selected_customr_code = get_emp_or_customer_code(mContext);
			}
			TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
			tvHeaderText.setText("Credit Limit");
			ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
			ivHeaderBack.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					finish();
				}
			});

			tv_credit_limit = (TextView) findViewById(R.id.tv_credit_limit);
			tv_pending_orders = (TextView) findViewById(R.id.tv_pending_orders);
			tv_credit_bal = (TextView) findViewById(R.id.tv_credit_bal);

			chartListSwipeRefreshLayout = (SwipeRefreshLayout) findViewById(R.id.chartListSwipeRefreshLayout);
			chartListSwipeRefreshLayout.setOnRefreshListener(this);
			chartListSwipeRefreshLayout.setColorSchemeResources(R.color.red, R.color.white, R.color.red, R.color.white);
			if (HTTPUtils.isConnectionPossible(mContext))
			{
				new getCreditLimit_Asynctask(mContext).execute();
			}
			else
			{
				Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
			}
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}
	@Override
	public void onRefresh()
	{
		try
		{
			chartListSwipeRefreshLayout.setRefreshing(false);
			Handler mHandler = new Handler();
			mHandler.postDelayed(new Runnable()
			{
				public void run()
				{
					if (HTTPUtils.isConnectionPossible(mContext))
					{
						new getCreditLimit_Asynctask(mContext).execute();
					}
					else
					{
						Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
					}
				}
			}, 10);
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}
	
	public final class getCreditLimit_Asynctask extends AsyncTaskCoroutine<String, String>
	{
		Context mContext;
		ProgressDialog mStepProgressDialog;
		JSONObject jo = new JSONObject();
		public getCreditLimit_Asynctask(Context mContext) {
			this.mContext = mContext;
		}

		@Override
		public void onPreExecute()
		{
			super.onPreExecute();
			mStepProgressDialog = new ProgressDialog(mContext);
			mStepProgressDialog.setMessage("Please wait..");
			mStepProgressDialog.setCancelable(false);
			mStepProgressDialog.show();
			tv_credit_limit.setText("00.00");
			tv_credit_bal.setText("00.00");
			tv_pending_orders.setText("0");
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
						final String url = acedns_star_ledger_balance_details_by_id;
						print_log_d("acedns_star_ledger_balance_details_by_id_125 ", url);
						mHttpParamPairs = new ArrayList<>(2);
						mHttpParamPairs.add(new BasicNameValuePair("the_id", selected_customr_code));

						POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, mHttpParamPairs);
						print_log_d("acedns_star_ledger_balance_details_by_id_140-params ", mHttpParamPairs.toString());
						print_log_d("acedns_star_ledger_balance_details_by_id_150 ", POST_result);
						jo = new JSONObject(POST_result);
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
		public void onPostExecute(String result)
		{
			super.onPostExecute(result);

			try
			{
				if(result.equalsIgnoreCase("Network Failure"))
				{
					Toast.makeText(mContext, "Try again...", Toast.LENGTH_SHORT).show();
				}
				else
				{
					if(jo.has("process_status") && jo.getString("process_status").equalsIgnoreCase("yes"))
					{
						if(!jo.optString("credit_limit").isEmpty())
							tv_credit_limit.setText(jo.optString("credit_limit"));
						if(!jo.optString("credit_balance").isEmpty())
							tv_credit_bal.setText(jo.optString("credit_balance"));
						if(!jo.optString("pending_orders").isEmpty())
							tv_pending_orders.setText(jo.optString("pending_orders"));
					}
				}

			}
			catch (Exception e)
			{
				e.printStackTrace();
			}
			finally
			{
				mStepProgressDialog.dismiss();
			}
		}
	}
}

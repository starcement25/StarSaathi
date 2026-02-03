package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.acedns_pay_ledger_amount_v2;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.twoDigitRoundOff;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import org.forcepower.starcement.R;
import org.forcepower.starcement.Utils_;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.HTTPUtils;
import org.json.JSONObject;

import java.util.ArrayList;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;

public final class PaymentSummaryActivity extends AceDnsParentActivity
{
	private Activity mContext;
	private String payment_environment = "netbanking", coming_from = "";
	private double processing_val = 00.00, gst_val = 00.00, total_payment_val = 00.0;
	private TextView tv_amount_val, tv_process_val, tv_gst_val, tv_total_val;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_pay_amount_summary);
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

			if(getIntent().hasExtra("coming_from"))
				coming_from = getIntent().getStringExtra("coming_from");

			TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
			tvHeaderText.setText("Payment Summary");
			ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
			ivHeaderBack.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					finish();
				}
			});
			//
			tv_amount_val = (TextView) findViewById(R.id.tv_amount_val);
			tv_process_val = (TextView) findViewById(R.id.tv_process_val);
			tv_gst_val = (TextView) findViewById(R.id.tv_gst_val);
			tv_total_val = (TextView) findViewById(R.id.tv_total_val);

			final double payment_amount = getIntent().getDoubleExtra("payment_amount", 00.00);
			payment_environment = getIntent().getStringExtra("payment_environment");

			tv_amount_val.setText(twoDigitRoundOff(payment_amount+""));

			if(payment_environment.matches("creditcard"))
			{
				processing_val=  (payment_amount * (0.95/100));
			}
			else if(payment_environment.matches("netbanking"))
			{
				processing_val = 10;
			}
			else if(payment_environment.matches("upi"))
			{
				processing_val = 0;
			}
			else if(payment_environment.matches("debitcard"))
			{
				if(payment_amount <= 2000)
				{
					processing_val = 0;
				}
				else
				{
					processing_val =  (payment_amount * (0.85/100));
				}
			}
			//
			tv_process_val.setText(twoDigitRoundOff(processing_val +""));
			gst_val =  (processing_val * (0.18));
			tv_gst_val.setText(twoDigitRoundOff(gst_val+""));


			total_payment_val = payment_amount + processing_val + gst_val;
			tv_total_val.setText(twoDigitRoundOff(total_payment_val+""));
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	public void payment_proceed(View view) {
		if (HTTPUtils.isConnectionPossible(mContext))
		{
			new TRANS_GetOrderId_Asynctask(mContext, total_payment_val+"", payment_environment).execute();
		}
		else
		{
			Utils_.closeApp(mContext,check_internet_connection);
		}
	}
	
	public final class TRANS_GetOrderId_Asynctask extends AsyncTaskCoroutine<String, String> {
		Context mContext;
		String amount = "", payment_environment = "";
		ProgressDialog mStepProgressDialog;
		ArrayList<NameValuePair> nameValuePairs = new ArrayList<>();
		JSONObject jo = new JSONObject();
		public TRANS_GetOrderId_Asynctask(final Context mContext, final String amount, final String payment_environment) {
			this.mContext = mContext;
			this.amount = amount;
			this.payment_environment = payment_environment;
		}

		@Override
		public void onPreExecute() {
			super.onPreExecute();
			mStepProgressDialog = new ProgressDialog(mContext);
			mStepProgressDialog.setMessage("Please wait..");
			mStepProgressDialog.setCancelable(false);
			mStepProgressDialog.show();
		}
		@Override
		public String doInBackground(final String... params) {
			String POST_result = "";
			try
			{
				if (HTTPUtils.isConnectionPossible(mContext))
				{
					try
					{
						nameValuePairs = new ArrayList<>(2);
						jo = new JSONObject();
						String url = acedns_pay_ledger_amount_v2;

						nameValuePairs.add(new BasicNameValuePair("customer_code", selected_customr_code));
						nameValuePairs.add(new BasicNameValuePair("the_amount", amount));
						nameValuePairs.add(new BasicNameValuePair("payment_by", payment_environment));


						print_log_d("acedns_pay_ledger_amount_v2_url ", url);
						print_log_d("acedns_pay_ledger_amount_v2_nameValuePairs ", nameValuePairs.toString());
						POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);
						print_log_d("acedns_pay_ledger_amount_v2_POST_result ", POST_result);
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
		public void onPostExecute(String result) {
			super.onPostExecute(result);

			try
			{
				if(jo.has("process_status") && jo.getString("process_status").equalsIgnoreCase("yes"))
				{
					if(!jo.optString("the_payment_link").isEmpty())
					{
						if (HTTPUtils.isConnectionPossible(mContext))
						{
							Intent intent = new Intent(mContext, PaymentWebViewActivity.class);
							intent.putExtra("webview_caption", "PAYMENT");
							intent.putExtra("webview_url", jo.optString("the_payment_link"));
							intent.putExtra("coming_from", coming_from);
							startActivity(intent);
						}
						else
						{
							Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
						}
					}
					else
					{
						Toast.makeText(mContext, jo.optString("process_message"), Toast.LENGTH_SHORT).show();
					}

				}
				else
				{
					Toast.makeText(mContext, jo.optString("process_message"), Toast.LENGTH_SHORT).show();
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

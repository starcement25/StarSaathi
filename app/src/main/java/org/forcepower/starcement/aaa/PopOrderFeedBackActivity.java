package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_branch_wise_pg_rollout;
import static org.forcepower.starcement.SharedPrefData.get_dns_emp_code;
import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.customerCode_;
import static org.forcepower.starcement.constants.Constants.save_pop_order_date_v1;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.isValidIndianMobile;
import static org.forcepower.starcement.util.Utils.isValidIndianPin;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.show_msg_Dialog;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.text.Html;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.CheckBox;
import android.widget.CompoundButton;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AlertDialog;

import org.forcepower.starcement.BuildConfig;
import org.forcepower.starcement.R;
import org.forcepower.starcement.bean.PopProductModel;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.HTTPUtils;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Map;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;

public final class PopOrderFeedBackActivity extends AceDnsParentActivity
{
	private Activity mContext;
	private EditText et_pop_address, et_pop_pin, et_pop_feedback,
			et_pop_address_pincode, et_pop_contact_no;
	private CheckBox cb_pop_tc;
	private String payment_environment = "", payment_option = "";

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_pop_order_feedback);
		try
		{
			mContext = this;
			if(get_user_type(mContext).equalsIgnoreCase("broker"))
			{
				customerCode_ = get_selected_customer_code(mContext);
			}
			else
			{
				customerCode_ = get_emp_or_customer_code(mContext);
			}

			if(get_user_type(mContext).equalsIgnoreCase("broker"))
			{
				selected_customr_code = get_selected_customer_code(mContext);
			}
			else
			{
				selected_customr_code = get_emp_or_customer_code(mContext);
			}
			if(getIntent().hasExtra("payment_option"))
			{
				payment_option = getIntent().getStringExtra("payment_option");
			}

			if(getIntent().hasExtra("payment_environment"))
				payment_environment = getIntent().getStringExtra("payment_environment");


			TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
			tvHeaderText.setText("Pop Order");
			ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
			ivHeaderBack.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					finish();
				}
			});

			et_pop_address_pincode = (EditText) findViewById(R.id.et_pop_address_pincode);
			et_pop_contact_no = (EditText) findViewById(R.id.et_pop_contact_no);
			CheckBox cb_same_as = (CheckBox) findViewById(R.id.cb_same_as);

			et_pop_address = (EditText) findViewById(R.id.et_pop_address);
			et_pop_pin = (EditText) findViewById(R.id.et_pop_pin);
			et_pop_feedback = (EditText) findViewById(R.id.et_pop_feedback);
			cb_pop_tc = (CheckBox) findViewById(R.id.cb_pop_tc);

			if(BuildConfig.DEBUG)
			{
				et_pop_address_pincode.setText("Address 743252");
				et_pop_contact_no.setText("9233743252");

				et_pop_address.setText("Dummmy Address");
				et_pop_pin.setText("743252");
				et_pop_feedback.setText("Dummmy feedback");
			}
			cb_same_as.setOnCheckedChangeListener(new CompoundButton.OnCheckedChangeListener() {
				@Override
				public void onCheckedChanged(CompoundButton compoundButton, boolean b) {
					if(b)
					{
						et_pop_address.setText(et_pop_address_pincode.getText().toString().trim());
					}
					else
					{
						et_pop_address.setText("");
					}
				}
			});
			final TextView tv_pop_tc = (TextView) findViewById(R.id.tv_pop_tc);
			tv_pop_tc.setText(Html.fromHtml("<u>Accept Terms &amp; Conditions</u>"));
			tv_pop_tc.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View view) {
					Intent intent = new Intent(mContext, DetailsActivity.class);
					intent.putExtra("details_text", getResources().getString(R.string.terms_conditions)+"");
					intent.putExtra("header_text", "Terms and Conditions");
					startActivity(intent);
				}
			});
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	public void order_submit(View view) {
		try
		{
			if(et_pop_address_pincode.getText().toString().trim().isEmpty())
			{
				Toast.makeText(mContext, "Please enter address and pin code", Toast.LENGTH_SHORT).show();
			}
			else if((!et_pop_contact_no.getText().toString().trim().isEmpty() && !isValidIndianMobile(et_pop_contact_no.getText().toString().trim())))
			{
				Toast.makeText(mContext, "Please enter valid 10 digit contact number", Toast.LENGTH_SHORT).show();
			}
			else if(et_pop_address.getText().toString().trim().isEmpty())
			{
				Toast.makeText(mContext, "Please enter delivery address", Toast.LENGTH_SHORT).show();
			}
			else if(!isValidIndianPin(et_pop_pin.getText().toString().trim()))
			{
				Toast.makeText(mContext, "Please enter 6 digit valid Pin code", Toast.LENGTH_SHORT).show();
			}
			else if(et_pop_feedback.getText().toString().trim().isEmpty())
			{
				Toast.makeText(mContext, "Please enter feedback", Toast.LENGTH_SHORT).show();
			}
			else if(!cb_pop_tc.isChecked())
			{
				Toast.makeText(mContext, "Please accept Terms And Conditions", Toast.LENGTH_SHORT).show();
			}
			else
			{
				if (HTTPUtils.isConnectionPossible(mContext))
				{
					new Submit_popOrder_Asynctask(mContext).execute();
				}
				else
				{
					Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
				}
			}
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}
	
	public final class Submit_popOrder_Asynctask extends AsyncTaskCoroutine<String, String> {
		private Activity mContext;
		private ProgressDialog mStepProgressDialog;
		private JSONObject jo = new JSONObject();
		private String the_payment_url = "";
		public Submit_popOrder_Asynctask(final Activity mContext)
		{
			this.mContext = mContext;
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
						final String url = save_pop_order_date_v1;

						final ArrayList<NameValuePair> mHttpParamPairs = new ArrayList<>(2);
						mHttpParamPairs.add(new BasicNameValuePair("customer_code", selected_customr_code));
						mHttpParamPairs.add(new BasicNameValuePair("user_type", get_user_type(mContext)));
						mHttpParamPairs.add(new BasicNameValuePair("dns_customer_code", get_dns_emp_code(mContext)));
						mHttpParamPairs.add(new BasicNameValuePair("address", et_pop_address.getText().toString()));
						mHttpParamPairs.add(new BasicNameValuePair("pin", et_pop_pin.getText().toString().trim()));
						mHttpParamPairs.add(new BasicNameValuePair("remarks", et_pop_feedback.getText().toString().trim()));
						mHttpParamPairs.add(new BasicNameValuePair("printed_address_pin", et_pop_address_pincode.getText().toString()));
						mHttpParamPairs.add(new BasicNameValuePair("contact_num_printed", et_pop_contact_no.getText().toString()));
						mHttpParamPairs.add(new BasicNameValuePair("payment_by", payment_environment));

						//customer_code, user_type, dns_customer_code,address,pin,remarks,printed_address_pin,contact_num_printed, payment_by

						//dns_prod_code , prod_desc , qty , prod_image, customer_code, dns_customer_code, printed_address_pin, contact_num_printed, address, pin, remarks..

						int count = 0;
						for (final Map.Entry<String, PopProductModel> entry : Constants.selectedList.entrySet())
						{
							final String data  = entry.getKey();

							mHttpParamPairs.add(new BasicNameValuePair("order_data["+ count +"][dns_prod_code]", Constants.selectedList.get(data).getDns_prod_code()));
							mHttpParamPairs.add(new BasicNameValuePair("order_data["+ count +"][prod_desc]", Constants.selectedList.get(data).getProd_desc()));
							mHttpParamPairs.add(new BasicNameValuePair("order_data["+ count +"][qty]", Constants.selectedList.get(data).get_qty()));

							mHttpParamPairs.add(new BasicNameValuePair("order_data["+ count +"][prod_image]", Constants.selectedList.get(data).getProd_image()));

							mHttpParamPairs.add(new BasicNameValuePair("order_data["+ count +"][customer_code]", customerCode_));
							mHttpParamPairs.add(new BasicNameValuePair("order_data["+ count +"][dns_customer_code]", get_dns_emp_code(mContext)));

							mHttpParamPairs.add(new BasicNameValuePair("order_data["+ count +"][printed_address_pin]", et_pop_address_pincode.getText().toString()));
							mHttpParamPairs.add(new BasicNameValuePair("order_data["+ count +"][contact_num_printed]", et_pop_contact_no.getText().toString()));

							mHttpParamPairs.add(new BasicNameValuePair("order_data["+ count +"][address]", et_pop_address.getText().toString()));
							mHttpParamPairs.add(new BasicNameValuePair("order_data["+ count +"][pin]", et_pop_pin.getText().toString().trim()));
							mHttpParamPairs.add(new BasicNameValuePair("order_data["+ count +"][remarks]", et_pop_feedback.getText().toString().trim()));

							count++;
						}
//
						POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, mHttpParamPairs);

						print_log_d("save_pop_order_date_v1_URL ", url);
						print_log_d("pop_order_date_v1_PARAMS ", mHttpParamPairs.toString());
						print_log_d("pop_order_date_v1_POST ", POST_result+"");

						jo = new JSONObject(POST_result);
						the_payment_url = jo.optString("the_payment_url");
						print_log_d("the_payment_url ", the_payment_url+"");
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
				if(jo.optString("process_status").equalsIgnoreCase("yes"))
				{
					if(payment_option.equalsIgnoreCase("Paid"))
					{
						if (HTTPUtils.isConnectionPossible(mContext))
						{
							Intent intent = new Intent(mContext, PopPaymentWebViewActivity.class);
							intent.putExtra("webview_caption", "PAYMENT");
							intent.putExtra("webview_url", the_payment_url);
							intent.putExtra("coming_from", "pop_order");
							startActivity(intent);
						}
						else
						{
							Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
						}
					}
					else if(payment_option.equalsIgnoreCase("Free"))
					{
						new AlertDialog.Builder(mContext, R.style.MyDialog)
								.setMessage(jo.optString("process_message"))
								.setCancelable(false)
								.setPositiveButton("Ok", new DialogInterface.OnClickListener() {
									@Override
									public void onClick(DialogInterface dialog, int which) {
										Intent intent = new Intent(mContext, PopProductActivity.class);
										intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
										startActivity(intent);
										finish();
									}
								}).create().show();
					}
					else
					{
						Intent intent = new Intent(mContext, MenuActivity.class);
						intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
						startActivity(intent);
						finish();
					}
				}
				else
				{
					new AlertDialog.Builder(mContext, R.style.MyDialog)
							.setMessage(jo.optString("process_message"))
							.setCancelable(false)
							.setPositiveButton("Ok", new DialogInterface.OnClickListener() {
								@Override
								public void onClick(DialogInterface dialog, int which) {
									Intent intent = new Intent(mContext, MenuActivity.class);
									intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
									startActivity(intent);
									finish();
								}
							}).create().show();

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

	public void make_payment(final View view) {
		try
		{
			if(get_branch_wise_pg_rollout(mContext).matches("ACTIVE"))
			{
				if (HTTPUtils.isConnectionPossible(mContext))
				{
					Intent intent = new Intent(mContext, PayAmountActivity.class);
					if(getIntent().hasExtra("tempTotal"))
						intent.putExtra("initialBalance", getIntent().getStringExtra("tempTotal")+"");

					intent.putExtra("coming_from", "pop_order");
					startActivity(intent);
				}
				else
				{
					show_msg_Dialog(mContext, check_internet_connection);
				}
			}
			else
			{
				show_msg_Dialog(mContext, "Payment Disabled, Please contact Admin");
			}
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}
}

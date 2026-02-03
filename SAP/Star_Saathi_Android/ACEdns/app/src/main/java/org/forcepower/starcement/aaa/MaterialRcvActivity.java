package org.forcepower.starcement.aaa;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;

import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.isSubmittedFeedback;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AlertDialog;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;
import org.forcepower.starcement.R;
import org.forcepower.starcement.util.HTTPUtils;
import org.json.JSONObject;

import java.util.ArrayList;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.Utils_.validEmailFormat;
import static org.forcepower.starcement.constants.Constants.acedns_star_update_material_receive_confirmation_v2;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.isValidIndianMobile;
import static org.forcepower.starcement.util.Utils.print_log_d;

public final class MaterialRcvActivity extends AceDnsParentActivity
{
	private Context mContext;
	private EditText tv_ch_quantity_no_of_bags, tv_no_of_damaged_bags;
	private String quantity_checking = "OK",quality_checking = "OK";
	private RadioGroup rg_quantity, rg_quality;

	@Override
	protected void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_material_rcv);
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
			tvHeaderText.setText("CHALLAN MATERIAL RECEIVED CONFIRMATION");
			ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
			ivHeaderBack.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					finish();
				}
			});
			//
			tv_ch_quantity_no_of_bags = (EditText) findViewById(R.id.tv_ch_quantity_no_of_bags);
			tv_no_of_damaged_bags = (EditText) findViewById(R.id.tv_no_of_damaged_bags);
			rg_quantity = (RadioGroup) findViewById(R.id.rg_quantity);
			rg_quantity.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
				@Override
				public void onCheckedChanged(RadioGroup group, int checkedId) {
					RadioButton radioButton = group.findViewById(checkedId);
					quantity_checking = radioButton.getText().toString();
				}
			});
			rg_quality = (RadioGroup) findViewById(R.id.rg_quality);
			rg_quality.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
				@Override
				public void onCheckedChanged(RadioGroup group, int checkedId) {
					RadioButton radioButton = group.findViewById(checkedId);
					quality_checking = radioButton.getText().toString();
				}
			});
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	public void RECEIVED(View view)
	{
		try
		{
			if(quantity_checking.matches(""))
			{
				Toast.makeText(mContext, "Please check quantity", Toast.LENGTH_SHORT).show();
			}
			else if(quality_checking.matches(""))
			{
				Toast.makeText(mContext, "Please check quality", Toast.LENGTH_SHORT).show();
			}
			else if(quantity_checking.equalsIgnoreCase("NOT OK")
					&& tv_ch_quantity_no_of_bags.getText().toString().trim().isEmpty())
			{
				Toast.makeText(mContext, "Please enter Quantity no of bags", Toast.LENGTH_SHORT).show();
			}
			else if(quality_checking.equalsIgnoreCase("NOT OK")
					&& tv_no_of_damaged_bags.getText().toString().trim().isEmpty())
			{
				Toast.makeText(mContext, "Please enter No. of damaged bags", Toast.LENGTH_SHORT).show();
			}
			else
			{
				if (HTTPUtils.isConnectionPossible(mContext))
				{
					new Update_Matirial_Rcv_Asynctask(mContext).execute();
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
	
	public final class Update_Matirial_Rcv_Asynctask extends AsyncTaskCoroutine<String, String>
	{
		Context mContext;
		ProgressDialog mStepProgressDialog;
		JSONObject jo = new JSONObject();
		public Update_Matirial_Rcv_Asynctask(Context mContext)
		{
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
						String url = acedns_star_update_material_receive_confirmation_v2;
						print_log_d("acedns_star_update_material_receive_confirmation_v2 ", url);

						ArrayList<NameValuePair> mHttpParamPairs = new ArrayList<>(2);
						mHttpParamPairs.add(new BasicNameValuePair("the_id", selected_customr_code));
						mHttpParamPairs.add(new BasicNameValuePair("ch_quantity_no_of_bags", tv_ch_quantity_no_of_bags.getText().toString()));
						mHttpParamPairs.add(new BasicNameValuePair("ch_quality_no_of_damaged_bags", tv_no_of_damaged_bags.getText().toString()));
						mHttpParamPairs.add(new BasicNameValuePair("ch_uid", getIntent().getStringExtra("ch_uid")));
						mHttpParamPairs.add(new BasicNameValuePair("challanno", getIntent().getStringExtra("challanno")));
						mHttpParamPairs.add(new BasicNameValuePair("quantity_checking", quantity_checking));
						mHttpParamPairs.add(new BasicNameValuePair("quality_checking", quality_checking));

						POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, mHttpParamPairs);
						print_log_d("receive_confirmation_155 ", mHttpParamPairs.toString());
						print_log_d("POST ", POST_result+"");

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
					isSubmittedFeedback = true;
					new AlertDialog.Builder(mContext, R.style.MyDialog)
							.setMessage(jo.optString("process_message"))
							.setCancelable(false)
							.setPositiveButton("OK", new DialogInterface.OnClickListener() {
								@Override
								public void onClick(DialogInterface dialog, int which) {
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
}

package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.save_dealer_sales_team_visit_details;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.show_msg_Dialog;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.RatingBar;
import android.widget.TextView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.DealerVisitAdapter;
import org.forcepower.starcement.bean.DealerVisitModel;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;

public final class ReviewRatingActivity extends AceDnsParentActivity
{
	private Activity mContext;
	private EditText et_rr_comments;
	private float get_rating = 0;
	private String emp_code = "", visit_datetime = "", emp_name = "";

	@Override
	protected void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_review_rating);
		try
		{
			mContext = this;

			emp_name = getIntent().getStringExtra("emp_name");
			emp_code = getIntent().getStringExtra("emp_code");
			visit_datetime = getIntent().getStringExtra("visit_datetime");

			final TextView tv_rr_top = (TextView) findViewById(R.id.tv_rr_top);
			tv_rr_top.setText("Hello,\nBased on your overall experience you had with our sales person "+ emp_name+",\nPlease rate us ");
			final TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
			tvHeaderText.setText("Rate Us");

			final ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
			ivHeaderBack.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					onBackPressed();
				}
			});

			et_rr_comments = (EditText) findViewById(R.id.et_rr_comments);
			final RatingBar rb_RR = findViewById(R.id.rb_RR);
			final Button btn_rr_submit = (Button) findViewById(R.id.btn_rr_submit);
			rb_RR.setOnRatingBarChangeListener(new RatingBar.OnRatingBarChangeListener() {
				@Override
				public void onRatingChanged(RatingBar ratingBar, float v, boolean b) {
					get_rating = ratingBar.getRating();
				}
			});
			btn_rr_submit.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View view) {
					try
					{
						if(!et_rr_comments.getText().toString().trim().isEmpty())
						{
							if(get_rating > 0)
							{
								if(HTTPUtils.isConnectionPossible(mContext))
								{
									new TRANS_PostRating_Asynctask(mContext).execute("");
								}
								else
								{
									show_msg_Dialog(mContext, check_internet_connection);
								}
							}
							else
							{
								show_msg_Dialog(mContext, "Please give a Rating");
							}
						}
						else
						{
							show_msg_Dialog(mContext, "Please write Remarks");
						}
					}
					catch (Exception e)
					{
						e.printStackTrace();
					}
				}
			});
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	public final class TRANS_PostRating_Asynctask extends AsyncTaskCoroutine<String, String>
	{
		private Activity mContext;
		private JSONObject jo = new JSONObject();
		public TRANS_PostRating_Asynctask(final Activity mContext) {
			this.mContext = mContext;
		}

		@Override
		public void onPreExecute()
		{
			super.onPreExecute();
			Utils.showProgressDialog(mContext, "Updating please wait..");
		}
		@Override
		public String doInBackground(final String... params)
		{
			String POST_result = "";

			if (HTTPUtils.isConnectionPossible(mContext))
			{
				try
				{
					final String url = save_dealer_sales_team_visit_details;
					print_log_d("gr32po_U ", url);

					final ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(4);

					if(get_user_type(mContext).equalsIgnoreCase("broker"))
					{
						print_Log_d("gr32po_b1 ", get_selected_dealer_sap_code(mContext) + " ");
						nameValuePairs.add(new BasicNameValuePair("customer_id", get_selected_dealer_sap_code(mContext)));
					}
					else
					{
						nameValuePairs.add(new BasicNameValuePair("customer_id", get_dealer_id(mContext)));
						print_Log_d("gr32po_b2 ", get_dealer_id(mContext) + " ");
					}

                    nameValuePairs.add(new BasicNameValuePair("emp_code", emp_code));
                    nameValuePairs.add(new BasicNameValuePair("visit_datetime", visit_datetime));
                    nameValuePairs.add(new BasicNameValuePair("survey_rating", get_rating + ""));
                    nameValuePairs.add(new BasicNameValuePair("remarks", et_rr_comments.getText().toString().trim()));

					POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);

					print_log_d("gr32po_P ", nameValuePairs.toString());
					print_log_d("gr32po_R ", POST_result);

					jo = new JSONObject(POST_result);

				}
				catch (Exception e)
				{
					POST_result = "Network Failure";
				}
			}
			return POST_result;
		}
		@Override
		public void onPostExecute(String result)
		{
			super.onPostExecute(result);
			try
			{
				if(jo.optString("process_status").equalsIgnoreCase("YES"))
				{
					show_msg_alert(mContext, jo.optString("process_message"), true);

				}
				else
				{
					show_msg_alert(mContext, jo.optString("process_message"), false);
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
}

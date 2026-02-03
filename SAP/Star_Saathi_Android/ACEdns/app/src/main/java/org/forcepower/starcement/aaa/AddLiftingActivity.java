package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_belong_dealer_code;
import static org.forcepower.starcement.SharedPrefData.get_belong_dealer_name;
import static org.forcepower.starcement.SharedPrefData.get_dns_emp_code;
import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_login_mobile_number;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.changeDateFormat_;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.acedns_star_add_lifting;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.downloadTableList;
import static org.forcepower.starcement.constants.Constants.game_authorization;
import static org.forcepower.starcement.constants.Constants.isSubmittedFeedback;
import static org.forcepower.starcement.constants.Constants.lifting_date_validation_data;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.content.Intent;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.os.Message;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.Gravity;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.ProductAdapter;
import org.forcepower.starcement.adapter.ProductMasterWithQtyInputAdapterAlternateDesign;
import org.forcepower.starcement.bean.ProductMasterDetails;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.database.AceDnsDatabase;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.forcepower.starcement.util.commonAsyncTaskMaster;
import org.json.JSONArray;
import org.json.JSONObject;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.Locale;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;

public final class AddLiftingActivity extends AceDnsParentActivity
{
	private Activity mContext;
	private ArrayList<ProductMasterDetails> productMasterList = new ArrayList<>();
	private TextView et_product, et_date_of_lifting;
	private AceDnsDatabase mAceDnsDatabase;
	private EditText et_quantity, et_challan_number;
	private String continue_status = "";
	private long validation_from = 0, validation_to = 0, validation_last_date = 0, current_date = 0;

	private Handler mPrepareSurveyHandler;
	@Override
	protected void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_add_lifting);
		try
		{
			mContext = this;
			mAceDnsDatabase = new AceDnsDatabase(mContext);
			productMasterList = mAceDnsDatabase.getProductMasterList("1", 1, false);

			final TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
			tvHeaderText.setText("ADD LIFTING");
			final ImageView iv_prod = (ImageView) findViewById(R.id.iv_prod);
			final ImageView iv_date_of_lifting = (ImageView) findViewById(R.id.iv_date_of_lifting);
			final ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
			ivHeaderBack.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					onBackPressed();
				}
			});

			final EditText et_linked_dealer = (EditText) findViewById(R.id.et_linked_dealer);
			et_product = (TextView) findViewById(R.id.et_product);
			et_date_of_lifting = (TextView) findViewById(R.id.et_date_of_lifting);
			et_quantity = (EditText) findViewById(R.id.et_quantity);
			et_challan_number = (EditText) findViewById(R.id.et_challan_number);
			et_linked_dealer.setText(get_belong_dealer_name(mContext));
			et_product.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View view) {
					show_product_list_dialog();
				}
			});
			iv_prod.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View view) {
					show_product_list_dialog();
				}
			});

			final Button btn_lifting_submit = (Button) findViewById(R.id.btn_lifting_submit);
			btn_lifting_submit.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View view) {
					try
					{
						int qty = 0;
						try
						{
							qty = Integer.parseInt(et_quantity.getText().toString().trim());
						}
						catch (Exception e)
						{
							e.printStackTrace();
						}

						if(et_product.getText().toString().trim().isEmpty())
						{
							show_msg_alert(mContext, "Please select a Product", false);
						}
						else if(et_quantity.getText().toString().trim().isEmpty() || qty <1)
						{
							show_msg_alert(mContext, "Please enter quantity", false);
						}
						else if(et_date_of_lifting.getText().toString().trim().isEmpty())
						{
							show_msg_alert(mContext, "Please select date of lifting", false);
						}
						else if(et_challan_number.getText().toString().trim().isEmpty())
						{
							show_msg_alert(mContext, "Please enter challan number", false);
						}
						else if (!HTTPUtils.isConnectionPossible(mContext))
						{
							show_msg_alert(mContext, check_internet_connection, false);
						}
						else if (continue_status.equalsIgnoreCase("yes"))
						{
							new TRANS_PostLifting_Asynctask(mContext).execute();
						}
						else
						{
							new TRANS_GetLifting_Asynctask(mContext).execute();
						}
					}
					catch (Exception e)
					{
						e.printStackTrace();
					}
				}
			});

			mPrepareSurveyHandler = new Handler(Looper.myLooper()) {
				public void handleMessage(@NonNull Message threadmsg) {
					//mPrepareSurveyProgressDialog.dismiss();
					final int listcount = threadmsg.getData().getInt("JOBALLOCATE");
					runOnUiThread(new Runnable() {
						public void run() {
							if(listcount==downloadTableList.size()-1)
							{
								Utils.changeProgressDialogMsg(mContext, "Successfully updated");
								dismissLoader();

								Constants.selectedProductMasterList.clear();
								productMasterList = mAceDnsDatabase.getProductMasterList("1", 1, false);

								new TRANS_GetLifting_Asynctask(mContext).execute();
							}
							else
							{
								mCount++;
								DownloadData(mCount, downloadTableList.get(mCount));
							}
						}
					});
				}
			};
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
		finally
		{
			if (HTTPUtils.isConnectionPossible(mContext))
			{
				if(productMasterList.size() == 0)
				{
					downloadTableList.clear();
					downloadTableList.add("product_data");

					mCount = 0;
					DownloadData(mCount, downloadTableList.get(mCount));
				}
				else
				{
					new TRANS_GetLifting_Asynctask(mContext).execute();
				}
			}
			else
			{
				show_msg_alert(mContext, check_internet_connection, true);
			}
		}
	}

	private int mCount = 0;
	public void DownloadData(final int task, final String params)
	{

		showLoader();
		new Thread()
		{
			public void run()
			{
//                if(params.equalsIgnoreCase("destination_master"))
//                {
				new commonAsyncTaskMaster(mContext,params);
//                }

				Message msg = mPrepareSurveyHandler.obtainMessage();
				Bundle bundle = new Bundle();
				bundle.putInt("JOBALLOCATE", task);
				msg.setData(bundle);
				mPrepareSurveyHandler.sendMessage(msg);
			}
		}.start();
	}
	public void showLoader()
	{
		Utils.showProgressDialog(mContext, "Updating please wait..");
	}
	public void dismissLoader()
	{
		Utils.cancelProgressDialog();
	}

	public void show_product_list_dialog()
	{
		try
		{
			final Dialog mDestinationDialog = new Dialog(mContext,
					android.R.style.Theme_DeviceDefault_Light_NoActionBar);
			mDestinationDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
			Window window = mDestinationDialog.getWindow();
			window.setGravity(Gravity.CENTER);
			window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.MATCH_PARENT);
			mDestinationDialog.setContentView(R.layout.select_with_search);
			mDestinationDialog.setCancelable(true);
			window.setStatusBarColor(getResources().getColor(R.color.colorRed_StatusBar));
			ImageView btnback = (ImageView) mDestinationDialog.findViewById(R.id.back);
			btnback.setOnClickListener(new View.OnClickListener() {
				@Override
				public void onClick(View v) {
					mDestinationDialog.dismiss();
				}
			});
			TextView title = (TextView) mDestinationDialog.findViewById(R.id.tvDestHeading);
			title.setText("Please select a Product");
			ListView dialogList = (ListView) mDestinationDialog.findViewById(R.id.list);

			//final ArrayAdapter<String> RoutePlanAdapter = new ArrayAdapter<String>(this,R.layout.activity_listview, mDestinationMasterList);

			final ProductAdapter pAdapter = new ProductAdapter(this,R.layout.customer_broker_list_child, productMasterList);

			dialogList.setAdapter(pAdapter);

			EditText searchText = (EditText) mDestinationDialog
					.findViewById(R.id.autoCompleteTextView1);
			searchText.addTextChangedListener(new TextWatcher() {
				@Override
				public void onTextChanged(CharSequence s, int arg1, int arg2,
										  int arg3) {
					pAdapter.getFilter().filter(s.toString());
				}

				@Override
				public void beforeTextChanged(CharSequence arg0, int arg1,
											  int arg2, int arg3) {
				}

				@Override
				public void afterTextChanged(Editable s) {
				}
			});

			dialogList.setOnItemClickListener(new AdapterView.OnItemClickListener() {
				@Override
				public void onItemClick(AdapterView<?> arg0, View arg1,
										int position, long arg3) {

					et_product.setText(pAdapter.getItem(position).getDesc());
					et_product.setTag(pAdapter.getItem(position).getProdCode());
					mDestinationDialog.dismiss();
				}
			});

			mDestinationDialog.show();
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	public void show_calendar(final View view)
	{
		try
		{
			final Calendar cldr = Calendar.getInstance();
			final int day = cldr.get(Calendar.DAY_OF_MONTH);
			final int month = cldr.get(Calendar.MONTH);
			final int year = cldr.get(Calendar.YEAR);

			// date picker dialog
			final DatePickerDialog picker_to = new DatePickerDialog(mContext,
					new DatePickerDialog.OnDateSetListener() {
						@Override
						public void onDateSet(final DatePicker datePicker, final int year, final int monthOfYear, final int dayOfMonth) {
							final String date = year + "-" + (monthOfYear + 1) + "-" + dayOfMonth;
							et_date_of_lifting.setText(date);
						}
					}, year, month, day);
			picker_to.show();

            picker_to.getDatePicker().setMaxDate(current_date);
			cldr.add(Calendar.MONTH, -1);
			picker_to.getDatePicker().setMinDate(validation_from);
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	public final class TRANS_PostLifting_Asynctask extends AsyncTaskCoroutine<String, String>
	{
		Activity mContext;
		JSONObject jo = new JSONObject();

		public TRANS_PostLifting_Asynctask(Activity mContext) {
			this.mContext = mContext;
		}

		@Override
		public void onPreExecute()
		{
			super.onPreExecute();
			Utils.showProgressDialog(mContext, "Submitting...");

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
						final String url = acedns_star_add_lifting;
						print_log_d("PRINT_acedns_star_add_lifting_", url);
						final ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(2);

						nameValuePairs.add(new BasicNameValuePair("linked_dealer_cust_code", get_belong_dealer_code(mContext)));
						nameValuePairs.add(new BasicNameValuePair("sub_dealer_cust_code", get_emp_or_customer_code(mContext)));
						nameValuePairs.add(new BasicNameValuePair("prod_code", et_product.getTag().toString()));
						nameValuePairs.add(new BasicNameValuePair("tot_bag_qty", et_quantity.getText().toString().trim()));
						nameValuePairs.add(new BasicNameValuePair("lifting_date", et_date_of_lifting.getText().toString()));
						nameValuePairs.add(new BasicNameValuePair("challan_no", et_challan_number.getText().toString().trim()));

						POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);


						jo = new JSONObject(POST_result);
						print_log_d("PRINT_265_lifting ", nameValuePairs.toString());
						print_log_d("PRINT_266_lifting ", POST_result);
						print_log_d("PRINT_266_lifting ", url);

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
				isSubmittedFeedback = true;
				show_msg_alert(mContext, jo.optString("process_message"), true);
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
	public final class TRANS_GetLifting_Asynctask extends AsyncTaskCoroutine<String, String>
	{
		Activity mContext;
		JSONObject jo = new JSONObject();

		public TRANS_GetLifting_Asynctask(Activity mContext) {
			this.mContext = mContext;
		}

		@Override
		public void onPreExecute()
		{
			super.onPreExecute();
			Utils.showProgressDialog(mContext, "Updating...");

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
						final String url = lifting_date_validation_data;
						print_log_d("lifting_date_validation_data_ ", url);
						final ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(2);

						nameValuePairs.add(new BasicNameValuePair("customer_code", get_dns_emp_code(mContext)));
						nameValuePairs.add(new BasicNameValuePair("user_type", get_user_type(mContext)));
//						nameValuePairs.add(new BasicNameValuePair("customer_code", "A012"));
//						nameValuePairs.add(new BasicNameValuePair("user_type", "dealer"));
						print_log_d("lifting_date_validation_data_URL ", url);
						print_log_d("lifting_date_validation_data_PARAMS ", nameValuePairs.toString());

						POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);
						print_log_d("lifting_date_validation_data_POST_RESULTS ", POST_result);

						jo = new JSONObject(POST_result);
						if(jo.optString("process_status").equalsIgnoreCase("Yes"))
						{
							print_log_d("PRINT_465_validation_data ", nameValuePairs.toString());
							print_log_d("PRINT_data ", POST_result);
							print_log_d("PRINT_466_validation_data_data ", url);

							final String lifting_validation_data = jo.optString("lifting_validation_data");
							print_log_d("lifting_validation_data_9010 ", lifting_validation_data);

							final JSONArray jsonArray = new JSONArray(lifting_validation_data);

							validation_from = dateToMilliSecond(jsonArray.getJSONObject(0).optString("validation_from"));
							validation_to = dateToMilliSecond(jsonArray.getJSONObject(0).optString("validation_to"));
							validation_last_date = dateToMilliSecond(jsonArray.getJSONObject(0).optString("validation_last_date"));
							current_date = dateToMilliSecond(jsonArray.getJSONObject(0).optString("current_date"));

							print_log_d("validation_from_data_9010 ", validation_from+"");
						}
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
				if(jo.optString("process_status").equalsIgnoreCase("No"))
				{
					continue_status = "No";
					show_msg_alert(mContext, jo.optString("process_message"), true);
				}
				else if(!(current_date > 0 && current_date >= validation_from && current_date <= validation_last_date))
				{
					continue_status = "No";
					show_msg_alert(mContext, "Please contact Admin", true);
				}
				else
				{
					continue_status = "Yes";
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
	public long dateToMilliSecond(final String mDate)
	{
		long millis = 0;
		try
		{
			final SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault());
			final Date date = sdf.parse(mDate);
			millis = date.getTime();
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
		return millis;
	}
}

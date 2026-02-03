package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_belong_dealer_code;
import static org.forcepower.starcement.SharedPrefData.get_belong_dealer_name;
import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.get_server_current_date;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.downloadTableList;
import static org.forcepower.starcement.constants.Constants.isSubmittedFeedback;
import static org.forcepower.starcement.constants.Constants.save_order_query_data;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.Dialog;
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
import android.widget.ListView;
import android.widget.TextView;

import androidx.annotation.NonNull;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.ProductAdapter;
import org.forcepower.starcement.bean.ProductMasterDetails;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.database.AceDnsDatabase;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.forcepower.starcement.util.commonAsyncTaskMaster;
import org.json.JSONObject;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.Locale;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;

public final class PlaceOrderRssdActivity extends AceDnsParentActivity
{
	private Activity mContext;
	private ArrayList<ProductMasterDetails> productMasterList = new ArrayList<>();
	private TextView et_product, et_date_of_lifting;
	private AceDnsDatabase mAceDnsDatabase;
	private EditText et_quantity, et_remarks;
	private long current_date = 0;

	private Handler mPrepareSurveyHandler;
	private int mCount = 0;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_place_order_rssd);
		try
		{
			mContext = this;
			mAceDnsDatabase = new AceDnsDatabase(mContext);
			productMasterList = mAceDnsDatabase.getProductMasterList("1", 1, false);

			if(get_user_type(mContext).equalsIgnoreCase("broker"))
			{
				selected_customr_code = get_selected_dealer_sap_code(mContext);
			}
			else
			{
				selected_customr_code = get_dealer_id(mContext);
			}

			final TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
			tvHeaderText.setText("New Order Enquiry");
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
			et_remarks = (EditText) findViewById(R.id.et_remarks);
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
						else if (!HTTPUtils.isConnectionPossible(mContext))
						{
							show_msg_alert(mContext, check_internet_connection, false);
						}
						else //if (continue_status.equalsIgnoreCase("yes"))
						{
							new TRANS_PostLifting_Asynctask(mContext).execute();
						}
//						else
//						{
//							new TRANS_GetLifting_Asynctask(mContext).execute();
//						}
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

//								new TRANS_GetLifting_Asynctask(mContext).execute();
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
			final String crr_dt = Utils.changeDateFormat( "yyyy-MM-dd", "yyyy-MM-dd", get_server_current_date(mContext));
			current_date = dateToMilliSecond(crr_dt);

			if (HTTPUtils.isConnectionPossible(mContext))
			{
				if(productMasterList.size() == 0)
				{
					downloadTableList.clear();
					downloadTableList.add("product_data");

					mCount = 0;
					DownloadData(mCount, downloadTableList.get(mCount));
				}
//				else
//				{
//					new TRANS_GetLifting_Asynctask(mContext).execute();
//				}
			}
			else
			{
				show_msg_alert(mContext, check_internet_connection, true);
			}
		}
	}

	public void DownloadData(final int task, final String params) {

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

	public void show_product_list_dialog() {
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
			btnback.setOnClickListener(new OnClickListener() {
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

	public void show_calendar(final View view) {
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

//            picker_to.getDatePicker().setMaxDate(validation_last_date);
			cldr.add(Calendar.MONTH, -1);
			picker_to.getDatePicker().setMinDate(current_date);
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	public long dateToMilliSecond(final String mDate) {
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

	public final class TRANS_PostLifting_Asynctask extends AsyncTaskCoroutine<String, String> {
		Activity mContext;
		JSONObject jo = new JSONObject();

		public TRANS_PostLifting_Asynctask(Activity mContext) {
			this.mContext = mContext;
		}

		@Override
		public void onPreExecute() {
			super.onPreExecute();
			Utils.showProgressDialog(mContext, "Submitting...");

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
						final String url = save_order_query_data;
						print_log_d("jr38et_Us ", url);
						final ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(2);



						final String global_timeStamp = Constants.dateString+ new SimpleDateFormat("HHmmss", Locale.getDefault()).format(Calendar.getInstance().getTime());
						final String t_order_id = "RS"+get_dealer_id(mContext)+ global_timeStamp+""+et_product.getTag().toString();


						print_log_d("jr38et_Us customer_id", selected_customr_code);
						print_log_d("jr38et_Us order_query_data[0][order_id]", t_order_id);
						print_log_d("jr38et_Us order_query_data[0][linked_dealer_code]", get_belong_dealer_code(mContext));
						print_log_d("jr38et_Us order_query_data[0][dns_prod_code]", et_product.getTag().toString());
						print_log_d("jr38et_Us order_query_data[0][prod_name]", et_product.getText().toString());
						print_log_d("jr38et_Us order_query_data[0][qty_bags]", et_quantity.getText().toString().trim());
						print_log_d("jr38et_Us order_query_data[0][query_date]", et_date_of_lifting.getText().toString());
						print_log_d("jr38et_Us order_query_data[0][date_of_lifting]", get_server_current_date(mContext));
						print_log_d("jr38et_Us order_query_data[0][remarks]", et_remarks.getText().toString());


//						order_query_data
//						nameValuePairs.add(new BasicNameValuePair("customer_id", selected_customr_code));
//						nameValuePairs.add(new BasicNameValuePair("order_query_data[0][order_id]", t_order_id));
//						nameValuePairs.add(new BasicNameValuePair("order_query_data[0][linked_dealer_code]", get_belong_dealer_code(mContext)));
//						nameValuePairs.add(new BasicNameValuePair("order_query_data[0][dns_prod_code]", et_product.getTag().toString()));
//						nameValuePairs.add(new BasicNameValuePair("order_query_data[0][prod_name]", et_product.getText().toString()));
//						nameValuePairs.add(new BasicNameValuePair("order_query_data[0][qty_bags]", et_quantity.getText().toString().trim()));
//						nameValuePairs.add(new BasicNameValuePair("order_query_data[0][query_date]", et_date_of_lifting.getText().toString()));
//						nameValuePairs.add(new BasicNameValuePair("order_query_data[0][date_of_lifting]", get_server_current_date(mContext)));
//						nameValuePairs.add(new BasicNameValuePair("order_query_data[0][remarks]", et_remarks.getText().toString()));

//						nameValuePairs.add(new BasicNameValuePair("remarks", et_remarks.getText().toString())); //vola2715

//						POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);


//						jo = new JSONObject(POST_result);
//						print_log_d("jr38et_Ps ", nameValuePairs.toString());
//						print_log_d("jr38et_Rs ", POST_result);
						POST_result = "Network Failure";
					}
					catch (Exception e)
					{
						print_log_d("jr38et_Err1 ", e.toString());

						POST_result = "Network Failure";
					}
				}
			}
			catch (Exception e)
			{
				print_log_d("jr38et_Err2 ", e.toString());
				e.printStackTrace();
			}
			return POST_result;
		}
		@Override
		public void onPostExecute(String result) {
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
}

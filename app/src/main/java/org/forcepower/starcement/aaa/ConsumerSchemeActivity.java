package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_name;
import static org.forcepower.starcement.SharedPrefData.get_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.get_server_current_date;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.changeDateFormat_;
import static org.forcepower.starcement.Utils_.dateToMilliSecond;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.downloadTableList;
import static org.forcepower.starcement.constants.Constants.save_consumer_scheme;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.isValidIndianMobile;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.show_msg_Dialog;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.os.Message;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;

import androidx.annotation.NonNull;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.ProductMasterWithQtyInputAdapterAlternateDesign_CS;
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
import java.util.Locale;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;

public final class ConsumerSchemeActivity extends AceDnsParentActivity
{
	private AceDnsDatabase mAceDnsDatabase;
	private Activity mContext;
	private ArrayList<ProductMasterDetails> productMasterList = new ArrayList<>();
	private ProductMasterWithQtyInputAdapterAlternateDesign_CS ProductMasterWithQtyInputAdapterObjectAlternateDesignObject;
	private TextView tv_caption, tv_ttl_qty, et_product;
	private ImageView iv_prod;
	private ListView dialogList;
	private Handler mPrepareSurveyHandler;
	private EditText et_owner_name, et_owner_phone;
	private int mCount = 0;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_consumer_scheme);
		try
		{
			et_owner_name = (EditText) findViewById(R.id.et_owner_name);
			et_owner_phone = (EditText) findViewById(R.id.et_owner_phone);
			et_product = (TextView) findViewById(R.id.et_product);
			dialogList = (ListView) findViewById(R.id.prodQtyRateListView);
			tv_caption = (TextView) findViewById(R.id.tvHeaderText);
			tv_ttl_qty = (TextView) findViewById(R.id.tv_ttl_qty);
			tv_caption.setText("Consumer Lottery Scheme");
            mContext = this;
			if(get_user_type(mContext).equalsIgnoreCase("broker"))
			{
				selected_customr_code = get_selected_customer_code(mContext);
			}
			else
			{
				selected_customr_code = get_emp_or_customer_code(mContext);
			}
            mAceDnsDatabase = new AceDnsDatabase(mContext);

			ImageView back = (ImageView) findViewById(R.id.ivHeaderBack);
			back.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					onBackPressed();
				}
			});


			if(get_user_type(mContext).equalsIgnoreCase("broker"))
			{
				tv_caption.setText(get_selected_customer_name(mContext));
			}


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
								productMasterList.clear();

//								productMasterList = mAceDnsDatabase.getProductMasterList("1", 1, false);

								ProductMasterDetails prodObj = new ProductMasterDetails();
								prodObj.setProdCode("dhalai_master");
								prodObj.setDnsProdCode("");
								prodObj.setDesc("Dhalai Master");
								prodObj.setGrpCode("");
								prodObj.setBranchCode("");
								productMasterList.add(prodObj);

								prodObj = new ProductMasterDetails();
								prodObj.setProdCode("weather_shield");
								prodObj.setDnsProdCode("");
								prodObj.setDesc("Weather Shield");
								prodObj.setGrpCode("");
								prodObj.setBranchCode("");
								productMasterList.add(prodObj);

								ProductMasterWithQtyInputAdapterObjectAlternateDesignObject = new ProductMasterWithQtyInputAdapterAlternateDesign_CS(mContext,R.layout.list_item__product_with_quantity_input_alternate_design, productMasterList, tv_ttl_qty);
								dialogList.setEmptyView(findViewById(R.id.empty_text_view));
								dialogList.setAdapter(ProductMasterWithQtyInputAdapterObjectAlternateDesignObject);
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

			final Button btn_conti = (Button) findViewById(R.id.btn_conti);
			btn_conti.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					checkoutProcess();
				}
			});

			et_product.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View view) {
					show_calendar();
				}
			});

			iv_prod = (ImageView) findViewById(R.id.iv_prod);
			iv_prod.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View view) {
					show_calendar();
				}
			});
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
		finally
		{
			downloadTableList.clear();
			downloadTableList.add("product_data");

			mCount = 0;
			DownloadData(mCount, downloadTableList.get(mCount));
		}
	}

	public void show_calendar() {
		try
		{
			final String cur_val_dt = Utils.changeDateFormat( "yyyy-MM-dd", "yyyy-MM-dd", get_server_current_date(mContext));
			final long curr_validation_dt = dateToMilliSecond(cur_val_dt);

			final String start_dt = Utils.changeDateFormat( "yyyy-MM-dd", "yyyy-MM-dd", "2025-01-21");
			final long val_start_date_21_jan = dateToMilliSecond(start_dt);


			final Calendar cldr = Calendar.getInstance();
			final int day = cldr.get(Calendar.DAY_OF_MONTH);
			final int month = cldr.get(Calendar.MONTH);
			final int year = cldr.get(Calendar.YEAR);

			// date picker dialog
			final DatePickerDialog picker_to = new DatePickerDialog(mContext,
					new DatePickerDialog.OnDateSetListener() {
						@Override
						public void onDateSet(final DatePicker datePicker, final int year, final int monthOfYear, final int dayOfMonth) {
							final String date = dayOfMonth + "/" + (monthOfYear + 1) + "/" + year;

							final String seleted_date = changeDateFormat_(date, "dd/MM/yyyy", "yyyy-MM-dd");
							final long val_seleted_date = dateToMilliSecond(seleted_date);

							if(val_seleted_date >= val_start_date_21_jan)
							{
								final String dateT = changeDateFormat_(date, "dd/MM/yyyy", "MM.dd.yyyy");
								et_product.setText(dateT);

								final String dateG = changeDateFormat_(date, "dd/MM/yyyy", "yyyy-MM-dd");
								et_product.setTag(dateG);
							}
							else
							{
								et_product.setText("");
								et_product.setTag("");
							}

						}
					}, year, month, day);
			picker_to.show();


			picker_to.getDatePicker().setMaxDate(curr_validation_dt); //  86400000
			cldr.add(Calendar.MONTH, -1);
			picker_to.getDatePicker().setMinDate(val_start_date_21_jan);
		}
		catch (Exception e)
		{
			e.printStackTrace();
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

	private void checkoutProcess() {
		try
		{
			if(et_owner_name.getText().toString().trim().isEmpty())
			{
				show_msg_Dialog(mContext, "Please enter Owner name");
			}
			else if(!isValidIndianMobile(et_owner_phone.getText().toString().trim()))
			{
				show_msg_Dialog(mContext, "Please enter valid phone number");
			}
			else if(et_product.getText().toString().trim().isEmpty())
			{
				show_msg_Dialog(mContext, "Please select the purchase date");
			}
			else if (Constants.selectedProductMasterList.size() == 0)
			{
				show_msg_Dialog(mContext, "Please add at least one product");
			}
			else if(HTTPUtils.isConnectionPossible(mContext))
			{
				new TRANS_PostConsumerScheme_Asynctask(mContext).execute();
			}
			else
			{
				show_msg_Dialog(mContext, check_internet_connection);
			}
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	@Override
	public void onResume() {
		super.onResume();

		Constants.selectedProductMasterList.clear();
		dialogList.setAdapter(ProductMasterWithQtyInputAdapterObjectAlternateDesignObject);
	}

	public final class TRANS_PostConsumerScheme_Asynctask extends AsyncTaskCoroutine<String, String> {
		private Activity mContext;
		private JSONObject jo = new JSONObject();
		public TRANS_PostConsumerScheme_Asynctask(final Activity mContext) {
			this.mContext = mContext;
		}

		@Override
		public void onPreExecute() {
			super.onPreExecute();
			Utils.showProgressDialog(mContext, "Updating please wait..");
		}
		@Override
		public String doInBackground(final String... params) {
			String POST_result = "";

			if (HTTPUtils.isConnectionPossible(mContext))
			{
				try
				{
					final String url = save_consumer_scheme;

					final ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(4);
					final String global_timeStamp = Constants.dateString+ new SimpleDateFormat("HHmmss", Locale.getDefault()).format(Calendar.getInstance().getTime());
					String o_id = "";

					if(get_user_type(mContext).equalsIgnoreCase("broker"))
					{
						o_id = "SO"+get_selected_dealer_sap_code(mContext)+ global_timeStamp;
						nameValuePairs.add(new BasicNameValuePair("customer_id", get_selected_dealer_sap_code(mContext)));
					}
					else
					{
						o_id = "SO"+get_dealer_id(mContext)+ global_timeStamp;
						nameValuePairs.add(new BasicNameValuePair("customer_id", get_dealer_id(mContext)));
					}

					nameValuePairs.add(new BasicNameValuePair("trans_id", o_id));
					nameValuePairs.add(new BasicNameValuePair("datetime", get_server_current_date(mContext) + " "+ new SimpleDateFormat("HH:mm:ss", Locale.getDefault()).format(Calendar.getInstance().getTime())));
					nameValuePairs.add(new BasicNameValuePair("customer_name", et_owner_name.getText().toString().trim()));
					nameValuePairs.add(new BasicNameValuePair("customer_phone_no", et_owner_phone.getText().toString().trim()));
					nameValuePairs.add(new BasicNameValuePair("date_of_purchase", et_product.getTag().toString().trim()));

					if(Constants.selectedProductMasterList.containsKey("dhalai_master"))
					{
						nameValuePairs.add(new BasicNameValuePair("dhalai_master_qty", Constants.selectedProductMasterList.get("dhalai_master").getQty()));
					}
					else
					{
						nameValuePairs.add(new BasicNameValuePair("dhalai_master_qty", "0"));
					}
					if(Constants.selectedProductMasterList.containsKey("weather_shield"))
					{
						nameValuePairs.add(new BasicNameValuePair("weather_shield_qty", Constants.selectedProductMasterList.get("weather_shield").getQty()));
					}
					else
					{
						nameValuePairs.add(new BasicNameValuePair("weather_shield_qty", "0"));
					}

					print_log_d("ger445_U ", url);
					print_log_d("ger445_P ", nameValuePairs.toString());

					POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);

					print_log_d("ger445_R ", POST_result);

					jo = new JSONObject(POST_result);

				}
				catch (Exception e)
				{
					print_log_d("ger445_Err ", e.toString());

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
				if(result.equalsIgnoreCase("Network Failure"))
				{
					show_msg_Dialog(mContext, check_internet_connection);
				}
				else if(jo.optString("process_status").equalsIgnoreCase("YES"))
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

	@Override
	public void onBackPressed()
	{
		finish();
	}
}

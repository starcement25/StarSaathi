package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_name;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.adapter.LedgerListViewAdapter.convertDate;
import static org.forcepower.starcement.constants.Constants.acedns_star_ledger_by_id;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.downloadTableList;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.os.Message;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;

import org.forcepower.starcement.R;
import org.forcepower.starcement.Utils_;
import org.forcepower.starcement.adapter.ProductMasterWithQtyInputAdapterAlternateDesign;
import org.forcepower.starcement.bean.ProductMasterDetails;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.database.AceDnsDatabase;
import org.forcepower.starcement.util.ConnectionDetector;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.forcepower.starcement.util.commonAsyncTaskMaster;
import org.json.JSONObject;

import java.util.ArrayList;

public final class OrderFormActivityAlternateDesign extends AceDnsParentActivity
{
	private AceDnsDatabase mAceDnsDatabase;
	private Activity mContext;
	private ArrayList<ProductMasterDetails> productMasterList = new ArrayList<>();
	private ProductMasterWithQtyInputAdapterAlternateDesign ProductMasterWithQtyInputAdapterObjectAlternateDesignObject;
	private TextView tv_caption;
	private ListView dialogList;
	private Handler mPrepareSurveyHandler;
	private int mCount = 0;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_order_form_alternate_design);
		try
		{
            dialogList = (ListView) findViewById(R.id.prodQtyRateListView);
			tv_caption = (TextView) findViewById(R.id.tv_caption);

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



			ConnectionDetector cd= new ConnectionDetector(mContext);
			Boolean isInternetPresent=cd.isConnectingToInternet();
			if(isInternetPresent)
			{
				new TRANS_GetLedgerDetails_Asynctask(mContext).execute("");
			}
			else
			{
				Utils_.closeApp(mContext,check_internet_connection);
			}

			ImageView back = (ImageView) findViewById(R.id.back);
			back.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					onBackPressed();
				}
			});
			ImageView ivForward = (ImageView) findViewById(R.id.ivForward);
			ivForward.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					goto_cart(null);
				}
			});

			if(get_user_type(mContext).equalsIgnoreCase("broker"))
			{
				tv_caption.setText(get_selected_customer_name(mContext));
			}

			final TextView empty_text_view = (TextView) findViewById(R.id.empty_text_view);
			empty_text_view.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					if(isInternetPresent)
					{
						downloadTableList.clear();
						downloadTableList.add("product_data");

						mCount = 0;
						DownloadData(mCount, downloadTableList.get(mCount));
					}
					else
					{
						Utils_.closeApp(mContext,check_internet_connection);
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
								productMasterList.clear();

								productMasterList = mAceDnsDatabase.getProductMasterList("1", 1, false);
								ProductMasterWithQtyInputAdapterObjectAlternateDesignObject = new ProductMasterWithQtyInputAdapterAlternateDesign(mContext,R.layout.list_item__product_with_quantity_input_alternate_design, productMasterList);
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

	@Override
	public void onResume() {
		super.onResume();

		Constants.selectedProductMasterList.clear();
		dialogList.setAdapter(ProductMasterWithQtyInputAdapterObjectAlternateDesignObject);
	}

	@Override
	public void onBackPressed()
	{
		finish();
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
		if (Constants.selectedProductMasterList.size() > 0)
		{
			Intent intent = new Intent(mContext, ActivityOrderFilter.class);
			startActivity(intent);
		}
		else
		{
			Toast.makeText(mContext,
					"Please add at least one product", Toast.LENGTH_SHORT).show();
		}
	}

	public void goto_cart(View view) {
		checkoutProcess();
	}
	
	public final class TRANS_GetLedgerDetails_Asynctask extends AsyncTaskCoroutine<String, String> {
		Context mContext;
		ProgressDialog mStepProgressDialog;
		String balance = "00.00", date = "";
		public TRANS_GetLedgerDetails_Asynctask(Context mContext) {
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
						String url = acedns_star_ledger_by_id + selected_customr_code;
						Log.d("PRINT_LEDGER_URL_DESIGN", url);
						POST_result = HTTPUtils.getDataByHTTP_GET(mContext, url);

						JSONObject jo = new JSONObject(POST_result);
						if(jo.has("process_status") && jo.getString("process_status").equalsIgnoreCase("yes"))
						{
							String ledger_balance_data = jo.getString("ledger_balance_data");
							JSONObject joBal = new JSONObject(ledger_balance_data);
							//need to add dns_customer_code
							balance = joBal.getString("balance");
							date = joBal.getString("date");
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
		public void onPostExecute(String result) {
			super.onPostExecute(result);

			try
			{
				TextView tvOutStanding = (TextView) findViewById(R.id.tvOutStanding);
				TextView tvOS = (TextView) findViewById(R.id.tvOS);

				tvOutStanding.setText(" " + balance);
				tvOS.setText("OUTSTANDING AS ON " +  convertDate(date));
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

package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.ApiRes;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.DEFAULT_TIMEOUT;
import static org.forcepower.starcement.constants.Constants.acedns_star_save_online_offline_app_order_new_v2;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.d_instruction_;
import static org.forcepower.starcement.util.Utils.print_log_d;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import org.forcepower.starcement.NonScrollListView;
import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.OrderConfirmAdapter;
import org.forcepower.starcement.bean.ProductMasterDetails;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Map;

import cz.msebera.android.httpclient.Header;

public final class NewOrderConfirmationActivity extends AceDnsParentActivity
{
	private Activity mContext;
	private ArrayList <ProductMasterDetails> masterDetails = new ArrayList<>();

	@Override
	protected void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_new_order_confirmation);
		try
		{
			mContext = this;

			final TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
			tvHeaderText.setText("Order Confirmation");
			final ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
			ivHeaderBack.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					onBackPressed();
				}
			});

			//
			final NonScrollListView productListView = (NonScrollListView) findViewById(R.id.list_prod);
			for (final Map.Entry<String, ProductMasterDetails> entry : Constants.selectedProductMasterList.entrySet())
				masterDetails.add(Constants.selectedProductMasterList.get(entry.getKey()));

			productListView.setAdapter(new OrderConfirmAdapter(mContext, masterDetails));

			final TextView tv_ship_to = (TextView) findViewById(R.id.tv_ship_to);
			tv_ship_to.setText("Ship to : " + getIntent().getStringExtra("order_for_type"));

			final TextView tv_consign_name = (TextView) findViewById(R.id.tv_consign_name);
			tv_consign_name.setText(getIntent().getStringExtra("consignee_name"));

			final TextView tv_consignee_address = (TextView) findViewById(R.id.tv_consignee_address);
			tv_consignee_address.setText(getIntent().getStringExtra("consignee_address"));

			final TextView tv_Fright_ex_for = (TextView) findViewById(R.id.tv_Fright_ex_for);
			tv_Fright_ex_for.setText("Fright : " + getIntent().getStringExtra("freight")+ ", " + getIntent().getStringExtra("Remarks_text"));

			if(getIntent().getStringExtra("freight").equalsIgnoreCase("FOR") ||
					getIntent().getStringExtra("freight").equalsIgnoreCase("DOT"))
			{
				tv_Fright_ex_for.setText("Fright : " + getIntent().getStringExtra("freight") + ", " + getIntent().getStringExtra("Delivery_point") + ", " + getIntent().getStringExtra("Remarks_text"));
			}
			final TextView tv_Destination = (TextView) findViewById(R.id.tv_Destination);
			tv_Destination.setText(getIntent().getStringExtra("destination_address"));

			final TextView tv_Dump_y_n = (TextView) findViewById(R.id.tv_Dump_y_n);
			tv_Dump_y_n.setText("Dump Details : " + getIntent().getStringExtra("dump_status"));

			final TextView tv_Dump_Name = (TextView) findViewById(R.id.tv_Dump_Name);
			tv_Dump_Name.setText(getIntent().getStringExtra("dump_name"));

			final Button btn_cnf_order = (Button) findViewById(R.id.btn_cnf_order);
			btn_cnf_order.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View view) {
					confirm_order();
				}
			});
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}
	public void confirm_order()
	{
		try
		{
			if (HTTPUtils.isConnectionPossible(mContext))
			{
				showLoader();

				final RequestParams jsObject = new RequestParams();
				//order_data
				for(int count = 0; count< masterDetails.size(); count++)
				{
					jsObject.add("order_data["+ count +"][apporderno]", masterDetails.get(count).get_u_order_id() + "");
//					jsObject.add("erporderno", "");
					jsObject.add("order_data["+ count +"][erporderdt]", "");
					jsObject.add("order_data["+ count +"][order_for]", d_instruction_ + "");
					if(get_user_type(mContext).equalsIgnoreCase("broker"))
					{
						jsObject.add("order_data["+ count +"][customer_code]", get_selected_customer_code(mContext) + "");
					}
					else
					{
						jsObject.add("order_data["+ count +"][customer_code]", get_emp_or_customer_code(mContext) + "");
					}

					jsObject.add("order_data["+ count +"][prod_code]", masterDetails.get(count).getProdCode());
					jsObject.add("order_data["+ count +"][qty]", masterDetails.get(count).getQty());

					jsObject.add("order_data["+ count +"][destination_name]", getIntent().getStringExtra("destination_name"));
					jsObject.add("order_data["+ count +"][destination_code]", getIntent().getStringExtra("destination_code"));

					//freight, destination_address,phone_no,dump_status,dump_name
					jsObject.add("order_data["+ count +"][freight]", getIntent().getStringExtra("freight"));
					jsObject.add("order_data["+ count +"][destination_address]", getIntent().getStringExtra("destination_address"));
					jsObject.add("order_data["+ count +"][phone_no]", getIntent().getStringExtra("phone_no"));
					jsObject.add("order_data["+ count +"][dump_status]", getIntent().getStringExtra("dump_status"));
					jsObject.add("order_data["+ count +"][dump_name]", getIntent().getStringExtra("dump_name"));
					jsObject.add("order_data["+ count +"][dump_code]", getIntent().getStringExtra("dump_code"));
					jsObject.add("order_data["+ count +"][sub_dealer_code]", getIntent().getStringExtra("sub_dealer_code"));
					jsObject.add("order_data["+ count +"][dealer_truck]", getIntent().getStringExtra("dealer_truck"));
					jsObject.add("order_data["+ count +"][order_for_type]", getIntent().getStringExtra("order_for_type"));

					jsObject.add("order_data["+ count +"][Delivery_point]", getIntent().getStringExtra("Delivery_point"));
					jsObject.add("order_data["+ count +"][Remarks_text]", getIntent().getStringExtra("Remarks_text"));

				}

				jsObject.add("user_type", get_user_type(mContext));

				if(get_user_type(mContext).equalsIgnoreCase("broker"))
				{
					jsObject.add("login_user_id", get_dealer_id(mContext));
				}

				print_log_d("kro49_169_A ", acedns_star_save_online_offline_app_order_new_v2);
				print_log_d("kro49_169_P ", " PARAMS " + jsObject + "");



				final AsyncHttpClient client = new AsyncHttpClient();
				client.setTimeout(40 * 1000);
//        HttpsAsyncHttpClient(client);

				client.post(acedns_star_save_online_offline_app_order_new_v2, jsObject, new AsyncHttpResponseHandler()
				{
					@Override
					public void onSuccess(int statusCode, Header[] headers, byte[] responseBody)
					{
						String str = new String(responseBody);
						try
						{
							str = ApiRes(str);
							print_Log_d("kro49_order_API ", acedns_star_save_online_offline_app_order_new_v2+"");
							print_Log_d("kro49_order_R ", str+"");
							print_Log_d("kro49_order_PARAM ", jsObject+"");

							final JSONObject reader = new JSONObject(str);
							if(reader.has("process_status") &&
									reader.getString("process_status").equalsIgnoreCase("yes"))
							{
								msg_dialog(reader.optString("process_message"), true);
							}
							else
							{
								msg_dialog(reader.optString("process_message"), false);
							}

						}
						catch (Exception e)
						{
							e.printStackTrace();
							print_log_d("kro49_205 ",  e.toString());

							Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
							finish();
						}
						finally
						{
							Utils.cancelProgressDialog();
						}
					}

					@Override
					public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {

						Utils.cancelProgressDialog();
						msg_dialog("Please Contact with Admin.", false);
						print_log_d("kro49_211_ ",  error.toString());
					}


				});
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
	private void msg_dialog(String msg, boolean status)
	{
		final AlertDialog.Builder AlertDG = new AlertDialog.Builder(mContext, R.style.MyDialog);
		AlertDG.setTitle(getResources().getString(R.string.app_name));
		masterDetails.clear();
		AlertDG.setMessage(msg + "");
		AlertDG.setPositiveButton("Ok", new DialogInterface.OnClickListener() {

			public void onClick(DialogInterface dialog, int which) {
				Intent intent = new Intent(mContext, MenuActivity.class);
				intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
				startActivity(intent);

				finish();
			}
		});

		AlertDG.setCancelable(false);
		AlertDG.create().show();
	}
	public void showLoader()
	{
		Utils.showProgressDialog(mContext, "Please wait..");
	}
	public void dismissLoader()
	{
		Utils.cancelProgressDialog();
	}
}

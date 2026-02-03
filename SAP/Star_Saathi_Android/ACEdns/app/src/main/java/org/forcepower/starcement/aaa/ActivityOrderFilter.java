package org.forcepower.starcement.aaa;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;

import org.forcepower.starcement.custom.AsyncTaskCoroutine;
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
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.TextView;
import android.widget.Toast;
import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.Desti_Ex_For_Adapter;
import org.forcepower.starcement.adapter.DestinationAdapter;
import org.forcepower.starcement.adapter.DumpAdapter;
import org.forcepower.starcement.backgroundTask.TRANS_GetLogOut_Asynctask;
import org.forcepower.starcement.bean.DestinationMaster;
import org.forcepower.starcement.bean.DumpMaster;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.database.AceDnsDatabase;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.forcepower.starcement.util.commonAsyncTaskMaster;
import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.fpx_delaer_truck_list;
import static org.forcepower.starcement.constants.Constants.ship_to_party_master_txt_V1;
import static org.forcepower.starcement.constants.Constants.branch_code;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.cusomer_code_sub_dealer_new_logic;
import static org.forcepower.starcement.constants.Constants.d_instruction_;
import static org.forcepower.starcement.constants.Constants.downloadTableList;
import static org.forcepower.starcement.constants.Constants.dump_code;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.constants.Constants.selected_SAP_code;
import static org.forcepower.starcement.constants.Constants.sub_dealer_code;
import static org.forcepower.starcement.constants.Constants.dealer_truck;
import static org.forcepower.starcement.constants.Constants.sub_dealer_destination_new_logic;
import static org.forcepower.starcement.util.Utils.isValidIndianMobile;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;

public final class ActivityOrderFilter extends AceDnsParentActivity
{
	private AceDnsDatabase mAceDnsDatabase;
	private Activity mContext;

	private final ArrayList<DestinationMaster> mDealerSubDealerList = new ArrayList<>();
	private final ArrayList<DumpMaster> mDealerTruckList = new ArrayList<>();

	private EditText et_con_address, et_con_name, et_phone_no, et_destination_address,
			et_delivery_remarks;
	private TextView et_dump_name, tv_for_single_multiple, tv_dot_sing_multi;

	private RadioButton rb_freight_exw, rb_freight_for, rb_freight_dot,
			radio_ship_self, radio_ship_sub_dealer;
	private String order_for_type = "";
	private Handler mPrepareSurveyHandler;
	private int mCount = 0;
	private boolean checkedOnce = false;
	private LinearLayout ll_dump_details, ll_remarks, ll_for_dot_value;
	private CharSequence[] items = {"Multiple", "Single"};
	@Override
	public void onCreate(Bundle savedInstanceState)
	{

		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_order_filter);

		mContext = this;
		try
		{
			sub_dealer_destination_new_logic = true;
			if(get_user_type(mContext).equalsIgnoreCase("broker"))
			{
				selected_customr_code = get_selected_customer_code(mContext);
				selected_SAP_code = get_selected_dealer_sap_code(mContext);
			}
			else
			{
				selected_customr_code = get_emp_or_customer_code(mContext);
				selected_SAP_code = get_dealer_id(mContext);

			}

			mAceDnsDatabase = new AceDnsDatabase(mContext);

			ll_dump_details = (LinearLayout) findViewById(R.id.ll_dump_details);
			ll_dump_details.setVisibility(View.GONE);

			ll_remarks = (LinearLayout) findViewById(R.id.ll_remarks);

			et_destination_address = (EditText) findViewById(R.id.et_destination_address);
			et_con_name = (EditText) findViewById(R.id.et_con_name);
			et_phone_no = (EditText) findViewById(R.id.et_phone_no);
			et_dump_name = (TextView) findViewById(R.id.et_dump_name);
			et_con_address = (EditText) findViewById(R.id.et_con_address);
			et_delivery_remarks = (EditText) findViewById(R.id.et_delivery_remarks);
			tv_for_single_multiple = (TextView) findViewById(R.id.tv_for_single_multiple);
			tv_dot_sing_multi = (TextView) findViewById(R.id.tv_dot_sing_multi);
			ll_for_dot_value = (LinearLayout) findViewById(R.id.ll_for_dot_value);

			et_destination_address.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					final ArrayList<DestinationMaster> mDesti_MasterList = mAceDnsDatabase.getDesti_List();
					if(mDesti_MasterList.size()>0)
					{
						Show_Destination_List_Dialog(mDesti_MasterList);
					}
					else
					{
						Toast.makeText(mContext, "No destination found", Toast.LENGTH_SHORT).show();
					}
				}
			});
			final Button btn_continue = (Button) findViewById(R.id.btn_continue);
			btn_continue.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					try
					{
						if(HTTPUtils.isConnectionPossible(mContext))
						{
							if(order_for_type.matches(""))
							{
								Toast.makeText(mContext, "Please choose Ship To", Toast.LENGTH_SHORT).show();
							}
							else if(et_con_name.getText().toString().trim().equalsIgnoreCase(""))
							{
								Toast.makeText(mContext, "Please enter consignee name", Toast.LENGTH_SHORT).show();
							}
							else if(et_con_address.getText().toString().trim().equalsIgnoreCase(""))
							{
								Toast.makeText(mContext, "Please enter consignee address", Toast.LENGTH_SHORT).show();
							}
							else if(et_destination_address.getText().toString().trim().equalsIgnoreCase(""))
							{
								Toast.makeText(mContext, "Please select destination address", Toast.LENGTH_SHORT).show();
							}
							else if(Constants.mFreightComponent.matches(""))
							{
								Toast.makeText(mContext, "Please choose Freight", Toast.LENGTH_SHORT).show();
							}
							else if(!isValidIndianMobile(et_phone_no.getText().toString().trim()))
							{
								Toast.makeText(mContext, "Please enter a valid mobile number", Toast.LENGTH_SHORT).show();
							}
							else if(!(rb_freight_for.isChecked() || rb_freight_exw.isChecked() /*|| rb_freight_dot.isChecked()*/))
							{
								Toast.makeText(mContext, "Please choose FOR or EXW ", Toast.LENGTH_SHORT).show();
							}
							else if(rb_freight_for.isChecked() && tv_for_single_multiple.getText().toString().trim().matches(""))
							{
								Toast.makeText(mContext, "Please choose FOR Multiple/Single", Toast.LENGTH_SHORT).show();
							}
							else if(rb_freight_exw.isChecked() && et_dump_name.getText().toString().trim().matches(""))
							{
								Toast.makeText(mContext, "Please choose Dump Name", Toast.LENGTH_SHORT).show();
							}
//							else if(rb_freight_dot.isChecked() && tv_dot_sing_multi.getText().toString().trim().matches(""))
//							{
//								Toast.makeText(mContext, "Please choose DOT single/multiple", Toast.LENGTH_SHORT).show();
//							}
							else
							{
								if(checkedOnce)
								{
									continueOrder();
								}
								else
								{
									final AlertDialog.Builder AlertDG = new AlertDialog.Builder(mContext, R.style.MyDialog);
									AlertDG.setMessage("Please confirm the CONSIGNEE ADDRESS as per the DESTINATION ADDRESS selected.");
									AlertDG.setPositiveButton("Ok", new DialogInterface.OnClickListener() {

										public void onClick(DialogInterface dialog, int which) {
											et_con_address.requestFocus();
											checkedOnce = true;
										}
									});

									AlertDG.setCancelable(false);
									AlertDG.create().show();
								}
							}
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
			});

			rb_freight_for = (RadioButton) findViewById(R.id.rb_freight_for);
			rb_freight_for.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					Constants.mFreightComponent="FOR";
					et_dump_name.setText("");
					et_dump_name.setTag("");

					single_multiple_dialog("FOR");

					rb_freight_exw.setChecked(false);
					rb_freight_dot.setChecked(false);

					ll_for_dot_value.setVisibility(View.VISIBLE);
					ll_remarks.setVisibility(View.VISIBLE);
					ll_dump_details.setVisibility(View.GONE);

					tv_for_single_multiple.setText("");
					tv_dot_sing_multi.setText("");
				}
			});

			rb_freight_exw = (RadioButton) findViewById(R.id.rb_freight_exw);
			rb_freight_exw.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					Constants.mFreightComponent="EXW";
					tv_for_single_multiple.setText("");
					tv_dot_sing_multi.setText("");
					et_delivery_remarks.setText("");

					rb_freight_for.setChecked(false);
					rb_freight_dot.setChecked(false);

					ll_for_dot_value.setVisibility(View.GONE);
					ll_remarks.setVisibility(View.GONE);
					ll_dump_details.setVisibility(View.VISIBLE);
				}
			});

			rb_freight_dot = (RadioButton) findViewById(R.id.rb_freight_dot);
			rb_freight_dot.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					Constants.mFreightComponent="DOT";

					et_dump_name.setText("");
					et_dump_name.setTag("");

					ll_for_dot_value.setVisibility(View.VISIBLE);
					ll_remarks.setVisibility(View.VISIBLE);
					ll_dump_details.setVisibility(View.GONE);

					tv_for_single_multiple.setText("");
					tv_dot_sing_multi.setText("");
			

					single_multiple_dialog("DOT");

					rb_freight_exw.setChecked(false);
					rb_freight_for.setChecked(false);
				}
			});

			radio_ship_self = (RadioButton) findViewById(R.id.radio_ship_self);
			radio_ship_self.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					try
					{
						radio_ship_sub_dealer.setChecked(false);
						checkedOnce = false;
						mAceDnsDatabase.TruncateTableByTableName("destination_master");
						mAceDnsDatabase.TruncateTableByTableName("branch_dump");

						if(HTTPUtils.isConnectionPossible(mContext))
						{
							et_destination_address.setText("");
							et_destination_address.setTag("");

							sub_dealer_code = "";
							order_for_type = "Self";

							new get_Shio_To_Party_Master_Asynctask("dealer").execute();
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
			});
			radio_ship_sub_dealer = (RadioButton) findViewById(R.id.radio_ship_sub_dealer);
			radio_ship_sub_dealer.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					try
					{
						radio_ship_self.setChecked(false);
						checkedOnce = false;
						mAceDnsDatabase.TruncateTableByTableName("destination_master");
						mAceDnsDatabase.TruncateTableByTableName("branch_dump");

						if(HTTPUtils.isConnectionPossible(mContext))
						{

							et_destination_address.setText("");
							et_destination_address.setTag("");

							et_con_address.setText("");
							et_con_name.setText("");
							et_phone_no.setText("");

							sub_dealer_code = "";
							order_for_type = "Sub Dealer";
							new get_Shio_To_Party_Master_Asynctask("sub dealer").execute();

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
			});

			ll_dump_details.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					try
					{
						if(HTTPUtils.isConnectionPossible(mContext))
						{
							final ArrayList<DumpMaster> mDesti_MasterList = mAceDnsDatabase.getDesti_Dump_List();
							if(mDesti_MasterList.size()>0)
							{
								dump_code = "";
								ShowDesti_Dump_Dialog(mDesti_MasterList);
							}
							else
							{
								Toast.makeText(mContext, "No dump details found", Toast.LENGTH_SHORT).show();
							}
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
			});

			ImageView back = (ImageView) findViewById(R.id.back);
			back.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					onBackPressed();
				}
			});

			if(branch_code.equalsIgnoreCase("B0042"))
			{
				final LinearLayout ll_dealer_truck = (LinearLayout) findViewById(R.id.ll_dealer_truck);
//				ll_dealer_truck.setVisibility(View.VISIBLE);
				final RadioButton rb_truck_yes = (RadioButton) findViewById(R.id.rb_truck_yes);
				final RadioButton rb_truck_no = (RadioButton) findViewById(R.id.rb_truck_no);
				rb_truck_yes.setOnClickListener(new OnClickListener() {
					@Override
					public void onClick(View v) {
						rb_truck_no.setChecked(false);
						rb_truck_yes.setChecked(true);
						dealer_truck ="YES";
					}
				});

				rb_truck_no.setOnClickListener(new OnClickListener() {
					@Override
					public void onClick(View v) {
						rb_truck_no.setChecked(true);
						rb_truck_yes.setChecked(false);
						dealer_truck = "NO";
					}
				});

			}

			mPrepareSurveyHandler = new Handler(Looper.myLooper()) {
				public void handleMessage(Message threadmsg) {
					//mPrepareSurveyProgressDialog.dismiss();
					final int listcount = threadmsg.getData().getInt("JOBALLOCATE");
					runOnUiThread(new Runnable() {
						public void run() {
							if(listcount==downloadTableList.size()-1)
							{
								Utils.changeProgressDialogMsg(mContext, "Successfully updated");
								dismissLoader();

								if(order_for_type.equalsIgnoreCase("Self"))
								{
									if(mDealerSubDealerList.size() == 0)
									{
										et_con_name.setText(mAceDnsDatabase.getValueFromKey("customer_name", selected_customr_code));
										et_con_address.setText(mAceDnsDatabase.getValueFromKey("address", selected_customr_code));
										et_phone_no.setText(mAceDnsDatabase.getValueFromKey("phone_no", selected_customr_code));
									}
								}

								//
								final ArrayList<DestinationMaster> mDesti_MasterList = mAceDnsDatabase.getDesti_List();
								if(mDesti_MasterList.size() > 0)
								{
									et_destination_address.setText(mDesti_MasterList.get(0).getDestinationName());
									et_destination_address.setTag(mDesti_MasterList.get(0).getDestinationCode());
								}
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
			if(HTTPUtils.isConnectionPossible(mContext))
			{
				new GetTruckDetails_Asynctask().execute();
			}
			else
			{
				show_msg_alert(mContext, check_internet_connection, true);
			}
		}
	}

	public void single_multiple_dialog(final String type)
	{
		try
		{
			AlertDialog.Builder AlertDG = new AlertDialog.Builder(mContext, R.style.MyDialog);

			TextView tvCPopup = new TextView(mContext);
			tvCPopup.setText("Point of Delivery");
			tvCPopup.setGravity(Gravity.CENTER);
			tvCPopup.setTextColor(mContext.getResources().getColor(R.color.white));
			tvCPopup.setTextSize(14);
			tvCPopup.setBackgroundColor(mContext.getResources().getColor(R.color.red));
			int margin = 15;
			tvCPopup.setPadding(0, margin*2, 0, margin*2);
			AlertDG.setCustomTitle(tvCPopup);




			AlertDG.setItems(items, new DialogInterface.OnClickListener() {
				@Override
				public void onClick(DialogInterface dialog, int item) {
//					if(type.equalsIgnoreCase("DOT"))
//					{
//						tv_dot_sing_multi.setText(items[item].toString());
//					}
//					else
//					{
						tv_for_single_multiple.setText(items[item].toString());
//					}
					dialog.dismiss();
				}
			});
//			AlertDG.setNegativeButton("Cancel", new DialogInterface.OnClickListener() {
//
//				public void onClick(DialogInterface dialog, int which) {
//					dialog.dismiss();
//				}
//			});
			AlertDG.setCancelable(false);
			AlertDG.create().show();
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	public void showLoader()
	{
		Utils.showProgressDialog(mContext, "Updating please wait..");
	}
	public void dismissLoader()
	{
		Utils.cancelProgressDialog();
	}
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

	public void ShowDesti_Dump_Dialog(final ArrayList<DumpMaster> mDumpList)
	{
		try
		{
			final Dialog mDumpDialog = new Dialog(mContext, android.R.style.Theme_DeviceDefault_Light_NoActionBar);
			mDumpDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
			Window window = mDumpDialog.getWindow();
			window.setGravity(Gravity.CENTER);
			window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.MATCH_PARENT);
			mDumpDialog.setContentView(R.layout.select_with_search);
			mDumpDialog.setCancelable(true);
			window.setStatusBarColor(mContext.getResources().getColor(R.color.colorRed_StatusBar));
			ImageView btnback = (ImageView) mDumpDialog.findViewById(R.id.back);
			btnback.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					mDumpDialog.dismiss();
				}
			});
			TextView tvDestHeading = (TextView) mDumpDialog.findViewById(R.id.tvDestHeading);
			tvDestHeading.setText("SELECT DUMP");
			TextView title = (TextView) mDumpDialog.findViewById(R.id.tvDestHeading);
			title.setText("Please select a dump");
			ListView dialogList = (ListView) mDumpDialog.findViewById(R.id.list);

			final DumpAdapter destinationAdapter = new DumpAdapter(this,R.layout.customer_broker_list_child, mDumpList);

			dialogList.setAdapter(destinationAdapter);

			EditText searchText = (EditText) mDumpDialog
					.findViewById(R.id.autoCompleteTextView1);
			searchText.addTextChangedListener(new TextWatcher() {
				@Override
				public void onTextChanged(CharSequence s, int arg1, int arg2,
										  int arg3) {
					destinationAdapter.getFilter().filter(s.toString());
				}

				@Override
				public void beforeTextChanged(CharSequence arg0, int arg1,
											  int arg2, int arg3) {
				}

				@Override
				public void afterTextChanged(Editable s) {
				}
			});

			dialogList.setOnItemClickListener(new OnItemClickListener() {
				@Override
				public void onItemClick(AdapterView<?> arg0, View arg1,
										int position, long arg3) {
					getWindow()
							.setSoftInputMode(
									WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);

					et_dump_name.setText(mDumpList.get(position).get_dump_name());
					et_dump_name.setTag(mDumpList.get(position).get_dump_code());
					dump_code = mDumpList.get(position).get_dump_code();
					mDumpDialog.dismiss();
				}
			});

			mDumpDialog.show();
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	public void continueOrder()
	{
		try
		{
			d_instruction_ = et_con_name.getText().toString() + ", " + et_con_address.getText().toString();

			Intent intent = new Intent(getApplicationContext(), NewOrderConfirmationActivity.class);
			//freight, destination_address,phone_no,dump_status,dump_name
			intent.putExtra("consignee_name", et_con_name.getText().toString()+"");
			intent.putExtra("consignee_address", et_con_address.getText().toString().trim()+"");

			intent.putExtra("freight", Constants.mFreightComponent+"");
			intent.putExtra("destination_address", et_destination_address.getText().toString().trim());
			intent.putExtra("phone_no", et_phone_no.getText().toString());
			if(et_dump_name.getText().toString().matches(""))
			{
				intent.putExtra("dump_status", "NO");
			}
			else
			{
				intent.putExtra("dump_status", "YES");
			}

			intent.putExtra("dump_name", et_dump_name.getText().toString());
			intent.putExtra("dump_code", et_dump_name.getTag().toString());
			intent.putExtra("sub_dealer_code", sub_dealer_code+"");
			intent.putExtra("dealer_truck", dealer_truck+"");
			intent.putExtra("order_for_type", order_for_type+""); //ship-to
			intent.putExtra("destination_name", et_destination_address.getText().toString().trim());
			intent.putExtra("destination_code", et_destination_address.getTag().toString().trim());


			intent.putExtra("Remarks_text", et_delivery_remarks.getText().toString().trim());

			if(Constants.mFreightComponent.equalsIgnoreCase("DOT"))
			{
				intent.putExtra("Delivery_point", tv_dot_sing_multi.getText().toString().trim());
			}
			else
			{
				intent.putExtra("Delivery_point", tv_for_single_multiple.getText().toString().trim());
			}

			if(selected_customr_code != null && selected_customr_code.trim().length() > 0)
			{
				startActivity(intent);
			}
			else
			{
				if (HTTPUtils.isConnectionPossible(mContext))
				{
					new TRANS_GetLogOut_Asynctask(mContext).execute("");
				}
				else
				{
					Utils.showToast(mContext, check_internet_connection);
				}
			}
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	public void Show_Destination_List_Dialog(final ArrayList<DestinationMaster> mDesti_MasterList)
	{
		try
		{
			final Dialog mDestinationDialog = new Dialog(mContext, android.R.style.Theme_DeviceDefault_Light_NoActionBar);
			mDestinationDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
			Window window = mDestinationDialog.getWindow();
			window.setGravity(Gravity.CENTER);
			window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.MATCH_PARENT);
			mDestinationDialog.setContentView(R.layout.select_with_search);
			mDestinationDialog.setCancelable(true);
			window.setStatusBarColor(mContext.getResources().getColor(R.color.colorRed_StatusBar));
			ImageView btnback = (ImageView) mDestinationDialog.findViewById(R.id.back);
			btnback.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					mDestinationDialog.dismiss();
				}
			});
			TextView tvDestHeading = (TextView) mDestinationDialog.findViewById(R.id.tvDestHeading);
			tvDestHeading.setText("SELECT DESTINATION");
			TextView title = (TextView) mDestinationDialog.findViewById(R.id.tvDestHeading);
			title.setText("Please select a destination");
			ListView dialogList = (ListView) mDestinationDialog.findViewById(R.id.list);

			final Desti_Ex_For_Adapter destinationAdapter = new Desti_Ex_For_Adapter(this,R.layout.customer_broker_list_child,mDesti_MasterList);

			dialogList.setAdapter(destinationAdapter);

			EditText searchText = (EditText) mDestinationDialog
					.findViewById(R.id.autoCompleteTextView1);
			searchText.addTextChangedListener(new TextWatcher() {
				@Override
				public void onTextChanged(CharSequence s, int arg1, int arg2,
										  int arg3) {
					destinationAdapter.getFilter().filter(s.toString());
				}

				@Override
				public void beforeTextChanged(CharSequence arg0, int arg1,
											  int arg2, int arg3) {
				}

				@Override
				public void afterTextChanged(Editable s) {
				}
			});

			dialogList.setOnItemClickListener(new OnItemClickListener() {
				@Override
				public void onItemClick(AdapterView<?> arg0, View arg1,
										int position, long arg3) {
					getWindow()
							.setSoftInputMode(
									WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);


					et_destination_address.setText(destinationAdapter.getItem(position).getDestinationName());
					et_destination_address.setTag(destinationAdapter.getItem(position).getDestinationCode());
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


	public final class get_Shio_To_Party_Master_Asynctask extends AsyncTaskCoroutine<String, String>
	{
		private String user_type = "", process_message = "";
		private ProgressDialog mStepProgressDialog;

		public get_Shio_To_Party_Master_Asynctask(final String user_type) {
			this.user_type = user_type;
		}

		@Override
		public void onPreExecute()
		{
			super.onPreExecute();
			mStepProgressDialog = new ProgressDialog(mContext);
			mStepProgressDialog.setMessage("Please wait..");
			mStepProgressDialog.setCancelable(false);
			mStepProgressDialog.show();
			mDealerSubDealerList.clear();
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
						final String url = ship_to_party_master_txt_V1;
						print_log_d("ship_to_party_master_txt_V1 ", url);
						final ArrayList<NameValuePair> mHttpParamPairs = new ArrayList<>(2);
						mHttpParamPairs.add(new BasicNameValuePair("emp_code", selected_customr_code));
						mHttpParamPairs.add(new BasicNameValuePair("user_type", user_type));
						mHttpParamPairs.add(new BasicNameValuePair("login_type", get_user_type(mContext)));

						POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, mHttpParamPairs);
						print_log_d("login_type_669 ", get_user_type(mContext));
						print_log_d("ship_to_party_url ", url);
						print_log_d("ship_to_party ", POST_result);
						print_log_d("ship_to_party_mHttpParamPairs ", mHttpParamPairs.toString());
						print_log_d("ship_to_party_selected_customr_code ", selected_customr_code);
						print_log_d("ship_to_party_get_emp_or_customer_code ", get_emp_or_customer_code(mContext));

						final JSONObject jo = new JSONObject(POST_result);
						if(jo.optString("process_status").equalsIgnoreCase("YES"))
						{
							String dealer_data = "";
							if(jo.has("dealer_data"))
								dealer_data = jo.getString("dealer_data");

							if(jo.has("sub_dealer_data"))
								dealer_data = jo.getString("sub_dealer_data");

							final JSONArray jsonArray = new JSONArray(dealer_data);
							print_log_d("ship_to_party_SIZE ", jsonArray.length() + "");


							for(int i=0; i<jsonArray.length(); i++)
							{
								final JSONObject e = jsonArray.getJSONObject(i);
								final DestinationMaster dM = new DestinationMaster();
								dM.setRow_position(i);
								dM.setDestinationName(e.optString("customer_name"));
								dM.setDestinationCode(e.optString("customer_code"));
								dM.setSubDealerCode(e.optString("customer_code"));
								dM.set_phone_no(e.optString("phone_no"));
								dM.set_address(e.optString("address"));
								mDealerSubDealerList.add(dM);
							}
						}
						else
						{
							process_message = jo.optString("process_message");
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
				if(mDealerSubDealerList.size() == 0)
				{
					Toast.makeText(mContext, process_message, Toast.LENGTH_SHORT).show();
					if(user_type.equalsIgnoreCase("dealer"))
					{
						mCount = 0;
						cusomer_code_sub_dealer_new_logic = selected_customr_code;

						downloadTableList.clear();
						downloadTableList.add("destination_master");
						downloadTableList.add("branch_dump");
						DownloadData(mCount, downloadTableList.get(mCount));

						et_con_name.setText(mAceDnsDatabase.getValueFromKey("customer_name", selected_customr_code));
						et_con_address.setText(mAceDnsDatabase.getValueFromKey("address", selected_customr_code));
						et_phone_no.setText(mAceDnsDatabase.getValueFromKey("phone_no", selected_customr_code));
					}
					else if(user_type.equalsIgnoreCase("sub dealer"))
					{
						mDealerSubDealerList.clear();
						showDealerSubDealerList(user_type, mDealerSubDealerList);
					}
				}
				else
				{
					showDealerSubDealerList(user_type, mDealerSubDealerList);
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
	public void showDealerSubDealerList(final String user_type, final ArrayList<DestinationMaster> mDealerSubDealerList)
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
			final ImageView btnback = (ImageView) mDestinationDialog.findViewById(R.id.back);
			btnback.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					mDestinationDialog.dismiss();
				}
			});
			TextView title = (TextView) mDestinationDialog.findViewById(R.id.tvDestHeading);
			title.setText("Please select a "+user_type);
			ListView dialogList = (ListView) mDestinationDialog.findViewById(R.id.list);

			ArrayList<DestinationMaster> mDestinationMasterList = new ArrayList<>();

			if(user_type.equalsIgnoreCase("sub dealer"))
			{
				mDestinationMasterList = mAceDnsDatabase.getMySubDealerList("");
			}

			mDestinationMasterList.addAll(mDealerSubDealerList);

			final DestinationAdapter destinationAdapter = new DestinationAdapter(this,R.layout.customer_broker_list_child, mDestinationMasterList);

			dialogList.setAdapter(destinationAdapter);

			final EditText searchText = (EditText) mDestinationDialog
					.findViewById(R.id.autoCompleteTextView1);
			searchText.addTextChangedListener(new TextWatcher() {
				@Override
				public void onTextChanged(CharSequence s, int arg1, int arg2,
										  int arg3) {
					destinationAdapter.getFilter().filter(s.toString());
				}

				@Override
				public void beforeTextChanged(CharSequence arg0, int arg1,
											  int arg2, int arg3) {
				}

				@Override
				public void afterTextChanged(Editable s) {
				}
			});

			dialogList.setOnItemClickListener(new OnItemClickListener() {
				@Override
				public void onItemClick(AdapterView<?> arg0, View arg1,
										int position, long arg3) {
					getWindow()
							.setSoftInputMode(
									WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
					Constants.selectedDestination = destinationAdapter.getItem(position);
					Constants.mDestinationCode=Constants.selectedDestination.getDestinationCode();
					sub_dealer_code = destinationAdapter.getItem(position).getSubDealerCode();
					et_con_name.setText(destinationAdapter.getItem(position).getDestinationName());
					et_con_address.setText(destinationAdapter.getItem(position).get_address());
					et_phone_no.setText(destinationAdapter.getItem(position).get_phone_no());


					et_con_name.setEnabled(false);

					mCount = 0;
					cusomer_code_sub_dealer_new_logic = destinationAdapter.getItem(position).getSubDealerCode();

					downloadTableList.clear();
					downloadTableList.add("destination_master");
					downloadTableList.add("branch_dump");
					DownloadData(mCount, downloadTableList.get(mCount));


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
	public final class GetTruckDetails_Asynctask extends AsyncTaskCoroutine<String, String>
	{
		private ProgressDialog mStepProgressDialog;

		public GetTruckDetails_Asynctask() {
		}

		@Override
		public void onPreExecute()
		{
			super.onPreExecute();
			mStepProgressDialog = new ProgressDialog(mContext);
			mStepProgressDialog.setMessage("Please wait..");
			mStepProgressDialog.setCancelable(false);
			mStepProgressDialog.show();
			mDealerTruckList.clear();
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
						final String url = fpx_delaer_truck_list;

						final ArrayList<NameValuePair> mHttpParamPairs = new ArrayList<>(2);


						mHttpParamPairs.add(new BasicNameValuePair("customer_code", selected_SAP_code));

						POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, mHttpParamPairs);

						print_log_d("iru774_U ", url);
						print_log_d("iru774_R ", POST_result);
						print_log_d("iru774_P ", mHttpParamPairs.toString());

						final JSONObject jo = new JSONObject(POST_result);
						if(jo.optString("process_status").equalsIgnoreCase("YES"))
						{
							final String truck_data = jo.getString("truck_data");

							final JSONArray jsonArray = new JSONArray(truck_data);
							print_log_d("iru774_SIZE ", jsonArray.length() + "");

							for(int i=0; i<jsonArray.length(); i++)
							{
								final JSONObject e = jsonArray.getJSONObject(i);
								final DumpMaster dM = new DumpMaster();
								dM.set_dump_code(e.optString("truck_no"));
								dM.set_dump_name(e.optString("truck_no"));
								mDealerTruckList.add(dM);
							}
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
				if(mDealerTruckList.isEmpty())
				{
					rb_freight_dot.setVisibility(View.INVISIBLE);
					items = new CharSequence[]{"Multiple", "Single"};
				}
				else
				{
					items = new CharSequence[]{"Multiple", "Single", "DOT"};
					rb_freight_dot.setVisibility(View.VISIBLE);
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

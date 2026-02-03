package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.Utils_.print_Log_d;
import static org.forcepower.starcement.constants.Constants.ajax_allocation_lifting_invoicewise;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.os.Bundle;
import android.os.Handler;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.DealerLiftingAllocatedAdapter_Date;
import org.forcepower.starcement.bean.AllocationListModel;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.MonthYearPickerDialog;
import org.forcepower.starcement.util.MyCallback;
import org.forcepower.starcement.util.Utils;
import org.forcepower.starcement.util.VolleyApiCAll;
import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

public final class SubDealerShowAllocateActivity extends AceDnsParentActivity
{
	private Activity mContext;
	private ArrayList<AllocationListModel> images = new ArrayList<>();
	private DealerLiftingAllocatedAdapter_Date mAdapter;
	private RecyclerView rv_Pending;
	private int page_no_P = 1;
	protected Handler handler_p;
	// The minimum amount of items to have below your current scroll position
	// before loading more.
	private final int visibleThreshold = 5;
	private int lastVisibleItem, totalItemCount;
	private boolean loading;
	private MyCallback callback;

	private String year_month = "";
	private EditText et_SearchSD;

	public void setLoaded() {
		loading = false;
	}

	public void setLoading() {
		loading = true;
	}

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_show_sub_dealer_allocaton);
		try
		{
			mContext = this;

			final TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
			tvHeaderText.setText("Allocation History");

			et_SearchSD = (EditText) findViewById(R.id.et_SearchSD);
			et_SearchSD.setHint("Search by counter name...");

			final ImageView ivMonthSelection = (ImageView) findViewById(R.id.ivMonthSelection);
			final ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
			ivHeaderBack.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					onBackPressed();
				}
			});
			final ImageView ivCrossSD = (ImageView) findViewById(R.id.ivCrossSD);
			ivCrossSD.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					et_SearchSD.setText("");
				}
			});

			handler_p = new Handler();

			rv_Pending = (RecyclerView) findViewById(R.id.recycler_view);
			rv_Pending.setHasFixedSize(true);
			rv_Pending.setLayoutManager(new LinearLayoutManager(mContext));

			images = new ArrayList<>();
			mAdapter = new DealerLiftingAllocatedAdapter_Date(mContext, images);
			rv_Pending.setAdapter(mAdapter);


			callback = new MyCallback() {

				public void callbackCall() {
					// callback code goes here
					onLoadMore_();
				}
			};
			if (rv_Pending.getLayoutManager() instanceof final LinearLayoutManager linearLayoutManager)
			{
				rv_Pending
						.addOnScrollListener(new RecyclerView.OnScrollListener() {
							@Override
							public void onScrolled(@NonNull RecyclerView recyclerView,
												   int dx, int dy) {
								super.onScrolled(recyclerView, dx, dy);

								totalItemCount = linearLayoutManager.getItemCount();
								lastVisibleItem = linearLayoutManager
										.findLastVisibleItemPosition();
								if(totalItemCount > 9)
									if (!loading
											&& totalItemCount <= (lastVisibleItem + visibleThreshold)) {
										// End has been reached
										// Do something
										callback.callbackCall();

										loading = true;
									}
							}
						});
			}

			ivMonthSelection.setOnClickListener(new View.OnClickListener() {
				@Override
				public void onClick(View view) {
					try
					{
						final MonthYearPickerDialog pd = new MonthYearPickerDialog();
						pd.setListener(new DatePickerDialog.OnDateSetListener() {
							@Override
							public void onDateSet(DatePicker view, int selectedYear, int selectedMonth, int selectedDay) {
								if (HTTPUtils.isConnectionPossible(mContext))
								{
									final String header_txt = Utils.changeDateFormat( "yyyy-MM", "MMM, yyyy", selectedYear + "-" + selectedMonth);
									tvHeaderText.setText("Allocation History ("+header_txt+")");
//                                    final String yyyyMM = Utils.changeDateFormat( "yyyy-MM", "yyyy-MM", selectedYear + "-" + selectedMonth);
									year_month = Utils.changeDateFormat( "yyyy-MM", "yyyy-MM", selectedYear + "-" + selectedMonth);
									et_SearchSD.setText("");
									reload();
								}
								else
								{
									show_msg_alert(mContext,check_internet_connection, false);
								}
							}
						});
						//
						pd.show(getSupportFragmentManager(), "MonthYearPickerDialog");
					}
					catch (Exception e)
					{
						e.printStackTrace();
					}
				}
			});

			//
			et_SearchSD.addTextChangedListener(new TextWatcher() {
				@Override
				public void onTextChanged(CharSequence s, int arg1, int arg2,
										  int arg3) {
					try
					{
						final ArrayList<AllocationListModel> temp = new ArrayList<>();

						for(int i = 0; i<images.size(); i++)
						{
							if(images.get(i).getCounter_name().toLowerCase().contains(s.toString().toLowerCase()))
							{
								temp.add(images.get(i));
							}
						}

						mAdapter.setFilter(temp);
					}
					catch (Exception e)
					{
						e.printStackTrace();
					}
				}

				@Override
				public void beforeTextChanged(CharSequence arg0, int arg1,
											  int arg2, int arg3) {
				}

				@Override
				public void afterTextChanged(Editable s) {
				}
			});
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
		finally
		{
			reload();
		}
	}

	public void reload() {
		try
		{
			if (HTTPUtils.isConnectionPossible(mContext))
			{
				final Map<String, String> jsonObject = new HashMap<>();

				jsonObject.put("customer_id", get_dealer_id(mContext));
				jsonObject.put("year_month", year_month); //vola2715 not confirmed yet

				if(get_user_type(mContext).equalsIgnoreCase("dealer") ||
						get_user_type(mContext).equalsIgnoreCase("broker"))
				{
					jsonObject.put("user_type", "DEALER");
				}
				else
				{
					jsonObject.put("user_type", "RSSD");
				}

				page_no_P = 1;
				images = new ArrayList<>();
				mAdapter = new DealerLiftingAllocatedAdapter_Date(mContext, images);
				rv_Pending.setAdapter(mAdapter);
				jsonObject.put("page_no", page_no_P + "");

				_doPOSTcall_(jsonObject, "initial");
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

	private void _doPOSTcall_(final Map<String, String> jsonObject, final String type) {
		try
		{
			if(page_no_P == 1)
				Utils.showProgressDialog(mContext, "");

			final String page = jsonObject.get("page_no") + "";
			print_Log_d("k3drrt_page ", page+"");
			print_Log_d("k3drrt_jsonObject ", jsonObject+"");

			final String url = ajax_allocation_lifting_invoicewise;
			//making post call
			final VolleyApiCAll volleyApiCAll = new VolleyApiCAll(mContext);
			volleyApiCAll.makeServiceCallPost_Time(jsonObject, url, new VolleyApiCAll.VolleyCallback()
			{
				@Override
				public void onSuccessResponse(String result)
				{
					try
					{
						images = new ArrayList<>();
						JSONObject jo = new JSONObject();

						print_Log_d("k3dr4t_URL ", url+"");
						print_Log_d("k3dr4t_PRARAM ", jsonObject+"");
						print_Log_d("k3dr4t_RES ", result+"");

						if(type.matches("add_p"))
						{
							//   remove progress item
							images.remove(images.size() - 1);
							mAdapter.notifyItemRemoved(images.size());
						}

						if(result.matches("VOLLEY_NETWORK_ERROR"))
						{
							Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
						}
						else
						{
							try
							{
								jo = new JSONObject(result);
								if(jo.has("process_status"))
								{
									if(jo.getString("process_status").toLowerCase().matches("yes"))
									{
										final String allocation_data  = jo.getString("allocation_data");
										final JSONArray jsonArray = new JSONArray(allocation_data );

										for (int i = 0; i < jsonArray.length() ; i++)
										{
											final JSONObject e = jsonArray.getJSONObject(i);
											final AllocationListModel lM = new AllocationListModel();

											//prod_desc,allocation_qty,date_and_time
											lM.setDns_prod_code(e.optString("dns_prod_code"));
											lM.setProd_desc(e.optString("prod_desc"));
											lM.setAllocation_qty(e.optString("allocation_qty"));
											lM.setDate_and_time(e.optString("date_and_time"));
											lM.setOrder_id(e.optString("order_id"));
											lM.setChallan_no(e.optString("inv_no"));
											lM.setChallan_date(e.optString("inv_date"));
											lM.setCounter_name(e.optString("counter_name"));


											images.add(lM);
										}

									}
								}
							}
							catch (Exception e)
							{
								e.printStackTrace();
							}
							finally
							{

								if(page_no_P == 1 && jo.optString("process_status").equalsIgnoreCase("NO"))
									show_msg_alert(mContext, jo.optString("process_message"), false);
								page_no_P++;
								if(mAdapter.getItemCount() == 0)
								{
									mAdapter.setFilter(images);
								}
								else if(images.size() > 0)
								{
									mAdapter.notifyItemInserted(images.size());
								}

								if(mAdapter.getItemCount() > 0)
								{
									rv_Pending.setVisibility(View.VISIBLE);
								}
								else
								{
									rv_Pending.setVisibility(View.GONE);
								}
							}
						}

					}
					catch (Exception e)
					{
						e.printStackTrace();
					}
					finally
					{
						//
						setLoaded();
						Utils.cancelProgressDialog();
					}
				}
			});

		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	public void onLoadMore_() {
		try
		{
			if(page_no_P > 1)
			{
				images.add(null);
				mAdapter.notifyItemInserted(images.size() - 1);

				handler_p.postDelayed(new Runnable() {
					@Override
					public void run() {
						final Map<String, String> jsonObject = new HashMap<>();

						jsonObject.put("customer_id", get_dealer_id(mContext));

						if(get_user_type(mContext).equalsIgnoreCase("dealer") ||
								get_user_type(mContext).equalsIgnoreCase("broker"))
						{
							jsonObject.put("user_type", "DEALER");
						}
						else
						{
							jsonObject.put("user_type", "RSSD");
						}

						jsonObject.put("page_no", page_no_P + "");

						_doPOSTcall_(jsonObject, "add_p");
						//or you can add all at once but do not forget to call pAdapter.notifyDataSetChanged();
					}
				}, 2000);
			}
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}
}

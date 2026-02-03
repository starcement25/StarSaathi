package org.forcepower.starcement.aaa;


import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.item_wise_target_achievement_SAP_data;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.print_log_d;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import android.app.Activity;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.graphics.Color;
import android.os.Bundle;
import android.view.Gravity;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;

import androidx.core.content.ContextCompat;
import com.github.mikephil.charting_.charts.BarChart;
import com.github.mikephil.charting_.components.Legend;
import com.github.mikephil.charting_.components.XAxis;
import com.github.mikephil.charting_.components.YAxis;
import com.github.mikephil.charting_.data.BarData;
import com.github.mikephil.charting_.data.BarDataSet;
import com.github.mikephil.charting_.data.BarEntry;
import com.github.mikephil.charting_.data.Entry;
import com.github.mikephil.charting_.highlight.Highlight;
import com.github.mikephil.charting_.listener.OnChartValueSelectedListener;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.MonthAdapter;
import org.forcepower.starcement.adapter.ProductGraphAdapter;
import org.forcepower.starcement.bean.KeyValue;
import org.forcepower.starcement.bean.SelfAppraisalDetailsProductGroupWise;
import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.Utils;
import org.json.JSONArray;
import org.json.JSONObject;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.List;
import java.util.Locale;

import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;


public final class ProductWiseGraphActivity extends AceDnsParentActivity
{
	private BarChart chart;
	private ArrayList<BarEntry> BARENTRYTarget  = new ArrayList<>() ;
	private ArrayList<BarEntry> BARENTRYAchievement = new ArrayList<>();
	private ArrayList<String> BarEntryLabels = new ArrayList<>();
	private BarDataSet Bardataset;
	private BarDataSet Bardataset2;
	private BarData BARDATA;
	private List<Float> targetList = new ArrayList<>();
	private List<Float> achievementList = new ArrayList<>();
	private List<String> itemTypeList = new ArrayList<>();
	private List<Integer> legendColors = new ArrayList<>();
	private List<String> legendLabels = new ArrayList<>();
	private Activity mContext;
	private TextView tvHeaderText;
	private String month= "";
	private int year = 2023;
	private final ArrayList<KeyValue> mMonthName = new ArrayList<>();
	private final ArrayList<KeyValue> mYearName = new ArrayList<>();

	@Override
	protected void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_graph);
		try
		{
			mContext =this;
			if(get_user_type(mContext).equalsIgnoreCase("broker"))
			{
				selected_customr_code = get_selected_dealer_sap_code(mContext);
			}
			else
			{
				selected_customr_code = get_dealer_id(mContext);
			}
			chart = (BarChart) findViewById(R.id.selfAppraisalBarChart);

			year = Integer.parseInt(new SimpleDateFormat("yyyy", Locale.getDefault()).format(new Date()));
			month= new SimpleDateFormat("MM", Locale.getDefault()).format(new Date());

//			year = "2023";
//			month= "01";

			get_month_arr(year);

			KeyValue ky = new KeyValue();
			ky.setKey("FY 2023 - 2024");
			ky.setValue("2023");
			mYearName.add(ky);

			ky = new KeyValue();
			ky.setKey("FY 2024 - 2025");
			ky.setValue("2024");
			mYearName.add(ky);
			
			tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);

			final String vall = Utils.changeDateFormat( "MM", "MMM", month);

			if(year == 2023)
			{
				tvHeaderText.setText(""+vall +", FY 2023 - 2024");
			}
			else
			{
				tvHeaderText.setText(""+vall +", FY 2024 - 2025");
			}

			final ImageView ivHeaderSecond = (ImageView) findViewById(R.id.ivHeaderSecond);
			ivHeaderSecond.setVisibility(View.VISIBLE);
			ivHeaderSecond.setImageResource(R.drawable.cal);
			ivHeaderSecond.setColorFilter(Color.WHITE);

			final ImageView ivHeaderForward = (ImageView) findViewById(R.id.ivHeaderForward);
			ivHeaderForward.setVisibility(View.VISIBLE);
			ivHeaderForward.setImageResource(R.drawable.filter);
			ivHeaderForward.setOnClickListener(new View.OnClickListener() {
				@Override
				public void onClick(View v) {
					month_dialog(mMonthName, "Month");
				}
			});
			ivHeaderSecond.setOnClickListener(new View.OnClickListener() {
				@Override
				public void onClick(View v) {
					month_dialog(mYearName, "Year");
				}
			});
			final ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
			ivHeaderBack.setOnClickListener(new View.OnClickListener() {
				@Override
				public void onClick(View v) {
					onBackPressed();
				}
			});

			//
			prepareTargetAchievementList();
			createChart(1f);
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
		finally
		{
			if(HTTPUtils.isConnectionPossible(mContext))
			{
				new GetTargetAchievement_Asynctask(mContext).execute();
			}
			else
			{
				show_msg_alert(mContext, check_internet_connection, true);
			}
		}
	}

	private void get_month_arr(final int yyyy)
	{
		try
		{
			mMonthName.clear();

			KeyValue kv = new KeyValue();
			kv.setKey("April - "+yyyy);
			kv.setValue("04");
			mMonthName.add(kv);

			kv = new KeyValue();
			kv.setKey("May - "+yyyy);
			kv.setValue("05");
			mMonthName.add(kv);

			kv = new KeyValue();
			kv.setKey("June - "+yyyy);
			kv.setValue("06");
			mMonthName.add(kv);

			kv = new KeyValue();
			kv.setKey("July - "+yyyy);
			kv.setValue("07");
			mMonthName.add(kv);

			kv = new KeyValue();
			kv.setKey("August - "+yyyy);
			kv.setValue("08");
			mMonthName.add(kv);

			kv = new KeyValue();
			kv.setKey("September - "+yyyy);
			kv.setValue("09");
			mMonthName.add(kv);

			kv = new KeyValue();
			kv.setKey("October - "+yyyy);
			kv.setValue("10");
			mMonthName.add(kv);

			kv = new KeyValue();
			kv.setKey("November - "+yyyy);
			kv.setValue("11");
			mMonthName.add(kv);

			kv = new KeyValue();
			kv.setKey("December - "+yyyy);
			kv.setValue("12");
			mMonthName.add(kv);

			kv = new KeyValue();
			kv.setKey("January - "+ (yyyy + 1));
			kv.setValue("01");
			mMonthName.add(kv);

			kv = new KeyValue();
			kv.setKey("February - "+ (yyyy + 1));
			kv.setValue("02");
			mMonthName.add(kv);

			kv = new KeyValue();
			kv.setKey("March - "+ (yyyy + 1));
			kv.setValue("03");
			mMonthName.add(kv);


		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	private void createChart(final  float fff)
	{
		try
		{
			chart.setPinchZoom(false);
			chart.setExtraBottomOffset(10);
			chart.setScaleMinima(fff, 1f);
			chart.setVisibleXvvvv(fff);
			chart.getAxisRight().setEnabled(false); // no right axis
			chart.setDescription("");
			final XAxis xAxis = chart.getXAxis();
			xAxis.setPosition(XAxis.XAxisPosition.BOTTOM);
			xAxis.setTextSize(12f);
			xAxis.setLabelRotationAngle(90f);
			xAxis.setTextColor(Color.BLACK);
			xAxis.setDrawAxisLine(true);
			xAxis.setDrawGridLines(true);

			final YAxis left = chart.getAxisLeft();
			left.setDrawLabels(true); // no axis labels
			left.setDrawAxisLine(true); // no axis line
			left.setDrawGridLines(false); // no grid lines

			Bardataset = new BarDataSet(BARENTRYTarget, "Targets");
			Bardataset.setColor(ContextCompat.getColor(mContext, R.color.blue));
			Bardataset2 = new BarDataSet(BARENTRYAchievement, "Achievements");
			Bardataset2.setColor(ContextCompat.getColor(mContext, R.color.colorGreen));
			final List<BarDataSet> dataSetsGroup=new ArrayList<>();

			dataSetsGroup.add(Bardataset);
			dataSetsGroup.add(Bardataset2);
			Bardataset.setBarSpacePercent(1f);
			Bardataset2.setBarSpacePercent(1f);
			Bardataset.setHighlightEnabled(false);
			Bardataset2.setHighlightEnabled(false);
			BARDATA = new BarData(BarEntryLabels, dataSetsGroup);
			BARDATA.setGroupSpace(20f);

			chart.setData(BARDATA);
			chart.invalidate();
			final Legend l = chart.getLegend();
			l.setCustom(legendColors,legendLabels);
			l.setFormSize(10f); // set the size of the legend forms/shapes
			l.setForm(Legend.LegendForm.SQUARE); // set what type of form/shape should be used

			chart.animateY(3000);
			chart.centerViewTo(Calendar.getInstance().get(Calendar.MONTH),0f, YAxis.AxisDependency.LEFT);
			chart.centerViewTo(Calendar.getInstance().get(Calendar.MONTH),0f, YAxis.AxisDependency.LEFT);
			chart.setOnChartValueSelectedListener( new OnChartValueSelectedListener()
			{
				@Override
				public void onValueSelected(Entry e, int dataSetIndex, Highlight h)
				{
					final int xIndex = e.getXIndex();
					details_dialog();
				}

				@Override
				public void onNothingSelected()
				{
					chart.invalidate();
				}
			});
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	public void prepareTargetAchievementList()
	{
		try
		{
			targetList=new ArrayList<>();
			achievementList=new ArrayList<>();
			BarEntryLabels = new ArrayList<>();
			itemTypeList = new ArrayList<>();

			BARENTRYTarget = new ArrayList<>();
			BARENTRYAchievement = new ArrayList<>();

			legendColors=new ArrayList<>();
			legendColors.add(ContextCompat.getColor(mContext, R.color.colorGreen));
			legendColors.add(ContextCompat.getColor(mContext, R.color.blue));

			legendLabels=new ArrayList<>();
			legendLabels.add("Achievement");
			legendLabels.add("Target");

		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	@Override
	public void onResume()
	{
		super.onResume();
		chart.invalidate();
	}

	public final class GetTargetAchievement_Asynctask extends AsyncTaskCoroutine<String, String>
	{
		private Activity mContext;
		private ProgressDialog mStepProgressDialog;
		private JSONObject jo = new JSONObject();
		public GetTargetAchievement_Asynctask(final Activity mContext) {
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
						String url = item_wise_target_achievement_SAP_data;
						final ArrayList<NameValuePair> mHttpParamPairs = new ArrayList<>(2);
						mHttpParamPairs.add(new BasicNameValuePair("customer_code", selected_customr_code));
//						mHttpParamPairs.add(new BasicNameValuePair("customer_code", "1000001497"));
						mHttpParamPairs.add(new BasicNameValuePair("year", year+""));
						mHttpParamPairs.add(new BasicNameValuePair("month", month));
						POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, mHttpParamPairs);

						print_log_d("target_achievement_239_url ", url);
						print_log_d("target_achievement_239_param ", mHttpParamPairs.toString());
						print_log_d("target_achievement_239_res ", POST_result);
						jo = new JSONObject(POST_result);

						if(jo.optString("process_status").equalsIgnoreCase("YES"))
						{


							final String target_ach_data = jo.optString("target_ach_data");
							final JSONArray jsonArray = new JSONArray(target_ach_data);

							targetList=new ArrayList<>();
							achievementList=new ArrayList<>();
							BarEntryLabels = new ArrayList<>();
							itemTypeList = new ArrayList<>();

							BARENTRYTarget = new ArrayList<>();
							BARENTRYAchievement = new ArrayList<>();


							for(int index=0; index<jsonArray.length(); index++)
							{
								final JSONObject e = jsonArray.getJSONObject(index);

								targetList.add(Float.valueOf(e.optString("TGTQTY")));
								achievementList.add(Float.valueOf(e.optString("ACHQTY")));
								itemTypeList.add(e.optString("item_type"));

								if(e.optString("itemname").isEmpty())
								{
									BarEntryLabels.add(e.optString("itemcode"));
								}
								else
								{
									BarEntryLabels.add(e.optString("itemname"));
								}

								BARENTRYTarget.add(new BarEntry(targetList.get(index), index));
								BARENTRYAchievement.add(new BarEntry(achievementList.get(index), index));

							}

							legendColors=new ArrayList<>();
							legendColors.add(ContextCompat.getColor(mContext, R.color.colorGreen));
							legendColors.add(ContextCompat.getColor(mContext, R.color.blue));

							legendLabels=new ArrayList<>();
							legendLabels.add("Achievement");
							legendLabels.add("Target");
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
				final String vall = Utils.changeDateFormat( "MM", "MMM", month);

				if(result.equalsIgnoreCase("Network Failure"))
				{
					show_msg_alert(mContext, check_internet_connection, false);
					chart.clear();
				}
				else if(jo.optString("process_status").equalsIgnoreCase("YES"))
				{
					if(BARENTRYTarget.size() < 4)
					{
						createChart(1f);
					}
					else
					{
						createChart(4f);
					}
				}
				else if(jo.has("process_message"))
				{
					chart.clear();
					show_msg_alert(mContext, jo.optString("process_message"), false);
				}
				else
				{
					chart.clear();
					show_msg_alert(mContext, "Details not available "+vall +", "+year, false);
				}


				if(year == 2023)
				{
					tvHeaderText.setText(""+vall +", FY 2023 - 2024");
				}
				else
				{
					tvHeaderText.setText(""+vall +", FY 2024 - 2025");
				}
				get_month_arr(year);
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
	public void month_dialog(final ArrayList<KeyValue> mName, final String type)
	{
        try
        {
            final Dialog mDialog = new Dialog(mContext, android.R.style.Theme_DeviceDefault_Light_NoActionBar);
            mDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            final Window window = mDialog.getWindow();
            window.setGravity(Gravity.CENTER);
            window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.MATCH_PARENT);
            window.setStatusBarColor(mContext.getResources().getColor(R.color.colorRed_StatusBar));

            mDialog.setContentView(R.layout.dialog_listview);
            mDialog.setCancelable(false);
            mDialog.setCanceledOnTouchOutside(true);
            final TextView tv_status_h = (TextView) mDialog.findViewById(R.id.tv_status_h);
            tv_status_h.setText("Select "+type);

            final ListView mListView = (ListView) mDialog.findViewById(R.id.mListView);
            final MonthAdapter destinationAdapter = new MonthAdapter(this, mName);
            mListView.setAdapter(destinationAdapter);

            mDialog.show();

            mListView.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                    if(HTTPUtils.isConnectionPossible(mContext))
                    {
                        try
                        {
                            if(type.equalsIgnoreCase("Year"))
                            {
                                year = Integer.parseInt(mName.get(position).getValue());

                                tvHeaderText.setText("Performance");
                                chart.clear();

                                get_month_arr(year);
                                month_dialog(mMonthName, "Month");
                            }
                            else if(type.equalsIgnoreCase("Month"))
                            {
                                month = mName.get(position).getValue();

                                new GetTargetAchievement_Asynctask(mContext).execute();
                            }


                        }
                        catch (Exception e)
                        {
                            e.printStackTrace();
                        }
                        finally
                        {
                            mDialog.dismiss();
                        }
                    }
                    else
                    {
                        show_msg_alert(mContext, check_internet_connection, true);
                    }
                }
            });
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
	}
	public void details_dialog()
	{
		try
		{
			final Dialog mDialog = new Dialog(mContext, android.R.style.Theme_DeviceDefault_Light_NoActionBar);
			mDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
			final Window window = mDialog.getWindow();
			window.setGravity(Gravity.CENTER);
			window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.MATCH_PARENT);
			window.setStatusBarColor(mContext.getResources().getColor(R.color.colorRed_StatusBar));

			mDialog.setContentView(R.layout.dialog_listview);

			final TextView tv_status_h = (TextView) mDialog.findViewById(R.id.tv_status_h);
//			final String vall = Utils.changeDateFormat( "MM", "MMM", month);
			tv_status_h.setText(tvHeaderText.getText().toString());
			final ArrayList<SelfAppraisalDetailsProductGroupWise> selfAppraisalListProductGroupWise = new ArrayList<>();

			SelfAppraisalDetailsProductGroupWise de = new SelfAppraisalDetailsProductGroupWise();
			de.setmonth("PROD. NAME");
			de.settarget("TARGET");
			de.setachievement("ACHIEV");
			de.set_item_type("TYPE");
			selfAppraisalListProductGroupWise.add(de);

			for(int index=0; index<BarEntryLabels.size(); index++)
			{
				de = new SelfAppraisalDetailsProductGroupWise();
				de.setmonth(BarEntryLabels.get(index));
				de.settarget(BARENTRYTarget.get(index).getVal()+"");
				de.setachievement(BARENTRYAchievement.get(index).getVal() + "");
				de.set_item_type(itemTypeList.get(index)+ "");

				selfAppraisalListProductGroupWise.add(de);
			}



			final ListView mListView = (ListView) mDialog.findViewById(R.id.mListView);
			final ProductGraphAdapter pAdapter = new ProductGraphAdapter(this, selfAppraisalListProductGroupWise);
			mListView.setAdapter(pAdapter);

			mDialog.setCancelable(true);
			mDialog.setCanceledOnTouchOutside(true);

			mDialog.show();

		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}
}

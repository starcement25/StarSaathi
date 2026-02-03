package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_dealer_id;
import static org.forcepower.starcement.SharedPrefData.get_selected_dealer_sap_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.TAG;
import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.constants.Constants.item_wise_target_achievement_SAP_data;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.graphics.Color;
import android.os.Bundle;
import android.util.Log;
import android.view.Gravity;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
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

public final class ProductWiseGraphActivity extends AceDnsParentActivity {
    private BarChart chart;
    private ArrayList<BarEntry> BARENTRYTarget = new ArrayList<>();
    private ArrayList<BarEntry> BARENTRYAchievement = new ArrayList<>();
    private ArrayList<String> BarEntryLabels = new ArrayList<>();
    private List<Float> targetList = new ArrayList<>();
    private List<Float> achievementList = new ArrayList<>();
    private List<String> itemTypeList = new ArrayList<>();
    private List<Integer> legendColors = new ArrayList<>();
    private List<String> legendLabels = new ArrayList<>();
    private Activity mContext;
    private TextView tvHeaderText;
    private String month = "";
    private int year = 2024;
    private final ArrayList<KeyValue> mMonthName = new ArrayList<>();
    private final ArrayList<KeyValue> mYearName = new ArrayList<>();

    @SuppressLint("SetTextI18n")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_graph);
        try {
            mContext = this;
            if (get_user_type(mContext).equalsIgnoreCase("broker")) {
                selected_customr_code = get_selected_dealer_sap_code(mContext);
            } else {
                selected_customr_code = get_dealer_id(mContext);
            }
            chart = findViewById(R.id.selfAppraisalBarChart);

            year = Integer.parseInt(new SimpleDateFormat("yyyy", Locale.getDefault()).format(new Date()));
            month = new SimpleDateFormat("MM", Locale.getDefault()).format(new Date());
            get_month_arr(year);

            KeyValue ky = new KeyValue();
            ky.setKey("FY 2024 - 2025");
            ky.setValue("2024");
            mYearName.add(ky);

            ky = new KeyValue();
            ky.setKey("FY 2025 - 2026");
            ky.setValue("2025");
            mYearName.add(ky);

            final String vall = Utils.changeDateFormat("MM", "MMM", month);

            tvHeaderText = findViewById(R.id.tvHeaderText);
            tvHeaderText.setText(year == 2024 ? vall + ", FY 2024 - 2025" : vall + ", FY 2025 - 2026");

            final ImageView ivHeaderSecond = findViewById(R.id.ivHeaderSecond);
            ivHeaderSecond.setVisibility(View.VISIBLE);
            ivHeaderSecond.setImageResource(R.drawable.cal);
            ivHeaderSecond.setColorFilter(Color.WHITE);
            ivHeaderSecond.setOnClickListener(v -> month_dialog(mYearName, "Year"));

            final ImageView ivHeaderForward = findViewById(R.id.ivHeaderForward);
            ivHeaderForward.setVisibility(View.VISIBLE);
            ivHeaderForward.setImageResource(R.drawable.filter);
            ivHeaderForward.setOnClickListener(v -> month_dialog(mMonthName, "Month"));

            final ImageView ivHeaderBack = findViewById(R.id.ivHeaderBack);
            ivHeaderBack.setOnClickListener(v -> onBackPressed());

            prepareTargetAchievementList();
            createChart(1f);
        } catch (Exception e) {
            Log.d(TAG, "onCreate: " + e.getMessage());
        } finally {
            if (HTTPUtils.isConnectionPossible(mContext)) {
                new GetTargetAchievement_Asynctask(mContext).execute();
            } else {
                show_msg_alert(mContext, check_internet_connection, true);
            }
        }
    }

    private void get_month_arr(final int yyyy) {
        try {
            mMonthName.clear();
            String[] names = {
                    "April", "May", "June", "July", "August", "September",
                    "October", "November", "December", "January", "February", "March"
            };
            String[] values = {
                    "04", "05", "06", "07", "08", "09",
                    "10", "11", "12", "01", "02", "03"
            };
            for (int i = 0; i < names.length; i++) {
                int year = (i < 9) ? yyyy : yyyy + 1;
                KeyValue kv = new KeyValue();
                kv.setKey(names[i] + " - " + year);
                kv.setValue(values[i]);
                mMonthName.add(kv);
            }
        } catch (Exception e) {
            Log.d(TAG, "get_month_arr: " + e.getMessage());
        }
    }

    private void createChart(final float fff) {
        try {
            chart.setPinchZoom(false);
            chart.setExtraBottomOffset(10);
            chart.setScaleMinima(fff, 1f);
            chart.setVisibleXvvvv(fff);
            chart.getAxisRight().setEnabled(false);
            chart.setDescription("");
            final XAxis xAxis = chart.getXAxis();
            xAxis.setPosition(XAxis.XAxisPosition.BOTTOM);
            xAxis.setTextSize(12f);
            xAxis.setLabelRotationAngle(90f);
            xAxis.setTextColor(Color.BLACK);
            xAxis.setDrawAxisLine(true);
            xAxis.setDrawGridLines(true);

            final YAxis left = chart.getAxisLeft();
            left.setDrawLabels(true);
            left.setDrawAxisLine(true);
            left.setDrawGridLines(false);

            BarDataSet bardataset = new BarDataSet(BARENTRYTarget, "Targets");
            bardataset.setColor(ContextCompat.getColor(mContext, R.color.blue));
            BarDataSet bardataset2 = new BarDataSet(BARENTRYAchievement, "Achievements");
            bardataset2.setColor(ContextCompat.getColor(mContext, R.color.colorGreen));
            final List<BarDataSet> dataSetsGroup = new ArrayList<>();

            dataSetsGroup.add(bardataset);
            dataSetsGroup.add(bardataset2);
            bardataset.setBarSpacePercent(1f);
            bardataset2.setBarSpacePercent(1f);
            bardataset.setHighlightEnabled(false);
            bardataset2.setHighlightEnabled(false);
            BarData BARDATA = new BarData(BarEntryLabels, dataSetsGroup);
            BARDATA.setGroupSpace(20f);

            chart.setData(BARDATA);
            chart.invalidate();
            final Legend l = chart.getLegend();
            l.setCustom(legendColors, legendLabels);
            l.setFormSize(10f);
            l.setForm(Legend.LegendForm.SQUARE);

            chart.animateY(3000);
            chart.centerViewTo(Calendar.getInstance().get(Calendar.MONTH), 0f, YAxis.AxisDependency.LEFT);
            chart.centerViewTo(Calendar.getInstance().get(Calendar.MONTH), 0f, YAxis.AxisDependency.LEFT);
            chart.setOnChartValueSelectedListener(new OnChartValueSelectedListener() {
                @Override
                public void onValueSelected(Entry e, int dataSetIndex, Highlight h) {
                    details_dialog();
                }

                @Override
                public void onNothingSelected() {
                    chart.invalidate();
                }
            });
        } catch (Exception e) {
            Log.d(TAG, "createChart: " + e.getMessage());
        }
    }

    public void prepareTargetAchievementList() {
        try {
            targetList = new ArrayList<>();
            achievementList = new ArrayList<>();
            BarEntryLabels = new ArrayList<>();
            itemTypeList = new ArrayList<>();

            BARENTRYTarget = new ArrayList<>();
            BARENTRYAchievement = new ArrayList<>();

            legendColors = new ArrayList<>();
            legendColors.add(ContextCompat.getColor(mContext, R.color.colorGreen));
            legendColors.add(ContextCompat.getColor(mContext, R.color.blue));

            legendLabels = new ArrayList<>();
            legendLabels.add("Achievement");
            legendLabels.add("Target");
        } catch (Exception e) {
            Log.d(TAG, "prepareTargetAchievementList: " + e.getMessage());
        }
    }

    @Override
    public void onResume() {
        super.onResume();
        chart.invalidate();
    }

    public final class GetTargetAchievement_Asynctask extends AsyncTaskCoroutine<String, String> {
        private final Activity mContext;
        private ProgressDialog mStepProgressDialog;
        private JSONObject jo = new JSONObject();

        public GetTargetAchievement_Asynctask(final Activity mContext) {
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
            try {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    try {
                        String url = item_wise_target_achievement_SAP_data;
                        final ArrayList<NameValuePair> mHttpParamPairs = new ArrayList<>(2);
                        mHttpParamPairs.add(new BasicNameValuePair("customer_code", selected_customr_code));
                        mHttpParamPairs.add(new BasicNameValuePair("year", year + ""));
                        mHttpParamPairs.add(new BasicNameValuePair("month", month));


                        Log.d(TAG, "_DOWNLOAD_: "+item_wise_target_achievement_SAP_data);
                        Log.d(TAG, "_DOWNLOAD_: "+selected_customr_code);
                        Log.d(TAG, "_DOWNLOAD_: "+year);
                        Log.d(TAG, "_DOWNLOAD_: "+month);

                        POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, mHttpParamPairs);

                        jo = new JSONObject(POST_result);
                        if (jo.optString("process_status").equalsIgnoreCase("YES")) {
                            final String target_ach_data = jo.optString("target_ach_data");
                            final JSONArray jsonArray = new JSONArray(target_ach_data);

                            targetList = new ArrayList<>();
                            achievementList = new ArrayList<>();
                            BarEntryLabels = new ArrayList<>();
                            itemTypeList = new ArrayList<>();
                            BARENTRYTarget = new ArrayList<>();
                            BARENTRYAchievement = new ArrayList<>();

                            for (int index = 0; index < jsonArray.length(); index++) {
                                final JSONObject e = jsonArray.getJSONObject(index);
                                targetList.add(Float.valueOf(e.optString("TGTQTY")));
                                achievementList.add(Float.valueOf(e.optString("ACHQTY")));
                                itemTypeList.add(e.optString("item_type"));
                                BarEntryLabels.add(e.optString("itemname").isEmpty() ? e.optString("itemcode") : e.optString("itemname"));
                                BARENTRYTarget.add(new BarEntry(targetList.get(index), index));
                                BARENTRYAchievement.add(new BarEntry(achievementList.get(index), index));
                            }

                            legendColors = new ArrayList<>();
                            legendColors.add(ContextCompat.getColor(mContext, R.color.colorGreen));
                            legendColors.add(ContextCompat.getColor(mContext, R.color.blue));

                            legendLabels = new ArrayList<>();
                            legendLabels.add("Achievement");
                            legendLabels.add("Target");
                        }
                    } catch (Exception e) {
                        POST_result = "Network Failure";
                    }
                }
            } catch (Exception e) {
                Log.d(TAG, "doInBackground: " + e.getMessage());
            }
            return POST_result;
        }

        @SuppressLint("SetTextI18n")
        @Override
        public void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                final String vall = Utils.changeDateFormat("MM", "MMM", month);
                if (result.equalsIgnoreCase("Network Failure")) {
                    show_msg_alert(mContext, check_internet_connection, false);
                    chart.clear();
                } else if (jo.optString("process_status").equalsIgnoreCase("YES")) {
                    createChart(BARENTRYTarget.size() < 4 ? 1f : 4f);
                } else if (jo.has("process_message")) {
                    chart.clear();
                    show_msg_alert(mContext, jo.optString("process_message"), false);
                } else {
                    chart.clear();
                    show_msg_alert(mContext, "Details not available " + vall + ", " + year, false);
                }
                tvHeaderText.setText(year == 2024 ? vall + ", FY 2024 - 2025" : vall + ", FY 2025 - 2026");
                get_month_arr(year);
            } catch (Exception e) {
                Log.d(TAG, "onPostExecute: " + e.getMessage());
            } finally {
                mStepProgressDialog.dismiss();
            }
        }
    }

    @SuppressLint("SetTextI18n")
    public void month_dialog(final ArrayList<KeyValue> mName, final String type) {
        try {
            final Dialog mDialog = new Dialog(mContext, android.R.style.Theme_DeviceDefault_Light_NoActionBar);
            mDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            final Window window = mDialog.getWindow();
            assert window != null;
            window.setGravity(Gravity.CENTER);
            window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.MATCH_PARENT);
            window.setStatusBarColor(mContext.getResources().getColor(R.color.colorRed_StatusBar));

            mDialog.setContentView(R.layout.dialog_listview);
            mDialog.setCancelable(false);
            mDialog.setCanceledOnTouchOutside(true);
            final TextView tv_status_h = mDialog.findViewById(R.id.tv_status_h);
            tv_status_h.setText("Select " + type);

            final ListView mListView = mDialog.findViewById(R.id.mListView);
            final MonthAdapter destinationAdapter = new MonthAdapter(this, mName);
            mListView.setAdapter(destinationAdapter);
            mDialog.show();

            mListView.setOnItemClickListener((parent, view, position, id) -> {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    try {
                        if (type.equalsIgnoreCase("Year")) {
                            year = Integer.parseInt(mName.get(position).getValue());
                            tvHeaderText.setText("Performance");
                            chart.clear();
                            get_month_arr(year);
                            month_dialog(mMonthName, "Month");
                        } else if (type.equalsIgnoreCase("Month")) {
                            month = mName.get(position).getValue();
                            new GetTargetAchievement_Asynctask(mContext).execute();
                        }
                    } catch (Exception e) {
                        Log.d(TAG, "onItemClick: ");
                    } finally {
                        mDialog.dismiss();
                    }
                } else {
                    show_msg_alert(mContext, check_internet_connection, true);
                }
            });
        } catch (Exception e) {
            Log.d(TAG, "month_dialog: " + e.getMessage());
        }
    }

    public void details_dialog() {
        try {
            final Dialog mDialog = new Dialog(mContext, android.R.style.Theme_DeviceDefault_Light_NoActionBar);
            mDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            final Window window = mDialog.getWindow();
            assert window != null;
            window.setGravity(Gravity.CENTER);
            window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.MATCH_PARENT);
            window.setStatusBarColor(mContext.getResources().getColor(R.color.colorRed_StatusBar));
            mDialog.setContentView(R.layout.dialog_listview);
            final TextView tv_status_h = mDialog.findViewById(R.id.tv_status_h);
            tv_status_h.setText(tvHeaderText.getText().toString());
            final ArrayList<SelfAppraisalDetailsProductGroupWise> selfAppraisalListProductGroupWise = new ArrayList<>();
            SelfAppraisalDetailsProductGroupWise de = new SelfAppraisalDetailsProductGroupWise();
            de.setmonth("PROD. NAME");
            de.settarget("TARGET");
            de.setachievement("ACHIEV");
            de.set_item_type("TYPE");
            selfAppraisalListProductGroupWise.add(de);
            for (int index = 0; index < BarEntryLabels.size(); index++) {
                de = new SelfAppraisalDetailsProductGroupWise();
                de.setmonth(BarEntryLabels.get(index));
                de.settarget(BARENTRYTarget.get(index).getVal() + "");
                de.setachievement(BARENTRYAchievement.get(index).getVal() + "");
                de.set_item_type(itemTypeList.get(index));
                selfAppraisalListProductGroupWise.add(de);
            }
            final ListView mListView = mDialog.findViewById(R.id.mListView);
            final ProductGraphAdapter pAdapter = new ProductGraphAdapter(this, selfAppraisalListProductGroupWise);
            mListView.setAdapter(pAdapter);
            mDialog.setCancelable(true);
            mDialog.setCanceledOnTouchOutside(true);
            mDialog.show();
        } catch (Exception e) {
            Log.d(TAG, "details_dialog: " + e.getMessage());
        }
    }
}
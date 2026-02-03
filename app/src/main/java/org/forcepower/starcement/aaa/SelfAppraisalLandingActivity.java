package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_name;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.TAG;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;

import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.Gravity;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

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
import org.forcepower.starcement.adapter.DestinationAdapter;
import org.forcepower.starcement.bean.DestinationMaster;
import org.forcepower.starcement.bean.SelfAppraisalDetailsCustomerWise;
import org.forcepower.starcement.constants.Constants;
import org.forcepower.starcement.database.AceDnsDatabase;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.List;

public final class SelfAppraisalLandingActivity extends AceDnsParentActivity {
    BarChart chart ;
    ArrayList<BarEntry> BARENTRYTarget,BARENTRYAchievement;
    ArrayList<String> BarEntryLabels ;
    BarDataSet Bardataset ;
    BarDataSet Bardataset2 ;
    BarData BARDATA ;
    List<Float> targetList;
    List<Float> achievementList;
    List<Integer> colorList,legendColors;
    List<String> legendLabels;
    Context mContext;
    AceDnsDatabase mAceDnsDatabase;
    String currentTargetAchievementType ="";
    TextView switchStatusTv, btnPreviousYear, btnCurrentYear, tvCurrYearLine, tvPreYearLine,
             tvHeaderText;
    public static String currentPrevYear = "c", PERFORMANCE_GRAPH = "PERFORMANCE GRAPH";
    ImageView ivHeaderForward;
    ArrayList<DestinationMaster> mDestinationMasterList = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_self_appraisal_landing);
        try {
            mContext =this;
            selected_customr_code = get_user_type(mContext).equalsIgnoreCase("broker") ? get_selected_customer_code(mContext) : get_emp_or_customer_code(mContext);

            tvHeaderText = findViewById(R.id.tvHeaderText);
            ivHeaderForward = findViewById(R.id.ivHeaderForward);
            ImageView ivHeaderBack = findViewById(R.id.ivHeaderBack);
            switchStatusTv= findViewById(R.id.switchStatus);
            tvCurrYearLine = findViewById(R.id.tvCurrYearLine);
            tvPreYearLine = findViewById(R.id.tvPreYearLine);
            btnCurrentYear = findViewById(R.id.btnCurrentYear);
            btnPreviousYear = findViewById(R.id.btnPreviousYear);

            tvHeaderText.setText(PERFORMANCE_GRAPH);
            mAceDnsDatabase = new AceDnsDatabase(mContext);
            colorList=new ArrayList<>();

            ivHeaderBack.setOnClickListener(v -> onBackPressed());
            btnCurrentYear.setOnClickListener(v -> btn_current_year());
            btnPreviousYear.setOnClickListener(v -> btn_previous_year());

            if(!(get_user_type(mContext).equalsIgnoreCase("broker")|| get_user_type(mContext).equalsIgnoreCase("dealer"))) {
                LinearLayout llHeaderDetails = findViewById(R.id.llHeaderDetails);
                llHeaderDetails.setVisibility(View.GONE);
            }
            if(get_user_type(mContext).equalsIgnoreCase("broker")) {
                ivHeaderForward.setVisibility(View.GONE);
                tvHeaderText.setText(get_selected_customer_name(mContext));
            }
            currentPrevYear = "c";
            prepareTargetAchievementList(currentPrevYear, selected_customr_code);
            createChart();
        }
        catch (Exception e) {
            Log.d(TAG, "onCreate: "+e.getMessage());
        }
    }

    private void btn_previous_year() {
        try {
            btnPreviousYear.setTextColor(Color.RED);
            tvPreYearLine.setBackgroundColor(Color.RED);
            btnCurrentYear.setTextColor(Color.GRAY);
            tvCurrYearLine.setBackgroundColor(Color.GRAY);
            currentPrevYear = "p";
            prepareTargetAchievementList(currentPrevYear, selected_customr_code);
            createChart();
        }
        catch (Exception e) {
            Log.d(TAG, "btn_previous_year: "+e.getMessage());
        }
    }

    private void btn_current_year() {
        try {
            btnCurrentYear.setTextColor(Color.RED);
            tvCurrYearLine.setBackgroundColor(Color.RED);
            btnPreviousYear.setTextColor(Color.GRAY);
            tvPreYearLine.setBackgroundColor(Color.GRAY);
            currentPrevYear = "c";
            prepareTargetAchievementList(currentPrevYear, selected_customr_code);
            createChart();
        }
        catch (Exception e) {
            Log.d(TAG, "btn_current_year: "+e.getMessage());
        }
    }

    private void createChart() {
        try {
            chart =  findViewById(R.id.selfAppraisalBarChart);
            chart.setPinchZoom(false);
            chart.setExtraBottomOffset(10);
            chart.setScaleMinima(4f, 1f);
            chart.setVisibleXvvvv(4f);
            chart.getAxisRight().setEnabled(false); // no right axis
            chart.setDescription("");
            addLegendColorAndLabels();
            XAxis xAxis = chart.getXAxis();
            xAxis.setPosition(XAxis.XAxisPosition.BOTTOM);
            xAxis.setTextSize(12f);
            xAxis.setLabelRotationAngle(-90f);
            xAxis.setTextColor(Color.RED);
            xAxis.setDrawAxisLine(true);
            xAxis.setDrawGridLines(true);

            YAxis left = chart.getAxisLeft();
            left.setDrawLabels(true); // no axis labels
            left.setDrawAxisLine(true); // no axis line
            left.setDrawGridLines(false); // no grid lines

            AddValuesToBarEntryLabels();

            Bardataset = new BarDataSet(BARENTRYTarget, "Targets (MT)");
            Bardataset.setColor(ContextCompat.getColor(mContext, R.color.blue));
            Bardataset2 = new BarDataSet(BARENTRYAchievement, "Achievements (MT)");
            Bardataset2.setColor(ContextCompat.getColor(mContext, R.color.colorGreen));
            List<BarDataSet> dataSetsGroup=new ArrayList<>();

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
            Legend l = chart.getLegend();
            l.setCustom(legendColors,legendLabels);
            l.setFormSize(10f); // set the size of the legend forms/shapes
            l.setForm(Legend.LegendForm.SQUARE); // set what type of form/shape should be used

            chart.animateY(3000);
            chart.centerViewTo(Calendar.getInstance().get(Calendar.MONTH),0f, YAxis.AxisDependency.LEFT);
            chart.setOnChartValueSelectedListener( new OnChartValueSelectedListener() {
                @Override
                public void onValueSelected(Entry e, int dataSetIndex, Highlight h) {
                    int xIndex = e.getXIndex();
                    Intent intent=new Intent(mContext, SelfAppraisalDetailsListActivity.class);
                    intent.putExtra("type",currentTargetAchievementType);
                    intent.putExtra("month", BarEntryLabels.get(xIndex));
                    intent.putExtra("PERFORMANCE_GRAPH", tvHeaderText.getText().toString());
                    intent.putExtra("customer_code",selected_customr_code);

                    startActivity(intent);
                    chart.invalidate();
                }

                @Override
                public void onNothingSelected()
                {
                    chart.invalidate();
                }
            });
        }
        catch (Exception e) {
            Log.d(TAG, "createChart: "+e.getMessage());
        }
    }

    public void AddValuesToBARENTRY() {
        try {
            BARENTRYTarget = new ArrayList<>();
            BARENTRYAchievement = new ArrayList<>();
            for(int i=0;i<targetList.size();i++) {
                BARENTRYTarget.add(new BarEntry(targetList.get(i), i));
            }
            for(int i=0;i<achievementList.size();i++) {
                BARENTRYAchievement.add(new BarEntry(achievementList.get(i), i));
            }
        }
        catch (Exception e) {
            Log.d(TAG, "AddValuesToBARENTRY: "+e.getMessage());
        }
    }

    public void AddValuesToBarEntryLabels() {
        try {
            BarEntryLabels = new ArrayList<>();
            BarEntryLabels.add("April");
            BarEntryLabels.add("May");
            BarEntryLabels.add("June");
            BarEntryLabels.add("July");
            BarEntryLabels.add("August");
            BarEntryLabels.add("September");
            BarEntryLabels.add("October");
            BarEntryLabels.add("November");
            BarEntryLabels.add("December");
            BarEntryLabels.add("January");
            BarEntryLabels.add("February");
            BarEntryLabels.add("March");
        }
        catch (Exception e) {
            Log.d(TAG, "AddValuesToBarEntryLabels: "+e.getMessage());
        }
    }

    public void prepareTargetAchievementList(String year, String customer_code) {
        try {
            targetList=new ArrayList<>();
            achievementList=new ArrayList<>();

            ArrayList<SelfAppraisalDetailsCustomerWise> targetListFromDB = mAceDnsDatabase.getTargetForAllMonths(year, customer_code);
            Log.d("productWise----",targetListFromDB.toString());
            int i=0;
            while(i<targetListFromDB.size()) {
                targetList.add(Float.valueOf(targetListFromDB.get(i).gettarget()));
                achievementList.add(Float.valueOf(targetListFromDB.get(i).getachievement()));
                i++;
            }

            for(int index=0;index<targetList.size();index++) {
                int backgroundColor = ContextCompat.getColor(mContext, R.color.colorGreen);
                Float currentTarget = targetList.get(index);
                Float currentAchievement = achievementList.get(index);
                if(currentTarget > currentAchievement) {
                    if((currentAchievement/currentTarget)*100>49) {
                        backgroundColor = ContextCompat.getColor(mContext, R.color.colorYellow);
                    }
                    else {
                        backgroundColor = ContextCompat.getColor(mContext, R.color.colorRed);
                    }
                }
                colorList=new ArrayList<>();
                colorList.add(backgroundColor);
            }
            AddValuesToBARENTRY();
        }
        catch (Exception e) {
            Log.d(TAG, "prepareTargetAchievementList: "+e.getMessage());
        }
    }

    private void addLegendColorAndLabels() {
        try {
            legendLabels=new ArrayList<>();
            legendColors=new ArrayList<>();

            legendColors.add(ContextCompat.getColor(mContext, R.color.colorGreen));
            legendColors.add(ContextCompat.getColor(mContext, R.color.blue));

            legendLabels.add("Achievements (MT)");
            legendLabels.add("Targets (MT)");
        }
        catch (Exception e) {
            Log.d(TAG, "addLegendColorAndLabels: "+e.getMessage());
        }
    }

    @Override
    public void onResume() {
        super.onResume();
        chart.invalidate();
    }

    @SuppressLint("MissingSuperCall")
    @Override
    public void onBackPressed() {
        finish();
    }

    public void select_sub_dealer(View view) {
        try {
            if(tvHeaderText.getText().toString().matches(PERFORMANCE_GRAPH)) {
                mAceDnsDatabase = new AceDnsDatabase(mContext);
                mDestinationMasterList=mAceDnsDatabase.getMySubDealerList("");
                if(!mDestinationMasterList.isEmpty()) {
                    showMySubDealerList();
                }
                else {
                    Toast.makeText(mContext, "No sub dealer found", Toast.LENGTH_SHORT).show();
                }
            }
            else {
                show_clear_filter_dialog();
            }
        }
        catch (Exception e) {
            Log.d(TAG, "select_sub_dealer: "+e.getMessage());
        }
        finally {
            if(mAceDnsDatabase != null)
                mAceDnsDatabase.close();
        }
    }

    private void show_clear_filter_dialog() {
        AlertDialog.Builder AlertDG = new AlertDialog.Builder(mContext, R.style.MyDialog);
        AlertDG.setTitle(getResources().getString(R.string.app_name));
        AlertDG.setMessage("Do you want to clear filter?");
        AlertDG.setNeutralButton("Clear Filter", (dialog, which) -> {
            tvHeaderText.setText(PERFORMANCE_GRAPH);
            btn_current_year();
            ivHeaderForward.setImageResource(R.drawable.filter);
        });
        AlertDG.setNegativeButton("Filter", (dialog, which) -> {
            try {
                mAceDnsDatabase = new AceDnsDatabase(mContext);
                mDestinationMasterList=mAceDnsDatabase.getMySubDealerList("");
                if(!mDestinationMasterList.isEmpty()) {
                    showMySubDealerList();
                }
                else {
                    Toast.makeText(mContext, "No sub dealer found", Toast.LENGTH_SHORT).show();
                }
            }
            catch (Exception e) {
                Log.d(TAG, "show_clear_filter_dialog: "+e.getMessage());
            }
            finally {
                if(mAceDnsDatabase != null)
                    mAceDnsDatabase.close();
            }
        });
        AlertDG.setCancelable(true);
        AlertDG.create().show();
    }

    @SuppressLint("SetTextI18n")
    public void showMySubDealerList() {
        try {
            final Dialog mDestinationDialog = new Dialog(mContext, android.R.style.Theme_DeviceDefault_Light_NoActionBar);
            mDestinationDialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            Window window = mDestinationDialog.getWindow();
            assert window != null;
            window.setGravity(Gravity.CENTER);
            window.setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.MATCH_PARENT);
            mDestinationDialog.setContentView(R.layout.select_with_search);
            mDestinationDialog.setCancelable(true);
            window.setStatusBarColor(getResources().getColor(R.color.colorRed_StatusBar));
            ImageView btnback =  mDestinationDialog.findViewById(R.id.back);
            btnback.setOnClickListener(v -> mDestinationDialog.dismiss());
            TextView title =  mDestinationDialog.findViewById(R.id.tvDestHeading);
            title.setText("Please select a sub-dealer");
            ListView dialogList =  mDestinationDialog.findViewById(R.id.list);

            final DestinationAdapter destinationAdapter = new DestinationAdapter(this,R.layout.customer_broker_list_child,mDestinationMasterList);
            dialogList.setAdapter(destinationAdapter);

            EditText searchText =  mDestinationDialog.findViewById(R.id.autoCompleteTextView1);
            searchText.addTextChangedListener(new TextWatcher() {
                @Override
                public void onTextChanged(CharSequence s, int arg1, int arg2, int arg3) {
                    destinationAdapter.getFilter().filter(s.toString());
                }

                @Override
                public void beforeTextChanged(CharSequence arg0, int arg1, int arg2, int arg3) {
                }

                @Override
                public void afterTextChanged(Editable s) {
                }
            });

            dialogList.setOnItemClickListener((arg0, arg1, position, arg3) -> {
                getWindow().setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN);
                Constants.selectedDestination = destinationAdapter.getItem(position);

                assert Constants.selectedDestination != null;
                tvHeaderText.setText(Constants.selectedDestination.getDestinationName());
                selected_customr_code = Constants.selectedDestination.getSubDealerCode();
                prepareTargetAchievementList(currentPrevYear, selected_customr_code);
                createChart();
                ivHeaderForward.setImageResource(R.drawable.filtered);
                mDestinationDialog.dismiss();
            });

            mDestinationDialog.show();
        }
        catch (Exception e) {
            Log.d(TAG, "showMySubDealerList: "+e.getMessage());
        }
    }
}
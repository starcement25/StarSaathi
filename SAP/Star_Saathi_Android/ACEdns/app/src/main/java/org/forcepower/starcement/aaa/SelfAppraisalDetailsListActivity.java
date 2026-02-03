package org.forcepower.starcement.aaa;

import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.view.MotionEvent;
import android.view.View;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.SelfAppraisalProductGroupWiseListAdapter;
import org.forcepower.starcement.bean.SelfAppraisalDetailsBranchWise;
import org.forcepower.starcement.bean.SelfAppraisalDetailsCustomerWise;
import org.forcepower.starcement.bean.SelfAppraisalDetailsProductGroupWise;
import org.forcepower.starcement.database.AceDnsDatabase;

import java.util.ArrayList;

import static org.forcepower.starcement.aaa.SelfAppraisalLandingActivity.currentPrevYear;

public final class SelfAppraisalDetailsListActivity extends AceDnsParentActivity
{
    String type="",month="", PERFORMANCE_GRAPH = "PERFORMANCE GRAPH", customer_code ="";
    TextView tvtarget_, tvachievement_;
    ArrayList<SelfAppraisalDetailsCustomerWise> selfAppraisalListCustomerWise;
    ArrayList<SelfAppraisalDetailsBranchWise> selfAppraisalListBranchWise;
    ArrayList<SelfAppraisalDetailsProductGroupWise> selfAppraisalListProductGroupWise;
    AceDnsDatabase mAceDnsDatabase;
    ListView selfAppraisalDetailsList;
    Context mContext;
    TextView tvAppOrder, tvOfflineOrder, tvApporderLine, tvOfflineorderLine, tvHeaderText;


    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_self_appraisal_details_list);
        try
        {

            mContext=this;
            mAceDnsDatabase = new AceDnsDatabase(mContext);

            selfAppraisalDetailsList=(ListView) findViewById(R.id.selfAppraisalDetailsList);
            selfAppraisalDetailsList.setEmptyView(findViewById(R.id.empty_text_view));
            Intent intent = getIntent();
            type=intent.getStringExtra("type");
            month=intent.getStringExtra("month");
            customer_code = intent.getStringExtra("customer_code");
            PERFORMANCE_GRAPH = intent.getStringExtra("PERFORMANCE_GRAPH");

            tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
            tvAppOrder = (TextView) findViewById(R.id.tvAppOrder);
            tvOfflineOrder = (TextView) findViewById(R.id.tvOfflineOrder);
            tvApporderLine = (TextView) findViewById(R.id.tvApporderLine);
            tvOfflineorderLine = (TextView) findViewById(R.id.tvOfflineorderLine);

            tvtarget_ = (TextView) findViewById(R.id.tvtarget_);
            tvachievement_ = (TextView) findViewById(R.id.tvachievement_);

            if(!PERFORMANCE_GRAPH.matches("PERFORMANCE GRAPH"))
            {
                tvHeaderText.setText(PERFORMANCE_GRAPH);
            }
            tvAppOrder.setOnTouchListener(new View.OnTouchListener() {
                @Override
                public boolean onTouch(View v, MotionEvent event) {
                    tvAppOrder.setTextColor(Color.RED);
                    tvApporderLine.setBackgroundColor(Color.RED);

                    tvOfflineOrder.setTextColor(Color.GRAY);
                    tvOfflineorderLine.setBackgroundColor(Color.GRAY);
                    setListViewData("c");
                    totalTargetAchv("c");
                    return false;
                }
            });
            tvOfflineOrder.setOnTouchListener(new View.OnTouchListener() {
                @Override
                public boolean onTouch(View v, MotionEvent event) {
                    tvOfflineOrder.setTextColor(Color.RED);
                    tvOfflineorderLine.setBackgroundColor(Color.RED);

                    tvAppOrder.setTextColor(Color.GRAY);
                    tvApporderLine.setBackgroundColor(Color.GRAY);
                    setListViewData("p");
                    totalTargetAchv("p");
                    return false;
                }
            });

            setListViewData(currentPrevYear);
            totalTargetAchv(currentPrevYear);
            ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
            ivHeaderBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    finish();
                }
            });

        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

    public void setListViewData(String curryerPrevious)
    {
        try
        {
            if(curryerPrevious.equalsIgnoreCase("c"))
            {
                tvAppOrder.setTextColor(Color.RED);
                tvApporderLine.setBackgroundColor(Color.RED);

                tvOfflineOrder.setTextColor(Color.GRAY);
                tvOfflineorderLine.setBackgroundColor(Color.GRAY);
            }
            else
            {
                tvOfflineOrder.setTextColor(Color.RED);
                tvOfflineorderLine.setBackgroundColor(Color.RED);

                tvAppOrder.setTextColor(Color.GRAY);
                tvApporderLine.setBackgroundColor(Color.GRAY);
            }
            selfAppraisalListProductGroupWise =mAceDnsDatabase.getProductGroupWiseTargetForSingleMonth(curryerPrevious, customer_code);
            final SelfAppraisalProductGroupWiseListAdapter adapter = new SelfAppraisalProductGroupWiseListAdapter(mContext,
                    R.layout.self_appraisal_list_item, selfAppraisalListProductGroupWise);
            selfAppraisalDetailsList.setAdapter(adapter);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    public void totalTargetAchv(String currentPrev)
    {
        try
        {
            if(currentPrev.equalsIgnoreCase("c"))
            {
                tvtarget_.setText(mAceDnsDatabase.getTotalByKey(currentPrev, "target", customer_code));
                tvachievement_.setText(mAceDnsDatabase.getTotalByKey(currentPrev, "achievement", customer_code));
            }
            else
            {
                tvtarget_.setText(mAceDnsDatabase.getTotalByKey(currentPrev, "prev_y_target", customer_code));
                tvachievement_.setText(mAceDnsDatabase.getTotalByKey(currentPrev, "prev_y_achievement", customer_code));
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    public void finishCurrentActivity(View v)
    {
        finish();
    }

}

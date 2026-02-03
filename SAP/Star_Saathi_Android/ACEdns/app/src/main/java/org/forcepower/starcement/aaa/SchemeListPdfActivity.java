package org.forcepower.starcement.aaa;

import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;

import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import android.view.View;
import android.widget.AdapterView;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.SchemeListAdapter;
import org.forcepower.starcement.bean.MenuObj;
import org.forcepower.starcement.bean.SchemeDetails;
import org.forcepower.starcement.database.AceDnsDatabase;
import org.forcepower.starcement.util.HTTPUtils;

import java.util.ArrayList;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;

public final class SchemeListPdfActivity extends AceDnsParentActivity implements SwipeRefreshLayout.OnRefreshListener
{
    Context mContext;
    ArrayList<MenuObj> mMenuList = new ArrayList<MenuObj>();
    AceDnsDatabase mAceDnsDatabase;
    SwipeRefreshLayout chartListSwipeRefreshLayout;

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_scheme_list);
        try
        {
            mContext=this;
            if(get_user_type(mContext).equalsIgnoreCase("broker"))
            {
                selected_customr_code = get_selected_customer_code(mContext);
            }
            else
            {
                selected_customr_code = get_emp_or_customer_code(mContext);
            }
            TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
            tvHeaderText.setText("ALL SCHEMES");

            ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
            ivHeaderBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    finish();
                }
            });
            LinearLayout llHeaderDetails = (LinearLayout) findViewById(R.id.llHeaderDetails);
            llHeaderDetails.setVisibility(View.INVISIBLE);
            mAceDnsDatabase = new AceDnsDatabase(this);
            ArrayList<SchemeDetails> prodList = mAceDnsDatabase.getSchemeFile();

            GridView mGridViewMenu = (GridView) findViewById(R.id.grid_menu_Scheme);
            prepareFeatureList(prodList);
            SchemeListAdapter mSchemeListAdapter = new SchemeListAdapter(this, mMenuList);
            mGridViewMenu.setEmptyView(findViewById(R.id.empty_text_view));
            mGridViewMenu.setAdapter(mSchemeListAdapter);

            mGridViewMenu.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
                                        long arg3) {
                    String feature = mMenuList.get(arg2).getFeatureName(); //GET pdf_file_name
                    Intent intent = new Intent(mContext, SchemePdfActivity.class);
                    intent.putExtra("pdf_file_name", feature);
                    intent.putExtra("scheme_header", "SCHEME " + (arg2+1));
                    startActivity(intent);
                }
            });

            chartListSwipeRefreshLayout = (SwipeRefreshLayout) findViewById(R.id.chartListSwipeRefreshLayout);
            chartListSwipeRefreshLayout.setOnRefreshListener(this);
            chartListSwipeRefreshLayout.setColorSchemeResources(R.color.red, R.color.white, R.color.red, R.color.white);
            chartListSwipeRefreshLayout.setEnabled(false);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
        finally {
            if(mAceDnsDatabase != null)
                mAceDnsDatabase.close();
        }
    }

    public void prepareFeatureList(ArrayList<SchemeDetails> prodList)
    {
        try
        {
            mMenuList = new ArrayList<MenuObj>();

            if(prodList.size() > 0)
            {
                for(int i =0; i<prodList.size(); i++)
                {
                    MenuObj menuObj0 = new MenuObj();
                    menuObj0.setResourceId(R.drawable.scheme_only);
                    menuObj0.setFeatureName(prodList.get(i).getSchemeValue()); //GET pdf_file_name from Local db
                    mMenuList.add(menuObj0);
                }
            }
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }

    }

    @Override
    public void onRefresh()
    {
        try
        {
            chartListSwipeRefreshLayout.setRefreshing(false);
            Handler mHandler = new Handler();
            mHandler.postDelayed(new Runnable()
            {
                public void run()
                {
                    if (HTTPUtils.isConnectionPossible(mContext))
                    {

                    }
                    else
                    {
//                        Toast.makeText(mContext, check_internet_connection, Toast.LENGTH_SHORT).show();
                    }
                }
            }, 10);
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }

}

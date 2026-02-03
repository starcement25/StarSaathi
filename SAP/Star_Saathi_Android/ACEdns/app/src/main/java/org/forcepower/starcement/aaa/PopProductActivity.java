package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.SharedPrefData.get_emp_or_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_selected_customer_code;
import static org.forcepower.starcement.SharedPrefData.get_user_type;
import static org.forcepower.starcement.constants.Constants.selected_customr_code;
import static org.forcepower.starcement.util.Utils.print_log_d;

import android.app.Activity;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import androidx.fragment.app.Fragment;
import androidx.viewpager2.widget.ViewPager2;

import com.google.android.material.tabs.TabLayout;
import com.google.android.material.tabs.TabLayoutMediator;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.ViewPagerFragmentAdapter;
import org.forcepower.starcement.fragments.FragmentPopOrder;
import org.forcepower.starcement.fragments.FragmentPopOrderHistory;

import java.util.ArrayList;

public final class PopProductActivity extends AceDnsParentActivity
{
    private Activity mContext_;
    private ViewPager2 view_pager2;
    private ImageView ivMonthSelection;
    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_pop_product);

        mContext_ = this;

        try
        {
            if(get_user_type(mContext_).equalsIgnoreCase("broker"))
            {
                selected_customr_code = get_selected_customer_code(mContext_);
            }
            else
            {
                selected_customr_code = get_emp_or_customer_code(mContext_);
            }
            TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
            tvHeaderText.setText("POP PRODUCT");

            ivMonthSelection = (ImageView) findViewById(R.id.ivMonthSelection);
            ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
            ivHeaderBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    finish();
                }
            });

            final TabLayout tabLayout = (TabLayout) findViewById(R.id.sliding_tabs);

            view_pager2 = (ViewPager2) findViewById(R.id.view_pager2);
            final String[] arrData = {"Pop Order", "Order History"};

            final ArrayList<Fragment> fragments = new ArrayList<>();
            fragments.add(FragmentPopOrder.newInstance(""));
            fragments.add(FragmentPopOrderHistory.newInstance(""));

            final ViewPagerFragmentAdapter adapter = new ViewPagerFragmentAdapter(this, fragments);
            view_pager2.setAdapter(adapter);

            new TabLayoutMediator(tabLayout, view_pager2,
                    (tab, position) -> {
                        // Set tab titles here
                        tab.setText(arrData[position]);
                    }).attach();

            view_pager2.registerOnPageChangeCallback(new ViewPager2.OnPageChangeCallback() {
                @Override
                public void onPageSelected(int position) {
                    super.onPageSelected(position);
                    if(position == 0)
                    {
                        ivMonthSelection.setVisibility(View.GONE);
                    }
                    else
                    {
                        ivMonthSelection.setVisibility(View.VISIBLE);
                    }
                }
            });
        }
        catch (Exception e)
        {
            e.printStackTrace();
        }
    }
    @Override
    public void onBackPressed()
    {
       if(view_pager2.getCurrentItem() ==1)
       {
           view_pager2.setCurrentItem(0);
       }
       else
       {
           finish();
       }
    }
}

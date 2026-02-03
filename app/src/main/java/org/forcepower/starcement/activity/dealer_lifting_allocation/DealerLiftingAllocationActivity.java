package org.forcepower.starcement.activity.dealer_lifting_allocation;

import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageView;
import android.widget.TextView;

import androidx.activity.EdgeToEdge;
import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;
import androidx.fragment.app.Fragment;
import androidx.viewpager2.widget.ViewPager2;

import com.google.android.material.tabs.TabLayout;
import com.google.android.material.tabs.TabLayoutMediator;

import org.forcepower.starcement.R;
import org.forcepower.starcement.aaa.AceDnsParentActivity;
import org.forcepower.starcement.activity.dealer_lifting_allocation.fragment.LiftingAllocationAssignedFragment;
import org.forcepower.starcement.adapter.ViewPagerFragmentAdapter;
import org.forcepower.starcement.util.HTTPUtils;

import java.util.ArrayList;

public class DealerLiftingAllocationActivity extends AceDnsParentActivity {
    private Activity mContext;
    private ViewPager2 view_pager2;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_dealer_lifting_allocation);
        try {
            mContext = this;

            final TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
            tvHeaderText.setText("LIFTING ALLOCATION Invoice)");

            final TabLayout tabLayout = (TabLayout) findViewById(R.id.sliding_tabs);
            view_pager2 = (ViewPager2) findViewById(R.id.view_pager2);

            final ImageView ivMonthSelection = (ImageView) findViewById(R.id.ivMonthSelection);
            ivMonthSelection.setVisibility(View.VISIBLE);

            final ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
            ivHeaderBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });
            try {
                final String[] arrData = {"ASSIGNED", "ALLOCATED"};

                final LiftingAllocationAssignedFragment frgASSIGNED = new LiftingAllocationAssignedFragment().newInstance("");
//                final DealerLiftingAlocattedInvFragment frgALLOCATED = new DealerLiftingAlocattedInvFragment().newInstance("");

                final ArrayList<Fragment> fragments = new ArrayList<>();
                fragments.add(frgASSIGNED);
//                fragments.add(frgALLOCATED);

                final ViewPagerFragmentAdapter adapter = new ViewPagerFragmentAdapter(this, fragments);
                view_pager2.setAdapter(adapter);
                view_pager2.setOffscreenPageLimit(1);
                new TabLayoutMediator(tabLayout, view_pager2,
                        (tab, position) -> {
                            // Set tab titles here
                            tab.setText(arrData[position]);
                        }).attach();

                view_pager2.registerOnPageChangeCallback(new ViewPager2.OnPageChangeCallback() {
                    @Override
                    public void onPageSelected(int position) {
                        super.onPageSelected(position);
                    }
                });

                ivMonthSelection.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View view) {
                        try {
                            if (HTTPUtils.isConnectionPossible(mContext)) {
                                if (view_pager2.getCurrentItem() == 0) {
                                    frgASSIGNED.assignFilter();
                                } else {
//                                    frgALLOCATED.allocattedFilter();
                                }
                            } else {
                                show_msg_alert(mContext, check_internet_connection, false);
                            }
                        } catch (Exception e) {
                            e.printStackTrace();
                        }
                    }
                });
            } catch (Exception e) {
                e.printStackTrace();
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    protected void onSaveInstanceState(@NonNull Bundle oldInstanceState) {
        super.onSaveInstanceState(oldInstanceState);
        oldInstanceState.clear();
    }

    @SuppressLint("MissingSuperCall")
    @Override
    public void onBackPressed() {
        if (view_pager2.getCurrentItem() != 0) {
            view_pager2.setCurrentItem(0);
        } else {
            finish();
        }
    }

    public void setV(final int index) {
        view_pager2.setCurrentItem(index);
    }
}

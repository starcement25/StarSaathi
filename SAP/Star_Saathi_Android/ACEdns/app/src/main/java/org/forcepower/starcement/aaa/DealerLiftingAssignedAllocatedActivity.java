package org.forcepower.starcement.aaa;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.ImageView;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;
import androidx.viewpager2.widget.ViewPager2;

import com.google.android.material.tabs.TabLayout;
import com.google.android.material.tabs.TabLayoutMediator;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.ViewPagerFragmentAdapter;
import org.forcepower.starcement.fragments.DealerLiftingAssignedFragment;
import org.forcepower.starcement.fragments.DealerLiftingAlocattedFragment;
import java.util.ArrayList;


public final class DealerLiftingAssignedAllocatedActivity extends AceDnsParentActivity
{
	private Activity mContext;
	private ViewPager2 view_pager2;

	@Override
	protected void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_dealer_assigned_open_allocated);
		try
		{
			mContext = this;

			final TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
			tvHeaderText.setText("LIFTING ALLOCATION");

            final TabLayout tabLayout = (TabLayout) findViewById(R.id.sliding_tabs);
			view_pager2 = (ViewPager2) findViewById(R.id.view_pager2);

			final ImageView ivMonthSelection = (ImageView) findViewById(R.id.ivMonthSelection);
			ivMonthSelection.setVisibility(View.VISIBLE);

			final ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
			ivHeaderBack.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					onBackPressed();
				}
			});

			//
			try
			{
				final String[] arrData = {"ASSIGNED", "ALLOCATED"};

				final DealerLiftingAssignedFragment frgASSIGNED = new DealerLiftingAssignedFragment().newInstance("");
				final DealerLiftingAlocattedFragment frgALLOCATED = new DealerLiftingAlocattedFragment().newInstance("");

				final ArrayList<Fragment> fragments = new ArrayList<>();
				fragments.add(frgASSIGNED);
				fragments.add(frgALLOCATED);

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
			}
			catch (Exception e)
			{
				e.printStackTrace();
			}
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	public void setV(final int index)
	{
		view_pager2.setCurrentItem(index);

	}
	@Override
	protected void onSaveInstanceState(@NonNull Bundle oldInstanceState)
	{
		super.onSaveInstanceState(oldInstanceState);
		oldInstanceState.clear();
	}
	@SuppressLint("MissingSuperCall")
	@Override
	public void onBackPressed()
	{
		if(view_pager2.getCurrentItem() != 0)
		{
			view_pager2.setCurrentItem(0);
		}
		else
		{
			finish();
		}
	}
}

package org.forcepower.starcement.aaa;

import static org.forcepower.starcement.constants.Constants.check_internet_connection;
import static org.forcepower.starcement.util.Utils.show_msg_Dialog;
import static org.forcepower.starcement.util.Utils.show_msg_alert;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.DatePicker;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;
import androidx.viewpager2.widget.ViewPager2;

import com.google.android.material.tabs.TabLayout;
import com.google.android.material.tabs.TabLayoutMediator;

import org.forcepower.starcement.R;
import org.forcepower.starcement.adapter.ViewPagerFragmentAdapter;
import org.forcepower.starcement.fragments.DealerFragmentLiftingApproved;
import org.forcepower.starcement.fragments.DealerFragmentLiftingPending;
import org.forcepower.starcement.fragments.DealerFragmentLiftingRejected;
import org.forcepower.starcement.util.HTTPUtils;
import org.forcepower.starcement.util.MonthYearPickerDialog;
import org.forcepower.starcement.util.Utils;

import java.util.ArrayList;

public final class DealerLiftingActivity extends AceDnsParentActivity
{
	private Activity mContext;
	private ViewPager2 view_pager2;
	private final ArrayList<Fragment> fragments = new ArrayList<>();
	private ViewPagerFragmentAdapter adapter;
	private DealerFragmentLiftingPending dfP;
	private DealerFragmentLiftingApproved dfA;
	private DealerFragmentLiftingRejected dfR;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_dealer_lifting);
		try
		{
			mContext = this;

			final TextView tvHeaderText = (TextView) findViewById(R.id.tvHeaderText);
			tvHeaderText.setText("LIFTING HISTORY");

			final ImageView ivHeaderAdd = (ImageView) findViewById(R.id.ivHeaderAdd);
//			ivHeaderAdd.setVisibility(View.GONE);
			ivHeaderAdd.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					if(HTTPUtils.isConnectionPossible(mContext))
					{
						Intent intent=new Intent(mContext, DealerLiftingAssignedAllocatedInvActivity.class);
						startActivity(intent);
					}
					else
					{
						show_msg_Dialog(mContext, check_internet_connection);
					}
				}
			});

			final LinearLayout llHeaderDetails = (LinearLayout) findViewById(R.id.llHeaderDetails);
			llHeaderDetails.setOnClickListener(new OnClickListener() {
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
									tvHeaderText.setText("LIFTING HISTORY ("+header_txt+")");
									final String year_month = Utils.changeDateFormat( "yyyy-MM", "yyyy-MM", selectedYear + "-" + selectedMonth);

									dfP = new DealerFragmentLiftingPending().newInstance(year_month);
									dfA = new DealerFragmentLiftingApproved().newInstance(year_month);
									dfR = new DealerFragmentLiftingRejected().newInstance(year_month);
									fragments.clear();
									fragments.add(dfP);
									fragments.add(dfA);
									fragments.add(dfR);

									adapter = new ViewPagerFragmentAdapter(DealerLiftingActivity.this, fragments);
									view_pager2.setAdapter(adapter);
									view_pager2.setOffscreenPageLimit(fragments.size());
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

			final TabLayout tabLayout = (TabLayout) findViewById(R.id.sliding_tabs);

			view_pager2 = (ViewPager2) findViewById(R.id.view_pager2);
			final String[] arrData = {"Pending", "Approved", "Rejected"};

			dfP = new DealerFragmentLiftingPending().newInstance("");
			dfA = new DealerFragmentLiftingApproved().newInstance("");
			dfR = new DealerFragmentLiftingRejected().newInstance("");
			fragments.clear();
			fragments.add(dfP);
			fragments.add(dfA);
			fragments.add(dfR);

			adapter = new ViewPagerFragmentAdapter(DealerLiftingActivity.this, fragments);
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
					if(view_pager2.getCurrentItem() == 0)
					{
						dfP.reload();
					}
					else if(view_pager2.getCurrentItem() == 1)
					{
						dfA.reload();
					}
					else if(view_pager2.getCurrentItem() == 2)
					{
						dfR.reload();
					}
				}

			});

			final ImageView ivHeaderBack = (ImageView) findViewById(R.id.ivHeaderBack);
			ivHeaderBack.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					onBackPressed();
				}
			});
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}

	protected void onSaveInstanceState(@NonNull Bundle oldInstanceState) {
		super.onSaveInstanceState(oldInstanceState);
		oldInstanceState.clear();
	}

	@Override
	public void onBackPressed() {
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
